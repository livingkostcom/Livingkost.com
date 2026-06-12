<div>
    @if (session('message'))
        <div class="mb-4 p-3 bg-green-100 border border-green-300 text-green-700 rounded-lg text-sm">
            {{ session('message') }}
        </div>
    @endif

    <!-- Header -->
    <div class="mb-8">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1
                    class="text-2xl sm:text-4xl font-bold bg-gradient-to-r from-blue-600 to-indigo-600 bg-clip-text text-transparent">
                    Permintaan Perbaikan
                </h1>
                <p class="mt-2 text-gray-600">Kelola permintaan perbaikan dari penyewa</p>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 mb-6">
        <div class="flex flex-wrap gap-4 items-end">
            <div class="flex-1 min-w-[200px]">
                <label class="block text-xs font-medium text-gray-600 mb-1">Cari</label>
                <input type="text" wire:model.live.debounce.300ms="search" placeholder="Cari judul, penyewa, kamar..."
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition text-sm">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Status</label>
                <select wire:model.live="statusFilter"
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition text-sm">
                    <option value="">Semua</option>
                    <option value="pending">Menunggu</option>
                    <option value="in_progress">Diproses</option>
                    <option value="completed">Selesai</option>
                    <option value="rejected">Ditolak</option>
                </select>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Kategori</label>
                <select wire:model.live="categoryFilter"
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition text-sm">
                    <option value="">Semua</option>
                    <option value="electrical">Listrik</option>
                    <option value="plumbing">Pipa/Air</option>
                    <option value="furniture">Furnitur</option>
                    <option value="cleaning">Kebersihan</option>
                    <option value="other">Lainnya</option>
                </select>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Prioritas</label>
                <select wire:model.live="priorityFilter"
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition text-sm">
                    <option value="">Semua</option>
                    <option value="high">Tinggi</option>
                    <option value="medium">Sedang</option>
                    <option value="low">Rendah</option>
                </select>
            </div>
        </div>
    </div>

    <!-- Table -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 text-gray-600">
                    <tr>
                        <th class="px-4 py-3 text-left font-medium">Judul</th>
                        <th class="px-4 py-3 text-left font-medium">Penyewa</th>
                        <th class="px-4 py-3 text-left font-medium">Kamar</th>
                        <th class="px-4 py-3 text-center font-medium">Kategori</th>
                        <th class="px-4 py-3 text-center font-medium">Prioritas</th>
                        <th class="px-4 py-3 text-center font-medium">Status</th>
                        <th class="px-4 py-3 text-left font-medium">Tanggal</th>
                        <th class="px-4 py-3 text-center font-medium">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse ($requests as $req)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3 font-medium text-gray-800">{{ Str::limit($req->title, 30) }}</td>
                            <td class="px-4 py-3">{{ $req->tenant?->display_name ?? '-' }}</td>
                            <td class="px-4 py-3">
                                <span class="text-gray-800">{{ $req->room?->room_number ?? '-' }}</span>
                                <span
                                    class="block text-xs text-gray-400">{{ $req->room?->roomType?->property?->name ?? '' }}</span>
                            </td>
                            <td class="px-4 py-3 text-center">
                                @php
                                    $catLabel = match ($req->category) {
                                        'electrical' => 'Listrik',
                                        'plumbing' => 'Pipa/Air',
                                        'furniture' => 'Furnitur',
                                        'cleaning' => 'Kebersihan',
                                        default => 'Lainnya',
                                    };
                                    $catColor = match ($req->category) {
                                        'electrical' => 'bg-yellow-100 text-yellow-700',
                                        'plumbing' => 'bg-blue-100 text-blue-700',
                                        'furniture' => 'bg-purple-100 text-purple-700',
                                        'cleaning' => 'bg-green-100 text-green-700',
                                        default => 'bg-gray-100 text-gray-700',
                                    };
                                @endphp
                                <span
                                    class="px-2 py-1 rounded-full text-xs font-medium {{ $catColor }}">{{ $catLabel }}</span>
                            </td>
                            <td class="px-4 py-3 text-center">
                                @php
                                    $priColor = match ($req->priority) {
                                        'high' => 'bg-red-100 text-red-700',
                                        'medium' => 'bg-yellow-100 text-yellow-700',
                                        default => 'bg-green-100 text-green-700',
                                    };
                                    $priLabel = match ($req->priority) {
                                        'high' => 'Tinggi',
                                        'medium' => 'Sedang',
                                        default => 'Rendah',
                                    };
                                @endphp
                                <span
                                    class="px-2 py-1 rounded-full text-xs font-medium {{ $priColor }}">{{ $priLabel }}</span>
                            </td>
                            <td class="px-4 py-3 text-center">
                                @php
                                    $stColor = match ($req->status) {
                                        'pending' => 'bg-yellow-100 text-yellow-700',
                                        'in_progress' => 'bg-blue-100 text-blue-700',
                                        'completed' => 'bg-green-100 text-green-700',
                                        'rejected' => 'bg-red-100 text-red-700',
                                        default => 'bg-gray-100 text-gray-700',
                                    };
                                    $stLabel = match ($req->status) {
                                        'pending' => 'Menunggu',
                                        'in_progress' => 'Diproses',
                                        'completed' => 'Selesai',
                                        'rejected' => 'Ditolak',
                                        default => '-',
                                    };
                                @endphp
                                <span
                                    class="px-2 py-1 rounded-full text-xs font-medium {{ $stColor }}">{{ $stLabel }}</span>
                            </td>
                            <td class="px-4 py-3 text-gray-500 text-xs">
                                {{ $req->created_at->translatedFormat('d M Y') }}</td>
                            <td class="px-4 py-3 text-center">
                                <div class="flex items-center justify-center gap-1">
                                    <button wire:click="openDetail({{ $req->id }})"
                                        class="p-1.5 text-blue-600 hover:bg-blue-50 rounded-lg transition"
                                        title="Detail">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z">
                                            </path>
                                        </svg>
                                    </button>
                                    @if (!in_array($req->status, ['completed', 'rejected']))
                                        <button wire:click="openProcess({{ $req->id }})"
                                            class="p-1.5 text-green-600 hover:bg-green-50 rounded-lg transition"
                                            title="Proses">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
                                                </path>
                                            </svg>
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-4 py-8 text-center text-gray-400">Tidak ada permintaan
                                perbaikan</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($requests->hasPages())
            <div class="px-4 py-3 border-t border-gray-100">
                {{ $requests->links() }}
            </div>
        @endif
    </div>

    <!-- Detail Modal -->
    @if ($showDetailModal && $selectedRequest)
        <div class="fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4"
            wire:click.self="$set('showDetailModal', false)">
            <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg max-h-[90vh] overflow-y-auto">
                <div class="p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-bold text-gray-800">Detail Permintaan</h3>
                        <button wire:click="$set('showDetailModal', false)" class="text-gray-400 hover:text-gray-600">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>

                    <div class="space-y-4">
                        <div>
                            <label class="text-xs font-medium text-gray-500 uppercase">Judul</label>
                            <p class="text-sm font-semibold text-gray-800 mt-1">{{ $selectedRequest->title }}</p>
                        </div>
                        <div>
                            <label class="text-xs font-medium text-gray-500 uppercase">Deskripsi</label>
                            <p class="text-sm text-gray-700 mt-1 whitespace-pre-line">
                                {{ $selectedRequest->description }}</p>
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="text-xs font-medium text-gray-500 uppercase">Penyewa</label>
                                <p class="text-sm text-gray-800 mt-1">{{ $selectedRequest->tenant?->display_name }}
                                </p>
                            </div>
                            <div>
                                <label class="text-xs font-medium text-gray-500 uppercase">Kamar</label>
                                <p class="text-sm text-gray-800 mt-1">{{ $selectedRequest->room?->room_number }} —
                                    {{ $selectedRequest->room?->roomType?->property?->name }}</p>
                            </div>
                            <div>
                                <label class="text-xs font-medium text-gray-500 uppercase">Kategori</label>
                                <p class="text-sm text-gray-800 mt-1">
                                    {{ match ($selectedRequest->category) {'electrical' => 'Listrik','plumbing' => 'Pipa/Air','furniture' => 'Furnitur','cleaning' => 'Kebersihan',default => 'Lainnya'} }}
                                </p>
                            </div>
                            <div>
                                <label class="text-xs font-medium text-gray-500 uppercase">Prioritas</label>
                                <p class="text-sm text-gray-800 mt-1">
                                    {{ match ($selectedRequest->priority) {'high' => 'Tinggi','medium' => 'Sedang',default => 'Rendah'} }}
                                </p>
                            </div>
                            <div>
                                <label class="text-xs font-medium text-gray-500 uppercase">Tanggal</label>
                                <p class="text-sm text-gray-800 mt-1">
                                    {{ $selectedRequest->created_at->translatedFormat('d M Y H:i') }}</p>
                            </div>
                            <div>
                                <label class="text-xs font-medium text-gray-500 uppercase">Status</label>
                                <p class="text-sm text-gray-800 mt-1">
                                    {{ match ($selectedRequest->status) {'pending' => 'Menunggu','in_progress' => 'Diproses','completed' => 'Selesai','rejected' => 'Ditolak',default => '-'} }}
                                </p>
                            </div>
                        </div>
                        @if ($selectedRequest->admin_notes)
                            <div>
                                <label class="text-xs font-medium text-gray-500 uppercase">Catatan Admin</label>
                                <p class="text-sm text-gray-700 mt-1 whitespace-pre-line bg-gray-50 p-3 rounded-lg">
                                    {{ $selectedRequest->admin_notes }}</p>
                            </div>
                        @endif
                        @if ($selectedRequest->resolved_at)
                            <div>
                                <label class="text-xs font-medium text-gray-500 uppercase">Diselesaikan</label>
                                <p class="text-sm text-gray-800 mt-1">
                                    {{ $selectedRequest->resolved_at->translatedFormat('d M Y H:i') }} oleh
                                    {{ $selectedRequest->resolver?->name ?? '-' }}</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Process Modal -->
    @if ($showProcessModal && $selectedRequest)
        <div class="fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4"
            wire:click.self="$set('showProcessModal', false)">
            <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md">
                <div class="p-6">
                    <h3 class="text-lg font-bold text-gray-800 mb-4">Proses Permintaan</h3>
                    <p class="text-sm text-gray-600 mb-4">{{ $selectedRequest->title }}</p>

                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                            <select wire:model="processStatus"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition text-sm">
                                <option value="pending">Menunggu</option>
                                <option value="in_progress">Diproses</option>
                                <option value="completed">Selesai</option>
                                <option value="rejected">Ditolak</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Catatan Admin</label>
                            <textarea wire:model="adminNotes" rows="3" placeholder="Catatan untuk penyewa..."
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition text-sm"></textarea>
                        </div>
                    </div>

                    <div class="flex justify-end gap-3 mt-6">
                        <button wire:click="$set('showProcessModal', false)"
                            class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg transition">
                            Batal
                        </button>
                        <button wire:click="updateStatus"
                            class="px-4 py-2 text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 rounded-lg transition shadow-sm">
                            Simpan
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
