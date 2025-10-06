<?php

namespace App\Mail;

use App\Models\GiftboxUsers;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class GiftboxWelcomeEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $giftboxUser;
    public $sectorName;
    public $downloadUrl;

    /**
     * Create a new message instance.
     */
    public function __construct(GiftboxUsers $giftboxUser)
    {
        $this->giftboxUser = $giftboxUser;
        $this->sectorName = $this->getTurkishSectorName($giftboxUser->sector);
        $this->downloadUrl = url('/gift/' . $giftboxUser->sector);
    }

    /**
     * Get Turkish sector name
     */
    private function getTurkishSectorName($sector)
    {
        $sectorNames = [
            'fashion' => 'Moda',
            'furniture' => 'Mobilya',
            'home-appliances' => 'Beyaz Eşya & Ev Aletleri',
            'health-beauty' => 'Sağlık & Güzellik',
            'electronics' => 'Elektronik'
        ];

        return $sectorNames[$sector] ?? ucfirst(str_replace('-', ' ', $sector));
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->sectorName . ' Sektörü Ücretsiz Kitapçığınız Hazır!',
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
            view: 'emails.giftbox-welcome',
            with: [
                'giftboxUser' => $this->giftboxUser,
                'sectorName' => $this->sectorName,
                'downloadUrl' => $this->downloadUrl,
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
