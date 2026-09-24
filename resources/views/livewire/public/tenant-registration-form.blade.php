<div class="min-h-screen bg-gradient-to-br from-orange-50 via-white to-orange-50 py-8 px-4">
    <div class="max-w-lg mx-auto">
        <!-- Brand -->
        <div class="text-center mb-6">
            <div class="text-2xl font-extrabold text-orange-600">Living<span class="text-gray-900">Kost</span></div>
        </div>

        @if ($submitted)
            <!-- Thank you -->
            <div class="bg-white rounded-3xl shadow-xl border border-gray-100 p-8 text-center">
                <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-5">
                    <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                </div>
                <h1 class="text-xl font-bold text-gray-900 mb-2">Pendaftaran Terkirim!</h1>
                <p class="text-gray-500 text-sm leading-relaxed">
                    Terima kasih, <span class="font-semibold text-gray-700">{{ $name }}</span>. Data kamu sudah kami terima dan akan ditinjau oleh pengelola
                    <span class="font-semibold">{{ $kosName }}</span>. Kamu akan dihubungi melalui WhatsApp/email untuk langkah berikutnya.
                </p>
            </div>
        @else
            <div class="bg-white rounded-3xl shadow-xl border border-gray-100 overflow-hidden">
                <div class="bg-orange-600 px-6 py-5 text-white">
                    <h1 class="text-lg font-bold">Formulir Pendaftaran Penyewa</h1>
                    <p class="text-orange-100 text-sm mt-0.5">{{ $kosName }}</p>
                </div>

                <form wire:submit="submit" class="p-6 space-y-5">
                    <p class="text-sm text-gray-500 -mt-1">Isi data diri kamu di bawah ini. Kolom bertanda <span class="text-red-600">*</span> wajib diisi.</p>

                    <div>
                        <label for="name" class="block text-sm font-semibold text-gray-700 mb-1.5">Nama Lengkap <span class="text-red-600">*</span></label>
                        <input wire:model="name" type="text" id="name" placeholder="Sesuai KTP"
                            class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-orange-500 @error('name') border-red-500 @enderror">
                        @error('name') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="phone" class="block text-sm font-semibold text-gray-700 mb-1.5">No. WhatsApp <span class="text-red-600">*</span></label>
                        <input wire:model="phone" type="tel" id="phone" placeholder="0812xxxxxxxx"
                            class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-orange-500 @error('phone') border-red-500 @enderror">
                        @error('phone') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="email" class="block text-sm font-semibold text-gray-700 mb-1.5">Email <span class="text-red-600">*</span></label>
                        <input wire:model="email" type="email" id="email" placeholder="email@contoh.com"
                            class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-orange-500 @error('email') border-red-500 @enderror">
                        <p class="mt-1 text-xs text-gray-400">Dipakai untuk akun login penyewa nanti.</p>
                        @error('email') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="nik" class="block text-sm font-semibold text-gray-700 mb-1.5">NIK (16 angka) <span class="text-red-600">*</span></label>
                        <input wire:model="nik" type="text" inputmode="numeric" id="nik" placeholder="16 digit sesuai KTP"
                            class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-orange-500 @error('nik') border-red-500 @enderror">
                        @error('nik') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="emergency_contact" class="block text-sm font-semibold text-gray-700 mb-1.5">Kontak Darurat</label>
                        <input wire:model="emergency_contact" type="text" id="emergency_contact" placeholder="Nama & no. HP keluarga (opsional)"
                            class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-orange-500 @error('emergency_contact') border-red-500 @enderror">
                        @error('emergency_contact') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="ktp_photo" class="block text-sm font-semibold text-gray-700 mb-1.5">Foto KTP</label>
                        <input wire:model="ktp_photo" type="file" id="ktp_photo" accept="image/*"
                            class="w-full text-sm text-gray-600 file:mr-3 file:py-2.5 file:px-4 file:rounded-lg file:border-0 file:bg-orange-600 file:text-white file:font-semibold file:cursor-pointer @error('ktp_photo') border-red-500 @enderror">
                        <div wire:loading wire:target="ktp_photo" class="mt-1 text-xs text-orange-600">Mengunggah…</div>
                        @error('ktp_photo') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="note" class="block text-sm font-semibold text-gray-700 mb-1.5">Catatan</label>
                        <textarea wire:model="note" id="note" rows="2" placeholder="Rencana masuk, tipe kamar diminati, dll (opsional)"
                            class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-orange-500 @error('note') border-red-500 @enderror"></textarea>
                        @error('note') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <button type="submit" wire:loading.attr="disabled" wire:target="submit"
                        class="w-full bg-orange-600 text-white font-bold py-3.5 rounded-xl hover:bg-orange-700 transition disabled:opacity-60">
                        <span wire:loading.remove wire:target="submit">Kirim Pendaftaran</span>
                        <span wire:loading wire:target="submit">Mengirim…</span>
                    </button>
                </form>
            </div>
        @endif

        <p class="text-center text-xs text-gray-400 mt-6">Dikelola oleh Living Kost — Sistem Manajemen Kos</p>
    </div>
</div>
