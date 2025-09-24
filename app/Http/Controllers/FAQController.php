<?php

namespace App\Http\Controllers;

use App\Models\FAQ;
use App\Models\Site;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;

class FAQController extends Controller
{
    /**
     * Display admin dashboard for FAQs
     */
    public function index(Request $request)
    {
        if ($request->expectsJson()) {
            // API request - return JSON
            try {
                $projectId = $request->get('project_id', 1); // Default project ID
                
                $faqs = FAQ::where('project_id', $projectId)
                    ->active()
                    ->ordered()
                    ->get();

                return response()->json([
                    'success' => true,
                    'data' => $faqs,
                    'message' => 'SSS başarıyla getirildi'
                ]);
            } catch (\Exception $e) {
                return response()->json([
                    'success' => false,
                    'message' => 'SSS getirilirken hata oluştu: ' . $e->getMessage()
                ], 500);
            }
        }

        // Web request - return admin view
        $projectId = $request->get('project_id', 1);
        $faqs = FAQ::where('project_id', $projectId)
            ->with('site')
            ->orderBy('sort_order', 'asc')
            ->orderBy('created_at', 'desc')
            ->get();
        return view('dashboard.faqs', compact('faqs', 'projectId'));
    }

    /**
     * Store a newly created FAQ
     */
    public function store(Request $request): JsonResponse
    {
        try {
            // Debug: Log request data
            
            $validator = Validator::make($request->all(), [
                'title' => 'required|string|max:255',
                'description' => 'nullable|string',
                'answer' => 'required|string',
                'short_answer' => 'required|string|max:255',
                'category' => 'required|string|max:100',
                'is_active' => 'boolean',
                'site_id' => 'nullable|integer',
                'project_id' => 'required|integer',
                'sort_order' => 'nullable|integer|min:0',
                // Yeni tasarım modeli fieldları
                'faq_code' => 'nullable|string|max:50|unique:faqs,faq_code',
                'keywords' => 'nullable|string',
                'related_faqs' => 'nullable|array',
                'related_faqs.*' => 'integer|exists:faqs,id',
                'difficulty_level' => 'nullable|in:easy,medium,hard,expert',
                'estimated_read_time' => 'nullable|integer|min:1',
                'featured' => 'boolean',
                'author' => 'nullable|string|max:100',
                'review_notes' => 'nullable|string',
                'metadata' => 'nullable|array'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validasyon hatası',
                    'errors' => $validator->errors()
                ], 422);
            }

            $data = $request->all();
            
            // Site ID'yi otomatik olarak ayarla veya oluştur
            if (empty($data['site_id'])) {
                $projectId = $data['project_id'];
                
                // Projeye ait site var mı kontrol et
                $site = Site::where('project_id', $projectId)->first();
                
                if (!$site) {
                    // Site yoksa oluştur
                    $project = \App\Models\Project::find($projectId);
                    $site = Site::create([
                        'name' => $project ? $project->name . ' Site' : 'Default Site',
                        'domain' => $project ? parse_url($project->url ?? 'https://example.com', PHP_URL_HOST) ?? 'example.com' : 'example.com',
                        'description' => 'Otomatik oluşturulan site',
                        'project_id' => $projectId,
                        'is_active' => true
                    ]);
                }
                
                $data['site_id'] = $site->id;
            }
            
            // Otomatik FAQ kodu oluştur
            if (empty($data['faq_code'])) {
                $data['faq_code'] = FAQ::generateFaqCode();
            }
            
            // Varsayılan değerler
            $data['difficulty_level'] = $data['difficulty_level'] ?? 'easy';
            $data['featured'] = $data['featured'] ?? false;
            $data['description'] = $data['description'] ?? '';
            
            // Otomatik okuma süresi hesapla
            if (empty($data['estimated_read_time'])) {
                $tempFaq = new FAQ($data);
                $data['estimated_read_time'] = $tempFaq->calculateReadTime();
            }
            
            $faq = FAQ::create($data);

            return response()->json([
                'success' => true,
                'data' => $faq,
                'message' => 'SSS başarıyla oluşturuldu'
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'SSS oluşturulurken hata oluştu: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified FAQ
     */
    public function show($id): JsonResponse
    {
        try {
            $faq = FAQ::findOrFail($id);
            
            // Increment view count
            $faq->incrementViewCount();

            return response()->json([
                'success' => true,
                'data' => $faq,
                'message' => 'SSS başarıyla getirildi'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'SSS bulunamadı: ' . $e->getMessage()
            ], 404);
        }
    }

   
    /**
     * Update the specified FAQ
     */
    public function update(Request $request, $id): JsonResponse
    {
        try {
            $faq = FAQ::findOrFail($id);

            $validator = Validator::make($request->all(), [
                'title' => 'sometimes|required|string|max:255',
                'description' => 'nullable|string',
                'answer' => 'sometimes|required|string',
                'short_answer' => 'sometimes|required|string|max:255',
                'category' => 'sometimes|required|string|max:100',
                'is_active' => 'sometimes|boolean',
                'site_id' => 'nullable|integer',
                'project_id' => 'sometimes|required|integer',
                'sort_order' => 'nullable|integer|min:0',
                // Yeni tasarım modeli fieldları
                'faq_code' => 'nullable|string|max:50|unique:faqs,faq_code,' . $id,
                'keywords' => 'nullable|string',
                'related_faqs' => 'nullable|array',
                'related_faqs.*' => 'integer|exists:faqs,id',
                'difficulty_level' => 'nullable|in:easy,medium,hard,expert',
                'estimated_read_time' => 'nullable|integer|min:1',
                'featured' => 'boolean',
                'author' => 'nullable|string|max:100',
                'review_notes' => 'nullable|string',
                'metadata' => 'nullable|array'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validasyon hatası',
                    'errors' => $validator->errors()
                ], 422);
            }

            $data = $request->all();
            
            // Site ID'yi otomatik olarak ayarla veya oluştur (sadece project_id varsa)
            if (isset($data['project_id']) && empty($data['site_id'])) {
                $projectId = $data['project_id'];
                
                // Projeye ait site var mı kontrol et
                $site = Site::where('project_id', $projectId)->first();
                
                if (!$site) {
                    // Site yoksa oluştur
                    $project = \App\Models\Project::find($projectId);
                    $site = Site::create([
                        'name' => $project ? $project->name . ' Site' : 'Default Site',
                        'domain' => $project ? parse_url($project->url ?? 'https://example.com', PHP_URL_HOST) ?? 'example.com' : 'example.com',
                        'description' => 'Otomatik oluşturulan site',
                        'project_id' => $projectId,
                        'is_active' => true
                    ]);
                }
                
                $data['site_id'] = $site->id;
            }
            
            // Okuma süresini yeniden hesapla eğer içerik değiştiyse
            if (isset($data['answer']) || isset($data['description'])) {
                $tempFaq = clone $faq;
                $tempFaq->fill($data);
                $data['estimated_read_time'] = $tempFaq->calculateReadTime();
            }
            
            $faq->update($data);

            return response()->json([
                'success' => true,
                'data' => $faq,
                'message' => 'SSS başarıyla güncellendi'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'SSS güncellenirken hata oluştu: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified FAQ
     */
    public function destroy($id): JsonResponse
    {
        try {
            $faq = FAQ::findOrFail($id);
            $faq->delete();

            return response()->json([
                'success' => true,
                'message' => 'SSS başarıyla silindi'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'SSS silinirken hata oluştu: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get FAQs by category
     */
    public function getByCategory(Request $request, $category): JsonResponse
    {
        try {
            $projectId = $request->get('project_id', 1);
            
            $faqs = FAQ::where('project_id', $projectId)
                ->where('category', $category)
                ->active()
                ->ordered()
                ->get();

            return response()->json([
                'success' => true,
                'data' => $faqs,
                'message' => 'Kategori SSS\'leri başarıyla getirildi'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Kategori SSS\'leri getirilirken hata oluştu: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Mark FAQ as helpful
     */
    public function markAsHelpful($id): JsonResponse
    {
        try {
            $faq = FAQ::findOrFail($id);
            $faq->markAsHelpful();

            return response()->json([
                'success' => true,
                'data' => ['helpful_count' => $faq->helpful_count],
                'message' => 'SSS faydalı olarak işaretlendi'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'İşlem sırasında hata oluştu: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Mark FAQ as not helpful
     */
    public function markAsNotHelpful($id): JsonResponse
    {
        try {
            $faq = FAQ::findOrFail($id);
            $faq->markAsNotHelpful();

            return response()->json([
                'success' => true,
                'data' => ['not_helpful_count' => $faq->not_helpful_count],
                'message' => 'SSS faydalı değil olarak işaretlendi'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'İşlem sırasında hata oluştu: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Search FAQs
     */
    public function search(Request $request): JsonResponse
    {
        try {
            $projectId = $request->get('project_id', 1);
            $query = $request->get('q', '');
            
            if (empty($query)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Arama sorgusu gerekli'
                ], 400);
            }

            $faqs = FAQ::where('project_id', $projectId)
                ->where('is_active', true)
                ->where(function($q) use ($query) {
                    $q->where('title', 'like', "%{$query}%")
                      ->orWhere('description', 'like', "%{$query}%")
                      ->orWhere('answer', 'like', "%{$query}%")
                      ->orWhere('category', 'like', "%{$query}%");
                })
                ->ordered()
                ->get();

            return response()->json([
                'success' => true,
                'data' => $faqs,
                'message' => 'Arama sonuçları getirildi'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Arama sırasında hata oluştu: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get popular FAQs
     */
    public function getPopular(Request $request): JsonResponse
    {
        try {
            $siteId = $request->get('site_id', 1);
            $limit = $request->get('limit', 10);
            
            $faqs = FAQ::where('site_id', $siteId)
                ->active()
                ->popular()
                ->limit($limit)
                ->get();

            return response()->json([
                'success' => true,
                'data' => $faqs,
                'message' => 'Popüler SSS\'ler getirildi'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Popüler SSS\'ler getirilirken hata oluştu: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Check if FAQs exist for a site
     */
    public function checkAvailability(Request $request): JsonResponse
    {
        try {
            $projectId = $request->get('project_id');
            
            if (!$projectId) {
                return response()->json([
                    'success' => false,
                    'has_faqs' => false,
                    'message' => 'Project ID gerekli'
                ], 400);
            }
            
            $faqCount = FAQ::where('project_id', $projectId)
                ->where('is_active', true)
                ->count();

            return response()->json([
                'success' => true,
                'has_faqs' => $faqCount > 0,
                'faq_count' => $faqCount,
                'message' => 'SSS durumu başarıyla kontrol edildi'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'has_faqs' => false,
                'message' => 'SSS durumu kontrol edilirken hata oluştu: ' . $e->getMessage()
            ], 500);
        }
    }
}
