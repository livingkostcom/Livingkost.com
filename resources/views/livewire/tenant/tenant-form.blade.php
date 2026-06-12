<form wire:submit.prevent="save" class="space-y-4">
    <!-- Name Input -->
    <div>
        <label for="name" class="block text-sm font-semibold text-gray-700 mb-2">
            Nama Lengkap <span class="text-red-600">*</span>
        </label>
        <input wire:model="name" type="text" id="name" placeholder="Contoh: Budi Santoso"
            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition @error('name') border-red-500 @enderror">
        @error('name')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <!-- Email Input -->
    <div>
        <label for="email" class="block text-sm font-semibold text-gray-700 mb-2">
            Email <span class="text-red-600">*</span>
        </label>
        <input wire:model="email" type="email" id="email" placeholder="Contoh: budi@example.com"
            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition @error('email') border-red-500 @enderror">
        @error('email')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <!-- NIK Input -->
    <div>
        <label for="nik" class="block text-sm font-semibold text-gray-700 mb-2">
            NIK (16 digit) <span class="text-red-600">*</span>
        </label>
        <input wire:model="nik" type="text" id="nik" placeholder="Contoh: 1234567890123456" maxlength="16"
            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition @error('nik') border-red-500 @enderror">
        @error('nik')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <!-- Phone Input -->
    <div>
        <label for="phone" class="block text-sm font-semibold text-gray-700 mb-2">
            Nomor Telepon <span class="text-red-600">*</span>
        </label>
        <input wire:model="phone" type="tel" id="phone" placeholder="Contoh: 081234567890"
            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition @error('phone') border-red-500 @enderror">
        @error('phone')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <!-- Emergency Contact Input -->
    <div>
        <label for="emergency_contact" class="block text-sm font-semibold text-gray-700 mb-2">
            Kontak Darurat <span class="text-gray-500 text-xs">(Opsional)</span>
        </label>
        <input wire:model="emergency_contact" type="text" id="emergency_contact"
            placeholder="Contoh: Nama + Nomor Telepon"
            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition @error('emergency_contact') border-red-500 @enderror">
        @error('emergency_contact')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <!-- Avatar Upload -->
    <div>
        <label for="avatar" class="block text-sm font-semibold text-gray-700 mb-2">
            Foto Profil <span class="text-gray-500 text-xs">(Opsional)</span>
        </label>
        <input wire:model="avatar" type="file" id="avatar" accept="image/*"
            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition @error('avatar') border-red-500 @enderror">
        @error('avatar')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <!-- KTP Photo Upload -->
    <div>
        <label for="ktp_photo" class="block text-sm font-semibold text-gray-700 mb-2">
            Foto KTP <span class="text-gray-500 text-xs">(Opsional)</span>
        </label>
        <input wire:model="ktp_photo" type="file" id="ktp_photo" accept="image/*"
            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition @error('ktp_photo') border-red-500 @enderror">
        @error('ktp_photo')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <!-- Status Select -->
    <div>
        <label for="status" class="block text-sm font-semibold text-gray-700 mb-2">
            Status <span class="text-red-600">*</span>
        </label>
        <select wire:model="status" id="status"
            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition @error('status') border-red-500 @enderror">
            <option value="active">Aktif</option>
            <option value="inactive">Tidak Aktif</option>
            <option value="evicted">Keluar</option>
        </select>
        @error('status')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <!-- Action Buttons -->
    <div class="flex gap-3 pt-4">
        <button type="button" wire:click="$parent.closeModal()"
            class="flex-1 px-4 py-3 bg-gray-200 text-gray-800 font-semibold rounded-lg hover:bg-gray-300 transition">
            Batal
        </button>
        <button type="submit"
            class="flex-1 px-4 py-3 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 transition flex items-center justify-center"
            wire:loading.attr="disabled" wire:loading.class="opacity-50">
            <span wire:loading.remove>
                {{ $tenantId ? 'Perbarui' : 'Simpan' }}
            </span>
            <span wire:loading class="flex items-center gap-2">
                <svg class="w-5 h-5 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15">
                    </path>
                </svg>
                Memproses...
            </span>
        </button>
    </div>
</form>
