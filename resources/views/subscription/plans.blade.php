@extends('layouts.dashboard')

@section('title', 'Plan Seçimi')

@section('content')
<div class="space-y-6">
    <!-- Plans Header -->
    <div class="glass-effect rounded-2xl p-8 relative overflow-hidden">
        <div class="absolute top-0 right-0 w-32 h-32 bg-purple-glow rounded-full mix-blend-multiply filter blur-xl opacity-20"></div>
        <div class="absolute bottom-0 left-0 w-40 h-40 bg-neon-purple rounded-full mix-blend-multiply filter blur-xl opacity-20"></div>
        
        <div class="relative z-10">
            <h1 class="text-4xl font-bold mb-4">
                <span class="gradient-text">Plan Seçin</span>
            </h1>
            <p class="text-xl text-gray-300 mb-6">
                İhtiyacınıza uygun planı seçin ve AI chatbot'unuzu kullanmaya başlayın
            </p>
        </div>
    </div>

    <!-- Plans Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
        @foreach($plans as $plan)
        <div class="glass-effect rounded-xl p-6 border border-gray-700 hover:border-purple-glow/50 transition-all duration-300 hover:shadow-lg hover:shadow-purple-glow/10 relative flex flex-col {{ $currentSubscription && $currentSubscription->plan_id == $plan->id ? 'ring-2 ring-purple-glow' : '' }}">
            @if($currentSubscription && $currentSubscription->plan_id == $plan->id)
            <div class="absolute -top-3 left-1/2 transform -translate-x-1/2">
                <span class="inline-flex items-center px-3 py-1 bg-green-900/50 text-green-300 text-xs font-medium rounded-full border border-green-600/50">
                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    Mevcut Plan
                </span>
            </div>
            @endif

            @if($plan->name === 'Professional')
            <div class="absolute -top-3 left-1/2 transform -translate-x-1/2">
                <span class="inline-flex items-center px-3 py-1 bg-purple-900/50 text-purple-300 text-xs font-medium rounded-full border border-purple-600/50">
                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"></path>
                    </svg>
                    Popüler
                </span>
            </div>
            @endif

            <div class="text-center">
                <h3 class="text-xl font-bold text-white mb-2">{{ $plan->name }}</h3>
                
                <div class="mb-6">
                    <span class="text-4xl font-bold text-white">{{ number_format($plan->price, 2) }} ₺</span>
                    <span class="text-gray-300">/{{ $plan->billing_cycle_text }}</span>
                </div>

                @if($plan->trial_days)
                <div class="mb-6">
                    <span class="inline-flex items-center px-3 py-1 bg-green-900/20 text-green-300 text-sm font-medium rounded-full border border-green-600/50">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v13m0-13V6a2 2 0 112 2h-2zm0 0V5.5A2.5 2.5 0 109.5 8H12zm-7 4h14M5 12a2 2 0 110-4h14a2 2 0 110 4M5 12v7a2 2 0 002 2h10a2 2 0 002-2v-7"></path>
                        </svg>
                        {{ $plan->trial_days }} gün ücretsiz deneme
                    </span>
                </div>
                @endif

                <ul class="space-y-3 mb-8 text-left flex-grow">
                    <li class="flex items-center">
                        <svg class="w-4 h-4 text-green-400 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        <span class="text-gray-300 text-sm">
                            {{ $plan->getFeature('usage_tokens') == -1 ? 'Sınırsız' : number_format($plan->getFeature('usage_tokens') ?? 0) }} Token
                        </span>
                    </li>
                    <li class="flex items-center">
                        <svg class="w-4 h-4 text-green-400 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        <span class="text-gray-300 text-sm">
                            {{ $plan->getFeature('max_products') == -1 ? 'Sınırsız' : number_format($plan->getFeature('max_products') ?? 0) }} Ürün
                        </span>
                    </li>
                    <li class="flex items-center">
                        <svg class="w-4 h-4 text-green-400 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        <span class="text-gray-300 text-sm">
                            {{ $plan->getFeature('max_messages') == -1 ? 'Sınırsız' : number_format($plan->getFeature('max_messages') ?? 0) }} Mesaj
                        </span>
                    </li>
                    <li class="flex items-center">
                        <svg class="w-4 h-4 text-green-400 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        <span class="text-gray-300 text-sm">
                            {{ $plan->getFeature('max_sessions') == -1 ? 'Sınırsız' : number_format($plan->getFeature('max_sessions') ?? 0) }} Session
                        </span>
                    </li>
                    <li class="flex items-center">
                        <svg class="w-4 h-4 text-green-400 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        <span class="text-gray-300 text-sm">
                            {{ $plan->getFeature('widget_campaigns') == -1 ? 'Sınırsız' : number_format($plan->getFeature('widget_campaigns') ?? 0) }} Widget Kampanya
                        </span>
                    </li>
                    <li class="flex items-center">
                        <svg class="w-4 h-4 text-green-400 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        <span class="text-gray-300 text-sm">
                            {{ $plan->getFeature('widget_faq') == -1 ? 'Sınırsız' : number_format($plan->getFeature('widget_faq') ?? 0) }} FAQ
                        </span>
                    </li>
                    <li class="flex items-center">
                        <svg class="w-4 h-4 text-green-400 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        <span class="text-gray-300 text-sm">
                            {{ $plan->getFeature('support') }}
                        </span>
                    </li>
                </ul>

                <div class="mt-auto">
                    @php
                        $hasPendingRequest = $user->planRequests()
                            ->where('plan_id', $plan->id)
                            ->where('status', 'pending')
                            ->exists();
                    @endphp
                    
                    @if($currentSubscription && $currentSubscription->plan_id == $plan->id)
                    <button disabled class="w-full py-3 px-6 bg-gray-600 text-gray-400 font-medium rounded-lg cursor-not-allowed">
                        <svg class="w-4 h-4 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        Mevcut Plan
                    </button>
                    @elseif($hasPendingRequest)
                    <button disabled class="w-full py-3 px-6 bg-yellow-600 text-yellow-200 font-medium rounded-lg cursor-not-allowed">
                        <svg class="w-4 h-4 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Bekleyen Talep
                    </button>
                    @else
                    <form action="{{ route('subscription.subscribe') }}" method="POST" class="w-full">
                        @csrf
                        <input type="hidden" name="plan_id" value="{{ $plan->id }}">
                        <a href="{{ route('payment.billing-form', $plan->id) }}" 
                         class="btn btn-primary">Satın Al</a>
                    </form>
                    @endif
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <!-- FAQ Section -->
    <div class="glass-effect rounded-xl p-8 border border-gray-700">
        <h2 class="text-2xl font-bold text-white mb-8 text-center">Sıkça Sorulan Sorular</h2>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="bg-gray-800/50 rounded-lg p-6 border border-gray-700">
                <h3 class="text-lg font-semibold text-white mb-3 flex items-center">
                    <svg class="w-5 h-5 text-purple-glow mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                    </svg>
                    Token nedir?
                </h3>
                <p class="text-gray-300">
                    Token, AI chatbot'unuzla yapılan her konuşma için kullanılan birimdir. 
                    Her mesaj gönderdiğinizde 1 token tüketilir.
                </p>
            </div>
            
            <div class="bg-gray-800/50 rounded-lg p-6 border border-gray-700">
                <h3 class="text-lg font-semibold text-white mb-3 flex items-center">
                    <svg class="w-5 h-5 text-purple-glow mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path>
                    </svg>
                    Plan değiştirebilir miyim?
                </h3>
                <p class="text-gray-300">
                    Evet, istediğiniz zaman planınızı yükseltebilir veya düşürebilirsiniz. 
                    Değişiklikler anında geçerli olur.
                </p>
            </div>
            
            <div class="bg-gray-800/50 rounded-lg p-6 border border-gray-700">
                <h3 class="text-lg font-semibold text-white mb-3 flex items-center">
                    <svg class="w-5 h-5 text-purple-glow mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                    İptal edebilir miyim?
                </h3>
                <p class="text-gray-300">
                    Evet, aboneliğinizi istediğiniz zaman iptal edebilirsiniz. 
                    İptal sonrası mevcut dönem sonuna kadar kullanmaya devam edebilirsiniz.
                </p>
            </div>
            
            <div class="bg-gray-800/50 rounded-lg p-6 border border-gray-700">
                <h3 class="text-lg font-semibold text-white mb-3 flex items-center">
                    <svg class="w-5 h-5 text-purple-glow mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                    </svg>
                    Güvenli ödeme
                </h3>
                <p class="text-gray-300">
                    Plan atamaları yöneticiler tarafından yönetilmektedir.
                </p>
            </div>
        </div>
    </div>

    <!-- Back to Dashboard -->
    <div class="text-center">
        <a href="{{ route('dashboard') }}" 
           class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-gray-600 to-gray-700 hover:from-gray-700 hover:to-gray-800 text-white font-medium rounded-lg transition-all duration-300 transform hover:scale-105">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            Dashboard'a Dön
        </a>
    </div>
</div>
@endsection
