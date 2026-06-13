<?php

namespace App\Services;

use App\Models\PaymentTransaction;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PaymentSettlementService
{
    /**
     * Settle a successful online (DOKU) payment. Idempotent: safe to call more
     * than once for the same transaction (webhooks may fire repeatedly).
     *
     * Marks the invoice paid, generates + emails the receipt, credits the
     * owner's wallet (net of platform fee), and WhatsApps the tenant.
     */
    public static function settleOnline(PaymentTransaction $pt, array $payload = []): void
    {
        $pt->loadMissing('invoice.lease.tenant', 'invoice.lease.room');
        $invoice = $pt->invoice;

        if (! $invoice) {
            Log::warning('Settle: payment transaction without invoice', ['pt' => $pt->id]);
            return;
        }

        // Idempotency guard — already settled.
        if ($pt->status === 'paid' || $invoice->status === 'paid') {
            return;
        }

        $ownerId = $invoice->owner_id ?? $invoice->lease->tenant->owner_id ?? null;

        DB::transaction(function () use ($pt, $invoice, $ownerId, $payload) {
            $invoice->update([
                'status' => 'paid',
                'verified_at' => now(),
            ]);

            $pt->update([
                'status' => 'paid',
                'paid_at' => now(),
                'external_id' => $payload['transaction']['original_request_id'] ?? ($payload['order']['invoice_number'] ?? $pt->external_id),
                'raw' => $payload ?: $pt->raw,
            ]);

            if ($ownerId) {
                WalletService::credit($ownerId, (float) $invoice->amount, $invoice);
            }
        });

        // Receipt PDF + email to tenant (created_by must be a real user → owner).
        try {
            if ($ownerId) {
                $invoice->generateReceipt($ownerId);
            }
        } catch (\Throwable $e) {
            Log::error('Settle: receipt generation failed', ['invoice' => $invoice->id, 'error' => $e->getMessage()]);
        }

        // WhatsApp the tenant that the payment was accepted.
        try {
            $tenant = $invoice->lease->tenant;
            $phone = $tenant->phone ?? null;
            if ($phone) {
                $name = $tenant->display_name ?? $tenant->name ?? 'Penghuni';
                $amount = 'Rp ' . number_format($invoice->amount, 0, ',', '.');
                $email = $tenant->email;
                $emailLine = $email
                    ? "Receipt telah kami kirim ke email Anda: {$email}. Silakan cek email Anda."
                    : "Receipt telah kami kirim ke email Anda. Silakan cek email Anda.";

                $message = "Halo {$name},\n\n"
                    . "Pembayaran online Anda untuk tagihan *{$invoice->reference_number}* sebesar *{$amount}* telah *BERHASIL* dan terverifikasi otomatis. \u{2705}\n\n"
                    . "{$emailLine}\n\n"
                    . "Terima kasih.\n\n_Living Kost_";

                WhatsAppService::send($phone, $message);
            }
        } catch (\Throwable $e) {
            Log::error('Settle: tenant WhatsApp failed', ['invoice' => $invoice->id, 'error' => $e->getMessage()]);
        }
    }
}
