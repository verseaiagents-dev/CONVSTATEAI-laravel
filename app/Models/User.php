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

    /**
     * Usage token ile ilişki
     */
    public function usageTokens()
    {
        return $this->hasMany(UsageToken::class);
    }

    /**
     * Aktif usage token ile ilişki
     */
    public function activeUsageToken()
    {
        return $this->hasOne(UsageToken::class)->where('is_active', true);
    }

    /**
     * Usage token ile ilişki (tekil - en son aktif olan)
     */
    public function usageToken()
    {
        return $this->hasOne(UsageToken::class)->where('is_active', true)->latest();
    }

}
