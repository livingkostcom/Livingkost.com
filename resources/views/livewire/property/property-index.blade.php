<div>
    <!-- Header -->
    <div class="mb-8">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1
                    class="text-2xl sm:text-4xl font-bold bg-gradient-to-r from-orange-600 to-orange-600 bg-clip-text text-transparent">
                    Properti</h1>
                <p class="mt-2 text-gray-600">Kelola semua properti Anda dengan mudah</p>
            </div>
            <button wire:click="openCreateModal"
                class="inline-flex items-center justify-center gap-2 px-6 py-3 bg-gradient-to-r from-orange-600 to-orange-600 hover:from-orange-700 hover:to-orange-700 text-white font-semibold rounded-xl transition duration-300 transform hover:scale-105 active:scale-95 shadow-lg hover:shadow-xl whitespace-nowrap">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                Tambah Properti
            </button>
        </div>
    </div>

    <!-- Flash Message -->
    @if (session('message'))
        <div class="mb-6 p-4 bg-green-50 border border-green-200 rounded-xl text-green-800 animate-fade-in shadow-md">
            <div class="flex items-center">
                <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd"
                        d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                        clip-rule="evenodd"></path>
                </svg>
                {{ session('message') }}
            </div>
        </div>
    @endif

    <!-- Search Box -->
    <div class="mb-6">
        <div class="relative group">
            <svg class="absolute left-4 top-3.5 w-5 h-5 text-gray-400 group-focus-within:text-orange-500 transition"
                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
            </svg>
            <input wire:model.live="search" type="text" placeholder="Cari nama atau alamat property..."
                class="w-full pl-12 pr-4 py-3 bg-white border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent transition duration-300 shadow-sm hover:shadow-md">
        </div>
    </div>

    <!-- Properties Table -->
    <div class="bg-white rounded-2xl shadow-lg overflow-hidden hover:shadow-xl transition duration-300">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gradient-to-r from-gray-50 to-gray-100">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wide">Nama
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wide">
                            Alamat
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wide">
                            Ruangan
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wide">
                            Status
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wide">Aksi
                        </th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse ($properties as $property)
                        <tr class="hover:bg-orange-50 transition duration-200">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div
                                        class="w-10 h-10 rounded-lg bg-gradient-to-br from-orange-100 to-orange-100 flex items-center justify-center font-semibold text-orange-600 shadow-sm">
                                        {{ substr($property->name, 0, 1) }}
                                    </div>
                                    <div class="ml-3">
                                        <p class="font-semibold text-gray-900">{{ $property->name }}</p>
                                        @if ($property->gender_type)
                                            <span class="inline-block mt-1 px-2 py-0.5 rounded-full text-xs font-semibold
                                                {{ $property->gender_type === 'putra' ? 'bg-blue-100 text-blue-700' : ($property->gender_type === 'putri' ? 'bg-pink-100 text-pink-700' : 'bg-purple-100 text-purple-700') }}">
                                                {{ $property->gender_type === 'putra' ? 'Putra' : ($property->gender_type === 'putri' ? 'Putri' : 'Putra & Putri') }}
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <p class="text-gray-600 text-sm">{{ $property->address }}</p>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span
                                    class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-sm font-medium bg-orange-100 text-orange-800">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M3 12l2-3m0 0l7-4 7 4M5 9v10a1 1 0 001 1h2V9m4 0v10h2V9m4-6v10a1 1 0 001 1h2V3">
                                        </path>
                                    </svg>
                                    {{ $property->total_rooms ?? 0 }} Ruangan
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span
                                    class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium @if ($property->status === 'active') bg-green-100 text-green-800 @else bg-gray-100 text-gray-800 @endif">
                                    @if ($property->status === 'active')
                                        <span class="w-2 h-2 rounded-full bg-green-500 mr-2"></span>Aktif
                                    @else
                                        <span class="w-2 h-2 rounded-full bg-gray-500 mr-2"></span>Non-Aktif
                                    @endif
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex gap-2">
                                    <button wire:click="openEditModal({{ $property->id }})"
                                        class="inline-flex items-center gap-1 px-3 py-2 bg-orange-50 text-orange-600 rounded-lg hover:bg-orange-100 transition font-medium text-sm border border-orange-200 shadow-sm hover:shadow-md">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
                                            </path>
                                        </svg>
                                        Edit
                                    </button>
                                    <button wire:click="openDeleteModal({{ $property->id }})"
                                        class="inline-flex items-center gap-1 px-3 py-2 bg-red-50 text-red-600 rounded-lg hover:bg-red-100 transition font-medium text-sm border border-red-200 shadow-sm hover:shadow-md">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
                            <td colspan="5" class="px-6 py-12 text-center">
                                <svg class="mx-auto h-12 w-12 text-gray-400 mb-4" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M3 12a9 9 0 110 18 9 9 0 010-18z"></path>
                                </svg>
                                <p class="text-gray-500">Tidak ada property ditemukan</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Pagination -->
    <div class="mt-8">
        {{ $properties->links('vendor.pagination.tailwind') }}
    </div>

    <!-- Modal -->
    @if ($showModal)
        <div class="fixed inset-0 bg-black/20 z-50 flex items-center justify-center p-4 backdrop-blur-sm">
            <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full overflow-y-auto max-h-[90vh] animate-fade-in">
                <!-- Header -->
                <div class="bg-gradient-to-r from-orange-600 to-orange-600 px-6 py-4 flex items-center justify-between">
                    <h2 class="text-lg font-bold text-white">
                        {{ $editingId ? 'Edit Property' : 'Tambah Property Baru' }}
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
                    <livewire:property.property-form :propertyId="$editingId" wire:key="form-{{ $editingId }}" />
                </div>
            </div>
        </div>
    @endif

    <!-- Delete Confirmation Modal -->
    @if ($showDeleteModal && $deletingProperty)
        <div class="fixed inset-0 bg-black/20 z-50 flex items-center justify-center p-4 backdrop-blur-sm">
            <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full overflow-y-auto max-h-[90vh] animate-fade-in">
                <!-- Header -->
                <div class="bg-gradient-to-r from-red-600 to-pink-600 px-6 py-4">
                    <h2 class="text-lg font-bold text-white flex items-center gap-3">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8v4m0 4v.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Hapus Property?
                    </h2>
                </div>

                <!-- Content -->
                <div class="p-6">
                    <p class="text-gray-700 mb-3 font-medium">Apakah Anda yakin ingin menghapus property berikut?</p>
                    <div
                        class="bg-gradient-to-br from-red-50 to-pink-50 p-4 rounded-xl mb-6 border-l-4 border-red-500">
                        <p class="text-sm text-gray-600">Nama Property</p>
                        <p class="text-lg font-bold text-gray-900">{{ $deletingProperty->name }}</p>
                        <p class="text-sm text-gray-600 mt-2">Alamat</p>
                        <p class="text-gray-900">{{ $deletingProperty->address }}</p>
                    </div>
                    <p class="text-sm text-red-600 font-semibold mb-6">
                        Tindakan ini tidak dapat dibatalkan. Data property akan dihapus secara permanen.
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

    .animate-fade-in {
        animation: fadeIn 0.3s ease-out;
    }

    .line-clamp-2 {
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
</style>
