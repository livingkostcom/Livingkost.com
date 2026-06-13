@php
    $disbStatus = [
        'pending' => ['label' => 'Menunggu', 'class' => 'bg-yellow-100 text-yellow-700'],
        'processing' => ['label' => 'Diproses', 'class' => 'bg-blue-100 text-blue-700'],
        'completed' => ['label' => 'Selesai', 'class' => 'bg-green-100 text-green-700'],
        'rejected' => ['label' => 'Ditolak', 'class' => 'bg-red-100 text-red-700'],
    ];
@endphp
<div>
    <div class="mb-8">
        <h1 class="text-2xl sm:text-4xl font-bold text-orange-600">Pencairan</h1>
        <p class="mt-2 text-gray-600">Cairkan saldo dompet owner ke rekeningnya dan lacak statusnya.</p>
    </div>

    @if ($successMessage)
        <div class="mb-6 p-4 bg-green-50 border border-green-200 rounded-xl text-green-800 shadow-sm"
            x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)">{{ $successMessage }}</div>
    @endif
    @if ($errorMessage)
        <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-xl text-red-800 shadow-sm">{{ $errorMessage }}</div>
    @endif

    <!-- Owners with balance -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden mb-8">
        <div class="px-6 py-4 border-b border-gray-100"><h3 class="text-lg font-bold text-gray-900">Saldo Owner</h3></div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wide whitespace-nowrap">Owner</th>
                        <th class="px-6 py-3 text-right text-xs font-semibold text-gray-700 uppercase tracking-wide whitespace-nowrap">Saldo Tersedia</th>
                        <th class="px-6 py-3 text-center text-xs font-semibold text-gray-700 uppercase tracking-wide whitespace-nowrap">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse ($owners as $o)
                        <tr class="hover:bg-orange-50 transition">
                            <td class="px-6 py-4 font-semibold text-gray-900 whitespace-nowrap">{{ $o['name'] }}</td>
                            <td class="px-6 py-4 text-right font-bold text-gray-900 whitespace-nowrap">Rp {{ number_format($o['balance'], 0, ',', '.') }}</td>
                            <td class="px-6 py-4 text-center">
                                <button wire:click="openCreate({{ $o['id'] }})" @disabled($o['balance'] <= 0)
                                    class="inline-flex items-center px-4 py-1.5 rounded-lg text-sm font-medium border {{ $o['balance'] > 0 ? 'bg-orange-50 text-orange-700 border-orange-200 hover:bg-orange-100' : 'bg-gray-50 text-gray-400 border-gray-200 cursor-not-allowed' }}">
                                    Cairkan
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="3" class="px-6 py-8 text-center text-gray-500">Belum ada owner.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Disbursement history -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100"><h3 class="text-lg font-bold text-gray-900">Riwayat Pencairan</h3></div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wide whitespace-nowrap">Tanggal</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wide whitespace-nowrap">Owner</th>
                        <th class="px-6 py-3 text-right text-xs font-semibold text-gray-700 uppercase tracking-wide whitespace-nowrap">Jumlah</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wide whitespace-nowrap">Rekening</th>
                        <th class="px-6 py-3 text-center text-xs font-semibold text-gray-700 uppercase tracking-wide whitespace-nowrap">Status</th>
                        <th class="px-6 py-3 text-center text-xs font-semibold text-gray-700 uppercase tracking-wide whitespace-nowrap">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse ($disbursements as $d)
                        <tr>
                            <td class="px-6 py-4 text-sm text-gray-600 whitespace-nowrap">{{ $d->created_at->translatedFormat('d M Y') }}</td>
                            <td class="px-6 py-4 text-sm font-medium text-gray-900 whitespace-nowrap">{{ $d->owner->name ?? '-' }}</td>
                            <td class="px-6 py-4 text-right font-semibold text-gray-900 whitespace-nowrap">Rp {{ number_format($d->amount, 0, ',', '.') }}</td>
                            <td class="px-6 py-4 text-sm text-gray-600">{{ $d->bank_name }} {{ $d->bank_account_number }}<br><span class="text-xs text-gray-400">{{ $d->bank_account_holder }}</span>
                                @if ($d->proof_path)
                                    <br><a href="{{ route('disbursements.proof', $d->id) }}" target="_blank" class="text-xs text-orange-600 hover:text-orange-800 font-medium">Lihat bukti transfer</a>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium {{ $disbStatus[$d->status]['class'] ?? 'bg-gray-100 text-gray-700' }}">{{ $disbStatus[$d->status]['label'] ?? $d->status }}</span>
                            </td>
                            <td class="px-6 py-4 text-center whitespace-nowrap">
                                @if ($d->status !== 'completed' && $d->status !== 'rejected')
                                    <div class="flex items-center justify-center gap-1">
                                        <button wire:click="setStatus({{ $d->id }}, 'completed')"
                                            wire:confirm="Tandai pencairan ini SELESAI? Saldo owner akan dipotong Rp {{ number_format($d->amount, 0, ',', '.') }}."
                                            class="px-2.5 py-1 bg-green-50 text-green-700 rounded-lg border border-green-200 hover:bg-green-100 text-xs font-medium">Selesai</button>
                                        <button wire:click="setStatus({{ $d->id }}, 'rejected')"
                                            wire:confirm="Tolak/batalkan pencairan ini?"
                                            class="px-2.5 py-1 bg-red-50 text-red-700 rounded-lg border border-red-200 hover:bg-red-100 text-xs font-medium">Tolak</button>
                                    </div>
                                @else
                                    <span class="text-xs text-gray-400">{{ $d->processed_at?->translatedFormat('d M Y H:i') }}</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="px-6 py-8 text-center text-gray-500">Belum ada pencairan.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4">{{ $disbursements->links('vendor.pagination.tailwind') }}</div>
    </div>

    <!-- Create modal -->
    @if ($showCreateModal)
        <div class="fixed inset-0 bg-black/40 z-50 flex items-center justify-center p-4 backdrop-blur-sm">
            <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full overflow-y-auto max-h-[90vh]">
                <div class="bg-orange-600 px-6 py-4 flex items-center justify-between">
                    <h2 class="text-lg font-bold text-white">Cairkan ke {{ $createOwnerName }}</h2>
                    <button wire:click="closeCreate" class="text-white/80 hover:text-white">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
                <div class="p-6 space-y-4">
                    <p class="text-sm text-gray-500">Saldo tersedia: <span class="font-bold text-gray-900">Rp {{ number_format($maxAmount, 0, ',', '.') }}</span></p>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Jumlah Pencairan (Rp)</label>
                        <input wire:model="amount" type="number" min="1" max="{{ $maxAmount }}" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500">
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">Bank</label>
                            <input wire:model="bankName" type="text" class="w-full px-3 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">No. Rekening</label>
                            <input wire:model="bankAccountNumber" type="text" class="w-full px-3 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500">
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Atas Nama</label>
                        <input wire:model="bankAccountHolder" type="text" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Catatan (opsional)</label>
                        <input wire:model="notes" type="text" placeholder="No. referensi transfer, dll" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Bukti Transfer (opsional)</label>
                        <input wire:model="proofFile" type="file" accept=".jpg,.jpeg,.png,.pdf"
                            class="w-full px-3 py-2 border-2 border-dashed border-gray-300 rounded-lg text-sm cursor-pointer focus:outline-none focus:border-orange-500">
                        <p class="text-xs text-gray-500 mt-1">JPG, PNG, atau PDF • maks 5MB</p>
                        <div wire:loading wire:target="proofFile" class="text-xs text-orange-600 mt-1">Mengunggah...</div>
                        @error('proofFile') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                        @if ($proofFile)
                            <p class="text-xs text-green-600 mt-1">✓ Siap: {{ $proofFile->getClientOriginalName() }}</p>
                        @endif
                    </div>
                    @if ($errorMessage)
                        <p class="text-sm text-red-600">{{ $errorMessage }}</p>
                    @endif
                    <p class="text-xs text-gray-500">Transfer dilakukan manual di luar sistem. Buat catatan ini lalu tandai "Selesai" setelah transfer berhasil — saldo owner akan otomatis terpotong.</p>
                </div>
                <div class="bg-gray-50 px-6 py-4 flex gap-3">
                    <button wire:click="closeCreate" class="flex-1 px-4 py-2.5 bg-gray-200 hover:bg-gray-300 text-gray-800 font-semibold rounded-xl">Batal</button>
                    <button wire:click="createDisbursement" class="flex-1 px-4 py-2.5 bg-orange-600 hover:bg-orange-700 text-white font-semibold rounded-xl">Buat Pencairan</button>
                </div>
            </div>
        </div>
    @endif
</div>
