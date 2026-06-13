@php
    $disbStatus = [
        'pending' => ['label' => 'Menunggu', 'class' => 'bg-yellow-100 text-yellow-700'],
        'processing' => ['label' => 'Diproses', 'class' => 'bg-blue-100 text-blue-700'],
        'completed' => ['label' => 'Selesai', 'class' => 'bg-green-100 text-green-700'],
        'rejected' => ['label' => 'Ditolak', 'class' => 'bg-red-100 text-red-700'],
    ];
@endphp
<div>
    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-2xl sm:text-4xl font-bold text-orange-600">Dompet</h1>
        <p class="mt-2 text-gray-600">Pendapatan pembayaran online dan status pencairan ke rekening Anda.</p>
    </div>

    <!-- Balance cards -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-8">
        <div class="bg-gradient-to-br from-orange-600 to-orange-500 text-white rounded-2xl shadow-lg p-6">
            <p class="text-sm text-orange-100">Saldo Tersedia</p>
            <p class="text-3xl font-bold mt-1">Rp {{ number_format($wallet->balance, 0, ',', '.') }}</p>
            <p class="text-xs text-orange-100 mt-2">Akan dicairkan oleh admin ke rekening Anda</p>
        </div>
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <p class="text-sm text-gray-500">Total Masuk</p>
            <p class="text-2xl font-bold text-green-600 mt-1">Rp {{ number_format($wallet->total_earned, 0, ',', '.') }}</p>
            <p class="text-xs text-gray-400 mt-2">Sepanjang waktu (setelah fee platform)</p>
        </div>
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <p class="text-sm text-gray-500">Total Dicairkan</p>
            <p class="text-2xl font-bold text-gray-700 mt-1">Rp {{ number_format($wallet->total_disbursed, 0, ',', '.') }}</p>
            <p class="text-xs text-gray-400 mt-2">Sudah ditransfer ke rekening Anda</p>
        </div>
    </div>

    @if (!$wallet->online_payment_enabled)
        <div class="mb-8 p-4 bg-blue-50 border border-blue-200 rounded-xl text-blue-800 text-sm">
            Pembayaran online belum diaktifkan untuk akun Anda. Hubungi admin Living Kost untuk mengaktifkan agar penyewa dapat membayar online.
        </div>
    @endif

    <!-- Disbursements -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden mb-8">
        <div class="px-6 py-4 border-b border-gray-100">
            <h3 class="text-lg font-bold text-gray-900">Riwayat Pencairan</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wide whitespace-nowrap">Tanggal</th>
                        <th class="px-6 py-3 text-right text-xs font-semibold text-gray-700 uppercase tracking-wide whitespace-nowrap">Jumlah</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wide whitespace-nowrap">Rekening Tujuan</th>
                        <th class="px-6 py-3 text-center text-xs font-semibold text-gray-700 uppercase tracking-wide whitespace-nowrap">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse ($disbursements as $d)
                        <tr>
                            <td class="px-6 py-4 text-sm text-gray-600 whitespace-nowrap">{{ $d->created_at->translatedFormat('d M Y') }}</td>
                            <td class="px-6 py-4 text-right font-semibold text-gray-900 whitespace-nowrap">Rp {{ number_format($d->amount, 0, ',', '.') }}</td>
                            <td class="px-6 py-4 text-sm text-gray-600">{{ $d->bank_name }} {{ $d->bank_account_number }}<br><span class="text-xs text-gray-400">{{ $d->bank_account_holder }}</span>
                                @if ($d->proof_path)
                                    <br><a href="{{ route('disbursements.proof', $d->id) }}" target="_blank" class="text-xs text-orange-600 hover:text-orange-800 font-medium">Lihat bukti transfer</a>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium {{ $disbStatus[$d->status]['class'] ?? 'bg-gray-100 text-gray-700' }}">{{ $disbStatus[$d->status]['label'] ?? $d->status }}</span>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="px-6 py-8 text-center text-gray-500">Belum ada pencairan.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Transactions ledger -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100">
            <h3 class="text-lg font-bold text-gray-900">Riwayat Transaksi</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wide whitespace-nowrap">Tanggal</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wide whitespace-nowrap">Keterangan</th>
                        <th class="px-6 py-3 text-right text-xs font-semibold text-gray-700 uppercase tracking-wide whitespace-nowrap">Jumlah</th>
                        <th class="px-6 py-3 text-right text-xs font-semibold text-gray-700 uppercase tracking-wide whitespace-nowrap">Saldo</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse ($transactions as $t)
                        <tr>
                            <td class="px-6 py-4 text-sm text-gray-600 whitespace-nowrap">{{ $t->created_at->translatedFormat('d M Y H:i') }}</td>
                            <td class="px-6 py-4 text-sm text-gray-700">{{ $t->description }}</td>
                            <td class="px-6 py-4 text-right font-semibold whitespace-nowrap {{ $t->type === 'credit' ? 'text-green-600' : 'text-red-600' }}">
                                {{ $t->type === 'credit' ? '+' : '-' }}Rp {{ number_format($t->amount, 0, ',', '.') }}
                            </td>
                            <td class="px-6 py-4 text-right text-sm text-gray-500 whitespace-nowrap">Rp {{ number_format($t->balance_after, 0, ',', '.') }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="px-6 py-8 text-center text-gray-500">Belum ada transaksi.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4">{{ $transactions->links('vendor.pagination.tailwind') }}</div>
    </div>
</div>
