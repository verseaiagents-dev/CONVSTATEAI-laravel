<?php

namespace App\Helpers;

use Unsplash\HttpClient;
use Unsplash\Photo;

class UnsplashHelper
{
    private static $initialized = false;
    
    /**
     * Unsplash client'ı başlat
     */
    private static function initialize()
    {
        if (!self::$initialized) {
            // Unsplash API credentials - .env dosyasından al
            $accessKey = env('UNSPLASH_ACCESS_KEY', '');
            $secret = env('UNSPLASH_SECRET', '');
            $utmSource = env('UNSPLASH_UTM_SOURCE', 'ConvStateAI Store');
            
            if ($accessKey) {
                HttpClient::init([
                    'applicationId' => $accessKey,
                    'secret' => $secret,
                    'utmSource' => $utmSource
                ]);
                self::$initialized = true;
            }
        }
    }
    
    /**
     * Kategoriye göre rastgele resim URL'i al
     */
    public static function getRandomImageByCategory($category = 'technology', $width = 1920, $height = 1080)
    {
        try {
            self::initialize();
            
            if (self::$initialized) {
                // Resmi Unsplash API'den al
                $filters = [
                    'query' => $category,
                    'w' => $width,
                    'h' => $height
                ];
                
                $photo = Photo::random($filters);
                
                if ($photo && isset($photo->urls['regular'])) {
                    // Download trigger - API guidelines'a uygun
                    $photo->download();
                    return $photo->urls['regular'];
                }
            }
        } catch (\Exception $e) {
            // Hata durumunda proxy URL'e dön
            return self::getProxyImageUrl($category, $width, $height);
        }
        
        // Fallback olarak proxy URL kullan
        return self::getProxyImageUrl($category, $width, $height);
    }
    
    /**
     * Unsplash proxy URL'i oluştur (API key gerektirmez)
     */
    public static function getProxyImageUrl($category = 'technology', $width = 1920, $height = 1080)
    {
        $categories = [
            'technology' => 'technology,ai,digital,innovation',
            'business' => 'business,office,corporate,modern',
            'nature' => 'nature,landscape,beautiful,outdoors',
            'abstract' => 'abstract,geometric,modern,design',
            'minimal' => 'minimal,simple,clean,white',
            'gradient' => 'gradient,colorful,modern,design',
            'fashion' => 'fashion,style,clothing,modern',
            'home' => 'home,interior,design,modern',
            'sports' => 'sports,fitness,athletic,active',
            'health' => 'health,wellness,medical,beauty',
            'automotive' => 'cars,automotive,vehicles,modern',
            'books' => 'books,reading,literature,education',
            'toys' => 'toys,playful,fun,children',
            'beauty' => 'beauty,cosmetics,makeup,skincare',
            'food' => 'food,culinary,cooking,delicious'
        ];
        
        $searchTerm = $categories[$category] ?? $categories['technology'];
        
        // Unsplash proxy URL formatı
        return "https://source.unsplash.com/{$width}x{$height}/?{$searchTerm}&auto=format&fit=crop&w={$width}&q=80";
    }
    
    /**
     * E-ticaret kategorilerine göre uygun resim kategorileri
     */
    public static function getCategoryImageMapping()
    {
        return [
            'Electronics' => 'technology',
            'Clothing' => 'fashion',
            'Home & Garden' => 'home',
            'Sports' => 'sports',
            'Health' => 'health',
            'Automotive' => 'automotive',
            'Books' => 'books',
            'Toys' => 'toys',
            'Beauty' => 'beauty',
            'Food' => 'food',
            'Garden' => 'nature',
            'Pet' => 'animals',
            'Hobby' => 'creative',
            'Cosmetics' => 'beauty',
            'Furniture' => 'home',
            'Kitchen' => 'food',
            'Bathroom' => 'home',
            'Bedroom' => 'home',
            'Living Room' => 'home',
            'Office' => 'business',
            'Gaming' => 'technology',
            'Music' => 'creative',
            'Art' => 'creative',
            'Photography' => 'creative',
            'Fitness' => 'sports',
            'Yoga' => 'health',
            'Meditation' => 'health',
            'Skincare' => 'beauty',
            'Makeup' => 'beauty',
            'Hair Care' => 'beauty',
            'Fragrance' => 'beauty',
            'Jewelry' => 'fashion',
            'Watches' => 'fashion',
            'Bags' => 'fashion',
            'Shoes' => 'fashion',
            'Accessories' => 'fashion',
            'Outdoor' => 'nature',
            'Camping' => 'nature',
            'Hiking' => 'nature',
            'Swimming' => 'sports',
            'Running' => 'sports',
            'Cycling' => 'sports',
            'Tennis' => 'sports',
            'Golf' => 'sports',
            'Basketball' => 'sports',
            'Football' => 'sports',
            'Soccer' => 'sports',
            'Baseball' => 'sports',
            'Volleyball' => 'sports',
            'Martial Arts' => 'sports',
            'Dance' => 'creative',
            'Theater' => 'creative',
            'Cinema' => 'creative',
            'Literature' => 'books',
            'Poetry' => 'books',
            'Fiction' => 'books',
            'Non-Fiction' => 'books',
            'Science' => 'technology',
            'Mathematics' => 'technology',
            'Engineering' => 'technology',
            'Medicine' => 'health',
            'Dental' => 'health',
            'Vision' => 'health',
            'Mental Health' => 'health',
            'Nutrition' => 'health',
            'Supplements' => 'health',
            'Vitamins' => 'health',
            'Herbs' => 'nature',
            'Organic' => 'nature',
            'Eco-Friendly' => 'nature',
            'Sustainable' => 'nature',
            'Recycled' => 'nature',
            'Vintage' => 'fashion',
            'Retro' => 'fashion',
            'Classic' => 'fashion',
            'Modern' => 'design',
            'Contemporary' => 'design',
            'Minimalist' => 'minimal',
            'Luxury' => 'business',
            'Premium' => 'business',
            'Exclusive' => 'business',
            'Handmade' => 'creative',
            'Custom' => 'creative',
            'Personalized' => 'creative',
            'Limited Edition' => 'creative',
            'Collector' => 'creative',
            'Antique' => 'vintage',
            'Heritage' => 'vintage',
            'Traditional' => 'vintage',
            'Cultural' => 'creative',
            'Ethnic' => 'fashion',
            'Regional' => 'fashion',
            'Local' => 'fashion',
            'Global' => 'fashion',
            'International' => 'business',
            'Worldwide' => 'business',
            'Universal' => 'business'
        ];
    }
    
    /**
     * Ürün kategorisine göre uygun resim al
     */
    public static function getImageForProductCategory($productCategory)
    {
        $mapping = self::getCategoryImageMapping();
        
        // Tam eşleşme ara
        foreach ($mapping as $category => $imageCategory) {
            if (strcasecmp($productCategory, $category) === 0) {
                return self::getRandomImageByCategory($imageCategory, 400, 400);
            }
        }
        
        // Kısmi eşleşme ara
        foreach ($mapping as $category => $imageCategory) {
            if (stripos($productCategory, $category) !== false) {
                return self::getRandomImageByCategory($imageCategory, 400, 400);
            }
        }
        
        // Kelime bazlı eşleşme ara
        $words = explode(' ', strtolower($productCategory));
        foreach ($words as $word) {
            foreach ($mapping as $category => $imageCategory) {
                if (stripos($category, $word) !== false || stripos($word, $category) !== false) {
                    return self::getRandomImageByCategory($imageCategory, 400, 400);
                }
            }
        }
        
        // Varsayılan olarak technology kategorisini kullan
        return self::getRandomImageByCategory('technology', 400, 400);
    }
    
    /**
     * Hero section için özel resim URL'i
     */
    public static function getHeroImage($theme = 'default')
    {
        $themes = [
            'default' => 'technology',
            'premium' => 'business',
            'creative' => 'abstract',
            'business' => 'business',
            'nature' => 'nature'
        ];
        
        $category = $themes[$theme] ?? $themes['default'];
        
        return self::getRandomImageByCategory($category, 1920, 1080);
    }
    
    /**
     * Dinamik resim URL'i oluştur
     */
    public static function getDynamicImage($query = '', $width = 1920, $height = 1080, $filters = [])
    {
        if (empty($query)) {
            $query = 'technology';
        }
        
        return self::getRandomImageByCategory($query, $width, $height);
    }
    
    /**
     * Belirli bir resmi ID ile bul
     */
    public static function getPhotoById($photoId)
    {
        try {
            self::initialize();
            
            if (self::$initialized) {
                $photo = Photo::find($photoId);
                
                if ($photo && isset($photo->urls['regular'])) {
                    // Download trigger
                    $photo->download();
                    return $photo->urls['regular'];
                }
            }
        } catch (\Exception $e) {
            // Hata durumunda varsayılan resim
            return self::getProxyImageUrl('technology', 1920, 1080);
        }
        
        return self::getProxyImageUrl('technology', 1920, 1080);
    }
    
    /**
     * Arama yaparak resim bul
     */
    public static function searchPhotos($query, $page = 1, $perPage = 10)
    {
        try {
            self::initialize();
            
            if (self::$initialized) {
                $photos = Photo::search($query, $page, $perPage);
                
                if ($photos && count($photos) > 0) {
                    // İlk resmi al ve download trigger
                    $photo = $photos[0];
                    $photo->download();
                    
                    return [
                        'url' => $photo->urls['regular'],
                        'alt' => $photo->alt_description ?? $query,
                        'photographer' => $photo->user['name'] ?? 'Unknown',
                        'total' => count($photos)
                    ];
                }
            }
        } catch (\Exception $e) {
            // Hata durumunda proxy URL
            return [
                'url' => self::getProxyImageUrl($query, 1920, 1080),
                'alt' => $query,
                'photographer' => 'Unsplash',
                'total' => 0
            ];
        }
        
        return [
            'url' => self::getProxyImageUrl($query, 1920, 1080),
            'alt' => $query,
            'photographer' => 'Unsplash',
            'total' => 0
        ];
    }
    
    /**
     * Featured (öne çıkan) resimler al
     */
    public static function getFeaturedPhotos($count = 5)
    {
        try {
            self::initialize();
            
            if (self::$initialized) {
                $photos = Photo::random(['featured' => true, 'count' => $count]);
                
                if ($photos && is_array($photos)) {
                    $result = [];
                    foreach ($photos as $photo) {
                        $photo->download(); // Download trigger
                        $result[] = [
                            'url' => $photo->urls['regular'],
                            'alt' => $photo->alt_description ?? 'Featured Photo',
                            'photographer' => $photo->user['name'] ?? 'Unknown'
                        ];
                    }
                    return $result;
                }
            }
        } catch (\Exception $e) {
            // Hata durumunda proxy URL'ler
            return array_map(function($i) {
                return [
                    'url' => self::getProxyImageUrl('technology', 1920, 1080),
                    'alt' => 'Featured Photo ' . $i,
                    'photographer' => 'Unsplash'
                ];
            }, range(1, $count));
        }
        
        // Fallback
        return array_map(function($i) {
            return [
                'url' => self::getProxyImageUrl('technology', 1920, 1080),
                'alt' => 'Featured Photo ' . $i,
                'photographer' => 'Unsplash'
            ];
        }, range(1, $count));
    }
}
