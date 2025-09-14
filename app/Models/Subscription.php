<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Carbon\Carbon;

class Subscription extends Model
{
    use HasFactory;

    protected $fillable = [
        'tenant_id',
        'plan_id',
        'start_date',
        'end_date',
        'status',
        'trial_ends_at'
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'trial_ends_at' => 'datetime'
    ];

    /**
     * User ile ilişki
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'tenant_id');
    }

    /**
     * Plan ile ilişki
     */
    public function plan(): BelongsTo
    {
        return $this->belongsTo(Plan::class);
    }

    /**
     * Invoices ile ilişki
     */
    public function invoices(): HasMany
    {
        return $this->hasMany(SubscriptionInvoice::class);
    }

    /**
     * Aktif subscription'ları getir
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Trial süresi dolmuş mu?
     */
    public function isTrialExpired(): bool
    {
        return $this->trial_ends_at && $this->trial_ends_at->isPast();
    }

    /**
     * Subscription süresi dolmuş mu?
     */
    public function isExpired(): bool
    {
        return $this->end_date && $this->end_date->isPast();
    }

    /**
     * Plan süresi dolmuş mu ve kullanıcı yönlendirilmeli mi?
     */
    public function shouldRedirectToExpired(): bool
    {
        return $this->isExpired() || ($this->end_date && Carbon::now()->diffInDays($this->end_date, false) <= 0);
    }

    /**
     * Kalan gün sayısı
     */
    public function getDaysRemainingAttribute(): int
    {
        if ($this->isExpired()) {
            return 0;
        }
        
        $diffInDays = Carbon::now()->diffInDays($this->end_date, false);
        return $diffInDays < 0 ? 0 : $diffInDays;
    }
}
