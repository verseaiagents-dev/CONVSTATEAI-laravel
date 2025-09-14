<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EventTemplate extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'intent_id',
        'name',
        'description',
    ];

    /**
     * Get the intent that owns this event template
     */
    public function intent(): BelongsTo
    {
        return $this->belongsTo(Intent::class);
    }

    /**
     * Get the actions for this event template
     */
    public function actions(): HasMany
    {
        return $this->hasMany(EventAction::class)->orderBy('seq');
    }

    /**
     * Get the event logs for this template
     */
    public function eventLogs(): HasMany
    {
        return $this->hasMany(EventLog::class);
    }

    /**
     * Execute all actions in sequence
     */
    public function execute(array $payload = []): array
    {
        $results = [];
        
        foreach ($this->actions as $action) {
            try {
                $result = $action->execute($payload);
                $results[] = [
                    'action_id' => $action->id,
                    'action_type' => $action->action_type,
                    'status' => 'success',
                    'result' => $result
                ];
            } catch (\Exception $e) {
                $results[] = [
                    'action_id' => $action->id,
                    'action_type' => $action->action_type,
                    'status' => 'failed',
                    'error' => $e->getMessage()
                ];
            }
        }

        return $results;
    }
}
