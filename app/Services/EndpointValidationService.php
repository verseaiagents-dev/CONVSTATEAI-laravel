<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

class EndpointValidationService
{
    /**
     * Endpoint'i güvenlik açısından doğrula
     */
    public function validateEndpoint(string $endpoint): array
    {
        try {
            // 1. URL format kontrolü
            if (!filter_var($endpoint, FILTER_VALIDATE_URL)) {
                return [
                    'valid' => false,
                    'error' => 'Geçersiz URL formatı',
                    'code' => 'INVALID_URL'
                ];
            }

            // 2. Protocol kontrolü
            $allowedProtocols = $this->getAllowedProtocols();
            $isValidProtocol = false;
            
            foreach ($allowedProtocols as $protocol) {
                if (str_starts_with($endpoint, $protocol)) {
                    $isValidProtocol = true;
                    break;
                }
            }

            if (!$isValidProtocol) {
                return [
                    'valid' => false,
                    'error' => 'İzin verilmeyen protokol. Sadece HTTPS kullanılabilir.',
                    'code' => 'INVALID_PROTOCOL',
                    'allowed_protocols' => $allowedProtocols
                ];
            }

            // 3. Domain kontrolü
            $domain = parse_url($endpoint, PHP_URL_HOST);
            if (!$this->isAllowedDomain($domain)) {
                return [
                    'valid' => false,
                    'error' => 'İzin verilmeyen domain',
                    'code' => 'INVALID_DOMAIN'
                ];
            }

            // 4. Port kontrolü (sadece production'da)
            if (app()->environment('production')) {
                $port = parse_url($endpoint, PHP_URL_PORT);
                if ($port && !in_array($port, [80, 443, 8080, 8443])) {
                    return [
                        'valid' => false,
                        'error' => 'İzin verilmeyen port',
                        'code' => 'INVALID_PORT'
                    ];
                }
            }

            // 5. Endpoint erişilebilirlik kontrolü (opsiyonel)
            if (config('security.endpoint.verify_ssl', true)) {
                $reachability = $this->checkEndpointReachability($endpoint);
                if (!$reachability['reachable']) {
                    return [
                        'valid' => false,
                        'error' => 'Endpoint erişilemiyor: ' . $reachability['error'],
                        'code' => 'ENDPOINT_UNREACHABLE'
                    ];
                }
            }

            return [
                'valid' => true,
                'message' => 'Endpoint güvenli ve erişilebilir',
                'code' => 'VALID'
            ];

        } catch (\Exception $e) {
            Log::error('Endpoint validation error', [
                'endpoint' => $endpoint,
                'error' => $e->getMessage()
            ]);

            return [
                'valid' => false,
                'error' => 'Endpoint doğrulama sırasında hata oluştu',
                'code' => 'VALIDATION_ERROR'
            ];
        }
    }

    /**
     * Tracking number'ı temizle ve doğrula
     */
    public function sanitizeTrackingNumber(string $trackingNumber): string
    {
        // Sadece alfanumerik karakterler ve tire (-) bırak
        $sanitized = preg_replace('/[^A-Z0-9\-]/', '', strtoupper($trackingNumber));
        
        // Maksimum uzunluk kontrolü
        if (strlen($sanitized) > 50) {
            $sanitized = substr($sanitized, 0, 50);
        }

        return $sanitized;
    }

    /**
     * Endpoint'in güvenli olup olmadığını kontrol et
     */
    public function isSecureEndpoint(string $endpoint): bool
    {
        $validation = $this->validateEndpoint($endpoint);
        return $validation['valid'];
    }

    /**
     * Environment'a göre izin verilen protokolleri döndür
     */
    private function getAllowedProtocols(): array
    {
        $environment = app()->environment();
        
        switch ($environment) {
            case 'production':
                return ['https://'];
                
            case 'staging':
                return [
                    'https://',
                    'http://localhost:3000',
                    'http://127.0.0.1:3000'
                ];
                
            case 'local':
            case 'testing':
                return [
                    'https://',
                    'http://localhost',
                    'http://127.0.0.1',
                    'http://localhost:3000',
                    'http://127.0.0.1:3000',
                    'http://localhost:8000',
                    'http://127.0.0.1:8000'
                ];
                
            default:
                return ['https://'];
        }
    }

    /**
     * Domain'in izin verilen domain'ler arasında olup olmadığını kontrol et
     */
    private function isAllowedDomain(string $domain): bool
    {
        $allowedDomains = config('security.allowed_domains', []);
        
        // Eğer allowed domains boşsa, tüm domain'lere izin ver (sadece development)
        if (empty($allowedDomains)) {
            return app()->environment(['local', 'testing']);
        }

        foreach ($allowedDomains as $allowedDomain) {
            // Wildcard kontrolü
            if (str_contains($allowedDomain, '*')) {
                $pattern = str_replace('*', '.*', $allowedDomain);
                if (preg_match('/^' . $pattern . '$/i', $domain)) {
                    return true;
                }
            } else {
                // Tam eşleşme
                if (strcasecmp($domain, $allowedDomain) === 0) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Endpoint'in erişilebilir olup olmadığını kontrol et
     */
    private function checkEndpointReachability(string $endpoint): array
    {
        try {
            $response = Http::timeout(5)
                ->withHeaders([
                    'User-Agent' => config('security.endpoint.user_agent', 'WidgetBot/1.0')
                ])
                ->get($endpoint);

            if ($response->successful()) {
                return [
                    'reachable' => true,
                    'status_code' => $response->status()
                ];
            } else {
                return [
                    'reachable' => false,
                    'error' => 'HTTP ' . $response->status(),
                    'status_code' => $response->status()
                ];
            }

        } catch (\Exception $e) {
            return [
                'reachable' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Endpoint'i güvenli hale getir
     */
    public function sanitizeEndpoint(string $endpoint): string
    {
        // URL'yi parse et
        $parsed = parse_url($endpoint);
        
        if (!$parsed) {
            return '';
        }

        // Sadece güvenli protokolleri kabul et
        $allowedProtocols = $this->getAllowedProtocols();
        $protocol = $parsed['scheme'] ?? 'https';
        
        if (!in_array($protocol . '://', $allowedProtocols)) {
            $protocol = 'https';
        }

        // Domain'i temizle
        $host = $parsed['host'] ?? '';
        $host = preg_replace('/[^a-zA-Z0-9\.\-]/', '', $host);

        // Port kontrolü
        $port = $parsed['port'] ?? '';
        if ($port && !in_array($port, [80, 443, 8080, 8443])) {
            $port = '';
        }

        // Path'i temizle
        $path = $parsed['path'] ?? '/';
        $path = preg_replace('/[^a-zA-Z0-9\/\-_\.]/', '', $path);

        // Query string'i temizle
        $query = $parsed['query'] ?? '';
        if ($query) {
            $query = '?' . preg_replace('/[^a-zA-Z0-9=&\-_\.]/', '', $query);
        }

        // URL'yi yeniden oluştur
        $sanitized = $protocol . '://' . $host;
        if ($port) {
            $sanitized .= ':' . $port;
        }
        $sanitized .= $path . $query;

        return $sanitized;
    }
}
