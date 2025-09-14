<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class AuditLogService
{
    /**
     * Log user action for audit purposes
     */
    public static function logAction(string $action, array $data = [], ?string $userId = null): void
    {
        $logData = [
            'action' => $action,
            'user_id' => $userId ?? Auth::id(),
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'timestamp' => now()->toISOString(),
            'data' => $data
        ];

        Log::channel('audit')->info('User Action', $logData);
    }

    /**
     * Log chat session activity
     */
    public static function logChatSessionActivity(string $sessionId, string $activity, array $details = []): void
    {
        self::logAction('chat_session_' . $activity, [
            'session_id' => $sessionId,
            'details' => $details
        ]);
    }

    /**
     * Log product interaction
     */
    public static function logProductInteraction(string $sessionId, int $productId, string $action, array $metadata = []): void
    {
        self::logAction('product_interaction', [
            'session_id' => $sessionId,
            'product_id' => $productId,
            'action' => $action,
            'metadata' => $metadata
        ]);
    }

    /**
     * Log data access
     */
    public static function logDataAccess(string $dataType, string $identifier, ?string $userId = null): void
    {
        self::logAction('data_access', [
            'data_type' => $dataType,
            'identifier' => $identifier,
            'access_method' => request()->method(),
            'endpoint' => request()->path()
        ], $userId);
    }

    /**
     * Log data modification
     */
    public static function logDataModification(string $dataType, string $identifier, array $changes, ?string $userId = null): void
    {
        self::logAction('data_modification', [
            'data_type' => $dataType,
            'identifier' => $identifier,
            'changes' => $changes,
            'previous_state' => self::getPreviousState($dataType, $identifier),
            'modification_method' => request()->method(),
            'endpoint' => request()->path()
        ], $userId);
    }

    /**
     * Log data deletion
     */
    public static function logDataDeletion(string $dataType, string $identifier, array $deletedData, ?string $userId = null): void
    {
        self::logAction('data_deletion', [
            'data_type' => $dataType,
            'identifier' => $identifier,
            'deleted_data' => $deletedData,
            'deletion_reason' => request()->input('reason', 'User request'),
            'deletion_method' => request()->method(),
            'endpoint' => request()->path()
        ], $userId);
    }

    /**
     * Log authentication events
     */
    public static function logAuthentication(string $event, array $details = []): void
    {
        self::logAction('authentication_' . $event, [
            'event' => $event,
            'details' => $details
        ]);
    }

    /**
     * Log authorization failures
     */
    public static function logAuthorizationFailure(string $action, string $resource, array $details = []): void
    {
        self::logAction('authorization_failure', [
            'action' => $action,
            'resource' => $resource,
            'details' => $details,
            'user_id' => Auth::id(),
            'ip_address' => request()->ip()
        ]);
    }

    /**
     * Log GDPR compliance actions
     */
    public static function logGDPRAction(string $action, string $sessionId, array $details = []): void
    {
        self::logAction('gdpr_' . $action, [
            'session_id' => $sessionId,
            'action' => $action,
            'details' => $details,
            'compliance_basis' => 'GDPR Article 17 (Right to Erasure)'
        ]);
    }

    /**
     * Log system configuration changes
     */
    public static function logConfigurationChange(string $configKey, $oldValue, $newValue, ?string $userId = null): void
    {
        self::logAction('configuration_change', [
            'config_key' => $configKey,
            'old_value' => $oldValue,
            'new_value' => $newValue,
            'change_timestamp' => now()->toISOString()
        ], $userId);
    }

    /**
     * Log performance metrics
     */
    public static function logPerformanceMetric(string $metric, float $value, array $context = []): void
    {
        self::logAction('performance_metric', [
            'metric' => $metric,
            'value' => $value,
            'unit' => $context['unit'] ?? 'ms',
            'context' => $context
        ]);
    }

    /**
     * Log security events
     */
    public static function logSecurityEvent(string $event, array $details = []): void
    {
        self::logAction('security_event', [
            'event' => $event,
            'severity' => $details['severity'] ?? 'medium',
            'details' => $details,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent()
        ]);
    }

    /**
     * Get previous state of data for audit comparison
     */
    private static function getPreviousState(string $dataType, string $identifier): ?array
    {
        // This would typically query the database for the previous state
        // For now, return null as this is a simplified implementation
        return null;
    }

    /**
     * Generate audit report for a specific time period
     */
    public static function generateAuditReport(\DateTime $startDate, \DateTime $endDate): array
    {
        // This would typically query the audit logs for the specified period
        // For now, return a basic structure
        return [
            'report_period' => [
                'start_date' => $startDate->toISOString(),
                'end_date' => $endDate->toISOString()
            ],
            'total_actions' => 0,
            'action_breakdown' => [],
            'user_activity' => [],
            'security_events' => [],
            'data_access_patterns' => [],
            'compliance_actions' => []
        ];
    }

    /**
     * Export audit logs for compliance purposes
     */
    public static function exportAuditLogs(\DateTime $startDate, \DateTime $endDate, string $format = 'json'): string
    {
        $report = self::generateAuditReport($startDate, $endDate);
        
        if ($format === 'csv') {
            return self::convertToCSV($report);
        }
        
        return json_encode($report, JSON_PRETTY_PRINT);
    }

    /**
     * Convert audit data to CSV format
     */
    private static function convertToCSV(array $data): string
    {
        // Simple CSV conversion for audit data
        $csv = "Action,Timestamp,User ID,IP Address,Details\n";
        
        // This would iterate through actual audit log entries
        // For now, return basic CSV structure
        
        return $csv;
    }
}
