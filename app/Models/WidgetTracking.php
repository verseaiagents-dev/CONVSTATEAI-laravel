<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WidgetTracking extends Model
{
    protected $table = 'widget_tracking';
    
    protected $fillable = [
        'session_id',
        'project_id',
        'event_type',
        'intent',
        'product_name',
        'product_url',
        'metadata',
        'user_agent',
        'ip_address'
    ];

    protected $casts = [
        'metadata' => 'array'
    ];
}
