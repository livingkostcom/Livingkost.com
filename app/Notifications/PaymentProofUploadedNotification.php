<?php

namespace App\Notifications;

use App\Mail\PaymentProofMail;
use App\Models\Invoice;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class PaymentProofUploadedNotification extends Notification
{
    use Queueable;

    public function __construct(public Invoice $invoice)
    {
    }

    public function via(object $notifiable): array
    {
        $channels = ['database'];

        if (config('mail.default') !== 'log' && !empty($notifiable->email)) {
            $channels[] = 'mail';
        }

        return $channels;
    }

    public function toMail(object $notifiable): PaymentProofMail
    {
        return (new PaymentProofMail($this->invoice))
            ->to($notifiable->email, $notifiable->name ?? '');
    }

    public function toArray(object $notifiable): array
    {
        $tenant = $this->invoice->lease->tenant->display_name ?? $this->invoice->lease->tenant->name;
        $room = $this->invoice->lease->room->room_number ?? '-';

        return [
            'type' => 'payment_proof_uploaded',
            'invoice_id' => $this->invoice->id,
            'reference_number' => $this->invoice->reference_number,
            'tenant_name' => $tenant,
            'room_number' => $room,
            'amount' => $this->invoice->amount,
            'message' => "Bukti pembayaran invoice {$this->invoice->reference_number} dari {$tenant} (Kamar {$room}) telah diunggah — menunggu verifikasi.",
        ];
    }
}
