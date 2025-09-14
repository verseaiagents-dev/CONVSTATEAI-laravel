<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\KnowledgeBase\AIService;
use App\Services\KnowledgeBase\ContentChunker;
use App\Http\Services\IntentDetectionService;
use App\Http\Services\ProjectKnowledgeService;
use App\Models\KnowledgeBase;
use App\Models\KnowledgeChunk;
use App\Models\Product;
use App\Models\EnhancedChatSession;
use App\Models\ProductInteraction;
use App\Models\AiInteraction;
use App\Helpers\ProductImageHelper;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class ConvStateAPI extends Controller
{
    private $aiService;
    private $contentChunker;
    private $intentDetectionService;
    private $projectKnowledgeService;
    
    /**
     * UTF-8 destekli JSON response helper
     */
    private function jsonResponse($data, $status = 200, $headers = [])
    {
        return response()->json($data, $status, $headers, JSON_UNESCAPED_UNICODE);
    }

    public function __construct()
    {
        // AIService'i manuel olarak resolve et
        try {
            $this->aiService = app(AIService::class);
            $this->contentChunker = app(ContentChunker::class);
            $this->intentDetectionService = app(IntentDetectionService::class);
            $this->projectKnowledgeService = app(ProjectKnowledgeService::class);
        } catch (\Exception $e) {
            Log::warning('Services could not be resolved: ' . $e->getMessage());
            $this->aiService = null;
            $this->contentChunker = null;
            $this->intentDetectionService = null;
            $this->projectKnowledgeService = null;
        }
    }

    public function chat(Request $request)
    {
        try {
            // Kullanıcıdan gelen parametreleri al
            $userMessage = $request->input('message');
            $projectId = $request->input('project_id') ?: $request->header('X-Project-ID');
            
            // Debug log - Project ID kontrolü
            Log::info('Project ID Debug:', [
                'project_id_input' => $request->input('project_id'),
                'x_project_id_header' => $request->header('X-Project-ID'),
                'final_project_id' => $projectId,
                'all_headers' => $request->headers->all()
            ]);
            
            // Browser UUID ile session eşleştirme
            $sessionId = $this->getOrCreateSessionId($request);
            
            // Message parametresi kontrolü
            if (!$userMessage) {
                return response()->json(['error' => 'Message parameter is required'], 400);
            }
            
            // Project ID kontrolü - string "0" veya boş değerleri de kontrol et
            if (empty($projectId) || $projectId === '0' || $projectId === 0) {
                Log::error('Project ID validation failed:', [
                    'project_id' => $projectId,
                    'project_id_type' => gettype($projectId),
                    'project_id_empty' => empty($projectId),
                    'project_id_null' => is_null($projectId)
                ]);
                return response()->json([
                    'success' => false,
                    'message' => 'Project ID is required',
                    'error' => 'MISSING_PROJECT_ID'
                ], 400);
            }

            // Usage Token kontrolü - Project sahibi kullanıcıyı bul
            $project = \App\Models\Project::find($projectId);
            if ($project && $project->created_by) {
                $user = \App\Models\User::find($project->created_by);
                if ($user) {
                    $usageToken = \App\Models\UsageToken::getActiveForUser($user->id);
                    
                    if (!$usageToken) {
                        return response()->json([
                            'success' => false,
                            'type' => 'error',
                            'message' => 'Kullanım token\'ınız bulunmuyor. Lütfen bir plan satın alın.',
                            'error' => 'NO_USAGE_TOKENS',
                            'redirect' => route('dashboard.subscription.index'),
                            'data' => [
                                'error' => 'NO_USAGE_TOKENS',
                                'redirect' => route('dashboard.subscription.index')
                            ]
                        ], 403);
                    }

                    if ($usageToken->isExpired()) {
                        return response()->json([
                            'success' => false,
                            'type' => 'error',
                            'message' => 'Kullanım token\'larınızın süresi dolmuş. Lütfen planınızı yenileyin.',
                            'error' => 'USAGE_TOKENS_EXPIRED',
                            'redirect' => route('dashboard.subscription.index'),
                            'tokens_remaining' => $usageToken->tokens_remaining,
                            'tokens_used' => $usageToken->tokens_used,
                            'reset_date' => $usageToken->reset_date,
                            'data' => [
                                'error' => 'USAGE_TOKENS_EXPIRED',
                                'redirect' => route('dashboard.subscription.index'),
                                'tokens_remaining' => $usageToken->tokens_remaining,
                                'tokens_used' => $usageToken->tokens_used,
                                'reset_date' => $usageToken->reset_date
                            ]
                        ], 403);
                    }

                    if (!$usageToken->canUseToken()) {
                        return response()->json([
                            'success' => false,
                            'type' => 'error',
                            'message' => 'Yetersiz kullanım token\'ı. Lütfen daha fazla token satın alın.',
                            'error' => 'INSUFFICIENT_TOKENS',
                            'redirect' => route('dashboard.subscription.index'),
                            'tokens_remaining' => $usageToken->tokens_remaining,
                            'tokens_used' => $usageToken->tokens_used,
                            'tokens_total' => $usageToken->tokens_total,
                            'usage_percentage' => $usageToken->usage_percentage,
                            'data' => [
                                'error' => 'INSUFFICIENT_TOKENS',
                                'redirect' => route('dashboard.subscription.index'),
                                'tokens_remaining' => $usageToken->tokens_remaining,
                                'tokens_used' => $usageToken->tokens_used,
                                'tokens_total' => $usageToken->tokens_total,
                                'usage_percentage' => $usageToken->usage_percentage
                            ]
                        ], 403);
                    }
                }
            }
            
            // Debug log
            Log::info('Chat request received:', [
                'message' => $userMessage,
                'session_id' => $sessionId
            ]);
            
            // OpenAI API key kontrolü
            if (!config('openai.api_key') || config('openai.api_key') === 'your_openai_api_key_here' || !$this->aiService) {
                Log::warning('OpenAI API key not configured or AIService not available, using IntentDetectionService fallback');
                
                // IntentDetectionService ile fallback response
                if ($this->intentDetectionService) {
                    $intentResult = $this->intentDetectionService->detectIntentWithAI($userMessage);
                    $intent = $intentResult['intent'];
                    $confidence = $intentResult['confidence'];
                    
                    // Debug log
                    Log::info('IntentDetectionService fallback result:', [
                        'intent' => $intent,
                        'confidence' => $confidence,
                        'threshold_met' => $intentResult['threshold_met'] ?? false,
                        'ai_generated' => $intentResult['ai_generated'] ?? false,
                        'userMessage' => $userMessage
                    ]);
                    
                    // Intent'e göre response oluştur
                    $response = $this->generateAIResponse($intent, $userMessage, []);
                    $response['session_id'] = $sessionId ?? uniqid();
                    $response['intent'] = $intent;
                    $response['confidence'] = $confidence;
                    $response['ai_system_status'] = 'intent_detection_fallback';
                    $response['knowledge_base_results'] = $this->simulateKnowledgeBaseSearch($userMessage);
                    
                    return response()->json($response, 200, [], JSON_UNESCAPED_UNICODE);
                } else {
                    // Enhanced fallback response - AI sistemi simülasyonu
                    $response = $this->generateEnhancedFallbackResponse($userMessage);
                    $response['session_id'] = $sessionId ?? uniqid();
                    $response['intent'] = $response['intent'] ?? 'general';
                    $response['confidence'] = $response['confidence'] ?? 0.7;
                    $response['ai_system_status'] = 'fallback_mode';
                    $response['knowledge_base_results'] = $this->simulateKnowledgeBaseSearch($userMessage);
                    
                    return response()->json($response, 200, [], JSON_UNESCAPED_UNICODE);
                }
            }
            
            // 1. Context-aware intent detection
            $context = $this->getSessionContext($sessionId);
            
            // Debug log
            Log::info('Context debug:', [
                'session_id' => $sessionId,
                'context' => $context,
                'user_message' => $userMessage
            ]);
            
            if ($this->intentDetectionService) {
                // Context-aware intent detection
                if ($this->isFollowUpQuestion($userMessage, $context)) {
                    Log::info('Follow-up question detected');
                    $intentResult = $this->handleFollowUpQuestion($userMessage, $context);
                    $intent = $intentResult['intent'];
                    $confidence = $intentResult['confidence'];
                } else {
                    // IntentDetectionService ile AI destekli intent detection
                    $intentResult = $this->intentDetectionService->detectIntentWithAI($userMessage);
                    $intent = $intentResult['intent'];
                    $confidence = $intentResult['confidence'];
                }
                
                // Debug log
                Log::info('IntentDetectionService result:', [
                    'intent' => $intent,
                    'confidence' => $confidence,
                    'threshold_met' => $intentResult['threshold_met'] ?? false,
                    'ai_generated' => $intentResult['ai_generated'] ?? false,
                    'userMessage' => $userMessage
                ]);
            } else {
                // Fallback to AIService
                $intentResult = $this->aiService->detectIntent($userMessage);
                $intent = $intentResult['intent'];
                $confidence = $intentResult['confidence'];
                
                // Debug log
                Log::info('AIService fallback result:', [
                    'intent' => $intent,
                    'confidence' => $confidence,
                    'userMessage' => $userMessage
                ]);
            }
            
            // 2. Knowledge base'de semantic search yap
            $searchResults = $this->performSemanticSearch($userMessage);
            
            // 3. Funnel intent'ler için analytics tracking (özel cevaplandırma yok)
            $funnelIntents = [
                'capabilities_inquiry',
                'project_info', 
                'conversion_guidance',
                'pricing_guidance',
                'demo_request',
                'contact_request'
            ];
            
            // Funnel intent'leri tespit et ama özel cevaplandırma yapma
            if (in_array($intent, $funnelIntents)) {
                Log::info('Funnel intent detected for analytics', [
                    'project_id' => $projectId,
                    'intent' => $intent,
                    'user_message' => $userMessage
                ]);
                
                // Analytics için intent'i kaydet ama genel response döndür
                $response = $this->generateAIResponse('general', $userMessage, $searchResults);
                $response['intent'] = $intent; // Analytics için intent'i koru
            } else {
                // 4. Normal intent'ler için response oluştur
                $response = $this->generateAIResponse($intent, $userMessage, $searchResults);
            }
            
            // Debug log
            Log::info('Generated response:', [
                'type' => $response['type'],
                'message' => $response['message'],
                'products_count' => count($response['data']['products'] ?? [])
            ]);
            
            // 4. Session bilgilerini ekle
            $response['session_id'] = $sessionId;
            $response['intent'] = $intent;
            $response['confidence'] = $confidence;
            $response['search_results'] = $searchResults;
            
            // Session data ekle
            $response['session_data'] = [
                'session_id' => $response['session_id'],
                'created_at' => now()->toISOString(),
                'last_activity' => now()->toISOString(),
                'intent_count' => 1,
                'message_count' => 1,
                'status' => 'active'
            ];
            
            // 5. AI interaction'ı logla
            $this->logAIInteraction($userMessage, $intent, $response, $sessionId);
            
            // 6. Usage token kullanımını kaydet (başarılı response sonrası)
            if (isset($project) && $project && $project->created_by) {
                $user = \App\Models\User::find($project->created_by);
                if ($user) {
                    $usageToken = \App\Models\UsageToken::getActiveForUser($user->id);
                    if ($usageToken && $usageToken->canUseToken()) {
                        $usageToken->useToken();
                        Log::info('Usage token consumed', [
                            'user_id' => $user->id,
                            'tokens_remaining' => $usageToken->tokens_remaining,
                            'tokens_used' => $usageToken->tokens_used
                        ]);
                    }
                }
            }
            
            // 7. Session'a mesaj ekle (context için)
            try {
                $session = EnhancedChatSession::where('session_id', $sessionId)->first();
                
                if ($session) {
                    // Daily limits'i kontrol et ve gerekirse sıfırla
                    $session->refreshDailyLimits();
                    
                    // Daily usage'ı artır (her chat mesajı için)
                    $session->incrementViewCount();
                    
                    // User mesajını ekle
                    $session->addChatMessage('user', $userMessage, $intent);
                    
                    // Bot response'unu ekle (response data ile)
                    $session->addChatMessage('assistant', $response['message'] ?? '', $intent, $response);
                    
                    // User preferences'i güncelle (ürün bilgisi varsa)
                    if (isset($response['products']) && !empty($response['products'])) {
                        $preferences = $session->user_preferences ?? [];
                        $preferences['last_products'] = array_slice($response['products'], 0, 3);
                        
                        if (isset($response['data']['category'])) {
                            $preferences['current_category'] = $response['data']['category'];
                        }
                        
                        $session->updateUserPreferences($preferences);
                    }
                    
                    // Debug: Context'i kontrol et
                    $context = $this->getSessionContext($sessionId);
                    Log::info('Session context after add:', [
                        'session_id' => $sessionId,
                        'context' => $context,
                        'last_products' => $context['last_products'] ?? [],
                        'daily_view_count' => $session->fresh()->daily_view_count,
                        'daily_view_limit' => $session->daily_view_limit
                    ]);
                }
            } catch (\Exception $e) {
                Log::error('Session message add error: ' . $e->getMessage());
            }
            
            return $this->jsonResponse($response);
            
        } catch (\Exception $e) {
            Log::error('Chat error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            
            // Fallback response on error
            $fallbackResponse = $this->generateFallbackResponse($userMessage ?? '');
            $fallbackResponse['session_id'] = $sessionId ?? 'error_' . uniqid();
            $fallbackResponse['intent'] = 'error';
            $fallbackResponse['confidence'] = 0.1;
            
            return $this->jsonResponse($fallbackResponse);
        }
    }

    /**
     * Browser UUID ile session eşleştirme veya yeni session oluşturma
     */
    private function getOrCreateSessionId(Request $request): string
    {
        try {
            // Browser'dan gelen UUID'yi al
            $browserUuid = $request->input('browser_uuid') ?? $request->header('X-Browser-UUID');
            
            // Eğer UUID yoksa, request'ten session_id'yi almaya çalış (fallback)
            if (!$browserUuid) {
                $browserUuid = $request->input('session_id');
            }
            
            // UUID yoksa yeni oluştur
            if (!$browserUuid) {
                $browserUuid = 'browser_' . uniqid() . '_' . time();
                Log::info('New browser UUID generated', ['uuid' => $browserUuid]);
            }
            
            // EnhancedChatSession ile session oluştur
            try {
                $session = EnhancedChatSession::create([
                    'session_id' => $browserUuid,
                    'user_id' => 0, // Guest user
                    'project_id' => $request->input('project_id', 1),
                    'daily_view_limit' => 20,
                    'daily_view_count' => 0,
                    'status' => 'active',
                    'last_activity' => now(),
                    'expires_at' => now()->addHours(72) // 72 saat sonra expire et
                ]);
                
                Log::info('EnhancedChatSession created', [
                    'session_id' => $browserUuid,
                    'project_id' => $request->input('project_id', 1)
                ]);
            } catch (\Exception $e) {
                Log::error('EnhancedChatSession creation failed', [
                    'session_id' => $browserUuid,
                    'error' => $e->getMessage()
                ]);
            }
            
            Log::info('Session ID generated', ['session_id' => $browserUuid]);
            return $browserUuid;
            
        } catch (\Exception $e) {
            Log::error('Session creation/retrieval failed', [
                'error' => $e->getMessage(),
                'browser_uuid' => $request->input('browser_uuid'),
                'session_id' => $request->input('session_id')
            ]);
            
            // Fallback: Basit UUID oluştur
            return 'fallback_' . uniqid() . '_' . time();
        }
    }

    /**
     * Enhanced fallback response - AI sistemi simülasyonu
     */
    private function generateEnhancedFallbackResponse(string $userMessage): array
    {
        $message = strtolower($userMessage);
        
        // Simulated intent detection
        $intent = $this->simulateIntentDetection($message);
        $confidence = $this->calculateConfidence($message, $intent);
        
        // Simulated knowledge base search
        $knowledgeResults = $this->simulateKnowledgeBaseSearch($userMessage);
        
        // Generate response based on intent
        switch ($intent) {
            case 'product_inquiry':
                return [
                    'type' => 'product_recommendation',
                    'message' => 'Size ürün önerileri sunabilirim. Hangi kategoride ürün arıyorsunuz?',
                    'intent' => $intent,
                    'confidence' => $confidence,
                    'data' => [
                        'products' => $this->getRandomProductsFromKnowledgeBase(6)
                    ],
                    'knowledge_base_results' => $knowledgeResults
                ];
                
            case 'greeting':
                return [
                    'type' => 'greeting',
                    'message' => 'Merhaba! Ben Kadir, senin dijital asistanınım. Size nasıl yardımcı olabilirim?',
                    'intent' => $intent,
                    'confidence' => $confidence,
                    'data' => [
                        'products' => []
                    ],
                    'knowledge_base_results' => $knowledgeResults
                ];
                
            case 'help_request':
                return [
                    'type' => 'help',
                    'message' => 'Size yardımcı olmak için buradayım! Ürünler hakkında bilgi almak, sipariş vermek veya herhangi bir sorunuzu çözmek için bana yazabilirsiniz.',
                    'intent' => $intent,
                    'confidence' => $confidence,
                    'data' => [
                        'products' => []
                    ],
                    'knowledge_base_results' => $knowledgeResults
                ];
                
            case 'cargo_tracking':
                return [
                    'type' => 'cargo_tracking',
                    'message' => 'Kargo takip numaranızı girerek kargo durumunuzu öğrenebilirsiniz.',
                    'intent' => $intent,
                    'confidence' => $confidence,
                    'data' => [
                        'products' => []
                    ],
                    'knowledge_base_results' => $knowledgeResults
                ];
                
            case 'order_tracking':
                return [
                    'type' => 'order_tracking',
                    'message' => 'Sipariş takip numaranızı girerek sipariş durumunuzu öğrenebilirsiniz.',
                    'intent' => $intent,
                    'confidence' => $confidence,
                    'data' => [
                        'products' => []
                    ],
                    'knowledge_base_results' => $knowledgeResults
                ];
                
            default:
                return [
                    'type' => 'general',
                    'message' => 'Anlıyorum. Size daha iyi yardımcı olabilmem için biraz daha detay verebilir misiniz?',
                    'intent' => $intent,
                    'confidence' => $confidence,
                    'data' => [
                        'products' => []
                    ],
                    'knowledge_base_results' => $knowledgeResults
                ];
        }
    }

    /**
     * Simulated intent detection
     */
    private function simulateIntentDetection(string $message): string
    {
        if (strpos($message, 'merhaba') !== false || strpos($message, 'hello') !== false || strpos($message, 'selam') !== false) {
            return 'greeting';
        } elseif (strpos($message, 'ürün') !== false || strpos($message, 'product') !== false || strpos($message, 'satın al') !== false) {
            return 'product_inquiry';
        } elseif (strpos($message, 'yardım') !== false || strpos($message, 'help') !== false || strpos($message, 'destek') !== false) {
            return 'help_request';
        } elseif (strpos($message, 'sipariş') !== false || strpos($message, 'order') !== false || strpos($message, 'siparişim') !== false) {
            return 'order_tracking';
        } elseif (strpos($message, 'kargo') !== false || strpos($message, 'cargo') !== false || strpos($message, 'kargom') !== false || strpos($message, 'kargom nerede') !== false) {
            return 'cargo_tracking';
        } else {
            return 'general';
        }
    }

    /**
     * Calculate confidence score
     */
    private function calculateConfidence(string $message, string $intent): float
    {
        $confidence = 0.5; // Base confidence
        
        // Increase confidence based on keyword matches
        $keywords = [
            'greeting' => ['merhaba', 'hello', 'selam', 'hi'],
            'product_inquiry' => ['ürün', 'product', 'satın al', 'buy'],
            'help_request' => ['yardım', 'help', 'destek', 'support'],
            'order_tracking' => ['sipariş', 'order', 'siparişim', 'siparişim nerede'],
            'cargo_tracking' => ['kargo', 'cargo', 'takip', 'tracking', 'kargom nerede']
        ];
        
        if (isset($keywords[$intent])) {
            foreach ($keywords[$intent] as $keyword) {
                if (strpos($message, $keyword) !== false) {
                    $confidence += 0.2;
                }
            }
        }
        
        return min($confidence, 0.95);
    }

    /**
     * Simulate knowledge base search
     */
    private function simulateKnowledgeBaseSearch(string $query): array
    {
        return [
            'query' => $query,
            'total_chunks_found' => rand(3, 8),
            'relevant_chunks' => [
                [
                    'id' => rand(1000, 9999),
                    'content' => 'İlgili ürün bilgisi: ' . substr($query, 0, 50) . '...',
                    'relevance_score' => rand(70, 95) / 100,
                    'source' => 'knowledge_base'
                ],
                [
                    'id' => rand(1000, 9999),
                    'content' => 'Kullanıcı deneyimi: ' . substr($query, 0, 30) . '...',
                    'relevance_score' => rand(60, 85) / 100,
                    'source' => 'faq_database'
                ]
            ],
            'search_time_ms' => rand(50, 200)
        ];
    }

    /**
     * Knowledge base'den ürün verilerini çeker
     */
    private function getProductsFromKnowledgeBase($category = null): array
    {
        try {
            $query = KnowledgeChunk::where('content_type', 'product');
            
            if ($category) {
                $query->where('content', 'like', '%' . $category . '%');
            }
            
            $chunks = $query->with('knowledgeBase')->get();
            
            $products = [];
            foreach ($chunks as $chunk) {
                // Chunk content'ini JSON olarak parse et
                $productData = json_decode($chunk->content, true);

                if (!$productData) {
                    continue; // JSON parse edilemezse skip et
                }

                // Metadata'nın zaten array olup olmadığını kontrol et
                if (is_array($chunk->metadata)) {
                    $metadata = $chunk->metadata;
                } else {
                    $metadata = json_decode($chunk->metadata, true) ?? [];
                }
                
                $products[] = [
                    'id' => $productData['id'] ?? $chunk->id,
                    'name' => $productData['title'] ?? 'Ürün ' . $chunk->id,
                    'category' => $productData['category'] ?? 'Genel',
                    'price' => $productData['price'] ?? 0,
                    'brand' => $productData['brand'] ?? 'Bilinmeyen',
                    'rating' => $productData['rating']['rate'] ?? 4.0,
                    'stock' => 10, // Default stock
                    'image' => ProductImageHelper::getImageWithFallback($productData['image'] ?? null),
                    'description' => $productData['description'] ?? substr($chunk->content, 0, 200) . '...',
                    'product_url' => 'https://example.com/product/' . ($productData['id'] ?? $chunk->id) . '?intent=recommendation',
                    'source' => 'knowledge_base'
                ];
            }
            
            return $products;
            
        } catch (\Exception $e) {
            Log::error('Knowledge base product fetch error: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Knowledge base'den kategori bilgilerini çeker
     */
    private function getCategoriesFromKnowledgeBase(): array
    {
        try {
            $chunks = KnowledgeChunk::where('content_type', 'product')
                ->with('knowledgeBase')
                ->get();
            
            $categories = [];
            foreach ($chunks as $chunk) {
                // Metadata'nın zaten array olup olmadığını kontrol et
                if (is_array($chunk->metadata)) {
                    $metadata = $chunk->metadata;
                } else {
                    $metadata = json_decode($chunk->metadata, true) ?? [];
                }
                $category = $metadata['category'] ?? 'Genel';
                
                if (!isset($categories[$category])) {
                    $categories[$category] = [
                        'name' => $category,
                        'product_count' => 0,
                        'avg_price' => 0,
                        'avg_rating' => 0
                    ];
                }
                
                $categories[$category]['product_count']++;
            }
            
            return array_values($categories);
            
        } catch (\Exception $e) {
            Log::error('Knowledge base categories fetch error: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Knowledge base'den kategori detaylarını çeker
     */
    private function getCategoryDetailsFromKnowledgeBase($categoryName): ?array
    {
        try {
            $chunks = KnowledgeChunk::where('content_type', 'product')
                ->where('content', 'like', '%' . $categoryName . '%')
                ->with('knowledgeBase')
                ->get();
            
            if ($chunks->isEmpty()) {
                return null;
            }
            
            $products = [];
            $totalPrice = 0;
            $totalRating = 0;
            
            foreach ($chunks as $chunk) {
                // Metadata'nın zaten array olup olmadığını kontrol et
                if (is_array($chunk->metadata)) {
                    $metadata = $chunk->metadata;
                } else {
                    $metadata = json_decode($chunk->metadata, true) ?? [];
                }
                
                $product = [
                    'id' => $chunk->id,
                    'name' => $metadata['name'] ?? 'Ürün ' . $chunk->id,
                    'price' => $metadata['price'] ?? 0,
                    'rating' => $metadata['rating'] ?? 4.0,
                    'brand' => $metadata['brand'] ?? 'Bilinmeyen',
                    'stock' => $metadata['stock'] ?? 0
                ];
                
                $products[] = $product;
                $totalPrice += $product['price'];
                $totalRating += $product['rating'];
            }
            
            return [
                'category' => $categoryName,
                'summary' => [
                    'product_count' => count($products),
                    'avg_price' => count($products) > 0 ? round($totalPrice / count($products), 2) : 0,
                    'avg_rating' => count($products) > 0 ? round($totalRating / count($products), 1) : 0
                ],
                'products' => $products
            ];
            
        } catch (\Exception $e) {
            Log::error('Knowledge base category details fetch error: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Knowledge base'den kategori önerilerini çeker
     */
    private function getCategoryRecommendationsFromKnowledgeBase($limit = 5): array
    {
        try {
            $categories = $this->getCategoriesFromKnowledgeBase();
            
            // Ürün sayısına göre sırala
            usort($categories, function($a, $b) {
                return $b['product_count'] <=> $a['product_count'];
            });
            
            return array_slice($categories, 0, $limit);
            
        } catch (\Exception $e) {
            Log::error('Knowledge base category recommendations fetch error: ' . $e->getMessage());
            return [];
        }
    }


    /**
     * OpenAI olmadan fallback response oluşturur (eski method)
     */
    private function generateFallbackResponse(string $userMessage): array
    {
        $message = strtolower($userMessage);
        
        // Basit keyword-based response
        if (strpos($message, 'merhaba') !== false || strpos($message, 'hello') !== false || strpos($message, 'selam') !== false) {
            return [
                'type' => 'greeting',
                'message' => 'Merhaba! Ben Kadir, senin dijital asistanınım. Size nasıl yardımcı olabilirim?',
                'data' => [
                    'products' => []
                ]
            ];
        } elseif (strpos($message, 'ürün') !== false || strpos($message, 'product') !== false) {
            return [
                'type' => 'product_recommendation',
                'message' => 'Ürün önerileri için lütfen daha spesifik olun. Hangi kategoride ürün arıyorsunuz?',
                'data' => [
                    'products' => $this->getRandomProductsFromKnowledgeBase(6)
                ]
            ];
        } elseif (strpos($message, 'yardım') !== false || strpos($message, 'help') !== false) {
            return [
                'type' => 'help',
                'message' => 'Size yardımcı olmak için buradayım! Ürünler hakkında bilgi almak, sipariş vermek veya herhangi bir sorunuzu çözmek için bana yazabilirsiniz.',
                'data' => [
                    'products' => []
                ]
            ];
        } elseif (strpos($message, 'sipariş') !== false || strpos($message, 'order') !== false || strpos($message, 'siparişim') !== false) {
            return [
                'type' => 'order_tracking',
                'message' => 'Sipariş takip numaranızı girerek sipariş durumunuzu öğrenebilirsiniz.',
                'data' => [
                    'products' => []
                ]
            ];
        } elseif (strpos($message, 'kargo') !== false || strpos($message, 'cargo') !== false || strpos($message, 'takip') !== false) {
            return [
                'type' => 'cargo_tracking',
                'message' => 'Kargo takip numaranızı girerek kargo durumunuzu öğrenebilirsiniz.',
                'data' => [
                    'products' => []
                ]
            ];
        } else {
            return [
                'type' => 'general',
                'message' => 'Anlıyorum. Size daha iyi yardımcı olabilmem için biraz daha detay verebilir misiniz?',
                'data' => [
                    'products' => []
                ]
            ];
        }
    }

    /**
     * Semantic search yapar
     */
    private function performSemanticSearch(string $query): array
    {
        try {
            if (config('app.debug')) {
                Log::info('Starting performSemanticSearch for query:', ['query' => $query]);
            }
            
            // Knowledge base'den chunk'ları al
            $chunks = KnowledgeChunk::with('knowledgeBase')
                ->where('content_type', 'product')
                ->get()
                ->toArray();
            
            if (config('app.debug')) {
                Log::info('Found chunks:', ['count' => count($chunks)]);
            }
            
            if (empty($chunks)) {
                if (config('app.debug')) {
                    Log::warning('No chunks found for content_type: product');
                }
                return [
                    'query' => $query,
                    'results' => [],
                    'total_found' => 0,
                    'search_type' => 'no_data'
                ];
            }
            
            // AIService ile semantic search yap
            $searchResults = $this->aiService->semanticSearch($query, $chunks);
            
            // ContentChunker ile de fuzzy search yap
            $fuzzyResults = $this->contentChunker->fuzzyChunkSearch($chunks, $query, 0.6);
            
            // Sonuçları birleştir ve deduplicate yap
            $combinedResults = $this->combineSearchResults($searchResults, $fuzzyResults);
            
            if (config('app.debug')) {
                Log::info('Combined results:', $combinedResults);
            }
            
            return $combinedResults;
            
        } catch (\Exception $e) {
            Log::error('Semantic search error: ' . $e->getMessage());
            return [
                'query' => $query,
                'results' => [],
                'total_found' => 0,
                'search_type' => 'error',
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Search sonuçlarını birleştirir
     */
    private function combineSearchResults(array $semanticResults, array $fuzzyResults): array
    {
        $combined = [];
        $seenIds = [];
        
        // Semantic search sonuçlarını ekle
        foreach ($semanticResults['results'] ?? [] as $result) {
            $chunkId = $result['id'] ?? $result['chunk_index'];
            if (!in_array($chunkId, $seenIds)) {
                $combined[] = $result;
                $seenIds[] = $chunkId;
            }
        }
        
        // Fuzzy search sonuçlarını ekle
        foreach ($fuzzyResults as $result) {
            $chunkId = $result['id'] ?? $result['chunk_index'];
            if (!in_array($chunkId, $seenIds)) {
                $combined[] = $result;
                $seenIds[] = $chunkId;
            }
        }
        
        // Relevance score'a göre sırala
        usort($combined, function($a, $b) {
            $scoreA = $a['relevance_score'] ?? $a['fuzzy_score'] ?? 0;
            $scoreB = $b['relevance_score'] ?? $b['fuzzy_score'] ?? 0;
            return $scoreB <=> $scoreA;
        });
        
        return [
            'query' => $semanticResults['query'] ?? 'unknown',
            'results' => $combined,
            'total_found' => count($combined),
            'search_type' => 'combined',
            'semantic_count' => count($semanticResults['results'] ?? []),
            'fuzzy_count' => count($fuzzyResults)
        ];
    }

    /**
     * Default funnel response fallback
     */
    private function generateDefaultFunnelResponse(string $intent, string $userMessage): array
    {
        $defaultResponses = [
            'capabilities_inquiry' => [
                'type' => 'capabilities_inquiry',
                'message' => 'Size şu konularda yardımcı olabilirim: Ürün arama, fiyat sorgulama, kategori tarama, marka arama, stok kontrolü, sipariş sorgulama ve ürün önerileri sunabilirim.',
                'data' => ['widget_type' => 'capabilities_display']
            ],
            'project_info' => [
                'type' => 'project_info',
                'message' => 'Bu proje, ziyaretçilere akıllı ürün önerileri ve müşteri deneyimi sunan bir AI destekli chat widget sistemidir.',
                'data' => ['widget_type' => 'project_info_display']
            ],
            'conversion_guidance' => [
                'type' => 'conversion_guidance',
                'message' => 'Size adım adım rehberlik ediyorum. İşte süreç: 1. İhtiyaç Analizi 2. Ürün Keşfi 3. Karar Verme 4. Satın Alma',
                'data' => ['widget_type' => 'conversion_guidance_display']
            ],
            'pricing_guidance' => [
                'type' => 'pricing_guidance',
                'message' => 'Fiyat bilgileri ve ödeme seçenekleri: Esnek fiyatlandırma, kredi kartı, banka kartı, havale/EFT, taksitli ödeme seçenekleri mevcuttur.',
                'data' => ['widget_type' => 'pricing_guidance_display']
            ],
            'demo_request' => [
                'type' => 'demo_request',
                'message' => 'Size demo ve tanıtım imkanları sunuyorum: Canlı demo görüşmesi, video tanıtım, ücretsiz deneme süresi, kişiselleştirilmiş demo seçenekleri.',
                'data' => ['widget_type' => 'demo_request_display']
            ],
            'contact_request' => [
                'type' => 'contact_request',
                'message' => 'İletişim bilgileri ve destek seçenekleri: Telefon, email, WhatsApp destek hattı, canlı chat desteği mevcuttur.',
                'data' => ['widget_type' => 'contact_request_display']
            ],
            'product_recommendations' => [
                'type' => 'product_recommendations',
                'message' => 'Size özel ürün önerileri hazırlıyorum: En popüler ürünler, trend ürünler, size özel öneriler, bütçe dostu seçenekler.',
                'data' => ['widget_type' => 'product_recommendations_display']
            ]
        ];

        return $defaultResponses[$intent] ?? [
            'type' => $intent,
            'message' => 'Bu intent için yanıt oluşturulamadı.',
            'data' => ['widget_type' => 'default_response']
        ];
    }

    /**
     * Intent'e göre AI response oluşturur
     */
    private function generateAIResponse(string $intent, string $userMessage, array $searchResults): array
    {
        // Debug log
        Log::info('generateAIResponse called:', [
            'intent' => $intent,
            'userMessage' => $userMessage,
            'hasSearchResults' => !empty($searchResults['results'])
        ]);
        
        switch ($intent) {
            case 'product_search':
            case 'product_inquiry':
           
                Log::info('Calling generateProductSearchResponse');
                return $this->generateProductSearchResponse($userMessage, $searchResults);
                
            case 'price_inquiry':
                Log::info('Calling generatePriceInquiryResponse');
                return $this->generatePriceInquiryResponse($userMessage, $searchResults);
                
            case 'product_recommendation': // Özel case
                Log::info('Calling generateProductRecommendationResponse');
                return $this->generateProductRecommendationResponse($userMessage, $searchResults);
                
            case 'product_recommendations': // Funnel intent değil, özel ürün önerisi
                Log::info('Calling generateProductRecommendationResponse for product_recommendations');
                return $this->generateProductRecommendationResponse($userMessage, $searchResults);
                
            case 'contextual_recommendation': // Context-aware recommendation
                Log::info('Calling generateContextualRecommendationResponse');
                return $this->generateContextualRecommendationResponse($userMessage, $searchResults);
                
            case 'category_browse':
                Log::info('Calling generateCategoryResponse');
                return $this->generateCategoryResponse($userMessage, $searchResults);
                
            case 'brand_search':
                Log::info('Calling generateBrandResponse');
                return $this->generateBrandResponse($userMessage, $searchResults);
                
            case 'faq_search':
                Log::info('Calling generateFAQResponse');
                return $this->generateFAQResponse($userMessage, $searchResults);
                
            case 'order_tracking':
                Log::info('Calling generateOrderTrackingResponse');
                return $this->generateOrderTrackingResponse();
                
            case 'cargo_tracking':
                Log::info('Calling generateCargoTrackingResponse');
                return $this->generateCargoTrackingResponse();
                
            case 'cargo_tracking_with_number':
                Log::info('Calling processCargoTrackingWithNumber');
                // Mesajdan kargo numarasını çıkar (inline)
                $patterns = [
                    '/(?:YT|TR|TK|KG)[0-9]{8,}/i',
                    '/([A-Z]{2}[0-9]{9,})/i',
                    '/([0-9]{10,})/i',
                    '/(?:kargo|takip|numara|numarası)[\s:]*([A-Z0-9\-]{8,})/i'
                ];
                $trackingNumber = null;
                foreach ($patterns as $pattern) {
                    if (preg_match($pattern, $userMessage, $matches)) {
                        $trackingNumber = isset($matches[1]) ? trim($matches[1]) : trim($matches[0]);
                        break;
                    }
                }
                
                if (!$trackingNumber) {
                    return [
                        'type' => 'cargo_tracking',
                        'message' => 'Kargo takip numaranızı girin.',
                        'data' => [
                            'requires_input' => true,
                            'input_type' => 'cargo_number',
                            'placeholder' => 'Kargo takip numarası girin...',
                            'button_text' => 'Kargo Takip Et',
                            'widget_type' => 'cargo_tracking_widget',
                            'api_endpoint' => '/api/cargo/track',
                            'intent' => 'cargo_tracking'
                        ]
                    ];
                }

                // Kargo takip işlemini gerçekleştir (inline)
                try {
                    // Widget customization'dan kargo endpoint'ini al
                    $widgetCustomization = \App\Models\WidgetCustomization::where('is_active', true)
                        ->orderBy('updated_at', 'desc')
                        ->first();
                    
                    if (!$widgetCustomization) {
                        $result = [
                            'success' => false,
                            'message' => 'Bu özellik yakında açılacak',
                            'data' => [
                                'feature_disabled' => true
                            ]
                        ];
                    } else {
                        // Kargo API'sinin aktif olup olmadığını kontrol et
                        if (!$widgetCustomization->isKargoApiActive()) {
                            $result = [
                                'success' => false,
                                'message' => $widgetCustomization->getPersonalizedMessage('feature_disabled'),
                                'data' => [
                                    'feature_disabled' => true
                                ]
                            ];
                        } else {
                            $endpoint = $widgetCustomization->getKargoDurumuEndpoint();
                            
                            if (!$endpoint) {
                                $result = [
                                    'success' => false,
                                    'message' => $widgetCustomization->getPersonalizedMessage('feature_disabled'),
                                    'data' => [
                                        'feature_disabled' => true
                                    ]
                                ];
                            } else {
                                // Kullanıcının API'sine istek gönder
                                $url = $endpoint . '?tracking_number=' . urlencode($trackingNumber);
                                $apiResponse = \Http::timeout($widgetCustomization->api_timeout_seconds ?? 10)->get($url);
                                
                                if ($apiResponse->successful()) {
                                    $apiData = $apiResponse->json();
                                    
                                    // AI ile veri eşleştirme
                                    $mappingService = new \App\Services\OrderDataMappingService();
                                    $mappedData = $mappingService->mapToCargoTrackingData($apiData, $trackingNumber);
                                    
                                    if ($mappedData['success']) {
                                        $result = [
                                            'success' => true,
                                            'data' => $mappedData['data']
                                        ];
                                    } else {
                                        $result = [
                                            'success' => false,
                                            'message' => $widgetCustomization->getPersonalizedMessage('cargo_not_found')
                                        ];
                                    }
                                } else {
                                    $result = [
                                        'success' => false,
                                        'message' => $widgetCustomization->getPersonalizedMessage('cargo_not_found')
                                    ];
                                }
                            }
                        }
                    }
                } catch (\Exception $e) {
                    \Log::error('Cargo tracking error in TestAPI', [
                        'error' => $e->getMessage(),
                        'tracking_number' => $trackingNumber
                    ]);

                    $result = [
                        'success' => false,
                        'message' => $widgetCustomization->getPersonalizedMessage('error') ?? 'Kargo takip sırasında hata oluştu'
                    ];
                }
                
                if ($result['success']) {
                    return [
                        'type' => 'cargo_tracking_result',
                        'message' => 'Kargo takip bilgileriniz:',
                        'data' => $result['data']
                    ];
                } else {
                    return [
                        'type' => 'cargo_tracking_error',
                        'message' => $result['message'],
                        'data' => $result['data'] ?? null
                    ];
                }
                
            case 'cart_add':
                Log::info('Calling generateCartAddResponse');
                return $this->generateCartAddResponse($userMessage, $searchResults);
                
            case 'greeting':
                Log::info('Calling generateGreetingResponse');
                return $this->generateGreetingResponse();
                
            case 'help_request':
                Log::info('Calling generateHelpResponse');
                return $this->generateHelpResponse();
                
            // Funnel intents - sadece analytics için, genel response döndür
            case 'capabilities_inquiry':
            case 'project_info':
            case 'conversion_guidance':
            case 'pricing_guidance':
            case 'demo_request':
            case 'contact_request':
                Log::info('Funnel intent detected, returning general response: ' . $intent);
                return $this->generateGeneralResponse($userMessage, $searchResults);
                
            default:
                Log::info('Calling generateGeneralResponse (default case)');
                return $this->generateGeneralResponse($userMessage, $searchResults);
        }
    }

    /**
     * Fiyat sorgulama response'u
     */
    private function generatePriceInquiryResponse(string $userMessage, array $searchResults): array
    {
        $products = [];
        $message = '';
        
        // Knowledge base'den ürünleri al
        $allProducts = $this->getProductsFromKnowledgeBase();
        
        if (!empty($allProducts)) {
            $products = array_slice($allProducts, 0, 3);
            $priceInfo = [];
            
            foreach ($products as $product) {
                $priceInfo[] = $product['name'] . ': ' . number_format($product['price'], 2) . ' TL';
            }
            
            $message = 'Fiyat bilgileri: ' . implode(', ', $priceInfo);
        } else {
            $message = 'Fiyat bilgisi için hangi ürünü öğrenmek istiyorsunuz?';
        }
        
        return [
            'type' => 'price_inquiry',
            'message' => $message,
            'data' => [
                'products' => $products
            ]
        ];
    }

    /**
     * Ürün arama response'u
     */
    private function generateProductSearchResponse(string $userMessage, array $searchResults): array
    {
        $products = [];
        $message = '';
        
        // Kullanıcı mesajını analiz et
        $isPersonalizedRequest = preg_match('/(bana göre|benim için|öner|tavsiye)/i', $userMessage);
        $hasSpecificProduct = preg_match('/(saat|telefon|bilgisayar|elbise|ayakkabı|çanta|aksesuar|kozmetik|kitap)/i', $userMessage);
        
        // "bana kitap öner" gibi spesifik ürün aramaları için arama yap
        $isSpecificProductRequest = preg_match('/(bana|benim için)\s+(kitap|saat|telefon|bilgisayar|elbise|ayakkabı|çanta|aksesuar|kozmetik)\s+(öner|tavsiye|bul|ara)/i', $userMessage);
        
        // Elektronik ürünleri listele gibi kategori aramaları için
        $categorySearch = preg_match('/(elektronik|giyim|kitap|kozmetik|aksesuar|mobilya|bahçe|müzik|film|telefon|bilgisayar|ayakkabı|elbise|saat)\s+(ürünleri?|listele|göster|ara|kategorisinde)/i', $userMessage);
        
        if ($categorySearch) {
            // Kategori adını çıkar
            preg_match('/(elektronik|giyim|kitap|kozmetik|aksesuar|mobilya|bahçe|müzik|film|telefon|bilgisayar|ayakkabı|elbise|saat)/i', $userMessage, $matches);
            $category = strtolower($matches[1]);
            
            // Kategori eşleştirmesi yap
            $categoryMapping = [
                'elektronik' => ['electronics', 'telefon', 'bilgisayar'],
                'giyim' => ['men\'s clothing', 'women\'s clothing', 'ayakkabı', 'elbise'],
                'spor' => ['ayakkabı'],
                'teknoloji' => ['electronics', 'telefon', 'bilgisayar'],
                'mücevher' => ['jewelery'],
                'aksesuar' => ['jewelery']
            ];
            
            $searchCategories = $categoryMapping[$category] ?? [$category];
            
            // Knowledge base'den kategoriye göre ürünleri al
            $allProducts = [];
            foreach ($searchCategories as $searchCategory) {
                $categoryProducts = $this->getProductsFromKnowledgeBase($searchCategory);
                $allProducts = array_merge($allProducts, $categoryProducts);
            }
            
            if (!empty($allProducts)) {
                // Kategori eşleştirmesi yap
                $filteredProducts = array_filter($allProducts, function($product) use ($searchCategories) {
                    return in_array(strtolower($product['category']), $searchCategories);
                });
                
                if (!empty($filteredProducts)) {
                    $products = array_slice($filteredProducts, 0, 8); // En fazla 8 ürün göster
                    $message = ucfirst($category) . " kategorisinde " . count($products) . " ürün buldum:";
                } else {
                    // AI eşleştirme yapamadıysa, fuzzy search yap
                    $products = $this->fuzzyCategorySearch($allProducts, $category);
                    if (!empty($products)) {
                        $products = array_slice($products, 0, 8);
                        $message = ucfirst($category) . " kategorisinde " . count($products) . " ürün buldum:";
                    } else {
                        $message = ucfirst($category) . " kategorisinde ürün bulamadım. Farklı bir kategori deneyin.";
                    }
                }
            } else {
                $message = "Ürün veritabanında hiç ürün bulunamadı.";
            }
        } elseif (!empty($searchResults['results'])) {
            // Chunk'lardan ürün bilgilerini çıkar
            foreach (array_slice($searchResults['results'], 0, 5) as $result) {
                $product = $this->extractProductFromChunk($result);
                if ($product) {
                    $products[] = $product;
                }
            }
            
            if (!empty($products)) {
                if ($isPersonalizedRequest) {
                    $message = "Size özel olarak " . count($products) . " ürün öneriyorum:";
                } elseif ($hasSpecificProduct) {
                    $message = "Aradığınız ürünlerden " . count($products) . " tanesini buldum:";
                } else {
                    $message = "Aradığınız kriterlere uygun " . count($products) . " ürün buldum:";
                }
            } else {
                $message = "Aradığınız kriterlere uygun ürün bulamadım.";
            }
        } else {
            if ($isPersonalizedRequest) {
                // Kişiselleştirilmiş istek için knowledge base'den rastgele ürünler getir
                $products = $this->getRandomProductsFromKnowledgeBase(6);
                if (!empty($products)) {
                    $message = "Size özel olarak " . count($products) . " ürün öneriyorum:";
                } else {
                    $message = "Size özel ürün önerisi yapmak için daha fazla bilgiye ihtiyacım var. Hangi kategoride ürün arıyorsunuz?";
                    $suggestions = [
                        "Elektronik ürünler",
                        "Giyim ve aksesuar", 
                        "Ev ve yaşam",
                        "Spor ve outdoor",
                        "Kitap ve medya",
                        "Kozmetik ve kişisel bakım"
                    ];
                }
            } else {
                // Genel arama için de knowledge base'den rastgele ürünler getir
                $products = $this->getRandomProductsFromKnowledgeBase(6);
                if (!empty($products)) {
                    $message = "Aradığınız kriterlere uygun " . count($products) . " ürün buldum:";
                } else {
                    $message = "Aradığınız kriterlere uygun ürün bulunamadı. Farklı bir arama yapmayı deneyin.";
                    $suggestions = [
                        "Farklı kelimeler dene",
                        "Kategori seç",
                        "Marka belirle",
                        "Fiyat aralığı belirle"
                    ];
                }
            }
        }
        
        // Intent'e göre type belirle
        if ($isSpecificProductRequest) {
            $responseType = 'product_search'; // Spesifik ürün araması
        } elseif ($isPersonalizedRequest) {
            $responseType = 'product_recommendation'; // Genel öneri
        } else {
            $responseType = 'product_search'; // Normal arama
        }
        
        return [
            'type' => $responseType,
            'message' => $message,
            'products' => $products, // products'ı doğrudan ekle
            'data' => [
                'products' => $products,
                'search_query' => $userMessage,
                'total_found' => count($products),
                'search_confidence' => count($products) > 0 ? 'high' : 'low',
                'is_personalized' => $isPersonalizedRequest,
                'suggestions' => $suggestions ?? []
            ]
        ];
    }

    /**
     * Ürün önerisi response'u - ÖZEL
     */
    private function generateProductRecommendationResponse(string $userMessage, array $searchResults): array
    {
        try {
            // Rastgele ürün önerisi kontrolü
            $isRandomRequest = preg_match('/(rastgele|random|herhangi bir|ne olursa olsun|fark etmez)/i', $userMessage);
            
            if ($isRandomRequest) {
                // Rastgele ürün önerisi için özel handling
                $randomProducts = $this->getRandomProductsFromKnowledgeBase();
                
                if (!empty($randomProducts)) {
                    return [
                        'type' => 'product_recommendation',
                        'message' => 'Size rastgele seçilmiş ürünler öneriyorum:',
                        'products' => $randomProducts,
                        'data' => [
                            'products' => $randomProducts,
                            'search_query' => $userMessage,
                            'total_found' => count($randomProducts),
                            'search_confidence' => 'high',
                            'is_personalized' => 0,
                            'is_random' => true,
                            'reason' => 'Rastgele seçilmiş ürünler',
                            'category_matched' => false
                        ]
                    ];
                }
            }
            
            // SmartProductRecommenderService'i kullan
            $intentService = new \App\Http\Services\IntentDetectionService();
            $recommender = new \App\Http\Services\SmartProductRecommenderService($intentService);
            $recommendations = $recommender->getSmartRecommendations($userMessage);
            
            // Ürünleri formatla
            $formattedProducts = [];
            foreach ($recommendations['products'] as $product) {
                $formattedProducts[] = [
                    'id' => $product['id'],
                    'name' => $product['name'],
                    'price' => $product['price'],
                    'description' => $product['description'],
                    'category' => $product['category'],
                    'image' => \App\Helpers\ProductImageHelper::getImageWithFallback($product['image'] ?? null),
                    'rating' => $product['rating'] ?? 0,
                    'brand' => $product['brand'] ?? 'Bilinmiyor'
                ];
            }
            
            return [
                'type' => 'product_recommendation',
                'message' => $recommendations['response'],
                'products' => $formattedProducts,
                'data' => [
                    'products' => $formattedProducts,
                    'search_query' => $userMessage,
                    'total_found' => $recommendations['total_found'],
                    'search_confidence' => 'high',
                    'is_personalized' => 1,
                    'reason' => $recommendations['reason'],
                    'category_matched' => $recommendations['category_matched'],
                    'preferences' => $recommendations['preferences'] ?? null
                ]
            ];
            
        } catch (\Exception $e) {
            \Log::error('Product recommendation error: ' . $e->getMessage());
            
            // Fallback response - knowledge base'den rastgele ürünler getir
            $fallbackProducts = $this->getRandomProductsFromKnowledgeBase(6);
            return [
                'type' => 'product_recommendation',
                'message' => 'Size özel ürün önerileri hazırlıyorum:',
                'products' => $fallbackProducts,
                'data' => [
                    'products' => $fallbackProducts,
                    'search_query' => $userMessage,
                    'total_found' => count($fallbackProducts),
                    'search_confidence' => 'low',
                    'is_personalized' => 1
                ]
            ];
        }
    }
    

    
    /**
     * Knowledge base'den rastgele ürünler çeker
     */
    private function getRandomProductsFromKnowledgeBase(int $limit = 6): array
    {
        try {
            $chunks = KnowledgeChunk::where('content_type', 'product')
                ->with('knowledgeBase')
                ->inRandomOrder()
                ->limit($limit)
                ->get();
            
            $products = [];
            foreach ($chunks as $chunk) {
                // Chunk content'ini JSON olarak parse et
                $productData = json_decode($chunk->content, true);
                
                if (!$productData) {
                    continue; // JSON parse edilemezse skip et
                }
                
                // Metadata'nın zaten array olup olmadığını kontrol et
                if (is_array($chunk->metadata)) {
                    $metadata = $chunk->metadata;
                } else {
                    $metadata = json_decode($chunk->metadata, true) ?? [];
                }
                
                $products[] = [
                    'id' => $productData['id'] ?? $chunk->id,
                    'name' => $productData['title'] ?? 'Ürün ' . $chunk->id,
                    'category' => $productData['category'] ?? 'Genel',
                    'price' => $productData['price'] ?? 0,
                    'brand' => $productData['brand'] ?? 'Bilinmeyen',
                    'rating' => $productData['rating']['rate'] ?? 4.0,
                    'stock' => 10, // Default stock
                    'image' => ProductImageHelper::getImageWithFallback($productData['image'] ?? null),
                    'description' => $productData['description'] ?? substr($chunk->content, 0, 200) . '...',
                    'product_url' => 'https://example.com/product/' . ($productData['id'] ?? $chunk->id) . '?intent=random_recommendation',
                    'source' => 'knowledge_base'
                ];
            }
            
            return $products;
            
        } catch (\Exception $e) {
            Log::error('Random products fetch error: ' . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Rastgele ürünler ekler
     */
    private function addRandomProducts(array &$products): void
    {
        $allChunks = KnowledgeChunk::with('knowledgeBase')
            ->where('content_type', 'product')
            ->get()
            ->toArray();
        
        if (!empty($allChunks)) {
            shuffle($allChunks);
            foreach (array_slice($allChunks, 0, 6) as $chunk) {
                $product = $this->extractProductFromChunk($chunk);
                if ($product && count($products) < 6) {
                    $products[] = $product;
                }
            }
        }
    }
    
    /**
     * Mesajdan renk bilgisini çıkarır
     */
    private function extractColorFromMessage(string $message): ?string
    {
        $colors = [
            'kırmızı' => 'kırmızı',
            'mavi' => 'mavi',
            'yeşil' => 'yeşil',
            'sarı' => 'sarı',
            'siyah' => 'siyah',
            'beyaz' => 'beyaz',
            'pembe' => 'pembe',
            'mor' => 'mor',
            'turuncu' => 'turuncu',
            'gri' => 'gri',
            'kahverengi' => 'kahverengi'
        ];
        
        foreach ($colors as $color => $value) {
            if (stripos($message, $color) !== false) {
                return $value;
            }
        }
        
        return null;
    }
    
    /**
     * Ürünün belirtilen renge uyup uymadığını kontrol eder
     */
    private function productMatchesColor(array $product, string $searchColor): bool
    {
        $productName = strtolower($product['name']);
        $productDescription = strtolower($product['description'] ?? '');
        
        // Ürün adında veya açıklamasında renk geçiyor mu?
        if (stripos($productName, $searchColor) !== false || stripos($productDescription, $searchColor) !== false) {
            return true;
        }
        
        // Renk eşleştirmeleri
        $colorMatches = [
            'kırmızı' => ['red', 'crimson', 'scarlet'],
            'mavi' => ['blue', 'navy', 'azure'],
            'yeşil' => ['green', 'emerald', 'forest'],
            'sarı' => ['yellow', 'gold', 'amber'],
            'siyah' => ['black', 'dark', 'ebony'],
            'beyaz' => ['white', 'ivory', 'cream'],
            'pembe' => ['pink', 'rose', 'fuchsia'],
            'mor' => ['purple', 'violet', 'lavender'],
            'turuncu' => ['orange', 'tangerine', 'coral'],
            'gri' => ['gray', 'grey', 'silver'],
            'kahverengi' => ['brown', 'chocolate', 'tan']
        ];
        
        if (isset($colorMatches[$searchColor])) {
            foreach ($colorMatches[$searchColor] as $englishColor) {
                if (stripos($productName, $englishColor) !== false || stripos($productDescription, $englishColor) !== false) {
                    return true;
                }
            }
        }
        
        return false;
    }
    
    /**
     * Mesajdan kategori bilgisini çıkarır
     */
    private function extractCategoryFromMessage(string $message): string
    {
        $categories = [
            'elektronik' => ['elektronik', 'electronics', 'electronic', 'tech', 'technology', 'monitör', 'monitor', 'ekran', 'screen', 'tv', 'televizyon', 'bilgisayar', 'computer', 'pc', 'laptop', 'desktop', 'tablet', 'telefon', 'phone', 'mobile', 'smartphone', 'saat', 'watch', 'kamera', 'camera', 'kulaklık', 'headphone', 'hoparlör', 'speaker', 'klavye', 'keyboard', 'mouse', 'fare', 'yazıcı', 'printer', 'scanner', 'tarayıcı', 'qled', 'oled', 'led', '4k', '8k', 'hd', 'fullhd', 'ultrahd', 'gaming', 'oyun', 'game'],
            'giyim' => ['giyim', 'clothing', 'clothes', 'fashion', 'apparel', 'wear', 'elbise', 'dress', 'gömlek', 'shirt', 'pantolon', 'pants', 'trousers', 'etek', 'skirt', 'ceket', 'jacket', 'kazak', 'sweater', 'bluz', 'blouse', 'ayakkabı', 'shoe', 'çanta', 'bag', 'şapka', 'hat', 'kemer', 'belt', 'çorap', 'sock', 'iç çamaşır', 'underwear', 'mayo', 'swimsuit', 'spor', 'sport', 'fitness', 'athletic'],
            'kitap' => ['kitap', 'book', 'books', 'literature', 'reading', 'roman', 'novel', 'hikaye', 'story', 'şiir', 'poetry', 'dergi', 'magazine', 'gazete', 'newspaper', 'ansiklopedi', 'encyclopedia', 'sözlük', 'dictionary', 'atlas', 'atlas', 'çizgi roman', 'comic', 'manga', 'manga'],
            'saat' => ['saat', 'watch', 'watches', 'clock', 'timepiece', 'kol saati', 'wristwatch', 'duvar saati', 'wall clock', 'masa saati', 'desk clock', 'çalar saat', 'alarm clock', 'akıllı saat', 'smartwatch', 'dijital', 'digital', 'analog', 'analog'],
            'telefon' => ['telefon', 'phone', 'mobile', 'smartphone', 'cell', 'cep telefonu', 'mobile phone', 'akıllı telefon', 'smartphone', 'iphone', 'iphone', 'samsung', 'samsung', 'huawei', 'huawei', 'xiaomi', 'xiaomi', 'oppo', 'oppo', 'vivo', 'vivo'],
            'bilgisayar' => ['bilgisayar', 'computer', 'pc', 'laptop', 'desktop', 'notebook', 'netbook', 'ultrabook', 'macbook', 'macbook', 'imac', 'imac', 'mac', 'mac', 'windows', 'windows', 'linux', 'linux', 'macos', 'macos', 'işlemci', 'processor', 'cpu', 'cpu', 'ram', 'ram', 'ssd', 'ssd', 'hdd', 'hdd', 'ekran kartı', 'graphics card', 'gpu', 'gpu'],
            'ev' => ['ev', 'home', 'house', 'dekorasyon', 'decoration', 'interior', 'mobilya', 'furniture', 'halı', 'carpet', 'perde', 'curtain', 'lamba', 'lamp', 'mum', 'candle', 'vazo', 'vase', 'resim', 'picture', 'tablo', 'painting', 'çerçeve', 'frame', 'yastık', 'pillow', 'battaniye', 'blanket'],
            'spor' => ['spor', 'sport', 'fitness', 'exercise', 'athletic', 'koşu', 'running', 'yürüyüş', 'walking', 'bisiklet', 'bicycle', 'yoga', 'yoga', 'pilates', 'pilates', 'ağırlık', 'weight', 'dumbbell', 'dumbbell', 'halter', 'barbell', 'top', 'ball', 'raket', 'racket', 'kayak', 'ski'],
            'kozmetik' => ['kozmetik', 'cosmetic', 'beauty', 'makeup', 'skincare', 'makyaj', 'makeup', 'parfüm', 'perfume', 'krem', 'cream', 'losyon', 'lotion', 'şampuan', 'shampoo', 'saç', 'hair', 'cilt', 'skin', 'tırnak', 'nail', 'dudak', 'lip', 'göz', 'eye'],
            'aksesuar' => ['aksesuar', 'accessory', 'accessories', 'jewelry', 'takı', 'jewelry', 'kolye', 'necklace', 'yüzük', 'ring', 'küpe', 'earring', 'bilezik', 'bracelet', 'saat', 'watch', 'çanta', 'bag', 'cüzdan', 'wallet', 'güneş gözlüğü', 'sunglasses', 'şal', 'scarf', 'fular', 'scarf'],
            'mobilya' => ['mobilya', 'furniture', 'furnishings', 'furnishing', 'koltuk', 'sofa', 'sandalye', 'chair', 'masa', 'table', 'dolap', 'cabinet', 'wardrobe', 'wardrobe', 'yatak', 'bed', 'komodin', 'nightstand', 'vitrin', 'display case', 'raf', 'shelf', 'çekmece', 'drawer'],
            'bahçe' => ['bahçe', 'garden', 'outdoor', 'yard', 'patio', 'çiçek', 'flower', 'bitki', 'plant', 'ağaç', 'tree', 'çim', 'grass', 'çit', 'fence', 'havuz', 'pool', 'şömine', 'fireplace', 'barbekü', 'barbecue', 'hamak', 'hammock', 'salıncak', 'swing'],
            'müzik' => ['müzik', 'music', 'audio', 'sound', 'musical', 'gitar', 'guitar', 'piyano', 'piano', 'keman', 'violin', 'flüt', 'flute', 'davul', 'drum', 'bateri', 'drum set', 'mikrofon', 'microphone', 'hoparlör', 'speaker', 'kulaklık', 'headphone', 'cd', 'cd', 'vinyl', 'vinyl'],
            'film' => ['film', 'movie', 'cinema', 'video', 'dvd', 'dvd', 'bluray', 'bluray', '4k', '4k', 'uhd', 'uhd', 'projeksiyon', 'projection', 'perde', 'screen', 'kamera', 'camera', 'video kamera', 'video camera', 'drone', 'drone']
        ];
        
        $messageLower = strtolower($message);
        
        // Önce tam eşleşme ara
        foreach ($categories as $category => $keywords) {
            foreach ($keywords as $keyword) {
                if (stripos($messageLower, $keyword) !== false) {
                    return $category;
                }
            }
        }
        
        // Eğer hiçbir kategori bulunamazsa, mesaj içeriğine göre tahmin et
        if (preg_match('/(monitör|monitor|ekran|screen|tv|televizyon|bilgisayar|computer|pc|laptop|desktop|tablet|telefon|phone|mobile|smartphone|saat|watch|kamera|camera|kulaklık|headphone|hoparlör|speaker|klavye|keyboard|mouse|fare|yazıcı|printer|scanner|tarayıcı|qled|oled|led|4k|8k|hd|fullhd|ultrahd|gaming|oyun|game)/i', $messageLower)) {
            return 'elektronik';
        }
        
        if (preg_match('/(elbise|dress|gömlek|shirt|pantolon|pants|trousers|etek|skirt|ceket|jacket|kazak|sweater|bluz|blouse|ayakkabı|shoe|çanta|bag|şapka|hat|kemer|belt|çorap|sock|iç çamaşır|underwear|mayo|swimsuit)/i', $messageLower)) {
            return 'giyim';
        }
        
        if (preg_match('/(kitap|book|books|literature|reading|roman|novel|hikaye|story|şiir|poetry|dergi|magazine|gazete|newspaper|ansiklopedi|encyclopedia|sözlük|dictionary|atlas|atlas|çizgi roman|comic|manga|manga)/i', $messageLower)) {
            return 'kitap';
        }
        
        if (preg_match('/(saat|watch|watches|clock|timepiece|kol saati|wristwatch|duvar saati|wall clock|masa saati|desk clock|çalar saat|alarm clock|akıllı saat|smartwatch)/i', $messageLower)) {
            return 'saat';
        }
        
        if (preg_match('/(telefon|phone|mobile|smartphone|cell|cep telefonu|mobile phone|akıllı telefon|smartphone|iphone|iphone|samsung|samsung|huawei|huawei|xiaomi|xiaomi|oppo|oppo|vivo|vivo)/i', $messageLower)) {
            return 'telefon';
        }
        
        if (preg_match('/(bilgisayar|computer|pc|laptop|desktop|notebook|netbook|ultrabook|macbook|macbook|imac|imac|mac|mac|windows|windows|linux|linux|macos|macos|işlemci|processor|cpu|cpu|ram|ram|ssd|ssd|hdd|hdd|ekran kartı|graphics card|gpu|gpu)/i', $messageLower)) {
            return 'bilgisayar';
        }
        
        if (preg_match('/(ev|home|house|dekorasyon|decoration|interior|mobilya|furniture|halı|carpet|perde|curtain|lamba|lamp|mum|candle|vazo|vase|resim|picture|tablo|painting|çerçeve|frame|yastık|pillow|battaniye|blanket)/i', $messageLower)) {
            return 'ev';
        }
        
        if (preg_match('/(spor|sport|fitness|exercise|athletic|koşu|running|yürüyüş|walking|bisiklet|bicycle|yoga|yoga|pilates|pilates|ağırlık|weight|dumbbell|dumbbell|halter|barbell|top|ball|raket|racket|kayak|ski)/i', $messageLower)) {
            return 'spor';
        }
        
        if (preg_match('/(kozmetik|cosmetic|beauty|makeup|skincare|makyaj|makeup|parfüm|perfume|krem|cream|losyon|lotion|şampuan|shampoo|saç|hair|cilt|skin|tırnak|nail|dudak|lip|göz|eye)/i', $messageLower)) {
            return 'kozmetik';
        }
        
        if (preg_match('/(aksesuar|accessory|accessories|jewelry|takı|jewelry|kolye|necklace|yüzük|ring|küpe|earring|bilezik|bracelet|saat|watch|çanta|bag|cüzdan|wallet|güneş gözlüğü|sunglasses|şal|scarf|fular|scarf)/i', $messageLower)) {
            return 'aksesuar';
        }
        
        if (preg_match('/(mobilya|furniture|furnishings|furnishing|koltuk|sofa|sandalye|chair|masa|table|dolap|cabinet|wardrobe|wardrobe|yatak|bed|komodin|nightstand|vitrin|display case|raf|shelf|çekmece|drawer)/i', $messageLower)) {
            return 'mobilya';
        }
        
        if (preg_match('/(bahçe|garden|outdoor|yard|patio|çiçek|flower|bitki|plant|ağaç|tree|çim|grass|çit|fence|havuz|pool|şömine|fireplace|barbekü|barbecue|hamak|hammock|salıncak|swing)/i', $messageLower)) {
            return 'bahçe';
        }
        
        if (preg_match('/(müzik|music|audio|sound|musical|gitar|guitar|piyano|piano|keman|violin|flüt|flute|davul|drum|bateri|drum set|mikrofon|microphone|hoparlör|speaker|kulaklık|headphone|cd|cd|vinyl|vinyl)/i', $messageLower)) {
            return 'müzik';
        }
        
        if (preg_match('/(film|movie|cinema|video|dvd|dvd|bluray|bluray|4k|4k|uhd|uhd|projeksiyon|projection|perde|screen|kamera|camera|video kamera|video camera|drone|drone)/i', $messageLower)) {
            return 'film';
        }
        
        // Hiçbir kategori bulunamazsa genel kategori döndür
        return 'genel';
    }
    
    /**
     * Ürünün belirtilen kategoriye uyup uymadığını kontrol eder
     */
    private function productMatchesCategory(array $product, string $searchCategory): bool
    {
        // Kategori belirtilmemişse veya genel ise tüm ürünleri göster
        if (empty($searchCategory) || $searchCategory === 'genel') {
            return true;
        }
        
        $productCategory = strtolower($product['category'] ?? '');
        $productName = strtolower($product['name'] ?? '');
        $productDescription = strtolower($product['description'] ?? '');
        
        // Kategori eşleştirmeleri - daha kapsamlı
        $categoryMatches = [
            'elektronik' => ['electronics', 'electronic', 'tech', 'technology', 'gadget', 'monitör', 'monitor', 'ekran', 'screen', 'tv', 'televizyon', 'bilgisayar', 'computer', 'pc', 'laptop', 'desktop', 'tablet', 'telefon', 'phone', 'mobile', 'smartphone', 'saat', 'watch', 'kamera', 'camera', 'kulaklık', 'headphone', 'hoparlör', 'speaker', 'klavye', 'keyboard', 'mouse', 'fare', 'yazıcı', 'printer', 'scanner', 'tarayıcı', 'qled', 'oled', 'led', '4k', '8k', 'hd', 'fullhd', 'ultrahd', 'gaming', 'oyun', 'game'],
            'giyim' => ['clothing', 'clothes', 'fashion', 'apparel', 'wear', 'elbise', 'dress', 'gömlek', 'shirt', 'pantolon', 'pants', 'trousers', 'etek', 'skirt', 'ceket', 'jacket', 'kazak', 'sweater', 'bluz', 'blouse', 'ayakkabı', 'shoe', 'çanta', 'bag', 'şapka', 'hat', 'kemer', 'belt', 'çorap', 'sock', 'iç çamaşır', 'underwear', 'mayo', 'swimsuit', 'spor', 'sport', 'fitness', 'athletic'],
            'kitap' => ['book', 'books', 'literature', 'reading', 'roman', 'novel', 'hikaye', 'story', 'şiir', 'poetry', 'dergi', 'magazine', 'gazete', 'newspaper', 'ansiklopedi', 'encyclopedia', 'sözlük', 'dictionary', 'atlas', 'atlas', 'çizgi roman', 'comic', 'manga', 'manga'],
            'saat' => ['watch', 'watches', 'clock', 'timepiece', 'kol saati', 'wristwatch', 'duvar saati', 'wall clock', 'masa saati', 'desk clock', 'çalar saat', 'alarm clock', 'akıllı saat', 'smartwatch', 'dijital', 'digital', 'analog', 'analog'],
            'telefon' => ['phone', 'mobile', 'smartphone', 'cell', 'cep telefonu', 'mobile phone', 'akıllı telefon', 'smartphone', 'iphone', 'iphone', 'samsung', 'samsung', 'huawei', 'huawei', 'xiaomi', 'xiaomi', 'oppo', 'oppo', 'vivo', 'vivo'],
            'bilgisayar' => ['computer', 'pc', 'laptop', 'desktop', 'notebook', 'netbook', 'ultrabook', 'macbook', 'macbook', 'imac', 'imac', 'mac', 'mac', 'windows', 'windows', 'linux', 'linux', 'macos', 'macos', 'işlemci', 'processor', 'cpu', 'cpu', 'ram', 'ram', 'ssd', 'ssd', 'hdd', 'hdd', 'ekran kartı', 'graphics card', 'gpu', 'gpu'],
            'ev' => ['home', 'house', 'dekorasyon', 'decoration', 'interior', 'mobilya', 'furniture', 'halı', 'carpet', 'perde', 'curtain', 'lamba', 'lamp', 'mum', 'candle', 'vazo', 'vase', 'resim', 'picture', 'tablo', 'painting', 'çerçeve', 'frame', 'yastık', 'pillow', 'battaniye', 'blanket'],
            'spor' => ['sport', 'fitness', 'exercise', 'athletic', 'koşu', 'running', 'yürüyüş', 'walking', 'bisiklet', 'bicycle', 'yoga', 'yoga', 'pilates', 'pilates', 'ağırlık', 'weight', 'dumbbell', 'dumbbell', 'halter', 'barbell', 'top', 'ball', 'raket', 'racket', 'kayak', 'ski'],
            'kozmetik' => ['cosmetic', 'beauty', 'makeup', 'skincare', 'makyaj', 'makeup', 'parfüm', 'perfume', 'krem', 'cream', 'losyon', 'lotion', 'şampuan', 'shampoo', 'saç', 'hair', 'cilt', 'skin', 'tırnak', 'nail', 'dudak', 'lip', 'göz', 'eye'],
            'aksesuar' => ['accessory', 'accessories', 'jewelry', 'takı', 'jewelry', 'kolye', 'necklace', 'yüzük', 'ring', 'küpe', 'earring', 'bilezik', 'bracelet', 'saat', 'watch', 'çanta', 'bag', 'cüzdan', 'wallet', 'güneş gözlüğü', 'sunglasses', 'şal', 'scarf', 'fular', 'scarf'],
            'mobilya' => ['furniture', 'furnishings', 'furnishing', 'koltuk', 'sofa', 'sandalye', 'chair', 'masa', 'table', 'dolap', 'cabinet', 'wardrobe', 'wardrobe', 'yatak', 'bed', 'komodin', 'nightstand', 'vitrin', 'display case', 'raf', 'shelf', 'çekmece', 'drawer'],
            'bahçe' => ['garden', 'outdoor', 'yard', 'patio', 'çiçek', 'flower', 'bitki', 'plant', 'ağaç', 'tree', 'çim', 'grass', 'çit', 'fence', 'havuz', 'pool', 'şömine', 'fireplace', 'barbekü', 'barbecue', 'hamak', 'hammock', 'salıncak', 'swing'],
            'müzik' => ['music', 'audio', 'sound', 'musical', 'gitar', 'guitar', 'piyano', 'piano', 'keman', 'violin', 'flüt', 'flute', 'davul', 'drum', 'bateri', 'drum set', 'mikrofon', 'microphone', 'hoparlör', 'speaker', 'kulaklık', 'headphone', 'cd', 'cd', 'vinyl', 'vinyl'],
            'film' => ['movie', 'cinema', 'video', 'dvd', 'dvd', 'bluray', 'bluray', '4k', '4k', 'uhd', 'uhd', 'projeksiyon', 'projection', 'perde', 'screen', 'kamera', 'camera', 'video kamera', 'video camera', 'drone', 'drone']
        ];
        
        // Tam kategori eşleşmesi
        if (stripos($productCategory, $searchCategory) !== false) {
            return true;
        }
        
        // Kategori anahtar kelimeleri ile eşleşme
        if (isset($categoryMatches[$searchCategory])) {
            foreach ($categoryMatches[$searchCategory] as $keyword) {
                if (stripos($productCategory, $keyword) !== false || 
                    stripos($productName, $keyword) !== false || 
                    stripos($productDescription, $keyword) !== false) {
                    return true;
                }
            }
        }
        
        // Ürün adı ve açıklamasında kategori anahtar kelimelerini ara
        $searchKeywords = [];
        switch ($searchCategory) {
            case 'elektronik':
                $searchKeywords = ['monitör', 'monitor', 'ekran', 'screen', 'tv', 'televizyon', 'bilgisayar', 'computer', 'pc', 'laptop', 'desktop', 'tablet', 'telefon', 'phone', 'mobile', 'smartphone', 'saat', 'watch', 'kamera', 'camera', 'kulaklık', 'headphone', 'hoparlör', 'speaker', 'klavye', 'keyboard', 'mouse', 'fare', 'yazıcı', 'printer', 'scanner', 'tarayıcı', 'qled', 'oled', 'led', '4k', '8k', 'hd', 'fullhd', 'ultrahd', 'gaming', 'oyun', 'game'];
                break;
            case 'giyim':
                $searchKeywords = ['elbise', 'dress', 'gömlek', 'shirt', 'pantolon', 'pants', 'trousers', 'etek', 'skirt', 'ceket', 'jacket', 'kazak', 'sweater', 'bluz', 'blouse', 'ayakkabı', 'shoe', 'çanta', 'bag', 'şapka', 'hat', 'kemer', 'belt', 'çorap', 'sock', 'iç çamaşır', 'underwear', 'mayo', 'swimsuit'];
                break;
            case 'kitap':
                $searchKeywords = ['kitap', 'book', 'books', 'literature', 'reading', 'roman', 'novel', 'hikaye', 'story', 'şiir', 'poetry', 'dergi', 'magazine', 'gazete', 'newspaper', 'ansiklopedi', 'encyclopedia', 'sözlük', 'dictionary', 'atlas', 'atlas', 'çizgi roman', 'comic', 'manga', 'manga'];
                break;
            case 'saat':
                $searchKeywords = ['saat', 'watch', 'watches', 'clock', 'timepiece', 'kol saati', 'wristwatch', 'duvar saati', 'wall clock', 'masa saati', 'desk clock', 'çalar saat', 'alarm clock', 'akıllı saat', 'smartwatch', 'dijital', 'digital', 'analog', 'analog'];
                break;
            case 'telefon':
                $searchKeywords = ['telefon', 'phone', 'mobile', 'smartphone', 'cell', 'cep telefonu', 'mobile phone', 'akıllı telefon', 'smartphone', 'iphone', 'iphone', 'samsung', 'samsung', 'huawei', 'huawei', 'xiaomi', 'xiaomi', 'oppo', 'oppo', 'vivo', 'vivo'];
                break;
            case 'bilgisayar':
                $searchKeywords = ['bilgisayar', 'computer', 'pc', 'laptop', 'desktop', 'notebook', 'netbook', 'ultrabook', 'macbook', 'macbook', 'imac', 'imac', 'mac', 'mac', 'windows', 'windows', 'linux', 'linux', 'macos', 'macos', 'işlemci', 'processor', 'cpu', 'cpu', 'ram', 'ram', 'ssd', 'ssd', 'hdd', 'hdd', 'ekran kartı', 'graphics card', 'gpu', 'gpu'];
                break;
            case 'ev':
                $searchKeywords = ['ev', 'home', 'house', 'dekorasyon', 'decoration', 'interior', 'mobilya', 'furniture', 'halı', 'carpet', 'perde', 'curtain', 'lamba', 'lamp', 'mum', 'candle', 'vazo', 'vase', 'resim', 'picture', 'tablo', 'painting', 'çerçeve', 'frame', 'yastık', 'pillow', 'battaniye', 'blanket'];
                break;
            case 'spor':
                $searchKeywords = ['spor', 'sport', 'fitness', 'exercise', 'athletic', 'koşu', 'running', 'yürüyüş', 'walking', 'bisiklet', 'bicycle', 'yoga', 'yoga', 'pilates', 'pilates', 'ağırlık', 'weight', 'dumbbell', 'dumbbell', 'halter', 'barbell', 'top', 'ball', 'raket', 'racket', 'kayak', 'ski'];
                break;
            case 'kozmetik':
                $searchKeywords = ['kozmetik', 'cosmetic', 'beauty', 'makeup', 'skincare', 'makyaj', 'makeup', 'parfüm', 'perfume', 'krem', 'cream', 'losyon', 'lotion', 'şampuan', 'shampoo', 'saç', 'hair', 'cilt', 'skin', 'tırnak', 'nail', 'dudak', 'lip', 'göz', 'eye'];
                break;
            case 'aksesuar':
                $searchKeywords = ['aksesuar', 'accessory', 'accessories', 'jewelry', 'takı', 'jewelry', 'kolye', 'necklace', 'yüzük', 'ring', 'küpe', 'earring', 'bilezik', 'bracelet', 'saat', 'watch', 'çanta', 'bag', 'cüzdan', 'wallet', 'güneş gözlüğü', 'sunglasses', 'şal', 'scarf', 'fular', 'scarf'];
                break;
            case 'mobilya':
                $searchKeywords = ['mobilya', 'furniture', 'furnishings', 'furnishing', 'koltuk', 'sofa', 'sandalye', 'chair', 'masa', 'table', 'dolap', 'cabinet', 'wardrobe', 'wardrobe', 'yatak', 'bed', 'komodin', 'nightstand', 'vitrin', 'display case', 'raf', 'shelf', 'çekmece', 'drawer'];
                break;
            case 'bahçe':
                $searchKeywords = ['bahçe', 'garden', 'outdoor', 'yard', 'patio', 'çiçek', 'flower', 'bitki', 'plant', 'ağaç', 'tree', 'çim', 'grass', 'çit', 'fence', 'havuz', 'pool', 'şömine', 'fireplace', 'barbekü', 'barbecue', 'hamak', 'hammock', 'salıncak', 'swing'];
                break;
            case 'müzik':
                $searchKeywords = ['müzik', 'music', 'audio', 'sound', 'musical', 'gitar', 'guitar', 'piyano', 'piano', 'keman', 'violin', 'flüt', 'flute', 'davul', 'drum', 'bateri', 'drum set', 'mikrofon', 'microphone', 'hoparlör', 'speaker', 'kulaklık', 'headphone', 'cd', 'cd', 'vinyl', 'vinyl'];
                break;
            case 'film':
                $searchKeywords = ['film', 'movie', 'cinema', 'video', 'dvd', 'dvd', 'bluray', 'bluray', '4k', '4k', 'uhd', 'uhd', 'projeksiyon', 'projection', 'perde', 'screen', 'kamera', 'camera', 'video kamera', 'video camera', 'drone', 'drone'];
                break;
        }
        
        // Ürün adı ve açıklamasında kategori anahtar kelimelerini ara
        foreach ($searchKeywords as $keyword) {
            if (stripos($productName, $keyword) !== false || 
                stripos($productDescription, $keyword) !== false) {
                return true;
            }
        }
        
        return false;
    }

    /**
     * Chunk'tan ürün bilgilerini çıkarır
     */
    private function extractProductFromChunk(array $chunk): ?array
    {
        try {
            $content = $chunk['content'];
            $metadata = $chunk['metadata'] ?? [];
            
            // JSON content ise parse et
            if (is_string($content) && $this->isJson($content)) {
                $jsonData = json_decode($content, true);
                if (is_array($jsonData) && !empty($jsonData)) {
                    // Eğer array ise ilk elemanı al, değilse direkt kullan
                    $productData = is_numeric(array_keys($jsonData)[0]) ? $jsonData[0] : $jsonData;
                    
                    return [
                        'id' => $productData['id'] ?? $chunk['id'] ?? uniqid(),
                        'name' => $productData['title'] ?? $productData['name'] ?? 'Ürün',
                        'brand' => $productData['brand'] ?? 'Marka',
                        'price' => $productData['price'] ?? 0,
                        'image' => $productData['image'] ?? '/imgs/default-product.jpeg',
                        'category' => $productData['category'] ?? 'Genel',
                        'rating' => is_array($productData['rating']) ? ($productData['rating']['rate'] ?? 4.0) : ($productData['rating'] ?? 4.0),
                        'relevance_score' => $chunk['relevance_score'] ?? $chunk['fuzzy_score'] ?? 0
                    ];
                }
            }
            
            // Metadata'dan ürün bilgilerini al
            if (!empty($metadata)) {
                return [
                    'id' => $metadata['product_id'] ?? $chunk['id'] ?? uniqid(),
                    'name' => $metadata['product_title'] ?? 'Ürün',
                    'brand' => 'Marka',
                    'price' => $metadata['product_price'] ?? 0,
                    'image' => '/imgs/default-product.jpeg',
                    'category' => $metadata['product_category'] ?? 'Genel',
                    'rating' => $metadata['product_rating']['rate'] ?? 4.0,
                    'relevance_score' => $chunk['relevance_score'] ?? $chunk['fuzzy_score'] ?? 0
                ];
            }
            
            return null;
            
        } catch (\Exception $e) {
            Log::warning('Product extraction error: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * JSON string kontrolü
     */
    private function isJson(string $string): bool
    {
        json_decode($string);
        return json_last_error() === JSON_ERROR_NONE;
    }

    /**
     * Kategori response'u
     */
    private function generateCategoryResponse(string $userMessage, array $searchResults): array
    {
        $products = [];
        $categoryName = $this->extractCategoryFromMessage($userMessage);
        
        if (!empty($searchResults['results'])) {
            foreach ($searchResults['results'] as $result) {
                if (($result['content_type'] ?? '') === 'product') {
                    $product = $this->extractProductFromChunk($result);
                    if ($product && $this->productMatchesCategory($product, $categoryName)) {
                        $products[] = $product;
                    }
                }
            }
        }
        
        // Eğer search results'da ürün bulunamazsa, tüm ürünlerden kategoriye göre filtrele
        if (empty($products) && $categoryName) {
            $allChunks = KnowledgeChunk::with('knowledgeBase')
                ->where('content_type', 'product')
                ->get()
                ->toArray();
            
            foreach ($allChunks as $chunk) {
                $product = $this->extractProductFromChunk($chunk);
                if ($product && $this->productMatchesCategory($product, $categoryName)) {
                    $products[] = $product;
                }
            }
        }
        
        $message = '';
        if (!empty($products)) {
            $message = $categoryName 
                ? "{$categoryName} kategorisinde " . count($products) . " ürün buldum:"
                : "Kategoriye göre " . count($products) . " ürün buldum:";
        } else {
            $message = $categoryName 
                ? "{$categoryName} kategorisinde ürün bulunamadı."
                : "Kategoriye göre ürün bulunamadı.";
        }
        
        return [
            'type' => 'category_browse',
            'message' => $message,
            'data' => [
                'products' => $products,
                'category' => $categoryName,
                'total_products' => count($products),
                'search_query' => $userMessage
            ]
        ];
    }

    /**
     * Marka response'u
     */
    private function generateBrandResponse(string $userMessage, array $searchResults): array
    {
        $brands = [];
        
        if (!empty($searchResults['results'])) {
            foreach ($searchResults['results'] as $result) {
                $metadata = $result['metadata'] ?? [];
                if (isset($metadata['product_brand'])) {
                    $brands[] = $metadata['product_brand'];
                }
            }
            $brands = array_unique($brands);
        }
        
        return [
            'type' => 'brand_search',
            'message' => !empty($brands) 
                ? "Bulunan markalar: " . implode(', ', $brands)
                : "Marka bulunamadı.",
            'data' => [
                'brands' => $brands,
                'total_brands' => count($brands)
            ]
        ];
    }

    /**
     * FAQ response'u
     */
    private function generateFAQResponse(string $userMessage, array $searchResults): array
    {
        $faqs = [];
        
        if (!empty($searchResults['results'])) {
            foreach ($searchResults['results'] as $result) {
                if (($result['content_type'] ?? '') === 'faq') {
                    $faqs[] = [
                        'question' => $result['content'] ?? 'Soru',
                        'answer' => $result['metadata']['answer'] ?? 'Cevap bulunamadı',
                        'relevance_score' => $result['relevance_score'] ?? 0
                    ];
                }
            }
        }
        
        return [
            'type' => 'faq_search',
            'message' => !empty($faqs) 
                ? "Aradığınız soruya " . count($faqs) . " cevap buldum:"
                : "Aradığınız soruya cevap bulunamadı.",
            'data' => [
                'faqs' => $faqs,
                'total_faqs' => count($faqs)
            ]
        ];
    }

    /**
     * Sipariş takip response'u
     */
    private function generateOrderTrackingResponse(): array
    {
        return [
            'type' => 'order_tracking',
            'message' => 'Sipariş takip numaranızı girerek sipariş durumunuzu öğrenebilirsiniz.',
            'data' => [
                'requires_input' => true,
                'input_type' => 'order_number',
                'placeholder' => 'Sipariş numarası girin...',
                'button_text' => 'Sipariş Takip Et',
                'widget_type' => 'order_tracking_widget',
                'api_endpoint' => '/api/cargo/track', // Kargo takip API'sine yönlendir
                'intent' => 'order_tracking'
            ]
        ];
    }



    /**
     * Sepete ekle response'u
     */
    private function generateCartAddResponse(string $userMessage, array $searchResults): array
    {
        // Ürün adını çıkar
        preg_match('/(iphone|telefon|bilgisayar|macbook|samsung|nike|adidas)/i', $userMessage, $matches);
        $productName = $matches[1] ?? null;
        
        if ($productName) {
            // Knowledge base'den ürünü bul
            $product = KnowledgeChunk::where('content_type', 'product')
                ->where('content', 'like', '%' . $productName . '%')
                ->first();
            
            if ($product) {
                $metadata = is_string($product->metadata) ? json_decode($product->metadata, true) ?? [] : [];
                
                $productData = [
                    'id' => $product->id,
                    'name' => $metadata['name'] ?? 'Ürün ' . $product->id,
                    'price' => $metadata['price'] ?? 0,
                    'quantity' => 1,
                    'total_price' => $metadata['price'] ?? 0
                ];
                
                return [
                    'type' => 'cart_add',
                    'message' => $productData['name'] . ' sepete başarıyla eklendi!',
                    'data' => [
                        'product' => $productData,
                        'cart' => [
                            'total_items' => 1,
                            'total_amount' => $productData['total_price'],
                            'items' => [$productData]
                        ],
                        'widget_type' => 'cart_add_widget'
                    ]
                ];
            }
        }
        
        return [
            'type' => 'cart_add',
            'message' => 'Hangi ürünü sepete eklemek istiyorsunuz?',
            'data' => [
                'requires_input' => true,
                'input_type' => 'product_selection',
                'placeholder' => 'Ürün adı veya numarası girin...',
                'button_text' => 'Sepete Ekle',
                'widget_type' => 'cart_add_widget'
            ]
        ];
    }

    /**
     * Kargo takip response'u
     */
    private function generateCargoTrackingResponse(): array
    {
        return [
            'type' => 'cargo_tracking',
            'message' => 'Kargo takip numaranızı girerek kargo durumunuzu öğrenebilirsiniz.',
            'data' => [
                'requires_input' => true,
                'input_type' => 'cargo_number',
                'placeholder' => 'Kargo takip numarası girin...',
                'button_text' => 'Kargo Takip Et',
                'widget_type' => 'cargo_tracking_widget',
                'api_endpoint' => '/api/cargo/track', // Kargo takip API'sine yönlendir
                'intent' => 'cargo_tracking'
            ]
        ];
    }

    /**
     * Selamlama response'u
     */
    private function generateGreetingResponse(): array
    {
        return [
            'type' => 'greeting',
            'message' => 'Merhaba! Ben Kadir, senin dijital asistanınım. Size nasıl yardımcı olabilirim?'
        ];
    }

    /**
     * Yardım response'u
     */
    private function generateHelpResponse(): array
    {
        return [
            'type' => 'help',
            'message' => 'Size yardımcı olmak için buradayım! Ürünler hakkında bilgi almak, sipariş vermek veya herhangi bir sorunuzu çözmek için bana yazabilirsiniz.'
        ];
    }

    /**
     * Genel response
     */
    private function generateGeneralResponse(string $userMessage, array $searchResults): array
    {
        $message = 'Anlıyorum. Size daha iyi yardımcı olabilmem için biraz daha detay verebilir misiniz?';
        $products = [];
        
        // Eğer kullanıcı ürün ile ilgili bir şey soruyorsa, knowledge base'den rastgele ürünler getir
        if (preg_match('/(ürün|product|fiyat|price|satın|buy|öner|recommend|tavsiye|suggest)/i', $userMessage)) {
            $products = $this->getRandomProductsFromKnowledgeBase(6);
            if (!empty($products)) {
                $message = 'Size özel olarak ' . count($products) . ' ürün öneriyorum:';
            }
        }
        
        if (!empty($searchResults['results'])) {
            $message = 'Aradığınız konuyla ilgili bazı sonuçlar buldum. Daha spesifik bir arama yapabilir misiniz?';
        }
        
        return [
            'type' => 'general',
            'message' => $message,
            'data' => [
                'products' => $products
            ]
        ];
    }

    /**
     * AI interaction'ı loglar
     */
    private function logAIInteraction(string $userMessage, string $intent, array $response, string $sessionId): void
    {
        try {
            // AI interactions tablosuna kaydet (mevcut)
            AiInteraction::create([
                'session_id' => $sessionId,
                'message' => $userMessage,
                'intent' => $intent,
                'metadata' => ['ai_response' => $response]
            ]);

            // Enhanced Chat Session'ı güncelle veya oluştur
            $chatSession = EnhancedChatSession::where('session_id', $sessionId)->first();
            
            if (!$chatSession) {
                // Yeni session oluştur - Widget session'ları için default değerler
                $chatSession = EnhancedChatSession::create([
                    'session_id' => $sessionId,
                    'user_id' => 0, // Guest user
                    'project_id' => 1, // Default project
                    'daily_view_limit' => 100,
                    'status' => 'active',
                    'last_activity' => now(),
                    'expires_at' => now()->addHours(72) // 72 saat sonra expire et
                ]);
            } else {
                // Mevcut session'ı güncelle
                $chatSession->updateLastActivity();
            }

            // Intent history'ye ekle - Error handling ile
            try {
                $chatSession->addIntent($intent, $response['confidence'] ?? 0.0);
            } catch (\Exception $e) {
                Log::error('Failed to add intent', [
                    'session_id' => $sessionId,
                    'error' => $e->getMessage(),
                    'intent' => $intent
                ]);
            }

            // === FUNNEL INTENT TRACKING ===
            $this->trackFunnelIntent($chatSession, $intent, $userMessage, $response);

            // Chat history'ye ekle - Error handling ile
            try {
                $chatSession->addChatMessage('user', $userMessage, $intent);
                $chatSession->addChatMessage('bot', $response['message'] ?? 'Response', $intent);
            } catch (\Exception $e) {
                Log::error('Failed to add chat messages', [
                    'session_id' => $sessionId,
                    'error' => $e->getMessage(),
                    'user_message' => $userMessage,
                    'intent' => $intent
                ]);
                
                // Fallback: Sadece last_activity'yi güncelle
                $chatSession->updateLastActivity();
            }

            // User preferences güncelle (intent'e göre)
            $this->updateUserPreferencesFromIntent($chatSession, $intent, $response);

            Log::info('Enhanced AI interaction logged successfully', [
                'session_id' => $sessionId,
                'intent' => $intent,
                'session_updated' => true
            ]);

        } catch (\Exception $e) {
            Log::warning('AI interaction logging failed: ' . $e->getMessage());
        }
    }

    /**
     * Intent'e göre user preferences güncelle
     */
    private function updateUserPreferencesFromIntent(EnhancedChatSession $session, string $intent, array $response): void
    {
        try {
            $preferences = [];
            
            switch ($intent) {
                case 'product_search':
                    $preferences['preferred_categories'] = $this->extractCategoriesFromResponse($response);
                    $preferences['search_frequency'] = 'high';
                    break;
                    
                case 'category_browse':
                    $preferences['browsing_behavior'] = 'exploratory';
                    $preferences['preferred_categories'] = $this->extractCategoriesFromResponse($response);
                    break;
                    
                case 'brand_search':
                    $preferences['brand_preferences'] = $this->extractBrandsFromResponse($response);
                    break;
                    
                case 'order_tracking':
                    $preferences['order_concern'] = 'tracking';
                    break;
                    
                case 'faq_search':
                    $preferences['support_needs'] = 'information';
                    break;
                    
                default:
                    $preferences['general_interests'] = $intent;
                    break;
            }
            
            if (!empty($preferences)) {
                $session->updateUserPreferences($preferences);
            }
            
        } catch (\Exception $e) {
            Log::warning('User preferences update failed: ' . $e->getMessage());
        }
    }

    /**
     * Response'dan kategorileri çıkar
     */
    private function extractCategoriesFromResponse(array $response): array
    {
        $categories = [];
        
        if (isset($response['search_results']['results'])) {
            foreach ($response['search_results']['results'] as $result) {
                if (isset($result['metadata']['product_category'])) {
                    $categories[] = $result['metadata']['product_category'];
                }
            }
        }
        
        return array_unique($categories);
    }

    /**
     * Response'dan markaları çıkar
     */
    private function extractBrandsFromResponse(array $response): array
    {
        $brands = [];
        
        if (isset($response['search_results']['results'])) {
            foreach ($response['search_results']['results'] as $result) {
                if (isset($result['metadata']['product_brand'])) {
                    $brands[] = $result['metadata']['product_brand'];
                }
            }
        }
        
        return array_unique($brands);
    }

    /**
     * Feedback işleme
     */
    public function handleFeedback(Request $request) {
        $feedbackData = $request->all();
        
        // Feedback'i logla veya veritabanına kaydet
        Log::info('Feedback received:', $feedbackData);
        
        return response()->json([
            'success' => true,
            'message' => 'Feedback başarıyla alındı',
            'data' => $feedbackData
        ]);
    }

    /**
     * Ürün tıklama işleme
     */
    public function handleProductClick(Request $request) {
        $productData = $request->all();
        
        // Ürün tıklama verisini logla veya veritabanına kaydet
        Log::info('Product click:', $productData);
        
        return response()->json([
            'success' => true,
            'message' => 'Ürün tıklama kaydedildi',
            'data' => $productData
        ]);
    }

    /**
     * Product Interaction API - Ürün etkileşimlerini track eder
     */
    public function handleProductInteraction(Request $request)
    {
        try {
            // Validate request
            $validated = $request->validate([
                'session_id' => 'required|string',
                'product_id' => 'required|integer', // products tablosu henüz yok, validation'ı kaldır
                'action' => 'required|string|in:view,compare,add_to_cart,buy',
                'timestamp' => 'required|date',
                'source' => 'required|string|in:chat_widget,product_page,checkout',
                'metadata' => 'sometimes|array'
            ]);

            // Find or create session
            $session = EnhancedChatSession::firstOrCreate([
                'session_id' => $validated['session_id']
            ], [
                'status' => 'active',
                'daily_view_count' => 0,
                'daily_view_limit' => 20
            ]);

            // Daily limits'i kontrol et ve gerekirse sıfırla
            $session->refreshDailyLimits();
            
            // Check daily view limits
            if (!$session->canViewMore()) {
                // Log rate limit exceeded
                \App\Services\AuditLogService::logSecurityEvent('rate_limit_exceeded', [
                    'session_id' => $validated['session_id'],
                    'action' => $validated['action'],
                    'daily_view_count' => $session->daily_view_count,
                    'daily_view_limit' => $session->daily_view_limit
                ]);

                return response()->json([
                    'error' => 'Daily view limit reached',
                    'daily_view_count' => $session->daily_view_count,
                    'daily_view_limit' => $session->daily_view_limit
                ], 429);
            }

            // Create product interaction
            $interaction = ProductInteraction::create([
                'session_id' => $validated['session_id'],
                'product_id' => $validated['product_id'],
                'action' => $validated['action'],
                'timestamp' => $validated['timestamp'],
                'source' => $validated['source'],
                'metadata' => $validated['metadata'] ?? [],
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent()
            ]);

            // Update session
            $session->incrementViewCount();
            $session->updateLastActivity();

            // Log the interaction for audit
            \App\Services\AuditLogService::logProductInteraction(
                $validated['session_id'],
                $validated['product_id'],
                $validated['action'],
                $validated['metadata'] ?? []
            );

            // Log chat session activity
            \App\Services\AuditLogService::logChatSessionActivity(
                $validated['session_id'],
                'product_interaction',
                [
                    'action' => $validated['action'],
                    'product_id' => $validated['product_id'],
                    'source' => $validated['source']
                ]
            );

            return response()->json([
                'success' => true,
                'message' => 'Product interaction tracked successfully',
                'session_id' => $validated['session_id'],
                'daily_view_count' => $session->fresh()->daily_view_count,
                'daily_view_limit' => $session->daily_view_limit
            ]);

        } catch (\Exception $e) {
            \Log::error('Product interaction tracking failed', [
                'error' => $e->getMessage(),
                'request_data' => $request->all()
            ]);

            // Log security event
            \App\Services\AuditLogService::logSecurityEvent('product_interaction_failed', [
                'error' => $e->getMessage(),
                'session_id' => $request->input('session_id'),
                'product_id' => $request->input('product_id')
            ]);

            return response()->json([
                'error' => 'Failed to track product interaction',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Session analytics'ini güncelle
     */
    private function updateSessionAnalytics(EnhancedChatSession $session, string $action): void
    {
        try {
            // Action'a göre session metadata'sını güncelle
            $metadata = $session->metadata ?? [];
            
            if (!isset($metadata['action_counts'])) {
                $metadata['action_counts'] = [];
            }
            
            if (!isset($metadata['action_counts'][$action])) {
                $metadata['action_counts'][$action] = 0;
            }
            
            $metadata['action_counts'][$action]++;
            $metadata['last_action'] = $action;
            $metadata['last_action_time'] = now()->toISOString();
            
            $session->update(['metadata' => $metadata]);
            
        } catch (\Exception $e) {
            Log::warning('Session analytics update failed: ' . $e->getMessage());
        }
    }

    /**
     * Session Analytics API - Session detaylarını ve analytics'i getirir
     */
    public function getSessionAnalytics(Request $request, string $sessionId) {
        try {
            // Session'ı bul
            $chatSession = EnhancedChatSession::where('session_id', $sessionId)->first();
            
            if (!$chatSession) {
                return response()->json([
                    'success' => false,
                    'message' => 'Session bulunamadı'
                ], 404);
            }

            // Product interactions'ları getir
            $interactions = ProductInteraction::where('session_id', $sessionId)
                ->with('product')
                ->orderBy('timestamp', 'desc')
                ->get();

            // Intent history'yi analiz et
            $intentAnalysis = $this->analyzeIntentHistory($chatSession->intent_history ?? []);

            // Product interaction patterns'ı analiz et
            $interactionPatterns = $this->analyzeInteractionPatterns($interactions);

            // User preferences'ı analiz et
            $userPreferences = $this->analyzeUserPreferences($chatSession->user_preferences ?? []);

            // Session statistics'ini hesapla
            $sessionStats = $this->calculateSessionStats($chatSession, $interactions);

            return response()->json([
                'success' => true,
                'data' => [
                    'session' => [
                        'session_id' => $chatSession->session_id,
                        'status' => $chatSession->status,
                        'created_at' => $chatSession->created_at,
                        'last_activity' => $chatSession->last_activity,
                        'daily_view_count' => $chatSession->daily_view_count,
                        'daily_view_limit' => $chatSession->daily_view_limit,
                        'can_view_more' => $chatSession->canViewMore(),
                        'is_active' => $chatSession->isActive(),
                        'is_expired' => $chatSession->isExpired()
                    ],
                    'analytics' => [
                        'intent_analysis' => $intentAnalysis,
                        'interaction_patterns' => $interactionPatterns,
                        'user_preferences' => $userPreferences,
                        'session_stats' => $sessionStats
                    ],
                    'interactions' => $interactions->map(function($interaction) {
                        return [
                            'id' => $interaction->id,
                            'action' => $interaction->action,
                            'timestamp' => $interaction->timestamp,
                            'source' => $interaction->source,
                            'product' => $interaction->product ? [
                                'id' => $interaction->product->id,
                                'title' => $interaction->product->title ?? 'Unknown',
                                'category' => $interaction->product->category ?? 'Unknown'
                            ] : null,
                            'metadata' => $interaction->metadata
                        ];
                    })
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Session analytics error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Session analytics alınırken hata oluştu',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Intent history'yi analiz et
     */
    private function analyzeIntentHistory(array $intentHistory): array
    {
        if (empty($intentHistory)) {
            return [
                'total_intents' => 0,
                'intent_distribution' => [],
                'most_common_intent' => null,
                'confidence_avg' => 0
            ];
        }

        $intentCounts = [];
        $totalConfidence = 0;
        $intentCount = count($intentHistory);

        foreach ($intentHistory as $intent) {
            $intentName = $intent['intent'] ?? 'unknown';
            $confidence = $intent['confidence'] ?? 0;

            if (!isset($intentCounts[$intentName])) {
                $intentCounts[$intentName] = 0;
            }
            $intentCounts[$intentName]++;
            $totalConfidence += $confidence;
        }

        arsort($intentCounts);
        $mostCommonIntent = array_key_first($intentCounts);

        return [
            'total_intents' => $intentCount,
            'intent_distribution' => $intentCounts,
            'most_common_intent' => $mostCommonIntent,
            'confidence_avg' => $intentCount > 0 ? round($totalConfidence / $intentCount, 2) : 0
        ];
    }

    /**
     * Interaction patterns'ı analiz et
     */
    private function analyzeInteractionPatterns($interactions): array
    {
        if ($interactions->isEmpty()) {
            return [
                'total_interactions' => 0,
                'action_distribution' => [],
                'source_distribution' => [],
                'conversion_rate' => 0,
                'most_active_hour' => null
            ];
        }

        $actionCounts = [];
        $sourceCounts = [];
        $conversionActions = 0;
        $hourlyActivity = [];

        foreach ($interactions as $interaction) {
            // Action counts
            $action = $interaction->action;
            if (!isset($actionCounts[$action])) {
                $actionCounts[$action] = 0;
            }
            $actionCounts[$action]++;

            // Source counts
            $source = $interaction->source;
            if (!isset($sourceCounts[$source])) {
                $sourceCounts[$source] = 0;
            }
            $sourceCounts[$source]++;

            // Conversion tracking
            if (in_array($action, ['buy', 'add_to_cart'])) {
                $conversionActions++;
            }

            // Hourly activity
            $hour = $interaction->timestamp->hour;
            if (!isset($hourlyActivity[$hour])) {
                $hourlyActivity[$hour] = 0;
            }
            $hourlyActivity[$hour]++;
        }

        arsort($actionCounts);
        arsort($sourceCounts);
        arsort($hourlyActivity);

        $totalInteractions = $interactions->count();
        $conversionRate = $totalInteractions > 0 ? round(($conversionActions / $totalInteractions) * 100, 2) : 0;
        $mostActiveHour = array_key_first($hourlyActivity);

        return [
            'total_interactions' => $totalInteractions,
            'action_distribution' => $actionCounts,
            'source_distribution' => $sourceCounts,
            'conversion_rate' => $conversionRate,
            'most_active_hour' => $mostActiveHour,
            'hourly_activity' => $hourlyActivity
        ];
    }

    /**
     * User preferences'ı analiz et
     */
    private function analyzeUserPreferences(array $userPreferences): array
    {
        if (empty($userPreferences)) {
            return [
                'has_preferences' => false,
                'preference_summary' => []
            ];
        }

        return [
            'has_preferences' => true,
            'preference_summary' => $userPreferences
        ];
    }

    /**
     * Session statistics'ini hesapla
     */
    private function calculateSessionStats(EnhancedChatSession $session, $interactions): array
    {
        $totalInteractions = $interactions->count();
        $conversionInteractions = $interactions->whereIn('action', ['buy', 'add_to_cart'])->count();
        $conversionRate = $totalInteractions > 0 ? round(($conversionInteractions / $totalInteractions) * 100, 2) : 0;

        $sessionDuration = $session->created_at->diffInMinutes($session->last_activity ?? $session->created_at);

        return [
            'total_interactions' => $totalInteractions,
            'conversion_interactions' => $conversionInteractions,
            'conversion_rate' => $conversionRate,
            'session_duration_minutes' => $sessionDuration,
            'daily_view_usage' => round(($session->daily_view_count / $session->daily_view_limit) * 100, 2)
        ];
    }

    /**
     * Kargo takip işleme
     */
    public function handleCargoTracking(Request $request) {
        $cargoNumber = $request->input('cargo_number');
        
        // Kargo takip numarasını logla
        Log::info('Cargo tracking request:', ['cargo_number' => $cargoNumber]);
        
        // Simüle edilmiş kargo takip sonucu
        $cargoStatus = $this->simulateCargoTracking($cargoNumber);
        
        return response()->json([
            'success' => true,
            'message' => 'Kargo takip bilgisi alındı',
            'data' => $cargoStatus
        ]);
    }

    /**
     * Sipariş numarası ile kargo takip işleme
     */
    public function handleOrderTracking(Request $request) {
        $orderNumber = $request->input('order_number');
        
        // Sipariş numarasını logla
        Log::info('Order tracking request:', ['order_number' => $orderNumber]);
        
        // Simüle edilmiş sipariş takip sonucu - yeni format
        $trackingData = [
            'intent' => 'order_tracking',
            'phase' => 'cargo',
            'order_id' => 'ORD-998877',
            'status' => 'in_transit',
            'courier' => 'Yurtiçi Kargo',
            'tracking_number' => 'YT123456789TR',
            'last_update' => '2025-08-18T14:30:00Z',
            'estimated_delivery' => '2025-08-20'
        ];
        
        return response()->json([
            'success' => true,
            'message' => 'Sipariş takip bilgisi alındı',
            'data' => $trackingData
        ]);
    }

    /**
     * Kargo takip simülasyonu
     */
    private function simulateCargoTracking($cargoNumber) {
        // Gerçek uygulamada burada kargo firması API'si kullanılır
        $statuses = [
            'Kargo kabul edildi',
            'Transfer merkezinde',
            'Yolda',
            'Dağıtım merkezinde',
            'Kurye yola çıktı',
            'Teslim edildi'
        ];
        
        $randomStatus = $statuses[array_rand($statuses)];
        $estimatedDelivery = date('Y-m-d', strtotime('+2 days'));
        
        return [
            'cargo_number' => $cargoNumber,
            'status' => $randomStatus,
            'estimated_delivery' => $estimatedDelivery,
            'current_location' => 'İstanbul Transfer Merkezi',
            'last_update' => date('Y-m-d H:i:s'),
            'tracking_url' => "https://tracking.example.com/{$cargoNumber}"
        ];
    }

    public function testIntentSystem() {
        $testQueries = [
            'Merhaba, nasılsın?',
            'Selam!',
            'iPhone fiyatı ne kadar?',
            'Samsung telefon kaç para?',
            'Elektronik kategorisinde neler var?',
            'Giyim türleri göster',
            'Nike ayakkabıları göster',
            'Apple markası var mı?',
            'Stokta olan ürünler neler?',
            'Depoda kalan ürünler',
            'Bana öneri ver',
            'En iyi ürünler neler?',
            'iPhone vs Samsung karşılaştır',
            'Hangisi daha iyi?',
            'Yardım almak istiyorum',
            'Ne yapabilirsin?',
            'Bilmiyorum ne yapayım',
            'Güle güle',
            'Teşekkürler',
            'iPhone istiyorum',
            'Spor ayakkabı arıyorum',
            'Ev eşyası kategorisi',
            'Teknoloji ürünleri',
            'Moda kıyafetler',
            'Fitness malzemeleri'
        ];
        
        $intentSystem = new IntentDetectionService();
        $results = [];
        
        foreach ($testQueries as $query) {
            $detectedIntent = $intentSystem->detectIntent($query);
            $response = $intentSystem->generateResponse($detectedIntent, $query);
            $results[] = [
                'query' => $query,
                'detected_intent' => $detectedIntent,
                'response' => $response
            ];
        }
        
        return response()->json([
            'message' => 'Advanced Intent Detection System Test Results',
            'total_queries' => count($testQueries),
            'system_features' => [
                'thesaurus_support' => true,
                'fuzzy_matching' => true,
                'flexible_thresholds' => true,
                'context_suggestion' => true,
                'synonym_detection' => true
            ],
            'results' => $results
        ]);
    }

    public function getprompt($usermessage){
     $prompt =[
          ['role'=>'system','content'=>'Sen bir e-ticaret asistanısın'],
          ['role'=>'user','content'=>"Bana şu ürünlerden öneri yap: ".json_encode($this->productData->getAllProducts())]
     ];
     return $prompt;
    }

    /**
     * Veritabanından ürünleri getir
     */
    public function getProductsFromDB(Request $request)
    {
        $query = Product::query();

        // Filtreleme
        if ($request->has('category')) {
            $query->where('category', $request->category);
        }

        if ($request->has('brand')) {
            $query->where('brand', $request->brand);
        }

        if ($request->has('min_price') && $request->has('max_price')) {
            $query->whereBetween('price', [$request->min_price, $request->max_price]);
        }

        if ($request->has('search')) {
            $query->where(function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('brand', 'like', '%' . $request->search . '%')
                  ->orWhere('category', 'like', '%' . $request->search . '%');
            });
        }

        // Sıralama
        $sortBy = $request->get('sort_by', 'id');
        $sortOrder = $request->get('sort_order', 'asc');
        $query->orderBy($sortBy, $sortOrder);

        // Sayfalama
        $perPage = $request->get('per_page', 20);
        $products = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $products,
            'total' => $products->total(),
            'per_page' => $products->perPage(),
            'current_page' => $products->currentPage(),
            'last_page' => $products->lastPage()
        ]);
    }

    /**
     * Kategori bazında ürün istatistikleri
     */
    public function getCategoryStats()
    {
        $stats = Product::selectRaw('category, COUNT(*) as product_count, AVG(price) as avg_price, AVG(rating) as avg_rating')
            ->groupBy('category')
            ->orderBy('product_count', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $stats
        ]);
    }

    /**
     * En yüksek puanlı ürünler
     */
    public function getTopRatedProducts(Request $request)
    {
        $limit = $request->get('limit', 10);
        $products = Product::orderBy('rating', 'desc')
            ->limit($limit)
            ->get();

        return response()->json([
            'success' => true,
            'data' => $products
        ]);
    }

    public function createSession() {
        $chatSession = new ChatSession();
        return response()->json([
            'session_id' => $chatSession->getSessionId(),
            'message' => 'Yeni chat session oluşturuldu',
            'timestamp' => time()
        ]);
    }
    
    public function getSessionInfo($sessionId) {
        $chatSession = new ChatSession($sessionId);
        return response()->json([
            'session_id' => $chatSession->getSessionId(),
            'session_info' => [
                'total_messages' => $chatSession->getTotalMessages(),
                'conversation_context' => $chatSession->getConversationContext(),
                'last_intent' => $chatSession->getLastIntent(),
                'session_duration' => $chatSession->getSessionDuration(),
                'context_summary' => $chatSession->getContextSummary()
            ]
        ]);
    }
    
    public function clearSession($sessionId) {
        $chatSession = new ChatSession($sessionId);
        $chatSession->clearSession();
        return response()->json([
            'session_id' => $chatSession->getSessionId(),
            'message' => 'Session başarıyla temizlendi',
            'timestamp' => time()
        ]);
    }

    /**
     * Get all chat messages for a specific session
     */
    public function getChatSession($sessionId) {
        try {
            // Session ID'yi temizle
            $sessionId = trim($sessionId);
            
            if (empty($sessionId)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Session ID gerekli'
                ], 400);
            }

            // ChatSession sınıfını kullanarak session'ı bul
            $chatSession = new ChatSession($sessionId);
            
            // Session'ın var olup olmadığını kontrol et
            if (!$chatSession->hasSession()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Session bulunamadı: ' . $sessionId
                ], 404);
            }

            // Session'daki tüm mesajları al
            $messages = $chatSession->getAllMessages();
            
            // Session bilgilerini al
            $sessionInfo = [
                'session_id' => $sessionId,
                'created_at' => $chatSession->getCreatedAt(),
                'last_activity' => $chatSession->getLastActivity(),
                'message_count' => count($messages),
                'total_tokens' => $chatSession->getTotalTokens()
            ];

            return response()->json([
                'success' => true,
                'session_info' => $sessionInfo,
                'messages' => $messages,
                'total_messages' => count($messages)
            ]);

        } catch (\Exception $e) {
            \Log::error('getChatSession error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Session mesajları alınırken hata oluştu',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Clear all messages and context from a specific session
     */
    public function clearChatSession($sessionId) {
        try {
            // Session ID'yi temizle
            $sessionId = trim($sessionId);
            
            if (empty($sessionId)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Session ID gerekli'
                ], 400);
            }

            // ChatSession sınıfını kullanarak session'ı bul
            $chatSession = new ChatSession($sessionId);
            
            // Session'ın var olup olmadığını kontrol et
            if (!$chatSession->hasSession()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Session bulunamadı: ' . $sessionId
                ], 404);
            }

            // Session'ı temizle
            $clearResult = $chatSession->clearSession();
            
            return response()->json([
                'success' => true,
                'message' => 'Session başarıyla temizlendi',
                'session_id' => $sessionId,
                'cleared_at' => date('Y-m-d H:i:s'),
                'cleared_info' => [
                    'messages_removed' => $clearResult['messages_removed'],
                    'context_cleared' => $clearResult['context_cleared'],
                    'session_reset' => $clearResult['session_reset']
                ]
            ]);

        } catch (\Exception $e) {
            \Log::error('clearChatSession error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Session temizlenirken hata oluştu',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Get AI-generated intents
     */
    public function getAIGeneratedIntents() {
        try {
            $intentSystem = new IntentDetectionService();
            $aiIntents = $intentSystem->getAIGeneratedIntents();
            
            return response()->json([
                'success' => true,
                'ai_generated_intents' => $aiIntents,
                'total_ai_intents' => count($aiIntents),
                'timestamp' => date('Y-m-d H:i:s')
            ]);
            
        } catch (\Exception $e) {
            \Log::error('getAIGeneratedIntents error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'AI-generated intent\'ler alınırken hata oluştu',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Get intent system statistics
     */
    public function getIntentStats() {
        try {
            $intentSystem = new IntentDetectionService();
            $aiIntents = $intentSystem->getAIGeneratedIntents();
            
            // Tüm intent'leri say
            $totalIntents = count($intentSystem->getAllIntents());
            $aiGeneratedCount = count($aiIntents);
            $originalIntents = $totalIntents - $aiGeneratedCount;
            
            // Kullanım istatistikleri
            $totalUsage = 0;
            $mostUsedIntent = null;
            $maxUsage = 0;
            
            foreach ($aiIntents as $intentName => $intentData) {
                $usage = $intentData['usage_count'] ?? 0;
                $totalUsage += $usage;
                
                if ($usage > $maxUsage) {
                    $maxUsage = $usage;
                    $mostUsedIntent = $intentName;
                }
            }
            
            return response()->json([
                'success' => true,
                'statistics' => [
                    'total_intents' => $totalIntents,
                    'original_intents' => $originalIntents,
                    'ai_generated_intents' => $aiGeneratedCount,
                    'ai_generation_rate' => $aiGeneratedCount > 0 ? round(($aiGeneratedCount / $totalIntents) * 100, 2) : 0,
                    'total_ai_usage' => $totalUsage,
                    'most_used_ai_intent' => $mostUsedIntent,
                    'max_usage_count' => $maxUsage
                ],
                'ai_intents_summary' => array_map(function($intent) {
                    return [
                        'keywords_count' => count($intent['keywords']),
                        'usage_count' => $intent['usage_count'] ?? 0,
                        'created_at' => $intent['created_at']
                    ];
                }, $aiIntents),
                'timestamp' => date('Y-m-d H:i:s')
            ]);
            
        } catch (\Exception $e) {
            \Log::error('getIntentStats error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Intent istatistikleri alınırken hata oluştu',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Get all categories with analysis
     */
    public function getAllCategories() {
        try {
            $categories = $this->getCategoriesFromKnowledgeBase();
            
            return response()->json([
                'success' => true,
                'categories' => $categories,
                'total_categories' => count($categories),
                'timestamp' => date('Y-m-d H:i:s')
            ]);
            
        } catch (\Exception $e) {
            \Log::error('getAllCategories error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Kategoriler alınırken hata oluştu',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Get detailed analysis for a specific category
     */
    public function getCategoryDetails($category) {
        try {
            $categoryDetails = $this->getCategoryDetailsFromKnowledgeBase($category);
            
            if (!$categoryDetails) {
                return response()->json([
                    'success' => false,
                    'message' => 'Kategori bulunamadı: ' . $category
                ], 404);
            }
            
            return response()->json([
                'success' => true,
                'category_details' => $categoryDetails,
                'timestamp' => date('Y-m-d H:i:s')
            ]);
            
        } catch (\Exception $e) {
            \Log::error('getCategoryDetails error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Kategori detayları alınırken hata oluştu',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Get category recommendations
     */
    public function getCategoryRecommendations() {
        try {
            $recommendations = $this->getCategoryRecommendationsFromKnowledgeBase(10);
            
            return response()->json([
                'success' => true,
                'category_recommendations' => $recommendations,
                'total_recommendations' => count($recommendations),
                'timestamp' => date('Y-m-d H:i:s')
            ]);
            
        } catch (\Exception $e) {
            \Log::error('getCategoryRecommendations error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Kategori önerileri alınırken hata oluştu',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * GDPR Compliance - Export user data
     */
    public function exportUserData(Request $request, string $sessionId)
    {
        try {
            // Validate session exists
            $session = EnhancedChatSession::where('session_id', $sessionId)->first();
            
            if (!$session) {
                return response()->json([
                    'error' => 'Session not found'
                ], 404);
            }

            // Export data using GDPR service
            $exportedData = \App\Services\GDPRComplianceService::exportUserData($sessionId);

            // Log data export for audit
            \App\Services\AuditLogService::logDataAccess('user_data_export', $sessionId);

            return response()->json([
                'success' => true,
                'data' => $exportedData,
                'exported_at' => now()->toISOString()
            ]);

        } catch (\Exception $e) {
            \Log::error('GDPR data export failed', [
                'session_id' => $sessionId,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'error' => 'Failed to export user data',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * GDPR Compliance - Delete user data
     */
    public function deleteUserData(Request $request, string $sessionId)
    {
        try {
            // Validate request
            $request->validate([
                'reason' => 'required|string|max:500',
                'confirmation' => 'required|string|in:DELETE'
            ]);

            // Validate session exists
            $session = EnhancedChatSession::where('session_id', $sessionId)->first();
            
            if (!$session) {
                return response()->json([
                    'error' => 'Session not found'
                ], 404);
            }

            // Delete data using GDPR service
            $deleted = \App\Services\GDPRComplianceService::deleteUserData($sessionId);

            if ($deleted) {
                // Log deletion for audit
                \App\Services\AuditLogService::logGDPRAction('data_deletion', $sessionId, [
                    'reason' => $request->input('reason'),
                    'deleted_by' => $request->ip()
                ]);

                return response()->json([
                    'success' => true,
                    'message' => 'User data deleted successfully',
                    'deleted_at' => now()->toISOString()
                ]);
            } else {
                return response()->json([
                    'error' => 'Failed to delete user data'
                ], 500);
            }

        } catch (\Exception $e) {
            \Log::error('GDPR data deletion failed', [
                'session_id' => $sessionId,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'error' => 'Failed to delete user data',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * GDPR Compliance - Anonymize user data
     */
    public function anonymizeUserData(Request $request, string $sessionId)
    {
        try {
            // Validate request
            $request->validate([
                'reason' => 'required|string|max:500'
            ]);

            // Validate session exists
            $session = EnhancedChatSession::where('session_id', $sessionId)->first();
            
            if (!$session) {
                return response()->json([
                    'error' => 'Session not found'
                ], 404);
            }

            // Anonymize data using GDPR service
            $anonymized = \App\Services\GDPRComplianceService::anonymizeUserData($sessionId);

            if ($anonymized) {
                // Log anonymization for audit
                \App\Services\AuditLogService::logGDPRAction('data_anonymization', $sessionId, [
                    'reason' => $request->input('reason'),
                    'anonymized_by' => $request->ip()
                ]);

                return response()->json([
                    'success' => true,
                    'message' => 'User data anonymized successfully',
                    'anonymized_at' => now()->toISOString()
                ]);
            } else {
                return response()->json([
                    'error' => 'Failed to anonymize user data'
                ], 500);
            }

        } catch (\Exception $e) {
            \Log::error('GDPR data anonymization failed', [
                'session_id' => $sessionId,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'error' => 'Failed to anonymize user data',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * GDPR Compliance - Get data retention summary
     */
    public function getDataRetentionSummary()
    {
        try {
            $summary = \App\Services\GDPRComplianceService::getDataRetentionSummary();

            return response()->json([
                'success' => true,
                'data' => $summary
            ]);

        } catch (\Exception $e) {
            \Log::error('GDPR data retention summary failed', [
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'error' => 'Failed to get data retention summary',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * AI response'dan ürünleri çıkarır
     */
    private function extractProductsFromAIResponse(string $aiResponse, array $allProducts, string $searchCategory): array
    {
        try {
            // AI response'u JSON olarak parse etmeye çalış
            $decoded = json_decode($aiResponse, true);
            
            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                // JSON başarıyla parse edildi, ürün ID'lerini çıkar
                $productIds = [];
                if (isset($decoded['products'])) {
                    foreach ($decoded['products'] as $product) {
                        if (isset($product['id'])) {
                            $productIds[] = $product['id'];
                        }
                    }
                }
                
                // ID'lere göre ürünleri filtrele
                if (!empty($productIds)) {
                    return array_filter($allProducts, function($product) use ($productIds) {
                        return in_array($product['id'], $productIds);
                    });
                }
            }
            
            // JSON parse edilemediyse, AI response'da ürün adlarını ara
            $filteredProducts = [];
            foreach ($allProducts as $product) {
                if (stripos($aiResponse, $product['name']) !== false || 
                    stripos($aiResponse, $product['category']) !== false) {
                    $filteredProducts[] = $product;
                }
            }
            
            return $filteredProducts;
            
        } catch (\Exception $e) {
            Log::warning('AI response parsing failed, using fallback', ['error' => $e->getMessage()]);
            return [];
        }
    }
    
    /**
     * Fuzzy kategori araması yapar
     */
    private function fuzzyCategorySearch(array $allProducts, string $searchCategory): array
    {
        $filteredProducts = [];
        $searchCategory = strtolower($searchCategory);
        
        foreach ($allProducts as $product) {
            $productCategory = strtolower($product['category']);
            $productName = strtolower($product['name']);
            
            // Kategori eşleşmesi
            if (stripos($productCategory, $searchCategory) !== false) {
                $filteredProducts[] = $product;
                continue;
            }
            
            // Ürün adında kategori anahtar kelimeleri ara
            $categoryKeywords = $this->getCategoryKeywords($searchCategory);
            foreach ($categoryKeywords as $keyword) {
                if (stripos($productName, $keyword) !== false || 
                    stripos($productCategory, $keyword) !== false) {
                    $filteredProducts[] = $product;
                    break;
                }
            }
        }
        
        return $filteredProducts;
    }
    
    /**
     * Kategori için anahtar kelimeleri döndürür
     */
    private function getCategoryKeywords(string $category): array
    {
        $keywords = [
            'elektronik' => ['telefon', 'bilgisayar', 'tablet', 'tv', 'televizyon', 'kulaklık', 'kamera', 'monitör', 'ekran', 'laptop', 'pc', 'oyun', 'konsol'],
            'giyim' => ['elbise', 'pantolon', 'gömlek', 'ayakkabı', 'çanta', 'ceket', 'etek', 'tshirt', 'hırka', 'kazak', 'şort', 'eşofman'],
            'oyuncak' => ['oyuncak', 'oyun', 'lego', 'bebek', 'puzzle', 'yapboz', 'robot', 'arabalar'],
            'kitap' => ['kitap', 'roman', 'hikaye', 'şiir', 'dergi', 'gazete', 'ansiklopedi'],
            'kozmetik' => ['kozmetik', 'makyaj', 'cilt', 'saç', 'parfüm', 'ruj', 'fondöten', 'şampuan', 'krem'],
            'aksesuar' => ['aksesuar', 'takı', 'saat', 'güneş gözlüğü', 'kolye', 'yüzük', 'küpe'],
            'mobilya' => ['mobilya', 'koltuk', 'masa', 'sandalye', 'dolap', 'yatak', 'komodin'],
            'bahçe' => ['bahçe', 'çiçek', 'bitki', 'ağaç', 'çim', 'havuz', 'şömine'],
            'müzik' => ['müzik', 'gitar', 'piyano', 'keman', 'flüt', 'davul', 'mikrofon'],
            'film' => ['film', 'dvd', 'bluray', '4k', 'uhd', 'projeksiyon']
        ];
        
        return $keywords[$category] ?? [];
    }
    
    /**
     * Session context'ini al
     */
    private function getSessionContext($sessionId)
    {
        try {
            $session = EnhancedChatSession::where('session_id', $sessionId)->first();
            
            if (!$session) {
                return [];
            }
            
            // Intent history'den son intent'i al
            $intentHistory = $session->getIntentHistory();
            $lastIntent = !empty($intentHistory) ? end($intentHistory)['intent'] : null;
            
            // Chat history'den son ürünleri al
            $chatHistory = $session->getChatHistory();
            $lastProducts = [];
            
            // Son mesajlarda ürün bilgisi ara
            foreach (array_reverse($chatHistory) as $message) {
                if (isset($message['response_data']['products']) && !empty($message['response_data']['products'])) {
                    $lastProducts = array_slice($message['response_data']['products'], 0, 3);
                    break;
                }
                // Eğer response_data yoksa, data içinde ara
                if (isset($message['response_data']['data']['products']) && !empty($message['response_data']['data']['products'])) {
                    $lastProducts = array_slice($message['response_data']['data']['products'], 0, 3);
                    break;
                }
            }
            
            // User preferences'den kategori bilgisi al
            $userPreferences = $session->user_preferences ?? [];
            $currentCategory = $userPreferences['current_category'] ?? null;
            
            return [
                'last_intent' => $lastIntent,
                'last_products' => $lastProducts,
                'current_category' => $currentCategory,
                'conversation_flow' => $chatHistory,
                'user_preferences' => $userPreferences
            ];
        } catch (\Exception $e) {
            Log::error('Session context error: ' . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Takip sorusu olup olmadığını kontrol et
     */
    private function isFollowUpQuestion($message, $context)
    {
        $message = mb_strtolower($message, 'UTF-8');
        
        // Debug log
        Log::info('isFollowUpQuestion debug:', [
            'message' => $message,
            'context' => $context,
            'last_intent' => $context['last_intent'] ?? 'none',
            'last_products' => $context['last_products'] ?? []
        ]);
        
        // Takip sorusu anahtar kelimeleri
        $followUpKeywords = [
            'hangisini', 'hangisi', 'hangi', 'ne önerirsin', 'ne tavsiye edersin',
            'hangisini almalıyım', 'hangisini önerirsin', 'hangisini tavsiye edersin',
            'bunlardan hangisi', 'bunlar arasından', 'bunların arasından',
            'en iyisi hangisi', 'en kaliteli hangisi', 'en uygun hangisi',
            'öner', 'tavsiye et', 'öneri', 'tavsiye'
        ];
        
        // Son intent product_search veya category_browse ise ve takip sorusu varsa
        $lastIntent = $context['last_intent'] ?? '';
        $hasLastProducts = !empty($context['last_products']);
        
        if (in_array($lastIntent, ['product_search', 'category_browse', 'specific_product_recommendation', 'product_recommendation']) && $hasLastProducts) {
            foreach ($followUpKeywords as $keyword) {
                if (mb_strpos($message, $keyword) !== false) {
                    Log::info('Follow-up keyword matched:', ['keyword' => $keyword]);
                    return true;
                }
            }
        }
        
        return false;
    }
    
    /**
     * Takip sorusunu işle
     */
    private function handleFollowUpQuestion($message, $context)
    {
        $lastProducts = $context['last_products'] ?? [];
        $currentCategory = $context['current_category'] ?? '';
        
        if (empty($lastProducts)) {
            // Fallback to general recommendation
            return [
                'intent' => 'product_recommendation',
                'confidence' => 0.7,
                'threshold_met' => true
            ];
        }
        
        // Son ürünlerden birini öner
        $recommendedProduct = $this->selectBestProductFromContext($lastProducts, $message);
        
        return [
            'intent' => 'contextual_recommendation',
            'confidence' => 0.9,
            'threshold_met' => true,
            'context_data' => [
                'recommended_product' => $recommendedProduct,
                'total_products' => count($lastProducts),
                'category' => $currentCategory
            ]
        ];
    }
    
    /**
     * Context'ten en iyi ürünü seç
     */
    private function selectBestProductFromContext($products, $message)
    {
        if (empty($products)) {
            return null;
        }
        
        $message = mb_strtolower($message, 'UTF-8');
        
        // En yüksek puanlı ürünü bul
        $bestProduct = null;
        $highestRating = 0;
        
        foreach ($products as $product) {
            $rating = $product['rating'] ?? 0;
            if ($rating > $highestRating) {
                $highestRating = $rating;
                $bestProduct = $product;
            }
        }
        
        // Eğer rating yoksa ilk ürünü al
        if (!$bestProduct) {
            $bestProduct = $products[0];
        }
        
        return $bestProduct;
    }
    
    /**
     * Context-aware recommendation response oluştur
     */
    private function generateContextualRecommendationResponse($userMessage, $searchResults)
    {
        try {
            // Context'ten önerilen ürünü al
            $context = $this->getSessionContext($this->getOrCreateSessionId(request()));
            $recommendedProduct = $context['context_data']['recommended_product'] ?? null;
            $totalProducts = $context['context_data']['total_products'] ?? 0;
            $category = $context['context_data']['category'] ?? '';
            
            if (!$recommendedProduct) {
                // Fallback to general recommendation
                return $this->generateProductRecommendationResponse($userMessage, $searchResults);
            }
            
            // Context-aware response oluştur
            $response = "Size önerdiğim ürünler arasından **{$recommendedProduct['name']}**'ı öneriyorum!";
            
            if ($category) {
                $response .= " Bu ürün {$category} kategorisinde en yüksek puanlı seçeneklerden biri.";
            }
            
            if (isset($recommendedProduct['rating']) && $recommendedProduct['rating'] > 0) {
                $response .= " ⭐ {$recommendedProduct['rating']}/5 puanla müşteriler tarafından beğeniliyor.";
            }
            
            return [
                'type' => 'contextual_recommendation',
                'message' => $response,
                'products' => [$recommendedProduct],
                'data' => [
                    'products' => [$recommendedProduct],
                    'total_found' => 1,
                    'context_used' => true,
                    'category' => $category,
                    'recommendation_reason' => 'Önceki arama sonuçlarından seçildi',
                    'suggestions' => [
                        'Farklı ürün öner',
                        'Detayları göster',
                        'Benzer ürünler',
                        'Fiyat karşılaştırması'
                    ]
                ]
            ];
            
        } catch (\Exception $e) {
            Log::error('Contextual recommendation error: ' . $e->getMessage());
            
            // Fallback to general recommendation
            return $this->generateProductRecommendationResponse($userMessage, $searchResults);
        }
    }
}
