<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Brand extends Model
{
    protected $fillable = [
        'site_id',
        'name',
        'slug',
        'description',
        'logo_url',
        'website_url',
        'country_of_origin',
        'founded_year',
        'is_active',
        'is_featured',
        'sort_order',
        'meta_title',
        'meta_description',
        'seo_url'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_featured' => 'boolean',
        'sort_order' => 'integer',
        'founded_year' => 'integer'
    ];

    // Relationships
    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    // Scope methods
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order', 'asc');
    }

    public function scopeByCountry($query, $country)
    {
        return $query->where('country_of_origin', $country);
    }

    // Helper methods
    public function getProductCountAttribute()
    {
        return $this->products()->count();
    }

    public function getLogoUrlAttribute($value)
    {
        if (!$value) {
            return asset('images/default-brand-logo.png');
        }
        
        if (filter_var($value, FILTER_VALIDATE_URL)) {
            return $value;
        }
        
        return asset('storage/' . $value);
    }
}
