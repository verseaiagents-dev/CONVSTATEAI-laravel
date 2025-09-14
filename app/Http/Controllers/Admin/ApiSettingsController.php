<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ApiSetting;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ApiSettingsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $apiSettings = ApiSetting::orderBy('created_at', 'desc')
            ->get();

        return view('admin.api-settings.index', compact('apiSettings'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Not used anymore - modal is used instead
        return redirect()->route('admin.api-settings.index');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'provider' => 'required|string|max:100',
            'api_key' => 'required|string',
            'base_url' => 'nullable|url|max:500',
            'description' => 'nullable|string|max:1000',
            'is_active' => 'boolean',
        ]);

        $apiSetting = ApiSetting::create([
            'name' => $request->name,
            'provider' => $request->provider,
            'api_key' => $request->api_key,
            'base_url' => $request->base_url,
            'description' => $request->description,
            'is_active' => $request->boolean('is_active', true),
            'is_default' => false, // Varsayılan olarak false
        ]);

        return response()->json([
            'success' => true,
            'message' => 'API ayarı başarıyla oluşturuldu.',
            'data' => $apiSetting
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(ApiSetting $apiSetting)
    {
        return response()->json([
            'success' => true,
            'data' => [
                'id' => $apiSetting->id,
                'name' => $apiSetting->name,
                'provider' => $apiSetting->provider,
                'api_key' => $apiSetting->api_key, // Decrypted API key
                'base_url' => $apiSetting->base_url,
                'description' => $apiSetting->description,
                'is_active' => $apiSetting->is_active,
                'created_at' => $apiSetting->created_at,
                'updated_at' => $apiSetting->updated_at,
            ]
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ApiSetting $apiSetting)
    {
        // Not used anymore - modal is used instead
        return redirect()->route('admin.api-settings.index');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ApiSetting $apiSetting): JsonResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'provider' => 'required|string|max:100',
            'api_key' => 'required|string',
            'base_url' => 'nullable|url|max:500',
            'description' => 'nullable|string|max:1000',
            'is_active' => 'boolean',
        ]);

        $apiSetting->update([
            'name' => $request->name,
            'provider' => $request->provider,
            'api_key' => $request->api_key,
            'base_url' => $request->base_url,
            'description' => $request->description,
            'is_active' => $request->boolean('is_active', true),
            'is_default' => false, // Varsayılan olarak false
        ]);

        return response()->json([
            'success' => true,
            'message' => 'API ayarı başarıyla güncellendi.',
            'data' => $apiSetting
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ApiSetting $apiSetting): JsonResponse
    {
        $apiSetting->delete();

        return response()->json([
            'success' => true,
            'message' => 'API ayarı başarıyla silindi.'
        ]);
    }

    /**
     * Toggle active status
     */
    public function toggleActive(ApiSetting $apiSetting): JsonResponse
    {
        try {
            $oldStatus = $apiSetting->is_active;
            $newStatus = !$oldStatus;
            
            $apiSetting->update(['is_active' => $newStatus]);

            return response()->json([
                'success' => true,
                'message' => $newStatus ? 'API aktif edildi.' : 'API pasif edildi.',
                'is_active' => $apiSetting->fresh()->is_active
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'API durumu güncellenirken hata oluştu: ' . $e->getMessage()
            ], 500);
        }
    }
}
