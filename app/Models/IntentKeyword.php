<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class IntentKeyword extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'intent_id',
        'keyword',
        'weight',
    ];

    protected $casts = [
        'weight' => 'decimal:2',
    ];

    /**
     * Get the intent that owns this keyword
     */
    public function intent(): BelongsTo
    {
        return $this->belongsTo(Intent::class);
    }
}
