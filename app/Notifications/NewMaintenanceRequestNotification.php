<?php

namespace App\Notifications;

use App\Models\MaintenanceRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class NewMaintenanceRequestNotification extends Notification
{
    use Queueable;

    public function __construct(public MaintenanceRequest $request)
    {
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        $priLabel = match ($this->request->priority) {
            'high' => 'Tinggi',
            'medium' => 'Sedang',
            default => 'Rendah',
        };

        return [
            'type' => 'new_maintenance_request',
            'maintenance_request_id' => $this->request->id,
            'title' => $this->request->title,
            'priority' => $this->request->priority,
            'category' => $this->request->category,
            'room_number' => $this->request->room?->room_number,
            'tenant_name' => $this->request->tenant?->display_name,
            'message' => "Permintaan perbaikan baru: \"{$this->request->title}\" dari {$this->request->tenant?->display_name} (Kamar {$this->request->room?->room_number}) — Prioritas {$priLabel}",
        ];
    }
}
