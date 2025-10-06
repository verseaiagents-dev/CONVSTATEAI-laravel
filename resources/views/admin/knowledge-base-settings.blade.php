@extends('layouts.admin')

@section('title', 'Bilgi Tabanı Ayarları - Admin Panel')

@section('content')
<div class="space-y-6">
    <!-- Page Header -->
    <div class="glass-effect rounded-2xl p-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-white mb-2">Bilgi Tabanı Ayarları</h1>
                <p class="text-gray-400">Knowledge base sisteminin genel ayarlarını yönetin</p>
            </div>
            <div class="text-6xl opacity-20">
                📚
            </div>
        </div>
    </div>

    <!-- Main Content Grid -->
    <div class="grid grid-cols-1 gap-6">
        <!-- Row 1: Promptlar -->
        <div class="glass-effect rounded-2xl p-8">
            <div class="flex items-center justify-between mb-6">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 bg-purple-500/20 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-purple-glow" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                        </svg>
                    </div>
                    <h2 class="text-xl font-semibold text-white">Promptlar</h2>
                </div>
                <a href="{{ route('admin.knowledge-base-prompts') }}" class="px-4 py-2 bg-purple-glow/20 text-purple-glow rounded-lg hover:bg-purple-glow/30 transition-colors">
                    Yönet
                </a>
            </div>
            
            <!-- Boş alan - içinde hiçbir şey yok -->
            <div class="min-h-[200px] flex items-center justify-center">
                <div class="text-center text-gray-500">
                    <svg class="w-16 h-16 mx-auto mb-4 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                    </svg>
                    <p class="text-lg">Promptlar bölümü</p>
                    <p class="text-sm">Bu alan şu anda boş</p>
                </div>
            </div>
        </div>

        <!-- Row 2: Sistem Durumu -->
        <div class="glass-effect rounded-2xl p-8">
            <div class="flex items-center space-x-3 mb-6">
                <div class="w-10 h-10 bg-green-500/20 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
                </div>
                <h2 class="text-xl font-semibold text-white">Sistem Durumu</h2>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="p-6 bg-gray-800/30 rounded-lg text-center">
                    <div class="text-3xl font-bold text-purple-glow mb-2">JSON</div>
                    <div class="text-gray-400">Desteklenen Format</div>
                </div>
                
                <div class="p-6 bg-gray-800/30 rounded-lg text-center">
                    <div class="text-3xl font-bold text-blue-400 mb-2">10MB</div>
                    <div class="text-gray-400">Maksimum Dosya Boyutu</div>
                </div>
                
                <div class="p-6 bg-gray-800/30 rounded-lg text-center">
                    <div class="text-3xl font-bold text-green-400 mb-2">Aktif</div>
                    <div class="text-gray-400">AI Analiz Sistemi</div>
                </div>
            </div>
        </div>

        <!-- Row 3: Gelecek Özellikler -->
        <div class="glass-effect rounded-2xl p-8">
            <div class="flex items-center space-x-3 mb-6">
                <div class="w-10 h-10 bg-yellow-500/20 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <h2 class="text-xl font-semibold text-white">Gelecek Özellikler</h2>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="p-4 bg-gray-800/30 rounded-lg">
                    <h3 class="text-lg font-medium text-white mb-2">Field Mapping Sistemi</h3>
                    <p class="text-gray-400">CSV, TXT, XML dosyaları için gelişmiş field mapping</p>
                </div>
                
                <div class="p-4 bg-gray-800/30 rounded-lg">
                    <h3 class="text-lg font-medium text-white mb-2">Çoklu Format Desteği</h3>
                    <p class="text-gray-400">Excel, PDF ve diğer formatlar için destek</p>
                </div>
                
                <div class="p-4 bg-gray-800/30 rounded-lg">
                    <h3 class="text-lg font-medium text-white mb-2">Gelişmiş AI Analiz</h3>
                    <p class="text-gray-400">Daha akıllı content type detection</p>
                </div>
                
                <div class="p-4 bg-gray-800/30 rounded-lg">
                    <h3 class="text-lg font-medium text-white mb-2">Batch Processing</h3>
                    <p class="text-gray-400">Toplu dosya işleme özelliği</p>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection
