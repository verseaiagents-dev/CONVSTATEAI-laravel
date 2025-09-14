<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AIController;
use App\Http\Controllers\ConvStateAPI;
use App\Http\Controllers\KnowledgeBaseController;

// AI Helper API Routes
Route::prefix('ai')->group(function () {
    Route::post('/response', [AIController::class, 'response']);
    Route::post('/personalized', [AIController::class, 'personalizedResponse']);
    Route::get('/stats', [AIController::class, 'getStats']);
    Route::post('/test-quality', [AIController::class, 'testQuality']);
    Route::get('/test-connection', [AIController::class, 'testConnection']);
});



// Chat Routes
Route::post('/chat', [App\Http\Controllers\ConvStateAPI::class, 'chat'])->name('api.chat');

// Test Routes
Route::post('/add-token', function() {
    $userId = request('user_id');
    $tokens = request('tokens', 100);
    
    $user = \App\Models\User::find($userId);
    if (!$user) {
        return response()->json(['error' => 'User not found'], 404);
    }
    
    $usageToken = \App\Models\UsageToken::getActiveForUser($userId);
    if ($usageToken) {
        $usageToken->tokens_remaining += $tokens;
        $usageToken->tokens_total += $tokens;
        $usageToken->save();
    } else {
        $usageToken = new \App\Models\UsageToken();
        $usageToken->user_id = $userId;
        $usageToken->tokens_total = $tokens;
        $usageToken->tokens_remaining = $tokens;
        $usageToken->tokens_used = 0;
        $usageToken->reset_date = now()->addMonth();
        $usageToken->is_active = true;
        $usageToken->save();
    }
    
    return response()->json([
        'success' => true,
        'message' => 'Token added successfully',
        'tokens_remaining' => $usageToken->tokens_remaining,
        'tokens_total' => $usageToken->tokens_total
    ]);
});

Route::post('/exhaust-token', function() {
    $userId = request('user_id');
    
    $usageToken = \App\Models\UsageToken::getActiveForUser($userId);
    if (!$usageToken) {
        return response()->json(['error' => 'No usage token found'], 404);
    }
    
    $usageToken->tokens_remaining = 0;
    $usageToken->tokens_used = $usageToken->tokens_total;
    $usageToken->save();
    
    return response()->json([
        'success' => true,
        'message' => 'Token exhausted successfully',
        'tokens_remaining' => $usageToken->tokens_remaining,
        'tokens_used' => $usageToken->tokens_used
    ]);
});
Route::get('/chat/{session_id}',  [ConvStateAPI::class, 'getChatSession']);
Route::post('/chat/{session_id}/clear',  [ConvStateAPI::class, 'clearChatSession']);

// Action APIs
Route::post('/order-tracking', [App\Http\Controllers\ActionAPIsController::class, 'orderTracking'])->name('api.order-tracking');
Route::post('/cargo-tracking', [App\Http\Controllers\ActionAPIsController::class, 'cargoTracking'])->name('api.cargo-tracking');
Route::post('/add-to-cart', [App\Http\Controllers\ActionAPIsController::class, 'addToCart'])->name('api.add-to-cart');
Route::post('/product-detail', [App\Http\Controllers\ActionAPIsController::class, 'productDetail'])->name('api.product-detail');
Route::post('/price-inquiry', [App\Http\Controllers\ActionAPIsController::class, 'priceInquiry'])->name('api.price-inquiry');

// Widget tracking endpoint
Route::post('/widget/track', [App\Http\Controllers\AnalyticsController::class, 'trackWidgetEvent'])->name('api.widget.track');

// Event handling routes
Route::post('/feedback', [ConvStateAPI::class, 'handleFeedback']);
Route::post('/product-click', [ConvStateAPI::class, 'handleProductClick']);
Route::post('/cargo-tracking', [ConvStateAPI::class, 'handleCargoTracking']);
Route::post('/order-tracking', [ConvStateAPI::class, 'handleOrderTracking']);

// Enhanced Chat Session & Product Interaction Routes
Route::post('/product-interaction', [ConvStateAPI::class, 'handleProductInteraction']);
Route::get('/chat-session/{session_id}/analytics', [ConvStateAPI::class, 'getSessionAnalytics']);

Route::get('/intents/ai-generated',  [ConvStateAPI::class, 'getAIGeneratedIntents']);
Route::get('/intents/stats',  [ConvStateAPI::class, 'getIntentStats']);
Route::get('/categories',  [ConvStateAPI::class, 'getAllCategories']);
Route::get('/categories/{category}',  [ConvStateAPI::class, 'getCategoryDetails']);
Route::get('/categories/analysis/recommendations',  [ConvStateAPI::class, 'getCategoryRecommendations']);

// Knowledge Base API Routes
Route::prefix('knowledge-base')->middleware(['web'])->group(function () {
    Route::get('/', [KnowledgeBaseController::class, 'index']);
    Route::post('/upload', [KnowledgeBaseController::class, 'uploadFile']);
    Route::post('/fetch-url', [KnowledgeBaseController::class, 'fetchFromUrl']);
    Route::post('/search', [KnowledgeBaseController::class, 'search']);
    Route::get('/{id}', [KnowledgeBaseController::class, 'show']);
    Route::get('/{id}/detail', [KnowledgeBaseController::class, 'getDetail']);
    Route::get('/{id}/chunks', [KnowledgeBaseController::class, 'getChunks']);
    Route::post('/{id}/refresh-chunks', [KnowledgeBaseController::class, 'refreshChunks']);
    Route::delete('/{id}', [KnowledgeBaseController::class, 'destroy']);
    Route::post('/{id}/optimize-faq', [KnowledgeBaseController::class, 'optimizeFAQ']);
    
    // Field Mapping Routes
    Route::get('/{id}/detect-fields', [KnowledgeBaseController::class, 'detectFields']);
    Route::post('/{id}/save-mappings', [KnowledgeBaseController::class, 'saveFieldMappings']);
    Route::post('/{id}/field-mappings', [KnowledgeBaseController::class, 'getFieldMappings']);
    Route::post('/{id}/preview-data', [KnowledgeBaseController::class, 'previewTransformedData']);
    
    // Advanced Field Mapping Routes
    Route::post('/{id}/validate-data', [KnowledgeBaseController::class, 'validateData']);
    Route::post('/{id}/process-batch', [KnowledgeBaseController::class, 'processBatchData']);
    Route::get('/{id}/mapping-stats', [KnowledgeBaseController::class, 'getMappingStats']);
    Route::post('/{id}/export-data', [KnowledgeBaseController::class, 'exportTransformedData']);
    
    // Background processing status routes
    Route::get('/{id}/processing-status', [KnowledgeBaseController::class, 'getProcessingStatus']);
    Route::get('/processing/list', [KnowledgeBaseController::class, 'getProcessingList']);
    Route::post('/{id}/retry', [KnowledgeBaseController::class, 'retryProcessing']);
});

// Campaign API Routes
Route::prefix('campaigns')->group(function () {
    Route::get('/', [App\Http\Controllers\CampaignController::class, 'index']);
    Route::post('/', [App\Http\Controllers\CampaignController::class, 'store']);
    Route::get('/check-availability', [App\Http\Controllers\CampaignController::class, 'checkAvailability']);
    Route::get('/count/active', [App\Http\Controllers\CampaignController::class, 'getActiveCount']);
    Route::get('/category/{category}', [App\Http\Controllers\CampaignController::class, 'getByCategory']);
    Route::get('/{id}', [App\Http\Controllers\CampaignController::class, 'show']);
    Route::put('/{id}', [App\Http\Controllers\CampaignController::class, 'update']);
    Route::delete('/{id}', [App\Http\Controllers\CampaignController::class, 'destroy']);
});

// FAQ API Routes
Route::prefix('faqs')->group(function () {
    Route::get('/', [App\Http\Controllers\FAQController::class, 'index']);
    Route::post('/', [App\Http\Controllers\FAQController::class, 'store']);
    Route::get('/check-availability', [App\Http\Controllers\FAQController::class, 'checkAvailability']);
    Route::get('/search', [App\Http\Controllers\FAQController::class, 'search']);
    Route::get('/popular', [App\Http\Controllers\FAQController::class, 'getPopular']);
    Route::get('/category/{category}', [App\Http\Controllers\FAQController::class, 'getByCategory']);
    Route::get('/{id}', [App\Http\Controllers\FAQController::class, 'show']);
    Route::put('/{id}', [App\Http\Controllers\FAQController::class, 'update']);
    Route::delete('/{id}', [App\Http\Controllers\FAQController::class, 'destroy']);
    Route::post('/{id}/helpful', [App\Http\Controllers\FAQController::class, 'markAsHelpful']);
    Route::post('/{id}/not-helpful', [App\Http\Controllers\FAQController::class, 'markAsNotHelpful']);
});

// Dashboard API Routes (for CRUD operations)
Route::prefix('dashboard')->group(function () {
    // Campaign Management
    Route::post('/campaigns', [App\Http\Controllers\CampaignController::class, 'store']);
    Route::put('/campaigns/{id}', [App\Http\Controllers\CampaignController::class, 'update']);
    Route::delete('/campaigns/{id}', [App\Http\Controllers\CampaignController::class, 'destroy']);
    
    // FAQ Management
    Route::post('/faqs', [App\Http\Controllers\FAQController::class, 'store']);
    Route::put('/faqs/{id}', [App\Http\Controllers\FAQController::class, 'update']);
    Route::delete('/faqs/{id}', [App\Http\Controllers\FAQController::class, 'destroy']);
});

// Product Database Routes
Route::get('/products', [ConvStateAPI::class, 'getProductsFromDB']);
Route::get('/products/stats/categories', [ConvStateAPI::class, 'getCategoryStats']);
Route::get('/products/top-rated', [ConvStateAPI::class, 'getTopRatedProducts']);

// Analytics & Reporting
Route::prefix('analytics')->group(function () {
    Route::get('/real-time', [App\Http\Controllers\AnalyticsController::class, 'getRealTimeAnalytics']);
    Route::get('/export', [App\Http\Controllers\AnalyticsController::class, 'exportAnalytics']);
    Route::post('/custom-range', [App\Http\Controllers\AnalyticsController::class, 'getCustomRangeAnalytics']);
});

// Product Interaction Tracking
Route::post('/product-interaction', [ConvStateAPI::class, 'handleProductInteraction']);

// GDPR Compliance Routes
Route::prefix('gdpr')->group(function () {
    Route::get('/export/{sessionId}', [App\Http\Controllers\ConvStateAPI::class, 'exportUserData'])->name('gdpr.export');
    Route::post('/delete/{sessionId}', [App\Http\Controllers\ConvStateAPI::class, 'deleteUserData'])->name('gdpr.delete');
    Route::post('/anonymize/{sessionId}', [App\Http\Controllers\ConvStateAPI::class, 'anonymizeUserData'])->name('gdpr.anonymize');
    Route::get('/retention-summary', [App\Http\Controllers\ConvStateAPI::class, 'getDataRetentionSummary'])->name('gdpr.retention');
});

// Cargo Tracking Routes
Route::prefix('cargo')->group(function () {
    Route::get('/track/{trackingNumber}', [App\Http\Controllers\CargoTrackingController::class, 'trackCargo'])->name('cargo.track');
    Route::post('/track', [App\Http\Controllers\CargoTrackingController::class, 'trackCargoPost'])->name('cargo.track.post');
});

// Widget Embed Routes
Route::prefix('widget')->group(function () {
    Route::get('/embed-script', [App\Http\Controllers\WidgetEmbedController::class, 'generateScript']);
    Route::get('/project/{projectId}', [App\Http\Controllers\WidgetEmbedController::class, 'getProjectInfo']);
});

// Widget Customization - Public endpoint (no auth required)
Route::get('/widget-customization', [App\Http\Controllers\WidgetCustomizationController::class, 'getPublicCustomization']);

// Protected API Routes (with Project Auth)
Route::middleware(['project.auth'])->group(function () {
    Route::post('/chat', [App\Http\Controllers\ConvStateAPI::class, 'chat']);
    Route::post('/cargo/track', [App\Http\Controllers\CargoTrackingController::class, 'trackCargoPost']);
});

// === FUNNEL TEST ROUTES (No Auth Required) ===
Route::get('/funnel/test', [App\Http\Controllers\ConvStateAPI::class, 'testFunnelIntents']);
Route::post('/funnel/test-specific', [App\Http\Controllers\ConvStateAPI::class, 'testSpecificIntent']);
Route::get('/funnel/intents', [App\Http\Controllers\ConvStateAPI::class, 'listFunnelIntents']);

// === SESSION MONITORING ROUTES ===
Route::get('/session-monitoring/{sessionId}', [App\Http\Controllers\ChatSessionDashboardController::class, 'getSessionMonitoringData']);

// === CHAT HISTORY ROUTES ===
Route::get('/chat-history/{sessionId}', [App\Http\Controllers\ChatSessionDashboardController::class, 'getChatHistory']);
Route::delete('/chat-history/{sessionId}', [App\Http\Controllers\ChatSessionDashboardController::class, 'clearChatHistory']);

// === DASHBOARD REFRESH ROUTES ===
Route::get('/dashboard/chat-sessions/refresh', [App\Http\Controllers\ChatSessionDashboardController::class, 'refresh']);
Route::get('/dashboard/chat-sessions/{sessionId}/history', [App\Http\Controllers\ChatSessionDashboardController::class, 'getChatHistory']);

// Subscription API Routes (Simplified for manual assignment)
Route::prefix('subscription')->middleware(['auth:web'])->group(function () {
    Route::get('/usage', [App\Http\Controllers\SubscriptionController::class, 'getUsage']);
    Route::get('/plans', [App\Http\Controllers\SubscriptionController::class, 'plans']);
    Route::get('/billing-history', [App\Http\Controllers\SubscriptionController::class, 'billingHistory']);
});



// Notification Widget API Routes
Route::prefix('notification-widget')->group(function () {
    Route::get('/settings', [App\Http\Controllers\NotificationWidgetController::class, 'getSettings']);
    Route::get('/check-availability', [App\Http\Controllers\NotificationWidgetController::class, 'checkAvailability']);
    Route::post('/settings', [App\Http\Controllers\NotificationWidgetController::class, 'updateSettings']);
});


