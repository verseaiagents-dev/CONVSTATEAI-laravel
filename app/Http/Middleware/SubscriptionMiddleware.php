<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class SubscriptionMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Kullanıcı giriş yapmamışsa devam et
        if (!Auth::check()) {
            return $next($request);
        }

        $user = Auth::user();
        
        // Admin kullanıcılar için kontrol yapma
        if ($user->isAdmin()) {
            return $next($request);
        }

        // Aktif subscription kontrolü
        $subscription = $user->activeSubscription;
        
        if (!$subscription) {
            // Subscription yoksa erişim kısıtla
            return $this->showSubscriptionRequired($request);
        }

        // Subscription süresi dolmuş mu kontrol et
        if ($subscription->shouldRedirectToExpired()) {
            return $this->showSubscriptionExpired($request, $subscription);
        }

        // Trial süresi dolmuş mu kontrol et
        if ($subscription->isTrialExpired()) {
            return $this->showTrialExpired($request, $subscription);
        }

        return $next($request);
    }

    /**
     * Subscription gerekli mesajını göster
     */
    private function showSubscriptionRequired(Request $request)
    {
        if ($request->expectsJson()) {
            return response()->json([
                'error' => 'Subscription gerekli',
                'message' => 'Bu özelliği kullanmak için aktif bir planınız olmalıdır.',
                'redirect' => route('dashboard.subscription.index')
            ], 403);
        }

        return redirect()->route('dashboard.subscription.index')
            ->with('error', 'Bu özelliği kullanmak için aktif bir planınız olmalıdır.');
    }

    /**
     * Subscription süresi dolmuş mesajını göster
     */
    private function showSubscriptionExpired(Request $request, $subscription)
    {
        if ($request->expectsJson()) {
            return response()->json([
                'error' => 'Subscription süresi dolmuş',
                'message' => 'Planınızın süresi dolmuştur. Lütfen planınızı yenileyin.',
                'redirect' => route('subscription.expired')
            ], 403);
        }

        return redirect()->route('subscription.expired');
    }

    /**
     * Trial süresi dolmuş mesajını göster
     */
    private function showTrialExpired(Request $request, $subscription)
    {
        if ($request->expectsJson()) {
            return response()->json([
                'error' => 'Trial süresi dolmuş',
                'message' => 'Deneme süreniz dolmuştur. Lütfen bir plan seçin.',
                'redirect' => route('dashboard.subscription.index')
            ], 403);
        }

        return redirect()->route('dashboard.subscription.index')
            ->with('error', 'Deneme süreniz dolmuştur. Lütfen bir plan seçin.');
    }
}
