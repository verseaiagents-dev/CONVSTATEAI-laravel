<?php

namespace App\Listeners;

use App\Events\UserSubscriptionActivated;
use App\Models\MailTemplate;
use App\Services\MailService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class SendSubscriptionWelcomeEmail implements ShouldQueue
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
    public function handle(UserSubscriptionActivated $event): void
    {
        try {
            // Aktif subscription template'ini bul
            $template = MailTemplate::where('category', 'subscription')
                ->where('is_active', true)
                ->first();

            if (!$template) {
                Log::warning('Subscription welcome mail template bulunamadı', [
                    'user_id' => $event->user->id,
                    'subscription_id' => $event->subscription->id,
                    'event' => $event->eventType
                ]);
                return;
            }

            // Template verilerini hazırla
            $mailData = [
                'username' => $event->user->name,
                'useremail' => $event->user->email,
                'userplan' => $event->subscription->plan->name ?? 'Premium Plan',
                'userplanexpired' => $event->subscription->next_billing_date ?? 'Bir sonraki ay',
                'usercreated' => $event->user->created_at->format('Y-m-d'),
                'sitename' => config('app.name'),
                'siteurl' => config('app.url'),
                'dashboardurl' => url('/dashboard'),
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

            Log::info('Subscription welcome email sent', [
                'user_id' => $event->user->id,
                'subscription_id' => $event->subscription->id,
                'template_id' => $template->id,
                'event' => $event->eventType
            ]);

        } catch (\Exception $e) {
            Log::error('Subscription welcome mail gönderimi başarısız', [
                'user_id' => $event->user->id,
                'subscription_id' => $event->subscription->id,
                'error' => $e->getMessage(),
                'event' => $event->eventType
            ]);
        }
    }
}
