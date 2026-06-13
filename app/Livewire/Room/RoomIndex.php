<?php

namespace App\Livewire\Room;

use App\Models\Room;
use App\Models\Property;
use Livewire\Component;
use Livewire\WithPagination;

class RoomIndex extends Component
{
    use WithPagination;

    #[\Livewire\Attributes\Validate('nullable|string|max:255')]
    public string $search = '';

    #[\Livewire\Attributes\Validate('nullable|integer')]
    public ?int $filterProperty = null;

    #[\Livewire\Attributes\Validate('nullable|string')]
    public ?string $filterStatus = null;

    public bool $showModal = false;
    public ?int $editingId = null;
    public bool $showDeleteModal = false;
    public ?int $deletingRoomId = null;
    public ?Room $deletingRoom = null;
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

    public function updatedFilterStatus()
    {
        $this->resetPage();
    }

    public function openCreateModal()
    {
        $this->editingId = null;
        $this->showModal = true;
    }

    public function openEditModal(Room $room)
    {
        try {
            // Check if room is occupied (has active lease)
            if ($room->computed_status === 'occupied') {
                $this->errorMessage = 'Ruangan yang terisi tidak dapat diubah. Harus melalui penyelesaian kontrak.';
                return;
            }

            $this->authorize('update', $room);
            $this->editingId = $room->id;
            $this->showModal = true;
            $this->errorMessage = '';
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            $this->errorMessage = 'Anda tidak memiliki izin untuk mengubah ruangan ini.';
        }
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->editingId = null;
    }

    #[\Livewire\Attributes\On('room-saved')]
    public function onRoomSaved($message = null)
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

    public function openDeleteModal(Room $room)
    {
        try {
            // Check if room is occupied (has active lease)
            if ($room->computed_status === 'occupied') {
                $this->errorMessage = 'Ruangan yang terisi tidak dapat dihapus. Harus melalui penyelesaian kontrak.';
                return;
            }

            $this->authorize('delete', $room);
            $this->deletingRoom = $room;
            $this->deletingRoomId = $room->id;
            $this->showDeleteModal = true;
            $this->errorMessage = '';
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            $this->errorMessage = 'Anda tidak memiliki izin untuk menghapus ruangan ini.';
        }
    }

    public function confirmDelete()
    {
        if ($this->deletingRoom) {
            $this->authorize('delete', $this->deletingRoom);
            $this->deletingRoom->delete();
            $this->successMessage = 'Ruangan berhasil dihapus!';
            $this->closeDeleteModal();
            $this->resetPage();
        }
    }

    public function closeDeleteModal()
    {
        $this->showDeleteModal = false;
        $this->deletingRoom = null;
        $this->deletingRoomId = null;
    }

    public function render()
    {
        $query = Room::with('roomType.property');

        if ($this->search) {
            $query->where('room_number', 'like', "%{$this->search}%");
        }

        if ($this->filterProperty) {
            $query->whereHas('roomType', function ($q) {
                $q->where('property_id', $this->filterProperty);
            });
        }

        if ($this->filterStatus) {
            $query->where('status', $this->filterStatus);
        }

        $rooms = $query->paginate(10);
        $properties = Property::where('status', 'active')->get();

        return view('livewire.room.room-index', [
            'rooms' => $rooms,
            'properties' => $properties,
        ]);
    }
}
