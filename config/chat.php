<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Chat Session Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration options for chat sessions, rate limiting, and security
    |
    */

    'rate_limit' => [
        'max_attempts' => env('CHAT_RATE_LIMIT_MAX_ATTEMPTS', 100),
        'decay_minutes' => env('CHAT_RATE_LIMIT_DECAY_MINUTES', 1),
    ],

    'session' => [
        'default_daily_limit' => env('CHAT_SESSION_DAILY_LIMIT', 20),
        'ip_daily_limit' => env('CHAT_IP_DAILY_LIMIT', 50),
        'use_ip_based_sessions' => env('CHAT_USE_IP_BASED_SESSIONS', true),
        'expiration_days' => env('CHAT_SESSION_EXPIRATION_DAYS', 7),
        'max_concurrent_sessions' => env('CHAT_MAX_CONCURRENT_SESSIONS', 5),
    ],

    'security' => [
        'encrypt_sensitive_data' => env('CHAT_ENCRYPT_SENSITIVE_DATA', true),
        'audit_logging' => env('CHAT_AUDIT_LOGGING', true),
        'gdpr_compliance' => env('CHAT_GDPR_COMPLIANCE', true),
    ],

    'gdpr' => [
        'data_retention' => [
            'active_sessions' => env('GDPR_ACTIVE_SESSIONS_RETENTION', 30), // days
            'completed_sessions' => env('GDPR_COMPLETED_SESSIONS_RETENTION', 90), // days
            'expired_sessions' => env('GDPR_EXPIRED_SESSIONS_RETENTION', 7), // days
            'interactions' => env('GDPR_INTERACTIONS_RETENTION', 730), // days
            'audit_logs' => env('GDPR_AUDIT_LOGS_RETENTION', 365), // days
        ],
        'auto_cleanup' => env('GDPR_AUTO_CLEANUP', true),
        'cleanup_frequency' => env('GDPR_CLEANUP_FREQUENCY', 'daily'), // daily, weekly, monthly
    ],

    'audit' => [
        'log_channel' => env('CHAT_AUDIT_LOG_CHANNEL', 'audit'),
        'log_level' => env('CHAT_AUDIT_LOG_LEVEL', 'info'),
        'retain_logs_for_days' => env('CHAT_AUDIT_LOG_RETENTION', 365),
    ],

    'encryption' => [
        'enabled' => env('CHAT_ENCRYPTION_ENABLED', true),
        'algorithm' => env('CHAT_ENCRYPTION_ALGORITHM', 'AES-256-CBC'),
        'key_rotation' => env('CHAT_ENCRYPTION_KEY_ROTATION', false),
    ],
];
