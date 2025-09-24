@extends('layouts.app')

@section('title', 'Ödeme - ConvStateAI')

@section('content')
<div class="min-h-screen bg-gray-900 py-8">
    <div class="max-w-4xl mx-auto px-4">
        <div class="glass-effect rounded-2xl p-8">
            <div class="text-center mb-8">
                <h1 class="text-3xl font-bold text-white mb-4">
                    <span class="gradient-text">Ödeme İşlemi</span> 💳
                </h1>
                <p class="text-gray-300">
                    Güvenli ödeme sayfasına yönlendiriliyorsunuz...
                </p>
            </div>

            <div class="bg-gray-800/50 rounded-lg p-6 mb-6">
                <h3 class="text-lg font-semibold text-white mb-4">Sipariş Detayları</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-gray-300">
                    <div>
                        <span class="text-gray-400">Sipariş No:</span>
                        <span class="ml-2 font-mono">{{ $order->uuid ?? 'N/A' }}</span>
                    </div>
                    <div>
                        <span class="text-gray-400">Plan:</span>
                        <span class="ml-2">{{ $order->plan->name ?? 'N/A' }}</span>
                    </div>
                    <div>
                        <span class="text-gray-400">Tutar:</span>
                        <span class="ml-2 font-semibold text-green-400">{{ number_format($order->amount ?? 0, 2) }} ₺</span>
                    </div>
                    <div>
                        <span class="text-gray-400">Ödeme Yöntemi:</span>
                        <span class="ml-2">PayTR</span>
                    </div>
                </div>
            </div>

            <div class="text-center">
                <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-purple-500 mb-4"></div>
                <p class="text-gray-400 mb-6">Ödeme sayfası yükleniyor...</p>
            </div>

            <script src="https://www.paytr.com/js/iframeResizer.min.js"></script>
            <iframe 
                src="https://www.paytr.com/odeme/guvenli/{{ $token }}" 
                id="paytriframe" 
                frameborder="0" 
                scrolling="no" 
                style="width: 100%; min-height: 600px; border-radius: 8px;">
            </iframe>
            <script>
                iFrameResize({
                    log: false,
                    checkOrigin: false,
                    heightCalculationMethod: 'max'
                }, '#paytriframe');
            </script>
        </div>
    </div>
</div>
@endsection
