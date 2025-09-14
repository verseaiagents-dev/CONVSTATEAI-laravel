<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Crypt;

class ApiSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'provider',
        'api_key',
        'base_url',
        'config',
        'is_active',
        'is_default',
        'description',
    ];

    protected $casts = [
        'config' => 'array',
        'is_active' => 'boolean',
        'is_default' => 'boolean',
    ];

    protected $hidden = [
        'api_key',
    ];

    /**
     * API key'i şifreleyerek kaydet
     */
    public function setApiKeyAttribute($value)
    {
        $this->attributes['api_key'] = Crypt::encryptString($value);
    }

    /**
     * API key'i şifresini çözerek getir
     */
    public function getApiKeyAttribute($value)
    {
        return $value ? Crypt::decryptString($value) : null;
    }

    /**
     * Aktif API'leri getir
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Varsayılan API'yi getir
     */
    public function scopeDefault($query)
    {
        return $query->where('is_default', true);
    }

    /**
     * Provider'a göre API'leri getir
     */
    public function scopeByProvider($query, $provider)
    {
        return $query->where('provider', $provider);
    }

    /**
     * Varsayılan API'yi ayarla (diğerlerini false yap)
     */
    public function setAsDefault()
    {
        // Önce tüm API'leri varsayılan olmaktan çıkar
        static::where('is_default', true)->update(['is_default' => false]);
        
        // Bu API'yi varsayılan yap
        $this->update(['is_default' => true]);
    }
}
