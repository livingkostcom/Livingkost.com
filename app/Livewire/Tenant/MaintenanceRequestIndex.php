<?php

namespace App\Livewire\Tenant;

use App\Models\MaintenanceRequest;
use App\Models\User;
use App\Notifications\NewMaintenanceRequestNotification;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class MaintenanceRequestIndex extends Component
{
    use WithPagination;

    public string $statusFilter = '';

    // Create modal
    public bool $showCreateModal = false;
    public string $title = '';
    public string $description = '';
    public string $category = 'other';
    public string $priority = 'medium';

    // Detail modal
    public bool $showDetailModal = false;
    public ?MaintenanceRequest $selectedRequest = null;

    public function openCreate()
    {
        $this->reset(['title', 'description', 'category', 'priority']);
        $this->showCreateModal = true;
    }

    public function createRequest()
    {
        $this->validate([
            'title' => 'required|min:5|max:255',
            'description' => 'required|min:10',
            'category' => 'required|in:electrical,plumbing,furniture,cleaning,other',
            'priority' => 'required|in:low,medium,high',
        ]);

        $tenant = Auth::user()->tenant;
        $activeLease = $tenant->leases()->where('status', 'active')->first();

        if (!$activeLease) {
            session()->flash('error', 'Anda tidak memiliki kontrak sewa aktif');
            return;
        }

        $maintenanceRequest = MaintenanceRequest::create([
            'tenant_id' => $tenant->id,
            'room_id' => $activeLease->room_id,
            'title' => $this->title,
            'description' => $this->description,
            'category' => $this->category,
            'priority' => $this->priority,
        ]);

        // Notify owner & manager
        $maintenanceRequest->load(['tenant.user', 'room']);
        $admins = User::role(['owner', 'manager'])->get();
        foreach ($admins as $admin) {
            $admin->notify(new NewMaintenanceRequestNotification($maintenanceRequest));
        }

        $this->showCreateModal = false;
        $this->reset(['title', 'description', 'category', 'priority']);
        session()->flash('message', 'Permintaan perbaikan berhasil dikirim');
    }

    public function openDetail(int $id)
    {
        $this->selectedRequest = MaintenanceRequest::with(['room.roomType.property', 'resolver'])->find($id);
        $this->showDetailModal = true;
    }

    public function render()
    {
        $tenant = Auth::user()->tenant;

        $query = MaintenanceRequest::where('tenant_id', $tenant->id)
            ->with(['room'])
            ->latest();

        if ($this->statusFilter) {
            $query->where('status', $this->statusFilter);
        }

        return view('livewire.tenant.maintenance-request-index', [
            'requests' => $query->paginate(10),
        ]);
    }
}
