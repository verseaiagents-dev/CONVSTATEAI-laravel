<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PlanRequest extends Model
{
    protected $fillable = [
        'user_id',
        'plan_id',
        'status',
        'amount',
        'company_name',
        'full_name',
        'email',
        'phone',
        'tax_number',
        'tax_office',
        'country',
        'city',
        'district',
        'address_line',
        'postal_code',
        'approved_at',
        'approved_by',
        'admin_notes'
    ];

    protected $casts = [
        'approved_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function plan(): BelongsTo
    {
        return $this->belongsTo(Plan::class);
    }

    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }

    public function isRejected(): bool
    {
        return $this->status === 'rejected';
    }
}
