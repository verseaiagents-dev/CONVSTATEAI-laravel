<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

/**
 * Product Detail Template Service
 * 
 * Bu service, ürün detayları için dinamik template yönetimi sağlar.
 * Kategori bazlı AI promptları ve statik template'ler içerir.
 * 
 * Kullanım:
 * $service = new ProductDetailTemplateService();
 * $details = $service->generateProductDetails($productData);
 */
class ProductDetailTemplateService
{
    /**
     * Template config dosyasından yüklenir
     */
    protected $templates;
    
    /**
     * AI Service instance
     */
    protected $aiService;
    
    /**
     * Cache süresi (saniye)
     */
    protected $cacheTime = 86400; // 24 saat
    
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->templates = config('product_detail_templates', []);
        // AI Service'i lazy loading ile al (sadece gerektiğinde yükle)
        // Bu sayede service bulunamasa bile diğer metodlar çalışabilir
    }
    
    /**
     * Ana method - Ürün detayları oluştur
     * 
     * @param array $productData ['name', 'description', 'price', 'category', 'brand', etc.]
     * @return array
     */
    public function generateProductDetails(array $productData): array
    {
        try {
            // 1. Kategori tespit et
            $category = $this->detectCategory($productData);
            
            // 2. Cache kontrolü
            $cacheKey = $this->getCacheKey($productData, $category);
            if ($cached = Cache::get($cacheKey)) {
                Log::info('Product details loaded from cache', ['category' => $category]);
                return $cached;
            }
            
            // 3. Template var mı kontrol et
            if ($this->hasTemplate($category)) {
                $details = $this->generateFromTemplate($category, $productData);
            } else {
                // 4. AI ile oluştur
                $details = $this->generateWithAI($category, $productData);
            }
            
            // 5. Cache'e kaydet
            Cache::put($cacheKey, $details, $this->cacheTime);
            
            return $details;
            
        } catch (\Exception $e) {
            Log::error('Product detail generation failed', [
                'error' => $e->getMessage(),
                'product' => $productData
            ]);
            
            return $this->getFallbackDetails($productData);
        }
    }
    
    /**
     * Kategori tespiti - Akıllı algoritma (UPDATED)
     * 
     * ÖNCELİK SIRASI:
     * 1. Önce category field'ına bak (en güvenilir)
     * 2. Sonra name field'ına bak
     * 3. Description ve brand'e bak
     * 4. Varsayılan olarak 'genel' döndür
     * 
     * @param array $productData
     * @return string
     */
    protected function detectCategory(array $productData): string
    {
        $name = strtolower($productData['name'] ?? '');
        $category = strtolower($productData['category'] ?? '');
        $description = strtolower($productData['description'] ?? '');
        $brand = strtolower($productData['brand'] ?? '');
        
        // 1. ÖNCELİK: Category field'ına bak (en güvenilir kaynak)
        if (!empty($category)) {
            foreach ($this->templates as $templateKey => $template) {
                if (isset($template['keywords'])) {
                    foreach ($template['keywords'] as $keyword) {
                        $keywordLower = strtolower($keyword);
                        if (preg_match('/\b' . preg_quote($keywordLower, '/') . '\b/u', $category)) {
                            Log::info('Category detected from category field', [
                                'detected' => $templateKey,
                                'keyword' => $keyword,
                                'category_value' => $category,
                                'product' => $productData['name'] ?? 'unknown'
                            ]);
                            return $templateKey;
                        }
                    }
                }
            }
        }
        
        // 2. İKİNCİL: Name field'ına bak
        if (!empty($name)) {
            foreach ($this->templates as $templateKey => $template) {
                if (isset($template['keywords'])) {
                    foreach ($template['keywords'] as $keyword) {
                        $keywordLower = strtolower($keyword);
                        if (preg_match('/\b' . preg_quote($keywordLower, '/') . '\b/u', $name)) {
                            Log::info('Category detected from name field', [
                                'detected' => $templateKey,
                                'keyword' => $keyword,
                                'name_value' => $name,
                                'product' => $productData['name'] ?? 'unknown'
                            ]);
                            return $templateKey;
                        }
                    }
                }
            }
        }
        
        // 3. ÜÇÜNCÜL: Description ve brand'e bak
        $text = implode(' ', [$description, $brand]);
        if (!empty($text)) {
            foreach ($this->templates as $templateKey => $template) {
                if (isset($template['keywords'])) {
                    foreach ($template['keywords'] as $keyword) {
                        $keywordLower = strtolower($keyword);
                        if (preg_match('/\b' . preg_quote($keywordLower, '/') . '\b/u', $text)) {
                            Log::info('Category detected from description/brand', [
                                'detected' => $templateKey,
                                'keyword' => $keyword,
                                'product' => $productData['name'] ?? 'unknown'
                            ]);
                            return $templateKey;
                        }
                    }
                }
            }
        }
        
        // Varsayılan kategori
        Log::warning('No category detected, using default', [
            'product_data' => $productData
        ]);
        return 'genel';
    }
    
    /**
     * Template var mı kontrol et
     * 
     * @param string $category
     * @return bool
     */
    protected function hasTemplate(string $category): bool
    {
        return isset($this->templates[$category]);
    }
    
    /**
     * Template'den detay oluştur (UPDATED)
     * 
     * OPTİMİZASYON: Önce statik template'i kullan, AI'yi fallback olarak kullan
     * Bu sayede cache hit rate artar ve AI maliyeti azalır
     * 
     * @param string $category
     * @param array $productData
     * @return array
     */
    protected function generateFromTemplate(string $category, array $productData): array
    {
        $template = $this->templates[$category];
        
        // ÖNCELİK: Statik template'i kullan (maliyet yok, hızlı)
        // AI sadece template yoksa veya özel durumlarda kullanılır
        if (isset($template['ai_description']) && !empty($template['ai_description'])) {
            Log::info('Using static template', ['category' => $category]);
            return $this->fillTemplate($template, $productData);
        }
        
        // FALLBACK: Template yoksa AI kullan
        if (isset($template['use_ai']) && $template['use_ai'] === true) {
            Log::info('Using AI generation (no static template)', ['category' => $category]);
            return $this->generateWithAI($category, $productData);
        }
        
        // SON ÇARE: Fallback detaylar
        return $this->getFallbackDetails($productData);
    }
    
    /**
     * Template'i ürün verileri ile doldur
     * 
     * @param array $template
     * @param array $productData
     * @return array
     */
    protected function fillTemplate(array $template, array $productData): array
    {
        $result = [
            'ai_description' => $this->replacePlaceholders(
                $template['ai_description'] ?? '',
                $productData
            ),
            'features' => $template['features'] ?? [],
            'usage_scenarios' => $template['usage_scenarios'] ?? [],
            'specifications' => $template['specifications'] ?? [],
            'recommendations' => $template['recommendations'] ?? [],
            'additional_info' => $this->replacePlaceholders(
                $template['additional_info'] ?? '',
                $productData
            )
        ];
        
        // Pros/cons varsa ekle
        if (isset($template['pros_cons'])) {
            $result['pros_cons'] = $template['pros_cons'];
        }
        
        // Care instructions varsa ekle
        if (isset($template['care_instructions'])) {
            $result['care_instructions'] = $template['care_instructions'];
        }
        
        // Target audience varsa ekle
        if (isset($template['target_audience'])) {
            $result['target_audience'] = $template['target_audience'];
        }
        
        // Warranty info varsa ekle
        if (isset($template['warranty_info'])) {
            $result['warranty_info'] = $template['warranty_info'];
        }
        
        return $result;
    }
    
    /**
     * AI ile detay oluştur
     * 
     * @param string $category
     * @param array $productData
     * @return array
     */
    protected function generateWithAI(string $category, array $productData): array
    {
        try {
            // AI Service'i lazy loading ile yükle
            if (!$this->aiService) {
                $this->aiService = app(\App\Services\KnowledgeBase\AIService::class);
            }
            
            $prompt = $this->buildAIPrompt($category, $productData);
            
            Log::info('Generating product details with AI', [
                'category' => $category,
                'product' => $productData['name']
            ]);
            
            $aiResponse = $this->aiService->generateResponse($prompt, [
                'max_tokens' => 800,
                'temperature' => 0.7
            ]);
            
            // AI response'unu parse et
            return $this->parseAIResponse($aiResponse, $productData);
            
        } catch (\Exception $e) {
            Log::error('AI generation failed, using fallback', [
                'error' => $e->getMessage(),
                'category' => $category
            ]);
            
            return $this->getFallbackDetails($productData);
        }
    }
    
    /**
     * AI prompt oluştur
     * 
     * @param string $category
     * @param array $productData
     * @return string
     */
    protected function buildAIPrompt(string $category, array $productData): string
    {
        $name = $productData['name'] ?? 'Ürün';
        $description = $productData['description'] ?? '';
        $price = $productData['price'] ?? 0;
        $brand = $productData['brand'] ?? '';
        
        // Kategori özel prompt varsa kullan
        if (isset($this->templates[$category]['ai_prompt_template'])) {
            $promptTemplate = $this->templates[$category]['ai_prompt_template'];
            return $this->replacePlaceholders($promptTemplate, $productData);
        }
        
        // Genel AI prompt
        return "Ürün: {$name}
Marka: {$brand}
Kategori: {$category}
Fiyat: {$price} TL
Açıklama: {$description}

Lütfen bu ürün için detaylı bir analiz yap ve aşağıdaki formatta JSON olarak döndür:

{
    \"ai_description\": \"2-3 cümlelik profesyonel ürün açıklaması (ürünün öne çıkan özelliklerini vurgula)\",
    \"features\": [
        \"Özellik 1 (somut ve açıklayıcı)\",
        \"Özellik 2\",
        \"Özellik 3\",
        \"Özellik 4\"
    ],
    \"usage_scenarios\": [
        \"Kullanım senaryosu 1\",
        \"Kullanım senaryosu 2\",
        \"Kullanım senaryosu 3\"
    ],
    \"pros_cons\": {
        \"pros\": [\"Artı 1\", \"Artı 2\", \"Artı 3\"],
        \"cons\": [\"Eksi 1\", \"Eksi 2\"]
    },
    \"recommendations\": [
        \"Öneri 1\",
        \"Öneri 2\"
    ]
}

Sadece JSON formatında cevap ver, başka açıklama ekleme.";
    }
    
    /**
     * AI response'unu parse et
     * 
     * @param string $aiResponse
     * @param array $productData
     * @return array
     */
    protected function parseAIResponse(string $aiResponse, array $productData): array
    {
        try {
            // JSON'u bul ve parse et
            $jsonStart = strpos($aiResponse, '{');
            $jsonEnd = strrpos($aiResponse, '}');
            
            if ($jsonStart !== false && $jsonEnd !== false) {
                $jsonString = substr($aiResponse, $jsonStart, $jsonEnd - $jsonStart + 1);
                $parsed = json_decode($jsonString, true);
                
                if (json_last_error() === JSON_ERROR_NONE && is_array($parsed)) {
                    // Tüm alanları döndür
                    $result = [
                        'ai_description' => $parsed['ai_description'] ?? '',
                        'features' => $parsed['features'] ?? [],
                        'usage_scenarios' => $parsed['usage_scenarios'] ?? [],
                        'specifications' => $parsed['specifications'] ?? [],
                        'recommendations' => $parsed['recommendations'] ?? [],
                        'additional_info' => $parsed['additional_info'] ?? ''
                    ];
                    
                    // Opsiyonel alanlar
                    if (isset($parsed['pros_cons'])) {
                        $result['pros_cons'] = $parsed['pros_cons'];
                    }
                    
                    if (isset($parsed['care_instructions'])) {
                        $result['care_instructions'] = $parsed['care_instructions'];
                    }
                    
                    if (isset($parsed['target_audience'])) {
                        $result['target_audience'] = $parsed['target_audience'];
                    }
                    
                    if (isset($parsed['warranty_info'])) {
                        $result['warranty_info'] = $parsed['warranty_info'];
                    }
                    
                    return $result;
                }
            }
            
            throw new \Exception('Invalid JSON response from AI');
            
        } catch (\Exception $e) {
            Log::warning('Failed to parse AI response', [
                'error' => $e->getMessage(),
                'response' => substr($aiResponse, 0, 500)
            ]);
            
            return $this->getFallbackDetails($productData);
        }
    }
    
    /**
     * Placeholder'ları değiştir
     * 
     * @param string $text
     * @param array $data
     * @return string
     */
    protected function replacePlaceholders(string $text, array $data): string
    {
        $replacements = [
            '{name}' => $data['name'] ?? 'ürün',
            '{brand}' => $data['brand'] ?? 'marka',
            '{price}' => $data['price'] ?? '0',
            '{category}' => $data['category'] ?? 'kategori',
            '{description}' => $data['description'] ?? ''
        ];
        
        return str_replace(array_keys($replacements), array_values($replacements), $text);
    }
    
    /**
     * Cache key oluştur
     * 
     * UPDATED: Kategori bazlı cache (daha verimli, aynı kategorideki ürünler aynı template'i kullanır)
     * 
     * @param array $productData
     * @param string $category
     * @return string
     */
    protected function getCacheKey(array $productData, string $category): string
    {
        // Kategori bazlı cache: Aynı kategorideki tüm ürünler aynı template'i kullanır
        // Bu sayede AI maliyeti azalır ve cache hit rate artar
        return "product_details_template_{$category}";
    }
    
    /**
     * Fallback detaylar (hata durumunda)
     * 
     * @param array $productData
     * @return array
     */
    protected function getFallbackDetails(array $productData): array
    {
        $name = $productData['name'] ?? 'Bu ürün';
        $price = $productData['price'] ?? 0;
        
        return [
            'ai_description' => "{$name}, kaliteli ve güvenilir bir üründür. Kullanıcı dostu tasarımı ile ihtiyaçlarınızı karşılar.",
            'features' => [
                'Kaliteli malzeme ve işçilik',
                'Kullanıcı dostu tasarım',
                'Güvenilir performans'
            ],
            'usage_scenarios' => [
                'Günlük kullanım için ideal',
                'Hediye seçeneği olarak uygun'
            ],
            'specifications' => [
                'Fiyat' => number_format($price, 2) . ' TL',
                'Durum' => 'Yeni'
            ],
            'recommendations' => [
                'Kullanmadan önce kullanım kılavuzunu okuyun',
                'Sorularınız için müşteri hizmetlerimize danışın'
            ],
            'additional_info' => 'Daha fazla bilgi için müşteri hizmetlerimizle iletişime geçebilirsiniz.'
        ];
    }
    
    /**
     * Yeni template ekle (runtime'da)
     * 
     * @param string $category
     * @param array $template
     * @return void
     */
    public function addTemplate(string $category, array $template): void
    {
        $this->templates[$category] = $template;
        Log::info('New template added', ['category' => $category]);
    }
    
    /**
     * Tüm template'leri getir
     * 
     * @return array
     */
    public function getAllTemplates(): array
    {
        return $this->templates;
    }
    
    /**
     * Belirli bir template'i getir
     * 
     * @param string $category
     * @return array|null
     */
    public function getTemplate(string $category): ?array
    {
        return $this->templates[$category] ?? null;
    }
}

