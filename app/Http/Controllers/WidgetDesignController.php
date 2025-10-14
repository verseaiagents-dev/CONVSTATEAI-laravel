<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Models\WidgetCustomization;
use App\Models\WidgetActions;
use App\Models\Project;
use App\Models\NotificationWidgetSetting;
use App\Models\Site;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class WidgetDesignController extends Controller
{
    public function index(Request $request)
    {
        $projectId = $request->query('project_id');
        
        // If project_id is provided, validate it exists
        if ($projectId) {
            $project = Project::find($projectId);
            if (!$project) {
                abort(404, 'Project not found');
            }
        }
        
        // Ensure user has a personal token for API access
        $user = Auth::user();
        if (!$user->personal_token || !$user->hasValidPersonalToken()) {
            $user->generatePersonalToken();
        }
        
        return view('dashboard.widget-design', compact('projectId'));
    }

    public function loadContent(Request $request): JsonResponse
    {
        try {
            $user = Auth::user();
            $projectId = $request->query('project_id');
            
            // Get widget customization data
            $widgetCustomization = WidgetCustomization::where('user_id', $user->id)
                ->where('project_id', $projectId)
                ->first();
            
            // If no customization found for this project, try to find any active customization for this user
            if (!$widgetCustomization) {
                $widgetCustomization = WidgetCustomization::where('user_id', $user->id)
                    ->where('is_active', true)
                    ->first();
            }
            
            $widgetActions = null;
            if ($widgetCustomization) {
                $widgetActions = $widgetCustomization->widgetActions;
            }
            
            // Get project data if project_id is provided
            $project = null;
            if ($projectId) {
                $project = Project::find($projectId);
            }
            
            // Get notification widget settings
            $notificationWidget = null;
            if ($projectId) {
                // Get site for this project
                $site = Site::where('project_id', $projectId)->first();
                if ($site) {
                    $notificationWidget = NotificationWidgetSetting::where('site_id', $site->id)->first();
                }
            } else {
                // Use the first available site
                $site = Site::first();
                if ($site) {
                    $notificationWidget = NotificationWidgetSetting::where('site_id', $site->id)->first();
                }
            }
            
            return response()->json([
                'success' => true,
                'data' => [
                    'widgetCustomization' => $widgetCustomization,
                    'widgetActions' => $widgetActions,
                    'project' => $project,
                    'notificationWidget' => $notificationWidget
                ]
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'İçerik yüklenirken hata oluştu: ' . $e->getMessage()
            ], 500);
        }
    }

    public function store(Request $request): JsonResponse
    {
        try {
            $user = Auth::user();
            
            // Validate project_id if provided
            if ($request->has('project_id') && $request->project_id) {
                $project = Project::find($request->project_id);
                if (!$project) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Geçersiz proje ID'
                    ], 400);
                }
            }
            
            // Debug: Gelen request verilerini logla
            Log::debug('Widget design store request', [
                'user_id' => $user->id,
                'project_id' => $request->project_id,
                'request_data' => $request->all()
            ]);
            
            // WidgetCustomization'ı bul veya oluştur (sadece güncelleme için)
            $widgetCustomization = WidgetCustomization::updateOrCreate(
                [
                    'user_id' => $user->id, 
                    'project_id' => $request->project_id ?? null
                ],
                [
                    'ai_name' => 'AI Asistan',
                    'welcome_message' => 'Merhaba ben AI Asistan, sizin tercihlerinize göre ürün önerileri sunuyorum. Size nasıl yardımcı olabilirim?',
                    'language' => 'tr',
                    'enable_typing_indicator' => false,
                    'enable_sound_notifications' => false,
                    'is_active' => true
                ]
            );
            
            // Yeni özelleştirme alanlarını güncelle
            $widgetCustomization->update([
                'ai_name' => $request->ai_name ?? $widgetCustomization->ai_name,
                'welcome_message' => $request->welcome_message ?? $widgetCustomization->welcome_message,
                'welcome_message_custom' => $request->welcome_message_custom ?? $widgetCustomization->welcome_message_custom,
                'cargo_not_found_message' => $request->cargo_not_found_message ?? $widgetCustomization->cargo_not_found_message,
                'feature_disabled_message' => $request->feature_disabled_message ?? $widgetCustomization->feature_disabled_message,
                'error_message_template' => $request->error_message_template ?? $widgetCustomization->error_message_template,
                'order_not_found_message' => $request->order_not_found_message ?? $widgetCustomization->order_not_found_message,
                'theme_color' => $request->theme_color ?? $widgetCustomization->theme_color,
                'font_family' => $request->font_family ?? $widgetCustomization->font_family ?? 'Roboto',
                'primary_color' => $request->primary_color ?? $widgetCustomization->primary_color,
                'secondary_color' => $request->secondary_color ?? $widgetCustomization->secondary_color,
                'language' => $request->language ?? $widgetCustomization->language ?? 'tr',
                'custom_messages' => $request->custom_messages ?? $widgetCustomization->custom_messages,
                'notification_message' => $request->notification_message ?? $widgetCustomization->notification_message,
                'rate_limit_per_minute' => $request->rate_limit_per_minute ?? $widgetCustomization->rate_limit_per_minute,
                'api_timeout_seconds' => $request->api_timeout_seconds ?? $widgetCustomization->api_timeout_seconds,
                'max_retry_attempts' => $request->max_retry_attempts ?? $widgetCustomization->max_retry_attempts,
                'enable_typing_indicator' => $request->boolean('enable_typing_indicator', $widgetCustomization->enable_typing_indicator ?? false),
                'enable_sound_notifications' => $request->boolean('enable_sound_notifications', $widgetCustomization->enable_sound_notifications ?? false),
            ]);
            
            // Sipariş durumu endpoint'ini güncelle/oluştur
            if ($request->siparis_durumu_endpoint) {
                WidgetActions::updateOrCreate(
                    [
                        'widget_customization_id' => $widgetCustomization->id,
                        'type' => 'siparis_durumu_endpoint'
                    ],
                    [
                        'endpoint' => $request->siparis_durumu_endpoint,
                        'http_action' => 'GET',
                        'is_active' => $request->has('siparis_active_toggle'),
                        'display_name' => 'Sipariş Durumu API'
                    ]
                );
            } else {
                // Eğer endpoint verisi yoksa, mevcut kaydı pasif yap
                WidgetActions::where('widget_customization_id', $widgetCustomization->id)
                    ->where('type', 'siparis_durumu_endpoint')
                    ->update(['is_active' => false]);
            }
            
            // Kargo durumu endpoint'ini güncelle/oluştur
            if ($request->kargo_durumu_endpoint) {
                WidgetActions::updateOrCreate(
                    [
                        'widget_customization_id' => $widgetCustomization->id,
                        'type' => 'kargo_durumu_endpoint'
                    ],
                    [
                        'endpoint' => $request->kargo_durumu_endpoint,
                        'http_action' => 'GET',
                        'is_active' => $request->has('kargo_active_toggle'),
                        'display_name' => 'Kargo Durumu API'
                    ]
                );
            } else {
                // Eğer endpoint verisi yoksa, mevcut kaydı pasif yap
                WidgetActions::where('widget_customization_id', $widgetCustomization->id)
                    ->where('type', 'kargo_durumu_endpoint')
                    ->update(['is_active' => false]);
            }
            
            // Notification Widget ayarlarını güncelle/oluştur
            if ($request->has('notification_active') || $request->notification_message_text) {
                $projectId = $request->project_id;
                if ($projectId) {
                    // Get or create site for this project
                    $site = Site::firstOrCreate(
                        ['project_id' => $projectId],
                        [
                            'name' => 'Default Site',
                            'domain' => 'example.com',
                            'is_active' => true
                        ]
                    );
                    
                    NotificationWidgetSetting::updateOrCreate(
                        ['site_id' => $site->id],
                        [
                            'message_text' => $request->notification_message_text ?? 'Sizin için kampanyamız var!',
                            'is_active' => $request->has('notification_active'),
                            'color_theme' => 'purple', // Fixed purple theme
                            'display_duration' => ($request->notification_display_duration ?? 5) * 1000, // Convert to milliseconds
                            'animation_type' => $request->notification_animation_type ?? 'fade-in',
                            'show_close_button' => $request->has('notification_show_close_button'),
                            'redirect_url' => null // No redirect URL
                        ]
                    );
                } else {
                    // Use the first available site
                    $site = Site::first();
                    if ($site) {
                        NotificationWidgetSetting::updateOrCreate(
                            ['site_id' => $site->id],
                            [
                                'message_text' => $request->notification_message_text ?? 'Sizin için kampanyamız var!',
                                'is_active' => $request->has('notification_active'),
                                'color_theme' => 'purple', // Fixed purple theme
                                'display_duration' => ($request->notification_display_duration ?? 5) * 1000, // Convert to milliseconds
                                'animation_type' => $request->notification_animation_type ?? 'fade-in',
                                'show_close_button' => $request->has('notification_show_close_button'),
                                'redirect_url' => null // No redirect URL
                            ]
                        );
                    }
                }
            }
            
            // Tüm widget actions'ları al
            $widgetActions = $widgetCustomization->widgetActions;
            
            return response()->json([
                'success' => true,
                'message' => 'Widget ayarları başarıyla kaydedildi',
                'data' => [
                    'widgetCustomization' => $widgetCustomization,
                    'widgetActions' => $widgetActions
                ]
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Widget ayarları kaydedilirken hata oluştu: ' . $e->getMessage()
            ], 500);
        }
    }

    public function testEndpoint(Request $request): JsonResponse
    {
        try {
            $endpoint = $request->endpoint;
            $type = $request->type; // 'siparis' veya 'kargo'
            
            if (!$endpoint) {
                return response()->json([
                    'success' => false,
                    'message' => 'Endpoint belirtilmedi'
                ], 400);
            }
            
            // Test number ekle
            $testNumber = $request->tracking_number ?? 'TEST123456789';
            $testUrl = $endpoint;
            if ($testNumber) {
                $separator = strpos($endpoint, '?') !== false ? '&' : '?';
                if ($type === 'kargo') {
                    $testUrl = $endpoint . $separator . 'track=' . urlencode($testNumber);
                } elseif ($type === 'siparis') {
                    $testUrl = $endpoint . $separator . 'order=' . urlencode($testNumber);
                }
            }
            
            // Test request gönder
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $testUrl);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HEADER, true);
            curl_setopt($ch, CURLOPT_NOBODY, false);
            curl_setopt($ch, CURLOPT_TIMEOUT, 10);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            
            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $error = curl_error($ch);
            curl_close($ch);
            
            if ($error) {
                return response()->json([
                    'success' => false,
                    'message' => 'CURL Hatası: ' . $error,
                    'data' => [
                        'status' => 0,
                        'endpoint' => $testUrl,
                        'type' => $type,
                        'tracking_number' => $testNumber
                    ]
                ]);
            }
            
            if ($httpCode >= 200 && $httpCode < 300) {
                return response()->json([
                    'success' => true,
                    'message' => 'Endpoint başarıyla test edildi',
                    'data' => [
                        'status' => $httpCode,
                        'endpoint' => $testUrl,
                        'type' => $type,
                        'tracking_number' => $testNumber,
                    ]
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Endpoint test edilemedi: HTTP ' . $httpCode,
                    'data' => [
                        'status' => $httpCode,
                        'endpoint' => $testUrl,
                        'type' => $type,
                        'tracking_number' => $testNumber,
                    ]
                ]);
            }
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Endpoint test edilirken hata oluştu: ' . $e->getMessage()
            ], 500);
        }
    }
}
