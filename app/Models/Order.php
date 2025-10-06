<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'plan_id',
        'amount',
        'amount_without_kdv',
        'kdv_amount',
        'kdv_rate',
        'uuid',
        'status',
        'payment_method',
        'transaction_id',
        'paid_at',
        'company_name',
        'full_name',
        'tax_number',
        'tax_office',
        'country',
        'city',
        'district',
        'address_line',
        'postal_code',
        'phone',
        'email'
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'amount_without_kdv' => 'decimal:2',
        'kdv_amount' => 'decimal:2',
        'kdv_rate' => 'decimal:4',
        'paid_at' => 'datetime'
    ];

    /**
     * User ile ilişki
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Plan ile ilişki
     */
    public function plan(): BelongsTo
    {
        return $this->belongsTo(Plan::class);
    }

    /**
     * Ödenmiş siparişleri getir
     */
    public function scopePaid($query)
    {
        return $query->where('status', 'paid');
    }

    /**
     * Bekleyen siparişleri getir
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Başarısız siparişleri getir
     */
    public function scopeFailed($query)
    {
        return $query->where('status', 'failed');
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
