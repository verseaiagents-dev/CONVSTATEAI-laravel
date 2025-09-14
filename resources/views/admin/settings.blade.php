@extends('layouts.admin')

@section('title', 'Admin Ayarlar - ConvStateAI')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="glass-effect rounded-2xl p-8 relative overflow-hidden">
        <div class="absolute top-0 right-0 w-32 h-32 bg-purple-glow rounded-full mix-blend-multiply filter blur-xl opacity-20"></div>
        <div class="absolute bottom-0 left-0 w-40 h-40 bg-neon-purple rounded-full mix-blend-multiply filter blur-xl opacity-20"></div>
        
        <div class="relative z-10">
            <h1 class="text-4xl font-bold mb-4">
                <span class="gradient-text">Sistem Ayarları</span> ⚙️
            </h1>
            <p class="text-xl text-gray-300 mb-6">
                Sistem genelinde ayarları yönetin ve konfigürasyonları düzenleyin.
            </p>
        </div>
    </div>

    <!-- Settings Categories -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- General Settings -->
        <div class="glass-effect rounded-2xl p-8">
            <h2 class="text-2xl font-bold mb-6 text-white">Genel Ayarlar</h2>
            
            <div class="space-y-6">
                <div class="bg-gray-800/50 rounded-lg p-4">
                    <h4 class="text-lg font-semibold text-white mb-3">Site Bilgileri</h4>
                    <div class="space-y-3">
                        <div>
                            <label class="block text-sm font-medium text-gray-400 mb-1">Site Adı</label>
                            <input type="text" value="ConvStateAI" class="w-full bg-gray-700 border border-gray-600 rounded-lg px-3 py-2 text-white focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-400 mb-1">Site Açıklaması</label>
                            <textarea class="w-full bg-gray-700 border border-gray-600 rounded-lg px-3 py-2 text-white focus:ring-2 focus:ring-purple-500 focus:border-transparent" rows="3">AI destekli e-ticaret chatbot çözümü</textarea>
                        </div>
                    </div>
                </div>

                <div class="bg-gray-800/50 rounded-lg p-4">
                    <h4 class="text-lg font-semibold text-white mb-3">E-posta Ayarları</h4>
                    <div class="space-y-3">
                        <div>
                            <label class="block text-sm font-medium text-gray-400 mb-1">SMTP Host</label>
                            <input type="text" placeholder="smtp.gmail.com" class="w-full bg-gray-700 border border-gray-600 rounded-lg px-3 py-2 text-white focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-400 mb-1">SMTP Port</label>
                            <input type="number" placeholder="587" class="w-full bg-gray-700 border border-gray-600 rounded-lg px-3 py-2 text-white focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Security Settings -->
        <div class="glass-effect rounded-2xl p-8">
            <h2 class="text-2xl font-bold mb-6 text-white">Güvenlik Ayarları</h2>
            
            <div class="space-y-6">
                <div class="bg-gray-800/50 rounded-lg p-4">
                    <h4 class="text-lg font-semibold text-white mb-3">Kimlik Doğrulama</h4>
                    <div class="space-y-3">
                        <div class="flex items-center justify-between">
                            <span class="text-gray-300">2FA Zorunlu</span>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" class="sr-only peer">
                                <div class="w-11 h-6 bg-gray-600 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-purple-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-purple-600"></div>
                            </label>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-gray-300">Güçlü Şifre Zorunlu</span>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" checked class="sr-only peer">
                                <div class="w-11 h-6 bg-gray-600 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-purple-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-purple-600"></div>
                            </label>
                        </div>
                    </div>
                </div>

                <div class="bg-gray-800/50 rounded-lg p-4">
                    <h4 class="text-lg font-semibold text-white mb-3">API Güvenliği</h4>
                    <div class="space-y-3">
                        <div>
                            <label class="block text-sm font-medium text-gray-400 mb-1">API Rate Limit</label>
                            <input type="number" placeholder="100" class="w-full bg-gray-700 border border-gray-600 rounded-lg px-3 py-2 text-white focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-400 mb-1">Token Süresi (saat)</label>
                            <input type="number" placeholder="24" class="w-full bg-gray-700 border border-gray-600 rounded-lg px-3 py-2 text-white focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- System Status -->
    <div class="glass-effect rounded-2xl p-8">
        <h2 class="text-2xl font-bold mb-6 text-white">Sistem Durumu</h2>
        
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div class="bg-gray-800/50 rounded-lg p-4 text-center">
                <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-3">
                    <i class="fas fa-server text-green-600 text-xl"></i>
                </div>
                <h4 class="text-sm font-medium text-gray-400 mb-1">Veritabanı</h4>
                <p class="text-green-400 font-semibold">Çevrimiçi</p>
            </div>
            <div class="bg-gray-800/50 rounded-lg p-4 text-center">
                <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-3">
                    <i class="fas fa-envelope text-green-600 text-xl"></i>
                </div>
                <h4 class="text-sm font-medium text-gray-400 mb-1">E-posta</h4>
                <p class="text-green-400 font-semibold">Aktif</p>
            </div>
            <div class="bg-gray-800/50 rounded-lg p-4 text-center">
                <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-3">
                    <i class="fas fa-robot text-green-600 text-xl"></i>
                </div>
                <h4 class="text-sm font-medium text-gray-400 mb-1">AI Servis</h4>
                <p class="text-green-400 font-semibold">Çalışıyor</p>
            </div>
            <div class="bg-gray-800/50 rounded-lg p-4 text-center">
                <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-3">
                    <i class="fas fa-shield-alt text-green-600 text-xl"></i>
                </div>
                <h4 class="text-sm font-medium text-gray-400 mb-1">Güvenlik</h4>
                <p class="text-green-400 font-semibold">Güvenli</p>
            </div>
        </div>
    </div>

    <!-- Save Button -->
    <div class="flex justify-end">
        <button class="px-8 py-3 bg-gradient-to-r from-purple-glow to-neon-purple text-white font-semibold rounded-lg hover:from-purple-dark hover:to-neon-purple transition-all duration-300 transform hover:scale-105">
            <i class="fas fa-save mr-2"></i>
            Ayarları Kaydet
        </button>
    </div>
</div>
@endsection
