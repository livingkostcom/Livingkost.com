@php
    $isIncome = $type === 'income';
    $title = $isIncome ? 'Pendapatan Lain' : 'Pengeluaran Perusahaan';
    $subtitle = $isIncome
        ? 'Pendapatan platform di luar fee (mis. iklan, kerja sama, dll).'
        : 'Pengeluaran operasional perusahaan (mis. server, domain, gaji, dll).';
    $accent = $isIncome ? 'emerald' : 'red';
    $addLabel = $isIncome ? 'Tambah Pendapatan' : 'Tambah Pengeluaran';
@endphp
<div>
    <!-- Header -->
    <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl sm:text-4xl font-bold text-orange-600">{{ $title }}</h1>
            <p class="mt-2 text-gray-600">{{ $subtitle }}</p>
        </div>
        <button wire:click="openCreate"
            class="inline-flex items-center justify-center gap-2 px-6 py-3 bg-orange-600 hover:bg-orange-700 text-white font-semibold rounded-xl transition shadow-lg whitespace-nowrap">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            {{ $addLabel }}
        </button>
    </div>

    @if ($successMessage)
        <div class="mb-6 p-4 bg-green-50 border border-green-200 rounded-xl text-green-800 shadow-sm"
            x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)">{{ $successMessage }}</div>
    @endif

    <!-- Total this month -->
    <div class="mb-6 bg-white rounded-2xl shadow-sm border border-{{ $accent }}-200 p-5">
        <p class="text-sm text-gray-500">Total {{ $title }} Bulan Ini</p>
        <p class="text-3xl font-bold text-{{ $accent }}-600 mt-1">Rp {{ number_format($totalThisMonth, 0, ',', '.') }}</p>
    </div>

    <!-- Table -->
    <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gradient-to-r from-gray-50 to-gray-100">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wide whitespace-nowrap">Tanggal</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wide whitespace-nowrap">Keterangan</th>
                        <th class="px-6 py-4 text-right text-xs font-semibold text-gray-700 uppercase tracking-wide whitespace-nowrap">Jumlah</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wide whitespace-nowrap">Catatan</th>
                        <th class="px-6 py-4 text-center text-xs font-semibold text-gray-700 uppercase tracking-wide whitespace-nowrap">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse ($rows as $r)
                        <tr class="hover:bg-orange-50 transition">
                            <td class="px-6 py-4 text-sm text-gray-600 whitespace-nowrap">{{ $r->transaction_date->translatedFormat('d M Y') }}</td>
                            <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ $r->description }}</td>
                            <td class="px-6 py-4 text-right font-bold text-{{ $accent }}-600 whitespace-nowrap">Rp {{ number_format($r->amount, 0, ',', '.') }}</td>
                            <td class="px-6 py-4 text-sm text-gray-500">{{ $r->notes ?: '-' }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center justify-center gap-2">
                                    <button wire:click="openEdit({{ $r->id }})" title="Edit"
                                        class="inline-flex items-center p-2 bg-orange-50 text-orange-600 rounded-lg hover:bg-orange-100 border border-orange-200">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    </button>
                                    <button wire:click="confirmDelete({{ $r->id }})" title="Hapus"
                                        class="inline-flex items-center p-2 bg-red-50 text-red-600 rounded-lg hover:bg-red-100 border border-red-200">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="px-6 py-12 text-center text-gray-500">Belum ada data.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4">{{ $rows->links('vendor.pagination.tailwind') }}</div>
    </div>

    <!-- Form Modal -->
    @if ($showModal)
        <div class="fixed inset-0 bg-black/40 z-50 flex items-center justify-center p-4 backdrop-blur-sm">
            <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full overflow-y-auto max-h-[90vh]">
                <div class="bg-orange-600 px-6 py-4 flex items-center justify-between">
                    <h2 class="text-lg font-bold text-white">{{ $editingId ? 'Edit' : $addLabel }}</h2>
                    <button wire:click="closeModal" class="text-white/80 hover:text-white">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
                <div class="p-6 space-y-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Keterangan</label>
                        <input wire:model="description" type="text" placeholder="{{ $isIncome ? 'mis. Kerja sama iklan' : 'mis. Sewa server bulanan' }}"
                            class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500">
                        @error('description') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">Jumlah (Rp)</label>
                            <input wire:model="amount" type="number" min="1" class="w-full px-3 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500">
                            @error('amount') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">Tanggal</label>
                            <input wire:model="transactionDate" type="date" class="w-full px-3 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500">
                            @error('transactionDate') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Catatan (opsional)</label>
                        <input wire:model="notes" type="text" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500">
                    </div>
                </div>
                <div class="bg-gray-50 px-6 py-4 flex gap-3">
                    <button wire:click="closeModal" class="flex-1 px-4 py-2.5 bg-gray-200 hover:bg-gray-300 text-gray-800 font-semibold rounded-xl">Batal</button>
                    <button wire:click="save" class="flex-1 px-4 py-2.5 bg-orange-600 hover:bg-orange-700 text-white font-semibold rounded-xl">Simpan</button>
                </div>
            </div>
        </div>
    @endif

    <!-- Delete Modal -->
    @if ($showDeleteModal)
        <div class="fixed inset-0 bg-black/40 z-50 flex items-center justify-center p-4 backdrop-blur-sm">
            <div class="bg-white rounded-2xl shadow-2xl max-w-sm w-full p-6">
                <p class="text-gray-800 font-medium mb-6">Yakin ingin menghapus data ini?</p>
                <div class="flex gap-3">
                    <button wire:click="$set('showDeleteModal', false)" class="flex-1 px-4 py-2.5 bg-gray-200 hover:bg-gray-300 text-gray-800 font-semibold rounded-xl">Batal</button>
                    <button wire:click="delete" class="flex-1 px-4 py-2.5 bg-red-600 hover:bg-red-700 text-white font-semibold rounded-xl">Hapus</button>
                </div>
            </div>
        </div>
    @endif
</div>
