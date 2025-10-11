# 🔧 FAQ Hataları Düzeltme Rehberi

## 🚨 Tespit Edilen Hatalar

### 1. **site_id yerine project_id kullanılmalı**
❌ **Eski:** `site_id` required  
✅ **Yeni:** `project_id` required

### 2. **description NULL olamıyor**
❌ **Eski:** Database'de `description TEXT NOT NULL`  
✅ **Yeni:** `description TEXT NULL`

### 3. **category NULL olamıyor**
❌ **Eski:** Database'de `category VARCHAR(255) NOT NULL`  
✅ **Yeni:** `category VARCHAR(255) NULL`

---

## 🔧 Düzeltmeler

### ✅ 1. FAQController.php Güncellendi

**Değişiklikler:**
- `site_id` validation kaldırıldı
- `project_id` required yapıldı ve `exists:projects,id` kontrolü eklendi
- `description` boş gelirse default `-` değeri atanıyor
- `site_id` otomatik olarak `1` atanıyor (legacy support)

### ✅ 2. Migration Oluşturuldu

**Dosya:** `2025_10_11_115000_fix_faqs_nullable_fields.php`

```bash
php artisan migrate
```

Bu migration `description` ve `category` alanlarını nullable yapacak.

---

## 📋 Manuel Düzeltme (Eğer migration çalışmazsa)

### SQL ile Düzeltme:

```sql
-- Description ve category'yi nullable yap
ALTER TABLE faqs MODIFY COLUMN description TEXT NULL;
ALTER TABLE faqs MODIFY COLUMN category VARCHAR(255) NULL;

-- Mevcut NULL description'ları düzelt
UPDATE faqs SET description = '-' WHERE description IS NULL OR description = '';

-- Mevcut NULL category'leri düzelt
UPDATE faqs SET category = 'Genel' WHERE category IS NULL OR category = '';
```

---

## 🎯 FAQ Ekleme - Doğru Format

### Dashboard'dan Ekleme:

```json
{
  "title": "ConvStateAI'ı kurdum, ne zaman sonuç almaya başlarım?",
  "description": "Kurulum sonrası bekleme süresi hakkında",
  "answer": "İlk konversiyon artışını 24-48 saat içinde göreceksiniz...",
  "category": "Başlangıç",
  "project_id": 5,
  "is_active": true,
  "sort_order": 1,
  "tags": ["hızlı başlangıç", "sonuçlar", "kurulum"]
}
```

### CURL ile Test:

```bash
curl -X POST https://convstateai.com/api/faqs \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -d '{
    "title": "Test FAQ",
    "description": "Test description",
    "answer": "Test answer",
    "category": "Test",
    "project_id": 5,
    "tags": ["test"]
  }'
```

---

## 🗂️ Kategori Listesi (Standardize)

CONVSTATEAI_FAQ_CONTENT.md dosyasındaki kategoriler:

1. **Başlangıç** - Getting Started
2. **Özellikler** - Features
3. **Fiyatlandırma** - Pricing
4. **Entegrasyon** - Integration
5. **ROI & Sonuçlar** - ROI & Results
6. **Teknik** - Technical
7. **Güvenlik** - Security

---

## 🔄 Migration Çalıştırma

```bash
# Yeni migration'ı çalıştır
php artisan migrate

# Eğer hata alırsan:
php artisan migrate:status
php artisan migrate --force

# Rollback gerekirse:
php artisan migrate:rollback --step=1
```

---

## ✅ Test Adımları

### 1. Migration Test:
```bash
php artisan migrate
# Output: Migrated: 2025_10_11_115000_fix_faqs_nullable_fields
```

### 2. FAQ Ekleme Test:
```bash
# Dashboard'dan yeni FAQ ekle
# Hata almamalı
```

### 3. Database Kontrol:
```sql
DESCRIBE faqs;
-- description ve category NULL olmalı
```

---

## 🐛 Sorun Giderme

### Hata: "description cannot be null"

**Çözüm 1:** Migration çalıştır
```bash
php artisan migrate
```

**Çözüm 2:** Manuel SQL
```sql
ALTER TABLE faqs MODIFY COLUMN description TEXT NULL;
```

**Çözüm 3:** Controller'da default değer atanıyor (zaten yapıldı)

### Hata: "site_id required"

**Çözüm:** FAQController.php güncellemesi yapıldı. Değişiklikleri deploy et.

### Hata: "project_id does not exist"

**Kontrol:**
```sql
SELECT * FROM projects WHERE id = 5;
```

Yoksa:
```sql
-- Dummy project oluştur
INSERT INTO projects (id, name, created_by, created_at, updated_at) 
VALUES (5, 'ConvStateAI Website', 1, NOW(), NOW());
```

---

## 📝 Checklist

- [x] FAQController.php güncellendi
- [x] Migration oluşturuldu
- [ ] Migration çalıştırıldı (`php artisan migrate`)
- [ ] Test FAQ eklendi
- [ ] 10 adet FAQ CONVSTATEAI_FAQ_CONTENT.md'den eklendi
- [ ] Dashboard'da görünüyor mu kontrol edildi

---

## 🚀 Sonraki Adımlar

1. **Migration'ı çalıştır:**
   ```bash
   php artisan migrate
   ```

2. **FAQ'leri ekle:**
   - Dashboard'dan manuel
   - veya SQL ile toplu ekleme

3. **Test et:**
   - https://convstateai.com/dashboard/faqs?project_id=5
   - Yeni FAQ ekle
   - Düzenle
   - Sil

---

## 💾 SQL Toplu FAQ Ekleme

```sql
-- Project ID kontrolü
SELECT * FROM projects WHERE id = 5;

-- Site ID kontrolü  
SELECT * FROM sites WHERE id = 1;

-- FAQ Toplu Ekleme
INSERT INTO faqs (project_id, site_id, title, description, answer, category, is_active, sort_order, tags, created_at, updated_at) VALUES
(5, 1, 'ConvStateAI'ı kurdum, ne zaman sonuç almaya başlarım?', 
'Kurulum sonrası bekleme süresi hakkında', 
'İlk konversiyon artışını 24-48 saat içinde göreceksiniz. İşte gerçek: Çoğu firma AI chatbot kurduktan sonra haftalarca "optimizasyon" peşinde koşar. Biz farklıyız...', 
'Başlangıç', 1, 1, '["hızlı başlangıç", "sonuçlar", "kurulum"]', NOW(), NOW()),

(5, 1, 'Başka chatbot'lardan farkınız ne? Neden daha pahalı?',
'Rakip analizi ve fiyat karşılaştırması',
'Pahalı değiliz. PAHALI görünüyoruz. İşte gerçek matematik: Diğer Chatbotlar: $99/ay + Entegrasyon $500 + Geliştirici $2000/ay = $2,599/ay...',
'Özellikler', 1, 2, '["karşılaştırma", "ROI", "değer"]', NOW(), NOW());

-- Diğer 8 FAQ'i de ekle...
```

---

**Son Güncelleme:** 2025-10-11  
**Durum:** ✅ Düzeltmeler tamamlandı, migration hazır

