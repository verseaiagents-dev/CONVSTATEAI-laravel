<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\WidgetCustomization;
use App\Models\WidgetActions;

class WidgetCustomizationController extends Controller
{
    /**
     * Show the main widget customization page
     */
    public function index(Request $request)
    {
        // Clean URL parameters and redirect to clean URL
        if ($request->hasAny(['_token', 'ai_name', 'welcome_message', 'project_id'])) {
            return redirect()->route('dashboard.widget-customization');
        }
        
        return view('dashboard.widget-customization-new');
    }

    /**
     * Get container content via AJAX
     */
    public function getContainer($container)
    {
        try {
            $view = "dashboard.widget-customization.{$container}";
            
            if (!view()->exists($view)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Container not found'
                ], 404);
            }
            
            $html = view($view)->render();
            
            return response()->json([
                'success' => true,
                'html' => $html
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error loading container: ' . $e->getMessage()
            ], 500);
        }
    }

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
                    'isOrderEnabled' => false
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
            'widget_position' => $customization->widget_position ?? 'right',
            'font_family' => $customization->font_family,
            'primary_color' => $customization->primary_color,
            'secondary_color' => $customization->secondary_color,
            'language' => $customization->language,
            'custom_messages' => $customization->custom_messages,
            'notification_message' => $customization->notification_message,
            'enable_typing_indicator' => $customization->enable_typing_indicator,
            'enable_sound_notifications' => $customization->enable_sound_notifications,
            'customization_data' => $customization->customization_data,
            'action_buttons' => $customization->getActionButtons(),
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
                'isOrderEnabled' => $isOrderActive
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
                'widget_position' => $request->widget_position ?? $widgetCustomization->widget_position ?? 'right',
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
                'action_buttons' => $request->action_buttons ?? $widgetCustomization->action_buttons,
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
     * Action buttons'ları güncelle
     */
    public function updateActionButtons(Request $request)
    {
        try {
            $user = $request->get('_user') ?? Auth::user();
            $project = $request->get('_project');
            
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Kullanıcı bulunamadı'
                ], 401);
            }
            
            $customization = WidgetCustomization::where('user_id', $user->id)
                ->when($project, function($query) use ($project) {
                    return $query->where('project_id', $project->id);
                })
                ->first();
            
            if (!$customization) {
                // Create a new widget customization if it doesn't exist
                $customization = WidgetCustomization::create([
                    'user_id' => $user->id,
                    'project_id' => $project ? $project->id : null,
                    'ai_name' => 'Convstate AI',
                    'welcome_message' => 'Merhaba ben Convstate AI, sizin tercihlerinize göre ürün önerileri sunuyorum. Size nasıl yardımcı olabilirim?',
                    'is_active' => true
                ]);
            }
            
            $actionButtons = $request->input('action_buttons', []);
            
            // Action buttons'ları doğrula
            $validatedButtons = $this->validateActionButtons($actionButtons);
            
            $customization->action_buttons = $validatedButtons;
            $customization->save();
            
            return response()->json([
                'success' => true,
                'message' => 'Action buttons başarıyla güncellendi',
                'data' => [
                    'action_buttons' => $customization->getActionButtons()
                ]
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Action buttons güncellenirken hata oluştu: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Action button'ı aktif/pasif yap
     */
    public function toggleActionButton(Request $request)
    {
        try {
            $user = $request->get('_user') ?? Auth::user();
            $project = $request->get('_project');
            
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Kullanıcı bulunamadı'
                ], 401);
            }
            
            $customization = WidgetCustomization::where('user_id', $user->id)
                ->when($project, function($query) use ($project) {
                    return $query->where('project_id', $project->id);
                })
                ->first();
            
            if (!$customization) {
                return response()->json([
                    'success' => false,
                    'message' => 'Widget özelleştirmesi bulunamadı'
                ], 404);
            }
            
            $buttonId = $request->input('button_id');
            $enabled = $request->input('enabled', true);
            
            if (!$buttonId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Button ID gerekli'
                ], 400);
            }
            
            $result = $customization->toggleActionButton($buttonId, $enabled);
            
            if ($result) {
                return response()->json([
                    'success' => true,
                    'message' => 'Action button başarıyla güncellendi',
                    'data' => [
                        'action_buttons' => $customization->getActionButtons()
                    ]
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Button bulunamadı'
                ], 404);
            }
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Action button güncellenirken hata oluştu: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Action button metnini güncelle
     */
    public function updateActionButtonText(Request $request)
    {
        try {
            $user = $request->get('_user') ?? Auth::user();
            $project = $request->get('_project');
            
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Kullanıcı bulunamadı'
                ], 401);
            }
            
            $customization = WidgetCustomization::where('user_id', $user->id)
                ->when($project, function($query) use ($project) {
                    return $query->where('project_id', $project->id);
                })
                ->first();
            
            if (!$customization) {
                return response()->json([
                    'success' => false,
                    'message' => 'Widget özelleştirmesi bulunamadı'
                ], 404);
            }
            
            $buttonId = $request->input('button_id');
            $newText = $request->input('text');
            
            if (!$buttonId || !$newText) {
                return response()->json([
                    'success' => false,
                    'message' => 'Button ID ve text gerekli'
                ], 400);
            }
            
            $result = $customization->updateActionButtonText($buttonId, $newText);
            
            if ($result) {
                return response()->json([
                    'success' => true,
                    'message' => 'Action button metni başarıyla güncellendi',
                    'data' => [
                        'action_buttons' => $customization->getActionButtons()
                    ]
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Button bulunamadı'
                ], 404);
            }
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Action button metni güncellenirken hata oluştu: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Action buttons'ları doğrula
     */
    private function validateActionButtons(array $actionButtons): array
    {
        $validatedButtons = [];
        
        foreach ($actionButtons as $button) {
            if (!isset($button['id']) || !isset($button['text']) || !isset($button['action'])) {
                continue; // Geçersiz button'ı atla
            }
            
            $validatedButtons[] = [
                'id' => $button['id'],
                'text' => $button['text'],
                'action' => $button['action'],
                'enabled' => $button['enabled'] ?? true,
                'order' => $button['order'] ?? 1
            ];
        }
        
        return $validatedButtons;
    }

    /**
     * Mesajlardaki placeholder'ları gerçek değerlerle değiştir
     */
    private function replacePlaceholders(string $message, string $aiName): string
    {
        return str_replace('{{ai_name}}', $aiName, $message);
    }

    /**
     * Get AI settings
     */
    public function getAISettings()
    {
        try {
            $user = Auth::user();
            $customization = WidgetCustomization::where('user_id', $user->id)->first();
            
            $data = [
                'ai_name' => $customization->ai_name ?? 'Convstate AI',
                'welcome_message' => $customization->welcome_message ?? 'Merhaba! Size nasıl yardımcı olabilirim?'
            ];
            
            return response()->json([
                'success' => true,
                'data' => $data
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'AI ayarları yüklenirken hata oluştu: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Save AI settings
     */
    public function saveAISettings(Request $request)
    {
        try {
            $user = Auth::user();
            $customization = WidgetCustomization::where('user_id', $user->id)->first();
            
            if (!$customization) {
                $customization = new WidgetCustomization();
                $customization->user_id = $user->id;
            }
            
            $customization->ai_name = $request->ai_name;
            $customization->welcome_message = $request->welcome_message;
            $customization->save();
            
            return response()->json([
                'success' => true,
                'message' => 'AI ayarları başarıyla kaydedildi'
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'AI ayarları kaydedilirken hata oluştu: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get action triggers data
     */
    public function getActionTriggers()
    {
        try {
            $user = Auth::user();
            $customization = WidgetCustomization::where('user_id', $user->id)->first();
            
            // Default buttons
            $defaultButtons = [
                ['id' => 'random_product', 'text' => 'Rastgele bir ürün öner.', 'enabled' => true],
                ['id' => 'cargo_tracking', 'text' => 'Kargom nerede?', 'enabled' => true],
                ['id' => 'order_tracking', 'text' => 'Siparişim nerede?', 'enabled' => true]
            ];
            
            // Custom buttons (from database)
            $customButtons = [];
            if ($customization && $customization->custom_buttons) {
                // Handle both array and JSON string cases
                if (is_array($customization->custom_buttons)) {
                    $customButtons = $customization->custom_buttons;
                } elseif (is_string($customization->custom_buttons)) {
                    $customButtons = json_decode($customization->custom_buttons, true) ?? [];
                }
            }
            
            // API settings
            $apiSettings = [
                'siparis_durumu_endpoint' => null,
                'kargo_durumu_endpoint' => null
            ];
            
            if ($customization) {
                $siparisAction = $customization->widgetActions()->where('type', 'siparis_durumu_endpoint')->first();
                $kargoAction = $customization->widgetActions()->where('type', 'kargo_durumu_endpoint')->first();
                
                if ($siparisAction) {
                    $apiSettings['siparis_durumu_endpoint'] = $siparisAction->endpoint;
                }
                if ($kargoAction) {
                    $apiSettings['kargo_durumu_endpoint'] = $kargoAction->endpoint;
                }
            }
            
            return response()->json([
                'success' => true,
                'data' => [
                    'defaultButtons' => $defaultButtons,
                    'customButtons' => $customButtons,
                    'apiSettings' => $apiSettings
                ]
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Eylem tetikleyicileri yüklenirken hata oluştu: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Toggle default button
     */
    public function toggleDefaultButton(Request $request)
    {
        try {
            $user = Auth::user();
            $customization = WidgetCustomization::where('user_id', $user->id)->first();
            
            if (!$customization) {
                $customization = new WidgetCustomization();
                $customization->user_id = $user->id;
            }
            
            $buttonId = $request->buttonId;
            $enabled = $request->enabled;
            
            // Update action buttons
            $actionButtons = $customization->action_buttons ?? $customization->getDefaultActionButtons();
            
            foreach ($actionButtons as &$button) {
                if ($button['id'] === $buttonId) {
                    $button['enabled'] = $enabled;
                    break;
                }
            }
            
            $customization->action_buttons = $actionButtons;
            $customization->save();
            
            return response()->json([
                'success' => true,
                'message' => 'Button durumu güncellendi'
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Button durumu güncellenirken hata oluştu: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Toggle custom button
     */
    public function toggleCustomButton(Request $request)
    {
        try {
            $user = Auth::user();
            $customization = WidgetCustomization::where('user_id', $user->id)->first();
            
            if (!$customization) {
                return response()->json([
                    'success' => false,
                    'message' => 'Customization bulunamadı'
                ], 404);
            }
            
            $buttonId = $request->buttonId;
            $enabled = $request->enabled;
            
            // Update custom buttons
            if (is_array($customization->custom_buttons)) {
                $customButtons = $customization->custom_buttons;
            } else {
                $customButtons = json_decode($customization->custom_buttons, true) ?? [];
            }
            
            foreach ($customButtons as &$button) {
                if ($button['id'] == $buttonId) {
                    $button['enabled'] = $enabled;
                    break;
                }
            }
            
            $customization->custom_buttons = $customButtons;
            $customization->save();
            
            return response()->json([
                'success' => true,
                'message' => 'Custom button durumu güncellendi'
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Custom button durumu güncellenirken hata oluştu: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Add custom button
     */
    public function addCustomButton(Request $request)
    {
        try {
            $user = Auth::user();
            $customization = WidgetCustomization::where('user_id', $user->id)->first();
            
            if (!$customization) {
                $customization = new WidgetCustomization();
                $customization->user_id = $user->id;
            }
            
            if (is_array($customization->custom_buttons)) {
                $customButtons = $customization->custom_buttons;
            } else {
                $customButtons = json_decode($customization->custom_buttons, true) ?? [];
            }
            
            $newButton = [
                'id' => uniqid(),
                'text' => $request->button_text,
                'intent_name' => $request->intent_name,
                'http_method' => $request->http_method,
                'api_endpoint' => $request->api_endpoint,
                'enabled' => true,
                'created_at' => now()
            ];
            
            $customButtons[] = $newButton;
            $customization->custom_buttons = $customButtons;
            $customization->save();
            
            return response()->json([
                'success' => true,
                'message' => 'Custom button başarıyla eklendi'
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Custom button eklenirken hata oluştu: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Save API settings
     */
    public function saveAPISettings(Request $request)
    {
        try {
            $user = Auth::user();
            $customization = WidgetCustomization::where('user_id', $user->id)->first();
            
            if (!$customization) {
                $customization = new WidgetCustomization();
                $customization->user_id = $user->id;
            }
            
            // Save sipariş durumu endpoint
            if ($request->siparis_durumu_endpoint) {
                WidgetActions::updateOrCreate(
                    [
                        'widget_customization_id' => $customization->id,
                        'type' => 'siparis_durumu_endpoint'
                    ],
                    [
                        'endpoint' => $request->siparis_durumu_endpoint,
                        'http_action' => 'GET',
                        'is_active' => true,
                        'display_name' => 'Sipariş Durumu API'
                    ]
                );
            }
            
            // Save kargo durumu endpoint
            if ($request->kargo_durumu_endpoint) {
                WidgetActions::updateOrCreate(
                    [
                        'widget_customization_id' => $customization->id,
                        'type' => 'kargo_durumu_endpoint'
                    ],
                    [
                        'endpoint' => $request->kargo_durumu_endpoint,
                        'http_action' => 'GET',
                        'is_active' => true,
                        'display_name' => 'Kargo Durumu API'
                    ]
                );
            }
            
            return response()->json([
                'success' => true,
                'message' => 'API ayarları başarıyla kaydedildi'
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'API ayarları kaydedilirken hata oluştu: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get API endpoints data
     */
    public function getAPIEndpoints()
    {
        try {
            $user = Auth::user();
            $customization = WidgetCustomization::where('user_id', $user->id)->first();
            
            $customButtons = [];
            if ($customization && $customization->custom_buttons) {
                if (is_array($customization->custom_buttons)) {
                    $customButtons = $customization->custom_buttons;
                } else {
                    $customButtons = json_decode($customization->custom_buttons, true) ?? [];
                }
            }
            
            return response()->json([
                'success' => true,
                'data' => [
                    'customButtons' => $customButtons
                ]
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'API endpoints yüklenirken hata oluştu: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete custom button
     */
    public function deleteCustomButton(Request $request)
    {
        try {
            $user = Auth::user();
            $customization = WidgetCustomization::where('user_id', $user->id)->first();
            
            if (!$customization) {
                return response()->json([
                    'success' => false,
                    'message' => 'Customization bulunamadı'
                ], 404);
            }
            
            $buttonId = $request->buttonId;
            if (is_array($customization->custom_buttons)) {
                $customButtons = $customization->custom_buttons;
            } else {
                $customButtons = json_decode($customization->custom_buttons, true) ?? [];
            }
            
            $customButtons = array_filter($customButtons, function($button) use ($buttonId) {
                return $button['id'] != $buttonId;
            });
            
            $customization->custom_buttons = array_values($customButtons);
            $customization->save();
            
            return response()->json([
                'success' => true,
                'message' => 'Custom button başarıyla silindi'
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Custom button silinirken hata oluştu: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get embed script
     */
    public function getEmbedScript()
    {
        try {
            $user = Auth::user();
            $customization = WidgetCustomization::where('user_id', $user->id)->first();
            
            $projectId = request('project_id') ?? '1';
            $apiUrl = config('app.url');
            
            $script = "
<!-- ConvStateAI Widget -->
<script src=\"{$apiUrl}/embed/convstateai.min.js\"></script>
<script>
    window.convstateaiConfig = {
        projectId: \"{$projectId}\",
        customizationToken: \"{$user->personal_token}\",
        apiUrl: \"{$apiUrl}/api\",
        debug: false
    };
</script>
            ";
            
            return response()->json([
                'success' => true,
                'data' => [
                    'script' => trim($script),
                    'position' => 'bottom-right',
                    'theme' => 'default',
                    'debug' => false
                ]
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Embed script yüklenirken hata oluştu: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Test widget
     */
    public function testWidget()
    {
        return view('dashboard.widget-customization.test-widget');
    }

    /**
     * Test API endpoint
     */
    public function testEndpoint(Request $request)
    {
        try {
            $endpoint = $request->endpoint;
            
            if (!$endpoint) {
                return response()->json([
                    'success' => false,
                    'message' => 'Endpoint URL gerekli'
                ], 400);
            }
            
            // Security check - no convstateai.com domain
            if (strpos($endpoint, 'convstateai.com') !== false) {
                return response()->json([
                    'success' => false,
                    'message' => 'Bu URL ile işlem yapılamaz'
                ], 400);
            }
            
            // Test endpoint with GET request
            $response = \Http::timeout(10)->get($endpoint);
            
            return response()->json([
                'success' => true,
                'message' => 'Endpoint başarıyla test edildi (Status: ' . $response->status() . ')'
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Endpoint test hatası: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Add multiple custom buttons
     */
    public function addMultipleCustomButtons(Request $request)
    {
        try {
            $user = Auth::user();
            $buttons = $request->buttons;
            
            if (!$buttons || !is_array($buttons)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Button verileri gerekli'
                ], 400);
            }
            
            $customization = WidgetCustomization::where('user_id', $user->id)->first();
            
            if (!$customization) {
                $customization = new WidgetCustomization();
                $customization->user_id = $user->id;
                $customization->custom_buttons = [];
            }
            
            $existingButtons = $customization->custom_buttons ?? [];
            
            foreach ($buttons as $button) {
                // Security check - no convstateai.com domain
                if (strpos($button['endpoint'], 'convstateai.com') !== false) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Bu URL ile işlem yapılamaz: ' . $button['endpoint']
                    ], 400);
                }
                
                $newButton = [
                    'id' => uniqid(),
                    'name' => $button['name'],
                    'intent' => $button['intent'],
                    'endpoint' => $button['endpoint'],
                    'method' => $button['method'] ?? 'GET',
                    'description' => $button['description'] ?? '',
                    'is_active' => true,
                    'created_at' => now()->toISOString()
                ];
                
                $existingButtons[] = $newButton;
            }
            
            $customization->custom_buttons = $existingButtons;
            $customization->save();
            
            return response()->json([
                'success' => true,
                'message' => count($buttons) . ' custom button başarıyla eklendi'
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Custom button\'lar eklenirken hata oluştu: ' . $e->getMessage()
            ], 500);
        }
    }
}
