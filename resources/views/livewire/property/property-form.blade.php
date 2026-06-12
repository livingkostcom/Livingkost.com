<form wire:submit.prevent="save" class="space-y-4">
    <!-- Name Input -->
    <div>
        <label for="name" class="block text-sm font-semibold text-gray-700 mb-2">
            Nama Property <span class="text-red-600">*</span>
        </label>
        <input wire:model="name" type="text" id="name" placeholder="Contoh: Griya Nyaman"
            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500 transition @error('name') border-red-500 @enderror">
        @error('name')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <!-- Address Input -->
    <div>
        <label for="address" class="block text-sm font-semibold text-gray-700 mb-2">
            Alamat <span class="text-red-600">*</span>
        </label>
        <input wire:model="address" type="text" id="address" placeholder="Contoh: Jl. Sudirman No. 123"
            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500 transition @error('address') border-red-500 @enderror">
        @error('address')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <!-- Description Input -->
    <div>
        <label for="description" class="block text-sm font-semibold text-gray-700 mb-2">
            Deskripsi
        </label>
        <textarea wire:model="description" id="description" rows="3" placeholder="Deskripsi property (opsional)"
            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500 transition resize-none @error('description') border-red-500 @enderror"></textarea>
        @error('description')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <!-- Status Select -->
    <div>
        <label for="status" class="block text-sm font-semibold text-gray-700 mb-2">
            Status <span class="text-red-600">*</span>
        </label>
        <select wire:model="status" id="status"
            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500 transition @error('status') border-red-500 @enderror">
            <option value="">-- Pilih Status --</option>
            <option value="active">Aktif</option>
            <option value="inactive">Non-Aktif</option>
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
            class="flex-1 px-4 py-3 bg-orange-600 text-white font-semibold rounded-lg hover:bg-orange-700 transition flex items-center justify-center"
            wire:loading.attr="disabled" wire:loading.class="opacity-50">
            <span wire:loading.remove>
                {{ $propertyId ? 'Perbarui' : 'Simpan' }}
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
