<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PersonalTokenController extends Controller
{
    /**
     * Generate new personal token for current user
     */
    public function generateToken()
    {
        $user = Auth::user();
        
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Kullanıcı girişi gerekli'
            ], 401);
        }
        
        $token = $user->generatePersonalToken();
        
        return response()->json([
            'success' => true,
            'message' => 'Personal token başarıyla oluşturuldu',
            'data' => [
                'token' => $token,
                'user_id' => $user->id,
                'expires_at' => $user->token_expires_at
            ]
        ]);
    }

    /**
     * Get current user's personal token info
     */
    public function getTokenInfo()
    {
        $user = Auth::user();
        
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Kullanıcı girişi gerekli'
            ], 401);
        }
        
        return response()->json([
            'success' => true,
            'data' => [
                'has_token' => $user->hasValidPersonalToken(),
                'expires_at' => $user->token_expires_at,
                'user_id' => $user->id
            ]
        ]);
    }

    /**
     * Revoke current user's personal token
     */
    public function revokeToken()
    {
        $user = Auth::user();
        
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Kullanıcı girişi gerekli'
            ], 401);
        }
        
        $user->revokePersonalToken();
        
        return response()->json([
            'success' => true,
            'message' => 'Personal token başarıyla iptal edildi'
        ]);
    }
}
