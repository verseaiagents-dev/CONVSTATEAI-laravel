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
        $projectId = $request->header('X-Project-ID') 
            ?? $request->json('project_id') 
            ?? $request->input('project_id');
            
        // Type casting - string veya integer olabilir
        $projectId = (string) $projectId;
        
        // Debug log
        \Log::info('ProjectAuth Debug', [
            'project_id_header' => $request->header('X-Project-ID'),
            'project_id_json' => $request->json('project_id'),
            'project_id_input' => $request->input('project_id'),
            'final_project_id' => $projectId,
            'project_id_type' => gettype($projectId)
        ]);
        
        if (!$projectId) {
            return response()->json([
                'success' => false,
                'message' => 'Proje ID gerekli',
                'error' => 'MISSING_PROJECT_ID'
            ], 400);
        }

        // Project'i kontrol et - hem string hem integer olarak dene
        $project = Project::where('id', $projectId)
            ->orWhere('id', (int) $projectId)
            ->where('status', 'active')
            ->first();
            
        \Log::info('Project Query Debug', [
            'project_id' => $projectId,
            'project_found' => $project ? 'Yes' : 'No',
            'project_status' => $project ? $project->status : 'N/A'
        ]);
            
        if (!$project) {
            return response()->json([
                'success' => false,
                'message' => 'Geçersiz veya aktif olmayan proje',
                'error' => 'INVALID_PROJECT'
            ], 404);
        }

        // Token kontrolü (opsiyonel - sadece varsa kontrol et)
        $token = $request->bearerToken();
        if ($token && !$this->validateToken($token, $projectId)) {
            return response()->json([
                'success' => false,
                'message' => 'Geçersiz kimlik doğrulama token\'ı',
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
