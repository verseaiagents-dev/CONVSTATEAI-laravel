<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\UsageToken;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class UsageTokenMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Kullanıcı giriş yapmamışsa devam et (public API'ler için)
        if (!Auth::check()) {
            return $next($request);
        }

        $user = Auth::user();
        
        // Admin kullanıcılar için kontrol yapma
        if ($user->isAdmin()) {
            return $next($request);
        }

        // Usage token kontrolü
        $usageToken = UsageToken::getActiveForUser($user->id);
        
        if (!$usageToken) {
            return $this->handleNoUsageToken($request);
        }

        // Token'ların süresi dolmuş mu?
        if ($usageToken->isExpired()) {
            return $this->handleExpiredToken($request, $usageToken);
        }

        // Token kullanılabilir mi?
        if (!$usageToken->canUseToken()) {
            return $this->handleInsufficientTokens($request, $usageToken);
        }

        // Token kullanımını kaydet (request sonrası)
        $response = $next($request);
        
        // Başarılı response ise token kullan
        if ($response->getStatusCode() >= 200 && $response->getStatusCode() < 300) {
            $usageToken->useToken();
        }

        return $response;
    }

    /**
     * Usage token yoksa ne yapılacak
     */
    private function handleNoUsageToken(Request $request)
    {
        if ($request->expectsJson()) {
            return response()->json([
                'success' => false,
                'error' => 'NO_USAGE_TOKENS',
                'message' => 'Kullanım token\'ınız bulunmuyor. Lütfen bir plan satın alın.',
                'redirect' => route('subscription.index')
            ], 403);
        }

        return redirect()->route('subscription.index')
            ->with('error', 'Kullanım token\'ınız bulunmuyor. Lütfen bir plan satın alın.');
    }

    /**
     * Token süresi dolmuşsa ne yapılacak
     */
    private function handleExpiredToken(Request $request, UsageToken $usageToken)
    {
        if ($request->expectsJson()) {
            return response()->json([
                'success' => false,
                'error' => 'USAGE_TOKENS_EXPIRED',
                'message' => 'Kullanım token\'larınızın süresi dolmuş. Lütfen planınızı yenileyin.',
                'redirect' => route('subscription.index'),
                'tokens_remaining' => $usageToken->tokens_remaining,
                'tokens_used' => $usageToken->tokens_used,
                'reset_date' => $usageToken->reset_date
            ], 403);
        }

        return redirect()->route('subscription.index')
            ->with('error', 'Kullanım token\'larınızın süresi dolmuş. Lütfen planınızı yenileyin.');
    }

    /**
     * Yetersiz token varsa ne yapılacak
     */
    private function handleInsufficientTokens(Request $request, UsageToken $usageToken)
    {
        if ($request->expectsJson()) {
            return response()->json([
                'success' => false,
                'error' => 'INSUFFICIENT_TOKENS',
                'message' => 'Yetersiz kullanım token\'ı. Lütfen daha fazla token satın alın.',
                'redirect' => route('subscription.index'),
                'tokens_remaining' => $usageToken->tokens_remaining,
                'tokens_used' => $usageToken->tokens_used,
                'tokens_total' => $usageToken->tokens_total,
                'usage_percentage' => $usageToken->usage_percentage
            ], 403);
        }

        return redirect()->route('subscription.index')
            ->with('error', 'Yetersiz kullanım token\'ı. Lütfen daha fazla token satın alın.');
    }
}
