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
        $this->info('Starting comprehensive daily view limits reset...');
        
        try {
            // Reset ALL sessions (not just active ones)
            $allSessions = EnhancedChatSession::all();
            $resetCount = 0;
            $fixedLimitCount = 0;
            
            foreach ($allSessions as $session) {
                // ✅ Eğer daily_view_limit 0 veya null ise, default 20 yap
                $dailyViewLimit = $session->daily_view_limit > 0 ? $session->daily_view_limit : 20;
                
                // Limit fix edildi mi kontrol et
                if ($session->daily_view_limit <= 0) {
                    $fixedLimitCount++;
                    $this->warn("Fixed session {$session->session_id}: limit was {$session->daily_view_limit}, now {$dailyViewLimit}");
                }
                
                // Reset daily view count and update activity
                $session->update([
                    'view_count' => 0,
                    'daily_view_count' => 0, // ✅ Günlük view sayacını sıfırla
                    'daily_view_limit' => $dailyViewLimit, // ✅ Limit'i de güncelle (0 ise 20 yap)
                    'last_activity' => now(),
                    'status' => 'active' // Ensure session is active
                ]);
                
                $resetCount++;
            }
            
            // Also clear any cached session data
            \Cache::flush();
            
            $this->info("Successfully reset daily view limits for ALL {$resetCount} sessions.");
            if ($fixedLimitCount > 0) {
                $this->info("Fixed {$fixedLimitCount} sessions with 0 or null daily_view_limit.");
            }
            $this->info("Cache cleared to ensure fresh start.");
            
            // Log the reset
            \Log::info('Comprehensive daily view limits reset completed', [
                'reset_count' => $resetCount,
                'fixed_limit_count' => $fixedLimitCount,
                'timestamp' => now(),
                'cache_cleared' => true
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
