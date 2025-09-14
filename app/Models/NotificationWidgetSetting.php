<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NotificationWidgetSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'site_id',
        'message_text',
        'is_active',
        'color_theme',
        'display_duration',
        'animation_type',
        'show_close_button',
        'redirect_url'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'show_close_button' => 'boolean',
        'display_duration' => 'integer'
    ];

    // Relationship with Site
    public function site()
    {
        return $this->belongsTo(Site::class);
    }

    // Scope for active settings
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // Get color theme CSS variables
    public function getColorThemeCss()
    {
        $themes = [
            'purple' => [
                'primary' => '#8B5CF6',
                'secondary' => '#A78BFA',
                'gradient' => 'linear-gradient(135deg, #8B5CF6 0%, #A78BFA 100%)'
            ],
            'blue' => [
                'primary' => '#3B82F6',
                'secondary' => '#60A5FA',
                'gradient' => 'linear-gradient(135deg, #3B82F6 0%, #60A5FA 100%)'
            ],
            'green' => [
                'primary' => '#10B981',
                'secondary' => '#34D399',
                'gradient' => 'linear-gradient(135deg, #10B981 0%, #34D399 100%)'
            ],
            'orange' => [
                'primary' => '#F59E0B',
                'secondary' => '#FBBF24',
                'gradient' => 'linear-gradient(135deg, #F59E0B 0%, #FBBF24 100%)'
            ]
        ];

        return $themes[$this->color_theme] ?? $themes['purple'];
    }
}
