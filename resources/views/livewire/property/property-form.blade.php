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

    <!-- Gender Type -->
    <div>
        <label for="gender_type" class="block text-sm font-semibold text-gray-700 mb-2">Tipe Penghuni</label>
        <select wire:model="gender_type" id="gender_type"
            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500 transition @error('gender_type') border-red-500 @enderror">
            <option value="">-- Tidak Ditentukan --</option>
            <option value="putra">Putra</option>
            <option value="putri">Putri</option>
            <option value="putra_putri">Putra & Putri</option>
        </select>
        @error('gender_type')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <!-- Fasilitas Umum -->
    <div>
        <label for="common_facilities_text" class="block text-sm font-semibold text-gray-700 mb-2">Fasilitas Umum</label>
        <input wire:model="common_facilities_text" type="text" id="common_facilities_text"
            placeholder="Contoh: Dapur, Kulkas, Mesin Cuci, Tempat Jemur"
            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500 transition @error('common_facilities_text') border-red-500 @enderror">
        <p class="mt-1 text-xs text-gray-500">Pisahkan dengan koma. Contoh: Dapur Gratis Gas, Kulkas, Mesin Cuci</p>
        @error('common_facilities_text')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <!-- Landing / Rekomendasi Kost -->
    <div class="border-t pt-4">
        <label class="flex items-center gap-3 cursor-pointer">
            <input type="checkbox" wire:model.live="is_featured"
                class="w-5 h-5 rounded border-gray-300 text-orange-600 focus:ring-orange-500">
            <span class="text-sm font-semibold text-gray-800">Tampilkan di Halaman Depan (Rekomendasi Kost)</span>
        </label>
        <p class="text-xs text-gray-500 mt-1">Harga & fasilitas otomatis diambil dari Tipe Kamar properti ini.</p>

        @if ($is_featured)
            <div class="mt-4 space-y-4 bg-orange-50 border border-orange-100 rounded-xl p-4">
                <!-- Featured Image -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Foto Kost</label>
                    @if ($featured_image)
                        <img src="{{ $featured_image->temporaryUrl() }}" class="w-full h-40 object-cover rounded-lg mb-2">
                    @elseif ($existing_featured_image)
                        <img src="/images/{{ $existing_featured_image }}" class="w-full h-40 object-cover rounded-lg mb-2">
                    @endif
                    <input type="file" wire:model="featured_image" accept="image/*"
                        class="w-full text-sm text-gray-600 file:mr-3 file:py-2 file:px-4 file:rounded-lg file:border-0 file:bg-orange-600 file:text-white file:font-semibold hover:file:bg-orange-700">
                    <div wire:loading wire:target="featured_image" class="text-xs text-orange-600 mt-1">Mengunggah...</div>
                    @error('featured_image')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Location -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Lokasi (label singkat)</label>
                    <input wire:model="location_label" type="text" placeholder="Contoh: Jakarta Selatan"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500 transition @error('location_label') border-red-500 @enderror">
                    @error('location_label')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Badge -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Badge (opsional)</label>
                    <input wire:model="badge_text" type="text" placeholder="Contoh: Sisa 2 Kamar"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500 transition @error('badge_text') border-red-500 @enderror">
                    @error('badge_text')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Galeri Foto (multi-foto untuk halaman detail) -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Galeri Foto (untuk halaman detail)</label>
                    @if (count($existing_gallery) || count($gallery_uploads))
                        <div class="grid grid-cols-3 gap-2 mb-3">
                            @foreach ($existing_gallery as $i => $img)
                                <div class="relative">
                                    <img src="/images/{{ $img }}" class="w-full h-20 object-cover rounded-lg">
                                    <button type="button" wire:click="removeExistingImage({{ $i }})"
                                        class="absolute -top-1.5 -right-1.5 bg-red-600 text-white w-5 h-5 rounded-full text-xs leading-none flex items-center justify-center shadow">&times;</button>
                                </div>
                            @endforeach
                            @foreach ($gallery_uploads as $i => $up)
                                <div class="relative">
                                    <img src="{{ $up->temporaryUrl() }}" class="w-full h-20 object-cover rounded-lg ring-2 ring-orange-400">
                                    <button type="button" wire:click="removeUpload({{ $i }})"
                                        class="absolute -top-1.5 -right-1.5 bg-red-600 text-white w-5 h-5 rounded-full text-xs leading-none flex items-center justify-center shadow">&times;</button>
                                </div>
                            @endforeach
                        </div>
                    @endif
                    <input type="file" wire:model="gallery_uploads" accept="image/*" multiple
                        class="w-full text-sm text-gray-600 file:mr-3 file:py-2 file:px-4 file:rounded-lg file:border-0 file:bg-orange-600 file:text-white file:font-semibold hover:file:bg-orange-700">
                    <div wire:loading wire:target="gallery_uploads" class="text-xs text-orange-600 mt-1">Mengunggah...</div>
                    @error('gallery_uploads.*')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        @endif
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
