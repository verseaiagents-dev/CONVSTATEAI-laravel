<?php

namespace App\Helpers;

class AIHelper
{
    /**
     * AI mesajını işle ve yanıt oluştur
     */
    public static function processMessage(string $message): string
    {
        // Basit AI işleme mantığı
        $keywords = [
            'merhaba' => 'Merhaba! Size nasıl yardımcı olabilirim?',
            'nasılsın' => 'Teşekkürler, iyiyim! Siz nasılsınız?',
            'yardım' => 'Size yardımcı olmaktan mutluluk duyarım. Ne konuda yardıma ihtiyacınız var?',
            'teşekkür' => 'Rica ederim! Başka bir konuda yardıma ihtiyacınız var mı?',
            'görüşürüz' => 'Görüşmek üzere! İyi günler dilerim.',
        ];

        $message = mb_strtolower(trim($message), 'UTF-8');
        
        foreach ($keywords as $keyword => $response) {
            if (str_contains($message, $keyword)) {
                return $response;
            }
        }

        // Varsayılan yanıt
        return "Mesajınızı aldım: '$message'. Size nasıl yardımcı olabilirim?";
    }

    /**
     * AI yanıtını formatla
     */
    public static function formatResponse(string $response, string $type = 'text'): array
    {
        return [
            'response' => $response,
            'type' => $type,
            'timestamp' => now()->toISOString(),
            'confidence' => rand(80, 95) / 100, // Simüle edilmiş güven skoru
        ];
    }

    /**
     * AI yanıtını analiz et
     */
    public static function analyzeResponse(string $response): array
    {
        $wordCount = str_word_count($response);
        $charCount = strlen($response);
        $sentiment = self::analyzeSentiment($response);

        return [
            'word_count' => $wordCount,
            'char_count' => $charCount,
            'sentiment' => $sentiment,
            'complexity' => $wordCount > 20 ? 'high' : ($wordCount > 10 ? 'medium' : 'low'),
        ];
    }

    /**
     * Basit duygu analizi
     */
    private static function analyzeSentiment(string $text): string
    {
        $positiveWords = ['teşekkür', 'güzel', 'harika', 'mükemmel', 'iyi', 'mutlu'];
        $negativeWords = ['kötü', 'korkunç', 'berbat', 'üzgün', 'kızgın', 'sinirli'];

        $text = mb_strtolower($text, 'UTF-8');
        
        $positiveCount = 0;
        $negativeCount = 0;

        foreach ($positiveWords as $word) {
            if (str_contains($text, $word)) {
                $positiveCount++;
            }
        }

        foreach ($negativeWords as $word) {
            if (str_contains($text, $word)) {
                $negativeCount++;
            }
        }

        if ($positiveCount > $negativeCount) {
            return 'positive';
        } elseif ($negativeCount > $positiveCount) {
            return 'negative';
        } else {
            return 'neutral';
        }
    }

    /**
     * AI yanıtını özelleştir
     */
    public static function personalizeResponse(string $response, array $userPreferences = []): string
    {
        if (empty($userPreferences)) {
            return $response;
        }

        // Kullanıcı tercihlerine göre yanıtı özelleştir
        if (isset($userPreferences['language']) && $userPreferences['language'] === 'en') {
            return self::translateToEnglish($response);
        }

        if (isset($userPreferences['formality']) && $userPreferences['formality'] === 'casual') {
            return self::makeCasual($response);
        }

        return $response;
    }

    /**
     * İngilizce'ye çevir (basit simülasyon)
     */
    private static function translateToEnglish(string $text): string
    {
        $translations = [
            'Merhaba' => 'Hello',
            'Teşekkür' => 'Thank you',
            'Yardım' => 'Help',
            'Görüşürüz' => 'See you later',
        ];

        foreach ($translations as $turkish => $english) {
            $text = str_replace($turkish, $english, $text);
        }

        return $text;
    }

    /**
     * Daha samimi hale getir
     */
    private static function makeCasual(string $text): string
    {
        $replacements = [
            'Size nasıl yardımcı olabilirim?' => 'Nasıl yardım edebilirim?',
            'Teşekkürler' => 'Teşekkürler!',
            'Rica ederim' => 'Rica ederim!',
        ];

        foreach ($replacements as $formal => $casual) {
            $text = str_replace($formal, $casual, $text);
        }

        return $text;
    }

    /**
     * AI yanıtını logla
     */
    public static function logResponse(string $input, string $output, array $metadata = []): void
    {
        $logData = [
            'input' => $input,
            'output' => $output,
            'metadata' => $metadata,
            'timestamp' => now()->toISOString(),
        ];

        // Log dosyasına yaz (production'da database kullanılabilir)
    }
}
