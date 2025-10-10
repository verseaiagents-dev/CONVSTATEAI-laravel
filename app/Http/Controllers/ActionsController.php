<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use App\Models\WidgetActions;
use App\Models\WidgetCustomization;
use App\Models\Project;

class ActionsController extends Controller
{
    /**
     * Show actions management page
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $projectId = $request->query('project_id');
        
        // Eğer project_id belirtilmemişse dashboard'a yönlendir
        if (!$projectId) {
            return redirect()->route('dashboard')
                ->with('warning', 'Lütfen önce bir proje seçin.');
        }
        
        // Project var mı kontrol et
        $project = Project::find($projectId);
        if (!$project) {
            abort(404, 'Proje bulunamadı');
        }
        
        // Kullanıcının bu projeye erişim yetkisi var mı kontrol et
        if ($project->created_by !== $user->id) {
            abort(403, 'Bu projeye erişim yetkiniz yok');
        }
        
        return view('dashboard.actions', compact('user', 'projectId', 'project'));
    }

    /**
     * Load actions content for lazy loading
     */
    public function loadContent(Request $request): JsonResponse
    {
        try {
            $user = Auth::user();
            $projectId = $request->query('project_id');
            
            // Get widget customizations for the user
            $widgetCustomizations = WidgetCustomization::where('user_id', $user->id)->get();
            
            // Get widget actions
            $widgetActions = WidgetActions::whereHas('widgetCustomization', function($query) use ($user) {
                $query->where('user_id', $user->id);
            })->get();
            
            // Get project if project_id is provided
            $project = null;
            if ($projectId) {
                $project = Project::find($projectId);
            }
            
            // Get stats data
            $stats = [
                'active_actions' => WidgetActions::where('is_active', true)->count(),
                'total_actions' => WidgetActions::count(),
                'siparis_endpoints' => WidgetActions::where('type', 'siparis_durumu_endpoint')->where('is_active', true)->count(),
                'kargo_endpoints' => WidgetActions::where('type', 'kargo_durumu_endpoint')->where('is_active', true)->count()
            ];
            
            return response()->json([
                'success' => true,
                'data' => [
                    'widgetCustomizations' => $widgetCustomizations,
                    'widgetActions' => $widgetActions,
                    'project' => $project,
                    'stats' => $stats
                ]
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'İçerik yüklenirken hata oluştu: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Test API endpoint
     */
    public function testEndpoint(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'endpoint' => 'required|url',
                'type' => 'required|in:siparis,kargo',
                'tracking_number' => 'nullable|string'
            ]);

            $endpoint = $request->input('endpoint');
            $type = $request->input('type');
            $trackingNumber = $request->input('tracking_number');

            // Make HTTP request to test the endpoint
            $client = new \GuzzleHttp\Client();
            
            try {
                $response = $client->get($endpoint, [
                    'timeout' => 30,
                    'headers' => [
                        'Accept' => 'application/json',
                        'User-Agent' => 'ConvStateAI/1.0'
                    ]
                ]);

                $statusCode = $response->getStatusCode();
                $body = $response->getBody()->getContents();
                
                // Try to decode JSON response
                $jsonData = json_decode($body, true);
                
                if ($statusCode === 200) {
                    return response()->json([
                        'success' => true,
                        'message' => 'API endpoint başarıyla test edildi',
                        'data' => [
                            'status_code' => $statusCode,
                            'response' => $jsonData ?: $body,
                            'endpoint' => $endpoint,
                            'type' => $type
                        ]
                    ]);
                } else {
                    return response()->json([
                        'success' => false,
                        'message' => "API endpoint HTTP {$statusCode} döndü",
                        'data' => [
                            'status_code' => $statusCode,
                            'response' => $jsonData ?: $body
                        ]
                    ]);
                }
                
            } catch (\GuzzleHttp\Exception\RequestException $e) {
                return response()->json([
                    'success' => false,
                    'message' => 'API endpoint\'e bağlanılamadı: ' . $e->getMessage(),
                    'data' => [
                        'error' => $e->getMessage()
                    ]
                ]);
            }
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Test sırasında hata oluştu: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Save widget actions
     */
    public function saveActions(Request $request): JsonResponse
    {
        try {
            $user = Auth::user();
            
            $request->validate([
                'siparis_durumu_endpoint' => 'nullable|url',
                'kargo_durumu_endpoint' => 'nullable|url',
                'siparis_active' => 'boolean',
                'kargo_active' => 'boolean'
            ]);

            // Get or create widget customization for the user
            $widgetCustomization = WidgetCustomization::firstOrCreate(
                ['user_id' => $user->id],
                [
                    'ai_name' => 'AI Asistan',
                    'welcome_message' => 'Merhaba! Size nasıl yardımcı olabilirim?',
                    'is_active' => true
                ]
            );

            // Update or create siparis endpoint action
            if ($request->has('siparis_durumu_endpoint')) {
                WidgetActions::updateOrCreate(
                    [
                        'widget_customization_id' => $widgetCustomization->id,
                        'type' => 'siparis_durumu_endpoint'
                    ],
                    [
                        'endpoint' => $request->input('siparis_durumu_endpoint'),
                        'is_active' => $request->input('siparis_active', false),
                        'display_name' => 'Sipariş Durumu API'
                    ]
                );
            }

            // Update or create kargo endpoint action
            if ($request->has('kargo_durumu_endpoint')) {
                WidgetActions::updateOrCreate(
                    [
                        'widget_customization_id' => $widgetCustomization->id,
                        'type' => 'kargo_durumu_endpoint'
                    ],
                    [
                        'endpoint' => $request->input('kargo_durumu_endpoint'),
                        'is_active' => $request->input('kargo_active', false),
                        'display_name' => 'Kargo Durumu API'
                    ]
                );
            }

            return response()->json([
                'success' => true,
                'message' => 'API endpoint ayarları başarıyla kaydedildi'
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Ayarlar kaydedilirken hata oluştu: ' . $e->getMessage()
            ], 500);
        }
    }
}
