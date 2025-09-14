<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use App\Models\User;

class KnowledgeBase extends Model
{
    protected $fillable = [
        'site_id',
        'project_id',
        'created_by',
        'name',
        'description',
        'source_type',
        'source_path',
        'source_config',
        'file_type',
        'file_size',
        'total_records',
        'processed_records',
        'chunk_count',
        'is_active',
        'is_processing',
        'last_processed_at',
        'processing_status',
        'error_message'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_processing' => 'boolean',
        'file_size' => 'integer',
        'total_records' => 'integer',
        'processed_records' => 'integer',
        'chunk_count' => 'integer',
        'source_config' => 'array',
        'last_processed_at' => 'datetime'
    ];

    // Relationships
    public function chunks(): HasMany
    {
        return $this->hasMany(KnowledgeChunk::class);
    }

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

    public function fieldMappings(): HasMany
    {
        return $this->hasMany(FieldMapping::class);
    }

    // Scope methods
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeBySourceType($query, $sourceType)
    {
        return $query->where('source_type', $sourceType);
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('processing_status', $status);
    }

    public function scopeCompleted($query)
    {
        return $query->where('processing_status', 'completed');
    }

    public function scopeFailed($query)
    {
        return $query->where('processing_status', 'failed');
    }

    // Helper methods
    public function getProgressPercentageAttribute()
    {
        if ($this->total_records == 0) {
            return 0;
        }
        
        return round(($this->processed_records / $this->total_records) * 100);
    }

    public function getIsCompletedAttribute()
    {
        return $this->processing_status === 'completed';
    }

    public function getIsFailedAttribute()
    {
        return $this->processing_status === 'failed';
    }

    public function getIsProcessingAttribute()
    {
        return $this->processing_status === 'processing';
    }

    public function getFormattedFileSizeAttribute()
    {
        $bytes = $this->file_size;
        $units = ['B', 'KB', 'MB', 'GB'];
        
        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, 2) . ' ' . $units[$i];
    }

    public function markAsProcessing()
    {
        $this->update([
            'is_processing' => true,
            'processing_status' => 'processing',
            'error_message' => null
        ]);
    }

    public function markAsCompleted($chunkCount = null)
    {
        $this->update([
            'is_processing' => false,
            'processing_status' => 'completed',
            'last_processed_at' => now(),
            'chunk_count' => $chunkCount ?? $this->chunk_count,
            'error_message' => null
        ]);
    }

    public function markAsFailed($errorMessage)
    {
        $this->update([
            'is_processing' => false,
            'processing_status' => 'failed',
            'error_message' => $errorMessage
        ]);
    }
}
