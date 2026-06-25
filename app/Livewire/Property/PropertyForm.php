<?php

namespace App\Livewire\Property;

use App\Models\Property;
use App\Models\RoomType;
use App\Models\Room;
use App\Models\User;
use Livewire\Component;
use Livewire\WithFileUploads;

class PropertyForm extends Component
{
    use WithFileUploads;

    public ?int $propertyId = null;

    /** Owner this property belongs to. Only super-admins choose this; for owners
     *  it is stamped automatically by the BelongsToOwner trait. */
    public ?int $owner_id = null;

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

    #[\Livewire\Attributes\Validate('nullable|mimes:jpeg,jpg,png,gif,webp,avif|max:2048')]
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
            $this->owner_id = $property->owner_id;
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

    /**
     * Reorder the saved gallery images. $order is the new sequence of the
     * current existing_gallery indexes, produced by the drag-and-drop UI.
     */
    public function reorderGallery(array $order): void
    {
        $reordered = [];
        foreach ($order as $idx) {
            $idx = (int) $idx;
            if (isset($this->existing_gallery[$idx])) {
                $reordered[] = $this->existing_gallery[$idx];
            }
        }
        // Safety: keep any image not represented in $order (no data loss).
        foreach ($this->existing_gallery as $idx => $img) {
            if (! in_array($idx, array_map('intval', $order), true)) {
                $reordered[] = $img;
            }
        }
        $this->existing_gallery = $reordered;
    }

    /**
     * Remove a newly-selected (not yet saved) upload.
     *
     * NOTE: must NOT be named removeUpload() — that collides with the reserved
     * WithFileUploads magic action `_removeUpload($name, $tmpFilename)`, which
     * Livewire's JS routes to instead, causing "Property [$0] not found" (500).
     */
    public function removeNewUpload(int $index): void
    {
        if (isset($this->gallery_uploads[$index])) {
            unset($this->gallery_uploads[$index]);
            $this->gallery_uploads = array_values($this->gallery_uploads);
        }
    }

    /**
     * Reorder the newly-selected uploads (drag-and-drop on unsaved photos).
     * $order is the new sequence of the current gallery_uploads indexes.
     */
    public function reorderUploads(array $order): void
    {
        $reordered = [];
        foreach ($order as $idx) {
            $idx = (int) $idx;
            if (isset($this->gallery_uploads[$idx])) {
                $reordered[] = $this->gallery_uploads[$idx];
            }
        }
        foreach ($this->gallery_uploads as $idx => $up) {
            if (! in_array($idx, array_map('intval', $order), true)) {
                $reordered[] = $up;
            }
        }
        $this->gallery_uploads = $reordered;
    }

    public function save()
    {
        $this->validate();

        // Super-admins must pick which owner this property belongs to.
        $isSuperAdmin = auth()->user()->isSuperAdmin();
        if ($isSuperAdmin) {
            $this->validate([
                'owner_id' => ['required', 'integer', \Illuminate\Validation\Rule::exists('users', 'id')],
            ], [], ['owner_id' => 'pemilik (owner)']);
        }

        if (!empty($this->gallery_uploads)) {
            $this->validate(['gallery_uploads.*' => 'mimes:jpeg,jpg,png,gif,webp,avif|max:2048']);
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

        // A super-admin explicitly assigns the owner; the BelongsToOwner trait
        // preserves a pre-set owner_id and only auto-stamps for non-super-admins.
        if ($isSuperAdmin) {
            $data['owner_id'] = $this->owner_id;
        }

        if ($this->propertyId) {
            $property = Property::findOrFail($this->propertyId);
            $this->authorize('update', $property);
            $previousOwnerId = $property->owner_id;
            $property->update($data);

            // If a super-admin reassigned the property to a different owner,
            // cascade the new owner_id to its room types and rooms so they stay
            // visible to (and owned by) the new owner under the tenant scope.
            if ($isSuperAdmin && $this->owner_id != $previousOwnerId) {
                RoomType::where('property_id', $property->id)->update(['owner_id' => $this->owner_id]);
                Room::whereIn('room_type_id', RoomType::where('property_id', $property->id)->pluck('id'))
                    ->update(['owner_id' => $this->owner_id]);
            }

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
        // Owner picker is only relevant for super-admins creating/editing on
        // behalf of an owner.
        $owners = auth()->user()->isSuperAdmin()
            ? User::role('owner')->orderBy('name')->get(['id', 'name', 'email'])
            : collect();

        return view('livewire.property.property-form', [
            'owners' => $owners,
        ]);
    }
}
