<?php

namespace App\Mail;

use App\Models\MaintenanceRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class MaintenanceRequestMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public MaintenanceRequest $request)
    {
    }

    public function envelope(): Envelope
    {
        $room = $this->request->room?->room_number ? " (Kamar {$this->request->room->room_number})" : '';

        return new Envelope(subject: "Permintaan Perbaikan Baru{$room} — {$this->request->title}");
    }

    public function content(): Content
    {
        return new Content(view: 'mail.maintenance-request');
    }
}
