<?php

namespace App\Livewire\Lease;

use App\Models\Lease;
use Livewire\Component;
use Livewire\WithPagination;

class LeaseIndex extends Component
{
    use WithPagination;

    #[\Livewire\Attributes\Validate('nullable|string|max:255')]
    public string $search = '';

    #[\Livewire\Attributes\Validate('nullable|string')]
    public ?string $filterStatus = null;

    public bool $showModal = false;
    public ?int $editingId = null;
    public bool $showDeleteModal = false;
    public ?Lease $deletingLease = null;
    public bool $showDetailModal = false;
    public ?Lease $detailingLease = null;
    public string $successMessage = '';
    public string $errorMessage = '';

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedFilterStatus()
    {
        $this->resetPage();
    }

    public function openCreateModal()
    {
        $this->editingId = null;
        $this->showModal = true;
    }

    public function openEditModal(Lease $lease)
    {
        try {
            $this->authorize('update', $lease);
            $this->editingId = $lease->id;
            $this->showModal = true;
            $this->errorMessage = '';
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            $this->errorMessage = 'Anda tidak memiliki izin untuk mengubah kontrak ini.';
        }
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->editingId = null;
    }

    public function openDetailModal(Lease $lease)
    {
        try {
            $this->authorize('view', $lease);
            $this->detailingLease = $lease->load('tenant', 'room.roomType.property', 'creator', 'updater');
            $this->showDetailModal = true;
            $this->errorMessage = '';
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            $this->errorMessage = 'Anda tidak memiliki izin untuk melihat kontrak ini.';
        }
    }

    public function closeDetailModal()
    {
        $this->showDetailModal = false;
        $this->detailingLease = null;
    }

    #[\Livewire\Attributes\On('lease-saved')]
    public function onLeaseSaved($message = null)
    {
        if ($message) {
            $this->successMessage = $message;
        }
        $this->closeModal();
        $this->resetPage();
    }

    #[\Livewire\Attributes\On('close-modal')]
    public function onCloseModal()
    {
        $this->closeModal();
    }

    #[\Livewire\Attributes\On('show-error')]
    public function onShowError($message = null)
    {
        if ($message) {
            $this->errorMessage = $message;
        }
    }

    public function openDeleteModal(Lease $lease)
    {
        try {
            $this->authorize('delete', $lease);
            $this->deletingLease = $lease;
            $this->showDeleteModal = true;
            $this->errorMessage = '';
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            $this->errorMessage = 'Anda tidak memiliki izin untuk menghapus kontrak ini.';
        }
    }

    public function confirmDelete()
    {
        if ($this->deletingLease) {
            try {
                $this->authorize('delete', $this->deletingLease);
                $this->deletingLease->delete();
                $this->successMessage = 'Kontrak berhasil dihapus!';
                $this->closeDeleteModal();
                $this->resetPage();
            } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
                $this->errorMessage = 'Anda tidak memiliki izin untuk menghapus kontrak ini.';
            }
        }
    }

    public function closeDeleteModal()
    {
        $this->showDeleteModal = false;
        $this->deletingLease = null;
    }

    public function render()
    {
        $query = Lease::with('tenant', 'room.roomType.property');

        if ($this->search) {
            $query->where(function ($q) {
                $q->whereHas('tenant', function ($subq) {
                    $subq->where('name', 'like', "%{$this->search}%")
                         ->orWhere('nik', 'like', "%{$this->search}%");
                })
                ->orWhereHas('room', function ($subq) {
                    $subq->where('room_number', 'like', "%{$this->search}%");
                });
            });
        }

        if ($this->filterStatus) {
            $query->where('status', $this->filterStatus);
        }

        $leases = $query->paginate(10);

        return view('livewire.lease.lease-index', [
            'leases' => $leases,
        ]);
    }
}
