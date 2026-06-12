<form wire:submit.prevent="save" class="space-y-4">
    <!-- Tenant Select -->
    <div>
        <label for="tenantSelect" class="block text-sm font-semibold text-gray-700 mb-2">
            Penyewa <span class="text-red-600">*</span>
        </label>
        <select wire:model.live="tenant_id" id="tenantSelect"
            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500 transition @error('tenant_id') border-red-500 @enderror">
            <option value="">-- Pilih Penyewa --</option>
            @foreach ($tenants as $tenant)
                <option value="{{ $tenant->id }}">{{ $tenant->display_name }} ({{ $tenant->nik }})</option>
            @endforeach
        </select>
        @error('tenant_id')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <!-- Property Select -->
    <div>
        <label for="propertySelect" class="block text-sm font-semibold text-gray-700 mb-2">
            Properti <span class="text-red-600">*</span>
        </label>
        <select wire:model.live="property_id" id="propertySelect"
            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500 transition">
            <option value="">-- Pilih Properti --</option>
            @foreach ($properties as $property)
                <option value="{{ $property->id }}">{{ $property->name }}</option>
            @endforeach
        </select>
    </div>

    <!-- Room Select -->
    <div>
        <label for="roomSelect" class="block text-sm font-semibold text-gray-700 mb-2">
            Ruangan <span class="text-red-600">*</span>
        </label>
        @if (!$property_id)
            <div class="w-full px-4 py-3 bg-gray-100 border border-gray-300 rounded-lg text-gray-500">
                Pilih properti terlebih dahulu
            </div>
        @else
            <select wire:model.live="room_id" id="roomSelect"
                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500 transition @error('room_id') border-red-500 @enderror">
                <option value="">-- Pilih Ruangan --</option>
                @forelse ($rooms as $room)
                    <option value="{{ $room->id }}">{{ $room->room_number }} - {{ $room->roomType->name ?? 'N/A' }}
                        (Rp
                        {{ number_format($room->roomType->price ?? 0, 0, ',', '.') }}/bln)
                    </option>
                @empty
                    <option disabled>Tidak ada ruangan tersedia</option>
                @endforelse
            </select>
        @endif
        @error('room_id')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <!-- Start Date -->
    <div>
        <label for="start_date" class="block text-sm font-semibold text-gray-700 mb-2">
            Tanggal Mulai <span class="text-red-600">*</span>
        </label>
        <input wire:model="start_date" type="date" id="start_date"
            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500 transition @error('start_date') border-red-500 @enderror">
        @error('start_date')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <!-- End Date -->
    <div>
        <label for="end_date" class="block text-sm font-semibold text-gray-700 mb-2">
            Tanggal Selesai <span class="text-red-600">*</span>
        </label>
        <input wire:model="end_date" type="date" id="end_date"
            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500 transition @error('end_date') border-red-500 @enderror">
        @error('end_date')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <!-- Due Date Per Month -->
    <div>
        <label for="due_date_per_month" class="block text-sm font-semibold text-gray-700 mb-2">
            Tanggal Jatuh Tempo (Setiap Bulan) <span class="text-red-600">*</span>
        </label>
        <input wire:model="due_date_per_month" type="number" id="due_date_per_month" min="1" max="31"
            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500 transition @error('due_date_per_month') border-red-500 @enderror">
        @error('due_date_per_month')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <!-- Deposit Amount -->
    <div>
        <label for="deposit_amount" class="block text-sm font-semibold text-gray-700 mb-2">
            Jumlah Deposit <span class="text-red-600">*</span>
        </label>
        <input wire:model="deposit_amount" type="number" id="deposit_amount" step="0.01" min="0"
            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500 transition @error('deposit_amount') border-red-500 @enderror"
            placeholder="Contoh: 1000000">
        @error('deposit_amount')
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
            <option value="pending">Tertunda</option>
            <option value="active">Aktif</option>
            <option value="completed">Selesai</option>
            <option value="terminated">Dibatalkan</option>
            <option value="cancelled">Dibatalkan</option>
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
                {{ $leaseId ? 'Perbarui' : 'Simpan' }}
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
