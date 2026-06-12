<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    {{-- Header --}}
    <div class="mb-8">
        <h1
            class="text-2xl sm:text-3xl font-bold bg-gradient-to-r from-orange-600 to-purple-600 bg-clip-text text-transparent">
            Pengumuman
        </h1>
        <p class="text-gray-500 mt-1">Informasi terbaru dari pengelola kos</p>
    </div>

    {{-- Filters --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4 mb-6">
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <select wire:model.live="priorityFilter"
                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500 transition">
                <option value="">Semua Prioritas</option>
                <option value="normal">Normal</option>
                <option value="important">Penting</option>
                <option value="urgent">Darurat</option>
            </select>
            <div class="relative">
                <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400" fill="none"
                    stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
                <input wire:model.live.debounce.300ms="search" type="text" placeholder="Cari pengumuman..."
                    class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500 transition">
            </div>
        </div>
    </div>

    {{-- Announcement Cards --}}
    <div class="space-y-4">
        @forelse($announcements as $announcement)
            @php
                $isRead = in_array($announcement->id, $readIds);
                $prioConfig = match ($announcement->priority) {
                    'urgent' => [
                        'bg' => 'bg-red-50 border-red-200',
                        'badge' => 'bg-red-100 text-red-700',
                        'icon' => 'text-red-500',
                    ],
                    'important' => [
                        'bg' => 'bg-yellow-50 border-yellow-200',
                        'badge' => 'bg-yellow-100 text-yellow-700',
                        'icon' => 'text-yellow-500',
                    ],
                    default => [
                        'bg' => 'bg-white border-gray-100',
                        'badge' => 'bg-orange-100 text-orange-700',
                        'icon' => 'text-orange-500',
                    ],
                };
            @endphp
            <div wire:click="openDetail({{ $announcement->id }})"
                class="relative cursor-pointer rounded-2xl shadow-sm border p-5 hover:shadow-md transition-all {{ $prioConfig['bg'] }} {{ !$isRead ? 'ring-2 ring-orange-300' : '' }}">

                {{-- Unread dot --}}
                @if (!$isRead)
                    <div class="absolute top-4 right-4 w-3 h-3 rounded-full bg-orange-500 animate-pulse"></div>
                @endif

                <div class="flex items-start gap-4">
                    {{-- Icon --}}
                    <div
                        class="flex-shrink-0 w-10 h-10 rounded-xl {{ $announcement->priority === 'urgent' ? 'bg-red-100' : ($announcement->priority === 'important' ? 'bg-yellow-100' : 'bg-orange-100') }} flex items-center justify-center">
                        @if ($announcement->priority === 'urgent')
                            <svg class="w-5 h-5 {{ $prioConfig['icon'] }}" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.998L13.732 4.002c-.77-1.333-2.694-1.333-3.464 0L3.34 16.002c-.77 1.331.192 2.998 1.732 2.998z" />
                            </svg>
                        @else
                            <svg class="w-5 h-5 {{ $prioConfig['icon'] }}" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z" />
                            </svg>
                        @endif
                    </div>

                    {{-- Content --}}
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-2 mb-1">
                            <h3 class="font-semibold text-gray-800 {{ !$isRead ? 'text-orange-700' : '' }}">
                                {{ $announcement->title }}</h3>
                            <span class="text-xs px-2 py-0.5 rounded-full font-medium {{ $prioConfig['badge'] }}">
                                {{ \App\Models\Announcement::getPriorityLabel($announcement->priority) }}
                            </span>
                        </div>
                        <p class="text-sm text-gray-600 line-clamp-2">{{ Str::limit($announcement->content, 120) }}</p>
                        <div class="flex items-center gap-3 mt-2 text-xs text-gray-400">
                            <span>{{ $announcement->published_at->format('d M Y') }}</span>
                            @if ($announcement->property)
                                <span>&bull; {{ $announcement->property->name }}</span>
                            @endif
                            <span>&bull; {{ $announcement->creator?->name }}</span>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-12 text-center">
                <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor"
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                        d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z" />
                </svg>
                <p class="text-gray-500 font-medium">Belum ada pengumuman</p>
                <p class="text-gray-400 text-sm mt-1">Pengumuman dari pengelola akan muncul di sini</p>
            </div>
        @endforelse
    </div>

    @if ($announcements->hasPages())
        <div class="mt-6">
            {{ $announcements->links() }}
        </div>
    @endif

    {{-- Detail Modal --}}
    @if ($showDetailModal && $viewingAnnouncement)
        <div class="fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4"
            wire:click.self="$set('showDetailModal', false)">
            <div class="bg-white rounded-2xl shadow-2xl w-full max-w-2xl max-h-[90vh] overflow-y-auto">
                <div class="p-6 border-b border-gray-100">
                    <div class="flex items-start justify-between">
                        <div>
                            <h3 class="text-xl font-bold text-gray-800">{{ $viewingAnnouncement->title }}</h3>
                            <p class="text-sm text-gray-500 mt-1">
                                {{ $viewingAnnouncement->creator?->name }} &bull;
                                {{ $viewingAnnouncement->published_at->format('d M Y') }}
                            </p>
                        </div>
                        @php
                            $prioColor = match ($viewingAnnouncement->priority) {
                                'urgent' => 'bg-red-100 text-red-700',
                                'important' => 'bg-yellow-100 text-yellow-700',
                                default => 'bg-orange-100 text-orange-700',
                            };
                        @endphp
                        <span class="text-xs px-3 py-1 rounded-full font-medium {{ $prioColor }}">
                            {{ \App\Models\Announcement::getPriorityLabel($viewingAnnouncement->priority) }}
                        </span>
                    </div>
                </div>
                <div class="p-6">
                    <div class="prose max-w-none text-gray-700 whitespace-pre-line">{{ $viewingAnnouncement->content }}
                    </div>

                    @if ($viewingAnnouncement->expires_at)
                        <div class="mt-6 bg-gray-50 rounded-lg p-3 text-sm">
                            <span class="text-gray-500">Berlaku sampai:</span>
                            <span
                                class="font-medium text-gray-800 ml-1">{{ $viewingAnnouncement->expires_at->format('d M Y') }}</span>
                        </div>
                    @endif
                </div>
                <div class="p-6 border-t border-gray-100 flex justify-end">
                    <button wire:click="$set('showDetailModal', false)"
                        class="px-5 py-2.5 text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-xl font-medium transition">
                        Tutup
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
