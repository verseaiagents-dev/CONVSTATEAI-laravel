<?php

namespace App\Services;

use App\Models\PromptTemplate;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;

class RealTimeSyncService
{
    private $redis;
    private $channelPrefix = 'prompt_sync:';

    public function __construct()
    {
        $this->redis = Redis::connection();
    }

    /**
     * Prompt değişikliğini tüm client'lara bildir
     */
    public function notifyPromptChange(PromptTemplate $prompt, string $action = 'updated'): void
    {
        try {
            $data = [
                'action' => $action,
                'prompt_id' => $prompt->id,
                'category' => $prompt->category,
                'environment' => $prompt->environment,
                'timestamp' => now()->toISOString(),
                'version' => $prompt->version
            ];

            // Redis pub/sub ile bildirim gönder
            $this->redis->publish($this->channelPrefix . 'prompt_changes', json_encode($data));

            // Cache'i temizle
            $this->clearPromptCache($prompt->category, $prompt->environment);

            Log::info('Prompt change notification sent', [
                'prompt_id' => $prompt->id,
                'action' => $action,
                'category' => $prompt->category
            ]);

        } catch (\Exception $e) {
            Log::error('Error sending prompt change notification', [
                'prompt_id' => $prompt->id,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Prompt cache'ini temizle
     */
    public function clearPromptCache(string $category = null, string $environment = null): void
    {
        try {
            if ($category && $environment) {
                // Belirli kategori ve environment için cache temizle
                $keys = [
                    "prompt_category_{$category}_{$environment}",
                    "prompt_category_all_{$category}_{$environment}"
                ];
                
                foreach ($keys as $key) {
                    Cache::forget($key);
                }
            } else {
                // Tüm prompt cache'lerini temizle
                $pattern = 'prompt_category_*';
                $keys = Cache::getRedis()->keys($pattern);
                
                if (!empty($keys)) {
                    Cache::getRedis()->del($keys);
                }
            }

            Log::info('Prompt cache cleared', [
                'category' => $category,
                'environment' => $environment
            ]);

        } catch (\Exception $e) {
            Log::error('Error clearing prompt cache', [
                'error' => $e->getMessage(),
                'category' => $category,
                'environment' => $environment
            ]);
        }
    }

    /**
     * Client'a prompt güncellemesi gönder
     */
    public function sendPromptUpdateToClient(string $clientId, array $promptData): void
    {
        try {
            $data = [
                'type' => 'prompt_update',
                'client_id' => $clientId,
                'data' => $promptData,
                'timestamp' => now()->toISOString()
            ];

            $this->redis->publish($this->channelPrefix . 'client_updates', json_encode($data));

        } catch (\Exception $e) {
            Log::error('Error sending prompt update to client', [
                'client_id' => $clientId,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Tüm aktif client'lara broadcast gönder
     */
    public function broadcastToAllClients(array $data): void
    {
        try {
            $broadcastData = [
                'type' => 'broadcast',
                'data' => $data,
                'timestamp' => now()->toISOString()
            ];

            $this->redis->publish($this->channelPrefix . 'broadcast', json_encode($broadcastData));

        } catch (\Exception $e) {
            Log::error('Error broadcasting to all clients', [
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Client bağlantı durumunu güncelle
     */
    public function updateClientStatus(string $clientId, bool $isOnline): void
    {
        try {
            $status = $isOnline ? 'online' : 'offline';
            $this->redis->hset('client_status', $clientId, $status);
            $this->redis->expire('client_status', 3600); // 1 saat expire

            Log::debug('Client status updated', [
                'client_id' => $clientId,
                'status' => $status
            ]);

        } catch (\Exception $e) {
            Log::error('Error updating client status', [
                'client_id' => $clientId,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Aktif client'ları getir
     */
    public function getActiveClients(): array
    {
        try {
            $clients = $this->redis->hgetall('client_status');
            return array_filter($clients, fn($status) => $status === 'online');

        } catch (\Exception $e) {
            Log::error('Error getting active clients', [
                'error' => $e->getMessage()
            ]);
            return [];
        }
    }

    /**
     * Prompt istatistiklerini güncelle
     */
    public function updatePromptStatistics(int $promptId, string $action): void
    {
        try {
            $key = "prompt_stats:{$promptId}";
            
            switch ($action) {
                case 'used':
                    $this->redis->hincrby($key, 'usage_count', 1);
                    $this->redis->hset($key, 'last_used', now()->toISOString());
                    break;
                case 'tested':
                    $this->redis->hincrby($key, 'test_count', 1);
                    break;
                case 'updated':
                    $this->redis->hincrby($key, 'update_count', 1);
                    $this->redis->hset($key, 'last_updated', now()->toISOString());
                    break;
            }

            // 30 gün expire
            $this->redis->expire($key, 2592000);

        } catch (\Exception $e) {
            Log::error('Error updating prompt statistics', [
                'prompt_id' => $promptId,
                'action' => $action,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Prompt istatistiklerini getir
     */
    public function getPromptStatistics(int $promptId): array
    {
        try {
            $key = "prompt_stats:{$promptId}";
            $stats = $this->redis->hgetall($key);
            
            return [
                'usage_count' => (int) ($stats['usage_count'] ?? 0),
                'test_count' => (int) ($stats['test_count'] ?? 0),
                'update_count' => (int) ($stats['update_count'] ?? 0),
                'last_used' => $stats['last_used'] ?? null,
                'last_updated' => $stats['last_updated'] ?? null
            ];

        } catch (\Exception $e) {
            Log::error('Error getting prompt statistics', [
                'prompt_id' => $promptId,
                'error' => $e->getMessage()
            ]);
            return [];
        }
    }

    /**
     * System health check
     */
    public function getSystemHealth(): array
    {
        try {
            $redisStatus = $this->redis->ping() === 'PONG';
            $activeClients = count($this->getActiveClients());
            
            return [
                'redis_connected' => $redisStatus,
                'active_clients' => $activeClients,
                'timestamp' => now()->toISOString(),
                'status' => $redisStatus ? 'healthy' : 'unhealthy'
            ];

        } catch (\Exception $e) {
            return [
                'redis_connected' => false,
                'active_clients' => 0,
                'timestamp' => now()->toISOString(),
                'status' => 'unhealthy',
                'error' => $e->getMessage()
            ];
        }
    }
}
