<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Security Configuration
    |--------------------------------------------------------------------------
    |
    | Bu dosya güvenlik ayarlarını içerir.
    |
    */

    /*
    |--------------------------------------------------------------------------
    | Allowed IPs
    |--------------------------------------------------------------------------
    |
    | Production ortamında sadece bu IP'lerden gelen isteklere izin verilir.
    | Boş array tüm IP'lere izin verir (sadece development için).
    |
    */
    'allowed_ips' => [
        // '192.168.1.100',
        // '10.0.0.50',
        // '203.0.113.0/24', // CIDR notation desteklenir
    ],

    /*
    |--------------------------------------------------------------------------
    | Allowed Domains
    |--------------------------------------------------------------------------
    |
    | Sadece bu domain'lerden gelen isteklere izin verilir.
    | Boş array tüm domain'lere izin verir (sadece development için).
    |
    */
    'allowed_domains' => [
        // 'example.com',
        // 'api.example.com',
        // '*.example.com', // Wildcard desteklenir
    ],

    /*
    |--------------------------------------------------------------------------
    | Endpoint Security
    |--------------------------------------------------------------------------
    |
    | Endpoint güvenlik ayarları.
    |
    */
    'endpoint' => [
        'max_redirects' => 3,
        'timeout' => 10, // saniye
        'verify_ssl' => true,
        'user_agent' => 'WidgetBot/1.0',
    ],

    /*
    |--------------------------------------------------------------------------
    | Rate Limiting
    |--------------------------------------------------------------------------
    |
    | Rate limiting ayarları.
    |
    */
    'rate_limiting' => [
        'cargo_tracking' => [
            'max_attempts' => 10,
            'decay_minutes' => 1,
        ],
        'order_tracking' => [
            'max_attempts' => 10,
            'decay_minutes' => 1,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Content Security
    |--------------------------------------------------------------------------
    |
    | İçerik güvenlik ayarları.
    |
    */
    'content' => [
        'max_response_size' => 1024 * 1024, // 1MB
        'allowed_content_types' => [
            'application/json',
            'application/xml',
            'text/plain',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Logging
    |--------------------------------------------------------------------------
    |
    | Güvenlik log ayarları.
    |
    */
    'logging' => [
        'enabled' => true,
        'log_failed_attempts' => true,
        'log_successful_requests' => false,
        'retention_days' => 30,
    ],
];
