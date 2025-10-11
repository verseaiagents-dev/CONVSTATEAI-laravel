<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\EnhancedChatSession;

class ResetDailyLimits extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'limits:reset {--all : Reset all sessions} {--session-id= : Reset specific session}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Reset daily view limits for chat sessions';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        if ($this->option('all')) {
            // ✅ FIX: Önce eski session'ları inactive yap
            $inactivatedCount = EnhancedChatSession::where('status', 'active')
                ->whereDate('last_activity', '<', today())
                ->update(['status' => 'inactive']);
                
            // Reset all active sessions - last_activity güncellemeden!
            $count = EnhancedChatSession::where('status', 'active')->update([
                'daily_view_count' => 0
                // ✅ FIX: last_activity ve view_count kaldırıldı
            ]);
            
            $this->info("✅ Reset daily limits for {$count} active sessions.");
            if ($inactivatedCount > 0) {
                $this->info("✅ Inactivated {$inactivatedCount} old sessions.");
            }
        } elseif ($sessionId = $this->option('session-id')) {
            // Reset specific session
            $session = EnhancedChatSession::where('session_id', $sessionId)->first();
            
            if ($session) {
                $session->update([
                    'daily_view_count' => 0
                    // ✅ FIX: last_activity ve view_count kaldırıldı
                ]);
                
                $this->info("✅ Reset daily limits for session: {$sessionId}");
            } else {
                $this->error("❌ Session not found: {$sessionId}");
            }
        } else {
            // Reset sessions that exceeded limit
            $sessions = EnhancedChatSession::where('status', 'active')
                ->whereRaw('daily_view_count >= daily_view_limit')
                ->get();
            
            foreach ($sessions as $session) {
                $session->update([
                    'daily_view_count' => 0
                    // ✅ FIX: last_activity ve view_count kaldırıldı
                ]);
            }
            
            $this->info("✅ Reset daily limits for {$sessions->count()} sessions that exceeded limit.");
        }
        
        // Clear cache to ensure fresh data
        \Cache::flush();
        $this->info("✅ Cache cleared.");
        
        return 0;
    }
}
