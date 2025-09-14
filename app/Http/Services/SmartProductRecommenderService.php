<?php

namespace App\Http\Services;

use Illuminate\Support\Facades\Log;

class SmartProductRecommenderService
{
    private $productData;
    private $categoryKeywords = [];
    private $productSynonyms = [];
    private $contextualRules = [];
    private $colorKeywords = [];
    
    public function __construct($productData) {
        $this->productData = $productData;
        $this->initializeCategoryKeywords();
        $this->initializeProductSynonyms();
        $this->initializeContextualRules();
        $this->initializeColorKeywords();
    }
    
    /**
     * Tüm ürünleri getir
     */
    public function getAllProducts() {
        return $this->productData->getAllProducts();
    }
    
    public function getSmartRecommendations($message) {
        $message = mb_strtolower($message, 'UTF-8');
        
        // Renk tercihi algıla
        $detectedColor = $this->detectColorFromMessage($message);
        
        // Kategori tespit et
        $detectedCategory = $this->detectCategoryFromMessage($message);
        
        // Fiyat tercihi algıla
        $detectedPrice = $this->detectPricePreference($message);
        
        // Marka tercihi algıla
        $detectedBrand = $this->detectBrandPreference($message);
        
        // Ürünleri filtrele
        $filteredProducts = $this->filterProductsByPreferences($detectedColor, $detectedCategory, $detectedPrice, $detectedBrand);
        
        if (empty($filteredProducts)) {
            return [
                'products' => [],
                'response' => 'Aradığınız kriterlere uygun ürün bulamadım. Farklı bir arama yapmayı deneyin.',
                'reason' => 'Arama sonucu bulunamadı',
                'category_matched' => $detectedCategory ? $detectedCategory['name'] : null,
                'total_found' => 0,
                'suggestions' => $this->generateSuggestions($detectedColor, $detectedCategory, $detectedPrice, $detectedBrand)
            ];
        }
        
        // Yanıt metni oluştur
        $response = $this->generateColorBasedResponse($detectedColor, $detectedCategory, $detectedPrice, $detectedBrand);
        
        return [
            'products' => array_slice($filteredProducts, 0, 5),
            'response' => $response,
            'reason' => $this->generateRecommendationReason($detectedColor, $detectedCategory, $detectedPrice, $detectedBrand),
            'category_matched' => $detectedCategory ? $detectedCategory['name'] : null,
            'total_found' => count($filteredProducts),
            'suggestions' => $this->generateSuggestions($detectedColor, $detectedCategory, $detectedPrice, $detectedBrand),
            'preferences' => [
                'color' => $detectedColor,
                'category' => $detectedCategory,
                'price' => $detectedPrice,
                'brand' => $detectedBrand
            ]
        ];
    }
    
    private function detectColorFromMessage($message) {
        foreach ($this->colorKeywords as $colorName => $colorData) {
            foreach ($colorData['synonyms'] as $synonym) {
                if (mb_strpos($message, $synonym) !== false) {
                    return [
                        'name' => $colorName,
                        'synonyms' => $colorData['synonyms'],
                        'hex_codes' => $colorData['hex_codes'],
                        'categories' => $colorData['categories'],
                        'priority' => $colorData['priority']
                    ];
                }
            }
        }
        return null;
    }
    
    private function detectCategoryFromMessage($message) {
        $bestMatch = null;
        $highestScore = 0;
        
        foreach ($this->categoryKeywords as $categoryKey => $categoryData) {
            $score = $this->calculateCategoryMatchScore($message, $categoryData['keywords']);
            
            if ($score > $highestScore) {
                $highestScore = $score;
                $bestMatch = [
                    'name' => $categoryKey,
                    'categories' => $categoryData['categories'],
                    'priority' => $categoryData['priority']
                ];
            }
        }
        
        // Eşik değeri kontrolü
        if ($highestScore >= 0.1) {
            return $bestMatch;
        }
        
        return null;
    }
    
    private function calculateCategoryMatchScore($message, $keywords) {
        $score = 0;
        $keywordCount = 0;
        $totalKeywords = count($keywords);
        
        // Doğrudan anahtar kelime eşleşmesi
        foreach ($keywords as $keyword) {
            if (mb_strpos($message, $keyword) !== false) {
                $score += 2.0;
                $keywordCount++;
            }
        }
        
        // Eşanlamlı kelimeleri kontrol et
        foreach ($this->productSynonyms as $mainWord => $synonyms) {
            if (mb_strpos($message, $mainWord) !== false) {
                foreach ($synonyms as $synonym) {
                    if (mb_strpos($message, $synonym) !== false) {
                        $score += 1.5;
                        $keywordCount++;
                    }
                }
            }
        }
        
        if ($keywordCount > 0) {
            $confidence = $score / $totalKeywords;
            return min($confidence, 1.0);
        }
        
        return 0;
    }
    
    private function detectPricePreference($message) {
        $priceKeywords = [
            'ucuz' => ['ucuz', 'ekonomik', 'uygun fiyat', 'düşük fiyat', 'bütçe'],
            'pahalı' => ['pahalı', 'premium', 'lüks', 'kaliteli', 'yüksek fiyat'],
            'orta' => ['orta', 'normal', 'makul', 'standart']
        ];
        
        foreach ($priceKeywords as $priceType => $keywords) {
            foreach ($keywords as $keyword) {
                if (mb_strpos($message, $keyword) !== false) {
                    return [
                        'type' => $priceType,
                        'keywords' => $keywords
                    ];
                }
            }
        }
        return null;
    }
    
    private function detectBrandPreference($message) {
        $brandKeywords = [
            'apple' => ['apple', 'iphone', 'macbook', 'ipad', 'mac'],
            'samsung' => ['samsung', 'galaxy', 'note', 'tab'],
            'nike' => ['nike', 'nike ayakkabı', 'nike spor'],
            'adidas' => ['adidas', 'adidas ayakkabı', 'adidas spor'],
            'sony' => ['sony', 'sony tv', 'sony kulaklık'],
            'lg' => ['lg', 'lg tv', 'lg telefon']
        ];
        
        foreach ($brandKeywords as $brandName => $keywords) {
            foreach ($keywords as $keyword) {
                if (mb_strpos($message, $keyword) !== false) {
                    return [
                        'name' => $brandName,
                        'keywords' => $keywords
                    ];
                }
            }
        }
        return null;
    }
    
    private function filterProductsByPreferences($color, $category, $price, $brand) {
        $allProducts = $this->productData->getAllProducts();
        $filteredProducts = [];
        
        foreach ($allProducts as $product) {
            $score = 0;
            
            // Renk skoru (en yüksek öncelik)
            if ($color) {
                $score += $this->calculateColorScore($product, $color);
            }
            
            // Kategori skoru
            if ($category && in_array($product['category'], $category['categories'])) {
                $score += 2;
            }
            
            // Marka skoru
            if ($brand && mb_strtolower($product['brand']) === mb_strtolower($brand['name'])) {
                $score += 2;
            }
            
            // Fiyat skoru
            if ($price) {
                $score += $this->calculatePriceScore($product, $price);
            }
            
            // Genel kalite skoru
            $score += $product['rating'] * 0.5;
            
            if ($score > 0) {
                $product['_score'] = $score;
                $filteredProducts[] = $product;
            }
        }
        
        // Skora göre sırala
        usort($filteredProducts, function($a, $b) {
            return $b['_score'] <=> $a['_score'];
        });
        
        return $filteredProducts;
    }
    
    private function calculateColorScore($product, $color) {
        // Renk önceliğine göre skor ver
        $baseScore = $color['priority'];
        
        // Ürün kategorisi renk kategorilerinde varsa bonus puan
        if (in_array($product['category'], $color['categories'])) {
            $baseScore += 1;
        }
        
        // Ürün adında renk geçiyorsa bonus puan
        $productName = mb_strtolower($product['name']);
        foreach ($color['synonyms'] as $synonym) {
            if (mb_strpos($productName, $synonym) !== false) {
                $baseScore += 2;
                break;
            }
        }
        
        return $baseScore;
    }
    
    private function calculatePriceScore($product, $price) {
        $productPrice = $product['price'];
        
        switch ($price['type']) {
            case 'ucuz':
                return $productPrice < 1000 ? 3 : ($productPrice < 5000 ? 1 : 0);
            case 'pahalı':
                return $productPrice > 10000 ? 3 : ($productPrice > 5000 ? 1 : 0);
            case 'orta':
                return ($productPrice >= 1000 && $productPrice <= 10000) ? 2 : 0;
            default:
                return 0;
        }
    }
    
    private function generateColorBasedResponse($color, $category, $price, $brand) {
        $response = '';
        
        if ($color) {
            $response .= "🎨 {$color['name']} renkteki ";
        }
        
        if ($category) {
            $response .= "{$category['name']} kategorisinde ";
        }
        
        if ($brand) {
            $response .= "{$brand['name']} markasının ";
        }
        
        if ($price) {
            $response .= "{$price['type']} fiyatlı ";
        }
        
        $response .= "en kaliteli ürünleri öneriyorum. İşte harika seçenekler:";
        
        return $response;
    }
    
    private function generateRecommendationReason($color, $category, $price, $brand) {
        $reasons = [];
        
        if ($color) {
            $reasons[] = "Renk: {$color['name']}";
        }
        
        if ($category) {
            $reasons[] = "Kategori: {$category['name']}";
        }
        
        if ($brand) {
            $reasons[] = "Marka: {$brand['name']}";
        }
        
        if ($price) {
            $reasons[] = "Fiyat: {$price['type']}";
        }
        
        return implode(', ', $reasons);
    }
    
    private function generateSuggestions($color, $category, $price, $brand) {
        $suggestions = [];
        
        if ($color) {
            $suggestions[] = "Farklı renk seçenekleri";
        }
        
        if ($category) {
            $suggestions[] = "Farklı kategori öner";
        }
        
        if ($price) {
            $suggestions[] = "Fiyat aralığını değiştir";
        }
        
        if ($brand) {
            $suggestions[] = "Farklı marka öner";
        }
        
        if (empty($suggestions)) {
            $suggestions = ["Farklı kategori öner", "Fiyat aralığı belirle", "Marka önerisi al"];
        }
        
        return $suggestions;
    }
    
    private function initializeCategoryKeywords() {
        $this->categoryKeywords = [
            'pet_shop' => [
                'keywords' => ['köpek', 'kedi', 'pet', 'hayvan', 'mama', 'evcil', 'kuş', 'balık', 'hamster', 'tavşan', 'kaplumbağa', 'sürüngen'],
                'categories' => ['Kedi Maması', 'Köpek Maması', 'Pet Shop'],
                'priority' => 1
            ],
            'electronics' => [
                'keywords' => ['telefon', 'iphone', 'samsung', 'bilgisayar', 'laptop', 'tablet', 'ipad', 'kulaklık', 'televizyon', 'tv', 'oyun', 'playstation', 'xbox'],
                'categories' => ['Telefon', 'Bilgisayar', 'Tablet', 'Kulaklık', 'Televizyon', 'Oyun Konsolu'],
                'priority' => 1
            ],
            'clothing' => [
                'keywords' => ['giyim', 'kıyafet', 'elbise', 'ayakkabı', 'spor', 'nike', 'adidas', 'ceket', 'pantolon', 'gömlek', 'tshirt', 'hırka'],
                'categories' => ['Spor Ayakkabı', 'Kot Pantolon', 'Polo Yaka', 'Ceket', 'Elbise', 'Gömlek', 'Sweatshirt', 'Etek', 'Çanta'],
                'priority' => 1
            ],
            'home_living' => [
                'keywords' => ['ev', 'mobilya', 'dekorasyon', 'mutfak', 'banyo', 'yatak odası', 'salon', 'ikea', 'mobilya', 'ev eşyası', 'yaşam'],
                'categories' => ['Mobilya', 'Aydınlatma', 'Beyaz Eşya', 'Mutfak'],
                'priority' => 1
            ],
            'sports_outdoor' => [
                'keywords' => ['spor', 'fitness', 'egzersiz', 'koşu', 'yürüyüş', 'bisiklet', 'yüzme', 'futbol', 'basketbol', 'tenis', 'golf', 'outdoor'],
                'categories' => ['Bisiklet', 'Spor Ayakkabı', 'Mont', 'Ceket', 'Hırka', 'Spor Çanta', 'Spor Çorap', 'Şort', 'Spor Tshirt', 'Spor Pantolon'],
                'priority' => 1
            ],
            'beauty_cosmetics' => [
                'keywords' => ['kozmetik', 'makyaj', 'cilt bakımı', 'saç bakımı', 'parfüm', 'ruj', 'fondöten', 'göz farı', 'şampuan', 'nemlendirici'],
                'categories' => ['Şampuan', 'Yüz Bakımı', 'Nemlendirici', 'Makyaj', 'Güneş Bakımı', 'Serum'],
                'priority' => 1
            ],
            'books_hobbies' => [
                'keywords' => ['kitap', 'okuma', 'hobi', 'oyuncak', 'lego', 'oyun', 'puzzle', 'sanat', 'müzik', 'film', 'dizi', 'roman', 'bilim'],
                'categories' => ['Kitap', 'Oyuncak', 'Oyun'],
                'priority' => 1
            ],
            'automotive' => [
                'keywords' => ['araba', 'otomobil', 'araç', 'lastik', 'akü', 'motor yağı', 'bakım', 'servis', 'parça', 'aksesuar'],
                'categories' => ['Lastik', 'Akü', 'Motor Yağı'],
                'priority' => 1
            ],
            'health_medicine' => [
                'keywords' => ['sağlık', 'ilaç', 'vitamin', 'mineral', 'ağrı kesici', 'ateş düşürücü', 'bağışıklık', 'enerji', 'uyku', 'stres'],
                'categories' => ['Ağrı Kesici', 'Vitamin', 'Mineral'],
                'priority' => 1
            ],
            'garden_tools' => [
                'keywords' => ['bahçe', 'çiçek', 'bitki', 'ağaç', 'çim', 'tırmık', 'makas', 'çim biçme', 'testere', 'el aleti', 'matkap', 'tornavida'],
                'categories' => ['Bahçe Aleti', 'Bahçe Makinesi', 'El Aleti'],
                'priority' => 1
            ]
        ];
    }
    
    private function initializeProductSynonyms() {
        $this->productSynonyms = [
            // Pet Shop
            'köpek' => ['pet', 'hayvan', 'evcil', 'mama', 'köpek maması', 'köpek oyuncağı', 'köpek tasması'],
            'kedi' => ['pet', 'hayvan', 'evcil', 'mama', 'kedi maması', 'kedi oyuncağı', 'kedi kumu'],
            'pet' => ['köpek', 'kedi', 'hayvan', 'evcil', 'mama', 'oyuncak', 'aksesuar'],
            
            // Electronics
            'telefon' => ['iphone', 'samsung', 'galaxy', 'smartphone', 'mobil', 'cep telefonu'],
            'bilgisayar' => ['laptop', 'macbook', 'dell', 'hp', 'lenovo', 'notebook', 'dizüstü'],
            'tablet' => ['ipad', 'samsung tab', 'android tablet', 'dijital tablet'],
            
            // Clothing
            'ayakkabı' => ['spor ayakkabı', 'günlük ayakkabı', 'topuklu', 'düz', 'sneaker'],
            'kıyafet' => ['elbise', 'pantolon', 'gömlek', 'tshirt', 'hırka', 'ceket', 'eşofman'],
            
            // Home & Living
            'ev' => ['mobilya', 'dekorasyon', 'mutfak', 'banyo', 'yatak odası', 'salon'],
            'mobilya' => ['yatak', 'dolap', 'masa', 'sandalye', 'koltuk', 'sehpa'],
            
            // Sports
            'spor' => ['fitness', 'egzersiz', 'koşu', 'yürüyüş', 'bisiklet', 'yüzme'],
            'fitness' => ['spor', 'egzersiz', 'koşu', 'yürüyüş', 'bisiklet', 'yüzme'],
            
            // Beauty
            'kozmetik' => ['makyaj', 'cilt bakımı', 'saç bakımı', 'parfüm', 'ruj', 'fondöten'],
            'makyaj' => ['kozmetik', 'ruj', 'fondöten', 'göz farı', 'maskara', 'allık'],
            
            // Books & Hobbies
            'kitap' => ['roman', 'bilim', 'tarih', 'felsefe', 'psikoloji', 'roman'],
            'hobi' => ['oyuncak', 'lego', 'oyun', 'puzzle', 'sanat', 'müzik'],
            
            // Automotive
            'araba' => ['otomobil', 'araç', 'lastik', 'akü', 'motor yağı', 'bakım'],
            'lastik' => ['araba', 'otomobil', 'araç', 'tekerlek', 'kauçuk'],
            
            // Health
            'sağlık' => ['ilaç', 'vitamin', 'mineral', 'ağrı kesici', 'ateş düşürücü'],
            'vitamin' => ['sağlık', 'mineral', 'bağışıklık', 'enerji', 'beslenme'],
            
            // Garden & Tools
            'bahçe' => ['çiçek', 'bitki', 'ağaç', 'çim', 'tırmık', 'makas'],
            'el aleti' => ['matkap', 'tornavida', 'çekiç', 'pense', 'anahtar']
        ];
    }
    
    private function initializeContextualRules() {
        $this->contextualRules = [
            'color_preference' => [
                'keywords' => ['kırmızı', 'mavi', 'yeşil', 'sarı', 'turuncu', 'mor', 'pembe', 'siyah', 'beyaz', 'gri', 'kahverengi', 'lacivert', 'turkuaz', 'altın', 'gümüş'],
                'priority' => 2,
                'response_template' => 'Renk tercihinize göre ürünleri öneriyorum. {color} renkteki en kaliteli seçenekler:'
            ],
            'size_preference' => [
                'keywords' => ['küçük', 'orta', 'büyük', 'xs', 's', 'm', 'l', 'xl', 'xxl', 'küçük boy', 'büyük boy'],
                'priority' => 1,
                'response_template' => 'Boyut tercihinize göre ürünleri öneriyorum. {size} boyuttaki seçenekler:'
            ],
            'price_preference' => [
                'keywords' => ['ucuz', 'pahalı', 'ekonomik', 'premium', 'lüks', 'bütçe', 'indirimli', 'kampanyalı'],
                'priority' => 1,
                'response_template' => 'Fiyat tercihinize göre ürünleri öneriyorum. {price_type} fiyatlı seçenekler:'
            ],
            'brand_preference' => [
                'keywords' => ['apple', 'samsung', 'nike', 'adidas', 'sony', 'lg', 'dell', 'hp', 'lenovo'],
                'priority' => 1,
                'response_template' => 'Marka tercihinize göre ürünleri öneriyorum. {brand} markasının en iyi ürünleri:'
            ]
        ];
    }
    
    private function initializeColorKeywords() {
        $this->colorKeywords = [
            'kırmızı' => [
                'synonyms' => ['kırmızı', 'red', 'kızıl', 'al', 'kan rengi'],
                'hex_codes' => ['#FF0000', '#DC143C', '#B22222', '#8B0000'],
                'categories' => ['Telefon', 'Bilgisayar', 'Kulaklık', 'Spor Ayakkabı', 'Elbise', 'Çanta', 'Aksesuar'],
                'priority' => 3
            ],
            'mavi' => [
                'synonyms' => ['mavi', 'blue', 'lacivert', 'navy', 'gök mavisi'],
                'hex_codes' => ['#0000FF', '#000080', '#4169E1', '#1E90FF'],
                'categories' => ['Telefon', 'Bilgisayar', 'Kulaklık', 'Spor Ayakkabı', 'Elbise', 'Çanta', 'Aksesuar'],
                'priority' => 3
            ],
            'yeşil' => [
                'synonyms' => ['yeşil', 'green', 'açık yeşil', 'koyu yeşil', 'zümrüt'],
                'hex_codes' => ['#008000', '#228B22', '#32CD32', '#90EE90'],
                'categories' => ['Telefon', 'Bilgisayar', 'Kulaklık', 'Spor Ayakkabı', 'Elbise', 'Çanta', 'Aksesuar'],
                'priority' => 3
            ],
            'siyah' => [
                'synonyms' => ['siyah', 'black', 'koyu', 'kara', 'ebony'],
                'hex_codes' => ['#000000', '#1C1C1C', '#2F2F2F', '#404040'],
                'categories' => ['Telefon', 'Bilgisayar', 'Kulaklık', 'Spor Ayakkabı', 'Elbise', 'Çanta', 'Aksesuar'],
                'priority' => 3
            ],
            'beyaz' => [
                'synonyms' => ['beyaz', 'white', 'açık', 'buz beyazı', 'fildişi'],
                'hex_codes' => ['#FFFFFF', '#F5F5F5', '#F0F0F0', '#E8E8E8'],
                'categories' => ['Telefon', 'Bilgisayar', 'Kulaklık', 'Spor Ayakkabı', 'Elbise', 'Çanta', 'Aksesuar'],
                'priority' => 3
            ]
        ];
    }
}
