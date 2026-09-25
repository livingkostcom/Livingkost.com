<?php

namespace App\Services;

use App\Models\Disbursement;
use App\Models\Expense;
use App\Models\Invoice;
use App\Models\Lease;
use App\Models\OwnerWallet;
use App\Models\Property;
use App\Models\Room;
use App\Models\RoomType;
use App\Models\User;
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
     * The properties a user earns from, with their agreed share percent.
     * - properties using the revenue-share pivot: the user's row share (if any)
     * - properties without any pivot rows: the primary owner gets 100%
     *
     * @return array<int, float>  [property_id => share_percent]
     */
    public static function shareMap(User $user): array
    {
        $map = [];

        // Revenue-share pivot rows for this user.
        foreach (DB::table('property_owners')->where('owner_id', $user->id)->get(['property_id', 'share_percent']) as $row) {
            $map[(int) $row->property_id] = (float) $row->share_percent;
        }

        // Solo properties (no pivot rows at all) primarily owned by this user → 100%.
        $pivotPropertyIds = DB::table('property_owners')->distinct()->pluck('property_id')->all();
        $soloIds = Property::withoutGlobalScopes()
            ->where('owner_id', $user->id)
            ->when(! empty($pivotPropertyIds), fn ($q) => $q->whereNotIn('id', $pivotPropertyIds))
            ->pluck('id');
        foreach ($soloIds as $pid) {
            $map[(int) $pid] = 100.0;
        }

        return $map;
    }

    /**
     * A user's wallet figures, computed from source data (not the running
     * ledger) so every owner sees their exact final share.
     *
     * available = (total income − Living Kost fee − total expenses) × share
     *             + deposits held by this owner (DP; primary owner only)
     *             − amounts already disbursed
     *
     * Deposits (DP) belong to the owner who took the registration and are NOT
     * split, so co-owner viewers never see them.
     */
    public static function figuresFor(User $user): array
    {
        $incomeShare = 0.0;
        $feeShare = 0.0;
        $expenseShare = 0.0;

        foreach (self::shareMap($user) as $propertyId => $sharePercent) {
            $share = $sharePercent / 100;

            $rtIds = RoomType::withoutGlobalScopes()->where('property_id', $propertyId)->pluck('id');
            $roomIds = Room::withoutGlobalScopes()->whereIn('room_type_id', $rtIds)->pluck('id');
            $leaseIds = Lease::withoutGlobalScopes()->whereIn('room_id', $roomIds)->pluck('id');

            $gross = (float) Invoice::withoutGlobalScopes()
                ->whereIn('lease_id', $leaseIds)
                ->where('status', 'paid')
                ->sum('amount');
            $feePercent = (float) (Property::withoutGlobalScopes()->where('id', $propertyId)->value('platform_fee_percent') ?? 0);
            $fee = $gross * $feePercent / 100;
            $expense = (float) Expense::withoutGlobalScopes()->where('property_id', $propertyId)->sum('amount');

            $incomeShare += $gross * $share;
            $feeShare += $fee * $share;
            $expenseShare += $expense * $share;
        }

        $profitShare = round($incomeShare - $feeShare - $expenseShare, 2);

        // Deposits (DP) are the registration owner's held cash — never split,
        // so a co-owner viewer gets none.
        $deposits = $user->isCoOwnerViewer() ? 0.0 : (float) WalletTransaction::where('owner_id', $user->id)
            ->where('type', 'credit')
            ->where('description', 'like', 'DP pendaftaran%')
            ->sum('amount');

        $disbursed = (float) (OwnerWallet::where('owner_id', $user->id)->value('total_disbursed') ?? 0);

        $totalEarned = round($profitShare + $deposits, 2);

        return [
            'income' => round($incomeShare, 2),
            'platform_fee' => round($feeShare, 2),
            'expense' => round($expenseShare, 2),
            'profit_share' => $profitShare,
            'deposits' => round($deposits, 2),
            'total_earned' => $totalEarned,
            'total_disbursed' => round($disbursed, 2),
            'available' => round($totalEarned - $disbursed, 2),
        ];
    }

    /**
     * Credit an owner's wallet (e.g. a tenant paid online). Returns the wallet.
     * Applies the configured platform fee; the net amount is what's credited.
     */
    public static function credit(int $ownerId, float $grossAmount, ?Invoice $invoice = null, ?string $description = null, bool $applyFee = true): OwnerWallet
    {
        return DB::transaction(function () use ($ownerId, $grossAmount, $invoice, $description, $applyFee) {
            $wallet = OwnerWallet::lockForUpdate()->firstOrCreate(['owner_id' => $ownerId]);

            // Deposits (DP) are credited in full — the platform fee only applies
            // to rent/invoice payments, not to the tenant's deposit.
            $fee = $applyFee ? round($grossAmount * ((float) $wallet->platform_fee_percent / 100), 2) : 0.0;
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
