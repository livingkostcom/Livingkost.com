<?php

namespace App\Services;

use App\Models\Disbursement;
use App\Models\Invoice;
use App\Models\OwnerWallet;
use App\Models\WalletTransaction;
use Illuminate\Support\Facades\DB;

class WalletService
{
    /**
     * Get (or lazily create) the wallet row for an owner.
     */
    public static function forOwner(int $ownerId): OwnerWallet
    {
        return OwnerWallet::firstOrCreate(['owner_id' => $ownerId]);
    }

    /**
     * Credit an owner's wallet (e.g. a tenant paid online). Returns the wallet.
     * Applies the configured platform fee; the net amount is what's credited.
     */
    public static function credit(int $ownerId, float $grossAmount, ?Invoice $invoice = null, ?string $description = null): OwnerWallet
    {
        return DB::transaction(function () use ($ownerId, $grossAmount, $invoice, $description) {
            $wallet = OwnerWallet::lockForUpdate()->firstOrCreate(['owner_id' => $ownerId]);

            $fee = round($grossAmount * ((float) $wallet->platform_fee_percent / 100), 2);
            $net = round($grossAmount - $fee, 2);

            $wallet->balance = (float) $wallet->balance + $net;
            $wallet->total_earned = (float) $wallet->total_earned + $net;
            $wallet->save();

            WalletTransaction::create([
                'owner_id' => $ownerId,
                'type' => 'credit',
                'source' => 'payment',
                'amount' => $net,
                'balance_after' => $wallet->balance,
                'invoice_id' => $invoice?->id,
                'description' => $description ?? ($invoice ? "Pembayaran invoice {$invoice->reference_number}" . ($fee > 0 ? " (potongan fee Rp " . number_format($fee, 0, ',', '.') . ")" : '') : 'Pemasukan'),
            ]);

            return $wallet;
        });
    }

    /**
     * Debit an owner's wallet when a disbursement is completed.
     */
    public static function debitForDisbursement(Disbursement $disbursement): OwnerWallet
    {
        return DB::transaction(function () use ($disbursement) {
            $wallet = OwnerWallet::lockForUpdate()->firstOrCreate(['owner_id' => $disbursement->owner_id]);

            $amount = (float) $disbursement->amount;
            $wallet->balance = (float) $wallet->balance - $amount;
            $wallet->total_disbursed = (float) $wallet->total_disbursed + $amount;
            $wallet->save();

            WalletTransaction::create([
                'owner_id' => $disbursement->owner_id,
                'type' => 'debit',
                'source' => 'disbursement',
                'amount' => $amount,
                'balance_after' => $wallet->balance,
                'disbursement_id' => $disbursement->id,
                'description' => "Pencairan ke rekening" . ($disbursement->bank_name ? " {$disbursement->bank_name}" : ''),
            ]);

            return $wallet;
        });
    }
}
