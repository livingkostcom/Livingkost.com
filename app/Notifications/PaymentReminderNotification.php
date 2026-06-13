<?php

namespace App\Notifications;

use App\Mail\PaymentReminderMail;
use App\Models\Invoice;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class PaymentReminderNotification extends Notification
{
    use Queueable;

    public Invoice $invoice;
    public string $reminderType;

    public function __construct(Invoice $invoice, string $reminderType = 'upcoming')
    {
        $this->invoice = $invoice;
        $this->reminderType = $reminderType;
    }

    public function via(object $notifiable): array
    {
        $channels = ['database'];

        if (config('mail.default') !== 'log' && !empty($notifiable->email)) {
            $channels[] = 'mail';
        }

        return $channels;
    }

    public function toMail(object $notifiable): PaymentReminderMail
    {
        return new PaymentReminderMail($this->invoice, $this->reminderType);
    }

    public function toArray(object $notifiable): array
    {
        $message = match ($this->reminderType) {
            'overdue' => "Pembayaran invoice {$this->invoice->reference_number} sudah melewati jatuh tempo ({$this->invoice->due_date->translatedFormat('d M Y')})",
            'due_today' => "Invoice {$this->invoice->reference_number} jatuh tempo hari ini",
            default => "Invoice {$this->invoice->reference_number} akan jatuh tempo pada {$this->invoice->due_date->translatedFormat('d M Y')}",
        ];

        return [
            'type' => 'payment_reminder',
            'reminder_type' => $this->reminderType,
            'invoice_id' => $this->invoice->id,
            'reference_number' => $this->invoice->reference_number,
            'amount' => $this->invoice->amount,
            'due_date' => $this->invoice->due_date->format('Y-m-d'),
            'month_year' => $this->invoice->month_year,
            'message' => $message,
        ];
    }
}
