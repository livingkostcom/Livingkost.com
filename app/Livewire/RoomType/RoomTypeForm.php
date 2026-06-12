<?php

namespace App\Livewire\RoomType;

use App\Models\RoomType;
use App\Models\Property;
use Livewire\Component;

class RoomTypeForm extends Component
{
    public ?int $roomTypeId = null;

    #[\Livewire\Attributes\Validate('required|integer|exists:properties,id')]
    public int $property_id = 0;

    #[\Livewire\Attributes\Validate('required|string|max:100')]
    public string $name = '';

    #[\Livewire\Attributes\Validate('required|numeric|min:0')]
    public string $price = '';

    #[\Livewire\Attributes\Validate('nullable|string')]
    public string $facilities = '';

    public function mount(?int $roomTypeId = null)
    {
        $this->roomTypeId = $roomTypeId;

        if ($roomTypeId) {
            $roomType = RoomType::findOrFail($roomTypeId);
            $this->authorize('update', $roomType);
            
            $this->property_id = $roomType->property_id;
            $this->name = $roomType->name;
            $this->price = (string) $roomType->price;
            $this->facilities = $roomType->facilities ? implode(', ', $roomType->facilities) : '';
        }
    }

    public function save()
    {
        $this->validate();

        // Parse facilities from comma-separated string to array
        $facilitiesArray = array_filter(
            array_map('trim', explode(',', $this->facilities))
        );

        if ($this->roomTypeId) {
            $roomType = RoomType::findOrFail($this->roomTypeId);
            $this->authorize('update', $roomType);
            $roomType->update([
                'property_id' => $this->property_id,
                'name' => $this->name,
                'price' => $this->price,
                'facilities' => $facilitiesArray ?: null,
            ]);
            $message = 'Tipe ruangan berhasil diperbarui!';
        } else {
            $this->authorize('create', RoomType::class);
            RoomType::create([
                'property_id' => $this->property_id,
                'name' => $this->name,
                'price' => $this->price,
                'facilities' => $facilitiesArray ?: null,
            ]);
            $message = 'Tipe ruangan berhasil dibuat!';
        }

        $this->dispatch('room-type-saved', message: $message);
    }

    public function render()
    {
        $properties = Property::where('status', 'active')->get();
        
        return view('livewire.room-type.room-type-form', [
            'properties' => $properties,
        ]);
    }
}
