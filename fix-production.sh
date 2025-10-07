#!/bin/bash

# Production Server Route ve Cache Temizleme Script'i
# Bu script'i production server'da çalıştırın

echo "🔄 Laravel Cache ve Route Temizleme Başlatılıyor..."

# Route cache'i temizle
php artisan route:clear
echo "✅ Route cache temizlendi"

# Config cache'i temizle
php artisan config:clear
echo "✅ Config cache temizlendi"

# Application cache'i temizle
php artisan cache:clear
echo "✅ Application cache temizlendi"

# View cache'i temizle
php artisan view:clear
echo "✅ View cache temizlendi"

# Daily limits'i sıfırla
php artisan daily:reset-view-limits
echo "✅ Daily limits sıfırlandı"

# Route'ları yeniden cache'le (production için)
php artisan route:cache
echo "✅ Route cache yenilendi"

# Config'i yeniden cache'le (production için)
php artisan config:cache
echo "✅ Config cache yenilendi"

echo "🎉 Tüm işlemler tamamlandı!"
echo ""
echo "📋 Yapılan İşlemler:"
echo "   - Route cache temizlendi ve yenilendi"
echo "   - Config cache temizlendi ve yenilendi"
echo "   - Application cache temizlendi"
echo "   - View cache temizlendi"
echo "   - Daily limits sıfırlandı"
echo ""
echo "🔗 Test URL'leri:"
echo "   - Unified API: /api/unified/check-availability?project_id=3"
echo "   - Test Sayfası: /test-unified-api.html"
