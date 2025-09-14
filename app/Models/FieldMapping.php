<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FieldMapping extends Model
{
    use HasFactory;

    protected $fillable = [
        'knowledge_base_id',
        'source_field',
        'target_field',
        'field_type',
        'is_required',
        'default_value',
        'transformation',
        'validation_rules',
        'mapping_order',
        'is_active'
    ];

    protected $casts = [
        'is_required' => 'boolean',
        'is_active' => 'boolean',
        'transformation' => 'array',
        'validation_rules' => 'array',
        'mapping_order' => 'integer'
    ];

    // Field types constants
    const FIELD_TYPE_TEXT = 'text';
    const FIELD_TYPE_NUMBER = 'number';
    const FIELD_TYPE_DATE = 'date';
    const FIELD_TYPE_BOOLEAN = 'boolean';
    const FIELD_TYPE_ARRAY = 'array';

    // Standard target fields
    const STANDARD_FIELDS = [
        'product_name' => ['name', 'product_name', 'title', 'product_title', 'item_name'],
        'product_description' => ['description', 'product_description', 'desc', 'product_desc', 'details'],
        'product_price' => ['price', 'cost', 'amount', 'product_price', 'sale_price'],
        'product_category' => ['category', 'product_category', 'cat', 'type', 'product_type'],
        'product_brand' => ['brand', 'product_brand', 'manufacturer', 'maker'],
        'product_sku' => ['sku', 'product_sku', 'code', 'product_code', 'item_code'],
        'product_stock' => ['stock', 'inventory', 'quantity', 'available', 'stock_quantity'],
        'product_image' => ['image', 'image_url', 'photo', 'picture', 'img_url'],
        'product_tags' => ['tags', 'keywords', 'labels', 'attributes'],
        'product_rating' => ['rating', 'score', 'stars', 'review_rating'],
        'product_reviews' => ['reviews', 'review_count', 'total_reviews']
    ];

    /**
     * Get the knowledge base that owns this field mapping
     */
    public function knowledgeBase(): BelongsTo
    {
        return $this->belongsTo(KnowledgeBase::class);
    }

    /**
     * Scope for active mappings
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for required mappings
     */
    public function scopeRequired($query)
    {
        return $query->where('is_required', true);
    }

    /**
     * Scope for mappings by field type
     */
    public function scopeByFieldType($query, $fieldType)
    {
        return $query->where('field_type', $fieldType);
    }

    /**
     * Get all available field types
     */
    public static function getFieldTypes(): array
    {
        return [
            self::FIELD_TYPE_TEXT => 'Text',
            self::FIELD_TYPE_NUMBER => 'Number',
            self::FIELD_TYPE_DATE => 'Date',
            self::FIELD_TYPE_BOOLEAN => 'Boolean',
            self::FIELD_TYPE_ARRAY => 'Array'
        ];
    }

    /**
     * Get all standard target fields
     */
    public static function getStandardFields(): array
    {
        return array_keys(self::STANDARD_FIELDS);
    }

    /**
     * Get suggested source fields for a target field
     */
    public static function getSuggestedSourceFields(string $targetField): array
    {
        return self::STANDARD_FIELDS[$targetField] ?? [];
    }

    /**
     * Check if field type is valid
     */
    public static function isValidFieldType(string $fieldType): bool
    {
        return in_array($fieldType, array_keys(self::getFieldTypes()));
    }

    /**
     * Check if target field is standard
     */
    public static function isStandardField(string $targetField): bool
    {
        return in_array($targetField, self::getStandardFields());
    }
}
