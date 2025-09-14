<?php

namespace App\Helpers;

class GeneralHelper
{
    /**
     * Metni kısalt
     */
    public static function truncate(string $text, int $length = 100, string $suffix = '...'): string
    {
        if (strlen($text) <= $length) {
            return $text;
        }

        return substr($text, 0, $length) . $suffix;
    }

    /**
     * Slug oluştur
     */
    public static function createSlug(string $text): string
    {
        $text = mb_strtolower($text, 'UTF-8');
        $text = preg_replace('/[^a-z0-9\s-]/', '', $text);
        $text = preg_replace('/[\s-]+/', '-', $text);
        return trim($text, '-');
    }

    /**
     * Rastgele string oluştur
     */
    public static function generateRandomString(int $length = 10): string
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $randomString = '';
        
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, strlen($characters) - 1)];
        }
        
        return $randomString;
    }

    /**
     * Para formatı
     */
    public static function formatMoney(float $amount, string $currency = '₺'): string
    {
        return $currency . number_format($amount, 2, ',', '.');
    }

    /**
     * Tarih formatı
     */
    public static function formatDate(string $date, string $format = 'd.m.Y H:i'): string
    {
        return date($format, strtotime($date));
    }

    /**
     * Dosya boyutu formatı
     */
    public static function formatFileSize(int $bytes): string
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        
        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, 2) . ' ' . $units[$i];
    }

    /**
     * Array'i JSON'a çevir
     */
    public static function arrayToJson(array $data): string
    {
        return json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    }

    /**
     * JSON'ı array'e çevir
     */
    public static function jsonToArray(string $json): array
    {
        return json_decode($json, true) ?? [];
    }

    /**
     * Email doğrula
     */
    public static function validateEmail(string $email): bool
    {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }

    /**
     * Telefon numarası formatı
     */
    public static function formatPhone(string $phone): string
    {
        $phone = preg_replace('/[^0-9]/', '', $phone);
        
        if (strlen($phone) === 10) {
            return substr($phone, 0, 3) . ' ' . substr($phone, 3, 3) . ' ' . substr($phone, 6, 4);
        }
        
        if (strlen($phone) === 11) {
            return substr($phone, 0, 1) . ' ' . substr($phone, 1, 3) . ' ' . substr($phone, 4, 3) . ' ' . substr($phone, 7, 4);
        }
        
        return $phone;
    }

    /**
     * Güvenli hash oluştur
     */
    public static function createHash(string $data, string $algorithm = 'sha256'): string
    {
        return hash($algorithm, $data);
    }

    /**
     * IP adresi al
     */
    public static function getClientIP(): string
    {
        $ipKeys = ['HTTP_X_FORWARDED_FOR', 'HTTP_X_FORWARDED', 'HTTP_X_CLUSTER_CLIENT_IP', 'HTTP_FORWARDED_FOR', 'HTTP_FORWARDED', 'HTTP_CLIENT_IP', 'REMOTE_ADDR'];
        
        foreach ($ipKeys as $key) {
            if (array_key_exists($key, $_SERVER) === true) {
                foreach (explode(',', $_SERVER[$key]) as $ip) {
                    $ip = trim($ip);
                    if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) !== false) {
                        return $ip;
                    }
                }
            }
        }
        
        return $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
    }
}
