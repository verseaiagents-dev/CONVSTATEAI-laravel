<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Intent extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'code',
        'name',
        'description',
        'threshold',
    ];

    protected $casts = [
        'threshold' => 'decimal:2',
    ];

    /**
     * Get the keywords for this intent
     */
    public function keywords(): HasMany
    {
        return $this->hasMany(IntentKeyword::class);
    }

    /**
     * Get the event templates for this intent
     */
    public function eventTemplates(): HasMany
    {
        return $this->hasMany(EventTemplate::class);
    }

    /**
     * Get the event logs for this intent
     */
    public function eventLogs(): HasMany
    {
        return $this->hasMany(EventLog::class, 'intent_code', 'code');
    }

    /**
     * Check if intent matches given text based on keywords and threshold
     */
    public function matches(string $text): bool
    {
        $text = strtolower($text);
        $totalWeight = 0;
        $matchedWeight = 0;

        foreach ($this->keywords as $keyword) {
            $totalWeight += $keyword->weight;
            if (str_contains($text, strtolower($keyword->keyword))) {
                $matchedWeight += $keyword->weight;
            }
        }

        if ($totalWeight === 0) {
            return false;
        }

        $similarity = $matchedWeight / $totalWeight;
        return $similarity >= $this->threshold;
    }
}
