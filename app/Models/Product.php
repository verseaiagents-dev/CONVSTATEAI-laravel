<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'site_id',
        'external_id',
        'name',
        'description',
        'short_description',
        'sku',
        'barcode',
        'brand_id',
        'category_id',
        'subcategory_id',
        'price',
        'compare_price',
        'cost_price',
        'sale_price',
        'sale_start_date',
        'sale_end_date',
        'weight',
        'dimensions',
        'stock_quantity',
        'stock',
        'profit_margin',
        'low_stock_threshold',
        'is_active',
        'is_featured',
        'is_bestseller',
        'is_new',
        'is_on_sale',
        'rating_average',
        'rating_count',
        'view_count',
        'sold_count',
        'meta_title',
        'meta_description',
        'meta_keywords',
        'seo_url',
        'tags',
        'attributes',
        'variants',
        'images',
        'documents'
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'compare_price' => 'decimal:2',
        'cost_price' => 'decimal:2',
        'sale_price' => 'decimal:2',
        'weight' => 'decimal:3',
        'dimensions' => 'array',
        'stock_quantity' => 'integer',
        'stock' => 'integer',
        'profit_margin' => 'decimal:2',
        'low_stock_threshold' => 'integer',
        'is_active' => 'boolean',
        'is_featured' => 'boolean',
        'is_bestseller' => 'boolean',
        'is_new' => 'boolean',
        'is_on_sale' => 'boolean',
        'rating_average' => 'decimal:2',
        'rating_count' => 'integer',
        'view_count' => 'integer',
        'sold_count' => 'integer',
        'tags' => 'array',
        'attributes' => 'array',
        'variants' => 'array',
        'images' => 'array',
        'documents' => 'array',
        'sale_start_date' => 'datetime',
        'sale_end_date' => 'datetime'
    ];

    // Relationships
    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function subcategory(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'subcategory_id');
    }

    public function getStockAttribute()
    {
        return $this->stock_quantity ?? 0;
    }

    public function getProfitMarginAttribute()
    {
        if ($this->cost_price && $this->price) {
            return round((($this->cost_price - $this->price) / $this->cost_price) * 100, 2);
        }
        return 20.0; // Default 20%
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    public function cartItems(): HasMany
    {
        return $this->hasMany(CartItem::class);
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    // Scope methods for filtering
    public function scopeByCategory($query, $categoryId)
    {
        return $query->where('category_id', $categoryId);
    }

    public function scopeByBrand($query, $brandId)
    {
        return $query->where('brand_id', $brandId);
    }

    public function scopeByPriceRange($query, $minPrice, $maxPrice)
    {
        return $query->whereBetween('price', [$minPrice, $maxPrice]);
    }

    public function scopeTopRated($query, $limit = 10)
    {
        return $query->orderBy('rating_average', 'desc')->limit($limit);
    }

    public function scopeLowStock($query, $threshold = null)
    {
        $threshold = $threshold ?? $this->low_stock_threshold;
        return $query->where('stock_quantity', '<=', $threshold);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    public function scopeOnSale($query)
    {
        return $query->where('is_on_sale', true)
                    ->where('sale_end_date', '>', now());
    }

    public function scopeInStock($query)
    {
        return $query->where('stock_quantity', '>', 0);
    }

    // Helper methods
    public function getMainImageAttribute()
    {
        $images = $this->images ?? [];
        return $images['main_image'] ?? $images[0] ?? null;
    }

    public function getIsOnSaleAttribute($value)
    {
        if (!$value) return false;
        
        $now = now();
        return $this->sale_start_date <= $now && 
               $this->sale_end_date >= $now;
    }

    public function getDiscountPercentageAttribute()
    {
        if (!$this->is_on_sale || !$this->compare_price) {
            return 0;
        }
        
        return round((($this->compare_price - $this->price) / $this->compare_price) * 100);
    }
}
