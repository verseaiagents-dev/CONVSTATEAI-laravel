<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class UsageToken extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'subscription_id',
        'plan_id',
        'tokens_remaining',
        'tokens_used',
        'tokens_total',
        'reset_date',
        'is_active',
        'metadata'
    ];

    protected $casts = [
        'reset_date' => 'date',
        'is_active' => 'boolean',
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
     * Subscription ile ilişki
     */
    public function subscription(): BelongsTo
    {
        return $this->belongsTo(Subscription::class);
    }

    /**
     * Plan ile ilişki
     */
    public function plan(): BelongsTo
    {
        return $this->belongsTo(Plan::class);
    }

    /**
     * Token kullan
     */
    public function useToken(int $amount = 1): bool
    {
        if ($this->tokens_remaining < $amount) {
            return false;
        }

        $this->increment('tokens_used', $amount);
        $this->decrement('tokens_remaining', $amount);
        
        return true;
    }

    /**
     * Token ekle (satın alma sonrası)
     */
    public function addTokens(int $amount): void
    {
        $this->increment('tokens_total', $amount);
        $this->increment('tokens_remaining', $amount);
    }

    /**
     * Token'ları yenile (aylık/yıllık reset)
     */
    public function resetTokens(): void
    {
        $this->update([
            'tokens_remaining' => $this->tokens_total,
            'tokens_used' => 0,
            'reset_date' => $this->getNextResetDate()
        ]);
    }

    /**
     * Token'ları yenileme tarihini hesapla
     */
    public function getNextResetDate(): Carbon
    {
        $subscription = $this->subscription;
        if (!$subscription) {
            return now()->addMonth();
        }

        $plan = $subscription->plan;
        if ($plan->billing_cycle === 'yearly') {
            return now()->addYear();
        }

        return now()->addMonth();
    }

    /**
     * Token'ların süresi dolmuş mu?
     */
    public function isExpired(): bool
    {
        return $this->reset_date && $this->reset_date->isPast();
    }

    /**
     * Token kullanılabilir mi?
     */
    public function canUseToken(int $amount = 1): bool
    {
        return $this->is_active && 
               $this->tokens_remaining >= $amount && 
               !$this->isExpired();
    }

    /**
     * Kullanım yüzdesi
     */
    public function getUsagePercentageAttribute(): float
    {
        if ($this->tokens_total == 0) {
            return 0;
        }

        return ($this->tokens_used / $this->tokens_total) * 100;
    }

    /**
     * Kalan gün sayısı
     */
    public function getDaysUntilResetAttribute(): int
    {
        if (!$this->reset_date) {
            return 0;
        }

        return max(0, now()->diffInDays($this->reset_date, false));
    }

    /**
     * Aktif usage token'ları getir
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Kullanıcının aktif usage token'ını getir
     */
    public static function getActiveForUser(int $userId): ?self
    {
        return static::where('user_id', $userId)
                    ->where('is_active', true)
                    ->first();
    }

    /**
     * Kullanıcı için yeni usage token oluştur
     */
    public static function createForUser(int $userId, int $tokensTotal, int $subscriptionId = null): self
    {
        // Mevcut aktif token'ı deaktive et
        static::where('user_id', $userId)
              ->where('is_active', true)
              ->update(['is_active' => false]);

        return static::create([
            'user_id' => $userId,
            'subscription_id' => $subscriptionId,
            'tokens_total' => $tokensTotal,
            'tokens_remaining' => $tokensTotal,
            'tokens_used' => 0,
            'reset_date' => now()->addMonth(),
            'is_active' => true
        ]);
    }
}
