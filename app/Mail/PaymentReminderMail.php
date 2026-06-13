<?php

namespace App\Mail;

use App\Models\Invoice;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PaymentReminderMail extends Mailable
{
    use Queueable, SerializesModels;

    public string $reminderType;

    public function __construct(public Invoice $invoice, string $reminderType = 'upcoming')
    {
        $this->reminderType = $reminderType;
    }

    public function envelope(): Envelope
    {
        $subject = match ($this->reminderType) {
            'overdue' => 'Tagihan Melewati Jatuh Tempo – ' . $this->invoice->reference_number,
            'due_today' => 'Tagihan Jatuh Tempo Hari Ini – ' . $this->invoice->reference_number,
            default => 'Pengingat Pembayaran – ' . $this->invoice->reference_number,
        };

        return new Envelope(subject: $subject);
    }

    public function content(): Content
    {
        return new Content(view: 'mail.payment-reminder');
    }
}
