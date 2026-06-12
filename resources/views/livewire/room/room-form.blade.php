<form wire:submit.prevent="save" class="space-y-4">
    <!-- Property Select -->
    <div>
        <label for="propertySelect" class="block text-sm font-semibold text-gray-700 mb-2">
            Property <span class="text-red-600">*</span>
        </label>
        <select wire:model.live="selectedProperty" id="propertySelect"
            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500 transition @error('room_type_id') border-red-500 @enderror">
            <option value="">-- Pilih Property --</option>
            @foreach ($properties as $property)
                <option value="{{ $property->id }}">{{ $property->name }}</option>
            @endforeach
        </select>
    </div>

    <!-- Room Type Select -->
    <div>
        <label for="roomTypeSelect" class="block text-sm font-semibold text-gray-700 mb-2">
            Tipe Ruangan <span class="text-red-600">*</span>
        </label>
        <select wire:model="room_type_id" id="roomTypeSelect"
            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500 transition @error('room_type_id') border-red-500 @enderror"
            @if (!$selectedProperty) disabled @endif>
            <option value="">-- Pilih Tipe Ruangan --</option>
            @if ($selectedProperty)
                @foreach ($roomTypes as $id => $name)
                    <option value="{{ $id }}">{{ $name }}</option>
                @endforeach
            @endif
        </select>
        @error('room_type_id')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <!-- Room Number Input -->
    <div>
        <label for="roomNumber" class="block text-sm font-semibold text-gray-700 mb-2">
            Nomor Ruangan <span class="text-red-600">*</span>
        </label>
        <input wire:model="room_number" type="text" id="roomNumber" placeholder="Contoh: 101, 202A"
            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500 transition @error('room_number') border-red-500 @enderror">
        @error('room_number')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <!-- Floor Input -->
    <div>
        <label for="floor" class="block text-sm font-semibold text-gray-700 mb-2">
            Lantai <span class="text-red-600">*</span>
        </label>
        <input wire:model="floor" type="number" id="floor" min="1"
            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500 transition @error('floor') border-red-500 @enderror">
        @error('floor')
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
                {{ $roomId ? 'Perbarui' : 'Simpan' }}
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
