<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\Project;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class CustomCorsMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // CORS Switch kontrolü - Eğer CORS devre dışıysa sadece request'i geçir
        if (!$this->isCorsEnabled()) {
            return $next($request);
        }

        // Handle preflight OPTIONS request
        if ($request->isMethod('OPTIONS')) {
            $response = response('', 200);
        } else {
            $response = $next($request);
        }

        // CORS headers - Environment-based configuration
        $origin = $request->headers->get('Origin');
        $environment = app()->environment();
        
        // Environment'a göre allowed origins belirle
        $allowedOrigins = $this->getAllowedOrigins($environment);
        
        // Debug için log ekle
        Log::debug('CORS check', [
            'origin' => $origin,
            'environment' => $environment,
            'allowed_origins' => $allowedOrigins,
            'is_allowed' => in_array($origin, $allowedOrigins),
            'cors_enabled' => $this->isCorsEnabled()
        ]);
        
        if ($environment === 'local') {
            // Local modunda tüm originlere izin ver
            $response->headers->set('Access-Control-Allow-Origin', '*');
        } elseif (in_array($origin, $allowedOrigins)) {
            $response->headers->set('Access-Control-Allow-Origin', $origin);
        } elseif ($environment === 'development') {
            // Development ortamında tüm originlere izin ver
            $response->headers->set('Access-Control-Allow-Origin', '*');
        } else {
            // Production'da sadece belirli originlere izin ver
            $response->headers->set('Access-Control-Allow-Origin', 'null');
        }

        $response->headers->set('Access-Control-Allow-Methods', 'GET, POST, PUT, PATCH, DELETE, OPTIONS');
        $response->headers->set('Access-Control-Allow-Headers', 'Content-Type, Accept, Authorization, X-Requested-With, Origin, X-CSRF-TOKEN, X-Browser-UUID, X-Project-ID, X-IP-Based-Session, X-Session-ID, X-User-Agent');
        $response->headers->set('Access-Control-Allow-Credentials', 'true');
        $response->headers->set('Access-Control-Max-Age', '86400');

        return $response;
    }

    /**
     * CORS'un aktif olup olmadığını kontrol et
     */
    private function isCorsEnabled(): bool
    {
        // Environment variable'dan kontrol et (öncelikli)
        $envCorsEnabled = env('CORS_ENABLED');
        if ($envCorsEnabled !== null) {
            return filter_var($envCorsEnabled, FILTER_VALIDATE_BOOLEAN);
        }

        // Config dosyasından kontrol et
        $configCorsEnabled = config('cors.enabled');
        if ($configCorsEnabled !== null) {
            return $configCorsEnabled;
        }

        // Varsayılan olarak aktif (mevcut davranışı koru)
        return true;
    }

    /**
     * Environment'a göre allowed origins döndür
     */
    private function getAllowedOrigins(string $environment): array
    {
        // Environment'dan CORS_ALLOWED_ORIGINS'ı al
        $envOrigins = env('CORS_ALLOWED_ORIGINS') ? explode(',', env('CORS_ALLOWED_ORIGINS')) : [];
        
        switch ($environment) {
            case 'local':
                // Local modunda tüm originlere izin ver
                return ['*'];
               
            case 'development':
                $defaultOrigins = [
                    'http://localhost:3000',
                    'http://127.0.0.1:3000',
                    'http://192.168.1.100:3000',
                    'http://localhost:3001',
                    'http://127.0.0.1:3001',
                    'http://localhost:8000',
                    'http://127.0.0.1:8000',
                    'http://localhost:8001',
                    'http://127.0.0.1:8001',
                    'http://localhost:8080',
                    'http://127.0.0.1:8080',
                    'https://localhost:3000',
                    'https://127.0.0.1:3000',
                    'https://192.168.1.100:3000',
                    'http://127.0.0.1:3000/widget/test.html',
                    'https://localhost:3001',
                    'https://127.0.0.1:3001',
                    'https://localhost:8000',
                    'https://localhost:8080',
                    'https://127.0.0.1:8000',
                    'https://127.0.0.1:8001',
                    'https://127.0.0.1:5500',
                    'https://127.0.0.1:8080'
                ];
                
                // Environment'dan gelen originleri ekle
                return array_unique(array_merge($defaultOrigins, $envOrigins));
                
            case 'staging':
                return [
                    env('FRONTEND_URL', 'https://staging.yourdomain.com'),
                    env('WIDGET_URL', 'https://widget-staging.yourdomain.com'),
                    'http://localhost:3000', // Sadece React dev server
                    'http://127.0.0.1:3000',
                    'http://localhost:8080',
                    'http://127.0.0.1:8080'
                ];
                
            case 'production':
                return $this->getProductionAllowedOrigins();
                
            default:
                return [];
        }
    }

    /**
     * Production ortamında Project tablosundan allowed origins döndür
     */
    private function getProductionAllowedOrigins(): array
    {
        // Cache'den kontrol et (5 dakika cache)
        $cacheKey = 'cors_allowed_origins_production';
        $cachedOrigins = Cache::get($cacheKey);
        
        if ($cachedOrigins !== null) {
            return $cachedOrigins;
        }

        // Project tablosundan aktif projelerin URL'lerini al
        $origins = [];
        
        try {
            $projects = Project::where('status', 'active')
                ->whereNotNull('url')
                ->where('url', '!=', '')
                ->get();

            foreach ($projects as $project) {
                // Project model'inin validation method'unu kullan
                if ($project->isAllowedForCors()) {
                    $url = trim($project->url);
                    
                    // Kullanıcının girdiği URL'yi tam olarak olduğu gibi kullan
                    if (!empty($url)) {
                        $origins[] = $url;
                    }
                }
            }

            // Environment'dan gelen ek URL'leri ekle
            $envOrigins = array_filter([
                env('FRONTEND_URL'),
                env('WIDGET_URL'),
                env('CORS_ALLOWED_ORIGINS') ? explode(',', env('CORS_ALLOWED_ORIGINS')) : []
            ]);
            
            // Local development için localhost originleri ekle
            $localhostOrigins = [
                'http://localhost:3000',
                'http://127.0.0.1:3000',
                'http://localhost:8080',
                'http://127.0.0.1:8080',
                'http://localhost:8000',
                'http://127.0.0.1:8000'
            ];
            
            // array_flatten yerine array_merge kullan
            $flatEnvOrigins = [];
            foreach ($envOrigins as $origin) {
                if (is_array($origin)) {
                    $flatEnvOrigins = array_merge($flatEnvOrigins, $origin);
                } else {
                    $flatEnvOrigins[] = $origin;
                }
            }
            
            $origins = array_merge($origins, $flatEnvOrigins, $localhostOrigins);
            $origins = array_unique(array_filter($origins));

            // Cache'e kaydet (5 dakika)
            Cache::put($cacheKey, $origins, 300);
            
            return $origins;
            
        } catch (\Exception $e) {
            // Database hatası durumunda güvenli varsayılan
            \Log::error('CORS: Database error while fetching allowed origins', [
                'error' => $e->getMessage()
            ]);
            
            return array_filter([
                env('FRONTEND_URL'),
                env('WIDGET_URL'),
                'http://localhost:3000',
                'http://127.0.0.1:3000',
                'http://localhost:8080',
                'http://127.0.0.1:8080',
                'http://localhost:8000',
                'http://127.0.0.1:8000'
            ]);
        }
    }

    /**
     * URL'yi normalize et (protocol ekle, www kontrolü vs.)
     */
    private function normalizeUrl(string $url): ?string
    {
        $url = trim($url);
        
        if (empty($url)) {
            return null;
        }

        // Protocol yoksa https ekle
        if (!preg_match('/^https?:\/\//', $url)) {
            $url = 'https://' . $url;
        }

        // URL'yi parse et
        $parsed = parse_url($url);
        if (!$parsed || !isset($parsed['host'])) {
            return null;
        }

        // Host'u temizle
        $host = strtolower(trim($parsed['host']));
        if (empty($host)) {
            return null;
        }

        // www. prefix'ini kaldır (opsiyonel)
        if (strpos($host, 'www.') === 0) {
            $host = substr($host, 4);
        }

        // Protocol + host döndür
        $protocol = isset($parsed['scheme']) ? $parsed['scheme'] : 'https';
        return $protocol . '://' . $host;
    }
}
