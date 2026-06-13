<?php

namespace App\Services;

use App\Models\Invoice;
use App\Models\OwnerWallet;
use App\Models\PaymentTransaction;
use Illuminate\Support\Str;

class PaymentInitiator
{
    /**
     * Create a DOKU Checkout payment for an invoice and record the transaction.
     *
     * @return array{success:bool, url?:string, error?:string}
     */
    public static function startForInvoice(Invoice $invoice): array
    {
        $invoice->loadMissing('lease.tenant');
        $ownerId = $invoice->owner_id ?? $invoice->lease?->tenant?->owner_id;

        if ($invoice->status === 'paid') {
            return ['success' => false, 'error' => 'Invoice ini sudah lunas.'];
        }
        if (! OwnerWallet::onlineEnabledFor($ownerId)) {
            return ['success' => false, 'error' => 'Pembayaran online belum tersedia untuk kos ini.'];
        }

        $doku = new DokuService();
        if (! $doku->isConfigured()) {
            return ['success' => false, 'error' => 'Gateway pembayaran belum dikonfigurasi.'];
        }

        $tenant = $invoice->lease->tenant;
        $reference = $invoice->reference_number . '-' . now()->format('YmdHis') . '-' . Str::upper(Str::random(4));

        $res = $doku->createCheckoutPayment(
            $reference,
            (float) $invoice->amount,
            $tenant->display_name ?? $tenant->name ?? 'Penghuni',
            $tenant->email,
            'https://www.livingkost.com/tenant/invoices'
        );

        if (empty($res['success'])) {
            return ['success' => false, 'error' => $res['error'] ?? 'Gagal memulai pembayaran.'];
        }

        PaymentTransaction::create([
            'invoice_id' => $invoice->id,
            'owner_id' => $ownerId,
            'gateway' => 'doku',
            'reference' => $reference,
            'amount' => $invoice->amount,
            'status' => 'pending',
            'payment_url' => $res['url'],
            'raw' => $res['raw'] ?? null,
        ]);

        return ['success' => true, 'url' => $res['url']];
    }
}
