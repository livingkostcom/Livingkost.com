<div>
    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-2xl sm:text-4xl font-bold text-orange-600">Kemitraan Pembayaran</h1>
        <p class="mt-2 text-gray-600">Aktifkan/nonaktifkan pembayaran online (DOKU) per owner dan atur fee platform.</p>
    </div>

    <!-- Flash -->
    @if ($successMessage)
        <div class="mb-6 p-4 bg-green-50 border border-green-200 rounded-xl text-green-800 shadow-sm"
            x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)">{{ $successMessage }}</div>
    @endif
    @if ($errorMessage)
        <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-xl text-red-800 shadow-sm">{{ $errorMessage }}</div>
    @endif

    <!-- Search -->
    <div class="mb-6">
        <input wire:model.live="search" type="text" placeholder="Cari nama atau email owner..."
            class="w-full px-4 py-3 bg-white border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-orange-500 transition shadow-sm">
    </div>

    <!-- Table -->
    <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gradient-to-r from-gray-50 to-gray-100">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wide whitespace-nowrap">Owner</th>
                        <th class="px-6 py-4 text-center text-xs font-semibold text-gray-700 uppercase tracking-wide whitespace-nowrap">Pembayaran Online</th>
                        <th class="px-6 py-4 text-center text-xs font-semibold text-gray-700 uppercase tracking-wide whitespace-nowrap">Fee Platform (%)</th>
                        <th class="px-6 py-4 text-right text-xs font-semibold text-gray-700 uppercase tracking-wide whitespace-nowrap">Saldo Dompet</th>
                        <th class="px-6 py-4 text-right text-xs font-semibold text-gray-700 uppercase tracking-wide whitespace-nowrap">Total Masuk / Cair</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse ($owners as $o)
                        <tr class="hover:bg-orange-50 transition">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <p class="font-semibold text-gray-900">{{ $o['name'] }}</p>
                                <p class="text-xs text-gray-500">{{ $o['email'] }}</p>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <button wire:click="toggle({{ $o['id'] }})" type="button"
                                    class="relative inline-flex h-6 w-11 items-center rounded-full transition {{ $o['enabled'] ? 'bg-green-500' : 'bg-gray-300' }}"
                                    title="{{ $o['enabled'] ? 'Klik untuk menonaktifkan' : 'Klik untuk mengaktifkan' }}">
                                    <span class="inline-block h-4 w-4 transform rounded-full bg-white transition {{ $o['enabled'] ? 'translate-x-6' : 'translate-x-1' }}"></span>
                                </button>
                                <p class="mt-1 text-xs font-medium {{ $o['enabled'] ? 'text-green-600' : 'text-gray-400' }}">{{ $o['enabled'] ? 'Aktif' : 'Nonaktif' }}</p>
                            </td>
                            <td class="px-6 py-4 text-center whitespace-nowrap">
                                <div class="inline-flex items-center gap-2">
                                    <input wire:model="fees.{{ $o['id'] }}" type="number" step="0.1" min="0" max="100"
                                        class="w-20 px-2 py-1.5 border border-gray-300 rounded-lg text-center focus:outline-none focus:ring-2 focus:ring-orange-500">
                                    <button wire:click="saveFee({{ $o['id'] }})" type="button"
                                        class="px-3 py-1.5 bg-orange-50 text-orange-700 rounded-lg hover:bg-orange-100 border border-orange-200 text-sm font-medium">Simpan</button>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-right font-bold text-gray-900 whitespace-nowrap">Rp {{ number_format($o['balance'], 0, ',', '.') }}</td>
                            <td class="px-6 py-4 text-right text-xs text-gray-600 whitespace-nowrap">
                                <p class="text-green-600">+Rp {{ number_format($o['total_earned'], 0, ',', '.') }}</p>
                                <p class="text-gray-400">-Rp {{ number_format($o['total_disbursed'], 0, ',', '.') }}</p>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="px-6 py-12 text-center text-gray-500">Belum ada owner terdaftar.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <p class="mt-4 text-sm text-gray-500">
        Saat pembayaran online aktif, penyewa owner tersebut dapat membayar invoice via DOKU. Dana masuk ke dompet owner (dipotong fee platform), lalu dicairkan manual oleh super admin di menu Pencairan.
    </p>
</div>
