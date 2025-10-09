<?php

/**
 * Product Detail Template System - Test Examples
 * 
 * Bu dosya, sistemi test etmek için örnek API çağrıları içerir.
 */

// ============================================================================
// TEST 1: TAKI KATEGORİSİ
// ============================================================================

/*
cURL:
curl -X POST http://127.0.0.1:8000/api/product-details \
  -H "Content-Type: application/json" \
  -d '{
    "session_id": "test123",
    "product_id": 1,
    "product_name": "925 Ayar Gümüş Kolye",
    "product_description": "El işçiliği ile üretilmiş, zarif tasarımlı gümüş kolye",
    "product_price": 299.99,
    "product_category": "Takı",
    "brand": "SilverArt",
    "action": "get_details"
  }'

Beklenen Sonuç:
- Kategori: "taki" tespit edilir
- AI ile detaylı analiz yapılır
- Malzeme bilgisi (925 ayar gümüş)
- Tasarım özellikleri
- Bakım önerileri
- Hangi kıyafetlerle uyumlu olduğu
*/

// ============================================================================
// TEST 2: DOĞALTAŞ KATEGORİSİ
// ============================================================================

/*
cURL:
curl -X POST http://127.0.0.1:8000/api/product-details \
  -H "Content-Type: application/json" \
  -d '{
    "session_id": "test123",
    "product_id": 2,
    "product_name": "Ametist Doğal Taş Bileklik",
    "product_description": "Doğal ametist taşından yapılmış enerji bilekliği",
    "product_price": 149.99,
    "product_category": "Aksesuar",
    "brand": "CrystalEnergy",
    "action": "get_details"
  }'

Beklenen Sonuç:
- Kategori: "dogaltas" tespit edilir (keyword: "doğal taş", "ametist")
- AI ile metafizik analiz yapılır
- Taşın enerji özellikleri
- Çakra bilgisi
- Temizleme yöntemleri
- Kullanım önerileri
*/

// ============================================================================
// TEST 3: AKSESUAR KATEGORİSİ
// ============================================================================

/*
cURL:
curl -X POST http://127.0.0.1:8000/api/product-details \
  -H "Content-Type: application/json" \
  -d '{
    "session_id": "test123",
    "product_id": 3,
    "product_name": "Deri El Çantası",
    "product_description": "Hakiki deri, modern tasarımlı kadın çantası",
    "product_price": 599.99,
    "product_category": "Aksesuar",
    "brand": "LeatherStyle",
    "action": "get_details"
  }'

Beklenen Sonuç:
- Kategori: "aksesuar" tespit edilir (keyword: "çanta")
- AI ile stil analizi yapılır
- Malzeme özellikleri
- Hangi kıyafetlerle kombine edilir
- Bakım önerileri
- Kullanım senaryoları
*/

// ============================================================================
// TEST 4: YAZILIM KATEGORİSİ (Statik Template)
// ============================================================================

/*
cURL:
curl -X POST http://127.0.0.1:8000/api/product-details \
  -H "Content-Type: application/json" \
  -d '{
    "session_id": "test123",
    "product_id": 4,
    "product_name": "ConvStateAI Chat Platform",
    "product_description": "AI destekli müşteri hizmetleri platformu",
    "product_price": 999.00,
    "product_category": "Yazılım",
    "brand": "ConvState",
    "action": "get_details"
  }'

Beklenen Sonuç:
- Kategori: "yazilim" tespit edilir (keyword: "platform", "AI")
- Statik template kullanılır (use_ai: false)
- Yazılım özellikleri
- Sistem gereksinimleri
- Lisans bilgisi
- Destek bilgisi
*/

// ============================================================================
// TEST 5: ELEKTRONİK KATEGORİSİ
// ============================================================================

/*
cURL:
curl -X POST http://127.0.0.1:8000/api/product-details \
  -H "Content-Type: application/json" \
  -d '{
    "session_id": "test123",
    "product_id": 5,
    "product_name": "Kablosuz Kulaklık Pro",
    "product_description": "ANC özellikli, 30 saat pil ömürlü premium kulaklık",
    "product_price": 1299.00,
    "product_category": "Elektronik",
    "brand": "TechAudio",
    "action": "get_details"
  }'

Beklenen Sonuç:
- Kategori: "elektronik" tespit edilir (keyword: "kulaklık")
- AI ile teknik analiz yapılır
- Teknik özellikler
- Performans bilgisi
- Kullanım senaryoları
- Pil ömrü ve şarj bilgisi
*/

// ============================================================================
// TEST 6: GENEL KATEGORİ (Fallback)
// ============================================================================

/*
cURL:
curl -X POST http://127.0.0.1:8000/api/product-details \
  -H "Content-Type: application/json" \
  -d '{
    "session_id": "test123",
    "product_id": 6,
    "product_name": "Özel Ürün XYZ",
    "product_description": "Tanımlanamayan kategori",
    "product_price": 99.00,
    "product_category": "Diğer",
    "brand": "Generic",
    "action": "get_details"
  }'

Beklenen Sonuç:
- Kategori: "genel" kullanılır (fallback)
- AI ile genel analiz yapılır
- Temel özellikler
- Genel kullanım önerileri
*/

// ============================================================================
// JAVASCRIPT / POSTMAN TEST
// ============================================================================

/*
JavaScript (Frontend):

const testProductDetails = async () => {
  const response = await fetch('http://127.0.0.1:8000/api/product-details', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json'
    },
    body: JSON.stringify({
      session_id: 'test123',
      product_id: 1,
      product_name: '925 Ayar Gümüş Kolye',
      product_description: 'El işçiliği gümüş kolye',
      product_price: 299.99,
      product_category: 'Takı',
      brand: 'SilverArt',
      action: 'get_details'
    })
  });
  
  const data = await response.json();
  console.log('Response:', data);
  
  // Beklenen yapı:
  // {
  //   success: true,
  //   data: {
  //     ai_description: "...",
  //     features: [...],
  //     usage_scenarios: [...],
  //     specifications: {...},
  //     pros_cons: {...},
  //     recommendations: [...]
  //   }
  // }
};
*/

// ============================================================================
// LOG KONTROL
// ============================================================================

/*
Terminal'de log'ları izle:

tail -f storage/logs/laravel.log | grep "Product details"

Göreceğin log'lar:
[INFO] Category detected: taki (keyword: kolye)
[INFO] Product details generated successfully (method: ProductDetailTemplateService)
[INFO] Generating product details with AI (category: taki)
*/

// ============================================================================
// CACHE TEST
// ============================================================================

/*
1. İlk istek: AI ile üretir (yavaş)
2. İkinci istek: Cache'ten döner (hızlı)
3. Cache temizle: php artisan cache:clear
4. Üçüncü istek: Tekrar AI ile üretir

Cache key formatı:
"product_details_{category}_{hash}"

Örnek:
"product_details_taki_a1b2c3d4e5f6..."
*/

// ============================================================================
// HATA SENARYOLARI
// ============================================================================

/*
Senaryo 1: Session ID eksik
Response:
{
  "success": false,
  "message": "Validation error",
  "errors": {
    "session_id": ["The session id field is required."]
  }
}

Senaryo 2: AI servisi çalışmıyor
- Otomatik fallback template kullanılır
- Log'da uyarı mesajı görülür
- Response başarılı döner (fallback ile)

Senaryo 3: Bilinmeyen kategori
- "genel" kategorisi kullanılır
- AI ile genel analiz yapılır
*/

// ============================================================================
// PERFORMANS TESTİ
// ============================================================================

/*
Apache Bench ile load test:

ab -n 100 -c 10 -p test_data.json -T application/json \
   http://127.0.0.1:8000/api/product-details

test_data.json:
{
  "session_id": "test123",
  "product_id": 1,
  "product_name": "Test Ürünü",
  "product_price": 100,
  "action": "get_details"
}

Beklenen:
- İlk 10 istek: AI'ya gider (yavaş)
- Sonraki 90 istek: Cache'ten döner (hızlı)
- Ortalama response time: < 200ms (cache ile)
*/

// ============================================================================
// YENİ KATEGORİ EKLEME TESTİ
// ============================================================================

/*
Adım 1: config/product_detail_templates.php içine yeni kategori ekle

'test_kategori' => [
    'keywords' => ['test', 'deneme'],
    'use_ai' => false,
    'ai_description' => 'Test kategorisi',
    'features' => ['Test özellik 1'],
    ...
],

Adım 2: Cache temizle
php artisan cache:clear

Adım 3: Test et
curl -X POST http://127.0.0.1:8000/api/product-details \
  -H "Content-Type: application/json" \
  -d '{
    "session_id": "test123",
    "product_id": 999,
    "product_name": "Test Ürünü Deneme",
    "product_price": 50,
    "action": "get_details"
  }'

Adım 4: Log'da "test_kategori" tespit edildiğini kontrol et
*/

// ============================================================================
// RUNTIME TEMPLATE EKLEME TESTİ
// ============================================================================

/*
PHP kodunda dinamik template ekleme:

use App\Services\ProductDetailTemplateService;

$service = app(ProductDetailTemplateService::class);

$service->addTemplate('ozel_kategori', [
    'keywords' => ['özel', 'custom'],
    'use_ai' => true,
    'ai_description' => 'Özel kategori açıklaması',
    'features' => ['Özel özellik 1', 'Özel özellik 2'],
    'usage_scenarios' => ['Özel kullanım 1'],
    'specifications' => ['Özel Alan' => 'Özel Değer'],
    'recommendations' => ['Özel öneri 1'],
    'additional_info' => 'Özel bilgi'
]);

// Artık "özel" keyword'ü içeren ürünler bu template'i kullanır
*/

// ============================================================================
// SONUÇ
// ============================================================================

/*
✅ Tüm testler başarılı olmalı
✅ Her kategori doğru tespit edilmeli
✅ AI veya statik template doğru kullanılmalı
✅ Cache çalışmalı
✅ Hata durumlarında fallback devreye girmeli
✅ Log'larda detaylı bilgi görülmeli

Sorun varsa:
1. Log'ları kontrol et: tail -f storage/logs/laravel.log
2. Cache temizle: php artisan cache:clear
3. Config'i kontrol et: config/product_detail_templates.php
4. Keyword'leri kontrol et: Ürün adında keyword geçiyor mu?
*/

