<div>
    <!-- Header -->
    <div class="mb-8">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1
                    class="text-2xl sm:text-4xl font-bold bg-gradient-to-r from-orange-600 to-orange-600 bg-clip-text text-transparent">
                    Verifikasi Pembayaran
                </h1>
                <p class="mt-2 text-gray-600">Kelola dan verifikasi pembayaran dari penghuni</p>
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
    <div class="bg-white rounded-lg shadow-md mb-6 p-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Cari Invoice / Tenant</label>
                <input type="text" wire:model.live="search" placeholder="Nomor invoice, nama tenant, email..."
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent" />
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Filter Status</label>
                <select wire:model.live="filterStatus"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent">
                    <option value="">Semua Status</option>
                    <option value="pending">Menunggu Verifikasi</option>
                    <option value="paid">Sudah Terverifikasi</option>
                    <option value="unpaid">Belum Bayar</option>
                </select>
            </div>
        </div>
    </div>

    <!-- Invoices Table -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wide whitespace-nowrap">
                            Invoice
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wide whitespace-nowrap">
                            Penghuni
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wide whitespace-nowrap">
                            Jumlah
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wide whitespace-nowrap">
                            Status
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wide whitespace-nowrap">Tgl
                            Upload</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wide whitespace-nowrap">Aksi
                        </th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse ($invoices as $invoice)
                        <tr class="hover:bg-orange-50 transition duration-200">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <p class="text-gray-900 font-semibold">
                                    {{ $invoice->reference_number ?? 'INV-' . str_pad($invoice->id, 5, '0', STR_PAD_LEFT) }}
                                </p>
                                <p class="text-xs text-gray-500">Lease #{{ $invoice->lease_id }}</p>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center gap-3">
                                    <div
                                        class="w-10 h-10 rounded-lg bg-gradient-to-br from-orange-100 to-orange-100 flex items-center justify-center font-semibold text-orange-600 shadow-sm">
                                        @if ($invoice->lease && $invoice->lease->tenant)
                                            {{ substr($invoice->lease->tenant->display_name, 0, 1) }}
                                        @else
                                            N
                                        @endif
                                    </div>
                                    <div>
                                        <p class="text-gray-900 font-semibold">
                                            @if ($invoice->lease && $invoice->lease->tenant)
                                                {{ $invoice->lease->tenant->display_name }}
                                            @else
                                                -
                                            @endif
                                        </p>
                                        <p class="text-xs text-gray-500">
                                            @if ($invoice->lease && $invoice->lease->room)
                                                {{ $invoice->lease->room->room_number }}
                                            @else
                                                -
                                            @endif
                                        </p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <p class="text-gray-900 font-semibold">Rp
                                    {{ number_format($invoice->amount, 0, ',', '.') }}
                                </p>
                            </td>
                            <td class="px-6 py-4 text-sm">
                                @php
                                    $statusMap = [
                                        'unpaid' => ['label' => 'Belum Bayar', 'color' => 'red'],
                                        'pending' => ['label' => 'Menunggu Verifikasi', 'color' => 'yellow'],
                                        'paid' => ['label' => 'Sudah Bayar', 'color' => 'green'],
                                    ];
                                    $status = $statusMap[$invoice->status] ?? $statusMap['unpaid'];
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
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                @if ($invoice->created_at)
                                    {{ $invoice->created_at->format('d M Y H:i') }}
                                @else
                                    -
                                @endif
                            </td>
                            <td class="px-6 py-4 text-sm space-x-2 flex">
                                <button wire:click="openDetailModal({{ $invoice->id }})"
                                    class="inline-flex items-center px-3 py-1 bg-orange-100 text-orange-700 rounded hover:bg-orange-200 transition font-medium">
                                    Detail
                                </button>
                                @if ($invoice->status === 'pending')
                                    <button wire:click="openApprovalModal({{ $invoice->id }})"
                                        class="inline-flex items-center px-3 py-1 bg-green-100 text-green-700 rounded hover:bg-green-200 transition font-medium">
                                        Approve
                                    </button>
                                    <button wire:click="openRejectionModal({{ $invoice->id }})"
                                        class="inline-flex items-center px-3 py-1 bg-red-100 text-red-700 rounded hover:bg-red-200 transition font-medium">
                                        Tolak
                                    </button>
                                @elseif ($invoice->status === 'paid' && $invoice->receipt)
                                    <a href="{{ route('receipts.download', $invoice->receipt->id) }}" target="_blank"
                                        class="inline-flex items-center px-3 py-1 bg-orange-100 text-orange-700 rounded hover:bg-orange-200 transition font-medium">
                                        Download Receipt
                                    </a>
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

    <!-- Detail Modal -->
    @if ($showDetailModal && $viewingInvoiceId)
        @php
            $detailInvoice =
                $invoices->firstWhere('id', $viewingInvoiceId) ?? \App\Models\Invoice::find($viewingInvoiceId);
        @endphp
        <div class="fixed inset-0 bg-black/50 flex items-center justify-center p-4 z-50">
            <div class="bg-white rounded-xl shadow-2xl max-w-2xl w-full max-h-[80vh] overflow-y-auto">
                <div
                    class="sticky top-0 bg-gradient-to-r from-gray-50 to-gray-100 border-b border-gray-200 px-6 py-4 flex justify-between items-center">
                    <h3 class="text-lg font-bold text-gray-900">Detail Invoice</h3>
                    <button wire:click="closeDetailModal" class="text-gray-500 hover:text-gray-700 transition">
                        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                                clip-rule="evenodd" />
                        </svg>
                    </button>
                </div>
                <div class="p-6 space-y-6">
                    @if ($detailInvoice)
                        <!-- Invoice Info -->
                        <div>
                            <h4 class="text-sm font-semibold text-gray-900 mb-3">Informasi Invoice</h4>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-xs text-gray-600 mb-1">Nomor Invoice</label>
                                    <p class="text-sm font-medium text-gray-900">
                                        {{ $detailInvoice->reference_number ?? 'INV-' . str_pad($detailInvoice->id, 5, '0', STR_PAD_LEFT) }}
                                    </p>
                                </div>
                                <div>
                                    <label class="block text-xs text-gray-600 mb-1">Tanggal</label>
                                    <p class="text-sm font-medium text-gray-900">
                                        {{ $detailInvoice->created_at?->format('d M Y') }}</p>
                                </div>
                                <div>
                                    <label class="block text-xs text-gray-600 mb-1">Jumlah</label>
                                    <p class="text-sm font-bold text-orange-600">
                                        Rp{{ number_format($detailInvoice->amount, 0, ',', '.') }}</p>
                                </div>
                                <div>
                                    <label class="block text-xs text-gray-600 mb-1">Status</label>
                                    @php
                                        $statusMap = [
                                            'unpaid' => ['label' => 'Belum Bayar', 'color' => 'red'],
                                            'pending' => ['label' => 'Menunggu Verifikasi', 'color' => 'yellow'],
                                            'paid' => ['label' => 'Sudah Bayar', 'color' => 'green'],
                                        ];
                                        $status = $statusMap[$detailInvoice->status] ?? $statusMap['unpaid'];
                                        $colorClass = match ($status['color']) {
                                            'red' => 'bg-red-100 text-red-800',
                                            'yellow' => 'bg-yellow-100 text-yellow-800',
                                            'green' => 'bg-green-100 text-green-800',
                                            default => 'bg-gray-100 text-gray-800',
                                        };
                                    @endphp
                                    <span
                                        class="px-2 py-1 rounded-full text-xs font-medium {{ $colorClass }}">{{ $status['label'] }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- Tenant Info -->
                        <div class="border-t border-gray-200 pt-4">
                            <h4 class="text-sm font-semibold text-gray-900 mb-3">Informasi Penghuni</h4>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-xs text-gray-600 mb-1">Nama</label>
                                    <p class="text-sm font-medium text-gray-900">
                                        {{ $detailInvoice->lease->tenant->display_name }}</p>
                                </div>
                                <div>
                                    <label class="block text-xs text-gray-600 mb-1">Email</label>
                                    <p class="text-sm font-medium text-gray-900">
                                        {{ $detailInvoice->lease->tenant->email }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Room Info -->
                        <div class="border-t border-gray-200 pt-4">
                            <h4 class="text-sm font-semibold text-gray-900 mb-3">Informasi Kamar</h4>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-xs text-gray-600 mb-1">Nomor Kamar</label>
                                    <p class="text-sm font-medium text-gray-900">
                                        {{ $detailInvoice->lease->room->room_number ?? '-' }}</p>
                                </div>
                                <div>
                                    <label class="block text-xs text-gray-600 mb-1">Tipe Kamar</label>
                                    <p class="text-sm font-medium text-gray-900">
                                        {{ $detailInvoice->lease->room->roomType->name ?? '-' }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Proof of Payment -->
                        @if ($detailInvoice->proof_of_payment)
                            <div class="border-t border-gray-200 pt-4">
                                <h4 class="text-sm font-semibold text-gray-900 mb-3">Bukti Pembayaran</h4>
                                @php
                                    $extension = pathinfo($detailInvoice->proof_of_payment, PATHINFO_EXTENSION);
                                    $fileUrl = route('payment-proofs.view', $detailInvoice->id);
                                @endphp
                                <div class="bg-gray-50 p-4 rounded-lg">
                                    @if (in_array(strtolower($extension), ['jpg', 'jpeg', 'png', 'gif']))
                                        <img src="{{ $fileUrl }}" alt="Bukti Pembayaran"
                                            class="max-h-60 rounded-lg mx-auto">
                                        <a href="{{ $fileUrl }}" target="_blank"
                                            class="mt-2 inline-block text-orange-600 hover:text-orange-800 text-sm">
                                            Buka di tab baru
                                        </a>
                                    @else
                                        <div class="flex items-center space-x-3">
                                            <svg class="h-8 w-8 text-red-500" fill="currentColor"
                                                viewBox="0 0 20 20">
                                                <path
                                                    d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4z">
                                                </path>
                                            </svg>
                                            <div>
                                                <p class="font-semibold text-gray-900">{{ strtoupper($extension) }}
                                                    File</p>
                                                <a href="{{ $fileUrl }}" target="_blank"
                                                    class="text-orange-600 hover:text-orange-800 text-sm">
                                                    Download
                                                </a>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endif

                        <!-- Verification Info -->
                        @if ($detailInvoice->verified_at)
                            <div class="border-t border-gray-200 pt-4 bg-green-50 p-4 rounded-lg">
                                <h4 class="text-sm font-semibold text-green-900 mb-2">Terverifikasi</h4>
                                <p class="text-sm text-green-800">
                                    {{ $detailInvoice->verified_at->format('d M Y H:i') }}</p>
                                @if ($detailInvoice->verifier)
                                    <p class="text-sm text-green-800">Oleh:
                                        <strong>{{ $detailInvoice->verifier->name }}</strong>
                                    </p>
                                @endif
                            </div>
                        @endif
                    @endif
                </div>
                <div class="bg-gray-50 border-t border-gray-200 px-6 py-4 flex justify-end">
                    <button wire:click="closeDetailModal"
                        class="px-4 py-2 text-gray-700 border border-gray-300 rounded-lg hover:bg-gray-50 font-medium transition">
                        Tutup
                    </button>
                </div>
            </div>
        </div>
    @endif

    <!-- Approval Modal -->
    @if ($showApprovalModal)
        <div class="fixed inset-0 bg-black/50 flex items-center justify-center p-4 z-50">
            <div class="bg-white rounded-xl shadow-2xl max-w-md w-full max-h-[90vh] overflow-y-auto">
                <div class="bg-gradient-to-r from-green-50 to-emerald-50 border-b border-green-200 px-6 py-4">
                    <h2 class="text-lg font-bold text-gray-900">Approve Pembayaran</h2>
                </div>
                <div class="p-6">
                    <p class="text-gray-600 mb-4">
                        Anda yakin ingin menyetujui pembayaran ini? Status invoice akan berubah menjadi <strong
                            class="text-green-600">PAID</strong>.
                    </p>
                    @php
                        $approvingInvoice = \App\Models\Invoice::find($approvingInvoiceId);
                    @endphp
                    @if ($approvingInvoice)
                        <div class="bg-orange-50 p-4 rounded-lg space-y-2">
                            <div class="grid grid-cols-2 gap-2">
                                <div>
                                    <p class="text-xs text-gray-600">Invoice</p>
                                    <p class="font-semibold text-gray-900">
                                        {{ $approvingInvoice->reference_number ?? 'INV-' . str_pad($approvingInvoice->id, 5, '0', STR_PAD_LEFT) }}
                                    </p>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-600">Jumlah</p>
                                    <p class="font-semibold text-gray-900">
                                        Rp{{ number_format($approvingInvoice->amount, 0, ',', '.') }}</p>
                                </div>
                            </div>
                            <div>
                                <p class="text-xs text-gray-600">Penghuni</p>
                                <p class="font-semibold text-gray-900">
                                    {{ $approvingInvoice->lease->tenant->display_name }}
                                </p>
                            </div>
                        </div>
                    @endif
                </div>
                <div class="bg-gray-50 border-t border-gray-200 px-6 py-4 flex justify-end space-x-3">
                    <button wire:click="closeApprovalModal"
                        class="px-4 py-2 text-gray-700 border border-gray-300 rounded-lg hover:bg-gray-100 font-medium transition">
                        Batal
                    </button>
                    <button wire:click="approvePayment"
                        class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg font-medium transition">
                        Approve
                    </button>
                </div>
            </div>
        </div>
    @endif

    <!-- Rejection Modal -->
    @if ($showRejectionModal)
        <div class="fixed inset-0 bg-black/50 flex items-center justify-center p-4 z-50">
            <div class="bg-white rounded-xl shadow-2xl max-w-md w-full max-h-[90vh] overflow-y-auto">
                <div class="bg-gradient-to-r from-red-50 to-red-100 border-b border-red-200 px-6 py-4">
                    <h2 class="text-lg font-bold text-gray-900">Tolak Pembayaran</h2>
                </div>
                <div class="p-6">
                    <p class="text-gray-600 mb-4">
                        Status invoice akan kembali ke <strong class="text-red-600">UNPAID</strong> dan bukti
                        pembayaran akan dihapus.
                    </p>
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Alasan Penolakan *
                        </label>
                        <textarea wire:model="rejectionReason" placeholder="Jelaskan mengapa pembayaran ditolak..." rows="4"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent">
                        </textarea>
                        @error('rejectionReason')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    @php
                        $rejectingInvoice = \App\Models\Invoice::find($approvingInvoiceId);
                    @endphp
                    @if ($rejectingInvoice)
                        <div class="bg-red-50 p-4 rounded-lg space-y-2">
                            <div>
                                <p class="text-xs text-gray-600">Invoice</p>
                                <p class="font-semibold text-gray-900">
                                    {{ $rejectingInvoice->reference_number ?? 'INV-' . str_pad($rejectingInvoice->id, 5, '0', STR_PAD_LEFT) }}
                                </p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-600">Jumlah</p>
                                <p class="font-semibold text-gray-900">
                                    Rp{{ number_format($rejectingInvoice->amount, 0, ',', '.') }}</p>
                            </div>
                        </div>
                    @endif
                </div>
                <div class="bg-gray-50 border-t border-gray-200 px-6 py-4 flex justify-end space-x-3">
                    <button wire:click="closeRejectionModal"
                        class="px-4 py-2 text-gray-700 border border-gray-300 rounded-lg hover:bg-gray-100 font-medium transition">
                        Batal
                    </button>
                    <button wire:click="rejectPayment"
                        class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg font-medium transition">
                        Tolak
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
