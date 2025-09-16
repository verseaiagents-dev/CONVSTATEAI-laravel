<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TokenCheckMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        try {
            // Project ID'yi al
            $projectId = $request->input('project_id') ?: $request->header('X-Project-ID');
            
            if (empty($projectId) || $projectId === '0' || $projectId === 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Project ID is required',
                    'error' => 'MISSING_PROJECT_ID'
                ], 400);
            }

            // Project'i bul
            $project = \App\Models\Project::find($projectId);
            if (!$project || !$project->created_by) {
                return response()->json([
                    'success' => false,
                    'message' => 'Project not found or invalid',
                    'error' => 'PROJECT_NOT_FOUND'
                ], 404);
            }

            // Kullanıcıyı bul
            $user = \App\Models\User::find($project->created_by);
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not found',
                    'error' => 'USER_NOT_FOUND'
                ], 404);
            }

            // Token kontrolleri
            if (!$user->current_plan_id) {
                return response()->json([
                    'success' => false,
                    'type' => 'error',
                    'message' => 'Aktif planınız bulunmuyor. Lütfen bir plan satın alın.',
                    'error' => 'NO_ACTIVE_PLAN',
                    'redirect' => route('dashboard.subscription.index'),
                    'data' => [
                        'error' => 'NO_ACTIVE_PLAN',
                        'redirect' => route('dashboard.subscription.index')
                    ]
                ], 403);
            }

            if ($user->isTokenExpired()) {
                return response()->json([
                    'success' => false,
                    'type' => 'error',
                    'message' => 'Kullanım token\'larınızın süresi dolmuş. Lütfen planınızı yenileyin.',
                    'error' => 'USAGE_TOKENS_EXPIRED',
                    'redirect' => route('dashboard.subscription.index'),
                    'tokens_remaining' => $user->tokens_remaining,
                    'tokens_used' => $user->tokens_used,
                    'reset_date' => $user->token_reset_date,
                    'data' => [
                        'error' => 'USAGE_TOKENS_EXPIRED',
                        'redirect' => route('dashboard.subscription.index'),
                        'tokens_remaining' => $user->tokens_remaining,
                        'tokens_used' => $user->tokens_used,
                        'reset_date' => $user->token_reset_date
                    ]
                ], 403);
            }

            if (!$user->canUseToken()) {
                return response()->json([
                    'success' => false,
                    'type' => 'error',
                    'message' => 'Yetersiz kullanım token\'ı. Lütfen daha fazla token satın alın.',
                    'error' => 'INSUFFICIENT_TOKENS',
                    'redirect' => route('dashboard.subscription.index'),
                    'tokens_remaining' => $user->tokens_remaining,
                    'tokens_used' => $user->tokens_used,
                    'tokens_total' => $user->tokens_total,
                    'usage_percentage' => $user->token_usage_percentage,
                    'data' => [
                        'error' => 'INSUFFICIENT_TOKENS',
                        'redirect' => route('dashboard.subscription.index'),
                        'tokens_remaining' => $user->tokens_remaining,
                        'tokens_used' => $user->tokens_used,
                        'tokens_total' => $user->tokens_total,
                        'usage_percentage' => $user->token_usage_percentage
                    ]
                ], 403);
            }

            // Request'e user bilgisini ekle
            $request->merge(['_user' => $user]);
            $request->merge(['_project' => $project]);

            return $next($request);

        } catch (\Exception $e) {
            \Log::error('TokenCheckMiddleware error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Token validation failed',
                'error' => 'TOKEN_VALIDATION_ERROR'
            ], 500);
        }
    }
}
