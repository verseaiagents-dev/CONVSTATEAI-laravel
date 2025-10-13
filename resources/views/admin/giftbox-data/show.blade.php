@extends('layouts.admin')

@section('title', 'Lead Detayı - ConvStateAI')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="glass-effect rounded-2xl p-8 relative overflow-hidden">
        <div class="absolute top-0 right-0 w-32 h-32 bg-pink-500 rounded-full mix-blend-multiply filter blur-xl opacity-20"></div>
        <div class="absolute bottom-0 left-0 w-40 h-40 bg-rose-500 rounded-full mix-blend-multiply filter blur-xl opacity-20"></div>
        
        <div class="relative z-10">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-4xl font-bold mb-4">
                        🎁 <span class="gradient-text">Lead Detayı</span>
                    </h1>
                    <p class="text-xl text-gray-300 mb-6">
                        {{ $giftboxUser->name }} {{ $giftboxUser->surname }} - {{ ucfirst($giftboxUser->sector) }} Sektörü
                    </p>
                </div>
                
                <div class="flex space-x-4">
                    <a href="{{ route('admin.giftbox-data.index') }}" class="px-6 py-3 bg-gradient-to-r from-gray-500 to-gray-600 rounded-lg text-white font-semibold hover:from-gray-600 hover:to-gray-700 transition-all duration-300 transform hover:scale-105">
                        ← Geri Dön
                    </a>
                    <form method="POST" action="{{ route('admin.giftbox-data.destroy', $giftboxUser) }}" class="inline" onsubmit="return confirm('Bu lead\'i silmek istediğinizden emin misiniz?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="px-6 py-3 bg-gradient-to-r from-red-500 to-red-600 rounded-lg text-white font-semibold hover:from-red-600 hover:to-red-700 transition-all duration-300 transform hover:scale-105">
                            🗑️ Sil
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Lead Information -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Personal Information -->
        <div class="glass-effect rounded-xl p-6">
            <h3 class="text-xl font-semibold text-white mb-6 flex items-center">
                <svg class="w-6 h-6 text-pink-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                </svg>
                Kişisel Bilgiler
            </h3>
            
            <div class="space-y-4">
                <div class="flex justify-between items-center py-3 border-b border-gray-700">
                    <span class="text-gray-400 font-medium">Ad:</span>
                    <span class="text-white font-semibold">{{ $giftboxUser->name }}</span>
                </div>
                
                <div class="flex justify-between items-center py-3 border-b border-gray-700">
                    <span class="text-gray-400 font-medium">Soyad:</span>
                    <span class="text-white font-semibold">{{ $giftboxUser->surname }}</span>
                </div>
                
                <div class="flex justify-between items-center py-3 border-b border-gray-700">
                    <span class="text-gray-400 font-medium">Email:</span>
                    <a href="mailto:{{ $giftboxUser->mail }}" class="text-pink-400 hover:text-pink-300 transition-colors font-semibold">
                        {{ $giftboxUser->mail }}
                    </a>
                </div>
                
                <div class="flex justify-between items-center py-3 border-b border-gray-700">
                    <span class="text-gray-400 font-medium">Telefon:</span>
                    <span class="text-white font-semibold">
                        @if($giftboxUser->phone)
                            <a href="tel:{{ $giftboxUser->phone }}" class="text-pink-400 hover:text-pink-300 transition-colors">
                                {{ $giftboxUser->phone }}
                            </a>
                        @else
                            <span class="text-gray-500">Belirtilmemiş</span>
                        @endif
                    </span>
                </div>
                
                <div class="flex justify-between items-center py-3">
                    <span class="text-gray-400 font-medium">Ziyaretçi Sayısı:</span>
                    <span class="text-white font-semibold">
                        {{ $giftboxUser->visitors ?? 'Belirtilmemiş' }}
                    </span>
                </div>
            </div>
        </div>

        <!-- Business Information -->
        <div class="glass-effect rounded-xl p-6">
            <h3 class="text-xl font-semibold text-white mb-6 flex items-center">
                <svg class="w-6 h-6 text-blue-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                </svg>
                İş Bilgileri
            </h3>
            
            <div class="space-y-4">
                <div class="flex justify-between items-center py-3 border-b border-gray-700">
                    <span class="text-gray-400 font-medium">Sektör:</span>
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
                        {{ ucfirst($giftboxUser->sector) }}
                    </span>
                </div>
                
                <div class="flex justify-between items-center py-3 border-b border-gray-700">
                    <span class="text-gray-400 font-medium">Kayıt Tarihi:</span>
                    <span class="text-white font-semibold">{{ $giftboxUser->created_at->format('d.m.Y H:i') }}</span>
                </div>
                
                <div class="flex justify-between items-center py-3 border-b border-gray-700">
                    <span class="text-gray-400 font-medium">Son Güncelleme:</span>
                    <span class="text-white font-semibold">{{ $giftboxUser->updated_at->format('d.m.Y H:i') }}</span>
                </div>
                
                <div class="flex justify-between items-center py-3">
                    <span class="text-gray-400 font-medium">Lead ID:</span>
                    <span class="text-white font-semibold font-mono">{{ $giftboxUser->id }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Actions -->
    <div class="glass-effect rounded-xl p-6">
        <h3 class="text-xl font-semibold text-white mb-6 flex items-center">
            <svg class="w-6 h-6 text-green-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
            </svg>
            Hızlı İşlemler
        </h3>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <a href="mailto:{{ $giftboxUser->mail }}" class="p-4 bg-gray-800/30 rounded-lg hover:bg-gray-800/50 transition-all duration-200 group">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 bg-pink-500/20 rounded-lg flex items-center justify-center group-hover:bg-pink-500/30 transition-all duration-200">
                        <svg class="w-5 h-5 text-pink-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                    <div>
                        <p class="text-white font-medium">Email Gönder</p>
                        <p class="text-gray-400 text-sm">{{ $giftboxUser->mail }}</p>
                    </div>
                </div>
            </a>

            @if($giftboxUser->phone)
            <a href="tel:{{ $giftboxUser->phone }}" class="p-4 bg-gray-800/30 rounded-lg hover:bg-gray-800/50 transition-all duration-200 group">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 bg-green-500/20 rounded-lg flex items-center justify-center group-hover:bg-green-500/30 transition-all duration-200">
                        <svg class="w-5 h-5 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                        </svg>
                    </div>
                    <div>
                        <p class="text-white font-medium">Telefon Et</p>
                        <p class="text-gray-400 text-sm">{{ $giftboxUser->phone }}</p>
                    </div>
                </div>
            </a>
            @endif

            <a href="{{ route('admin.giftbox-data.index') }}" class="p-4 bg-gray-800/30 rounded-lg hover:bg-gray-800/50 transition-all duration-200 group">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 bg-blue-500/20 rounded-lg flex items-center justify-center group-hover:bg-blue-500/30 transition-all duration-200">
                        <svg class="w-5 h-5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>
                        </svg>
                    </div>
                    <div>
                        <p class="text-white font-medium">Tüm Leadleri Gör</p>
                        <p class="text-gray-400 text-sm">Listeye geri dön</p>
                    </div>
                </div>
            </a>
        </div>
    </div>
</div>
@endsection
