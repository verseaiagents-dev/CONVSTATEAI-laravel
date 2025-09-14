<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class KnowledgeChunk extends Model
{
    use HasFactory;

    protected $table = 'knowledge_chunks';

    protected $fillable = [
        'knowledge_base_id',
        'chunk_index',
        'content',
        'content_hash',
        'content_type',
        'entity_type',
        'entity_id',
        'metadata',
        'embedding_vector',
        'vector_id',
        'chunk_size',
        'word_count',
        'is_indexed',
    ];

    protected $casts = [
        'metadata' => 'array',
        'embedding_vector' => 'array',
        'is_indexed' => 'boolean',
        'chunk_size' => 'integer',
        'word_count' => 'integer',
    ];

    /**
     * Get the knowledge base that owns this chunk
     */
    public function knowledgeBase(): BelongsTo
    {
        return $this->belongsTo(KnowledgeBase::class);
    }

    /**
     * Scope for indexed chunks
     */
    public function scopeIndexed($query)
    {
        return $query->where('is_indexed', true);
    }

    /**
     * Scope for chunks by content type
     */
    public function scopeByContentType($query, $contentType)
    {
        return $query->where('content_type', $contentType);
    }

    /**
     * Scope for chunks by entity type
     */
    public function scopeByEntityType($query, $entityType)
    {
        return $query->where('entity_type', $entityType);
    }

    /**
     * Get content preview (first 100 characters)
     */
    public function getContentPreviewAttribute(): string
    {
        return mb_substr($this->content, 0, 100) . (mb_strlen($this->content) > 100 ? '...' : '');
    }

    /**
     * Get formatted chunk size
     */
    public function getFormattedChunkSizeAttribute(): string
    {
        if ($this->chunk_size < 1024) {
            return $this->chunk_size . ' B';
        } elseif ($this->chunk_size < 1024 * 1024) {
            return round($this->chunk_size / 1024, 2) . ' KB';
        } else {
            return round($this->chunk_size / (1024 * 1024), 2) . ' MB';
        }
    }

    /**
     * Check if chunk has embedding
     */
    public function getHasEmbeddingAttribute(): bool
    {
        return !empty($this->embedding_vector);
    }

    /**
     * Get metadata value by key
     */
    public function getMetadataValue(string $key, $default = null)
    {
        return data_get($this->metadata, $key, $default);
    }

    /**
     * Set metadata value
     */
    public function setMetadataValue(string $key, $value): void
    {
        $metadata = $this->metadata ?? [];
        $metadata[$key] = $value;
        $this->metadata = $metadata;
    }

    /**
     * Mark chunk as indexed
     */
    public function markAsIndexed(): void
    {
        $this->update(['is_indexed' => true]);
    }

    /**
     * Mark chunk as not indexed
     */
    public function markAsNotIndexed(): void
    {
        $this->update(['is_indexed' => false]);
    }

    /**
     * Update embedding vector
     */
    public function updateEmbedding(array $vector, ?string $vectorId = null): void
    {
        $this->update([
            'embedding_vector' => $vector,
            'vector_id' => $vectorId,
            'is_indexed' => true,
        ]);
    }

    /**
     * Clear embedding vector
     */
    public function clearEmbedding(): void
    {
        $this->update([
            'embedding_vector' => null,
            'vector_id' => null,
            'is_indexed' => false,
        ]);
    }
}
