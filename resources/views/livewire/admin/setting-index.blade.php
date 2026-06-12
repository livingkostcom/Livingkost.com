<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    {{-- Flash Message --}}
    @if (session()->has('message'))
        <div class="mb-6 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg flex items-center justify-between"
            x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)"
            x-transition:leave="transition ease-in duration-300" x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0">
            <span>{{ session('message') }}</span>
            <button @click="show = false" class="text-green-500 hover:text-green-700">&times;</button>
        </div>
    @endif

    {{-- Header --}}
    <div class="mb-8">
        <h1 class="text-3xl font-bold bg-gradient-to-r from-gray-700 to-gray-900 bg-clip-text text-transparent">
            Pengaturan Sistem
        </h1>
        <p class="text-gray-500 mt-1">Konfigurasi aplikasi dan pengaturan kos</p>
    </div>

    {{-- Tabs --}}
    <div class="flex gap-1 mb-6 bg-gray-100 p-1 rounded-xl overflow-x-auto">
        <button wire:click="$set('activeTab', 'general')"
            class="px-5 py-2.5 rounded-lg text-sm font-medium transition-all {{ $activeTab === 'general' ? 'bg-white shadow text-gray-800' : 'text-gray-500 hover:text-gray-700' }}">
            <span class="flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                </svg>
                Umum
            </span>
        </button>
        <button wire:click="$set('activeTab', 'payment')"
            class="px-5 py-2.5 rounded-lg text-sm font-medium transition-all {{ $activeTab === 'payment' ? 'bg-white shadow text-gray-800' : 'text-gray-500 hover:text-gray-700' }}">
            <span class="flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                </svg>
                Pembayaran
            </span>
        </button>
        <button wire:click="$set('activeTab', 'late_fee')"
            class="px-5 py-2.5 rounded-lg text-sm font-medium transition-all {{ $activeTab === 'late_fee' ? 'bg-white shadow text-gray-800' : 'text-gray-500 hover:text-gray-700' }}">
            <span class="flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                Denda
            </span>
        </button>
    </div>

    {{-- General Settings Tab --}}
    @if ($activeTab === 'general')
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="p-6 border-b border-gray-100 bg-gradient-to-r from-gray-50 to-white">
                <h3 class="text-lg font-bold text-gray-800">Pengaturan Umum</h3>
                <p class="text-sm text-gray-500 mt-1">Informasi dasar tentang kos Anda</p>
            </div>
            <div class="p-6 space-y-5">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nama Kos <span
                            class="text-red-500">*</span></label>
                    <input wire:model="app_name" type="text" placeholder="Nama kos Anda..."
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition">
                    @error('app_name')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tagline</label>
                    <input wire:model="app_tagline" type="text" placeholder="Sistem Manajemen Kos"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition">
                    @error('app_tagline')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Alamat</label>
                    <textarea wire:model="app_address" rows="2" placeholder="Alamat lengkap kos..."
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition"></textarea>
                    @error('app_address')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">No. Telepon</label>
                        <input wire:model="app_phone" type="text" placeholder="08xx-xxxx-xxxx"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition">
                        @error('app_phone')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                        <input wire:model="app_email" type="email" placeholder="info@kos.com"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition">
                        @error('app_email')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>
            <div class="p-6 border-t border-gray-100 bg-gray-50 flex justify-end">
                <button wire:click="saveGeneral"
                    class="px-6 py-2.5 bg-gradient-to-r from-blue-600 to-indigo-600 text-white rounded-xl font-semibold shadow hover:shadow-lg transition">
                    Simpan Pengaturan Umum
                </button>
            </div>
        </div>
    @endif

    {{-- Payment Settings Tab --}}
    @if ($activeTab === 'payment')
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="p-6 border-b border-gray-100 bg-gradient-to-r from-gray-50 to-white">
                <h3 class="text-lg font-bold text-gray-800">Pengaturan Pembayaran</h3>
                <p class="text-sm text-gray-500 mt-1">Informasi rekening dan metode pembayaran</p>
            </div>
            <div class="p-6 space-y-5">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nama Bank</label>
                        <input wire:model="bank_name" type="text" placeholder="BCA, BNI, Mandiri, dll"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition">
                        @error('bank_name')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">No. Rekening</label>
                        <input wire:model="bank_account_number" type="text" placeholder="1234567890"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition">
                        @error('bank_account_number')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Atas Nama</label>
                    <input wire:model="bank_account_holder" type="text" placeholder="Nama pemilik rekening"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition">
                    @error('bank_account_holder')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Instruksi Pembayaran</label>
                    <textarea wire:model="payment_instructions" rows="4"
                        placeholder="Cara pembayaran, catatan penting untuk penyewa..."
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition"></textarea>
                    @error('payment_instructions')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Preview Card --}}
                @if ($bank_name || $bank_account_number)
                    <div class="bg-gradient-to-r from-blue-600 to-indigo-700 rounded-xl p-5 text-white">
                        <p class="text-sm text-blue-200 mb-1">Preview Informasi Pembayaran</p>
                        <p class="text-lg font-bold">{{ $bank_name ?: '-' }}</p>
                        <p class="text-2xl font-mono tracking-wider mt-1">{{ $bank_account_number ?: '-' }}</p>
                        <p class="text-sm text-blue-200 mt-2">a.n. {{ $bank_account_holder ?: '-' }}</p>
                    </div>
                @endif
            </div>
            <div class="p-6 border-t border-gray-100 bg-gray-50 flex justify-end">
                <button wire:click="savePayment"
                    class="px-6 py-2.5 bg-gradient-to-r from-blue-600 to-indigo-600 text-white rounded-xl font-semibold shadow hover:shadow-lg transition">
                    Simpan Pengaturan Pembayaran
                </button>
            </div>
        </div>
    @endif

    {{-- Late Fee Settings Tab --}}
    @if ($activeTab === 'late_fee')
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="p-6 border-b border-gray-100 bg-gradient-to-r from-gray-50 to-white">
                <h3 class="text-lg font-bold text-gray-800">Pengaturan Denda Keterlambatan</h3>
                <p class="text-sm text-gray-500 mt-1">Atur denda otomatis untuk pembayaran yang terlambat</p>
            </div>
            <div class="p-6 space-y-5">
                {{-- Enable toggle --}}
                <div class="flex items-center justify-between p-4 bg-gray-50 rounded-xl">
                    <div>
                        <p class="font-medium text-gray-800">Aktifkan Denda Keterlambatan</p>
                        <p class="text-sm text-gray-500 mt-0.5">Denda akan dihitung otomatis untuk invoice yang
                            melewati jatuh tempo</p>
                    </div>
                    <button wire:click="$set('late_fee_enabled', '{{ $late_fee_enabled === '1' ? '0' : '1' }}')"
                        class="relative inline-flex h-6 w-11 items-center rounded-full transition-colors focus:outline-none {{ $late_fee_enabled === '1' ? 'bg-green-500' : 'bg-gray-300' }}">
                        <span
                            class="inline-block h-4 w-4 transform rounded-full bg-white transition-transform {{ $late_fee_enabled === '1' ? 'translate-x-6' : 'translate-x-1' }}"></span>
                    </button>
                </div>

                @if ($late_fee_enabled === '1')
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Tipe Denda</label>
                            <select wire:model.live="late_fee_type"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition">
                                <option value="fixed">Nominal Tetap (Rp)</option>
                                <option value="percentage">Persentase (%)</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                {{ $late_fee_type === 'percentage' ? 'Persentase Denda (%)' : 'Nominal Denda (Rp)' }}
                            </label>
                            <input wire:model="late_fee_amount" type="number" min="0"
                                step="{{ $late_fee_type === 'percentage' ? '0.1' : '1000' }}"
                                placeholder="{{ $late_fee_type === 'percentage' ? '5' : '50000' }}"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition">
                            @error('late_fee_amount')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Masa Tenggang (hari)</label>
                        <input wire:model="late_fee_grace_days" type="number" min="0" max="30"
                            placeholder="3"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition sm:max-w-xs">
                        <p class="text-xs text-gray-400 mt-1">Jumlah hari setelah jatuh tempo sebelum denda mulai
                            berlaku</p>
                        @error('late_fee_grace_days')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Example --}}
                    <div class="bg-amber-50 border border-amber-200 rounded-xl p-4">
                        <p class="text-sm font-medium text-amber-800 mb-1">Contoh Perhitungan:</p>
                        @if ($late_fee_type === 'percentage')
                            <p class="text-sm text-amber-700">
                                Tagihan Rp 1.000.000 × {{ $late_fee_amount ?: 0 }}% = Rp
                                {{ number_format(1000000 * (floatval($late_fee_amount ?: 0) / 100), 0, ',', '.') }}
                                denda per bulan.
                                Berlaku setelah {{ $late_fee_grace_days }} hari dari jatuh tempo.
                            </p>
                        @else
                            <p class="text-sm text-amber-700">
                                Denda tetap Rp {{ number_format(floatval($late_fee_amount ?: 0), 0, ',', '.') }} per
                                invoice yang terlambat.
                                Berlaku setelah {{ $late_fee_grace_days }} hari dari jatuh tempo.
                            </p>
                        @endif
                    </div>
                @endif
            </div>
            <div class="p-6 border-t border-gray-100 bg-gray-50 flex justify-end">
                <button wire:click="saveLateFee"
                    class="px-6 py-2.5 bg-gradient-to-r from-blue-600 to-indigo-600 text-white rounded-xl font-semibold shadow hover:shadow-lg transition">
                    Simpan Pengaturan Denda
                </button>
            </div>
        </div>
    @endif
</div>
