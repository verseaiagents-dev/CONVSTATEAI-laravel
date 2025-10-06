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
     * Domain'in geçerli olup olmadığını kontrol et
     */
    public function isValidDomain(): bool
    {
        if (empty($this->url)) {
            return false;
        }

        $url = trim($this->url);
        
        // URL'yi parse et
        $parsed = parse_url($url);
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

        // URL boş olamaz
        if (empty($this->url)) {
            return false;
        }

        return true;
    }
}
