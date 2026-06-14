<?php

namespace App\Livewire\Admin;

use App\Models\CompanyTransaction;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class CompanyLedgerIndex extends Component
{
    use WithPagination;

    /** 'income' or 'expense' */
    public string $type = 'expense';

    public string $successMessage = '';
    public string $errorMessage = '';

    // Form modal
    public bool $showModal = false;
    public ?int $editingId = null;
    public string $description = '';
    public $amount = '';
    public string $transactionDate = '';
    public string $notes = '';

    // Delete modal
    public bool $showDeleteModal = false;
    public ?int $deletingId = null;

    public function mount(string $type = 'expense')
    {
        abort_unless(Auth::user()?->isSuperAdmin(), 403);
        $this->type = $type === 'income' ? 'income' : 'expense';
    }

    public function openCreate()
    {
        $this->reset(['editingId', 'description', 'amount', 'notes']);
        $this->transactionDate = now()->format('Y-m-d');
        $this->errorMessage = '';
        $this->showModal = true;
    }

    public function openEdit(int $id)
    {
        $t = CompanyTransaction::where('type', $this->type)->findOrFail($id);
        $this->editingId = $t->id;
        $this->description = $t->description;
        $this->amount = (string) (int) $t->amount;
        $this->transactionDate = $t->transaction_date->format('Y-m-d');
        $this->notes = (string) $t->notes;
        $this->errorMessage = '';
        $this->showModal = true;
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->editingId = null;
    }

    public function save()
    {
        abort_unless(Auth::user()?->isSuperAdmin(), 403);

        $this->validate([
            'description' => 'required|string|max:255',
            'amount' => 'required|numeric|min:1',
            'transactionDate' => 'required|date',
            'notes' => 'nullable|string|max:1000',
        ], [], [
            'description' => 'keterangan',
            'amount' => 'jumlah',
            'transactionDate' => 'tanggal',
        ]);

        $data = [
            'type' => $this->type,
            'description' => $this->description,
            'amount' => (float) $this->amount,
            'transaction_date' => $this->transactionDate,
            'notes' => $this->notes ?: null,
        ];

        if ($this->editingId) {
            $t = CompanyTransaction::where('type', $this->type)->findOrFail($this->editingId);
            $t->update($data);
            $this->successMessage = 'Data berhasil diperbarui.';
        } else {
            $data['created_by'] = Auth::id();
            CompanyTransaction::create($data);
            $this->successMessage = 'Data berhasil ditambahkan.';
        }

        $this->closeModal();
        $this->resetPage();
    }

    public function confirmDelete(int $id)
    {
        $this->deletingId = $id;
        $this->showDeleteModal = true;
    }

    public function delete()
    {
        abort_unless(Auth::user()?->isSuperAdmin(), 403);

        if ($this->deletingId) {
            CompanyTransaction::where('type', $this->type)->where('id', $this->deletingId)->delete();
            $this->successMessage = 'Data berhasil dihapus.';
        }
        $this->showDeleteModal = false;
        $this->deletingId = null;
        $this->resetPage();
    }

    public function render()
    {
        $rows = CompanyTransaction::where('type', $this->type)
            ->orderByDesc('transaction_date')
            ->orderByDesc('id')
            ->paginate(15);

        $totalThisMonth = CompanyTransaction::where('type', $this->type)
            ->whereYear('transaction_date', now()->year)
            ->whereMonth('transaction_date', now()->month)
            ->sum('amount');

        return view('livewire.admin.company-ledger-index', [
            'rows' => $rows,
            'totalThisMonth' => $totalThisMonth,
        ]);
    }
}
