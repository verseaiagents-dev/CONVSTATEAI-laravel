<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Plan;
use App\Models\Subscription;
// UsageToken modeli kaldırıldı - artık User tablosunda token bilgileri tutuluyor
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class UserPlanController extends Controller
{
    /**
     * Show the form for assigning a plan to a user
     */
    public function showAssignForm(User $user)
    {
        $plans = Plan::where('is_active', true)->get();
        $currentSubscription = $user->subscriptions()->where('status', 'active')->first();
        
        return view('admin.users.assign-plan', compact('user', 'plans', 'currentSubscription'));
    }

    /**
     * Assign a plan to a user
     */
    public function assignPlan(Request $request, User $user)
    {
        $request->validate([
            'plan_id' => 'required|exists:plans,id',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after:start_date',
            'notes' => 'nullable|string|max:500'
        ]);

        $plan = Plan::findOrFail($request->plan_id);
        
        DB::beginTransaction();
        try {
            // Cancel any existing active subscription
            $user->subscriptions()->where('status', 'active')->update([
                'status' => 'cancelled'
            ]);

            // Create new subscription
            $subscription = Subscription::create([
                'tenant_id' => $user->id,
                'plan_id' => $plan->id,
                'status' => 'active',
                'start_date' => $request->start_date ?: now(),
                'end_date' => $request->end_date ?: now()->addYear(),
                'trial_ends_at' => null
            ]);

            // Assign plan and tokens directly to user
            $user->assignPlan($plan, $subscription->id);

            DB::commit();

            Log::info('Plan assigned to user', [
                'user_id' => $user->id,
                'plan_id' => $plan->id,
                'assigned_by' => auth()->id(),
                'subscription_id' => $subscription->id
            ]);

            return redirect()->route('admin.users.index')
                ->with('success', "Plan '{$plan->name}' successfully assigned to {$user->name}");

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to assign plan to user', [
                'user_id' => $user->id,
                'plan_id' => $request->plan_id,
                'error' => $e->getMessage()
            ]);

            return back()->with('error', 'Failed to assign plan: ' . $e->getMessage());
        }
    }

    /**
     * Remove plan from user
     */
    public function removePlan(Request $request, User $user)
    {
        DB::beginTransaction();
        try {
            // Cancel active subscription
            $subscription = $user->subscriptions()->where('status', 'active')->first();
            
            if ($subscription) {
                $subscription->update([
                    'status' => 'cancelled',
                    'cancelled_at' => now(),
                    'metadata' => array_merge($subscription->metadata ?? [], [
                        'removed_by' => auth()->id(),
                        'removed_at' => now(),
                        'reason' => $request->reason ?? 'Manual removal'
                    ])
                ]);

                Log::info('Plan removed from user', [
                    'user_id' => $user->id,
                    'subscription_id' => $subscription->id,
                    'removed_by' => auth()->id()
                ]);
            }

            DB::commit();

            return redirect()->route('admin.users.index')
                ->with('success', "Plan removed from {$user->name}");

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to remove plan from user', [
                'user_id' => $user->id,
                'error' => $e->getMessage()
            ]);

            return back()->with('error', 'Failed to remove plan: ' . $e->getMessage());
        }
    }

    /**
     * Get user's current plan and usage
     */
    public function getUserPlanInfo(User $user)
    {
        $subscription = $user->subscriptions()->where('status', 'active')->first();
        
        return response()->json([
            'subscription' => $subscription ? [
                'id' => $subscription->id,
                'plan_name' => $subscription->plan->name,
                'status' => $subscription->status,
                'started_at' => $subscription->started_at,
                'expires_at' => $subscription->expires_at,
                'metadata' => $subscription->metadata
            ] : null,
            'usage_token' => [
                'tokens_total' => $user->tokens_total,
                'tokens_used' => $user->tokens_used,
                'tokens_remaining' => $user->tokens_remaining,
                'token_reset_date' => $user->token_reset_date,
                'usage_percentage' => $user->token_usage_percentage,
                'days_until_reset' => $user->days_until_token_reset
            ]
        ]);
    }
}
