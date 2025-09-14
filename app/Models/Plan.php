<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Plan extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'price',
        'yearly_price',
        'billing_cycle',
        'features',
        'is_active',
        'trial_days',
        'usage_tokens',
        'token_reset_period'
    ];

    protected $casts = [
        'features' => 'array',
        'is_active' => 'boolean',
        'price' => 'decimal:2',
        'yearly_price' => 'decimal:2',
        'trial_days' => 'integer',
        'usage_tokens' => 'integer'
    ];

    /**
     * Subscriptions ile ilişki
     */
    public function subscriptions(): HasMany
    {
        return $this->hasMany(Subscription::class);
    }

    /**
     * UsageToken ile ilişki (plan üzerinden)
     */
    public function usageTokens(): HasMany
    {
        return $this->hasMany(UsageToken::class, 'plan_id');
    }

    /**
     * Aktif planları getir
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Fiyat formatı
     */
    public function getFormattedPriceAttribute(): string
    {
        return number_format($this->price, 2) . ' ₺';
    }

    /**
     * Billing cycle text
     */
    public function getBillingCycleTextAttribute(): string
    {
        return match($this->billing_cycle) {
            'monthly' => 'Aylık',
            'yearly' => 'Yıllık',
            'trial' => 'Deneme',
            default => 'Bilinmiyor'
        };
    }

    /**
     * Trial plan kontrolü
     */
    public function isTrial(): bool
    {
        return $this->billing_cycle === 'trial';
    }

    /**
     * Feature değeri getir
     */
    public function getFeature(string $key, $default = null)
    {
        return $this->features[$key] ?? $default;
    }

    /**
     * Feature limiti kontrol et
     */
    public function hasFeatureLimit(string $feature, int $currentUsage): bool
    {
        $limit = $this->getFeature($feature, 0);
        
        if ($limit === -1) {
            return true; // Sınırsız
        }
        
        return $currentUsage < $limit;
    }

    /**
     * Plan için usage token hesapla
     */
    public function calculateUsageTokens(): int
    {
        // AI responses per month değerini usage token olarak kullan
        $aiResponses = $this->getFeature('ai_responses_per_month', 0);
        
        if ($aiResponses === -1) {
            return -1; // Sınırsız
        }
        
        return $aiResponses;
    }

    /**
     * Token reset periyodunu hesapla
     */
    public function getTokenResetPeriod(): string
    {
        return $this->token_reset_period ?? 'monthly';
    }

    /**
     * Token reset tarihini hesapla
     */
    public function getNextTokenResetDate(): \Carbon\Carbon
    {
        $period = $this->getTokenResetPeriod();
        
        return match($period) {
            'yearly' => now()->addYear(),
            'monthly' => now()->addMonth(),
            default => now()->addMonth()
        };
    }

    /**
     * Kullanıcı için usage token oluştur
     */
    public function createUsageTokenForUser(int $userId, int $subscriptionId = null): UsageToken
    {
        $tokens = $this->calculateUsageTokens();
        
        // Mevcut aktif token'ı deaktive et
        UsageToken::where('user_id', $userId)
                  ->where('is_active', true)
                  ->update(['is_active' => false]);

        return UsageToken::create([
            'user_id' => $userId,
            'subscription_id' => $subscriptionId,
            'plan_id' => $this->id,
            'tokens_total' => $tokens,
            'tokens_remaining' => $tokens,
            'tokens_used' => 0,
            'reset_date' => $this->getNextTokenResetDate(),
            'is_active' => true
        ]);
    }
}
