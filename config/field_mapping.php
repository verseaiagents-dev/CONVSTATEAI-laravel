<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Field Mapping Configuration
    |--------------------------------------------------------------------------
    |
    | This file contains configuration options for the field mapping system
    | including cache settings, rate limits, and notification preferences.
    |
    */

    /*
    |--------------------------------------------------------------------------
    | Cache Configuration
    |--------------------------------------------------------------------------
    */
    'cache' => [
        'enabled' => env('FIELD_MAPPING_CACHE_ENABLED', true),
        'ttl' => env('FIELD_MAPPING_CACHE_TTL', 3600), // 1 hour in seconds
        'prefix' => env('FIELD_MAPPING_CACHE_PREFIX', 'field_mapping'),
        'channels' => [
            'detection' => env('FIELD_MAPPING_CACHE_CHANNEL_DETECTION', 'redis'),
            'mappings' => env('FIELD_MAPPING_CACHE_CHANNEL_MAPPINGS', 'redis'),
            'transformations' => env('FIELD_MAPPING_CACHE_CHANNEL_TRANSFORMATIONS', 'redis'),
            'validations' => env('FIELD_MAPPING_CACHE_CHANNEL_VALIDATIONS', 'redis'),
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Rate Limiting Configuration
    |--------------------------------------------------------------------------
    */
    'rate_limiting' => [
        'enabled' => env('FIELD_MAPPING_RATE_LIMITING_ENABLED', true),
        'default' => [
            'max_attempts' => env('FIELD_MAPPING_RATE_LIMIT_DEFAULT', 20),
            'decay_minutes' => env('FIELD_MAPPING_RATE_LIMIT_DECAY', 1),
        ],
        'operations' => [
            'field_detection' => [
                'max_attempts' => env('FIELD_MAPPING_RATE_LIMIT_DETECTION', 10),
                'decay_minutes' => env('FIELD_MAPPING_RATE_LIMIT_DETECTION_DECAY', 1),
            ],
            'save_mappings' => [
                'max_attempts' => env('FIELD_MAPPING_RATE_LIMIT_SAVE', 20),
                'decay_minutes' => env('FIELD_MAPPING_RATE_LIMIT_SAVE_DECAY', 1),
            ],
            'data_preview' => [
                'max_attempts' => env('FIELD_MAPPING_RATE_LIMIT_PREVIEW', 30),
                'decay_minutes' => env('FIELD_MAPPING_RATE_LIMIT_PREVIEW_DECAY', 1),
            ],
            'data_validation' => [
                'max_attempts' => env('FIELD_MAPPING_RATE_LIMIT_VALIDATION', 25),
                'decay_minutes' => env('FIELD_MAPPING_RATE_LIMIT_VALIDATION_DECAY', 1),
            ],
            'batch_processing' => [
                'max_attempts' => env('FIELD_MAPPING_RATE_LIMIT_BATCH', 5),
                'decay_minutes' => env('FIELD_MAPPING_RATE_LIMIT_BATCH_DECAY', 5),
            ],
            'data_export' => [
                'max_attempts' => env('FIELD_MAPPING_RATE_LIMIT_EXPORT', 15),
                'decay_minutes' => env('FIELD_MAPPING_RATE_LIMIT_EXPORT_DECAY', 1),
            ],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Error Handling Configuration
    |--------------------------------------------------------------------------
    */
    'error_handling' => [
        'notification_level' => env('FIELD_MAPPING_ERROR_LEVEL', 'critical'),
        'log_levels' => [
            'critical' => 'error_log',
            'error' => 'error_log',
            'warning' => 'warning_log',
            'info' => 'daily',
            'debug' => 'daily',
        ],
        'store_errors' => env('FIELD_MAPPING_STORE_ERRORS', true),
        'error_retention_days' => env('FIELD_MAPPING_ERROR_RETENTION', 30),
    ],

    /*
    |--------------------------------------------------------------------------
    | Notification Configuration
    |--------------------------------------------------------------------------
    */
    'notifications' => [
        'email' => [
            'enabled' => env('FIELD_MAPPING_EMAIL_NOTIFICATIONS', true),
            'recipients' => explode(',', env('FIELD_MAPPING_EMAIL_RECIPIENTS', 'admin@example.com')),
            'template' => env('FIELD_MAPPING_EMAIL_TEMPLATE', 'emails.field-mapping-error'),
        ],
        'slack' => [
            'enabled' => env('FIELD_MAPPING_SLACK_NOTIFICATIONS', false),
            'webhook_url' => env('FIELD_MAPPING_SLACK_WEBHOOK_URL'),
            'channel' => env('FIELD_MAPPING_SLACK_CHANNEL', '#alerts'),
        ],
        'in_app' => [
            'enabled' => env('FIELD_MAPPING_IN_APP_NOTIFICATIONS', true),
            'recipients' => ['admin', 'super_admin'], // User roles
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | File Processing Configuration
    |--------------------------------------------------------------------------
    */
    'file_processing' => [
        'max_file_size' => env('FIELD_MAPPING_MAX_FILE_SIZE', 10485760), // 10MB in bytes
        'supported_formats' => [
            'csv' => ['delimiter' => ',', 'encoding' => 'UTF-8'],
            'xlsx' => ['sheet' => 0, 'header_row' => 0],
            'xls' => ['sheet' => 0, 'header_row' => 0],
            'json' => ['max_depth' => 10],
            'xml' => ['max_depth' => 10, 'encoding' => 'UTF-8'],
        ],
        'chunk_size' => env('FIELD_MAPPING_CHUNK_SIZE', 100),
        'max_rows_preview' => env('FIELD_MAPPING_MAX_PREVIEW_ROWS', 1000),
        'timeout' => env('FIELD_MAPPING_PROCESSING_TIMEOUT', 300), // 5 minutes
    ],

    /*
    |--------------------------------------------------------------------------
    | Field Detection Configuration
    |--------------------------------------------------------------------------
    */
    'field_detection' => [
        'confidence_threshold' => env('FIELD_MAPPING_CONFIDENCE_THRESHOLD', 0.7),
        'max_suggestions' => env('FIELD_MAPPING_MAX_SUGGESTIONS', 5),
        'fuzzy_matching' => [
            'enabled' => env('FIELD_MAPPING_FUZZY_MATCHING', true),
            'algorithm' => env('FIELD_MAPPING_FUZZY_ALGORITHM', 'levenshtein'),
            'threshold' => env('FIELD_MAPPING_FUZZY_THRESHOLD', 0.8),
        ],
        'ai_suggestions' => [
            'enabled' => env('FIELD_MAPPING_AI_SUGGESTIONS', false),
            'model' => env('FIELD_MAPPING_AI_MODEL', 'gpt-3.5-turbo'),
            'max_tokens' => env('FIELD_MAPPING_AI_MAX_TOKENS', 100),
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Transformation Rules Configuration
    |--------------------------------------------------------------------------
    */
    'transformations' => [
        'currency' => [
            'enabled' => env('FIELD_MAPPING_CURRENCY_CONVERSION', true),
            'default_rate' => env('FIELD_MAPPING_DEFAULT_CURRENCY_RATE', 30.5),
            'supported_currencies' => ['USD', 'EUR', 'GBP', 'TRY', 'JPY', 'CNY'],
            'update_frequency' => env('FIELD_MAPPING_CURRENCY_UPDATE_FREQ', 'daily'),
        ],
        'date' => [
            'enabled' => env('FIELD_MAPPING_DATE_CONVERSION', true),
            'default_format' => env('FIELD_MAPPING_DEFAULT_DATE_FORMAT', 'Y-m-d'),
            'supported_formats' => [
                'Y-m-d', 'd/m/Y', 'm/d/Y', 'Y/m/d', 'd-m-Y', 'm-d-Y',
                'Y-m-d H:i:s', 'd/m/Y H:i', 'm/d/Y H:i'
            ],
            'timezone' => env('FIELD_MAPPING_TIMEZONE', 'UTC'),
        ],
        'text' => [
            'enabled' => env('FIELD_MAPPING_TEXT_PROCESSING', true),
            'max_length' => env('FIELD_MAPPING_TEXT_MAX_LENGTH', 1000),
            'strip_html' => env('FIELD_MAPPING_STRIP_HTML', true),
            'normalize_whitespace' => env('FIELD_MAPPING_NORMALIZE_WHITESPACE', true),
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Validation Rules Configuration
    |--------------------------------------------------------------------------
    */
    'validation' => [
        'strict_mode' => env('FIELD_MAPPING_STRICT_VALIDATION', false),
        'custom_rules' => [
            'enabled' => env('FIELD_MAPPING_CUSTOM_VALIDATION', true),
            'max_rules_per_field' => env('FIELD_MAPPING_MAX_VALIDATION_RULES', 10),
        ],
        'default_rules' => [
            'text' => ['min_length' => 1, 'max_length' => 255],
            'number' => ['min_value' => null, 'max_value' => null],
            'date' => ['min_date' => null, 'max_date' => null],
            'email' => ['format' => 'email'],
            'url' => ['format' => 'url'],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Performance Configuration
    |--------------------------------------------------------------------------
    */
    'performance' => [
        'batch_size' => env('FIELD_MAPPING_BATCH_SIZE', 100),
        'max_concurrent_jobs' => env('FIELD_MAPPING_MAX_CONCURRENT_JOBS', 5),
        'memory_limit' => env('FIELD_MAPPING_MEMORY_LIMIT', '512M'),
        'timeout' => env('FIELD_MAPPING_JOB_TIMEOUT', 300),
        'retry_attempts' => env('FIELD_MAPPING_RETRY_ATTEMPTS', 3),
        'retry_delay' => env('FIELD_MAPPING_RETRY_DELAY', 60),
    ],

    /*
    |--------------------------------------------------------------------------
    | Security Configuration
    |--------------------------------------------------------------------------
    */
    'security' => [
        'input_sanitization' => env('FIELD_MAPPING_INPUT_SANITIZATION', true),
        'sql_injection_protection' => env('FIELD_MAPPING_SQL_PROTECTION', true),
        'xss_protection' => env('FIELD_MAPPING_XSS_PROTECTION', true),
        'file_upload_validation' => env('FIELD_MAPPING_FILE_VALIDATION', true),
        'max_file_uploads_per_hour' => env('FIELD_MAPPING_MAX_UPLOADS_PER_HOUR', 10),
    ],

    /*
    |--------------------------------------------------------------------------
    | Monitoring Configuration
    |--------------------------------------------------------------------------
    */
    'monitoring' => [
        'enabled' => env('FIELD_MAPPING_MONITORING', true),
        'metrics' => [
            'processing_time' => true,
            'success_rate' => true,
            'error_rate' => true,
            'cache_hit_rate' => true,
            'memory_usage' => true,
        ],
        'alerts' => [
            'error_threshold' => env('FIELD_MAPPING_ERROR_ALERT_THRESHOLD', 10),
            'performance_threshold' => env('FIELD_MAPPING_PERFORMANCE_ALERT_THRESHOLD', 30),
            'memory_threshold' => env('FIELD_MAPPING_MEMORY_ALERT_THRESHOLD', 80),
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Development Configuration
    |--------------------------------------------------------------------------
    */
    'development' => [
        'debug_mode' => env('FIELD_MAPPING_DEBUG', false),
        'log_queries' => env('FIELD_MAPPING_LOG_QUERIES', false),
        'show_errors' => env('FIELD_MAPPING_SHOW_ERRORS', false),
        'performance_profiling' => env('FIELD_MAPPING_PROFILING', false),
    ],
];
