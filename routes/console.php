<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Schedule tanımları
Schedule::command('knowledge-base:retry-stuck --minutes=10')
    ->everyTenMinutes()
    ->withoutOverlapping()
    ->runInBackground();

// Her gün saat 02:00'da log dosyalarını temizle
Schedule::call(function () {
    $logPath = storage_path('logs');
    $files = glob($logPath . '/*.log');
    $cutoff = now()->subDays(7);
    
    foreach ($files as $file) {
        if (filemtime($file) < $cutoff->timestamp) {
            unlink($file);
        }
    }
})->dailyAt('02:00');

// Her gün saat 03:00'da cache'i temizle
Schedule::command('cache:clear')->dailyAt('03:00');
