@php
    $roleLabels = ['super-admin' => 'Super Admin', 'owner' => 'Owner', 'manager' => 'Manajer', 'tenant' => 'Penyewa'];
@endphp
<form wire:submit.prevent="save" class="space-y-4">
    <!-- Name -->
    <div>
        <label for="name" class="block text-sm font-semibold text-gray-700 mb-2">
            Nama <span class="text-red-600">*</span>
        </label>
        <input wire:model="name" type="text" id="name" placeholder="Nama lengkap"
            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500 transition @error('name') border-red-500 @enderror">
        @error('name')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <!-- Email -->
    <div>
        <label for="email" class="block text-sm font-semibold text-gray-700 mb-2">
            Email <span class="text-red-600">*</span>
        </label>
        <input wire:model="email" type="email" id="email" placeholder="email@contoh.com"
            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500 transition @error('email') border-red-500 @enderror">
        @error('email')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <!-- Password -->
    <div>
        <label for="password" class="block text-sm font-semibold text-gray-700 mb-2">
            Password @if (!$userId)<span class="text-red-600">*</span>@endif
        </label>
        <input wire:model="password" type="password" id="password"
            placeholder="{{ $userId ? 'Kosongkan jika tidak diubah' : 'Minimal 6 karakter' }}"
            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500 transition @error('password') border-red-500 @enderror">
        @error('password')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <!-- Role -->
    <div>
        <label for="role" class="block text-sm font-semibold text-gray-700 mb-2">
            Role <span class="text-red-600">*</span>
        </label>
        <select wire:model="role" id="role"
            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500 transition @error('role') border-red-500 @enderror">
            @foreach ($this->roleChoices() as $r)
                <option value="{{ $r }}">{{ $roleLabels[$r] ?? ucfirst($r) }}</option>
            @endforeach
        </select>
        @error('role')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <!-- Actions -->
    <div class="flex gap-3 pt-4">
        <button type="button" wire:click="$parent.closeModal()"
            class="flex-1 px-4 py-3 bg-gray-200 text-gray-800 font-semibold rounded-lg hover:bg-gray-300 transition">
            Batal
        </button>
        <button type="submit"
            class="flex-1 px-4 py-3 bg-orange-600 text-white font-semibold rounded-lg hover:bg-orange-700 transition flex items-center justify-center"
            wire:loading.attr="disabled" wire:loading.class="opacity-50">
            <span wire:loading.remove>{{ $userId ? 'Perbarui' : 'Simpan' }}</span>
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
