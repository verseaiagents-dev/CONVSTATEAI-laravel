# Product Detail Template System - Kullanım Kılavuzu

## 📋 Genel Bakış

Bu sistem, ürün detay sayfalarında gösterilecek içerikleri **kategori bazlı** olarak yönetmenizi sağlar. Her kategori için özel template'ler tanımlayabilir, AI destekli veya statik içerik üretebilirsiniz.

## 🏗️ Mimari Yapı

```
┌─────────────────────────────────────────────────────────┐
│  ConvStateAPI.php (Controller)                          │
│  └─> generateAIProductDetails()                         │
│       └─> ProductDetailTemplateService                  │
│            ├─> Config'den template'leri yükle           │
│            ├─> Kategori tespit et (keyword bazlı)       │
│            ├─> Template varsa kullan                    │
│            └─> AI gerekiyorsa AI ile üret              │
└─────────────────────────────────────────────────────────┘
                          │
                          ▼
         ┌────────────────────────────────┐
         │  config/product_detail_        │
         │  templates.php                 │
         │  (Template Tanımları)          │
         └────────────────────────────────┘
```

## 📁 Dosya Yapısı

### 1. Service Dosyası
```
app/Services/ProductDetailTemplateService.php
```
- Template yönetimi
- Kategori tespiti
- AI entegrasyonu
- Cache yönetimi

### 2. Config Dosyası
```
config/product_detail_templates.php
```
- Kategori tanımları
- Template içerikleri
- Keyword listesi
- AI prompt'ları

### 3. Controller Entegrasyonu
```
app/Http/Controllers/ConvStateAPI.php
```
- generateAIProductDetails() metodu güncellendi
- Yeni service'i kullanıyor

## 🚀 Kullanım

### Temel Kullanım

```php
// Service'i çağır
$templateService = app(\App\Services\ProductDetailTemplateService::class);

// Ürün detayları oluştur
$details = $templateService->generateProductDetails([
    'name' => 'Gümüş Kolye',
    'description' => 'El işi gümüş kolye',
    'price' => 299.99,
    'category' => 'Takı',
    'brand' => 'MarkaAdı'
]);

// Sonuç:
// $details = [
//     'ai_description' => '...',
//     'features' => [...],
//     'usage_scenarios' => [...],
//     'specifications' => [...],
//     'pros_cons' => [...],
//     'recommendations' => [...]
// ]
```

## 📝 Yeni Template Ekleme

### Yöntem 1: Config Dosyasına Ekle (Kalıcı)

`config/product_detail_templates.php` dosyasını açın ve yeni kategori ekleyin:

```php
'yeni_kategori' => [
    // Kategori tespiti için keyword'ler
    'keywords' => [
        'keyword1', 'keyword2', 'keyword3'
    ],
    
    // AI kullanılsın mı?
    'use_ai' => true,  // true: AI kullan, false: statik template
    
    // AI kullanılacaksa özel prompt (opsiyonel)
    'ai_prompt_template' => "Ürün: {name}
Marka: {brand}
Fiyat: {price} TL

[Özel prompt talimatları buraya...]

JSON formatında döndür:
{
    \"ai_description\": \"...\",
    \"features\": [...],
    ...
}",
    
    // Statik template (AI çalışmazsa veya use_ai=false ise)
    'ai_description' => '{name} açıklaması...',
    'features' => [
        'Özellik 1',
        'Özellik 2'
    ],
    'usage_scenarios' => [
        'Senaryo 1',
        'Senaryo 2'
    ],
    'specifications' => [
        'Alan' => 'Değer'
    ],
    'pros_cons' => [
        'pros' => ['Artı 1', 'Artı 2'],
        'cons' => ['Eksi 1', 'Eksi 2']
    ],
    'recommendations' => [
        'Öneri 1',
        'Öneri 2'
    ],
    'additional_info' => 'Ek bilgiler...'
]
```

### Yöntem 2: Runtime'da Ekle (Geçici)

```php
$templateService = app(\App\Services\ProductDetailTemplateService::class);

$templateService->addTemplate('spor_malzemeleri', [
    'keywords' => ['spor', 'fitness', 'koşu', 'ayakkabı', 'top'],
    'use_ai' => true,
    'ai_description' => 'Spor ürünü açıklaması...',
    'features' => ['Özellik 1', 'Özellik 2'],
    // ... diğer alanlar
]);
```

## 🎯 Mevcut Template'ler

### 1. TAKI (Takı/Mücevherat)
- **Keywords**: kolye, küpe, yüzük, bilezik, altın, gümüş, pırlanta
- **AI Destekli**: ✅ Evet
- **Özellikler**: Malzeme analizi, tasarım özellikleri, bakım önerileri

### 2. DOĞALTAŞ (Natural Stone)
- **Keywords**: doğaltaş, kristal, ametist, kuvars, akik, şifa taşı
- **AI Destekli**: ✅ Evet
- **Özellikler**: Enerji analizi, çakra bilgisi, metafizik özellikler

### 3. AKSESUAR (Accessories)
- **Keywords**: aksesuar, çanta, kemer, şapka, eşarp, gözlük
- **AI Destekli**: ✅ Evet
- **Özellikler**: Stil önerileri, kombinasyon tavsiyeleri

### 4. YAZILIM (Software)
- **Keywords**: yazılım, software, app, uygulama, program
- **AI Destekli**: ❌ Statik template
- **Özellikler**: Teknik özellikler, sistem gereksinimleri

### 5. ELEKTRONİK (Electronics)
- **Keywords**: elektronik, telefon, bilgisayar, laptop, tablet
- **AI Destekli**: ✅ Evet
- **Özellikler**: Teknik özellikler, performans bilgileri

### 6. GENEL (Fallback)
- **Keywords**: Yok (her şey için)
- **AI Destekli**: ✅ Evet
- **Özellikler**: Genel amaçlı template

## 🔍 Kategori Tespiti Nasıl Çalışır?

Sistem, aşağıdaki verileri birleştirerek analiz yapar:
1. Ürün adı (`name`)
2. Ürün kategorisi (`category`)
3. Ürün açıklaması (`description`)
4. Marka (`brand`)

Bu verilerde config'deki **keywords** aranır. İlk eşleşen keyword'ün kategorisi kullanılır.

**Örnek:**
```php
Ürün Adı: "Gümüş Kolye"
Keywords kontrol: ['kolye', 'küpe', 'yüzük', ...]
Sonuç: 'taki' kategorisi tespit edilir
```

## 🎨 Placeholder Sistemi

Template'lerde kullanabileceğiniz placeholder'lar:

| Placeholder | Açıklama | Örnek |
|------------|----------|-------|
| `{name}` | Ürün adı | "Gümüş Kolye" |
| `{brand}` | Marka adı | "MarkaAdı" |
| `{price}` | Fiyat | "299.99" |
| `{category}` | Kategori | "Takı" |
| `{description}` | Açıklama | "El işi..." |

**Kullanım:**
```php
'ai_description' => '{name}, {brand} markası tarafından üretilmiş, {price} TL fiyatlı harika bir üründür.'
```

## 🤖 AI Prompt Özelleştirme

### Genel AI Prompt Yapısı

```php
'ai_prompt_template' => "Ürün: {name}
Marka: {brand}
Kategori: [Kategori Adı]
Fiyat: {price} TL
Açıklama: {description}

[Kategori özel talimatlar...]

Aşağıdaki JSON formatında döndür:
{
    \"ai_description\": \"[Açıklama talimatı]\",
    \"features\": [\"[Özellik talimatı]\"],
    \"usage_scenarios\": [\"[Kullanım talimatı]\"],
    \"specifications\": {\"Alan\": \"Değer\"},
    \"pros_cons\": {
        \"pros\": [\"...\"],
        \"cons\": [\"...\"]
    },
    \"recommendations\": [\"[Öneri talimatı]\"]
}

Sadece JSON döndür, başka açıklama ekleme."
```

### Takı Kategorisi İçin Örnek AI Prompt

```php
"Bu takı ürünü için lütfen profesyonel bir analiz yap. Şu konulara dikkat et:
- Takının hangi malzemeden yapıldığı (altın, gümüş, pırlanta, taş vb.)
- Tasarım özellikleri ve estetik değeri
- Hangi kıyafetlerle veya hangi etkinliklerde kullanılabileceği
- Bakım önerileri ve kullanım ipuçları
- Hediye seçeneği olarak değeri"
```

## 💾 Cache Sistemi

Sistem, oluşturulan detayları **1 saat** boyunca cache'ler. Bu sayede:
- ✅ AI API maliyeti azalır
- ✅ Hız artar
- ✅ Aynı ürün için tekrar işlem yapılmaz

Cache key formatı:
```
product_details_{category}_{hash}
```

Cache'i manuel temizlemek için:
```php
Cache::forget('product_details_*');
```

## 🔧 İleri Seviye Özelleştirme

### Template'leri Programatik Olarak Almak

```php
$templateService = app(\App\Services\ProductDetailTemplateService::class);

// Tüm template'leri al
$allTemplates = $templateService->getAllTemplates();

// Belirli bir template'i al
$takiTemplate = $templateService->getTemplate('taki');
```

### Özel Kategori Tespiti

Service'i extend ederek kendi kategori tespit metodunuzu yazabilirsiniz:

```php
class CustomProductDetailService extends ProductDetailTemplateService
{
    protected function detectCategory(array $productData): string
    {
        // Özel kategori tespit logiği
        if ($this->isLuxuryProduct($productData)) {
            return 'luxury';
        }
        
        return parent::detectCategory($productData);
    }
}
```

## 📊 Response Formatı

Service'in döndürdüğü veri yapısı:

```json
{
    "ai_description": "2-3 cümlelik ürün açıklaması",
    "features": [
        "Özellik 1",
        "Özellik 2",
        "Özellik 3"
    ],
    "usage_scenarios": [
        "Kullanım senaryosu 1",
        "Kullanım senaryosu 2"
    ],
    "specifications": {
        "Alan1": "Değer1",
        "Alan2": "Değer2"
    },
    "pros_cons": {
        "pros": ["Artı 1", "Artı 2"],
        "cons": ["Eksi 1", "Eksi 2"]
    },
    "recommendations": [
        "Öneri 1",
        "Öneri 2"
    ],
    "additional_info": "Ek bilgiler"
}
```

## 🐛 Hata Ayıklama

### Log Kontrol

```bash
tail -f storage/logs/laravel.log | grep "Product details"
```

### Kategori Tespiti Kontrolü

```php
Log::info('Category detected', [
    'detected' => $templateKey,
    'keyword' => $keyword,
    'product' => $productData['name']
]);
```

### AI Response Kontrolü

```php
Log::info('Generating product details with AI', [
    'category' => $category,
    'product' => $productData['name']
]);
```

## 📈 Performans İpuçları

1. **Cache Kullanın**: Varsayılan olarak aktif, 1 saat cache süresi
2. **AI'yı Akıllıca Kullanın**: Statik içerik yeterli ise `use_ai: false` yapın
3. **Keyword Optimizasyonu**: Daha spesifik keyword'ler daha iyi sonuç verir
4. **Fallback Template**: Her zaman `genel` kategorisi fallback olarak çalışır

## 🎓 Örnek Senaryolar

### Senaryo 1: Yeni Kategori Ekleme (Kitap)

```php
// config/product_detail_templates.php içine ekle
'kitap' => [
    'keywords' => ['kitap', 'roman', 'dergi', 'yayın', 'edebiyat', 'book'],
    'use_ai' => true,
    'ai_prompt_template' => "Kitap: {name}
Yazar/Yayınevi: {brand}
Fiyat: {price} TL

Bu kitap için profesyonel bir analiz yap:
- Kitabın türü ve içeriği
- Hedef kitle
- Sayfa sayısı ve cilt bilgisi (varsa)
- Okuma önerileri

JSON formatında döndür...",
    
    'ai_description' => '{name}, {brand} tarafından yayınlanan ilgi çekici bir eserdir.',
    'features' => [
        'Özgün içerik',
        'Kaliteli baskı',
        'Kolay okunabilir font'
    ],
    // ... diğer alanlar
],
```

### Senaryo 2: Marka Özel Template

```php
// Runtime'da marka özel template
if ($productData['brand'] === 'PremiumBrand') {
    $templateService->addTemplate('premium_custom', [
        'keywords' => ['premium', 'lüks', 'luxury'],
        'use_ai' => true,
        'ai_description' => 'Premium {name}, lüks yaşamın vazgeçilmezidir...',
        // ...
    ]);
}
```

### Senaryo 3: Sezon Özel Template

```php
// Yılbaşı dönemi için özel template
if (now()->month === 12) {
    $template['additional_info'] = 'Yılbaşı hediyesi için ideal bir seçim!';
    $template['usage_scenarios'][] = 'Yılbaşı hediyesi olarak';
}
```

## 📞 Destek ve Katkı

Yeni template eklemek veya sistem geliştirmek için:
1. `config/product_detail_templates.php` dosyasını güncelleyin
2. Yeni keyword'ler ekleyin
3. AI prompt'larını optimize edin
4. Cache'i temizleyin: `php artisan cache:clear`

## 🔐 Güvenlik Notları

- ✅ Template'ler config dosyasında, güvenli
- ✅ AI response'ları parse edilir ve validate edilir
- ✅ XSS koruması için HTML escape kullanılır (frontend'de)
- ✅ Rate limiting ve cache ile API maliyeti kontrolü

## 🚦 Sonuç

Bu sistem ile:
- ✅ Kategori bazlı özelleştirilmiş ürün detayları
- ✅ AI veya statik template seçenekleri
- ✅ Kolay genişletilebilir yapı
- ✅ Cache ve performans optimizasyonu
- ✅ Detaylı log ve hata yönetimi

**Yeni kategori eklemek sadece 5 dakika!** 🚀

---

*Son güncelleme: Ekim 2025*
*Versiyon: 1.0.0*

