# 🧮 AI Token Hesaplayıcı & Fiyat Stratejisi Dokümantasyonu

## 📋 Genel Bakış

Admin paneline eklenen AI Token Hesaplayıcı, OpenAI API kullanımınızın maliyetlerini hesaplamanıza ve AI destekli akıllı fiyatlandırma önerileri almanıza olanak sağlar.

## 🎯 Özellikler

### 1. **Token Kullanım Hesaplayıcı**
- Session sayısı ve mesaj başına token kullanımı hesaplama
- Farklı OpenAI modelleri için maliyet karşılaştırması
- Gerçek zamanlı hesaplama
- 4 farklı kullanım senaryosu (Düşük, Orta, Yüksek, Kurumsal)

### 2. **AI Fiyat Önerisi Agent**
- Token maliyetlerinize göre akıllı plan önerileri
- 4 kademe plan önerisi (Başlangıç, Profesyonel, İş, Kurumsal)
- Kar marjı hesaplaması
- Hedef pazar analizi
- Tek tıkla plan veritabanına kaydetme

### 3. **Görselleştirme**
- Chart.js ile interaktif grafikler
- Maliyet dağılımı görselleştirmesi
- Senaryo karşılaştırma tabloları

### 4. **Export Özelliği**
- CSV formatında raporlama
- Excel ile uyumlu export

## 🚀 Kullanım

### Erişim
```
URL: /admin/token-calculator
Yetki: Admin
```

### Token Hesaplama Adımları

1. **Sol Panel - Token Hesaplayıcı**
   - Session Sayısı girin (örn: 10, 100, 1000)
   - Session başına ortalama mesaj sayısı (örn: 5-10)
   - OpenAI Model seçin:
     - GPT-4o Mini (Önerilen - Projede kullanılan)
     - GPT-4o (Premium)
     - GPT-3.5 Turbo (Ekonomik)
   - "Hesapla" butonuna tıklayın

2. **Sonuçları İnceleyin**
   - Token kullanım istatistikleri
   - USD ve TRY cinsinden maliyet
   - Session/Mesaj başına birim maliyet
   - 4 farklı kullanım senaryosu maliyetleri
   - Görsel grafikler

3. **Export Edin**
   - "Excel'e Aktar" butonu ile CSV formatında indirin

### AI Fiyat Önerisi Alma

1. **Sağ Panel - AI Fiyat Agent**
   - Aylık hedef session sayısı girin
   - Mesaj/session değeri girin
   - Model seçin
   - Kar marjı belirleyin (örn: %200)
   - Hedef pazar seçin:
     - Ekonomik: 3x maliyet çarpanı
     - Standart: 5x maliyet çarpanı
     - Premium: 8x maliyet çarpanı
     - Kurumsal: 12x maliyet çarpanı
   - "AI Önerisi Al" butonuna tıklayın

2. **AI Önerilerini İnceleyin**
   - 4 farklı plan önerisi
   - Her plan için:
     - Aylık ve yıllık fiyat
     - Token limiti
     - Özellikler listesi
     - Hedef müşteri profili
     - Fiyatlandırma mantığı
   - Pazar analizi
   - Genel öneriler
   - Kar özeti

3. **Plana Kaydet**
   - Beğendiğiniz planın "Plana Kaydet" butonuna tıklayın
   - Plan otomatik olarak veritabanına kaydedilir
   - `/admin/plans` sayfasından düzenleyebilirsiniz

## 📊 Teknik Detaylar

### Token Hesaplama Metodolojisi

Ortalama token kullanımı (ConvStateAPI analizi):
```
- Kullanıcı mesajı: ~50 token
- System prompt: ~300 token
- Conversation history: ~200 token/mesaj
- Knowledge base chunks: ~500 token
- AI yanıtı: ~150 token (basit)
- Ürün listesi response: ~300 token
- Detaylı açıklama: ~200 token
```

### OpenAI Fiyatlandırması (Güncel)

**GPT-4o Mini:**
- Input: $0.150 / 1M tokens
- Output: $0.600 / 1M tokens

**GPT-4o:**
- Input: $2.50 / 1M tokens
- Output: $10.00 / 1M tokens

**GPT-3.5 Turbo:**
- Input: $0.50 / 1M tokens
- Output: $1.50 / 1M tokens

### Döviz Kuru
- USD/TRY: 34.50 (Controller içinden güncellenebilir)

## 🔧 API Endpoints

```php
// Ana sayfa
GET /admin/token-calculator

// Token hesaplama
POST /admin/token-calculator/calculate
Body: {
    "sessions": 10,
    "messages_per_session": 5,
    "model": "gpt-4o-mini"
}

// AI fiyat önerisi
POST /admin/token-calculator/ai-price
Body: {
    "sessions_per_month": 1000,
    "messages_per_session": 5,
    "model": "gpt-4o-mini",
    "profit_margin": 200,
    "target_market": "standard"
}

// Plan kaydetme
POST /admin/token-calculator/save-plan
Body: {
    "name": "Profesyonel",
    "monthly_price": 999,
    "yearly_price": 9990,
    "usage_tokens": 1000,
    "features": [...],
    "token_reset_period": "monthly"
}

// Export
POST /admin/token-calculator/export
Body: {
    "data": { ... calculation results ... }
}
```

## 🎨 UI Bileşenleri

### Renkler
- Mavi: Token kullanımı, genel bilgiler
- Yeşil: Maliyet bilgileri (TRY)
- Mor: Özel hesaplamalar
- Turuncu: Toplam değerler
- Gradient (Mor-Pembe): AI Agent paneli

### İkonlar
- 🧮 Token Hesaplayıcı
- 🤖 AI Agent
- 💰 Maliyet
- 📊 Grafikler
- 📈 Senaryolar
- 💡 Öneriler
- ✅ Başarılı işlem

## 🔐 Güvenlik

- Sadece admin kullanıcılar erişebilir
- CSRF koruması aktif
- OpenAI API key .env dosyasından alınır
- API key yoksa fallback hesaplama kullanılır

## 💡 Kullanım Örneri

### Senaryo: E-ticaret sitesi için plan oluşturma

1. **Token Analizi**
   - Aylık 5000 session
   - Session başına 7 mesaj
   - Model: GPT-4o Mini
   - **Sonuç**: Aylık ~$15 maliyet

2. **AI Önerisi**
   - Kar marjı: %250
   - Hedef pazar: Premium
   - **Sonuç**: 4 plan önerisi
     - Başlangıç: ₺499/ay (500 token)
     - Profesyonel: ₺999/ay (1500 token)
     - İş: ₺1999/ay (3000 token)
     - Kurumsal: ₺3999/ay (7500 token)

3. **Plana Kaydet**
   - Profesyonel planı seç
   - Veritabanına kaydet
   - Kullanıcılara sun

## 🐛 Sorun Giderme

### AI Önerisi Çalışmıyor
- `.env` dosyasında `OPENAI_API_KEY` kontrolü
- Fallback sistem otomatik devreye girer
- Manuel fiyatlandırma önerileri gösterilir

### Grafik Gösterilmiyor
- Chart.js CDN bağlantısı kontrolü
- Tarayıcı console'da hata kontrolü

### Plan Kaydedilemiyor
- Database bağlantısı kontrolü
- Plans tablosu migration kontrolü
- Log dosyalarını inceleyin

## 📚 Dosya Yapısı

```
laravel/
├── app/Http/Controllers/Admin/
│   └── TokenCalculatorController.php (YENİ)
├── resources/views/admin/
│   └── token-calculator.blade.php (YENİ)
├── routes/
│   └── web.php (GÜNCELLENDİ)
└── TOKEN_CALCULATOR_README.md (YENİ)
```

## 🔄 Güncelleme Notları

### Döviz Kuru Güncelleme
`TokenCalculatorController.php` dosyasında:
```php
private const USD_TO_TRY = 34.50; // Güncel kuru buradan değiştirin
```

### Token Ortalamaları Güncelleme
Gerçek kullanım verilerine göre:
```php
private const AVERAGE_TOKENS = [
    'user_message' => 50,
    'system_prompt' => 300,
    // ... diğer değerler
];
```

### OpenAI Fiyat Güncelleme
```php
private const PRICING = [
    'gpt-4o-mini' => [
        'input' => 0.150,
        'output' => 0.600,
    ],
    // ... diğer modeller
];
```

## 📞 Destek

Sorularınız için:
- GitHub Issues
- Dokümantasyon: Bu dosya
- Log dosyaları: `storage/logs/laravel.log`

---

**Son Güncelleme**: 2025-10-11
**Versiyon**: 1.0.0
**Geliştirici**: ConvStateAI Team

