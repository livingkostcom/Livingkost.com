<?php

namespace App\Livewire\Room;

use App\Models\Room;
use App\Models\RoomType;
use App\Models\Property;
use Illuminate\Validation\Rule;
use Illuminate\Database\QueryException;
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

        // Room number must be unique within its room type (matches the DB unique
        // constraint on rooms[room_type_id, room_number]). Validating here turns
        // a would-be 500 (QueryException) into a friendly field message.
        $this->validate([
            'room_number' => [
                Rule::unique('rooms', 'room_number')
                    ->where(fn ($q) => $q->where('room_type_id', $this->room_type_id))
                    ->ignore($this->roomId),
            ],
        ], [
            'room_number.unique' => 'Nomor ruangan "' . $this->room_number . '" sudah ada di tipe kamar ini. Silakan pakai nomor lain.',
        ]);

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

        try {
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
        } catch (QueryException $e) {
            // Safety net for a race condition slipping past validation (e.g. two
            // submits at once): surface a field error instead of a 500.
            if ($e->getCode() === '23000') {
                $this->addError('room_number', 'Nomor ruangan "' . $this->room_number . '" sudah ada di tipe kamar ini. Silakan pakai nomor lain.');
                return;
            }
            throw $e;
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
