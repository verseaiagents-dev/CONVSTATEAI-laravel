@extends('layouts.dashboard')

@section('title', 'Abonelik Yönetimi')

@section('content')
<div class="space-y-6">
    <!-- Subscription Dashboard Header -->
    <div class="glass-effect rounded-2xl p-8 relative overflow-hidden">
        <div class="absolute top-0 right-0 w-32 h-32 bg-purple-glow rounded-full mix-blend-multiply filter blur-xl opacity-20"></div>
        <div class="absolute bottom-0 left-0 w-40 h-40 bg-neon-purple rounded-full mix-blend-multiply filter blur-xl opacity-20"></div>
        
        <div class="relative z-10">
            <h1 class="text-4xl font-bold mb-4">
                <span class="gradient-text">Abonelik Yönetimi</span>
            </h1>
            <p class="text-xl text-gray-300 mb-6">
                Planınızı yönetin, kullanım durumunuzu takip edin ve token satın alın
            </p>
            <div class="flex flex-wrap gap-4">
                <a href="{{ route('subscription.plans') }}" class="px-6 py-3 bg-gradient-to-r from-purple-glow to-neon-purple rounded-lg text-white font-semibold hover:from-purple-dark hover:to-neon-purple transition-all duration-300 transform hover:scale-105">
                    <svg class="w-5 h-5 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    Plan Seç
                </a>
                {{-- <a href="{{ route('subscription.billing-history') }}" class="px-6 py-3 bg-gradient-to-r from-blue-500 to-cyan-500 rounded-lg text-white font-semibold hover:from-blue-600 hover:to-cyan-600 transition-all duration-300 transform hover:scale-105">
                    <svg class="w-5 h-5 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                    </svg>
                    Fatura Geçmişi
                </a> --}}
            </div>
        </div>
    </div>

    <!-- Usage Token Status -->
    @if($usageToken)
    <div class="glass-effect rounded-xl p-6 border border-gray-700 hover:border-purple-glow/50 transition-all duration-300 hover:shadow-lg hover:shadow-purple-glow/10">
        <h2 class="text-xl font-semibold text-white mb-4 flex items-center">
            <svg class="w-6 h-6 mr-3 text-purple-glow" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
            </svg>
            Kullanım Durumu
        </h2>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
            <div class="text-center bg-gray-800/50 rounded-lg p-4 border border-gray-700">
                <div class="text-3xl font-bold text-blue-400">{{ $usageToken->tokens_remaining }}</div>
                <div class="text-sm text-gray-300">Kalan Token</div>
            </div>
            <div class="text-center bg-gray-800/50 rounded-lg p-4 border border-gray-700">
                <div class="text-3xl font-bold text-green-400">{{ $usageToken->tokens_used }}</div>
                <div class="text-sm text-gray-300">Kullanılan Token</div>
            </div>
            <div class="text-center bg-gray-800/50 rounded-lg p-4 border border-gray-700">
                <div class="text-3xl font-bold text-purple-glow">{{ $usageToken->tokens_total }}</div>
                <div class="text-sm text-gray-300">Toplam Token</div>
            </div>
        </div>

        <!-- Progress Bar -->
        <div class="mb-4">
            <div class="flex justify-between text-sm text-gray-300 mb-2">
                <span>Kullanım Oranı</span>
                <span>{{ number_format($usageToken->usage_percentage, 1) }}%</span>
            </div>
            <div class="w-full bg-gray-700 rounded-full h-3">
                <div class="bg-gradient-to-r from-blue-500 to-purple-glow h-3 rounded-full transition-all duration-300" 
                     style="width: {{ $usageToken->usage_percentage }}%"></div>
            </div>
        </div>

        <!-- Reset Date -->
        @if($usageToken->reset_date)
        <div class="text-sm text-gray-300 flex items-center">
            <svg class="w-4 h-4 mr-2 text-purple-glow" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
            </svg>
            Token'lar {{ $usageToken->reset_date->format('d.m.Y') }} tarihinde yenilenecek
            ({{ $usageToken->days_until_reset }} gün kaldı)
        </div>
        @endif

        <!-- Warning if low tokens -->
        @if($usageToken->usage_percentage > 80)
        <div class="mt-4 p-4 bg-yellow-900/20 border border-yellow-600/50 rounded-lg">
            <div class="flex">
                <svg class="w-5 h-5 text-yellow-400 mt-1 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                </svg>
                <div>
                    <h3 class="text-sm font-medium text-yellow-200">Token Uyarısı</h3>
                    <p class="text-sm text-yellow-300 mt-1">
                        Token kullanımınız %80'i aştı. Token alınız.
                    </p>
                </div>
            </div>
        </div>
        @endif
    </div>
    @else
    <!-- No Usage Token -->
    <div class="glass-effect rounded-xl p-6 border border-red-600/50 bg-red-900/20">
        <div class="flex">
            <svg class="w-6 h-6 text-red-400 mt-1 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
            </svg>
            <div>
                <h3 class="text-lg font-medium text-red-200">Kullanım Token'ı Bulunamadı</h3>
                <p class="text-red-300 mt-1">
                    AI chatbot'unuzu kullanabilmek için bir plan satın almanız gerekiyor.
                </p>
                <div class="mt-4">
                    <a href="{{ route('subscription.plans') }}" target="_blank"
                       class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-red-600 to-red-500 hover:from-red-700 hover:to-red-600 text-white font-medium rounded-lg transition-all duration-300 transform hover:scale-105">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4m0 0L7 13m0 0l-2.5 5M7 13l2.5 5m6-5v6a2 2 0 01-2 2H9a2 2 0 01-2-2v-6m8 0V9a2 2 0 00-2-2H9a2 2 0 00-2 2v4.01"></path>
                        </svg>
                        Plan Satın Al
                    </a>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Current Subscription -->
    @if($currentSubscription)
    <div class="glass-effect rounded-xl p-6 border border-gray-700 hover:border-purple-glow/50 transition-all duration-300 hover:shadow-lg hover:shadow-purple-glow/10">
        <h2 class="text-xl font-semibold text-white mb-4 flex items-center">
            <svg class="w-6 h-6 mr-3 text-purple-glow" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            Mevcut Plan
        </h2>
        
        <div class="flex items-center justify-between">
            <div>
                <h3 class="text-lg font-medium text-white">{{ $currentSubscription->plan->name }}</h3>
                <p class="text-gray-300">
                    {{ $currentSubscription->plan->billing_cycle_text }} - 
                    ${{ number_format($currentSubscription->plan->price, 2) }}
                </p>
                <p class="text-sm text-gray-400 mt-1">
                    Başlangıç: {{ $currentSubscription->start_date->format('d.m.Y') }} | 
                    Bitiş: {{ $currentSubscription->end_date->format('d.m.Y') }}
                </p>
            </div>
            <div class="text-right">
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-900/50 text-green-300 border border-green-600/50">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    Aktif
                </span>
            </div>
        </div>

        <!-- Cancel Subscription -->
        <div class="mt-6 pt-6 border-t border-gray-700">
            <form action="{{ route('subscription.cancel') }}" method="POST" 
                  onsubmit="return confirm('Aboneliğinizi iptal etmek istediğinizden emin misiniz?')">
                @csrf
                <button type="submit" 
                        class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-red-600 to-red-500 hover:from-red-700 hover:to-red-600 text-white font-medium rounded-lg transition-all duration-300 transform hover:scale-105">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                    Aboneliği İptal Et
                </button>
            </form>
        </div>
    </div>
    @endif

    <!-- Buy More Tokens - TEMPORARILY HIDDEN -->
    {{-- <div class="glass-effect rounded-xl p-6 border border-gray-700 hover:border-purple-glow/50 transition-all duration-300 hover:shadow-lg hover:shadow-purple-glow/10">
        <h2 class="text-xl font-semibold text-white mb-4 flex items-center">
            <svg class="w-6 h-6 mr-3 text-purple-glow" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
            </svg>
            Ek Token Satın Al
        </h2>
        
        <form id="buyTokensForm" class="space-y-4">
            @csrf
            <div>
                <label for="token_amount" class="block text-sm font-medium text-gray-300 mb-2">
                    Token Miktarı
                </label>
                <select name="token_amount" id="token_amount" 
                        class="w-full px-3 py-2 bg-gray-800/50 border border-gray-600 rounded-lg focus:ring-2 focus:ring-purple-glow focus:border-purple-glow text-white">
                    <option value="100">100 Token - ₺1.00</option>
                    <option value="500">500 Token - ₺5.00</option>
                    <option value="1000">1,000 Token - ₺10.00</option>
                    <option value="5000">5,000 Token - ₺50.00</option>
                    <option value="10000">10,000 Token - ₺100.00</option>
                </select>
            </div>
            
            <button type="submit" 
                    class="w-full inline-flex justify-center items-center px-4 py-2 bg-gradient-to-r from-purple-glow to-neon-purple hover:from-purple-dark hover:to-neon-purple text-white font-medium rounded-lg transition-all duration-300 transform hover:scale-105">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4m0 0L7 13m0 0l-2.5 5M7 13l2.5 5m6-5v6a2 2 0 01-2 2H9a2 2 0 01-2-2v-6m8 0V9a2 2 0 00-2-2H9a2 2 0 00-2 2v4.01"></path>
                </svg>
                Token Satın Al
            </button>
        </form>
    </div> --}}

    <!-- Available Plans - TEMPORARILY HIDDEN -->
    {{-- <div class="glass-effect rounded-xl p-6 border border-gray-700 hover:border-purple-glow/50 transition-all duration-300 hover:shadow-lg hover:shadow-purple-glow/10">
        <h2 class="text-xl font-semibold text-white mb-4 flex items-center">
            <svg class="w-6 h-6 mr-3 text-purple-glow" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
            </svg>
            Mevcut Planlar
        </h2>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            @foreach($plans as $plan)
            <div class="bg-gray-800/50 border border-gray-700 rounded-lg p-6 hover:border-purple-glow/50 transition-all duration-300 {{ $currentSubscription && $currentSubscription->plan_id == $plan->id ? 'ring-2 ring-purple-glow' : '' }}">
                <div class="text-center">
                    <h3 class="text-lg font-semibold text-white">{{ $plan->name }}</h3>
                    <div class="mt-2">
                        <span class="text-3xl font-bold text-white">{{ number_format($plan->price, 2) }} ₺</span>
                        <span class="text-gray-300">/{{ $plan->billing_cycle_text }}</span>
                    </div>
                    
                    <div class="mt-4 space-y-2 text-sm text-gray-300">
                        <div class="flex justify-between">
                            <span>Token:</span>
                            <span class="text-purple-glow">{{ $plan->getFeature('usage_tokens') == -1 ? 'Sınırsız' : number_format($plan->getFeature('usage_tokens')) }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span>Ürün:</span>
                            <span class="text-purple-glow">{{ $plan->getFeature('max_products') == -1 ? 'Sınırsız' : number_format($plan->getFeature('max_products')) }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span>Mesaj:</span>
                            <span class="text-purple-glow">{{ $plan->getFeature('max_messages') == -1 ? 'Sınırsız' : number_format($plan->getFeature('max_messages')) }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span>Destek:</span>
                            <span class="text-purple-glow">{{ $plan->getFeature('support') }}</span>
                        </div>
                    </div>
                    
                    @if($currentSubscription && $currentSubscription->plan_id == $plan->id)
                    <div class="mt-4">
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-900/50 text-green-300 border border-green-600/50">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            Mevcut Plan
                        </span>
                    </div>
                    @else
                    <div class="mt-4">
                        <a href="{{ route('subscription.plans') }}" target="_blank"
                                class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-purple-glow to-neon-purple hover:from-purple-dark hover:to-neon-purple text-white font-medium rounded-lg transition-all duration-300 transform hover:scale-105">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                            </svg>
                            Plan Seç
                        </a>
                    </div>
                    @endif
                </div>
            </div>
            @endforeach
        </div>
    </div> --}}

    <!-- Billing History Link -->
    {{-- <div class="mt-8 text-center">
        <a href="{{ route('subscription.billing-history') }}" 
           class="inline-flex items-center text-purple-glow hover:text-neon-purple transition-colors duration-300">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
            </svg>
            Fatura Geçmişi
        </a>
    </div> --}}
</div>

<script>
// Token satın alma formu - TEMPORARILY DISABLED
// document.getElementById('buyTokensForm').addEventListener('submit', function(e) {
//     e.preventDefault();
//     
//     const tokenAmount = document.getElementById('token_amount').value;
//     const amount = tokenAmount * 0.01; // Token başına 0.01 TL
//     
//     buyTokens(tokenAmount, amount);
// });

// Plan satın alma fonksiyonu kaldırıldı - artık convstateai.com/abone/ adresine yönlendiriliyor

// Token satın alma - TEMPORARILY DISABLED
// function buyTokens(tokenAmount, amount) {
//     fetch('/api/payment/create', {
//         method: 'POST',
//         headers: {
//             'Content-Type': 'application/json',
//             'X-CSRF-TOKEN': '{{ csrf_token() }}'
//         },
//         body: JSON.stringify({
//             amount: amount,
//             type: 'tokens',
//             token_amount: tokenAmount
//         })
//     })
//     .then(response => {
//         if (!response.ok) {
//             throw new Error(`HTTP error! status: ${response.status}`);
//         }
//         return response.json();
//     })
//     .then(data => {
//         if (data.success) {
//             window.location.href = '/payment/iframe/' + data.payment_id;
//         } else {
//             alert('Hata: ' + data.message);
//         }
//     })
//     .catch(error => {
//         console.error('Error:', error);
//         alert('Token satın alınırken hata oluştu: ' + error.message);
//     });
// }
</script>
@endsection