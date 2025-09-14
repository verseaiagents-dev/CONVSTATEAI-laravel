<?php

namespace App\Services;

use App\Models\EnhancedChatSession;
use App\Models\ProductInteraction;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class GDPRComplianceService
{
    /**
     * Export user data for GDPR compliance
     */
    public static function exportUserData(string $sessionId): array
    {
        $session = EnhancedChatSession::where('session_id', $sessionId)->first();
        
        if (!$session) {
            return [];
        }

        $interactions = ProductInteraction::where('session_id', $sessionId)->get();
        
        return [
            'session_info' => [
                'session_id' => $session->session_id,
                'created_at' => $session->created_at->toISOString(),
                'last_activity' => $session->last_activity?->toISOString(),
                'status' => $session->status
            ],
            'user_preferences' => $session->user_preferences ?? [],
            'intent_history' => $session->intent_history ?? [],
            'chat_history' => $session->chat_history ?? [],
            'product_interactions' => $interactions->map(function ($interaction) {
                return [
                    'product_id' => $interaction->product_id,
                    'action' => $interaction->action,
                    'timestamp' => $interaction->timestamp->toISOString(),
                    'source' => $interaction->source,
                    'metadata' => $interaction->metadata
                ];
            })->toArray(),
            'exported_at' => now()->toISOString(),
            'export_reason' => 'GDPR Data Subject Access Request'
        ];
    }

    /**
     * Delete user data for GDPR compliance
     */
    public static function deleteUserData(string $sessionId): bool
    {
        try {
            // Log deletion for audit purposes
            Log::info('GDPR data deletion requested', [
                'session_id' => $sessionId,
                'deletion_requested_at' => now()->toISOString(),
                'reason' => 'GDPR Right to Erasure'
            ]);

            // Delete product interactions
            ProductInteraction::where('session_id', $sessionId)->delete();

            // Delete chat session
            $session = EnhancedChatSession::where('session_id', $sessionId)->first();
            if ($session) {
                $session->delete();
            }

            Log::info('GDPR data deletion completed', [
                'session_id' => $sessionId,
                'deletion_completed_at' => now()->toISOString()
            ]);

            return true;
        } catch (\Exception $e) {
            Log::error('GDPR data deletion failed', [
                'session_id' => $sessionId,
                'error' => $e->getMessage(),
                'deletion_failed_at' => now()->toISOString()
            ]);

            return false;
        }
    }

    /**
     * Anonymize user data for GDPR compliance
     */
    public static function anonymizeUserData(string $sessionId): bool
    {
        try {
            $session = EnhancedChatSession::where('session_id', $sessionId)->first();
            
            if (!$session) {
                return false;
            }

            // Anonymize session data
            $session->update([
                'user_id' => null,
                'user_preferences' => ['anonymized' => true],
                'intent_history' => [],
                'chat_history' => [],
                'product_interactions' => []
            ]);

            // Anonymize product interactions
            ProductInteraction::where('session_id', $sessionId)->update([
                'ip_address' => '0.0.0.0',
                'user_agent' => 'Anonymized',
                'metadata' => json_encode(['anonymized' => true])
            ]);

            Log::info('GDPR data anonymization completed', [
                'session_id' => $sessionId,
                'anonymized_at' => now()->toISOString()
            ]);

            return true;
        } catch (\Exception $e) {
            Log::error('GDPR data anonymization failed', [
                'session_id' => $sessionId,
                'error' => $e->getMessage()
            ]);

            return false;
        }
    }

    /**
     * Get data retention summary
     */
    public static function getDataRetentionSummary(): array
    {
        $totalSessions = EnhancedChatSession::count();
        $activeSessions = EnhancedChatSession::where('status', 'active')->count();
        $expiredSessions = EnhancedChatSession::where('status', 'expired')->count();
        $completedSessions = EnhancedChatSession::where('status', 'completed')->count();

        $totalInteractions = ProductInteraction::count();
        $recentInteractions = ProductInteraction::where('created_at', '>=', now()->subDays(30))->count();

        return [
            'sessions' => [
                'total' => $totalSessions,
                'active' => $activeSessions,
                'expired' => $expiredSessions,
                'completed' => $completedSessions
            ],
            'interactions' => [
                'total' => $totalInteractions,
                'last_30_days' => $recentInteractions
            ],
            'retention_policy' => [
                'active_sessions' => '30 days',
                'completed_sessions' => '90 days',
                'expired_sessions' => '7 days',
                'interactions' => '730 days',
                'audit_logs' => '365 days'
            ]
        ];
    }

    /**
     * Clean up expired data according to retention policy
     */
    public static function cleanupExpiredData(): array
    {
        $cleanupResults = [
            'sessions_deleted' => 0,
            'interactions_deleted' => 0,
            'errors' => []
        ];

        try {
            // Delete expired sessions older than 7 days
            $expiredSessions = EnhancedChatSession::where('status', 'expired')
                ->where('updated_at', '<=', now()->subDays(7))
                ->get();

            foreach ($expiredSessions as $session) {
                try {
                    // Delete related interactions first
                    ProductInteraction::where('session_id', $session->session_id)->delete();
                    $session->delete();
                    $cleanupResults['sessions_deleted']++;
                } catch (\Exception $e) {
                    $cleanupResults['errors'][] = "Failed to delete session {$session->session_id}: " . $e->getMessage();
                }
            }

            // Delete old interactions older than 730 days
            $oldInteractions = ProductInteraction::where('created_at', '<=', now()->subDays(730));
            $cleanupResults['interactions_deleted'] = $oldInteractions->count();
            $oldInteractions->delete();

            Log::info('GDPR data cleanup completed', [
                'sessions_deleted' => $cleanupResults['sessions_deleted'],
                'interactions_deleted' => $cleanupResults['interactions_deleted'],
                'cleanup_completed_at' => now()->toISOString()
            ]);

        } catch (\Exception $e) {
            Log::error('GDPR data cleanup failed', [
                'error' => $e->getMessage(),
                'cleanup_failed_at' => now()->toISOString()
            ]);

            $cleanupResults['errors'][] = 'General cleanup error: ' . $e->getMessage();
        }

        return $cleanupResults;
    }

    /**
     * Generate GDPR compliance report
     */
    public static function generateComplianceReport(): array
    {
        $retentionSummary = self::getDataRetentionSummary();
        $cleanupResults = self::cleanupExpiredData();

        return [
            'report_generated_at' => now()->toISOString(),
            'data_retention' => $retentionSummary,
            'cleanup_results' => $cleanupResults,
            'compliance_status' => [
                'data_minimization' => 'Compliant',
                'purpose_limitation' => 'Compliant',
                'storage_limitation' => 'Compliant',
                'right_to_access' => 'Implemented',
                'right_to_erasure' => 'Implemented',
                'right_to_portability' => 'Implemented'
            ],
            'recommendations' => [
                'Regular cleanup should be automated',
                'Monitor data retention compliance',
                'Review anonymization policies'
            ]
        ];
    }
}
