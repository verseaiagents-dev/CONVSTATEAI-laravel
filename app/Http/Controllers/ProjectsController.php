<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Project;
use App\Models\KnowledgeBase;
use App\Models\EnhancedChatSession;
use App\Models\WidgetCustomization;

class ProjectsController extends Controller
{
    /**
     * Show projects page
     */
    public function index()
    {
        $user = Auth::user();
        return view('dashboard.projects', compact('user'));
    }

    /**
     * Load projects content via AJAX
     */
    public function loadContent()
    {
        try {
            $user = Auth::user();
            $projects = Project::where('created_by', $user->id)
                ->with(['creator', 'knowledgeBases', 'chatSessions'])
                ->orderBy('created_at', 'desc')
                ->get();
            
            return response()->json([
                'success' => true,
                'data' => [
                    'projects' => $projects
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Projeler yüklenirken hata oluştu: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store a new project
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'url' => 'required|url|max:500',
            'status' => 'required|in:active,inactive,completed,archived',
        ]);

        $project = Project::create([
            'name' => $request->name,
            'description' => $request->description,
            'url' => $request->url,
            'status' => 'active', // Otomatik olarak aktif yap
            'created_by' => Auth::id(),
        ]);

        // Proje oluşturulduğunda WidgetCustomization kaydını da oluştur
        WidgetCustomization::create([
            'user_id' => Auth::id(),
            'project_id' => $project->id,
            'customization_token' => hash('sha256', $project->id . time() . uniqid()),
            'customization_data' => json_encode([])
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Proje başarıyla oluşturuldu.',
            'project' => $project
        ]);
    }

    /**
     * Update project
     */
    public function update(Request $request, Project $project)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'url' => 'required|url|max:500',
            'status' => 'required|in:active,inactive,completed,archived',
        ]);

        $project->update([
            'name' => $request->name,
            'description' => $request->description,
            'url' => $request->url,
            'status' => $request->status,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Proje başarıyla güncellendi.',
            'project' => $project
        ]);
    }

    /**
     * Delete project
     */
    public function destroy(Project $project)
    {
        $project->delete();

        return response()->json([
            'success' => true,
            'message' => 'Proje başarıyla silindi.'
        ]);
    }

    /**
     * Show project details
     */
    public function show(Project $project)
    {
        $project->load(['creator', 'knowledgeBases', 'chatSessions']);
        
        return response()->json([
            'success' => true,
            'project' => $project
        ]);
    }

    /**
     * Get project embed code
     */
    public function getEmbedCode(Project $project)
    {
        try {
            // Widget customization kaydını al
            $widgetCustomization = WidgetCustomization::where('project_id', $project->id)
                ->where('user_id', Auth::id())
                ->firstOrFail();

            $embedCode = view('dashboard.partials.embed-code', [
                'project' => $project,
                'customization_token' => $widgetCustomization->customization_token
            ])->render();

            return response()->json([
                'success' => true,
                'embedCode' => $embedCode,
                'customizationToken' => $widgetCustomization->customization_token
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Embed kodu oluşturulurken bir hata oluştu: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Test if a URL is accessible
     */
    public function testUrl(Request $request)
    {
        $request->validate([
            'url' => 'required|url'
        ]);

        try {
            $ch = curl_init($request->url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_NOBODY, true);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 10);
            curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            $isAccessible = $httpCode >= 200 && $httpCode < 400;

            return response()->json([
                'success' => true,
                'isAccessible' => $isAccessible,
                'statusCode' => $httpCode
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'URL test edilirken bir hata oluştu: ' . $e->getMessage(),
                'isAccessible' => false
            ], 500);
        }
    }
}