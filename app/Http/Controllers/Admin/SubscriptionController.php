<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Subscription;
use App\Models\Plan;
use App\Models\User;
use App\Models\PlanRequest;
use Illuminate\Support\Facades\DB;

class SubscriptionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Sadece aktif abonelikleri göster
        $subscriptions = Subscription::with(['user', 'plan'])
            ->where('status', 'active')
            ->orderBy('created_at', 'desc')
            ->get();
        
        // Her kullanıcının sadece en son talebini göster
        $planRequests = PlanRequest::with(['user', 'plan', 'approvedBy'])
            ->whereIn('id', function($query) {
                $query->selectRaw('MAX(id)')
                    ->from('plan_requests')
                    ->groupBy('user_id');
            })
            ->orderBy('created_at', 'desc')
            ->get();
            
        return view('admin.subscriptions.index', compact('subscriptions', 'planRequests'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $users = User::all();
        $plans = Plan::where('is_active', true)->get();
        return view('admin.subscriptions.create', compact('users', 'plans'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'tenant_id' => 'required|exists:users,id',
            'plan_id' => 'required|exists:plans,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'status' => 'required|in:active,expired,cancelled',
            'trial_ends_at' => 'nullable|date'
        ]);

        DB::beginTransaction();
        try {
            // Cancel any existing active subscription for this user
            User::find($request->tenant_id)->subscriptions()
                ->where('status', 'active')
                ->update(['status' => 'cancelled']);

            // Create new subscription
            $subscription = Subscription::create($request->all());

            // Assign plan and tokens to user if subscription is active
            if ($request->status === 'active') {
                $plan = Plan::find($request->plan_id);
                $user = User::find($request->tenant_id);
                $user->assignPlan($plan, $subscription->id);
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Abonelik oluşturulurken hata oluştu: ' . $e->getMessage());
        }

        return redirect()->route('admin.subscriptions.index')
            ->with('success', 'Abonelik başarıyla oluşturuldu.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Subscription $subscription)
    {
        $subscription->load(['user', 'plan', 'invoices']);
        return view('admin.subscriptions.show', compact('subscription'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Subscription $subscription)
    {
        // Since we're using modal-based editing, redirect to index
        // The modal will handle the editing functionality
        return redirect()->route('admin.subscriptions.index');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Subscription $subscription)
    {
        $request->validate([
            'tenant_id' => 'required|exists:users,id',
            'plan_id' => 'required|exists:plans,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'status' => 'required|in:active,expired,cancelled',
            'trial_ends_at' => 'nullable|date'
        ]);

        DB::beginTransaction();
        try {
            $oldPlanId = $subscription->plan_id;
            $subscription->update($request->all());

            // Plan değiştiyse veya status aktif olduysa usage token güncelle
            if ($oldPlanId != $request->plan_id || $request->status === 'active') {
                $plan = Plan::find($request->plan_id);
                $user = User::find($request->tenant_id);
                
                if ($plan && $request->status === 'active') {
                    $user->assignPlan($plan, $subscription->id);
                }
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Abonelik güncellenirken hata oluştu: ' . $e->getMessage());
        }

        return redirect()->route('admin.subscriptions.index')
            ->with('success', 'Abonelik başarıyla güncellendi.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Subscription $subscription)
    {
        $subscription->delete();

        return redirect()->route('admin.subscriptions.index')
            ->with('success', 'Abonelik başarıyla silindi.');
    }

    /**
     * Plan talebini onayla
     */
    public function approveRequest(Request $request, PlanRequest $planRequest)
    {
        $request->validate([
            'admin_notes' => 'nullable|string|max:1000'
        ]);

        DB::beginTransaction();
        try {
            // Plan talebini onayla
            $planRequest->update([
                'status' => 'approved',
                'approved_at' => now(),
                'approved_by' => auth()->id(),
                'admin_notes' => $request->admin_notes
            ]);

            // Kullanıcının mevcut aktif aboneliğini iptal et
            $user = $planRequest->user;
            $activeSubscription = $user->subscriptions()->where('status', 'active')->first();
            if ($activeSubscription) {
                $activeSubscription->update(['status' => 'cancelled']);
            }

            // Yeni abonelik oluştur
            $subscription = Subscription::create([
                'tenant_id' => $user->id,
                'plan_id' => $planRequest->plan_id,
                'start_date' => now(),
                'end_date' => $planRequest->plan->billing_cycle === 'yearly' ? now()->addYear() : now()->addMonth(),
                'status' => 'active',
                'trial_ends_at' => $planRequest->plan->trial_days ? now()->addDays($planRequest->plan->trial_days) : null
            ]);

            // Plan ve token'ları user'a ata
            $user->assignPlan($planRequest->plan, $subscription->id);

            DB::commit();

            return redirect()->route('admin.subscriptions.index')
                ->with('success', 'Plan talebi onaylandı ve kullanıcıya atandı.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('admin.subscriptions.index')
                ->with('error', 'Plan talebi onaylanırken hata oluştu: ' . $e->getMessage());
        }
    }

    /**
     * Plan talebini reddet
     */
    public function rejectRequest(Request $request, PlanRequest $planRequest)
    {
        $request->validate([
            'admin_notes' => 'required|string|max:1000'
        ]);

        $planRequest->update([
            'status' => 'rejected',
            'approved_by' => auth()->id(),
            'admin_notes' => $request->admin_notes
        ]);

        return redirect()->route('admin.subscriptions.index')
            ->with('success', 'Plan talebi reddedildi.');
    }

    /**
     * Kullanıcının plan geçmişini getir
     */
    public function getUserPlanHistory(User $user)
    {
        $subscriptions = $user->subscriptions()
            ->with(['plan'])
            ->orderBy('start_date', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => [
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email
                ],
                'subscriptions' => $subscriptions->map(function ($subscription) {
                    return [
                        'id' => $subscription->id,
                        'plan_name' => $subscription->plan->name,
                        'plan_price' => $subscription->plan->formatted_price,
                        'start_date' => $subscription->start_date->format('d.m.Y H:i'),
                        'end_date' => $subscription->end_date ? $subscription->end_date->format('d.m.Y H:i') : 'Süresiz',
                        'status' => $subscription->status,
                        'status_text' => $this->getStatusText($subscription->status),
                        'created_at' => $subscription->created_at->format('d.m.Y H:i')
                    ];
                })
            ]
        ]);
    }

    /**
     * VIP Token yönetimi
     */
    public function manageVipToken(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'usage_token' => 'required|integer|min:0',
            'max_projects' => 'required|integer|min:0',
            'priority_support' => 'nullable|boolean',
            'advanced_analytics' => 'nullable|boolean',
            'custom_branding' => 'nullable|boolean',
            'api_access' => 'nullable|boolean',
            'admin_notes' => 'nullable|string|max:1000'
        ]);

        try {
            $user = User::findOrFail($request->user_id);
            
            // Kullanıcının mevcut VIP özelliklerini güncelle
            $user->update([
                'usage_token' => $request->usage_token,
                'max_projects' => $request->max_projects,
                'priority_support' => $request->has('priority_support'),
                'advanced_analytics' => $request->has('advanced_analytics'),
                'custom_branding' => $request->has('custom_branding'),
                'api_access' => $request->has('api_access'),
            ]);

            // Admin notlarını kaydet (eğer varsa)
            if ($request->admin_notes) {
                // Burada admin notlarını kaydetmek için bir log tablosu kullanabilirsiniz
                // Şimdilik basit bir şekilde user'ın bio alanına ekleyebiliriz
                $currentBio = $user->bio ?? '';
                $vipNote = "\n\n[VIP Token - " . now()->format('d.m.Y H:i') . "]: " . $request->admin_notes;
                $user->update(['bio' => $currentBio . $vipNote]);
            }

            return redirect()->route('admin.subscriptions.index')
                ->with('success', 'VIP token başarıyla güncellendi.');

        } catch (\Exception $e) {
            return redirect()->route('admin.subscriptions.index')
                ->with('error', 'VIP token güncellenirken hata oluştu: ' . $e->getMessage());
        }
    }

    /**
     * Durum metnini getir
     */
    private function getStatusText($status)
    {
        return match($status) {
            'active' => 'Aktif',
            'expired' => 'Süresi Dolmuş',
            'cancelled' => 'İptal Edilmiş',
            default => 'Bilinmiyor'
        };
    }
}
