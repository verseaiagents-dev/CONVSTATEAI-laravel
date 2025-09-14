@extends('layouts.dashboard')

@section('title', 'Fatura Geçmişi')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Fatura Geçmişi</h1>
        <p class="text-gray-600 dark:text-gray-400 mt-2">Tüm ödemelerinizi ve faturalarınızı görüntüleyin</p>
    </div>

    @if($payments->count() > 0)
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Tarih
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Açıklama
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Miktar
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Durum
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Ödeme Yöntemi
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            İşlemler
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                    @foreach($payments as $payment)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                            {{ $payment->created_at->format('d.m.Y H:i') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                            @if($payment->subscription)
                                {{ $payment->subscription->plan->name }} Planı
                            @elseif(isset($payment->metadata['token_amount']))
                                {{ number_format($payment->metadata['token_amount']) }} Token Satın Alma
                            @else
                                Ödeme
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                            {{ $payment->formatted_amount }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                @if($payment->status === 'completed') bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200
                                @elseif($payment->status === 'pending') bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200
                                @elseif($payment->status === 'failed') bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200
                                @elseif($payment->status === 'refunded') bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200
                                @else bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-200
                                @endif">
                                @if($payment->status === 'completed')
                                    <i class="fas fa-check-circle mr-1"></i>
                                @elseif($payment->status === 'pending')
                                    <i class="fas fa-clock mr-1"></i>
                                @elseif($payment->status === 'failed')
                                    <i class="fas fa-times-circle mr-1"></i>
                                @elseif($payment->status === 'refunded')
                                    <i class="fas fa-undo mr-1"></i>
                                @else
                                    <i class="fas fa-question-circle mr-1"></i>
                                @endif
                                {{ $payment->status_text }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                            <div class="flex items-center">
                                @if($payment->gateway === 'stripe')
                                    <i class="fab fa-stripe text-blue-600 mr-2"></i>
                                    Stripe
                                @elseif($payment->gateway === 'iyzico')
                                    <i class="fas fa-credit-card text-purple-600 mr-2"></i>
                                    İyzico
                                @elseif($payment->gateway === 'paytr')
                                    <i class="fas fa-credit-card text-green-600 mr-2"></i>
                                    PayTR
                                @else
                                    <i class="fas fa-credit-card text-gray-600 mr-2"></i>
                                    {{ ucfirst($payment->gateway) }}
                                @endif
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                            <div class="flex space-x-2">
                                <button onclick="viewPaymentDetails({{ $payment->id }})" 
                                        class="text-blue-600 hover:text-blue-700 dark:text-blue-400 dark:hover:text-blue-300">
                                    <i class="fas fa-eye"></i>
                                </button>
                                
                                @if($payment->status === 'completed')
                                <button onclick="downloadInvoice({{ $payment->id }})" 
                                        class="text-green-600 hover:text-green-700 dark:text-green-400 dark:hover:text-green-300">
                                    <i class="fas fa-download"></i>
                                </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700">
            {{ $payments->links() }}
        </div>
    </div>
    @else
    <!-- No Payments -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-12 text-center">
        <i class="fas fa-receipt text-6xl text-gray-400 dark:text-gray-600 mb-4"></i>
        <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-2">Henüz Ödeme Bulunmuyor</h3>
        <p class="text-gray-600 dark:text-gray-400 mb-6">
            Henüz hiç ödeme yapmamışsınız. İlk planınızı satın alarak başlayın.
        </p>
        <a href="{{ route('subscription.plans') }}" 
           class="inline-flex items-center px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-colors">
            <i class="fas fa-shopping-cart mr-2"></i>
            Plan Seç
        </a>
    </div>
    @endif

    <!-- Back to Subscription -->
    <div class="mt-8 text-center">
        <a href="{{ route('dashboard.subscription.index') }}" 
           class="inline-flex items-center text-blue-600 hover:text-blue-700 dark:text-blue-400 dark:hover:text-blue-300">
            <i class="fas fa-arrow-left mr-2"></i>
            Abonelik Yönetimine Dön
        </a>
    </div>
</div>

<!-- Payment Details Modal -->
<div id="paymentModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden z-50">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl max-w-md w-full">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Ödeme Detayları</h3>
                <button onclick="closePaymentModal()" class="absolute top-4 right-4 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div id="paymentDetails" class="px-6 py-4">
                <!-- Payment details will be loaded here -->
            </div>
        </div>
    </div>
</div>

<script>
function viewPaymentDetails(paymentId) {
    // Load payment details via AJAX
    fetch(`/api/payment/${paymentId}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const payment = data.data;
                document.getElementById('paymentDetails').innerHTML = `
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Ödeme ID</label>
                            <p class="text-sm text-gray-900 dark:text-white">${payment.id}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Miktar</label>
                            <p class="text-sm text-gray-900 dark:text-white">${payment.formatted_amount}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Durum</label>
                            <p class="text-sm text-gray-900 dark:text-white">${payment.status_text}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Ödeme Yöntemi</label>
                            <p class="text-sm text-gray-900 dark:text-white">${payment.gateway}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Tarih</label>
                            <p class="text-sm text-gray-900 dark:text-white">${new Date(payment.created_at).toLocaleString('tr-TR')}</p>
                        </div>
                        ${payment.paid_at ? `
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Ödeme Tarihi</label>
                            <p class="text-sm text-gray-900 dark:text-white">${new Date(payment.paid_at).toLocaleString('tr-TR')}</p>
                        </div>
                        ` : ''}
                    </div>
                `;
                document.getElementById('paymentModal').classList.remove('hidden');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Ödeme detayları yüklenirken hata oluştu.');
        });
}

function closePaymentModal() {
    document.getElementById('paymentModal').classList.add('hidden');
}

function downloadInvoice(paymentId) {
    // Implement invoice download
    window.open(`/api/payment/${paymentId}/invoice`, '_blank');
}
</script>
@endsection
