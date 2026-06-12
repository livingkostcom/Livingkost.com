<?php

namespace App\Livewire\Property;

use App\Models\Property;
use Livewire\Component;

class PropertyForm extends Component
{
    public ?int $propertyId = null;

    #[\Livewire\Attributes\Validate('required|string|max:255')]
    public string $name = '';

    #[\Livewire\Attributes\Validate('required|string|max:500')]
    public string $address = '';

    #[\Livewire\Attributes\Validate('nullable|string|max:1000')]
    public string $description = '';

    #[\Livewire\Attributes\Validate('required|string|in:active,inactive')]
    public string $status = 'active';

    public function mount(?int $propertyId = null)
    {
        $this->propertyId = $propertyId;

        if ($propertyId) {
            $property = Property::findOrFail($propertyId);
            $this->authorize('update', $property);
            $this->name = $property->name;
            $this->address = $property->address;
            $this->description = $property->description ?? '';
            $this->status = $property->status ?? 'active';
        }
    }

    public function save()
    {
        $this->validate();

        if ($this->propertyId) {
            $property = Property::findOrFail($this->propertyId);
            $this->authorize('update', $property);
            $property->update([
                'name' => $this->name,
                'address' => $this->address,
                'description' => $this->description,
                'status' => $this->status,
            ]);
            session()->flash('message', 'Property berhasil diperbarui!');
        } else {
            $this->authorize('create', Property::class);
            Property::create([
                'name' => $this->name,
                'address' => $this->address,
                'description' => $this->description,
                'status' => $this->status,
            ]);
            session()->flash('message', 'Property berhasil dibuat!');
        }

        $this->dispatch('property-saved');
    }

    public function render()
    {
        return view('livewire.property.property-form');
    }
}
