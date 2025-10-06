<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Plan;

class UsageTokenController extends Controller
{
    /**
     * Kullanıcının usage token'ını güncelle (Yeni User tablosu sistemi)
     */
    public function updateUserTokens(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'tokens_remaining' => 'required|integer|min:0',
            'tokens_total' => 'required|integer|min:0'
        ]);

        $user = User::findOrFail($request->user_id);

        // Yeni User tablosundaki token sistemi
        $user->update([
            'tokens_remaining' => $request->tokens_remaining,
            'tokens_total' => $request->tokens_total,
            'tokens_used' => $request->tokens_total - $request->tokens_remaining
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Usage token başarıyla güncellendi',
            'data' => [
                'tokens_remaining' => $user->tokens_remaining,
                'tokens_total' => $user->tokens_total,
                'tokens_used' => $user->tokens_used,
                'usage_percentage' => $user->token_usage_percentage
            ]
        ]);
    }

    /**
     * Kullanıcıya token ekle (Yeni User tablosu sistemi)
     */
    public function addTokens(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'amount' => 'required|integer|min:1'
        ]);

        $user = User::findOrFail($request->user_id);

        // Yeni User tablosundaki token sistemi
        $user->addTokens($request->amount);

        return response()->json([
            'success' => true,
            'message' => 'Token başarıyla eklendi',
            'data' => [
                'tokens_remaining' => $user->tokens_remaining,
                'tokens_total' => $user->tokens_total,
                'tokens_used' => $user->tokens_used,
                'usage_percentage' => $user->token_usage_percentage
            ]
        ]);
    }

    /**
     * Kullanıcının usage token bilgilerini getir (Yeni User tablosu sistemi)
     */
    public function getUserTokens(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id'
        ]);

        $user = User::findOrFail($request->user_id);

        return response()->json([
            'success' => true,
            'data' => [
                'tokens_remaining' => $user->tokens_remaining,
                'tokens_total' => $user->tokens_total,
                'tokens_used' => $user->tokens_used,
                'usage_percentage' => $user->token_usage_percentage,
                'reset_date' => $user->token_reset_date ? $user->token_reset_date->format('Y-m-d') : null,
                'is_expired' => $user->isTokenExpired(),
                'can_use_token' => $user->canUseToken(),
                'current_plan_id' => $user->current_plan_id
            ]
        ]);
    }

    /**
     * Plan için kullanıcıların token bilgilerini getir (Yeni User tablosu sistemi)
     */
    public function getPlanTokens(Request $request)
    {
        $request->validate([
            'plan_id' => 'required|exists:plans,id'
        ]);

        $plan = Plan::findOrFail($request->plan_id);
        
        // Bu plana sahip kullanıcıları bul
        $users = User::where('current_plan_id', $plan->id)->get();
        
        $tokenData = $users->map(function ($user) {
            return [
                'user_id' => $user->id,
                'user_name' => $user->name,
                'user_email' => $user->email,
                'tokens_remaining' => $user->tokens_remaining,
                'tokens_total' => $user->tokens_total,
                'tokens_used' => $user->tokens_used,
                'usage_percentage' => $user->token_usage_percentage,
                'can_use_token' => $user->canUseToken(),
                'is_expired' => $user->isTokenExpired(),
                'reset_date' => $user->token_reset_date ? $user->token_reset_date->format('Y-m-d') : null
            ];
        });

        return response()->json([
            'success' => true,
            'data' => [
                'plan_name' => $plan->name,
                'plan_usage_tokens' => $plan->calculateUsageTokens(),
                'total_remaining' => $users->sum('tokens_remaining'),
                'total_used' => $users->sum('tokens_used'),
                'users' => $tokenData
            ]
        ]);
    }
}