<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DemoRequest;
use App\Services\MailService;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class DemoRequestController extends Controller
{
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:demo_requests,email',
            'password' => 'required|string|min:8',
            'phone' => 'nullable|string|max:20',
            'site_visitor_count' => 'nullable|integer|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Lütfen tüm alanları doğru şekilde doldurun.',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $demoRequest = DemoRequest::create([
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'phone' => $request->phone,
                'site_visitor_count' => $request->site_visitor_count,
                'status' => 'pending'
            ]);

            // Admin'e email bildirimi gönder
            $mailService = new MailService();
            $mailService->sendDemoRequestNotification($demoRequest);

            return response()->json([
                'success' => true,
                'message' => 'Demo talebiniz başarıyla alındı! En kısa sürede sizinle iletişime geçeceğiz.',
                'data' => $demoRequest
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Bir hata oluştu. Lütfen tekrar deneyin.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function index()
    {
        $demoRequests = DemoRequest::orderBy('created_at', 'desc')->paginate(20);
        return view('admin.demo-requests.index', compact('demoRequests'));
    }

    public function show(DemoRequest $demoRequest)
    {
        return view('admin.demo-requests.show', compact('demoRequest'));
    }

    public function updateStatus(Request $request, DemoRequest $demoRequest)
    {
        $validator = Validator::make($request->all(), [
            'status' => 'required|in:pending,contacted,completed,cancelled',
            'notes' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Geçersiz veri.',
                'errors' => $validator->errors()
            ], 422);
        }

        $demoRequest->update([
            'status' => $request->status,
            'notes' => $request->notes
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Durum başarıyla güncellendi.',
            'data' => $demoRequest
        ]);
    }
}
