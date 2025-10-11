<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Carbon\Carbon;

class EnhancedChatSession extends Model
{
    use HasFactory;

    protected $fillable = [
        'session_id',
        'ip_address',
        'user_agent',
        'user_id',
        'project_id',
        'intent_history',
        'chat_history',
        'daily_view_count',
        'daily_view_limit',
        'last_activity',
        'user_preferences',
        'product_interactions',
        'status',
        'expires_at'
    ];

    protected $casts = [
        'intent_history' => 'array',
        'chat_history' => 'array',
        'user_preferences' => 'array',
        'product_interactions' => 'array',
        'last_activity' => 'datetime',
        'expires_at' => 'datetime',
    ];

    // Chat history için sabitler
    const MAX_CHAT_HISTORY = 30;
    const MAX_INTENT_HISTORY = 50;

    /**
     * Boot the model and add encryption/decryption
     */
    protected static function boot()
    {
        parent::boot();

        // Temporarily disable encryption for testing
        if (config('app.env') === 'testing') {
            return;
        }

        // Encrypt sensitive data before saving
        static::saving(function ($session) {
            $session->encryptSensitiveData();
        });

        // Decrypt sensitive data after retrieving
        static::retrieved(function ($session) {
            $session->decryptSensitiveData();
        });
    }

    /**
     * Encrypt sensitive data before saving
     */
    protected function encryptSensitiveData(): void
    {
        if (!empty($this->user_preferences)) {
            $this->user_preferences = \App\Services\EncryptionService::encrypt($this->user_preferences);
        }
        
        if (!empty($this->intent_history)) {
            $this->intent_history = \App\Services\EncryptionService::encrypt($this->intent_history);
        }
        
        if (!empty($this->chat_history)) {
            $this->chat_history = \App\Services\EncryptionService::encrypt($this->chat_history);
        }
    }

    /**
     * Decrypt sensitive data after retrieving
     */
    protected function decryptSensitiveData(): void
    {
        if (!empty($this->user_preferences)) {
            $this->user_preferences = \App\Services\EncryptionService::decrypt($this->user_preferences) ?? [];
        }
        
        if (!empty($this->intent_history)) {
            $this->intent_history = \App\Services\EncryptionService::decrypt($this->intent_history) ?? [];
        }
        
        if (!empty($this->chat_history)) {
            $this->chat_history = \App\Services\EncryptionService::decrypt($this->chat_history) ?? [];
        }
    }

    /**
     * User ile ilişki
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Project ile ilişki
     */
    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    /**
     * Product interactions ile ilişki
     */
    public function productInteractions(): HasMany
    {
        return $this->hasMany(ProductInteraction::class, 'session_id', 'session_id');
    }

    /**
     * Session'ın aktif olup olmadığını kontrol et
     */
    public function isActive(): bool
    {
        return $this->status === 'active' && 
               (!$this->expires_at || $this->expires_at->isFuture());
    }

    /**
     * Session'ın süresi dolmuş mu kontrol et
     */
    public function isExpired(): bool
    {
        return $this->expires_at && $this->expires_at->isPast();
    }

    /**
     * Session daha fazla ürün görüntüleyebilir mi kontrol et
     * DEVELOPMENT MODE: Limit kontrolü devre dışı
     */
    public function canViewMore(): bool
    {
        // Development için limit kontrolünü devre dışı bırak
       /* if (app()->environment('local', 'development')) {
            return true;
        }
        
        return $this->isActive() && 
               $this->daily_view_count < $this->daily_view_limit;
               */
        return true;
    }

    /**
     * Check if IP can view more products today (IP bazlı kontrol)
     * DEVELOPMENT MODE: IP kontrolü devre dışı
     */
    public static function canIPViewMore(string $ipAddress): bool
    {
        // Development için IP kontrolünü devre dışı bırak
        if (app()->environment('local', 'development')) {
            return true;
        }
        
        // Bugün aktif olan aynı IP'deki session'ları kontrol et
        $todaySessions = self::where('ip_address', $ipAddress)
            ->where('status', 'active')
            ->whereDate('last_activity', today())
            ->get();

        // Toplam günlük görüntüleme sayısını hesapla
        $totalDailyViews = $todaySessions->sum('daily_view_count');
        $maxDailyViews = config('chat.session.ip_daily_limit', 50); // IP bazlı limit

        return $totalDailyViews < $maxDailyViews;
    }

    /**
     * Get total daily views for IP
     */
    public static function getIPDailyViewCount(string $ipAddress): int
    {
        return self::where('ip_address', $ipAddress)
            ->where('status', 'active')
            ->whereDate('last_activity', today())
            ->sum('daily_view_count');
    }

    /**
     * Get IP daily limit
     */
    public static function getIPDailyLimit(): int
    {
        return config('chat.session.ip_daily_limit', 50);
    }

    /**
     * Find existing session for IP or create new one
     */
    public static function findOrCreateForIP(string $ipAddress, string $userAgent, int $projectId = 1): self
    {
        // Önce aynı IP'deki aktif session'ı ara
        $existingSession = self::where('ip_address', $ipAddress)
            ->where('status', 'active')
            ->where('project_id', $projectId)
            ->whereDate('last_activity', today())
            ->first();

        if ($existingSession) {
            return $existingSession;
        }

        // Yeni session oluştur
        return self::create([
            'session_id' => 'ip_' . $ipAddress . '_' . time(),
            'ip_address' => $ipAddress,
            'user_agent' => $userAgent,
            'user_id' => 0,
            'project_id' => $projectId,
            'daily_view_limit' => 20,
            'daily_view_count' => 0,
            'status' => 'active',
            'last_activity' => now(),
            'expires_at' => now()->addHours(72)
        ]);
    }

    /**
     * Daily view count'u artır
     */
    public function incrementViewCount(): bool
    {
        if ($this->canViewMore()) {
            $this->increment('daily_view_count');
            $this->updateLastActivity();
            return true;
        }
        return false;
    }

    /**
     * Last activity'yi güncelle
     */
    public function updateLastActivity(): void
    {
        $this->update(['last_activity' => now()]);
    }

    /**
     * Update user preferences
     */
    public function updateUserPreferences(array $preferences): void
    {
        $currentPreferences = is_array($this->user_preferences) ? $this->user_preferences : [];
        $updatedPreferences = array_merge($currentPreferences, $preferences);
        
        $this->update(['user_preferences' => $updatedPreferences]);
    }

    /**
     * Product interaction ekle
     */
    public function addProductInteraction(int $productId, string $action, array $metadata = []): void
    {
        $interactions = $this->product_interactions ?? [];
        $interactions[] = [
            'product_id' => $productId,
            'action' => $action,
            'metadata' => $metadata,
            'timestamp' => now()->toISOString()
        ];

        $this->update(['product_interactions' => $interactions]);
    }

    /**
     * Refresh daily view limits (reset at midnight)
     */
    public function refreshDailyLimits(): void
    {
        $lastActivity = $this->last_activity ?? $this->created_at;
        
        if ($lastActivity && $lastActivity->isToday()) {
            // Already today, no need to refresh
            return;
        }

        // Reset daily view count
        $this->update([
            'daily_view_count' => 0,
            'last_activity' => now()
        ]);
    }

    /**
     * Session'ı expire et
     */
    public function expire(): void
    {
        $this->update([
            'status' => 'expired',
            'expires_at' => now()
        ]);
    }

    /**
     * Session'ı tamamla
     */
    public function complete(): void
    {
        $this->update([
            'status' => 'completed',
            'expires_at' => now()
        ]);
    }

    /**
     * Scope: Aktif session'lar
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active')
                    ->where(function($q) {
                        $q->whereNull('expires_at')
                          ->orWhere('expires_at', '>', now());
                    });
    }

    /**
     * Scope: Bugün aktif olan session'lar
     */
    public function scopeActiveToday($query)
    {
        return $query->whereDate('last_activity', today())
                    ->where('status', 'active');
    }

    /**
     * Scope: Daily limit'i aşan session'lar
     */
    public function scopeExceededDailyLimit($query)
    {
        return $query->whereRaw('daily_view_count >= daily_view_limit');
    }

    /**
     * Chat history'ye mesaj ekle - Encryption Safe
     */
    public function addChatMessage(string $role, string $content, ?string $intent = null, ?array $responseData = null): void
    {
        try {
            // Chat history'yi güvenli şekilde al (encryption durumunu kontrol et)
            $chatHistory = $this->getSafeChatHistory();
            
            // Yeni mesaj
            $message = [
                'role' => $role, // 'user' veya 'assistant'
                'content' => $content,
                'timestamp' => now()->toISOString(),
                'intent' => $intent,
                'response_data' => $responseData
            ];
            
            // Mesajı ekle
            $chatHistory[] = $message;
            
            // 30 mesaj limitini kontrol et
            if (count($chatHistory) > self::MAX_CHAT_HISTORY) {
                $chatHistory = array_slice($chatHistory, -self::MAX_CHAT_HISTORY);
            }
            
            // Intent'i de ekle
            if ($intent) {
                $this->addIntent($intent);
            }
            
            // Güncelle
            $this->update([
                'chat_history' => $chatHistory,
                'last_activity' => now()
            ]);
            
        } catch (\Exception $e) {
            \Log::error('addChatMessage failed', [
                'session_id' => $this->session_id,
                'error' => $e->getMessage(),
                'role' => $role,
                'content' => $content
            ]);
            
            // Fallback: Sadece last_activity'yi güncelle
            $this->update(['last_activity' => now()]);
        }
    }

    /**
     * Intent history'ye intent ekle - Encryption Safe
     */
    public function addIntent(string $intent, float $confidence = 0.0): void
    {
        try {
            // Intent history'yi güvenli şekilde al (encryption durumunu kontrol et)
            $intentHistory = $this->getSafeIntentHistory();
            
            // Intent'i ekle
            $intentHistory[] = [
                'intent' => $intent,
                'confidence' => $confidence,
                'timestamp' => now()->toISOString()
            ];
            
            // 50 intent limitini kontrol et
            if (count($intentHistory) > self::MAX_INTENT_HISTORY) {
                $intentHistory = array_slice($intentHistory, -self::MAX_INTENT_HISTORY);
            }
            
            // Güncelle
            $this->update([
                'intent_history' => $intentHistory
            ]);
            
        } catch (\Exception $e) {
            \Log::error('addIntent failed', [
                'session_id' => $this->session_id,
                'error' => $e->getMessage(),
                'intent' => $intent
            ]);
        }
    }

    /**
     * Chat history'yi güvenli şekilde al (encryption safe)
     */
    private function getSafeChatHistory(): array
    {
        $chatHistory = $this->chat_history ?? [];
        
        // Eğer string ise (encrypted), decrypt et
        if (is_string($chatHistory)) {
            try {
                $decrypted = \App\Services\EncryptionService::decrypt($chatHistory);
                return is_array($decrypted) ? $decrypted : [];
            } catch (\Exception $e) {
                \Log::warning('Failed to decrypt chat_history', [
                    'session_id' => $this->session_id,
                    'error' => $e->getMessage()
                ]);
                return [];
            }
        }
        
        return is_array($chatHistory) ? $chatHistory : [];
    }

    /**
     * Intent history'yi güvenli şekilde al (encryption safe)
     */
    private function getSafeIntentHistory(): array
    {
        $intentHistory = $this->intent_history ?? [];
        
        // Eğer string ise (encrypted), decrypt et
        if (is_string($intentHistory)) {
            try {
                $decrypted = \App\Services\EncryptionService::decrypt($intentHistory);
                return is_array($decrypted) ? $decrypted : [];
            } catch (\Exception $e) {
                \Log::warning('Failed to decrypt intent_history', [
                    'session_id' => $this->session_id,
                    'error' => $e->getMessage()
                ]);
                return [];
            }
        }
        
        return is_array($intentHistory) ? $intentHistory : [];
    }

    /**
     * Chat history'yi getir
     */
    public function getChatHistory(): array
    {
        return $this->getSafeChatHistory();
    }

    /**
     * Intent history'yi getir
     */
    public function getIntentHistory(): array
    {
        return $this->getSafeIntentHistory();
    }

    /**
     * Son N mesajı getir
     */
    public function getLastMessages(int $count = 10): array
    {
        $chatHistory = $this->getChatHistory();
        return array_slice($chatHistory, -$count);
    }

    /**
     * Chat history'yi temizle
     */
    public function clearChatHistory(): void
    {
        $this->update([
            'chat_history' => [],
            'intent_history' => []
        ]);
    }

    /**
     * Chat history istatistikleri
     */
    public function getChatStats(): array
    {
        $chatHistory = $this->getChatHistory();
        $intentHistory = $this->getIntentHistory();
        
        $userMessages = collect($chatHistory)->where('role', 'user')->count();
        $assistantMessages = collect($chatHistory)->where('role', 'assistant')->count();
        
        $intentCounts = collect($intentHistory)->countBy('intent')->toArray();
        
        return [
            'total_messages' => count($chatHistory),
            'user_messages' => $userMessages,
            'assistant_messages' => $assistantMessages,
            'intent_count' => count($intentHistory),
            'intent_distribution' => $intentCounts,
            'last_activity' => $this->last_activity?->diffForHumans()
        ];
    }
}
