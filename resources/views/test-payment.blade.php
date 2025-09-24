@extends('layouts.app')

@section('title', 'Test Payment - ConvStateAI')

@section('content')
<div class="min-h-screen bg-gray-900 py-8">
    <div class="max-w-4xl mx-auto px-4">
        <div class="glass-effect rounded-2xl p-8">
            <h1 class="text-3xl font-bold text-white mb-6">Payment Test</h1>
            
            <div class="bg-gray-800/50 rounded-lg p-6 mb-6">
                <h3 class="text-lg font-semibold text-white mb-4">Plan Bilgileri</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-gray-300">
                    <div>
                        <span class="text-gray-400">Plan ID:</span>
                        <span class="ml-2">{{ $plan->id }}</span>
                    </div>
                    <div>
                        <span class="text-gray-400">Plan Adı:</span>
                        <span class="ml-2">{{ $plan->name }}</span>
                    </div>
                    <div>
                        <span class="text-gray-400">Fiyat:</span>
                        <span class="ml-2 font-semibold text-green-400">{{ number_format($plan->price, 2) }} ₺</span>
                    </div>
                    <div>
                        <span class="text-gray-400">Billing Cycle:</span>
                        <span class="ml-2">{{ $plan->billing_cycle_text }}</span>
                    </div>
                </div>
            </div>

            <div class="bg-gray-800/50 rounded-lg p-6 mb-6">
                <h3 class="text-lg font-semibold text-white mb-4">PayTR Config Kontrolü</h3>
                <div class="space-y-2 text-gray-300">
                    <div>
                        <span class="text-gray-400">Merchant ID:</span>
                        <span class="ml-2 font-mono">{{ config('services.paytr.merchant_id') ?? 'NOT SET' }}</span>
                    </div>
                    <div>
                        <span class="text-gray-400">Merchant Key:</span>
                        <span class="ml-2 font-mono">{{ config('services.paytr.merchant_key') ? 'SET' : 'NOT SET' }}</span>
                    </div>
                    <div>
                        <span class="text-gray-400">Merchant Salt:</span>
                        <span class="ml-2 font-mono">{{ config('services.paytr.merchant_salt') ? 'SET' : 'NOT SET' }}</span>
                    </div>
                    <div>
                        <span class="text-gray-400">Test Mode:</span>
                        <span class="ml-2">{{ config('services.paytr.test_mode') ? 'ON' : 'OFF' }}</span>
                    </div>
                </div>
            </div>

            <div class="space-y-4">
                <a href="{{ route('payment.checkout', $plan->id) }}" 
                   class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-purple-glow to-neon-purple text-white font-semibold rounded-lg hover:from-purple-dark hover:to-neon-purple transition-all duration-300 transform hover:scale-105">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                    </svg>
                    Ödeme Yap (Checkout)
                </a>
                
                <a href="{{ route('subscription.plans') }}" 
                   class="inline-flex items-center px-6 py-3 bg-gray-700 text-white font-semibold rounded-lg hover:bg-gray-600 transition-all duration-300">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Geri Dön
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
