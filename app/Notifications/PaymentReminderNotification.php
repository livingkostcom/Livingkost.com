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
        return (new PaymentReminderMail($this->invoice, $this->reminderType))
            ->to($notifiable->email, $notifiable->name ?? '');
    }

    public function toArray(object $notifiable): array
    {
        $dueFormatted = $this->invoice->due_date->translatedFormat('d M Y');
        $ref = $this->invoice->reference_number;

        $message = match ($this->reminderType) {
            'overdue' => "Pembayaran invoice {$ref} sudah melewati jatuh tempo ({$dueFormatted})",
            'due_today' => "Invoice {$ref} jatuh tempo hari ini",
            default => "Invoice {$ref} akan jatuh tempo pada {$dueFormatted}",
        };

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
