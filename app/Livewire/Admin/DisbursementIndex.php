<?php

namespace App\Livewire\Admin;

use App\Models\Disbursement;
use App\Models\Setting;
use App\Models\User;
use App\Services\WalletService;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class DisbursementIndex extends Component
{
    use WithPagination;

    public string $successMessage = '';
    public string $errorMessage = '';

    // Create modal
    public bool $showCreateModal = false;
    public ?int $createOwnerId = null;
    public string $createOwnerName = '';
    public $maxAmount = 0;
    public $amount = '';
    public string $bankName = '';
    public string $bankAccountNumber = '';
    public string $bankAccountHolder = '';
    public string $notes = '';

    public function mount()
    {
        abort_unless(Auth::user()?->isSuperAdmin(), 403);
    }

    public function openCreate(int $ownerId)
    {
        abort_unless(Auth::user()?->isSuperAdmin(), 403);

        $owner = User::findOrFail($ownerId);
        $wallet = WalletService::forOwner($ownerId);

        $this->createOwnerId = $ownerId;
        $this->createOwnerName = $owner->name;
        $this->maxAmount = (float) $wallet->balance;
        $this->amount = (string) (int) $wallet->balance;
        $this->bankName = (string) Setting::getForOwner('bank_name', $ownerId);
        $this->bankAccountNumber = (string) Setting::getForOwner('bank_account_number', $ownerId);
        $this->bankAccountHolder = (string) Setting::getForOwner('bank_account_holder', $ownerId);
        $this->notes = '';
        $this->errorMessage = '';
        $this->showCreateModal = true;
    }

    public function closeCreate()
    {
        $this->showCreateModal = false;
        $this->createOwnerId = null;
    }

    public function createDisbursement()
    {
        abort_unless(Auth::user()?->isSuperAdmin(), 403);

        $amount = (float) $this->amount;
        $wallet = WalletService::forOwner($this->createOwnerId);

        if ($amount <= 0) {
            $this->errorMessage = 'Jumlah pencairan harus lebih dari 0.';
            return;
        }
        if ($amount > (float) $wallet->balance) {
            $this->errorMessage = 'Jumlah melebihi saldo owner (Rp ' . number_format($wallet->balance, 0, ',', '.') . ').';
            return;
        }

        Disbursement::create([
            'owner_id' => $this->createOwnerId,
            'amount' => $amount,
            'status' => 'pending',
            'bank_name' => $this->bankName ?: null,
            'bank_account_number' => $this->bankAccountNumber ?: null,
            'bank_account_holder' => $this->bankAccountHolder ?: null,
            'notes' => $this->notes ?: null,
            'requested_by' => Auth::id(),
        ]);

        $this->successMessage = 'Pencairan dibuat (status: Menunggu). Tandai "Selesai" setelah transfer berhasil agar saldo terpotong.';
        $this->closeCreate();
    }

    /**
     * Transition a disbursement. Moving to "completed" debits the owner wallet once.
     */
    public function setStatus(int $disbursementId, string $status)
    {
        abort_unless(Auth::user()?->isSuperAdmin(), 403);

        if (! in_array($status, ['processing', 'completed', 'rejected'], true)) {
            return;
        }

        $disbursement = Disbursement::findOrFail($disbursementId);

        if ($disbursement->status === 'completed') {
            $this->errorMessage = 'Pencairan sudah selesai dan tidak dapat diubah.';
            return;
        }

        if ($status === 'completed') {
            $wallet = WalletService::forOwner($disbursement->owner_id);
            if ((float) $disbursement->amount > (float) $wallet->balance) {
                $this->errorMessage = 'Saldo owner tidak cukup untuk menyelesaikan pencairan ini.';
                return;
            }

            WalletService::debitForDisbursement($disbursement);
            $disbursement->update([
                'status' => 'completed',
                'processed_by' => Auth::id(),
                'processed_at' => now(),
            ]);
            $this->successMessage = 'Pencairan ditandai selesai dan saldo owner telah dipotong.';
        } else {
            $disbursement->update([
                'status' => $status,
                'processed_by' => Auth::id(),
                'processed_at' => $status === 'rejected' ? now() : null,
            ]);
            $this->successMessage = 'Status pencairan diperbarui.';
        }

        $this->errorMessage = '';
    }

    public function render()
    {
        $owners = User::role('owner')->with('wallet')->orderBy('name')->get()->map(function ($o) {
            $wallet = $o->wallet ?: WalletService::forOwner($o->id);
            return ['id' => $o->id, 'name' => $o->name, 'balance' => (float) $wallet->balance];
        });

        $disbursements = Disbursement::with('owner')->latest()->paginate(15);

        return view('livewire.admin.disbursement-index', [
            'owners' => $owners,
            'disbursements' => $disbursements,
        ]);
    }
}
