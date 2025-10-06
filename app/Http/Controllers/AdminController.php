<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\AdminUser;
use App\Models\Campaign;
use App\Models\FAQ;
use App\Models\KnowledgeBase;
use App\Models\EnhancedChatSession;
use App\Models\DemoRequest;

class AdminController extends Controller
{
    /**
     * Show admin dashboard
     */
    public function index()
    {
        $admin = Auth::user();
        
        // Admin istatistikleri
        $stats = [
            'total_users' => User::count(),
            'total_campaigns' => Campaign::count(),
            'total_faqs' => FAQ::count(),
            'total_knowledge_bases' => KnowledgeBase::count(),
            'total_chat_sessions' => EnhancedChatSession::count(),
            'total_demo_requests' => DemoRequest::count(),
            'pending_demo_requests' => DemoRequest::where('status', 'pending')->count(),
            'recent_users' => User::latest()->take(5)->get(),
            'recent_campaigns' => Campaign::latest()->take(5)->get(),
            'recent_demo_requests' => DemoRequest::latest()->take(5)->get(),
        ];
        
        return view('admin.dashboard', compact('admin', 'stats'));
    }

    /**
     * Show admin profile
     */
    public function profile()
    {
        $admin = Auth::user();
        return view('admin.profile', compact('admin'));
    }

    /**
     * Show admin settings
     */
    public function settings()
    {
        $admin = Auth::user();
        return view('admin.settings', compact('admin'));
    }
    /**
     * Get user for editing
     */
    public function getUser($id)
    {
        $user = User::findOrFail($id);
        return response()->json($user);
    }

    /**
     * Update user
     */
    public function updateUser(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $id,
            'bio' => 'nullable|string|max:1000',
        ]);

        $user = User::findOrFail($id);
        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'bio' => $request->bio,
        ]);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Kullanıcı başarıyla güncellendi.',
                'user' => $user
            ]);
        }

        return back()->with('success', 'Kullanıcı başarıyla güncellendi.');
    }

    /**
     * Toggle admin status
     */
    public function toggleAdmin($id)
    {
        $user = User::findOrFail($id);
        
        // Kendini admin yapamaz
        if ($user->id === Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'Kendi admin yetkinizi kaldıramazsınız.'
            ], 400);
        }

        $user->update(['is_admin' => !$user->is_admin]);

        return response()->json([
            'success' => true,
            'message' => $user->is_admin ? 'Kullanıcı admin yapıldı.' : 'Admin yetkisi kaldırıldı.',
            'is_admin' => $user->is_admin
        ]);
    }

    /**
     * Delete user
     */
    public function deleteUser($id)
    {
        $user = User::findOrFail($id);
        
        // Kendini silemez
        if ($user->id === Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'Kendinizi silemezsiniz.'
            ], 400);
        }

        // Kullanıcının kampanyalarını sil
        $user->campaigns()->delete();
        
        // Kullanıcıyı sil
        $user->delete();

        return response()->json([
            'success' => true,
            'message' => 'Kullanıcı başarıyla silindi.'
        ]);
    }

    /**
     * Show system analytics
     */
    public function analytics()
    {
        $admin = Auth::user();
        return view('admin.analytics', compact('admin'));
    }

    /**
     * Load analytics content via AJAX
     */
    public function loadAnalyticsContent()
    {
        try {
            // Sistem istatistikleri
            $stats = [
                'total_users' => User::count(),
                'active_users' => User::where('last_login_at', '>', now()->subDays(30))->count(),
                'total_campaigns' => Campaign::count(),
                'active_campaigns' => Campaign::active()->count(),
                'total_faqs' => FAQ::count(),
                'total_knowledge_bases' => KnowledgeBase::count(),
                'total_chat_sessions' => EnhancedChatSession::count(),
                'total_demo_requests' => DemoRequest::count(),
                'pending_demo_requests' => DemoRequest::where('status', 'pending')->count(),
                'recent_chat_sessions' => EnhancedChatSession::with('user')->latest()->take(10)->get(),
                'recent_demo_requests' => DemoRequest::latest()->take(10)->get(),
            ];
            
            return response()->json([
                'success' => true,
                'data' => compact('stats')
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Analytics verileri yüklenirken hata oluştu: ' . $e->getMessage()
            ], 500);
        }
    }
}
