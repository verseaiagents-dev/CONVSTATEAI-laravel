@extends('layouts.app')

@section('title', 'Ödeme Başarılı - ConvStateAI')

@section('content')
<div class="min-h-screen bg-gray-900 py-8">
    <div class="max-w-2xl mx-auto px-4">
        <div class="glass-effect rounded-2xl p-8 text-center">
            <div class="mb-8">
                <div class="inline-flex items-center justify-center w-20 h-20 bg-green-500 rounded-full mb-6">
                    <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                </div>
                <h1 class="text-4xl font-bold text-white mb-4">
                    <span class="gradient-text">Ödeme Başarılı!</span> ✅
                </h1>
                <p class="text-xl text-gray-300 mb-6">
                    Aboneliğiniz başarıyla aktif edildi.
                </p>
            </div>

            <div class="bg-gray-800/50 rounded-lg p-6 mb-8">
                <h3 class="text-lg font-semibold text-white mb-4">Abonelik Detayları</h3>
                <div class="space-y-3 text-gray-300">
                    <div class="flex justify-between">
                        <span>Durum:</span>
                        <span class="text-green-400 font-semibold">Aktif</span>
                    </div>
                    <div class="flex justify-between">
                        <span>Başlangıç:</span>
                        <span>{{ now()->format('d.m.Y') }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span>Token Durumu:</span>
                        <span class="text-green-400 font-semibold">Yenilendi</span>
                    </div>
                </div>
            </div>

            <div class="space-y-4">
                <a href="{{ route('dashboard.subscription.index') }}" 
                   class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-purple-glow to-neon-purple text-white font-semibold rounded-lg hover:from-purple-dark hover:to-neon-purple transition-all duration-300 transform hover:scale-105">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    Abonelik Yönetimi
                </a>
                
                <a href="{{ route('dashboard') }}" 
                   class="inline-flex items-center px-6 py-3 bg-gray-700 text-white font-semibold rounded-lg hover:bg-gray-600 transition-all duration-300">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                    </svg>
                    Ana Sayfa
                </a>
            </div>

            <div class="mt-8 text-sm text-gray-400">
                <p>Ödeme işleminiz başarıyla tamamlandı. Abonelik bilgileriniz e-posta adresinize gönderilmiştir.</p>
            </div>
        </div>
    </div>
</div>
@endsection
