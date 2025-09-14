# Project-Based API Endpoints Documentation

Bu dokümantasyon, ConvStateAI Laravel uygulamasındaki project-based API endpoint'lerini açıklar.

## 📋 API Endpoint'leri ve URL'leri

### 1. CampaignController::checkAvailability

**API Group:** `campaigns`  
**Method:** `GET`  
**URL:** `http://127.0.0.1:8000/api/campaigns/check-availability`  
**Query Parameter:** `project_id` (required)

#### Request Example:
```bash
curl "http://127.0.0.1:8000/api/campaigns/check-availability?project_id=1"
```

#### Response:
```json
{
  "success": true,
  "has_campaigns": false,
  "campaign_count": 0,
  "message": "Kampanya durumu başarıyla kontrol edildi"
}
```

#### Error Response:
```json
{
  "success": false,
  "has_campaigns": false,
  "message": "Project ID gerekli"
}
```

---

### 2. FAQController::checkAvailability

**API Group:** `faqs`  
**Method:** `GET`  
**URL:** `http://127.0.0.1:8000/api/faqs/check-availability`  
**Query Parameter:** `project_id` (required)

#### Request Example:
```bash
curl "http://127.0.0.1:8000/api/faqs/check-availability?project_id=1"
```

#### Response:
```json
{
  "success": true,
  "has_faqs": false,
  "faq_count": 0,
  "message": "SSS durumu başarıyla kontrol edildi"
}
```

#### Error Response:
```json
{
  "success": false,
  "has_faqs": false,
  "message": "Project ID gerekli"
}
```

---

### 3. NotificationWidgetController::checkAvailability

**API Group:** `notification-widget`  
**Method:** `GET`  
**URL:** `http://127.0.0.1:8000/api/notification-widget/check-availability`  
**Query Parameter:** `project_id` (optional)

#### Request Example:
```bash
curl "http://127.0.0.1:8000/api/notification-widget/check-availability?project_id=1"
```

#### Response (No Notification):
```json
{
  "success": true,
  "has_notification": false
}
```

#### Response (With Notification):
```json
{
  "success": true,
  "has_notification": true,
  "data": {
    "message_text": "Sizin için kampanyamız var!",
    "ai_name": "CONVSTATEAI",
    "color_theme": "purple",
    "display_duration": 5000,
    "animation_type": "fade",
    "show_close_button": true,
    "redirect_url": "https://example.com",
    "color_theme_css": "background: linear-gradient(135deg, #8B5CF6, #A855F7);"
  }
}
```

---

## 🔧 Route Tanımlamaları

### Campaign API Routes
```php
Route::prefix('campaigns')->group(function () {
    Route::get('/check-availability', [App\Http\Controllers\CampaignController::class, 'checkAvailability']);
    Route::get('/', [App\Http\Controllers\CampaignController::class, 'index']);
    Route::post('/', [App\Http\Controllers\CampaignController::class, 'store']);
    Route::get('/count/active', [App\Http\Controllers\CampaignController::class, 'getActiveCount']);
    Route::get('/category/{category}', [App\Http\Controllers\CampaignController::class, 'getByCategory']);
    Route::get('/{id}', [App\Http\Controllers\CampaignController::class, 'show']);
    Route::put('/{id}', [App\Http\Controllers\CampaignController::class, 'update']);
    Route::delete('/{id}', [App\Http\Controllers\CampaignController::class, 'destroy']);
});
```

### FAQ API Routes
```php
Route::prefix('faqs')->group(function () {
    Route::get('/check-availability', [App\Http\Controllers\FAQController::class, 'checkAvailability']);
    Route::get('/', [App\Http\Controllers\FAQController::class, 'index']);
    Route::post('/', [App\Http\Controllers\FAQController::class, 'store']);
    Route::get('/search', [App\Http\Controllers\FAQController::class, 'search']);
    Route::get('/popular', [App\Http\Controllers\FAQController::class, 'getPopular']);
    Route::get('/category/{category}', [App\Http\Controllers\FAQController::class, 'getByCategory']);
    Route::get('/{id}', [App\Http\Controllers\FAQController::class, 'show']);
    Route::put('/{id}', [App\Http\Controllers\FAQController::class, 'update']);
    Route::delete('/{id}', [App\Http\Controllers\FAQController::class, 'destroy']);
});
```

### Notification Widget API Routes
```php
Route::prefix('notification-widget')->group(function () {
    Route::get('/check-availability', [App\Http\Controllers\NotificationWidgetController::class, 'checkAvailability']);
    Route::get('/settings', [App\Http\Controllers\NotificationWidgetController::class, 'getSettings']);
    Route::post('/settings', [App\Http\Controllers\NotificationWidgetController::class, 'updateSettings']);
});
```

---

## 📊 Response Format

Tüm API endpoint'leri aşağıdaki standart response formatını kullanır:

### Success Response
```json
{
  "success": true,
  "has_[resource]": boolean,
  "[resource]_count": number,
  "message": string
}
```

### Error Response
```json
{
  "success": false,
  "has_[resource]": false,
  "message": string
}
```

---

## 🚀 Kullanım Örnekleri

### JavaScript/Fetch API
```javascript
// Campaign availability check
const checkCampaigns = async (projectId) => {
  const response = await fetch(`/api/campaigns/check-availability?project_id=${projectId}`);
  const data = await response.json();
  return data;
};

// FAQ availability check
const checkFAQs = async (projectId) => {
  const response = await fetch(`/api/faqs/check-availability?project_id=${projectId}`);
  const data = await response.json();
  return data;
};

// Notification widget availability check
const checkNotifications = async (projectId) => {
  const response = await fetch(`/api/notification-widget/check-availability?project_id=${projectId}`);
  const data = await response.json();
  return data;
};
```

### cURL Examples
```bash
# Check campaigns
curl "http://127.0.0.1:8000/api/campaigns/check-availability?project_id=1"

# Check FAQs
curl "http://127.0.0.1:8000/api/faqs/check-availability?project_id=1"

# Check notifications
curl "http://127.0.0.1:8000/api/notification-widget/check-availability?project_id=1"
```

---

## 🔍 Özellikler

- **Project-based:** Tüm endpoint'ler `project_id` parametresi ile çalışır
- **RESTful:** GET method kullanılır
- **Consistent:** Tüm endpoint'ler aynı response formatını kullanır
- **Error Handling:** Eksik parametreler için uygun hata mesajları
- **Performance:** Sadece aktif kayıtlar sayılır
- **Security:** Project ID validation ile güvenlik sağlanır

---

## 📝 Notlar

- Tüm endpoint'ler project-based çalışır
- `project_id` parametresi zorunludur (notification-widget hariç)
- Response'lar JSON formatındadır
- HTTP status kodları: 200 (success), 400 (bad request), 500 (server error)
- Cache header'ları optimize edilmiştir

---

## 🔗 İlgili Dosyalar

- **Controllers:** `app/Http/Controllers/CampaignController.php`, `app/Http/Controllers/FAQController.php`, `app/Http/Controllers/NotificationWidgetController.php`
- **Models:** `app/Models/Campaign.php`, `app/Models/FAQ.php`, `app/Models/NotificationWidgetSetting.php`
- **Routes:** `routes/api.php`
- **Migrations:** `database/migrations/2025_09_13_181447_add_project_id_to_campaigns_table.php`, `database/migrations/2025_09_13_181306_add_project_id_to_faqs_table.php`

---

*Son güncelleme: 13 Eylül 2025*
