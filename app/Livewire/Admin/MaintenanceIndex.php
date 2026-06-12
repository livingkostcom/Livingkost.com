<?php

namespace App\Livewire\Admin;

use App\Models\MaintenanceRequest;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class MaintenanceIndex extends Component
{
    use WithPagination;

    public string $search = '';
    public string $statusFilter = '';
    public string $categoryFilter = '';
    public string $priorityFilter = '';

    // Detail modal
    public bool $showDetailModal = false;
    public ?MaintenanceRequest $selectedRequest = null;

    // Process modal
    public bool $showProcessModal = false;
    public string $processStatus = '';
    public string $adminNotes = '';

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingStatusFilter()
    {
        $this->resetPage();
    }

    public function openDetail(int $id)
    {
        $this->selectedRequest = MaintenanceRequest::with(['tenant.user', 'room.roomType.property', 'resolver'])->find($id);
        $this->showDetailModal = true;
    }

    public function openProcess(int $id)
    {
        $this->selectedRequest = MaintenanceRequest::find($id);
        $this->processStatus = $this->selectedRequest->status;
        $this->adminNotes = $this->selectedRequest->admin_notes ?? '';
        $this->showProcessModal = true;
    }

    public function updateStatus()
    {
        $this->validate([
            'processStatus' => 'required|in:pending,in_progress,completed,rejected',
        ]);

        $this->selectedRequest->status = $this->processStatus;
        $this->selectedRequest->admin_notes = $this->adminNotes;

        if (in_array($this->processStatus, ['completed', 'rejected'])) {
            $this->selectedRequest->resolved_at = now();
            $this->selectedRequest->resolved_by = Auth::id();
        } else {
            $this->selectedRequest->resolved_at = null;
            $this->selectedRequest->resolved_by = null;
        }

        $this->selectedRequest->save();

        // Notify tenant
        if ($this->selectedRequest->tenant->user_id) {
            $user = $this->selectedRequest->tenant->user;
            $user->notify(new \App\Notifications\MaintenanceStatusNotification($this->selectedRequest));
        }

        $this->showProcessModal = false;
        $this->reset(['processStatus', 'adminNotes']);
        session()->flash('message', 'Status permintaan berhasil diperbarui');
    }

    public function render()
    {
        $query = MaintenanceRequest::with(['tenant.user', 'room.roomType.property'])
            ->latest();

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('title', 'like', "%{$this->search}%")
                    ->orWhereHas('tenant', function ($tq) {
                        $tq->whereHas('user', function ($uq) {
                            $uq->where('name', 'like', "%{$this->search}%");
                        });
                    })
                    ->orWhereHas('room', function ($rq) {
                        $rq->where('room_number', 'like', "%{$this->search}%");
                    });
            });
        }

        if ($this->statusFilter) {
            $query->where('status', $this->statusFilter);
        }

        if ($this->categoryFilter) {
            $query->where('category', $this->categoryFilter);
        }

        if ($this->priorityFilter) {
            $query->where('priority', $this->priorityFilter);
        }

        return view('livewire.admin.maintenance-index', [
            'requests' => $query->paginate(10),
        ]);
    }
}
