<?php

namespace App\Notifications;

use App\Models\MaintenanceRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class MaintenanceStatusNotification extends Notification
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
        $statusLabel = match ($this->request->status) {
            'in_progress' => 'sedang diproses',
            'completed' => 'telah selesai',
            'rejected' => 'ditolak',
            default => 'menunggu',
        };

        return [
            'type' => 'maintenance_status',
            'maintenance_request_id' => $this->request->id,
            'title' => $this->request->title,
            'status' => $this->request->status,
            'message' => "Permintaan perbaikan \"{$this->request->title}\" {$statusLabel}",
            'admin_notes' => $this->request->admin_notes,
        ];
    }
}
