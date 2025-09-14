<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserEndpoint extends Model
{
    protected $fillable = [
        'user_id',
        'intent_type',
        'name',
        'description',
        'method',
        'endpoint_url',
        'headers',
        'payload_template',
        'is_active',
        'timeout'
    ];

    protected $casts = [
        'headers' => 'array',
        'payload_template' => 'array',
        'is_active' => 'boolean',
        'timeout' => 'integer'
    ];

    // Intent types constants
    const INTENT_ORDER_TRACKING = 'order-tracking';
    const INTENT_CARGO_TRACKING = 'cargo-tracking';
    const INTENT_ADD_TO_CART = 'add-to-cart';

    // Available intent types
    public static function getIntentTypes(): array
    {
        return [
            self::INTENT_ORDER_TRACKING => 'Sipariş Takibi',
            self::INTENT_CARGO_TRACKING => 'Kargo Takibi',
            self::INTENT_ADD_TO_CART => 'Sepete Ekle'
        ];
    }

    // Available HTTP methods
    public static function getMethods(): array
    {
        return ['GET', 'POST'];
    }

    // Relationship with User
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Scope for active endpoints
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // Scope for specific intent
    public function scopeForIntent($query, $intentType)
    {
        return $query->where('intent_type', $intentType);
    }
}
