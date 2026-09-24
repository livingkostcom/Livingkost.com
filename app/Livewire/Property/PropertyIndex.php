<?php

namespace App\Livewire\Property;

use App\Models\Property;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithPagination;

class PropertyIndex extends Component
{
    use WithPagination;

    #[\Livewire\Attributes\Validate('nullable|string|max:255')]
    public string $search = '';

    public bool $showModal = false;
    public ?int $editingId = null;
    public bool $showDeleteModal = false;
    public ?int $deletingPropertyId = null;
    public ?Property $deletingProperty = null;

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function openCreateModal()
    {
        $this->editingId = null;
        $this->showModal = true;
    }

    public function openEditModal(Property $property)
    {
        $this->editingId = $property->id;
        $this->showModal = true;
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->editingId = null;
    }

    #[\Livewire\Attributes\On('property-saved')]
    public function onPropertySaved()
    {
        $this->closeModal();
        $this->resetPage();
    }

    public function openDeleteModal(Property $property)
    {
        $this->authorize('delete', $property);
        $this->deletingProperty = $property;
        $this->deletingPropertyId = $property->id;
        $this->showDeleteModal = true;
    }

    public function confirmDelete()
    {
        if ($this->deletingProperty) {
            $this->authorize('delete', $this->deletingProperty);
            $this->deletingProperty->delete();
            session()->flash('message', 'Property berhasil dihapus!');
            $this->closeDeleteModal();
            $this->resetPage();
        }
    }

    public function closeDeleteModal()
    {
        $this->showDeleteModal = false;
        $this->deletingProperty = null;
        $this->deletingPropertyId = null;
    }

    public function deleteProperty(Property $property)
    {
        $this->authorize('delete', $property);
        $property->delete();
        session()->flash('message', 'Property berhasil dihapus!');
    }

    public function render()
    {
        $user = Auth::user();

        if ($user->isSuperAdmin()) {
            $query = Property::query()->with('owner', 'owners');
        } else {
            // Own (managed) properties + properties this owner co-owns (read-only).
            $ownerId = $user->ownerId();
            $coOwnedIds = DB::table('property_owners')->where('owner_id', $ownerId)->pluck('property_id');
            $query = Property::withoutGlobalScopes()->with('owner', 'owners')
                ->where(function ($q) use ($ownerId, $coOwnedIds) {
                    $q->where('owner_id', $ownerId)->orWhereIn('id', $coOwnedIds);
                });
        }

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('name', 'like', "%{$this->search}%")
                  ->orWhere('address', 'like', "%{$this->search}%");
            });
        }

        $properties = $query->paginate(10);

        return view('livewire.property.property-index', [
            'properties' => $properties,
        ]);
    }
}
