# ConvStateAI Server Setup

Bu doküman ConvStateAI uygulamasını sunucuya kurmak ve otomatik çalıştırmak için gerekli adımları açıklar.

## 🚀 Hızlı Kurulum

### 1. Basit Kurulum (Önerilen)
```bash
# Proje dizininde
chmod +x quick-setup.sh
./quick-setup.sh
```

### 2. Detaylı Kurulum
```bash
# Proje dizininde
chmod +x setup-server.sh
./setup-server.sh
```

## 📋 Kurulum Öncesi Gereksinimler

### Mac
- PHP 8.1+
- Composer
- MySQL/PostgreSQL
- Node.js (opsiyonel)

### Linux
- PHP 8.1+
- Composer
- MySQL/PostgreSQL
- Node.js (opsiyonel)
- systemd (servis yönetimi için)

## 🔧 Kurulum Sonrası

### Otomatik Servisler
- **Queue Worker**: Knowledge base dosyalarını işler
- **Cron Jobs**: Stuck knowledge base'leri otomatik retry eder
- **Laravel Scheduler**: Zamanlanmış görevleri çalıştırır

### Manuel Komutlar

#### Queue Worker Yönetimi
```bash
# Queue worker'ı başlat
php artisan queue:work --queue=knowledge-base-processing --tries=3 --timeout=300

# Queue worker'ı durdur
pkill -f "queue:work"

# Queue durumunu kontrol et
ps aux | grep "queue:work"
```

#### Stuck Knowledge Base Retry
```bash
# Manuel retry
php artisan knowledge-base:retry-stuck --minutes=30

# Tüm pending'leri retry et
php artisan knowledge-base:retry-stuck --minutes=0
```

#### Log Kontrolü
```bash
# Laravel logları
tail -f storage/logs/laravel.log

# Queue worker logları
tail -f storage/logs/worker.log

# Hata logları
tail -f storage/logs/worker-error.log
```

#### Cron Job Yönetimi
```bash
# Cron job'ları listele
crontab -l

# Cron job'ları düzenle
crontab -e

# Cron servisini yeniden başlat (Linux)
sudo service cron restart
```

## 🐛 Sorun Giderme

### Queue Worker Çalışmıyor
```bash
# Process kontrolü
ps aux | grep "queue:work"

# Manuel başlatma
nohup php artisan queue:work --queue=knowledge-base-processing > storage/logs/worker.log 2>&1 &

# Log kontrolü
tail -f storage/logs/worker.log
```

### Cron Job'ları Çalışmıyor
```bash
# Cron servis durumu (Linux)
sudo systemctl status cron

# Cron logları (Linux)
sudo tail -f /var/log/cron

# Cron job'ları test et
php artisan schedule:run
```

### Knowledge Base İşleme Sorunları
```bash
# Pending knowledge base'leri kontrol et
php artisan tinker --execute="echo \App\Models\KnowledgeBase::where('processing_status', 'pending')->count();"

# Manuel retry
php artisan knowledge-base:retry-stuck --minutes=0

# Queue'da job var mı kontrol et
php artisan tinker --execute="echo \Illuminate\Support\Facades\DB::table('jobs')->count();"
```

## 🔄 Servis Yönetimi

### Systemd (Linux)
```bash
# Servis durumu
sudo systemctl status convstateai-worker

# Servis başlat
sudo systemctl start convstateai-worker

# Servis durdur
sudo systemctl stop convstateai-worker

# Servis yeniden başlat
sudo systemctl restart convstateai-worker

# Servis otomatik başlatma
sudo systemctl enable convstateai-worker
```

### LaunchAgent (macOS)
```bash
# LaunchAgent durumu
launchctl list | grep convstateai

# LaunchAgent yükle
launchctl load ~/Library/LaunchAgents/com.convstateai.worker.plist

# LaunchAgent kaldır
launchctl unload ~/Library/LaunchAgents/com.convstateai.worker.plist
```

## 📊 Monitoring

### Sistem Durumu Kontrolü
```bash
# Tüm servislerin durumu
./setup-server.sh  # Sadece check_services kısmını çalıştırır
```

### Log Rotasyonu
Log dosyaları otomatik olarak 7 günden eski olanlar silinir. Manuel temizlik için:
```bash
find storage/logs -name "*.log" -mtime +7 -delete
```

## 🚨 Önemli Notlar

1. **Queue Worker**: Sürekli çalışması gereken kritik servis
2. **Cron Jobs**: Stuck knowledge base'leri otomatik retry eder
3. **Log Dosyaları**: Düzenli olarak temizlenir
4. **Cache**: Her gün otomatik temizlenir
5. **Database**: Migration'lar otomatik çalışır

## 📞 Destek

Sorun yaşarsanız:
1. Log dosyalarını kontrol edin
2. Servis durumlarını kontrol edin
3. Manuel komutları deneyin
4. Gerekirse servisleri yeniden başlatın

## 🔄 Güncelleme

Yeni sürüm kurulumu:
```bash
git pull origin main
composer install --no-dev --optimize-autoloader
php artisan migrate --force
php artisan config:cache
php artisan route:cache
php artisan view:cache
```
