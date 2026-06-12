<?php

namespace App\Notifications;

use App\Models\Announcement;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class NewAnnouncementNotification extends Notification
{
    use Queueable;

    public function __construct(public Announcement $announcement)
    {
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        $prioLabel = Announcement::getPriorityLabel($this->announcement->priority);

        return [
            'type' => 'new_announcement',
            'announcement_id' => $this->announcement->id,
            'title' => $this->announcement->title,
            'priority' => $this->announcement->priority,
            'message' => "Pengumuman baru ({$prioLabel}): \"{$this->announcement->title}\"",
        ];
    }
}
