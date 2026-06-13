<?php

namespace App\Livewire\Admin;

use App\Models\OwnerWallet;
use App\Models\User;
use App\Services\WalletService;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class PaymentPartnerIndex extends Component
{
    public string $search = '';
    public string $successMessage = '';
    public string $errorMessage = '';

    /** Per-owner fee inputs, keyed by owner id. */
    public array $fees = [];

    public function mount()
    {
        abort_unless(Auth::user()?->isSuperAdmin(), 403);

        foreach (User::role('owner')->get() as $owner) {
            $wallet = WalletService::forOwner($owner->id);
            $this->fees[$owner->id] = (float) $wallet->platform_fee_percent;
        }
    }

    public function toggle(int $ownerId)
    {
        abort_unless(Auth::user()?->isSuperAdmin(), 403);

        $wallet = WalletService::forOwner($ownerId);
        $wallet->online_payment_enabled = ! $wallet->online_payment_enabled;
        $wallet->save();

        $this->successMessage = $wallet->online_payment_enabled
            ? 'Pembayaran online DIAKTIFKAN untuk owner ini.'
            : 'Pembayaran online DINONAKTIFKAN untuk owner ini.';
        $this->errorMessage = '';
    }

    public function saveFee(int $ownerId)
    {
        abort_unless(Auth::user()?->isSuperAdmin(), 403);

        $fee = (float) ($this->fees[$ownerId] ?? 0);
        if ($fee < 0 || $fee > 100) {
            $this->errorMessage = 'Fee platform harus antara 0–100%.';
            return;
        }

        $wallet = WalletService::forOwner($ownerId);
        $wallet->platform_fee_percent = $fee;
        $wallet->save();

        $this->successMessage = 'Fee platform diperbarui menjadi ' . rtrim(rtrim(number_format($fee, 2), '0'), '.') . '% untuk owner ini.';
        $this->errorMessage = '';
    }

    public function render()
    {
        $query = User::role('owner')->with('wallet');

        if ($this->search !== '') {
            $query->where(function ($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                    ->orWhere('email', 'like', '%' . $this->search . '%');
            });
        }

        $owners = $query->orderBy('name')->get()->map(function ($owner) {
            $wallet = $owner->wallet ?: WalletService::forOwner($owner->id);
            return [
                'id' => $owner->id,
                'name' => $owner->name,
                'email' => $owner->email,
                'enabled' => (bool) $wallet->online_payment_enabled,
                'fee' => (float) $wallet->platform_fee_percent,
                'balance' => (float) $wallet->balance,
                'total_earned' => (float) $wallet->total_earned,
                'total_disbursed' => (float) $wallet->total_disbursed,
            ];
        });

        return view('livewire.admin.payment-partner-index', ['owners' => $owners]);
    }
}
