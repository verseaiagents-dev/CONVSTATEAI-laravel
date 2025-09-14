<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class FieldMappingRateLimit
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $key = $this->resolveRequestSignature($request);
        
        // Different rate limits for different operations
        $operation = $this->getOperationType($request);
        $limits = $this->getRateLimits($operation);
        
        if (RateLimiter::tooManyAttempts($key, $limits['max_attempts'])) {
            $retryAfter = RateLimiter::availableIn($key);
            
            Log::warning("Rate limit exceeded for field mapping operation", [
                'operation' => $operation,
                'key' => $key,
                'retry_after' => $retryAfter
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Rate limit exceeded. Please try again later.',
                'retry_after' => $retryAfter,
                'operation' => $operation
            ], 429)->header('Retry-After', $retryAfter);
        }
        
        RateLimiter::hit($key, $limits['decay_minutes'] * 60);
        
        $response = $next($request);
        
        // Add rate limit headers
        $response->headers->add([
            'X-RateLimit-Limit' => $limits['max_attempts'],
            'X-RateLimit-Remaining' => RateLimiter::remaining($key, $limits['max_attempts']),
            'X-RateLimit-Reset' => time() + RateLimiter::availableIn($key)
        ]);
        
        return $response;
    }
    
    /**
     * Resolve request signature for rate limiting
     */
    private function resolveRequestSignature(Request $request): string
    {
        $user = $request->user();
        $ip = $request->ip();
        $route = $request->route();
        
        if ($user) {
            return 'field_mapping:user:' . $user->id;
        }
        
        return 'field_mapping:ip:' . $ip;
    }
    
    /**
     * Get operation type from request
     */
    private function getOperationType(Request $request): string
    {
        $route = $request->route();
        $action = $route->getActionName();
        
        if (str_contains($action, 'detectFields')) {
            return 'field_detection';
        }
        
        if (str_contains($action, 'saveFieldMappings')) {
            return 'save_mappings';
        }
        
        if (str_contains($action, 'previewTransformedData')) {
            return 'data_preview';
        }
        
        if (str_contains($action, 'validateData')) {
            return 'data_validation';
        }
        
        if (str_contains($action, 'processBatchData')) {
            return 'batch_processing';
        }
        
        if (str_contains($action, 'exportTransformedData')) {
            return 'data_export';
        }
        
        return 'default';
    }
    
    /**
     * Get rate limits for operation type
     */
    private function getRateLimits(string $operation): array
    {
        $limits = [
            'field_detection' => [
                'max_attempts' => 10,
                'decay_minutes' => 1
            ],
            'save_mappings' => [
                'max_attempts' => 20,
                'decay_minutes' => 1
            ],
            'data_preview' => [
                'max_attempts' => 30,
                'decay_minutes' => 1
            ],
            'data_validation' => [
                'max_attempts' => 25,
                'decay_minutes' => 1
            ],
            'batch_processing' => [
                'max_attempts' => 5,
                'decay_minutes' => 5
            ],
            'data_export' => [
                'max_attempts' => 15,
                'decay_minutes' => 1
            ],
            'default' => [
                'max_attempts' => 20,
                'decay_minutes' => 1
            ]
        ];
        
        return $limits[$operation] ?? $limits['default'];
    }
    
    /**
     * Check if request should be rate limited
     */
    private function shouldRateLimit(Request $request): bool
    {
        // Skip rate limiting for certain conditions
        if ($request->isMethod('GET')) {
            return false; // Don't rate limit GET requests
        }
        
        // Skip rate limiting for admin users
        $user = $request->user();
        if ($user && $user->hasRole('admin')) {
            return false;
        }
        
        return true;
    }
}
