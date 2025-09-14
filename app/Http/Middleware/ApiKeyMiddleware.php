<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\User;

class ApiKeyMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $apiKey = $request->header('X-API-Key');
        
        if (!$apiKey) {
            return response()->json([
                'success' => false,
                'message' => 'API key is required'
            ], 401);
        }

        $user = User::where('personal_token', $apiKey)
                   ->where('token_expires_at', '>', now())
                   ->first();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid or expired API key'
            ], 401);
        }

        // Add user to request for use in controllers
        $request->merge(['api_user' => $user]);

        return $next($request);
    }
}
