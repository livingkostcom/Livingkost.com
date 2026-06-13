<?php

namespace App\Livewire\Tenant;

use App\Models\MaintenanceRequest;
use App\Models\Setting;
use App\Models\User;
use App\Notifications\NewMaintenanceRequestNotification;
use App\Services\WhatsAppService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
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

        // Notify this tenant's owner & managers only (in-app + email)
        $maintenanceRequest->load(['tenant.user', 'room']);
        $ownerId = $tenant->owner_id;
        $admins = User::role(['owner', 'manager'])
            ->where(fn ($q) => $q->where('id', $ownerId)->orWhere('owner_id', $ownerId))
            ->get();
        foreach ($admins as $admin) {
            // Email may fail (e.g. owner address has no real mailbox); never let
            // that break request creation. The in-app DB notification is sent
            // first, so it persists even when the mail channel throws.
            try {
                $admin->notify(new NewMaintenanceRequestNotification($maintenanceRequest));
            } catch (\Throwable $e) {
                Log::error('Maintenance request notification failed', [
                    'admin_id' => $admin->id,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        // WhatsApp: owner's business number (Setting app_phone) + each manager's own phone
        $waMessage = $this->buildWaMessage($maintenanceRequest);
        $sentTo = [];

        $ownerPhone = Setting::getValue('app_phone');
        if ($ownerPhone) {
            WhatsAppService::send($ownerPhone, $waMessage);
            $sentTo[] = preg_replace('/[^0-9]/', '', $ownerPhone);
        }

        foreach ($admins as $admin) {
            $phone = $admin->phone ?? null;
            if ($phone && !in_array(preg_replace('/[^0-9]/', '', $phone), $sentTo, true)) {
                WhatsAppService::send($phone, $waMessage);
                $sentTo[] = preg_replace('/[^0-9]/', '', $phone);
            }
        }

        $this->showCreateModal = false;
        $this->reset(['title', 'description', 'category', 'priority']);
        session()->flash('message', 'Permintaan perbaikan berhasil dikirim');
    }

    private function buildWaMessage(MaintenanceRequest $request): string
    {
        $priLabel = match ($request->priority) {
            'high' => 'Tinggi',
            'medium' => 'Sedang',
            default => 'Rendah',
        };
        $catLabel = match ($request->category) {
            'electrical' => 'Listrik',
            'plumbing' => 'Saluran Air',
            'furniture' => 'Perabot',
            'cleaning' => 'Kebersihan',
            default => 'Lainnya',
        };

        return "*Permintaan Perbaikan Baru*\n\n"
            . "Judul: {$request->title}\n"
            . "Penghuni: " . ($request->tenant?->display_name ?? '-') . "\n"
            . "Kamar: " . ($request->room?->room_number ?? '-') . "\n"
            . "Kategori: {$catLabel}\n"
            . "Prioritas: {$priLabel}\n\n"
            . "Deskripsi:\n{$request->description}\n\n"
            . "Silakan tindak lanjuti melalui dashboard Living Kost.\n\n"
            . "_Living Kost_";
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
