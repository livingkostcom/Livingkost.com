<div>
    <!-- Header -->
    <div class="mb-8">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1
                    class="text-2xl sm:text-4xl font-bold bg-gradient-to-r from-blue-600 to-indigo-600 bg-clip-text text-transparent">
                    Kontrak Sewa</h1>
                <p class="mt-2 text-gray-600">Kelola semua kontrak sewa penyewa</p>
            </div>
            <button wire:click="openCreateModal"
                class="inline-flex items-center justify-center gap-2 px-6 py-3 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white font-semibold rounded-xl transition duration-300 transform hover:scale-105 active:scale-95 shadow-lg hover:shadow-xl whitespace-nowrap">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                Buat Kontrak Baru
            </button>
        </div>
    </div>

    <!-- Flash Message -->
    @if ($successMessage)
        <div class="mb-6 p-4 bg-green-50 border border-green-200 rounded-xl text-green-800 animate-fade-in shadow-md"
            x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)" @click.away="show = false">
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd"
                            d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                            clip-rule="evenodd"></path>
                    </svg>
                    {{ $successMessage }}
                </div>
                <button @click="show = false" class="text-green-600 hover:text-green-800">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd"
                            d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                            clip-rule="evenodd"></path>
                    </svg>
                </button>
            </div>
        </div>
    @endif

    <!-- Error Flash Message -->
    @if ($errorMessage)
        <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-xl text-red-800 animate-fade-in shadow-md"
            x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)" @click.away="show = false">
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd"
                            d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                            clip-rule="evenodd"></path>
                    </svg>
                    {{ $errorMessage }}
                </div>
                <button @click="show = false" class="text-red-600 hover:text-red-800">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd"
                            d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                            clip-rule="evenodd"></path>
                    </svg>
                </button>
            </div>
        </div>
    @endif

    <!-- Filters -->
    <div class="mb-6 grid grid-cols-1 md:grid-cols-2 gap-4">
        <!-- Search -->
        <div class="relative group">
            <svg class="absolute left-4 top-3.5 w-5 h-5 text-gray-400 group-focus-within:text-blue-500 transition"
                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
            </svg>
            <input wire:model.live="search" type="text" placeholder="Cari nama penyewa atau ruangan..."
                class="w-full pl-12 pr-4 py-3 bg-white border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition duration-300 shadow-sm hover:shadow-md">
        </div>

        <!-- Filter by Status -->
        <select wire:model.live="filterStatus"
            class="px-4 py-3 bg-white border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition duration-300 shadow-sm hover:shadow-md">
            <option value="">-- Semua Status --</option>
            <option value="pending">Tertunda</option>
            <option value="active">Aktif</option>
            <option value="completed">Selesai</option>
            <option value="terminated">Dibatalkan</option>
            <option value="cancelled">Dibatalkan</option>
        </select>
    </div>

    <!-- Leases Table -->
    <div class="bg-white rounded-2xl shadow-lg overflow-hidden hover:shadow-xl transition duration-300">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gradient-to-r from-gray-50 to-gray-100">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wide">
                            Penyewa
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wide">
                            Ruangan
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wide">
                            Tanggal
                            Mulai</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wide">
                            Tanggal
                            Selesai</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wide">
                            Deposit
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wide">
                            Status
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wide">Aksi
                        </th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse ($leases as $lease)
                        <tr class="hover:bg-blue-50 transition duration-200">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div
                                        class="w-10 h-10 rounded-lg bg-gradient-to-br from-blue-100 to-indigo-100 flex items-center justify-center font-semibold text-blue-600 shadow-sm">
                                        {{ substr($lease->tenant->display_name, 0, 1) }}
                                    </div>
                                    <div class="ml-3">
                                        <p class="font-semibold text-gray-900">{{ $lease->tenant->display_name }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <p class="text-gray-900 font-semibold">{{ $lease->room->room_number }}</p>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <p class="text-gray-600 text-sm">
                                    {{ is_string($lease->start_date) ? \Carbon\Carbon::parse($lease->start_date)->format('d/m/Y') : $lease->start_date->format('d/m/Y') }}
                                </p>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <p class="text-gray-600 text-sm">
                                    {{ is_string($lease->end_date) ? \Carbon\Carbon::parse($lease->end_date)->format('d/m/Y') : $lease->end_date->format('d/m/Y') }}
                                </p>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <p class="text-gray-900 font-semibold">Rp
                                    {{ number_format($lease->deposit_amount, 0, ',', '.') }}</p>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span
                                    class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium
                                @if ($lease->status === 'active') bg-green-100 text-green-800
                                @elseif ($lease->status === 'pending') bg-yellow-100 text-yellow-800
                                @elseif ($lease->status === 'completed') bg-blue-100 text-blue-800
                                @else bg-red-100 text-red-800 @endif">
                                    @if ($lease->status === 'active')
                                        <span class="w-2 h-2 rounded-full bg-green-500 mr-2"></span>Aktif
                                    @elseif ($lease->status === 'pending')
                                        <span class="w-2 h-2 rounded-full bg-yellow-500 mr-2"></span>Tertunda
                                    @elseif ($lease->status === 'completed')
                                        <span class="w-2 h-2 rounded-full bg-blue-500 mr-2"></span>Selesai
                                    @else
                                        <span class="w-2 h-2 rounded-full bg-red-500 mr-2"></span>Dibatalkan
                                    @endif
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex gap-2">
                                    <button wire:click="openDetailModal({{ $lease->id }})"
                                        class="inline-flex items-center gap-1 px-3 py-2 bg-indigo-50 text-indigo-600 rounded-lg hover:bg-indigo-100 transition font-medium text-sm border border-indigo-200 shadow-sm hover:shadow-md">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z">
                                            </path>
                                        </svg>
                                        Detail
                                    </button>
                                    <button wire:click="openEditModal({{ $lease->id }})"
                                        class="inline-flex items-center gap-1 px-3 py-2 bg-blue-50 text-blue-600 rounded-lg hover:bg-blue-100 transition font-medium text-sm border border-blue-200 shadow-sm hover:shadow-md">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
                                            </path>
                                        </svg>
                                        Edit
                                    </button>
                                    <button wire:click="openDeleteModal({{ $lease->id }})"
                                        class="inline-flex items-center gap-1 px-3 py-2 bg-red-50 text-red-600 rounded-lg hover:bg-red-100 transition font-medium text-sm border border-red-200 shadow-sm hover:shadow-md">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                            </path>
                                        </svg>
                                        Hapus
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center">
                                <svg class="mx-auto h-12 w-12 text-gray-400 mb-4" fill="none"
                                    stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M3 12a9 9 0 110 18 9 9 0 010-18z"></path>
                                </svg>
                                <p class="text-gray-500">Tidak ada kontrak ditemukan</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Pagination -->
    <div class="mt-8 relative z-40">
        {{ $leases->links('vendor.pagination.tailwind') }}
    </div>

    <!-- Modal -->
    @if ($showModal)
        <div class="fixed inset-0 bg-black/20 z-50 flex items-center justify-center p-4 backdrop-blur-sm">
            <div class="bg-white rounded-2xl shadow-2xl max-w-2xl w-full overflow-hidden animate-fade-in">
                <!-- Header -->
                <div class="bg-gradient-to-r from-blue-600 to-indigo-600 px-6 py-4 flex items-center justify-between">
                    <h2 class="text-lg font-bold text-white">
                        {{ $editingId ? 'Edit Kontrak' : 'Buat Kontrak Baru' }}
                    </h2>
                    <button wire:click="closeModal" class="text-white hover:bg-white/20 rounded-lg p-1 transition">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                <!-- Form -->
                <div class="p-6">
                    <livewire:lease.lease-form :leaseId="$editingId" wire:key="form-{{ $editingId }}" />
                </div>
            </div>
        </div>
    @endif

    <!-- Delete Confirmation Modal -->
    @if ($showDeleteModal && $deletingLease)
        <div class="fixed inset-0 bg-black/20 z-50 flex items-center justify-center p-4 backdrop-blur-sm">
            <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full overflow-hidden animate-fade-in">
                <!-- Header -->
                <div class="bg-gradient-to-r from-red-600 to-pink-600 px-6 py-4">
                    <h2 class="text-lg font-bold text-white flex items-center gap-3">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8v4m0 4v.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Hapus Kontrak?
                    </h2>
                </div>

                <!-- Content -->
                <div class="p-6">
                    <p class="text-gray-700 mb-3 font-medium">Apakah Anda yakin ingin menghapus kontrak berikut?</p>
                    <div
                        class="bg-gradient-to-br from-red-50 to-pink-50 p-4 rounded-xl mb-6 border-l-4 border-red-500">
                        <p class="text-sm text-gray-600">Penyewa</p>
                        <p class="text-lg font-bold text-gray-900">{{ $deletingLease->tenant->display_name }}</p>
                        <p class="text-sm text-gray-600 mt-2">Ruangan</p>
                        <p class="text-gray-900 font-semibold">{{ $deletingLease->room->room_number }}</p>
                        <p class="text-sm text-gray-600 mt-2">Periode</p>
                        <p class="text-gray-900">
                            {{ is_string($deletingLease->start_date) ? \Carbon\Carbon::parse($deletingLease->start_date)->format('d/m/Y') : $deletingLease->start_date->format('d/m/Y') }}
                            -
                            {{ is_string($deletingLease->end_date) ? \Carbon\Carbon::parse($deletingLease->end_date)->format('d/m/Y') : $deletingLease->end_date->format('d/m/Y') }}
                        </p>
                    </div>
                    <p class="text-sm text-red-600 font-semibold mb-6">
                        Tindakan ini tidak dapat dibatalkan. Data kontrak akan dihapus secara permanen.
                    </p>
                </div>

                <!-- Footer -->
                <div class="bg-gray-50 px-6 py-4 flex gap-3">
                    <button wire:click="closeDeleteModal()"
                        class="flex-1 px-4 py-3 bg-gray-200 hover:bg-gray-300 text-gray-800 font-semibold rounded-xl transition duration-300 transform hover:scale-105 active:scale-95">
                        Batal
                    </button>
                    <button wire:click="confirmDelete()"
                        class="flex-1 px-4 py-3 bg-red-600 hover:bg-red-700 text-white font-semibold rounded-xl transition duration-300 transform hover:scale-105 active:scale-95">
                        Hapus
                    </button>
                </div>
            </div>
        </div>
    @endif

    <!-- Detail Modal -->
    @if ($showDetailModal && $detailingLease)
        <div class="fixed inset-0 bg-black/20 z-50 flex items-center justify-center p-4 backdrop-blur-sm">
            <div class="bg-white rounded-2xl shadow-2xl max-w-2xl w-full overflow-hidden animate-fade-in">
                <!-- Header -->
                <div class="bg-gradient-to-r from-indigo-600 to-blue-600 px-6 py-4 flex items-center justify-between">
                    <h2 class="text-lg font-bold text-white flex items-center gap-3">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Detail Kontrak Sewa
                    </h2>
                    <button wire:click="closeDetailModal"
                        class="text-white hover:bg-white/20 rounded-lg p-1 transition">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                <!-- Content -->
                <div class="p-6 grid grid-cols-1 sm:grid-cols-2 gap-6">
                    <!-- Penyewa Section -->
                    <div>
                        <h3 class="text-sm font-semibold text-gray-600 uppercase tracking-wide mb-2">Penyewa</h3>
                        <div class="flex items-center gap-3">
                            <div
                                class="w-10 h-10 rounded-lg bg-gradient-to-br from-blue-100 to-indigo-100 flex items-center justify-center font-semibold text-blue-600 shadow-sm">
                                {{ substr($detailingLease->tenant->display_name, 0, 1) }}
                            </div>
                            <div>
                                <p class="font-semibold text-gray-900">{{ $detailingLease->tenant->display_name }}</p>
                                <p class="text-sm text-gray-600">{{ $detailingLease->tenant->email ?? 'N/A' }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Properti Section -->
                    <div>
                        <h3 class="text-sm font-semibold text-gray-600 uppercase tracking-wide mb-2">Properti</h3>
                        <p class="font-semibold text-gray-900">
                            {{ $detailingLease->room->roomType->property->name ?? 'N/A' }}</p>
                        <p class="text-sm text-gray-600 mt-1">
                            {{ $detailingLease->room->roomType->property->address ?? 'N/A' }}</p>
                    </div>

                    <!-- Tipe Ruangan Section -->
                    <div>
                        <h3 class="text-sm font-semibold text-gray-600 uppercase tracking-wide mb-2">Tipe Ruangan</h3>
                        <p class="font-semibold text-gray-900">{{ $detailingLease->room->roomType->name ?? 'N/A' }}
                        </p>
                        <p class="text-sm text-gray-600 mt-1">Harga: Rp
                            {{ number_format($detailingLease->room->roomType->price ?? 0, 0, ',', '.') }}</p>
                    </div>

                    <!-- Ruangan Section -->
                    <div>
                        <h3 class="text-sm font-semibold text-gray-600 uppercase tracking-wide mb-2">Ruangan</h3>
                        <p class="font-semibold text-gray-900">Ruangan {{ $detailingLease->room->room_number }}</p>
                        <p class="text-sm text-gray-600 mt-1">Lantai {{ $detailingLease->room->floor ?? 'N/A' }}</p>
                    </div>

                    <!-- Tanggal Mulai Section -->
                    <div>
                        <h3 class="text-sm font-semibold text-gray-600 uppercase tracking-wide mb-2">Tanggal Mulai</h3>
                        <p class="font-semibold text-gray-900">
                            {{ is_string($detailingLease->start_date) ? \Carbon\Carbon::parse($detailingLease->start_date)->format('d/m/Y') : $detailingLease->start_date->format('d/m/Y') }}
                        </p>
                    </div>

                    <!-- Tanggal Selesai Section -->
                    <div>
                        <h3 class="text-sm font-semibold text-gray-600 uppercase tracking-wide mb-2">Tanggal Selesai
                        </h3>
                        <p class="font-semibold text-gray-900">
                            {{ is_string($detailingLease->end_date) ? \Carbon\Carbon::parse($detailingLease->end_date)->format('d/m/Y') : $detailingLease->end_date->format('d/m/Y') }}
                        </p>
                    </div>

                    <!-- Deposit Section -->
                    <div>
                        <h3 class="text-sm font-semibold text-gray-600 uppercase tracking-wide mb-2">Deposit</h3>
                        <p class="font-semibold text-gray-900">Rp
                            {{ number_format($detailingLease->deposit_amount, 0, ',', '.') }}</p>
                    </div>

                    <!-- Status Section -->
                    <div>
                        <h3 class="text-sm font-semibold text-gray-600 uppercase tracking-wide mb-2">Status</h3>
                        <span
                            class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium
                        @if ($detailingLease->status === 'active') bg-green-100 text-green-800
                        @elseif ($detailingLease->status === 'pending') bg-yellow-100 text-yellow-800
                        @elseif ($detailingLease->status === 'completed') bg-blue-100 text-blue-800
                        @else bg-red-100 text-red-800 @endif">
                            @if ($detailingLease->status === 'active')
                                <span class="w-2 h-2 rounded-full bg-green-500 mr-2"></span>Aktif
                            @elseif ($detailingLease->status === 'pending')
                                <span class="w-2 h-2 rounded-full bg-yellow-500 mr-2"></span>Tertunda
                            @elseif ($detailingLease->status === 'completed')
                                <span class="w-2 h-2 rounded-full bg-blue-500 mr-2"></span>Selesai
                            @else
                                <span class="w-2 h-2 rounded-full bg-red-500 mr-2"></span>Dibatalkan
                            @endif
                        </span>
                    </div>

                    <!-- Due Date Per Month Section -->
                    <div>
                        <h3 class="text-sm font-semibold text-gray-600 uppercase tracking-wide mb-2">Jatuh Tempo Per
                            Bulan</h3>
                        <p class="font-semibold text-gray-900">Tanggal {{ $detailingLease->due_date_per_month }}</p>
                    </div>

                    <!-- Created/Updated Section -->
                    <div class="col-span-1 sm:col-span-2 pt-4 border-t border-gray-200">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
                            <div>
                                <p class="text-gray-600">Dibuat Oleh</p>
                                <p class="font-semibold text-gray-900">{{ $detailingLease->creator->name ?? 'N/A' }}
                                </p>
                                <p class="text-gray-500 text-xs">
                                    {{ $detailingLease->created_at?->format('d/m/Y H:i') ?? '-' }}</p>
                            </div>
                            <div>
                                <p class="text-gray-600">Diubah Oleh</p>
                                <p class="font-semibold text-gray-900">{{ $detailingLease->updater->name ?? 'N/A' }}
                                </p>
                                <p class="text-gray-500 text-xs">
                                    {{ $detailingLease->updated_at?->format('d/m/Y H:i') ?? '-' }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Footer -->
                <div class="bg-gray-50 px-6 py-4 flex gap-3">
                    <button wire:click="closeDetailModal()"
                        class="flex-1 px-4 py-3 bg-gray-600 hover:bg-gray-700 text-white font-semibold rounded-xl transition duration-300 transform hover:scale-105 active:scale-95">
                        Tutup
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
