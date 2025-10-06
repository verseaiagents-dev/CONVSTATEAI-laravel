<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Project;
use App\Models\KnowledgeBase;
use App\Models\EnhancedChatSession;

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
                'data' => compact('projects')
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
            'status' => $request->status,
            'created_by' => Auth::id(),
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
        // Check if user owns this project
        if ($project->created_by !== Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'Bu projeyi düzenleme yetkiniz yok.'
            ], 403);
        }

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
        // Check if user owns this project
        if ($project->created_by !== Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'Bu projeyi silme yetkiniz yok.'
            ], 403);
        }

        $project->delete();

        return response()->json([
            'success' => true,
            'message' => 'Proje başarıyla silindi.'
        ]);
    }

    /**
     * Get project details with knowledge bases
     */
    public function show(Project $project)
    {
        // Check if user owns this project
        if ($project->created_by !== Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'Bu projeye erişim yetkiniz yok.'
            ], 403);
        }

        $project->load(['knowledgeBases', 'chatSessions']);

        return response()->json([
            'success' => true,
            'project' => $project
        ]);
    }

    /**
     * Get embed code for project
     */
    public function getEmbedCode(Project $project)
    {
        // Check if user owns this project
        if ($project->created_by !== Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'Bu projeye erişim yetkiniz yok.'
            ], 403);
        }

        $user = Auth::user();
        $customizationToken = $user->personal_token ? $user->personal_token : 'dcf91b8e63c9552b724a4523261318e565ef33992e454dbc0cff1064aae19246';
        
        // Generate embed code
        $embedCode = <<<EOT
<script src="https://convstateai.com/embed/convstateai.min.js"></script>
<script>
window.convstateaiConfig = {
    projectId: "{$project->id}",
    customizationToken: "{$customizationToken}"
};
</script>
EOT;

        return response()->json([
            'success' => true,
            'embedCode' => $embedCode,
            'project' => [
                'id' => $project->id,
                'name' => $project->name
            ]
        ]);
    }
}
