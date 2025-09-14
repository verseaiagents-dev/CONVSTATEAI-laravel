# Knowledge Base AI Servisleri

Bu dizin, Knowledge Base sisteminde AI destekli işlemler için gerekli servisleri içerir.

## AIService

Ana AI servis sınıfı. OpenAI API ile entegrasyon sağlar.

### Özellikler

- **Intent Detection**: Kullanıcı sorgularının amacını tespit eder
- **Response Generation**: AI destekli yanıt üretimi
- **Content Analysis**: İçerik analizi ve kategorilendirme
- **Image Analysis**: Ürün resimlerinin AI ile analizi
- **Semantic Search**: Anlamsal arama
- **Query Expansion**: Sorgu genişletme

### Resim Analizi Özelliği

Knowledge Base chunk'larında bulunan resim URL'lerini otomatik olarak tespit eder ve analiz eder.

#### Kullanım

```php
use App\Services\KnowledgeBase\AIService;

$aiService = app(AIService::class);

// Tek resim analizi
$result = $aiService->analyzeImageContent($imageUrl, $context);

// Chunk içeriğindeki resimleri işle
$result = $aiService->processChunkImages($chunkContent, $context);
```

#### Resim Analizi Sonucu

```json
{
    "product_type": "akıllı saat",
    "category": "elektronik",
    "visual_features": ["siyah", "yuvarlak", "dijital ekran"],
    "technical_features": ["bluetooth", "GPS", "nabız ölçer"],
    "usage_area": "fitness ve günlük kullanım",
    "target_audience": "spor yapan yetişkinler",
    "summary": "Modern tasarımlı, fitness takibi yapabilen akıllı saat"
}
```

#### Desteklenen Resim Formatları

- HTML `<img>` tag'leri
- Markdown resim syntax'ı
- Plain text URL'ler (jpg, jpeg, png, gif, webp, svg)

## ContentChunker

İçeriği chunk'lara böler ve her chunk için resim analizi yapar.

### Özellikler

- Akıllı cümle bazlı bölme
- Token sayısı hesaplama
- Resim URL tespiti
- Otomatik resim analizi
- `image_vision` field'ı ekleme

### Chunk Yapısı

```php
[
    'chunk_index' => 0,
    'content' => 'chunk içeriği',
    'content_hash' => 'sha256 hash',
    'chunk_size' => 1000,
    'word_count' => 150,
    'content_type' => 'product',
    'metadata' => [...],
    'image_vision' => 'JSON string',
    'has_images' => true,
    'processed_images' => 1
]
```

## Test Komutları

### Resim Analizi Test

```bash
php artisan app:process-product-updates --test-image-analysis
```

Bu komut, örnek bir resim URL'i ile resim analizi yapar ve sonuçları gösterir.

## Kurulum

1. `.env` dosyasında `OPENAI_API_KEY` tanımlayın
2. `gpt-4o-mini` modeli kullanılır (değiştirilmemeli)
3. Composer autoload'u yenileyin

## Hata Yönetimi

- API hatalarında fallback mekanizmalar
- Log kayıtları
- Graceful degradation
- JSON parse hatalarında fallback analiz

## Performans

- Resim analizi cache'lenebilir
- Batch processing desteği
- Async processing için hazır
- Rate limiting uyumlu
