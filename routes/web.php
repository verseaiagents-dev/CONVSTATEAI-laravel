<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\KnowledgeBaseController;
use App\Http\Controllers\ShopController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\Admin\KnowledgeBaseSettingsController;
use App\Http\Controllers\Admin\KnowledgeBasePromptsController;
use App\Http\Controllers\DemoRequestController;
use App\Http\Controllers\GiftDataController;
use App\Http\Controllers\HomeController;


Route::get('/', [HomeController::class, 'index'])->name('index');
Route::get('/home', [HomeController::class, 'index'])->name('home');

Route::get('/widget-customization', function () {
    return view('public.widget-customization');
})->name('widget-customization');

// Public Plan Selection
Route::get('/subscription/plans', [App\Http\Controllers\SubscriptionController::class, 'plans'])->name('subscription.plans');

// Widget Customization Assets - Public Access (moved to API routes for CORS support)

// Legal Pages
Route::get('/privacy-policy', function () {
    return view('privacy-policy');
})->name('privacy-policy');

// Chat Session Dashboard Routes
Route::get('/dashboard/chat-sessions', [App\Http\Controllers\ChatSessionDashboardController::class, 'index'])->name('dashboard.chat-sessions');
Route::get('/dashboard/chat-sessions/{session_id}', [App\Http\Controllers\ChatSessionDashboardController::class, 'show'])->name('dashboard.chat-sessions.show');
Route::get('/dashboard/chat-sessions/export', [App\Http\Controllers\ChatSessionDashboardController::class, 'export'])->name('dashboard.chat-sessions.export');

Route::get('/terms-of-service', function () {
    return view('terms-of-service');
})->name('terms-of-service');

Route::get('/cookies', function () {
    return view('cookies');
})->name('cookies');

// Language Routes
Route::post('/change-language', [App\Http\Controllers\LanguageController::class, 'changeLanguage'])->name('change-language');
Route::get('/current-language', [App\Http\Controllers\LanguageController::class, 'getCurrentLanguage'])->name('current-language');

// Auth Routes
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register']);
Route::get('/forgot-password', [AuthController::class, 'showForgotPassword'])->name('password.request');
Route::post('/forgot-password', [AuthController::class, 'forgotPassword'])->name('password.email');
Route::get('/reset-password/{token}', [AuthController::class, 'showResetPassword'])->name('password.reset');
Route::post('/reset-password', [AuthController::class, 'resetPassword'])->name('password.update');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Dashboard Routes (Protected)
Route::middleware(['auth'])->group(function () {
    Route::post('/dashboard/profile', [DashboardController::class, 'updateProfile'])->name('dashboard.profile.update');
    Route::post('/dashboard/profile/avatar', [DashboardController::class, 'updateAvatar'])->name('dashboard.profile.avatar.update');
    Route::post('/dashboard/profile/avatar/remove', [DashboardController::class, 'removeAvatar'])->name('dashboard.profile.avatar.remove');
    Route::get('/dashboard/settings', [DashboardController::class, 'settings'])->name('dashboard.settings');
    Route::post('/dashboard/password', [DashboardController::class, 'updatePassword'])->name('dashboard.password.update');
    
    // Subscription Routes
    Route::get('/dashboard/subscription', [App\Http\Controllers\SubscriptionController::class, 'index'])->name('dashboard.subscription.index');
    Route::post('/subscription/subscribe', [App\Http\Controllers\SubscriptionController::class, 'subscribe'])->name('subscription.subscribe');
    Route::post('/subscription/buy-tokens', [App\Http\Controllers\SubscriptionController::class, 'buyTokens'])->name('subscription.buy-tokens');
    Route::post('/subscription/cancel', [App\Http\Controllers\SubscriptionController::class, 'cancel'])->name('subscription.cancel');
    Route::get('/subscription/billing-history', [App\Http\Controllers\SubscriptionController::class, 'billingHistory'])->name('subscription.billing-history');
    
    // Expired Subscription Route
    Route::get('/subscription/expired', [App\Http\Controllers\SubscriptionController::class, 'expired'])->name('subscription.expired');
    
});

// Dashboard Routes (Protected)
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    // Projects Routes
    Route::get('/dashboard/projects', [App\Http\Controllers\ProjectsController::class, 'index'])->name('dashboard.projects');
    Route::get('/dashboard/projects/load-content', [App\Http\Controllers\ProjectsController::class, 'loadContent'])->name('dashboard.projects.load-content');
    Route::post('/dashboard/projects', [App\Http\Controllers\ProjectsController::class, 'store'])->name('dashboard.projects.store');
    Route::get('/dashboard/projects/{project}', [App\Http\Controllers\ProjectsController::class, 'show'])->name('dashboard.projects.show');
    Route::put('/dashboard/projects/{project}', [App\Http\Controllers\ProjectsController::class, 'update'])->name('dashboard.projects.update');
    Route::delete('/dashboard/projects/{project}', [App\Http\Controllers\ProjectsController::class, 'destroy'])->name('dashboard.projects.destroy');

    // Campaign Management
    Route::get('/dashboard/campaigns', [App\Http\Controllers\CampaignController::class, 'index'])->name('dashboard.campaigns.index');
    Route::post('/dashboard/campaigns', [App\Http\Controllers\CampaignController::class, 'store'])->name('dashboard.campaigns.store');
    Route::get('/dashboard/campaigns/{id}', [App\Http\Controllers\CampaignController::class, 'show'])->name('dashboard.campaigns.show');
    Route::put('/dashboard/campaigns/{id}', [App\Http\Controllers\CampaignController::class, 'update'])->name('dashboard.campaigns.update');
    Route::delete('/dashboard/campaigns/{id}', [App\Http\Controllers\CampaignController::class, 'destroy'])->name('dashboard.campaigns.destroy');
    
    // AI Campaign Suggestions
    Route::get('/dashboard/campaigns/products/list', [App\Http\Controllers\CampaignController::class, 'getProductsForCampaign'])->name('dashboard.campaigns.products');
    Route::post('/dashboard/campaigns/ai-suggestions', [App\Http\Controllers\CampaignController::class, 'generateAICampaignSuggestions'])->name('dashboard.campaigns.ai-suggestions');
    Route::post('/dashboard/campaigns/create-from-ai', [App\Http\Controllers\CampaignController::class, 'createFromAISuggestion'])->name('dashboard.campaigns.create-from-ai');
    Route::post('/dashboard/campaigns/create-multiple-from-ai', [App\Http\Controllers\CampaignController::class, 'createMultipleFromAISuggestions'])->name('dashboard.campaigns.create-multiple-from-ai');
    
    // FAQ Management
    Route::get('/dashboard/faqs', [App\Http\Controllers\FAQController::class, 'index'])->name('dashboard.faqs.index');
    
    // Actions Management
    Route::get('/dashboard/actions', [App\Http\Controllers\ActionsController::class, 'index'])->name('dashboard.actions.index');
    Route::get('/dashboard/actions/load-content', [App\Http\Controllers\ActionsController::class, 'loadContent'])->name('dashboard.actions.load-content');
    Route::post('/dashboard/actions/test-endpoint', [App\Http\Controllers\ActionsController::class, 'testEndpoint'])->name('dashboard.actions.test-endpoint');
    Route::post('/dashboard/actions/save', [App\Http\Controllers\ActionsController::class, 'saveActions'])->name('dashboard.actions.save');
    
    // Knowledge Base Routes
    Route::get('/dashboard/knowledge-base', [KnowledgeBaseController::class, 'index'])->name('dashboard.knowledge-base');
    Route::get('/dashboard/knowledge-base/load-content', [KnowledgeBaseController::class, 'loadContent'])->name('dashboard.knowledge-base.load-content');
    Route::post('/dashboard/knowledge-base/upload', [KnowledgeBaseController::class, 'uploadFile'])->name('dashboard.knowledge-base.upload');
    Route::post('/dashboard/knowledge-base/fetch-url', [KnowledgeBaseController::class, 'fetchFromUrl'])->name('dashboard.knowledge-base.fetch-url');
    Route::post('/dashboard/knowledge-base/refresh-images', [KnowledgeBaseController::class, 'refreshImageAnalysis'])->name('dashboard.knowledge-base.refresh-images');
    Route::get('/dashboard/knowledge-base/image-status', [KnowledgeBaseController::class, 'getImageAnalysisStatus'])->name('dashboard.knowledge-base.image-status');
    Route::delete('/dashboard/knowledge-base/{id}', [KnowledgeBaseController::class, 'destroy'])->name('dashboard.knowledge-base.destroy');
    Route::get('/dashboard/knowledge-base/{id}/detail', [KnowledgeBaseController::class, 'getDetail'])->name('dashboard.knowledge-base.detail');
    
    
    Route::get('/dashboard/analytics', [App\Http\Controllers\AnalyticsController::class, 'index'])->name('dashboard.analytics');
    
    // Widget Design
    Route::get('/dashboard/widget-design', [App\Http\Controllers\WidgetDesignController::class, 'index'])->name('dashboard.widget-design');
    Route::get('/dashboard/widget-design/load-content', [App\Http\Controllers\WidgetDesignController::class, 'loadContent'])->name('dashboard.widget-design.load-content');
    Route::post('/dashboard/widget-design/store', [App\Http\Controllers\WidgetDesignController::class, 'store'])->name('dashboard.widget-design.store');
    Route::post('/dashboard/widget-design/test-endpoint', [App\Http\Controllers\WidgetDesignController::class, 'testEndpoint'])->name('dashboard.widget-design.test-endpoint');
    
    // Widget Customization Routes
    Route::get('/dashboard/widget-customization', [App\Http\Controllers\WidgetCustomizationController::class, 'getCustomization'])->name('dashboard.widget-customization.get');
    Route::post('/dashboard/widget-customization', [App\Http\Controllers\WidgetCustomizationController::class, 'store'])->name('dashboard.widget-customization.store');
    
    // Personal Token Routes
    Route::get('/dashboard/personal-token', [App\Http\Controllers\PersonalTokenController::class, 'getTokenInfo'])->name('dashboard.personal-token.info');
    Route::post('/dashboard/personal-token/generate', [App\Http\Controllers\PersonalTokenController::class, 'generateToken'])->name('dashboard.personal-token.generate');
    Route::delete('/dashboard/personal-token/revoke', [App\Http\Controllers\PersonalTokenController::class, 'revokeToken'])->name('dashboard.personal-token.revoke');
    
    // Action APIs Routes (Temporarily disabled)
    // Route::get('/dashboard/action-apis', [App\Http\Controllers\ActionAPIsController::class, 'index'])->name('dashboard.action-apis');
    // Route::post('/dashboard/action-apis/endpoints', [App\Http\Controllers\ActionAPIsController::class, 'store'])->name('dashboard.action-apis.store');
    // Route::put('/dashboard/action-apis/endpoints/{id}', [App\Http\Controllers\ActionAPIsController::class, 'update'])->name('dashboard.action-apis.update');
    // Route::delete('/dashboard/action-apis/endpoints/{id}', [App\Http\Controllers\ActionAPIsController::class, 'destroy'])->name('dashboard.action-apis.destroy');
});

// Admin Routes (Protected)
Route::middleware(['auth', 'admin'])->group(function () {
    Route::get('/admin', [AdminController::class, 'index'])->name('admin.dashboard');
    Route::get('/admin/profile', [AdminController::class, 'profile'])->name('admin.profile');
    Route::get('/admin/settings', [AdminController::class, 'settings'])->name('admin.settings');
    // User Management Routes (Legacy - AdminController)
    Route::get('/admin/users/{id}', [AdminController::class, 'getUser'])->name('admin.users.get');
    Route::put('/admin/users/{id}', [AdminController::class, 'updateUser'])->name('admin.users.update.legacy');
    Route::post('/admin/users/{id}/toggle-admin', [AdminController::class, 'toggleAdmin'])->name('admin.users.toggle-admin');
    Route::delete('/admin/users/{id}', [AdminController::class, 'deleteUser'])->name('admin.users.delete');
    
    Route::get('/admin/analytics', [AdminController::class, 'analytics'])->name('admin.analytics');
Route::get('/admin/analytics/load-content', [AdminController::class, 'loadAnalyticsContent'])->name('admin.analytics.load-content');
    
    // Plans Management
    Route::resource('admin/plans', \App\Http\Controllers\Admin\PlanController::class)->names([
        'index' => 'admin.plans.index',
        'create' => 'admin.plans.create',
        'store' => 'admin.plans.store',
        'show' => 'admin.plans.show',
        'edit' => 'admin.plans.edit',
        'update' => 'admin.plans.update',
        'destroy' => 'admin.plans.destroy',
    ]);
    
    // Payment Settings
    Route::get('/admin/payment-settings', function () {
        return view('admin.payment-settings.index');
    })->name('admin.payment-settings.index');
    
    // Subscriptions Management
    Route::resource('admin/subscriptions', \App\Http\Controllers\Admin\SubscriptionController::class)->names([
        'index' => 'admin.subscriptions.index',
        'create' => 'admin.subscriptions.create',
        'store' => 'admin.subscriptions.store',
        'show' => 'admin.subscriptions.show',
        'edit' => 'admin.subscriptions.edit',
        'update' => 'admin.subscriptions.update',
        'destroy' => 'admin.subscriptions.destroy',
    ]);
    
    // Plan Request Routes
    Route::post('/admin/subscriptions/requests/{planRequest}/approve', [\App\Http\Controllers\Admin\SubscriptionController::class, 'approveRequest'])->name('admin.subscriptions.requests.approve');
    Route::post('/admin/subscriptions/requests/{planRequest}/reject', [\App\Http\Controllers\Admin\SubscriptionController::class, 'rejectRequest'])->name('admin.subscriptions.requests.reject');
    
    // User Plan History Route
    Route::get('/admin/subscriptions/user/{user}/history', [\App\Http\Controllers\Admin\SubscriptionController::class, 'getUserPlanHistory'])->name('admin.subscriptions.user.history');
    
    // VIP Token Management Route
    Route::post('/admin/subscriptions/vip-token', [\App\Http\Controllers\Admin\SubscriptionController::class, 'manageVipToken'])->name('admin.subscriptions.vip-token');
    
    // User Management with Subscriptions and Requests
    Route::get('/admin/users', [\App\Http\Controllers\Admin\UserController::class, 'index'])->name('admin.users.index');
    Route::post('/admin/users/subscriptions', [\App\Http\Controllers\Admin\UserController::class, 'storeSubscription'])->name('admin.users.subscriptions.store');
    Route::put('/admin/users/subscriptions/{subscription}', [\App\Http\Controllers\Admin\UserController::class, 'updateSubscription'])->name('admin.users.subscriptions.update');
    Route::delete('/admin/users/subscriptions/{subscription}', [\App\Http\Controllers\Admin\UserController::class, 'destroySubscription'])->name('admin.users.subscriptions.destroy');
    Route::post('/admin/users/requests/{planRequest}/approve', [\App\Http\Controllers\Admin\UserController::class, 'approveRequest'])->name('admin.users.requests.approve');
    Route::post('/admin/users/requests/{planRequest}/reject', [\App\Http\Controllers\Admin\UserController::class, 'rejectRequest'])->name('admin.users.requests.reject');
    Route::get('/admin/users/{user}/plan-history', [\App\Http\Controllers\Admin\UserController::class, 'getUserPlanHistory'])->name('admin.users.plan-history');
    Route::get('/admin/users/{user}/plan-request-history', [\App\Http\Controllers\Admin\UserController::class, 'getUserPlanRequestHistory'])->name('admin.users.plan-request-history');
    
    // Mail Templates Management
    Route::resource('admin/mail-templates', \App\Http\Controllers\Admin\MailTemplateController::class)->names([
        'index' => 'admin.mail-templates.index',
        'create' => 'admin.mail-templates.create',
        'store' => 'admin.mail-templates.store',
        'show' => 'admin.mail-templates.show',
        'edit' => 'admin.mail-templates.edit',
        'update' => 'admin.mail-templates.update',
        'destroy' => 'admin.mail-templates.destroy',
    ]);
    
    // Mail Template Additional Routes
    Route::post('/admin/mail-templates/{id}/toggle-status', [\App\Http\Controllers\Admin\MailTemplateController::class, 'toggleStatus'])->name('admin.mail-templates.toggle-status');
    Route::post('/admin/mail-templates/{id}/duplicate', [\App\Http\Controllers\Admin\MailTemplateController::class, 'duplicate'])->name('admin.mail-templates.duplicate');
    Route::get('/admin/mail-templates/stats', [\App\Http\Controllers\Admin\MailTemplateController::class, 'stats'])->name('admin.mail-templates.stats');
    
    // API Settings Management
    Route::resource('admin/api-settings', \App\Http\Controllers\Admin\ApiSettingsController::class)->names([
        'index' => 'admin.api-settings.index',
        'create' => 'admin.api-settings.create',
        'store' => 'admin.api-settings.store',
        'show' => 'admin.api-settings.show',
        'edit' => 'admin.api-settings.edit',
        'update' => 'admin.api-settings.update',
        'destroy' => 'admin.api-settings.destroy',
    ]);
    
    // API Settings Additional Routes
    Route::post('/admin/api-settings/{apiSetting}/toggle-active', [\App\Http\Controllers\Admin\ApiSettingsController::class, 'toggleActive'])->name('admin.api-settings.toggle-active');
    
    // Knowledge Base Settings
    Route::get('/admin/knowledge-base-settings', [\App\Http\Controllers\Admin\KnowledgeBaseSettingsController::class, 'index'])->name('admin.knowledge-base-settings');
    
    // Knowledge Base Prompts
    Route::get('/admin/knowledge-base-prompts', [\App\Http\Controllers\Admin\KnowledgeBasePromptsController::class, 'index'])->name('admin.knowledge-base-prompts');
    
});

Route::get('/admin/demo-requests', [\App\Http\Controllers\Admin\DemoRequestController::class, 'index'])->name('admin.demo-requests.index');
Route::get('/admin/demo-requests/{demoRequest}', [\App\Http\Controllers\Admin\DemoRequestController::class, 'show'])->name('admin.demo-requests.show');
Route::post('/admin/demo-requests/{demoRequest}/status', [\App\Http\Controllers\Admin\DemoRequestController::class, 'updateStatus'])->name('admin.demo-requests.update-status');
// // Demo Request Routes
Route::post('/demo-request', [DemoRequestController::class, 'store'])->name('demo-request.storee');

    // Test route for payment integration
    Route::get('/test-payment/{plan}', function($planId) {
     $plan = \App\Models\Plan::find($planId);
     if (!$plan) {
         return 'Plan bulunamadı';
     }
     return view('test-payment', compact('plan'));
 })->name('test.payment');
 
 // Test route for manual plan assignment (development only)
 Route::post('/test-assign-plan/{orderId}', [App\Http\Controllers\PaymentController::class, 'testAssignPlan'])
     ->name('test.assign-plan');

  // Payment Routes - PayTR geçici olarak kapatıldı
  Route::middleware(['auth'])->group(function () {
     Route::get('/billing-form/{plan}', [App\Http\Controllers\PaymentController::class, 'billingForm'])->name('payment.billing-form');
     Route::post('/checkout/{plan}', [App\Http\Controllers\PaymentController::class, 'checkout'])->name('payment.checkout');
     Route::get('/payment/success', [App\Http\Controllers\PaymentController::class, 'success'])->name('payment.success');
     Route::get('/payment/fail', [App\Http\Controllers\PaymentController::class, 'fail'])->name('payment.fail');
 });




// Public Widget Customization API (for React app)
Route::get('/api/widget-customization', [App\Http\Controllers\WidgetCustomizationController::class, 'getPublicCustomization'])->name('api.widget-customization');

// Test ortamı için rastgele proje seçimi
Route::get('/api/random-project', [App\Http\Controllers\WidgetCustomizationController::class, 'getRandomProjectForTesting'])->name('api.random-project');

// Public Cargo Tracking API (for React app)
Route::get('/api/cargo/track/{trackingNumber}', [App\Http\Controllers\CargoTrackingController::class, 'trackCargo'])->name('api.cargo.track');
Route::post('/api/cargo/track', [App\Http\Controllers\CargoTrackingController::class, 'trackCargoPost'])->name('api.cargo.track.post');

    // Users Management Routes
    Route::resource('admin/users', \App\Http\Controllers\Admin\UserController::class)->names([
        'index' => 'admin.users.index',
        'create' => 'admin.users.create',
        'store' => 'admin.users.store',
        'show' => 'admin.users.show',
        'edit' => 'admin.users.edit',
        'update' => 'admin.users.update',
        'destroy' => 'admin.users.destroy',
    ]);
    
    Route::post('/admin/users/{user}/toggle-status', [\App\Http\Controllers\Admin\UserController::class, 'toggleStatus'])->name('admin.users.toggle-status');
    
    // Manual Plan Assignment Routes
    Route::get('/admin/users/{user}/assign-plan', [\App\Http\Controllers\Admin\UserPlanController::class, 'showAssignForm'])->name('admin.users.assign-plan');
    Route::post('/admin/users/{user}/assign-plan', [\App\Http\Controllers\Admin\UserPlanController::class, 'assignPlan'])->name('admin.users.assign-plan.store');
    Route::post('/admin/users/{user}/remove-plan', [\App\Http\Controllers\Admin\UserPlanController::class, 'removePlan'])->name('admin.users.remove-plan');
    Route::get('/admin/users/{user}/plan-info', [\App\Http\Controllers\Admin\UserPlanController::class, 'getUserPlanInfo'])->name('admin.users.plan-info');

    // Usage Token Management Routes
    Route::post('/admin/usage-tokens/update-user-tokens', [\App\Http\Controllers\Admin\UsageTokenController::class, 'updateUserTokens'])->name('admin.usage-tokens.update-user-tokens');
    Route::post('/admin/usage-tokens/add-tokens', [\App\Http\Controllers\Admin\UsageTokenController::class, 'addTokens'])->name('admin.usage-tokens.add-tokens');
    Route::get('/admin/usage-tokens/get-user-tokens', [\App\Http\Controllers\Admin\UsageTokenController::class, 'getUserTokens'])->name('admin.usage-tokens.get-user-tokens');
    Route::get('/admin/usage-tokens/get-plan-tokens', [\App\Http\Controllers\Admin\UsageTokenController::class, 'getPlanTokens'])->name('admin.usage-tokens.get-plan-tokens');

// Widget dosyaları artık public dizininden direkt servis ediliyor


Route::get('/gift', function () {
     return redirect('/');
 })->name('gift-data.index');
 Route::get('/gift/fashion', [GiftDataController::class, 'fashionSector'])->name('gift-data.fashion-sector');
 Route::get('/gift/furniture', [GiftDataController::class, 'furnitureSector'])->name('gift-data.furniture-sector');
 Route::get('/gift/home', [GiftDataController::class, 'homeAppliancesSector'])->name('gift-data.home-appliances-sector');
 Route::get('/gift/health', [GiftDataController::class, 'healthBeautySector'])->name('gift-data.health-beauty-sector');
 Route::get('/gift/electronics', [GiftDataController::class, 'electronicsSector'])->name('gift-data.electronics-sector');
 
 // Gift Data Sector POST Routes
 Route::post('/gift/fashion', action: [GiftDataController::class, 'storeFashionSector'])->name('gift-data.fashion-sector.store');
 Route::post('/gift/furniture', [GiftDataController::class, 'storeFurnitureSector'])->name('gift-data.furniture-sector.store');
 Route::post('/gift/home', [GiftDataController::class, 'storeHomeAppliancesSector'])->name('gift-data.home-appliances-sector.store');
 Route::post('/gift/health', [GiftDataController::class, 'storeHealthBeautySector'])->name('gift-data.health-beauty-sector.store');
 Route::post('/gift/electronics', [GiftDataController::class, 'storeElectronicsSector'])->name('gift-data.electronics-sector.store');
     // Giftbox Data Management
  Route::prefix('admin/giftbox-data')->name('admin.giftbox-data.')->group(function () {
         Route::get('/', [\App\Http\Controllers\GiftDataController::class, 'adminIndex'])->name('index');
         Route::get('/{giftboxUser}', [\App\Http\Controllers\GiftDataController::class, 'adminShow'])->name('show');
         Route::delete('/{giftboxUser}', [\App\Http\Controllers\GiftDataController::class, 'adminDestroy'])->name('destroy');
         Route::get('/export', [\App\Http\Controllers\GiftDataController::class, 'adminExport'])->name('export');
     });