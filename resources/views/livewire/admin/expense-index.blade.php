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
                    Pencatatan Pengeluaran
                </h1>
                <p class="mt-2 text-gray-600">Kelola semua pengeluaran operasional kos</p>
            </div>
            <button wire:click="openCreateModal"
                class="inline-flex items-center gap-2 px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-xl shadow-sm transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                Tambah Pengeluaran
            </button>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 mb-6">
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
            <div class="flex items-center gap-3 mb-2">
                <div class="p-2.5 bg-red-100 rounded-xl">
                    <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M13 17h8m0 0V9m0 8l-8-8-4 4-6-6"></path>
                    </svg>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Total Pengeluaran</p>
                    <p class="text-2xl font-bold text-gray-900">
                        Rp {{ number_format($summary['total'], 0, ',', '.') }}</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
            <div class="flex items-center gap-3 mb-2">
                <div class="p-2.5 bg-blue-100 rounded-xl">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2">
                        </path>
                    </svg>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Jumlah Transaksi</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $summary['count'] }}</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
            <div class="flex items-start gap-3">
                <div class="p-2.5 bg-amber-100 rounded-xl">
                    <svg class="w-6 h-6 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343">
                        </path>
                    </svg>
                </div>
                <div class="flex-1">
                    <p class="text-sm text-gray-500 mb-2">Per Kategori</p>
                    @forelse ($summary['by_category'] as $cat)
                        <div class="flex items-center justify-between text-xs mb-1">
                            <span
                                class="text-gray-600">{{ \App\Models\Expense::getCategoryLabel($cat->category) }}</span>
                            <span class="font-medium text-gray-800">
                                Rp {{ number_format($cat->total, 0, ',', '.') }}</span>
                        </div>
                    @empty
                        <p class="text-xs text-gray-400">Belum ada data</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 mb-6">
        <div class="flex flex-wrap gap-4 items-end">
            <div class="flex-1 min-w-[200px]">
                <label class="block text-xs font-medium text-gray-600 mb-1">Cari</label>
                <input type="text" wire:model.live.debounce.300ms="search" placeholder="Cari judul atau deskripsi..."
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition text-sm">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Bulan</label>
                <input type="month" wire:model.live="monthFilter"
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition text-sm">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Kategori</label>
                <select wire:model.live="categoryFilter"
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition text-sm">
                    <option value="">Semua</option>
                    <option value="maintenance">Perbaikan</option>
                    <option value="utility">Utilitas</option>
                    <option value="cleaning">Kebersihan</option>
                    <option value="supplies">Perlengkapan</option>
                    <option value="salary">Gaji</option>
                    <option value="tax">Pajak</option>
                    <option value="insurance">Asuransi</option>
                    <option value="other">Lainnya</option>
                </select>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Properti</label>
                <select wire:model.live="propertyFilter"
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition text-sm">
                    <option value="">Semua</option>
                    @foreach ($properties as $prop)
                        <option value="{{ $prop->id }}">{{ $prop->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>

    <!-- Table -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50 text-left text-xs text-gray-500 uppercase">
                        <th class="px-5 py-4 font-medium">Tanggal</th>
                        <th class="px-5 py-4 font-medium">Judul</th>
                        <th class="px-5 py-4 font-medium">Kategori</th>
                        <th class="px-5 py-4 font-medium">Properti</th>
                        <th class="px-5 py-4 font-medium">Jumlah</th>
                        <th class="px-5 py-4 font-medium text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse ($expenses as $expense)
                        <tr class="hover:bg-gray-50 transition cursor-pointer"
                            wire:click="showDetail({{ $expense->id }})">
                            <td class="px-5 py-4 text-gray-600">
                                {{ $expense->expense_date->translatedFormat('d M Y') }}
                            </td>
                            <td class="px-5 py-4">
                                <p class="font-medium text-gray-800">{{ $expense->title }}</p>
                                @if ($expense->description)
                                    <p class="text-xs text-gray-500 truncate max-w-[200px]">
                                        {{ $expense->description }}</p>
                                @endif
                            </td>
                            <td class="px-5 py-4">
                                @php
                                    $catColor = match ($expense->category) {
                                        'maintenance' => 'bg-orange-100 text-orange-700',
                                        'utility' => 'bg-blue-100 text-blue-700',
                                        'cleaning' => 'bg-teal-100 text-teal-700',
                                        'supplies' => 'bg-purple-100 text-purple-700',
                                        'salary' => 'bg-green-100 text-green-700',
                                        'tax' => 'bg-red-100 text-red-700',
                                        'insurance' => 'bg-indigo-100 text-indigo-700',
                                        default => 'bg-gray-100 text-gray-700',
                                    };
                                @endphp
                                <span class="text-xs px-2 py-1 rounded-full font-medium {{ $catColor }}">
                                    {{ \App\Models\Expense::getCategoryLabel($expense->category) }}
                                </span>
                            </td>
                            <td class="px-5 py-4 text-gray-600">
                                {{ $expense->property?->name ?? '-' }}
                            </td>
                            <td class="px-5 py-4 font-semibold text-gray-800">
                                Rp {{ number_format($expense->amount, 0, ',', '.') }}
                            </td>
                            <td class="px-5 py-4 text-center" wire:click.stop>
                                <div class="flex items-center justify-center gap-2">
                                    <button wire:click="openEditModal({{ $expense->id }})"
                                        class="p-1.5 text-blue-600 hover:bg-blue-50 rounded-lg transition"
                                        title="Edit">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
                                            </path>
                                        </svg>
                                    </button>
                                    <button wire:click="confirmDelete({{ $expense->id }})"
                                        class="p-1.5 text-red-600 hover:bg-red-50 rounded-lg transition"
                                        title="Hapus">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                            </path>
                                        </svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-5 py-12 text-center">
                                <svg class="w-12 h-12 mx-auto mb-3 text-gray-300" fill="none"
                                    stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2">
                                    </path>
                                </svg>
                                <p class="text-gray-500 font-medium">Belum ada pengeluaran</p>
                                <p class="text-sm text-gray-400 mt-1">Klik tombol "Tambah Pengeluaran" untuk mulai
                                    mencatat</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($expenses->hasPages())
            <div class="px-5 py-4 border-t border-gray-100">
                {{ $expenses->links() }}
            </div>
        @endif
    </div>

    <!-- Create/Edit Modal -->
    @if ($showModal)
        <div class="fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4"
            wire:click.self="$set('showModal', false)">
            <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg max-h-[90vh] overflow-y-auto">
                <div class="p-6">
                    <h3 class="text-lg font-bold text-gray-800 mb-4">
                        {{ $isEditing ? 'Edit Pengeluaran' : 'Tambah Pengeluaran' }}
                    </h3>

                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Judul <span
                                    class="text-red-500">*</span></label>
                            <input type="text" wire:model="title" placeholder="Contoh: Beli lampu kamar 5"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition text-sm">
                            @error('title')
                                <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Jumlah (Rp) <span
                                        class="text-red-500">*</span></label>
                                <input type="number" wire:model="amount" placeholder="0" min="1"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition text-sm">
                                @error('amount')
                                    <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal <span
                                        class="text-red-500">*</span></label>
                                <input type="date" wire:model="expense_date"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition text-sm">
                                @error('expense_date')
                                    <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Kategori</label>
                                <select wire:model="category"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition text-sm">
                                    <option value="maintenance">Perbaikan</option>
                                    <option value="utility">Utilitas (Listrik/Air)</option>
                                    <option value="cleaning">Kebersihan</option>
                                    <option value="supplies">Perlengkapan</option>
                                    <option value="salary">Gaji</option>
                                    <option value="tax">Pajak</option>
                                    <option value="insurance">Asuransi</option>
                                    <option value="other">Lainnya</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Properti</label>
                                <select wire:model="property_id"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition text-sm">
                                    <option value="">-- Pilih Properti --</option>
                                    @foreach ($properties as $prop)
                                        <option value="{{ $prop->id }}">{{ $prop->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Deskripsi</label>
                            <textarea wire:model="description" rows="3" placeholder="Detail pengeluaran (opsional)"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition text-sm"></textarea>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Bukti / Nota (opsional)</label>
                            <input type="file" wire:model="receipt_image" accept="image/*"
                                class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                            @error('receipt_image')
                                <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="flex justify-end gap-3 mt-6">
                        <button wire:click="$set('showModal', false)"
                            class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg transition">
                            Batal
                        </button>
                        <button wire:click="save"
                            class="px-4 py-2 text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 rounded-lg transition shadow-sm">
                            {{ $isEditing ? 'Simpan Perubahan' : 'Simpan' }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Detail Modal -->
    @if ($showDetailModal && $selectedExpense)
        <div class="fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4"
            wire:click.self="$set('showDetailModal', false)">
            <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md">
                <div class="p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-bold text-gray-800">Detail Pengeluaran</h3>
                        <button wire:click="$set('showDetailModal', false)"
                            class="p-1 text-gray-400 hover:text-gray-600 rounded-lg transition">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>

                    <div class="space-y-4">
                        <div>
                            <label class="text-xs font-medium text-gray-500 uppercase">Judul</label>
                            <p class="text-sm text-gray-800 mt-1 font-medium">{{ $selectedExpense->title }}</p>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="text-xs font-medium text-gray-500 uppercase">Jumlah</label>
                                <p class="text-lg font-bold text-red-600 mt-1">
                                    Rp {{ number_format($selectedExpense->amount, 0, ',', '.') }}</p>
                            </div>
                            <div>
                                <label class="text-xs font-medium text-gray-500 uppercase">Tanggal</label>
                                <p class="text-sm text-gray-800 mt-1">
                                    {{ $selectedExpense->expense_date->translatedFormat('d F Y') }}</p>
                            </div>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="text-xs font-medium text-gray-500 uppercase">Kategori</label>
                                <p class="text-sm text-gray-800 mt-1">
                                    {{ \App\Models\Expense::getCategoryLabel($selectedExpense->category) }}</p>
                            </div>
                            <div>
                                <label class="text-xs font-medium text-gray-500 uppercase">Properti</label>
                                <p class="text-sm text-gray-800 mt-1">
                                    {{ $selectedExpense->property?->name ?? '-' }}</p>
                            </div>
                        </div>
                        @if ($selectedExpense->description)
                            <div>
                                <label class="text-xs font-medium text-gray-500 uppercase">Deskripsi</label>
                                <p class="text-sm text-gray-700 mt-1 whitespace-pre-line bg-gray-50 p-3 rounded-lg">
                                    {{ $selectedExpense->description }}</p>
                            </div>
                        @endif
                        @if ($selectedExpense->receipt_image)
                            <div>
                                <label class="text-xs font-medium text-gray-500 uppercase">Bukti / Nota</label>
                                <img src="{{ asset('storage/' . $selectedExpense->receipt_image) }}"
                                    alt="Bukti Pengeluaran"
                                    class="mt-2 rounded-lg border border-gray-200 max-h-48 object-contain">
                            </div>
                        @endif
                        <div>
                            <label class="text-xs font-medium text-gray-500 uppercase">Dicatat Oleh</label>
                            <p class="text-sm text-gray-800 mt-1">
                                {{ $selectedExpense->creator?->name ?? '-' }} &middot;
                                {{ $selectedExpense->created_at->translatedFormat('d M Y H:i') }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Delete Confirmation Modal -->
    @if ($showDeleteModal)
        <div class="fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4"
            wire:click.self="$set('showDeleteModal', false)">
            <div class="bg-white rounded-2xl shadow-2xl w-full max-w-sm">
                <div class="p-6 text-center">
                    <div class="w-12 h-12 mx-auto mb-4 bg-red-100 rounded-full flex items-center justify-center">
                        <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-gray-800 mb-2">Hapus Pengeluaran?</h3>
                    <p class="text-sm text-gray-500 mb-6">Data yang dihapus tidak dapat dikembalikan.</p>
                    <div class="flex justify-center gap-3">
                        <button wire:click="$set('showDeleteModal', false)"
                            class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg transition">
                            Batal
                        </button>
                        <button wire:click="delete"
                            class="px-4 py-2 text-sm font-medium text-white bg-red-600 hover:bg-red-700 rounded-lg transition shadow-sm">
                            Hapus
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
