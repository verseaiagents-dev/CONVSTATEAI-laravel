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
                // Sadece project_id kullan (site_id artık kullanılmıyor)
                $projectId = $request->get('project_id');
                
                if (!$projectId) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Project ID gerekli'
                    ], 400);
                }
                
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
        $projectId = $request->query('project_id');
        
        // Eğer project_id belirtilmemişse dashboard'a yönlendir
        if (!$projectId) {
            return redirect()->route('dashboard')
                ->with('warning', 'Lütfen önce bir proje seçin.');
        }
        
        // Project var mı kontrol et
        $project = \App\Models\Project::find($projectId);
        if (!$project) {
            abort(404, 'Proje bulunamadı');
        }
        
        // Kullanıcının bu projeye erişim yetkisi var mı kontrol et
        $user = auth()->user();
        if ($project->created_by !== $user->id) {
            abort(403, 'Bu projeye erişim yetkiniz yok');
        }
        
        return view('dashboard.faqs', compact('projectId', 'project'));
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
                'category' => 'nullable|string|max:100',
                'is_active' => 'boolean',
                'site_id' => 'required|exists:sites,id',
                'project_id' => 'nullable|integer',
                'sort_order' => 'nullable|integer|min:0',
                'tags' => 'nullable|array',
                'tags.*' => 'string|max:50'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validasyon hatası',
                    'errors' => $validator->errors()
                ], 422);
            }

            $faq = FAQ::create($request->all());

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
                'category' => 'nullable|string|max:100',
                'is_active' => 'sometimes|boolean',
                'project_id' => 'nullable|integer',
                'sort_order' => 'nullable|integer|min:0',
                'tags' => 'nullable|array',
                'tags.*' => 'string|max:50'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validasyon hatası',
                    'errors' => $validator->errors()
                ], 422);
            }

            $faq->update($request->all());

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
            $projectId = $request->get('project_id');
            
            if (!$projectId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Project ID gerekli'
                ], 400);
            }
            
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
            // Sadece project_id kullan (site_id artık kullanılmıyor)
            $projectId = $request->get('project_id');
            $query = $request->get('q', '');
            
            if (empty($query)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Arama sorgusu gerekli'
                ], 400);
            }

            if (!$projectId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Project ID gerekli'
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
            // Sadece project_id kullan (site_id artık kullanılmıyor)
            $projectId = $request->get('project_id');
            $limit = $request->get('limit', 10);
            
            if (!$projectId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Project ID gerekli'
                ], 400);
            }
            
            $faqs = FAQ::where('project_id', $projectId)
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
