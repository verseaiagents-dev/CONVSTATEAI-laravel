<?php

namespace App\Http\Controllers;

use App\Models\NotificationWidgetSetting;
use App\Models\Site;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class NotificationWidgetController extends Controller
{
    /**
     * Get notification widget settings for a site
     */
    public function getSettings(Request $request): JsonResponse
    {
        $projectId = $request->get('project_id', 1);
        
        $settings = NotificationWidgetSetting::where('project_id', $projectId)
            ->active()
            ->first();

        if (!$settings) {
            return response()->json([
                'success' => false,
                'message' => 'Notification widget settings not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'message_text' => $settings->message_text,
                'color_theme' => $settings->color_theme,
                'display_duration' => $settings->display_duration,
                'animation_type' => $settings->animation_type,
                'show_close_button' => $settings->show_close_button,
                'redirect_url' => $settings->redirect_url,
                'color_theme_css' => $settings->getColorThemeCss()
            ]
        ]);
    }

    /**
     * Update notification widget settings
     */
    public function updateSettings(Request $request): JsonResponse
    {
        $request->validate([
            'site_id' => 'required|exists:sites,id',
            'message_text' => 'required|string|max:255',
            'is_active' => 'boolean',
            'color_theme' => 'required|in:purple,blue,green,orange',
            'display_duration' => 'required|integer|min:1000|max:30000',
            'animation_type' => 'required|in:fade-in,slide-in,bounce',
            'show_close_button' => 'boolean',
            'redirect_url' => 'nullable|url'
        ]);

        $settings = NotificationWidgetSetting::updateOrCreate(
            ['project_id' => $request->project_id],
            $request->only([
                'message_text',
                'is_active',
                'color_theme',
                'display_duration',
                'animation_type',
                'show_close_button',
                'redirect_url'
            ])
        );

        return response()->json([
            'success' => true,
            'message' => 'Notification widget settings updated successfully',
            'data' => $settings
        ]);
    }

    /**
     * Check if notification widget should be shown (first visit check)
     */
    public function checkAvailability(Request $request): JsonResponse
    {
        $projectId = $request->get('project_id');
        
        $settings = NotificationWidgetSetting::active()
            ->first();

        if (!$settings) {
            return response()->json([
                'success' => true,
                'has_notification' => false
            ]);
        }

        // Get WidgetCustomization data for AI name and notification message
        $widgetCustomization = \App\Models\WidgetCustomization::where('user_id', function($query) use ($projectId) {
            // Get user_id from project
            $query->select('user_id')
                  ->from('projects')
                  ->where('id', $projectId);
        })->first();

        // Get color theme CSS
        $colorThemeCss = $settings->getColorThemeCss();

        // Use custom notification message or default
        $messageText = $widgetCustomization->notification_message ?? $settings->message_text ?? 'Sizin için kampanyamız var!';
        
        // Use AI name from customization or default
        $aiName = $widgetCustomization->ai_name ?? 'CONVSTATEAI';

        return response()->json([
            'success' => true,
            'has_notification' => true,
            'data' => [
                'message_text' => $messageText,
                'ai_name' => $aiName,
                'color_theme' => $settings->color_theme,
                'display_duration' => $settings->display_duration,
                'animation_type' => $settings->animation_type,
                'show_close_button' => $settings->show_close_button,
                'redirect_url' => $settings->redirect_url,
                'color_theme_css' => $colorThemeCss
            ]
        ]);
    }
}