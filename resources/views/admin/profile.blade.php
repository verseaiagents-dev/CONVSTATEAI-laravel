@extends('layouts.admin')

@section('title', 'Admin Profil - ConvStateAI')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="glass-effect rounded-2xl p-8 relative overflow-hidden">
        <div class="absolute top-0 right-0 w-32 h-32 bg-purple-glow rounded-full mix-blend-multiply filter blur-xl opacity-20"></div>
        <div class="absolute bottom-0 left-0 w-40 h-40 bg-neon-purple rounded-full mix-blend-multiply filter blur-xl opacity-20"></div>
        
        <div class="relative z-10">
            <h1 class="text-4xl font-bold mb-4">
                <span class="gradient-text">Admin Profil</span> 👤
            </h1>
            <p class="text-xl text-gray-300 mb-6">
                Profil bilgilerinizi görüntüleyin ve düzenleyin.
            </p>
        </div>
    </div>

    <!-- Profile Information -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Profile Details -->
        <div class="glass-effect rounded-2xl p-8">
            <h2 class="text-2xl font-bold mb-6 text-white">Profil Bilgileri</h2>
            
            <div class="space-y-4">
                <div class="flex items-center space-x-4">
                    <div class="w-20 h-20 bg-gradient-to-r from-purple-glow to-neon-purple rounded-full flex items-center justify-center">
                        <span class="text-2xl font-bold text-white">{{ substr($admin->name, 0, 1) }}</span>
                    </div>
                    <div>
                        <h3 class="text-xl font-semibold text-white">{{ $admin->name }}</h3>
                        <p class="text-gray-400">{{ $admin->email }}</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-6">
                    <div class="bg-gray-800/50 rounded-lg p-4">
                        <h4 class="text-sm font-medium text-gray-400 mb-2">Ad Soyad</h4>
                        <p class="text-white">{{ $admin->name }}</p>
                    </div>
                    <div class="bg-gray-800/50 rounded-lg p-4">
                        <h4 class="text-sm font-medium text-gray-400 mb-2">E-posta</h4>
                        <p class="text-white">{{ $admin->email }}</p>
                    </div>
                    <div class="bg-gray-800/50 rounded-lg p-4">
                        <h4 class="text-sm font-medium text-gray-400 mb-2">Admin Durumu</h4>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                            <i class="fas fa-check-circle mr-1"></i>
                            Aktif Admin
                        </span>
                    </div>
                    <div class="bg-gray-800/50 rounded-lg p-4">
                        <h4 class="text-sm font-medium text-gray-400 mb-2">Kayıt Tarihi</h4>
                        <p class="text-white">{{ $admin->created_at->format('d.m.Y H:i') }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Account Actions -->
        <div class="glass-effect rounded-2xl p-8">
            <h2 class="text-2xl font-bold mb-6 text-white">Hesap İşlemleri</h2>
            
            <div class="space-y-4">
                <button class="w-full bg-gradient-to-r from-purple-glow to-neon-purple text-white font-semibold py-3 px-6 rounded-lg hover:from-purple-dark hover:to-neon-purple transition-all duration-300 transform hover:scale-105">
                    <i class="fas fa-edit mr-2"></i>
                    Profili Düzenle
                </button>
                
                <button class="w-full bg-gradient-to-r from-blue-500 to-cyan-500 text-white font-semibold py-3 px-6 rounded-lg hover:from-blue-600 hover:to-cyan-600 transition-all duration-300 transform hover:scale-105">
                    <i class="fas fa-key mr-2"></i>
                    Şifre Değiştir
                </button>
                
                <button class="w-full bg-gradient-to-r from-green-500 to-emerald-500 text-white font-semibold py-3 px-6 rounded-lg hover:from-green-600 hover:to-emerald-600 transition-all duration-300 transform hover:scale-105">
                    <i class="fas fa-download mr-2"></i>
                    Verileri İndir
                </button>
            </div>
        </div>
    </div>

    <!-- Security Information -->
    <div class="glass-effect rounded-2xl p-8">
        <h2 class="text-2xl font-bold mb-6 text-white">Güvenlik Bilgileri</h2>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="bg-gray-800/50 rounded-lg p-4">
                <h4 class="text-sm font-medium text-gray-400 mb-2">Son Giriş</h4>
                <p class="text-white">{{ $admin->last_login_at ? $admin->last_login_at->format('d.m.Y H:i') : 'Bilinmiyor' }}</p>
            </div>
            <div class="bg-gray-800/50 rounded-lg p-4">
                <h4 class="text-sm font-medium text-gray-400 mb-2">E-posta Doğrulama</h4>
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $admin->email_verified_at ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                    <i class="fas {{ $admin->email_verified_at ? 'fa-check-circle' : 'fa-times-circle' }} mr-1"></i>
                    {{ $admin->email_verified_at ? 'Doğrulanmış' : 'Doğrulanmamış' }}
                </span>
            </div>
            <div class="bg-gray-800/50 rounded-lg p-4">
                <h4 class="text-sm font-medium text-gray-400 mb-2">Hesap Durumu</h4>
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                    <i class="fas fa-check-circle mr-1"></i>
                    Aktif
                </span>
            </div>
        </div>
    </div>
</div>
@endsection
