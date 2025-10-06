<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Sadece gerekli middleware'leri tanımla
        $middleware->web([
            \Illuminate\Session\Middleware\StartSession::class,
            \Illuminate\Foundation\Http\Middleware\ValidateCsrfToken::class,
            \App\Http\Middleware\SetLocale::class,
            \App\Http\Middleware\CustomCorsMiddleware::class,
        ]);
        
        // API middleware'leri - Custom CORS middleware ekle
        $middleware->api([
            \App\Http\Middleware\CustomCorsMiddleware::class,
        ]);
        
        // Laravel'in varsayılan CORS middleware'ini devre dışı bırak
       // $middleware->remove(\Illuminate\Http\Middleware\HandleCors::class);
        
        // Middleware alias'ları
        $middleware->alias([
            'admin' => \App\Http\Middleware\AdminMiddleware::class,
            'subscription' => \App\Http\Middleware\SubscriptionMiddleware::class,
            'project.auth' => \App\Http\Middleware\ProjectAuth::class,
            'usage.token' => \App\Http\Middleware\UsageTokenMiddleware::class,
            'api.key' => \App\Http\Middleware\ApiKeyMiddleware::class,
            'token.check' => \App\Http\Middleware\TokenCheckMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
