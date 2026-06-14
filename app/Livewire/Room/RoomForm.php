<?php

namespace App\Livewire\Room;

use App\Models\Room;
use App\Models\RoomType;
use App\Models\Property;
use Livewire\Component;

class RoomForm extends Component
{
    public ?int $roomId = null;

    #[\Livewire\Attributes\Validate('required|integer|exists:room_types,id')]
    public int $room_type_id = 0;

    #[\Livewire\Attributes\Validate('required|string|max:50')]
    public string $room_number = '';

    #[\Livewire\Attributes\Validate('required|integer|min:1')]
    public int $floor = 1;

    #[\Livewire\Attributes\Validate('required|string|in:available,maintenance')]
    public string $status = 'available';

    public ?int $selectedProperty = null;
    public array $roomTypes = [];

    public function mount(?int $roomId = null)
    {
        $this->roomId = $roomId;

        if ($roomId) {
            $room = Room::findOrFail($roomId);
            $this->authorize('update', $room);
            $this->room_type_id = $room->room_type_id;
            $this->room_number = $room->room_number;
            $this->floor = $room->floor;
            $this->status = $room->status;
            $this->selectedProperty = $room->roomType->property_id;
            $this->loadRoomTypes($room->roomType->property_id);
        }
    }

    public function updatedSelectedProperty($value)
    {
        if ($value) {
            $this->loadRoomTypes($value);
            $this->room_type_id = 0;
        }
    }

    private function loadRoomTypes($propertyId)
    {
        $this->roomTypes = RoomType::where('property_id', $propertyId)
            ->pluck('name', 'id')
            ->toArray();
    }

    public function save()
    {
        $this->validate();

        $data = [
            'room_type_id' => $this->room_type_id,
            'room_number' => $this->room_number,
            'floor' => $this->floor,
            'status' => $this->status,
        ];

        // Inherit the owner from the room type (which inherits it from the
        // property). Needed so super-admin-created rooms get a non-null owner.
        $roomType = RoomType::find($this->room_type_id);
        if ($roomType) {
            $data['owner_id'] = $roomType->owner_id;
        }

        if ($this->roomId) {
            $room = Room::findOrFail($this->roomId);
            $this->authorize('update', $room);
            $room->update($data);
            $message = 'Ruangan berhasil diperbarui!';
        } else {
            $this->authorize('create', Room::class);
            Room::create($data);
            $message = 'Ruangan berhasil dibuat!';
        }

        $this->dispatch('room-saved', message: $message);
    }

    public function render()
    {
        $properties = Property::where('status', 'active')->get();
        
        return view('livewire.room.room-form', [
            'properties' => $properties,
        ]);
    }
}
