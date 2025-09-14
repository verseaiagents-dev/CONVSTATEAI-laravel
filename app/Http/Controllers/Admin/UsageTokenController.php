<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\UsageToken;
use App\Models\User;
use App\Models\Plan;

class UsageTokenController extends Controller
{
    /**
     * Kullanıcının usage token'ını güncelle
     */
    public function updateUserTokens(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'tokens_remaining' => 'required|integer|min:0',
            'tokens_total' => 'required|integer|min:0'
        ]);

        $user = User::findOrFail($request->user_id);
        $usageToken = UsageToken::getActiveForUser($user->id);

        if (!$usageToken) {
            return response()->json([
                'success' => false,
                'message' => 'Kullanıcının aktif usage token\'ı bulunamadı'
            ], 404);
        }

        // Token'ları güncelle
        $usageToken->update([
            'tokens_remaining' => $request->tokens_remaining,
            'tokens_total' => $request->tokens_total,
            'tokens_used' => $request->tokens_total - $request->tokens_remaining
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Usage token başarıyla güncellendi',
            'data' => [
                'tokens_remaining' => $usageToken->tokens_remaining,
                'tokens_total' => $usageToken->tokens_total,
                'tokens_used' => $usageToken->tokens_used
            ]
        ]);
    }

    /**
     * Kullanıcıya token ekle
     */
    public function addTokens(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'amount' => 'required|integer|min:1'
        ]);

        $user = User::findOrFail($request->user_id);
        $usageToken = UsageToken::getActiveForUser($user->id);

        if (!$usageToken) {
            return response()->json([
                'success' => false,
                'message' => 'Kullanıcının aktif usage token\'ı bulunamadı'
            ], 404);
        }

        // Token ekle
        $usageToken->addTokens($request->amount);

        return response()->json([
            'success' => true,
            'message' => 'Token başarıyla eklendi',
            'data' => [
                'tokens_remaining' => $usageToken->tokens_remaining,
                'tokens_total' => $usageToken->tokens_total,
                'tokens_used' => $usageToken->tokens_used
            ]
        ]);
    }

    /**
     * Kullanıcının usage token bilgilerini getir
     */
    public function getUserTokens(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id'
        ]);

        $user = User::findOrFail($request->user_id);
        $usageToken = UsageToken::getActiveForUser($user->id);

        if (!$usageToken) {
            return response()->json([
                'success' => false,
                'message' => 'Kullanıcının aktif usage token\'ı bulunamadı'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'tokens_remaining' => $usageToken->tokens_remaining,
                'tokens_total' => $usageToken->tokens_total,
                'tokens_used' => $usageToken->tokens_used,
                'usage_percentage' => $usageToken->usage_percentage,
                'reset_date' => $usageToken->reset_date ? $usageToken->reset_date->format('Y-m-d') : null,
                'is_expired' => $usageToken->isExpired()
            ]
        ]);
    }

    /**
     * Plan için kullanıcıların token bilgilerini getir
     */
    public function getPlanTokens(Request $request)
    {
        $request->validate([
            'plan_id' => 'required|exists:plans,id'
        ]);

        $plan = Plan::with('usageTokens.user')->findOrFail($request->plan_id);
        
        $tokenData = $plan->usageTokens->map(function ($token) {
            return [
                'user_id' => $token->user_id,
                'user_name' => $token->user->name,
                'user_email' => $token->user->email,
                'tokens_remaining' => $token->tokens_remaining,
                'tokens_total' => $token->tokens_total,
                'tokens_used' => $token->tokens_used,
                'usage_percentage' => $token->usage_percentage,
                'is_active' => $token->is_active,
                'reset_date' => $token->reset_date ? $token->reset_date->format('Y-m-d') : null
            ];
        });

        return response()->json([
            'success' => true,
            'data' => [
                'plan_name' => $plan->name,
                'plan_usage_tokens' => $plan->usage_tokens,
                'total_remaining' => $plan->usageTokens->sum('tokens_remaining'),
                'users' => $tokenData
            ]
        ]);
    }
}