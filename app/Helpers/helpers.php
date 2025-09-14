<?php

/**
 * Global Helper Functions
 * Bu dosya composer autoload ile otomatik yüklenir
 */

if (!function_exists('ai_process')) {
    /**
     * AI mesaj işleme helper'ı
     */
    function ai_process(string $message): string
    {
        return \App\Helpers\AIHelper::processMessage($message);
    }
}

if (!function_exists('ai_format')) {
    /**
     * AI yanıt formatı helper'ı
     */
    function ai_format(string $response, string $type = 'text'): array
    {
        return \App\Helpers\AIHelper::formatResponse($response, $type);
    }
}

if (!function_exists('ai_analyze')) {
    /**
     * AI yanıt analizi helper'ı
     */
    function ai_analyze(string $response): array
    {
        return \App\Helpers\AIHelper::analyzeResponse($response);
    }
}

if (!function_exists('ai_log')) {
    /**
     * AI yanıt log helper'ı
     */
    function ai_log(string $input, string $output, array $metadata = []): void
    {
        \App\Helpers\AIHelper::logResponse($input, $output, $metadata);
    }
}

if (!function_exists('truncate')) {
    /**
     * Metin kısaltma helper'ı
     */
    function truncate(string $text, int $length = 100, string $suffix = '...'): string
    {
        return \App\Helpers\GeneralHelper::truncate($text, $length, $suffix);
    }
}

if (!function_exists('create_slug')) {
    /**
     * Slug oluşturma helper'ı
     */
    function create_slug(string $text): string
    {
        return \App\Helpers\GeneralHelper::createSlug($text);
    }
}

if (!function_exists('random_string')) {
    /**
     * Rastgele string oluşturma helper'ı
     */
    function random_string(int $length = 10): string
    {
        return \App\Helpers\GeneralHelper::generateRandomString($length);
    }
}

if (!function_exists('format_money')) {
    /**
     * Para formatı helper'ı
     */
    function format_money(float $amount, string $currency = '₺'): string
    {
        return \App\Helpers\GeneralHelper::formatMoney($amount, $currency);
    }
}

if (!function_exists('format_date')) {
    /**
     * Tarih formatı helper'ı
     */
    function format_date(string $date, string $format = 'd.m.Y H:i'): string
    {
        return \App\Helpers\GeneralHelper::formatDate($date, $format);
    }
}

if (!function_exists('format_file_size')) {
    /**
     * Dosya boyutu formatı helper'ı
     */
    function format_file_size(int $bytes): string
    {
        return \App\Helpers\GeneralHelper::formatFileSize($bytes);
    }
}

if (!function_exists('validate_email')) {
    /**
     * Email doğrulama helper'ı
     */
    function validate_email(string $email): bool
    {
        return \App\Helpers\GeneralHelper::validateEmail($email);
    }
}

if (!function_exists('format_phone')) {
    /**
     * Telefon formatı helper'ı
     */
    function format_phone(string $phone): string
    {
        return \App\Helpers\GeneralHelper::formatPhone($phone);
    }
}

if (!function_exists('client_ip')) {
    /**
     * Client IP helper'ı
     */
    function client_ip(): string
    {
        return \App\Helpers\GeneralHelper::getClientIP();
    }
}
