<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use App\Models\User;
use App\Services\ReferralRewards;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class ReferralController extends Controller
{
    private function owner(Request $r): int
    {
        abort_unless($r->user()?->isOwner() && ! $r->user()->isCoOwnerViewer(), 403);
        return $r->user()->id;
    }

    public function manage(Request $r)
    {
        $ownerId = $this->owner($r);
        ReferralRewards::syncOwner($ownerId);
        $agents = DB::table('sales_agents as a')->join('users as u', 'u.id', '=', 'a.user_id')->where('a.owner_id', $ownerId)
            ->select('a.*', 'u.name', 'u.email')->orderBy('u.name')->get();
        $token = $agents->isEmpty() ? null : ReferralRewards::registrationToken($ownerId);
        return response()->view('referrals.manage', [
            'agents' => $agents, 'registrationToken' => $token,
            'rates' => ReferralRewards::rates($ownerId),
            'roomTypes' => DB::table('room_types as rt')->join('properties as p', 'p.id', '=', 'rt.property_id')->where('rt.owner_id', $ownerId)->where('p.owner_id', $ownerId)->orderBy('p.name')->orderBy('rt.name')->get(['rt.id', 'rt.name', 'rt.price', 'p.name as property_name']),
            'leads' => $this->leads($ownerId)->paginate(20), 'summary' => $this->summary($ownerId),
        ])->header('Cache-Control', 'private, no-store');
    }

    public function rate(Request $r)
    {
        $ownerId = $this->owner($r);
        $data = $r->validate(['rates' => 'required|array', 'rates.*' => 'required|integer|min:0|max:10000000']);
        $owned = DB::table('room_types')->where('owner_id', $ownerId)->pluck('id')->map(fn ($id) => (string) $id)->all();
        $submitted = array_map('strval', array_keys($data['rates']));
        if (array_diff($submitted, $owned) || array_diff($owned, $submitted)) {
            throw ValidationException::withMessages(['rates' => 'Tipe ruangan berubah atau tidak sesuai owner. Muat ulang halaman lalu simpan kembali.']);
        }
        $rates = array_map('intval', $data['rates']);
        Setting::updateOrCreate(['owner_id' => $ownerId, 'key' => 'referral_reward_rates'], ['value' => json_encode($rates), 'group' => 'referral']);
        return back()->with('referral_notice', 'Reward per tipe ruangan disimpan untuk pendaftaran baru. Ketentuan referral lama tetap.');
    }

    public function createSales(Request $r)
    {
        $ownerId = $this->owner($r);
        $data = $r->validate(['name' => 'required|string|max:100', 'email' => 'required|email|max:255|unique:users,email', 'phone' => 'required|string|max:20', 'password' => 'required|string|min:12|max:128|confirmed']);
        DB::transaction(function () use ($ownerId, $data) {
            $user = User::create(['owner_id' => $ownerId, 'name' => $data['name'], 'email' => $data['email'], 'phone' => $data['phone'], 'password' => Hash::make($data['password'])]);
            $user->assignRole('sales');
            DB::table('sales_agents')->insert(['owner_id' => $ownerId, 'user_id' => $user->id, 'code' => Str::upper(Str::random(24)), 'active' => true, 'created_at' => now(), 'updated_at' => now()]);
            ReferralRewards::registrationToken($ownerId);
        });
        return back()->with('referral_notice', 'Akun sales dibuat. Sampaikan email login dan password kepada sales melalui saluran pribadi.');
    }

    public function toggle(Request $r, int $agent)
    {
        $ownerId = $this->owner($r);
        DB::transaction(function () use ($ownerId, $agent) {
            $row = DB::table('sales_agents')->where('id', $agent)->where('owner_id', $ownerId)->lockForUpdate()->first();
            abort_unless($row, 404);
            DB::table('sales_agents')->where('id', $row->id)->update(['active' => ! $row->active, 'updated_at' => now()]);
        });
        return back()->with('referral_notice', 'Status akses akun dan tautan sales diperbarui.');
    }

    public function verifyDp(Request $r, int $registration)
    {
        $ownerId = $this->owner($r);
        DB::transaction(function () use ($ownerId, $registration) {
            $reg = DB::table('tenant_registrations')->where('owner_id', $ownerId)->where('id', $registration)->whereNotNull('sales_agent_id')->lockForUpdate()->first();
            abort_unless($reg, 404);
            abort_unless($reg->dp_method === 'manual' && $reg->dp_status === 'submitted' && $reg->dp_proof && (float) $reg->dp_amount > 0, 422);
            DB::table('tenant_registrations')->where('id', $reg->id)->update(['dp_status' => 'paid', 'dp_paid_at' => now(), 'updated_at' => now()]);
            if ($reg->tenant_id) ReferralRewards::syncTenant($ownerId, $reg->tenant_id);
        });
        return back()->with('referral_notice', 'DP transfer manual ditandai terverifikasi. Tidak ada transfer uang otomatis.');
    }

    public function paid(Request $r, int $reward)
    {
        $ownerId = $this->owner($r);
        $data = $r->validate(['reference' => 'required|string|max:200']);
        DB::transaction(function () use ($ownerId, $reward, $data, $r) {
            $row = DB::table('referral_rewards')->where('id', $reward)->where('owner_id', $ownerId)->lockForUpdate()->first();
            abort_unless($row && $row->earned_at && (float) $row->amount > 0, 422);
            if ($row->paid_at) return;
            DB::table('referral_rewards')->where('id', $row->id)->update(['paid_at' => now(), 'paid_by' => $r->user()->id, 'payment_reference' => $data['reference'], 'updated_at' => now()]);
        });
        return back()->with('referral_notice', 'Pembayaran reward dicatat. Fitur ini mencatat pembayaran di luar aplikasi, bukan melakukan transfer.');
    }

    public function sales(Request $r)
    {
        abort_unless($r->user()?->hasRole('sales'), 403);
        $agent = DB::table('sales_agents')->where('user_id', $r->user()->id)->where('owner_id', $r->user()->owner_id)->where('active', true)->first();
        abort_unless($agent, 403, 'Akses sales dinonaktifkan. Hubungi owner.');
        ReferralRewards::syncOwner($agent->owner_id, $agent->id);
        $rooms = DB::table('rooms as r')->join('room_types as rt', 'rt.id', '=', 'r.room_type_id')->join('properties as p', 'p.id', '=', 'rt.property_id')
            ->where('r.owner_id', $agent->owner_id)->where('rt.owner_id', $agent->owner_id)->where('p.owner_id', $agent->owner_id)->where('p.status', 'active')
            ->select('r.room_number', 'r.floor', 'r.room_type_id', 'rt.name as type_name', 'rt.price', 'p.name as property_name')
            ->selectRaw("CASE WHEN EXISTS (SELECT 1 FROM leases l WHERE l.room_id = r.id AND l.status = 'active' AND l.deleted_at IS NULL) THEN 'occupied' WHEN r.status = 'maintenance' THEN 'maintenance' ELSE 'available' END AS availability")
            ->orderBy('p.name')->orderBy('r.floor')->orderBy('r.room_number')->get();
        $rates = ReferralRewards::rates($agent->owner_id);
        foreach ($rooms as $room) $room->referral_reward_amount = $rates[$room->room_type_id] ?? 0;
        // Do not send NIK, KTP, emergency contacts, proof images or other sales' leads to this view.
        $leads = $this->leads($agent->owner_id, $agent->id)->paginate(15);
        foreach ($leads as $lead) $lead->phone = '••••' . substr(preg_replace('/\D/', '', $lead->phone), -4);
        return response()->view('referrals.sales', [
            'agent' => $agent, 'rooms' => $rooms, 'leads' => $leads,
            'summary' => $this->summary($agent->owner_id, $agent->id),
            'shareUrl' => route('tenant.register', ReferralRewards::registrationToken($agent->owner_id)) . '?ref=' . $agent->code,
        ])->header('Cache-Control', 'private, no-store');
    }

    public function logout(Request $r)
    {
        Auth::logout(); $r->session()->invalidate(); $r->session()->regenerateToken();
        return redirect()->route('login');
    }

    private function leads(int $ownerId, ?int $agentId = null)
    {
        return DB::table('tenant_registrations as tr')->join('sales_agents as a', 'a.id', '=', 'tr.sales_agent_id')->join('users as u', 'u.id', '=', 'a.user_id')
            ->leftJoin('referral_rewards as rw', 'rw.registration_id', '=', 'tr.id')->leftJoin('leases as l', 'l.id', '=', 'rw.lease_id')->leftJoin('rooms as r', 'r.id', '=', 'l.room_id')
            ->where('tr.owner_id', $ownerId)->where('a.owner_id', $ownerId)->when($agentId, fn ($q) => $q->where('a.id', $agentId))
            ->select('tr.id', 'tr.name', 'tr.phone', 'tr.status', 'tr.dp_method', 'tr.dp_status', 'tr.dp_amount', 'tr.dp_paid_at', 'tr.created_at', 'tr.referral_reward_amount', 'tr.referral_reward_rates', 'rw.room_type_name', 'u.name as sales_name', 'rw.id as reward_id', 'rw.earned_at', 'rw.paid_at', 'rw.amount as reward_amount', 'rw.payment_reference', 'r.room_number')
            ->orderByDesc('tr.id');
    }

    private function summary(int $ownerId, ?int $agentId = null): array
    {
        $regs = DB::table('tenant_registrations')->where('owner_id', $ownerId)->whereNotNull('sales_agent_id')->when($agentId, fn ($q) => $q->where('sales_agent_id', $agentId));
        $rewards = DB::table('referral_rewards')->where('owner_id', $ownerId)->when($agentId, fn ($q) => $q->where('sales_agent_id', $agentId));
        return ['registered' => (clone $regs)->count(), 'dp_paid' => (clone $regs)->where('dp_status', 'paid')->whereNotNull('dp_paid_at')->count(), 'converted' => (clone $rewards)->whereNotNull('earned_at')->count(), 'earned' => (clone $rewards)->whereNotNull('earned_at')->sum('amount'), 'paid' => (clone $rewards)->whereNotNull('paid_at')->sum('amount'), 'available' => (clone $rewards)->whereNotNull('earned_at')->whereNull('paid_at')->sum('amount')];
    }
}
