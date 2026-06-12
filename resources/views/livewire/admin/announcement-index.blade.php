<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    {{-- Flash Message --}}
    @if (session()->has('message'))
        <div class="mb-6 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg flex items-center justify-between"
            x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)"
            x-transition:leave="transition ease-in duration-300" x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0">
            <span>{{ session('message') }}</span>
            <button @click="show = false" class="text-green-500 hover:text-green-700">&times;</button>
        </div>
    @endif

    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-8">
        <div>
            <h1
                class="text-2xl sm:text-3xl font-bold bg-gradient-to-r from-indigo-600 to-purple-600 bg-clip-text text-transparent">
                Pengumuman
            </h1>
            <p class="text-gray-500 mt-1">Kelola pengumuman untuk penyewa</p>
        </div>
        <button wire:click="openCreateModal"
            class="mt-4 sm:mt-0 inline-flex items-center gap-2 px-5 py-2.5 bg-gradient-to-r from-indigo-600 to-purple-600 text-white rounded-xl font-semibold shadow hover:shadow-lg transform hover:-translate-y-0.5 transition-all">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            Buat Pengumuman
        </button>
    </div>

    {{-- Summary Cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 mb-8">
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-indigo-100 flex items-center justify-center">
                    <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z" />
                    </svg>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Total Pengumuman</p>
                    <p class="text-2xl font-bold text-gray-800">{{ $summary['total'] }}</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-green-100 flex items-center justify-center">
                    <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Aktif</p>
                    <p class="text-2xl font-bold text-green-600">{{ $summary['active'] }}</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-red-100 flex items-center justify-center">
                    <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.998L13.732 4.002c-.77-1.333-2.694-1.333-3.464 0L3.34 16.002c-.77 1.331.192 2.998 1.732 2.998z" />
                    </svg>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Darurat Aktif</p>
                    <p class="text-2xl font-bold text-red-600">{{ $summary['urgent'] }}</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Filters --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4 mb-6">
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <select wire:model.live="priorityFilter"
                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 transition">
                <option value="">Semua Prioritas</option>
                <option value="normal">Normal</option>
                <option value="important">Penting</option>
                <option value="urgent">Darurat</option>
            </select>
            <select wire:model.live="statusFilter"
                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 transition">
                <option value="">Semua Status</option>
                <option value="active">Aktif</option>
                <option value="inactive">Nonaktif</option>
            </select>
            <div class="relative">
                <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400" fill="none"
                    stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
                <input wire:model.live.debounce.300ms="search" type="text" placeholder="Cari pengumuman..."
                    class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 transition">
            </div>
        </div>
    </div>

    {{-- Table --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gradient-to-r from-gray-50 to-gray-100">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            Judul</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            Prioritas</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            Target</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            Tanggal</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            Status</th>
                        <th class="px-6 py-4 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($announcements as $announcement)
                        @php
                            $prioColor = match ($announcement->priority) {
                                'urgent' => 'bg-red-100 text-red-700',
                                'important' => 'bg-yellow-100 text-yellow-700',
                                default => 'bg-blue-100 text-blue-700',
                            };
                        @endphp
                        <tr class="hover:bg-gray-50/50 transition-colors">
                            <td class="px-6 py-4">
                                <button wire:click="openDetailModal({{ $announcement->id }})"
                                    class="text-left hover:text-indigo-600 transition-colors">
                                    <p class="font-semibold text-gray-800">{{ $announcement->title }}</p>
                                    <p class="text-sm text-gray-500 mt-0.5 line-clamp-1">
                                        {{ Str::limit($announcement->content, 60) }}</p>
                                </button>
                            </td>
                            <td class="px-6 py-4">
                                <span class="text-xs px-2.5 py-1 rounded-full font-medium {{ $prioColor }}">
                                    {{ \App\Models\Announcement::getPriorityLabel($announcement->priority) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600">
                                @if ($announcement->target === 'all')
                                    <span
                                        class="text-xs px-2.5 py-1 rounded-full font-medium bg-indigo-100 text-indigo-700">Semua</span>
                                @else
                                    <span
                                        class="text-xs px-2.5 py-1 rounded-full font-medium bg-purple-100 text-purple-700">{{ $announcement->property?->name ?? '-' }}</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600">
                                <p>{{ $announcement->published_at->format('d M Y') }}</p>
                                @if ($announcement->expires_at)
                                    <p class="text-xs text-gray-400">s/d
                                        {{ $announcement->expires_at->format('d M Y') }}</p>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <button wire:click="toggleActive({{ $announcement->id }})"
                                    class="relative inline-flex h-6 w-11 items-center rounded-full transition-colors focus:outline-none {{ $announcement->is_active ? 'bg-green-500' : 'bg-gray-300' }}">
                                    <span
                                        class="inline-block h-4 w-4 transform rounded-full bg-white transition-transform {{ $announcement->is_active ? 'translate-x-6' : 'translate-x-1' }}"></span>
                                </button>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center justify-center gap-2">
                                    <button wire:click="openDetailModal({{ $announcement->id }})"
                                        class="p-2 text-gray-400 hover:text-indigo-600 hover:bg-indigo-50 rounded-lg transition-all"
                                        title="Detail">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                    </button>
                                    <button wire:click="openEditModal({{ $announcement->id }})"
                                        class="p-2 text-gray-400 hover:text-yellow-600 hover:bg-yellow-50 rounded-lg transition-all"
                                        title="Edit">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                    </button>
                                    <button wire:click="confirmDelete({{ $announcement->id }})"
                                        class="p-2 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-all"
                                        title="Hapus">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center">
                                    <svg class="w-12 h-12 text-gray-300 mb-3" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                            d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z" />
                                    </svg>
                                    <p class="text-gray-500 font-medium">Belum ada pengumuman</p>
                                    <p class="text-gray-400 text-sm mt-1">Buat pengumuman pertama untuk penyewa</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($announcements->hasPages())
            <div class="px-6 py-4 border-t border-gray-100">
                {{ $announcements->links() }}
            </div>
        @endif
    </div>

    {{-- Create/Edit Modal --}}
    @if ($showModal)
        <div class="fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4"
            wire:click.self="$set('showModal', false)">
            <div class="bg-white rounded-2xl shadow-2xl w-full max-w-2xl max-h-[90vh] overflow-y-auto">
                <div class="p-6 border-b border-gray-100">
                    <h3 class="text-xl font-bold text-gray-800">
                        {{ $isEditing ? 'Edit Pengumuman' : 'Buat Pengumuman Baru' }}
                    </h3>
                </div>
                <div class="p-6 space-y-5">
                    {{-- Title --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Judul <span
                                class="text-red-500">*</span></label>
                        <input wire:model="title" type="text" placeholder="Judul pengumuman..."
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 transition">
                        @error('title')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Content --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Isi Pengumuman <span
                                class="text-red-500">*</span></label>
                        <textarea wire:model="content" rows="5" placeholder="Tulis isi pengumuman..."
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 transition"></textarea>
                        @error('content')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                        {{-- Priority --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Prioritas</label>
                            <select wire:model="priority"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 transition">
                                <option value="normal">Normal</option>
                                <option value="important">Penting</option>
                                <option value="urgent">Darurat</option>
                            </select>
                        </div>

                        {{-- Target --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Target</label>
                            <select wire:model.live="target"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 transition">
                                <option value="all">Semua Penyewa</option>
                                <option value="property">Per Properti</option>
                            </select>
                        </div>
                    </div>

                    {{-- Property (conditional) --}}
                    @if ($target === 'property')
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Properti <span
                                    class="text-red-500">*</span></label>
                            <select wire:model="property_id"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 transition">
                                <option value="">Pilih Properti</option>
                                @foreach ($properties as $property)
                                    <option value="{{ $property->id }}">{{ $property->name }}</option>
                                @endforeach
                            </select>
                            @error('property_id')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    @endif

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                        {{-- Published At --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Terbit <span
                                    class="text-red-500">*</span></label>
                            <input wire:model="published_at" type="date"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 transition">
                            @error('published_at')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Expires At --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Kedaluwarsa</label>
                            <input wire:model="expires_at" type="date"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 transition">
                            @error('expires_at')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>
                <div class="p-6 border-t border-gray-100 flex justify-end gap-3">
                    <button wire:click="$set('showModal', false)"
                        class="px-5 py-2.5 text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-xl font-medium transition">
                        Batal
                    </button>
                    <button wire:click="save"
                        class="px-5 py-2.5 bg-gradient-to-r from-indigo-600 to-purple-600 text-white rounded-xl font-semibold shadow hover:shadow-lg transition">
                        {{ $isEditing ? 'Simpan Perubahan' : 'Kirim Pengumuman' }}
                    </button>
                </div>
            </div>
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
                                Oleh {{ $viewingAnnouncement->creator?->name }} &bull;
                                {{ $viewingAnnouncement->published_at->format('d M Y') }}
                            </p>
                        </div>
                        @php
                            $prioColor = match ($viewingAnnouncement->priority) {
                                'urgent' => 'bg-red-100 text-red-700',
                                'important' => 'bg-yellow-100 text-yellow-700',
                                default => 'bg-blue-100 text-blue-700',
                            };
                        @endphp
                        <span class="text-xs px-3 py-1 rounded-full font-medium {{ $prioColor }}">
                            {{ \App\Models\Announcement::getPriorityLabel($viewingAnnouncement->priority) }}
                        </span>
                    </div>
                </div>
                <div class="p-6">
                    <div class="prose max-w-none text-gray-700 whitespace-pre-line">
                        {{ $viewingAnnouncement->content }}</div>

                    <div class="mt-6 grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
                        <div class="bg-gray-50 rounded-lg p-3">
                            <p class="text-gray-500">Target</p>
                            <p class="font-medium text-gray-800">
                                {{ $viewingAnnouncement->target === 'all' ? 'Semua Penyewa' : $viewingAnnouncement->property?->name }}
                            </p>
                        </div>
                        <div class="bg-gray-50 rounded-lg p-3">
                            <p class="text-gray-500">Status</p>
                            <p
                                class="font-medium {{ $viewingAnnouncement->is_active ? 'text-green-600' : 'text-red-600' }}">
                                {{ $viewingAnnouncement->is_active ? 'Aktif' : 'Nonaktif' }}
                            </p>
                        </div>
                        <div class="bg-gray-50 rounded-lg p-3">
                            <p class="text-gray-500">Kedaluwarsa</p>
                            <p class="font-medium text-gray-800">
                                {{ $viewingAnnouncement->expires_at ? $viewingAnnouncement->expires_at->format('d M Y') : 'Tidak ada' }}
                            </p>
                        </div>
                        <div class="bg-gray-50 rounded-lg p-3">
                            <p class="text-gray-500">Dibaca</p>
                            <p class="font-medium text-gray-800">
                                {{ $viewingAnnouncement->readByUsers->count() }} penyewa
                            </p>
                        </div>
                    </div>
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

    {{-- Delete Confirmation Modal --}}
    @if ($showDeleteModal)
        <div class="fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4"
            wire:click.self="$set('showDeleteModal', false)">
            <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md">
                <div class="p-6 text-center">
                    <div class="w-16 h-16 mx-auto mb-4 rounded-full bg-red-100 flex items-center justify-center">
                        <svg class="w-8 h-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.998L13.732 4.002c-.77-1.333-2.694-1.333-3.464 0L3.34 16.002c-.77 1.331.192 2.998 1.732 2.998z" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-gray-800 mb-2">Hapus Pengumuman?</h3>
                    <p class="text-gray-500">Pengumuman yang dihapus tidak dapat dikembalikan.</p>
                </div>
                <div class="p-6 border-t border-gray-100 flex justify-center gap-3">
                    <button wire:click="$set('showDeleteModal', false)"
                        class="px-5 py-2.5 text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-xl font-medium transition">
                        Batal
                    </button>
                    <button wire:click="delete"
                        class="px-5 py-2.5 bg-red-600 hover:bg-red-700 text-white rounded-xl font-semibold shadow transition">
                        Hapus
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
