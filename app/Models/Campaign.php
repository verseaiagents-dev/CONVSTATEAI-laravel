<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\User;

class Campaign extends Model
{
    protected $fillable = [
        'title',
        'description',
        'category',
        'discount',
        'valid_until',
        'is_active',
        'site_id',
        'project_id',
        'created_by',
        'start_date',
        'end_date',
        'discount_type', // percentage, fixed, buy_x_get_y, free_shipping
        'discount_value',
        'minimum_order_amount',
        'max_usage',
        'current_usage',
        'image_url',
        'terms_conditions',
        'ai_generated',
        'ai_confidence_score',
        // Yeni tasarım modeli fieldları
        'campaign_code',
        'target_audience',
        'product_ids',
        'budget_limit',
        'priority_level',
        'requires_approval',
        'approval_status',
        'notes',
        'metadata',
        'last_modified_at'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'valid_until' => 'datetime',
        'minimum_order_amount' => 'decimal:2',
        'max_usage' => 'integer',
        'current_usage' => 'integer',
        'discount_value' => 'decimal:2',
        'ai_generated' => 'boolean',
        'ai_confidence_score' => 'decimal:2',
        // Yeni tasarım modeli fieldları
        'product_ids' => 'array',
        'budget_limit' => 'decimal:2',
        'priority_level' => 'integer',
        'requires_approval' => 'boolean',
        'metadata' => 'array',
        'last_modified_at' => 'datetime'
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

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // Scope methods
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeValid($query)
    {
        $now = now();
        return $query->where(function($q) use ($now) {
                        $q->where('start_date', '<=', $now)
                          ->orWhereNull('start_date');
                    })
                    ->where(function($q) use ($now) {
                        $q->where('end_date', '>=', $now)
                          ->orWhereNull('end_date');
                    });
    }

    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    // Helper methods
    public function getIsValidAttribute()
    {
        $now = now();
        return $this->is_active && 
               $this->start_date <= $now && 
               ($this->end_date === null || $this->end_date >= $now);
    }

    public function getRemainingUsageAttribute()
    {
        if ($this->max_usage === null) {
            return null;
        }
        return max(0, $this->max_usage - $this->current_usage);
    }

    public function getFormattedDiscountAttribute()
    {
        switch ($this->discount_type) {
            case 'percentage':
                return '%' . $this->discount_value . ' İndirim';
            case 'fixed':
                return $this->discount_value . ' TL İndirim';
            case 'buy_x_get_y':
                return $this->discount;
            case 'free_shipping':
                return 'Ücretsiz Kargo';
            default:
                return $this->discount;
        }
    }

    /**
     * Get campaign status
     */
    public function getStatusAttribute()
    {
        if (!$this->is_active) {
            return 'inactive';
        }
        
        if ($this->start_date && $this->start_date > now()) {
            return 'pending';
        }
        
        if ($this->end_date && $this->end_date < now()) {
            return 'expired';
        }
        
        return 'active';
    }

    /**
     * Get status text for display
     */
    public function getStatusTextAttribute()
    {
        $status = $this->status;
        
        return match($status) {
            'active' => 'Aktif',
            'inactive' => 'Pasif',
            'pending' => 'Beklemede',
            'expired' => 'Süresi Dolmuş',
            default => 'Bilinmiyor'
        };
    }

    /**
     * Get status color for display
     */
    public function getStatusColorAttribute()
    {
        $status = $this->status;
        
        return match($status) {
            'active' => 'green',
            'inactive' => 'red',
            'pending' => 'yellow',
            'expired' => 'gray',
            default => 'gray'
        };
    }

    /**
     * Get priority text for display
     */
    public function getPriorityTextAttribute()
    {
        return match($this->priority_level) {
            1 => 'Düşük',
            2 => 'Normal',
            3 => 'Orta',
            4 => 'Yüksek',
            5 => 'Kritik',
            default => 'Normal'
        };
    }

    /**
     * Get priority color for display
     */
    public function getPriorityColorAttribute()
    {
        return match($this->priority_level) {
            1 => 'gray',
            2 => 'blue',
            3 => 'yellow',
            4 => 'orange',
            5 => 'red',
            default => 'blue'
        };
    }

    /**
     * Get approval status text for display
     */
    public function getApprovalStatusTextAttribute()
    {
        return match($this->approval_status) {
            'pending' => 'Beklemede',
            'approved' => 'Onaylandı',
            'rejected' => 'Reddedildi',
            'draft' => 'Taslak',
            default => 'Beklemede'
        };
    }

    /**
     * Get approval status color for display
     */
    public function getApprovalStatusColorAttribute()
    {
        return match($this->approval_status) {
            'pending' => 'yellow',
            'approved' => 'green',
            'rejected' => 'red',
            'draft' => 'gray',
            default => 'yellow'
        };
    }

    /**
     * Generate unique campaign code
     */
    public static function generateCampaignCode()
    {
        do {
            $code = 'CAMP-' . strtoupper(substr(md5(uniqid()), 0, 8));
        } while (self::where('campaign_code', $code)->exists());
        
        return $code;
    }

    /**
     * Scope for campaigns requiring approval
     */
    public function scopeRequiresApproval($query)
    {
        return $query->where('requires_approval', true);
    }

    /**
     * Scope for approved campaigns
     */
    public function scopeApproved($query)
    {
        return $query->where('approval_status', 'approved');
    }

    /**
     * Scope for pending approval campaigns
     */
    public function scopePendingApproval($query)
    {
        return $query->where('approval_status', 'pending');
    }

    /**
     * Scope for featured campaigns
     */
    public function scopeByPriority($query, $priority)
    {
        return $query->where('priority_level', $priority);
    }
}
