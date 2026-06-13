<?php

namespace App\Livewire\Admin;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class UserIndex extends Component
{
    use WithPagination;

    #[\Livewire\Attributes\Validate('nullable|string|max:255')]
    public string $search = '';

    public bool $showModal = false;
    public ?int $editingId = null;

    public bool $showDeleteModal = false;
    public ?int $deletingUserId = null;
    public ?User $deletingUser = null;

    public function mount()
    {
        $this->authorize('viewAny', User::class);
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function openCreateModal()
    {
        $this->authorize('create', User::class);
        $this->editingId = null;
        $this->showModal = true;
    }

    public function openEditModal(User $user)
    {
        $this->authorize('update', $user);
        $this->editingId = $user->id;
        $this->showModal = true;
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->editingId = null;
    }

    #[\Livewire\Attributes\On('user-saved')]
    public function onUserSaved()
    {
        $this->closeModal();
        $this->resetPage();
    }

    public function openDeleteModal(User $user)
    {
        $this->authorize('delete', $user);
        $this->deletingUser = $user;
        $this->deletingUserId = $user->id;
        $this->showDeleteModal = true;
    }

    public function closeDeleteModal()
    {
        $this->showDeleteModal = false;
        $this->deletingUserId = null;
        $this->deletingUser = null;
    }

    public function confirmDelete()
    {
        $user = User::findOrFail($this->deletingUserId);
        $this->authorize('delete', $user);

        $user->syncRoles([]);
        $user->delete();

        $this->showDeleteModal = false;
        $this->deletingUserId = null;
        $this->deletingUser = null;
        $this->resetPage();

        session()->flash('message', 'User berhasil dihapus!');
    }

    public function render()
    {
        $actor = Auth::user();

        $query = User::query()->with('roles', 'tenant');

        if (! $actor->isSuperAdmin()) {
            // Owners only see their own managers & tenants
            $query->where('owner_id', $actor->id);
        }

        if ($this->search !== '') {
            $query->where(function ($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                    ->orWhere('email', 'like', '%' . $this->search . '%');
            });
        }

        $users = $query->orderBy('name')->paginate(10);

        return view('livewire.admin.user-index', [
            'users' => $users,
            'isSuperAdmin' => $actor->isSuperAdmin(),
        ]);
    }
}
