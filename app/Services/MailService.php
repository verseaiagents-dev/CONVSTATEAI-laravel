<?php

namespace App\Services;

use App\Mail\WelcomeEmail;
use App\Mail\PasswordResetEmail;
use App\Mail\SubscriptionWelcomeEmail;
use App\Mail\AccountVerificationEmail;
use App\Mail\NotificationEmail;
use App\Models\User;
use App\Models\Subscription;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class MailService
{
    /**
     * Kullanıcıya hoşgeldin maili gönder
     */
    public function sendWelcomeEmail(User $user): bool
    {
        try {
            Mail::to($user->email)->send(new WelcomeEmail($user));
            return true;
        } catch (\Exception $e) {
            Log::error('Failed to send welcome email to: ' . $user->email . ' Error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Şifre sıfırlama maili gönder
     */
    public function sendPasswordResetEmail(string $email, string $resetUrl, string $userName): bool
    {
        try {
            Mail::to($email)->send(new PasswordResetEmail($resetUrl, $userName));
            return true;
        } catch (\Exception $e) {
            Log::error('Failed to send password reset email to: ' . $email . ' Error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Abonelik hoşgeldin maili gönder
     */
    public function sendSubscriptionWelcomeEmail(User $user, Subscription $subscription): bool
    {
        try {
            Mail::to($user->email)->send(new SubscriptionWelcomeEmail($user, $subscription));
            return true;
        } catch (\Exception $e) {
            Log::error('Failed to send subscription welcome email to: ' . $user->email . ' Error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Hesap doğrulama maili gönder
     */
    public function sendAccountVerificationEmail(User $user, string $verificationUrl): bool
    {
        try {
            Mail::to($user->email)->send(new AccountVerificationEmail($user, $verificationUrl));
            return true;
        } catch (\Exception $e) {
            Log::error('Failed to send account verification email to: ' . $user->email . ' Error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Toplu mail gönderimi
     */
    public function sendBulkEmail(array $emails, string $subject, string $view, array $data = []): array
    {
        $results = [
            'success' => 0,
            'failed' => 0,
            'errors' => []
        ];

        foreach ($emails as $email) {
            try {
                Mail::send($view, $data, function ($message) use ($email, $subject) {
                    $message->to($email)->subject($subject);
                });
                $results['success']++;
            } catch (\Exception $e) {
                $results['failed']++;
                $results['errors'][] = [
                    'email' => $email,
                    'error' => $e->getMessage()
                ];
                Log::error('Bulk email failed for: ' . $email . ' Error: ' . $e->getMessage());
            }
        }

        return $results;
    }

    /**
     * Genel bildirim maili gönder
     */
    public function sendNotificationEmail(string $email, string $title, string $message, string $userName, ?string $actionUrl = null, ?string $actionText = null): bool
    {
        try {
            Mail::to($email)->send(new NotificationEmail($title, $message, $userName, $actionUrl, $actionText));
            return true;
        } catch (\Exception $e) {
            Log::error('Failed to send notification email to: ' . $email . ' Error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Demo request bildirimi gönder
     */
    public function sendDemoRequestNotification(\App\Models\DemoRequest $demoRequest): bool
    {
        try {
            $adminEmail = config('mail.admin_email', 'admin@convstateai.com');
            
            $subject = 'Yeni Demo Talebi - ConvState AI';
            $message = $this->buildDemoRequestEmailContent($demoRequest);
            
            Mail::to($adminEmail)->send(new \App\Mail\NotificationEmail(
                $subject,
                $message,
                'Admin',
                route('admin.demo-requests.show', $demoRequest),
                'Demo Talebini Görüntüle'
            ));
            
            return true;
        } catch (\Exception $e) {
            Log::error('Failed to send demo request notification. Error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Demo request email içeriği oluştur
     */
    private function buildDemoRequestEmailContent(\App\Models\DemoRequest $demoRequest): string
    {
        $statusLabels = [
            'pending' => 'Bekleyen',
            'contacted' => 'İletişim Kuruldu',
            'completed' => 'Tamamlandı',
            'cancelled' => 'İptal Edildi'
        ];

        return "
        <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; padding: 20px;'>
            <div style='background: linear-gradient(135deg, #8B5CF6, #A855F7); padding: 20px; border-radius: 10px; margin-bottom: 20px;'>
                <h1 style='color: white; margin: 0; text-align: center;'>Yeni Demo Talebi</h1>
            </div>
            
            <div style='background: #f8f9fa; padding: 20px; border-radius: 8px; margin-bottom: 20px;'>
                <h2 style='color: #333; margin-top: 0;'>Talep Detayları</h2>
                
                <table style='width: 100%; border-collapse: collapse;'>
                    <tr>
                        <td style='padding: 8px; border-bottom: 1px solid #ddd; font-weight: bold;'>Ad Soyad:</td>
                        <td style='padding: 8px; border-bottom: 1px solid #ddd;'>{$demoRequest->full_name}</td>
                    </tr>
                    <tr>
                        <td style='padding: 8px; border-bottom: 1px solid #ddd; font-weight: bold;'>E-posta:</td>
                        <td style='padding: 8px; border-bottom: 1px solid #ddd;'>{$demoRequest->email}</td>
                    </tr>
                    <tr>
                        <td style='padding: 8px; border-bottom: 1px solid #ddd; font-weight: bold;'>Telefon:</td>
                        <td style='padding: 8px; border-bottom: 1px solid #ddd;'>" . ($demoRequest->phone ?: 'Belirtilmemiş') . "</td>
                    </tr>
                    <tr>
                        <td style='padding: 8px; border-bottom: 1px solid #ddd; font-weight: bold;'>Site Ziyaretçi Sayısı:</td>
                        <td style='padding: 8px; border-bottom: 1px solid #ddd;'>" . ($demoRequest->site_visitor_count ? number_format($demoRequest->site_visitor_count) : 'Belirtilmemiş') . "</td>
                    </tr>
                    <tr>
                        <td style='padding: 8px; border-bottom: 1px solid #ddd; font-weight: bold;'>Durum:</td>
                        <td style='padding: 8px; border-bottom: 1px solid #ddd;'>{$statusLabels[$demoRequest->status]}</td>
                    </tr>
                    <tr>
                        <td style='padding: 8px; border-bottom: 1px solid #ddd; font-weight: bold;'>Talep Tarihi:</td>
                        <td style='padding: 8px; border-bottom: 1px solid #ddd;'>{$demoRequest->created_at->format('d.m.Y H:i')}</td>
                    </tr>
                </table>
            </div>
            
            <div style='text-align: center; margin-top: 20px;'>
                <a href='" . route('admin.demo-requests.show', $demoRequest) . "' 
                   style='background: linear-gradient(135deg, #8B5CF6, #A855F7); color: white; padding: 12px 24px; text-decoration: none; border-radius: 6px; display: inline-block; font-weight: bold;'>
                    Demo Talebini Görüntüle
                </a>
            </div>
            
            <div style='margin-top: 30px; padding-top: 20px; border-top: 1px solid #eee; text-align: center; color: #666; font-size: 12px;'>
                <p>ConvState AI Admin Paneli</p>
                <p>Bu e-posta otomatik olarak gönderilmiştir.</p>
            </div>
        </div>
        ";
    }

    /**
     * Mail gönderim durumunu kontrol et
     */
    public function testMailConnection(): bool
    {
        try {
            // Test mail gönderimi
            Mail::raw('Test mail connection', function ($message) {
                $message->to('test@example.com')
                        ->subject('Test Connection')
                        ->from(config('mail.from.address'));
            });
            return true;
        } catch (\Exception $e) {
            Log::error('Mail connection test failed: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Mail istatistiklerini getir
     */
    public function getMailStats(): array
    {
        // Bu metod mail gönderim istatistiklerini döndürür
        // Gerçek implementasyonda veritabanından veri çekilebilir
        return [
            'total_sent' => 0,
            'successful' => 0,
            'failed' => 0,
            'last_sent' => null,
        ];
    }
}
