<?php

namespace App\Livewire;

use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class NotificationDropdown extends Component
{
    public bool $isOpen = false;

    public function toggle()
    {
        $this->isOpen = !$this->isOpen;
    }

    public function markAsRead(string $notificationId)
    {
        $notification = Auth::user()->notifications()->find($notificationId);
        $notification?->markAsRead();
    }

    public function markAllAsRead()
    {
        Auth::user()->unreadNotifications->markAsRead();
    }

    public function getUnreadCountProperty(): int
    {
        return Auth::user()->unreadNotifications()->count();
    }

    public function render()
    {
        return view('livewire.notification-dropdown', [
            'notifications' => Auth::user()->notifications()->latest()->take(20)->get(),
        ]);
    }
}
