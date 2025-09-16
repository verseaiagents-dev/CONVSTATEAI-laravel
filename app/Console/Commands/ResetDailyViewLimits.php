<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\EnhancedChatSession;
use Carbon\Carbon;

class ResetDailyViewLimits extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'daily:reset-view-limits';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Reset daily view limits for all active chat sessions at midnight';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting daily view limits reset...');
        
        try {
            // Get all active sessions that need reset
            $sessions = EnhancedChatSession::where('status', 'active')
                ->where(function($query) {
                    $query->whereNull('last_activity')
                          ->orWhere('last_activity', '<', Carbon::today());
                })
                ->get();

            $resetCount = 0;
            
            foreach ($sessions as $session) {
                // Reset daily view count
                $session->update([
                    'daily_view_count' => 0,
                    'last_activity' => now()
                ]);
                
                $resetCount++;
            }
            
            $this->info("Successfully reset daily view limits for {$resetCount} sessions.");
            
            // Log the reset
            \Log::info('Daily view limits reset completed', [
                'reset_count' => $resetCount,
                'timestamp' => now()
            ]);
            
        } catch (\Exception $e) {
            $this->error('Failed to reset daily view limits: ' . $e->getMessage());
            \Log::error('Daily view limits reset failed', [
                'error' => $e->getMessage(),
                'timestamp' => now()
            ]);
            
            return 1;
        }
        
        return 0;
    }
}
