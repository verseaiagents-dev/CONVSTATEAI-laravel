@if(Auth::check())
    @php
        $user = Auth::user();
        $hasActiveSubscription = $user->hasActiveSubscription();
        $subscription = $user->activeSubscription;
        $daysRemaining = $subscription ? $subscription->days_remaining : null;
    @endphp
    

    
    @if($hasActiveSubscription && $subscription)
        @if($daysRemaining <= 0)
            <script>
                // Plan süresi dolmuş ise kullanıcıyı direkt expired sayfasına yönlendir
                window.location.href = "{{ route('subscription.expired') }}";
            </script>
        @elseif($daysRemaining <= 7)
            <div class="bg-orange-500/20 border border-orange-500/50 rounded-lg p-4 mb-4">
                <div class="flex items-center space-x-3">
                    <svg class="w-6 h-6 text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                    </svg>
                    <div>
                        <h3 class="text-lg font-semibold text-orange-400">Plan Süresi Dolmak Üzere</h3>
                        <p class="text-orange-300">Planınızın süresi {{ $daysRemaining }} gün sonra dolacak.</p>
                    </div>
                </div>
                <div class="mt-3">
                    <a href="{{ route('dashboard.subscription.index') }}" class="inline-flex items-center px-4 py-2 bg-orange-500 hover:bg-orange-600 text-white font-medium rounded-lg transition-colors duration-200">
                        Planı Yenile
                    </a>
                </div>
            </div>
        @endif
    @endif
@endif
