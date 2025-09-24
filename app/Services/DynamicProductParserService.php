<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use App\Services\KnowledgeBase\AIService;

class DynamicProductParserService
{
    private $aiService;

    public function __construct()
    {
        $this->aiService = app(AIService::class);
    }

    /**
     * Kullanıcı mesajını AI ile analiz ederek ürün kriterlerini çıkarır
     */
    public function parseProductCriteria(string $userMessage): array
    {
        try {
            $prompt = $this->buildParsingPrompt($userMessage);
            $aiResponse = $this->aiService->generateResponse($prompt, []);
            
            $parsedData = $this->parseAIResponse($aiResponse);
            
            Log::info('Dynamic Product Parser Result', [
                'user_message' => $userMessage,
                'parsed_criteria' => $parsedData
            ]);
            
            return $parsedData;
            
        } catch (\Exception $e) {
            Log::error('Dynamic parsing failed', [
                'error' => $e->getMessage(),
                'user_message' => $userMessage
            ]);
            
            return $this->getFallbackCriteria($userMessage);
        }
    }

    /**
     * Fiyat sorguları için AI parsing
     */
    public function parsePriceCriteria(string $userMessage): array
    {
        try {
            $prompt = $this->buildPriceParsingPrompt($userMessage);
            $aiResponse = $this->aiService->generateResponse($prompt, []);
            
            $parsedData = $this->parseAIResponse($aiResponse);
            
            Log::info('Dynamic Price Parser Result', [
                'user_message' => $userMessage,
                'parsed_criteria' => $parsedData
            ]);
            
            return $parsedData;
        } catch (\Exception $e) {
            Log::error('Dynamic Price Parsing failed: ' . $e->getMessage());
            return $this->fallbackPriceParsing($userMessage);
        }
    }

    /**
     * FAQ sorguları için AI parsing
     */
    public function parseFAQCriteria(string $userMessage): array
    {
        try {
            $prompt = $this->buildFAQParsingPrompt($userMessage);
            $aiResponse = $this->aiService->generateResponse($prompt, []);
            
            $parsedData = $this->parseAIResponse($aiResponse);
            
            Log::info('Dynamic FAQ Parser Result', [
                'user_message' => $userMessage,
                'parsed_criteria' => $parsedData
            ]);
            
            return $parsedData;
        } catch (\Exception $e) {
            Log::error('Dynamic FAQ Parsing failed: ' . $e->getMessage());
            return $this->fallbackFAQParsing($userMessage);
        }
    }

    /**
     * Genel sorgular için AI parsing
     */
    public function parseGeneralCriteria(string $userMessage): array
    {
        try {
            $prompt = $this->buildGeneralParsingPrompt($userMessage);
            $aiResponse = $this->aiService->generateResponse($prompt, []);
            
            $parsedData = $this->parseAIResponse($aiResponse);
            
            Log::info('Dynamic General Parser Result', [
                'user_message' => $userMessage,
                'parsed_criteria' => $parsedData
            ]);
            
            return $parsedData;
        } catch (\Exception $e) {
            Log::error('Dynamic General Parsing failed: ' . $e->getMessage());
            return $this->fallbackGeneralParsing($userMessage);
        }
    }

    /**
     * Sepet işlemleri için AI parsing
     */
    public function parseCartCriteria(string $userMessage): array
    {
        try {
            $prompt = $this->buildCartParsingPrompt($userMessage);
            $aiResponse = $this->aiService->generateResponse($prompt, []);
            
            $parsedData = $this->parseAIResponse($aiResponse);
            
            Log::info('Dynamic Cart Parser Result', [
                'user_message' => $userMessage,
                'parsed_criteria' => $parsedData
            ]);
            
            return $parsedData;
        } catch (\Exception $e) {
            Log::error('Dynamic Cart Parsing failed: ' . $e->getMessage());
            return $this->fallbackCartParsing($userMessage);
        }
    }

    /**
     * AI parsing prompt'u oluşturur
     */
    private function buildParsingPrompt(string $userMessage): string
    {
        return "Sen bir e-ticaret ürün arama uzmanısın. Kullanıcının mesajından ürün kriterlerini çıkar.

Kullanıcı mesajı: \"{$userMessage}\"

Analiz etmen gereken kriterler:
1. Ürün türü/kategorisi (giyim, elektronik, ev, spor, sağlık, otomotiv, oyun, vb.)
2. Renk (kırmızı, mavi, siyah, vb.)
3. Boyut (S, M, L, XL, 42, 43, 44, 2m, 3m, vb.)
4. Marka (Nike, Adidas, Apple, Samsung, vb.)
5. Fiyat aralığı (ucuz, pahalı, 100-500 TL, vb.)
6. Özel özellikler (kışlık, yazlık, spor, klasik, vb.)
7. Miktar (2 adet, 3 tane, vb.)

Sektör kategorileri:
- GİYİM & MODA: ayakkabı, elbise, pantolon, tişört, çanta, saat, takı, gözlük
- ELEKTRONİK: telefon, tablet, bilgisayar, kulaklık, hoparlör, kamera, TV
- EV & YAŞAM: mobilya, halı, perde, mutfak eşyası, dekorasyon, temizlik
- SPOR & OUTDOOR: spor ayakkabı, spor kıyafet, fitness ekipmanı, outdoor
- SAĞLIK & BAKIM: kozmetik, vitamin, ilaç, kişisel bakım, sağlık cihazı
- OTOMOTİV: araç aksesuarı, yedek parça, motosiklet ekipmanı
- OYUN & EĞLENCE: video oyunu, oyun konsolu, masa oyunu, hobi malzemesi
- HIRDAVAT: el aleti, elektrikli alet, yapı malzemesi, bahçe aleti
- KİTAP & EĞİTİM: kitap, dergi, eğitim materyali, ofis malzemesi
- BEBEK & ÇOCUK: bebek bezi, oyuncak, çocuk giyimi, bebek bakımı

Yanıtı şu JSON formatında ver:
{
    \"product_type\": \"ayakkabı\",
    \"category\": \"giyim\",
    \"color\": \"kırmızı\",
    \"size\": \"44\",
    \"brand\": null,
    \"price_range\": null,
    \"special_features\": [],
    \"quantity\": 1,
    \"search_intent\": \"specific_product\",
    \"confidence\": 0.9,
    \"extracted_keywords\": [\"kırmızı\", \"ayakkabı\", \"44\"],
    \"search_priority\": [\"color\", \"product_type\", \"size\"]
}

Eğer kriter belirsizse null kullan. Confidence 0-1 arası olsun.";
    }

    /**
     * AI yanıtını parse eder
     */
    private function parseAIResponse(string $aiResponse): array
    {
        try {
            // JSON'u bul ve parse et
            if (preg_match('/\{.*\}/s', $aiResponse, $matches)) {
                $jsonData = json_decode($matches[0], true);
                
                if (json_last_error() === JSON_ERROR_NONE) {
                    return $this->validateAndNormalizeCriteria($jsonData);
                }
            }
            
            // JSON bulunamazsa, fallback kullan
            return $this->getFallbackCriteria($aiResponse);
            
        } catch (\Exception $e) {
            Log::warning('AI response parsing failed', [
                'ai_response' => $aiResponse,
                'error' => $e->getMessage()
            ]);
            
            return $this->getFallbackCriteria($aiResponse);
        }
    }

    /**
     * Kriterleri doğrular ve normalize eder
     */
    private function validateAndNormalizeCriteria(array $criteria): array
    {
        return [
            'product_type' => $criteria['product_type'] ?? null,
            'category' => $criteria['category'] ?? null,
            'color' => $criteria['color'] ?? null,
            'size' => $criteria['size'] ?? null,
            'brand' => $criteria['brand'] ?? null,
            'price_range' => $criteria['price_range'] ?? null,
            'special_features' => is_array($criteria['special_features'] ?? null) ? $criteria['special_features'] : [],
            'quantity' => (int)($criteria['quantity'] ?? 1),
            'search_intent' => $criteria['search_intent'] ?? 'general',
            'confidence' => min(1.0, max(0.0, (float)($criteria['confidence'] ?? 0.5))),
            'extracted_keywords' => is_array($criteria['extracted_keywords'] ?? null) ? $criteria['extracted_keywords'] : [],
            'search_priority' => is_array($criteria['search_priority'] ?? null) ? $criteria['search_priority'] : []
        ];
    }

    /**
     * Fallback kriterleri (AI başarısız olursa)
     */
    private function getFallbackCriteria(string $userMessage): array
    {
        $criteria = [
            'product_type' => null,
            'category' => null,
            'color' => null,
            'size' => null,
            'brand' => null,
            'price_range' => null,
            'special_features' => [],
            'quantity' => 1,
            'search_intent' => 'general',
            'confidence' => 0.3,
            'extracted_keywords' => [],
            'search_priority' => []
        ];

        // Basit regex fallback'leri
        $patterns = [
            'color' => '/(kırmızı|mavi|yeşil|sarı|siyah|beyaz|pembe|mor|turuncu|gri|kahverengi)/i',
            'size' => '/(\d+)\s*(numara|num|size|beden|cm|m|metre)/i',
            'brand' => '/(nike|adidas|apple|samsung|sony|lg|hp|dell|lenovo|asus|huawei|xiaomi)/i',
            'product_type' => '/(ayakkabı|telefon|bilgisayar|elbise|pantolon|tişört|saat|çanta|aksesuar|kozmetik|kitap|halı|mobilya|oyuncak|vitamin|araç|hırdavat|spor|bebek|kitap|oyun)/i'
        ];

        foreach ($patterns as $key => $pattern) {
            if (preg_match($pattern, $userMessage, $matches)) {
                $criteria[$key] = strtolower(trim($matches[1]));
                $criteria['extracted_keywords'][] = $criteria[$key];
            }
        }

        return $criteria;
    }

    /**
     * Fiyat sorguları için AI parsing prompt'u
     */
    private function buildPriceParsingPrompt(string $userMessage): string
    {
        return "Sen bir e-ticaret fiyat analiz uzmanısın. Kullanıcının mesajından fiyat kriterlerini çıkar.

Kullanıcı mesajı: \"{$userMessage}\"

Analiz etmen gereken kriterler:
1. Ürün türü/kategorisi (giyim, elektronik, ev, spor, sağlık, otomotiv, oyun, vb.)
2. Fiyat aralığı (ucuz, pahalı, 100-500 TL, 1000 TL altında, 5000 TL üstü, vb.)
3. Renk (kırmızı, mavi, siyah, vb.)
4. Boyut (S, M, L, XL, 42, 43, 44, 2m, 3m, vb.)
5. Marka (Nike, Adidas, Apple, Samsung, vb.)
6. Özel özellikler (kışlık, yazlık, spor, klasik, vb.)
7. Karşılaştırma türü (en ucuz, en pahalı, ortalama, vb.)

Yanıtı şu JSON formatında ver:
{
    \"product_type\": \"ayakkabı\",
    \"category\": \"giyim\",
    \"color\": \"kırmızı\",
    \"size\": \"44\",
    \"brand\": null,
    \"price_range\": {
        \"min\": 100,
        \"max\": 500,
        \"currency\": \"TL\",
        \"type\": \"range\"
    },
    \"special_features\": [],
    \"quantity\": 1,
    \"search_intent\": \"price_inquiry\",
    \"confidence\": 0.9,
    \"extracted_keywords\": [\"kırmızı\", \"ayakkabı\", \"fiyat\"],
    \"search_priority\": [\"price_range\", \"product_type\", \"color\"]
}";
    }

    /**
     * FAQ sorguları için AI parsing prompt'u
     */
    private function buildFAQParsingPrompt(string $userMessage): string
    {
        return "Sen bir e-ticaret müşteri hizmetleri uzmanısın. Kullanıcının mesajından FAQ kategorisini çıkar.

Kullanıcı mesajı: \"{$userMessage}\"

Analiz etmen gereken kategoriler:
1. KARGO & TESLİMAT: kargo süresi, teslimat, kargo ücreti, kargo takip
2. ÖDEME: ödeme yöntemleri, taksit, kredi kartı, havale
3. İADE & DEĞİŞİM: iade politikası, değişim, iade süresi
4. ÜRÜN BİLGİSİ: ürün detayları, özellikler, garanti
5. HESAP & ÜYELİK: üyelik, giriş, şifre, profil
6. TEKNİK DESTEK: teknik sorun, yardım, iletişim
7. SİPARİŞ: sipariş durumu, sipariş iptali, sipariş değişikliği

Yanıtı şu JSON formatında ver:
{
    \"category\": \"kargo\",
    \"subcategory\": \"teslimat\",
    \"question_type\": \"kargo_süresi\",
    \"urgency\": \"normal\",
    \"search_intent\": \"faq_search\",
    \"confidence\": 0.9,
    \"extracted_keywords\": [\"kargo\", \"süre\", \"ne kadar\"],
    \"search_priority\": [\"category\", \"question_type\"]
}";
    }

    /**
     * Genel sorgular için AI parsing prompt'u
     */
    private function buildGeneralParsingPrompt(string $userMessage): string
    {
        return "Sen bir e-ticaret genel danışmanısın. Kullanıcının mesajından genel kriterleri çıkar.

Kullanıcı mesajı: \"{$userMessage}\"

Analiz etmen gereken kriterler:
1. Ürün türü/kategorisi (giyim, elektronik, ev, spor, sağlık, otomotiv, oyun, vb.)
2. Renk (kırmızı, mavi, siyah, vb.)
3. Boyut (S, M, L, XL, 42, 43, 44, 2m, 3m, vb.)
4. Marka (Nike, Adidas, Apple, Samsung, vb.)
5. Fiyat aralığı (ucuz, pahalı, 100-500 TL, vb.)
6. Özel özellikler (kışlık, yazlık, spor, klasik, vb.)
7. Sorgu türü (öneri, arama, karşılaştırma, bilgi, vb.)

Yanıtı şu JSON formatında ver:
{
    \"product_type\": \"ayakkabı\",
    \"category\": \"giyim\",
    \"color\": \"kırmızı\",
    \"size\": \"44\",
    \"brand\": null,
    \"price_range\": null,
    \"special_features\": [],
    \"quantity\": 1,
    \"search_intent\": \"general_inquiry\",
    \"confidence\": 0.9,
    \"extracted_keywords\": [\"kırmızı\", \"ayakkabı\"],
    \"search_priority\": [\"product_type\", \"color\"]
}";
    }

    /**
     * Sepet işlemleri için AI parsing prompt'u
     */
    private function buildCartParsingPrompt(string $userMessage): string
    {
        return "Sen bir e-ticaret sepet yönetimi uzmanısın. Kullanıcının mesajından sepet kriterlerini çıkar.

Kullanıcı mesajı: \"{$userMessage}\"

Analiz etmen gereken kriterler:
1. Ürün türü/kategorisi (giyim, elektronik, ev, spor, sağlık, otomotiv, oyun, vb.)
2. Renk (kırmızı, mavi, siyah, vb.)
3. Boyut (S, M, L, XL, 42, 43, 44, 2m, 3m, vb.)
4. Marka (Nike, Adidas, Apple, Samsung, vb.)
5. Miktar (1 adet, 2 tane, 3 adet, vb.)
6. İşlem türü (ekle, çıkar, güncelle, temizle, vb.)
7. Özel özellikler (kışlık, yazlık, spor, klasik, vb.)

Yanıtı şu JSON formatında ver:
{
    \"product_type\": \"ayakkabı\",
    \"category\": \"giyim\",
    \"color\": \"kırmızı\",
    \"size\": \"44\",
    \"brand\": null,
    \"quantity\": 1,
    \"action\": \"add\",
    \"special_features\": [],
    \"search_intent\": \"cart_add\",
    \"confidence\": 0.9,
    \"extracted_keywords\": [\"kırmızı\", \"ayakkabı\", \"sepete\", \"ekle\"],
    \"search_priority\": [\"product_type\", \"color\", \"size\"]
}";
    }

    /**
     * Fiyat sorguları için fallback parsing
     */
    private function fallbackPriceParsing(string $userMessage): array
    {
        $criteria = [
            'product_type' => null,
            'category' => null,
            'color' => null,
            'size' => null,
            'brand' => null,
            'price_range' => null,
            'special_features' => [],
            'quantity' => 1,
            'search_intent' => 'price_inquiry',
            'confidence' => 0.3,
            'extracted_keywords' => [],
            'search_priority' => []
        ];

        // Fiyat aralığı regex'leri
        $pricePatterns = [
            '/(\d+)\s*-\s*(\d+)\s*tl/i' => 'range',
            '/(\d+)\s*tl\s*altında/i' => 'under',
            '/(\d+)\s*tl\s*üstü/i' => 'over',
            '/(\d+)\s*tl\s*üzerinde/i' => 'over',
            '/(ucuz|pahalı|orta)/i' => 'qualitative'
        ];

        foreach ($pricePatterns as $pattern => $type) {
            if (preg_match($pattern, $userMessage, $matches)) {
                $criteria['price_range'] = [
                    'type' => $type,
                    'value' => $matches[1] ?? $matches[0],
                    'currency' => 'TL'
                ];
                $criteria['extracted_keywords'][] = $matches[0];
                break;
            }
        }

        return $criteria;
    }

    /**
     * FAQ sorguları için fallback parsing
     */
    private function fallbackFAQParsing(string $userMessage): array
    {
        $criteria = [
            'category' => 'genel',
            'subcategory' => null,
            'question_type' => 'genel_soru',
            'urgency' => 'normal',
            'search_intent' => 'faq_search',
            'confidence' => 0.3,
            'extracted_keywords' => [],
            'search_priority' => []
        ];

        // FAQ kategorileri
        $faqCategories = [
            'kargo' => ['kargo', 'teslimat', 'gönderi', 'kurye'],
            'ödeme' => ['ödeme', 'taksit', 'kredi', 'havale'],
            'iade' => ['iade', 'değişim', 'geri', 'iptal'],
            'ürün' => ['ürün', 'detay', 'özellik', 'garanti'],
            'hesap' => ['hesap', 'üyelik', 'giriş', 'şifre'],
            'destek' => ['destek', 'yardım', 'sorun', 'iletişim']
        ];

        foreach ($faqCategories as $category => $keywords) {
            foreach ($keywords as $keyword) {
                if (stripos($userMessage, $keyword) !== false) {
                    $criteria['category'] = $category;
                    $criteria['extracted_keywords'][] = $keyword;
                    break 2;
                }
            }
        }

        return $criteria;
    }

    /**
     * Genel sorgular için fallback parsing
     */
    private function fallbackGeneralParsing(string $userMessage): array
    {
        return $this->getFallbackCriteria($userMessage);
    }

    /**
     * Sepet işlemleri için fallback parsing
     */
    private function fallbackCartParsing(string $userMessage): array
    {
        $criteria = $this->getFallbackCriteria($userMessage);
        $criteria['search_intent'] = 'cart_add';
        $criteria['action'] = 'add';
        $criteria['quantity'] = 1;

        // Sepet işlem türleri
        if (preg_match('/(ekle|sepete|at)/i', $userMessage)) {
            $criteria['action'] = 'add';
        } elseif (preg_match('/(çıkar|sil|kaldır)/i', $userMessage)) {
            $criteria['action'] = 'remove';
        } elseif (preg_match('/(güncelle|değiştir)/i', $userMessage)) {
            $criteria['action'] = 'update';
        } elseif (preg_match('/(temizle|boşalt)/i', $userMessage)) {
            $criteria['action'] = 'clear';
        }

        return $criteria;
    }

    /**
     * Parsed kriterlere göre ürünleri filtreler
     */
    public function filterProductsByCriteria(array $products, array $criteria): array
    {
        if (empty($criteria['extracted_keywords'])) {
            return $products;
        }

        $filteredProducts = [];
        $keywords = array_map('strtolower', $criteria['extracted_keywords']);

        foreach ($products as $product) {
            $score = $this->calculateProductMatchScore($product, $criteria, $keywords);
            
            if ($score > 0.3) { // Minimum eşleşme skoru
                $product['match_score'] = $score;
                $filteredProducts[] = $product;
            }
        }

        // Skora göre sırala
        usort($filteredProducts, function($a, $b) {
            return $b['match_score'] <=> $a['match_score'];
        });

        return $filteredProducts;
    }

    /**
     * Ürün eşleşme skorunu hesaplar
     */
    private function calculateProductMatchScore(array $product, array $criteria, array $keywords): float
    {
        $score = 0.0;
        $productText = strtolower($product['name'] . ' ' . $product['description'] . ' ' . $product['category']);

        // Renk eşleşmesi (yüksek ağırlık)
        if ($criteria['color'] && strpos($productText, $criteria['color']) !== false) {
            $score += 0.4;
        }

        // Ürün türü eşleşmesi (yüksek ağırlık)
        if ($criteria['product_type'] && strpos($productText, $criteria['product_type']) !== false) {
            $score += 0.3;
        }

        // Boyut eşleşmesi (orta ağırlık)
        if ($criteria['size'] && strpos($productText, $criteria['size']) !== false) {
            $score += 0.2;
        }

        // Marka eşleşmesi (orta ağırlık)
        if ($criteria['brand'] && strpos($productText, $criteria['brand']) !== false) {
            $score += 0.2;
        }

        // Genel anahtar kelime eşleşmesi
        foreach ($keywords as $keyword) {
            if (strpos($productText, $keyword) !== false) {
                $score += 0.1;
            }
        }

        return min(1.0, $score);
    }

    /**
     * Kriterlere göre arama mesajı oluşturur
     */
    public function generateSearchMessage(array $criteria): string
    {
        $message = "Arama kriterlerinize göre ";
        
        if ($criteria['product_type']) {
            $message .= $criteria['product_type'];
        }
        
        if ($criteria['color']) {
            $message .= " " . $criteria['color'];
        }
        
        if ($criteria['size']) {
            $message .= " " . $criteria['size'] . " numara";
        }
        
        $message .= " ürünleri buldum:";
        
        return $message;
    }
}
