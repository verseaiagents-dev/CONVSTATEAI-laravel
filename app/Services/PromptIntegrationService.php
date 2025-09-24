<?php

namespace App\Services;

use App\Models\PromptTemplate;
use App\Services\PromptManagementService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class PromptIntegrationService
{
    protected $promptService;

    public function __construct(PromptManagementService $promptService)
    {
        $this->promptService = $promptService;
    }

    /**
     * Intent detection için dinamik prompt al
     */
    public function getIntentDetectionPrompt(array $context = []): string
    {
        $prompt = $this->getPromptByCategory('intent_detection', $context);
        
        if ($prompt) {
            return $this->processPrompt($prompt, $context);
        }

        // Fallback - mevcut statik prompt
        return $this->getFallbackIntentPrompt($context);
    }

    /**
     * Response generation için dinamik prompt al
     */
    public function getResponseGenerationPrompt(string $intent, array $context = []): string
    {
        $category = $this->mapIntentToCategory($intent);
        $prompt = $this->getPromptByCategory($category, $context);
        
        if ($prompt) {
            return $this->processPrompt($prompt, $context);
        }

        // Fallback - mevcut statik prompt
        return $this->getFallbackResponsePrompt($intent, $context);
    }

    /**
     * Template message için dinamik prompt al
     */
    public function getTemplatePrompt(string $templateType, array $context = []): string
    {
        $category = $this->mapTemplateToCategory($templateType);
        $prompt = $this->getPromptByCategory($category, $context);
        
        if ($prompt) {
            return $this->processPrompt($prompt, $context);
        }

        // Fallback - template için varsayılan prompt
        return $this->getFallbackTemplatePrompt($templateType, $context);
    }

    /**
     * Product recommendation template için prompt getirir.
     */
    public function getProductRecommendationPrompt(array $context = [], string $environment = 'production', string $language = 'tr'): ?string
    {
        return $this->getPrompt('product_recommendation_template_prompt', $context, $environment, $language);
    }

    /**
     * Product detail template için prompt getirir.
     */
    public function getProductDetailPrompt(array $context = [], string $environment = 'production', string $language = 'tr'): ?string
    {
        return $this->getPrompt('product_detail_template_prompt', $context, $environment, $language);
    }

    /**
     * Order tracking template için prompt getirir.
     */
    public function getOrderTrackingPrompt(array $context = [], string $environment = 'production', string $language = 'tr'): ?string
    {
        return $this->getPrompt('order_tracking_template_prompt', $context, $environment, $language);
    }

    /**
     * Cargo tracking template için prompt getirir.
     */
    public function getCargoTrackingPrompt(array $context = [], string $environment = 'production', string $language = 'tr'): ?string
    {
        return $this->getPrompt('cargo_tracking_template_prompt', $context, $environment, $language);
    }

    /**
     * FAQ template için prompt getirir.
     */
    public function getFAQPrompt(array $context = [], string $environment = 'production', string $language = 'tr'): ?string
    {
        return $this->getPrompt('faq_template_prompt', $context, $environment, $language);
    }

    /**
     * Welcome template için prompt getirir.
     */
    public function getWelcomePrompt(array $context = [], string $environment = 'production', string $language = 'tr'): ?string
    {
        return $this->getPrompt('welcome_template_prompt', $context, $environment, $language);
    }

    /**
     * Error template için prompt getirir.
     */
    public function getErrorPrompt(array $context = [], string $environment = 'production', string $language = 'tr'): ?string
    {
        return $this->getPrompt('error_template_prompt', $context, $environment, $language);
    }

    /**
     * Kategoriye göre aktif prompt al
     */
    private function getPromptByCategory(string $category, array $context = []): ?PromptTemplate
    {
        $cacheKey = "prompt_{$category}_" . md5(json_encode($context));
        
        return Cache::remember($cacheKey, 300, function () use ($category, $context) {
            // Önce production ortamından aktif prompt'u al
            $prompt = PromptTemplate::where('category', $category)
                ->where('is_active', true)
                ->where('environment', 'production')
                ->orderBy('priority', 'desc')
                ->first();

            if (!$prompt) {
                // Production'da yoksa test ortamından al
                $prompt = PromptTemplate::where('category', $category)
                    ->where('is_active', true)
                    ->where('environment', 'test')
                    ->orderBy('priority', 'desc')
                    ->first();
            }

            return $prompt;
        });
    }

    /**
     * Prompt'u context ile işle
     */
    private function processPrompt(PromptTemplate $prompt, array $context = []): string
    {
        $content = $prompt->content;
        
        // Değişkenleri context ile değiştir
        foreach ($context as $key => $value) {
            $content = str_replace("{{$key}}", $value, $content);
        }

        // Prompt'un kendi değişkenlerini de işle
        if (!empty($prompt->variables)) {
            foreach ($prompt->variables as $variable) {
                $value = $context[$variable] ?? "[{$variable}]";
                $content = str_replace("{{$variable}}", $value, $content);
            }
        }

        return $content;
    }

    /**
     * Intent'i kategoriye map et
     */
    private function mapIntentToCategory(string $intent): string
    {
        $mapping = [
            'product_search' => 'product_recommendation',
            'product_recommendation' => 'product_recommendation',
            'product_inquiry' => 'product_detail',
            'price_inquiry' => 'price_comparison',
            'order_status' => 'order_tracking',
            'cargo_tracking' => 'order_tracking',
            'faq_search' => 'faq',
            'general_help' => 'customer_support',
            'welcome' => 'welcome',
            'error' => 'error_handling'
        ];

        return $mapping[$intent] ?? 'general';
    }

    /**
     * Template type'ı kategoriye map et
     */
    private function mapTemplateToCategory(string $templateType): string
    {
        $mapping = [
            'product_recommendation' => 'product_recommendation',
            'product_detail' => 'product_detail',
            'order_tracking' => 'order_tracking',
            'cargo_tracking' => 'order_tracking',
            'price_comparison' => 'price_comparison',
            'faq' => 'faq',
            'welcome' => 'welcome',
            'error' => 'error_handling',
            'general' => 'general'
        ];

        return $mapping[$templateType] ?? 'general';
    }

    /**
     * Fallback intent detection prompt
     */
    private function getFallbackIntentPrompt(array $context): string
    {
        return "Sen bir e-ticaret intent detection uzmanısın. Kullanıcının sorgusunu analiz et ve aşağıdaki intent'lerden birini belirle.

        Intent Categories:
        - product_search: Ürün arama, bulma, listeleme
        - product_recommendation: Ürün önerisi, tavsiye
        - product_inquiry: Ürün bilgisi, detay, özellik
        - price_inquiry: Fiyat sorgulama
        - order_status: Sipariş durumu, takip
        - cargo_tracking: Kargo takibi
        - faq_search: Sık sorulan sorular
        - general_help: Genel yardım, destek
        - welcome: Hoş geldin mesajı
        - error: Hata durumu

        Context: " . json_encode($context, JSON_UNESCAPED_UNICODE) . "

        Yanıtı şu formatta ver:
        {
            \"intent\": \"intent_name\",
            \"confidence\": 0.95,
            \"category\": \"intent_category\",
            \"entities\": [\"entity1\", \"entity2\"]
        }";
    }

    /**
     * Fallback response generation prompt
     */
    private function getFallbackResponsePrompt(string $intent, array $context): string
    {
        $basePrompt = "Sen bir e-ticaret asistanısın. Kullanıcının sorusuna, verilen bilgileri kullanarak yardımcı ol.

        Context: " . json_encode($context, JSON_UNESCAPED_UNICODE) . "

        Kurallar:
        1. Sadece verilen bilgileri kullan
        2. Türkçe yanıt ver
        3. Kısa ve net ol
        4. Eğer bilgi yoksa, bilgi olmadığını belirt
        5. Ürün önerilerinde fiyat ve özellik bilgisi ver";

        // Intent'e özel ek talimatlar
        switch ($intent) {
            case 'product_recommendation':
                $basePrompt .= "\n\nÖzel Talimatlar:\n- Ürün önerilerinde fiyat, özellik ve avantajları belirt\n- Kullanıcının ihtiyaçlarına uygun öneriler yap\n- Ürün karşılaştırması yapabilirsin";
                break;
            case 'product_inquiry':
                $basePrompt .= "\n\nÖzel Talimatlar:\n- Ürün detaylarını net bir şekilde açıkla\n- Teknik özellikleri liste halinde ver\n- Fiyat ve stok durumunu belirt";
                break;
            case 'order_status':
                $basePrompt .= "\n\nÖzel Talimatlar:\n- Sipariş durumunu açık bir şekilde belirt\n- Kargo takip bilgilerini ver\n- Tahmini teslimat tarihini söyle";
                break;
        }

        return $basePrompt;
    }

    /**
     * Fallback template prompt
     */
    private function getFallbackTemplatePrompt(string $templateType, array $context): string
    {
        $templates = [
            'product_recommendation' => "Size özel ürün önerileri hazırladım. Bu ürünler ihtiyaçlarınıza uygun olarak seçilmiştir.",
            'product_detail' => "Ürün detayları yükleniyor. Size en güncel bilgileri sunacağım.",
            'order_tracking' => "Sipariş durumunuzu kontrol ediyorum. En güncel bilgileri size sunacağım.",
            'cargo_tracking' => "Kargo takip bilgilerinizi kontrol ediyorum.",
            'welcome' => "Hoş geldiniz! Size nasıl yardımcı olabilirim?",
            'error' => "Üzgünüm, bir hata oluştu. Lütfen daha sonra tekrar deneyin."
        ];

        return $templates[$templateType] ?? "Size yardımcı olmaya çalışıyorum.";
    }

    /**
     * Prompt kullanım istatistiklerini güncelle
     */
    public function updatePromptUsage(string $category, int $promptId): void
    {
        try {
            $prompt = PromptTemplate::find($promptId);
            if ($prompt) {
                // Usage count'u artır (metadata'da saklanır)
                $metadata = $prompt->metadata ?? [];
                $metadata['usage_count'] = ($metadata['usage_count'] ?? 0) + 1;
                $metadata['last_used'] = now()->toISOString();
                
                $prompt->update(['metadata' => $metadata]);
            }
        } catch (\Exception $e) {
            Log::error('Error updating prompt usage', [
                'category' => $category,
                'prompt_id' => $promptId,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Tüm aktif prompt'ları getir
     */
    public function getAllActivePrompts(): array
    {
        return Cache::remember('all_active_prompts', 300, function () {
            return PromptTemplate::where('is_active', true)
                ->orderBy('category')
                ->orderBy('priority', 'desc')
                ->get()
                ->groupBy('category')
                ->toArray();
        });
    }
}
