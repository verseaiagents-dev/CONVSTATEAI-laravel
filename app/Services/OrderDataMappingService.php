<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class OrderDataMappingService
{
    private string $apiKey;
    private string $baseUrl;
    private string $model;

    public function __construct()
    {
        $this->apiKey = config('openai.api_key');
        $this->baseUrl = config('openai.base_url', 'https://api.openai.com/v1');
        $this->model = config('openai.model', 'gpt-4o-mini');
    }

    /**
     * Kullanıcının API'sinden gelen veriyi OrderTrackingData formatına çevirir
     */
    public function mapToOrderTrackingData(array $apiResponse, string $trackingNumber): array
    {
        try {
            $systemPrompt = "Sen bir veri eşleştirme uzmanısın. Kullanıcının API'sinden gelen veriyi analiz et ve aşağıdaki OrderTrackingData formatına çevir.

            Hedef format (OrderTrackingData):
            {
                \"order_id\": \"string\",
                \"status\": \"string\",
                \"order_date\": \"string (ISO 8601)\",
                \"items\": [
                    {
                        \"product_id\": \"number\",
                        \"name\": \"string\",
                        \"quantity\": \"number\",
                        \"price\": \"number\"
                    }
                ],
                \"shipping\": {
                    \"courier\": \"string\",
                    \"tracking_number\": \"string\",
                    \"last_update\": \"string (ISO 8601)\",
                    \"location\": \"string\",
                    \"estimated_delivery\": \"string (ISO 8601)\"
                },
                \"message\": \"string\"
            }

            Status değerleri: 'pending', 'processing', 'shipped', 'in_transit', 'delivered', 'cancelled'
            
            Eğer veri bulunamazsa veya hata varsa, success: false döndür.
            Eğer veri varsa, success: true ve data: OrderTrackingData formatında döndür.";

            $userPrompt = "API Response: " . json_encode($apiResponse, JSON_PRETTY_PRINT) . "\n\nTracking Number: " . $trackingNumber;

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Content-Type' => 'application/json',
            ])->post($this->baseUrl . '/chat/completions', [
                'model' => $this->model,
                'messages' => [
                    ['role' => 'system', 'content' => $systemPrompt],
                    ['role' => 'user', 'content' => $userPrompt]
                ],
                'temperature' => 0.1,
                'max_tokens' => 1000
            ]);

            if (!$response->successful()) {
                throw new \Exception('OpenAI API Error: ' . $response->body());
            }

            $data = $response->json();
            $mappedData = json_decode($data['choices'][0]['message']['content'], true);
            
            if (json_last_error() === JSON_ERROR_NONE) {
                return $mappedData;
            }

            // Fallback mapping
            return $this->fallbackMapping($apiResponse, $trackingNumber);

        } catch (\Exception $e) {
            Log::warning('Order data mapping failed, using fallback', ['error' => $e->getMessage()]);
            return $this->fallbackMapping($apiResponse, $trackingNumber);
        }
    }

    /**
     * Fallback mapping - basit eşleştirme
     */
    private function fallbackMapping(array $apiResponse, string $trackingNumber): array
    {
        // Basit eşleştirme mantığı
        $mappedData = [
            'success' => false,
            'data' => null,
            'message' => 'Veri eşleştirilemedi'
        ];

        // Eğer API response'da temel bilgiler varsa
        if (isset($apiResponse['order_id']) || isset($apiResponse['tracking_number']) || isset($apiResponse['status'])) {
            $mappedData = [
                'success' => true,
                'data' => [
                    'order_id' => $apiResponse['order_id'] ?? $trackingNumber,
                    'status' => $this->mapStatus($apiResponse['status'] ?? 'unknown'),
                    'order_date' => $apiResponse['order_date'] ?? date('c'),
                    'items' => $this->mapItems($apiResponse['items'] ?? []),
                    'shipping' => [
                        'courier' => $apiResponse['courier'] ?? $apiResponse['shipping_company'] ?? 'Bilinmeyen',
                        'tracking_number' => $apiResponse['tracking_number'] ?? $trackingNumber,
                        'last_update' => $apiResponse['last_update'] ?? $apiResponse['updated_at'] ?? date('c'),
                        'location' => $apiResponse['location'] ?? $apiResponse['current_location'] ?? 'Bilinmeyen',
                        'estimated_delivery' => $apiResponse['estimated_delivery'] ?? $apiResponse['delivery_date'] ?? date('c', strtotime('+3 days'))
                    ],
                    'message' => $apiResponse['message'] ?? 'Kargo takip bilgileri'
                ]
            ];
        }

        return $mappedData;
    }

    /**
     * Status değerlerini standartlaştır
     */
    private function mapStatus(string $status): string
    {
        $statusMap = [
            'pending' => 'pending',
            'processing' => 'processing',
            'shipped' => 'shipped',
            'in_transit' => 'in_transit',
            'delivered' => 'delivered',
            'cancelled' => 'cancelled',
            'kargoya_verildi' => 'shipped',
            'yolda' => 'in_transit',
            'teslim_edildi' => 'delivered',
            'iptal' => 'cancelled',
            'hazirlaniyor' => 'processing'
        ];

        $status = strtolower(trim($status));
        return $statusMap[$status] ?? 'unknown';
    }

    /**
     * Items array'ini eşleştir
     */
    private function mapItems(array $items): array
    {
        $mappedItems = [];
        
        foreach ($items as $item) {
            $mappedItems[] = [
                'product_id' => $item['product_id'] ?? $item['id'] ?? 0,
                'name' => $item['name'] ?? $item['product_name'] ?? 'Bilinmeyen Ürün',
                'quantity' => $item['quantity'] ?? $item['qty'] ?? 1,
                'price' => $item['price'] ?? $item['unit_price'] ?? 0
            ];
        }

        return $mappedItems;
    }

    /**
     * Kargo takip verisi için özel mapping
     */
    public function mapToCargoTrackingData(array $apiResponse, string $trackingNumber): array
    {
        try {
            $systemPrompt = "Sen bir kargo takip veri eşleştirme uzmanısın. Kullanıcının API'sinden gelen veriyi analiz et ve aşağıdaki CargoTrackingData formatına çevir.

            Hedef format (CargoTrackingData):
            {
                \"intent\": \"order_tracking\",
                \"phase\": \"cargo\",
                \"order_id\": \"string\",
                \"status\": \"string\",
                \"courier\": \"string\",
                \"tracking_number\": \"string\",
                \"last_update\": \"string (ISO 8601)\",
                \"estimated_delivery\": \"string (ISO 8601)\"
            }

            Status değerleri: 'pending', 'in_transit', 'delivered', 'cancelled'
            
            Eğer veri bulunamazsa veya hata varsa, success: false döndür.
            Eğer veri varsa, success: true ve data: CargoTrackingData formatında döndür.";

            $userPrompt = "API Response: " . json_encode($apiResponse, JSON_PRETTY_PRINT) . "\n\nTracking Number: " . $trackingNumber;

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Content-Type' => 'application/json',
            ])->post($this->baseUrl . '/chat/completions', [
                'model' => $this->model,
                'messages' => [
                    ['role' => 'system', 'content' => $systemPrompt],
                    ['role' => 'user', 'content' => $userPrompt]
                ],
                'temperature' => 0.1,
                'max_tokens' => 800
            ]);

            if (!$response->successful()) {
                throw new \Exception('OpenAI API Error: ' . $response->body());
            }

            $data = $response->json();
            $mappedData = json_decode($data['choices'][0]['message']['content'], true);
            
            if (json_last_error() === JSON_ERROR_NONE) {
                return $mappedData;
            }

            // Fallback mapping
            return $this->fallbackCargoMapping($apiResponse, $trackingNumber);

        } catch (\Exception $e) {
            Log::warning('Cargo data mapping failed, using fallback', ['error' => $e->getMessage()]);
            return $this->fallbackCargoMapping($apiResponse, $trackingNumber);
        }
    }

    /**
     * Fallback cargo mapping
     */
    private function fallbackCargoMapping(array $apiResponse, string $trackingNumber): array
    {
        $mappedData = [
            'success' => false,
            'data' => null,
            'message' => 'Kargo verisi eşleştirilemedi'
        ];

        // Eğer API response'da temel bilgiler varsa
        if (isset($apiResponse['tracking_number']) || isset($apiResponse['status']) || isset($apiResponse['courier'])) {
            $mappedData = [
                'success' => true,
                'data' => [
                    'intent' => 'order_tracking',
                    'phase' => 'cargo',
                    'order_id' => $apiResponse['order_id'] ?? $trackingNumber,
                    'status' => $this->mapStatus($apiResponse['status'] ?? 'unknown'),
                    'courier' => $apiResponse['courier'] ?? $apiResponse['shipping_company'] ?? 'Bilinmeyen Kargo',
                    'tracking_number' => $apiResponse['tracking_number'] ?? $trackingNumber,
                    'last_update' => $apiResponse['last_update'] ?? $apiResponse['updated_at'] ?? date('c'),
                    'estimated_delivery' => $apiResponse['estimated_delivery'] ?? $apiResponse['delivery_date'] ?? date('c', strtotime('+3 days'))
                ]
            ];
        }

        return $mappedData;
    }
}
