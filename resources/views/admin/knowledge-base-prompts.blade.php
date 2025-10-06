@extends('layouts.admin')

@section('title', 'Promptlar Yönetimi - Admin Panel')

@section('content')
<div class="space-y-6">
    <!-- Page Header -->
    <div class="glass-effect rounded-2xl p-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-white mb-2">Promptlar Yönetimi</h1>
                <p class="text-gray-400">AI promptlarını yönetin ve düzenleyin</p>
            </div>
            <div class="text-6xl opacity-20">
                💬
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="glass-effect rounded-2xl p-8">
        <div class="flex items-center justify-between mb-6">
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 bg-purple-500/20 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-purple-glow" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                    </svg>
                </div>
                <h2 class="text-xl font-semibold text-white">Prompt Kategorileri</h2>
            </div>
            <button class="px-4 py-2 bg-purple-glow/20 text-purple-glow rounded-lg hover:bg-purple-glow/30 transition-colors">
                Yeni Prompt Ekle
            </button>
        </div>
        
        <!-- Empty State -->
        <div class="min-h-[400px] flex items-center justify-center">
            <div class="text-center text-gray-500">
                <svg class="w-24 h-24 mx-auto mb-6 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                </svg>
                <h3 class="text-2xl font-semibold text-gray-400 mb-2">Henüz Prompt Eklenmemiş</h3>
                <p class="text-gray-500 mb-6">AI sisteminiz için özel promptlar oluşturun ve yönetin</p>
                <button class="px-6 py-3 bg-purple-glow/20 text-purple-glow rounded-lg hover:bg-purple-glow/30 transition-colors">
                    İlk Promptunuzu Oluşturun
                </button>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- FAQ Prompts -->
        <div class="glass-effect rounded-2xl p-6">
            <div class="flex items-center space-x-3 mb-4">
                <div class="w-8 h-8 bg-blue-500/20 rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <h3 class="text-lg font-semibold text-white">FAQ Prompts</h3>
            </div>
            <p class="text-gray-400 mb-4">Sık sorulan sorular için özel promptlar</p>
            <div class="text-2xl font-bold text-blue-400 mb-2">0</div>
            <div class="text-sm text-gray-500">Aktif Prompt</div>
        </div>

        <!-- Product Prompts -->
        <div class="glass-effect rounded-2xl p-6">
            <div class="flex items-center space-x-3 mb-4">
                <div class="w-8 h-8 bg-green-500/20 rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                    </svg>
                </div>
                <h3 class="text-lg font-semibold text-white">Product Prompts</h3>
            </div>
            <p class="text-gray-400 mb-4">Ürün bilgileri için özel promptlar</p>
            <div class="text-2xl font-bold text-green-400 mb-2">0</div>
            <div class="text-sm text-gray-500">Aktif Prompt</div>
        </div>

        <!-- General Prompts -->
        <div class="glass-effect rounded-2xl p-6">
            <div class="flex items-center space-x-3 mb-4">
                <div class="w-8 h-8 bg-purple-500/20 rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5 text-purple-glow" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path>
                    </svg>
                </div>
                <h3 class="text-lg font-semibold text-white">General Prompts</h3>
            </div>
            <p class="text-gray-400 mb-4">Genel amaçlı AI promptlar</p>
            <div class="text-2xl font-bold text-purple-glow mb-2">0</div>
            <div class="text-sm text-gray-500">Aktif Prompt</div>
        </div>
    </div>
</div>
@endsection
