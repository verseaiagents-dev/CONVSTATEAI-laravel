<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class CampaignPromptService
{
    private $apiKey;
    private $baseUrl = 'https://api.openai.com/v1';
    private $model = 'gpt-4o-mini';

    public function __construct()
    {
        $this->apiKey = config('openai.api_key');
        
        if (!$this->apiKey) {
            throw new \Exception('OpenAI API key bulunamadı. Lütfen .env dosyasında OPENAI_API_KEY değerini kontrol edin.');
        }
    }

    /**
     * Ürün bilgilerine göre AI destekli kampanya önerileri oluşturur
     */
    public function generateCampaignSuggestions(array $productData, array $businessData): array
    {
        try {
            $systemPrompt = $this->buildCampaignGenerationPrompt();
            
            $userPrompt = $this->buildUserPrompt($productData, $businessData);
            
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Content-Type' => 'application/json',
            ])->post($this->baseUrl . '/chat/completions', [
                'model' => $this->model,
                'messages' => [
                    ['role' => 'system', 'content' => $systemPrompt],
                    ['role' => 'user', 'content' => $userPrompt]
                ],
                'max_tokens' => 1500,
                'temperature' => 0.7
            ]);

            if (!$response->successful()) {
                throw new \Exception('OpenAI API Error: ' . $response->body());
            }

            $data = $response->json();
            $content = $data['choices'][0]['message']['content'];
            
            // JSON formatında response gelirse parse et
            if (preg_match('/\{.*\}/s', $content)) {
                try {
                    $suggestions = json_decode($content, true);
                    if (json_last_error() === JSON_ERROR_NONE) {
                        return $suggestions;
                    }
                } catch (\Exception $e) {
                    Log::warning('JSON parsing failed:', ['error' => $e->getMessage()]);
                }
            }

            // Fallback: Structured text parsing
            return $this->parseStructuredResponse($content);
            
        } catch (\Exception $e) {
            Log::error('Campaign generation error: ' . $e->getMessage());
            return $this->getFallbackSuggestions($productData, $businessData);
        }
    }

    /**
     * Kampanya oluşturma için system prompt
     */
    private function buildCampaignGenerationPrompt(): string
    {
        return "Sen bir e-ticaret kampanya uzmanısın. Verilen ürün bilgilerine göre etkili kampanya önerileri oluştur.

        Kampanya türleri:
        1. **Yüzde İndirim**: %10, %20, %30 gibi oranlarda indirim
        2. **Sabit İndirim**: 50 TL, 100 TL gibi sabit tutarlarda indirim
        3. **2 Al 1 Bedava**: Belirli ürünlerde al 2 öde 1
        4. **Ücretsiz Kargo**: Belirli tutar üzeri ücretsiz kargo
        5. **Paket İndirimi**: Birden fazla ürün alımında ek indirim
        6. **Sezon Sonu**: Stok tükenme kampanyaları
        7. **Yeni Müşteri**: İlk alımda özel indirim
        8. **Sadakat**: Tekrar alımda özel fiyat
        9. **Flash Sale**: Sınırlı süreli kampanya
        10. **Bundle**: Ürün paketlerinde indirim

        Kampanya özellikleri:
        - Başlık: Çekici ve net
        - Açıklama: Detaylı ve ikna edici
        - İndirim türü: Yukarıdaki türlerden biri
        - İndirim değeri: Sayısal değer
        - Geçerlilik süresi: Gerçekçi süre
        - Minimum sipariş tutarı: Uygun limit
        - Şartlar: Net ve anlaşılır

        Yanıtı şu formatta ver:
        {
            \"suggestions\": [
                {
                    \"title\": \"Kampanya başlığı\",
                    \"description\": \"Detaylı açıklama\",
                    \"campaign_type\": \"kampanya_türü\",
                    \"discount_type\": \"indirim_türü\",
                    \"discount_value\": \"indirim_değeri\",
                    \"validity_days\": \"geçerlilik_günü\",
                    \"minimum_order\": \"minimum_sipariş_tutarı\",
                    \"terms\": \"şartlar_ve_koşullar\",
                    \"target_audience\": \"hedef_kitle\",
                    \"expected_impact\": \"beklenen_etki\",
                    \"confidence_score\": \"güven_skoru\"
                }
            ],
            \"summary\": {
                \"total_suggestions\": \"toplam_öneri_sayısı\",
                \"best_campaign\": \"en_iyi_kampanya_türü\",
                \"estimated_revenue\": \"tahmini_gelir_artışı\",
                \"risk_level\": \"risk_seviyesi\"
            }
        }";
    }

    /**
     * Kullanıcı prompt'unu oluşturur
     */
    private function buildUserPrompt(array $productData, array $businessData): string
    {
        $productInfo = "Ürün Bilgileri:\n";
        foreach ($productData as $product) {
            $productInfo .= "- Ürün: {$product['name']}\n";
            $productInfo .= "  Kategori: {$product['category']}\n";
            $productInfo .= "  Fiyat: {$product['price']} TL\n";
            $productInfo .= "  Stok: {$product['stock']}\n";
            $productInfo .= "  Kar Oranı: %{$product['profit_margin']}\n";
        }

        $businessInfo = "Kampanya Ayarları:\n";
        foreach ($businessData['product_settings'] as $productId => $settings) {
            $businessInfo .= "- Ürün ID {$productId}:\n";
            $businessInfo .= "  * Satış Fiyatı: {$settings['salePrice']} TL\n";
            $businessInfo .= "  * Kar Oranı: %{$settings['profitMargin']}\n";
            $businessInfo .= "  * Stok Miktarı: {$settings['stockQuantity']} adet\n";
            $businessInfo .= "  * Sezon: {$settings['season']}\n";
        }
        $businessInfo .= "- Genel Sezon: {$businessData['season']}\n";

        return $productInfo . "\n" . $businessInfo . "\n\nBu bilgilere göre 5 farklı kampanya önerisi oluştur. Her öneri farklı kampanya türünde olsun ve gerçekçi değerler içersin.";
    }

    /**
     * Structured response'u parse eder
     */
    private function parseStructuredResponse(string $content): array
    {
        $suggestions = [];
        
        // Kampanya türlerini tespit et
        if (preg_match('/(yüzde|%)/i', $content)) {
            $suggestions[] = [
                'title' => 'Yüzde İndirim Kampanyası',
                'description' => 'Seçili ürünlerde özel indirim fırsatı',
                'campaign_type' => 'percentage_discount',
                'discount_type' => 'percentage',
                'discount_value' => '20',
                'validity_days' => '30',
                'minimum_order' => '100',
                'terms' => 'Kampanya süresi boyunca geçerlidir',
                'target_audience' => 'Tüm müşteriler',
                'expected_impact' => 'Satış artışı ve stok tüketimi',
                'confidence_score' => '85'
            ];
        }

        if (preg_match('/(2 al|1 bedava|bundle)/i', $content)) {
            $suggestions[] = [
                'title' => '2 Al 1 Bedava Kampanyası',
                'description' => 'Seçili ürünlerde 2 al 1 bedava fırsatı',
                'campaign_type' => 'buy_x_get_y',
                'discount_type' => 'buy_x_get_y',
                'discount_value' => '50',
                'validity_days' => '15',
                'minimum_order' => '200',
                'terms' => 'Aynı üründen 2 adet alındığında 1 tanesi bedava',
                'target_audience' => 'Toplu alım yapan müşteriler',
                'expected_impact' => 'Stok hızlı tüketimi ve gelir artışı',
                'confidence_score' => '90'
            ];
        }

        if (preg_match('/(kargo|shipping)/i', $content)) {
            $suggestions[] = [
                'title' => 'Ücretsiz Kargo Kampanyası',
                'description' => 'Belirli tutar üzeri ücretsiz kargo',
                'campaign_type' => 'free_shipping',
                'discount_type' => 'free_shipping',
                'discount_value' => '0',
                'validity_days' => '45',
                'minimum_order' => '150',
                'terms' => '150 TL üzeri alımlarda ücretsiz kargo',
                'target_audience' => 'Orta-yüksek segment müşteriler',
                'expected_impact' => 'Sepet tutarı artışı',
                'confidence_score' => '80'
            ];
        }

        if (preg_match('/(flash|sınırlı|acil)/i', $content)) {
            $suggestions[] = [
                'title' => 'Flash Sale Kampanyası',
                'description' => 'Sınırlı süreli özel fırsat',
                'campaign_type' => 'flash_sale',
                'discount_type' => 'percentage',
                'discount_value' => '40',
                'validity_days' => '3',
                'minimum_order' => '50',
                'terms' => 'Sadece 3 gün geçerli, stokla sınırlı',
                'target_audience' => 'Hızlı karar veren müşteriler',
                'expected_impact' => 'Hızlı satış ve stok tüketimi',
                'confidence_score' => '95'
            ];
        }

        if (preg_match('/(yeni|ilk|müşteri)/i', $content)) {
            $suggestions[] = [
                'title' => 'Yeni Müşteri Kampanyası',
                'description' => 'İlk alımda özel indirim',
                'campaign_type' => 'new_customer',
                'discount_type' => 'percentage',
                'discount_value' => '25',
                'validity_days' => '60',
                'minimum_order' => '75',
                'terms' => 'Sadece yeni üyeler için geçerli',
                'target_audience' => 'Yeni müşteriler',
                'expected_impact' => 'Müşteri kazanımı ve sadakat',
                'confidence_score' => '88'
            ];
        }

        return [
            'suggestions' => $suggestions,
            'summary' => [
                'total_suggestions' => count($suggestions),
                'best_campaign' => 'percentage_discount',
                'estimated_revenue' => '25-40% artış',
                'risk_level' => 'Düşük'
            ]
        ];
    }

    /**
     * Fallback kampanya önerileri
     */
    private function getFallbackSuggestions(array $productData, array $businessData): array
    {
        return [
            'suggestions' => [
                [
                    'title' => 'Genel İndirim Kampanyası',
                    'description' => 'Tüm ürünlerde %15 indirim',
                    'campaign_type' => 'general_discount',
                    'discount_type' => 'percentage',
                    'discount_value' => '15',
                    'validity_days' => '30',
                    'minimum_order' => '100',
                    'terms' => 'Kampanya süresi boyunca geçerlidir',
                    'target_audience' => 'Tüm müşteriler',
                    'expected_impact' => 'Satış artışı',
                    'confidence_score' => '75'
                ],
                [
                    'title' => 'Stok Tüketim Kampanyası',
                    'description' => 'Seçili ürünlerde %25 indirim',
                    'campaign_type' => 'stock_clearance',
                    'discount_type' => 'percentage',
                    'discount_value' => '25',
                    'validity_days' => '20',
                    'minimum_order' => '75',
                    'terms' => 'Stokla sınırlı kampanya',
                    'target_audience' => 'Fırsat avcıları',
                    'expected_impact' => 'Hızlı stok tüketimi',
                    'confidence_score' => '85'
                ]
            ],
            'summary' => [
                'total_suggestions' => 2,
                'best_campaign' => 'stock_clearance',
                'estimated_revenue' => '20-30% artış',
                'risk_level' => 'Orta'
            ]
        ];
    }

    /**
     * Kampanya performans tahmini yapar
     */
    public function predictCampaignPerformance(array $campaignData, array $productData): array
    {
        try {
            $systemPrompt = "Sen bir e-ticaret analiz uzmanısın. Verilen kampanya verilerine göre performans tahmini yap.";
            
            $userPrompt = "Kampanya: " . json_encode($campaignData, JSON_UNESCAPED_UNICODE) . "\nÜrünler: " . json_encode($productData, JSON_UNESCAPED_UNICODE);
            
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Content-Type' => 'application/json',
            ])->post($this->baseUrl . '/chat/completions', [
                'model' => $this->model,
                'messages' => [
                    ['role' => 'system', 'content' => $systemPrompt],
                    ['role' => 'user', 'content' => $userPrompt]
                ],
                'max_tokens' => 800,
                'temperature' => 0.5
            ]);

            if ($response->successful()) {
                $data = $response->json();
                $content = $data['choices'][0]['message']['content'];
                
                // JSON parse etmeye çalış
                if (preg_match('/\{.*\}/s', $content)) {
                    try {
                        return json_decode($content, true);
                    } catch (\Exception $e) {
                        Log::warning('Performance prediction JSON parsing failed');
                    }
                }
            }
        } catch (\Exception $e) {
            Log::error('Performance prediction error: ' . $e->getMessage());
        }

        // Fallback tahmin
        return [
            'expected_sales_increase' => '15-25%',
            'estimated_revenue' => 'Tahmin yapılamadı',
            'risk_factors' => ['Stok yetersizliği', 'Rekabet'],
            'success_probability' => '75%'
        ];
    }
}
