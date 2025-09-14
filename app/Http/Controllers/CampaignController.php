<?php

namespace App\Http\Controllers;

use App\Models\Campaign;
use App\Models\Site;
use App\Models\Product;
use App\Services\CampaignPromptService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class CampaignController extends Controller
{
    private $campaignPromptService;

    public function __construct(CampaignPromptService $campaignPromptService)
    {
        $this->campaignPromptService = $campaignPromptService;
    }

    /**
     * Display admin dashboard for campaigns
     */
    public function index(Request $request)
    {
        if ($request->expectsJson()) {
            // API request - return JSON
            try {
                $siteId = $request->get('site_id', 1); // Default site ID
                
                // Site'in var olup olmadığını kontrol et
                $site = Site::find($siteId);
                if (!$site) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Belirtilen site bulunamadı',
                        'data' => []
                    ], 404);
                }
                
                $campaigns = Campaign::where('site_id', $siteId)
                    ->active()
                    ->valid()
                    ->orderBy('start_date', 'desc')
                    ->get();

                return response()->json([
                    'success' => true,
                    'data' => $campaigns,
                    'message' => 'Kampanyalar başarıyla getirildi'
                ]);
            } catch (\Exception $e) {
                return response()->json([
                    'success' => false,
                    'message' => 'Kampanyalar getirilirken hata oluştu: ' . $e->getMessage()
                ], 500);
            }
        }

        // Web request - return admin view
        $campaigns = Campaign::with('site')->orderBy('created_at', 'desc')->get();
        return view('dashboard.campaigns', compact('campaigns'));
    }

    /**
     * Store a newly created campaign
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'title' => 'required|string|max:255',
                'description' => 'required|string',
                'category' => 'required|string|max:100',
                'discount' => 'required|string|max:255',
                'valid_until' => 'nullable|date',
                'is_active' => 'boolean',
                'site_id' => 'required|exists:sites,id',
                'start_date' => 'nullable|date',
                'end_date' => 'nullable|date',
                'discount_type' => 'required|in:percentage,fixed,buy_x_get_y,free_shipping',
                'discount_value' => 'nullable|numeric|min:0',
                'minimum_order_amount' => 'nullable|numeric|min:0',
                'max_usage' => 'nullable|integer|min:1',
                'image_url' => 'nullable|url',
                'terms_conditions' => 'nullable|string'
            ]);

            // Custom validation for end_date after start_date
            $validator->after(function ($validator) use ($request) {
                if ($request->start_date && $request->end_date) {
                    if (strtotime($request->end_date) < strtotime($request->start_date)) {
                        $validator->errors()->add('end_date', 'Bitiş tarihi başlangıç tarihinden sonra olmalıdır.');
                    }
                }
            });

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validasyon hatası',
                    'errors' => $validator->errors()
                ], 422);
            }

            $campaign = Campaign::create($request->all());

            return response()->json([
                'success' => true,
                'data' => $campaign,
                'message' => 'Kampanya başarıyla oluşturuldu'
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Kampanya oluşturulurken hata oluştu: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified campaign
     */
    public function show($id): JsonResponse
    {
        try {
            $campaign = Campaign::findOrFail($id);

            return response()->json([
                'success' => true,
                'data' => $campaign,
                'message' => 'Kampanya başarıyla getirildi'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Kampanya bulunamadı: ' . $e->getMessage()
            ], 404);
        }
    }

    /**
     * Update the specified campaign
     */
    public function update(Request $request, $id): JsonResponse
    {
        try {
            $campaign = Campaign::findOrFail($id);

            $validator = Validator::make($request->all(), [
                'title' => 'sometimes|required|string|max:255',
                'description' => 'sometimes|required|string',
                'category' => 'sometimes|required|string|max:100',
                'discount' => 'sometimes|required|string|max:255',
                'valid_until' => 'nullable|date',
                'is_active' => 'sometimes|boolean',
                'start_date' => 'nullable|date',
                'end_date' => 'nullable|date',
                'discount_type' => 'sometimes|required|in:percentage,fixed,buy_x_get_y,free_shipping',
                'discount_value' => 'nullable|numeric|min:0',
                'minimum_order_amount' => 'nullable|numeric|min:0',
                'max_usage' => 'nullable|integer|min:1',
                'image_url' => 'nullable|url',
                'terms_conditions' => 'nullable|string'
            ]);

            // Custom validation for end_date after start_date
            $validator->after(function ($validator) use ($request) {
                if ($request->start_date && $request->end_date) {
                    if (strtotime($request->end_date) < strtotime($request->start_date)) {
                        $validator->errors()->add('end_date', 'Bitiş tarihi başlangıç tarihinden sonra olmalıdır.');
                    }
                }
            });

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validasyon hatası',
                    'errors' => $validator->errors()
                ], 422);
            }

            $campaign->update($request->all());

            return response()->json([
                'success' => true,
                'data' => $campaign,
                'message' => 'Kampanya başarıyla güncellendi'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Kampanya güncellenirken hata oluştu: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified campaign
     */
    public function destroy($id): JsonResponse
    {
        try {
            $campaign = Campaign::findOrFail($id);
            $campaign->delete();

            return response()->json([
                'success' => true,
                'message' => 'Kampanya başarıyla silindi'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Kampanya silinirken hata oluştu: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get campaigns by category
     */
    public function getByCategory(Request $request, $category): JsonResponse
    {
        try {
            $siteId = $request->get('site_id', 1);
            
            $campaigns = Campaign::where('site_id', $siteId)
                ->where('category', $category)
                ->active()
                ->valid()
                ->orderBy('start_date', 'desc')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $campaigns,
                'message' => 'Kategori kampanyaları başarıyla getirildi'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Kategori kampanyaları getirilirken hata oluştu: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Check if campaigns exist for a site
     */
    public function checkAvailability(Request $request): JsonResponse
    {
        try {
            $projectId = $request->get('project_id');
            
            if (!$projectId) {
                return response()->json([
                    'success' => false,
                    'has_campaigns' => false,
                    'message' => 'Project ID gerekli'
                ], 400);
            }
            
            $campaignCount = Campaign::where('project_id', $projectId)
                ->where('is_active', true)
                ->count();

            return response()->json([
                'success' => true,
                'has_campaigns' => $campaignCount > 0,
                'campaign_count' => $campaignCount,
                'message' => 'Kampanya durumu başarıyla kontrol edildi'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'has_campaigns' => false,
                'message' => 'Kampanya durumu kontrol edilirken hata oluştu: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get active campaigns count
     */
    public function getActiveCount(Request $request): JsonResponse
    {
        try {
            $siteId = $request->get('site_id', 1);
            
            $count = Campaign::where('site_id', $siteId)
                ->active()
                ->valid()
                ->count();

            return response()->json([
                'success' => true,
                'data' => ['count' => $count],
                'message' => 'Aktif kampanya sayısı getirildi'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Kampanya sayısı getirilirken hata oluştu: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * AI destekli kampanya önerileri oluşturur
     */
    public function generateAICampaignSuggestions(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'product_ids' => 'required|array',
                'product_ids.*' => 'exists:products,id',
                'product_settings' => 'required|array',
                'product_settings.*' => 'required|array',
                'product_settings.*.salePrice' => 'required|numeric|min:0',
                'product_settings.*.profitMargin' => 'required|numeric|min:0|max:100',
                'product_settings.*.stockQuantity' => 'required|integer|min:0',
                'product_settings.*.season' => 'required|string',
                'season' => 'required|string'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validasyon hatası',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Ürün bilgilerini al
            $products = Product::whereIn('id', $request->product_ids)->get();
            $productData = [];
            
            foreach ($products as $product) {
                $productData[] = [
                    'name' => $product->name,
                    'category' => $product->category,
                    'price' => $product->price,
                    'stock' => $product->stock ?? 0,
                    'profit_margin' => $product->profit_margin ?? 20 // Varsayılan %20
                ];
            }

            $businessData = [
                'product_settings' => $request->product_settings,
                'season' => $request->season
            ];

            // AI kampanya önerileri oluştur
            $suggestions = $this->campaignPromptService->generateCampaignSuggestions($productData, $businessData);

            return response()->json([
                'success' => true,
                'data' => $suggestions,
                'message' => 'AI kampanya önerileri başarıyla oluşturuldu'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Kampanya önerileri oluşturulurken hata oluştu: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Ürün listesini getirir (kampanya oluşturma için)
     */
    public function getProductsForCampaign(Request $request): JsonResponse
    {
        try {
            $products = Product::select('id', 'name', 'category_id', 'price', 'stock', 'profit_margin')
                ->with('category:id,name')
                ->where('stock', '>', 0)
                ->orderBy('name')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $products,
                'message' => 'Ürünler başarıyla getirildi'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Ürünler getirilirken hata oluştu: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * AI önerisi ile kampanya oluşturur
     */
    public function createFromAISuggestion(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'suggestion_data' => 'required|array',
                'selected_products' => 'required|array',
                'selected_products.*' => 'exists:products,id',
                'site_id' => 'required|exists:sites,id'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validasyon hatası',
                    'errors' => $validator->errors()
                ], 422);
            }

            $suggestion = $request->suggestion_data;
            $selectedProducts = $request->selected_products;

            // Kampanya verilerini hazırla
            $campaignData = [
                'title' => $suggestion['title'] ?? 'AI Kampanya',
                'description' => $suggestion['description'] ?? 'AI tarafından oluşturulan kampanya',
                'category' => $suggestion['campaign_type'] ?? 'Genel',
                'discount' => ($suggestion['discount_type'] ?? 'percentage') . ': ' . ($suggestion['discount_value'] ?? '10'),
                'discount_type' => $suggestion['discount_type'] ?? 'percentage',
                'discount_value' => is_numeric($suggestion['discount_value']) ? (float)$suggestion['discount_value'] : 10.0,
                'valid_until' => now()->addDays($suggestion['validity_days'] ?? 30),
                'start_date' => now(),
                'end_date' => now()->addDays($suggestion['validity_days'] ?? 30),
                'minimum_order_amount' => is_numeric($suggestion['minimum_order']) ? (float)$suggestion['minimum_order'] : 0.0,
                'terms_conditions' => $suggestion['terms'] ?? '',
                'is_active' => true,
                'site_id' => $request->site_id,
                'ai_generated' => true,
                'ai_confidence_score' => is_numeric($suggestion['confidence_score']) ? (float)$suggestion['confidence_score'] : 0.0
            ];

            // Kampanyayı oluştur
            $campaign = Campaign::create($campaignData);

            // Seçili ürünleri kampanyaya bağla (eğer pivot tablo varsa)
            if (method_exists($campaign, 'products')) {
                $campaign->products()->attach($selectedProducts);
            }

            return response()->json([
                'success' => true,
                'data' => $campaign,
                'message' => 'AI önerisi ile kampanya başarıyla oluşturuldu'
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Kampanya oluşturulurken hata oluştu: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Birden fazla AI önerisi ile kampanya oluşturur
     */
    public function createMultipleFromAISuggestions(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'suggestions' => 'required|array',
                'suggestions.*' => 'required|array',
                'selected_products' => 'required|array',
                'selected_products.*' => 'exists:products,id',
                'site_id' => 'required|exists:sites,id'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validasyon hatası',
                    'errors' => $validator->errors()
                ], 422);
            }

            $suggestions = $request->suggestions;
            $selectedProducts = $request->selected_products;
            $createdCampaigns = [];
            $errors = [];

            foreach ($suggestions as $index => $suggestion) {
                try {
                    // Kampanya verilerini hazırla
                    $campaignData = [
                        'title' => $suggestion['title'] ?? 'AI Kampanya ' . ($index + 1),
                        'description' => $suggestion['description'] ?? 'AI tarafından oluşturulan kampanya',
                        'category' => $suggestion['campaign_type'] ?? 'Genel',
                        'discount' => ($suggestion['discount_type'] ?? 'percentage') . ': ' . ($suggestion['discount_value'] ?? '10'),
                        'discount_type' => $suggestion['discount_type'] ?? 'percentage',
                        'discount_value' => is_numeric($suggestion['discount_value']) ? (float)$suggestion['discount_value'] : 10.0,
                        'valid_until' => now()->addDays($suggestion['validity_days'] ?? 30),
                        'start_date' => now(),
                        'end_date' => now()->addDays($suggestion['validity_days'] ?? 30),
                        'minimum_order_amount' => is_numeric($suggestion['minimum_order']) ? (float)$suggestion['minimum_order'] : 0.0,
                        'terms_conditions' => $suggestion['terms'] ?? '',
                        'is_active' => true,
                        'site_id' => $request->site_id,
                        'ai_generated' => true,
                        'ai_confidence_score' => is_numeric($suggestion['confidence_score']) ? (float)$suggestion['confidence_score'] : 0.0
                    ];

                    // Kampanyayı oluştur
                    $campaign = Campaign::create($campaignData);
                    $createdCampaigns[] = $campaign;

                } catch (\Exception $e) {
                    $errors[] = "Kampanya " . ($index + 1) . " oluşturulamadı: " . $e->getMessage();
                }
            }

            if (empty($createdCampaigns)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Hiçbir kampanya oluşturulamadı',
                    'errors' => $errors
                ], 500);
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'created_campaigns' => $createdCampaigns,
                    'total_created' => count($createdCampaigns),
                    'errors' => $errors
                ],
                'message' => count($createdCampaigns) . ' kampanya başarıyla oluşturuldu' . (count($errors) > 0 ? ' (' . count($errors) . ' hata)' : '')
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Kampanyalar oluşturulurken hata oluştu: ' . $e->getMessage()
            ], 500);
        }
    }
}
