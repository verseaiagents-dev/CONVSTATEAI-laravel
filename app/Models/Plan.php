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
     * Users with this plan (for token management)
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class, 'current_plan_id');
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
        // usage_tokens alanını kullan
        if ($this->usage_tokens === -1) {
            return -1; // Sınırsız
        }
        
        return $this->usage_tokens ?? 0;
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
     * Kullanıcı için usage token oluştur (yeni User tablosu sistemi)
     */
    public function createUsageTokenForUser(int $userId, int $subscriptionId = null): User
    {
        $tokens = $this->calculateUsageTokens();
        
        $user = User::findOrFail($userId);
        
        // Kullanıcıya plan atama ve token ekleme
        $user->assignPlan($this, $subscriptionId);
        
        return $user;
    }
}
