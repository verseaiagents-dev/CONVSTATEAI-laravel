<?php

namespace App\Services\KnowledgeBase;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Carbon\Carbon;

class FAQOptimizationService
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
     * FAQ content'ini optimize eder ve soru-cevap çiftleri oluşturur
     */
    public function optimizeFAQContent(string $content, array $config = []): array
    {
        try {
            // Input validation
            if (empty($content) || !is_string($content)) {
                throw new \InvalidArgumentException('Content must be a non-empty string');
            }

            $config = array_merge([
                'max_questions' => 10,
                'question_style' => 'natural', // natural, formal, casual
                'answer_style' => 'detailed', // detailed, concise, step_by_step
                'language' => 'tr', // tr, en, mixed
                'category' => 'general'
            ], $config);

            // Content'i token limitine göre kısalt (OpenAI context limit: ~16k tokens)
            $truncatedContent = $this->truncateContentForAI($content);
            
            // Content'i analiz et
            $contentAnalysis = $this->analyzeContent($truncatedContent);
            
            // FAQ soruları oluştur
            $questions = $this->generateQuestions($truncatedContent, $contentAnalysis, $config);
            
            // FAQ cevapları oluştur
            $answers = $this->generateAnswers($truncatedContent, $questions, $config);
            
            // FAQ çiftlerini optimize et
            $optimizedFAQs = $this->optimizeFAQPairs($questions, $answers, $config);
            
            // Metadata oluştur
            $metadata = $this->createFAQMetadata($optimizedFAQs, $contentAnalysis, $config);

            return [
                'faqs' => $optimizedFAQs,
                'metadata' => $metadata,
                'content_analysis' => $contentAnalysis,
                'optimization_score' => $this->calculateOptimizationScore($optimizedFAQs)
            ];

        } catch (\Exception $e) {
            Log::error('FAQ optimization error: ' . $e->getMessage(), [
                'content_length' => strlen($content ?? ''),
                'content_type' => gettype($content),
                'config' => $config,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            // Return fallback result instead of throwing exception
            return $this->getFallbackFAQResult($content, $config);
        }
    }

    /**
     * Content'i analiz eder
     */
    private function analyzeContent(string $content): array
    {
        try {
            // Content çok büyükse fallback kullan
            if (strlen($content) > 100000) { // 100KB'dan büyük
                return $this->fallbackContentAnalysis($content);
            }

            $systemPrompt = "Sen bir content analiz uzmanısın. Verilen content'i analiz et ve aşağıdaki bilgileri JSON formatında döndür:

            - main_topics: Ana konular (array)
            - key_concepts: Anahtar kavramlar (array)
            - target_audience: Hedef kitle (string)
            - content_type: Content tipi (product_info, how_to, troubleshooting, policy, etc.)
            - difficulty_level: Zorluk seviyesi (beginner, intermediate, advanced)
            - language_style: Dil stili (formal, casual, technical, friendly)
            - estimated_questions: Tahmini soru sayısı (integer)
            - content_summary: İçerik özeti (string)";

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Content-Type' => 'application/json',
            ])->post($this->baseUrl . '/chat/completions', [
                'model' => $this->model,
                'messages' => [
                    ['role' => 'system', 'content' => $systemPrompt],
                    ['role' => 'user', 'content' => $content]
                ],
                'temperature' => 0.3,
                'max_tokens' => 500
            ]);

            if (!$response->successful()) {
                throw new \Exception('OpenAI API Error: ' . $response->body());
            }

            $data = $response->json();
            $analysis = json_decode($data['choices'][0]['message']['content'], true);
            
            if (json_last_error() === JSON_ERROR_NONE) {
                return $analysis;
            }

            // Fallback analysis
            return $this->fallbackContentAnalysis($content);

        } catch (\Exception $e) {
            Log::warning('Content analysis failed, using fallback', ['error' => $e->getMessage()]);
            return $this->fallbackContentAnalysis($content);
        }
    }

    /**
     * FAQ soruları oluşturur
     */
    private function generateQuestions(string $content, array $analysis, array $config): array
    {
        try {
            // Content çok büyükse fallback kullan
            if (strlen($content) > 80000) { // 80KB'dan büyük
                return $this->fallbackQuestionGeneration($content, $analysis, $config);
            }

            $systemPrompt = $this->buildQuestionGenerationPrompt($analysis, $config);
            
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Content-Type' => 'application/json',
            ])->post($this->baseUrl . '/chat/completions', [
                'model' => $this->model,
                'messages' => [
                    ['role' => 'system', 'content' => $systemPrompt],
                    ['role' => 'user', 'content' => $content]
                ],
                'temperature' => 0.7,
                'max_tokens' => 800
            ]);

            if (!$response->successful()) {
                throw new \Exception('OpenAI API Error: ' . $response->body());
            }

            $data = $response->json();
            $questions = json_decode($data['choices'][0]['message']['content'], true);
            
            if (json_last_error() === JSON_ERROR_NONE && isset($questions['questions'])) {
                return $questions['questions'];
            }

            // Fallback question generation
            return $this->fallbackQuestionGeneration($content, $analysis, $config);

        } catch (\Exception $e) {
            Log::warning('Question generation failed, using fallback', ['error' => $e->getMessage()]);
            return $this->fallbackQuestionGeneration($content, $analysis, $config);
        }
    }

    /**
     * FAQ cevapları oluşturur
     */
    private function generateAnswers(string $content, array $questions, array $config): array
    {
        try {
            // Content çok büyükse fallback kullan
            if (strlen($content) > 60000) { // 60KB'dan büyük
                $fallbackAnswers = [];
                foreach ($questions as $index => $question) {
                    $fallbackAnswers[$index] = $this->fallbackAnswerGeneration($content, $question);
                }
                return $fallbackAnswers;
            }

            $systemPrompt = $this->buildAnswerGenerationPrompt($config);
            
            $answers = [];
            
            foreach ($questions as $index => $question) {
                $userPrompt = "Soru: {$question}\n\nContent: {$content}\n\nBu soruya detaylı ve doğru bir cevap ver.";
                
                $response = Http::withHeaders([
                    'Authorization' => 'Bearer ' . $this->apiKey,
                    'Content-Type' => 'application/json',
                ])->post($this->baseUrl . '/chat/completions', [
                    'model' => $this->model,
                    'messages' => [
                        ['role' => 'system', 'content' => $systemPrompt],
                        ['role' => 'user', 'content' => $userPrompt]
                    ],
                    'temperature' => 0.5,
                    'max_tokens' => 600
                ]);

                if ($response->successful()) {
                    $data = $response->json();
                    $answers[$index] = $data['choices'][0]['message']['content'];
                } else {
                    $answers[$index] = $this->fallbackAnswerGeneration($content, $question);
                }
                
                // API rate limit koruması
                usleep(100000); // 0.1 saniye bekle
            }

            return $answers;

        } catch (\Exception $e) {
            Log::warning('Answer generation failed, using fallback', ['error' => $e->getMessage()]);
            
            // Ensure we return an array of strings
            $fallbackAnswers = [];
            foreach ($questions as $index => $question) {
                $fallbackAnswers[$index] = $this->fallbackAnswerGeneration($content, $question);
            }
            return $fallbackAnswers;
        }
    }

    /**
     * FAQ çiftlerini optimize eder
     */
    private function optimizeFAQPairs(array $questions, array $answers, array $config): array
    {
        $optimizedFAQs = [];
        
        foreach ($questions as $index => $question) {
            if (isset($answers[$index])) {
                $optimizedFAQs[] = [
                    'id' => $index + 1,
                    'question' => $this->optimizeQuestion($question, $config),
                    'answer' => $this->optimizeAnswer($answers[$index], $config),
                    'category' => $this->categorizeFAQ($question, $answers[$index]),
                    'difficulty' => $this->assessDifficulty($question, $answers[$index]),
                    'keywords' => $this->extractKeywords($question, $answers[$index]),
                    'search_terms' => $this->generateSearchTerms($question, $answers[$index])
                ];
            }
        }

        return $optimizedFAQs;
    }

    /**
     * Soru oluşturma prompt'u oluşturur
     */
    private function buildQuestionGenerationPrompt(array $analysis, array $config): string
    {
        $language = $config['language'] === 'tr' ? 'Türkçe' : 'İngilizce';
        $style = $this->getQuestionStyle($config['question_style']);
        
        return "Sen bir FAQ uzmanısın. Verilen content'ten {$config['max_questions']} adet kaliteli soru oluştur.

        Dil: {$language}
        Soru Stili: {$style}
        Ana Konular: " . implode(', ', $analysis['main_topics'] ?? []) . "
        Hedef Kitle: " . ($analysis['target_audience'] ?? 'Genel kullanıcılar') . "

        Kurallar:
        1. Sorular doğal ve anlaşılır olmalı
        2. Her soru farklı bir konuyu ele almalı
        3. Kullanıcıların gerçekten sorabileceği sorular olmalı
        4. Sorular content'teki bilgileri kapsamalı
        5. Sorular farklı zorluk seviyelerinde olmalı

        Yanıtı şu formatta ver:
        {
            \"questions\": [
                \"Soru 1\",
                \"Soru 2\",
                \"Soru 3\"
            ]
        }";
    }

    /**
     * Cevap oluşturma prompt'u oluşturur
     */
    private function buildAnswerGenerationPrompt(array $config): string
    {
        $style = $this->getAnswerStyle($config['answer_style']);
        $language = $config['language'] === 'tr' ? 'Türkçe' : 'İngilizce';
        
        return "Sen bir FAQ cevap uzmanısın. Verilen soruya detaylı ve doğru bir cevap ver.

        Dil: {$language}
        Cevap Stili: {$style}
        
        Kurallar:
        1. Cevap net ve anlaşılır olmalı
        2. Content'teki bilgileri kullan
        3. Gerekirse örnekler ver
        4. Adım adım açıklama yap
        5. Kullanıcı dostu dil kullan
        6. Teknik terimleri açıkla
        7. Pratik öneriler sun";
    }

    /**
     * Soru stili belirler
     */
    private function getQuestionStyle(string $style): string
    {
        $styles = [
            'natural' => 'Doğal, günlük konuşma dili',
            'formal' => 'Resmi, profesyonel dil',
            'casual' => 'Samimi, arkadaşça dil',
            'technical' => 'Teknik, uzman dili'
        ];
        
        return $styles[$style] ?? $styles['natural'];
    }

    /**
     * Cevap stili belirler
     */
    private function getAnswerStyle(string $style): string
    {
        $styles = [
            'detailed' => 'Detaylı, kapsamlı açıklama',
            'concise' => 'Kısa, öz açıklama',
            'step_by_step' => 'Adım adım açıklama',
            'technical' => 'Teknik detaylar ile'
        ];
        
        return $styles[$style] ?? $styles['detailed'];
    }

    /**
     * Soru optimizasyonu
     */
    private function optimizeQuestion(string $question, array $config): string
    {
        // Soru işareti kontrolü
        if (!str_ends_with(trim($question), '?')) {
            $question .= '?';
        }
        
        // Gereksiz kelimeleri temizle
        $question = preg_replace('/\b(acaba|belki|muhtemelen)\b/i', '', $question);
        
        return trim($question);
    }

    /**
     * Cevap optimizasyonu
     */
    private function optimizeAnswer(string $answer, array $config): string
    {
        // HTML tag'leri temizle
        $answer = strip_tags($answer);
        
        // Fazla boşlukları temizle
        $answer = preg_replace('/\s+/', ' ', $answer);
        
        // Cümle sonlarını düzelt
        $answer = preg_replace('/\s*([.!?])\s*/', '$1 ', $answer);
        
        return trim($answer);
    }

    /**
     * FAQ kategorilendirme
     */
    private function categorizeFAQ(string $question, string $answer): string
    {
        $question = strtolower($question);
        $answer = strtolower($answer);
        
        if (preg_match('/\b(nasıl|how|yapılır|kurulum|setup|install)\b/', $question)) {
            return 'how_to';
        } elseif (preg_match('/\b(sorun|problem|hata|error|troubleshoot)\b/', $question)) {
            return 'troubleshooting';
        } elseif (preg_match('/\b(fiyat|price|ücret|cost|ödeme|payment)\b/', $question)) {
            return 'pricing';
        } elseif (preg_match('/\b(özellik|feature|spec|teknik|technical)\b/', $question)) {
            return 'technical';
        } elseif (preg_match('/\b(politika|policy|koşul|term|şart|condition)\b/', $question)) {
            return 'policy';
        } else {
            return 'general';
        }
    }

    /**
     * Zorluk seviyesi değerlendirmesi
     */
    private function assessDifficulty(string $question, string $answer): string
    {
        $wordCount = str_word_count($answer);
        $technicalTerms = preg_match_all('/\b(api|sdk|framework|algorithm|protocol|encryption|database|server|client)\b/i', $answer);
        
        if ($wordCount > 100 || $technicalTerms > 3) {
            return 'advanced';
        } elseif ($wordCount > 50 || $technicalTerms > 1) {
            return 'intermediate';
        } else {
            return 'beginner';
        }
    }

    /**
     * Anahtar kelimeler çıkarır
     */
    private function extractKeywords(string $question, string $answer): array
    {
        $text = $question . ' ' . $answer;
        $text = strtolower($text);
        
        // Stop words
        $stopWords = ['ve', 'veya', 'ile', 'için', 'bu', 'şu', 'o', 'bir', 'da', 'de', 'mi', 'mı', 'mu', 'mü', 'nasıl', 'neden', 'ne', 'hangi', 'kim', 'nerede', 'ne zaman'];
        
        // Kelimeleri çıkar
        $words = preg_split('/\s+/', $text);
        $words = array_filter($words, function($word) use ($stopWords) {
            return strlen($word) > 2 && !in_array($word, $stopWords);
        });
        
        // Frekans hesapla
        $wordFreq = array_count_values($words);
        arsort($wordFreq);
        
        return array_slice(array_keys($wordFreq), 0, 10);
    }

    /**
     * Arama terimleri oluşturur
     */
    private function generateSearchTerms(string $question, string $answer): array
    {
        $keywords = $this->extractKeywords($question, $answer);
        $searchTerms = [];
        
        foreach ($keywords as $keyword) {
            $searchTerms[] = $keyword;
            $searchTerms[] = $keyword . ' nasıl';
            $searchTerms[] = $keyword . ' nedir';
            $searchTerms[] = $keyword . ' sorunu';
        }
        
        return array_slice($searchTerms, 0, 15);
    }

    /**
     * FAQ metadata oluşturur
     */
    private function createFAQMetadata(array $faqs, array $analysis, array $config): array
    {
        return [
            'total_faqs' => count($faqs),
            'categories' => array_count_values(array_column($faqs, 'category')),
            'difficulty_distribution' => array_count_values(array_column($faqs, 'difficulty')),
            'language' => $config['language'],
            'question_style' => $config['question_style'],
            'answer_style' => $config['answer_style'],
            'main_topics' => $analysis['main_topics'] ?? [],
            'target_audience' => $analysis['target_audience'] ?? 'Genel kullanıcılar',
            'created_at' => now()->toISOString(),
            'optimization_version' => '1.0'
        ];
    }

    /**
     * Optimizasyon skoru hesaplar
     */
    private function calculateOptimizationScore(array $faqs): float
    {
        if (empty($faqs)) return 0.0;
        
        $scores = [];
        
        foreach ($faqs as $faq) {
            $score = 0;
            
            // Soru kalitesi
            if (strlen($faq['question']) > 10 && strlen($faq['question']) < 100) $score += 0.3;
            if (str_ends_with($faq['question'], '?')) $score += 0.2;
            
            // Cevap kalitesi
            if (strlen($faq['answer']) > 20) $score += 0.3;
            if (count($faq['keywords']) > 3) $score += 0.1;
            if (count($faq['search_terms']) > 5) $score += 0.1;
            
            $scores[] = $score;
        }
        
        return round(array_sum($scores) / count($scores), 2);
    }

    /**
     * Fallback content analizi
     */
    private function fallbackContentAnalysis(string $content): array
    {
        return [
            'main_topics' => ['genel'],
            'key_concepts' => [],
            'target_audience' => 'Genel kullanıcılar',
            'content_type' => 'general',
            'difficulty_level' => 'beginner',
            'language_style' => 'casual',
            'estimated_questions' => 5,
            'content_summary' => substr($content, 0, 200) . '...'
        ];
    }

    /**
     * Fallback soru oluşturma
     */
    private function fallbackQuestionGeneration(string $content, array $analysis, array $config): array
    {
        $questions = [
            'Bu ürün/hizmet hakkında genel bilgi alabilir miyim?',
            'Nasıl kullanılır?',
            'Hangi özellikleri var?',
            'Fiyat bilgisi nedir?',
            'Destek alabilir miyim?'
        ];
        
        return array_slice($questions, 0, $config['max_questions']);
    }

    /**
     * Fallback answer generation
     */
    private function fallbackAnswerGeneration(string $content, $questions): string
    {
        if (is_array($questions)) {
            // Return first question's fallback answer
            return 'Bu soruya cevap için lütfen içeriği detaylı olarak inceleyin.';
        } else {
            return 'Bu soruya cevap için lütfen içeriği detaylı olarak inceleyin.';
        }
    }

    /**
     * OpenAI API'yi test eder
     */
    public function testConnection(): array
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
            ])->get($this->baseUrl . '/models');

            if ($response->successful()) {
                return [
                    'success' => true,
                    'message' => 'FAQ Optimization Service bağlantısı başarılı',
                    'models' => $response->json()['data'] ?? []
                ];
            } else {
                return [
                    'success' => false,
                    'message' => 'OpenAI API Error: ' . $response->body(),
                    'status' => $response->status()
                ];
            }
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Bağlantı hatası: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Fallback FAQ result when optimization fails
     */
    private function getFallbackFAQResult($content, array $config): array
    {
        $fallbackQuestions = [
            'Bu içerik hakkında genel bilgi alabilir miyim?',
            'Nasıl kullanılır?',
            'Hangi özellikleri var?',
            'Destek alabilir miyim?',
            'Daha fazla bilgi nereden bulabilirim?'
        ];

        $fallbackAnswers = [
            'Bu içerik hakkında detaylı bilgi için lütfen içeriği dikkatlice okuyun.',
            'Kullanım talimatları için lütfen içeriği inceleyin.',
            'Özellikler hakkında bilgi için içeriği detaylı olarak okuyun.',
            'Destek için müşteri hizmetleri ile iletişime geçin.',
            'Daha fazla bilgi için resmi dokümantasyonu inceleyin.'
        ];

        $maxQuestions = min($config['max_questions'] ?? 5, count($fallbackQuestions));
        
        $fallbackFAQs = [];
        for ($i = 0; $i < $maxQuestions; $i++) {
            $fallbackFAQs[] = [
                'id' => $i + 1,
                'question' => $fallbackQuestions[$i],
                'answer' => $fallbackAnswers[$i],
                'category' => 'general',
                'difficulty' => 'beginner',
                'keywords' => ['genel', 'bilgi', 'destek'],
                'search_terms' => ['genel bilgi', 'nasıl kullanılır', 'destek al']
            ];
        }

        return [
            'faqs' => $fallbackFAQs,
            'metadata' => [
                'total_faqs' => count($fallbackFAQs),
                'categories' => ['general' => count($fallbackFAQs)],
                'difficulty_distribution' => ['beginner' => count($fallbackFAQs)],
                'language' => $config['language'] ?? 'tr',
                'question_style' => $config['question_style'] ?? 'natural',
                'answer_style' => $config['answer_style'] ?? 'detailed',
                'main_topics' => ['genel'],
                'target_audience' => 'Genel kullanıcılar',
                'created_at' => Carbon::now()->toISOString(),
                'optimization_version' => 'fallback'
            ],
            'content_analysis' => [
                'main_topics' => ['genel'],
                'key_concepts' => [],
                'target_audience' => 'Genel kullanıcılar',
                'content_type' => 'general',
                'difficulty_level' => 'beginner',
                'language_style' => 'casual',
                'estimated_questions' => count($fallbackFAQs),
                'content_summary' => 'Fallback FAQ content'
            ],
            'optimization_score' => 0.5
        ];
    }

    /**
     * Content'i token limitine göre kısaltır
     */
    private function truncateContentForAI(string $content): string
    {
        // OpenAI context limit: ~16k tokens, güvenli margin için 12k
        $maxTokens = 12000;
        $estimatedTokens = $this->countTokens($content);
        
        if ($estimatedTokens <= $maxTokens) {
            return $content;
        }
        
        // Content'i kısalt
        $words = explode(' ', $content);
        $targetWords = (int) ($maxTokens * 4); // Yaklaşık kelime sayısı
        $truncatedWords = array_slice($words, 0, $targetWords);
        
        return implode(' ', $truncatedWords);
    }

    /**
     * Token sayısını hesaplar
     */
    private function countTokens(string $text): int
    {
        try {
            if (!mb_check_encoding($text, 'UTF-8')) {
                $text = mb_convert_encoding($text, 'UTF-8', 'auto');
            }
            if (empty($text)) {
                return 0;
            }
            $tokenCount = (int) ceil(mb_strlen($text, 'UTF-8') / 4);
            return max(1, $tokenCount);
        } catch (\Exception $e) {
            Log::warning('countTokens error, falling back to strlen: ' . $e->getMessage());
            return (int) ceil(strlen($text) / 4);
        }
    }
}
