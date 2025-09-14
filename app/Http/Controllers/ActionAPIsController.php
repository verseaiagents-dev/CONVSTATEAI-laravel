<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Models\EnhancedChatSession;
use App\Models\Product;
use App\Models\KnowledgeChunk;
use App\Models\UserEndpoint;
use App\Helpers\ProductImageHelper;

class ActionAPIsController extends Controller
{
    /**
     * Show Action APIs page
     */
    public function index()
    {
        $user = Auth::user();
        
        // Kullanıcının tanımladığı endpoint'leri getir
        $userEndpoints = UserEndpoint::where('user_id', $user->id)
            ->orderBy('intent_type')
            ->get()
            ->keyBy('intent_type');
        
        // Desteklenen intent tipleri
        $intentTypes = UserEndpoint::getIntentTypes();
        
        // Son kullanılan API'ler
        $recentUsage = $this->getRecentAPIUsage($user->id);
        
        // API istatistikleri
        $stats = $this->getAPIStats($user->id);
        
        return view('dashboard.action-apis', compact('user', 'userEndpoints', 'intentTypes', 'recentUsage', 'stats'));
    }
    
    /**
     * Store or update user endpoint
     */
    public function store(Request $request)
    {
        
        $request->validate([
            'intent_type' => 'required|string|in:' . implode(',', array_keys(UserEndpoint::getIntentTypes())),
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'method' => 'required|string|in:' . implode(',', UserEndpoint::getMethods()),
            'endpoint_url' => 'required|url',
            'headers' => 'nullable|array',
            'payload_template' => 'nullable|array',
            'timeout' => 'nullable|integer|min:1|max:300'
        ]);

        $user = Auth::user();
        
        // Aynı intent için mevcut endpoint'i deaktif et
        UserEndpoint::where('user_id', $user->id)
            ->where('intent_type', $request->input('intent_type'))
            ->update(['is_active' => false]);

        // Yeni endpoint oluştur
        $endpoint = UserEndpoint::create([
            'user_id' => $user->id,
            'intent_type' => $request->input('intent_type'),
            'name' => $request->input('name'),
            'description' => $request->input('description'),
            'method' => $request->input('method'),
            'endpoint_url' => $request->input('endpoint_url'),
            'headers' => $request->input('headers', []),
            'payload_template' => $request->input('payload_template', []),
            'timeout' => $request->input('timeout', 30),
            'is_active' => true
        ]);

        
        return response()->json([
            'success' => true,
            'message' => 'Endpoint başarıyla kaydedildi',
            'endpoint' => $endpoint
        ]);
    }

    /**
     * Update user endpoint
     */
    public function update(Request $request, $id)
    {
        $endpoint = UserEndpoint::where('user_id', Auth::id())->findOrFail($id);
        
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'method' => 'required|string|in:' . implode(',', UserEndpoint::getMethods()),
            'endpoint_url' => 'required|url',
            'headers' => 'nullable|array',
            'payload_template' => 'nullable|array',
            'timeout' => 'nullable|integer|min:1|max:300',
            'is_active' => 'boolean'
        ]);

        $endpoint->update($request->only([
            'name', 'description', 'method', 'endpoint_url', 
            'headers', 'payload_template', 'timeout', 'is_active'
        ]));

        return response()->json([
            'success' => true,
            'message' => 'Endpoint başarıyla güncellendi',
            'endpoint' => $endpoint
        ]);
    }

    /**
     * Delete user endpoint
     */
    public function destroy($id)
    {
        $endpoint = UserEndpoint::where('user_id', Auth::id())->findOrFail($id);
        $endpoint->delete();

        return response()->json([
            'success' => true,
            'message' => 'Endpoint başarıyla silindi'
        ]);
    }

    /**
     * Test user endpoint
     */
    public function testEndpoint(Request $request, $id)
    {
        $endpoint = UserEndpoint::where('user_id', Auth::id())->findOrFail($id);
        
        $request->validate([
            'test_data' => 'nullable|array'
        ]);

        try {
            $testData = $request->input('test_data', []);
            
            // Test payload'u hazırla
            $payload = array_merge($endpoint->payload_template ?? [], $testData);
            
            // API'yi test et
            $response = $this->makeAPICall($endpoint, $payload);
            
            return response()->json([
                'success' => true,
                'response' => $response,
                'endpoint' => $endpoint->endpoint_url,
                'method' => $endpoint->method
            ]);
            
        } catch (\Exception $e) {
            Log::error('Endpoint test error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Make API call to user endpoint
     */
    private function makeAPICall(UserEndpoint $endpoint, array $payload = [])
    {
        $client = new \GuzzleHttp\Client(['timeout' => $endpoint->timeout]);
        
        $options = [
            'headers' => array_merge([
                'Content-Type' => 'application/json',
                'Accept' => 'application/json'
            ], $endpoint->headers ?? [])
        ];

        if ($endpoint->method === 'POST' && !empty($payload)) {
            $options['json'] = $payload;
        } elseif ($endpoint->method === 'GET' && !empty($payload)) {
            $options['query'] = $payload;
        }

        $response = $client->request($endpoint->method, $endpoint->endpoint_url, $options);
        
        return [
            'status_code' => $response->getStatusCode(),
            'headers' => $response->getHeaders(),
            'body' => json_decode($response->getBody()->getContents(), true)
        ];
    }
    
    /**
     * Get recent API usage
     */
    private function getRecentAPIUsage($userId)
    {
        try {
            // Son 7 gün içindeki API kullanımlarını getir
            $sessions = EnhancedChatSession::where('user_id', $userId)
                ->where('created_at', '>=', now()->subDays(7))
                ->whereNotNull('intent_history')
                ->select('intent_history', 'created_at')
                ->orderBy('created_at', 'desc')
                ->take(10)
                ->get();

            $intentUsage = collect();
            
            foreach ($sessions as $session) {
                if ($session->intent_history) {
                    $intents = is_string($session->intent_history) 
                        ? json_decode($session->intent_history, true) 
                        : $session->intent_history;
                    
                    if (is_array($intents)) {
                        foreach ($intents as $intent) {
                            if (isset($intent['intent'])) {
                                $intentName = $intent['intent'];
                                if (!$intentUsage->has($intentName)) {
                                    $intentUsage->put($intentName, [
                                        'count' => 0,
                                        'last_used' => $session->created_at
                                    ]);
                                }
                                $intentUsage[$intentName]['count']++;
                                if ($session->created_at > $intentUsage[$intentName]['last_used']) {
                                    $intentUsage[$intentName]['last_used'] = $session->created_at;
                                }
                            }
                        }
                    }
                }
            }
            
            return $intentUsage;
        } catch (\Exception $e) {
            \Log::error('Error getting recent API usage: ' . $e->getMessage());
            return collect();
        }
    }
    
    /**
     * Get API statistics
     */
    private function getAPIStats($userId)
    {
        try {
            $totalSessions = EnhancedChatSession::where('user_id', $userId)->count();
            
            // Intent history'den unique intent'leri say
            $sessions = EnhancedChatSession::where('user_id', $userId)
                ->whereNotNull('intent_history')
                ->get();
            
            $uniqueIntents = collect();
            foreach ($sessions as $session) {
                if ($session->intent_history) {
                    $intents = is_string($session->intent_history) 
                        ? json_decode($session->intent_history, true) 
                        : $session->intent_history;
                    
                    if (is_array($intents)) {
                        foreach ($intents as $intent) {
                            if (isset($intent['intent'])) {
                            }
                        }
                    }
                }
            }
            
            $totalIntents = $uniqueIntents->unique()->count();
            
            // Top intent'leri hesapla
            $intentCounts = $uniqueIntents->countBy();
            $topIntents = $intentCounts->sortDesc()->take(5)->map(function ($count, $intent) {
                return (object) ['intent' => $intent, 'count' => $count];
            })->values();
            
            return [
                'total_sessions' => $totalSessions,
                'total_intents' => $totalIntents,
                'top_intents' => $topIntents
            ];
        } catch (\Exception $e) {
            \Log::error('Error getting API stats: ' . $e->getMessage());
            return [
                'total_sessions' => 0,
                'total_intents' => 0,
                'top_intents' => collect()
            ];
        }
    }
    
    /**
     * Order tracking API endpoint
     */
    public function orderTracking(Request $request)
    {
        $request->validate([
            'order_number' => 'required|string',
            'session_id' => 'nullable|string'
        ]);
        
        try {
            $orderNumber = $request->input('order_number');
            $sessionId = $request->input('session_id', uniqid());
            
            // Mock order data - gerçek uygulamada veritabanından çekilecek
            $orderData = [
                'order_number' => $orderNumber,
                'status' => 'shipped',
                'order_date' => '2025-01-15',
                'items' => [
                    [
                        'product_id' => 1,
                        'name' => 'iPhone 15 Pro',
                        'quantity' => 1,
                        'price' => 54999.99
                    ]
                ],
                'total_amount' => 54999.99,
                'shipping_address' => 'İstanbul, Türkiye',
                'estimated_delivery' => '2025-01-20'
            ];
            
            // Session'ı logla
            $this->logAPICall('order_tracking', $sessionId, $request->all(), $orderData);
            
            return response()->json([
                'success' => true,
                'order' => $orderData,
                'message' => 'Sipariş durumu başarıyla getirildi',
                'widget_type' => 'order_tracking_widget'
            ]);
            
        } catch (\Exception $e) {
            Log::error('Order tracking API error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Sipariş durumu getirilemedi: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Cargo tracking API endpoint
     */
    public function cargoTracking(Request $request)
    {
        $request->validate([
            'tracking_number' => 'required|string',
            'session_id' => 'nullable|string'
        ]);
        
        try {
            $trackingNumber = $request->input('tracking_number');
            $sessionId = $request->input('session_id', uniqid());
            
            // Mock cargo data - gerçek uygulamada kargo API'sinden çekilecek
            $cargoData = [
                'tracking_number' => $trackingNumber,
                'status' => 'shipped',
                'courier' => 'Yurtiçi Kargo',
                'last_update' => '2025-01-18T14:30:00Z',
                'location' => 'İstanbul Aktarma Merkezi',
                'estimated_delivery' => '2025-01-20',
                'history' => [
                    [
                        'date' => '2025-01-15T10:00:00Z',
                        'status' => 'Picked up',
                        'location' => 'İstanbul Merkez'
                    ],
                    [
                        'date' => '2025-01-18T14:30:00Z',
                        'status' => 'In transit',
                        'location' => 'İstanbul Aktarma Merkezi'
                    ]
                ]
            ];
            
            // Session'ı logla
            $this->logAPICall('cargo_tracking', $sessionId, $request->all(), $cargoData);
            
            return response()->json([
                'success' => true,
                'tracking' => $cargoData,
                'message' => 'Kargo durumu başarıyla getirildi',
                'widget_type' => 'cargo_tracking_widget'
            ]);
            
        } catch (\Exception $e) {
            Log::error('Cargo tracking API error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Kargo durumu getirilemedi: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Add to cart API endpoint
     */
    public function addToCart(Request $request)
    {
        $request->validate([
            'product_id' => 'required|integer',
            'quantity' => 'nullable|integer|min:1',
            'session_id' => 'nullable|string'
        ]);
        
        try {
            $productId = $request->input('product_id');
            $quantity = $request->input('quantity', 1);
            $sessionId = $request->input('session_id', uniqid());
            
            // Knowledge base'den ürün bilgilerini çek
            $product = KnowledgeChunk::where('content_type', 'product')
                ->where('id', $productId)
                ->first();
            
            if (!$product) {
                return response()->json([
                    'success' => false,
                    'message' => 'Ürün bulunamadı'
                ], 404);
            }
            
            $metadata = [];
            $productMetadata = $product->metadata;
            if (is_string($productMetadata)) {
                $decoded = json_decode($productMetadata, true);
                $metadata = is_array($decoded) ? $decoded : [];
            } elseif (is_array($productMetadata)) {
                $metadata = $productMetadata;
            } else {
                $metadata = [];
            }
            
            
            $productData = [
                'product_id' => $productId,
                'name' => $metadata['name'] ?? 'Ürün ' . $productId,
                'price' => $metadata['price'] ?? 0,
                'quantity' => $quantity,
                'total_price' => ($metadata['price'] ?? 0) * $quantity
            ];
            
            // Mock cart data - gerçek uygulamada session/cart tablosundan çekilecek
            $cartData = [
                'total_items' => $quantity,
                'total_amount' => $productData['total_price'],
                'items' => [$productData]
            ];
            
            // Session'ı logla
            $this->logAPICall('cart_add', $sessionId, $request->all(), $cartData);
            
            return response()->json([
                'success' => true,
                'cart' => $cartData,
                'message' => 'Ürün sepete başarıyla eklendi',
                'widget_type' => 'cart_add_widget'
            ]);
            
        } catch (\Exception $e) {
            Log::error('Add to cart API error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Ürün sepete eklenemedi: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Product detail API endpoint
     */
    public function productDetail(Request $request)
    {
        $request->validate([
            'product_id' => 'required|integer',
            'session_id' => 'nullable|string'
        ]);
        
        try {
            $productId = $request->input('product_id');
            $sessionId = $request->input('session_id', uniqid());
            
            // Knowledge base'den ürün bilgilerini çek
            $product = KnowledgeChunk::where('content_type', 'product')
                ->where('id', $productId)
                ->first();
            
            if (!$product) {
                return response()->json([
                    'success' => false,
                    'message' => 'Ürün bulunamadı'
                ], 404);
            }
            
            $metadata = [];
            $productMetadata = $product->metadata;
            if (is_string($productMetadata)) {
                $decoded = json_decode($productMetadata, true);
                $metadata = is_array($decoded) ? $decoded : [];
            } elseif (is_array($productMetadata)) {
                $metadata = $productMetadata;
            } else {
                $metadata = [];
            }
            
            $productData = [
                'id' => $productId,
                'name' => $metadata['name'] ?? 'Ürün ' . $productId,
                'category' => $metadata['category'] ?? 'Genel',
                'price' => $metadata['price'] ?? 0,
                'brand' => $metadata['brand'] ?? 'Bilinmeyen',
                'rating' => $metadata['rating'] ?? 4.0,
                'stock' => $metadata['stock'] ?? 0,
                'image' => ProductImageHelper::getImageWithFallback($metadata['image'] ?? null),
                'description' => substr($product->content, 0, 500) . '...',
                'features' => [
                    'Garanti' => '2 Yıl',
                    'Kargo' => 'Ücretsiz',
                    'İade' => '14 Gün'
                ]
            ];
            
            // Session'ı logla
            $this->logAPICall('product_detail', $sessionId, $request->all(), $productData);
            
            return response()->json([
                'success' => true,
                'product' => $productData,
                'message' => 'Ürün detayları başarıyla getirildi',
                'widget_type' => 'product_detail_widget'
            ]);
            
        } catch (\Exception $e) {
            Log::error('Product detail API error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Ürün detayları getirilemedi: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Price inquiry API endpoint
     */
    public function priceInquiry(Request $request)
    {
        $request->validate([
            'product_id' => 'nullable|integer',
            'product_name' => 'nullable|string',
            'session_id' => 'nullable|string'
        ]);
        
        try {
            $productId = $request->input('product_id');
            $productName = $request->input('product_name');
            $sessionId = $request->input('session_id', uniqid());
            
            $query = KnowledgeChunk::where('content_type', 'product');
            
            if ($productId) {
                $query->where('id', $productId);
            } elseif ($productName) {
                $query->where('content', 'like', '%' . $productName . '%');
            } else {
                // Rastgele ürünler getir
                $query->inRandomOrder()->limit(3);
            }
            
            $products = $query->get();
            
            if ($products->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Ürün bulunamadı'
                ], 404);
            }
            
            $priceData = [];
            foreach ($products as $product) {
                $metadata = [];
            $productMetadata = $product->metadata;
            if (is_string($productMetadata)) {
                $decoded = json_decode($productMetadata, true);
                $metadata = is_array($decoded) ? $decoded : [];
            } elseif (is_array($productMetadata)) {
                $metadata = $productMetadata;
            } else {
                $metadata = [];
            }
                
                $priceData[] = [
                    'id' => $product->id,
                    'name' => $metadata['name'] ?? 'Ürün ' . $product->id,
                    'price' => $metadata['price'] ?? 0,
                    'currency' => 'TL',
                    'discount' => 0,
                    'final_price' => $metadata['price'] ?? 0
                ];
            }
            
            // Session'ı logla
            $this->logAPICall('price_inquiry', $sessionId, $request->all(), $priceData);
            
            return response()->json([
                'success' => true,
                'prices' => $priceData,
                'message' => 'Fiyat bilgileri başarıyla getirildi',
                'widget_type' => 'price_inquiry_widget'
            ]);
            
        } catch (\Exception $e) {
            Log::error('Price inquiry API error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Fiyat bilgileri getirilemedi: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Log API call
     */
    private function logAPICall($intent, $sessionId, $request, $response)
    {
        try {
            // Enhanced chat session oluştur veya güncelle
            $session = EnhancedChatSession::firstOrCreate(
                ['session_id' => $sessionId],
                [
                    'user_id' => Auth::id(),
                    'intent' => $intent,
                    'last_activity' => now(),
                    'message_count' => 0
                ]
            );
            
            // Session'ı güncelle
            $session->update([
                'intent' => $intent,
                'last_activity' => now(),
                'message_count' => $session->message_count + 1
            ]);
            
            Log::debug('Action API processed', [
                'intent' => $intent,
                'session_id' => $sessionId,
                'user_id' => Auth::id(),
                'request' => $request,
                'response_type' => gettype($response)
            ]);
            
        } catch (\Exception $e) {
            Log::error('API call logging error: ' . $e->getMessage());
        }
    }
}
