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

    #[\Livewire\Attributes\Validate('nullable|string|in:putra,putri,putra_putri')]
    public string $gender_type = '';

    #[\Livewire\Attributes\Validate('nullable|string|max:1000')]
    public string $common_facilities_text = '';

    // --- Landing "Rekomendasi Kost" fields ---
    public bool $is_featured = false;

    #[\Livewire\Attributes\Validate('nullable|image|max:2048')]
    public $featured_image = null;

    public ?string $existing_featured_image = null;

    #[\Livewire\Attributes\Validate('nullable|string|max:100')]
    public string $location_label = '';

    #[\Livewire\Attributes\Validate('nullable|string|max:50')]
    public string $badge_text = '';

    /** @var array<int,\Livewire\Features\SupportFileUploads\TemporaryUploadedFile> */
    public array $gallery_uploads = [];

    /** @var array<int,string> already-saved gallery paths */
    public array $existing_gallery = [];

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
            $this->existing_gallery = is_array($property->gallery) ? $property->gallery : [];
            $this->gender_type = $property->gender_type ?? '';
            $facs = is_array($property->common_facilities) ? $property->common_facilities : [];
            $this->common_facilities_text = implode(', ', $facs);
        }
    }

    public function removeExistingImage(int $index): void
    {
        if (isset($this->existing_gallery[$index])) {
            unset($this->existing_gallery[$index]);
            $this->existing_gallery = array_values($this->existing_gallery);
        }
    }

    public function removeUpload(int $index): void
    {
        if (isset($this->gallery_uploads[$index])) {
            unset($this->gallery_uploads[$index]);
            $this->gallery_uploads = array_values($this->gallery_uploads);
        }
    }

    public function save()
    {
        $this->validate();

        if (!empty($this->gallery_uploads)) {
            $this->validate(['gallery_uploads.*' => 'image|max:2048']);
        }

        $commonFacs = array_values(array_filter(
            array_map('trim', explode(',', $this->common_facilities_text))
        ));

        $data = [
            'name' => $this->name,
            'address' => $this->address,
            'description' => $this->description,
            'status' => $this->status,
            'gender_type' => $this->gender_type ?: null,
            'common_facilities' => !empty($commonFacs) ? $commonFacs : null,
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

        // Gallery: keep remaining existing images + append newly uploaded ones
        $gallery = $this->existing_gallery;
        foreach ($this->gallery_uploads as $upload) {
            if ($upload instanceof \Livewire\Features\SupportFileUploads\TemporaryUploadedFile) {
                $gname = 'kost-' . uniqid() . '.' . $upload->getClientOriginalExtension();
                $upload->storeAs('gallery', $gname, 'landing');
                $gallery[] = 'gallery/' . $gname;
            }
        }
        $data['gallery'] = !empty($gallery) ? array_values($gallery) : null;

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
