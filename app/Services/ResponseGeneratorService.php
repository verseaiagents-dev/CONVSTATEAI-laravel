<?php 

namespace App\Services;

use Illuminate\Support\Facades\Log;
use App\Models\KnowledgeChunk;
use App\Helpers\ProductImageHelper;
use App\Services\DynamicProductParserService;
class ResponseGeneratorService
{
    public static function generateResponse(string $intent, string $userMessage, array $searchResults): array
    {
        Log::info('=== RESPONSE GENERATOR DEBUG START ===');
        Log::info('Intent: ' . $intent);
        Log::info('User Message: ' . $userMessage);
        Log::info('Search Results Count: ' . count($searchResults['results'] ?? []));
        Log::info('Search Results: ' . json_encode($searchResults));
        
        $instance = new self();
        return $instance->generateAIResponse($intent, $userMessage, $searchResults);
    }
 
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
     * Ürün arama response'u - AI Destekli Dinamik Parsing
     */
    private function generateProductSearchResponse(string $userMessage, array $searchResults): array
    {
        $products = [];
        $message = '';
        
        // AI ile dinamik parsing
        $parser = app(DynamicProductParserService::class);
        $criteria = $parser->parseProductCriteria($userMessage);
        
        Log::info('Dynamic Product Parsing Result', [
            'user_message' => $userMessage,
            'parsed_criteria' => $criteria
        ]);
        
        // Kullanıcı mesajını analiz et (fallback için)
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
                    $products = array_slice($filteredProducts, 0, 6); // En fazla 6 ürün göster
                    $message = ucfirst($category) . " kategorisinde " . count($products) . " ürün buldum:";
                } else {
                    // AI eşleştirme yapamadıysa, fuzzy search yap
                    $products = $this->fuzzyCategorySearch($category, $allProducts);
                    if (!empty($products)) {
                        $products = array_slice($products, 0, 6);
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
            $allProducts = [];
            foreach (array_slice($searchResults['results'], 0, 10) as $result) {
                $product = $this->extractProductFromChunk($result);
                if ($product) {
                    $allProducts[] = $product;
                }
            }
            
            // AI parsing kriterlerine göre filtrele
            if (!empty($allProducts) && $criteria['confidence'] > 0.3) {
                $products = $parser->filterProductsByCriteria($allProducts, $criteria);
                $products = array_slice($products, 0, 6); // En iyi eşleşen 6 ürün
                
                if (!empty($products)) {
                    $message = $parser->generateSearchMessage($criteria);
                } else {
                    $message = "Arama kriterlerinize uygun ürün bulunamadı, genel öneriler:";
                    $products = array_slice($allProducts, 0, 6);
                }
            } else {
                // Fallback: Tüm ürünleri göster
                $products = array_slice($allProducts, 0, 6);
                if ($isPersonalizedRequest) {
                    $message = "Size özel olarak " . count($products) . " ürün öneriyorum:";
                } elseif ($hasSpecificProduct) {
                    $message = "Aradığınız ürünlerden " . count($products) . " tanesini buldum:";
                } else {
                    $message = "Aradığınız kriterlere uygun " . count($products) . " ürün buldum:";
                }
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
     * Fiyat sorgulama response'u - AI Destekli Dinamik Parsing
     */
    private function generatePriceInquiryResponse(string $userMessage, array $searchResults): array
    {
        Log::info('=== GENERATE PRICE INQUIRY RESPONSE ===');
        Log::info('User message: ' . $userMessage);
        Log::info('Search results count: ' . count($searchResults['results'] ?? []));
        
        $products = [];
        $message = '';
        
        // AI ile dinamik parsing
        $parser = app(DynamicProductParserService::class);
        $criteria = $parser->parsePriceCriteria($userMessage);
        
        Log::info('Dynamic Price Parsing Result', [
            'user_message' => $userMessage,
            'parsed_criteria' => $criteria
        ]);
        
        // Knowledge base'den ürünleri al
        $allProducts = $this->getProductsFromKnowledgeBase();
        
        if (!empty($allProducts)) {
            // AI parsing sonuçlarına göre filtrele
            if (!empty($criteria['product_type']) || !empty($criteria['color']) || !empty($criteria['brand'])) {
                $filteredProducts = $parser->filterProductsByCriteria($allProducts, $criteria);
                if (!empty($filteredProducts)) {
                    $products = array_slice($filteredProducts, 0, 6);
                }
            }
            
            // Eğer filtreleme sonucu ürün yoksa, tüm ürünlerden al
            if (empty($products)) {
                $products = array_slice($allProducts, 0, 6);
            }
            
            // Fiyat aralığı kontrolü
            if (!empty($criteria['price_range'])) {
                $priceRange = $criteria['price_range'];
                $filteredByPrice = [];
                
                foreach ($products as $product) {
                    $productPrice = $product['price'] ?? 0;
                    
                    switch ($priceRange['type']) {
                        case 'range':
                            if ($productPrice >= $priceRange['min'] && $productPrice <= $priceRange['max']) {
                                $filteredByPrice[] = $product;
                            }
                            break;
                        case 'under':
                            if ($productPrice < $priceRange['value']) {
                                $filteredByPrice[] = $product;
                            }
                            break;
                        case 'over':
                            if ($productPrice > $priceRange['value']) {
                                $filteredByPrice[] = $product;
                            }
                            break;
                        case 'qualitative':
                            // Kalitatif fiyat aralıkları
                            if ($priceRange['value'] === 'ucuz' && $productPrice < 500) {
                                $filteredByPrice[] = $product;
                            } elseif ($priceRange['value'] === 'pahalı' && $productPrice > 1000) {
                                $filteredByPrice[] = $product;
                            } elseif ($priceRange['value'] === 'orta' && $productPrice >= 500 && $productPrice <= 1000) {
                                $filteredByPrice[] = $product;
                            }
                            break;
                    }
                }
                
                if (!empty($filteredByPrice)) {
                    $products = array_slice($filteredByPrice, 0, 6);
                }
            }
            
            // Fiyat bilgilerini hazırla
            $priceInfo = [];
            foreach ($products as $product) {
                $priceInfo[] = $product['name'] . ': ' . number_format($product['price'], 2) . ' TL';
            }
            
            // AI parsing sonuçlarına göre mesaj oluştur
            if (!empty($criteria['product_type']) || !empty($criteria['color']) || !empty($criteria['price_range'])) {
                $message = $parser->generateSearchMessage($criteria);
                if (strpos($message, 'fiyat') === false) {
                    $message = 'Fiyat bilgileri: ' . implode(', ', $priceInfo);
                }
            } else {
                $message = 'Fiyat bilgileri: ' . implode(', ', $priceInfo);
            }
        } else {
            $message = 'Fiyat bilgisi için hangi ürünü öğrenmek istiyorsunuz?';
        }
        
        return [
            'type' => 'price_inquiry',
            'message' => $message,
            'data' => [
                'products' => $products,
                'search_query' => $userMessage,
                'total_found' => count($products),
                'search_confidence' => empty($products) ? 'low' : 'high',
                'is_personalized' => 0,
                'is_random' => empty($searchResults['results']),
                'reason' => empty($products) ? 'Ürün bulunamadı' : 'AI parsing başarılı',
                'category_matched' => false
            ],
            'search_results' => $searchResults,
            'parsed_criteria' => $criteria, // Debug için
        ];
    }

    /**
     * Ürün önerisi response'u oluşturur
     */
    private function generateProductRecommendationResponse(string $userMessage, array $searchResults): array
    {
        Log::info('=== GENERATE PRODUCT RECOMMENDATION RESPONSE ===');
        Log::info('User message: ' . $userMessage);
        Log::info('Search results count: ' . count($searchResults['results'] ?? []));
        
        $products = [];
        
        if (!empty($searchResults['results'])) {
            foreach ($searchResults['results'] as $chunk) {
                $productData = json_decode($chunk['content'], true);
                if ($productData && is_array($productData) && count($productData) > 0) {
                    $product = $productData[0];
                    // Sadece geçerli ürün verilerini ekle
                    if (isset($product['title']) && !empty($product['title'])) {
                        // Resim kontrolü yap ve fallback bilgisi ekle
                        $imageInfo = ProductImageHelper::getSafeImageUrlWithCheck($product['image'] ?? null);
                        
                        $products[] = [
                            'id' => $product['id'] ?? $chunk['id'],
                            'name' => $product['title'],
                            'category' => $product['category'] ?? 'Genel',
                            'price' => $product['price'] ?? 0,
                            'brand' => $product['brand'] ?? 'Bilinmeyen',
                            'rating' => $product['rating']['rate'] ?? 4.0,
                            'stock' => 10,
                            'image' => $imageInfo['url'],
                            'fallback_image' => '/imgs/product-placeholder.svg',
                            'image_accessible' => $imageInfo['is_accessible'],
                            'fallback_used' => $imageInfo['fallback_used'],
                            'description' => $product['description'] ?? substr($chunk['content'], 0, 200) . '...',
                            'product_url' => 'https://example.com/product/' . ($product['id'] ?? $chunk['id']) . '?intent=recommendation',
                            'source' => 'knowledge_base',
                            'relevance_score' => $chunk['relevance_score'] ?? 0
                        ];
                    }
                }
            }
        }
        
        // Eğer semantic search'ten ürün bulunamazsa, knowledge base'den rastgele ürünler al
        if (empty($products)) {
            Log::info('No products from semantic search, getting random products from knowledge base');
            $products = $this->getProductsFromKnowledgeBase();
            $products = array_slice($products, 0, 6); // İlk 6 ürünü al
        }
        
        // AI ile dinamik parsing
        $parser = app(DynamicProductParserService::class);
        $criteria = $parser->parseProductCriteria($userMessage);
        
        Log::info('Dynamic Product Parsing for Recommendation', [
            'user_message' => $userMessage,
            'parsed_criteria' => $criteria
        ]);
        
        // AI parsing sonuçlarına göre mesaj oluştur
        if (!empty($criteria['product_type']) || !empty($criteria['color']) || !empty($criteria['size'])) {
            $message = $parser->generateSearchMessage($criteria);
        } else {
            $message = empty($products) 
                ? "Üzgünüm, şu anda önerebileceğim ürün bulunmuyor. Lütfen daha sonra tekrar deneyin."
                : "İşte size önerdiğim ürünler:";
        }
        
        return [
            'type' => 'product_recommendation',
            'message' => $message,
            'products' => $products,
            'data' => [
                'products' => $products,
                'search_query' => $userMessage,
                'total_found' => count($products),
                'search_confidence' => empty($products) ? 'low' : 'high',
                'is_personalized' => 0,
                'is_random' => empty($searchResults['results']),
                'reason' => empty($products) ? 'Ürün bulunamadı' : 'Semantic search başarılı',
                'category_matched' => false
            ],
            'search_results' => $searchResults,
            'parsed_criteria' => $criteria, // Debug için
        ];
    }

    /**
     * Contextual recommendation response'u oluşturur
     */
    private function generateContextualRecommendationResponse(string $userMessage, array $searchResults): array
    {
        return $this->generateProductRecommendationResponse($userMessage, $searchResults);
    }

    /**
     * Kategori response'u oluşturur
     */
    private function generateCategoryResponse(string $userMessage, array $searchResults): array
    {
        return $this->generateProductRecommendationResponse($userMessage, $searchResults);
    }

    /**
     * Marka response'u oluşturur
     */
    private function generateBrandResponse(string $userMessage, array $searchResults): array
    {
        return $this->generateProductRecommendationResponse($userMessage, $searchResults);
    }

      /**
     * Knowledge base'den ürün verilerini çeker
     */
    public function getProductsFromKnowledgeBase(?string $category = null): array
    {
        try {
            // Tüm chunk'ları al, content_type kontrolü yapma
            $query = KnowledgeChunk::query();
            
            if ($category) {
                $query->where('content', 'like', '%' . $category . '%');
            }
            
            $chunks = $query->with('knowledgeBase')->get();
            
            $products = [];
            foreach ($chunks as $chunk) {
                // Chunk content'ini JSON olarak parse et
                $productData = json_decode($chunk->content, true);

                if (!$productData || !is_array($productData)) {
                    continue; // JSON parse edilemezse skip et
                }

                // Eğer tek bir ürün array'i ise, ilk elemanı al
                if (count($productData) > 0 && is_array($productData[0])) {
                    $product = $productData[0];
                } else {
                    $product = $productData;
                }
                
                // Sadece geçerli ürün verilerini ekle
                if (isset($product['title']) && !empty($product['title'])) {
                    // Resim kontrolü yap ve fallback bilgisi ekle
                    $imageInfo = ProductImageHelper::getSafeImageUrlWithCheck($product['image'] ?? null);
                    
                    $products[] = [
                        'id' => $product['id'] ?? $chunk->id,
                        'name' => $product['title'],
                        'category' => $product['category'] ?? 'Genel',
                        'price' => $product['price'] ?? 0,
                        'brand' => $product['brand'] ?? 'Bilinmeyen',
                        'rating' => $product['rating']['rate'] ?? 4.0,
                        'stock' => 10, // Default stock
                        'image' => $imageInfo['url'],
                        'fallback_image' => '/imgs/product-placeholder.svg',
                        'image_accessible' => $imageInfo['is_accessible'],
                        'fallback_used' => $imageInfo['fallback_used'],
                        'description' => $product['description'] ?? substr($chunk->content, 0, 200) . '...',
                        'product_url' => 'https://example.com/product/' . ($product['id'] ?? $chunk->id) . '?intent=recommendation',
                        'source' => 'knowledge_base'
                    ];
                }
            }
            
            return $products;
            
        } catch (\Exception $e) {
            Log::error('Knowledge base product fetch error: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Chunk'tan ürün bilgilerini çıkarır
     */
    private function extractProductFromChunk(array $chunk): ?array
    {
        try {
            $productData = json_decode($chunk['content'], true);
            if (!$productData || !is_array($productData)) {
                return null;
            }

            // Eğer array içinde array varsa, ilk elemanı al
            if (count($productData) > 0 && is_array($productData[0])) {
                $product = $productData[0];
            } else {
                $product = $productData;
            }

            // Geçerli ürün verisi kontrolü
            if (!isset($product['title']) || empty($product['title'])) {
                return null;
            }

            // Resim kontrolü yap ve fallback bilgisi ekle
            $imageInfo = ProductImageHelper::getSafeImageUrlWithCheck($product['image'] ?? null);
            
            return [
                'id' => $product['id'] ?? $chunk['id'],
                'name' => $product['title'],
                'category' => $product['category'] ?? 'Genel',
                'price' => $product['price'] ?? 0,
                'brand' => $product['brand'] ?? 'Bilinmeyen',
                'rating' => $product['rating']['rate'] ?? 4.0,
                'stock' => 10,
                'image' => $imageInfo['url'],
                'fallback_image' => '/imgs/product-placeholder.svg',
                'image_accessible' => $imageInfo['is_accessible'],
                'fallback_used' => $imageInfo['fallback_used'],
                'description' => $product['description'] ?? substr($chunk['content'], 0, 200) . '...',
                'product_url' => 'https://example.com/product/' . ($product['id'] ?? $chunk['id']) . '?intent=search',
                'source' => 'knowledge_base',
                'relevance_score' => $chunk['relevance_score'] ?? 0
            ];

        } catch (\Exception $e) {
            Log::warning('Product extraction failed', [
                'chunk_id' => $chunk['id'] ?? 'unknown',
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }

    /**
     * Genel response - AI Destekli Dinamik Parsing
     */
    private function generateGeneralResponse(string $userMessage, array $searchResults): array
    {
        Log::info('=== GENERATE GENERAL RESPONSE ===');
        Log::info('User message: ' . $userMessage);
        Log::info('Search results count: ' . count($searchResults['results'] ?? []));
        
        $message = 'Anlıyorum. Size daha iyi yardımcı olabilmem için biraz daha detay verebilir misiniz?';
        $products = [];
        
        // AI ile dinamik parsing
        $parser = app(DynamicProductParserService::class);
        $criteria = $parser->parseGeneralCriteria($userMessage);
        
        Log::info('Dynamic General Parsing Result', [
            'user_message' => $userMessage,
            'parsed_criteria' => $criteria
        ]);
        
        // Eğer kullanıcı ürün ile ilgili bir şey soruyorsa, knowledge base'den ürünler getir
        if (preg_match('/(ürün|product|fiyat|price|satın|buy|öner|recommend|tavsiye|suggest)/i', $userMessage)) {
            $allProducts = $this->getProductsFromKnowledgeBase();
            
            // AI parsing sonuçlarına göre filtrele
            if (!empty($criteria['product_type']) || !empty($criteria['color']) || !empty($criteria['brand'])) {
                $filteredProducts = $parser->filterProductsByCriteria($allProducts, $criteria);
                if (!empty($filteredProducts)) {
                    $products = array_slice($filteredProducts, 0, 6);
                }
            }
            
            // Eğer filtreleme sonucu ürün yoksa, tüm ürünlerden al
            if (empty($products)) {
                $products = array_slice($allProducts, 0, 6);
            }
            
            if (!empty($products)) {
                // AI parsing sonuçlarına göre mesaj oluştur
                if (!empty($criteria['product_type']) || !empty($criteria['color']) || !empty($criteria['brand'])) {
                    $message = $parser->generateSearchMessage($criteria);
                } else {
                    $message = 'Size özel olarak ' . count($products) . ' ürün öneriyorum:';
                }
            }
        }
        
        // Eğer semantic search'ten sonuç varsa
        if (!empty($searchResults['results'])) {
            $message = 'Aradığınız konuyla ilgili bazı sonuçlar buldum. Daha spesifik bir arama yapabilir misiniz?';
        }
        
        return [
            'type' => 'general',
            'message' => $message,
            'data' => [
                'products' => $products,
                'search_query' => $userMessage,
                'total_found' => count($products),
                'search_confidence' => empty($products) ? 'low' : 'high',
                'is_personalized' => 0,
                'is_random' => empty($searchResults['results']),
                'reason' => empty($products) ? 'Ürün bulunamadı' : 'AI parsing başarılı',
                'category_matched' => false
            ],
            'search_results' => $searchResults,
            'parsed_criteria' => $criteria, // Debug için
        ];
    }

    /**
     * FAQ response'u oluşturur - AI Destekli Dinamik Parsing
     */
    private function generateFAQResponse(string $userMessage, array $searchResults): array
    {
        Log::info('=== GENERATE FAQ RESPONSE ===');
        Log::info('User message: ' . $userMessage);
        Log::info('Search results count: ' . count($searchResults['results'] ?? []));
        
        $message = 'Size nasıl yardımcı olabilirim?';
        $faqData = [];
        
        // AI ile dinamik parsing
        $parser = app(DynamicProductParserService::class);
        $criteria = $parser->parseFAQCriteria($userMessage);
        
        Log::info('Dynamic FAQ Parsing Result', [
            'user_message' => $userMessage,
            'parsed_criteria' => $criteria
        ]);
        
        // FAQ kategorilerine göre yanıtlar
        $faqResponses = [
            'kargo' => [
                'kargo_süresi' => 'Kargo süremiz 1-3 iş günüdür. Hızlı teslimat için express kargo seçeneğimiz de mevcuttur.',
                'teslimat' => 'Tüm Türkiye\'ye ücretsiz kargo! 150 TL ve üzeri siparişlerde kargo ücretsizdir.',
                'kargo_takip' => 'Kargo takip numaranızı SMS ile gönderiyoruz. Ayrıca hesabınızdan da takip edebilirsiniz.',
                'kargo_ücreti' => '150 TL altındaki siparişlerde kargo ücreti 15 TL\'dir. 150 TL ve üzeri siparişlerde kargo ücretsizdir.'
            ],
            'ödeme' => [
                'ödeme_yöntemleri' => 'Kredi kartı, banka kartı, havale/EFT ve kapıda ödeme seçeneklerimiz mevcuttur.',
                'taksit' => 'Tüm kredi kartlarıyla 2-12 taksit arası seçeneklerimiz bulunmaktadır.',
                'kredi_kartı' => 'Tüm kredi kartları kabul edilir. Güvenli ödeme için SSL sertifikası kullanıyoruz.',
                'havale' => 'Havale/EFT ile ödeme yapmak isterseniz, hesap bilgilerimizi size gönderebiliriz.'
            ],
            'iade' => [
                'iade_politikası' => '14 gün içinde koşulsuz iade hakkınız vardır. Ürün orijinal ambalajında olmalıdır.',
                'değişim' => 'Beden değişimi için 14 gün içinde başvurabilirsiniz. Kargo ücreti tarafımızdan karşılanır.',
                'iade_süresi' => 'İade işlemi 3-5 iş günü içinde tamamlanır. Para iadesi 1-2 iş günü içinde hesabınıza yansır.'
            ],
            'ürün' => [
                'ürün_detayları' => 'Ürün detaylarını sayfamızda bulabilirsiniz. Ek bilgi için müşteri hizmetlerimizle iletişime geçin.',
                'özellikler' => 'Ürün özelliklerini detaylı olarak inceleyebilirsiniz. Teknik destek için bize ulaşın.',
                'garanti' => 'Tüm ürünlerimiz 2 yıl garantilidir. Garanti kapsamında ücretsiz onarım yapılır.'
            ],
            'hesap' => [
                'üyelik' => 'Ücretsiz üyelik oluşturabilirsiniz. Üyelik avantajlarından yararlanın.',
                'giriş' => 'E-posta adresiniz ve şifrenizle giriş yapabilirsiniz. Şifremi unuttum seçeneğini kullanabilirsiniz.',
                'şifre' => 'Şifre sıfırlama için e-posta adresinize link gönderiyoruz.',
                'profil' => 'Profil bilgilerinizi istediğiniz zaman güncelleyebilirsiniz.'
            ],
            'destek' => [
                'teknik_sorun' => 'Teknik sorunlarınız için 7/24 destek hattımızı arayabilirsiniz.',
                'yardım' => 'Size nasıl yardımcı olabiliriz? Detaylı bilgi için müşteri hizmetlerimizle iletişime geçin.',
                'iletişim' => 'WhatsApp, telefon ve e-posta ile bize ulaşabilirsiniz. Yanıt süremiz 1 saattir.'
            ],
            'sipariş' => [
                'sipariş_durumu' => 'Sipariş durumunuzu hesabınızdan takip edebilirsiniz. SMS ile de bilgilendiriliyorsunuz.',
                'sipariş_iptali' => 'Kargo çıkmadan önce siparişinizi iptal edebilirsiniz. İptal işlemi anında gerçekleşir.',
                'sipariş_değişikliği' => 'Sipariş değişikliği için müşteri hizmetlerimizle iletişime geçin.'
            ]
        ];
        
        // Kategori ve alt kategoriye göre yanıt seç
        $category = $criteria['category'] ?? 'genel';
        $questionType = $criteria['question_type'] ?? 'genel_soru';
        
        if (isset($faqResponses[$category][$questionType])) {
            $message = $faqResponses[$category][$questionType];
        } elseif (isset($faqResponses[$category])) {
            $message = $faqResponses[$category]['genel_soru'] ?? 'Bu konuda size yardımcı olabilirim. Daha detaylı bilgi için müşteri hizmetlerimizle iletişime geçin.';
        }
        
        // FAQ verilerini hazırla
        $faqData = [
            'category' => $category,
            'question_type' => $questionType,
            'urgency' => $criteria['urgency'] ?? 'normal',
            'related_questions' => $this->getRelatedFAQQuestions($category)
        ];
        
        return [
            'type' => 'faq_response',
            'message' => $message,
            'data' => [
                'faq_data' => $faqData,
                'search_query' => $userMessage,
                'confidence' => $criteria['confidence'] ?? 0.5,
                'is_personalized' => 0,
                'reason' => 'FAQ AI parsing başarılı',
                'category_matched' => $category !== 'genel'
            ],
            'search_results' => $searchResults,
            'parsed_criteria' => $criteria, // Debug için
        ];
    }

    /**
     * Sepet işlemleri response'u oluşturur - AI Destekli Dinamik Parsing
     */
    private function generateCartAddResponse(string $userMessage, array $searchResults): array
    {
        Log::info('=== GENERATE CART ADD RESPONSE ===');
        Log::info('User message: ' . $userMessage);
        Log::info('Search results count: ' . count($searchResults['results'] ?? []));
        
        $message = 'Ürün sepete eklenmiştir.';
        $products = [];
        $cartData = [];
        
        // AI ile dinamik parsing
        $parser = app(DynamicProductParserService::class);
        $criteria = $parser->parseCartCriteria($userMessage);
        
        Log::info('Dynamic Cart Parsing Result', [
            'user_message' => $userMessage,
            'parsed_criteria' => $criteria
        ]);
        
        // Knowledge base'den ürünleri al
        $allProducts = $this->getProductsFromKnowledgeBase();
        
        if (!empty($allProducts)) {
            // AI parsing sonuçlarına göre filtrele
            if (!empty($criteria['product_type']) || !empty($criteria['color']) || !empty($criteria['brand'])) {
                $filteredProducts = $parser->filterProductsByCriteria($allProducts, $criteria);
                if (!empty($filteredProducts)) {
                    $products = array_slice($filteredProducts, 0, 6);
                }
            }
            
            // Eğer filtreleme sonucu ürün yoksa, tüm ürünlerden al
            if (empty($products)) {
                $products = array_slice($allProducts, 0, 6);
            }
        }
        
        // Sepet işlem türüne göre mesaj oluştur
        $action = $criteria['action'] ?? 'add';
        $quantity = $criteria['quantity'] ?? 1;
        
        switch ($action) {
            case 'add':
                if (!empty($products)) {
                    $productName = $products[0]['name'] ?? 'Ürün';
                    $message = "{$quantity} adet {$productName} sepete eklendi.";
                } else {
                    $message = 'Ürün sepete eklendi.';
                }
                break;
            case 'remove':
                $message = 'Ürün sepetten çıkarıldı.';
                break;
            case 'update':
                $message = 'Sepet güncellendi.';
                break;
            case 'clear':
                $message = 'Sepet temizlendi.';
                break;
        }
        
        // Sepet verilerini hazırla
        $cartData = [
            'action' => $action,
            'quantity' => $quantity,
            'products' => $products,
            'total_items' => count($products),
            'estimated_total' => array_sum(array_column($products, 'price'))
        ];
        
        return [
            'type' => 'cart_response',
            'message' => $message,
            'data' => [
                'cart_data' => $cartData,
                'search_query' => $userMessage,
                'confidence' => $criteria['confidence'] ?? 0.5,
                'is_personalized' => 0,
                'reason' => 'Cart AI parsing başarılı',
                'category_matched' => false
            ],
            'search_results' => $searchResults,
            'parsed_criteria' => $criteria, // Debug için
        ];
    }

    /**
     * İlgili FAQ sorularını getirir
     */
    private function getRelatedFAQQuestions(string $category): array
    {
        $relatedQuestions = [
            'kargo' => [
                'Kargo süresi ne kadar?',
                'Kargo ücreti ne kadar?',
                'Kargo takip nasıl yapılır?'
            ],
            'ödeme' => [
                'Hangi ödeme yöntemleri kabul ediliyor?',
                'Taksit seçenekleri neler?',
                'Güvenli ödeme nasıl sağlanıyor?'
            ],
            'iade' => [
                'İade politikası nedir?',
                'İade süresi ne kadar?',
                'Değişim nasıl yapılır?'
            ],
            'ürün' => [
                'Ürün detaylarını nereden görebilirim?',
                'Garanti süresi ne kadar?',
                'Teknik özellikler neler?'
            ],
            'hesap' => [
                'Üyelik nasıl oluşturulur?',
                'Şifremi nasıl sıfırlarım?',
                'Profil bilgilerimi nasıl güncellerim?'
            ],
            'destek' => [
                'Nasıl destek alabilirim?',
                'İletişim bilgileri neler?',
                'Teknik sorun yaşıyorum'
            ]
        ];
        
        return $relatedQuestions[$category] ?? [];
    }

    /**
     * Sipariş takip response'u oluşturur
     */
    private function generateOrderTrackingResponse(): array
    {
        return [
            'type' => 'order_tracking',
            'message' => 'Sipariş takip numaranızı girin.',
            'data' => [
                'requires_input' => true,
                'input_type' => 'order_number',
                'placeholder' => 'Sipariş numarası girin...',
                'button_text' => 'Sipariş Takip Et'
            ]
        ];
    }

    /**
     * Kargo takip response'u oluşturur
     */
    private function generateCargoTrackingResponse(): array
    {
        return [
            'type' => 'cargo_tracking',
            'message' => 'Kargo takip numaranızı girin.',
            'data' => [
                'requires_input' => true,
                'input_type' => 'cargo_number',
                'placeholder' => 'Kargo takip numarası girin...',
                'button_text' => 'Kargo Takip Et'
            ]
        ];
    }

    /**
     * Selamlama response'u oluşturur
     */
    private function generateGreetingResponse(): array
    {
        return [
            'type' => 'greeting',
            'message' => 'Merhaba! Size nasıl yardımcı olabilirim?',
            'data' => [
                'greeting_type' => 'welcome',
                'suggestions' => [
                    'Ürün arayabilirsiniz',
                    'Fiyat sorgulayabilirsiniz',
                    'Ürün önerisi alabilirsiniz',
                    'Sipariş takip edebilirsiniz'
                ]
            ]
        ];
    }

    /**
     * Yardım response'u oluşturur
     */
    private function generateHelpResponse(): array
    {
        return [
            'type' => 'help',
            'message' => 'Size nasıl yardımcı olabilirim?',
            'data' => [
                'help_categories' => [
                    'Ürün Arama' => 'Ürün arayabilir, fiyat sorgulayabilirsiniz',
                    'Sipariş' => 'Sipariş takip edebilir, iptal edebilirsiniz',
                    'Kargo' => 'Kargo durumunu sorgulayabilirsiniz',
                    'İade' => 'İade ve değişim işlemleri yapabilirsiniz',
                    'Hesap' => 'Hesap bilgilerinizi yönetebilirsiniz'
                ],
                'contact_info' => [
                    'telefon' => '0850 123 45 67',
                    'email' => 'destek@example.com',
                    'whatsapp' => '0850 123 45 67'
                ]
            ]
        ];
    }

    /**
     * Knowledge base'den rastgele ürünler getirir
     */
    private function getRandomProductsFromKnowledgeBase(int $limit = 3): array
    {
        $allProducts = $this->getProductsFromKnowledgeBase();
        return array_slice($allProducts, 0, $limit);
    }

    /**
     * Bulanık kategori araması yapar
     */
    private function fuzzyCategorySearch(string $category, array $products): array
    {
        $categoryMapping = [
            'elektronik' => ['electronics', 'telefon', 'bilgisayar'],
            'giyim' => ['men\'s clothing', 'women\'s clothing', 'ayakkabı', 'elbise'],
            'spor' => ['ayakkabı'],
            'teknoloji' => ['electronics', 'telefon', 'bilgisayar'],
            'mücevher' => ['jewelery'],
            'aksesuar' => ['jewelery']
        ];

        $searchCategories = $categoryMapping[$category] ?? [$category];
        $filteredProducts = [];

        foreach ($products as $product) {
            $productCategory = strtolower($product['category'] ?? '');
            foreach ($searchCategories as $searchCategory) {
                if (strpos($productCategory, strtolower($searchCategory)) !== false) {
                    $filteredProducts[] = $product;
                    break;
                }
            }
        }

        return $filteredProducts;
    }

}