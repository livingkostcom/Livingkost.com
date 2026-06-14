<?php

namespace App\Livewire\RoomType;

use App\Models\RoomType;
use App\Models\Property;
use Livewire\Component;
use Livewire\WithFileUploads;

class RoomTypeForm extends Component
{
    use WithFileUploads;

    public ?int $roomTypeId = null;

    #[\Livewire\Attributes\Validate('required|integer|exists:properties,id')]
    public int $property_id = 0;

    #[\Livewire\Attributes\Validate('required|string|max:100')]
    public string $name = '';

    #[\Livewire\Attributes\Validate('required|numeric|min:0')]
    public string $price = '';

    #[\Livewire\Attributes\Validate('nullable|string')]
    public string $facilities = '';

    #[\Livewire\Attributes\Validate('nullable')]
    public array $image_uploads = [];

    public array $existing_images = [];

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
            $this->existing_images = is_array($roomType->images) ? $roomType->images : [];
        }
    }

    public function removeExistingImage(int $index): void
    {
        if (isset($this->existing_images[$index])) {
            unset($this->existing_images[$index]);
            $this->existing_images = array_values($this->existing_images);
        }
    }

    // Renamed from removeUpload() to avoid colliding with the reserved
    // WithFileUploads magic action `_removeUpload` (caused a 500 in the browser).
    public function removeNewUpload(int $index): void
    {
        if (isset($this->image_uploads[$index])) {
            unset($this->image_uploads[$index]);
            $this->image_uploads = array_values($this->image_uploads);
        }
    }

    public function save()
    {
        $this->validate();

        if (!empty($this->image_uploads)) {
            $this->validate(['image_uploads.*' => 'image|max:2048']);
        }

        $facilitiesArray = array_values(array_filter(
            array_map('trim', explode(',', $this->facilities))
        ));

        $images = $this->existing_images;
        foreach ($this->image_uploads as $upload) {
            if ($upload instanceof \Livewire\Features\SupportFileUploads\TemporaryUploadedFile) {
                $fname = 'room-types/' . uniqid('rt-') . '.' . $upload->getClientOriginalExtension();
                $upload->storeAs('room-types', basename($fname), 'landing');
                $images[] = $fname;
            }
        }

        $data = [
            'property_id' => $this->property_id,
            'name' => $this->name,
            'price' => $this->price,
            'facilities' => $facilitiesArray ?: null,
            'images' => !empty($images) ? array_values($images) : null,
        ];

        // Inherit the owner from the parent property. Critical for super-admins:
        // they aren't auto-stamped by BelongsToOwner, so without this the room
        // type would have a null owner and be invisible under the tenant scope.
        $parent = Property::find($this->property_id);
        if ($parent) {
            $data['owner_id'] = $parent->owner_id;
        }

        if ($this->roomTypeId) {
            $roomType = RoomType::findOrFail($this->roomTypeId);
            $this->authorize('update', $roomType);
            $roomType->update($data);
            $message = 'Tipe ruangan berhasil diperbarui!';
        } else {
            $this->authorize('create', RoomType::class);
            RoomType::create($data);
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
