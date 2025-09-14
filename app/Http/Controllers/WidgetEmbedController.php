<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Project;
use App\Models\WidgetCustomization;

class WidgetEmbedController extends Controller
{
    /**
     * Generate embed script for a project
     */
    public function generateScript(Request $request)
    {
        $projectId = $request->input('project_id');
        $customization = $request->input('customization', []);
        $apiUrl = config('app.url');
        
        if (!$projectId) {
            return response()->json([
                'success' => false,
                'message' => 'Project ID is required'
            ], 400);
        }

        // Project'i kontrol et
        $project = Project::where('id', $projectId)
            ->where('is_active', true)
            ->first();
            
        if (!$project) {
            return response()->json([
                'success' => false,
                'message' => 'Project not found or inactive'
            ], 404);
        }

        // Widget customization'ı al
        $widgetCustomization = WidgetCustomization::where('project_id', $projectId)
            ->where('is_active', true)
            ->orderBy('updated_at', 'desc')
            ->first();

        // Embed script'i oluştur
        $script = $this->generateEmbedScript($projectId, $apiUrl, $customization, $widgetCustomization);

        return response($script)
            ->header('Content-Type', 'application/javascript')
            ->header('Access-Control-Allow-Origin', '*')
            ->header('Access-Control-Allow-Methods', 'GET, POST, OPTIONS')
            ->header('Access-Control-Allow-Headers', 'Content-Type, Authorization');
    }

    /**
     * Generate the actual embed script
     */
    private function generateEmbedScript($projectId, $apiUrl, $customization, $widgetCustomization)
    {
        $theme = $customization['theme'] ?? 'default';
        $position = $customization['position'] ?? 'bottom-right';
        $primaryColor = $customization['colors']['primary'] ?? '#667eea';
        $secondaryColor = $customization['colors']['secondary'] ?? '#764ba2';

        return "
(function(d, t) {
  var v = d.createElement(t), s = d.getElementsByTagName(t)[0];
  v.onload = function() {
    window.convstateai = window.convstateai || {};
    window.convstateai.chat = window.convstateai.chat || {};
    
    window.convstateai.chat.load({
      projectId: '{$projectId}',
      apiUrl: '{$apiUrl}/api',
      version: 'production',
      customization: {
        theme: '{$theme}',
        position: '{$position}',
        colors: {
          primary: '{$primaryColor}',
          secondary: '{$secondaryColor}'
        }
      },
      auth: {
        token: null,
        userId: null
      }
    }).then(() => {
      console.log('ConvStateAI Widget loaded successfully for project: {$projectId}');
    }).catch((error) => {
      console.error('ConvStateAI Widget failed to load:', error);
    });
  }
  v.src = '{$apiUrl}/widget/dist/bundle.js';
  v.type = 'text/javascript';
  s.parentNode.insertBefore(v, s);
})(document, 'script');
        ";
    }

    /**
     * Get project information
     */
    public function getProjectInfo(Request $request, $projectId)
    {
        $project = Project::where('id', $projectId)
            ->where('is_active', true)
            ->first();
            
        if (!$project) {
            return response()->json([
                'success' => false,
                'message' => 'Project not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $project->id,
                'name' => $project->name,
                'description' => $project->description,
                'is_active' => $project->is_active,
                'created_at' => $project->created_at,
                'updated_at' => $project->updated_at
            ]
        ]);
    }
}
