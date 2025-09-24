<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DemoRequest;
use Illuminate\Http\Request;

class DemoRequestController extends Controller
{
    /**
     * Display a listing of demo requests
     */
    public function index()
    {
        $demoRequests = DemoRequest::orderBy('created_at', 'desc')->paginate(20);
        
        return view('admin.demo-requests.index', compact('demoRequests'));
    }

    /**
     * Display the specified demo request
     */
    public function show(DemoRequest $demoRequest)
    {
        return view('admin.demo-requests.show', compact('demoRequest'));
    }

    /**
     * Update the status of a demo request
     */
    public function updateStatus(Request $request, DemoRequest $demoRequest)
    {
        $request->validate([
            'status' => 'required|in:pending,contacted,completed,cancelled'
        ]);

        $oldStatus = $demoRequest->status;
        $demoRequest->update(['status' => $request->status]);

        $statusLabels = [
            'pending' => 'Bekleyen',
            'contacted' => 'İletişim Kuruldu',
            'completed' => 'Tamamlandı',
            'cancelled' => 'İptal Edildi'
        ];

        $message = "Demo talebi durumu '{$statusLabels[$request->status]}' olarak güncellendi.";

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => $message,
                'old_status' => $oldStatus,
                'new_status' => $request->status
            ]);
        }

        return back()->with('success', $message);
    }
}
