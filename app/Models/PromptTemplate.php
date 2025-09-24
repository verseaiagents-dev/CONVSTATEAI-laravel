<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PromptTemplate extends Model
{
    protected $fillable = [
        'name',
        'category',
        'content',
        'description',
        'variables',
        'metadata',
        'is_active',
        'environment',
        'priority',
        'language',
        'tags',
        'version',
        'created_by',
        'updated_by'
    ];

    protected $guarded = [];

    protected $casts = [
        'variables' => 'array',
        'metadata' => 'array',
        'tags' => 'array',
        'is_active' => 'boolean',
        'priority' => 'integer',
        'version' => 'integer'
    ];

    /**
     * Prompt'u oluşturan kullanıcı
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Prompt'u son güncelleyen kullanıcı
     */
    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Aktif prompt'ları getir
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Production ortamındaki prompt'ları getir
     */
    public function scopeProduction($query)
    {
        return $query->where('environment', 'production');
    }

    /**
     * Test ortamındaki prompt'ları getir
     */
    public function scopeTest($query)
    {
        return $query->where('environment', 'test');
    }

    /**
     * Kategoriye göre filtrele
     */
    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    /**
     * Arama yap
     */
    public function scopeSearch($query, $search)
    {
        return $query->where(function($q) use ($search) {
            $q->where('name', 'like', "%{$search}%")
              ->orWhere('content', 'like', "%{$search}%")
              ->orWhere('description', 'like', "%{$search}%");
        });
    }

    /**
     * Prompt'un değişkenlerini al
     */
    public function getVariablesAttribute($value)
    {
        return json_decode($value, true) ?? [];
    }

    /**
     * Prompt'un metadata'sını al
     */
    public function getMetadataAttribute($value)
    {
        return json_decode($value, true) ?? [];
    }

    /**
     * Prompt'un etiketlerini al
     */
    public function getTagsAttribute($value)
    {
        return json_decode($value, true) ?? [];
    }

    /**
     * Prompt'un değişkenlerini ayarla
     */
    public function setVariablesAttribute($value)
    {
        $this->attributes['variables'] = json_encode($value ?? []);
    }

    /**
     * Prompt'un metadata'sını ayarla
     */
    public function setMetadataAttribute($value)
    {
        $this->attributes['metadata'] = json_encode($value ?? []);
    }

    /**
     * Prompt'un etiketlerini ayarla
     */
    public function setTagsAttribute($value)
    {
        $this->attributes['tags'] = json_encode($value ?? []);
    }

    /**
     * Prompt'un içeriğini ayarla
     */
    public function setContentAttribute($value)
    {
        $this->attributes['content'] = $value;
    }
}
