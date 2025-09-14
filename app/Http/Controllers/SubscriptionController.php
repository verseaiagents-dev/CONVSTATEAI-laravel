<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Plan;
use App\Models\Subscription;
use App\Models\UsageToken;
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
        $usageToken = UsageToken::getActiveForUser($user->id);
        $plans = Plan::active()->get();
        
        return view('dashboard.subscription.index', compact(
            'currentSubscription', 
            'user', 
            'usageToken', 
            'plans'
        ));
    }

    /**
     * Plan seçim sayfasını göster
     */
    public function plans()
    {
        $plans = Plan::active()->get();
        $user = Auth::user();
        $currentSubscription = $user ? $user->activeSubscription : null;
        
        return view('subscription.plans', compact('plans', 'user', 'currentSubscription'));
    }

    /**
     * Plan satın alma
     */
    public function subscribe(Request $request)
    {
        $user = Auth::user();
        
        // Kullanıcının aktif aboneliği var mı kontrol et
        $activeSubscription = $user->subscriptions()->where('status', 'active')->first();
        
        if ($activeSubscription) {
            return redirect()->route('dashboard.subscription.index')
                ->with('info', 'You already have an active subscription. Please contact admin to change your plan.');
        }

        return redirect()->route('dashboard.subscription.index')
            ->with('info', 'Plan assignment is managed by administrators. Please contact support to get a plan assigned.');
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

            // Usage token oluştur
            $plan->createUsageTokenForUser($user->id, $subscription->id);

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
        $usageToken = UsageToken::getActiveForUser($user->id);

        if (!$usageToken) {
            return response()->json([
                'success' => false,
                'message' => 'Usage token bulunamadı'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'tokens_remaining' => $usageToken->tokens_remaining,
                'tokens_used' => $usageToken->tokens_used,
                'tokens_total' => $usageToken->tokens_total,
                'usage_percentage' => $usageToken->usage_percentage,
                'days_until_reset' => $usageToken->days_until_reset,
                'reset_date' => $usageToken->reset_date ? $usageToken->reset_date->format('Y-m-d') : null,
                'is_expired' => $usageToken->isExpired()
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
