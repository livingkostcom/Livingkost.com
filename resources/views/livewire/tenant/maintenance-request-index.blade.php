<div>
    @if (session('message'))
        <div class="mb-4 p-3 bg-green-100 border border-green-300 text-green-700 rounded-lg text-sm">
            {{ session('message') }}
        </div>
    @endif

    @if (session('error'))
        <div class="mb-4 p-3 bg-red-100 border border-red-300 text-red-700 rounded-lg text-sm">
            {{ session('error') }}
        </div>
    @endif

    <!-- Header -->
    <div class="mb-8">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1
                    class="text-2xl sm:text-4xl font-bold bg-gradient-to-r from-orange-600 to-orange-600 bg-clip-text text-transparent">
                    Permintaan Perbaikan
                </h1>
                <p class="mt-2 text-gray-600">Ajukan permintaan perbaikan untuk kamar Anda</p>
            </div>
            <button wire:click="openCreate"
                class="inline-flex items-center gap-2 px-4 py-2 bg-orange-600 hover:bg-orange-700 text-white text-sm font-medium rounded-lg transition shadow-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                Buat Permintaan
            </button>
        </div>
    </div>

    <!-- Filter -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 mb-6">
        <div>
            <label class="block text-xs font-medium text-gray-600 mb-1">Status</label>
            <select wire:model.live="statusFilter"
                class="rounded-lg border-gray-300 text-sm focus:ring-orange-500 focus:border-orange-500">
                <option value="">Semua</option>
                <option value="pending">Menunggu</option>
                <option value="in_progress">Diproses</option>
                <option value="completed">Selesai</option>
                <option value="rejected">Ditolak</option>
            </select>
        </div>
    </div>

    <!-- Cards -->
    <div class="space-y-4">
        @forelse ($requests as $req)
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5 hover:shadow-md transition cursor-pointer"
                wire:click="openDetail({{ $req->id }})">
                <div class="flex items-start justify-between">
                    <div class="flex-1">
                        <h3 class="text-sm font-semibold text-gray-800">{{ $req->title }}</h3>
                        <p class="text-xs text-gray-500 mt-1">{{ Str::limit($req->description, 80) }}</p>
                    </div>
                    <div class="shrink-0 ml-4">
                        @php
                            $stColor = match ($req->status) {
                                'pending' => 'bg-yellow-100 text-yellow-700',
                                'in_progress' => 'bg-orange-100 text-orange-700',
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
                            class="px-2.5 py-1 rounded-full text-xs font-medium {{ $stColor }}">{{ $stLabel }}</span>
                    </div>
                </div>
                <div class="flex items-center gap-4 mt-3 text-xs text-gray-400">
                    <span class="flex items-center gap-1">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4">
                            </path>
                        </svg>
                        Kamar {{ $req->room?->room_number }}
                    </span>
                    <span>
                        @php
                            $catLabel = match ($req->category) {
                                'electrical' => 'Listrik',
                                'plumbing' => 'Pipa/Air',
                                'furniture' => 'Furnitur',
                                'cleaning' => 'Kebersihan',
                                default => 'Lainnya',
                            };
                        @endphp
                        {{ $catLabel }}
                    </span>
                    <span>
                        @php
                            $priLabel = match ($req->priority) {
                                'high' => 'Prioritas Tinggi',
                                'medium' => 'Prioritas Sedang',
                                default => 'Prioritas Rendah',
                            };
                        @endphp
                        {{ $priLabel }}
                    </span>
                    <span>{{ $req->created_at->translatedFormat('d M Y') }}</span>
                </div>
            </div>
        @empty
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-8 text-center">
                <svg class="w-12 h-12 text-gray-300 mx-auto mb-3" fill="none" stroke="currentColor"
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4">
                    </path>
                </svg>
                <p class="text-sm text-gray-400">Belum ada permintaan perbaikan</p>
                <button wire:click="openCreate" class="mt-3 text-sm text-orange-600 hover:text-orange-800 font-medium">
                    + Buat permintaan baru
                </button>
            </div>
        @endforelse
    </div>

    @if ($requests->hasPages())
        <div class="mt-4">
            {{ $requests->links() }}
        </div>
    @endif

    <!-- Create Modal -->
    @if ($showCreateModal)
        <div class="fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4"
            wire:click.self="$set('showCreateModal', false)">
            <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md">
                <div class="p-6">
                    <h3 class="text-lg font-bold text-gray-800 mb-4">Buat Permintaan Perbaikan</h3>

                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Judul <span
                                    class="text-red-500">*</span></label>
                            <input type="text" wire:model="title" placeholder="Contoh: AC tidak dingin"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500 transition @error('title') border-red-500 @enderror">
                            @error('title')
                                <span class="text-xs text-red-500 mt-1">{{ $message }}</span>
                            @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Deskripsi <span
                                    class="text-red-500">*</span></label>
                            <textarea wire:model="description" rows="3" placeholder="Jelaskan masalah secara detail..."
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500 transition @error('description') border-red-500 @enderror"></textarea>
                            @error('description')
                                <span class="text-xs text-red-500 mt-1">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Kategori</label>
                                <select wire:model="category"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500 transition">
                                    <option value="electrical">Listrik</option>
                                    <option value="plumbing">Pipa/Air</option>
                                    <option value="furniture">Furnitur</option>
                                    <option value="cleaning">Kebersihan</option>
                                    <option value="other">Lainnya</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Prioritas</label>
                                <select wire:model="priority"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500 transition">
                                    <option value="low">Rendah</option>
                                    <option value="medium">Sedang</option>
                                    <option value="high">Tinggi</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="flex justify-end gap-3 mt-6">
                        <button wire:click="$set('showCreateModal', false)"
                            class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg transition">
                            Batal
                        </button>
                        <button wire:click="createRequest"
                            class="px-4 py-2 text-sm font-medium text-white bg-orange-600 hover:bg-orange-700 rounded-lg transition shadow-sm">
                            Kirim Permintaan
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

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
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="text-xs font-medium text-gray-500 uppercase">Kamar</label>
                                <p class="text-sm text-gray-800 mt-1">{{ $selectedRequest->room?->room_number }}</p>
                            </div>
                            <div>
                                <label class="text-xs font-medium text-gray-500 uppercase">Status</label>
                                <p class="text-sm mt-1">
                                    @php
                                        $stColor2 = match ($selectedRequest->status) {
                                            'pending' => 'text-yellow-600',
                                            'in_progress' => 'text-orange-600',
                                            'completed' => 'text-green-600',
                                            'rejected' => 'text-red-600',
                                            default => 'text-gray-600',
                                        };
                                        $stLabel2 = match ($selectedRequest->status) {
                                            'pending' => 'Menunggu',
                                            'in_progress' => 'Diproses',
                                            'completed' => 'Selesai',
                                            'rejected' => 'Ditolak',
                                            default => '-',
                                        };
                                    @endphp
                                    <span class="font-semibold {{ $stColor2 }}">{{ $stLabel2 }}</span>
                                </p>
                            </div>
                            <div>
                                <label class="text-xs font-medium text-gray-500 uppercase">Tanggal</label>
                                <p class="text-sm text-gray-800 mt-1">
                                    {{ $selectedRequest->created_at->translatedFormat('d M Y H:i') }}</p>
                            </div>
                        </div>
                        @if ($selectedRequest->admin_notes)
                            <div class="bg-orange-50 border border-orange-200 rounded-lg p-3">
                                <label class="text-xs font-medium text-orange-600 uppercase">Catatan dari Admin</label>
                                <p class="text-sm text-gray-700 mt-1 whitespace-pre-line">
                                    {{ $selectedRequest->admin_notes }}</p>
                            </div>
                        @endif
                        @if ($selectedRequest->resolved_at)
                            <div>
                                <label class="text-xs font-medium text-gray-500 uppercase">Diselesaikan pada</label>
                                <p class="text-sm text-gray-800 mt-1">
                                    {{ $selectedRequest->resolved_at->translatedFormat('d M Y H:i') }}</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
