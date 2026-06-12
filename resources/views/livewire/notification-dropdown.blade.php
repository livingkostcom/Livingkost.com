<div class="relative" x-data="{ open: @entangle('isOpen') }" @click.outside="open = false">
    <!-- Bell Icon -->
    <button @click="open = !open" class="relative text-gray-600 hover:text-orange-600 transition focus:outline-none">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9">
            </path>
        </svg>
        @if ($this->unreadCount > 0)
            <span
                class="absolute -top-1 -right-1 bg-red-500 text-white text-xs font-bold rounded-full w-5 h-5 flex items-center justify-center">
                {{ $this->unreadCount > 9 ? '9+' : $this->unreadCount }}
            </span>
        @endif
    </button>

    <!-- Dropdown Panel -->
    <div x-show="open" x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-100" x-transition:leave-start="opacity-100 scale-100"
        x-transition:leave-end="opacity-0 scale-95"
        class="absolute right-0 mt-2 w-80 bg-white rounded-xl shadow-xl border border-gray-200 z-50 overflow-hidden"
        style="display: none;">

        <!-- Header -->
        <div class="flex items-center justify-between px-4 py-3 border-b border-gray-100 bg-gray-50">
            <h3 class="text-sm font-semibold text-gray-800">Notifikasi</h3>
            @if ($this->unreadCount > 0)
                <button wire:click="markAllAsRead"
                    class="text-xs text-orange-600 hover:text-orange-800 font-medium transition">
                    Tandai semua dibaca
                </button>
            @endif
        </div>

        <!-- Notification List -->
        <div class="max-h-80 overflow-y-auto divide-y divide-gray-100">
            @forelse ($notifications as $notification)
                <div wire:key="notif-{{ $notification->id }}"
                    class="px-4 py-3 hover:bg-orange-50/50 transition cursor-pointer {{ is_null($notification->read_at) ? 'bg-orange-50/30' : '' }}"
                    wire:click="markAsRead('{{ $notification->id }}')">
                    <div class="flex items-start gap-3">
                        {{-- Icon based on notification type --}}
                        @php
                            $notifType = $notification->data['type'] ?? 'payment_reminder';
                            $reminderType = $notification->data['reminder_type'] ?? 'info';

                            if ($notifType === 'new_announcement') {
                                $iconColor = match ($notification->data['priority'] ?? 'normal') {
                                    'urgent' => 'text-red-500',
                                    'important' => 'text-yellow-500',
                                    default => 'text-orange-500',
                                };
                            } elseif ($notifType === 'new_maintenance_request') {
                                $iconColor = 'text-orange-500';
                            } elseif ($notifType === 'maintenance_status') {
                                $iconColor = match ($notification->data['status'] ?? '') {
                                    'completed' => 'text-green-500',
                                    'rejected' => 'text-red-500',
                                    'in_progress' => 'text-orange-500',
                                    default => 'text-yellow-500',
                                };
                            } else {
                                $iconColor = match ($reminderType) {
                                    'overdue' => 'text-red-500',
                                    'due_today' => 'text-yellow-500',
                                    'upcoming' => 'text-orange-500',
                                    default => 'text-gray-500',
                                };
                            }
                        @endphp
                        <div class="shrink-0 mt-0.5">
                            <svg class="w-5 h-5 {{ $iconColor }}" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                @if ($notifType === 'new_announcement')
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z">
                                    </path>
                                @elseif (in_array($notifType, ['new_maintenance_request', 'maintenance_status']))
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.066 2.573c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.573 1.066c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.066-2.573c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z">
                                    </path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                @elseif ($reminderType === 'overdue')
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z">
                                    </path>
                                @elseif ($reminderType === 'due_today')
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                @else
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                @endif
                            </svg>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p
                                class="text-sm text-gray-800 {{ is_null($notification->read_at) ? 'font-semibold' : '' }}">
                                {{ $notification->data['message'] ?? 'Notifikasi baru' }}
                            </p>
                            <p class="text-xs text-gray-500 mt-1">
                                {{ $notification->created_at->diffForHumans() }}
                            </p>
                        </div>
                        @if (is_null($notification->read_at))
                            <div class="shrink-0 mt-1.5">
                                <span class="w-2 h-2 bg-orange-500 rounded-full block"></span>
                            </div>
                        @endif
                    </div>
                </div>
            @empty
                <div class="px-4 py-8 text-center">
                    <svg class="w-10 h-10 text-gray-300 mx-auto mb-2" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9">
                        </path>
                    </svg>
                    <p class="text-sm text-gray-400">Belum ada notifikasi</p>
                </div>
            @endforelse
        </div>
    </div>
</div>
