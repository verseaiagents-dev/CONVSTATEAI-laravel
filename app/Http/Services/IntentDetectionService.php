<?php

namespace App\Http\Services;

use Illuminate\Support\Facades\Log;
use App\Models\KnowledgeChunk;
use App\Helpers\ProductImageHelper;

class IntentDetectionService {
    private $intents = [];
    private $thesaurus = [];
    private $aiIntentCache = [];
    private $unknownIntentThreshold = 0.15;
    
    public function __construct() {
        $this->initializeIntents();
        $this->initializeThesaurus();
        $this->loadAIIntentCache();
    }
    
    /**
     * AI-powered intent detection for unknown words/phrases
     */
    public function detectIntentWithAI($message) {
        // Önce mevcut sistemle dene
        $detectedIntent = $this->detectIntent($message);
        
        // Eğer intent bulunamadıysa veya confidence çok düşükse AI kullan
        if ($detectedIntent['intent'] === 'unknown' || $detectedIntent['confidence'] < $this->unknownIntentThreshold) {
            $aiIntent = $this->analyzeIntentWithAI($message);
            
            if ($aiIntent && $aiIntent['confidence'] > 0.6) {
                // AI'dan gelen intent'i sisteme ekle
                $this->addDynamicIntent($aiIntent);
                
                // Cache'e kaydet
                $this->saveAIIntentCache();
                
                return [
                    'intent' => $aiIntent['intent'],
                    'confidence' => $aiIntent['confidence'],
                    'message' => $message,
                    'threshold_met' => true,
                    'ai_generated' => true,
                    'new_keywords' => $aiIntent['keywords'],
                    'closest_intent' => $aiIntent['intent']
                ];
            }
        }
        
        return $detectedIntent;
    }
    
    /**
     * AI ile intent analizi yap
     */
    private function analyzeIntentWithAI($message) {
        try {
            // AI prompt'u hazırla
            $prompt = $this->generateIntentAnalysisPrompt($message);
            
            // AI'dan yanıt al (burada gerçek AI API'si kullanılacak)
            $aiResponse = $this->callAIForIntentAnalysis($prompt);
            
            if ($aiResponse && isset($aiResponse['intent'])) {
                return [
                    'intent' => $aiResponse['intent'],
                    'confidence' => $aiResponse['confidence'],
                    'keywords' => $aiResponse['keywords'] ?? [$message],
                    'response' => $aiResponse['response'] ?? $this->getDefaultResponseForIntent($aiResponse['intent']),
                    'confidence_threshold' => 0.25,
                    'ai_analyzed' => true
                ];
            }
        } catch (\Exception $e) {
            Log::error('AI intent analysis error: ' . $e->getMessage());
        }
        
        return null;
    }
    
    /**
     * Intent analizi için AI prompt'u oluştur
     */
    private function generateIntentAnalysisPrompt($message) {
        $existingIntents = array_keys($this->intents);
        $existingKeywords = [];
        
        foreach ($this->intents as $intent => $data) {
            $existingKeywords[$intent] = $data['keywords'];
        }
        
        $prompt = "Aşağıdaki Kullanıcının diliyle yazılan mesajı analiz et ve hangi intent'e ait olduğunu belirle:\n\n";
        $prompt .= "Mesaj: \"{$message}\"\n\n";
        $prompt .= "Mevcut intent'ler ve anahtar kelimeleri:\n";
        
        foreach ($existingIntents as $intent) {
            $keywords = implode(', ', $existingKeywords[$intent]);
            $prompt .= "- {$intent}: {$keywords}\n";
        }
        
        $prompt .= "\nLütfen aşağıdaki JSON formatında yanıt ver:\n";
        $prompt .= "{\n";
        $prompt .= "  \"intent\": \"intent_adı\",\n";
        $prompt .= "  \"confidence\": 0.85,\n";
        $prompt .= "  \"keywords\": [\"yeni_anahtar_kelime1\", \"yeni_anahtar_kelime2\"],\n";
        $prompt .= "  \"response\": \"Yanıt metni\",\n";
        $prompt .= "  \"reasoning\": \"Neden bu intent seçildi\"\n";
        $prompt .= "}\n\n";
        $prompt .= "Eğer hiçbir intent'e uymuyorsa 'unknown' olarak işaretle.";
        
        return $prompt;
    }
    
    /**
     * AI API'sini çağır (şimdilik simüle ediyoruz)
     */
    private function callAIForIntentAnalysis($prompt) {
        // Burada gerçek AI API'si kullanılacak (OpenAI, Claude, vb.)
        // Şimdilik simüle ediyoruz
        
        $message = mb_strtolower($prompt, 'UTF-8');
        
        // Basit AI simülasyonu - gerçek implementasyonda bu kısım değişecek
        if (mb_strpos($message, 'kırmızı') !== false || mb_strpos($message, 'renk') !== false) {
            return [
                'intent' => 'color_preference',
                'confidence' => 0.9,
                'keywords' => ['renk', 'kırmızı', 'renkli'],
                'response' => 'Renk tercihinize göre ürünleri öneriyorum.',
                'reasoning' => 'Mesajda renk tercihi belirtilmiş'
            ];
        }
        
        if (mb_strpos($message, 'ucuz') !== false || mb_strpos($message, 'ekonomik') !== false) {
            return [
                'intent' => 'price_preference',
                'confidence' => 0.85,
                'keywords' => ['ucuz', 'ekonomik', 'uygun fiyat'],
                'response' => 'Ekonomik fiyatlı ürünleri öneriyorum.',
                'reasoning' => 'Mesajda fiyat tercihi belirtilmiş'
            ];
        }
        
        if (mb_strpos($message, 'yeni') !== false || mb_strpos($message, 'güncel') !== false) {
            return [
                'intent' => 'trend_products',
                'confidence' => 0.8,
                'keywords' => ['yeni', 'güncel', 'trend', 'popüler'],
                'response' => 'En yeni ve trend ürünleri öneriyorum.',
                'reasoning' => 'Mesajda yenilik/güncellik aranıyor'
            ];
        }
        
        return null;
    }
    
    /**
     * AI'dan gelen intent'i sisteme dinamik olarak ekle
     */
    private function addDynamicIntent($aiIntent) {
        $intentName = $aiIntent['intent'];
        
        // Eğer intent zaten varsa, sadece yeni keywords ekle
        if (isset($this->intents[$intentName])) {
            $existingKeywords = $this->intents[$intentName]['keywords'];
            $newKeywords = array_diff($aiIntent['keywords'], $existingKeywords);
            
            if (!empty($newKeywords)) {
                $this->intents[$intentName]['keywords'] = array_merge($existingKeywords, $newKeywords);
                
                // Thesaurus'u da güncelle
                foreach ($newKeywords as $keyword) {
                    $this->thesaurus[$keyword] = $existingKeywords;
                }
                
            }
        } else {
            // Yeni intent oluştur
            $this->intents[$intentName] = [
                'keywords' => $aiIntent['keywords'],
                'response' => $aiIntent['response'],
                'confidence_threshold' => $aiIntent['confidence_threshold'] ?? 0.25,
                'ai_generated' => true,
                'created_at' => date('Y-m-d H:i:s')
            ];
            
            // Thesaurus'a ekle
            foreach ($aiIntent['keywords'] as $keyword) {
                $this->thesaurus[$keyword] = $aiIntent['keywords'];
            }
            
        }
        
        // Cache'e kaydet
        $this->aiIntentCache[$intentName] = [
            'keywords' => $aiIntent['keywords'],
            'response' => $aiIntent['response'],
            'confidence' => $aiIntent['confidence'],
            'created_at' => date('Y-m-d H:i:s'),
            'usage_count' => 0
        ];
    }
    
    /**
     * AI intent cache'ini yükle
     */
    private function loadAIIntentCache() {
        $cacheFile = storage_path('app/ai_intent_cache.json');
        
        if (file_exists($cacheFile)) {
            try {
                $cacheData = json_decode(file_get_contents($cacheFile), true);
                if ($cacheData) {
                    $this->aiIntentCache = $cacheData;
                    
                    // Cache'deki intent'leri sisteme yükle
                    foreach ($this->aiIntentCache as $intentName => $intentData) {
                        if (!isset($this->intents[$intentName])) {
                            $this->intents[$intentName] = [
                                'keywords' => $intentData['keywords'],
                                'response' => $intentData['response'],
                                'confidence_threshold' => 0.25,
                                'ai_generated' => true,
                                'created_at' => $intentData['created_at']
                            ];
                        }
                    }
                }
            } catch (\Exception $e) {
                Log::error('AI intent cache load error: ' . $e->getMessage());
            }
        }
    }
    
    /**
     * AI intent cache'ini kaydet
     */
    private function saveAIIntentCache() {
        $cacheFile = storage_path('app/ai_intent_cache.json');
        
        try {
            // Kullanım sayısını artır
            foreach ($this->aiIntentCache as $intentName => &$intentData) {
                if (isset($this->intents[$intentName])) {
                    $intentData['usage_count']++;
                    $intentData['last_used'] = date('Y-m-d H:i:s');
                }
            }
            
            file_put_contents($cacheFile, json_encode($this->aiIntentCache, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        } catch (\Exception $e) {
            Log::error('AI intent cache save error: ' . $e->getMessage());
        }
    }
    
    /**
     * Intent için varsayılan yanıt al
     */
    private function getDefaultResponseForIntent($intentName) {
        $defaultResponses = [
            'color_preference' => 'Renk tercihinize göre ürünleri öneriyorum.',
            'price_preference' => 'Fiyat tercihinize göre ürünleri öneriyorum.',
            'trend_products' => 'En yeni ve trend ürünleri öneriyorum.',
            'size_preference' => 'Boyut tercihinize göre ürünleri öneriyorum.',
            'quality_preference' => 'Kalite tercihinize göre ürünleri öneriyorum.'
        ];
        
        return $defaultResponses[$intentName] ?? 'Size yardımcı olmaya çalışıyorum.';
    }
    
    /**
     * AI-generated intent'leri listele
     */
    public function getAIGeneratedIntents() {
        $aiIntents = [];
        
        foreach ($this->intents as $intentName => $intentData) {
            if (isset($intentData['ai_generated']) && $intentData['ai_generated']) {
                $aiIntents[$intentName] = [
                    'keywords' => $intentData['keywords'],
                    'response' => $intentData['response'],
                    'created_at' => $intentData['created_at'],
                    'usage_count' => $this->aiIntentCache[$intentName]['usage_count'] ?? 0
                ];
            }
        }
        
        return $aiIntents;
    }
    
    /**
     * Intent için threshold değerini al
     */
    public function getIntentThreshold($intentName) {
        if (isset($this->intents[$intentName])) {
            return $this->intents[$intentName]['confidence_threshold'];
        }
        
        return 0.25; // Varsayılan threshold
    }
    
    /**
     * Tüm intent'leri al
     */
    public function getAllIntents() {
        return $this->intents;
    }

    /**
     * AI ile akıllı ürün önerisi al
     */
    public function getSmartRecommendations($message) {
        try {
            // SmartProductRecommenderService kullan
            // Fallback smart recommendations
            $recommendations = $this->getSampleProducts();
            
            if ($recommendations && !empty($recommendations)) {
                return [
                    'products' => $recommendations,
                    'response' => 'Size özel ürün önerileri',
                    'reason' => 'Örnek ürünler',
                    'suggestions' => ['Daha fazla ürün göster', 'Fiyat bilgisi', 'Teknik özellikler', 'Kategori değiştir']
                ];
            }
            
            // Fallback ürünler
            $products = $this->getSampleProducts();
            
            return [
                'products' => $products,
                'response' => 'Size özel ürün önerileri',
                'reason' => 'Veritabanından seçilen ürünler',
                'suggestions' => ['Daha fazla ürün göster', 'Fiyat bilgisi', 'Teknik özellikler', 'Kategori değiştir']
            ];
            
        } catch (\Exception $e) {
            \Log::error('Smart recommendations error: ' . $e->getMessage());
            
            // Hata durumunda fallback
            return [
                'products' => $products,
                'response' => 'Ürün önerisi yapılamadı',
                'reason' => 'Sistem hatası',
                'suggestions' => ['Tekrar dene', 'Yardım al']
            ];
        }
    }
    
    private function initializeIntents() {
        $this->intents = [
            'greeting' => [
                'keywords' => ['merhaba', 'selam', 'hey', 'hi', 'hello', 'günaydın', 'iyi günler', 'iyi akşamlar', 'iyi geceler'],
                'response' => 'Merhaba! Size nasıl yardımcı olabilirim? Ürün arama, fiyat sorgulama veya genel bilgi için yardımcı olabilirim.',
                'confidence_threshold' => 0.3
            ],
            'product_search' => [
                'keywords' => ['ara', 'bul', 'hangi', 'nerede', 'var mı', 'göster', 'listele', 'ürün', 'ne var', 'bulabilir miyim'],
                'response' => 'Ürün arama yapıyorum. Hangi kategoride veya markada ürün arıyorsunuz?',
                'confidence_threshold' => 0.25
            ],
            'price_inquiry' => [
                'keywords' => ['kaç para', 'ne kadar', 'ücret', 'bedel', 'maliyet', 'para', 'tl', 'lira', 'kuruş', 'fiyat öğren', 'fiyatı nedir'],
                'response' => 'Fiyat bilgisi için hangi ürünü öğrenmek istiyorsunuz?',
                'confidence_threshold' => 0.4
            ],
            'category_browse' => [
                'keywords' => ['kategori', 'elektronik', 'giyim', 'ev', 'spor', 'kozmetik', 'kitap', 'otomotiv', 'sağlık', 'bahçe', 'pet', 'tür', 'çeşit', 'göster', 'öner', 'listele', 'kategorileri'],
                'response' => 'Kategoriye göre ürünleri listeliyorum. Hangi kategoriyi detaylı görmek istiyorsunuz?',
                'confidence_threshold' => 0.3
            ],
            'brand_search' => [
                'keywords' => ['apple', 'samsung', 'nike', 'adidas', 'ikea', 'bosch', 'sony', 'lg', 'dell', 'hp', 'lenovo', 'marka', 'firma'],
                'response' => 'Marka bazlı ürün arama yapıyorum. Hangi markanın ürünlerini görmek istiyorsunuz?',
                'confidence_threshold' => 0.25
            ],
            'stock_inquiry' => [
                'keywords' => ['stok', 'mevcut', 'var mı', 'bulunuyor mu', 'tükenmiş', 'kalmış', 'elde', 'depoda', 'mağazada'],
                'response' => 'Stok durumu için hangi ürünü kontrol etmemi istiyorsunuz?',
                'confidence_threshold' => 0.25
            ],

            'comparison' => [
                'keywords' => ['karşılaştır', 'hangi daha iyi', 'fark', 'benzer', 'aynı', 'vs', 'veya', 'ya da', 'hangisi'],
                'response' => 'Ürün karşılaştırması yapıyorum. Hangi ürünleri karşılaştırmak istiyorsunuz?',
                'confidence_threshold' => 0.25
            ],
            'help' => [
                'keywords' => ['yardım', 'ne yapabilir', 'destek', 'açıkla', 'öğren', 'bilmiyorum', 'kafam karıştı', 'müşteri hizmetleri', 'müşteri hizmeti', 'hizmet', 'konuşabilir miyim', 'görüşebilir miyim', 'iletişime geç', 'iletişime geçebilir miyim', 'nasıl yardımcı olabilirsin', 'ne yapabilirsin'],
                'response' => 'Size yardımcı olabilirim! Ürün arama, fiyat sorgulama, kategori tarama, marka arama, stok kontrolü, sipariş sorgulama ve ürün önerileri sunabilirim. Ne yapmak istiyorsunuz?',
                'confidence_threshold' => 0.5
            ],
            'goodbye' => [
                'keywords' => ['güle güle', 'hoşça kal', 'görüşürüz', 'bye', 'çıkış', 'kapat', 'teşekkür', 'sağol', 'tamam'],
                'response' => 'Görüşmek üzere! Başka bir sorunuz olursa yardımcı olmaktan mutluluk duyarım.',
                'confidence_threshold' => 0.3
            ],
            'cart_add' => [
                'keywords' => ['sepete ekle', 'sepete ekler misin', 'ekle', 'ekler misin', 'sepete koy', 'sepete koyar mısın', 'koy', 'koyar mısın', 'ürün ekle', 'ürün koy', 'cart', 'basket', 'iphone sepete ekle', 'telefon sepete ekle', 'bilgisayar sepete ekle'],
                'response' => 'Ürünü sepete ekliyorum. Hangi ürünü eklemek istiyorsunuz?',
                'confidence_threshold' => 0.25
            ],
            'cart_remove' => [
                'keywords' => ['sepetten çıkar', 'sepetten çıkarır mısın', 'çıkar', 'çıkarır mısın', 'sepetten sil', 'sepetten siler misin', 'sil', 'siler misin', 'ürün çıkar', 'ürün sil'],
                'response' => 'Ürünü sepetten çıkarıyorum. Hangi ürünü çıkarmak istiyorsunuz?',
                'confidence_threshold' => 0.25
            ],
            'cart_view' => [
                'keywords' => ['sepetim', 'sepetimi göster', 'sepetimi aç', 'sepetimde ne var', 'sepetimdeki ürünler', 'sepeti göster', 'sepetimde ne var'],
                'response' => 'Sepetinizdeki ürünleri gösteriyorum.',
                'confidence_threshold' => 0.25
            ],
            'order_inquiry' => [
                'keywords' => ['sipariş', 'siparişim', 'sipariş durumu', 'sipariş takibi', 'sipariş nerede', 'sipariş bilgisi', 'sipariş sorgula', 'sipariş numarası', 'sipariş geçmişi', 'sipariş durumumu öğren', 'siparişim nerede', 'öner', 'tavsiye', 'en iyi', 'popüler', 'trend', 'yeni', 'güncel', 'öneri', 'tavsiye et', 'ne alayım'],
                'response' => 'Sipariş durumunuzu kontrol ediyorum veya size ürün önerileri sunuyorum. Sipariş numaranızı girebilir veya hangi kategoride öneri istediğinizi belirtebilirsiniz.',
                'confidence_threshold' => 0.25
            ],
            'cargo_tracking' => [
                'keywords' => ['kargo', 'kargom', 'kargom nerede', 'kargo takip', 'kargo durumu', 'kargo numarası', 'kargo firması', 'kargo takip numarası', 'kargo bilgisi', 'kargo sorgula'],
                'response' => 'Kargo takip numaranızı girerek kargo durumunuzu öğrenebilirsiniz.',
                'confidence_threshold' => 0.25
            ],
            'campaign_inquiry' => [
                'keywords' => ['kampanya', 'kampanyalar', 'kampanyalarda', 'indirim', 'fırsat', 'bedava', 'ücretsiz', 'taksit', 'promosyon', 'teklif', 'özel', 'avantaj'],
                'response' => 'Aktif kampanyalarımızı listeliyorum. Size özel fırsatları kaçırmayın!',
                'confidence_threshold' => 0.25
            ],
            
            // === FUNNEL INTENT'LERİ ===
            'capabilities_inquiry' => [
                'keywords' => ['ne yapabilirsin', 'nasıl yardımcı olabilirsin', 'hangi hizmetleri sunuyorsun', 'ne sunuyorsun', 'özelliklerin neler', 'neler yapabilirsin', 'yeteneklerin neler', 'hangi konularda yardımcı olabilirsin', 'ne işe yarar', 'nasıl çalışır'],
                'response' => 'Size şu konularda yardımcı olabilirim:',
                'confidence_threshold' => 0.3
            ],
            'project_info' => [
                'keywords' => ['proje hakkında', 'site hakkında', 'şirket hakkında', 'kimsiniz', 'nedir bu', 'açıkla', 'tanıt', 'hangi proje', 'bu ne', 'ne iş yapıyorsunuz', 'firma hakkında', 'şirket bilgisi', 'proje', 'hakkında', 'bilgi', 'ver', 'nedir', 'ne', 'iş', 'yapıyorsunuz', 'yapıyor', 'hizmet', 'sunuyorsunuz', 'sunuyor'],
                'response' => 'Bu proje hakkında bilgi veriyorum...',
                'confidence_threshold' => 0.15
            ],
            'conversion_guidance' => [
                'keywords' => ['nasıl alırım', 'nasıl sipariş veririm', 'nasıl başlarım', 'adım adım', 'süreç nasıl', 'ne yapmalıyım', 'nasıl devam ederim', 'sonraki adım', 'nasıl ilerlerim', 'rehberlik et', 'yol göster', 'nasıl satın alırım', 'nasıl üye olurum', 'nasıl kayıt olurum', 'nasıl başlarım', 'nasıl devam ederim', 'adımlar neler', 'süreç nedir'],
                'response' => 'Size adım adım rehberlik ediyorum...',
                'confidence_threshold' => 0.15
            ],
            'pricing_guidance' => [
                'keywords' => ['fiyat nasıl', 'ücretlendirme', 'maliyet nedir', 'ne kadar tutar', 'bütçe', 'ödeme nasıl', 'fiyat politikası', 'paket fiyatları', 'ücret yapısı', 'fiyat bilgisi', 'fiyat rehberi', 'fiyat seçenekleri', 'ödeme seçenekleri', 'fiyat paketleri'],
                'response' => 'Fiyat bilgileri ve ödeme seçenekleri hakkında bilgi veriyorum...',
                'confidence_threshold' => 0.1
            ],
            'demo_request' => [
                'keywords' => ['demo', 'deneme', 'test', 'göster', 'nasıl çalışır', 'örnek', 'uygulama', 'canlı gösterim', 'tanıtım'],
                'response' => 'Size demo ve tanıtım imkanları sunuyorum...',
                'confidence_threshold' => 0.3
            ],
            'contact_request' => [
                'keywords' => ['iletişim', 'konuş', 'görüş', 'randevu', 'toplantı', 'telefon', 'email', 'adres', 'nerede', 'nasıl ulaşırım', 'destek', 'müşteri hizmetleri', 'müşteri hizmeti', 'hizmet', 'yardım', 'destek al', 'konuşabilir miyim', 'görüşebilir miyim', 'iletişime geç', 'iletişime geçebilir miyim', 'müşteri temsilcisine bağla', 'temsilci', 'temsilciye bağla', 'müşteri temsilcisi', 'bağla'],
                'response' => 'İletişim bilgileri ve destek seçenekleri sunuyorum...',
                'confidence_threshold' => 0.25
            ],
            'product_recommendation' => [
                'keywords' => ['ürün öner', 'tavsiye et', 'öner', 'öneri', 'hangisini almalıyım', 'ne önerirsin', 'hangi ürün', 'size uygun', 'kişisel öneri', 'özel öneri', 'bana öner', 'bana tavsiye', 'ürün tavsiye', 'tavsiye', 'öneri ver', 'öneri yap', 'tavsiye yap', 'öneri al', 'tavsiye al', 'hangi', 'ne almalı', 'ne öneriyorsun', 'ne tavsiye edersin', 'hangi marka', 'hangi model', 'en iyi', 'en kaliteli', 'en uygun', 'rastgele', 'rastgele ürün', 'rastgele öner', 'rastgele tavsiye', 'random', 'random product', 'random öner', 'herhangi bir', 'herhangi bir ürün', 'herhangi bir şey', 'ne olursa olsun', 'fark etmez'],
                'response' => 'Size kişisel ürün önerileri sunuyorum...',
                'confidence_threshold' => 0.1
            ],
            'product_recommendations' => [
                'keywords' => ['ürün öner', 'tavsiye et', 'öner', 'öneri', 'hangisini almalıyım', 'ne önerirsin', 'hangi ürün', 'size uygun', 'kişisel öneri', 'özel öneri', 'tavsiye', 'öneri ver', 'öneri yap', 'tavsiye yap', 'öneri al', 'tavsiye al', 'hangi', 'ne almalı', 'ne öneriyorsun', 'ne tavsiye edersin', 'hangi marka', 'hangi model', 'en iyi', 'en kaliteli', 'en uygun'],
                'response' => 'Size kişisel ürün önerileri sunuyorum...',
                'confidence_threshold' => 0.15
            ],
            'specific_product_recommendation' => [
                'keywords' => ['kırmızı', 'mavi', 'siyah', 'beyaz', 'yeşil', 'sarı', 'mor', 'pembe', 'turuncu', 'gri', 'kahverengi', 'lacivert', 'tişört', 'gömlek', 'pantolon', 'ayakkabı', 'telefon', 'bilgisayar', 'çanta', 'saat', 'gözlük', 'şapka', 'eldiven', 'çorap', 'xs', 's', 'm', 'l', 'xl', 'xxl', 'büyük', 'küçük', 'orta', '36', '37', '38', '39', '40', '41', '42', '43', '44', '45', 'nike', 'adidas', 'apple', 'samsung', 'sony', 'lg', 'hp', 'dell', 'lenovo', 'huawei', 'xiaomi', 'pamuk', 'polyester', 'deri', 'jean', 'keten', 'yün', 'ipek', 'naylon', 'koton', 'casual', 'formal', 'sport', 'klasik', 'modern', 'vintage', 'trendy', 'minimalist'],
                'response' => 'Spesifik ürün önerileri sunuyorum...',
                'confidence_threshold' => 0.2
            ]
        ];
    }
    
    private function initializeThesaurus() {
        $this->thesaurus = [
            // Ürün arama eşanlamlıları
            'ara' => ['bul', 'hangi', 'nerede', 'var mı', 'göster', 'listele', 'ürün', 'ne var', 'bulabilir miyim', 'istiyorum', 'arıyorum'],
            'bul' => ['ara', 'hangi', 'nerede', 'var mı', 'göster', 'listele', 'ürün', 'ne var', 'bulabilir miyim'],
            'göster' => ['ara', 'bul', 'hangi', 'nerede', 'var mı', 'listele', 'ürün', 'ne var'],
            'listele' => ['ara', 'bul', 'hangi', 'nerede', 'var mı', 'göster', 'ürün', 'ne var'],
            
            // Fiyat eşanlamlıları
            'fiyat' => ['kaç para', 'ne kadar', 'ücret', 'bedel', 'maliyet', 'para', 'tl', 'lira', 'kuruş', 'pahalı', 'ucuz'],
            'kaç para' => ['fiyat', 'ne kadar', 'ücret', 'bedel', 'maliyet', 'para', 'tl', 'lira'],
            'ne kadar' => ['fiyat', 'kaç para', 'ücret', 'bedel', 'maliyet', 'para', 'tl'],
            
            // Kategori eşanlamlıları
            'kategori' => ['tür', 'çeşit', 'bölüm', 'alan', 'sektör', 'tip', 'sınıf'],
            'elektronik' => ['elektrik', 'teknoloji', 'tech', 'digital', 'akıllı'],
            'giyim' => ['kıyafet', 'elbise', 'moda', 'textil', 'konfeksiyon'],
            'ev' => ['ev eşyası', 'mobilya', 'dekorasyon', 'yaşam', 'household'],
            'spor' => ['spor malzemesi', 'fitness', 'egzersiz', 'atletik'],
            
            // Marka eşanlamlıları
            'marka' => ['firma', 'şirket', 'brand', 'üretici', 'yapımcı'],
            'apple' => ['iphone', 'mac', 'ipad', 'macbook', 'airpods'],
            'samsung' => ['galaxy', 'note', 'tab', 'samsung telefon'],
            'nike' => ['nike ayakkabı', 'nike spor', 'nike giyim'],
            'adidas' => ['adidas ayakkabı', 'adidas spor', 'adidas giyim'],
            
            // Stok eşanlamlıları
            'stok' => ['mevcut', 'var mı', 'bulunuyor mu', 'elde', 'depoda', 'mağazada', 'kalmış'],
            'mevcut' => ['stok', 'var mı', 'bulunuyor mu', 'elde', 'depoda'],
            'var mı' => ['stok', 'mevcut', 'bulunuyor mu', 'elde', 'depoda'],
            
            // Öneri eşanlamlıları
            'öner' => ['tavsiye', 'öneri', 'tavsiye et', 'ne alayım', 'hangisini alayım', 'en iyi'],
            
            // Sepet eşanlamlıları
            'sepete ekle' => ['ekle', 'ekler misin', 'sepete koy', 'sepete koyar mısın', 'koy', 'koyar mısın', 'ürün ekle', 'ürün koy', 'cart', 'basket'],
            'ekle' => ['sepete ekle', 'sepete ekler misin', 'sepete koy', 'sepete koyar mısın', 'koy', 'koyar mısın', 'ürün ekle', 'ürün koy'],
            'sepete koy' => ['sepete ekle', 'ekle', 'ekler misin', 'koy', 'koyar mısın', 'ürün ekle', 'ürün koy'],
            'koy' => ['sepete ekle', 'ekle', 'ekler misin', 'sepete koy', 'sepete koyar mısın', 'ürün ekle', 'ürün koy'],
            'sepet' => ['cart', 'basket', 'sepetim', 'sepetimi göster', 'sepetimi aç'],
            'sepetim' => ['sepet', 'cart', 'basket', 'sepetimi göster', 'sepetimi aç', 'sepetimde ne var'],
            
            // Müşteri hizmetleri eşanlamlıları
            'müşteri hizmetleri' => ['müşteri hizmeti', 'hizmet', 'destek', 'yardım', 'iletişim', 'konuş', 'görüş', 'konuşabilir miyim', 'görüşebilir miyim'],
            'konuş' => ['görüş', 'iletişim', 'müşteri hizmetleri', 'destek', 'yardım', 'konuşabilir miyim', 'görüşebilir miyim'],
            'görüş' => ['konuş', 'iletişim', 'müşteri hizmetleri', 'destek', 'yardım', 'konuşabilir miyim', 'görüşebilir miyim'],
            'iletişim' => ['konuş', 'görüş', 'müşteri hizmetleri', 'destek', 'yardım', 'iletişime geç', 'iletişime geçebilir miyim'],
            'destek' => ['yardım', 'müşteri hizmetleri', 'iletişim', 'konuş', 'görüş', 'destek al']
        ];
    }
    
    public function detectIntent($message) {
        $message = mb_strtolower(trim($message), 'UTF-8');
        // Fix Turkish character issues
        $message = str_replace(['i̇', 'ı̇'], ['i', 'i'], $message);
        $bestIntent = null;
        $highestConfidence = 0;
        
        // Check for random product recommendation first (special case)
        $randomKeywords = ['rastgele', 'random', 'herhangi bir', 'ne olursa olsun', 'fark etmez'];
        $hasRandomKeyword = false;
        foreach ($randomKeywords as $keyword) {
            if (mb_strpos($message, $keyword) !== false) {
                $hasRandomKeyword = true;
                break;
            }
        }
        
        if ($hasRandomKeyword) {
            return [
                'intent' => 'product_recommendation',
                'confidence' => 0.95,
                'message' => $message,
                'threshold_met' => true
            ];
        }
        
        // Check for customer service keywords first (special case)
        $customerServiceKeywords = ['müşteri hizmetleri', 'müşteri hizmeti', 'konuşabilir miyim', 'görüşebilir miyim', 'iletişime geç', 'iletişime geçebilir miyim', 'müşteri temsilcisine bağla', 'temsilci', 'temsilciye bağla', 'müşteri temsilcisi', 'bağla'];
        foreach ($customerServiceKeywords as $keyword) {
            if (mb_strpos($message, $keyword) !== false) {
                return [
                    'intent' => 'contact_request',
                    'confidence' => 0.95,
                    'message' => $message,
                    'threshold_met' => true
                ];
            }
        }
        
        // Check for order tracking keywords first (special case)
        $orderTrackingKeywords = ['siparişim nerede', 'sipariş durumu', 'sipariş takibi', 'sipariş nerede'];
        foreach ($orderTrackingKeywords as $keyword) {
            if (mb_strpos($message, $keyword) !== false) {
                return [
                    'intent' => 'order_tracking',
                    'confidence' => 0.95,
                    'message' => $message,
                    'threshold_met' => true
                ];
            }
        }
        
        // Check for cargo tracking with number (special case)
        $cargoTrackingKeywords = ['kargom nerede', 'kargo durumu', 'kargo takibi', 'kargo nerede', 'kargom', 'kargo'];
        $hasCargoKeyword = false;
        foreach ($cargoTrackingKeywords as $keyword) {
            if (mb_strpos($message, $keyword) !== false) {
                $hasCargoKeyword = true;
                break;
            }
        }
        
        // Eğer kargo kelimesi varsa ve mesajda numara pattern'i varsa
        if ($hasCargoKeyword) {
            $trackingPatterns = [
                '/(?:YT|TR|TK|KG)[0-9]{8,}/i',
                '/([A-Z]{2}[0-9]{9,})/i',
                '/([0-9]{10,})/i',
                '/(?:kargo|takip|numara|numarası)[\s:]*([A-Z0-9\-]{8,})/i'
            ];
            
            foreach ($trackingPatterns as $pattern) {
                if (preg_match($pattern, $message)) {
                    return [
                        'intent' => 'cargo_tracking_with_number',
                        'confidence' => 0.95,
                        'message' => $message,
                        'threshold_met' => true
                    ];
                }
            }
            
            // Sadece kargo kelimesi varsa normal cargo_tracking
            return [
                'intent' => 'cargo_tracking',
                'confidence' => 0.9,
                'message' => $message,
                'threshold_met' => true
            ];
        }
        
        // Her niyet için güvenilirlik hesapla
        foreach ($this->intents as $intentName => $intentData) {
            $confidence = $this->calculateAdvancedConfidence($message, $intentData['keywords']);
            
            if ($confidence > $highestConfidence) {
                $highestConfidence = $confidence;
                $bestIntent = $intentName;
            }
        }
        
        // Eşik değeri kontrolü - daha esnek
        $threshold = $this->intents[$bestIntent]['confidence_threshold'] ?? 0.25;
        
        if ($highestConfidence >= $threshold) {
            return [
                'intent' => $bestIntent,
                'confidence' => $highestConfidence,
                'message' => $message,
                'threshold_met' => true
            ];
        } else {
            // Eşik değeri karşılanmazsa, en yakın niyeti döndür ama düşük güvenilirlikle
            return [
                'intent' => $bestIntent ?? 'unknown',
                'confidence' => $highestConfidence,
                'message' => $message,
                'threshold_met' => false,
                'closest_intent' => $bestIntent
            ];
        }
    }
    
    private function calculateAdvancedConfidence($message, $keywords) {
        $scores = [
            'keyword_match' => 0,      // 0-2 puan
            'context_analysis' => 0,   // 0-1 puan
            'semantic_analysis' => 0,  // 0-1 puan
            'pattern_matching' => 0,   // 0-1 puan
            'special_rules' => 0,      // 0-2 puan
            'negative_penalty' => 0    // Negatif puanlama
        ];
        
        $keywordCount = 0;
        $totalKeywords = count($keywords);
        $message = mb_strtolower($message, 'UTF-8');
        
        // 1. Doğrudan anahtar kelime eşleşmesi (0-2 puan)
        foreach ($keywords as $keyword) {
            if (mb_strpos($message, $keyword) !== false) {
                $scores['keyword_match'] += 1.0;
                $keywordCount++;
            }
        }
        
        // Eşleşen kelime sayısına göre bonus
        if ($keywordCount > 1) {
            $scores['keyword_match'] += 0.5; // Çoklu eşleşme bonusu
        }
        if ($keywordCount > 2) {
            $scores['keyword_match'] += 0.5; // Üçlü eşleşme bonusu
        }
        
        // 2. Thesaurus ile genişletilmiş arama (0-1 puan)
        foreach ($this->thesaurus as $mainWord => $synonyms) {
            if (mb_strpos($message, $mainWord) !== false) {
                foreach ($synonyms as $synonym) {
                    if (mb_strpos($message, $synonym) !== false) {
                        $scores['context_analysis'] += 0.3; // Eşanlamlı kelime bonusu
                    }
                }
            }
        }
        
        // 3. Kısmi kelime eşleşmesi ve semantic analiz (0-1 puan)
        $words = explode(' ', $message);
        foreach ($words as $word) {
            foreach ($keywords as $keyword) {
                $similarity = $this->calculateSimilarity($word, $keyword);
                if ($similarity > 0.7) { // %70 benzerlik eşiği
                    $scores['semantic_analysis'] += $similarity * 0.4;
                }
            }
        }
        
        // 4. Pattern matching - özel kalıplar (0-1 puan)
        $scores['pattern_matching'] = $this->analyzePatterns($message, $keywords);
        
        // 5. Özel kurallar ve context analizi (0-2 puan)
        $scores['special_rules'] = $this->applySpecialRules($message, $keywords);
        
        // 6. Negatif puanlama - yanlış intent cezası
        $scores['negative_penalty'] = $this->calculateNegativePenalty($message, $keywords);
        
        // Toplam puanı hesapla
        $totalScore = array_sum($scores);
        
        // Mesaj uzunluğu ve anahtar kelime yoğunluğu bonusu
        if ($keywordCount > 0) {
            $densityBonus = min($keywordCount / count($words), 1.0) * 0.2;
            $totalScore += $densityBonus;
        }
        
        return min(max($totalScore, 0), 1.0); // 0-1 arasında sınırla
    }
    
    private function analyzePatterns($message, $keywords) {
        $score = 0;
        
        // Rastgele ürün önerisi için özel handling
        $randomPatterns = ['rastgele', 'random', 'herhangi bir', 'ne olursa olsun', 'fark etmez'];
        foreach ($randomPatterns as $pattern) {
            if (mb_strpos($message, $pattern) !== false) {
                $score += 0.8; // Yüksek bonus rastgele kelimeler için
            }
        }
        
        // Soru kalıpları
        $questionPatterns = ['ne', 'nasıl', 'hangi', 'kaç', 'nerede', 'ne zaman', 'kim'];
        foreach ($questionPatterns as $pattern) {
            if (mb_strpos($message, $pattern) !== false) {
                $score += 0.2;
            }
        }
        
        // Arama kalıpları (rastgele kelimesi yoksa)
        $hasRandomPattern = false;
        foreach ($randomPatterns as $pattern) {
            if (mb_strpos($message, $pattern) !== false) {
                $hasRandomPattern = true;
                break;
            }
        }
        
        if (!$hasRandomPattern) {
            $searchPatterns = ['ara', 'bul', 'göster', 'listele', 'getir', 'ver'];
            foreach ($searchPatterns as $pattern) {
                if (mb_strpos($message, $pattern) !== false) {
                    $score += 0.3;
                }
            }
        }
        
        // Fiyat kalıpları
        $pricePatterns = ['fiyat', 'para', 'ücret', 'maliyet', 'kaç lira', 'ne kadar'];
        foreach ($pricePatterns as $pattern) {
            if (mb_strpos($message, $pattern) !== false) {
                $score += 0.2;
            }
        }
        
        return min($score, 1.0);
    }
    
    private function applySpecialRules($message, $keywords) {
        $score = 0;
        
        // Ürün arama için özel kurallar
        if (in_array('product_search', $keywords)) {
            // "bilgisayar parçası" gibi genel terimler
            $productTerms = ['parça', 'bileşen', 'aksesuar', 'malzeme', 'ürün', 'eşya'];
            foreach ($productTerms as $term) {
                if (mb_strpos($message, $term) !== false) {
                    $score += 0.5;
                }
            }
            
            // Teknik terimler
            $techTerms = ['işlemci', 'ram', 'ekran kartı', 'anakart', 'hard disk', 'ssd'];
            foreach ($techTerms as $term) {
                if (mb_strpos($message, $term) !== false) {
                    $score += 0.8;
                }
            }
        }
        
        // Marka arama için özel kurallar
        if (in_array('brand_search', $keywords)) {
            // Marka isimleri
            $brandNames = ['samsung', 'apple', 'nike', 'adidas', 'sony', 'lg', 'dell', 'hp'];
            foreach ($brandNames as $brand) {
                if (mb_strpos($message, $brand) !== false) {
                    $score += 0.6;
                }
            }
        }
        
        // Fiyat sorgusu için özel kurallar
        if (in_array('price_inquiry', $keywords)) {
            // Fiyat belirteçleri
            $priceIndicators = ['fiyat', 'para', 'ücret', 'maliyet', 'kaç lira', 'ne kadar', 'pahalı', 'ucuz'];
            foreach ($priceIndicators as $indicator) {
                if (mb_strpos($message, $indicator) !== false) {
                    $score += 0.4;
                }
            }
        }
        
        return min($score, 2.0);
    }
    
    private function calculateNegativePenalty($message, $keywords) {
        $penalty = 0;
        
        // Yanlış intent'i cezalandır
        $negativePatterns = [
            'product_search' => ['marka', 'firma', 'şirket'], // Marka arama product_search'ü cezalandırır
            'brand_search' => ['fiyat', 'para', 'ücret'], // Fiyat sorgusu brand_search'ü cezalandırır
            'price_inquiry' => ['marka', 'firma', 'şirket'] // Marka arama price_inquiry'yi cezalandırır
        ];
        
        foreach ($negativePatterns as $intent => $patterns) {
            if (in_array($intent, $keywords)) {
                foreach ($patterns as $pattern) {
                    if (mb_strpos($message, $pattern) !== false) {
                        $penalty -= 0.3; // Negatif puan
                    }
                }
            }
        }
        
        return $penalty;
    }
    
    private function calculateSimilarity($str1, $str2) {
        $str1 = mb_strtolower($str1, 'UTF-8');
        $str2 = mb_strtolower($str2, 'UTF-8');
        
        // Levenshtein mesafesi hesapla
        $lev = levenshtein($str1, $str2);
        $maxLen = max(mb_strlen($str1), mb_strlen($str2));
        
        if ($maxLen === 0) return 1.0;
        
        return 1 - ($lev / $maxLen);
    }
    
    public function generateResponse($detectedIntent, $originalMessage) {
        $intent = $detectedIntent['intent'];
        $confidence = $detectedIntent['confidence'];
        $thresholdMet = $detectedIntent['threshold_met'] ?? true;
        
        // Eşik değeri karşılanmazsa ama en yakın niyet varsa, o niyeti kullan
        if (!$thresholdMet && $detectedIntent['closest_intent'] && $confidence > 0.15) {
            $intent = $detectedIntent['closest_intent'];
            $thresholdMet = true;
        }
        
        // Bilinmeyen niyet durumunda akıllı tahmin yap
        if ($intent === 'unknown' || !$thresholdMet) {
            return $this->handleUnknownIntent($originalMessage, $confidence);
        }
        
        // Niyete göre özel yanıtlar
        switch ($intent) {
            case 'product_search':
                return $this->handleProductSearch($originalMessage);
                
            case 'price_inquiry':
                return $this->handlePriceInquiry($originalMessage);
                
            case 'category_browse':
                return $this->handleCategoryBrowse($originalMessage);
                
            case 'brand_search':
                return $this->handleBrandSearch($originalMessage);
                
            case 'stock_inquiry':
                return $this->handleStockInquiry($originalMessage);
                
            case 'recommendation':
                // Redirect to order_inquiry since recommendation is now part of it
                return $this->handleOrderInquiry($originalMessage);
                
            case 'comparison':
                return $this->handleComparison($originalMessage);
                
            case 'cart_add':
                
            case 'cart_remove':
                return $this->handleCartRemove($originalMessage);
                
            case 'cart_view':
                return $this->handleCartView($originalMessage);
                
            case 'order_inquiry':
                return $this->handleOrderInquiry($originalMessage);
                
            case 'order_tracking':
                return $this->handleOrderTracking($originalMessage);
                
            // === FUNNEL INTENT HANDLERS ===
            case 'capabilities_inquiry':
                return $this->handleCapabilitiesInquiry($originalMessage);
                
            case 'project_info':
                return $this->handleProjectInfo($originalMessage);
                
            case 'conversion_guidance':
                return $this->handleConversionGuidance($originalMessage);
                
            case 'pricing_guidance':
                return $this->handlePricingGuidance($originalMessage);
                
            case 'demo_request':
                return $this->handleDemoRequest($originalMessage);
                
            case 'contact_request':
                return $this->handleContactRequest($originalMessage);
                
            case 'product_recommendations':
                return $this->handleProductRecommendations($originalMessage);
                
            case 'specific_product_recommendation':
                return $this->handleSpecificProductRecommendation($originalMessage);
                
            default:
                return [
                    'intent' => $intent,
                    'confidence' => $confidence,
                    'response' => $this->intents[$intent]['response'],
                    'products' => null,
                    'suggestions' => $this->getSuggestionsForIntent($intent),
                    'threshold_met' => $thresholdMet
                ];
        }
    }
    
    public function generateResponseWithContext($detectedIntent, $originalMessage, $chatSession) {
        $intent = $detectedIntent['intent'];
        $confidence = $detectedIntent['confidence'];
        $thresholdMet = $detectedIntent['threshold_met'] ?? true;
        
        // Session context'i kontrol et
        $context = $chatSession->getConversationContext();
        $lastIntent = $chatSession->getLastIntent();
        $lastProducts = $chatSession->getLastProducts();
        
        // Context-aware yanıt oluştur
        if ($intent === 'product_search' && $context['current_category']) {
            return $this->handleContextualProductSearch($originalMessage, $context);
        }
        
        if ($intent === 'order_inquiry' && $context['current_category']) {
            // Check if this is a recommendation request
            $message = mb_strtolower($originalMessage, 'UTF-8');
            $recommendationKeywords = ['öner', 'tavsiye', 'en iyi', 'popüler', 'trend', 'yeni', 'güncel', 'öneri', 'tavsiye et', 'ne alayım'];
            $isRecommendationRequest = false;
            
            foreach ($recommendationKeywords as $keyword) {
                if (mb_strpos($message, $keyword) !== false) {
                    $isRecommendationRequest = true;
                    break;
                }
            }
            
            if ($isRecommendationRequest) {
                return $this->handleContextualRecommendation($originalMessage, $context);
            }
        }
        
        if ($intent === 'comparison' && !empty($lastProducts)) {
            return $this->handleContextualComparison($originalMessage, $context, $lastProducts);
        }
        
        // Normal yanıt oluştur
        return $this->generateResponse($detectedIntent, $originalMessage);
    }
    
    private function handleUnknownIntent($message, $confidence) {
        // Mesaj içeriğine göre akıllı tahmin yap
        $suggestedIntent = $this->suggestIntentFromContext($message);
        
        if ($suggestedIntent) {
            return [
                'intent' => 'suggested_' . $suggestedIntent,
                'confidence' => $confidence,
                'response' => 'Mesajınızı tam anlayamadım ama muhtemelen ' . $this->getIntentDescription($suggestedIntent) . ' istiyorsunuz. Size yardımcı olayım mı?',
                'products' => null,
                'suggestions' => $this->getSuggestionsForIntent($suggestedIntent),
                'suggested_intent' => $suggestedIntent,
                'help_text' => 'Daha net yazarsanız size daha iyi yardımcı olabilirim.'
            ];
        }
        
        return [
            'intent' => 'unknown',
            'confidence' => $confidence,
            'response' => 'Mesajınızı tam olarak anlayamadım. Size nasıl yardımcı olabilirim?',
            'products' => null,
            'suggestions' => ['Ürün ara', 'Kategori göster', 'Fiyat öğren', 'Yardım al'],
            'help_text' => 'Örnek: "iPhone ara", "Elektronik kategorisi", "Nike fiyatı" gibi yazabilirsiniz.'
        ];
    }
    
    private function suggestIntentFromContext($message) {
        $message = mb_strtolower($message, 'UTF-8');
        
        // Ürün isimleri varsa arama niyeti
        $productKeywords = ['iphone', 'samsung', 'nike', 'adidas', 'apple', 'macbook', 'ipad', 'playstation', 'xbox'];
        foreach ($productKeywords as $product) {
            if (mb_strpos($message, $product) !== false) {
                return 'product_search';
            }
        }
        
        // Fiyat kelimeleri varsa fiyat sorgulama
        $priceKeywords = ['para', 'tl', 'lira', 'kuruş', 'pahalı', 'ucuz'];
        foreach ($priceKeywords as $price) {
            if (mb_strpos($message, $price) !== false) {
                return 'price_inquiry';
            }
        }
        
        // Kategori kelimeleri varsa kategori tarama
        $categoryKeywords = ['elektronik', 'giyim', 'ev', 'spor', 'kitap'];
        foreach ($categoryKeywords as $category) {
            if (mb_strpos($message, $category) !== false) {
                return 'category_browse';
            }
        }
        
        return null;
    }
    
    private function getIntentDescription($intent) {
        $descriptions = [
            'product_search' => 'ürün arama',
            'price_inquiry' => 'fiyat sorgulama',
            'category_browse' => 'kategori tarama',
            'brand_search' => 'marka arama',
            'stock_inquiry' => 'stok sorgulama',
            'order_inquiry' => 'sipariş sorgulama ve ürün önerisi',
            'comparison' => 'ürün karşılaştırma'
        ];
        
        return $descriptions[$intent] ?? 'yardım';
    }
    
    private function getSuggestionsForIntent($intent) {
        $suggestions = [
            'greeting' => ['Ürün ara', 'Kategori göster', 'Fiyat öğren'],
            'help' => ['Ürün ara', 'Kategori göster', 'Marka ara', 'Stok kontrolü'],
            'goodbye' => ['Tekrar görüşmek üzere!', 'Başka bir sorunuz olursa yardımcı olurum.']
        ];
        
        return $suggestions[$intent] ?? ['Ürün ara', 'Kategori göster', 'Yardım al'];
    }
    
    private function handleProductSearch($message) {
        // Knowledge base'den ürün arama
        $products = $this->getSampleProducts(null, 5);
        
        return [
            'intent' => 'product_search',
            'confidence' => 0.8,
            'response' => 'Arama sonuçlarınız: ' . count($products) . ' ürün bulundu. İşte en uygun sonuçlar:',
            'products' => $products,
            'total_found' => count($products),
            'suggestions' => ['Fiyata göre sırala', 'Markaya göre filtrele', 'Kategoriye göre filtrele']
        ];
    }
    
    private function handlePriceInquiry($message) {
        // Knowledge base'den fiyat sorgulama
        $products = $this->getSampleProducts(null, 3);
        
        $priceInfo = [];
        foreach ($products as $product) {
            $priceInfo[] = $product['name'] . ': ' . number_format($product['price'], 2) . ' TL';
        }
        
        return [
            'intent' => 'price_inquiry',
            'confidence' => 0.85,
            'response' => 'Fiyat bilgileri: ' . implode(', ', $priceInfo),
            'products' => $products,
            'suggestions' => ['Benzer ürünleri göster', 'Fiyat aralığı belirle', 'Kategoriye göre ara']
        ];
    }
    
    private function handleCategoryBrowse($message) {
        $messageLower = mb_strtolower($message, 'UTF-8');
        
        // Kategori önerisi isteniyorsa
        if (mb_strpos($messageLower, 'kategori') !== false && 
            mb_strpos($messageLower, 'öner') !== false) {
            
            $categoryRecommendations = $this->getSampleCategoryRecommendations();
            
            return [
                'intent' => 'category_recommendation',
                'confidence' => 0.95,
                'response' => 'Ürün kategorilerimizi analiz ettim! İşte size özel kategori önerileri:',
                'category_analysis' => [
                    'total_categories' => count($categoryRecommendations),
                    'recommendations' => $categoryRecommendations
                ],
                'suggestions' => [
                    'Kategori detayı göster',
                    'Fiyat aralığı belirle',
                    'En popüler kategoriler',
                    'Yeni kategoriler keşfet'
                ]
            ];
        }
        
        // Spesifik kategori önerisi isteniyorsa (örn: "Elektronik ürünler öner")
        $categoryKeywords = [
            'elektronik' => ['telefon', 'bilgisayar', 'tablet', 'kulaklık', 'televizyon', 'oyun'],
            'giyim' => ['elbise', 'pantolon', 'gömlek', 'ayakkabı', 'ceket', 'hırka'],
            'ev' => ['mobilya', 'dekorasyon', 'mutfak', 'banyo', 'aydınlatma'],
            'spor' => ['fitness', 'koşu', 'yürüyüş', 'bisiklet', 'yüzme', 'futbol'],
            'kitap' => ['roman', 'bilim', 'tarih', 'çocuk', 'ders', 'eğitim'],
            'kozmetik' => ['makyaj', 'cilt bakım', 'parfüm', 'saç bakım', 'güzellik']
        ];
        
        foreach ($categoryKeywords as $categoryName => $keywords) {
            if (mb_strpos($messageLower, $categoryName) !== false && 
                mb_strpos($messageLower, 'öner') !== false) {
                
                $products = $this->getSampleProductsByCategory($categoryName, $keywords);
                
                return [
                    'intent' => 'category_recommendation',
                    'confidence' => 0.9,
                    'response' => "{$categoryName} kategorisinde size özel ürün önerileri:",
                    'products' => $products,
                    'category' => $categoryName,
                    'suggestions' => [
                        'Farklı kategori öner',
                        'Fiyat aralığı belirle',
                        'Marka önerisi al',
                        'En popüler ürünler'
                    ]
                ];
            }
        }
        
        // Belirli bir kategori aranıyorsa
        $categories = ['Telefon', 'Bilgisayar', 'Giyim', 'Ev & Yaşam', 'Spor', 'Kozmetik', 'Kitap', 'Otomotiv', 'Sağlık', 'Bahçe', 'Pet Shop'];
        $category = $this->extractCategoryFromMessage($message, $categories);
        
        if ($category) {
            $categoryDetails = $this->getSampleCategoryDetails($category);
            
            if ($categoryDetails) {
                $limitedProducts = array_slice($categoryDetails['all_products'], 0, 5);
                
                return [
                    'intent' => 'category_browse',
                    'confidence' => 0.9,
                    'response' => "📊 **{$category}** kategorisi analizi:\n\n" .
                                 "• Toplam ürün: {$categoryDetails['summary']['product_count']}\n" .
                                 "• Ortalama fiyat: ₺{$categoryDetails['summary']['avg_price']}\n" .
                                 "• Ortalama puan: {$categoryDetails['summary']['avg_rating']}/5\n" .
                                 "• Pazar payı: %{$categoryDetails['summary']['market_share']}\n\n" .
                                 "İşte öne çıkan ürünler:",
                    'products' => $limitedProducts,
                    'category' => $category,
                    'category_analysis' => $categoryDetails,
                    'suggestions' => [
                        'Fiyata göre sırala',
                        'Markaya göre filtrele',
                        'En yüksek puanlılar',
                        'Başka kategori göster'
                    ]
                ];
            }
        }
        
        // Genel kategori listesi
        $categoryRecommendations = $this->getSampleCategoryRecommendations();
        
        return [
            'intent' => 'category_browse',
            'confidence' => 0.9,
            'response' => '🛍️ **Mevcut kategorilerimiz:**\n\n' . 
                          $this->formatCategoryList($categoryRecommendations) . 
                          '\n\nHangi kategoriyi detaylı incelemek istiyorsunuz?',
            'categories' => $categoryRecommendations,
            'suggestions' => [
                'Kategori önerisi al',
                'En popüler kategoriler',
                'Fiyat aralığı belirle',
                'Tüm ürünleri göster'
            ]
        ];
    }
    
    private function handleBrandSearch($message) {
        $brands = ['Apple', 'Samsung', 'Nike', 'Adidas', 'IKEA', 'Bosch', 'Sony', 'LG', 'Dell', 'HP', 'Lenovo'];
        $brand = $this->extractBrandFromMessage($message, $brands);
        
        if ($brand) {
            $products = $this->getSampleProductsByBrand($brand);
            $limitedProducts = array_slice($products, 0, 5);
            
            return [
                'intent' => 'brand_search',
                'confidence' => 0.8,
                'response' => $brand . ' markasında ' . count($products) . ' ürün bulundu. İşte öne çıkan ürünler:',
                'products' => $limitedProducts,
                'brand' => $brand,
                'total_products' => count($products),
                'suggestions' => ['Fiyata göre sırala', 'Kategoriye göre filtrele', 'Başka marka ara']
            ];
        }
        
        return [
            'intent' => 'brand_search',
            'confidence' => 0.8,
            'response' => 'Popüler markalar: ' . implode(', ', $brands),
            'brands' => $brands,
            'suggestions' => ['Marka seç', 'Tüm ürünleri göster', 'Marka karşılaştır']
        ];
    }
    
    private function handleStockInquiry($message) {
        $products = $this->getSampleProducts(null, 3);
        $stockInfo = [];
        
        foreach ($products as $product) {
            $stockStatus = $product['stock'] > 0 ? 'Mevcut (' . $product['stock'] . ' adet)' : 'Tükendi';
            $stockInfo[] = $product['name'] . ': ' . $stockStatus;
        }
        
        return [
            'intent' => 'stock_inquiry',
            'confidence' => 0.75,
            'response' => 'Stok durumu: ' . implode(', ', $stockInfo),
            'products' => $products,
            'suggestions' => ['Benzer ürünleri göster', 'Stokta olanları filtrele', 'Kategoriye göre ara']
        ];
    }
    
    private function handleRecommendation($message) {
        // Debug: Log the message
        
        // Akıllı ürün önerisi sistemi
        try {
            // Fallback recommendation
            $recommendations = $this->getSampleProducts();
            
            if (!empty($recommendations)) {
                return [
                    'intent' => 'smart_recommendation',
                    'confidence' => 0.9,
                    'response' => 'Size özel ürün önerileri',
                    'products' => $recommendations,
                    'recommendation_reason' => 'Örnek ürünler',
                    'category_matched' => false,
                    'suggestions' => ['Farklı kategori öner', 'Fiyat aralığı belirle', 'Marka önerisi al']
                ];
            }
        } catch (\Exception $e) {
            Log::error('Smart recommendation error: ' . $e->getMessage());
        }
        
        // Fallback to general recommendation
        return [
            'intent' => 'recommendation',
            'confidence' => 0.8,
            'response' => 'Size en popüler ve yüksek puanlı ürünleri öneriyorum:',
            'products' => $this->getSampleProducts(),
            'suggestions' => ['Farklı kategori öner', 'Fiyat aralığı belirle', 'Marka önerisi al']
        ];
    }
    
    private function handleComparison($message) {
        $products = $this->getSampleProducts(null, 2);
        
        if (count($products) >= 2) {
            $comparison = [];
            foreach ($products as $product) {
                $comparison[] = $product['name'] . ' - ' . number_format($product['price'], 2) . ' TL - Puan: ' . $product['rating'] . '/5';
            }
            
            return [
                'intent' => 'comparison',
                'confidence' => 0.7,
                'response' => 'Ürün karşılaştırması: ' . implode(' vs ', $comparison),
                'products' => $products,
                'suggestions' => ['Detaylı karşılaştır', 'Başka ürünler karşılaştır', 'Fiyat analizi']
            ];
        }
        
        return [
            'intent' => 'comparison',
            'confidence' => 0.7,
            'response' => 'Karşılaştırma için en az 2 ürün gerekli. Lütfen karşılaştırmak istediğiniz ürünleri belirtin.',
            'products' => null,
            'suggestions' => ['Ürün ara', 'Kategori göster', 'Marka seç']
        ];
    }
    
    private function handleCartAddIntent($message) {
        // Ürün numarası veya adından ürünü bul
        $productInfo = $this->extractProductFromMessage($message);
        
        if (!$productInfo) {
            return [
                'intent' => 'cart_add',
                'confidence' => 0.8,
                'response' => 'Hangi ürünü sepete eklemek istiyorsunuz? Ürün numarası veya adını belirtebilir misiniz?',
                'products' => null,
                'suggestions' => ['Ürün listesini göster', 'Kategoriye göre ara', 'Markaya göre ara']
            ];
        }
        
        // Ürünü sepete ekle (burada gerçek sepet işlemi yapılacak)
        $cartResult = $this->addToCart($productInfo);
        
        return [
            'intent' => 'cart_add',
            'confidence' => 0.9,
            'response' => $cartResult['message'],
            'products' => $cartResult['added_product'],
            'cart_status' => $cartResult['status'],
            'suggestions' => ['Sepetimi göster', 'Başka ürün ekle', 'Alışverişe devam et']
        ];
    }
    
    private function handleCartRemove($message) {
        // Ürün numarası veya adından ürünü bul
        $productInfo = $this->extractProductFromMessage($message);
        
        if (!$productInfo) {
            return [
                'intent' => 'cart_remove',
                'confidence' => 0.8,
                'response' => 'Hangi ürünü sepetten çıkarmak istiyorsunuz? Ürün numarası veya adını belirtebilir misiniz?',
                'products' => null,
                'suggestions' => ['Sepetimi göster', 'Ürün listesini göster', 'Yardım al']
            ];
        }
        
        // Ürünü sepetten çıkar (burada gerçek sepet işlemi yapılacak)
        $cartResult = $this->removeFromCart($productInfo);
        
        return [
            'intent' => 'cart_remove',
            'confidence' => 0.9,
            'response' => $cartResult['message'],
            'removed_product' => $cartResult['removed_product'],
            'cart_status' => $cartResult['status'],
            'suggestions' => ['Sepetimi göster', 'Başka ürün çıkar', 'Alışverişe devam et']
        ];
    }
    
    private function handleCartView($message) {
        // Sepet içeriğini göster (burada gerçek sepet işlemi yapılacak)
        $cartContent = $this->getCartContent();
        
        return [
            'intent' => 'cart_view',
            'confidence' => 0.9,
            'response' => $cartContent['message'],
            'cart_items' => $cartContent['items'],
            'cart_total' => $cartContent['total'],
            'cart_count' => $cartContent['count'],
            'suggestions' => ['Ürün ekle', 'Ürün çıkar', 'Ödeme yap', 'Alışverişe devam et']
        ];
    }
    
    private function handleOrderInquiry($message) {
        $message = mb_strtolower($message, 'UTF-8');
        
        // Check if this is a specific product recommendation request
        $specificProductKeywords = ['tişört', 'gömlek', 'pantolon', 'ayakkabı', 'telefon', 'bilgisayar', 'kırmızı', 'mavi', 'siyah', 'beyaz', 'büyük', 'küçük', 'orta'];
        $hasSpecificProduct = false;
        
        foreach ($specificProductKeywords as $keyword) {
            if (mb_strpos($message, $keyword) !== false) {
                $hasSpecificProduct = true;
                break;
            }
        }
        
        // Check if this is a product recommendation request
        $recommendationKeywords = ['öner', 'tavsiye', 'en iyi', 'popüler', 'trend', 'yeni', 'güncel', 'öneri', 'tavsiye et', 'ne alayım'];
        $isRecommendationRequest = false;
        
        foreach ($recommendationKeywords as $keyword) {
            if (mb_strpos($message, $keyword) !== false) {
                $isRecommendationRequest = true;
                break;
            }
        }
        
        // If it's a specific product with recommendation, do targeted search
        if ($hasSpecificProduct && $isRecommendationRequest) {
            return $this->handleSpecificProductRecommendation($message);
        } elseif ($isRecommendationRequest) {
            // Handle as general product recommendation
            return $this->handleRecommendation($message);
        } else {
            // Check if this is an order tracking request
            $orderTrackingKeywords = ['siparişim nerede', 'sipariş durumu', 'sipariş takibi', 'sipariş nerede', 'siparişim', 'sipariş'];
            $isOrderTrackingRequest = false;
            
            foreach ($orderTrackingKeywords as $keyword) {
                if (mb_strpos($message, $keyword) !== false) {
                    $isOrderTrackingRequest = true;
                    break;
                }
            }
            
            if ($isOrderTrackingRequest) {
                // Return order tracking response
                return [
                    'intent' => 'order_tracking',
                    'order_id' => 'ORD-998877',
                    'status' => 'shipped',
                    'order_date' => '2025-08-15',
                    'items' => [
                        [
                            'product_id' => 101,
                            'name' => 'Sneaker X 42 Numara',
                            'quantity' => 1,
                            'price' => 999
                        ],
                        [
                            'product_id' => 102,
                            'name' => 'Siyah Hoodie',
                            'quantity' => 1,
                            'price' => 499
                        ]
                    ],
                    'shipping' => [
                        'courier' => 'Yurtiçi Kargo',
                        'tracking_number' => 'YT123456789TR',
                        'last_update' => '2025-08-18T14:30:00Z',
                        'location' => 'İstanbul Aktarma Merkezi',
                        'estimated_delivery' => '2025-08-20'
                    ],
                    'message' => 'Siparişiniz kargoya verildi. Takip numaranız: YT123456789TR. Tahmini teslim tarihi 20 Ağustos.',
                    'suggestions' => ['Kargo takip', 'Sipariş geçmişi', 'İletişim', 'Yardım al']
                ];
            } else {
                // Handle as general order inquiry
                return [
                    'intent' => 'order_inquiry',
                    'confidence' => 0.9,
                    'response' => 'Sipariş durumunuzu kontrol ediyorum. Sipariş numaranızı veya müşteri bilgilerinizi girebilir misiniz?',
                    'suggestions' => ['Sipariş numarası gir', 'Müşteri bilgileri göster', 'Sipariş geçmişi göster', 'Yardım al']
                ];
            }
        }
    }
    
    private function handleOrderTracking($message) {
        // Return order tracking response
        return [
            'intent' => 'order_tracking',
            'order_id' => 'ORD-998877',
            'status' => 'shipped',
            'order_date' => '2025-08-15',
            'items' => [
                [
                    'product_id' => 101,
                    'name' => 'Sneaker X 42 Numara',
                    'quantity' => 1,
                    'price' => 999
                ],
                [
                    'product_id' => 102,
                    'name' => 'Siyah Hoodie',
                    'quantity' => 1,
                    'price' => 499
                ]
            ],
            'shipping' => [
                'courier' => 'Yurtiçi Kargo',
                'tracking_number' => 'YT123456789TR',
                'last_update' => '2025-08-18T14:30:00Z',
                'location' => 'İstanbul Aktarma Merkezi',
                'estimated_delivery' => '2025-08-20'
            ],
            'message' => 'Siparişiniz kargoya verildi. Takip numaranız: YT123456789TR. Tahmini teslim tarihi 20 Ağustos.',
            'suggestions' => ['Kargo takip', 'Sipariş geçmişi', 'İletişim', 'Yardım al']
        ];
    }
    
    private function extractProductFromMessage($message) {
        // Ürün numarası ara (örn: "9. ürün", "9 numaralı ürün", "9. ürünü")
        if (preg_match('/(\d+)\s*[\.\s]?\s*(?:numaralı\s+)?ürün(?:ü)?/i', $message, $matches)) {
            $productNumber = (int)$matches[1];
            $product = $this->findProductByNumber($productNumber);
            if ($product) {
                return [
                    'type' => 'by_number',
                    'value' => $productNumber,
                    'product' => $product
                ];
            }
        }
        
        // Ürün adı ara
        foreach ($this->getSampleProducts() as $product) {
            if (stripos($message, $product['name']) !== false) {
                return [
                    'type' => 'by_name',
                    'value' => $product['name'],
                    'product' => $product
                ];
            }
        }
        
        return null;
    }
    
    private function findProductByNumber($number) {
        $products = $this->getSampleProducts();
        
        // Ürün numarası 1'den başlar
        if ($number > 0 && $number <= count($products)) {
            return $products[$number - 1];
        }
        
        return null;
    }
    
    private function addToCart($productInfo) {
        // Burada gerçek sepet işlemi yapılacak
        // Şimdilik simüle ediyoruz
        
        $product = $productInfo['product'];
        
        return [
            'status' => 'success',
            'message' => "✅ {$product['name']} başarıyla sepete eklendi! Fiyat: ₺{$product['price']}",
            'added_product' => $product,
            'cart_total' => $product['price'], // Gerçek sepet toplamı hesaplanacak
            'cart_count' => 1 // Gerçek sepet ürün sayısı hesaplanacak
        ];
    }
    
    private function removeFromCart($productInfo) {
        // Burada gerçek sepet işlemi yapılacak
        // Şimdilik simüle ediyoruz
        
        $product = $productInfo['product'];
        
        return [
            'status' => 'success',
            'message' => "❌ {$product['name']} sepetten çıkarıldı.",
            'removed_product' => $product,
            'cart_total' => 0, // Gerçek sepet toplamı hesaplanacak
            'cart_count' => 0 // Gerçek sepet ürün sayısı hesaplanacak
        ];
    }
    
    private function getCartContent() {
        // Burada gerçek sepet işlemi yapılacak
        // Şimdilik simüle ediyoruz
        
        return [
            'message' => '🛒 Sepetinizde şu anda ürün bulunmuyor.',
            'items' => [],
            'total' => 0,
            'count' => 0
        ];
    }
    
    private function extractCategoryFromMessage($message, $categories) {
        $message = mb_strtolower($message, 'UTF-8');
        $bestMatch = null;
        $bestScore = 0;
        
        foreach ($categories as $category) {
            $categoryLower = mb_strtolower($category, 'UTF-8');
            
            // Tam eşleşme kontrolü
            if (mb_strpos($message, $categoryLower) !== false) {
                $score = 10; // Tam eşleşme için yüksek skor
                
                // Mesajda kategori kelimesi varsa ekstra puan
                if (mb_strpos($message, 'kategori') !== false) {
                    $score += 5;
                }
                
                // Mesajda "göster" kelimesi varsa ekstra puan
                if (mb_strpos($message, 'göster') !== false) {
                    $score += 3;
                }
                
                if ($score > $bestScore) {
                    $bestScore = $score;
                    $bestMatch = $category;
                }
            }
        }
        
        return $bestMatch;
    }
    
    private function extractBrandFromMessage($message, $brands) {
        foreach ($brands as $brand) {
            if (mb_strpos($message, mb_strtolower($brand, 'UTF-8')) !== false) {
                return $brand;
            }
        }
        return null;
    }
    
    private function formatCategoryList($categories) {
        $formatted = '';
        
        foreach ($categories as $index => $category) {
            $emoji = $this->getCategoryEmoji($category['category']);
            $formatted .= ($index + 1) . ". {$emoji} **{$category['category']}** ";
            $formatted .= "({$category['product_count']} ürün) ";
            $formatted .= "• Ort. ₺{$category['avg_price']} ";
            $formatted .= "• ⭐ {$category['avg_rating']}/5\n";
        }
        
        return $formatted;
    }
    
    private function getCategoryEmoji($category) {
        $emojis = [
            'Telefon' => '📱',
            'Bilgisayar' => '💻',
            'Tablet' => '📱',
            'Kulaklık' => '🎧',
            'Televizyon' => '📺',
            'Oyun Konsolu' => '🎮',
            'Spor Ayakkabı' => '👟',
            'Kot Pantolon' => '👖',
            'Polo Yaka' => '👕',
            'Ceket' => '🧥',
            'Elbise' => '👗',
            'Gömlek' => '👔',
            'Sweatshirt' => '🧥',
            'Etek' => '👗',
            'Çanta' => '👜',
            'Mobilya' => '🪑',
            'Aydınlatma' => '💡',
            'Elektrikli Süpürge' => '🧹',
            'Beyaz Eşya' => '🏠',
            'Mutfak' => '🍳',
            'Bisiklet' => '🚲',
            'Mont' => '🧥',
            'Hırka' => '🧥',
            'Spor Çanta' => '🎒',
            'Spor Çorap' => '🧦',
            'Şort' => '🩳',
            'Spor Tshirt' => '👕',
            'Spor Pantolon' => '👖',
            'Şampuan' => '🧴',
            'Yüz Bakımı' => '🧴',
            'Nemlendirici' => '🧴',
            'Makyaj' => '💄',
            'Güneş Bakımı' => '☀️',
            'Serum' => '🧴',
            'Kitap' => '📚',
            'Oyuncak' => '🧸',
            'Oyun' => '🎲',
            'Lastik' => '🚗',
            'Akü' => '🔋',
            'Motor Yağı' => '🛢️',
            'Ağrı Kesici' => '💊',
            'Vitamin' => '💊',
            'Mineral' => '💊',
            'Bahçe Aleti' => '🌱',
            'Bahçe Makinesi' => '🪚',
            'El Aleti' => '🔧',
            'Kedi Maması' => '🐱',
            'Köpek Maması' => '🐕'
        ];
        
        return $emojis[$category] ?? '📦';
    }
    
    /**
     * Knowledge base'den kategori önerilerini çeker
     */
    private function getSampleCategoryRecommendations() {
        try {
            $chunks = KnowledgeChunk::where('content_type', 'product')
                ->with('knowledgeBase')
                ->get();
            
            $categories = [];
            foreach ($chunks as $chunk) {
                // Chunk content'ini JSON olarak parse et
                $productData = json_decode($chunk->content, true);

                if (!$productData) {
                    continue; // JSON parse edilemezse skip et
                }

                $category = $productData['category'] ?? 'Genel';
                
                if (!isset($categories[$category])) {
                    $categories[$category] = [
                        'category' => $category,
                        'product_count' => 0,
                        'total_price' => 0,
                        'total_rating' => 0
                    ];
                }
                
                $categories[$category]['product_count']++;
                $categories[$category]['total_price'] += $productData['price'] ?? 0;
                $categories[$category]['total_rating'] += $productData['rating']['rate'] ?? 4.0;
            }
            
            // Kategorileri işle ve sırala
            $recommendations = [];
            foreach ($categories as $category) {
                $recommendations[] = [
                    'category' => $category['category'],
                    'product_count' => $category['product_count'],
                    'avg_price' => $category['product_count'] > 0 ? round($category['total_price'] / $category['product_count'], 2) : 0,
                    'avg_rating' => $category['product_count'] > 0 ? round($category['total_rating'] / $category['product_count'], 1) : 4.0
                ];
            }
            
            // Ürün sayısına göre sırala
            usort($recommendations, function($a, $b) {
                return $b['product_count'] <=> $a['product_count'];
            });
            
            return array_slice($recommendations, 0, 8);
            
        } catch (\Exception $e) {
            Log::error('Knowledge base category recommendations fetch error: ' . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Knowledge base'den kategori detaylarını çeker
     */
    private function getSampleCategoryDetails($category) {
        try {
            $chunks = KnowledgeChunk::where('content_type', 'product')
                ->where('content', 'like', '%' . $category . '%')
                ->with('knowledgeBase')
                ->get();
            
            if ($chunks->isEmpty()) {
                return null;
            }
            
            $products = [];
            $totalPrice = 0;
            $totalRating = 0;
            
            foreach ($chunks as $chunk) {
                // Chunk content'ini JSON olarak parse et
                $productData = json_decode($chunk->content, true);

                if (!$productData) {
                    continue; // JSON parse edilemezse skip et
                }
                
                $product = [
                    'id' => $productData['id'] ?? $chunk->id,
                    'name' => $productData['title'] ?? 'Ürün ' . $chunk->id,
                    'price' => $productData['price'] ?? 0,
                    'rating' => $productData['rating']['rate'] ?? 4.0,
                    'brand' => $productData['brand'] ?? 'Bilinmeyen',
                    'stock' => 10, // Default stock
                    'image' => ProductImageHelper::getImageWithFallback($productData['image'] ?? null),
                    'description' => $productData['description'] ?? substr($chunk->content, 0, 200) . '...',
                    'source' => 'knowledge_base'
                ];
                
                $products[] = $product;
                $totalPrice += $product['price'];
                $totalRating += $product['rating'];
            }
            
            return [
                'summary' => [
                    'product_count' => count($products),
                    'avg_price' => count($products) > 0 ? round($totalPrice / count($products), 2) : 0,
                    'avg_rating' => count($products) > 0 ? round($totalRating / count($products), 1) : 4.0,
                    'market_share' => 15 // Bu değer hesaplanabilir
                ],
                'all_products' => $products
            ];
            
        } catch (\Exception $e) {
            Log::error('Knowledge base category details fetch error: ' . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Knowledge base'den ürünleri çeker (ConvStateAPI'deki getRandomProductsFromKnowledgeBase ile aynı mantık)
     */
    private function getSampleProducts($category = null, $limit = 10) {
        try {
            $query = KnowledgeChunk::where('content_type', 'product');
            
            if ($category) {
                $query->where('content', 'like', '%' . $category . '%');
            }
            
            $chunks = $query->with('knowledgeBase')->inRandomOrder()->limit($limit)->get();
            
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
     * Knowledge base'den markaya göre ürünleri çeker
     */
    private function getSampleProductsByBrand($brand) {
        try {
            $chunks = KnowledgeChunk::where('content_type', 'product')
                ->where('content', 'like', '%' . $brand . '%')
                ->with('knowledgeBase')
                ->limit(10)
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
                    'product_url' => 'https://example.com/product/' . ($productData['id'] ?? $chunk->id) . '?intent=recommendation',
                    'source' => 'knowledge_base'
                ];
            }
            
            return $products;
            
        } catch (\Exception $e) {
            Log::error('Knowledge base brand product fetch error: ' . $e->getMessage());
            return [];
        }
    }
    
    private function handleContextualProductSearch($message, $context) {
        $category = $context['current_category'];
        $brand = $context['current_brand'];
        
        $searchQuery = $message;
        if ($brand) {
            $searchQuery .= " " . $brand;
        }
        
        // Kategoriye göre ürünleri çek
        $searchResults = $this->getSampleProducts($category, 10);
        
        // Markaya göre filtrele
        if ($brand) {
            $searchResults = array_filter($searchResults, function($product) use ($brand) {
                return strtolower($product['brand']) === strtolower($brand);
            });
        }
        
        $limitedResults = array_slice($searchResults, 0, 5);
        
        return [
            'intent' => 'contextual_product_search',
            'confidence' => 0.9,
            'response' => $category . ' kategorisinde ' . $searchQuery . ' için ' . count($searchResults) . ' ürün bulundu. İşte en uygun sonuçlar:',
            'products' => $limitedResults,
            'total_found' => count($searchResults),
            'context_used' => [
                'category' => $category,
                'brand' => $brand,
                'search_query' => $searchQuery
            ],
            'suggestions' => ['Fiyata göre sırala', 'Markaya göre filtrele', 'Başka kategori ara']
        ];
    }
    
    private function handleContextualRecommendation($message, $context) {
        $category = $context['current_category'];
        $brand = $context['current_brand'];
        
        if ($category) {
            $products = $this->getSampleProducts($category, 10);
            if ($brand) {
                $products = array_filter($products, function($product) use ($brand) {
                    return strtolower($product['brand']) === strtolower($brand);
                });
            }
            
            // En yüksek puanlı ürünleri seç
            usort($products, function($a, $b) {
                return $b['rating'] <=> $a['rating'];
            });
            
            $topProducts = array_slice($products, 0, 5);
            
            return [
                'intent' => 'contextual_recommendation',
                'confidence' => 0.9,
                'response' => $category . ($brand ? ' kategorisinde ' . $brand . ' markasından ' : ' kategorisinde ') . 'en iyi ürünleri öneriyorum:',
                'products' => $topProducts,
                'context_used' => [
                    'category' => $category,
                    'brand' => $brand
                ],
                'suggestions' => ['Farklı marka öner', 'Fiyat aralığı belirle', 'Başka kategori öner']
            ];
        }
        
        // Context yoksa normal öneri
        return $this->handleRecommendation($message);
    }
    
    private function handleContextualComparison($message, $context, $lastProducts) {
        if (count($lastProducts) >= 2) {
            $comparison = [];
            foreach (array_slice($lastProducts, 0, 2) as $product) {
                $comparison[] = $product['name'] . ' - ' . number_format($product['price'], 2) . ' TL - Puan: ' . $product['rating'] . '/5';
            }
            
            return [
                'intent' => 'contextual_comparison',
                'confidence' => 0.9,
                'response' => 'Son konuştuğumuz ürünleri karşılaştırıyorum: ' . implode(' vs ', $comparison),
                'products' => array_slice($lastProducts, 0, 2),
                'context_used' => [
                    'last_products' => count($lastProducts),
                    'category' => $context['current_category'],
                    'brand' => $context['current_brand']
                ],
                'suggestions' => ['Detaylı karşılaştır', 'Başka ürünler karşılaştır', 'Fiyat analizi']
            ];
        }
        
        return $this->handleComparison($message);
    }
    
    // === FUNNEL INTENT HANDLER METHODS ===
    
    /**
     * Yetenekler ve hizmetler hakkında bilgi ver
     */
    private function handleCapabilitiesInquiry($message) {
        $capabilities = [
            '🔍 **Ürün Arama & Keşif**',
            '• 15+ kategoride 500+ ürün arasında arama',
            '• Marka, fiyat, özellik bazlı filtreleme',
            '• Akıllı ürün önerileri',
            '',
            '💰 **Fiyat & Stok Bilgisi**',
            '• Anlık fiyat sorgulama',
            '• Stok durumu kontrolü',
            '• Fiyat karşılaştırması',
            '',
            '📦 **Sipariş & Kargo**',
            '• Sipariş takibi',
            '• Kargo durumu sorgulama',
            '• Sipariş geçmişi',
            '',
            '🎯 **Kişisel Öneriler**',
            '• Geçmiş alışverişlerinize göre öneriler',
            '• Favori kategorilerinizden ürünler',
            '• Size özel kampanyalar'
        ];
        
        return [
            'intent' => 'capabilities_inquiry',
            'confidence' => 0.9,
            'response' => 'Size şu konularda yardımcı olabilirim:',
            'capabilities' => $capabilities,
            'suggestions' => [
                'Ürün ara',
                'Fiyat öğren',
                'Kategori göster',
                'Demo talep et',
                'İletişime geç'
            ]
        ];
    }
    
    /**
     * Proje hakkında bilgi ver
     */
    private function handleProjectInfo($message) {
        return [
            'intent' => 'project_info',
            'confidence' => 0.9,
            'response' => 'Bu proje, ziyaretçilere akıllı ürün önerileri ve müşteri deneyimi sunan bir AI destekli chat widget sistemidir.',
            'project_details' => [
                'name' => 'Conv State AI Projesi',
                'description' => 'Müşteri kazanma odaklı akıllı chat sistemi',
                'features' => [
                    'Gerçek zamanlı ürün arama',
                    'Kişiselleştirilmiş öneriler',
                    'Sipariş takip sistemi',
                    'Çok dilli destek'
                ],
                'benefits' => [
                    'Müşteri memnuniyeti artışı',
                    'Satış dönüşüm oranı yükseltme',
                    '7/24 müşteri desteği',
                    'Otomatik müşteri yönlendirme'
                ]
            ],
            'suggestions' => [
                'Yeteneklerimi öğren',
                'Demo talep et',
                'Fiyat bilgisi al',
                'İletişime geç'
            ]
        ];
    }
    
    /**
     * Dönüşüm süreci için rehberlik et
     */
    private function handleConversionGuidance($message) {
        $steps = [
            '1️⃣ **İhtiyaç Analizi**',
            '• Hangi ürün/hizmete ihtiyacınız var?',
            '• Bütçe aralığınız nedir?',
            '• Öncelikleriniz neler?',
            '',
            '2️⃣ **Ürün Keşfi**',
            '• Kategorilere göz atın',
            '• Ürünleri karşılaştırın',
            '• Detaylı bilgi alın',
            '',
            '3️⃣ **Karar Verme**',
            '• Fiyat analizi yapın',
            '• Müşteri yorumlarını inceleyin',
            '• Teknik özellikleri değerlendirin',
            '',
            '4️⃣ **Satın Alma**',
            '• Sepete ekleyin',
            '• Ödeme bilgilerini girin',
            '• Siparişi tamamlayın'
        ];
        
        return [
            'intent' => 'conversion_guidance',
            'confidence' => 0.9,
            'response' => 'Size adım adım rehberlik ediyorum. İşte süreç:',
            'guidance_steps' => $steps,
            'suggestions' => [
                'Ürün ara',
                'Kategori göster',
                'Fiyat karşılaştır',
                'Demo talep et',
                'İletişime geç'
            ]
        ];
    }
    
    /**
     * Fiyat ve ödeme bilgileri ver
     */
    private function handlePricingGuidance($message) {
        return [
            'intent' => 'pricing_guidance',
            'confidence' => 0.9,
            'response' => 'Fiyat bilgileri ve ödeme seçenekleri:',
            'pricing_info' => [
                'flexible_pricing' => 'Esnek fiyatlandırma seçenekleri',
                'payment_methods' => [
                    'Kredi kartı',
                    'Banka kartı',
                    'Havale/EFT',
                    'Taksitli ödeme'
                ],
                'discounts' => [
                    'Toplu alım indirimleri',
                    'Sadık müşteri avantajları',
                    'Sezonluk kampanyalar'
                ]
            ],
            'suggestions' => [
                'Detaylı fiyat listesi',
                'Özel teklif talep et',
                'Taksit seçenekleri',
                'İletişime geç'
            ]
        ];
    }
    
    /**
     * Demo ve tanıtım imkanları sun
     */
    private function handleDemoRequest($message) {
        return [
            'intent' => 'demo_request',
            'confidence' => 0.9,
            'response' => 'Size demo ve tanıtım imkanları sunuyorum:',
            'demo_options' => [
                'live_demo' => 'Canlı demo görüşmesi',
                'video_demo' => 'Video tanıtım',
                'trial_period' => 'Ücretsiz deneme süresi',
                'personalized_demo' => 'Kişiselleştirilmiş demo'
            ],
            'suggestions' => [
                'Canlı demo talep et',
                'Video izle',
                'Ücretsiz deneme başlat',
                'İletişime geç'
            ]
        ];
    }
    
    /**
     * İletişim ve destek seçenekleri sun
     */
    private function handleContactRequest($message) {
        return [
            'intent' => 'contact_request',
            'confidence' => 0.9,
            'response' => 'İletişim bilgileri ve destek seçenekleri:',
            'contact_options' => [
                'phone' => '📞 +90 (212) 555-0123',
                'email' => '📧 info@example.com',
                'whatsapp' => '💬 WhatsApp destek hattı',
                'live_chat' => '💬 Canlı chat desteği',
                'office_hours' => '🕒 Çalışma saatleri: 09:00-18:00'
            ],
            'suggestions' => [
                'Telefon ara',
                'Email gönder',
                'WhatsApp mesaj',
                'Canlı chat başlat'
            ]
        ];
    }

    /**
     * Kişisel ürün önerileri sun
     */
    private function handleProductRecommendations($message) {
        try {
            // SmartProductRecommenderService'i kullan
            $recommender = new \App\Http\Services\SmartProductRecommenderService($this);
            $recommendations = $recommender->getSmartRecommendations($message);
            
            // Ürünleri formatla
            $formattedProducts = [];
            foreach ($recommendations['products'] as $product) {
                $formattedProducts[] = [
                    'id' => $product['id'],
                    'name' => $product['name'],
                    'price' => $product['price'],
                    'description' => $product['description'],
                    'category' => $product['category'],
                    'image' => ProductImageHelper::getImageWithFallback($product['image'] ?? null),
                    'rating' => $product['rating'] ?? 0,
                    'brand' => $product['brand'] ?? 'Bilinmiyor'
                ];
            }
            
            return [
                'intent' => 'product_recommendations',
                'confidence' => 0.9,
                'response' => $recommendations['response'],
                'products' => $formattedProducts,
                'reason' => $recommendations['reason'],
                'category_matched' => $recommendations['category_matched'],
                'total_found' => $recommendations['total_found'],
                'suggestions' => $recommendations['suggestions'],
                'preferences' => $recommendations['preferences'] ?? null
            ];
            
        } catch (\Exception $e) {
            Log::error('Product recommendation error: ' . $e->getMessage());
            
            // Fallback response
            return [
                'intent' => 'product_recommendations',
                'confidence' => 0.9,
                'response' => 'Size özel ürün önerileri hazırlıyorum:',
                'products' => [],
                'suggestions' => [
                    'Popüler ürünleri göster',
                    'Trend ürünleri listele',
                    'Bütçe dostu öneriler',
                    'Premium seçenekler',
                    'Kişisel öneriler'
                ]
            ];
        }
    }
    
    /**
     * Spesifik ürün önerisi handler'ı
     */
    private function handleSpecificProductRecommendation($message) {
        // Debug: Log the message
        
        try {
            // Extract product attributes from message
            $productAttributes = $this->extractProductAttributes($message);
            
            // Search for products matching these attributes
            $filteredProducts = $this->searchProductsByAttributes($productAttributes);
            
            if (!empty($filteredProducts)) {
                return [
                    'intent' => 'specific_product_recommendation',
                    'confidence' => 0.95,
                    'response' => $this->generateSpecificProductResponse($productAttributes, count($filteredProducts)),
                    'products' => array_slice($filteredProducts, 0, 5), // Limit to 5 products
                    'search_attributes' => $productAttributes,
                    'total_found' => count($filteredProducts),
                    'suggestions' => [
                        'Farklı renk öner',
                        'Farklı marka öner', 
                        'Fiyat aralığı belirle',
                        'Benzer ürünler göster'
                    ]
                ];
            } else {
                // No specific products found, suggest alternatives
                return [
                    'intent' => 'specific_product_recommendation',
                    'confidence' => 0.7,
                    'response' => 'Aradığınız kriterlere uygun ürün bulamadım. Size benzer alternatifler öneriyorum:',
                    'products' => $this->getSampleProducts(),
                    'search_attributes' => $productAttributes,
                    'total_found' => 0,
                    'suggestions' => [
                        'Farklı kriterler dene',
                        'Genel öneriler al',
                        'Kategori seç',
                        'Marka belirle'
                    ]
                ];
            }
        } catch (\Exception $e) {
            Log::error('Specific product recommendation error: ' . $e->getMessage());
            
            // Fallback to general recommendation
            return [
                'intent' => 'specific_product_recommendation',
                'confidence' => 0.6,
                'response' => 'Arama sırasında bir hata oluştu. Size genel öneriler sunuyorum:',
                'products' => $this->getSampleProducts(),
                'suggestions' => ['Farklı arama yap', 'Kategori seç', 'Yardım al']
            ];
        }
    }
    
    /**
     * Mesajdan ürün özelliklerini çıkar
     */
    private function extractProductAttributes($message) {
        $attributes = [
            'colors' => [],
            'categories' => [],
            'sizes' => [],
            'brands' => [],
            'materials' => [],
            'styles' => []
        ];
        
        $message = mb_strtolower($message, 'UTF-8');
        
        // Renk tespiti
        $colors = ['kırmızı', 'mavi', 'siyah', 'beyaz', 'yeşil', 'sarı', 'mor', 'pembe', 'turuncu', 'gri', 'kahverengi', 'lacivert'];
        foreach ($colors as $color) {
            if (mb_strpos($message, $color) !== false) {
                $attributes['colors'][] = $color;
            }
        }
        
        // Kategori tespiti
        $categories = ['tişört', 'gömlek', 'pantolon', 'ayakkabı', 'telefon', 'bilgisayar', 'çanta', 'saat', 'gözlük', 'şapka', 'eldiven', 'çorap'];
        foreach ($categories as $category) {
            if (mb_strpos($message, $category) !== false) {
                $attributes['categories'][] = $category;
            }
        }
        
        // Boyut tespiti
        $sizes = ['xs', 's', 'm', 'l', 'xl', 'xxl', 'büyük', 'küçük', 'orta', '36', '37', '38', '39', '40', '41', '42', '43', '44', '45'];
        foreach ($sizes as $size) {
            if (mb_strpos($message, $size) !== false) {
                $attributes['sizes'][] = $size;
            }
        }
        
        // Marka tespiti
        $brands = ['nike', 'adidas', 'apple', 'samsung', 'sony', 'lg', 'hp', 'dell', 'lenovo', 'huawei', 'xiaomi'];
        foreach ($brands as $brand) {
            if (mb_strpos($message, $brand) !== false) {
                $attributes['brands'][] = $brand;
            }
        }
        
        // Materyal tespiti
        $materials = ['pamuk', 'polyester', 'deri', 'jean', 'keten', 'yün', 'ipek', 'naylon', 'koton'];
        foreach ($materials as $material) {
            if (mb_strpos($message, $material) !== false) {
                $attributes['materials'][] = $material;
            }
        }
        
        // Stil tespiti
        $styles = ['casual', 'formal', 'sport', 'klasik', 'modern', 'vintage', 'trendy', 'minimalist'];
        foreach ($styles as $style) {
            if (mb_strpos($message, $style) !== false) {
                $attributes['styles'][] = $style;
            }
        }
        
        return $attributes;
    }
    
    /**
     * Özelliklere göre ürün ara
     */
    private function searchProductsByAttributes($attributes) {
        try {
            $query = KnowledgeChunk::where('content_type', 'product');
            
            // Renk filtresi
            if (!empty($attributes['colors'])) {
                $colorConditions = [];
                foreach ($attributes['colors'] as $color) {
                    $colorConditions[] = "content LIKE '%{$color}%'";
                }
                $query->whereRaw('(' . implode(' OR ', $colorConditions) . ')');
            }
            
            // Kategori filtresi
            if (!empty($attributes['categories'])) {
                $categoryConditions = [];
                foreach ($attributes['categories'] as $category) {
                    $categoryConditions[] = "content LIKE '%{$category}%'";
                }
                $query->whereRaw('(' . implode(' OR ', $categoryConditions) . ')');
            }
            
            // Marka filtresi
            if (!empty($attributes['brands'])) {
                $brandConditions = [];
                foreach ($attributes['brands'] as $brand) {
                    $brandConditions[] = "content LIKE '%{$brand}%'";
                }
                $query->whereRaw('(' . implode(' OR ', $brandConditions) . ')');
            }
            
            $chunks = $query->with('knowledgeBase')->limit(20)->get();
            
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
                    'stock' => 10,
                    'image' => ProductImageHelper::getImageWithFallback($productData['image'] ?? null),
                    'description' => $productData['description'] ?? substr($chunk->content, 0, 200) . '...',
                    'product_url' => 'https://example.com/product/' . ($productData['id'] ?? $chunk->id) . '?intent=specific_search',
                    'source' => 'knowledge_base',
                    'matched_attributes' => $this->getMatchedAttributes($chunk->content, $attributes)
                ];
            }
            
            // Eşleşme skoruna göre sırala
            usort($products, function($a, $b) {
                return count($b['matched_attributes']) <=> count($a['matched_attributes']);
            });
            
            return $products;
            
        } catch (\Exception $e) {
            Log::error('Product search by attributes error: ' . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Hangi özelliklerin eşleştiğini tespit et
     */
    private function getMatchedAttributes($content, $attributes) {
        $matched = [];
        $content = mb_strtolower($content, 'UTF-8');
        
        foreach ($attributes as $type => $values) {
            foreach ($values as $value) {
                if (mb_strpos($content, $value) !== false) {
                    $matched[] = $value;
                }
            }
        }
        
        return $matched;
    }
    
    /**
     * Spesifik ürün önerisi için yanıt oluştur
     */
    private function generateSpecificProductResponse($attributes, $productCount) {
        $response = "Aradığınız kriterlere uygun ";
        
        if (!empty($attributes['colors'])) {
            $response .= implode(', ', $attributes['colors']) . " renkli ";
        }
        
        if (!empty($attributes['categories'])) {
            $response .= implode(', ', $attributes['categories']) . " ";
        }
        
        $response .= "ürünlerden {$productCount} adet buldum. İşte en uygun seçenekler:";
        
        return $response;
    }

    /**
     * Tüm ürünleri getir (SmartProductRecommenderService için)
     */
    public function getAllProducts() {
        try {
            // Knowledge base'den ürün verilerini çek
            $products = KnowledgeChunk::where('content_type', 'product')
                ->get()
                ->map(function ($chunk) {
                    $content = json_decode($chunk->content, true);
                    if (is_array($content) && isset($content[0])) {
                        // Eğer array içinde tek ürün varsa
                        $product = $content[0];
                    } else {
                        // Eğer tek ürün objesi varsa
                        $product = $content;
                    }
                    
                    return [
                        'id' => $product['id'] ?? $chunk->id,
                        'name' => $product['title'] ?? $product['name'] ?? 'Bilinmeyen Ürün',
                        'price' => $product['price'] ?? 0,
                        'description' => $product['description'] ?? '',
                        'category' => $product['category'] ?? 'Genel',
                        'image' => $product['image'] ?? null,
                        'rating' => $product['rating']['rate'] ?? $product['rating'] ?? 0,
                        'brand' => $product['brand'] ?? 'Bilinmiyor'
                    ];
                })
                ->toArray();
                
            return $products;
            
        } catch (\Exception $e) {
            \Log::error('Error getting products from knowledge base: ' . $e->getMessage());
            
            // Fallback: Örnek veri döndür
            return [
                [
                    'id' => 1,
                    'name' => 'iPhone 15 Pro',
                    'price' => 45000,
                    'description' => 'Apple iPhone 15 Pro - En yeni teknoloji',
                    'category' => 'Telefon',
                    'image' => 'https://example.com/iphone15.jpg',
                    'rating' => 4.8,
                    'brand' => 'Apple'
                ],
                [
                    'id' => 2,
                    'name' => 'Samsung Galaxy S24',
                    'price' => 35000,
                    'description' => 'Samsung Galaxy S24 - Android telefon',
                    'category' => 'Telefon',
                    'image' => 'https://example.com/galaxy-s24.jpg',
                    'rating' => 4.6,
                    'brand' => 'Samsung'
                ]
            ];
        }
    }
    
    /**
     * Kategoriye göre örnek ürünler döndür
     */
    private function getSampleProductsByCategory($categoryName, $keywords) {
        $allProducts = $this->getSampleProducts();
        $categoryProducts = [];
        
        foreach ($allProducts as $product) {
            $productName = mb_strtolower($product['name'], 'UTF-8');
            $productCategory = mb_strtolower($product['category'], 'UTF-8');
            
            // Kategori adı veya anahtar kelimelerle eşleşme kontrolü
            $isMatch = false;
            
            // Kategori adı eşleşmesi
            if (mb_strpos($productCategory, $categoryName) !== false) {
                $isMatch = true;
            }
            
            // Anahtar kelime eşleşmesi
            foreach ($keywords as $keyword) {
                if (mb_strpos($productName, $keyword) !== false) {
                    $isMatch = true;
                    break;
                }
            }
            
            if ($isMatch) {
                $categoryProducts[] = $product;
            }
        }
        
        // En fazla 5 ürün döndür
        return array_slice($categoryProducts, 0, 5);
    }
}
