<?php

namespace App\Mail;

use App\Models\User;
use App\Models\Subscription;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class SubscriptionWelcomeEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $subscription;
    public $plan;
    public $dashboardUrl;

    /**
     * Create a new message instance.
     */
    public function __construct(User $user, Subscription $subscription)
    {
        $this->user = $user;
        $this->subscription = $subscription;
        $this->plan = $subscription->plan;
        $this->dashboardUrl = url('/dashboard');
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Aboneliğiniz Aktif! ConvState AI Premium Özelliklerine Erişim',
            from: config('mail.from.address'),
            replyTo: config('mail.from.address'),
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.subscription-welcome',
            with: [
                'user' => $this->user,
                'subscription' => $this->subscription,
                'plan' => $this->plan,
                'dashboardUrl' => $this->dashboardUrl,
            ],
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
