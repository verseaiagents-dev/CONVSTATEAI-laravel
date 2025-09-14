@extends('layouts.plans')

@section('title', 'Plan Seçimi')

@section('content')
<div class="min-h-screen bg-gray-50 dark:bg-gray-900 py-12 pb-20 md:pb-12">
    <div class="container mx-auto px-4">
        <div class="text-center mb-12">
            <h1 class="text-4xl font-bold text-gray-900 dark:text-white mb-4">Plan Seçin</h1>
            <p class="text-xl text-gray-600 dark:text-gray-400">İhtiyacınıza uygun planı seçin ve AI chatbot'unuzu kullanmaya başlayın</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8 max-w-7xl mx-auto">
            @foreach($plans as $plan)
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg p-8 relative flex flex-col {{ $currentSubscription && $currentSubscription->plan_id == $plan->id ? 'ring-2 ring-blue-500' : '' }}">
                @if($currentSubscription && $currentSubscription->plan_id == $plan->id)
                <div class="absolute -top-4 left-1/2 transform -translate-x-1/2">
                    <span class="inline-flex items-center px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-full">
                        <i class="fas fa-check-circle mr-2"></i>
                        Mevcut Plan
                    </span>
                </div>
                @endif

                @if($plan->name === 'Professional')
                <div class="absolute -top-4 left-1/2 transform -translate-x-1/2">
                    <span class="inline-flex items-center px-4 py-2 bg-green-600 text-white text-sm font-medium rounded-full">
                        <i class="fas fa-star mr-2"></i>
                        Popüler
                    </span>
                </div>
                @endif

                <div class="text-center">
                    <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-2">{{ $plan->name }}</h3>
                    
                    <div class="mb-6">
                        <span class="text-5xl font-bold text-gray-900 dark:text-white">{{ number_format($plan->price, 2) }} ₺</span>
                        <span class="text-gray-600 dark:text-gray-400">/{{ $plan->billing_cycle_text }}</span>
                    </div>

                    @if($plan->trial_days)
                    <div class="mb-6">
                        <span class="inline-flex items-center px-3 py-1 bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200 text-sm font-medium rounded-full">
                            <i class="fas fa-gift mr-2"></i>
                            {{ $plan->trial_days }} gün ücretsiz deneme
                        </span>
                    </div>
                    @endif

                    <ul class="space-y-4 mb-8 text-left flex-grow">
                        <li class="flex items-center">
                            <i class="fas fa-check text-green-500 mr-3"></i>
                            <span class="text-gray-700 dark:text-gray-300">
                                {{ $plan->getFeature('usage_tokens') == -1 ? 'Sınırsız' : number_format($plan->getFeature('usage_tokens')) }} Token
                            </span>
                        </li>
                        <li class="flex items-center">
                            <i class="fas fa-check text-green-500 mr-3"></i>
                            <span class="text-gray-700 dark:text-gray-300">
                                {{ $plan->getFeature('max_products') == -1 ? 'Sınırsız' : number_format($plan->getFeature('max_products')) }} Ürün
                            </span>
                        </li>
                        <li class="flex items-center">
                            <i class="fas fa-check text-green-500 mr-3"></i>
                            <span class="text-gray-700 dark:text-gray-300">
                                {{ $plan->getFeature('max_messages') == -1 ? 'Sınırsız' : number_format($plan->getFeature('max_messages')) }} Mesaj
                            </span>
                        </li>
                        <li class="flex items-center">
                            <i class="fas fa-check text-green-500 mr-3"></i>
                            <span class="text-gray-700 dark:text-gray-300">
                                {{ $plan->getFeature('max_sessions') == -1 ? 'Sınırsız' : number_format($plan->getFeature('max_sessions')) }} Session
                            </span>
                        </li>
                        <li class="flex items-center">
                            <i class="fas fa-check text-green-500 mr-3"></i>
                            <span class="text-gray-700 dark:text-gray-300">
                                {{ $plan->getFeature('widget_campaigns') == -1 ? 'Sınırsız' : number_format($plan->getFeature('widget_campaigns')) }} Widget Kampanya
                            </span>
                        </li>
                        <li class="flex items-center">
                            <i class="fas fa-check text-green-500 mr-3"></i>
                            <span class="text-gray-700 dark:text-gray-300">
                                {{ $plan->getFeature('widget_faq') == -1 ? 'Sınırsız' : number_format($plan->getFeature('widget_faq')) }} FAQ
                            </span>
                        </li>
                        <li class="flex items-center">
                            <i class="fas fa-check text-green-500 mr-3"></i>
                            <span class="text-gray-700 dark:text-gray-300">
                                {{ $plan->getFeature('support') }}
                            </span>
                        </li>
                    </ul>

                    <div class="mt-auto">
                        @if($currentSubscription && $currentSubscription->plan_id == $plan->id)
                        <button disabled class="w-full py-3 px-6 bg-gray-300 text-gray-500 font-medium rounded-lg cursor-not-allowed">
                            <i class="fas fa-check-circle mr-2"></i>
                            Mevcut Plan
                        </button>
                        @else
                        <form action="{{ route('subscription.subscribe') }}" method="POST" class="w-full">
                            @csrf
                            <input type="hidden" name="plan_id" value="{{ $plan->id }}">
                            <button type="submit" class="w-full py-3 px-6 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-colors">
                                @if($plan->price == 0)
                                <i class="fas fa-play mr-2"></i>
                                Ücretsiz Başla
                                @else
                                <i class="fas fa-credit-card mr-2"></i>
                                Abone Ol
                                @endif
                            </button>
                        </form>
                        @endif
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        <!-- Additional Info -->
        <div class="mt-16 text-center">
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-4">Sıkça Sorulan Sorular</h2>
            
            <div class="max-w-4xl mx-auto grid grid-cols-1 md:grid-cols-2 gap-8 text-left">
                <div class="bg-white dark:bg-gray-800 rounded-lg p-6 shadow-md">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Token nedir?</h3>
                    <p class="text-gray-600 dark:text-gray-400">
                        Token, AI chatbot'unuzla yapılan her konuşma için kullanılan birimdir. 
                        Her mesaj gönderdiğinizde 1 token tüketilir.
                    </p>
                </div>
                
                <div class="bg-white dark:bg-gray-800 rounded-lg p-6 shadow-md">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Plan değiştirebilir miyim?</h3>
                    <p class="text-gray-600 dark:text-gray-400">
                        Evet, istediğiniz zaman planınızı yükseltebilir veya düşürebilirsiniz. 
                        Değişiklikler anında geçerli olur.
                    </p>
                </div>
                
                <div class="bg-white dark:bg-gray-800 rounded-lg p-6 shadow-md">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">İptal edebilir miyim?</h3>
                    <p class="text-gray-600 dark:text-gray-400">
                        Evet, aboneliğinizi istediğiniz zaman iptal edebilirsiniz. 
                        İptal sonrası mevcut dönem sonuna kadar kullanmaya devam edebilirsiniz.
                    </p>
                </div>
                
                <div class="bg-white dark:bg-gray-800 rounded-lg p-6 shadow-md">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Güvenli ödeme</h3>
                    <p class="text-gray-600 dark:text-gray-400">
                        Plan atamaları yöneticiler tarafından yönetilmektedir.
                    </p>
                </div>
            </div>
        </div>

        <!-- Back to Dashboard -->
        <div class="mt-12 text-center">
            <a href="{{ route('dashboard') }}" 
               class="inline-flex items-center text-blue-600 hover:text-blue-700 dark:text-blue-400 dark:hover:text-blue-300">
                <i class="fas fa-arrow-left mr-2"></i>
                Dashboard'a Dön
            </a>
        </div>
    </div>
</div>
@endsection
