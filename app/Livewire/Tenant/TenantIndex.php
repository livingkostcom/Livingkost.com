<?php

namespace App\Livewire\Tenant;

use App\Models\Tenant;
use Livewire\Component;
use Livewire\WithPagination;

class TenantIndex extends Component
{
    use WithPagination;

    #[\Livewire\Attributes\Validate('nullable|string|max:255')]
    public string $search = '';

    #[\Livewire\Attributes\Validate('nullable|string')]
    public ?string $filterStatus = null;

    public bool $showModal = false;
    public ?int $editingId = null;
    public bool $showDeleteModal = false;
    public ?int $deletingTenantId = null;
    public ?Tenant $deletingTenant = null;
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

    public function openEditModal(Tenant $tenant)
    {
        try {
            $this->authorize('update', $tenant);
            $this->editingId = $tenant->id;
            $this->showModal = true;
            $this->errorMessage = '';
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            $this->errorMessage = 'Anda tidak memiliki izin untuk mengubah penyewa ini.';
        }
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->editingId = null;
    }

    #[\Livewire\Attributes\On('tenant-saved')]
    public function onTenantSaved($message = null)
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

    public function openDeleteModal(Tenant $tenant)
    {
        try {
            $this->authorize('delete', $tenant);
            $this->deletingTenant = $tenant;
            $this->deletingTenantId = $tenant->id;
            $this->showDeleteModal = true;
            $this->errorMessage = '';
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            $this->errorMessage = 'Anda tidak memiliki izin untuk menghapus penyewa ini.';
        }
    }

    public function confirmDelete()
    {
        if ($this->deletingTenant) {
            try {
                $this->authorize('delete', $this->deletingTenant);
                
                // Check if tenant has active leases
                if ($this->deletingTenant->leases()->where('status', 'active')->count() > 0) {
                    $this->errorMessage = 'Penyewa ini memiliki kontrak aktif. Akhiri kontrak terlebih dahulu.';
                    return;
                }
                
                $this->deletingTenant->delete();
                $this->successMessage = 'Penyewa berhasil dihapus!';
                $this->closeDeleteModal();
                $this->resetPage();
            } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
                $this->errorMessage = 'Anda tidak memiliki izin untuk menghapus penyewa ini.';
            }
        }
    }

    public function closeDeleteModal()
    {
        $this->showDeleteModal = false;
        $this->deletingTenant = null;
        $this->deletingTenantId = null;
    }

    public function render()
    {
        $query = Tenant::query();

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('nik', 'like', "%{$this->search}%")
                  ->orWhere('name', 'like', "%{$this->search}%")
                  ->orWhere('email', 'like', "%{$this->search}%")
                  ->orWhere('phone', 'like', "%{$this->search}%");
            });
        }

        if ($this->filterStatus) {
            $query->where('status', $this->filterStatus);
        }

        $tenants = $query->paginate(10);

        return view('livewire.tenant.tenant-index', [
            'tenants' => $tenants,
        ]);
    }
}
