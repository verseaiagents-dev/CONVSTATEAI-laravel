<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class QueryLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'site_id',
        'session_id',
        'user_id',
        'query_text',
        'detected_intent',
        'confidence_score',
        'response_text',
        'response_template',
        'chunks_used',
        'response_time_ms',
        'is_successful',
        'error_message',
        'user_feedback'
    ];

    protected $casts = [
        'confidence_score' => 'decimal:2',
        'chunks_used' => 'array',
        'is_successful' => 'boolean',
        'response_time_ms' => 'integer'
    ];

    /**
     * User ile ilişki
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Site ile ilişki
     */
    public function site(): BelongsTo
    {
        return $this->belongsTo(Site::class);
    }

    /**
     * Enhanced Chat Session ile ilişki
     */
    public function chatSession(): BelongsTo
    {
        return $this->belongsTo(EnhancedChatSession::class, 'session_id', 'session_id');
    }

    /**
     * Scope: Belirli site
     */
    public function scopeBySite($query, int $siteId)
    {
        return $query->where('site_id', $siteId);
    }

    /**
     * Scope: Belirli session
     */
    public function scopeBySession($query, string $sessionId)
    {
        return $query->where('session_id', $sessionId);
    }

    /**
     * Scope: Belirli kullanıcı
     */
    public function scopeByUser($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope: Belirli intent
     */
    public function scopeByIntent($query, string $intent)
    {
        return $query->where('detected_intent', $intent);
    }

    /**
     * Scope: Başarılı query'ler
     */
    public function scopeSuccessful($query)
    {
        return $query->where('is_successful', true);
    }

    /**
     * Scope: Başarısız query'ler
     */
    public function scopeFailed($query)
    {
        return $query->where('is_successful', false);
    }

    /**
     * Scope: Yüksek confidence score'lar
     */
    public function scopeHighConfidence($query, float $threshold = 0.8)
    {
        return $query->where('confidence_score', '>=', $threshold);
    }

    /**
     * Scope: Belirli user feedback
     */
    public function scopeByFeedback($query, string $feedback)
    {
        return $query->where('user_feedback', $feedback);
    }

    /**
     * Scope: Belirli tarih aralığı
     */
    public function scopeInDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('created_at', [$startDate, $endDate]);
    }

    /**
     * Scope: Bugünkü query'ler
     */
    public function scopeToday($query)
    {
        return $query->whereDate('created_at', today());
    }

    /**
     * Scope: Hızlı response'lar
     */
    public function scopeFastResponse($query, int $maxMs = 1000)
    {
        return $query->where('response_time_ms', '<=', $maxMs);
    }

    /**
     * Scope: Yavaş response'lar
     */
    public function scopeSlowResponse($query, int $minMs = 3000)
    {
        return $query->where('response_time_ms', '>=', $minMs);
    }

    /**
     * Intent'in geçerli olup olmadığını kontrol et
     */
    public function isValidIntent(): bool
    {
        $validIntents = ['product_search', 'product_recommendation', 'cart_add', 'order_checkout', 'faq', 'general'];
        return in_array($this->detected_intent, $validIntents);
    }

    /**
     * User feedback'in geçerli olup olmadığını kontrol et
     */
    public function isValidFeedback(): bool
    {
        $validFeedbacks = ['helpful', 'not_helpful', 'neutral'];
        return in_array($this->user_feedback, $validFeedbacks);
    }

    /**
     * Confidence score'u yüzde olarak getir
     */
    public function getConfidencePercentageAttribute(): float
    {
        return round($this->confidence_score * 100, 2);
    }

    /**
     * Response time'ı saniye olarak getir
     */
    public function getResponseTimeSecondsAttribute(): float
    {
        return round($this->response_time_ms / 1000, 2);
    }

    /**
     * Kullanılan chunk sayısını getir
     */
    public function getChunkCountAttribute(): int
    {
        return is_array($this->chunks_used) ? count($this->chunks_used) : 0;
    }

    /**
     * Query'nin başarılı olup olmadığını kontrol et
     */
    public function isSuccessful(): bool
    {
        return $this->is_successful && $this->confidence_score >= 0.7;
    }

    /**
     * Query'nin hızlı olup olmadığını kontrol et
     */
    public function isFastResponse(int $threshold = 1000): bool
    {
        return $this->response_time_ms <= $threshold;
    }

    /**
     * User feedback'i güncelle
     */
    public function updateFeedback(string $feedback): void
    {
        if ($this->isValidFeedback()) {
            $this->update(['user_feedback' => $feedback]);
        }
    }
}
