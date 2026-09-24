<?php

namespace App\Services;

use App\Models\Invoice;

class RevenueSplitService
{
    /**
     * Credit an online (DOKU) invoice payment to the property's owners.
     *
     * Flow: deduct the property's platform fee (Living Kost's cut) from the gross
     * rent first, then split the NET among the property's co-owners by their
     * share percentage. Each owner's wallet is credited their exact final amount
     * (no further fee — it was already taken at the property level).
     */
    public static function creditOnlineInvoice(Invoice $invoice): void
    {
        $invoice->loadMissing('lease.room.roomType.property');
        $property = $invoice->lease?->room?->roomType?->property;
        $gross = (float) $invoice->amount;

        // Fallback: property not resolvable — credit the invoice owner as before.
        if (! $property) {
            if ($invoice->owner_id) {
                WalletService::credit($invoice->owner_id, $gross, $invoice);
            }
            return;
        }

        $feePct = (float) ($property->platform_fee_percent ?? 0);
        $net = round($gross * (1 - $feePct / 100), 2);
        $shares = $property->ownerShares(); // [owner_id => percent]

        if (empty($shares) || $net <= 0) {
            return;
        }

        // Compute each owner's portion; the largest-share owner absorbs any
        // rounding remainder so the parts always sum exactly to the net.
        arsort($shares);
        $portions = [];
        $sum = 0.0;
        foreach ($shares as $ownerId => $pct) {
            $portions[$ownerId] = round($net * $pct / 100, 2);
            $sum += $portions[$ownerId];
        }
        $first = array_key_first($portions);
        $portions[$first] = round($portions[$first] + ($net - $sum), 2);

        foreach ($portions as $ownerId => $portion) {
            if ($portion == 0.0) {
                continue;
            }
            $pct = rtrim(rtrim(number_format($shares[$ownerId], 2), '0'), '.');
            $desc = "Bagi hasil {$property->name} ({$pct}%)" . ($feePct > 0 ? " — net setelah fee {$feePct}%" : '');
            WalletService::credit((int) $ownerId, (float) $portion, $invoice, $desc, applyFee: false);
        }
    }
}
