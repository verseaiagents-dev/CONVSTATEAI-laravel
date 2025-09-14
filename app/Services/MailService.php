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
