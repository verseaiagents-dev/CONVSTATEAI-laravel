<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductInteraction extends Model
{
    use HasFactory;

    protected $fillable = [
        'session_id',
        'product_id',
        'action',
        'timestamp',
        'source',
        'metadata',
        'ip_address',
        'user_agent',
        'intent',
        'user_message',
        'success',
        'response_time'
    ];

    protected $casts = [
        'timestamp' => 'datetime',
        'metadata' => 'array'
    ];

    /**
     * Enhanced Chat Session ile ilişki
     */
    public function chatSession(): BelongsTo
    {
        return $this->belongsTo(EnhancedChatSession::class, 'session_id', 'session_id');
    }

    /**
     * Product ile ilişki (eğer product_id varsa)
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Action'ın geçerli olup olmadığını kontrol et
     */
    public function isValidAction(): bool
    {
        $validActions = ['view', 'compare', 'buy', 'add_to_cart', 'wishlist'];
        return in_array($this->action, $validActions);
    }

    /**
     * Action'ın conversion olup olmadığını kontrol et
     */
    public function isConversionAction(): bool
    {
        $conversionActions = ['buy', 'add_to_cart'];
        return in_array($this->action, $conversionActions);
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
     * Scope: Belirli action'lar
     */
    public function scopeByAction($query, string $action)
    {
        return $query->where('action', $action);
    }

    /**
     * Scope: Belirli source'dan gelen interaction'lar
     */
    public function scopeBySource($query, string $source)
    {
        return $query->where('source', $source);
    }

    /**
     * Scope: Belirli tarih aralığındaki interaction'lar
     */
    public function scopeInDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('timestamp', [$startDate, $endDate]);
    }

    /**
     * Scope: Bugünkü interaction'lar
     */
    public function scopeToday($query)
    {
        return $query->whereDate('timestamp', today());
    }

    /**
     * Scope: Conversion action'ları
     */
    public function scopeConversions($query)
    {
        return $query->whereIn('action', ['buy', 'add_to_cart']);
    }

    /**
     * Scope: Belirli session'dan gelen interaction'lar
     */
    public function scopeBySession($query, string $sessionId)
    {
        return $query->where('session_id', $sessionId);
    }

    /**
     * Scope: Belirli ürün için olan interaction'lar
     */
    public function scopeByProduct($query, int $productId)
    {
        return $query->where('product_id', $productId);
    }
}
