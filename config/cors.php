<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Cross-Origin Resource Sharing (CORS) Configuration
    |--------------------------------------------------------------------------
    |
    | Here you may configure your settings for cross-origin resource sharing
    | or "CORS". This determines what cross-origin operations may execute
    | in web browsers. You are free to adjust these settings as needed.
    |
    | To learn more: https://developer.mozilla.org/en-US/docs/Web/HTTP/CORS
    |
    */

    /*
    |--------------------------------------------------------------------------
    | CORS Switch
    |--------------------------------------------------------------------------
    |
    | This setting allows you to enable or disable CORS functionality entirely.
    | When disabled, no CORS headers will be added to responses.
    | You can also control this via CORS_ENABLED environment variable.
    |
    */
    'enabled' => env('CORS_ENABLED', true),

    'paths' => ['api/*', 'sanctum/csrf-cookie', 'widgetcust/*'],

    'allowed_methods' => ['GET', 'POST', 'PUT', 'PATCH', 'DELETE', 'OPTIONS'],

    'allowed_origins' => [
        // Development origins - sadece development ortamında aktif
        ...(env('APP_ENV') === 'local' || env('APP_ENV') === 'development' ? [
            'http://localhost:3000',
            'http://127.0.0.1:3000',
            'http://localhost:3001',
            'http://127.0.0.1:3001',
            'http://localhost:8000',
            'http://127.0.0.1:8000',
            'http://localhost:8001',
            'http://127.0.0.1:8001',
            'http://localhost:8080',
            'http://127.0.0.1:8080'
        ] : []),
        
        // Production origins - sadece production ortamında aktif
        ...(env('APP_ENV') === 'production' ? [
            'https://convstateai.com',
            '*' // Tüm domainlerden widget erişimine izin ver
        ] : []),
        
        // Custom allowed origins from environment
        ...(env('CORS_ALLOWED_ORIGINS') ? explode(',', env('CORS_ALLOWED_ORIGINS')) : [])
    ],

    'allowed_origins_patterns' => [],

    'allowed_headers' => [
        'Content-Type',
        'Accept',
        'Authorization',
        'X-Requested-With',
        'Origin',
        'X-CSRF-TOKEN',
        'X-Browser-UUID',
        'X-Project-ID',
        'X-IP-Based-Session',
        'X-Session-ID',
        'X-User-Agent'
    ],

    'exposed_headers' => [],

    'max_age' => 0,

    'supports_credentials' => false,

];
