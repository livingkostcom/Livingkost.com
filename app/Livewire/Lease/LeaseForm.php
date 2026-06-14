<?php

namespace App\Livewire\Lease;

use App\Models\Lease;
use App\Models\Tenant;
use App\Models\Room;
use App\Models\Property;
use Livewire\Component;

class LeaseForm extends Component
{
    public ?int $leaseId = null;

    public int $property_id = 0;

    #[\Livewire\Attributes\Validate('required|integer|exists:tenants,id')]
    public int $tenant_id = 0;

    #[\Livewire\Attributes\Validate('required|integer|exists:rooms,id')]
    public int $room_id = 0;

    #[\Livewire\Attributes\Validate('required|date')]
    public string $start_date = '';

    #[\Livewire\Attributes\Validate('required|date|after:start_date')]
    public string $end_date = '';

    #[\Livewire\Attributes\Validate('required|integer|min:1|max:31')]
    public int $due_date_per_month = 1;

    #[\Livewire\Attributes\Validate('required|numeric|min:0')]
    public string $deposit_amount = '0';

    #[\Livewire\Attributes\Validate('required|string|in:pending,active,completed,terminated,cancelled')]
    public string $status = 'pending';

    public function mount(?int $leaseId = null)
    {
        $this->leaseId = $leaseId;

        if ($leaseId) {
            $lease = Lease::findOrFail($leaseId);
            $this->authorize('update', $lease);
            
            $this->property_id = $lease->room->roomType->property_id;
            $this->tenant_id = $lease->tenant_id;
            $this->room_id = $lease->room_id;
            $this->start_date = $lease->start_date->format('Y-m-d');
            $this->end_date = $lease->end_date->format('Y-m-d');
            $this->due_date_per_month = $lease->due_date_per_month;
            $this->deposit_amount = (string) $lease->deposit_amount;
            $this->status = $lease->status;
        }
    }

    #[\Livewire\Attributes\On('property_id')]
    public function updatedPropertyId()
    {
        $this->room_id = 0;
    }

    public function save()
    {
        $validationRules = [
            'tenant_id' => 'required|integer|exists:tenants,id',
            'room_id' => 'required|integer|exists:rooms,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'due_date_per_month' => 'required|integer|min:1|max:31',
            'deposit_amount' => 'required|numeric|min:0',
            'status' => 'required|string|in:pending,active,completed,terminated,cancelled',
        ];

        $this->validate($validationRules);

        // Enforce one ACTIVE lease per tenant (DB can't do partial unique on MySQL).
        if ($this->status === 'active') {
            $conflict = Lease::where('tenant_id', $this->tenant_id)
                ->where('status', 'active')
                ->when($this->leaseId, fn ($q) => $q->where('id', '!=', $this->leaseId))
                ->exists();

            if ($conflict) {
                $this->addError('tenant_id', 'Penyewa ini sudah memiliki kontrak aktif. Selesaikan/batalkan kontrak lama dulu.');
                return;
            }
        }

        $data = [
            'tenant_id' => $this->tenant_id,
            'room_id' => $this->room_id,
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
            'due_date_per_month' => $this->due_date_per_month,
            'deposit_amount' => (float) $this->deposit_amount,
            'status' => $this->status,
        ];

        if ($this->leaseId) {
            $lease = Lease::findOrFail($this->leaseId);
            $this->authorize('update', $lease);
            // created_by/updated_by are user IDs (resolved via the creator/updater
            // relationships) — LeaseObserver sets them from the auth user.
            $lease->update($data);
            $message = 'Kontrak berhasil diperbarui!';
        } else {
            $this->authorize('create', Lease::class);
            Lease::create($data);
            $message = 'Kontrak berhasil dibuat!';
        }

        $this->dispatch('lease-saved', message: $message);
    }

    public function render()
    {
        $properties = Property::orderBy('name')->get();
        
        // Selectable tenants: those without a pending/active lease — PLUS the lease's
        // current tenant when editing (it already has this lease, so it would
        // otherwise be filtered out and the dropdown would show nothing selected).
        $tenants = Tenant::where(function ($query) {
                $query->where(function ($q) {
                        $q->where('status', 'active')
                          ->whereDoesntHave('leases', function ($qq) {
                              $qq->whereIn('status', ['pending', 'active']);
                          });
                    });
                if ($this->tenant_id) {
                    $query->orWhere('id', $this->tenant_id);
                }
            })
            ->orderBy('name')
            ->get();

        $rooms = collect();
        if ($this->property_id) {
            $rooms = Room::with('roomType')
                ->whereHas('roomType', function ($query) {
                    $query->where('property_id', $this->property_id);
                })
                // Available rooms — PLUS the lease's current (occupied) room when editing.
                ->where(function ($query) {
                    $query->where('status', 'available');
                    if ($this->room_id) {
                        $query->orWhere('id', $this->room_id);
                    }
                })
                ->orderBy('room_number')
                ->get();
        }
        
        return view('livewire.lease.lease-form', [
            'properties' => $properties,
            'tenants' => $tenants,
            'rooms' => $rooms,
        ]);
    }
}
