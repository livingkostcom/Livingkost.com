@php
    $statusMap = [
        'unpaid' => ['label' => 'Belum Bayar', 'color' => 'red'],
        'pending' => ['label' => 'Pending', 'color' => 'yellow'],
        'paid' => ['label' => 'Sudah Bayar', 'color' => 'green'],
    ];
@endphp

<div>
    <!-- Header -->
    <div class="mb-8">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1
                    class="text-2xl sm:text-4xl font-bold bg-gradient-to-r from-orange-600 to-orange-600 bg-clip-text text-transparent">
                    Invoice</h1>
                <p class="mt-2 text-gray-600">Kelola daftar invoice penghuni</p>
            </div>
            <div class="flex flex-col sm:flex-row gap-3">
                <button wire:click="sendBulkReminders"
                    wire:confirm="Kirim pengingat ke semua invoice yang belum bayar & mendekati/melewati jatuh tempo?"
                    wire:loading.attr="disabled"
                    class="inline-flex items-center justify-center gap-2 px-6 py-3 bg-gradient-to-r from-yellow-500 to-orange-500 hover:from-yellow-600 hover:to-orange-600 text-white font-semibold rounded-xl transition duration-300 transform hover:scale-105 active:scale-95 shadow-lg hover:shadow-xl whitespace-nowrap disabled:opacity-50">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9">
                        </path>
                    </svg>
                    <span wire:loading.remove wire:target="sendBulkReminders">Kirim Pengingat</span>
                    <span wire:loading wire:target="sendBulkReminders">Mengirim...</span>
                </button>
                <button wire:click="openGenerateModal"
                    class="inline-flex items-center justify-center gap-2 px-6 py-3 bg-gradient-to-r from-green-600 to-emerald-600 hover:from-green-700 hover:to-emerald-700 text-white font-semibold rounded-xl transition duration-300 transform hover:scale-105 active:scale-95 shadow-lg hover:shadow-xl whitespace-nowrap">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10">
                        </path>
                    </svg>
                    Generate Bulanan
                </button>
                <a href="{{ route('invoices.create') }}"
                    class="inline-flex items-center justify-center gap-2 px-6 py-3 bg-gradient-to-r from-orange-600 to-orange-600 hover:from-orange-700 hover:to-orange-700 text-white font-semibold rounded-xl transition duration-300 transform hover:scale-105 active:scale-95 shadow-lg hover:shadow-xl whitespace-nowrap">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    Buat Invoice
                </a>
            </div>
        </div>
    </div>

    <!-- Flash Messages -->
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

    <!-- Search & Filter -->
    <div class="mb-6 grid grid-cols-1 md:grid-cols-2 gap-4">
        <!-- Search -->
        <div class="relative group">
            <svg class="absolute left-4 top-3.5 w-5 h-5 text-gray-400 group-focus-within:text-orange-500 transition"
                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
            </svg>
            <input wire:model.live="search" type="text"
                placeholder="Cari nama penghuni, email, atau nomor referensi..."
                class="w-full pl-12 pr-4 py-3 bg-white border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent transition duration-300 shadow-sm hover:shadow-md">
        </div>

        <!-- Filter by Status -->
        <select wire:model.live="filterStatus"
            class="px-4 py-3 bg-white border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent transition duration-300 shadow-sm hover:shadow-md">
            <option value="">-- Semua Status --</option>
            <option value="unpaid">Belum Bayar</option>
            <option value="pending">Pending</option>
            <option value="paid">Sudah Bayar</option>
        </select>
    </div>

    <!-- Table -->
    <div class="bg-white rounded-2xl shadow-lg overflow-hidden hover:shadow-xl transition duration-300">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gradient-to-r from-gray-50 to-gray-100">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wide whitespace-nowrap">Nomor Invoice</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wide whitespace-nowrap">Penghuni</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wide whitespace-nowrap">Bulan</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wide whitespace-nowrap">Jumlah</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wide whitespace-nowrap">Jatuh Tempo</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wide whitespace-nowrap">Status</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wide whitespace-nowrap">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($invoices as $invoice)
                        <tr class="hover:bg-orange-50 transition duration-200">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <p class="text-gray-900 font-semibold">{{ $invoice->reference_number }}</p>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center gap-3">
                                    <div
                                        class="w-10 h-10 rounded-lg bg-gradient-to-br from-orange-100 to-orange-100 flex items-center justify-center font-semibold text-orange-600 shadow-sm">
                                        {{ substr($invoice->lease->tenant->display_name, 0, 1) }}
                                    </div>
                                    <div>
                                        <p class="text-gray-900 font-semibold">
                                            {{ $invoice->lease->tenant->display_name }}
                                        </p>
                                        <p class="text-xs text-gray-500">{{ $invoice->lease->room->room_number }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <p class="text-gray-600 text-sm">
                                    {{ \Carbon\Carbon::createFromFormat('Y-m', $invoice->month_year)->locale('id')->format('F Y') }}
                                </p>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <p class="text-gray-900 font-semibold">Rp
                                    {{ number_format($invoice->amount, 0, ',', '.') }}
                                </p>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <p class="text-gray-600 text-sm">
                                    {{ $invoice->due_date?->locale('id')->translatedFormat('d M Y') }}</p>
                            </td>
                            <td class="px-6 py-4 text-sm">
                                @php
                                    $status = $statusMap[$invoice->status] ?? [
                                        'label' => $invoice->status,
                                        'color' => 'gray',
                                    ];
                                    $colorClass = match ($status['color']) {
                                        'red' => 'bg-red-100 text-red-800',
                                        'yellow' => 'bg-yellow-100 text-yellow-800',
                                        'green' => 'bg-green-100 text-green-800',
                                        default => 'bg-gray-100 text-gray-800',
                                    };
                                @endphp
                                <span class="px-3 py-1 rounded-full text-xs font-medium {{ $colorClass }}">
                                    {{ $status['label'] }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center gap-2">
                                    <button wire:click="openDetailModal({{ $invoice->id }})"
                                        class="inline-flex items-center gap-1 px-3 py-1.5 bg-orange-50 text-orange-700 rounded-lg hover:bg-orange-100 transition font-medium text-sm border border-orange-200">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                        Detail
                                    </button>
                                    @if ($invoice->verified_at)
                                        <button disabled
                                            class="inline-flex items-center gap-1 px-3 py-1.5 bg-gray-50 text-gray-400 rounded-lg cursor-not-allowed font-medium text-sm border border-gray-200"
                                            title="Invoice sudah diverifikasi">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                            Edit
                                        </button>
                                    @else
                                        <a href="{{ route('invoices.edit', $invoice->id) }}"
                                            class="inline-flex items-center gap-1 px-3 py-1.5 bg-orange-50 text-orange-700 rounded-lg hover:bg-orange-100 transition font-medium text-sm border border-orange-200">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                            Edit
                                        </a>
                                    @endif
                                    @if ($invoice->verified_at)
                                        <button disabled
                                            class="inline-flex items-center gap-1 px-3 py-1.5 bg-gray-50 text-gray-400 rounded-lg cursor-not-allowed font-medium text-sm border border-gray-200"
                                            title="Invoice sudah diverifikasi">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                            Hapus
                                        </button>
                                    @else
                                        <button wire:click="openDeleteModal({{ $invoice->id }})"
                                            class="inline-flex items-center gap-1 px-3 py-1.5 bg-red-50 text-red-700 rounded-lg hover:bg-red-100 transition font-medium text-sm border border-red-200">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                            Hapus
                                        </button>
                                    @endif
                                    @if ($invoice->status === 'unpaid')
                                        <button wire:click="sendReminder({{ $invoice->id }})"
                                            wire:loading.attr="disabled" wire:target="sendReminder({{ $invoice->id }})"
                                            class="inline-flex items-center gap-1 px-3 py-1.5 bg-yellow-50 text-yellow-700 rounded-lg hover:bg-yellow-100 transition font-medium text-sm border border-yellow-200 disabled:opacity-50">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                                            <span wire:loading.remove wire:target="sendReminder({{ $invoice->id }})">Ingatkan</span>
                                            <span wire:loading wire:target="sendReminder({{ $invoice->id }})">...</span>
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-8 text-center text-gray-500">
                                <svg class="mx-auto h-12 w-12 text-gray-400 mb-3" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                                Tidak ada invoice ditemukan
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Pagination -->
    <div class="mt-6">
        {{ $invoices->links('vendor.pagination.tailwind') }}
    </div>

    <!-- Detail Modal -->
    @if ($showDetailModal && $detailingInvoice)
        <div class="fixed inset-0 bg-black/50 flex items-center justify-center p-4 z-50">
            <div class="bg-white rounded-lg shadow-xl max-w-4xl w-full max-h-[80vh] overflow-y-auto">
                <div
                    class="sticky top-0 bg-gray-100 border-b border-gray-200 px-6 py-4 flex justify-between items-center">
                    <h3 class="text-lg font-bold text-gray-900">Detail Invoice</h3>
                    <button wire:click="closeDetailModal" class="text-gray-500 hover:text-gray-700">
                        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                                clip-rule="evenodd" />
                        </svg>
                    </button>
                </div>

                <div class="p-6 space-y-6">
                    <!-- Invoice Info -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="col-span-2">
                            <h4 class="text-sm font-semibold text-gray-900 mb-3">Informasi Invoice</h4>
                        </div>
                        <div>
                            <label class="block text-xs text-gray-600 mb-1">Nomor Invoice</label>
                            <p class="text-sm font-medium text-gray-900">{{ $detailingInvoice->reference_number }}</p>
                        </div>
                        <div>
                            <label class="block text-xs text-gray-600 mb-1">Status</label>
                            @php
                                $status = $statusMap[$detailingInvoice->status] ?? [
                                    'label' => $detailingInvoice->status,
                                    'color' => 'gray',
                                ];
                                $colorClass = match ($status['color']) {
                                    'red' => 'bg-red-100 text-red-800',
                                    'yellow' => 'bg-yellow-100 text-yellow-800',
                                    'green' => 'bg-green-100 text-green-800',
                                    default => 'bg-gray-100 text-gray-800',
                                };
                            @endphp
                            <span
                                class="px-2 py-1 rounded text-xs font-medium {{ $colorClass }}">{{ $status['label'] }}</span>
                        </div>
                        <div>
                            <label class="block text-xs text-gray-600 mb-1">Bulan</label>
                            <p class="text-sm font-medium text-gray-900">
                                {{ \Carbon\Carbon::createFromFormat('Y-m', $detailingInvoice->month_year)->locale('id')->format('F Y') }}
                            </p>
                        </div>
                        <div>
                            <label class="block text-xs text-gray-600 mb-1">Jatuh Tempo</label>
                            <p class="text-sm font-medium text-gray-900">
                                {{ $detailingInvoice->due_date?->locale('id')->translatedFormat('d M Y') }}</p>
                        </div>
                    </div>

                    <!-- Tenant Info -->
                    <div class="border-t border-gray-200 pt-4">
                        <h4 class="text-sm font-semibold text-gray-900 mb-3">Informasi Penghuni</h4>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs text-gray-600 mb-1">Nama</label>
                                <p class="text-sm font-medium text-gray-900">
                                    {{ $detailingInvoice->lease->tenant->display_name }}</p>
                            </div>
                            <div>
                                <label class="block text-xs text-gray-600 mb-1">Email</label>
                                <p class="text-sm font-medium text-gray-900">
                                    {{ $detailingInvoice->lease->tenant->email ?? '-' }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Room Info -->
                    <div class="border-t border-gray-200 pt-4">
                        <h4 class="text-sm font-semibold text-gray-900 mb-3">Informasi Ruangan</h4>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs text-gray-600 mb-1">Properti</label>
                                <p class="text-sm font-medium text-gray-900">
                                    {{ $detailingInvoice->lease->room->roomType->property->name }}</p>
                            </div>
                            <div>
                                <label class="block text-xs text-gray-600 mb-1">Tipe Ruangan</label>
                                <p class="text-sm font-medium text-gray-900">
                                    {{ $detailingInvoice->lease->room->roomType->name }}</p>
                            </div>
                            <div>
                                <label class="block text-xs text-gray-600 mb-1">Nomor Ruangan</label>
                                <p class="text-sm font-medium text-gray-900">
                                    {{ $detailingInvoice->lease->room->room_number }}</p>
                            </div>
                            <div>
                                <label class="block text-xs text-gray-600 mb-1">Harga</label>
                                <p class="text-sm font-medium text-gray-900">Rp
                                    {{ number_format($detailingInvoice->lease->room->roomType->price, 0, ',', '.') }}/bln
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Amount -->
                    <div class="border-t border-gray-200 pt-4 bg-orange-50 p-4 rounded-lg">
                        <label class="block text-xs text-gray-600 mb-1">Jumlah Tagihan</label>
                        <p class="text-2xl font-bold text-orange-600">Rp
                            {{ number_format($detailingInvoice->amount, 0, ',', '.') }}</p>
                    </div>

                    <!-- Audit Info -->
                    <div class="border-t border-gray-200 pt-4 text-xs text-gray-500 space-y-1">
                        <p>Dibuat oleh: {{ $detailingInvoice->creator?->name ?? 'Sistem' }} -
                            {{ $detailingInvoice->created_at?->locale('id')->translatedFormat('d M Y H:i') }}</p>
                        @if ($detailingInvoice->verified_at)
                            <p>Diverifikasi oleh: {{ $detailingInvoice->verifier?->name ?? '-' }} -
                                {{ $detailingInvoice->verified_at->locale('id')->translatedFormat('d M Y H:i') }}</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Delete Confirmation Modal -->
    @if ($showDeleteModal && $deletingInvoice)
        <div class="fixed inset-0 bg-black/20 z-50 flex items-center justify-center p-4 backdrop-blur-sm">
            <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full overflow-y-auto max-h-[90vh] animate-fade-in">
                <!-- Header -->
                <div class="bg-gradient-to-r from-red-600 to-pink-600 px-6 py-4">
                    <h2 class="text-lg font-bold text-white flex items-center gap-3">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8v4m0 4v.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Hapus Invoice?
                    </h2>
                </div>

                <!-- Content -->
                <div class="p-6">
                    <p class="text-gray-700 mb-3 font-medium">Apakah Anda yakin ingin menghapus invoice berikut?</p>
                    <div
                        class="bg-gradient-to-br from-red-50 to-pink-50 p-4 rounded-xl mb-6 border-l-4 border-red-500">
                        <p class="text-sm text-gray-600">Nomor Invoice</p>
                        <p class="text-lg font-bold text-gray-900">{{ $deletingInvoice->reference_number }}</p>
                        <p class="text-sm text-gray-600 mt-2">Penghuni</p>
                        <p class="text-gray-900 font-semibold">{{ $deletingInvoice->lease->tenant->display_name }}</p>
                        <p class="text-sm text-gray-600 mt-2">Jumlah</p>
                        <p class="text-gray-900">Rp {{ number_format($deletingInvoice->amount, 0, ',', '.') }}</p>
                        <p class="text-sm text-gray-600 mt-2">Bulan</p>
                        <p class="text-gray-900">
                            {{ \Carbon\Carbon::createFromFormat('Y-m', $deletingInvoice->month_year)->locale('id')->format('F Y') }}
                        </p>
                    </div>
                    <p class="text-sm text-red-600 font-semibold mb-6">
                        Tindakan ini tidak dapat dibatalkan. Data invoice akan dihapus secara permanen.
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

    <!-- Generate Invoice Modal -->
    @if ($showGenerateModal)
        <div class="fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4 backdrop-blur-sm">
            <div class="bg-white rounded-2xl shadow-2xl max-w-3xl w-full max-h-[85vh] overflow-y-auto max-h-[90vh] animate-fade-in">
                <!-- Header -->
                <div
                    class="bg-gradient-to-r from-green-600 to-emerald-600 px-6 py-4 flex items-center justify-between">
                    <h2 class="text-lg font-bold text-white flex items-center gap-3">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10">
                            </path>
                        </svg>
                        Generate Invoice Bulanan
                    </h2>
                    <button wire:click="closeGenerateModal" class="text-white/80 hover:text-white transition">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                <!-- Content -->
                <div class="p-6 overflow-y-auto max-h-[calc(85vh-130px)]">
                    <!-- Month Selector -->
                    <div class="flex flex-col sm:flex-row gap-4 items-end mb-6">
                        <div class="flex-1">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Pilih Bulan</label>
                            <input type="month" wire:model="generateMonthYear"
                                class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent" />
                        </div>
                        <button wire:click="previewGenerate" wire:loading.attr="disabled"
                            class="px-6 py-2.5 bg-orange-600 text-white rounded-lg hover:bg-orange-700 transition font-medium flex items-center gap-2 disabled:opacity-50">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                            <span wire:loading.remove wire:target="previewGenerate">Preview</span>
                            <span wire:loading wire:target="previewGenerate">Memuat...</span>
                        </button>
                    </div>

                    @if ($showGeneratePreview)
                        @if (count($generatePreview) > 0)
                            <!-- Summary -->
                            <div class="mb-4 text-sm text-gray-600">
                                <span class="font-semibold text-gray-900">{{ count($generatePreview) }}</span> kontrak
                                aktif ditemukan,
                                <span class="text-green-600 font-semibold">{{ $this->eligibleGenerateCount }} siap
                                    dibuat</span>
                                @if (count($generatePreview) - $this->eligibleGenerateCount > 0)
                                    , <span
                                        class="text-yellow-600 font-semibold">{{ count($generatePreview) - $this->eligibleGenerateCount }}
                                        sudah ada</span>
                                @endif
                            </div>

                            <!-- Preview Table -->
                            <div class="overflow-x-auto rounded-lg border border-gray-200">
                                <table class="w-full text-sm">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-4 py-3 text-left font-semibold text-gray-700">Penyewa</th>
                                            <th class="px-4 py-3 text-left font-semibold text-gray-700">Kamar</th>
                                            <th class="px-4 py-3 text-right font-semibold text-gray-700">Jumlah</th>
                                            <th class="px-4 py-3 text-left font-semibold text-gray-700">Jatuh Tempo
                                            </th>
                                            <th class="px-4 py-3 text-center font-semibold text-gray-700">Status</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-100">
                                        @foreach ($generatePreview as $item)
                                            <tr
                                                class="{{ $item['already_exists'] ? 'bg-yellow-50/50' : 'hover:bg-gray-50' }}">
                                                <td class="px-4 py-3 font-medium text-gray-900">
                                                    {{ $item['tenant_name'] }}</td>
                                                <td class="px-4 py-3 text-gray-700">{{ $item['room_number'] }} <span
                                                        class="text-gray-400 text-xs">({{ $item['room_type'] }})</span>
                                                </td>
                                                <td class="px-4 py-3 text-right font-medium text-gray-900">Rp
                                                    {{ number_format($item['amount'], 0, ',', '.') }}</td>
                                                <td class="px-4 py-3 text-gray-700">
                                                    {{ \Carbon\Carbon::parse($item['due_date'])->translatedFormat('d M Y') }}
                                                </td>
                                                <td class="px-4 py-3 text-center">
                                                    @if ($item['already_exists'])
                                                        <span
                                                            class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">Sudah
                                                            Ada</span>
                                                    @else
                                                        <span
                                                            class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">Siap
                                                            Dibuat</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="py-12 text-center">
                                <svg class="w-12 h-12 mx-auto text-gray-300 mb-4" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                                <p class="text-gray-500">Tidak ada kontrak sewa aktif untuk bulan ini.</p>
                            </div>
                        @endif
                    @endif
                </div>

                <!-- Footer -->
                <div class="bg-gray-50 px-6 py-4 flex justify-between items-center border-t border-gray-200">
                    <button wire:click="closeGenerateModal"
                        class="px-5 py-2.5 bg-gray-200 hover:bg-gray-300 text-gray-800 font-semibold rounded-xl transition">
                        Tutup
                    </button>
                    @if ($showGeneratePreview && $this->eligibleGenerateCount > 0)
                        <button wire:click="generateInvoices" wire:loading.attr="disabled"
                            wire:confirm="Yakin ingin membuat {{ $this->eligibleGenerateCount }} invoice?"
                            class="px-6 py-2.5 bg-green-600 hover:bg-green-700 text-white font-semibold rounded-xl transition flex items-center gap-2 disabled:opacity-50">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                            </svg>
                            <span wire:loading.remove wire:target="generateInvoices">Generate
                                {{ $this->eligibleGenerateCount }} Invoice</span>
                            <span wire:loading wire:target="generateInvoices">Memproses...</span>
                        </button>
                    @endif
                </div>
            </div>
        </div>
    @endif
</div>
