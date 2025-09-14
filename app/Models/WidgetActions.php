<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WidgetActions extends Model
{
    protected $fillable = [
        'type',
        'endpoint',
        'http_action',
        'widget_customization_id',
        'is_active',
        'display_name'
    ];

    protected $casts = [
        'http_action' => 'string',
        'is_active' => 'boolean'
    ];

    // WidgetCustomization ile ilişki
    public function widgetCustomization(): BelongsTo
    {
        return $this->belongsTo(WidgetCustomization::class);
    }
}
