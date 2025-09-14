<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Models\WidgetActions;
use App\Models\WidgetCustomization;
use App\Services\OrderDataMappingService;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class CargoTrackingController extends Controller
{
    private OrderDataMappingService $mappingService;

    public function __construct(OrderDataMappingService $mappingService)
    {
        $this->mappingService = $mappingService;
    }

    /**
     * GET ile kargo takip
     */
    public function trackCargo(Request $request, string $trackingNumber): JsonResponse
    {
        return $this->processCargoTracking($request, $trackingNumber);
    }

    /**
     * POST ile kargo takip
     */
    public function trackCargoPost(Request $request): JsonResponse
    {
        $trackingNumber = $request->input('tracking_number');
        
        if (!$trackingNumber) {
            return response()->json([
                'success' => false,
                'message' => 'Kargo takip numarası gerekli'
            ], 400);
        }

        return $this->processCargoTracking($request, $trackingNumber);
    }

    /**
     * Kargo takip işlemini gerçekleştir
     */
    private function processCargoTracking(Request $request, string $trackingNumber): JsonResponse
    {
        try {
            // Widget için en son aktif kullanıcının ayarlarını al
            $widgetCustomization = WidgetCustomization::where('is_active', true)
                ->orderBy('updated_at', 'desc')
                ->first();
            
            if (!$widgetCustomization) {
                return response()->json([
                    'success' => false,
                    'message' => 'Kargo takip özelliği yakında açılacak',
                    'data' => [
                        'feature_disabled' => true
                    ]
                ], 200);
            }

            // Kargo durumu endpoint'ini al
            $endpoint = $widgetCustomization->getKargoDurumuEndpoint();
            
            if (!$endpoint) {
                return response()->json([
                    'success' => false,
                    'message' => 'Kargo takip özelliği yakında açılacak',
                    'data' => [
                        'feature_disabled' => true
                    ]
                ], 200);
            }

            // HTTP method'u al
            $kargoAction = $widgetCustomization->widgetActions()
                ->where('type', 'kargo_durumu_endpoint')
                ->first();
            $httpMethod = $kargoAction ? $kargoAction->http_action : 'GET';
            
            $apiResponse = $this->callUserAPI($endpoint, $trackingNumber, $httpMethod);
            
            if (!$apiResponse['success']) {
                return response()->json([
                    'success' => false,
                    'message' => $apiResponse['message'] ?? 'Kargo bilgisi alınamadı'
                ], 200);
            }

            // AI ile veri eşleştirme
            $mappedData = $this->mappingService->mapToCargoTrackingData($apiResponse['data'], $trackingNumber);
            
            if (!$mappedData['success']) {
                return response()->json([
                    'success' => false,
                    'message' => 'Kargo numarası ile kargo bulunamadı'
                ], 200);
            }

            return response()->json([
                'success' => true,
                'data' => $mappedData['data']
            ]);

        } catch (\Exception $e) {
            Log::error('Cargo tracking error', [
                'error' => $e->getMessage(),
                'tracking_number' => $trackingNumber,
                'user_id' => Auth::id()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Kargo takip sırasında hata oluştu'
            ], 500);
        }
    }

    /**
     * Kullanıcının API'sine istek gönder
     */
    private function callUserAPI(string $endpoint, string $trackingNumber, string $method = 'GET'): array
    {
        try {
            // Endpoint'e tracking number parametresi ekle
            $url = $this->buildUrlWithParams($endpoint, $trackingNumber);
            
            $response = Http::timeout(10)->$method($url);
            
            if ($response->successful()) {
                return [
                    'success' => true,
                    'data' => $response->json()
                ];
            } else {
                return [
                    'success' => false,
                    'message' => 'API isteği başarısız: HTTP ' . $response->status()
                ];
            }
            
        } catch (\Exception $e) {
            Log::warning('User API call failed', [
                'endpoint' => $endpoint,
                'tracking_number' => $trackingNumber,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'message' => 'API bağlantısı kurulamadı'
            ];
        }
    }

    /**
     * URL'e tracking number parametresi ekle
     */
    private function buildUrlWithParams(string $endpoint, string $trackingNumber): string
    {
        $parsedUrl = parse_url($endpoint);
        
        if (isset($parsedUrl['query'])) {
            parse_str($parsedUrl['query'], $queryParams);
        } else {
            $queryParams = [];
        }
        
        // Yaygın parametre isimlerini dene
        $possibleParams = ['tracking_number', 'trackingNumber', 'tracking', 'number', 'id', 'order_id'];
        $paramAdded = false;
        
        foreach ($possibleParams as $param) {
            if (!isset($queryParams[$param])) {
                $queryParams[$param] = $trackingNumber;
                $paramAdded = true;
                break;
            }
        }
        
        // Eğer hiç parametre eklenmediyse, varsayılan olarak tracking_number ekle
        if (!$paramAdded) {
            $queryParams['tracking_number'] = $trackingNumber;
        }
        
        $queryString = http_build_query($queryParams);
        
        return $parsedUrl['scheme'] . '://' . $parsedUrl['host'] . 
               (isset($parsedUrl['port']) ? ':' . $parsedUrl['port'] : '') .
               (isset($parsedUrl['path']) ? $parsedUrl['path'] : '/') .
               '?' . $queryString;
    }
}
