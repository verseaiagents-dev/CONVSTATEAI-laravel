<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Plan;
use App\Models\Subscription;
use App\Models\PlanRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    /**
     * Display a listing of users
     */
    public function index(Request $request)
    {
        $query = User::with(['subscriptions.plan', 'activeSubscription.plan', 'campaigns']);

        // Search functionality
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Filter by plan
        if ($request->has('plan') && $request->plan) {
            $query->whereHas('subscriptions', function($q) use ($request) {
                $q->where('plan_id', $request->plan)
                  ->where('status', 'active');
            });
        }

        // Filter by subscription status
        if ($request->has('subscription_status') && $request->subscription_status) {
            if ($request->subscription_status === 'active') {
                $query->whereHas('subscriptions', function($q) {
                    $q->where('status', 'active');
                });
            } elseif ($request->subscription_status === 'inactive') {
                $query->whereDoesntHave('subscriptions', function($q) {
                    $q->where('status', 'active');
                });
            }
        }

        $users = $query->paginate(20);
        $plans = Plan::where('is_active', true)->get();

        // Get subscriptions for subscriptions tab
        $subscriptions = \App\Models\Subscription::with(['user', 'plan'])
            ->where('status', 'active')
            ->orderBy('created_at', 'desc')
            ->get();

        // Get plan requests for requests tab
        $planRequests = \App\Models\PlanRequest::with(['user', 'plan', 'approvedBy'])
            ->whereIn('id', function($query) {
                $query->selectRaw('MAX(id)')
                    ->from('plan_requests')
                    ->groupBy('user_id');
            })
            ->orderBy('created_at', 'desc')
            ->get();

        return view('admin.users', compact('users', 'plans', 'subscriptions', 'planRequests'));
    }

    /**
     * Display the specified user
     */
    public function show(User $user)
    {
        $user->load(['subscriptions.plan', 'usageToken', 'payments']);
        
        $currentSubscription = $user->subscriptions()->where('status', 'active')->first();
        $subscriptionHistory = $user->subscriptions()->with('plan')->orderBy('created_at', 'desc')->get();
        
        return view('admin.users.show', compact('user', 'currentSubscription', 'subscriptionHistory'));
    }

    /**
     * Show the form for editing the user
     */
    public function edit(User $user)
    {
        return view('admin.users.edit', compact('user'));
    }

    /**
     * Update the specified user
     */
    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:8',
            'is_admin' => 'boolean',
            'is_active' => 'boolean'
        ]);

        $updateData = [
            'name' => $request->name,
            'email' => $request->email,
            'is_admin' => $request->has('is_admin'),
            'is_active' => $request->has('is_active')
        ];

        // Only update password if provided
        if ($request->filled('password')) {
            $updateData['password'] = Hash::make($request->password);
        }

        $user->update($updateData);

        return redirect()->route('admin.users.index')
            ->with('success', 'User updated successfully');
    }

    /**
     * Remove the specified user
     */
    public function destroy(User $user)
    {
        // Prevent deleting admin users
        if ($user->is_admin) {
            return back()->with('error', 'Cannot delete admin users');
        }

        $user->delete();

        return redirect()->route('admin.users.index')
            ->with('success', 'User deleted successfully');
    }

    /**
     * Toggle user active status
     */
    public function toggleStatus(User $user)
    {
        $user->update(['is_active' => !$user->is_active]);
        
        $status = $user->is_active ? 'activated' : 'deactivated';
        return back()->with('success', "User {$status} successfully");
    }

    /**
     * Store a newly created subscription
     */
    public function storeSubscription(Request $request)
    {
        $request->validate([
            'tenant_id' => 'required|exists:users,id',
            'plan_id' => 'required|exists:plans,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'status' => 'required|in:active,expired,cancelled',
            'trial_ends_at' => 'nullable|date'
        ]);

        $subscription = Subscription::create($request->all());

        // Usage token oluştur
        $plan = Plan::find($request->plan_id);
        if ($plan && $request->status === 'active') {
            $plan->createUsageTokenForUser($request->tenant_id, $subscription->id);
        }

        return redirect()->route('admin.users.index')
            ->with('success', 'Abonelik başarıyla oluşturuldu.');
    }

    /**
     * Update the specified subscription
     */
    public function updateSubscription(Request $request, Subscription $subscription)
    {
        $request->validate([
            'tenant_id' => 'required|exists:users,id',
            'plan_id' => 'required|exists:plans,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'status' => 'required|in:active,expired,cancelled',
            'trial_ends_at' => 'nullable|date'
        ]);

        $oldPlanId = $subscription->plan_id;
        $subscription->update($request->all());

        // Plan değiştiyse veya status aktif olduysa usage token güncelle
        if ($oldPlanId != $request->plan_id || $request->status === 'active') {
            $plan = Plan::find($request->plan_id);
            if ($plan && $request->status === 'active') {
                $plan->createUsageTokenForUser($request->tenant_id, $subscription->id);
            }
        }

        return redirect()->route('admin.users.index')
            ->with('success', 'Abonelik başarıyla güncellendi.');
    }

    /**
     * Remove the specified subscription
     */
    public function destroySubscription(Subscription $subscription)
    {
        $subscription->delete();

        return redirect()->route('admin.users.index')
            ->with('success', 'Abonelik başarıyla silindi.');
    }

    /**
     * Approve plan request
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

            return redirect()->route('admin.users.index')
                ->with('success', 'Plan talebi onaylandı ve kullanıcıya atandı.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('admin.users.index')
                ->with('error', 'Plan talebi onaylanırken hata oluştu: ' . $e->getMessage());
        }
    }

    /**
     * Reject plan request
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

        return redirect()->route('admin.users.index')
            ->with('success', 'Plan talebi reddedildi.');
    }

    /**
     * Get user plan history
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
                        'start_date' => $subscription->start_date ? $subscription->start_date->format('d.m.Y H:i') : 'N/A',
                        'end_date' => $subscription->end_date ? $subscription->end_date->format('d.m.Y H:i') : 'Süresiz',
                        'status' => $subscription->status,
                        'status_text' => $this->getStatusText($subscription->status),
                        'created_at' => $subscription->created_at ? $subscription->created_at->format('d.m.Y H:i') : 'N/A'
                    ];
                })
            ]
        ]);
    }

    /**
     * Get user plan request history
     */
    public function getUserPlanRequestHistory(User $user)
    {
        $planRequests = $user->planRequests()
            ->with(['plan', 'approvedBy'])
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => [
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email
                ],
                'planRequests' => $planRequests->map(function ($request) {
                    return [
                        'id' => $request->id,
                        'plan_name' => $request->plan->name,
                        'plan_price' => $request->plan->formatted_price,
                        'status' => $request->status,
                        'created_at' => $request->created_at->format('d.m.Y H:i'),
                        'admin_notes' => $request->admin_notes,
                        'approved_by' => $request->approvedBy ? $request->approvedBy->name : null,
                        'approved_at' => $request->approved_at ? $request->approved_at->format('d.m.Y H:i') : null
                    ];
                })
            ]
        ]);
    }

    /**
     * Get status text
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
