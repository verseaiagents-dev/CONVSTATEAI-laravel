<?php

namespace App\Listeners;

use App\Events\UserPasswordReset;
use App\Models\MailTemplate;
use App\Services\MailService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class SendPasswordResetEmail implements ShouldQueue
{
    use InteractsWithQueue;

    protected $mailService;

    /**
     * Create the event listener.
     */
    public function __construct(MailService $mailService)
    {
        $this->mailService = $mailService;
    }

    /**
     * Handle the event.
     */
    public function handle(UserPasswordReset $event): void
    {
        try {
            // Aktif security template'ini bul
            $template = MailTemplate::where('category', 'security')
                ->where('is_active', true)
                ->first();

            if (!$template) {
                Log::warning('Password reset mail template bulunamadı', [
                    'user_id' => $event->user->id,
                    'event' => $event->eventType
                ]);
                return;
            }

            // Template verilerini hazırla
            $mailData = [
                'username' => $event->user->name,
                'useremail' => $event->user->email,
                'reseturl' => $event->resetUrl,
                'sitename' => config('app.name'),
                'siteurl' => config('app.url'),
                'currentdate' => now()->format('Y-m-d'),
                'companyname' => 'ConvState AI',
                'companyaddress' => 'İstanbul, Türkiye',
                'companyphone' => '+90 212 XXX XX XX'
            ];

            // Template'i parse et
            $subject = $template->parseSubject($mailData);
            $content = $template->parseContent($mailData);

            // Mail gönder
            $this->mailService->sendNotificationEmail(
                $event->user->email,
                $subject,
                $content,
                $event->user->name
            );

            Log::info('Password reset email sent', [
                'user_id' => $event->user->id,
                'template_id' => $template->id,
                'event' => $event->eventType
            ]);

        } catch (\Exception $e) {
            Log::error('Password reset mail gönderimi başarısız', [
                'user_id' => $event->user->id,
                'error' => $e->getMessage(),
                'event' => $event->eventType
            ]);
        }
    }
}
