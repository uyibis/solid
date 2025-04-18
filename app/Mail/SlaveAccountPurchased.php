<?php
namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class SlaveAccountPurchased extends Mailable
{
    use Queueable, SerializesModels;

    public $masterIds;  // Declare a public property to hold the master IDs (codes)
    public $slave_trader;
    public $slave_name;
    /**
     * Create a new message instance.
     */
    public function __construct($masterIds,$slave_trader,$slave_name)
    {
        $this->masterIds = $masterIds;  // Assign the passed master IDs to the property
        $this->slave_trader=$slave_trader;
        $this->slave_name=$slave_name;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Slave Account Purchased',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.customer',
            with: [
                'masterIds' => $this->masterIds,
                'slave_trader'=>$this->slave_trader,
            ]
        );
    }
}
