<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EventLog extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'intent_code',
        'event_template_id',
        'status',
        'request_payload',
        'response_payload',
    ];

    protected $casts = [
        'request_payload' => 'array',
        'response_payload' => 'array',
    ];

    /**
     * Get the event template for this log
     */
    public function eventTemplate(): BelongsTo
    {
        return $this->belongsTo(EventTemplate::class);
    }

    /**
     * Get the intent for this log
     */
    public function intent(): BelongsTo
    {
        return $this->belongsTo(Intent::class, 'intent_code', 'code');
    }

    /**
     * Scope for pending logs
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope for successful logs
     */
    public function scopeSuccessful($query)
    {
        return $query->where('status', 'success');
    }

    /**
     * Scope for failed logs
     */
    public function scopeFailed($query)
    {
        return $query->where('status', 'failed');
    }

    /**
     * Mark log as successful
     */
    public function markAsSuccess(array $response = []): void
    {
        $this->update([
            'status' => 'success',
            'response_payload' => $response
        ]);
    }

    /**
     * Mark log as failed
     */
    public function markAsFailed(array $response = []): void
    {
        $this->update([
            'status' => 'failed',
            'response_payload' => $response
        ]);
    }
}
