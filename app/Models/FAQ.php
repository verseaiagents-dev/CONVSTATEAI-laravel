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
        'not_helpful_count',
        // Yeni tasarım modeli fieldları
        'faq_code',
        'keywords',
        'related_faqs',
        'difficulty_level',
        'estimated_read_time',
        'featured',
        'author',
        'last_reviewed_at',
        'review_notes',
        'metadata'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
        'view_count' => 'integer',
        'helpful_count' => 'integer',
        'not_helpful_count' => 'integer',
        'tags' => 'array',
        // Yeni tasarım modeli fieldları
        'related_faqs' => 'array',
        'estimated_read_time' => 'integer',
        'featured' => 'boolean',
        'last_reviewed_at' => 'datetime',
        'metadata' => 'array'
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

    /**
     * Get difficulty level text for display
     */
    public function getDifficultyTextAttribute()
    {
        return match($this->difficulty_level) {
            'easy' => 'Kolay',
            'medium' => 'Orta',
            'hard' => 'Zor',
            'expert' => 'Uzman',
            default => 'Kolay'
        };
    }

    /**
     * Get difficulty level color for display
     */
    public function getDifficultyColorAttribute()
    {
        return match($this->difficulty_level) {
            'easy' => 'green',
            'medium' => 'yellow',
            'hard' => 'orange',
            'expert' => 'red',
            default => 'green'
        };
    }

    /**
     * Get formatted read time
     */
    public function getFormattedReadTimeAttribute()
    {
        if (!$this->estimated_read_time) {
            return 'Belirtilmemiş';
        }
        
        if ($this->estimated_read_time < 60) {
            return $this->estimated_read_time . ' saniye';
        }
        
        $minutes = floor($this->estimated_read_time / 60);
        $seconds = $this->estimated_read_time % 60;
        
        if ($seconds > 0) {
            return $minutes . ' dakika ' . $seconds . ' saniye';
        }
        
        return $minutes . ' dakika';
    }

    /**
     * Generate unique FAQ code
     */
    public static function generateFaqCode()
    {
        do {
            $code = 'FAQ-' . strtoupper(substr(md5(uniqid()), 0, 8));
        } while (self::where('faq_code', $code)->exists());
        
        return $code;
    }

    /**
     * Scope for featured FAQs
     */
    public function scopeFeatured($query)
    {
        return $query->where('featured', true);
    }

    /**
     * Scope for FAQs by difficulty level
     */
    public function scopeByDifficulty($query, $difficulty)
    {
        return $query->where('difficulty_level', $difficulty);
    }

    /**
     * Scope for FAQs by author
     */
    public function scopeByAuthor($query, $author)
    {
        return $query->where('author', $author);
    }

    /**
     * Scope for recently reviewed FAQs
     */
    public function scopeRecentlyReviewed($query, $days = 30)
    {
        return $query->where('last_reviewed_at', '>=', now()->subDays($days));
    }

    /**
     * Scope for FAQs needing review
     */
    public function scopeNeedsReview($query, $days = 90)
    {
        return $query->where(function($q) use ($days) {
            $q->whereNull('last_reviewed_at')
              ->orWhere('last_reviewed_at', '<', now()->subDays($days));
        });
    }

    /**
     * Calculate estimated read time based on content
     */
    public function calculateReadTime()
    {
        $wordCount = str_word_count($this->answer . ' ' . $this->description);
        $this->estimated_read_time = max(30, round($wordCount / 200 * 60)); // 200 words per minute
        return $this->estimated_read_time;
    }

    /**
     * Mark as reviewed
     */
    public function markAsReviewed($notes = null)
    {
        $this->last_reviewed_at = now();
        if ($notes) {
            $this->review_notes = $notes;
        }
        $this->save();
    }
}
