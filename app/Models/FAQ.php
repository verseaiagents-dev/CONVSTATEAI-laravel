<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FAQ extends Model
{
    protected $table = 'faqs';

    protected $fillable = [
        'title',
        'description',
        'answer',
        'short_answer',
        'category',
        'is_active',
        'site_id',
        'project_id',
        'sort_order',
        'tags',
        'view_count',
        'helpful_count',
        'not_helpful_count'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
        'view_count' => 'integer',
        'helpful_count' => 'integer',
        'not_helpful_count' => 'integer',
        'tags' => 'array'
    ];

    // Relationships
    public function site(): BelongsTo
    {
        return $this->belongsTo(Site::class);
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    // Scope methods
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order', 'asc')->orderBy('id', 'asc');
    }

    public function scopePopular($query)
    {
        return $query->orderBy('view_count', 'desc');
    }

    public function scopeHelpful($query)
    {
        return $query->orderBy('helpful_count', 'desc');
    }

    // Helper methods
    public function incrementViewCount()
    {
        $this->increment('view_count');
    }

    public function markAsHelpful()
    {
        $this->increment('helpful_count');
    }

    public function markAsNotHelpful()
    {
        $this->increment('not_helpful_count');
    }

    public function getHelpfulPercentageAttribute()
    {
        $total = $this->helpful_count + $this->not_helpful_count;
        if ($total === 0) {
            return 0;
        }
        return round(($this->helpful_count / $total) * 100);
    }

    public function getFormattedTagsAttribute()
    {
        if (empty($this->tags)) {
            return [];
        }
        return $this->tags;
    }
}
