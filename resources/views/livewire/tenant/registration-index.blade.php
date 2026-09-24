<div class="max-w-6xl mx-auto">
    <!-- Header -->
    <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Pendaftaran Penyewa</h1>
            <p class="text-gray-500 text-sm">Formulir yang diisi calon penyewa lewat tautan yang kamu bagikan.</p>
        </div>
        @if ($this->shareUrl)
            <button wire:click="toggleShare"
                class="inline-flex items-center justify-center gap-2 bg-orange-600 text-white px-5 py-2.5 rounded-full font-semibold hover:bg-orange-700 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z"/></svg>
                Bagikan Formulir
            </button>
        @endif
    </div>

    <!-- Share panel -->
    @if ($this->shareUrl && $showShare)
        <div x-data="{ url: @js($this->shareUrl), copied: false }"
            class="mb-6 bg-orange-50 border border-orange-100 rounded-2xl p-5">
            <p class="text-sm font-semibold text-gray-700 mb-1">Tautan formulir pendaftaran</p>
            <p class="text-xs text-gray-500 mb-3">Bagikan tautan ini ke calon penyewa. Mereka bisa mengisi data sendiri, lalu masuk ke daftar di bawah untuk kamu setujui.</p>
            <div class="flex flex-col sm:flex-row gap-2">
                <input type="text" readonly :value="url" x-ref="link"
                    class="flex-1 px-4 py-2.5 border border-gray-300 rounded-lg bg-white text-sm text-gray-700">
                <button type="button"
                    @click="navigator.clipboard.writeText(url).then(() => { copied = true; setTimeout(() => copied = false, 2000); })"
                    class="px-4 py-2.5 bg-gray-800 text-white rounded-lg text-sm font-semibold hover:bg-gray-900 transition whitespace-nowrap">
                    <span x-show="!copied">Salin</span>
                    <span x-show="copied" x-cloak>Tersalin ✓</span>
                </button>
                <a :href="'https://wa.me/?text=' + encodeURIComponent('Halo, silakan isi formulir pendaftaran penyewa di: ' + url)" target="_blank"
                    class="px-4 py-2.5 bg-green-500 text-white rounded-lg text-sm font-semibold hover:bg-green-600 transition whitespace-nowrap text-center">
                    Kirim via WhatsApp
                </a>
            </div>
        </div>
    @endif

    <!-- Notice -->
    @if ($notice)
        <div class="mb-5 bg-blue-50 border border-blue-200 text-blue-800 px-4 py-3 rounded-xl text-sm flex items-start justify-between gap-3">
            <span>{{ $notice }}</span>
            <button wire:click="$set('notice', '')" class="text-blue-400 hover:text-blue-600 font-bold">&times;</button>
        </div>
    @endif

    <!-- Status filter -->
    <div class="flex flex-wrap gap-2 mb-5">
        @foreach (['pending' => 'Menunggu', 'approved' => 'Disetujui', 'rejected' => 'Ditolak'] as $val => $label)
            <button wire:click="$set('filterStatus', '{{ $val }}')"
                class="px-4 py-2 rounded-full text-sm font-semibold transition {{ $filterStatus === $val ? 'bg-orange-600 text-white' : 'bg-white border border-gray-200 text-gray-600 hover:border-orange-400' }}">
                {{ $label }}
                @if ($val === 'pending' && $pendingCount > 0)
                    <span class="ml-1 inline-flex items-center justify-center bg-red-500 text-white text-xs rounded-full w-5 h-5">{{ $pendingCount }}</span>
                @endif
            </button>
        @endforeach
    </div>

    <!-- List -->
    <div class="space-y-4">
        @forelse ($registrations as $reg)
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
                <div class="flex flex-col md:flex-row md:items-start md:justify-between gap-4">
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-2 mb-2">
                            <h3 class="font-bold text-gray-900">{{ $reg->name }}</h3>
                            @if ($reg->status === 'approved')
                                <span class="text-xs font-bold px-2 py-0.5 rounded-full bg-green-50 text-green-600">Disetujui</span>
                            @elseif ($reg->status === 'rejected')
                                <span class="text-xs font-bold px-2 py-0.5 rounded-full bg-gray-100 text-gray-500">Ditolak</span>
                            @else
                                <span class="text-xs font-bold px-2 py-0.5 rounded-full bg-orange-50 text-orange-600">Menunggu</span>
                            @endif
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-1 text-sm text-gray-600">
                            <p><span class="text-gray-400">HP:</span> {{ $reg->phone }}</p>
                            <p class="truncate"><span class="text-gray-400">Email:</span> {{ $reg->email ?: '-' }}</p>
                            <p><span class="text-gray-400">NIK:</span> {{ $reg->nik ?: '-' }}</p>
                            <p class="truncate"><span class="text-gray-400">Darurat:</span> {{ $reg->emergency_contact ?: '-' }}</p>
                        </div>
                        @if ($reg->note)
                            <p class="mt-2 text-sm text-gray-500 italic">"{{ $reg->note }}"</p>
                        @endif

                        @if ($reg->dp_method)
                            <div class="mt-2.5 flex flex-wrap items-center gap-2 text-xs">
                                <span class="font-semibold text-gray-600">DP Rp {{ number_format((float) $reg->dp_amount, 0, ',', '.') }}</span>
                                @if ($reg->dp_method === 'online')
                                    @if ($reg->dp_status === 'paid')
                                        <span class="px-2 py-0.5 rounded-full bg-green-50 text-green-600 font-bold">Online · Lunas ✓</span>
                                    @else
                                        <span class="px-2 py-0.5 rounded-full bg-yellow-50 text-yellow-700 font-bold">Online · Belum bayar</span>
                                    @endif
                                @else
                                    <span class="px-2 py-0.5 rounded-full bg-blue-50 text-blue-600 font-bold">Transfer Manual</span>
                                    @if ($reg->dp_proof)
                                        <a href="{{ \Illuminate\Support\Facades\Storage::url($reg->dp_proof) }}" target="_blank" class="text-orange-600 font-semibold hover:underline">Lihat Bukti DP</a>
                                    @endif
                                @endif
                            </div>
                        @endif

                        <div class="mt-2 flex items-center gap-4 text-xs text-gray-400">
                            <span>{{ $reg->created_at->format('d/m/Y H:i') }}</span>
                            @if ($reg->ktp_photo)
                                <a href="{{ \Illuminate\Support\Facades\Storage::url($reg->ktp_photo) }}" target="_blank" class="text-orange-600 font-semibold hover:underline">Lihat Foto KTP</a>
                            @endif
                        </div>
                    </div>

                    @if ($reg->status === 'pending')
                        <div class="flex md:flex-col gap-2 shrink-0">
                            <button wire:click="approve({{ $reg->id }})" wire:loading.attr="disabled"
                                wire:confirm="Setujui pendaftaran {{ $reg->name }}? Penyewa & akun login akan dibuat."
                                class="flex-1 md:flex-none px-4 py-2 bg-green-600 text-white rounded-lg text-sm font-semibold hover:bg-green-700 transition">Setujui</button>
                            <button wire:click="reject({{ $reg->id }})" wire:loading.attr="disabled"
                                wire:confirm="Tolak pendaftaran ini?"
                                class="flex-1 md:flex-none px-4 py-2 bg-white border border-gray-300 text-gray-600 rounded-lg text-sm font-semibold hover:bg-gray-50 transition">Tolak</button>
                        </div>
                    @endif
                </div>
            </div>
        @empty
            <div class="bg-white rounded-2xl border border-gray-100 p-12 text-center text-gray-400">
                Belum ada pendaftaran {{ ['pending' => 'yang menunggu', 'approved' => 'yang disetujui', 'rejected' => 'yang ditolak'][$filterStatus] ?? '' }}.
            </div>
        @endforelse
    </div>

    <div class="mt-6">
        {{ $registrations->links() }}
    </div>
</div>
