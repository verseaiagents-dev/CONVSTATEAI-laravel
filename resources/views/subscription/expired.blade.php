@extends('layouts.dashboard')

@section('title', 'Plan Süresi Dolmuş')

@section('content')
<div class="space-y-6">
    <!-- Header Section -->
    <div class="glass-effect rounded-2xl p-8 relative overflow-hidden">
        <div class="absolute top-0 right-0 w-32 h-32 bg-purple-glow rounded-full mix-blend-multiply filter blur-xl opacity-20"></div>
        <div class="absolute bottom-0 left-0 w-40 h-40 bg-neon-purple rounded-full mix-blend-multiply filter blur-xl opacity-20"></div>
        
        <div class="relative z-10 text-center">
            <div class="mx-auto flex items-center justify-center h-20 w-20 rounded-full bg-red-500/20 mb-6">
                <svg class="h-12 w-12 text-red-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L3.732 16.5c-.77.833.192 2.5 1.732 2.5z" />
                </svg>
            </div>
            
            <h1 class="text-4xl font-bold mb-4 text-white">
                Plan Süreniz <span class="gradient-text">Dolmuş</span>! ⚠️
            </h1>
            <p class="text-xl text-gray-300 mb-6">
                Plan süreniz ve limitiniz dolmuştur. Devam etmek için planınızı yenileyin.
            </p>
        </div>
    </div>

    <!-- Subscription Details -->
    @if(isset($subscription) && $subscription)
    <div class="glass-effect rounded-xl p-6 border border-red-500/30">
        <div class="flex items-center space-x-3 mb-4">
            <svg class="w-6 h-6 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
            </svg>
            <h3 class="text-lg font-semibold text-red-400">Plan Bilgileri</h3>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="p-4 bg-gray-800/30 rounded-lg border border-gray-700">
                <p class="text-sm text-gray-400 mb-1">Plan Adı</p>
                <p class="text-white font-medium">{{ $subscription->plan->name ?? 'Bilinmiyor' }}</p>
            </div>
            
            <div class="p-4 bg-gray-800/30 rounded-lg border border-gray-700">
                <p class="text-sm text-gray-400 mb-1">Bitiş Tarihi</p>
                <p class="text-white font-medium">{{ $subscription->end_date ? $subscription->end_date->format('d.m.Y H:i') : 'Bilinmiyor' }}</p>
            </div>
            
            <div class="p-4 bg-gray-800/30 rounded-lg border border-gray-700">
                <p class="text-sm text-gray-400 mb-1">Durum</p>
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-500/20 text-red-400 border border-red-500/30">
                    Süresi Dolmuş
                </span>
            </div>
        </div>
    </div>
    @endif

    <!-- Action Buttons -->
    <div class="glass-effect rounded-xl p-6">
        <div class="text-center">
            <h3 class="text-xl font-semibold text-white mb-6">Ne Yapmak İstiyorsunuz?</h3>
            
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <a href="{{ route('dashboard.subscription.index') }}" 
                   class="inline-flex items-center justify-center px-8 py-4 bg-purple-glow hover:bg-purple-dark text-white font-medium rounded-lg transition-all duration-200 transform hover:scale-105 hover:shadow-lg hover:shadow-purple-500/25">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                    </svg>
                    Plan Yenile
                </a>
                
                <a href="{{ route('dashboard') }}" 
                   class="inline-flex items-center justify-center px-8 py-4 bg-gray-700 hover:bg-gray-600 text-white font-medium rounded-lg transition-all duration-200 transform hover:scale-105 border border-gray-600">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Dashboard'a Dön
                </a>
            </div>
        </div>
    </div>

    <!-- Help Section -->
    <div class="glass-effect rounded-xl p-6 border border-blue-500/30">
        <div class="text-center">
            <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-blue-500/20 mb-4">
                <svg class="h-6 w-6 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
            
            <h3 class="text-lg font-semibold text-blue-400 mb-2">Yardıma mı ihtiyacınız var?</h3>
            <p class="text-gray-300 mb-4">
                Plan yenileme konusunda sorularınız varsa destek ekibimiz size yardımcı olmaktan mutluluk duyacaktır.
            </p>
            
            <a href="mailto:support@example.com" 
               class="inline-flex items-center px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-colors duration-200 border border-blue-500/30">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                </svg>
                Destek Ekibiyle İletişime Geçin
            </a>
        </div>
    </div>
</div>
@endsection
