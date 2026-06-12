<?php

namespace App\Livewire\Tenant;

use App\Models\Announcement;
use App\Models\Tenant;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithPagination;

class AnnouncementList extends Component
{
    use WithPagination;

    public string $search = '';
    public string $priorityFilter = '';
    public ?Announcement $viewingAnnouncement = null;
    public bool $showDetailModal = false;

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function openDetail(int $id)
    {
        $this->viewingAnnouncement = Announcement::with(['property', 'creator'])->findOrFail($id);
        $this->showDetailModal = true;

        // Mark as read
        DB::table('announcement_reads')->insertOrIgnore([
            'announcement_id' => $id,
            'user_id' => Auth::id(),
            'read_at' => now(),
        ]);
    }

    public function render()
    {
        $user = Auth::user();

        // Get tenant's property IDs through leases
        $propertyIds = Tenant::where('user_id', $user->id)
            ->with('leases.room.roomType')
            ->get()
            ->flatMap(function ($tenant) {
                return $tenant->leases->map(function ($lease) {
                    return $lease->room?->roomType?->property_id;
                });
            })
            ->filter()
            ->unique()
            ->values();

        $query = Announcement::with(['property', 'creator'])
            ->active()
            ->where(function ($q) use ($propertyIds) {
                $q->where('target', 'all')
                    ->orWhere(function ($q2) use ($propertyIds) {
                        $q2->where('target', 'property')
                            ->whereIn('property_id', $propertyIds);
                    });
            });

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('title', 'like', '%' . $this->search . '%')
                    ->orWhere('content', 'like', '%' . $this->search . '%');
            });
        }

        if ($this->priorityFilter) {
            $query->where('priority', $this->priorityFilter);
        }

        // Get read announcement IDs
        $readIds = DB::table('announcement_reads')
            ->where('user_id', $user->id)
            ->pluck('announcement_id')
            ->toArray();

        return view('livewire.tenant.announcement-list', [
            'announcements' => $query->orderByDesc('published_at')->paginate(10),
            'readIds' => $readIds,
        ]);
    }
}
