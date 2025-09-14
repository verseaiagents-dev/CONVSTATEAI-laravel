<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\WidgetCustomization;
use App\Models\WidgetActions;

class WidgetCustomizationController extends Controller
{
    /**
     * Get widget customization data for current user
     */
    public function getCustomization()
    {
        $user = Auth::user();
        $customization = WidgetCustomization::where('user_id', $user->id)->first();
        $widgetActions = null;
        
        if ($customization) {
            $widgetActions = $customization->widgetActions;
        }
        
        if (!$customization) {
            // Return default values if no customization exists
            return response()->json([
                'success' => true,
                'data' => [
                    'widgetCustomization' => [
                        'ai_name' => 'Convstate AI',
                        'welcome_message' => 'Merhaba ben Convstate AI, sizin tercihlerinize göre ürün önerileri sunuyorum. Size nasıl yardımcı olabilirim?',
                        'customization_data' => null
                    ],
                    'widgetActions' => null
                ]
            ]);
        }
        
        return response()->json([
            'success' => true,
            'data' => [
                'widgetCustomization' => $customization,
                'widgetActions' => $widgetActions
            ]
        ]);
    }

 
    /**
     * Get public widget customization data (for React app)
     */
    public function getPublicCustomization(Request $request)
    {
        // Widget için public endpoint - authentication gerektirmez
        // Project ID'yi request'ten al (opsiyonel)
        $projectId = $request->input('project_id') ?? $request->header('X-Project-ID');
        
        // Project ID varsa ona göre filtrele, yoksa en son aktif olanı al
        $query = WidgetCustomization::where('is_active', true);
        
        if ($projectId) {
            $query->where('project_id', $projectId);
        }
        
        $customization = $query->orderBy('updated_at', 'desc')->first();
        
        if (!$customization) {
            // Return default values if no customization exists
            return response()->json([
                'success' => true,
                'data' => [
                    'widgetCustomization' => [
                        'ai_name' => 'Convstate AI',
                        'welcome_message' => 'Merhaba ben Convstate AI, sizin tercihlerinize göre ürün önerileri sunuyorum. Size nasıl yardımcı olabilirim?',
                        'customization_data' => null
                    ],
                    'apiEndpoints' => [
                        'cargoTracking' => null,
                        'orderTracking' => null
                    ],
                    'isCargoEnabled' => false,
                    'isOrderEnabled' => false,
                    'isCargoApiActive' => false,
                    'isOrderApiActive' => false,
                  
                ]
            ]);
        }
        
        // Sadece aktif endpoint'leri döndür
        $cargoEndpoint = $customization->getKargoDurumuEndpoint();
        $orderEndpoint = $customization->getSiparisDurumuEndpoint();
        
        // Endpoint'lerin aktif olup olmadığını kontrol et
        $isCargoActive = $customization->isKargoApiActive() && !is_null($cargoEndpoint);
        $isOrderActive = $customization->isSiparisApiActive() && !is_null($orderEndpoint);
        
        // Sadece gerekli alanları döndür (hassas bilgileri gizle)
        $publicCustomization = [
            'ai_name' => $customization->ai_name,
            'welcome_message' => $this->replacePlaceholders($customization->welcome_message, $customization->ai_name),
            'welcome_message_custom' => $customization->welcome_message_custom,
            'cargo_not_found_message' => $customization->cargo_not_found_message,
            'feature_disabled_message' => $customization->feature_disabled_message,
            'error_message_template' => $customization->error_message_template,
            'order_not_found_message' => $customization->order_not_found_message,
            'theme_color' => $customization->theme_color,
            'logo_url' => $customization->logo_url,
            'font_family' => $customization->font_family,
            'primary_color' => $customization->primary_color,
            'secondary_color' => $customization->secondary_color,
            'language' => $customization->language,
            'custom_messages' => $customization->custom_messages,
            'notification_message' => $customization->notification_message,
            'enable_typing_indicator' => $customization->enable_typing_indicator,
            'enable_sound_notifications' => $customization->enable_sound_notifications,
            'customization_data' => $customization->customization_data,
            'is_active' => $customization->is_active
        ];

        return response()->json([
            'success' => true,
            'data' => [
                'widgetCustomization' => $publicCustomization,
                'apiEndpoints' => [
                    'cargoTracking' => $isCargoActive ? $cargoEndpoint : null,
                    'orderTracking' => $isOrderActive ? $orderEndpoint : null
                ],
                'isCargoEnabled' => $isCargoActive,
                'isOrderEnabled' => $isOrderActive,
                'isCargoApiActive' => $isCargoActive,
                'isOrderApiActive' => $isOrderActive,
               
            ]
        ]);
    }

    /**
     * Store widget customization data
     */
    public function store(Request $request)
    {
        try {
            $user = Auth::user();
            
            // WidgetCustomization'ı bul veya oluştur (sadece güncelleme için)
            $widgetCustomization = WidgetCustomization::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'ai_name' => $request->ai_name ?? 'AI Asistan',
                    'welcome_message' => $request->welcome_message ?? 'Merhaba ben AI Asistan, sizin tercihlerinize göre ürün önerileri sunuyorum. Size nasıl yardımcı olabilirim?',
                    'is_active' => true
                ]
            );

            // Mevcut verileri güncelle
            $widgetCustomization->update([
                'project_id' => $request->project_id ?? $widgetCustomization->project_id,
                'ai_name' => $request->ai_name ?? $widgetCustomization->ai_name,
                'welcome_message' => $request->welcome_message ?? $widgetCustomization->welcome_message,
                'ai_personality' => $request->ai_personality ?? $widgetCustomization->ai_personality,
                'welcome_message_custom' => $request->welcome_message_custom ?? $widgetCustomization->welcome_message_custom,
                'cargo_not_found_message' => $request->cargo_not_found_message ?? $widgetCustomization->cargo_not_found_message,
                'feature_disabled_message' => $request->feature_disabled_message ?? $widgetCustomization->feature_disabled_message,
                'error_message_template' => $request->error_message_template ?? $widgetCustomization->error_message_template,
                'order_not_found_message' => $request->order_not_found_message ?? $widgetCustomization->order_not_found_message,
                'theme_color' => $request->theme_color ?? $widgetCustomization->theme_color,
                'logo_url' => $request->logo_url ?? $widgetCustomization->logo_url,
                'font_family' => $request->font_family ?? $widgetCustomization->font_family,
                'primary_color' => $request->primary_color ?? $widgetCustomization->primary_color,
                'secondary_color' => $request->secondary_color ?? $widgetCustomization->secondary_color,
                'language' => $request->language ?? $widgetCustomization->language,
                'custom_messages' => $request->custom_messages ?? $widgetCustomization->custom_messages,
                'rate_limit_per_minute' => $request->rate_limit_per_minute ?? $widgetCustomization->rate_limit_per_minute,
                'api_timeout_seconds' => $request->api_timeout_seconds ?? $widgetCustomization->api_timeout_seconds,
                'max_retry_attempts' => $request->max_retry_attempts ?? $widgetCustomization->max_retry_attempts,
                'enable_typing_indicator' => $request->has('enable_typing_indicator') ? $request->enable_typing_indicator : $widgetCustomization->enable_typing_indicator,
                'enable_sound_notifications' => $request->has('enable_sound_notifications') ? $request->enable_sound_notifications : $widgetCustomization->enable_sound_notifications,
                'customization_data' => $request->customization_data ?? $widgetCustomization->customization_data,
                'is_active' => $request->is_active ?? $widgetCustomization->is_active
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
                        'http_action' => $request->http_action ?? 'GET'
                    ]
                );
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
                        'http_action' => $request->http_action ?? 'GET'
                    ]
                );
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

    /**
     * Environment'a göre izin verilen protokolleri döndür
     */
    private function getAllowedProtocols(): array
    {
        $environment = app()->environment();
        
        switch ($environment) {
            case 'production':
                // Production'da sadece HTTPS
                return ['https://'];
                
            case 'staging':
                // Staging'de HTTPS + sınırlı localhost
                return [
                    'https://',
                    'http://localhost:3000', // Sadece React dev server
                    'http://127.0.0.1:3000'
                ];
                
            case 'local':
            case 'testing':
                // Development'ta localhost'a izin ver
                return [
                    'https://',
                    'http://localhost',
                    'http://127.0.0.1',
                    'http://localhost:3000',
                    'http://127.0.0.1:3000',
                    'http://localhost:8000',
                    'http://127.0.0.1:8000'
                ];
                
            default:
                // Güvenli varsayılan
                return ['https://'];
        }
    }

    /**
     * IP whitelist'i döndür
     */
    private function getIpWhitelist(): array
    {
        $environment = app()->environment();
        
        switch ($environment) {
            case 'production':
                // Production'da sadece belirli IP'ler
                return config('security.allowed_ips', []);
                
            case 'staging':
                // Staging'de sınırlı IP'ler
                return array_merge(
                    config('security.allowed_ips', []),
                    ['127.0.0.1', '::1'] // Localhost
                );
                
            case 'local':
            case 'testing':
                // Development'ta tüm IP'lere izin ver
                return ['*'];
                
            default:
                // Güvenli varsayılan
                return config('security.allowed_ips', []);
        }
    }

    /**
     * Mesajlardaki placeholder'ları gerçek değerlerle değiştir
     */
    private function replacePlaceholders(string $message, string $aiName): string
    {
        return str_replace('{{ai_name}}', $aiName, $message);
    }
}
