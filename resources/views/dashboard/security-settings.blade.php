{{-- Security Settings Component - To be implemented later --}}
{{-- This file contains security-related settings that will be implemented in the future --}}

<!-- Security Settings Header -->
<div class="glass-effect rounded-2xl p-8 relative overflow-hidden">
    <div class="absolute top-0 right-0 w-32 h-32 bg-purple-glow rounded-full mix-blend-multiply filter blur-xl opacity-20"></div>
    <div class="absolute bottom-0 left-0 w-40 h-40 bg-neon-purple rounded-full mix-blend-multiply filter blur-xl opacity-20"></div>
    
    <div class="relative z-10">
        <h1 class="text-4xl font-bold mb-4">
            <span class="gradient-text">Güvenlik Ayarları</span>
        </h1>
        <p class="text-xl text-gray-300">
            Hesap güvenliğinizi ve gizlilik ayarlarınızı buradan yönetebilirsiniz.
        </p>
    </div>
</div>

<!-- Security Settings -->
<div class="glass-effect rounded-2xl p-8">
    <h2 class="text-2xl font-bold mb-6 text-white">Güvenlik Ayarları</h2>
    
    <div class="space-y-6">
        <!-- Two-Factor Authentication -->
        <div class="flex items-center justify-between p-4 bg-gray-800/30 rounded-lg">
            <div>
                <h3 class="text-lg font-semibold text-white">İki Faktörlü Doğrulama</h3>
                <p class="text-gray-400 text-sm">Hesabınızı ekstra güvenlik katmanı ile koruyun</p>
            </div>
            <button class="px-4 py-2 bg-purple-glow hover:bg-purple-dark rounded-lg text-white font-medium transition-colors duration-200">
                Etkinleştir
            </button>
        </div>

        <!-- Login History -->
        <div class="flex items-center justify-between p-4 bg-gray-800/30 rounded-lg">
            <div>
                <h3 class="text-lg font-semibold text-white">Giriş Geçmişi</h3>
                <p class="text-gray-400 text-sm">Hesabınıza yapılan girişleri görüntüleyin</p>
            </div>
            <button class="px-4 py-2 glass-effect rounded-lg text-white font-medium hover:bg-white hover:text-black transition-all duration-200">
                Görüntüle
            </button>
        </div>

        <!-- Active Sessions -->
        <div class="flex items-center justify-between p-4 bg-gray-800/30 rounded-lg">
            <div>
                <h3 class="text-lg font-semibold text-white">Aktif Oturumlar</h3>
                <p class="text-gray-400 text-sm">Şu anda aktif olan oturumları yönetin</p>
            </div>
            <button class="px-4 py-2 glass-effect rounded-lg text-white font-medium hover:bg-white hover:text-black transition-all duration-200">
                Yönet
            </button>
        </div>
    </div>
</div>

<!-- Notification Settings -->
<div class="glass-effect rounded-2xl p-8">
    <h2 class="text-2xl font-bold mb-6 text-white">Bildirim Ayarları</h2>
    
    <div class="space-y-6">
        <!-- Email Notifications -->
        <div>
            <h3 class="text-lg font-semibold text-white mb-4">E-posta Bildirimleri</h3>
            <div class="space-y-3">
                <label class="flex items-center space-x-3 cursor-pointer">
                    <input type="checkbox" class="w-4 h-4 text-purple-glow bg-gray-800 border-gray-600 rounded focus:ring-purple-glow focus:ring-2" checked>
                    <span class="text-gray-300">Hesap güvenliği bildirimleri</span>
                </label>
                <label class="flex items-center space-x-3 cursor-pointer">
                    <input type="checkbox" class="w-4 h-4 text-purple-glow bg-gray-800 border-gray-600 rounded focus:ring-purple-glow focus:ring-2" checked>
                    <span class="text-gray-300">Yeni özellik duyuruları</span>
                </label>
                <label class="flex items-center space-x-3 cursor-pointer">
                    <input type="checkbox" class="w-4 h-4 text-purple-glow bg-gray-800 border-gray-600 rounded focus:ring-purple-glow focus:ring-2">
                    <span class="text-gray-300">Haftalık raporlar</span>
                </label>
                <label class="flex items-center space-x-3 cursor-pointer">
                    <input type="checkbox" class="w-4 h-4 text-purple-glow bg-gray-800 border-gray-600 rounded focus:ring-purple-glow focus:ring-2" checked>
                    <span class="text-gray-300">Önemli güncellemeler</span>
                </label>
            </div>
        </div>

        <!-- Push Notifications -->
        <div>
            <h3 class="text-lg font-semibold text-white mb-4">Push Bildirimleri</div>
            <div class="space-y-3">
                <label class="flex items-center space-x-3 cursor-pointer">
                    <input type="checkbox" class="w-4 h-4 text-purple-glow bg-gray-800 border-gray-600 rounded focus:ring-purple-glow focus:ring-2">
                    <span class="text-gray-300">Tarayıcı push bildirimleri</span>
                </label>
                <label class="flex items-center space-x-3 cursor-pointer">
                    <input type="checkbox" class="w-4 h-4 text-purple-glow bg-gray-800 border-gray-600 rounded focus:ring-purple-glow focus:ring-2" checked>
                    <span class="text-gray-300">Sistem bildirimleri</span>
                </label>
            </div>
        </div>
    </div>
</div>

<!-- Privacy Settings -->
<div class="glass-effect rounded-2xl p-8">
    <h2 class="text-2xl font-bold mb-6 text-white">Gizlilik Ayarları</h2>
    
    <div class="space-y-6">
        <!-- Profile Visibility -->
        <div>
            <h3 class="text-lg font-semibold text-white mb-4">Profil Görünürlüğü</h3>
            <div class="space-y-3">
                <label class="flex items-center space-x-3 cursor-pointer">
                    <input type="radio" name="profile_visibility" class="w-4 h-4 text-purple-glow bg-gray-800 border-gray-600 focus:ring-purple-glow focus:ring-2" checked>
                    <span class="text-gray-300">Herkese açık</span>
                </label>
                <label class="flex items-center space-x-3 cursor-pointer">
                    <input type="radio" name="profile_visibility" class="w-4 h-4 text-purple-glow bg-gray-800 border-gray-600 focus:ring-purple-glow focus:ring-2">
                    <span class="text-gray-300">Sadece arkadaşlar</span>
                </label>
                <label class="flex items-center space-x-3 cursor-pointer">
                    <input type="radio" name="profile_visibility" class="w-4 h-4 text-purple-glow bg-gray-800 border-gray-600 focus:ring-purple-glow focus:ring-2">
                    <span class="text-gray-300">Gizli</span>
                </label>
            </div>
        </div>

        <!-- Data Collection -->
        <div>
            <h3 class="text-lg font-semibold text-white mb-4">Veri Toplama</h3>
            <div class="space-y-3">
                <label class="flex items-center space-x-3 cursor-pointer">
                    <input type="checkbox" class="w-4 h-4 text-purple-glow bg-gray-800 border-gray-600 rounded focus:ring-purple-glow focus:ring-2" checked>
                    <span class="text-gray-300">Kullanım analitikleri</span>
                </label>
                <label class="flex items-center space-x-3 cursor-pointer">
                    <input type="checkbox" class="w-4 h-4 text-purple-glow bg-gray-800 border-gray-600 rounded focus:ring-purple-glow focus:ring-2" checked>
                    <span class="text-gray-300">Hata raporları</span>
                </label>
                <label class="flex items-center space-x-3 cursor-pointer">
                    <input type="checkbox" class="w-4 h-4 text-purple-glow bg-gray-800 border-gray-600 rounded focus:ring-purple-glow focus:ring-2">
                    <span class="text-gray-300">Kişiselleştirilmiş reklamlar</span>
                </label>
            </div>
        </div>
    </div>
</div>
