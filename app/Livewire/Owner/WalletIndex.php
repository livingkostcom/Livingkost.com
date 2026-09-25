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
        $user = Auth::user();
        $ownerId = $user->ownerId() ?? $user->id;
        $wallet = WalletService::forOwner($ownerId);

        // Available balance & totals are computed from source data so each owner
        // sees their exact final share (income − fee − expense) × share.
        $figures = WalletService::figuresFor($user);

        $transactions = WalletTransaction::where('owner_id', $ownerId)
            // DP deposits belong to the registration owner only; never show them
            // to a co-owner viewer.
            ->when($user->isCoOwnerViewer(), fn ($q) => $q->where('description', 'not like', 'DP pendaftaran%'))
            ->latest()
            ->paginate(15);

        $disbursements = Disbursement::where('owner_id', $ownerId)
            ->latest()
            ->take(10)
            ->get();

        return view('livewire.owner.wallet-index', [
            'wallet' => $wallet,
            'figures' => $figures,
            'isCoOwner' => $user->isCoOwnerViewer(),
            'transactions' => $transactions,
            'disbursements' => $disbursements,
        ]);
    }
}
