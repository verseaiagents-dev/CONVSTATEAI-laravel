<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Plan;
use App\Models\Subscription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * Display a listing of users
     */
    public function index(Request $request)
    {
        $query = User::with(['subscriptions.plan', 'usageToken']);

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

        return view('admin.users.index', compact('users', 'plans'));
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
}
