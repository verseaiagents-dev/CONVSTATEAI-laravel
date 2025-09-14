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
}
