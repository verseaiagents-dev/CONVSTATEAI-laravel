<?php

namespace App\Services;

use Illuminate\Support\Facades\Crypt;
use Illuminate\Contracts\Encryption\DecryptException;

class EncryptionService
{
    /**
     * Encrypt sensitive data
     */
    public static function encrypt($data): string
    {
        if (is_array($data)) {
            $data = json_encode($data);
        }
        
        return Crypt::encryptString($data);
    }

    /**
     * Decrypt sensitive data
     */
    public static function decrypt(string $encryptedData)
    {
        try {
            $decrypted = Crypt::decryptString($encryptedData);
            
            // Try to decode JSON if it was an array
            $decoded = json_decode($decrypted, true);
            return json_last_error() === JSON_ERROR_NONE ? $decoded : $decrypted;
        } catch (DecryptException $e) {
            \Log::error('Failed to decrypt data', [
                'error' => $e->getMessage(),
                'data_length' => strlen($encryptedData)
            ]);
            
            return null;
        }
    }

    /**
     * Encrypt session data
     */
    public static function encryptSessionData(array $data): array
    {
        $sensitiveFields = [
            'user_preferences',
            'product_interactions',
            'intent_history',
            'chat_history'
        ];

        foreach ($sensitiveFields as $field) {
            if (isset($data[$field]) && !empty($data[$field])) {
                $data[$field] = self::encrypt($data[$field]);
            }
        }

        return $data;
    }

    /**
     * Decrypt session data
     */
    public static function decryptSessionData(array $data): array
    {
        $sensitiveFields = [
            'user_preferences',
            'product_interactions',
            'intent_history',
            'chat_history'
        ];

        foreach ($sensitiveFields as $field) {
            if (isset($data[$field]) && !empty($data[$field])) {
                $data[$field] = self::decrypt($data[$field]) ?? [];
            }
        }

        return $data;
    }

    /**
     * Hash sensitive identifiers
     */
    public static function hashIdentifier(string $identifier): string
    {
        return hash('sha256', $identifier . config('app.key'));
    }

    /**
     * Verify hashed identifier
     */
    public static function verifyIdentifier(string $identifier, string $hash): bool
    {
        return hash_equals(self::hashIdentifier($identifier), $hash);
    }
}
