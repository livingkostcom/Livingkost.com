<?php

namespace App\Livewire\Property;

use App\Models\Property;
use Livewire\Component;
use Livewire\WithFileUploads;

class PropertyForm extends Component
{
    use WithFileUploads;

    public ?int $propertyId = null;

    #[\Livewire\Attributes\Validate('required|string|max:255')]
    public string $name = '';

    #[\Livewire\Attributes\Validate('required|string|max:500')]
    public string $address = '';

    #[\Livewire\Attributes\Validate('nullable|string|max:1000')]
    public string $description = '';

    #[\Livewire\Attributes\Validate('required|string|in:active,inactive')]
    public string $status = 'active';

    // --- Landing "Rekomendasi Kost" fields ---
    public bool $is_featured = false;

    #[\Livewire\Attributes\Validate('nullable|image|max:2048')]
    public $featured_image = null;

    public ?string $existing_featured_image = null;

    #[\Livewire\Attributes\Validate('nullable|string|max:100')]
    public string $location_label = '';

    #[\Livewire\Attributes\Validate('nullable|string|max:50')]
    public string $badge_text = '';

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
            $this->is_featured = (bool) $property->is_featured;
            $this->existing_featured_image = $property->featured_image;
            $this->location_label = $property->location_label ?? '';
            $this->badge_text = $property->badge_text ?? '';
        }
    }

    public function save()
    {
        $this->validate();

        $data = [
            'name' => $this->name,
            'address' => $this->address,
            'description' => $this->description,
            'status' => $this->status,
            'is_featured' => $this->is_featured,
            'location_label' => $this->location_label ?: null,
            'badge_text' => $this->badge_text ?: null,
        ];

        // Store newly uploaded featured image on the public "landing" disk (/images)
        if ($this->featured_image instanceof \Livewire\Features\SupportFileUploads\TemporaryUploadedFile) {
            $filename = 'kost-' . uniqid() . '.' . $this->featured_image->getClientOriginalExtension();
            $this->featured_image->storeAs('featured', $filename, 'landing');
            $data['featured_image'] = 'featured/' . $filename;
        }

        if ($this->propertyId) {
            $property = Property::findOrFail($this->propertyId);
            $this->authorize('update', $property);
            $property->update($data);
            session()->flash('message', 'Property berhasil diperbarui!');
        } else {
            $this->authorize('create', Property::class);
            Property::create($data);
            session()->flash('message', 'Property berhasil dibuat!');
        }

        $this->dispatch('property-saved');
    }

    public function render()
    {
        return view('livewire.property.property-form');
    }
}
