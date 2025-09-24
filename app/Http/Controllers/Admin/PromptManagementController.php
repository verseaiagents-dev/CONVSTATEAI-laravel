<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PromptTemplate;
use App\Services\PromptManagementService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class PromptManagementController extends Controller
{
    protected $promptService;

    public function __construct(PromptManagementService $promptService)
    {
        $this->promptService = $promptService;
        $this->middleware('auth');
        $this->middleware('can:manage-prompts');
    }

    /**
     * Prompt listesi
     */
    public function index(Request $request)
    {
        $query = PromptTemplate::with(['creator', 'updater']);

        // Filtreleme
        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        if ($request->filled('environment')) {
            $query->where('environment', $request->environment);
        }

        if ($request->filled('is_active')) {
            $query->where('is_active', $request->boolean('is_active'));
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('content', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $prompts = $query->orderBy('updated_at', 'desc')->paginate(20);
        $categories = $this->promptService->getCategories();
        $statistics = $this->promptService->getStatistics();

        return view('admin.prompts.index', compact('prompts', 'categories', 'statistics'));
    }

    /**
     * Prompt oluşturma formu
     */
    public function create()
    {
        $categories = $this->promptService->getCategories();
        return view('admin.prompts.create', compact('categories'));
    }

    /**
     * Prompt kaydet
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'category' => 'required|in:' . implode(',', array_keys($this->promptService->getCategories())),
            'content' => 'required|string|max:10000',
            'description' => 'nullable|string|max:1000',
            'variables' => 'nullable|array',
            'variables.*' => 'string|max:255',
            'metadata' => 'nullable|array',
            'is_active' => 'boolean',
            'environment' => 'required|in:test,production',
            'priority' => 'integer|min:0|max:100',
            'language' => 'string|max:5',
            'tags' => 'nullable|array',
            'tags.*' => 'string|max:50'
        ]);

        if ($validator->fails()) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }
            
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            $prompt = new PromptTemplate();
            $prompt->fill([
                'name' => $request->name,
                'category' => $request->category,
                'content' => $request->content,
                'description' => $request->description,
                'variables' => $request->variables ?? [],
                'metadata' => $request->metadata ?? [],
                'tags' => $request->tags ?? [],
                'is_active' => $request->boolean('is_active', true),
                'environment' => $request->environment,
                'priority' => $request->priority ?? 0,
                'language' => $request->language ?? 'tr',
                'created_by' => auth()->id(),
                'updated_by' => auth()->id()
            ]);
            $prompt->save();

            Log::info('Prompt template created', [
                'id' => $prompt->id,
                'name' => $prompt->name,
                'category' => $prompt->category,
                'created_by' => auth()->id()
            ]);

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Prompt template başarıyla oluşturuldu.',
                    'prompt' => $prompt
                ]);
            }

            return redirect()->route('admin.prompts.index')
                ->with('success', 'Prompt template başarıyla oluşturuldu.');

        } catch (\Exception $e) {
            Log::error('Error creating prompt template', [
                'error' => $e->getMessage(),
                'data' => $request->all()
            ]);

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Prompt template oluşturulurken hata oluştu: ' . $e->getMessage()
                ], 500);
            }

            return redirect()->back()
                ->with('error', 'Prompt template oluşturulurken hata oluştu: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Prompt düzenleme formu
     */
    public function edit(PromptTemplate $prompt)
    {
        $categories = $this->promptService->getCategories();
        return view('admin.prompts.edit', compact('prompt', 'categories'));
    }

    /**
     * Prompt güncelle
     */
    public function update(Request $request, PromptTemplate $prompt)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'category' => 'required|in:' . implode(',', array_keys($this->promptService->getCategories())),
            'content' => 'required|string|max:10000',
            'description' => 'nullable|string|max:1000',
            'variables' => 'nullable|array',
            'variables.*' => 'string|max:255',
            'metadata' => 'nullable|array',
            'is_active' => 'boolean',
            'environment' => 'required|in:test,production',
            'priority' => 'integer|min:0|max:100',
            'language' => 'string|max:5',
            'tags' => 'nullable|array',
            'tags.*' => 'string|max:50'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            $oldVersion = $prompt->version ?? 1;
            
            $prompt->fill([
                'name' => $request->name,
                'category' => $request->category,
                'content' => $request->content,
                'description' => $request->description,
                'variables' => $request->variables ?? [],
                'metadata' => $request->metadata ?? [],
                'tags' => $request->tags ?? [],
                'is_active' => $request->boolean('is_active', true),
                'environment' => $request->environment,
                'priority' => $request->priority ?? 0,
                'language' => $request->language ?? 'tr',
                'version' => $oldVersion + 1,
                'updated_by' => auth()->id()
            ]);
            $prompt->save();

            Log::info('Prompt template updated', [
                'id' => $prompt->id,
                'name' => $prompt->name,
                'version' => $oldVersion + 1,
                'updated_by' => auth()->id()
            ]);

            return redirect()->route('admin.prompts.index')
                ->with('success', 'Prompt template başarıyla güncellendi.');

        } catch (\Exception $e) {
            Log::error('Error updating prompt template', [
                'id' => $prompt->id,
                'error' => $e->getMessage(),
                'data' => $request->all()
            ]);

            return redirect()->back()
                ->with('error', 'Prompt template güncellenirken hata oluştu: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Prompt sil
     */
    public function destroy(PromptTemplate $prompt)
    {
        try {
            $promptName = $prompt->name;
            $prompt->delete();

            Log::info('Prompt template deleted', [
                'id' => $prompt->id,
                'name' => $promptName,
                'deleted_by' => auth()->id()
            ]);

            return redirect()->route('admin.prompts.index')
                ->with('success', 'Prompt template başarıyla silindi.');

        } catch (\Exception $e) {
            Log::error('Error deleting prompt template', [
                'id' => $prompt->id,
                'error' => $e->getMessage()
            ]);

            return redirect()->back()
                ->with('error', 'Prompt template silinirken hata oluştu: ' . $e->getMessage());
        }
    }

    /**
     * Prompt test et
     */
    public function test(Request $request, PromptTemplate $prompt)
    {
        $validator = Validator::make($request->all(), [
            'variables' => 'nullable|array',
            'test_data' => 'nullable|array'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $result = $this->promptService->testPrompt(
                $prompt->content,
                $request->variables ?? [],
                $request->test_data ?? []
            );

            return response()->json($result);

        } catch (\Exception $e) {
            Log::error('Error testing prompt', [
                'prompt_id' => $prompt->id,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'error' => 'Test sırasında hata oluştu: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Prompt performans analizi
     */
    public function analyze(PromptTemplate $prompt)
    {
        try {
            $analysis = $this->promptService->analyzePromptPerformance($prompt->id);
            $optimization = $this->promptService->optimizePrompt($prompt->id);

            return response()->json([
                'performance' => $analysis,
                'optimization' => $optimization
            ]);

        } catch (\Exception $e) {
            Log::error('Error analyzing prompt', [
                'prompt_id' => $prompt->id,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'error' => 'Analiz sırasında hata oluştu: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Prompt istatistikleri
     */
    public function statistics()
    {
        try {
            $statistics = $this->promptService->getStatistics();
            return response()->json($statistics);

        } catch (\Exception $e) {
            Log::error('Error getting prompt statistics', [
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'error' => 'İstatistikler alınırken hata oluştu: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Prompt'u aktif/pasif yap
     */
    public function toggle(PromptTemplate $prompt)
    {
        try {
            $prompt->update([
                'is_active' => !$prompt->is_active,
                'updated_by' => auth()->id()
            ]);

            $status = $prompt->is_active ? 'aktif' : 'pasif';

            Log::info('Prompt template toggled', [
                'id' => $prompt->id,
                'is_active' => $prompt->is_active,
                'updated_by' => auth()->id()
            ]);

            return response()->json([
                'success' => true,
                'message' => "Prompt template {$status} yapıldı.",
                'is_active' => $prompt->is_active
            ]);

        } catch (\Exception $e) {
            Log::error('Error toggling prompt template', [
                'id' => $prompt->id,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'error' => 'Durum değiştirilirken hata oluştu: ' . $e->getMessage()
            ], 500);
        }
    }
}