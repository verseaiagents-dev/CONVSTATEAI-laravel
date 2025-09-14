<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AiInteraction extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'session_id',
        'message',
        'intent',
        'confidence',
        'action_type',
        'metadata',
        'ip_address',
        'user_agent'
    ];

    protected $casts = [
        'confidence' => 'decimal:2',
        'metadata' => 'array'
    ];

    /**
     * User ile ilişki
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Enhanced Chat Session ile ilişki
     */
    public function chatSession(): BelongsTo
    {
        return $this->belongsTo(EnhancedChatSession::class, 'session_id', 'session_id');
    }

    /**
     * Scope: Belirli intent'ler
     */
    public function scopeByIntent($query, string $intent)
    {
        return $query->where('intent', $intent);
    }

    /**
     * Scope: Belirli action type'lar
     */
    public function scopeByActionType($query, string $actionType)
    {
        return $query->where('action_type', $actionType);
    }

    /**
     * Scope: Belirli kullanıcı
     */
    public function scopeByUser($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope: Belirli session
     */
    public function scopeBySession($query, string $sessionId)
    {
        return $query->where('session_id', $sessionId);
    }

    /**
     * Scope: Yüksek confidence score'lar
     */
    public function scopeHighConfidence($query, float $threshold = 0.8)
    {
        return $query->where('confidence', '>=', $threshold);
    }

    /**
     * Scope: Belirli tarih aralığı
     */
    public function scopeInDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('created_at', [$startDate, $endDate]);
    }

    /**
     * Scope: Bugünkü interaction'lar
     */
    public function scopeToday($query)
    {
        return $query->whereDate('created_at', today());
    }

    /**
     * Intent'in geçerli olup olmadığını kontrol et
     */
    public function isValidIntent(): bool
    {
        $validIntents = ['product_search', 'product_recommendation', 'cart_add', 'order_checkout', 'faq', 'general'];
        return in_array($this->intent, $validIntents);
    }

    /**
     * Action type'ın geçerli olup olmadığını kontrol et
     */
    public function isValidActionType(): bool
    {
        $validActionTypes = ['search', 'recommend', 'add_to_cart', 'checkout', 'answer', 'redirect'];
        return in_array($this->action_type, $validActionTypes);
    }

    /**
     * Metadata'dan belirli bir değeri al
     */
    public function getMetadataValue(string $key, $default = null)
    {
        return $this->metadata[$key] ?? $default;
    }

    /**
     * Metadata'ya yeni değer ekle
     */
    public function addMetadata(string $key, $value): void
    {
        $metadata = $this->metadata ?? [];
        $metadata[$key] = $value;
        $this->update(['metadata' => $metadata]);
    }

    /**
     * Confidence score'u yüzde olarak getir
     */
    public function getConfidencePercentageAttribute(): float
    {
        return round($this->confidence * 100, 2);
    }

    /**
     * Interaction'ın başarılı olup olmadığını kontrol et
     */
    public function isSuccessful(): bool
    {
        return $this->confidence >= 0.7 && !empty($this->action_type);
    }
}
