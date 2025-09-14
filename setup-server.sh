#!/bin/bash

# ConvStateAI Server Setup Script
# Mac ve Linux sunucularda çalışır
# Kullanım: ./setup-server.sh

set -e  # Hata durumunda scripti durdur

# Renkli çıktı için
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Log fonksiyonu
log() {
    echo -e "${GREEN}[$(date +'%Y-%m-%d %H:%M:%S')] $1${NC}"
}

error() {
    echo -e "${RED}[ERROR] $1${NC}"
    exit 1
}

warning() {
    echo -e "${YELLOW}[WARNING] $1${NC}"
}

info() {
    echo -e "${BLUE}[INFO] $1${NC}"
}

# Sistem kontrolü
check_system() {
    log "Sistem kontrolü yapılıyor..."
    
    # OS tespiti
    if [[ "$OSTYPE" == "darwin"* ]]; then
        OS="mac"
        PHP_PATH="/usr/local/bin/php"
        CRON_SERVICE="cron"
    elif [[ "$OSTYPE" == "linux-gnu"* ]]; then
        OS="linux"
        PHP_PATH="/usr/bin/php"
        CRON_SERVICE="cron"
    else
        error "Desteklenmeyen işletim sistemi: $OSTYPE"
    fi
    
    info "İşletim sistemi: $OS"
    
    # PHP kontrolü
    if ! command -v php &> /dev/null; then
        error "PHP bulunamadı. Lütfen PHP yükleyin."
    fi
    
    # Composer kontrolü
    if ! command -v composer &> /dev/null; then
        error "Composer bulunamadı. Lütfen Composer yükleyin."
    fi
    
    # Node.js kontrolü (opsiyonel)
    if ! command -v node &> /dev/null; then
        warning "Node.js bulunamadı. Frontend build için gerekli olabilir."
    fi
    
    log "Sistem kontrolü tamamlandı."
}

# Proje dizinini bul
find_project_dir() {
    SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
    PROJECT_DIR="$SCRIPT_DIR"
    
    if [ ! -f "$PROJECT_DIR/artisan" ]; then
        error "Laravel projesi bulunamadı. Script Laravel proje dizininde çalıştırılmalı."
    fi
    
    log "Proje dizini: $PROJECT_DIR"
}

# Bağımlılıkları yükle
install_dependencies() {
    log "Bağımlılıklar yükleniyor..."
    
    cd "$PROJECT_DIR"
    
    # Composer bağımlılıkları
    if [ -f "composer.json" ]; then
        composer install --no-dev --optimize-autoloader
        log "Composer bağımlılıkları yüklendi."
    fi
    
    # NPM bağımlılıkları (varsa)
    if [ -f "package.json" ]; then
        if command -v npm &> /dev/null; then
            npm install --production
            npm run build
            log "NPM bağımlılıkları yüklendi ve build edildi."
        else
            warning "NPM bulunamadı, frontend build atlandı."
        fi
    fi
}

# Laravel konfigürasyonu
setup_laravel() {
    log "Laravel konfigürasyonu yapılıyor..."
    
    cd "$PROJECT_DIR"
    
    # .env dosyası kontrolü
    if [ ! -f ".env" ]; then
        if [ -f ".env.example" ]; then
            cp .env.example .env
            warning ".env dosyası .env.example'dan oluşturuldu. Lütfen gerekli ayarları yapın."
        else
            error ".env dosyası bulunamadı."
        fi
    fi
    
    # Cache temizleme
    php artisan config:clear
    php artisan cache:clear
    php artisan route:clear
    php artisan view:clear
    
    # Cache oluşturma
    php artisan config:cache
    php artisan route:cache
    php artisan view:cache
    
    # Migration'ları çalıştır
    php artisan migrate --force
    
    # Storage link oluştur
    php artisan storage:link
    
    log "Laravel konfigürasyonu tamamlandı."
}

# Queue worker servisi oluştur
create_queue_service() {
    log "Queue worker servisi oluşturuluyor..."
    
    if [ "$OS" = "linux" ]; then
        # Systemd service oluştur
        cat > /tmp/convstateai-worker.service << EOF
[Unit]
Description=ConvStateAI Queue Worker
After=network.target

[Service]
Type=simple
User=www-data
Group=www-data
WorkingDirectory=$PROJECT_DIR
ExecStart=$PHP_PATH artisan queue:work --queue=knowledge-base-processing --tries=3 --timeout=300 --sleep=3 --max-time=3600
Restart=always
RestartSec=5
StandardOutput=syslog
StandardError=syslog
SyslogIdentifier=convstateai-worker

[Install]
WantedBy=multi-user.target
EOF
        
        sudo mv /tmp/convstateai-worker.service /etc/systemd/system/
        sudo systemctl daemon-reload
        sudo systemctl enable convstateai-worker
        sudo systemctl start convstateai-worker
        
        log "Systemd servisi oluşturuldu ve başlatıldı."
        
    elif [ "$OS" = "mac" ]; then
        # LaunchAgent oluştur (macOS)
        mkdir -p ~/Library/LaunchAgents
        cat > ~/Library/LaunchAgents/com.convstateai.worker.plist << EOF
<?xml version="1.0" encoding="UTF-8"?>
<!DOCTYPE plist PUBLIC "-//Apple//DTD PLIST 1.0//EN" "http://www.apple.com/DTDs/PropertyList-1.0.dtd">
<plist version="1.0">
<dict>
    <key>Label</key>
    <string>com.convstateai.worker</string>
    <key>ProgramArguments</key>
    <array>
        <string>$PHP_PATH</string>
        <string>$PROJECT_DIR/artisan</string>
        <string>queue:work</string>
        <string>--queue=knowledge-base-processing</string>
        <string>--tries=3</string>
        <string>--timeout=300</string>
        <string>--sleep=3</string>
        <string>--max-time=3600</string>
    </array>
    <key>WorkingDirectory</key>
    <string>$PROJECT_DIR</string>
    <key>RunAtLoad</key>
    <true/>
    <key>KeepAlive</key>
    <true/>
    <key>StandardOutPath</key>
    <string>$PROJECT_DIR/storage/logs/worker.log</string>
    <key>StandardErrorPath</key>
    <string>$PROJECT_DIR/storage/logs/worker-error.log</string>
</dict>
</plist>
EOF
        
        launchctl load ~/Library/LaunchAgents/com.convstateai.worker.plist
        log "LaunchAgent oluşturuldu ve yüklendi."
    fi
}

# Cron job'ları kur
setup_cron() {
    log "Cron job'ları kuruluyor..."
    
    # Mevcut crontab'ı yedekle
    crontab -l > /tmp/crontab_backup_$(date +%Y%m%d_%H%M%S) 2>/dev/null || true
    
    # Yeni cron job'ları oluştur
    cat > /tmp/convstateai_cron << EOF
# ConvStateAI Cron Jobs
# Her dakika Laravel scheduler'ı çalıştır
* * * * * $PHP_PATH $PROJECT_DIR/artisan schedule:run >> /dev/null 2>&1

# Her 10 dakikada stuck knowledge base'leri retry et
*/10 * * * * $PHP_PATH $PROJECT_DIR/artisan knowledge-base:retry-stuck --minutes=10 >> /dev/null 2>&1

# Her gün saat 02:00'da log dosyalarını temizle
0 2 * * * find $PROJECT_DIR/storage/logs -name "*.log" -mtime +7 -delete >> /dev/null 2>&1

# Her gün saat 03:00'da cache'i temizle
0 3 * * * $PHP_PATH $PROJECT_DIR/artisan cache:clear >> /dev/null 2>&1
EOF
    
    # Crontab'ı kur
    crontab /tmp/convstateai_cron
    rm /tmp/convstateai_cron
    
    log "Cron job'ları kuruldu."
}

# Laravel scheduler'ı etkinleştir
setup_scheduler() {
    log "Laravel scheduler etkinleştiriliyor..."
    
    # Kernel.php'de schedule tanımları ekle (eğer yoksa)
    KERNEL_FILE="$PROJECT_DIR/app/Console/Kernel.php"
    
    if [ -f "$KERNEL_FILE" ]; then
        # Schedule tanımları kontrol et
        if ! grep -q "knowledge-base:retry-stuck" "$KERNEL_FILE"; then
            warning "Laravel scheduler'da knowledge-base:retry-stuck tanımlı değil."
            info "Manuel olarak Kernel.php'de schedule tanımları ekleyebilirsiniz."
        fi
    fi
}

# Log dizinlerini oluştur
setup_logs() {
    log "Log dizinleri oluşturuluyor..."
    
    mkdir -p "$PROJECT_DIR/storage/logs"
    chmod 755 "$PROJECT_DIR/storage/logs"
    
    # Log dosyalarını oluştur
    touch "$PROJECT_DIR/storage/logs/laravel.log"
    touch "$PROJECT_DIR/storage/logs/worker.log"
    touch "$PROJECT_DIR/storage/logs/worker-error.log"
    
    chmod 644 "$PROJECT_DIR/storage/logs"/*.log
    
    log "Log dizinleri oluşturuldu."
}

# Servis durumlarını kontrol et
check_services() {
    log "Servis durumları kontrol ediliyor..."
    
    # Queue worker kontrolü
    if [ "$OS" = "linux" ]; then
        if systemctl is-active --quiet convstateai-worker; then
            log "Queue worker servisi çalışıyor."
        else
            error "Queue worker servisi çalışmıyor."
        fi
    elif [ "$OS" = "mac" ]; then
        if launchctl list | grep -q "com.convstateai.worker"; then
            log "Queue worker LaunchAgent yüklü."
        else
            error "Queue worker LaunchAgent yüklenmemiş."
        fi
    fi
    
    # Cron kontrolü
    if crontab -l | grep -q "convstateai"; then
        log "Cron job'ları kurulu."
    else
        error "Cron job'ları kurulmamış."
    fi
    
    # Laravel kontrolü
    cd "$PROJECT_DIR"
    if php artisan --version > /dev/null 2>&1; then
        log "Laravel çalışıyor."
    else
        error "Laravel çalışmıyor."
    fi
}

# Test komutları
run_tests() {
    log "Test komutları çalıştırılıyor..."
    
    cd "$PROJECT_DIR"
    
    # Queue test
    php artisan queue:work --once --queue=knowledge-base-processing > /dev/null 2>&1 && log "Queue test başarılı." || warning "Queue test başarısız."
    
    # Retry command test
    php artisan knowledge-base:retry-stuck --minutes=0 > /dev/null 2>&1 && log "Retry command test başarılı." || warning "Retry command test başarısız."
    
    log "Test komutları tamamlandı."
}

# Temizlik
cleanup() {
    log "Geçici dosyalar temizleniyor..."
    rm -f /tmp/convstateai_*
    log "Temizlik tamamlandı."
}

# Ana fonksiyon
main() {
    log "ConvStateAI Server Setup başlatılıyor..."
    
    check_system
    find_project_dir
    install_dependencies
    setup_laravel
    create_queue_service
    setup_cron
    setup_scheduler
    setup_logs
    check_services
    run_tests
    cleanup
    
    log "Setup tamamlandı!"
    info "Servisler:"
    info "- Queue Worker: Otomatik başlatıldı"
    info "- Cron Jobs: Kuruldu"
    info "- Laravel Scheduler: Etkinleştirildi"
    info ""
    info "Manuel komutlar:"
    info "- Queue durumu: systemctl status convstateai-worker (Linux) veya launchctl list | grep convstateai (Mac)"
    info "- Logları görüntüle: tail -f $PROJECT_DIR/storage/logs/laravel.log"
    info "- Cron job'ları: crontab -l"
    info ""
    info "Sorun giderme:"
    info "- Queue worker yeniden başlat: systemctl restart convstateai-worker (Linux)"
    info "- Cron yeniden başlat: sudo service cron restart (Linux) veya sudo launchctl stop com.apple.cron (Mac)"
}

# Script'i çalıştır
main "$@"
