<?php

namespace App\Services;

use App\Models\PromptTemplate;
use App\Services\PromptIntegrationService;
use Illuminate\Support\Facades\Log;

class TemplatePromptService
{
    protected $promptIntegration;

    public function __construct(PromptIntegrationService $promptIntegration)
    {
        $this->promptIntegration = $promptIntegration;
    }

    /**
     * Frontend template için dinamik mesaj oluştur
     */
    public function generateTemplateMessage(string $templateType, array $data = [], array $context = []): array
    {
        try {
            // Template type'a göre prompt al
            $prompt = $this->promptIntegration->getTemplatePrompt($templateType, $context);
            
            // Data ile prompt'u işle
            $processedPrompt = $this->processTemplatePrompt($prompt, $data, $context);
            
            // Template'e özel ek işlemler
            $message = $this->buildTemplateMessage($templateType, $processedPrompt, $data);
            
            return [
                'success' => true,
                'message' => $message,
                'template_type' => $templateType,
                'data' => $data
            ];
            
        } catch (\Exception $e) {
            Log::error('Template message generation failed', [
                'template_type' => $templateType,
                'error' => $e->getMessage()
            ]);
            
            return [
                'success' => false,
                'message' => $this->getFallbackMessage($templateType),
                'template_type' => $templateType,
                'data' => $data
            ];
        }
    }

    /**
     * Product recommendation template için mesaj oluştur
     */
    public function generateProductRecommendationMessage(array $products, array $context = []): array
    {
        $data = [
            'products' => $products,
            'product_count' => count($products),
            'context' => $context
        ];

        return $this->generateTemplateMessage('product_recommendation', $data, $context);
    }

    /**
     * Product detail template için mesaj oluştur
     */
    public function generateProductDetailMessage(array $product, array $context = []): array
    {
        $data = [
            'product' => $product,
            'context' => $context
        ];

        return $this->generateTemplateMessage('product_detail', $data, $context);
    }

    /**
     * Order tracking template için mesaj oluştur
     */
    public function generateOrderTrackingMessage(array $orderData, array $context = []): array
    {
        $data = [
            'order' => $orderData,
            'context' => $context
        ];

        return $this->generateTemplateMessage('order_tracking', $data, $context);
    }

    /**
     * Cargo tracking template için mesaj oluştur
     */
    public function generateCargoTrackingMessage(array $cargoData, array $context = []): array
    {
        $data = [
            'cargo' => $cargoData,
            'context' => $context
        ];

        return $this->generateTemplateMessage('cargo_tracking', $data, $context);
    }

    /**
     * FAQ template için mesaj oluştur
     */
    public function generateFAQMessage(array $faqData, array $context = []): array
    {
        $data = [
            'faq' => $faqData,
            'context' => $context
        ];

        return $this->generateTemplateMessage('faq', $data, $context);
    }

    /**
     * Welcome template için mesaj oluştur
     */
    public function generateWelcomeMessage(array $context = []): array
    {
        return $this->generateTemplateMessage('welcome', [], $context);
    }

    /**
     * Error template için mesaj oluştur
     */
    public function generateErrorMessage(string $errorType, array $context = []): array
    {
        $data = [
            'error_type' => $errorType,
            'context' => $context
        ];

        return $this->generateTemplateMessage('error', $data, $context);
    }

    /**
     * Template prompt'unu data ile işle
     */
    private function processTemplatePrompt(string $prompt, array $data, array $context): string
    {
        $processed = $prompt;
        
        // Data değişkenlerini işle
        foreach ($data as $key => $value) {
            if (is_array($value)) {
                $processed = str_replace("{{$key}}", json_encode($value, JSON_UNESCAPED_UNICODE), $processed);
            } else {
                $processed = str_replace("{{$key}}", $value, $processed);
            }
        }
        
        // Context değişkenlerini işle
        foreach ($context as $key => $value) {
            if (is_array($value)) {
                $processed = str_replace("{{$key}}", json_encode($value, JSON_UNESCAPED_UNICODE), $processed);
            } else {
                $processed = str_replace("{{$key}}", $value, $processed);
            }
        }
        
        return $processed;
    }

    /**
     * Template'e özel mesaj oluştur
     */
    private function buildTemplateMessage(string $templateType, string $prompt, array $data): array
    {
        $baseMessage = [
            'id' => uniqid(),
            'timestamp' => now()->toISOString(),
            'template_type' => $templateType,
            'prompt_used' => $prompt
        ];

        switch ($templateType) {
            case 'product_recommendation':
                return array_merge($baseMessage, [
                    'type' => 'product_recommendation',
                    'message' => $this->extractMessageFromPrompt($prompt),
                    'products' => $data['products'] ?? [],
                    'suggestions' => $this->generateProductSuggestions($data['products'] ?? [])
                ]);

            case 'product_detail':
                return array_merge($baseMessage, [
                    'type' => 'product_detail',
                    'message' => $this->extractMessageFromPrompt($prompt),
                    'product' => $data['product'] ?? null,
                    'actions' => $this->generateProductActions($data['product'] ?? [])
                ]);

            case 'order_tracking':
                return array_merge($baseMessage, [
                    'type' => 'order_tracking',
                    'message' => $this->extractMessageFromPrompt($prompt),
                    'order' => $data['order'] ?? null,
                    'actions' => $this->generateOrderActions()
                ]);

            case 'cargo_tracking':
                return array_merge($baseMessage, [
                    'type' => 'cargo_tracking',
                    'message' => $this->extractMessageFromPrompt($prompt),
                    'cargo' => $data['cargo'] ?? null,
                    'actions' => $this->generateCargoActions()
                ]);

            case 'faq':
                return array_merge($baseMessage, [
                    'type' => 'faq',
                    'message' => $this->extractMessageFromPrompt($prompt),
                    'faq' => $data['faq'] ?? null,
                    'suggestions' => $this->generateFAQSuggestions()
                ]);

            case 'welcome':
                return array_merge($baseMessage, [
                    'type' => 'welcome',
                    'message' => $this->extractMessageFromPrompt($prompt),
                    'suggestions' => $this->generateWelcomeSuggestions()
                ]);

            case 'error':
                return array_merge($baseMessage, [
                    'type' => 'error',
                    'message' => $this->extractMessageFromPrompt($prompt),
                    'error_type' => $data['error_type'] ?? 'general',
                    'actions' => $this->generateErrorActions()
                ]);

            default:
                return array_merge($baseMessage, [
                    'type' => 'general',
                    'message' => $this->extractMessageFromPrompt($prompt)
                ]);
        }
    }

    /**
     * Prompt'tan mesaj çıkar
     */
    private function extractMessageFromPrompt(string $prompt): string
    {
        // Eğer prompt JSON formatındaysa parse et
        if (preg_match('/\{.*\}/s', $prompt)) {
            $decoded = json_decode($prompt, true);
            if ($decoded && isset($decoded['message'])) {
                return $decoded['message'];
            }
        }
        
        // Normal prompt ise direkt döndür
        return $prompt;
    }

    /**
     * Ürün önerileri için öneriler oluştur
     */
    private function generateProductSuggestions(array $products): array
    {
        $suggestions = [];
        
        if (!empty($products)) {
            $suggestions[] = 'Daha fazla ürün gör';
            $suggestions[] = 'Fiyat filtrele';
            $suggestions[] = 'Kategori değiştir';
        }
        
        return $suggestions;
    }

    /**
     * Ürün detayı için aksiyonlar oluştur
     */
    private function generateProductActions(array $product): array
    {
        return [
            'view_details' => 'Detayları gör',
            'add_to_cart' => 'Sepete ekle',
            'compare' => 'Karşılaştır',
            'buy_now' => 'Hemen satın al'
        ];
    }

    /**
     * Sipariş takibi için aksiyonlar oluştur
     */
    private function generateOrderActions(): array
    {
        return [
            'track_order' => 'Sipariş takip et',
            'view_order' => 'Siparişi görüntüle',
            'contact_support' => 'Destek ile iletişim'
        ];
    }

    /**
     * Kargo takibi için aksiyonlar oluştur
     */
    private function generateCargoActions(): array
    {
        return [
            'track_cargo' => 'Kargo takip et',
            'get_updates' => 'Güncellemeleri al',
            'contact_courier' => 'Kargo firması ile iletişim'
        ];
    }

    /**
     * FAQ için öneriler oluştur
     */
    private function generateFAQSuggestions(): array
    {
        return [
            'Başka soru sor',
            'İlgili konular',
            'Yardım al',
            'Canlı destek'
        ];
    }

    /**
     * Hoş geldin mesajı için öneriler oluştur
     */
    private function generateWelcomeSuggestions(): array
    {
        return [
            'Ürün ara',
            'Kategorileri keşfet',
            'Yardım al',
            'Kampanyaları gör'
        ];
    }

    /**
     * Hata mesajı için aksiyonlar oluştur
     */
    private function generateErrorActions(): array
    {
        return [
            'try_again' => 'Tekrar dene',
            'contact_support' => 'Destek ile iletişim',
            'go_home' => 'Ana sayfaya dön'
        ];
    }

    /**
     * Fallback mesajları
     */
    private function getFallbackMessage(string $templateType): string
    {
        $fallbacks = [
            'product_recommendation' => 'Size özel ürün önerileri hazırlıyorum...',
            'product_detail' => 'Ürün detayları yükleniyor...',
            'order_tracking' => 'Sipariş durumunuzu kontrol ediyorum...',
            'cargo_tracking' => 'Kargo takip bilgilerinizi kontrol ediyorum...',
            'faq' => 'Sorunuzu yanıtlamaya çalışıyorum...',
            'welcome' => 'Hoş geldiniz! Size nasıl yardımcı olabilirim?',
            'error' => 'Üzgünüm, bir hata oluştu. Lütfen daha sonra tekrar deneyin.'
        ];

        return $fallbacks[$templateType] ?? 'Size yardımcı olmaya çalışıyorum.';
    }
}
