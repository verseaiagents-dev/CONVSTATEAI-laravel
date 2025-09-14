<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class ChatSessionRateLimit
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $sessionId = $request->input('session_id') ?? $request->route('session_id');
        
        if (!$sessionId) {
            return $next($request);
        }

        // Rate limit key based on session ID
        $key = 'chat_session:' . $sessionId;
        
        // Check if rate limit exceeded
        if (RateLimiter::tooManyAttempts($key, $this->maxAttempts())) {
            Log::warning('Rate limit exceeded for chat session', [
                'session_id' => $sessionId,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'endpoint' => $request->path()
            ]);

            return response()->json([
                'error' => 'Rate limit exceeded. Please try again later.',
                'retry_after' => RateLimiter::availableIn($key)
            ], 429);
        }

        // Increment rate limit counter
        RateLimiter::hit($key, $this->decayMinutes() * 60);

        // Add rate limit headers to response
        $response = $next($request);
        $response->headers->add([
            'X-RateLimit-Limit' => $this->maxAttempts(),
            'X-RateLimit-Remaining' => RateLimiter::remaining($key, $this->maxAttempts()),
            'X-RateLimit-Reset' => time() + RateLimiter::availableIn($key)
        ]);

        return $response;
    }

    /**
     * Get the maximum number of attempts for the rate limiter.
     */
    protected function maxAttempts(): int
    {
        return config('chat.rate_limit.max_attempts', 100);
    }

    /**
     * Get the number of minutes to decay the rate limiter.
     */
    protected function decayMinutes(): int
    {
        return config('chat.rate_limit.decay_minutes', 1);
    }
}
