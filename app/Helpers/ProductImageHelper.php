<?php

namespace App\Helpers;

class ProductImageHelper
{
    /**
     * Ürün resmi için güvenli URL oluşturur
     * 
     * @param string|null $imagePath
     * @param string $defaultPath
     * @return string
     */
    public static function getSafeImageUrl(?string $imagePath, string $defaultPath = '/imgs/default-product.svg'): string
    {
        // Eğer resim yolu boş veya null ise default resmi döndür (base URL ile birleştir)
        if (empty($imagePath) || trim($imagePath) === '') {
            return url($defaultPath);
        }

        // Eğer resim yolu zaten tam URL ise (http/https ile başlıyorsa) olduğu gibi döndür
        if (filter_var($imagePath, FILTER_VALIDATE_URL)) {
            return $imagePath;
        }

        // Eğer resim yolu relative path ise (/) ile başlıyorsa olduğu gibi döndür
        if (str_starts_with($imagePath, '/')) {
            return $imagePath;
        }

        // Eğer resim yolu relative path ise başına / ekle
        return '/' . ltrim($imagePath, '/');
    }

    /**
     * Ürün resminin var olup olmadığını kontrol eder
     * 
     * @param string $imagePath
     * @return bool
     */
    public static function imageExists(string $imagePath): bool
    {
        // Eğer tam URL ise kontrol etme (external resim)
        if (filter_var($imagePath, FILTER_VALIDATE_URL)) {
            return true; // External resimler için her zaman true döndür
        }

        // Local dosya yolu ise kontrol et
        $fullPath = public_path(ltrim($imagePath, '/'));
        return file_exists($fullPath);
    }

    /**
     * Ürün resmi için fallback mekanizması
     * 
     * @param string|null $imagePath
     * @param string $defaultPath
     * @return string
     */
    public static function getImageWithFallback(?string $imagePath, string $defaultPath = '/imgs/default-product.svg'): string
    {
        // Eğer resim yolu boş veya null ise default resmi döndür (base URL ile birleştir)
        if (empty($imagePath) || trim($imagePath) === '') {
            return url($defaultPath);
        }

        // Eğer resim yolu zaten tam URL ise (http/https ile başlıyorsa) olduğu gibi döndür
        if (filter_var($imagePath, FILTER_VALIDATE_URL)) {
            return $imagePath;
        }

        // Eğer resim yolu relative path ise (/) ile başlıyorsa olduğu gibi döndür
        if (str_starts_with($imagePath, '/')) {
            return $imagePath;
        }

        // Eğer resim yolu relative path ise başına / ekle
        $safeUrl = '/' . ltrim($imagePath, '/');
        
        // Eğer dosya yoksa default'a dön (base URL ile birleştir)
        if (!self::imageExists($safeUrl)) {
            return url($defaultPath);
        }

        return $safeUrl;
    }

    /**
     * Ürün resmi için placeholder oluşturur
     * 
     * @param string $productName
     * @param int $width
     * @param int $height
     * @return string
     */
    public static function generatePlaceholder(string $productName, int $width = 300, int $height = 300): string
    {
        // İlk harfleri al
        $initials = '';
        $words = explode(' ', $productName);
        foreach (array_slice($words, 0, 2) as $word) {
            if (!empty($word)) {
                $initials .= strtoupper(substr($word, 0, 1));
            }
        }

        // SVG placeholder oluştur
        $svg = sprintf(
            '<svg width="%d" height="%d" xmlns="http://www.w3.org/2000/svg">
                <rect width="100%%" height="100%%" fill="#f3f4f6"/>
                <text x="50%%" y="50%%" font-family="Arial, sans-serif" font-size="%d" 
                      text-anchor="middle" dominant-baseline="middle" fill="#6b7280">
                    %s
                </text>
            </svg>',
            $width,
            $height,
            min($width, $height) / 8,
            htmlspecialchars($initials)
        );

        return 'data:image/svg+xml;base64,' . base64_encode($svg);
    }
}
