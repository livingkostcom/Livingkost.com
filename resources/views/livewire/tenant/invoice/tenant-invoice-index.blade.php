@php
    $statusMap = [
        'unpaid' => ['label' => 'Belum Bayar', 'color' => 'red'],
        'pending' => ['label' => 'Menunggu Verifikasi', 'color' => 'yellow'],
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
                    Invoices Saya
                </h1>
                <p class="mt-2 text-gray-600">Lihat dan bayar tagihan bulan ini</p>
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
            <input wire:model.live="search" type="text" placeholder="Cari nomor invoice atau bulan..."
                class="w-full pl-12 pr-4 py-3 bg-white border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent transition duration-300 shadow-sm hover:shadow-md">
        </div>

        <!-- Filter by Status -->
        <select wire:model.live="filterStatus"
            class="px-4 py-3 bg-white border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent transition duration-300 shadow-sm hover:shadow-md">
            <option value="">-- Semua Status --</option>
            <option value="unpaid">Belum Bayar</option>
            <option value="pending">Menunggu Verifikasi</option>
            <option value="paid">Sudah Bayar</option>
        </select>
    </div>

    <!-- Table -->
    <div class="bg-white rounded-2xl shadow-lg overflow-hidden hover:shadow-xl transition duration-300">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gradient-to-r from-gray-50 to-gray-100">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wide">
                            Nomor
                            Invoice</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wide">
                            Bulan
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wide">
                            Jumlah
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wide">
                            Jatuh
                            Tempo</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wide">
                            Status
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wide">Aksi
                        </th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($invoices as $invoice)
                        <tr class="hover:bg-orange-50 transition duration-200">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <p class="text-gray-900 font-semibold">{{ $invoice->reference_number }}</p>
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
                            <td class="px-6 py-4 text-sm flex gap-2">
                                @if ($invoice->status === 'unpaid')
                                    <button wire:click="openUploadModal({{ $invoice->id }})"
                                        class="inline-flex items-center px-3 py-1 bg-orange-100 text-orange-700 rounded hover:bg-orange-200 transition font-medium">
                                        Upload Bukti
                                    </button>
                                @elseif ($invoice->status === 'pending')
                                    <span
                                        class="inline-flex items-center px-3 py-1 bg-yellow-100 text-yellow-700 rounded font-medium text-xs">
                                        ⏳ Menunggu...
                                    </span>
                                @else
                                    <span
                                        class="inline-flex items-center px-3 py-1 bg-green-100 text-green-700 rounded font-medium text-xs">
                                        ✓ Terverifikasi
                                    </span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-8 text-center text-gray-500">
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

    <!-- Payment Upload Modal -->
    @if ($showUploadModal)
        <div class="fixed inset-0 bg-black/50 flex items-center justify-center p-4 z-50">
            <div class="bg-white rounded-lg shadow-xl max-w-md w-full overflow-hidden">
                <div class="bg-orange-600 px-6 py-4 flex justify-between items-center">
                    <h3 class="text-lg font-bold text-white">Upload Bukti Pembayaran</h3>
                    <button wire:click="closeUploadModal" class="text-white hover:text-orange-100">
                        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                                clip-rule="evenodd" />
                        </svg>
                    </button>
                </div>

                <form wire:submit.prevent="submitProof" class="p-6 space-y-4">
                    <!-- File Input -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Pilih File</label>
                        <div class="relative">
                            <input type="file" wire:model="proofFile" accept=".pdf,.jpg,.jpeg,.png"
                                class="w-full px-4 py-3 border-2 border-dashed border-gray-300 rounded-xl focus:outline-none focus:border-orange-500 file:hidden cursor-pointer"
                                @dragover="$el.classList.add('border-orange-500')"
                                @dragleave="$el.classList.remove('border-orange-500')">
                            <p class="text-xs text-gray-500 mt-2">PDF, JPG, PNG • Max 5MB</p>
                        </div>
                        @error('proofFile')
                            <p class="text-red-600 text-xs mt-2">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- File Preview -->
                    @if ($proofFile)
                        <div class="bg-green-50 border border-green-200 rounded-xl p-3">
                            <p class="text-sm text-green-800">
                                <svg class="w-4 h-4 inline mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                        clip-rule="evenodd"></path>
                                </svg>
                                File siap diunggah: {{ $proofFile->getClientOriginalName() }}
                            </p>
                        </div>
                    @endif

                    <!-- Buttons -->
                    <div class="flex gap-3 pt-4">
                        <button type="button" wire:click="closeUploadModal"
                            class="flex-1 px-4 py-3 bg-gray-200 hover:bg-gray-300 text-gray-800 font-semibold rounded-xl transition">
                            Batal
                        </button>
                        <button type="submit"
                            class="flex-1 px-4 py-3 bg-orange-600 hover:bg-orange-700 text-white font-semibold rounded-xl transition">
                            Upload
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
