<?php

namespace App\Livewire\Admin;

use App\Models\Announcement;
use App\Models\Property;
use App\Models\User;
use App\Notifications\NewAnnouncementNotification;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class AnnouncementIndex extends Component
{
    use WithPagination;

    public string $search = '';
    public string $priorityFilter = '';
    public string $statusFilter = '';

    // Form fields
    public string $title = '';
    public string $content = '';
    public string $priority = 'normal';
    public string $target = 'all';
    public ?int $property_id = null;
    public string $published_at = '';
    public string $expires_at = '';

    // Modal state
    public bool $showModal = false;
    public bool $showDetailModal = false;
    public bool $showDeleteModal = false;
    public bool $isEditing = false;
    public ?int $editingId = null;
    public ?Announcement $viewingAnnouncement = null;

    public function mount()
    {
        if (!Auth::user()->can('manage-announcements')) {
            abort(403);
        }
        $this->published_at = now()->format('Y-m-d');
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedPriorityFilter()
    {
        $this->resetPage();
    }

    public function updatedStatusFilter()
    {
        $this->resetPage();
    }

    public function openCreateModal()
    {
        $this->resetForm();
        $this->showModal = true;
    }

    public function openEditModal(int $id)
    {
        $announcement = Announcement::findOrFail($id);
        $this->editingId = $id;
        $this->isEditing = true;
        $this->title = $announcement->title;
        $this->content = $announcement->content;
        $this->priority = $announcement->priority;
        $this->target = $announcement->target;
        $this->property_id = $announcement->property_id;
        $this->published_at = $announcement->published_at->format('Y-m-d');
        $this->expires_at = $announcement->expires_at?->format('Y-m-d') ?? '';
        $this->showModal = true;
    }

    public function openDetailModal(int $id)
    {
        $this->viewingAnnouncement = Announcement::with(['property', 'creator', 'readByUsers'])->findOrFail($id);
        $this->showDetailModal = true;
    }

    public function confirmDelete(int $id)
    {
        $this->editingId = $id;
        $this->showDeleteModal = true;
    }

    public function save()
    {
        $rules = [
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'priority' => 'required|in:normal,important,urgent',
            'target' => 'required|in:all,property',
            'published_at' => 'required|date',
            'expires_at' => 'nullable|date|after_or_equal:published_at',
        ];

        if ($this->target === 'property') {
            $rules['property_id'] = 'required|exists:properties,id';
        }

        $this->validate($rules);

        $data = [
            'title' => $this->title,
            'content' => $this->content,
            'priority' => $this->priority,
            'target' => $this->target,
            'property_id' => $this->target === 'property' ? $this->property_id : null,
            'published_at' => $this->published_at,
            'expires_at' => $this->expires_at ?: null,
            'created_by' => Auth::id(),
        ];

        if ($this->isEditing) {
            $announcement = Announcement::findOrFail($this->editingId);
            $announcement->update($data);
            session()->flash('message', 'Pengumuman berhasil diperbarui.');
        } else {
            $announcement = Announcement::create($data);
            $this->sendNotifications($announcement);
            session()->flash('message', 'Pengumuman berhasil dibuat dan dikirim.');
        }

        $this->showModal = false;
        $this->resetForm();
    }

    public function toggleActive(int $id)
    {
        $announcement = Announcement::findOrFail($id);
        $announcement->update(['is_active' => !$announcement->is_active]);
    }

    public function delete()
    {
        Announcement::findOrFail($this->editingId)->delete();
        $this->showDeleteModal = false;
        $this->editingId = null;
        session()->flash('message', 'Pengumuman berhasil dihapus.');
    }

    private function sendNotifications(Announcement $announcement)
    {
        if ($announcement->target === 'property' && $announcement->property_id) {
            // Get user IDs of tenants who have active leases in the target property
            $userIds = \App\Models\Tenant::whereHas('leases', function ($q) use ($announcement) {
                $q->whereHas('room.roomType', function ($q2) use ($announcement) {
                    $q2->where('property_id', $announcement->property_id);
                });
            })->whereNotNull('user_id')->pluck('user_id');

            $tenants = User::role('tenant')->whereIn('id', $userIds)->get();
        } else {
            $tenants = User::role('tenant')->get();
        }

        foreach ($tenants as $tenant) {
            $tenant->notify(new NewAnnouncementNotification($announcement));
        }
    }

    private function resetForm()
    {
        $this->title = '';
        $this->content = '';
        $this->priority = 'normal';
        $this->target = 'all';
        $this->property_id = null;
        $this->published_at = now()->format('Y-m-d');
        $this->expires_at = '';
        $this->isEditing = false;
        $this->editingId = null;
        $this->resetValidation();
    }

    public function getSummary(): array
    {
        return [
            'total' => Announcement::count(),
            'active' => Announcement::active()->count(),
            'urgent' => Announcement::where('priority', 'urgent')->where('is_active', true)->count(),
        ];
    }

    public function render()
    {
        $query = Announcement::with(['property', 'creator']);

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('title', 'like', '%' . $this->search . '%')
                    ->orWhere('content', 'like', '%' . $this->search . '%');
            });
        }

        if ($this->priorityFilter) {
            $query->where('priority', $this->priorityFilter);
        }

        if ($this->statusFilter === 'active') {
            $query->where('is_active', true);
        } elseif ($this->statusFilter === 'inactive') {
            $query->where('is_active', false);
        }

        $summary = $this->getSummary();

        return view('livewire.admin.announcement-index', [
            'announcements' => $query->orderByDesc('published_at')->paginate(10),
            'properties' => Property::orderBy('name')->get(),
            'summary' => $summary,
        ]);
    }
}
