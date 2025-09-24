#!/bin/bash

# ConvState AI - Production Deployment Script
# Bu script production sunucusuna deploy için gerekli adımları içerir

echo "🚀 ConvState AI Production Deployment Başlatılıyor..."

# 1. Git durumunu kontrol et
echo "📋 Git durumu kontrol ediliyor..."
git status

# 2. Değişiklikleri commit et
echo "💾 Değişiklikler commit ediliyor..."
git add .
git commit -m "feat: Add unified availability API endpoint

- Add UnifiedAvailabilityController for single API call
- Merge checkTabAvailability and loadNotificationWidget functions
- Reduce API calls from 3 to 1 for better performance
- Add development/production environment support
- Update React build with development mode"

# 3. Production branch'e push et
echo "📤 Production branch'e push ediliyor..."
git push origin main

# 4. Production sunucusunda çalıştırılacak komutlar
echo "🔧 Production sunucusunda çalıştırılacak komutlar:"
echo ""
echo "1. Git pull yapın:"
echo "   git pull origin main"
echo ""
echo "2. Composer dependencies güncelleyin:"
echo "   composer install --no-dev --optimize-autoloader"
echo ""
echo "3. Cache'leri temizleyin:"
echo "   php artisan config:cache"
echo "   php artisan route:cache"
echo "   php artisan view:cache"
echo ""
echo "4. Route'ları kontrol edin:"
echo "   php artisan route:list | grep check-availability"
echo ""
echo "5. API endpoint'ini test edin:"
echo "   curl -X GET 'https://convstateai.com/api/check-availability?project_id=1'"
echo ""

echo "✅ Deployment script tamamlandı!"
echo "📝 Production sunucusunda yukarıdaki adımları takip edin."
