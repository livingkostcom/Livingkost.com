<form wire:submit.prevent="save" class="space-y-4">
    <!-- Property Select -->
    <div>
        <label for="propertySelect" class="block text-sm font-semibold text-gray-700 mb-2">
            Property <span class="text-red-600">*</span>
        </label>
        <select wire:model="property_id" id="propertySelect"
            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500 transition @error('property_id') border-red-500 @enderror">
            <option value="">-- Pilih Property --</option>
            @foreach ($properties as $property)
                <option value="{{ $property->id }}">{{ $property->name }}</option>
            @endforeach
        </select>
        @error('property_id')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <!-- Name Input -->
    <div>
        <label for="name" class="block text-sm font-semibold text-gray-700 mb-2">
            Nama Tipe Ruangan <span class="text-red-600">*</span>
        </label>
        <input wire:model="name" type="text" id="name" placeholder="Contoh: Studio, 1 Kamar Tidur"
            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500 transition @error('name') border-red-500 @enderror">
        @error('name')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <!-- Price Input -->
    <div>
        <label for="price" class="block text-sm font-semibold text-gray-700 mb-2">
            Harga/Bulan (Rp) <span class="text-red-600">*</span>
        </label>
        <div class="relative">
            <span class="absolute left-4 top-3.5 text-gray-500 font-medium">Rp</span>
            <input wire:model="price" type="number" id="price" placeholder="1000000" step="0.01"
                class="w-full pl-12 pr-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500 transition @error('price') border-red-500 @enderror">
        </div>
        @error('price')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <!-- Facilities Input -->
    <div>
        <label for="facilities" class="block text-sm font-semibold text-gray-700 mb-2">
            Fasilitas <span class="text-gray-500 text-xs">(Pisahkan dengan koma)</span>
        </label>
        <textarea wire:model="facilities" id="facilities" rows="3" placeholder="Contoh: AC, TV, WiFi, Kasur, Lemari"
            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500 transition @error('facilities') border-red-500 @enderror"></textarea>
        @error('facilities')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
        <p class="mt-1 text-xs text-gray-500">Masukkan fasilitas yang tersedia dipisahkan dengan koma</p>
    </div>

    <!-- Foto Kamar (multi-upload) -->
    <div>
        <label class="block text-sm font-semibold text-gray-700 mb-2">Foto Kamar</label>

        @if (count($existing_images) || count($image_uploads))
            <div class="grid grid-cols-3 gap-2 mb-3">
                @foreach ($existing_images as $i => $img)
                    <div class="relative">
                        <img src="/images/{{ $img }}" class="w-full h-24 object-cover rounded-lg">
                        <button type="button" wire:click="removeExistingImage({{ $i }})"
                            class="absolute -top-1.5 -right-1.5 bg-red-600 text-white w-5 h-5 rounded-full text-xs leading-none flex items-center justify-center shadow">&times;</button>
                    </div>
                @endforeach
                @foreach ($image_uploads as $i => $up)
                    <div wire:key="rt-upload-{{ $up->getFilename() }}" class="relative">
                        <img src="{{ $up->temporaryUrl() }}" class="w-full h-24 object-cover rounded-lg ring-2 ring-orange-400">
                        <button type="button" wire:click="removeNewUpload({{ $i }})"
                            class="absolute -top-1.5 -right-1.5 bg-red-600 text-white w-5 h-5 rounded-full text-xs leading-none flex items-center justify-center shadow">&times;</button>
                    </div>
                @endforeach
            </div>
        @endif

        <input type="file" wire:model="image_uploads" accept="image/*" multiple
            class="w-full text-sm text-gray-600 file:mr-3 file:py-2 file:px-4 file:rounded-lg file:border-0 file:bg-orange-600 file:text-white file:font-semibold hover:file:bg-orange-700">
        <div wire:loading wire:target="image_uploads" class="text-xs text-orange-600 mt-1">Mengunggah...</div>
        @error('image_uploads.*')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
        <p class="mt-1 text-xs text-gray-500">Foto akan ditampilkan di halaman detail properti.</p>
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
                {{ $roomTypeId ? 'Perbarui' : 'Simpan' }}
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
