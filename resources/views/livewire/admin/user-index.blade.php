@php
    $roleBadges = [
        'super-admin' => ['label' => 'Super Admin', 'class' => 'bg-purple-100 text-purple-800'],
        'owner' => ['label' => 'Owner', 'class' => 'bg-orange-100 text-orange-800'],
        'manager' => ['label' => 'Manajer', 'class' => 'bg-blue-100 text-blue-800'],
        'tenant' => ['label' => 'Penyewa', 'class' => 'bg-green-100 text-green-800'],
    ];
@endphp
<div>
    <!-- Header -->
    <div class="mb-8">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl sm:text-4xl font-bold text-orange-600">Kelola User</h1>
                <p class="mt-2 text-gray-600">
                    {{ $isSuperAdmin ? 'Kelola semua akun: owner, manajer, dan penyewa.' : 'Kelola manajer & penyewa di properti Anda.' }}
                </p>
            </div>
            <button wire:click="openCreateModal"
                class="inline-flex items-center justify-center gap-2 px-6 py-3 bg-orange-600 hover:bg-orange-700 text-white font-semibold rounded-xl transition duration-300 transform hover:scale-105 active:scale-95 shadow-lg hover:shadow-xl whitespace-nowrap">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                Tambah User
            </button>
        </div>
    </div>

    <!-- Flash Message -->
    @if (session('message'))
        <div class="mb-6 p-4 bg-green-50 border border-green-200 rounded-xl text-green-800 shadow-md">
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

    <!-- Search -->
    <div class="mb-6">
        <div class="relative group">
            <svg class="absolute left-4 top-3.5 w-5 h-5 text-gray-400 group-focus-within:text-orange-500 transition"
                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
            </svg>
            <input wire:model.live="search" type="text" placeholder="Cari nama atau email user..."
                class="w-full pl-12 pr-4 py-3 bg-white border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent transition duration-300 shadow-sm hover:shadow-md">
        </div>
    </div>

    <!-- Table -->
    <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gradient-to-r from-gray-50 to-gray-100">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wide whitespace-nowrap">Nama</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wide whitespace-nowrap">Email</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wide whitespace-nowrap">Role</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wide whitespace-nowrap">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse ($users as $user)
                        @php $roleName = $user->getRoleNames()->first(); @endphp
                        <tr class="hover:bg-orange-50 transition duration-200">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="w-10 h-10 rounded-lg bg-gradient-to-br from-orange-100 to-orange-100 flex items-center justify-center font-semibold text-orange-600 shadow-sm">
                                        {{ strtoupper(substr($user->name, 0, 1)) }}
                                    </div>
                                    <div class="ml-3">
                                        <p class="font-semibold text-gray-900">{{ $user->name }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <p class="text-gray-600 text-sm">{{ $user->email }}</p>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium {{ $roleBadges[$roleName]['class'] ?? 'bg-gray-100 text-gray-800' }}">
                                    {{ $roleBadges[$roleName]['label'] ?? ($roleName ?? '-') }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex gap-2">
                                    @can('update', $user)
                                        <button wire:click="openEditModal({{ $user->id }})"
                                            class="inline-flex items-center p-2 bg-orange-50 text-orange-600 rounded-lg hover:bg-orange-100 transition font-medium text-sm border border-orange-200 shadow-sm" title="Edit">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                            </svg>
                                        </button>
                                    @endcan
                                    @can('delete', $user)
                                        <button wire:click="openDeleteModal({{ $user->id }})"
                                            class="inline-flex items-center p-2 bg-red-50 text-red-600 rounded-lg hover:bg-red-100 transition font-medium text-sm border border-red-200 shadow-sm" title="Hapus">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                            </svg>
                                        </button>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-12 text-center">
                                <p class="text-gray-500">Belum ada user.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Pagination -->
    <div class="mt-8">
        {{ $users->links('vendor.pagination.tailwind') }}
    </div>

    <!-- Create/Edit Modal -->
    @if ($showModal)
        <div class="fixed inset-0 bg-black/20 z-50 flex items-center justify-center p-4 backdrop-blur-sm">
            <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full max-h-[90vh] overflow-y-auto">
                <div class="bg-orange-600 px-6 py-4 flex items-center justify-between">
                    <h2 class="text-lg font-bold text-white">{{ $editingId ? 'Edit User' : 'Tambah User Baru' }}</h2>
                    <button wire:click="closeModal" class="text-white hover:bg-white/20 rounded-lg p-1 transition">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
                <div class="p-6">
                    <livewire:admin.user-form :userId="$editingId" wire:key="user-form-{{ $editingId }}" />
                </div>
            </div>
        </div>
    @endif

    <!-- Delete Modal -->
    @if ($showDeleteModal && $deletingUser)
        <div class="fixed inset-0 bg-black/20 z-50 flex items-center justify-center p-4 backdrop-blur-sm">
            <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full max-h-[90vh] overflow-y-auto">
                <div class="bg-gradient-to-r from-red-600 to-pink-600 px-6 py-4">
                    <h2 class="text-lg font-bold text-white flex items-center gap-3">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8v4m0 4v.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Hapus User?
                    </h2>
                </div>
                <div class="p-6">
                    <p class="text-gray-700 mb-3 font-medium">Yakin ingin menghapus user berikut?</p>
                    <div class="bg-gradient-to-br from-red-50 to-pink-50 p-4 rounded-xl mb-6 border-l-4 border-red-500">
                        <p class="text-lg font-bold text-gray-900">{{ $deletingUser->name }}</p>
                        <p class="text-gray-900">{{ $deletingUser->email }}</p>
                    </div>
                    <p class="text-sm text-red-600 font-semibold mb-6">Tindakan ini tidak dapat dibatalkan.</p>
                </div>
                <div class="bg-gray-50 px-6 py-4 flex gap-3">
                    <button wire:click="closeDeleteModal()"
                        class="flex-1 px-4 py-3 bg-gray-200 hover:bg-gray-300 text-gray-800 font-semibold rounded-xl transition">
                        Batal
                    </button>
                    <button wire:click="confirmDelete()"
                        class="flex-1 px-4 py-3 bg-gradient-to-r from-red-600 to-pink-600 hover:from-red-700 hover:to-pink-700 text-white font-semibold rounded-xl transition flex items-center justify-center gap-2 shadow-lg"
                        wire:loading.attr="disabled" wire:loading.class="opacity-50">
                        <span wire:loading.remove>Hapus Sekarang</span>
                        <span wire:loading>Menghapus...</span>
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
