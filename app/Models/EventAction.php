<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class EventAction extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'event_template_id',
        'action_type',
        'config',
        'seq',
    ];

    protected $casts = [
        'config' => 'array',
        'seq' => 'integer',
    ];

    /**
     * Get the event template that owns this action
     */
    public function eventTemplate(): BelongsTo
    {
        return $this->belongsTo(EventTemplate::class);
    }

    /**
     * Execute the action based on its type
     */
    public function execute(array $payload = []): mixed
    {
        return match ($this->action_type) {
            'http_call' => $this->executeHttpCall($payload),
            'db_insert' => $this->executeDbInsert($payload),
            'log' => $this->executeLog($payload),
            'notify' => $this->executeNotify($payload),
            default => throw new \InvalidArgumentException("Unknown action type: {$this->action_type}")
        };
    }

    /**
     * Execute HTTP call action
     */
    private function executeHttpCall(array $payload): array
    {
        $config = $this->config;
        $url = $config['url'] ?? '';
        $method = strtoupper($config['method'] ?? 'POST');
        $headers = $config['headers'] ?? [];
        $body = $config['body'] ?? $payload;

        if (empty($url)) {
            throw new \InvalidArgumentException('URL is required for HTTP call action');
        }

        $response = Http::withHeaders($headers)->send($method, $url, [
            'json' => $body
        ]);

        return [
            'status_code' => $response->status(),
            'response' => $response->json() ?? $response->body(),
            'headers' => $response->headers()
        ];
    }

    /**
     * Execute database insert action
     */
    private function executeDbInsert(array $payload): array
    {
        $config = $this->config;
        $table = $config['table'] ?? '';
        $data = $config['data'] ?? $payload;

        if (empty($table)) {
            throw new \InvalidArgumentException('Table name is required for DB insert action');
        }

        $insertData = array_merge($data, [
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $id = DB::table($table)->insertGetId($insertData);

        return [
            'inserted_id' => $id,
            'table' => $table,
            'data' => $insertData
        ];
    }

    /**
     * Execute log action
     */
    private function executeLog(array $payload): array
    {
        $config = $this->config;
        $level = $config['level'] ?? 'info';
        $message = $config['message'] ?? 'Event action executed';
        $context = array_merge($config['context'] ?? [], $payload);

        Log::log($level, $message, $context);

        return [
            'logged' => true,
            'level' => $level,
            'message' => $message,
            'context' => $context
        ];
    }

    /**
     * Execute notify action
     */
    private function executeNotify(array $payload): array
    {
        $config = $this->config;
        $type = $config['type'] ?? 'email';
        $recipient = $config['recipient'] ?? '';
        $subject = $config['subject'] ?? 'Notification';
        $content = $config['content'] ?? '';

        // Burada gerçek notification sistemi entegrasyonu yapılabilir
        // Şimdilik sadece log olarak kaydediyoruz
        Log::info('Event notification sent', [
            'type' => $type,
            'recipient' => $recipient,
            'subject' => $subject,
            'content' => $content,
            'payload' => $payload
        ]);

        return [
            'notified' => true,
            'type' => $type,
            'recipient' => $recipient,
            'subject' => $subject
        ];
    }
}
