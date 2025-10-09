# Product Detail Template System

## 🎯 Ne Yapıldı?

Laravel backend'inize **dinamik ve genişletilebilir** bir ürün detay sistemi kuruldu. Artık her ürün kategorisi için özel, AI destekli veya statik template'ler tanımlayabilirsiniz.

## 📦 Oluşturulan Dosyalar

### 1. Service Dosyası
```
📄 app/Services/ProductDetailTemplateService.php
```
- Ana template yönetim servisi
- Kategori tespiti
- AI entegrasyonu
- Cache yönetimi

### 2. Config Dosyası
```
📄 config/product_detail_templates.php
```
Mevcut kategoriler:
- ✅ **TAKI** (Takı/Mücevherat) - AI Destekli
- ✅ **DOĞALTAŞ** (Natural Stone/Kristal) - AI Destekli
- ✅ **AKSESUAR** (Accessories) - AI Destekli
- ✅ **YAZILIM** (Software) - Statik Template
- ✅ **ELEKTRONİK** (Electronics) - AI Destekli
- ✅ **GENEL** (Fallback) - AI Destekli

### 3. Dokümantasyon
```
📄 app/Services/PRODUCT_DETAIL_TEMPLATE_GUIDE.md
📄 app/Services/QUICK_START_TEMPLATE.md
```

### 4. Controller Entegrasyonu
```
📄 app/Http/Controllers/ConvStateAPI.php
```
`generateAIProductDetails()` metodu güncellendi.

## 🚀 Nasıl Kullanılır?

### Otomatik Kullanım (Zaten Çalışıyor!)

Sistem otomatik olarak çalışıyor. API'ye gelen isteklerde:

```javascript
POST /api/product-details
{
  "product_name": "Gümüş Kolye",  // ← "kolye" keyword'ü tespit edilir
  "product_price": 299.99,
  // ...
}
```

→ Sistem otomatik olarak **"taki"** kategorisini tespit eder
→ AI ile özel takı analizi oluşturur
→ Detaylı sonuç döner

### Yeni Kategori Ekleme (5 Dakika!)

1. **Config dosyasını aç:**
```bash
nano config/product_detail_templates.php
```

2. **Yeni kategori ekle:**
```php
'kategori_adi' => [
    'keywords' => ['keyword1', 'keyword2'],
    'use_ai' => true,
    'ai_prompt_template' => "...",
    'ai_description' => '...',
    'features' => [...],
    // ...
],
```

3. **Cache temizle:**
```bash
php artisan cache:clear
```

4. **Test et!**

Detaylı örnekler için: `QUICK_START_TEMPLATE.md`

## 🎨 Özellikler

### ✅ Kategori Bazlı Template'ler
Her kategori için özel içerik:
- Takı → Malzeme, tasarım, bakım önerileri
- Doğaltaş → Enerji, çakra, metafizik özellikler
- Aksesuar → Stil önerileri, kombinasyonlar

### ✅ AI Entegrasyonu
- AI destekli dinamik içerik üretimi
- Özel AI prompt'ları her kategori için
- Fallback sistemle hata yönetimi

### ✅ Akıllı Kategori Tespiti
- Keyword bazlı otomatik tespit
- Ürün adı, kategori, açıklama analizi
- Çoklu keyword desteği

### ✅ Cache Sistemi
- 1 saat otomatik cache
- Hız optimizasyonu
- AI API maliyet tasarrufu

### ✅ Placeholder Desteği
```php
'{name}' → Ürün adı
'{brand}' → Marka
'{price}' → Fiyat
'{category}' → Kategori
```

### ✅ Esnek Yapı
- Config dosyasından yönetim
- Runtime'da template ekleme
- Statik veya AI seçimi

## 📊 Response Formatı

```json
{
  "success": true,
  "data": {
    "ai_description": "2-3 cümlelik açıklama",
    "features": ["Özellik 1", "Özellik 2", ...],
    "usage_scenarios": ["Kullanım 1", "Kullanım 2", ...],
    "specifications": {
      "Alan": "Değer"
    },
    "pros_cons": {
      "pros": ["Artı 1", "Artı 2"],
      "cons": ["Eksi 1", "Eksi 2"]
    },
    "recommendations": ["Öneri 1", "Öneri 2"],
    "additional_info": "Ek bilgiler"
  }
}
```

## 🎯 Örnek Kategoriler

### TAKI Template'i
```php
'keywords' => ['kolye', 'küpe', 'yüzük', 'bilezik', 'altın', 'gümüş']
'use_ai' => true

AI Promptu:
- Malzeme analizi (altın, gümüş, pırlanta)
- Tasarım özellikleri
- Hangi kıyafetlerle uyumlu
- Bakım önerileri
- Hediye değeri
```

**Örnek Ürün:** "925 Ayar Gümüş Kolye"
→ Kategori: `taki`
→ AI ile detaylı analiz
→ Malzeme, tasarım, bakım bilgileri

### DOĞALTAŞ Template'i
```php
'keywords' => ['doğaltaş', 'kristal', 'ametist', 'kuvars', 'şifa taşı']
'use_ai' => true

AI Promptu:
- Taş özellikleri
- Enerji ve metafizik özellikler
- Çakra ilişkileri
- Temizleme yöntemleri
- Kullanım önerileri
```

**Örnek Ürün:** "Ametist Doğal Taş Kolye"
→ Kategori: `dogaltas`
→ AI ile enerji analizi
→ Çakra, kullanım, bakım bilgileri

### AKSESUAR Template'i
```php
'keywords' => ['aksesuar', 'çanta', 'kemer', 'şapka', 'gözlük']
'use_ai' => true

AI Promptu:
- Tasarım özellikleri
- Hangi kıyafetlerle kombine edilir
- Stil önerileri
- Bakım tavsiyeleri
```

**Örnek Ürün:** "Deri El Çantası"
→ Kategori: `aksesuar`
→ AI ile stil analizi
→ Kombinasyon, kullanım önerileri

## 🔧 Teknik Detaylar

### Kategori Tespit Algoritması
```php
1. Ürün verilerini birleştir (name + category + description + brand)
2. Config'deki tüm kategorilerin keyword'lerini kontrol et
3. İlk eşleşen keyword'ün kategorisini kullan
4. Eşleşme yoksa 'genel' kategorisini kullan
```

### Cache Mekanizması
```php
Cache Key: "product_details_{category}_{hash}"
Süre: 3600 saniye (1 saat)
```

### Fallback Sistemi
```
1. Template var mı? → Kullan
2. AI çalışıyor mu? → Kullan
3. Hata var mı? → Fallback template kullan
```

## 📝 Yeni Template Ekleme Adımları

### 1. Config'e Ekle
```php
'yeni_kategori' => [
    'keywords' => ['key1', 'key2'],
    'use_ai' => true,
    'ai_description' => '...',
    // ...
],
```

### 2. Cache Temizle
```bash
php artisan cache:clear
```

### 3. Test Et
```bash
curl -X POST http://127.0.0.1:8000/api/product-details \
  -d '{"product_name": "key1 içeren ürün", ...}'
```

Detaylı rehber: `QUICK_START_TEMPLATE.md`

## 🐛 Hata Ayıklama

### Log Kontrol
```bash
tail -f storage/logs/laravel.log | grep "Product details"
```

### Cache Temizleme
```bash
php artisan cache:clear
php artisan config:clear
```

### Debug Modda Test
```php
Log::info('Category detected', [
    'category' => $category,
    'product' => $productName
]);
```

## 📚 Dokümantasyon Dosyaları

1. **PRODUCT_DETAIL_TEMPLATE_GUIDE.md**
   - Detaylı kullanım kılavuzu
   - Mimari açıklamalar
   - İleri seviye özelleştirme

2. **QUICK_START_TEMPLATE.md**
   - 5 dakikada başlangıç
   - Hızlı template ekleme
   - Örnek kategoriler
   - Sık sorulan sorular

3. **Bu Dosya (README.md)**
   - Genel bakış
   - Hızlı başlangıç
   - Temel özellikler

## ✨ Avantajlar

### Mevcut Sisteme Göre İyileştirmeler:

❌ **Eski Sistem:**
- Sabit template'ler
- Kategori bazlı özelleştirme yok
- AI prompt'ları kod içinde
- Yeni kategori eklemek zor

✅ **Yeni Sistem:**
- Dinamik template yönetimi
- Kategori bazlı özel içerik
- Config dosyasından yönetim
- 5 dakikada yeni kategori
- AI veya statik template seçimi
- Cache optimizasyonu
- Detaylı log ve debug

## 🎓 Öğrenme Kaynakları

### Yeni Başlayanlar İçin:
→ `QUICK_START_TEMPLATE.md` oku
→ Örnek kategorileri incele
→ Basit bir kategori ekle

### İleri Seviye:
→ `PRODUCT_DETAIL_TEMPLATE_GUIDE.md` oku
→ Özel AI prompt'ları yaz
→ Service'i extend et

## 🎯 Sonraki Adımlar

1. ✅ Sistemi test et
2. ✅ Mevcut ürünlerle dene
3. ✅ Kendi kategorilerini ekle
4. ✅ AI prompt'larını optimize et
5. ✅ Feedback'lere göre iyileştir

## 📞 Destek

- Detaylı kullanım: `PRODUCT_DETAIL_TEMPLATE_GUIDE.md`
- Hızlı başlangıç: `QUICK_START_TEMPLATE.md`
- Log kontrol: `storage/logs/laravel.log`

---

## 🎉 Özet

Artık ürün detayları için:
- ✅ Kategori bazlı özel template'ler
- ✅ AI destekli dinamik içerik
- ✅ Kolay genişletilebilir yapı
- ✅ 5 dakikada yeni kategori
- ✅ Cache ve performans optimizasyonu
- ✅ Takı, Doğaltaş, Aksesuar örnekleri hazır

**Sistem hazır ve çalışıyor! 🚀**

Herhangi bir ürün için `/api/product-details` endpoint'i otomatik olarak doğru template'i bulup, zengin içerik üretecek.

---

*Kurulum Tarihi: Ekim 2025*
*Versiyon: 1.0.0*

