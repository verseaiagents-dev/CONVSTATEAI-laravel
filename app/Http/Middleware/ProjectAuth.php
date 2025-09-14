<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\Project;

class ProjectAuth
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
        // Project ID'yi al - önce header, sonra JSON body, sonra form data
        $projectId = $request->header('""X-Project-ID""') 
            ?? $request->json('project_id') 
            ?? $request->input('project_id');
        
        if (!$projectId) {
            return response()->json([
                'success' => false,
                'message' => 'Project ID is required',
                'error' => 'MISSING_PROJECT_ID'
            ], 400);
        }

        // Project'i kontrol et
        $project = Project::where('id', $projectId)
            ->where('status', 'active')
            ->first();
            
        if (!$project) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid or inactive project',
                'error' => 'INVALID_PROJECT'
            ], 404);
        }

        // Token kontrolü (opsiyonel - sadece varsa kontrol et)
        $token = $request->bearerToken();
        if ($token && !$this->validateToken($token, $projectId)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid authentication token',
                'error' => 'INVALID_TOKEN'
            ], 401);
        }

        // User ID'yi al
        $userId = $request->header('X-User-ID') ?? $request->input('user_id');
        
        // Request'e project ve user bilgilerini ekle
        $request->merge([
            'project' => $project,
            'user_id' => $userId,
            'project_id' => $projectId
        ]);

        return $next($request);
    }

    /**
     * Token doğrulama (basit implementasyon)
     */
    private function validateToken(string $token, string $projectId): bool
    {
        // Burada daha gelişmiş token doğrulama yapılabilir
        // Şimdilik basit bir kontrol
        return !empty($token) && strlen($token) > 10;
    }
}
