<?php

namespace App\Mail;

use App\Models\Invoice;
use App\Models\Setting;
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
        // Resolve the owning landlord's bank details explicitly (no Auth context
        // in scheduler/queue), based on the invoice's owner.
        $ownerId = $this->invoice->owner_id ?? $this->invoice->lease->tenant->owner_id ?? null;

        return new Content(view: 'mail.payment-reminder', with: [
            'bank' => [
                'name' => Setting::getForOwner('bank_name', $ownerId),
                'number' => Setting::getForOwner('bank_account_number', $ownerId),
                'holder' => Setting::getForOwner('bank_account_holder', $ownerId),
                'instructions' => Setting::getForOwner('payment_instructions', $ownerId),
            ],
            'onlineEnabled' => $this->invoice->isOnlinePaymentEnabled(),
            'payUrl' => $this->invoice->payUrl(),
        ]);
    }
}
