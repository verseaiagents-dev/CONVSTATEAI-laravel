<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use App\Models\KnowledgeBase;
use App\Models\KnowledgeChunk;
use App\Models\FieldMapping;
use App\Models\Project;
use App\Models\QueryLog;
use App\Models\Campaign;
use App\Models\FAQ;
use App\Models\WidgetActions;
use App\Services\KnowledgeBase\ContentChunker;
use App\Services\KnowledgeBase\AIService;
use App\Services\KnowledgeBase\FAQOptimizationService;
use App\Services\KnowledgeBase\FieldMappingService;
use App\Jobs\ProcessKnowledgeBaseFile;
use App\Jobs\ProcessKnowledgeBaseUrl;
use Maatwebsite\Excel\Facades\Excel;

class KnowledgeBaseController extends Controller
{
    protected $contentChunker;
    protected $aiService;
    protected $faqOptimizer;
    protected $fieldMappingService;

    public function __construct(ContentChunker $contentChunker, AIService $aiService, FAQOptimizationService $faqOptimizer, FieldMappingService $fieldMappingService)
    {
        $this->contentChunker = $contentChunker;
        $this->aiService = $aiService;
        $this->faqOptimizer = $faqOptimizer;
        $this->fieldMappingService = $fieldMappingService;
    }

    /**
     * Show knowledge base page
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $projectId = $request->query('project_id');
        
        // If project_id is provided, validate it exists
        if ($projectId) {
            $project = Project::find($projectId);
            if (!$project) {
                abort(404, 'Project not found');
            }
        }
        
        return view('dashboard.knowledge-base', compact('user', 'projectId'));
    }

    /**
     * Load knowledge base content for lazy loading
     */
    public function loadContent(Request $request): JsonResponse
    {
        try {
            $user = Auth::user();
            $projectId = $request->query('project_id');
            
            // Get knowledge bases
            $knowledgeBases = KnowledgeBase::with('chunks')->orderBy('created_at', 'desc')->get();
            
            // Get projects
            $projects = Project::where('created_by', $user->id)->orderBy('created_at', 'desc')->get();
            
            // Get project if project_id is provided
            $project = null;
            if ($projectId) {
                $project = Project::find($projectId);
            }
            
            // Get stats data
            $stats = [
                'campaign_count' => Campaign::where('project_id', $projectId ?: 1)->where('is_active', true)->count(),
                'faq_count' => FAQ::where('project_id', $projectId ?: 1)->where('is_active', true)->count(),
                'active_actions' => WidgetActions::where('is_active', true)->count(),
                'total_actions' => WidgetActions::count(),
                'siparis_endpoints' => WidgetActions::where('type', 'siparis_durumu_endpoint')->where('is_active', true)->count(),
                'kargo_endpoints' => WidgetActions::where('type', 'kargo_durumu_endpoint')->where('is_active', true)->count()
            ];
            
            return response()->json([
                'success' => true,
                'data' => [
                    'knowledgeBases' => $knowledgeBases,
                    'projects' => $projects,
                    'project' => $project,
                    'stats' => $stats
                ]
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'İçerik yüklenirken hata oluştu: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Bilgi tabanı adı çakışmasını kontrol eder ve benzersiz isim oluşturur
     */
    private function generateUniqueKnowledgeBaseName(string $baseName, int $userId = null): string
    {
        // İlk olarak temel ismi kontrol et
        $query = KnowledgeBase::where('name', $baseName);
        
        // Eğer userId verilmişse, sadece o kullanıcının bilgi tabanlarını kontrol et
        if ($userId) {
            $query->whereHas('project', function($q) use ($userId) {
                $q->where('created_by', $userId);
            });
        }
        
        if (!$query->exists()) {
            return $baseName;
        }
        
        // Çakışma varsa benzersiz isim oluştur
        $counter = 1;
        $uniqueName = $baseName;
        
        do {
            $uniqueName = $baseName . '_' . $counter;
            $query = KnowledgeBase::where('name', $uniqueName);
            
            if ($userId) {
                $query->whereHas('project', function($q) use ($userId) {
                    $q->where('created_by', $userId);
                });
            }
            
            $counter++;
        } while ($query->exists());
        
        return $uniqueName;
    }

    /**
     * Handle file upload - Sadece JSON dosyaları desteklenir
     */
    public function uploadFile(Request $request)
    {
        try {
            // Validation - Sadece JSON dosyaları
            $validator = \Validator::make($request->all(), [
                'file' => 'required|file|mimes:json|max:10240', // 10MB max, sadece JSON
                'description' => 'nullable|string|max:1000',
                'project_id' => 'nullable|exists:projects,id',
            ], [
                'file.required' => 'Dosya seçilmedi',
                'file.file' => 'Geçersiz dosya',
                'file.mimes' => 'Sadece JSON dosyaları desteklenir',
                'file.max' => 'Dosya boyutu çok büyük. Maksimum 10MB olmalı',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation hatası',
                    'errors' => $validator->errors()
                ], 422);
            }

            DB::beginTransaction();

            $file = $request->file('file');
            $extension = strtolower($file->getClientOriginalExtension());
            $originalFileName = $file->getClientOriginalName();
            $fileName = time() . '_' . $originalFileName;
            
            // Dosya adından bilgi tabanı adını oluştur (uzantıyı kaldır)
            $baseName = pathinfo($originalFileName, PATHINFO_FILENAME);
            
            // Çakışma kontrolü yap ve benzersiz isim oluştur
            $userId = Auth::id();
            $knowledgeBaseName = $this->generateUniqueKnowledgeBaseName($baseName, $userId);
            
            // File size check
            if ($file->getSize() > 10 * 1024 * 1024) {
                throw new \Exception('Dosya boyutu 10MB\'dan büyük olamaz');
            }

            // File extension check - Sadece JSON
            $allowedExtensions = ['json'];
            if (!in_array($extension, $allowedExtensions)) {
                throw new \Exception("Desteklenmeyen dosya formatı: {$extension}. Sadece JSON dosyaları desteklenir.");
            }
            
            // Store file
            $path = $file->storeAs('knowledge-base', $fileName, 'public');
            
            // Create knowledge base record
            $knowledgeBase = KnowledgeBase::create([
                'site_id' => 1, // Default site
                'project_id' => $request->input('project_id'),
                'name' => $knowledgeBaseName, // Otomatik dosya adından oluşturulan isim
                'description' => $request->input('description'),
                'source_type' => 'file',
                'source_path' => $path,
                'file_type' => $extension,
                'file_size' => $this->contentChunker->countTokens(file_get_contents($file->getPathname())),
                'processing_status' => 'processing',
                'is_processing' => true,
            ]);

            DB::commit();

            // Process file immediately (sync processing)
            try {
                // Process file and create chunks immediately
                $chunks = $this->processFileAndCreateChunks($file, $extension, $knowledgeBase);
                
                // Update knowledge base with results
                $knowledgeBase->update([
                    'chunk_count' => count($chunks),
                    'total_records' => $this->getTotalRecords($file, $extension),
                    'processed_records' => count($chunks),
                    'processing_status' => 'completed',
                    'is_processing' => false,
                    'last_processed_at' => Carbon::now(),
                ]);
                
                \Log::info('Knowledge base file processed successfully', [
                    'knowledge_base_id' => $knowledgeBase->id,
                    'file_path' => $path,
                    'file_type' => $extension,
                    'knowledge_base_name' => $knowledgeBaseName,
                    'original_name' => $baseName,
                    'name_conflict_resolved' => $baseName !== $knowledgeBaseName,
                    'chunks_created' => count($chunks)
                ]);
            } catch (\Exception $jobException) {
                \Log::error('Failed to process knowledge base file', [
                    'knowledge_base_id' => $knowledgeBase->id,
                    'error' => $jobException->getMessage(),
                    'trace' => $jobException->getTraceAsString()
                ]);
                
                // Mark as failed if processing fails
                $knowledgeBase->update([
                    'processing_status' => 'failed',
                    'is_processing' => false,
                    'error_message' => 'Processing failed: ' . $jobException->getMessage()
                ]);
                
                return response()->json([
                    'success' => false,
                    'message' => 'Dosya yüklendi ancak işlenemedi: ' . $jobException->getMessage()
                ], 500);
            }
        
            return response()->json([
                'success' => true,
                'message' => 'Dosya başarıyla yüklendi ve işlendi.',
                'knowledge_base_id' => $knowledgeBase->id,
                'file_name' => $fileName,
                'file_size' => $this->contentChunker->countTokens(file_get_contents($file->getPathname())),
                'extension' => $extension,
                'processing_status' => 'completed',
                'is_processing' => false,
                'chunk_count' => count($chunks)
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            
            if (isset($knowledgeBase)) {
                $knowledgeBase->update([
                    'processing_status' => 'failed',
                    'is_processing' => false,
                    'error_message' => $e->getMessage()
                ]);
            }

            \Log::error('Knowledge base upload error: ' . $e->getMessage(), [
                'file' => $request->file('file') ? $request->file('file')->getClientOriginalName() : 'N/A',
                'knowledge_base_name' => $knowledgeBaseName ?? 'N/A',
                'original_name' => $baseName ?? 'N/A',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Dosya işlenirken hata oluştu: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Handle URL content fetch - Sadece JSON URL'leri desteklenir
     */
    public function fetchFromUrl(Request $request)
    {
        $request->validate([
            'url' => 'required|url|max:500',
            'description' => 'nullable|string|max:1000',
            'project_id' => 'nullable|exists:projects,id',
        ]);

        try {
            DB::beginTransaction();

            $url = $request->input('url');
            
            // Proper headers ile HTTP request yap
            $response = \Http::timeout(30)
                ->withHeaders([
                    'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
                    'Accept' => 'application/json, text/plain, */*',
                    'Accept-Language' => 'tr-TR,tr;q=0.9,en-US;q=0.8,en;q=0.7',
                    'Accept-Encoding' => 'gzip, deflate, br',
                    'Connection' => 'keep-alive',
                    'Upgrade-Insecure-Requests' => '1',
                ])
                ->withOptions([
                    'verify' => false, // SSL sertifika hatalarını bypass et (gerekirse)
                ])
                ->get($url);
            
            if (!$response->successful()) {
                // Daha detaylı hata mesajı
                $errorMessage = 'URL\'den içerik alınamadı. HTTP Status: ' . $response->status();
                
                // HTTP status koduna göre özel mesajlar
                if ($response->status() === 401) {
                    $errorMessage .= ' - URL authentication gerektiriyor veya erişim izni yok.';
                } elseif ($response->status() === 403) {
                    $errorMessage .= ' - URL erişimi engellenmiş.';
                } elseif ($response->status() === 404) {
                    $errorMessage .= ' - URL bulunamadı.';
                } elseif ($response->status() >= 500) {
                    $errorMessage .= ' - Sunucu hatası.';
                }
                
                \Log::error('URL fetch failed', [
                    'url' => $url,
                    'status' => $response->status(),
                    'headers' => $response->headers(),
                    'body' => substr($response->body(), 0, 500)
                ]);
                
                return response()->json([
                    'success' => false,
                    'message' => $errorMessage
                ], 400);
            }

            $content = $response->body();
            $contentType = $response->header('Content-Type');
            
            // UTF-8 encoding kontrolü ve düzeltme
            if (!mb_check_encoding($content, 'UTF-8')) {
                // Farklı encoding'leri dene
                $encodings = ['ISO-8859-1', 'ISO-8859-9', 'Windows-1254', 'Windows-1252', 'ASCII'];
                
                foreach ($encodings as $encoding) {
                    if (mb_check_encoding($content, $encoding)) {
                        $content = mb_convert_encoding($content, 'UTF-8', $encoding);
                        break;
                    }
                }
                
                // Hala UTF-8 değilse, force convert
                if (!mb_check_encoding($content, 'UTF-8')) {
                    $content = mb_convert_encoding($content, 'UTF-8', 'auto');
                    \Log::warning("URL content encoding force converted to UTF-8: " . $url);
                }
            }
            
            // JSON URL kontrolü
            $extension = 'json';
            
            // URL'nin JSON içerik döndürdüğünü kontrol et
            if (!str_contains($contentType, 'json') && !str_contains($contentType, 'application/json')) {
                // URL'den gelen içeriğin JSON olup olmadığını kontrol et
                $decodedContent = json_decode($content, true);
                if (json_last_error() !== JSON_ERROR_NONE) {
                    return response()->json([
                        'success' => false,
                        'message' => 'URL\'den alınan içerik JSON formatında değil. Sadece JSON URL\'leri desteklenir.'
                    ], 400);
                }
            }
            
            // URL'den bilgi tabanı adını oluştur
            $parsedUrl = parse_url($url);
            $host = $parsedUrl['host'] ?? 'unknown';
            $path = $parsedUrl['path'] ?? '';
            
            // Domain adından ve path'den bilgi tabanı adını oluştur
            $baseName = $host;
            if ($path) {
                $pathParts = explode('/', trim($path, '/'));
                $lastPart = end($pathParts);
                if ($lastPart && !empty($lastPart)) {
                    // Uzantıyı kaldır
                    $baseName = pathinfo($lastPart, PATHINFO_FILENAME) ?: $host;
                }
            }
            
            // Çakışma kontrolü yap ve benzersiz isim oluştur
            $userId = Auth::id();
            $knowledgeBaseName = $this->generateUniqueKnowledgeBaseName($baseName, $userId);
            
            // Create knowledge base record
            $knowledgeBase = KnowledgeBase::create([
                'site_id' => 1, // Default site
                'project_id' => $request->input('project_id'),
                'name' => $knowledgeBaseName, // Otomatik URL'den oluşturulan isim
                'description' => $request->input('description') . ' | URL: ' . $url, // URL'yi description'a ekle
                'source_type' => 'url',
                'source_path' => $url,
                'file_type' => $extension,
                'file_size' => $this->contentChunker->countTokens($content),
                'processing_status' => 'processing',
                'is_processing' => true,
            ]);
            
            DB::commit();

            // Process URL content immediately (sync processing)
            try {
                // Create temporary file for processing
                $tempFile = tempnam(sys_get_temp_dir(), 'kb_url_');
                file_put_contents($tempFile, $content);
                
                // Create a file object for processing
                $file = new \Illuminate\Http\UploadedFile(
                    $tempFile,
                    basename($url),
                    $this->getMimeType($extension),
                    null,
                    true
                );
                
                // Process content and create chunks immediately
                $chunks = $this->processFileAndCreateChunks($file, $extension, $knowledgeBase);
                
                // Update knowledge base with results
                $knowledgeBase->update([
                    'chunk_count' => count($chunks),
                    'total_records' => $this->getTotalRecords($file, $extension),
                    'processed_records' => count($chunks),
                    'processing_status' => 'completed',
                    'is_processing' => false,
                    'last_processed_at' => Carbon::now(),
                ]);
                
                // Clean up temp file
                unlink($tempFile);
                
                \Log::info('Knowledge base URL processed successfully', [
                    'knowledge_base_id' => $knowledgeBase->id,
                    'url' => $url,
                    'knowledge_base_name' => $knowledgeBaseName,
                    'original_name' => $baseName,
                    'name_conflict_resolved' => $baseName !== $knowledgeBaseName,
                    'chunks_created' => count($chunks)
                ]);
            } catch (\Exception $jobException) {
                \Log::error('Failed to process knowledge base URL', [
                    'knowledge_base_id' => $knowledgeBase->id,
                    'url' => $url,
                    'error' => $jobException->getMessage(),
                    'trace' => $jobException->getTraceAsString()
                ]);
                
                // Mark as failed if processing fails
                $knowledgeBase->update([
                    'processing_status' => 'failed',
                    'is_processing' => false,
                    'error_message' => 'Processing failed: ' . $jobException->getMessage()
                ]);
                
                return response()->json([
                    'success' => false,
                    'message' => 'URL alındı ancak işlenemedi: ' . $jobException->getMessage()
                ], 500);
            }
            
            return response()->json([
                'success' => true,
                'message' => 'URL başarıyla alındı ve işlendi.',
                'knowledge_base_id' => $knowledgeBase->id,
                'file_name' => basename($url),
                'file_size' => $this->contentChunker->countTokens($content),
                'extension' => $extension,
                'url' => $url,
                'processing_status' => 'completed',
                'is_processing' => false,
                'chunk_count' => count($chunks)
            ]);
            
        } catch (\Exception $e) {
            DB::rollBack();
            
            if (isset($knowledgeBase)) {
                $knowledgeBase->update([
                    'processing_status' => 'failed',
                    'is_processing' => false,
                    'error_message' => $e->getMessage()
                ]);
            }

            \Log::error('Knowledge base URL fetch error: ' . $e->getMessage(), [
                'url' => $url ?? 'N/A',
                'knowledge_base_name' => $knowledgeBaseName ?? 'N/A',
                'original_name' => $baseName ?? 'N/A',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'URL işlenirken hata oluştu: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get processing status of a knowledge base
     */
    public function getProcessingStatus(Request $request, $id)
    {
        try {
            $knowledgeBase = KnowledgeBase::findOrFail($id);
            
            return response()->json([
                'success' => true,
                'knowledge_base_id' => $knowledgeBase->id,
                'processing_status' => $knowledgeBase->processing_status,
                'is_processing' => $knowledgeBase->is_processing,
                'progress_percentage' => $knowledgeBase->progress_percentage,
                'chunk_count' => $knowledgeBase->chunk_count,
                'total_records' => $knowledgeBase->total_records,
                'processed_records' => $knowledgeBase->processed_records,
                'last_processed_at' => $knowledgeBase->last_processed_at,
                'error_message' => $knowledgeBase->error_message,
                'is_completed' => $knowledgeBase->is_completed,
                'is_failed' => $knowledgeBase->is_failed
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Knowledge base bulunamadı: ' . $e->getMessage()
            ], 404);
        }
    }

    /**
     * Get all processing knowledge bases
     */
    public function getProcessingList(Request $request)
    {
        try {
            $knowledgeBases = KnowledgeBase::whereIn('processing_status', ['pending', 'processing'])
                ->orderBy('created_at', 'desc')
                ->get()
                ->map(function ($kb) {
                    return [
                        'id' => $kb->id,
                        'name' => $kb->name,
                        'source_type' => $kb->source_type,
                        'processing_status' => $kb->processing_status,
                        'is_processing' => $kb->is_processing,
                        'progress_percentage' => $kb->progress_percentage,
                        'chunk_count' => $kb->chunk_count,
                        'total_records' => $kb->total_records,
                        'processed_records' => $kb->processed_records,
                        'created_at' => $kb->created_at,
                        'last_processed_at' => $kb->last_processed_at,
                        'error_message' => $kb->error_message
                    ];
                });
            
            return response()->json([
                'success' => true,
                'processing_count' => $knowledgeBases->count(),
                'knowledge_bases' => $knowledgeBases
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'İşleme listesi alınırken hata oluştu: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Retry failed knowledge base processing
     */
    public function retryProcessing(Request $request, $id)
    {
        try {
            $knowledgeBase = KnowledgeBase::findOrFail($id);
            
            if ($knowledgeBase->processing_status !== 'failed') {
                return response()->json([
                    'success' => false,
                    'message' => 'Sadece başarısız olan knowledge base\'ler yeniden denenebilir'
                ], 400);
            }
            
            // Reset status
            $knowledgeBase->update([
                'processing_status' => 'pending',
                'is_processing' => false,
                'error_message' => null
            ]);
            
            // Dispatch appropriate job based on source type
            if ($knowledgeBase->source_type === 'file') {
                ProcessKnowledgeBaseFile::dispatch($knowledgeBase, $knowledgeBase->source_path, $knowledgeBase->file_type)
                    ->onQueue('knowledge-base-processing')
                    ->delay(now()->addSeconds(5));
            } else {
                ProcessKnowledgeBaseUrl::dispatch($knowledgeBase, $knowledgeBase->source_path)
                    ->onQueue('knowledge-base-processing')
                    ->delay(now()->addSeconds(5));
            }
            
            return response()->json([
                'success' => true,
                'message' => 'Knowledge base işleme yeniden başlatıldı',
                'knowledge_base_id' => $knowledgeBase->id,
                'processing_status' => 'pending'
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Yeniden deneme sırasında hata oluştu: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Process file and create chunks - Gelişmiş algoritma
     */
    private function processFileAndCreateChunks($file, $extension, KnowledgeBase $knowledgeBase): array
    {
        $chunks = [];
        
        // Akıllı chunking konfigürasyonu
        $chunkConfig = [
            'max_chunk_size' => 800, // Daha küçük chunk'lar
            'overlap_size' => 150,   // Daha az overlap
            'min_chunk_size' => 200,
            'preserve_words' => true, // Kelime bütünlüğünü koru
            'smart_sizing' => true,   // Akıllı boyutlandırma
            'quality_check' => true   // Kalite kontrolü
        ];
        
        switch ($extension) {
            case 'csv':
                $chunks = $this->processCSV($file, $knowledgeBase, $chunkConfig);
                break;
            case 'txt':
                $chunks = $this->processTXT($file, $knowledgeBase, $chunkConfig);
                break;
            case 'xml':
                $chunks = $this->processXML($file, $knowledgeBase, $chunkConfig);
                break;
            case 'json':
                $chunks = $this->processJSON($file, $knowledgeBase, $chunkConfig);
                break;
            case 'xlsx':
            case 'xls':
                $chunks = $this->processExcel($file, $knowledgeBase, $chunkConfig);
                break;
            default:
                throw new \Exception('Desteklenmeyen dosya formatı: ' . $extension);
        }

        // Chunk kalitesini değerlendir
        if ($chunkConfig['quality_check']) {
            $quality = $this->contentChunker->evaluateChunkQuality($chunks);
            
            // Düşük kaliteli chunk'ları yeniden işle
            if ($quality['overlap_quality'] < 80) {
                \Log::warning('Low chunk quality detected, reprocessing with better config');
                $chunkConfig['overlap_size'] = 200;
                $chunkConfig['max_chunk_size'] = 600;
                
                // Yeniden işle
                switch ($extension) {
                    case 'csv':
                        $chunks = $this->processCSV($file, $knowledgeBase, $chunkConfig);
                        break;
                    case 'txt':
                        $chunks = $this->processTXT($file, $knowledgeBase, $chunkConfig);
                        break;
                    default:
                        break;
                }
            }
        }

        // Create chunks in database
        $chunkModels = [];
        
        foreach ($chunks as $chunkData) {
            // Content type'ı daha iyi belirle
            $contentType = $this->determineContentType($chunkData['content'], $extension);
            
            $chunkModels[] = KnowledgeChunk::create([
                'knowledge_base_id' => $knowledgeBase->id,
                'project_id' => $knowledgeBase->project_id, // Proje ID'sini ekle
                'chunk_index' => $chunkData['chunk_index'],
                'content' => $chunkData['content'],
                'content_hash' => $chunkData['content_hash'],
                'content_type' => $contentType,
                'chunk_size' => $chunkData['chunk_size'],
                'word_count' => $chunkData['word_count'],
                'has_images' => false,
                'processed_images' => 0,
                'image_vision' => null,
                'metadata' => array_merge($chunkData['metadata'] ?? [], [
                    'original_content_type' => $chunkData['content_type'] ?? 'unknown',
                    'detected_content_type' => $contentType
                ]),
            ]);
        }

        // FAQ optimizasyonu yap (eğer content_type faq ise veya genel content ise)
        // Bu işlem başarısız olsa bile upload işlemi devam etsin
        try {
            // Sadece küçük dosyalar için FAQ optimizasyonu yap
            $totalContentSize = collect($chunks)->sum('chunk_size');
            if ($totalContentSize < 50000) { // 50KB'dan küçük dosyalar
                $this->optimizeFAQContent($knowledgeBase, $chunks);
            } else {
                Log::info('Skipping FAQ optimization for large file', [
                    'knowledge_base_id' => $knowledgeBase->id,
                    'total_size' => $totalContentSize
                ]);
            }
        } catch (\Exception $e) {
            Log::warning('FAQ optimization failed during upload, continuing without optimization', [
                'knowledge_base_id' => $knowledgeBase->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            // FAQ optimizasyonu başarısız olsa bile upload işlemi devam etsin
            // Kullanıcıya bilgi ver
            Log::info('Upload completed without FAQ optimization', [
                'knowledge_base_id' => $knowledgeBase->id,
                'chunks_created' => count($chunkModels)
            ]);
        }

        return $chunkModels;
    }

    /**
     * Process CSV file
     */
    private function processCSV($file, KnowledgeBase $knowledgeBase, array $config = []): array
    {
        $content = $this->readFileSafely($file->getPathname());
        return $this->contentChunker->chunkCsvContent($content, array_merge([
            'max_rows_per_chunk' => 30, // Daha az satır per chunk
            'preserve_rows' => true
        ], $config));
    }

    /**
     * Process TXT file
     */
    private function processTXT($file, KnowledgeBase $knowledgeBase, array $config = []): array
    {
        $content = $this->readFileSafely($file->getPathname());
        return $this->contentChunker->chunkContent($content, array_merge([
            'max_chunk_size' => 800,
            'overlap_size' => 150,
            'preserve_words' => true
        ], $config));
    }

    /**
     * Process XML file
     */
    private function processXML($file, KnowledgeBase $knowledgeBase, array $config = []): array
    {
        $content = $this->readFileSafely($file->getPathname());
        $xml = simplexml_load_string($content);
        
        if ($xml === false) {
            throw new \Exception('XML dosyası okunamadı');
        }
        
        // Convert XML to text for chunking
        $textContent = $this->xmlToText($xml);
        return $this->contentChunker->chunkContent($textContent, array_merge([
            'max_chunk_size' => 800,
            'overlap_size' => 150,
            'preserve_words' => true
        ], $config));
    }

    /**
     * Process JSON file
     */
    private function processJSON($file, KnowledgeBase $knowledgeBase, array $config = []): array
    {
        $content = $this->readFileSafely($file->getPathname());
        return $this->contentChunker->chunkJsonContent($content, array_merge([
            'max_items_per_chunk' => 1, // Her ürün için ayrı chunk
            'preserve_structure' => true
        ], $config));
    }

    /**
     * Process Excel file
     */
    private function processExcel($file, KnowledgeBase $knowledgeBase, array $config = []): array
    {
        $content = Excel::toArray(new \stdClass(), $file);
        $textContent = $this->excelToText($content);
        
        return $this->contentChunker->chunkContent($textContent, array_merge([
            'max_chunk_size' => 800,
            'overlap_size' => 150,
            'preserve_words' => true
        ], $config));
    }

    /**
     * Convert XML to text
     */
    private function xmlToText($xml, $depth = 0): string
    {
        $text = '';
        $indent = str_repeat('  ', $depth);
        
        foreach ($xml->children() as $child) {
            $text .= $indent . $child->getName() . ': ' . (string)$child . "\n";
            
            if (count($child->children()) > 0) {
                $text .= $this->xmlToText($child, $depth + 1);
            }
        }
        
        return $text;
    }

    /**
     * Convert Excel to text
     */
    private function excelToText(array $sheets): string
    {
        $text = '';
        
        foreach ($sheets as $sheetIndex => $sheet) {
            $text .= "Sheet " . ($sheetIndex + 1) . ":\n";
            
            foreach ($sheet as $rowIndex => $row) {
                $text .= "Row " . ($rowIndex + 1) . ": " . implode(' | ', $row) . "\n";
            }
            
            $text .= "\n";
        }
        
        return $text;
    }

    /**
     * Get total records count
     */
    private function getTotalRecords($file, $extension): int
    {
        switch ($extension) {
            case 'csv':
                $content = file_get_contents($file->getPathname());
                $lines = explode("\n", $content);
                return count(array_filter($lines, 'trim')) - 1; // Exclude header
            case 'json':
                $content = file_get_contents($file->getPathname());
                $data = json_decode($content, true);
                return is_array($data) ? count($data) : 1;
            case 'xlsx':
            case 'xls':
                $content = Excel::toArray(new \stdClass(), $file);
                return array_sum(array_map('count', $content));
            default:
                return 1;
        }
    }

    /**
     * Search knowledge base
     */
    public function search(Request $request)
    {
        $request->validate([
            'query' => 'required|string|max:1000',
        ]);

        try {
            $query = $request->input('query');
            
            // Intent detection
            $intent = $this->aiService->detectIntent($query);
            
            // Search for relevant chunks
            $chunks = KnowledgeChunk::where('is_indexed', true)
                ->where(function($q) use ($query) {
                    $q->where('content', 'like', '%' . $query . '%')
                      ->orWhereRaw("JSON_EXTRACT(metadata, '$.keywords') LIKE ?", ['%' . $query . '%']);
                })
                ->with('knowledgeBase')
                ->limit(5)
                ->get();
            
            // Generate response
            $response = $this->aiService->generateResponse($query, $chunks->all());
            
            // Log query
            QueryLog::create([
                'site_id' => 1,
                'session_id' => $request->session()->getId() ?? 'api_' . uniqid(),
                'user_id' => Auth::id() ?? 1,
                'query_text' => $query,
                'detected_intent' => $intent['intent'],
                'confidence_score' => $intent['confidence'],
                'response_text' => $response,
                'chunks_used' => $chunks->pluck('id')->all(),
                'response_time_ms' => 0, // TODO: Calculate actual response time
            ]);
            
            return response()->json([
                'success' => true,
                'intent' => $intent,
                'response' => $response,
                'chunks' => $chunks,
                'suggestions' => $this->generateSuggestions($intent)
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Arama yapılırken hata oluştu: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Generate suggestions based on intent
     */
    private function generateSuggestions(array $intent): array
    {
        $suggestions = [];
        
        switch ($intent['intent']) {
            case 'product_search':
                $suggestions = [
                    'Ürün kategorilerini görmek ister misiniz?',
                    'Fiyat aralığı belirtebilir misiniz?',
                    'Hangi markayı tercih edersiniz?'
                ];
                break;
            case 'faq_search':
                $suggestions = [
                    'Sık sorulan sorular sayfasını ziyaret etmek ister misiniz?',
                    'Başka bir konuda yardım almak ister misiniz?'
                ];
                break;
            default:
                $suggestions = [
                    'Ürün aramak ister misiniz?',
                    'Kategorileri keşfetmek ister misiniz?',
                    'Yardım almak ister misiniz?'
                ];
        }
        
        return $suggestions;
    }

    /**
     * Get knowledge base details
     */
    public function show($id)
    {
        $knowledgeBase = KnowledgeBase::with('chunks')->findOrFail($id);
        
        return response()->json([
            'success' => true,
            'knowledge_base' => $knowledgeBase
        ]);
    }

    /**
     * Get knowledge base detail with chunks and stats
     */
    public function getDetail($id)
    {
        try {
            $knowledgeBase = KnowledgeBase::findOrFail($id);
            $chunks = KnowledgeChunk::where('knowledge_base_id', $id)
                ->orderBy('chunk_index', 'asc')
                ->orderBy('created_at', 'asc')
                ->get();
            
            // Calculate statistics
            $stats = [
                'total_chunks' => $chunks->count(),
                'avg_chunk_size' => $chunks->count() > 0 ? round($chunks->avg('content_length') ?? 0) : 0,
                'total_tokens' => $chunks->sum('token_count') ?? 0,
            ];
            
            return response()->json([
                'success' => true,
                'knowledge_base' => $knowledgeBase,
                'chunks' => $chunks,
                'stats' => $stats
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Knowledge base bulunamadı: ' . $e->getMessage()
            ], 404);
        }
    }

    /**
     * Get chunks for a knowledge base
     */
    public function getChunks($id)
    {
        try {
            $knowledgeBase = KnowledgeBase::findOrFail($id);
            $chunks = KnowledgeChunk::where('knowledge_base_id', $id)
                ->orderBy('chunk_index', 'asc')
                ->get();

            return response()->json([
                'success' => true,
                'knowledge_base' => $knowledgeBase,
                'chunks' => $chunks
            ]);
        } catch (\Exception $e) {
            Log::error('Error getting chunks: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Chunk\'lar alınırken hata oluştu: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Refresh chunks from URL and update image vision
     */
    public function refreshChunks(Request $request, $id)
    {
        try {
            $knowledgeBase = KnowledgeBase::findOrFail($id);
            
            // Check if knowledge base is from URL
            if ($knowledgeBase->source_type !== 'url') {
                return response()->json([
                    'success' => false,
                    'message' => 'Bu knowledge base dosyadan oluşturulmuş. Sadece URL\'den oluşturulan knowledge base\'ler yenilenebilir.'
                ], 400);
            }

            // Get original URL from source_path field or description
            $originalUrl = $knowledgeBase->source_path;
            
            // Eğer source_path'de URL yoksa, description'dan çıkar
            if (empty($originalUrl) || !filter_var($originalUrl, FILTER_VALIDATE_URL)) {
                if (preg_match('/URL: (https?:\/\/[^\s|]+)/', $knowledgeBase->description, $matches)) {
                    $originalUrl = $matches[1];
                }
            }
            
            if (empty($originalUrl) || !filter_var($originalUrl, FILTER_VALIDATE_URL)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Orijinal URL bulunamadı. Knowledge base yenilenemez. Lütfen knowledge base\'i tekrar oluşturun.'
                ], 400);
            }

            // Set timeout limit
            set_time_limit(60); // 60 saniye

            DB::beginTransaction();

            try {
                // Fetch content from URL with timeout
                $content = $this->fetchContentFromUrl($originalUrl);
                
                // Process content and create new chunks
                $chunks = $this->contentChunker->chunkContent($content, $knowledgeBase->id);
                
                // Delete old chunks
                KnowledgeChunk::where('knowledge_base_id', $id)->delete();
                
                // Create new chunks
                foreach ($chunks as $chunkData) {
                    $chunk = KnowledgeChunk::create([
                        'knowledge_base_id' => $id,
                        'project_id' => $knowledgeBase->project_id, // Proje ID'sini ekle
                        'content' => $chunkData['content'],
                        'content_type' => $chunkData['content_type'] ?? 'text',
                        'chunk_index' => $chunkData['chunk_index'],
                        'word_count' => $chunkData['word_count'],
                        'chunk_size' => $chunkData['chunk_size'],
                        'content_hash' => $chunkData['content_hash'],
                        'metadata' => $chunkData['metadata'] ?? null,
                        'has_images' => false,
                        'processed_images' => 0,
                        'image_vision' => null,
                    ]);
                }

                // Update knowledge base
                $knowledgeBase->update([
                    'chunk_count' => count($chunks),
                    'updated_at' => now()
                ]);

                DB::commit();

                return response()->json([
                    'success' => true,
                    'message' => 'Chunk\'lar başarıyla yenilendi. ' . count($chunks) . ' yeni chunk oluşturuldu.',
                    'chunk_count' => count($chunks)
                ]);

            } catch (\Exception $e) {
                DB::rollback();
                throw $e;
            }

        } catch (\Exception $e) {
            Log::error('Error refreshing chunks: ' . $e->getMessage());
            
            // Kullanıcı dostu hata mesajları
            $userMessage = 'Chunk\'lar yenilenirken hata oluştu. ';
            
            if (strpos($e->getMessage(), 'Maximum execution time') !== false) {
                $userMessage .= 'İşlem çok uzun sürdü. Lütfen daha sonra tekrar deneyin.';
            } elseif (strpos($e->getMessage(), 'URL') !== false) {
                $userMessage .= 'URL erişilemez durumda. Lütfen URL\'nin doğru olduğundan emin olun.';
            } else {
                $userMessage .= 'Teknik bir hata oluştu. Lütfen daha sonra tekrar deneyin.';
            }
            
            return response()->json([
                'success' => false,
                'message' => $userMessage
            ], 500);
        }
    }

    /**
     * Fetch content from URL
     */
    private function fetchContentFromUrl($url)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 45); // 45 saniye
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15); // Bağlantı timeout'u
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36');
        
        $content = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);

        if ($error) {
            throw new \Exception("URL'den içerik alınamadı. CURL Hatası: {$error}");
        }

        if ($httpCode !== 200 || empty($content)) {
            throw new \Exception("URL'den içerik alınamadı. HTTP Kodu: {$httpCode}");
        }

        return $content;
    }

    /**
     * Delete knowledge base
     */
    public function destroy($id)
    {
        try {
            $knowledgeBase = KnowledgeBase::findOrFail($id);
            
            // Log deletion for audit purposes
            $user = Auth::user();
            Log::info('Knowledge base deleted', [
                'knowledge_base_id' => $id,
                'deleted_by' => $user->id,
                'knowledge_base_name' => $knowledgeBase->name
            ]);
            
            // Delete associated chunks
            $knowledgeBase->chunks()->delete();
            
            // Delete field mappings if any
            FieldMapping::where('knowledge_base_id', $id)->delete();
            
            // Delete file from storage
            if ($knowledgeBase->source_type === 'file' && $knowledgeBase->source_path) {
                Storage::disk('public')->delete($knowledgeBase->source_path);
            }
            
            // Delete knowledge base
            $knowledgeBase->delete();
            
            return response()->json([
                'success' => true,
                'message' => 'Knowledge base başarıyla silindi'
            ]);
            
        } catch (\Exception $e) {
            Log::error('Knowledge base deletion error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Knowledge base silinirken hata oluştu: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Determine file extension from URL and content type
     */
    private function determineExtensionFromUrl(string $url, string $contentType): string
    {
        // Try to get extension from URL first
        $path = parse_url($url, PHP_URL_PATH);
        if ($path) {
            $pathExtension = pathinfo($path, PATHINFO_EXTENSION);
            if (in_array(strtolower($pathExtension), ['csv', 'txt', 'xml', 'json', 'xlsx', 'xls'])) {
                return strtolower($pathExtension);
            }
        }
        
        // Try to determine from content type
        if (str_contains($contentType, 'json')) return 'json';
        if (str_contains($contentType, 'xml')) return 'xml';
        if (str_contains($contentType, 'csv') || str_contains($contentType, 'text/csv')) return 'csv';
        if (str_contains($contentType, 'text/plain')) return 'txt';
        if (str_contains($contentType, 'spreadsheet') || str_contains($contentType, 'excel')) return 'xlsx';
        
        // Default to txt if can't determine
        return 'txt';
    }

    /**
     * Get MIME type for extension
     */
    private function getMimeType(string $extension): string
    {
        return match ($extension) {
            'csv' => 'text/csv',
            'xml' => 'application/xml',
            'json' => 'application/json',
            'txt' => 'text/plain',
            'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'xls' => 'application/vnd.ms-excel',
            default => 'text/plain'
        };
    }

    /**
     * Safely read file content with UTF-8 encoding
     */
    private function readFileSafely(string $path): string
    {
        $content = file_get_contents($path);
        if ($content === false) {
            throw new \Exception("Dosya okunamadı: " . $path);
        }
        
        // UTF-8 encoding kontrolü ve düzeltme
        if (!mb_check_encoding($content, 'UTF-8')) {
            // Farklı encoding'leri dene
            $encodings = ['ISO-8859-1', 'ISO-8859-9', 'Windows-1254', 'Windows-1252', 'ASCII'];
            
            foreach ($encodings as $encoding) {
                if (mb_check_encoding($content, $encoding)) {
                    $content = mb_convert_encoding($content, 'UTF-8', $encoding);
                    break;
                }
            }
            
            // Hala UTF-8 değilse, force convert
            if (!mb_check_encoding($content, 'UTF-8')) {
                $content = mb_convert_encoding($content, 'UTF-8', 'auto');
                \Log::warning("File encoding force converted to UTF-8: " . $path);
            }
        }
        
        return $content;
    }

    /**
     * Optimize FAQ content based on chunks
     */
    private function optimizeFAQContent(KnowledgeBase $knowledgeBase, array $chunks)
    {
        try {
            $faqChunks = collect($chunks)->filter(function ($chunk) {
                return $chunk['content_type'] === 'faq';
            })->values();

            if ($faqChunks->isEmpty()) {
                return;
            }

            // Combine all FAQ content into a single string
            $combinedContent = $faqChunks->pluck('content')->implode("\n\n");
            
            // Optimize FAQ content
            $optimizationResult = $this->faqOptimizer->optimizeFAQContent($combinedContent, [
                'max_questions' => min(10, $faqChunks->count()),
                'question_style' => 'natural',
                'answer_style' => 'detailed',
                'language' => 'tr'
            ]);

            // Update chunks with optimized content
            foreach ($faqChunks as $index => $chunk) {
                if (isset($optimizationResult['faqs'][$index])) {
                    $faq = $optimizationResult['faqs'][$index];
                    
                    // Find the actual chunk model to update
                    $chunkModel = KnowledgeChunk::find($chunk['id'] ?? $chunk['chunk_index']);
                    if ($chunkModel) {
                        $chunkModel->update([
                            'content' => "Soru: {$faq['question']}\n\nCevap: {$faq['answer']}",
                            'content_hash' => hash('sha256', $faq['question'] . $faq['answer']),
                            'metadata' => array_merge($chunkModel->metadata ?? [], [
                                'optimized_at' => Carbon::now(),
                                'faq_data' => $faq,
                                'optimization_score' => $optimizationResult['optimization_score']
                            ])
                        ]);
                    }
                }
            }

            // Update knowledge base metadata
            $knowledgeBase->update([
                'metadata' => array_merge($knowledgeBase->metadata ?? [], [
                    'faq_optimized_at' => Carbon::now(),
                    'faq_optimization_score' => $optimizationResult['optimization_score'],
                    'faq_metadata' => $optimizationResult['metadata']
                ])
            ]);

            Log::info('FAQ optimization completed', [
                'knowledge_base_id' => $knowledgeBase->id,
                'chunks_optimized' => $faqChunks->count(),
                'optimization_score' => $optimizationResult['optimization_score']
            ]);

        } catch (\Exception $e) {
            Log::error('FAQ optimization failed', [
                'knowledge_base_id' => $knowledgeBase->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            // Don't throw exception, just log the error to avoid breaking the upload process
        }
    }

    /**
     * Manually optimize FAQ content for a knowledge base
     */
    public function optimizeFAQ(Request $request, $id)
    {
        try {
            $knowledgeBase = KnowledgeBase::findOrFail($id);
            
            $request->validate([
                'config' => 'array',
                'config.max_questions' => 'integer|min:1|max:20',
                'config.question_style' => 'string|in:natural,formal,casual,technical',
                'config.answer_style' => 'string|in:detailed,concise,step_by_step,technical',
                'config.language' => 'string|in:tr,en,mixed'
            ]);

            $config = $request->input('config', []);
            
            // Get FAQ chunks
            $faqChunks = KnowledgeChunk::where('knowledge_base_id', $id)
                ->where('content_type', 'faq')
                ->get();

            if ($faqChunks->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Bu knowledge base\'de FAQ content bulunamadı'
                ], 404);
            }

            // Combine all FAQ content
            $combinedContent = $faqChunks->pluck('content')->implode("\n\n");
            
            // Optimize FAQ content
            $optimizationResult = $this->faqOptimizer->optimizeFAQContent($combinedContent, $config);
            
            // Update chunks with optimized content
            foreach ($faqChunks as $index => $chunk) {
                if (isset($optimizationResult['faqs'][$index])) {
                    $faq = $optimizationResult['faqs'][$index];
                    
                    $chunk->update([
                        'content' => "Soru: {$faq['question']}\n\nCevap: {$faq['answer']}",
                        'content_hash' => hash('sha256', $faq['question'] . $faq['answer']),
                        'metadata' => array_merge($chunk->metadata ?? [], [
                            'optimized_at' => Carbon::now(),
                            'faq_data' => $faq,
                            'optimization_score' => $optimizationResult['optimization_score']
                        ])
                    ]);
                }
            }

            // Update knowledge base metadata
            $knowledgeBase->update([
                'metadata' => array_merge($knowledgeBase->metadata ?? [], [
                    'faq_optimized_at' => Carbon::now(),
                    'faq_optimization_score' => $optimizationResult['optimization_score'],
                    'faq_metadata' => $optimizationResult['metadata']
                ])
            ]);

            return response()->json([
                'success' => true,
                'message' => 'FAQ content başarıyla optimize edildi',
                'optimization_result' => $optimizationResult,
                'chunks_updated' => $faqChunks->count()
            ]);

        } catch (\Exception $e) {
            Log::error('FAQ optimization error: ' . $e->getMessage(), [
                'knowledge_base_id' => $id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'FAQ optimizasyonu başarısız: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Evaluate chunk quality
     */
    private function evaluateChunkQuality(array $chunks): array
    {
        if (empty($chunks)) {
            return [
                'overlap_quality' => 0,
                'size_consistency' => 0,
                'content_quality' => 0,
                'overall_score' => 0
            ];
        }

        $totalChunks = count($chunks);
        $sizes = array_column($chunks, 'chunk_size');
        $avgSize = array_sum($sizes) / $totalChunks;
        
        // Size consistency
        $sizeVariance = 0;
        foreach ($sizes as $size) {
            $sizeVariance += pow($size - $avgSize, 2);
        }
        $sizeVariance = $sizeVariance / $totalChunks;
        $sizeConsistency = max(0, 100 - ($sizeVariance / 100));
        
        // Content quality (basic check)
        $contentQuality = 0;
        foreach ($chunks as $chunk) {
            if (isset($chunk['content']) && strlen($chunk['content']) > 50) {
                $contentQuality += 100;
            }
        }
        $contentQuality = $contentQuality / $totalChunks;
        
        // Overlap quality (simplified)
        $overlapQuality = 80; // Default value
        
        $overallScore = ($sizeConsistency + $contentQuality + $overlapQuality) / 3;
        
        return [
            'overlap_quality' => $overlapQuality,
            'size_consistency' => $sizeConsistency,
            'content_quality' => $contentQuality,
            'overall_score' => $overallScore
        ];
    }

    /**
     * Analyze JSON content and determine its type
     */
    private function analyzeJsonContent(string $content): string
    {
        // FAQ detection patterns
        $faqPatterns = [
            '"soru":', '"cevap":', '"sorular":', '"cevaplar":',
            '"question":', '"answer":', '"questions":', '"answers":',
            '"faq":', '"frequently":', '"asked":'
        ];
        
        foreach ($faqPatterns as $pattern) {
            if (str_contains($content, $pattern)) {
                return 'faq';
            }
        }
        
        // Product detection patterns - more comprehensive
        $productPatterns = [
            // Basic product structure
            ['"id":', ['"name":', '"title":'], ['"price":', '"category":', '"description":']],
            // Flexible product structure
            ['"id":', ['"name":', '"title":'], ['"brand":', '"category":', '"price":']],
            // Title-based product
            ['"title":', '"price":', '"category":'],
            // Plan/Package detection
            ['"name":', ['plan', 'package', 'starter', 'professional', 'enterprise', 'premium', 'basic']]
        ];
        
        foreach ($productPatterns as $pattern) {
            if ($this->matchesProductPattern($content, $pattern)) {
                return 'product';
            }
        }
        
        // Default to general if no specific pattern is found
        return 'general';
    }
    
    /**
     * Check if content matches a product pattern
     */
    private function matchesProductPattern(string $content, array $pattern): bool
    {
        if (count($pattern) === 3 && is_array($pattern[1]) && is_array($pattern[2])) {
            // Pattern: ["id":, ["name":, "title":], ["price":, "category":]]
            if (!str_contains($content, $pattern[0])) return false;
            
            $hasNameOrTitle = false;
            foreach ($pattern[1] as $namePattern) {
                if (str_contains($content, $namePattern)) {
                    $hasNameOrTitle = true;
                    break;
                }
            }
            if (!$hasNameOrTitle) return false;
            
            $hasProperty = false;
            foreach ($pattern[2] as $propPattern) {
                if (str_contains($content, $propPattern)) {
                    $hasProperty = true;
                    break;
                }
            }
            return $hasProperty;
            
        } elseif (count($pattern) === 3 && is_string($pattern[0]) && is_string($pattern[1]) && is_string($pattern[2])) {
            // Pattern: ["title":, "price":, "category":]
            return str_contains($content, $pattern[0]) && 
                   str_contains($content, $pattern[1]) && 
                   str_contains($content, $pattern[2]);
                   
        } elseif (count($pattern) === 2 && is_string($pattern[0]) && is_array($pattern[1])) {
            // Pattern: ["name":, ["plan", "package", ...]]
            if (!str_contains($content, $pattern[0])) return false;
            
            foreach ($pattern[1] as $keyword) {
                if (str_contains($content, $keyword)) {
                    return true;
                }
            }
            return false;
        }
        
        return false;
    }

    /**
     * Determine content type of a chunk
     */
    private function determineContentType(string $content, string $extension): string
    {
        // Basic checks for common FAQ patterns
        $content = strtolower($content);
        if (str_contains($content, 'soru:') || str_contains($content, 'cevap:') || str_contains($content, 'sorular:') || str_contains($content, 'cevaplar:')) {
            return 'faq';
        }

        // More sophisticated checks for specific file types
        if ($extension === 'txt') {
            if (str_contains($content, 'soru:') || str_contains($content, 'cevap:') || str_contains($content, 'sorular:') || str_contains($content, 'cevaplar:')) {
                return 'faq';
            }
            if (str_contains($content, 'faq:') || str_contains($content, 'soru:') || str_contains($content, 'cevap:')) {
                return 'faq';
            }
        }

        if ($extension === 'csv') {
            // Look for specific columns or patterns in CSV
            if (str_contains($content, 'soru,cevap') || str_contains($content, 'soru,cevaplar') || str_contains($content, 'soru,cevaplar')) {
                return 'faq';
            }
        }

        if ($extension === 'json') {
            return $this->analyzeJsonContent($content);
        }

        if ($extension === 'xml') {
            // Look for specific XML tags or attributes
            if (str_contains($content, '<soru>') || str_contains($content, '<cevap>') || str_contains($content, '<sorular>') || str_contains($content, '<cevaplar>')) {
                return 'faq';
            }
        }

        // Default to 'general' if no specific pattern is found
        return 'general';
    }

    /**
     * AI-powered field mapping - JSON verilerini analiz ederek otomatik field mapping yapar
     */
    public function performAIFieldMapping(Request $request)
    {
        try {
            $request->validate([
                'json_data' => 'required|array',
                'content_type' => 'nullable|string|in:auto,product,faq,general',
                'project_id' => 'nullable|exists:projects,id'
            ]);

            $jsonData = $request->input('json_data');
            $contentType = $request->input('content_type', 'auto');
            $projectId = $request->input('project_id');

            Log::info('AI Field Mapping request received', [
                'data_count' => count($jsonData),
                'content_type' => $contentType,
                'project_id' => $projectId
            ]);

            // AI Service ile field mapping yap
            $mappingResult = $this->aiService->performAIFieldMapping($jsonData, $contentType);

            if (!$mappingResult['success']) {
                return response()->json([
                    'success' => false,
                    'message' => 'AI Field Mapping başarısız: ' . $mappingResult['error']
                ], 500);
            }

            // Mapping sonuçlarını logla
            Log::info('AI Field Mapping completed', [
                'original_fields' => $mappingResult['analysis']['detected_fields'] ?? [],
                'mapped_fields' => array_keys($mappingResult['field_mapping']['field_mapping'] ?? []),
                'content_type' => $mappingResult['content_type'],
                'confidence_score' => $mappingResult['confidence_score']
            ]);

            return response()->json([
                'success' => true,
                'message' => 'AI Field Mapping başarıyla tamamlandı',
                'data' => [
                    'original_data' => $mappingResult['original_data'],
                    'mapped_data' => $mappingResult['mapped_data'],
                    'field_mapping' => $mappingResult['field_mapping'],
                    'analysis' => $mappingResult['analysis'],
                    'content_type' => $mappingResult['content_type'],
                    'confidence_score' => $mappingResult['confidence_score']
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('AI Field Mapping error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'AI Field Mapping hatası: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Detect fields from uploaded file - GEÇİCİ OLARAK DEVRE DIŞI
     */
    public function detectFields(Request $request, $id)
    {
        return response()->json([
            'success' => false,
            'message' => 'Field mapping sistemi geçici olarak devre dışı bırakıldı. Sadece JSON dosya ve URL desteği aktif.'
        ], 503);
    }

    /**
     * Save field mappings - GEÇİCİ OLARAK DEVRE DIŞI
     */
    public function saveFieldMappings(Request $request, $id)
    {
        return response()->json([
            'success' => false,
            'message' => 'Field mapping sistemi geçici olarak devre dışı bırakıldı. Sadece JSON dosya ve URL desteği aktif.'
        ], 503);
    }

    /**
     * Get field mappings for knowledge base - GEÇİCİ OLARAK DEVRE DIŞI
     */
    public function getFieldMappings($id)
    {
        return response()->json([
            'success' => false,
            'message' => 'Field mapping sistemi geçici olarak devre dışı bırakıldı. Sadece JSON dosya ve URL desteği aktif.'
        ], 503);
    }

    /**
     * Preview transformed data - GEÇİCİ OLARAK DEVRE DIŞI
     */
    public function previewTransformedData(Request $request, $id)
    {
        return response()->json([
            'success' => false,
            'message' => 'Field mapping sistemi geçici olarak devre dışı bırakıldı. Sadece JSON dosya ve URL desteği aktif.'
        ], 503);
    }

    /**
     * Get sample data from file for preview
     */
    private function getSampleDataFromFile(string $filePath, string $fileType, int $rows = 5): array
    {
        return $this->fieldMappingService->getSampleData($filePath, $fileType, $rows);
    }

    /**
     * Validate data against field mappings - GEÇİCİ OLARAK DEVRE DIŞI
     */
    public function validateData(Request $request, $id)
    {
        return response()->json([
            'success' => false,
            'message' => 'Field mapping sistemi geçici olarak devre dışı bırakıldı. Sadece JSON dosya ve URL desteği aktif.'
        ], 503);
    }

    /**
     * Process data in batches - GEÇİCİ OLARAK DEVRE DIŞI
     */
    public function processBatchData(Request $request, $id)
    {
        return response()->json([
            'success' => false,
            'message' => 'Field mapping sistemi geçici olarak devre dışı bırakıldı. Sadece JSON dosya ve URL desteği aktif.'
        ], 503);
    }

    /**
     * Get field mapping statistics - GEÇİCİ OLARAK DEVRE DIŞI
     */
    public function getMappingStats($id)
    {
        return response()->json([
            'success' => false,
            'message' => 'Field mapping sistemi geçici olarak devre dışı bırakıldı. Sadece JSON dosya ve URL desteği aktif.'
        ], 503);
    }

    /**
     * Export transformed data - GEÇİCİ OLARAK DEVRE DIŞI
     */
    public function exportTransformedData(Request $request, $id)
    {
        return response()->json([
            'success' => false,
            'message' => 'Field mapping sistemi geçici olarak devre dışı bırakıldı. Sadece JSON dosya ve URL desteği aktif.'
        ], 503);
    }

    /**
     * Export data to CSV
     */
    private function exportToCsv(array $data, string $filename)
    {
        if (empty($data)) {
            return response()->json([
                'success' => false,
                'message' => 'No data to export'
            ], 400);
        }

        $headers = array_keys($data[0]);
        $csvContent = implode(',', $headers) . "\n";

        foreach ($data as $row) {
            $csvRow = [];
            foreach ($headers as $header) {
                $value = $row[$header] ?? '';
                $csvRow[] = '"' . str_replace('"', '""', $value) . '"';
            }
            $csvContent .= implode(',', $csvRow) . "\n";
        }

        $filename = str_replace(' ', '_', $filename) . '_transformed.csv';

        return response($csvContent)
            ->header('Content-Type', 'text/csv')
            ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
    }

    /**
     * Mevcut chunk'larda resim analizi yeniler
     */
    public function refreshImageAnalysis(Request $request)
    {
        try {
            
            // Background job olarak çalıştır
            \Artisan::queue('app:process-product-updates', ['--refresh-chunks' => true]);
            
            return response()->json([
                'success' => true,
                'message' => 'Resim analizi yenileme başlatıldı. Bu işlem arka planda çalışacak.',
                'job_started' => true
            ]);
            
        } catch (\Exception $e) {
            Log::error('Image analysis refresh error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Resim analizi yenileme hatası: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Resim analizi durumunu kontrol eder
     */
    public function getImageAnalysisStatus()
    {
        try {
            $chunks = KnowledgeChunk::all();
            
            $summary = [
                'total_chunks' => $chunks->count(),
                'chunks_with_images' => $chunks->where('has_images', true)->count(),
                'chunks_without_images' => $chunks->where('has_images', false)->count(),
                'total_images_processed' => $chunks->sum('processed_images'),
                'chunks_with_vision' => $chunks->whereNotNull('image_vision')->count(),
                'last_updated' => $chunks->max('updated_at')?->diffForHumans()
            ];
            
            return response()->json([
                'success' => true,
                'summary' => $summary
            ]);
            
        } catch (\Exception $e) {
            Log::error('Image analysis status error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Durum bilgisi alınamadı: ' . $e->getMessage()
            ], 500);
        }
    }

}
