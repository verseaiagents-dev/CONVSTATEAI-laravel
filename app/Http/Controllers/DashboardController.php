<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Project;
use App\Models\KnowledgeBase;
use App\Models\Intent;
use App\Models\EventLog;
use App\Models\EnhancedChatSession;
use App\Models\ProductInteraction;
use App\Models\Product;

class DashboardController extends Controller
{
    /**
     * Show dashboard
     */
    public function index()
    {
        $user = Auth::user();
        
        // Dashboard istatistikleri
        $stats = [
            'total_projects' => Project::where('created_by', $user->id)->count(),
            'total_knowledge_bases' => KnowledgeBase::whereIn('project_id', function($query) use ($user) {
                $query->select('id')->from('projects')->where('created_by', $user->id);
            })->count(),
            'total_chat_sessions' => EnhancedChatSession::where('user_id', $user->id)->count(),
            'total_intents' => Intent::count(),
            'total_products' => Product::count(),
            'total_interactions' => ProductInteraction::whereIn('session_id', function($query) use ($user) {
                $query->select('session_id')->from('enhanced_chat_sessions')->where('user_id', $user->id);
            })->count(),
        ];

        // Son 7 gün chat session trendi
        $chatTrend = EnhancedChatSession::where('user_id', $user->id)
            ->where('created_at', '>=', now()->subDays(7))
            ->selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->pluck('count', 'date')
            ->toArray();

        // En çok kullanılan intents
        $topIntents = Intent::withCount('keywords')
            ->orderBy('keywords_count', 'desc')
            ->take(5)
            ->get();

        // Son chat oturumları
        $recentChatSessions = EnhancedChatSession::where('user_id', $user->id)
            ->latest()
            ->take(5)
            ->get();

        // Knowledge base durumları
        $kbStatuses = KnowledgeBase::whereIn('project_id', function($query) use ($user) {
            $query->select('id')->from('projects')->where('created_by', $user->id);
        })
        ->selectRaw('processing_status as status, COUNT(*) as count')
        ->groupBy('processing_status')
        ->get()
        ->pluck('count', 'status')
        ->toArray();

        // Proje bazlı knowledge base sayıları
        $projectStats = Project::where('created_by', $user->id)
            ->withCount(['knowledgeBases', 'chatSessions'])
            ->take(5)
            ->get();

        return view('dashboard.index', compact('user', 'stats', 'chatTrend', 'topIntents', 'recentChatSessions', 'kbStatuses', 'projectStats'));
    }

    /**
     * Show settings page
     */
    public function settings()
    {
        $user = Auth::user();
        return view('dashboard.settings', compact('user'));
    }

    /**
     * Update profile
     */
    public function updateProfile(Request $request)
    {
        $user = Auth::user();
        
        $request->validate([
            'name' => 'required|string|max:255',
            'bio' => 'nullable|string|max:1000',
        ]);

        $user->update([
            'name' => $request->name,
            'bio' => $request->bio,
        ]);

        return back()->with('success', 'Profil başarıyla güncellendi.');
    }

    /**
     * Update avatar
     */
    public function updateAvatar(Request $request)
    {
        $request->validate([
            'avatar' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        $user = Auth::user();
        
        // Eski avatar'ı sil
        if ($user->avatar) {
            \Storage::disk('public')->delete($user->avatar);
        }
        
        // Yeni avatar'ı yükle
        $avatarPath = $request->file('avatar')->store('avatars', 'public');
        
        $user->update(['avatar' => $avatarPath]);
        
        // AJAX request için JSON response, normal request için redirect
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Profil resmi başarıyla güncellendi.',
                'avatar_url' => $user->getAvatarUrl()
            ]);
        }
        
        return back()->with('success', 'Profil resmi başarıyla güncellendi.');
    }

    /**
     * Remove avatar
     */
    public function removeAvatar(Request $request)
    {
        $user = Auth::user();
        
        if ($user->avatar) {
            \Storage::disk('public')->delete($user->avatar);
            $user->update(['avatar' => null]);
        }
        
        // AJAX request için JSON response, normal request için redirect
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Profil resmi kaldırıldı.',
                'avatar_url' => $user->getAvatarUrl()
            ]);
        }
        
        return back()->with('success', 'Profil resmi kaldırıldı.');
    }

    /**
     * Update password
     */
    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required|current_password',
            'password' => 'required|string|min:8|confirmed',
        ]);

        Auth::user()->update([
            'password' => bcrypt($request->password),
        ]);

        return back()->with('success', 'Şifre başarıyla güncellendi.');
    }

    /**
     * Show widget design page
     */
    public function widgetDesign()
    {
        $user = Auth::user();
        return view('dashboard.widget-design', compact('user'));
    }
}
