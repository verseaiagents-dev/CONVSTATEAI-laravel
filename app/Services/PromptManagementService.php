<?php

namespace App\Services;

use App\Models\PromptTemplate;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class PromptManagementService
{
    /**
     * Prompt kategorilerini getir
     */
    public function getCategories(): array
    {
        return [
            'general' => 'Genel',
            'product_recommendation' => 'Ürün Önerisi',
            'product_detail' => 'Ürün Detayı',
            'price_comparison' => 'Fiyat Karşılaştırma',
            'order_tracking' => 'Sipariş Takibi',
            'customer_support' => 'Müşteri Destek',
            'marketing' => 'Pazarlama',
            'technical' => 'Teknik',
            'faq' => 'Sık Sorulan Sorular',
            'welcome' => 'Hoş Geldin',
            'error_handling' => 'Hata Yönetimi',
            'conversation' => 'Konuşma',
            'analysis' => 'Analiz',
            'reporting' => 'Raporlama'
        ];
    }

    /**
     * Prompt istatistiklerini getir
     */
    public function getStatistics(): array
    {
        return Cache::remember('prompt_statistics', 300, function () {
            $total = PromptTemplate::count();
            $active = PromptTemplate::where('is_active', true)->count();
            $inactive = $total - $active;
            $production = PromptTemplate::where('environment', 'production')->count();
            $test = PromptTemplate::where('environment', 'test')->count();
            
            $categories = PromptTemplate::selectRaw('category, COUNT(*) as count')
                ->groupBy('category')
                ->pluck('count', 'category')
                ->toArray();

            $recent = PromptTemplate::where('created_at', '>=', now()->subDays(7))->count();

            return [
                'total' => $total,
                'active' => $active,
                'inactive' => $inactive,
                'production' => $production,
                'test' => $test,
                'categories' => $categories,
                'recent' => $recent,
                'completion_rate' => $total > 0 ? round(($active / $total) * 100, 2) : 0
            ];
        });
    }

    /**
     * Prompt test et
     */
    public function testPrompt(string $content, array $variables = [], array $testData = []): array
    {
        try {
            // Değişkenleri prompt içeriğine yerleştir
            $processedContent = $this->processVariables($content, $variables);
            
            // Test verilerini işle
            $processedContent = $this->processTestData($processedContent, $testData);
            
            // Prompt uzunluğunu kontrol et
            $length = strlen($processedContent);
            $wordCount = str_word_count($processedContent);
            
            // Token tahmini (yaklaşık)
            $estimatedTokens = intval($length / 4);
            
            return [
                'success' => true,
                'processed_content' => $processedContent,
                'length' => $length,
                'word_count' => $wordCount,
                'estimated_tokens' => $estimatedTokens,
                'variables_used' => $this->extractVariables($content),
                'test_data_applied' => !empty($testData)
            ];
            
        } catch (\Exception $e) {
            Log::error('Error testing prompt', [
                'error' => $e->getMessage(),
                'content' => $content,
                'variables' => $variables
            ]);
            
            return [
                'success' => false,
                'error' => 'Test sırasında hata oluştu: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Prompt performans analizi
     */
    public function analyzePromptPerformance(int $promptId): array
    {
        try {
            $prompt = PromptTemplate::findOrFail($promptId);
            
            // Temel analiz
            $content = $prompt->content;
            $length = strlen($content);
            $wordCount = str_word_count($content);
            $estimatedTokens = intval($length / 4);
            
            // Karmaşıklık analizi
            $complexity = $this->analyzeComplexity($content);
            
            // Değişken analizi
            $variables = $this->extractVariables($content);
            $variableCount = count($variables);
            
            // Kategori analizi
            $categoryScore = $this->getCategoryScore($prompt->category);
            
            return [
                'basic_metrics' => [
                    'length' => $length,
                    'word_count' => $wordCount,
                    'estimated_tokens' => $estimatedTokens,
                    'variable_count' => $variableCount
                ],
                'complexity' => $complexity,
                'variables' => $variables,
                'category_score' => $categoryScore,
                'recommendations' => $this->generateRecommendations($content, $complexity, $variableCount)
            ];
            
        } catch (\Exception $e) {
            Log::error('Error analyzing prompt performance', [
                'prompt_id' => $promptId,
                'error' => $e->getMessage()
            ]);
            
            return [
                'error' => 'Analiz sırasında hata oluştu: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Prompt optimizasyonu
     */
    public function optimizePrompt(int $promptId): array
    {
        try {
            $prompt = PromptTemplate::findOrFail($promptId);
            $content = $prompt->content;
            
            $optimizations = [];
            
            // Uzunluk optimizasyonu
            if (strlen($content) > 2000) {
                $optimizations[] = [
                    'type' => 'length',
                    'message' => 'Prompt çok uzun. Daha kısa ve öz ifadeler kullanın.',
                    'priority' => 'high'
                ];
            }
            
            // Değişken optimizasyonu
            $variables = $this->extractVariables($content);
            if (count($variables) > 10) {
                $optimizations[] = [
                    'type' => 'variables',
                    'message' => 'Çok fazla değişken kullanılıyor. Değişkenleri gruplandırın.',
                    'priority' => 'medium'
                ];
            }
            
            // Dil optimizasyonu
            if (strpos($content, 'lütfen') !== false || strpos($content, 'rica ederim') !== false) {
                $optimizations[] = [
                    'type' => 'language',
                    'message' => 'Daha direkt ve net ifadeler kullanın.',
                    'priority' => 'low'
                ];
            }
            
            return [
                'optimizations' => $optimizations,
                'optimized_content' => $this->applyOptimizations($content, $optimizations),
                'score' => $this->calculateOptimizationScore($content, $optimizations)
            ];
            
        } catch (\Exception $e) {
            Log::error('Error optimizing prompt', [
                'prompt_id' => $promptId,
                'error' => $e->getMessage()
            ]);
            
            return [
                'error' => 'Optimizasyon sırasında hata oluştu: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Değişkenleri işle
     */
    private function processVariables(string $content, array $variables): string
    {
        $processed = $content;
        
        foreach ($variables as $key => $value) {
            $processed = str_replace("{$key}", $value, $processed);
        }
        
        return $processed;
    }

    /**
     * Test verilerini işle
     */
    private function processTestData(string $content, array $testData): string
    {
        $processed = $content;
        
        foreach ($testData as $key => $value) {
            $processed = str_replace("{$key}", $value, $processed);
        }
        
        return $processed;
    }

    /**
     * İçerikten değişkenleri çıkar
     */
    private function extractVariables(string $content): array
    {
        preg_match_all('/\{(\w+)\}/', $content, $matches);
        return array_unique($matches[1]);
    }

    /**
     * Karmaşıklık analizi
     */
    private function analyzeComplexity(string $content): array
    {
        $length = strlen($content);
        $wordCount = str_word_count($content);
        $sentenceCount = substr_count($content, '.') + substr_count($content, '!') + substr_count($content, '?');
        
        $avgWordsPerSentence = $sentenceCount > 0 ? $wordCount / $sentenceCount : 0;
        
        $complexityScore = 0;
        
        // Uzunluk puanı
        if ($length > 2000) $complexityScore += 3;
        elseif ($length > 1000) $complexityScore += 2;
        elseif ($length > 500) $complexityScore += 1;
        
        // Cümle karmaşıklığı
        if ($avgWordsPerSentence > 20) $complexityScore += 2;
        elseif ($avgWordsPerSentence > 15) $complexityScore += 1;
        
        // Özel karakterler
        $specialChars = preg_match_all('/[^\w\s]/', $content);
        if ($specialChars > 50) $complexityScore += 1;
        
        $level = 'low';
        if ($complexityScore >= 5) $level = 'high';
        elseif ($complexityScore >= 3) $level = 'medium';
        
        return [
            'score' => $complexityScore,
            'level' => $level,
            'word_count' => $wordCount,
            'sentence_count' => $sentenceCount,
            'avg_words_per_sentence' => round($avgWordsPerSentence, 2)
        ];
    }

    /**
     * Kategori puanı
     */
    private function getCategoryScore(string $category): int
    {
        $scores = [
            'general' => 5,
            'product_recommendation' => 8,
            'product_detail' => 7,
            'price_comparison' => 6,
            'order_tracking' => 7,
            'customer_support' => 9,
            'marketing' => 6,
            'technical' => 8,
            'faq' => 7,
            'welcome' => 5,
            'error_handling' => 8,
            'conversation' => 7,
            'analysis' => 8,
            'reporting' => 6
        ];
        
        return $scores[$category] ?? 5;
    }

    /**
     * Öneriler oluştur
     */
    private function generateRecommendations(string $content, array $complexity, int $variableCount): array
    {
        $recommendations = [];
        
        if ($complexity['level'] === 'high') {
            $recommendations[] = 'Prompt karmaşıklığını azaltın. Daha kısa cümleler kullanın.';
        }
        
        if ($variableCount > 8) {
            $recommendations[] = 'Değişken sayısını azaltın veya değişkenleri gruplandırın.';
        }
        
        if (strlen($content) > 1500) {
            $recommendations[] = 'Prompt uzunluğunu azaltın. Daha öz ifadeler kullanın.';
        }
        
        if (empty($recommendations)) {
            $recommendations[] = 'Prompt iyi optimize edilmiş görünüyor.';
        }
        
        return $recommendations;
    }

    /**
     * Optimizasyonları uygula
     */
    private function applyOptimizations(string $content, array $optimizations): string
    {
        $optimized = $content;
        
        foreach ($optimizations as $optimization) {
            if ($optimization['type'] === 'language') {
                $optimized = str_replace(['lütfen', 'rica ederim'], '', $optimized);
                $optimized = preg_replace('/\s+/', ' ', $optimized);
            }
        }
        
        return trim($optimized);
    }

    /**
     * Optimizasyon puanı hesapla
     */
    private function calculateOptimizationScore(string $content, array $optimizations): int
    {
        $baseScore = 100;
        
        foreach ($optimizations as $optimization) {
            if ($optimization['priority'] === 'high') {
                $baseScore -= 20;
            } elseif ($optimization['priority'] === 'medium') {
                $baseScore -= 10;
            } else {
                $baseScore -= 5;
            }
        }
        
        return max(0, $baseScore);
    }
}
