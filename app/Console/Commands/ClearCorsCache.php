<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;

class ClearCorsCache extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cors:clear-cache';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clear CORS allowed origins cache';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $cacheKey = 'cors_allowed_origins_production';
        
        if (Cache::has($cacheKey)) {
            Cache::forget($cacheKey);
            $this->info('CORS cache cleared successfully.');
        } else {
            $this->info('CORS cache was already empty.');
        }
        
        return 0;
    }
}
