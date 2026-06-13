<div>
    <!-- Header -->
    <div class="mb-8">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1
                    class="text-2xl sm:text-4xl font-bold bg-gradient-to-r from-orange-600 to-orange-600 bg-clip-text text-transparent">
                    Ruangan</h1>
                <p class="mt-2 text-gray-600">Kelola semua ruangan di properti Anda</p>
            </div>
            <button wire:click="openCreateModal"
                class="inline-flex items-center justify-center gap-2 px-6 py-3 bg-gradient-to-r from-orange-600 to-orange-600 hover:from-orange-700 hover:to-orange-700 text-white font-semibold rounded-xl transition duration-300 transform hover:scale-105 active:scale-95 shadow-lg hover:shadow-xl whitespace-nowrap">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                Tambah Ruangan
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
    <div class="mb-6 grid grid-cols-1 md:grid-cols-3 gap-4">
        <!-- Search -->
        <div class="relative group">
            <svg class="absolute left-4 top-3.5 w-5 h-5 text-gray-400 group-focus-within:text-orange-500 transition"
                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
            </svg>
            <input wire:model.live="search" type="text" placeholder="Cari nomor ruangan..."
                class="w-full pl-12 pr-4 py-3 bg-white border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent transition duration-300 shadow-sm hover:shadow-md">
        </div>

        <!-- Filter by Property -->
        <select wire:model.live="filterProperty"
            class="px-4 py-3 bg-white border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent transition duration-300 shadow-sm hover:shadow-md">
            <option value="">-- Semua Property --</option>
            @foreach ($properties as $property)
                <option value="{{ $property->id }}">{{ $property->name }}</option>
            @endforeach
        </select>

        <!-- Filter by Status -->
        <select wire:model.live="filterStatus"
            class="px-4 py-3 bg-white border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent transition duration-300 shadow-sm hover:shadow-md">
            <option value="">-- Semua Status --</option>
            <option value="available">Tersedia</option>
            <option value="maintenance">Maintenance</option>
        </select>
    </div>

    <!-- Rooms Table -->
    <div class="bg-white rounded-2xl shadow-lg overflow-hidden hover:shadow-xl transition duration-300">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gradient-to-r from-gray-50 to-gray-100">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wide">
                            Ruangan
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wide">
                            Lantai
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wide">
                            Tipe
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wide">
                            Property
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wide">
                            Harga/Bulan
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wide">
                            Status
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wide">Aksi
                        </th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse ($rooms as $room)
                        <tr class="hover:bg-orange-50 transition duration-200">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div
                                        class="w-10 h-10 rounded-lg bg-gradient-to-br from-orange-100 to-orange-100 flex items-center justify-center font-semibold text-orange-600 shadow-sm">
                                        {{ substr($room->room_number, 0, 1) }}
                                    </div>
                                    <div class="ml-3">
                                        <p class="font-semibold text-gray-900">{{ $room->room_number }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span
                                    class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-purple-100 text-purple-800">
                                    Lt. {{ $room->floor }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <p class="text-gray-900 font-medium">{{ $room->roomType->name }}</p>
                            </td>
                            <td class="px-6 py-4">
                                <p class="text-gray-600 text-sm">{{ $room->roomType->property->name }}</p>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <p class="text-gray-900 font-semibold">Rp
                                    {{ number_format($room->roomType->price, 0, ',', '.') }}</p>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span
                                    class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium
                                @if ($room->computed_status === 'available') bg-green-100 text-green-800
                                @elseif ($room->computed_status === 'occupied')
                                    bg-red-100 text-red-800
                                @else
                                    bg-yellow-100 text-yellow-800 @endif">
                                    @if ($room->computed_status === 'available')
                                        <span class="w-2 h-2 rounded-full bg-green-500 mr-2"></span>Tersedia
                                    @elseif ($room->computed_status === 'occupied')
                                        <span class="w-2 h-2 rounded-full bg-red-500 mr-2"></span>Tidak Tersedia
                                    @else
                                        <span class="w-2 h-2 rounded-full bg-yellow-500 mr-2"></span>Maintenance
                                    @endif
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex gap-2">
                                    <button wire:click="openEditModal({{ $room->id }})"
                                        class="inline-flex items-center gap-1 px-3 py-2 bg-orange-50 text-orange-600 rounded-lg hover:bg-orange-100 transition font-medium text-sm border border-orange-200 shadow-sm hover:shadow-md">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
                                            </path>
                                        </svg>
                                        Edit
                                    </button>
                                    <button wire:click="openDeleteModal({{ $room->id }})"
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
                                <p class="text-gray-500">Tidak ada ruangan ditemukan</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Pagination -->
    <div class="mt-8 relative z-40">
        {{ $rooms->links('vendor.pagination.tailwind') }}
    </div>

    <!-- Modal -->
    @if ($showModal)
        <div class="fixed inset-0 bg-black/20 z-50 flex items-center justify-center p-4 backdrop-blur-sm">
            <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full overflow-y-auto max-h-[90vh] animate-fade-in">
                <!-- Header -->
                <div class="bg-gradient-to-r from-orange-600 to-orange-600 px-6 py-4 flex items-center justify-between">
                    <h2 class="text-lg font-bold text-white">
                        {{ $editingId ? 'Edit Ruangan' : 'Tambah Ruangan Baru' }}
                    </h2>
                    <button wire:click="closeModal" class="text-white hover:bg-white/20 rounded-lg p-1 transition">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12">
                            </path>
                        </svg>
                    </button>
                </div>

                <!-- Form -->
                <div class="p-6">
                    <livewire:room.room-form :roomId="$editingId" wire:key="form-{{ $editingId }}" />
                </div>
            </div>
        </div>
    @endif

    <!-- Delete Confirmation Modal -->
    @if ($showDeleteModal && $deletingRoom)
        <div class="fixed inset-0 bg-black/20 z-50 flex items-center justify-center p-4 backdrop-blur-sm">
            <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full overflow-y-auto max-h-[90vh] animate-fade-in">
                <!-- Header -->
                <div class="bg-gradient-to-r from-red-600 to-pink-600 px-6 py-4">
                    <h2 class="text-lg font-bold text-white flex items-center gap-3">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8v4m0 4v.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Hapus Ruangan?
                    </h2>
                </div>

                <!-- Content -->
                <div class="p-6">
                    <p class="text-gray-700 mb-3 font-medium">Apakah Anda yakin ingin menghapus ruangan berikut?</p>
                    <div
                        class="bg-gradient-to-br from-red-50 to-pink-50 p-4 rounded-xl mb-6 border-l-4 border-red-500">
                        <p class="text-sm text-gray-600">Nomor Ruangan</p>
                        <p class="text-lg font-bold text-gray-900">{{ $deletingRoom->room_number }}</p>
                        <p class="text-sm text-gray-600 mt-2">Tipe</p>
                        <p class="text-gray-900">{{ $deletingRoom->roomType->name }}</p>
                        <p class="text-sm text-gray-600 mt-2">Property</p>
                        <p class="text-gray-900">{{ $deletingRoom->roomType->property->name }}</p>
                    </div>
                    <p class="text-sm text-red-600 font-semibold mb-6">
                        Tindakan ini tidak dapat dibatalkan. Data ruangan akan dihapus secara permanen.
                    </p>
                </div>

                <!-- Footer -->
                <div class="bg-gray-50 px-6 py-4 flex gap-3">
                    <button wire:click="closeDeleteModal()"
                        class="flex-1 px-4 py-3 bg-gray-200 hover:bg-gray-300 text-gray-800 font-semibold rounded-xl transition duration-300 transform hover:scale-105 active:scale-95">
                        Batal
                    </button>
                    <button wire:click="confirmDelete()"
                        class="flex-1 px-4 py-3 bg-gradient-to-r from-red-600 to-pink-600 hover:from-red-700 hover:to-pink-700 text-white font-semibold rounded-xl transition duration-300 transform hover:scale-105 active:scale-95 flex items-center justify-center gap-2 shadow-lg"
                        wire:loading.attr="disabled" wire:loading.class="opacity-50">
                        <span wire:loading.remove>
                            Hapus Sekarang
                        </span>
                        <span wire:loading class="flex items-center gap-2">
                            <svg class="w-4 h-4 animate-spin" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15">
                                </path>
                            </svg>
                            Menghapus...
                        </span>
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>

<style>
    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(-10px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
</style>
