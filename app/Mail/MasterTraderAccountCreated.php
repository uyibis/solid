<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class MasterTraderAccountCreated extends Mailable
{
    use Queueable, SerializesModels;

    public $userTrader;
    public $masterIds;

    public function __construct($userTrader)
    {
        $this->userTrader = $userTrader;
        $this->masterIds = $userTrader->trader->pluck('code')->toArray();
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Master Trader Account Created',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.master',
            with: [
                'userTrader' => $this->userTrader,
                'masterIds' => $this->masterIds,
            ]
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
