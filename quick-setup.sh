#!/bin/bash

# ConvStateAI Quick Setup Script
# Hızlı kurulum için basit script

echo "🚀 ConvStateAI Quick Setup başlatılıyor..."

# Proje dizinini bul
SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
cd "$SCRIPT_DIR"

# PHP path'i bul
if command -v php &> /dev/null; then
    PHP_PATH=$(which php)
else
    echo "❌ PHP bulunamadı!"
    exit 1
fi

echo "📁 Proje dizini: $SCRIPT_DIR"
echo "🐘 PHP path: $PHP_PATH"

# 1. Bağımlılıkları yükle
echo "📦 Bağımlılıklar yükleniyor..."
composer install --no-dev --optimize-autoloader

# 2. Laravel cache'leri temizle ve oluştur
echo "🧹 Laravel cache'leri temizleniyor..."
$PHP_PATH artisan config:clear
$PHP_PATH artisan cache:clear
$PHP_PATH artisan route:clear
$PHP_PATH artisan view:clear

echo "⚡ Laravel cache'leri oluşturuluyor..."
$PHP_PATH artisan config:cache
$PHP_PATH artisan route:cache
$PHP_PATH artisan view:cache

# 3. Migration'ları çalıştır
echo "🗄️ Veritabanı migration'ları çalıştırılıyor..."
$PHP_PATH artisan migrate --force

# 4. Storage link oluştur
echo "🔗 Storage link oluşturuluyor..."
$PHP_PATH artisan storage:link

# 5. Cron job'ları kur
echo "⏰ Cron job'ları kuruluyor..."

# Mevcut crontab'ı yedekle
crontab -l > /tmp/crontab_backup_$(date +%Y%m%d_%H%M%S) 2>/dev/null || true

# Yeni cron job'ları oluştur
cat > /tmp/convstateai_cron << EOF
# ConvStateAI Cron Jobs
# Her dakika Laravel scheduler'ı çalıştır
* * * * * $PHP_PATH $SCRIPT_DIR/artisan schedule:run >> /dev/null 2>&1

# Her 10 dakikada stuck knowledge base'leri retry et
*/10 * * * * $PHP_PATH $SCRIPT_DIR/artisan knowledge-base:retry-stuck --minutes=10 >> /dev/null 2>&1
EOF

# Crontab'ı kur
crontab /tmp/convstateai_cron
rm /tmp/convstateai_cron

echo "✅ Cron job'ları kuruldu."

# 6. Queue worker'ı başlat (arka planda)
echo "🔄 Queue worker başlatılıyor..."
nohup $PHP_PATH artisan queue:work --queue=knowledge-base-processing --tries=3 --timeout=300 --sleep=3 > storage/logs/worker.log 2>&1 &
QUEUE_PID=$!

echo "✅ Queue worker başlatıldı (PID: $QUEUE_PID)"

# 7. Test komutları
echo "🧪 Test komutları çalıştırılıyor..."

# Queue test
$PHP_PATH artisan queue:work --once --queue=knowledge-base-processing > /dev/null 2>&1 && echo "✅ Queue test başarılı" || echo "⚠️ Queue test başarısız"

# Retry command test
$PHP_PATH artisan knowledge-base:retry-stuck --minutes=0 > /dev/null 2>&1 && echo "✅ Retry command test başarılı" || echo "⚠️ Retry command test başarısız"

echo ""
echo "🎉 Kurulum tamamlandı!"
echo ""
echo "📋 Servisler:"
echo "   - Queue Worker: Çalışıyor (PID: $QUEUE_PID)"
echo "   - Cron Jobs: Kuruldu"
echo "   - Laravel Scheduler: Etkinleştirildi"
echo ""
echo "🔧 Manuel komutlar:"
echo "   - Queue durumu: ps aux | grep 'queue:work'"
echo "   - Logları görüntüle: tail -f storage/logs/laravel.log"
echo "   - Cron job'ları: crontab -l"
echo ""
echo "⚠️ Önemli: Queue worker'ı durdurmak için: kill $QUEUE_PID"
echo ""
echo "🚀 Sunucunuz hazır! Artık dosya yükleyebilirsiniz."
