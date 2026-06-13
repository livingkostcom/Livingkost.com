<?php

namespace App\Livewire\Owner;

use App\Models\Disbursement;
use App\Models\WalletTransaction;
use App\Services\WalletService;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class WalletIndex extends Component
{
    use WithPagination;

    public function mount()
    {
        // Owners (and super-admin) only.
        abort_unless(Auth::user()?->isOwner() || Auth::user()?->isSuperAdmin(), 403);
    }

    public function render()
    {
        $ownerId = Auth::user()->ownerId() ?? Auth::id();
        $wallet = WalletService::forOwner($ownerId);

        $transactions = WalletTransaction::where('owner_id', $ownerId)
            ->latest()
            ->paginate(15);

        $disbursements = Disbursement::where('owner_id', $ownerId)
            ->latest()
            ->take(10)
            ->get();

        return view('livewire.owner.wallet-index', [
            'wallet' => $wallet,
            'transactions' => $transactions,
            'disbursements' => $disbursements,
        ]);
    }
}
