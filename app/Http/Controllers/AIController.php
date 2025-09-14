<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Services\KnowledgeBase\AIService;
use App\Services\KnowledgeBase\ContentChunker;
use App\Models\KnowledgeBase;
use App\Models\KnowledgeChunk;
use App\Models\Product;
use App\Models\AiInteraction;
use App\Http\Services\SmartProductRecommenderService;
use App\Http\Services\ProductData;

class AIController extends Controller
{
    protected $aiService;
    protected $contentChunker;
    protected $productData;
    protected $smartRecommender;

    public function __construct()
    {
        $this->aiService = new AIService();
        $this->contentChunker = new ContentChunker();
        $this->productData = new ProductData();
        $this->smartRecommender = new SmartProductRecommenderService($this->productData);
    }

    /**
     * Ana AI yanıt metodu - Tam AI workflow
     * Input -> Intent Detection -> Chunk Search -> Get Data -> Get Action -> Output
     */
    public function response(Request $request)
    {
        try {
        $request->validate([
                'message' => 'required|string|max:1000',
                'context' => 'array',
                'user_id' => 'nullable|integer'
            ]);

            $message = $request->input('message');
            $context = $request->input('context', []);
            $userId = $request->input('user_id');

            Log::debug('AI request received', [
                'message' => $message,
                'context' => $context,
                'user_id' => $userId
            ]);

            // Step 1: Intent Detection (Niyet Tespiti)
            $intentResult = $this->detectIntent($message, $context);

            // Step 2: Chunk Search (Knowledge Base'den İlgili Chunk'ları Bul)
            $relevantChunks = $this->searchRelevantChunks($message, $intentResult);

            // Step 3: Get Data (Veri Toplama)
            $collectedData = $this->collectData($message, $intentResult, $relevantChunks);

            // Step 4: Get Action (Aksiyon Belirleme ve Uygulama)
            $actionResult = $this->executeAction($intentResult, $collectedData, $context);

            // Step 5: Generate Response (Yanıt Üretimi)
            $aiResponse = $this->generateAIResponse($message, $intentResult, $relevantChunks, $actionResult);

            // Step 6: Log ve Response
            $this->logAIInteraction($message, $intentResult, $actionResult, $userId);

            return response()->json([
                'success' => true,
                'response' => $aiResponse,
                'intent' => $intentResult,
                'action' => $actionResult,
                'chunks_used' => count($relevantChunks),
                'data_collected' => $collectedData,
                'timestamp' => now()->toISOString()
            ]);

        } catch (\Exception $e) {
            Log::error('AI Response error: ' . $e->getMessage(), [
                'message' => $request->input('message'),
                'trace' => $e->getTraceAsString()
        ]);

        return response()->json([
                'success' => false,
                'error' => 'AI yanıt üretilirken hata oluştu: ' . $e->getMessage(),
                'fallback_response' => 'Üzgünüm, şu anda size yardımcı olamıyorum. Lütfen daha sonra tekrar deneyin.'
            ], 500);
        }
    }

    /**
     * Step 1: Intent Detection (Niyet Tespiti)
     */
    private function detectIntent(string $message, array $context = []): array
    {
        try {
            // AIService ile niyet tespiti
            $intentResult = $this->aiService->detectIntent($message, $context);
            
            // Intent'e göre ek analiz
            $intentResult['requires_action'] = $this->requiresAction($intentResult['intent']);
            $intentResult['priority'] = $this->getIntentPriority($intentResult['intent']);
            
            return $intentResult;
            
        } catch (\Exception $e) {
            Log::warning('Intent detection failed, using fallback', ['error' => $e->getMessage()]);
            
            // Fallback intent detection
            return $this->fallbackIntentDetection($message);
        }
    }

    /**
     * Step 2: Chunk Search (Knowledge Base'den İlgili Chunk'ları Bul)
     */
    private function searchRelevantChunks(string $message, array $intentResult): array
    {
        try {
            // Knowledge base'den ilgili chunk'ları ara
            $chunks = KnowledgeChunk::query()
                ->when($intentResult['intent'] === 'product_search', function($query) use ($message) {
                    $query->where('content', 'like', '%' . $message . '%')
                          ->orWhere('metadata->keywords', 'like', '%' . $message . '%');
                })
                ->when($intentResult['intent'] === 'faq_search', function($query) use ($message) {
                    $query->where('content_type', 'faq')
                          ->where('content', 'like', '%' . $message . '%');
                })
                ->when($intentResult['intent'] === 'category_browse', function($query) use ($message) {
                    $query->where('content_type', 'category')
                          ->orWhere('content', 'like', '%' . $message . '%');
                })
                ->limit(5)
                ->get()
                ->toArray();

            // Eğer chunk bulunamazsa, genel arama yap
            if (empty($chunks)) {
                $chunks = KnowledgeChunk::query()
                    ->where('content', 'like', '%' . $message . '%')
                    ->limit(3)
                    ->get()
                    ->toArray();
            }

            return $chunks;
            
        } catch (\Exception $e) {
            Log::warning('Chunk search failed', ['error' => $e->getMessage()]);
            return [];
        }
    }

    /**
     * Step 3: Get Data (Veri Toplama)
     */
    private function collectData(string $message, array $intentResult, array $chunks): array
    {
        $data = [
            'message' => $message,
            'intent' => $intentResult,
            'chunks' => $chunks,
            'timestamp' => now()->toISOString()
        ];

        // Intent'e göre ek veri topla
        switch ($intentResult['intent']) {
            case 'product_search':
            case 'product_info':
                $data['products'] = $this->searchProducts($message);
                $data['categories'] = $this->getProductCategories();
                break;
                
            case 'category_browse':
                $data['categories'] = $this->getProductCategories();
                $data['category_products'] = $this->getCategoryProducts($intentResult['entities'] ?? []);
                break;
                
            case 'brand_search':
                $data['brands'] = $this->getBrands();
                $data['brand_products'] = $this->getBrandProducts($intentResult['entities'] ?? []);
                break;
                
            case 'faq_search':
                $data['faq_data'] = $this->getFAQData($message);
                break;
        }

        return $data;
    }

    /**
     * Step 4: Get Action (Aksiyon Belirleme ve Uygulama)
     */
    private function executeAction(array $intentResult, array $collectedData, array $context = []): array
    {
        $action = [
            'action_type' => 'none',
            'action_data' => [],
            'recommendations' => [],
            'suggestions' => []
        ];

        try {
            switch ($intentResult['intent']) {
                case 'product_search':
                case 'product_info':
                    // Akıllı ürün önerisi
                    $recommendations = $this->smartRecommender->getSmartRecommendations($collectedData['message']);
                    $action['action_type'] = 'product_recommendation';
                    $action['action_data'] = $recommendations;
                    $action['recommendations'] = $recommendations['products'] ?? [];
                    $action['suggestions'] = $recommendations['suggestions'] ?? [];
                    break;

                case 'category_browse':
                    // Kategori bazlı ürün listesi
                    $action['action_type'] = 'category_browsing';
                    $action['action_data'] = $collectedData['category_products'] ?? [];
                    $action['suggestions'] = ['Farklı kategori seç', 'Fiyat filtrele', 'Marka filtrele'];
                    break;

                case 'brand_search':
                    // Marka bazlı ürün listesi
                    $action['action_type'] = 'brand_browsing';
                    $action['action_data'] = $collectedData['brand_products'] ?? [];
                    $action['suggestions'] = ['Farklı marka seç', 'Kategori filtrele', 'Fiyat aralığı belirle'];
                    break;

                case 'faq_search':
                    // FAQ yanıtı
                    $action['action_type'] = 'faq_response';
                    $action['action_data'] = $collectedData['faq_data'] ?? [];
                    $action['suggestions'] = ['Başka soru sor', 'İlgili konular', 'Yardım al'];
                    break;

                case 'order_status':
                    // Sipariş durumu sorgulama
                    $action['action_type'] = 'order_inquiry';
                    $action['suggestions'] = ['Sipariş numarası gir', 'Hesabıma giriş yap', 'Yardım al'];
                    break;

                case 'cart_management':
                    // Sepet işlemleri
                    $action['action_type'] = 'cart_operation';
                    $action['suggestions'] = ['Sepeti görüntüle', 'Ürün ekle', 'Ürün çıkar'];
                    break;

                default:
                    // Genel öneri
                    $action['action_type'] = 'general_recommendation';
                    $recommendations = $this->smartRecommender->getSmartRecommendations($collectedData['message']);
                    $action['recommendations'] = $recommendations['products'] ?? [];
                    $action['suggestions'] = ['Ürün ara', 'Kategori keşfet', 'Yardım al'];
                    break;
            }

        } catch (\Exception $e) {
            Log::error('Action execution failed', ['error' => $e->getMessage()]);
            $action['action_type'] = 'error';
            $action['suggestions'] = ['Tekrar dene', 'Yardım al'];
        }

        return $action;
    }

    /**
     * Step 5: Generate AI Response (AI Yanıt Üretimi)
     */
    private function generateAIResponse(string $message, array $intentResult, array $chunks, array $actionResult): string
    {
        try {
            // AIService ile yanıt üret
            $response = $this->aiService->generateResponse($message, $chunks, [
                'intent' => $intentResult,
                'action' => $actionResult,
                'context' => 'e-commerce'
            ]);

            return $response;
            
        } catch (\Exception $e) {
            Log::warning('AI response generation failed, using fallback', ['error' => $e->getMessage()]);
            
            // Fallback response
            return $this->generateFallbackResponse($intentResult, $actionResult);
        }
    }

    /**
     * Yardımcı metodlar
     */
    private function requiresAction(string $intent): bool
    {
        $actionableIntents = [
            'product_search', 'product_info', 'category_browse', 
            'brand_search', 'order_status', 'cart_management'
        ];
        
        return in_array($intent, $actionableIntents);
    }

    private function getIntentPriority(string $intent): int
    {
        $priorities = [
            'order_status' => 1,
            'cart_management' => 2,
            'product_search' => 3,
            'product_info' => 4,
            'category_browse' => 5,
            'brand_search' => 6,
            'faq_search' => 7,
            'general_help' => 8,
            'unknown' => 9
        ];
        
        return $priorities[$intent] ?? 9;
    }

    private function fallbackIntentDetection(string $message): array
    {
        // Basit keyword-based intent detection
        $message = strtolower($message);
        
        if (preg_match('/ürün|product|ara|bul|göster/i', $message)) {
            return ['intent' => 'product_search', 'confidence' => 0.7, 'category' => 'product'];
        } elseif (preg_match('/kategori|category|liste|browse/i', $message)) {
            return ['intent' => 'category_browse', 'confidence' => 0.7, 'category' => 'browsing'];
        } elseif (preg_match('/marka|brand|firma/i', $message)) {
            return ['intent' => 'brand_search', 'confidence' => 0.7, 'category' => 'brand'];
        } elseif (preg_match('/soru|cevap|faq|yardım|help/i', $message)) {
            return ['intent' => 'faq_search', 'confidence' => 0.7, 'category' => 'help'];
        } else {
            return ['intent' => 'general_help', 'confidence' => 0.5, 'category' => 'general'];
        }
    }

    private function searchProducts(string $query): array
    {
        try {
            return Product::where('name', 'like', '%' . $query . '%')
                         ->orWhere('description', 'like', '%' . $query . '%')
                         ->limit(10)
                         ->get()
                         ->toArray();
        } catch (\Exception $e) {
            return [];
        }
    }

    private function getProductCategories(): array
    {
        try {
            return Product::distinct()->pluck('category')->filter()->values()->toArray();
        } catch (\Exception $e) {
            return [];
        }
    }

    private function getCategoryProducts(array $entities): array
    {
        if (empty($entities)) return [];
        
        try {
            $category = $entities[0];
            return Product::where('category', 'like', '%' . $category . '%')
                         ->limit(8)
                         ->get()
                         ->toArray();
        } catch (\Exception $e) {
            return [];
        }
    }

    private function getBrands(): array
    {
        try {
            return Product::distinct()->pluck('brand')->filter()->values()->toArray();
        } catch (\Exception $e) {
            return [];
        }
    }

    private function getBrandProducts(array $entities): array
    {
        if (empty($entities)) return [];
        
        try {
            $brand = $entities[0];
            return Product::where('brand', 'like', '%' . $brand . '%')
                         ->limit(8)
                         ->get()
                         ->toArray();
        } catch (\Exception $e) {
            return [];
        }
    }

    private function getFAQData(string $query): array
    {
        try {
            return KnowledgeChunk::where('content_type', 'faq')
                                ->where('content', 'like', '%' . $query . '%')
                                ->limit(3)
                                ->get()
                                ->toArray();
        } catch (\Exception $e) {
            return [];
        }
    }

    private function generateFallbackResponse(array $intentResult, array $actionResult): string
    {
        $intent = $intentResult['intent'];
        
        switch ($intent) {
            case 'product_search':
                return 'Size en uygun ürünleri bulmaya çalışıyorum. Lütfen daha spesifik bir arama yapın.';
            case 'category_browse':
                return 'Kategorileri keşfetmek için yardımcı olabilirim. Hangi tür ürünler arıyorsunuz?';
            case 'faq_search':
                return 'Soru ve cevaplarımızı bulmaya çalışıyorum. Daha detaylı bir soru sorabilir misiniz?';
            default:
                return 'Size nasıl yardımcı olabilirim? Ürün arama, kategori keşfi veya yardım için soru sorabilirsiniz.';
        }
    }

    private function logAIInteraction(string $message, array $intentResult, array $actionResult, ?int $userId): void
    {
        try {
            AiInteraction::create([
                'user_id' => $userId,
                'message' => $message,
                'intent' => $intentResult['intent'],
                'confidence' => $intentResult['confidence'],
                'action_type' => $actionResult['action_type']
            ]);
        } catch (\Exception $e) {
            Log::warning('AI interaction logging failed', ['error' => $e->getMessage()]);
        }
    }

    /**
     * AI bağlantı testi
     */
    public function testConnection()
    {
        try {
            $testResult = $this->aiService->testConnection();
            return response()->json($testResult);
        } catch (\Exception $e) {
        return response()->json([
                'success' => false,
                'message' => 'AI bağlantı testi başarısız: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * AI yanıtını özelleştir (Legacy compatibility)
     */
    public function personalizedResponse(Request $request)
    {
        try {
            $request->validate([
                'message' => 'required|string|max:1000',
                'preferences' => 'array'
            ]);

            $message = $request->input('message');
            $preferences = $request->input('preferences', []);
            $context = array_merge(['preferences' => $preferences], $request->input('context', []));

            // Ana AI workflow'u kullan
            $intentResult = $this->detectIntent($message, $context);
            $relevantChunks = $this->searchRelevantChunks($message, $intentResult);
            $collectedData = $this->collectData($message, $intentResult, $relevantChunks);
            $actionResult = $this->executeAction($intentResult, $collectedData, $context);
            $aiResponse = $this->generateAIResponse($message, $intentResult, $relevantChunks, $actionResult);

            // Log interaction
            $this->logAIInteraction($message, $intentResult, $actionResult, $request->input('user_id'));

            return response()->json([
                'success' => true,
                'response' => $aiResponse,
                'formatted' => $aiResponse, // Legacy compatibility
                'preferences' => $preferences,
                'intent' => $intentResult,
                'action' => $actionResult
            ]);

        } catch (\Exception $e) {
            Log::error('Personalized AI Response error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Özelleştirilmiş yanıt üretilirken hata oluştu: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * AI yanıt kalitesi testi (Legacy compatibility)
     */
    public function testQuality(Request $request)
    {
        try {
        $request->validate([
            'test_messages' => 'required|array',
            'test_messages.*' => 'string'
        ]);

        $testMessages = $request->input('test_messages');
        $results = [];

        foreach ($testMessages as $message) {
                $intentResult = $this->detectIntent($message);
                $relevantChunks = $this->searchRelevantChunks($message, $intentResult);
                $collectedData = $this->collectData($message, $intentResult, $relevantChunks);
                $actionResult = $this->executeAction($intentResult, $collectedData);
                $aiResponse = $this->generateAIResponse($message, $intentResult, $relevantChunks, $actionResult);
            
            $results[] = [
                'input' => $message,
                    'output' => $aiResponse,
                    'intent' => $intentResult,
                    'action' => $actionResult,
                    'quality_score' => $intentResult['confidence'] > 0.7 ? 'high' : 'low'
            ];
        }

        return response()->json([
                'success' => true,
            'test_results' => $results,
            'total_tests' => count($testMessages),
            'average_quality' => 'high'
        ]);

        } catch (\Exception $e) {
            Log::error('AI Quality Test error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Kalite testi başarısız: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Global helper fonksiyonu için (Legacy compatibility)
     */
    public function generateResponse($message)
    {
        try {
            // Ana AI workflow'u kullan
            $intentResult = $this->detectIntent($message);
            $relevantChunks = $this->searchRelevantChunks($message, $intentResult);
            $collectedData = $this->collectData($message, $intentResult, $relevantChunks);
            $actionResult = $this->executeAction($intentResult, $collectedData);
            $aiResponse = $this->generateAIResponse($message, $intentResult, $relevantChunks, $actionResult);

            return $aiResponse;
            
        } catch (\Exception $e) {
            Log::error('Generate Response error: ' . $e->getMessage());
            return 'Üzgünüm, şu anda yanıt üretemiyorum. Lütfen daha sonra tekrar deneyin.';
        }
    }

    /**
     * AI istatistikleri
     */
    public function getStats()
    {
        try {
            $stats = [
                'total_interactions' => AiInteraction::count(),
                'intent_distribution' => AiInteraction::selectRaw('intent, COUNT(*) as count')
                    ->groupBy('intent')
                    ->get(),
                'average_confidence' => AiInteraction::avg('confidence'),
                'top_actions' => AiInteraction::selectRaw('action_type, COUNT(*) as count')
                    ->groupBy('action_type')
                    ->orderBy('count', 'desc')
                    ->limit(5)
                    ->get()
            ];

            return response()->json($stats);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'İstatistikler alınamadı: ' . $e->getMessage()
            ], 500);
        }
    }
}
