<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\App;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Set remember me cookie lifetime to 90 days
        Cookie::queue('remember_web', null, 90 * 24 * 60); // 90 days in minutes
        
        // Force Turkish language for all users temporarily
        App::setLocale('tr');
        
        // Set JSON encoding options for UTF-8 support
        if (function_exists('json_encode')) {
            ini_set('default_charset', 'UTF-8');
        }
    }
}
