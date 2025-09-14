<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Subscription;
use App\Models\Plan;
use App\Models\User;

class SubscriptionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $subscriptions = Subscription::with(['user', 'plan'])
            ->orderBy('created_at', 'desc')
            ->get();
        return view('admin.subscriptions.index', compact('subscriptions'));
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

        $subscription = Subscription::create($request->all());

        // Usage token oluştur
        $plan = Plan::find($request->plan_id);
        if ($plan && $request->status === 'active') {
            $plan->createUsageTokenForUser($request->tenant_id, $subscription->id);
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

        $oldPlanId = $subscription->plan_id;
        $subscription->update($request->all());

        // Plan değiştiyse veya status aktif olduysa usage token güncelle
        if ($oldPlanId != $request->plan_id || $request->status === 'active') {
            $plan = Plan::find($request->plan_id);
            if ($plan && $request->status === 'active') {
                $plan->createUsageTokenForUser($request->tenant_id, $subscription->id);
            }
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
}
