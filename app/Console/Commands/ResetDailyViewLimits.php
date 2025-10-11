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
            // ✅ FIX: Önce önceki günlerin session'larını inactive yap
            $inactivatedCount = EnhancedChatSession::where('status', 'active')
                ->whereDate('last_activity', '<', today())
                ->update(['status' => 'inactive']);
            
            if ($inactivatedCount > 0) {
                $this->info("Inactivated {$inactivatedCount} old sessions from previous days.");
            }
            
            // Reset ONLY today's active sessions (bugün aktif olanları sıfırla)
            $todaySessions = EnhancedChatSession::where('status', 'active')
                ->whereDate('last_activity', today())
                ->get();
            $resetCount = 0;
            $fixedLimitCount = 0;
            
            foreach ($todaySessions as $session) {
                // ✅ Eğer daily_view_limit 0 veya null ise, default 20 yap
                $dailyViewLimit = $session->daily_view_limit > 0 ? $session->daily_view_limit : 20;
                
                // Limit fix edildi mi kontrol et
                if ($session->daily_view_limit <= 0) {
                    $fixedLimitCount++;
                    $this->warn("Fixed session {$session->session_id}: limit was {$session->daily_view_limit}, now {$dailyViewLimit}");
                }
                
                // Reset daily view count - last_activity'yi GÜNCELLEMEDEN
                // ✅ FIX: last_activity bugüne taşınmamalı yoksa IP kontrolü yanlış çalışır
                $session->update([
                    'daily_view_count' => 0, // ✅ Günlük view sayacını sıfırla
                    'daily_view_limit' => $dailyViewLimit // ✅ Limit'i de güncelle (0 ise 20 yap)
                    // last_activity => now() kaldırıldı - IP kontrolü için önemli!
                    // status => 'active' kaldırıldı - zaten aktif olanları alıyoruz
                    // view_count kaldırıldı - veritabanında yok
                ]);
                
                $resetCount++;
            }
            
            // Also clear any cached session data
            \Cache::flush();
            
            $this->info("✅ Successfully reset daily view limits for {$resetCount} today's active sessions.");
            if ($inactivatedCount > 0) {
                $this->info("✅ Inactivated {$inactivatedCount} old sessions from previous days.");
            }
            if ($fixedLimitCount > 0) {
                $this->info("✅ Fixed {$fixedLimitCount} sessions with 0 or null daily_view_limit.");
            }
            $this->info("✅ Cache cleared to ensure fresh start.");
            
            // Log the reset
            \Log::info('Comprehensive daily view limits reset completed', [
                'reset_count' => $resetCount,
                'fixed_limit_count' => $fixedLimitCount,
                'inactivated_count' => $inactivatedCount,
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
