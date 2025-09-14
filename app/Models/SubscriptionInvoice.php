<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SubscriptionInvoice extends Model
{
    use HasFactory;

    protected $fillable = [
        'subscription_id',
        'amount',
        'status',
        'payment_gateway',
        'paid_at'
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'paid_at' => 'datetime'
    ];

    /**
     * Subscription ile ilişki
     */
    public function subscription(): BelongsTo
    {
        return $this->belongsTo(Subscription::class);
    }

    /**
     * Ödenmiş invoice'ları getir
     */
    public function scopePaid($query)
    {
        return $query->where('status', 'paid');
    }

    /**
     * Bekleyen invoice'ları getir
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Başarısız invoice'ları getir
     */
    public function scopeFailed($query)
    {
        return $query->where('status', 'failed');
    }

    /**
     * Fiyat formatı
     */
    public function getFormattedAmountAttribute(): string
    {
        return '$' . number_format($this->amount, 2);
    }

    /**
     * Status text
     */
    public function getStatusTextAttribute(): string
    {
        return match($this->status) {
            'paid' => 'Ödendi',
            'pending' => 'Bekliyor',
            'failed' => 'Başarısız',
            default => 'Bilinmiyor'
        };
    }

    /**
     * Status color
     */
    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'paid' => 'green',
            'pending' => 'yellow',
            'failed' => 'red',
            default => 'gray'
        };
    }
}
