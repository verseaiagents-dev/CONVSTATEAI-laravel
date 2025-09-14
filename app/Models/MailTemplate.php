<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MailTemplate extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'subject',
        'content',
        'variables',
        'is_active',
        'category',
        'description',
        'created_by',
        'updated_by'
    ];

    protected $casts = [
        'variables' => 'array',
        'is_active' => 'boolean',
    ];

    protected $attributes = [
        'is_active' => true,
        'variables' => '[]',
    ];

    /**
     * Template kategorileri
     */
    public const CATEGORIES = [
        'welcome' => 'Hoşgeldin',
        'notification' => 'Bildirim',
        'security' => 'Güvenlik',
        'subscription' => 'Abonelik',
        'marketing' => 'Pazarlama',
        'custom' => 'Özel'
    ];

    /**
     * Varsayılan değişkenler
     */
    public const DEFAULT_VARIABLES = [
        '{{username}}' => 'Kullanıcı adı',
        '{{useremail}}' => 'Kullanıcı e-posta adresi',
        '{{userplan}}' => 'Kullanıcı planı',
        '{{userplanexpired}}' => 'Plan sona erme tarihi',
        '{{usercreated}}' => 'Kullanıcı oluşturma tarihi',
        '{{userlastlogin}}' => 'Son giriş tarihi',
        '{{userstatus}}' => 'Kullanıcı durumu',
        '{{sitename}}' => 'Site adı',
        '{{siteurl}}' => 'Site URL',
        '{{adminname}}' => 'Admin adı',
        '{{currentdate}}' => 'Güncel tarih',
        '{{loginurl}}' => 'Giriş linki',
        '{{dashboardurl}}' => 'Dashboard linki',
        '{{supportemail}}' => 'Destek e-posta',
        '{{companyname}}' => 'Şirket adı',
        '{{companyaddress}}' => 'Şirket adresi',
        '{{companyphone}}' => 'Şirket telefonu'
    ];

    /**
     * Template'i oluşturan admin
     */
    public function creator()
    {
        return $this->belongsTo(AdminUser::class, 'created_by');
    }

    /**
     * Template'i güncelleyen admin
     */
    public function updater()
    {
        return $this->belongsTo(AdminUser::class, 'updated_by');
    }

    /**
     * Aktif template'leri getir
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Kategoriye göre filtrele
     */
    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    /**
     * Template içeriğindeki değişkenleri parse et
     */
    public function parseContent($data = [])
    {
        $content = $this->content;
        
        // Ensure content is a string
        if (!is_string($content)) {
            $content = (string) $content;
        }
        
        // Replace variables
        foreach ($data as $key => $value) {
            if ($value !== null) {
                $content = str_replace('{{' . $key . '}}', (string) $value, $content);
            }
        }
        
        return $content;
    }

    /**
     * Template konusundaki değişkenleri parse et
     */
    public function parseSubject($data = [])
    {
        $subject = $this->subject;
        
        // Ensure subject is a string
        if (!is_string($subject)) {
            $subject = (string) $subject;
        }
        
        // Replace variables
        foreach ($data as $key => $value) {
            if ($value !== null) {
                $subject = str_replace('{{' . $key . '}}', (string) $value, $subject);
            }
        }
        
        return $subject;
    }

    /**
     * Template'i test et
     */
    public function test($testData = [])
    {
        $defaultData = [
            'username' => 'Test Kullanıcı',
            'useremail' => 'test@example.com',
            'userplan' => 'Premium',
            'userplanexpired' => '2024-12-31',
            'usercreated' => '2024-01-01',
            'userlastlogin' => '2024-08-30',
            'userstatus' => 'Aktif',
            'sitename' => config('app.name'),
            'siteurl' => config('app.url'),
            'adminname' => 'Admin User',
            'currentdate' => date('Y-m-d'),
            'loginurl' => config('app.url') . '/login',
            'dashboardurl' => config('app.url') . '/dashboard',
            'supportemail' => 'support@convstateai.com',
            'companyname' => 'ConvState AI',
            'companyaddress' => 'İstanbul, Türkiye',
            'companyphone' => '+90 212 XXX XX XX'
        ];

        $data = array_merge($defaultData, $testData);
        
        return [
            'subject' => $this->parseSubject($data),
            'content' => $this->parseContent($data)
        ];
    }

    /**
     * Template kategorisini getir
     */
    public function getCategoryNameAttribute()
    {
        return self::CATEGORIES[$this->category] ?? 'Bilinmeyen';
    }

    /**
     * Template durumunu getir
     */
    public function getStatusTextAttribute()
    {
        return $this->is_active ? 'Aktif' : 'Pasif';
    }
}
