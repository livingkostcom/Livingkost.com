<?php

namespace App\Services;

use App\Models\Lease;
use App\Models\Setting;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ReferralRewards
{
    public static function rate(int $ownerId): int
    {
        return max(0, (int) Setting::where('owner_id', $ownerId)->where('key', 'referral_reward_amount')->value('value'));
    }

    public static function registrationToken(int $ownerId): string
    {
        return (string) Setting::firstOrCreate(['owner_id' => $ownerId, 'key' => 'tenant_reg_token'], ['value' => Str::random(24), 'group' => 'registration'])->value;
    }

    public static function trackLease(Lease $lease): void
    {
        if (! $lease->tenant_id) return;
        self::syncTenant((int) $lease->owner_id, (int) $lease->tenant_id);
    }

    public static function syncOwner(int $ownerId, ?int $agentId = null): void
    {
        DB::table('tenant_registrations')->where('owner_id', $ownerId)->whereNotNull('sales_agent_id')
            ->where('status', 'approved')->whereNotNull('tenant_id')
            ->when($agentId, fn ($q) => $q->where('sales_agent_id', $agentId))
            ->orderBy('id')->chunkById(100, function ($rows) use ($ownerId) {
                foreach ($rows as $row) self::syncTenant($ownerId, (int) $row->tenant_id);
            });
    }

    public static function syncTenant(int $ownerId, int $tenantId): void
    {
        DB::transaction(function () use ($ownerId, $tenantId) {
            // Lock the beneficiary, so repeated page loads/webhooks cannot award twice.
            $tenant = DB::table('tenants')->where('id', $tenantId)->where('owner_id', $ownerId)->whereNull('deleted_at')->lockForUpdate()->first();
            if (! $tenant) return;
            $reg = DB::table('tenant_registrations as tr')->join('sales_agents as sa', 'sa.id', '=', 'tr.sales_agent_id')
                ->where('tr.owner_id', $ownerId)->where('sa.owner_id', $ownerId)
                ->where('tr.tenant_id', $tenantId)->where('tr.status', 'approved')->orderBy('tr.id')
                ->select('tr.*')->first();
            if (! $reg) return;
            $reward = DB::table('referral_rewards')->where('tenant_id', $tenantId)->first();
            if ($reward?->earned_at || $reward?->paid_at) return;
            // First non-cancelled contract is the only contract that can earn this referral.
            $lease = DB::table('leases as l')->join('rooms as r', 'r.id', '=', 'l.room_id')->join('room_types as rt', 'rt.id', '=', 'r.room_type_id')
                ->where('l.owner_id', $ownerId)->where('r.owner_id', $ownerId)->where('rt.owner_id', $ownerId)
                ->where('l.tenant_id', $tenantId)->whereNull('l.deleted_at')->where('l.status', '!=', 'cancelled')
                ->orderBy('l.start_date')->orderBy('l.id')->select('l.*', 'rt.price')->first();
            if (! $lease) return;
            if (! $reward) {
                DB::table('referral_rewards')->insert([
                    'owner_id' => $ownerId, 'sales_agent_id' => $reg->sales_agent_id, 'registration_id' => $reg->id,
                    'tenant_id' => $tenantId, 'lease_id' => $lease->id, 'amount' => $reg->referral_reward_amount,
                    'rent_amount' => $lease->price, 'created_at' => now(), 'updated_at' => now(),
                ]);
                $reward = DB::table('referral_rewards')->where('tenant_id', $tenantId)->first();
            } elseif ((int) $reward->lease_id !== (int) $lease->id) {
                DB::table('referral_rewards')->where('id', $reward->id)->update(['lease_id' => $lease->id, 'rent_amount' => $lease->price, 'updated_at' => now()]);
                $reward = DB::table('referral_rewards')->where('id', $reward->id)->first();
            }
            if (! in_array($lease->status, ['active', 'completed', 'terminated'], true)) return;
            $month = substr($lease->start_date, 0, 7);
            $invoices = DB::table('invoices')->where('owner_id', $ownerId)->where('lease_id', $lease->id)
                ->where('month_year', $month)->whereNull('deleted_at')->get();
            // Proof upload, payment redirect, DP alone, or a later month's invoice is insufficient.
            if ($invoices->isEmpty() || $invoices->contains(fn ($i) => $i->status !== 'paid' || ! $i->verified_at)) return;
            $paidCents = (int) round($invoices->sum('amount') * 100);
            $dpCents = $reg->dp_status === 'paid' && $reg->dp_paid_at ? (int) round((float) $reg->dp_amount * 100) : 0;
            if ((float) $reward->rent_amount <= 0 || $paidCents + $dpCents < (int) round((float) $reward->rent_amount * 100)) return;
            DB::table('referral_rewards')->where('id', $reward->id)->whereNull('earned_at')->update(['earned_at' => now(), 'updated_at' => now()]);
        });
    }
}
