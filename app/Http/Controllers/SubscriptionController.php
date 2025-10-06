<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Plan;
use App\Models\Subscription;
use App\Models\PlanRequest;
// UsageToken modeli kaldırıldı - artık User tablosunda token bilgileri tutuluyor
use App\Events\UserSubscriptionActivated;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class SubscriptionController extends Controller
{
    /**
     * Subscription sayfasını göster
     */
    public function index()
    {
        $user = Auth::user();
        $currentSubscription = $user->activeSubscription;
        // Token bilgileri artık User tablosunda tutuluyor
        $plans = Plan::active()->get();
        
        // UsageToken için uyumluluk - User'ın kendisi token bilgilerini tutar
        $usageToken = $user->tokens_total > 0 ? $user : null;
        
        return view('dashboard.subscription.index', compact(
            'currentSubscription', 
            'user', 
            'plans',
            'usageToken'
        ));
    }


    
    /**
     * Plan seçim sayfasını göster
     */
    public function plans()
    {
        // Free planları hariç tut (price > 0 olan planlar)
        $plans = Plan::active()->where('price', '>', 0)->get();
        $user = Auth::user();
        $currentSubscription = $user ? $user->activeSubscription : null;
        
        return view('subscription.plans', compact('plans', 'user', 'currentSubscription'));
    }

    public function subscribe(Request $request)
    {
        $request->validate([
            'plan_id' => 'required|exists:plans,id'
        ]);

        $user = Auth::user();
        $plan = Plan::findOrFail($request->plan_id);

        // Aynı plan için bekleyen talep var mı kontrol et
        $existingRequest = $user->planRequests()
            ->where('plan_id', $plan->id)
            ->where('status', 'pending')
            ->first();

        if ($existingRequest) {
            return redirect()->route('subscription.plans')
                ->with('error', 'Bu plan için zaten bekleyen bir talebiniz bulunuyor.');
        }

        // Plan talebi oluştur
        PlanRequest::create([
            'user_id' => $user->id,
            'plan_id' => $plan->id,
            'status' => 'pending'
        ]);

        return redirect()->route('subscription.plans')
            ->with('success', 'Plan talebiniz başarıyla oluşturuldu! Yöneticiler tarafından incelendikten sonra size bilgi verilecektir.');
    }


    /**
     * Ücretsiz planı aktif et
     */
    private function activateFreePlan($user, $plan)
    {
        DB::beginTransaction();
        try {
            // Mevcut aktif subscription varsa iptal et
            if ($user->activeSubscription) {
                $user->activeSubscription->update(['status' => 'cancelled']);
            }

            // Yeni subscription oluştur
            $subscription = Subscription::create([
                'tenant_id' => $user->id,
                'plan_id' => $plan->id,
                'start_date' => now(),
                'end_date' => $plan->billing_cycle === 'yearly' ? now()->addYear() : now()->addMonth(),
                'status' => 'active',
                'trial_ends_at' => $plan->trial_days ? now()->addDays($plan->trial_days) : null
            ]);

            // Plan ve token'ları user'a ata
            $user->assignPlan($plan, $subscription->id);

            // Event fire et
            event(new UserSubscriptionActivated($user, $subscription));

            DB::commit();

            return redirect()->route('subscription.index')
                ->with('success', 'Plan başarıyla aktif edildi!');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Plan aktif edilirken hata oluştu: ' . $e->getMessage());
        }
    }


    /**
     * Token satın alma
     */
    public function buyTokens(Request $request)
    {
        return redirect()->route('dashboard.subscription.index')
            ->with('info', 'Token purchase is managed by administrators. Please contact support to get tokens added to your account.');
    }

    /**
     * Subscription iptal et
     */
    public function cancel(Request $request)
    {
        $user = Auth::user();
        $subscription = $user->activeSubscription;

        if (!$subscription) {
            return back()->with('error', 'Aktif aboneliğiniz bulunmuyor.');
        }

        $subscription->update([
            'status' => 'cancelled',
            'end_date' => now()
        ]);

        return redirect()->route('subscription.index')
            ->with('success', 'Aboneliğiniz iptal edildi.');
    }

    /**
     * Usage token bilgilerini getir (API)
     */
    public function getUsage(Request $request)
    {
        $user = Auth::user();

        return response()->json([
            'success' => true,
            'data' => [
                'tokens_remaining' => $user->tokens_remaining,
                'tokens_used' => $user->tokens_used,
                'tokens_total' => $user->tokens_total,
                'usage_percentage' => $user->token_usage_percentage,
                'days_until_reset' => $user->days_until_token_reset,
                'reset_date' => $user->token_reset_date ? $user->token_reset_date->format('Y-m-d') : null,
                'is_expired' => $user->isTokenExpired()
            ]
        ]);
    }

    /**
     * Billing history
     */
    public function billingHistory()
    {
        $user = Auth::user();
        
        return view('subscription.billing-history', [
            'payments' => collect([]) // Boş collection döndür
        ]);
    }

    /**
     * Expired subscription sayfasını göster
     */
    public function expired()
    {
        $user = Auth::user();
        $subscription = $user->subscriptions()->latest()->first();
        
        return view('subscription.expired', compact('subscription'));
    }
}
