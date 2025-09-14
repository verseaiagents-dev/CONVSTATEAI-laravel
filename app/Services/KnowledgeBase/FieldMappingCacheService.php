<?php

namespace App\Services\KnowledgeBase;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class FieldMappingCacheService
{
    private const CACHE_PREFIX = 'field_mapping';
    private const CACHE_TTL = 3600; // 1 hour

    /**
     * Get cached field detection results
     */
    public function getCachedFieldDetection(int $knowledgeBaseId): ?array
    {
        $cacheKey = $this->getFieldDetectionCacheKey($knowledgeBaseId);
        
        try {
            return Cache::get($cacheKey);
        } catch (\Exception $e) {
            Log::warning("Cache retrieval failed for KB {$knowledgeBaseId}: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Cache field detection results
     */
    public function cacheFieldDetection(int $knowledgeBaseId, array $results): bool
    {
        $cacheKey = $this->getFieldDetectionCacheKey($knowledgeBaseId);
        
        try {
            Cache::put($cacheKey, $results, self::CACHE_TTL);
            return true;
        } catch (\Exception $e) {
            Log::warning("Cache storage failed for KB {$knowledgeBaseId}: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Get cached field mappings
     */
    public function getCachedFieldMappings(int $knowledgeBaseId): ?array
    {
        $cacheKey = $this->getFieldMappingsCacheKey($knowledgeBaseId);
        
        try {
            return Cache::get($cacheKey);
        } catch (\Exception $e) {
            Log::warning("Cache retrieval failed for KB {$knowledgeBaseId}: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Cache field mappings
     */
    public function cacheFieldMappings(int $knowledgeBaseId, array $mappings): bool
    {
        $cacheKey = $this->getFieldMappingsCacheKey($knowledgeBaseId);
        
        try {
            Cache::put($cacheKey, $mappings, self::CACHE_TTL);
            return true;
        } catch (\Exception $e) {
            Log::warning("Cache storage failed for KB {$knowledgeBaseId}: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Get cached transformation results
     */
    public function getCachedTransformation(int $knowledgeBaseId, string $dataHash): ?array
    {
        $cacheKey = $this->getTransformationCacheKey($knowledgeBaseId, $dataHash);
        
        try {
            return Cache::get($cacheKey);
        } catch (\Exception $e) {
            Log::warning("Cache retrieval failed for KB {$knowledgeBaseId}: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Cache transformation results
     */
    public function cacheTransformation(int $knowledgeBaseId, string $dataHash, array $results): bool
    {
        $cacheKey = $this->getTransformationCacheKey($knowledgeBaseId, $dataHash);
        
        try {
            Cache::put($cacheKey, $results, self::CACHE_TTL);
            return true;
        } catch (\Exception $e) {
            Log::warning("Cache storage failed for KB {$knowledgeBaseId}: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Get cached validation results
     */
    public function getCachedValidation(int $knowledgeBaseId, string $dataHash): ?array
    {
        $cacheKey = $this->getValidationCacheKey($knowledgeBaseId, $dataHash);
        
        try {
            return Cache::get($cacheKey);
        } catch (\Exception $e) {
            Log::warning("Cache retrieval failed for KB {$knowledgeBaseId}: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Cache validation results
     */
    public function cacheValidation(int $knowledgeBaseId, string $dataHash, array $results): bool
    {
        $cacheKey = $this->getValidationCacheKey($knowledgeBaseId, $dataHash);
        
        try {
            Cache::put($cacheKey, $results, self::CACHE_TTL);
            return true;
        } catch (\Exception $e) {
            Log::warning("Cache storage failed for KB {$knowledgeBaseId}: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Clear all cache for a knowledge base
     */
    public function clearKnowledgeBaseCache(int $knowledgeBaseId): bool
    {
        try {
            $patterns = [
                $this->getFieldDetectionCacheKey($knowledgeBaseId),
                $this->getFieldMappingsCacheKey($knowledgeBaseId),
                $this->getTransformationCacheKey($knowledgeBaseId, '*'),
                $this->getValidationCacheKey($knowledgeBaseId, '*')
            ];

            foreach ($patterns as $pattern) {
                if (str_contains($pattern, '*')) {
                    // Clear pattern-based cache keys
                    $this->clearPatternCache($pattern);
                } else {
                    Cache::forget($pattern);
                }
            }

            return true;
        } catch (\Exception $e) {
            Log::warning("Cache clearing failed for KB {$knowledgeBaseId}: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Clear pattern-based cache keys
     */
    private function clearPatternCache(string $pattern): void
    {
        try {
            $keys = Cache::get('cache_keys_' . md5($pattern), []);
            foreach ($keys as $key) {
                Cache::forget($key);
            }
        } catch (\Exception $e) {
            Log::warning("Pattern cache clearing failed: " . $e->getMessage());
        }
    }

    /**
     * Get cache statistics
     */
    public function getCacheStats(): array
    {
        try {
            $stats = [
                'total_keys' => 0,
                'memory_usage' => 0,
                'hit_rate' => 0,
                'miss_rate' => 0
            ];

            // This is a simplified implementation
            // In production, you might want to use Redis INFO command or similar
            $stats['total_keys'] = Cache::get('cache_stats_total_keys', 0);
            $stats['memory_usage'] = Cache::get('cache_stats_memory', 0);

            return $stats;
        } catch (\Exception $e) {
            Log::warning("Cache stats retrieval failed: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Generate cache keys
     */
    private function getFieldDetectionCacheKey(int $knowledgeBaseId): string
    {
        return self::CACHE_PREFIX . ":detection:{$knowledgeBaseId}";
    }

    private function getFieldMappingsCacheKey(int $knowledgeBaseId): string
    {
        return self::CACHE_PREFIX . ":mappings:{$knowledgeBaseId}";
    }

    private function getTransformationCacheKey(int $knowledgeBaseId, string $dataHash): string
    {
        return self::CACHE_PREFIX . ":transformation:{$knowledgeBaseId}:{$dataHash}";
    }

    private function getValidationCacheKey(int $knowledgeBaseId, string $dataHash): string
    {
        return self::CACHE_PREFIX . ":validation:{$knowledgeBaseId}:{$dataHash}";
    }

    /**
     * Generate data hash for caching
     */
    public function generateDataHash(array $data): string
    {
        return md5(serialize($data));
    }

    /**
     * Check if cache is enabled
     */
    public function isCacheEnabled(): bool
    {
        return config('cache.default') !== 'null';
    }
}
