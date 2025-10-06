<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use App\Models\Campaign;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'is_admin',
        'avatar',
        'bio',
        'personal_token',
        'token_expires_at',
        'language',
        'address',
        'phone',
        'tokens_total',
        'tokens_used',
        'tokens_remaining',
        'token_reset_date',
        'current_plan_id',
        'usage_token',
        'max_projects',
        'priority_support',
        'advanced_analytics',
        'custom_branding',
        'api_access',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_admin' => 'boolean',
            'last_login_at' => 'datetime',
            'token_expires_at' => 'datetime',
            'token_reset_date' => 'date',
            'tokens_total' => 'integer',
            'tokens_used' => 'integer',
            'tokens_remaining' => 'integer',
        ];
    }

    /**
     * Check if user is admin
     */
    public function isAdmin(): bool
    {
        return $this->is_admin;
    }

    /**
     * Get user's display name
     */
    public function getDisplayName(): string
    {
        return $this->name ?: explode('@', $this->email)[0];
    }

    /**
     * Get user's avatar URL
     */
    public function getAvatarUrl(): string
    {
        if ($this->avatar) {
            return asset('storage/' . $this->avatar);
        }
        
        // Uygulama varsayılan resmi
        return asset('imgs/ai-conversion-logo.svg');
    }

    /**
     * Get user's language preference
     */
    public function getLanguage(): string
    {
        return $this->language ?? 'tr';
    }

    /**
     * Get user's campaigns
     */
    public function campaigns()
    {
        return $this->hasMany(Campaign::class, 'created_by');
    }

    /**
     * Active subscription ile ilişki
     */
    public function activeSubscription()
    {
        return $this->hasOne(Subscription::class, 'tenant_id')->where('status', 'active');
    }

    /**
     * Tüm subscription'lar ile ilişki
     */
    public function subscriptions()
    {
        return $this->hasMany(Subscription::class, 'tenant_id');
    }

    /**
     * Kullanıcının aktif planı var mı?
     */
    public function hasActiveSubscription(): bool
    {
        return $this->activeSubscription()->exists();
    }

    /**
     * Kullanıcının plan adını getir
     */
    public function getPlanNameAttribute(): ?string
    {
        $subscription = $this->activeSubscription()->with('plan')->first();
        return $subscription?->plan?->name;
    }
    
    /**
     * Set user's language preference
     */
    public function setLanguage(string $language): void
    {
        $this->update(['language' => $language]);
    }

    /**
     * Generate new personal token for user
     */
    public function generatePersonalToken(): string
    {
        $token = bin2hex(random_bytes(32));
        $this->update([
            'personal_token' => $token,
            'token_expires_at' => now()->addYears(10) // 10 yıl geçerli
        ]);
        
        return $token;
    }

    /**
     * Check if personal token is valid
     */
    public function hasValidPersonalToken(): bool
    {
        return $this->personal_token && 
               $this->token_expires_at && 
               $this->token_expires_at->isFuture();
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    /**
     * SubscriptionInvoices ile ilişki
     */
    public function subscriptionInvoices()
    {
        return $this->hasMany(SubscriptionInvoice::class);
    }
    /**
     * Revoke personal token
     */
    public function revokePersonalToken(): void
    {
        $this->update([
            'personal_token' => null,
            'token_expires_at' => null
        ]);
    }

    /**
     * Find user by personal token
     */
    public static function findByPersonalToken(string $token): ?self
    {
        return static::where('personal_token', $token)
                    ->where('token_expires_at', '>', now())
                    ->first();
    }

    // UsageToken ilişkileri kaldırıldı - artık User tablosunda token bilgileri tutuluyor

    /**
     * Current plan ile ilişki
     */
    public function currentPlan()
    {
        return $this->belongsTo(Plan::class, 'current_plan_id');
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
     * Token ekle (plan atama sonrası)
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
        $plan = $this->currentPlan;
        $tokenResetDate = $plan ? $this->calculateTokenResetDate($plan) : now()->addMonth();
        
        $this->update([
            'tokens_remaining' => $this->tokens_total,
            'tokens_used' => 0,
            'token_reset_date' => $tokenResetDate
        ]);
    }

    /**
     * Token'ları yenileme tarihini hesapla
     */
    public function getNextTokenResetDate(): \Carbon\Carbon
    {
        $plan = $this->currentPlan;
        if (!$plan) {
            return now()->addMonth();
        }

        return $this->calculateTokenResetDate($plan);
    }

    /**
     * Plan parametresi ile token reset tarihini hesapla
     */
    public function calculateTokenResetDate(Plan $plan): \Carbon\Carbon
    {
        $period = $plan->token_reset_period ?? 'monthly';
        
        return match($period) {
            'yearly' => now()->addYear(),
            'monthly' => now()->addMonth(),
            default => now()->addMonth()
        };
    }

    /**
     * Token'ların süresi dolmuş mu?
     */
    public function isTokenExpired(): bool
    {
        return $this->token_reset_date && $this->token_reset_date->isPast();
    }

    /**
     * Token kullanılabilir mi?
     */
    public function canUseToken(int $amount = 1): bool
    {
        return $this->tokens_remaining >= $amount && !$this->isTokenExpired();
    }

    /**
     * Kullanım yüzdesi
     */
    public function getTokenUsagePercentageAttribute(): float
    {
        if ($this->tokens_total == 0) {
            return 0;
        }

        return round(($this->tokens_used / $this->tokens_total) * 100, 2);
    }

    /**
     * Kullanım yüzdesi (view uyumluluğu için)
     */
    public function getUsagePercentageAttribute(): float
    {
        return $this->token_usage_percentage;
    }

    /**
     * Kalan gün sayısı
     */
    public function getDaysUntilTokenResetAttribute(): int
    {
        if (!$this->token_reset_date) {
            return 0;
        }

        return max(0, (int) now()->diffInDays($this->token_reset_date, false));
    }

    /**
     * Kalan gün sayısı (view uyumluluğu için)
     */
    public function getDaysUntilResetAttribute(): int
    {
        return $this->days_until_token_reset;
    }

    /**
     * Reset tarihi (view uyumluluğu için)
     */
    public function getResetDateAttribute()
    {
        return $this->token_reset_date;
    }

    /**
     * Plan atama ve token ekleme
     */
    public function assignPlan(Plan $plan, int $subscriptionId = null): void
    {
        $tokens = $plan->calculateUsageTokens();
        
        // Token reset tarihini hesapla (plan parametresini kullan)
        $tokenResetDate = $this->calculateTokenResetDate($plan);
        
        $this->update([
            'current_plan_id' => $plan->id,
            'tokens_total' => $tokens,
            'tokens_remaining' => $tokens,
            'tokens_used' => 0,
            'token_reset_date' => $tokenResetDate
        ]);
    }

    /**
     * Usage token ile uyumluluk için (API arayüzünü korumak için)
     */
    public function usageToken()
    {
        return $this; // User'ın kendisi token bilgilerini tutar
    }

    public function planRequests()
    {
        return $this->hasMany(PlanRequest::class);
    }

}
