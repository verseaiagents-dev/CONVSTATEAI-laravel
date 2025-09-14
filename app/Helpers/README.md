# Laravel Helper Kullanım Kılavuzu

Bu dizin, projede kullanılan helper sınıflarını ve fonksiyonlarını içerir.

## 📁 Dizin Yapısı

```
app/Helpers/
├── AIHelper.php          # AI işlemleri için helper sınıfı
├── GeneralHelper.php     # Genel yardımcı fonksiyonlar
├── helpers.php          # Global helper fonksiyonları
└── README.md            # Bu dosya
```

## 🚀 Kullanım Yöntemleri

### 1. Helper Sınıflarını Kullanma

```php
use App\Helpers\AIHelper;
use App\Helpers\GeneralHelper;

// AI Helper kullanımı
$response = AIHelper::processMessage('merhaba');
$formatted = AIHelper::formatResponse($response);
$analysis = AIHelper::analyzeResponse($response);

// General Helper kullanımı
$slug = GeneralHelper::createSlug('Merhaba Dünya!');
$money = GeneralHelper::formatMoney(1234.56);
$date = GeneralHelper::formatDate('2024-01-15');
```

### 2. Global Helper Fonksiyonlarını Kullanma

```php
// AI Helper fonksiyonları
$response = ai_process('merhaba');
$formatted = ai_format('Test yanıtı');
$analysis = ai_analyze('Bu bir test mesajıdır');
ai_log('input', 'output', ['metadata']);

// General Helper fonksiyonları
$truncated = truncate('Uzun metin', 20);
$slug = create_slug('Merhaba Dünya!');
$random = random_string(8);
$money = format_money(1234.56);
$date = format_date('2024-01-15');
$email_valid = validate_email('test@example.com');
$phone = format_phone('05551234567');
$ip = client_ip();
```

## 🔧 Yeni Helper Ekleme

### 1. Helper Sınıfı Oluşturma

```php
<?php

namespace App\Helpers;

class NewHelper
{
    public static function newFunction(): string
    {
        return 'Yeni fonksiyon';
    }
}
```

### 2. Global Helper Fonksiyonu Ekleme

`app/Helpers/helpers.php` dosyasına ekleyin:

```php
if (!function_exists('new_helper_function')) {
    function new_helper_function(): string
    {
        return \App\Helpers\NewHelper::newFunction();
    }
}
```

### 3. Composer Autoload Güncelleme

```bash
composer dump-autoload
```

## 📝 Test Route'ları

Helper'ları test etmek için:

- `GET /test-helpers` - Tüm helper fonksiyonlarını test eder
- `POST /ai/response` - AI yanıt helper'ını test eder
- `POST /ai/personalized` - Kişiselleştirilmiş AI yanıt helper'ını test eder
- `GET /ai/stats` - AI istatistik helper'ını test eder
- `POST /ai/test-quality` - AI kalite test helper'ını test eder

## 🎯 Örnek Kullanım Senaryoları

### Controller'da Kullanım

```php
<?php

namespace App\Http\Controllers;

use App\Helpers\AIHelper;
use App\Helpers\GeneralHelper;

class ExampleController extends Controller
{
    public function processMessage(Request $request)
    {
        $message = $request->input('message');
        
        // Helper sınıfını kullan
        $response = AIHelper::processMessage($message);
        $analysis = AIHelper::analyzeResponse($response);
        
        // Global helper fonksiyonunu kullan
        $formatted = ai_format($response);
        
        return response()->json([
            'response' => $response,
            'analysis' => $analysis,
            'formatted' => $formatted,
            'ip' => client_ip()
        ]);
    }
}
```

### Blade Template'de Kullanım

```php
{{-- Global helper fonksiyonları kullan --}}
<p>{{ truncate($longText, 100) }}</p>
<p>{{ format_money($price) }}</p>
<p>{{ format_date($createdAt) }}</p>
<p>{{ create_slug($title) }}</p>
```

## ⚠️ Önemli Notlar

1. **Namespace**: Helper sınıfları `App\Helpers` namespace'inde olmalı
2. **Static Methods**: Helper sınıflarındaki metodlar static olmalı
3. **Function Exists**: Global helper fonksiyonlarında `function_exists` kontrolü yapılmalı
4. **Autoload**: Yeni helper ekledikten sonra `composer dump-autoload` çalıştırılmalı
5. **Performance**: Helper'lar lazy loading ile yüklenir, performans etkisi minimaldir

## 🔍 Debug ve Test

Helper'ları test etmek için:

```bash
# Laravel Tinker ile test
php artisan tinker

# Helper sınıfını test et
App\Helpers\AIHelper::processMessage('merhaba');

# Global helper fonksiyonunu test et
ai_process('merhaba');
```
