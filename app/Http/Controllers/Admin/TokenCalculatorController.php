<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\Plan;

class TokenCalculatorController extends Controller
{
    // OpenAI API Fiyatlandırması (Official - Per 1M tokens)
    private const PRICING = [
        'gpt-4o-mini' => [
            'input' => 0.150,  // $0.150 per 1M tokens
            'output' => 0.600, // $0.600 per 1M tokens
        ],
        'gpt-4o' => [
            'input' => 2.50,   // $2.50 per 1M tokens
            'output' => 10.00, // $10.00 per 1M tokens
        ],
        'gpt-3.5-turbo' => [
            'input' => 0.50,   // $0.50 per 1M tokens
            'output' => 1.50,  // $1.50 per 1M tokens
        ],
    ];

    // Döviz kuru (güncellenebilir)
    private const USD_TO_TRY = 34.50;

    // Ortalama token kullanımı (ConvStateAPI analizi)
    private const AVERAGE_TOKENS = [
        'user_message' => 50,           // Ortalama kullanıcı mesajı
        'system_prompt' => 300,         // System prompt
        'conversation_history' => 200,  // Her mesaj için conversation history
        'knowledge_base' => 500,        // Knowledge base chunks
        'ai_response' => 150,           // Ortalama AI yanıtı
        'product_list_response' => 300, // Ürün listesi response
        'detailed_response' => 200,     // Detaylı açıklama
    ];

    /**
     * Ana sayfa - Token Calculator
     */
    public function index()
    {
        $plans = Plan::active()->get();
        
        return view('admin.token-calculator', [
            'plans' => $plans,
            'pricing' => self::PRICING,
            'usdToTry' => self::USD_TO_TRY,
            'averageTokens' => self::AVERAGE_TOKENS
        ]);
    }

    /**
     * Token kullanımını hesapla
     */
    public function calculate(Request $request)
    {
        $request->validate([
            'sessions' => 'required|integer|min:1|max:1000000',
            'messages_per_session' => 'required|integer|min:1|max:100',
            'model' => 'required|in:gpt-4o-mini,gpt-4o,gpt-3.5-turbo',
        ]);

        $sessions = $request->input('sessions');
        $messagesPerSession = $request->input('messages_per_session');
        $model = $request->input('model');

        // Token hesaplama
        $totalMessages = $sessions * $messagesPerSession;
        
        // Input tokens hesaplama
        $inputTokensPerMessage = 
            self::AVERAGE_TOKENS['user_message'] +
            self::AVERAGE_TOKENS['system_prompt'] +
            self::AVERAGE_TOKENS['conversation_history'] +
            self::AVERAGE_TOKENS['knowledge_base'];
        
        $totalInputTokens = $totalMessages * $inputTokensPerMessage;

        // Output tokens hesaplama (ortalama - bazı mesajlar ürün listesi, bazıları basit yanıt)
        $outputTokensPerMessage = 
            (self::AVERAGE_TOKENS['ai_response'] * 0.5) +
            (self::AVERAGE_TOKENS['product_list_response'] * 0.3) +
            (self::AVERAGE_TOKENS['detailed_response'] * 0.2);
        
        $totalOutputTokens = $totalMessages * $outputTokensPerMessage;

        // Maliyet hesaplama
        $inputCostUSD = ($totalInputTokens / 1000000) * self::PRICING[$model]['input'];
        $outputCostUSD = ($totalOutputTokens / 1000000) * self::PRICING[$model]['output'];
        $totalCostUSD = $inputCostUSD + $outputCostUSD;
        $totalCostTRY = $totalCostUSD * self::USD_TO_TRY;

        // Session ve mesaj başına maliyet
        $costPerSession = $totalCostUSD / $sessions;
        $costPerMessage = $totalCostUSD / $totalMessages;

        // Aylık senaryolar
        $scenarios = [
            'low' => $this->calculateScenario(100, $messagesPerSession, $model),
            'medium' => $this->calculateScenario(1000, $messagesPerSession, $model),
            'high' => $this->calculateScenario(10000, $messagesPerSession, $model),
            'enterprise' => $this->calculateScenario(100000, $messagesPerSession, $model),
        ];

        return response()->json([
            'success' => true,
            'data' => [
                'input' => [
                    'sessions' => $sessions,
                    'messages_per_session' => $messagesPerSession,
                    'total_messages' => $totalMessages,
                    'model' => $model,
                ],
                'tokens' => [
                    'input_tokens' => $totalInputTokens,
                    'output_tokens' => $totalOutputTokens,
                    'total_tokens' => $totalInputTokens + $totalOutputTokens,
                    'input_tokens_per_message' => $inputTokensPerMessage,
                    'output_tokens_per_message' => round($outputTokensPerMessage),
                ],
                'costs' => [
                    'input_cost_usd' => round($inputCostUSD, 4),
                    'output_cost_usd' => round($outputCostUSD, 4),
                    'total_cost_usd' => round($totalCostUSD, 4),
                    'total_cost_try' => round($totalCostTRY, 2),
                    'cost_per_session_usd' => round($costPerSession, 6),
                    'cost_per_message_usd' => round($costPerMessage, 6),
                ],
                'scenarios' => $scenarios,
            ]
        ]);
    }

    /**
     * Senaryo hesaplama
     */
    private function calculateScenario($sessions, $messagesPerSession, $model)
    {
        $totalMessages = $sessions * $messagesPerSession;
        
        $inputTokensPerMessage = 
            self::AVERAGE_TOKENS['user_message'] +
            self::AVERAGE_TOKENS['system_prompt'] +
            self::AVERAGE_TOKENS['conversation_history'] +
            self::AVERAGE_TOKENS['knowledge_base'];
        
        $outputTokensPerMessage = 
            (self::AVERAGE_TOKENS['ai_response'] * 0.5) +
            (self::AVERAGE_TOKENS['product_list_response'] * 0.3) +
            (self::AVERAGE_TOKENS['detailed_response'] * 0.2);
        
        $totalInputTokens = $totalMessages * $inputTokensPerMessage;
        $totalOutputTokens = $totalMessages * $outputTokensPerMessage;

        $inputCostUSD = ($totalInputTokens / 1000000) * self::PRICING[$model]['input'];
        $outputCostUSD = ($totalOutputTokens / 1000000) * self::PRICING[$model]['output'];
        $totalCostUSD = $inputCostUSD + $outputCostUSD;
        $totalCostTRY = $totalCostUSD * self::USD_TO_TRY;

        return [
            'sessions' => $sessions,
            'messages' => $totalMessages,
            'tokens' => $totalInputTokens + $totalOutputTokens,
            'cost_usd' => round($totalCostUSD, 2),
            'cost_try' => round($totalCostTRY, 2),
            'monthly_cost_usd' => round($totalCostUSD, 2),
            'monthly_cost_try' => round($totalCostTRY, 2),
            'yearly_cost_usd' => round($totalCostUSD * 12, 2),
            'yearly_cost_try' => round($totalCostTRY * 12, 2),
        ];
    }

    /**
     * AI Fiyat Önerisi Agent
     */
    public function aiPriceRecommendation(Request $request)
    {
        $request->validate([
            'sessions_per_month' => 'required|integer|min:1',
            'messages_per_session' => 'required|integer|min:1',
            'model' => 'required|in:gpt-4o-mini,gpt-4o,gpt-3.5-turbo',
            'profit_margin' => 'required|numeric|min:0|max:500',
            'target_market' => 'required|in:budget,standard,premium,enterprise',
        ]);

        try {
            $sessionsPerMonth = $request->input('sessions_per_month');
            $messagesPerSession = $request->input('messages_per_session');
            $model = $request->input('model');
            $profitMargin = $request->input('profit_margin') / 100; // Yüzdeyi decimal'e çevir
            $targetMarket = $request->input('target_market');

            // Maliyet hesaplama
            $costData = $this->calculateScenario($sessionsPerMonth, $messagesPerSession, $model);
            
            // OpenAI API key kontrolü
            $apiKey = config('openai.api_key');
            
            if (!$apiKey || $apiKey === 'your_openai_api_key_here') {
                // Fallback: AI olmadan hesaplama
                return $this->fallbackPriceRecommendation($costData, $profitMargin, $targetMarket);
            }

            // AI ile fiyat önerisi oluştur
            $prompt = $this->buildPricingPrompt($costData, $profitMargin, $targetMarket);
            
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $apiKey,
                'Content-Type' => 'application/json',
            ])->timeout(30)->post('https://api.openai.com/v1/chat/completions', [
                'model' => 'gpt-4o-mini',
                'messages' => [
                    [
                        'role' => 'system',
                        'content' => 'Sen bir SaaS fiyatlandırma uzmanısın. Token maliyetlerine göre rekabetçi ve karlı plan fiyatları öneriyorsun. Her zaman JSON formatında yanıt veriyorsun.'
                    ],
                    [
                        'role' => 'user',
                        'content' => $prompt
                    ]
                ],
                'temperature' => 0.7,
                'max_tokens' => 1500,
            ]);

            if (!$response->successful()) {
                return $this->fallbackPriceRecommendation($costData, $profitMargin, $targetMarket);
            }

            $data = $response->json();
            $aiResponse = $data['choices'][0]['message']['content'] ?? '';

            // JSON parse et
            $aiRecommendation = json_decode($aiResponse, true);
            
            if (json_last_error() !== JSON_ERROR_NONE) {
                return $this->fallbackPriceRecommendation($costData, $profitMargin, $targetMarket);
            }

            return response()->json([
                'success' => true,
                'data' => $aiRecommendation,
                'cost_analysis' => $costData,
                'source' => 'ai'
            ]);

        } catch (\Exception $e) {
            Log::error('AI Price Recommendation Error: ' . $e->getMessage());
            
            // Fallback
            $costData = $this->calculateScenario(
                $request->input('sessions_per_month'),
                $request->input('messages_per_session'),
                $request->input('model')
            );
            
            return $this->fallbackPriceRecommendation(
                $costData,
                $request->input('profit_margin') / 100,
                $request->input('target_market')
            );
        }
    }

    /**
     * AI Prompt oluşturma
     */
    private function buildPricingPrompt($costData, $profitMargin, $targetMarket)
    {
        $profitPercentage = $profitMargin * 100;
        
        return "SaaS AI Chatbot platformu için plan fiyatlandırması öner.

MALIYET ANALİZİ:
- Aylık Kullanım: {$costData['sessions']} session, {$costData['messages']} mesaj
- Token Maliyeti: {$costData['cost_usd']} USD / {$costData['cost_try']} TRY (aylık)
- Yıllık Token Maliyeti: {$costData['yearly_cost_usd']} USD / {$costData['yearly_cost_try']} TRY

HEDEF PAZAR: {$targetMarket}
KAR MARJI: %{$profitPercentage}

4 PLAN ÖNER:
1. Başlangıç (Starter) - Küçük işletmeler için
2. Profesyonel - Büyüyen işletmeler için
3. İş (Business) - Orta ölçekli firmalar için
4. Kurumsal (Enterprise) - Büyük şirketler için

Her plan için:
- Aylık fiyat (TRY)
- Yıllık fiyat (TRY) - %15-20 indirimli
- Usage token limiti (session sayısı olarak)
- Önerilen özellikler
- Hedef müşteri profili
- Fiyatlandırma mantığı

ÖNEMLI:
- Token maliyetini karşılayacak fiyatlar
- Kar marjını ekle
- Psikolojik fiyatlandırma kullan (99, 90 ile biten)
- Türkiye pazarına uygun
- Rekabetçi

JSON formatında yanıt ver:
{
  \"plans\": [
    {
      \"name\": \"Plan adı\",
      \"monthly_price\": 299,
      \"yearly_price\": 2990,
      \"usage_tokens\": 1000,
      \"features\": [\"özellik1\", \"özellik2\"],
      \"target_customer\": \"hedef müşteri\",
      \"reasoning\": \"fiyatlandırma mantığı\"
    }
  ],
  \"market_analysis\": \"pazar analizi\",
  \"recommendations\": \"öneriler\",
  \"profit_summary\": {
    \"monthly_profit_per_plan\": [100, 200, 500, 1000],
    \"break_even_customers\": [10, 5, 3, 1]
  }
}";
    }

    /**
     * Akıllı fiyat hesaplama (psikolojik fiyatlandırma)
     */
    private function calculatePrice($basePrice, $minCost, $multiplier)
    {
        // Maliyetin üzerinde olmalı
        $calculatedPrice = max($basePrice, $minCost * $multiplier);
        
        // 100'e yuvarla ve psikolojik fiyat yap (99 ile biten)
        if ($calculatedPrice < 100) {
            return round($calculatedPrice / 10) * 10 - 1; // 29, 49, 79, 99
        } else {
            return round($calculatedPrice / 100) * 100 - 1; // 199, 299, 499, 999
        }
    }
    
    /**
     * Yıllık fiyat hesaplama (%15-20 indirimli)
     */
    private function calculateYearlyPrice($basePrice, $minCost, $multiplier)
    {
        $monthlyPrice = $this->calculatePrice($basePrice, $minCost, $multiplier);
        $yearlyPrice = $monthlyPrice * 12 * 0.85; // %15 indirim
        
        // 100'e yuvarla ve psikolojik fiyat yap
        if ($yearlyPrice < 1000) {
            return round($yearlyPrice / 100) * 100 - 1;
        } else {
            return round($yearlyPrice / 1000) * 1000 - 1;
        }
    }

    /**
     * Fallback fiyat önerisi (AI olmadan)
     */
    private function fallbackPriceRecommendation($costData, $profitMargin, $targetMarket)
    {
        // Basit matematik ile fiyat hesaplama
        $baseCostTRY = $costData['cost_try'];
        
        // Pazar bazlı temel fiyatlar (TRY)
        $basePrices = [
            'budget' => [199, 499, 999, 1999],      // Ekonomik pazar
            'standard' => [299, 799, 1499, 2999],   // Standart pazar
            'premium' => [499, 1299, 2499, 4999],   // Premium pazar
            'enterprise' => [999, 2499, 4999, 9999], // Kurumsal pazar
        ];
        
        $prices = $basePrices[$targetMarket] ?? $basePrices['standard'];
        
        // Kar marjına göre fiyatları ayarla (ama makul sınırlar içinde)
        $priceMultiplier = 1 + min($profitMargin, 3); // Max %300 artış
        
        // Token limitleri (makul değerler)
        $tokenLimits = [
            500,   // Başlangıç
            1500,  // Profesyonel
            5000,  // İş
            15000  // Kurumsal
        ];
        
        // 4 plan hesapla
        $plans = [
            [
                'name' => 'Başlangıç',
                'monthly_price' => $this->calculatePrice($prices[0], $baseCostTRY * 0.2, $priceMultiplier),
                'yearly_price' => $this->calculateYearlyPrice($prices[0], $baseCostTRY * 0.2, $priceMultiplier),
                'usage_tokens' => $tokenLimits[0],
                'features' => [
                    '1 Proje',
                    'Temel AI Chatbot',
                    'Email Destek',
                    'Standart Raporlar'
                ],
                'target_customer' => 'Küçük işletmeler, girişimciler',
                'reasoning' => 'Giriş seviyesi plan, düşük kullanım için optimal'
            ],
            [
                'name' => 'Profesyonel',
                'monthly_price' => $this->calculatePrice($prices[1], $baseCostTRY * 0.5, $priceMultiplier),
                'yearly_price' => $this->calculateYearlyPrice($prices[1], $baseCostTRY * 0.5, $priceMultiplier),
                'usage_tokens' => $tokenLimits[1],
                'features' => [
                    '3 Proje',
                    'Gelişmiş AI Chatbot',
                    'Öncelikli Destek',
                    'Detaylı Raporlar',
                    'Özel Entegrasyonlar'
                ],
                'target_customer' => 'Büyüyen işletmeler, e-ticaret siteleri',
                'reasoning' => 'En popüler plan, orta ölçek için ideal'
            ],
            [
                'name' => 'İş',
                'monthly_price' => $this->calculatePrice($prices[2], $baseCostTRY * 1.0, $priceMultiplier),
                'yearly_price' => $this->calculateYearlyPrice($prices[2], $baseCostTRY * 1.0, $priceMultiplier),
                'usage_tokens' => $tokenLimits[2],
                'features' => [
                    '10 Proje',
                    'Premium AI Chatbot',
                    '7/24 Destek',
                    'API Erişimi',
                    'Özel Widget Tasarımı',
                    'Gelişmiş Analytics'
                ],
                'target_customer' => 'Orta ölçekli şirketler',
                'reasoning' => 'Yüksek trafikli siteler için güçlü özellikler'
            ],
            [
                'name' => 'Kurumsal',
                'monthly_price' => $this->calculatePrice($prices[3], $baseCostTRY * 2.0, $priceMultiplier),
                'yearly_price' => $this->calculateYearlyPrice($prices[3], $baseCostTRY * 2.0, $priceMultiplier),
                'usage_tokens' => $tokenLimits[3],
                'features' => [
                    'Sınırsız Proje',
                    'Özel AI Modeli',
                    'Özel Hesap Yöneticisi',
                    'SLA Garantisi',
                    'Özel Altyapı',
                    'Özel Geliştirmeler',
                    'Beyaz Etiket'
                ],
                'target_customer' => 'Büyük şirketler, kurumlar',
                'reasoning' => 'Maksimum özellik ve destek'
            ]
        ];

        $profitPercentage = $profitMargin * 100;
        
        // Her planın maliyetini hesapla
        $planCosts = [
            $baseCostTRY * 0.2,  // Başlangıç
            $baseCostTRY * 0.5,  // Profesyonel
            $baseCostTRY * 1.0,  // İş
            $baseCostTRY * 2.0,  // Kurumsal
        ];
        
        return response()->json([
            'success' => true,
            'data' => [
                'plans' => $plans,
                'market_analysis' => "Türkiye SaaS pazarı için {$targetMarket} segmentine uygun fiyatlandırma. Token maliyeti ve %{$profitPercentage} kar marjı gözetilerek hesaplandı. Psikolojik fiyatlandırma uygulandı.",
                'recommendations' => 'Profesyonel plan en popüler olacaktır. Yıllık planları öne çıkararak daha stabil gelir sağlayabilirsiniz. Token kullanımını düzenli takip edin. Fiyatlar pazar standardına ve maliyete göre optimize edilmiştir.',
                'profit_summary' => [
                    'monthly_profit_per_plan' => [
                        max(0, round($plans[0]['monthly_price'] - $planCosts[0])),
                        max(0, round($plans[1]['monthly_price'] - $planCosts[1])),
                        max(0, round($plans[2]['monthly_price'] - $planCosts[2])),
                        max(0, round($plans[3]['monthly_price'] - $planCosts[3])),
                    ],
                    'break_even_customers' => [
                        $plans[0]['monthly_price'] > $planCosts[0] ? max(1, ceil($planCosts[0] / ($plans[0]['monthly_price'] - $planCosts[0]))) : 1,
                        $plans[1]['monthly_price'] > $planCosts[1] ? max(1, ceil($planCosts[1] / ($plans[1]['monthly_price'] - $planCosts[1]))) : 1,
                        $plans[2]['monthly_price'] > $planCosts[2] ? max(1, ceil($planCosts[2] / ($plans[2]['monthly_price'] - $planCosts[2]))) : 1,
                        $plans[3]['monthly_price'] > $planCosts[3] ? max(1, ceil($planCosts[3] / ($plans[3]['monthly_price'] - $planCosts[3]))) : 1,
                    ]
                ]
            ],
            'cost_analysis' => $costData,
            'source' => 'fallback'
        ]);
    }

    /**
     * Plan veritabanına kaydet
     */
    public function savePlan(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'monthly_price' => 'required|numeric|min:0',
            'yearly_price' => 'required|numeric|min:0',
            'usage_tokens' => 'required|integer|min:-1',
            'features' => 'required|array',
            'token_reset_period' => 'required|in:monthly,yearly',
        ]);

        try {
            $plan = Plan::create([
                'name' => $request->name,
                'price' => $request->monthly_price,
                'yearly_price' => $request->yearly_price,
                'billing_cycle' => 'monthly',
                'features' => $request->features,
                'is_active' => true,
                'trial_days' => 0,
                'usage_tokens' => $request->usage_tokens,
                'token_reset_period' => $request->token_reset_period,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Plan başarıyla oluşturuldu',
                'plan' => $plan
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Plan oluşturulurken hata oluştu: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Excel export
     */
    public function exportCalculation(Request $request)
    {
        // CSV formatında export
        $data = $request->input('data');
        
        $csv = "Token Hesaplama Raporu\n\n";
        $csv .= "Tarih," . now()->format('Y-m-d H:i:s') . "\n\n";
        $csv .= "PARAMETRE,DEĞER\n";
        $csv .= "Session Sayısı," . $data['sessions'] . "\n";
        $csv .= "Mesaj/Session," . $data['messages_per_session'] . "\n";
        $csv .= "Model," . $data['model'] . "\n";
        $csv .= "Toplam Mesaj," . $data['total_messages'] . "\n\n";
        
        $csv .= "TOKEN KULLANIMI\n";
        $csv .= "Input Tokens," . $data['input_tokens'] . "\n";
        $csv .= "Output Tokens," . $data['output_tokens'] . "\n";
        $csv .= "Toplam Tokens," . $data['total_tokens'] . "\n\n";
        
        $csv .= "MALİYETLER\n";
        $csv .= "Input Maliyet (USD),$" . $data['input_cost_usd'] . "\n";
        $csv .= "Output Maliyet (USD),$" . $data['output_cost_usd'] . "\n";
        $csv .= "Toplam Maliyet (USD),$" . $data['total_cost_usd'] . "\n";
        $csv .= "Toplam Maliyet (TRY),₺" . $data['total_cost_try'] . "\n";
        $csv .= "Session Başına Maliyet,$" . $data['cost_per_session_usd'] . "\n";
        $csv .= "Mesaj Başına Maliyet,$" . $data['cost_per_message_usd'] . "\n";

        $filename = 'token-calculation-' . now()->format('Y-m-d-His') . '.csv';
        
        return response($csv, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }
}

