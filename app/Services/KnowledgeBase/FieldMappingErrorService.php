<?php

namespace App\Services\KnowledgeBase;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;
use App\Notifications\FieldMappingErrorNotification;
use Exception;
use Throwable;

class FieldMappingErrorService
{
    private const ERROR_LEVELS = [
        'critical' => 1,
        'error' => 2,
        'warning' => 3,
        'info' => 4,
        'debug' => 5
    ];

    /**
     * Log error with context
     */
    public function logError(
        string $message,
        array $context = [],
        string $level = 'error',
        ?Throwable $exception = null
    ): void {
        $logData = [
            'message' => $message,
            'context' => $context,
            'level' => $level,
            'timestamp' => now()->toISOString(),
            'user_id' => auth()->id(),
            'request_id' => request()->id() ?? uniqid(),
            'user_agent' => request()->userAgent(),
            'ip_address' => request()->ip()
        ];

        if ($exception) {
            $logData['exception'] = [
                'class' => get_class($exception),
                'message' => $exception->getMessage(),
                'file' => $exception->getFile(),
                'line' => $exception->getLine(),
                'trace' => $exception->getTraceAsString()
            ];
        }

        // Log to appropriate channel based on level
        $this->logToChannel($logData, $level);

        // Send notifications for critical errors
        if ($this->shouldNotify($level)) {
            $this->sendErrorNotification($logData);
        }

        // Store error in database for monitoring
        $this->storeErrorRecord($logData);
    }

    /**
     * Log to appropriate channel
     */
    private function logToChannel(array $logData, string $level): void
    {
        $channel = $this->getChannelForLevel($level);
        
        switch ($level) {
            case 'critical':
            case 'error':
                Log::channel($channel)->error($logData['message'], $logData);
                break;
            case 'warning':
                Log::channel($channel)->warning($logData['message'], $logData);
                break;
            case 'info':
                Log::channel($channel)->info($logData['message'], $logData);
                break;
            case 'debug':
                Log::channel($channel)->debug($logData['message'], $logData);
                break;
        }
    }

    /**
     * Get channel for error level
     */
    private function getChannelForLevel(string $level): string
    {
        if (in_array($level, ['critical', 'error'])) {
            return 'error_log';
        }
        
        if ($level === 'warning') {
            return 'warning_log';
        }
        
        return 'daily';
    }

    /**
     * Check if error should trigger notification
     */
    private function shouldNotify(string $level): bool
    {
        $configLevel = config('field_mapping.notification_level', 'critical');
        return self::ERROR_LEVELS[$level] <= self::ERROR_LEVELS[$configLevel];
    }

    /**
     * Send error notification
     */
    private function sendErrorNotification(array $logData): void
    {
        try {
            // Send email notification
            if (config('field_mapping.notifications.email.enabled', true)) {
                $this->sendEmailNotification($logData);
            }

            // Send Slack notification
            if (config('field_mapping.notifications.slack.enabled', false)) {
                $this->sendSlackNotification($logData);
            }

            // Send in-app notification
            if (config('field_mapping.notifications.in_app.enabled', true)) {
                $this->sendInAppNotification($logData);
            }
        } catch (Exception $e) {
            Log::error('Failed to send error notification: ' . $e->getMessage());
        }
    }

    /**
     * Send email notification
     */
    private function sendEmailNotification(array $logData): void
    {
        $recipients = config('field_mapping.notifications.email.recipients', []);
        
        if (empty($recipients)) {
            return;
        }

        try {
            Mail::send('emails.field-mapping-error', $logData, function ($message) use ($recipients, $logData) {
                $message->to($recipients)
                    ->subject('Field Mapping Error: ' . $logData['message'])
                    ->priority(1); // High priority
            });
        } catch (Exception $e) {
            Log::error('Failed to send email notification: ' . $e->getMessage());
        }
    }

    /**
     * Send Slack notification
     */
    private function sendSlackNotification(array $logData): void
    {
        $webhookUrl = config('field_mapping.notifications.slack.webhook_url');
        
        if (!$webhookUrl) {
            return;
        }

        try {
            $payload = [
                'text' => "🚨 Field Mapping Error",
                'attachments' => [
                    [
                        'color' => '#ff0000',
                        'fields' => [
                            [
                                'title' => 'Error Message',
                                'value' => $logData['message'],
                                'short' => false
                            ],
                            [
                                'title' => 'Level',
                                'value' => strtoupper($logData['level']),
                                'short' => true
                            ],
                            [
                                'title' => 'User ID',
                                'value' => $logData['user_id'] ?? 'Guest',
                                'short' => true
                            ],
                            [
                                'title' => 'Timestamp',
                                'value' => $logData['timestamp'],
                                'short' => true
                            ]
                        ]
                    ]
                ]
            ];

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $webhookUrl);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
            curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 5);
            
            $response = curl_exec($ch);
            curl_close($ch);

            if ($response === false) {
                Log::warning('Slack notification failed');
            }
        } catch (Exception $e) {
            Log::error('Failed to send Slack notification: ' . $e->getMessage());
        }
    }

    /**
     * Send in-app notification
     */
    private function sendInAppNotification(array $logData): void
    {
        try {
            // This would typically send to admin users or store in database
            // For now, we'll just log it
            Log::channel('daily')->info('In-app error notification', $logData);
        } catch (Exception $e) {
            Log::error('Failed to send in-app notification: ' . $e->getMessage());
        }
    }

    /**
     * Store error record in database
     */
    private function storeErrorRecord(array $logData): void
    {
        try {
            // This would typically store in a dedicated errors table
            // For now, we'll use cache to track recent errors
            $errorKey = 'field_mapping_errors:' . date('Y-m-d');
            $errors = cache()->get($errorKey, []);
            
            $errors[] = [
                'message' => $logData['message'],
                'level' => $logData['level'],
                'timestamp' => $logData['timestamp'],
                'user_id' => $logData['user_id'],
                'context' => $logData['context']
            ];
            
            // Keep only last 100 errors per day
            if (count($errors) > 100) {
                $errors = array_slice($errors, -100);
            }
            
            cache()->put($errorKey, $errors, 86400); // 24 hours
        } catch (Exception $e) {
            Log::error('Failed to store error record: ' . $e->getMessage());
        }
    }

    /**
     * Get error statistics
     */
    public function getErrorStats(string $period = 'today'): array
    {
        try {
            $stats = [
                'total_errors' => 0,
                'errors_by_level' => [],
                'errors_by_hour' => [],
                'most_common_errors' => []
            ];

            switch ($period) {
                case 'today':
                    $date = date('Y-m-d');
                    break;
                case 'week':
                    $date = date('Y-m-d', strtotime('-7 days'));
                    break;
                case 'month':
                    $date = date('Y-m-d', strtotime('-30 days'));
                    break;
                default:
                    $date = date('Y-m-d');
            }

            $errorKey = 'field_mapping_errors:' . $date;
            $errors = cache()->get($errorKey, []);

            $stats['total_errors'] = count($errors);

            // Count by level
            foreach ($errors as $error) {
                $level = $error['level'];
                $stats['errors_by_level'][$level] = ($stats['errors_by_level'][$level] ?? 0) + 1;

                // Count by hour
                $hour = date('H', strtotime($error['timestamp']));
                $stats['errors_by_hour'][$hour] = ($stats['errors_by_hour'][$hour] ?? 0) + 1;
            }

            // Most common errors
            $errorMessages = array_count_values(array_column($errors, 'message'));
            arsort($errorMessages);
            $stats['most_common_errors'] = array_slice($errorMessages, 0, 5, true);

            return $stats;
        } catch (Exception $e) {
            Log::error('Failed to get error statistics: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Clear old error records
     */
    public function clearOldErrorRecords(int $daysToKeep = 30): void
    {
        try {
            $cutoffDate = date('Y-m-d', strtotime("-{$daysToKeep} days"));
            
            // Clear old error caches
            for ($i = $daysToKeep + 1; $i <= 365; $i++) {
                $oldDate = date('Y-m-d', strtotime("-{$i} days"));
                $oldKey = 'field_mapping_errors:' . $oldDate;
                cache()->forget($oldKey);
            }
            
        } catch (Exception $e) {
            Log::error('Failed to clear old error records: ' . $e->getMessage());
        }
    }

    /**
     * Handle specific field mapping errors
     */
    public function handleFieldMappingError(
        string $operation,
        array $context,
        ?Throwable $exception = null
    ): array {
        $errorCode = $this->generateErrorCode($operation);
        $message = $this->getErrorMessage($operation, $context);
        
        $this->logError($message, $context, 'error', $exception);
        
        return [
            'success' => false,
            'error_code' => $errorCode,
            'message' => $message,
            'details' => $this->getErrorDetails($operation, $context)
        ];
    }

    /**
     * Generate error code
     */
    private function generateErrorCode(string $operation): string
    {
        $prefix = 'FM'; // Field Mapping
        $operationCode = strtoupper(substr($operation, 0, 3));
        $timestamp = time();
        
        return "{$prefix}-{$operationCode}-{$timestamp}";
    }

    /**
     * Get error message
     */
    private function getErrorMessage(string $operation, array $context): string
    {
        $messages = [
            'field_detection' => 'Field detection failed',
            'save_mappings' => 'Failed to save field mappings',
            'data_preview' => 'Data preview generation failed',
            'data_validation' => 'Data validation failed',
            'batch_processing' => 'Batch processing failed',
            'data_export' => 'Data export failed'
        ];
        
        return $messages[$operation] ?? 'Field mapping operation failed';
    }

    /**
     * Get error details
     */
    private function getErrorDetails(string $operation, array $context): array
    {
        return [
            'operation' => $operation,
            'context' => $context,
            'suggestions' => $this->getErrorSuggestions($operation)
        ];
    }

    /**
     * Get error suggestions
     */
    private function getErrorSuggestions(string $operation): array
    {
        $suggestions = [
            'field_detection' => [
                'Check if the file format is supported',
                'Verify file is not corrupted',
                'Ensure file has readable content'
            ],
            'save_mappings' => [
                'Verify all required fields are filled',
                'Check field type compatibility',
                'Ensure target fields are valid'
            ],
            'data_preview' => [
                'Verify field mappings are correct',
                'Check if sample data exists',
                'Ensure transformation rules are valid'
            ]
        ];
        
        return $suggestions[$operation] ?? ['Please try again or contact support'];
    }
}
