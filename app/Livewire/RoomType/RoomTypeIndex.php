<?php

namespace App\Livewire\RoomType;

use App\Models\RoomType;
use App\Models\Property;
use Livewire\Component;
use Livewire\WithPagination;

class RoomTypeIndex extends Component
{
    use WithPagination;

    #[\Livewire\Attributes\Validate('nullable|string|max:255')]
    public string $search = '';

    #[\Livewire\Attributes\Validate('nullable|integer')]
    public ?int $filterProperty = null;

    public bool $showModal = false;
    public ?int $editingId = null;
    public bool $showDeleteModal = false;
    public ?int $deletingRoomTypeId = null;
    public ?RoomType $deletingRoomType = null;
    public string $successMessage = '';
    public string $errorMessage = '';

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedFilterProperty()
    {
        $this->resetPage();
    }

    public function openCreateModal()
    {
        $this->editingId = null;
        $this->showModal = true;
    }

    public function openEditModal(RoomType $roomType)
    {
        try {
            $this->authorize('update', $roomType);
            $this->editingId = $roomType->id;
            $this->showModal = true;
            $this->errorMessage = '';
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            $this->errorMessage = 'Anda tidak memiliki izin untuk mengubah tipe ruangan ini.';
        }
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->editingId = null;
    }

    #[\Livewire\Attributes\On('room-type-saved')]
    public function onRoomTypeSaved($message = null)
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

    public function openDeleteModal(RoomType $roomType)
    {
        try {
            $this->authorize('delete', $roomType);
            $this->deletingRoomType = $roomType;
            $this->deletingRoomTypeId = $roomType->id;
            $this->showDeleteModal = true;
            $this->errorMessage = '';
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            $this->errorMessage = 'Anda tidak memiliki izin untuk menghapus tipe ruangan ini.';
        }
    }

    public function confirmDelete()
    {
        if ($this->deletingRoomType) {
            try {
                $this->authorize('delete', $this->deletingRoomType);
                
                // Check if room type has rooms
                if ($this->deletingRoomType->rooms()->count() > 0) {
                    $this->errorMessage = 'Tipe ruangan ini memiliki ruangan. Hapus ruangan terlebih dahulu.';
                    return;
                }
                
                $this->deletingRoomType->delete();
                $this->successMessage = 'Tipe ruangan berhasil dihapus!';
                $this->closeDeleteModal();
                $this->resetPage();
            } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
                $this->errorMessage = 'Anda tidak memiliki izin untuk menghapus tipe ruangan ini.';
            }
        }
    }

    public function closeDeleteModal()
    {
        $this->showDeleteModal = false;
        $this->deletingRoomType = null;
        $this->deletingRoomTypeId = null;
    }

    public function render()
    {
        $query = RoomType::with('property');

        if ($this->search) {
            $query->where('name', 'like', "%{$this->search}%");
        }

        if ($this->filterProperty) {
            $query->where('property_id', $this->filterProperty);
        }

        $roomTypes = $query->paginate(10);
        $properties = Property::where('status', 'active')->get();

        return view('livewire.room-type.room-type-index', [
            'roomTypes' => $roomTypes,
            'properties' => $properties,
        ]);
    }
}
