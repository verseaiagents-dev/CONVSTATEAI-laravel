<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class WidgetCustomization extends Model
{
    protected $fillable = [
        'user_id',
        'ai_name',
        'welcome_message',
        'welcome_message_custom',
        'cargo_not_found_message',
        'feature_disabled_message',
        'error_message_template',
        'order_not_found_message',
        'theme_color',
        'logo_url',
        'font_family',
        'primary_color',
        'secondary_color',
        'language',
        'custom_messages',
        'rate_limit_per_minute',
        'api_timeout_seconds',
        'max_retry_attempts',
        'notification_message',
        'enable_typing_indicator',
        'enable_sound_notifications',
        'customization_data',
        'is_active'
    ];

    protected $casts = [
        'customization_data' => 'array',
        'custom_messages' => 'array',
        'is_active' => 'boolean',
        'enable_typing_indicator' => 'boolean',
        'enable_sound_notifications' => 'boolean'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // WidgetActions ile ilişki (artık birden fazla endpoint olabilir)
    public function widgetActions(): HasMany
    {
        return $this->hasMany(WidgetActions::class);
    }
    
    // Belirli bir tip için endpoint al
    public function getEndpointByType(string $type): ?string
    {
        $action = $this->widgetActions()->where('type', $type)->first();
        return $action ? $action->endpoint : null;
    }
    
    // Sipariş durumu endpoint'i
    public function getSiparisDurumuEndpoint(): ?string
    {
        return $this->getEndpointByType('siparis_durumu_endpoint');
    }
    
    // Kargo durumu endpoint'i
    public function getKargoDurumuEndpoint(): ?string
    {
        return $this->getEndpointByType('kargo_durumu_endpoint');
    }
    
    // Endpoint'in aktif olup olmadığını kontrol et
    public function isEndpointActive(string $type): bool
    {
        $action = $this->widgetActions()->where('type', $type)->first();
        return $action ? $action->is_active : false;
    }
    
    // Sipariş durumu API'si aktif mi?
    public function isSiparisApiActive(): bool
    {
        return $this->isEndpointActive('siparis_durumu_endpoint');
    }
    
    // Kargo durumu API'si aktif mi?
    public function isKargoApiActive(): bool
    {
        return $this->isEndpointActive('kargo_durumu_endpoint');
    }
    
    // Özelleştirilmiş mesajları al
    public function getCustomMessage(string $key, string $default = null): ?string
    {
        $customMessages = $this->custom_messages ?? [];
        return $customMessages[$key] ?? $default;
    }
    
    // AI kişiliğine göre mesaj oluştur
    public function getPersonalizedMessage(string $messageType): string
    {
        $personality = $this->ai_personality ?? 'friendly';
        
        $messages = [
            'friendly' => [
                'cargo_not_found' => $this->cargo_not_found_message ?? 'Üzgünüm, kargo numaranızla eşleşen bir kargo bulamadım. Lütfen numarayı kontrol edip tekrar deneyin.',
                'feature_disabled' => $this->feature_disabled_message ?? 'Bu özellik şu anda aktif değil, ancak yakında hizmetinizde olacak! 😊',
                'error' => $this->error_message_template ?? 'Bir sorun oluştu, lütfen daha sonra tekrar deneyin.',
                'order_not_found' => $this->order_not_found_message ?? 'Sipariş numaranızla eşleşen bir sipariş bulamadım. Lütfen numarayı kontrol edin.'
            ],
            'professional' => [
                'cargo_not_found' => $this->cargo_not_found_message ?? 'Belirtilen kargo numarası ile kayıt bulunamadı. Lütfen numarayı doğrulayarak tekrar deneyiniz.',
                'feature_disabled' => $this->feature_disabled_message ?? 'Bu özellik şu anda kullanılamamaktadır. Yakında hizmetinizde olacaktır.',
                'error' => $this->error_message_template ?? 'Sistem hatası oluştu. Lütfen daha sonra tekrar deneyiniz.',
                'order_not_found' => $this->order_not_found_message ?? 'Belirtilen sipariş numarası ile kayıt bulunamadı. Lütfen numarayı doğrulayınız.'
            ],
            'casual' => [
                'cargo_not_found' => $this->cargo_not_found_message ?? 'Kargo bulunamadı! Numara doğru mu kontrol et bakalım.',
                'feature_disabled' => $this->feature_disabled_message ?? 'Bu özellik henüz hazır değil, biraz bekleyin!',
                'error' => $this->error_message_template ?? 'Bir şeyler ters gitti, tekrar dener misin?',
                'order_not_found' => $this->order_not_found_message ?? 'Sipariş bulunamadı, numarayı kontrol et.'
            ]
        ];
        
        return $messages[$personality][$messageType] ?? $messages['friendly'][$messageType];
    }
    
    // Tema ayarlarını al
    public function getThemeSettings(): array
    {
        return [
            'primary_color' => $this->primary_color ?? '#3B82F6',
            'secondary_color' => $this->secondary_color ?? '#6B7280',
            'theme_color' => $this->theme_color ?? '#3B82F6',
            'font_family' => $this->font_family ?? 'Inter',
            'logo_url' => $this->logo_url
        ];
    }
    
    // Gelişmiş ayarları al
    public function getAdvancedSettings(): array
    {
        return [
            'rate_limit_per_minute' => $this->rate_limit_per_minute ?? 10,
            'api_timeout_seconds' => $this->api_timeout_seconds ?? 10,
            'max_retry_attempts' => $this->max_retry_attempts ?? 2,
            'enable_typing_indicator' => $this->enable_typing_indicator ?? true,
            'enable_sound_notifications' => $this->enable_sound_notifications ?? false
        ];
    }
}
