<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Plan;

class PlanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $plans = Plan::with('usageTokens')->orderBy('price', 'asc')->get();
        return view('admin.plans.index', compact('plans'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.plans.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'billing_cycle' => 'required|in:monthly,yearly,trial',
            'features' => 'required|array',
            'is_active' => 'boolean',
            'trial_days' => 'nullable|integer|min:1|max:365',
            'usage_tokens' => 'required|integer|min:-1',
            'token_reset_period' => 'required|in:monthly,yearly'
        ]);

        $plan = Plan::create($request->all());

        return redirect()->route('admin.plans.index')
            ->with('success', 'Plan başarıyla oluşturuldu.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Plan $plan)
    {
        return view('admin.plans.show', compact('plan'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Plan $plan)
    {
        return response()->json($plan);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Plan $plan)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'billing_cycle' => 'required|in:monthly,yearly,trial',
            'features' => 'required|array',
            'is_active' => 'boolean',
            'trial_days' => 'nullable|integer|min:1|max:365',
            'usage_tokens' => 'required|integer|min:-1',
            'token_reset_period' => 'required|in:monthly,yearly'
        ]);

        $plan->update($request->all());

        return redirect()->route('admin.plans.index')
            ->with('success', 'Plan başarıyla güncellendi.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Plan $plan)
    {
        // Check if plan has active subscriptions
        if ($plan->subscriptions()->where('status', 'active')->exists()) {
            return back()->with('error', 'Bu plana sahip aktif abonelikler bulunduğu için silinemez.');
        }

        $plan->delete();

        return redirect()->route('admin.plans.index')
            ->with('success', 'Plan başarıyla silindi.');
    }
}
