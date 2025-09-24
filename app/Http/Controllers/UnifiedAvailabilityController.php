<?php

namespace App\Http\Controllers;

use App\Models\Campaign;
use App\Models\FAQ;
use App\Models\NotificationWidgetSetting;
use App\Models\WidgetCustomization;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class UnifiedAvailabilityController extends Controller
{
    /**
     * Tüm availability kontrollerini tek bir endpoint'te toplar
     */
    public function checkAvailability(Request $request): JsonResponse
    {
        try {
            $projectId = $request->get('project_id');
            
            if (!$projectId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Project ID gerekli'
                ], 400);
            }

            // Paralel olarak tüm kontrolleri yap
            $results = $this->performAvailabilityChecks($projectId);

            return response()->json([
                'success' => true,
                'data' => $results,
                'message' => 'Tüm availability kontrolleri başarıyla tamamlandı'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Availability kontrolü sırasında hata oluştu: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Tüm availability kontrollerini gerçekleştirir
     */
    private function performAvailabilityChecks(int $projectId): array
    {
        $results = [];

        // 1. Campaigns availability check
        try {
            $campaignCount = Campaign::where('project_id', $projectId)
                ->where('is_active', true)
                ->count();

            $results['campaigns'] = [
                'has_campaigns' => $campaignCount > 0,
                'campaign_count' => $campaignCount,
                'success' => true
            ];
        } catch (\Exception $e) {
            $results['campaigns'] = [
                'has_campaigns' => false,
                'campaign_count' => 0,
                'success' => false,
                'error' => $e->getMessage()
            ];
        }

        // 2. FAQs availability check
        try {
            $faqCount = FAQ::where('project_id', $projectId)
                ->where('is_active', true)
                ->count();

            $results['faqs'] = [
                'has_faqs' => $faqCount > 0,
                'faq_count' => $faqCount,
                'success' => true
            ];
        } catch (\Exception $e) {
            $results['faqs'] = [
                'has_faqs' => false,
                'faq_count' => 0,
                'success' => false,
                'error' => $e->getMessage()
            ];
        }

        // 3. Notification Widget availability check
        try {
            $settings = NotificationWidgetSetting::active()->first();

            if (!$settings) {
                $results['notification_widget'] = [
                    'has_notification' => false,
                    'success' => true
                ];
            } else {
                // Get WidgetCustomization data for AI name and notification message
                $widgetCustomization = WidgetCustomization::where('user_id', function($query) use ($projectId) {
                    $query->select('user_id')
                          ->from('projects')
                          ->where('id', $projectId);
                })->first();

                // Get color theme CSS
                $colorThemeCss = $settings->getColorThemeCss();

                $results['notification_widget'] = [
                    'has_notification' => true,
                    'settings' => [
                        'message_text' => $settings->message_text,
                        'color_theme' => $settings->color_theme,
                        'display_duration' => $settings->display_duration,
                        'animation_type' => $settings->animation_type,
                        'show_close_button' => $settings->show_close_button,
                        'redirect_url' => $settings->redirect_url,
                        'color_theme_css' => $colorThemeCss
                    ],
                    'customization' => $widgetCustomization ? [
                        'ai_name' => $widgetCustomization->ai_name,
                        'notification_message' => $widgetCustomization->notification_message
                    ] : null,
                    'success' => true
                ];
            }
        } catch (\Exception $e) {
            $results['notification_widget'] = [
                'has_notification' => false,
                'success' => false,
                'error' => $e->getMessage()
            ];
        }

        return $results;
    }

    /**
     * Sadece belirli bir modülün availability'sini kontrol eder
     */
    public function checkSpecificAvailability(Request $request, string $module): JsonResponse
    {
        try {
            $projectId = $request->get('project_id');
            
            if (!$projectId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Project ID gerekli'
                ], 400);
            }

            $results = $this->performAvailabilityChecks($projectId);

            if (!isset($results[$module])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Geçersiz modül: ' . $module
                ], 400);
            }

            return response()->json([
                'success' => true,
                'data' => $results[$module],
                'message' => ucfirst($module) . ' availability kontrolü tamamlandı'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Availability kontrolü sırasında hata oluştu: ' . $e->getMessage()
            ], 500);
        }
    }
}
