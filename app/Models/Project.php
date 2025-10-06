<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Project extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'url',
        'status',
        'is_featured',
        'created_by',
        'knowledge_list',
        'project_token'
    ];

    protected $casts = [
        'knowledge_list' => 'array',
        'is_featured' => 'boolean',
    ];

    /**
     * Model event'leri
     */
    protected static function booted()
    {
        // Project güncellendiğinde CORS cache'ini temizle
        static::updated(function ($project) {
            if ($project->isDirty(['url', 'status'])) {
                Cache::forget('cors_allowed_origins_production');
            }
        });

        // Project oluşturulduğunda CORS cache'ini temizle
        static::created(function ($project) {
            Cache::forget('cors_allowed_origins_production');
        });

        // Project silindiğinde CORS cache'ini temizle
        static::deleted(function ($project) {
            Cache::forget('cors_allowed_origins_production');
        });
    }

    /**
     * Projeyi oluşturan kullanıcı
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Projeye bağlı chat session'ları
     */
    public function chatSessions()
    {
        return $this->hasMany(EnhancedChatSession::class);
    }

    /**
     * Projeye bağlı knowledge base'ler
     */
    public function knowledgeBases()
    {
        return $this->hasMany(KnowledgeBase::class);
    }

    /**
     * Status badge rengi
     */
    public function getStatusColorAttribute()
    {
        return match($this->status) {
            'active' => 'green',
            'inactive' => 'gray',
            'completed' => 'blue',
            'archived' => 'red',
            default => 'gray'
        };
    }

    /**
     * Status badge metni
     */
    public function getStatusTextAttribute()
    {
        return match($this->status) {
            'active' => 'Aktif',
            'inactive' => 'Pasif',
            'completed' => 'Tamamlandı',
            'archived' => 'Arşivlendi',
            default => 'Bilinmiyor'
        };
    }

    /**
     * Domain'i normalize et ve doğrula
     */
    public function getNormalizedUrlAttribute(): ?string
    {
        if (empty($this->url)) {
            return null;
        }

        $url = trim($this->url);
        
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

    /**
     * Domain'in geçerli olup olmadığını kontrol et
     */
    public function isValidDomain(): bool
    {
        $normalizedUrl = $this->getNormalizedUrlAttribute();
        if (!$normalizedUrl) {
            return false;
        }

        $parsed = parse_url($normalizedUrl);
        if (!$parsed || !isset($parsed['host'])) {
            return false;
        }

        $host = $parsed['host'];
        
        // Basit domain validation
        return filter_var($host, FILTER_VALIDATE_DOMAIN, FILTER_FLAG_HOSTNAME) !== false;
    }

    /**
     * CORS için güvenli domain kontrolü
     */
    public function isAllowedForCors(): bool
    {
        if (!$this->isValidDomain()) {
            return false;
        }

        // Sadece aktif projeler CORS'a izin verir
        if ($this->status !== 'active') {
            return false;
        }

        // URL boş olamaz
        if (empty($this->url)) {
            return false;
        }

        return true;
    }

    /**
     * URL'nin erişilebilir olup olmadığını test et
     */
    public function testUrlAccessibility(): array
    {
        if (empty($this->url)) {
            return [
                'success' => false,
                'message' => 'URL boş olamaz',
                'status_code' => null,
                'response_time' => null
            ];
        }

        $url = trim($this->url);
        
        // Protocol yoksa https ekle
        if (!preg_match('/^https?:\/\//', $url)) {
            $url = 'https://' . $url;
        }

        $startTime = microtime(true);
        
        try {
            // cURL ile HEAD request gönder (daha hızlı)
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_NOBODY, true); // HEAD request
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 10); // 10 saniye timeout
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // SSL doğrulamasını atla
            curl_setopt($ch, CURLOPT_USERAGENT, 'ConvStateAI/1.0');
            
            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $error = curl_error($ch);
            curl_close($ch);
            
            $endTime = microtime(true);
            $responseTime = round(($endTime - $startTime) * 1000, 2); // milisaniye
            
            if ($error) {
                return [
                    'success' => false,
                    'message' => 'cURL hatası: ' . $error,
                    'status_code' => null,
                    'response_time' => $responseTime,
                    'url_tested' => $url
                ];
            }
            
            // 200-299 arası başarılı sayılır
            if ($httpCode >= 200 && $httpCode < 300) {
                return [
                    'success' => true,
                    'message' => 'URL erişilebilir',
                    'status_code' => $httpCode,
                    'response_time' => $responseTime,
                    'url_tested' => $url
                ];
            } else {
                return [
                    'success' => false,
                    'message' => 'HTTP ' . $httpCode . ' hatası',
                    'status_code' => $httpCode,
                    'response_time' => $responseTime,
                    'url_tested' => $url
                ];
            }
            
        } catch (\Exception $e) {
            $endTime = microtime(true);
            $responseTime = round(($endTime - $startTime) * 1000, 2);
            
            return [
                'success' => false,
                'message' => 'Test hatası: ' . $e->getMessage(),
                'status_code' => null,
                'response_time' => $responseTime,
                'url_tested' => $url
            ];
        }
    }

    /**
     * URL'yi test et ve sonucu kaydet
     */
    public function testAndSaveUrl(): array
    {
        $testResult = $this->testUrlAccessibility();
        
        // Test sonucunu logla
        \Log::info('Project URL Test', [
            'project_id' => $this->id,
            'project_name' => $this->name,
            'url' => $this->url,
            'test_result' => $testResult
        ]);
        
        return $testResult;
    }

    /**
     * CORS için tüm URL varyasyonlarını döndür
     */
    public function getAllCorsUrls(): array
    {
        if (empty($this->url)) {
            return [];
        }

        $urls = [];
        $originalUrl = trim($this->url);
        
        // Orijinal URL'yi ekle
        $urls[] = $originalUrl;
        
        // Protocol ekleme
        if (!preg_match('/^https?:\/\//', $originalUrl)) {
            $urls[] = 'https://' . $originalUrl;
            $urls[] = 'http://' . $originalUrl;
        }
        
        // www. varyasyonları
        $parsed = parse_url($originalUrl);
        if ($parsed && isset($parsed['host'])) {
            $host = $parsed['host'];
            $protocol = isset($parsed['scheme']) ? $parsed['scheme'] : 'https';
            
            if (strpos($host, 'www.') === 0) {
                // www. ile başlıyorsa www. olmadan versiyonu ekle
                $withoutWww = substr($host, 4);
                $urls[] = $protocol . '://' . $withoutWww;
            } else {
                // www. ile başlamıyorsa www. ile versiyonu ekle
                $urls[] = $protocol . '://www.' . $host;
            }
        }
        
        return array_unique(array_filter($urls));
    }
}
