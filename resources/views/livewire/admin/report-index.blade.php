<div>
    <!-- Header -->
    <div class="mb-8">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1
                    class="text-2xl sm:text-4xl font-bold bg-gradient-to-r from-blue-600 to-indigo-600 bg-clip-text text-transparent">
                    Laporan
                </h1>
                <p class="mt-2 text-gray-600">Ringkasan data properti, pembayaran, dan penyewa</p>
            </div>
        </div>
    </div>

    <!-- Tabs -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 mb-6">
        <div class="flex overflow-x-auto border-b border-gray-200">
            @php
                $tabs = [
                    'payment' => [
                        'label' => 'Rekap Pembayaran',
                        'icon' =>
                            'M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z',
                    ],
                    'occupancy' => [
                        'label' => 'Tingkat Hunian',
                        'icon' =>
                            'M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4',
                    ],
                    'outstanding' => [
                        'label' => 'Tagihan Belum Lunas',
                        'icon' => 'M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z',
                    ],
                    'tenant' => [
                        'label' => 'Riwayat Penyewa',
                        'icon' =>
                            'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z',
                    ],
                ];
            @endphp
            @foreach ($tabs as $key => $t)
                <button wire:click="setTab('{{ $key }}')"
                    class="flex items-center gap-2 px-5 py-3 text-sm font-medium whitespace-nowrap border-b-2 transition-colors {{ $tab === $key ? 'border-blue-600 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $t['icon'] }}">
                        </path>
                    </svg>
                    {{ $t['label'] }}
                </button>
            @endforeach
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 mb-6">
        <div class="flex flex-wrap gap-4 items-end">
            @if ($tab === 'payment')
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Bulan</label>
                    <input type="month" wire:model.live="monthYear"
                        class="rounded-lg border-gray-300 text-sm focus:ring-blue-500 focus:border-blue-500">
                </div>
            @endif

            @if (in_array($tab, ['payment', 'outstanding']))
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Properti</label>
                    <select wire:model.live="propertyFilter"
                        class="rounded-lg border-gray-300 text-sm focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Semua Properti</option>
                        @foreach ($properties as $property)
                            <option value="{{ $property->id }}">{{ $property->name }}</option>
                        @endforeach
                    </select>
                </div>
            @endif

            {{-- Export PDF Button --}}
            @if ($tab === 'payment')
                <div class="ml-auto">
                    <button wire:click="exportPaymentPdf"
                        class="inline-flex items-center gap-2 px-4 py-2 bg-red-600 hover:bg-red-700 text-white text-sm font-medium rounded-lg transition shadow-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                            </path>
                        </svg>
                        Cetak PDF
                    </button>
                </div>
            @endif

            @if ($tab === 'outstanding')
                <div class="ml-auto">
                    <button wire:click="exportOutstandingPdf"
                        class="inline-flex items-center gap-2 px-4 py-2 bg-red-600 hover:bg-red-700 text-white text-sm font-medium rounded-lg transition shadow-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                            </path>
                        </svg>
                        Cetak PDF
                    </button>
                </div>
            @endif
        </div>
    </div>

    <!-- Content -->
    <div wire:loading.class="opacity-50 pointer-events-none" class="transition-opacity">

        {{-- Tab 1: Rekap Pembayaran --}}
        @if ($tab === 'payment' && isset($paymentRecap))
            {{-- Summary Cards --}}
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4">
                    <p class="text-xs text-gray-500 font-medium">Total Invoice</p>
                    <p class="text-2xl font-bold text-gray-800 mt-1">
                        {{ $paymentRecap['paid_count'] + $paymentRecap['pending_count'] + $paymentRecap['unpaid_count'] }}
                    </p>
                    <p class="text-xs text-gray-400 mt-1">Rp
                        {{ number_format($paymentRecap['total_amount'], 0, ',', '.') }}</p>
                </div>
                <div class="bg-white rounded-xl shadow-sm border border-green-200 p-4">
                    <p class="text-xs text-green-600 font-medium">Lunas</p>
                    <p class="text-2xl font-bold text-green-600 mt-1">{{ $paymentRecap['paid_count'] }}</p>
                    <p class="text-xs text-green-500 mt-1">Rp
                        {{ number_format($paymentRecap['paid_amount'], 0, ',', '.') }}</p>
                </div>
                <div class="bg-white rounded-xl shadow-sm border border-yellow-200 p-4">
                    <p class="text-xs text-yellow-600 font-medium">Menunggu Verifikasi</p>
                    <p class="text-2xl font-bold text-yellow-600 mt-1">{{ $paymentRecap['pending_count'] }}</p>
                    <p class="text-xs text-yellow-500 mt-1">Rp
                        {{ number_format($paymentRecap['pending_amount'], 0, ',', '.') }}</p>
                </div>
                <div class="bg-white rounded-xl shadow-sm border border-red-200 p-4">
                    <p class="text-xs text-red-600 font-medium">Belum Bayar</p>
                    <p class="text-2xl font-bold text-red-600 mt-1">{{ $paymentRecap['unpaid_count'] }}</p>
                    <p class="text-xs text-red-500 mt-1">Rp
                        {{ number_format($paymentRecap['unpaid_amount'], 0, ',', '.') }}</p>
                </div>
            </div>

            {{-- Collection Rate --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 mb-6">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-sm font-medium text-gray-700">Tingkat Koleksi Pembayaran</span>
                    <span
                        class="text-sm font-bold {{ $paymentRecap['collection_rate'] >= 80 ? 'text-green-600' : ($paymentRecap['collection_rate'] >= 50 ? 'text-yellow-600' : 'text-red-600') }}">
                        {{ $paymentRecap['collection_rate'] }}%
                    </span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-3">
                    <div class="h-3 rounded-full transition-all duration-500 {{ $paymentRecap['collection_rate'] >= 80 ? 'bg-green-500' : ($paymentRecap['collection_rate'] >= 50 ? 'bg-yellow-500' : 'bg-red-500') }}"
                        style="width: {{ $paymentRecap['collection_rate'] }}%"></div>
                </div>
            </div>

            {{-- Invoice Table --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-4 py-3 border-b border-gray-100">
                    <h3 class="text-sm font-semibold text-gray-700">Detail Invoice -
                        {{ \Carbon\Carbon::createFromFormat('Y-m', $monthYear)->translatedFormat('F Y') }}</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-gray-50 text-gray-600">
                            <tr>
                                <th class="px-4 py-3 text-left font-medium">No. Invoice</th>
                                <th class="px-4 py-3 text-left font-medium">Penyewa</th>
                                <th class="px-4 py-3 text-left font-medium">Kamar</th>
                                <th class="px-4 py-3 text-right font-medium">Jumlah</th>
                                <th class="px-4 py-3 text-left font-medium">Jatuh Tempo</th>
                                <th class="px-4 py-3 text-center font-medium">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse ($paymentRecap['invoices'] as $invoice)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 py-3 font-mono text-xs">{{ $invoice->reference_number }}</td>
                                    <td class="px-4 py-3">{{ $invoice->lease?->tenant?->display_name ?? '-' }}</td>
                                    <td class="px-4 py-3">{{ $invoice->lease?->room?->room_number ?? '-' }}</td>
                                    <td class="px-4 py-3 text-right font-medium">Rp
                                        {{ number_format($invoice->amount, 0, ',', '.') }}</td>
                                    <td class="px-4 py-3">{{ $invoice->due_date?->translatedFormat('d M Y') ?? '-' }}
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        @php
                                            $statusColor = match ($invoice->status) {
                                                'paid' => 'bg-green-100 text-green-700',
                                                'pending' => 'bg-yellow-100 text-yellow-700',
                                                default => 'bg-red-100 text-red-700',
                                            };
                                            $statusLabel = match ($invoice->status) {
                                                'paid' => 'Lunas',
                                                'pending' => 'Pending',
                                                default => 'Belum Bayar',
                                            };
                                        @endphp
                                        <span
                                            class="px-2 py-1 rounded-full text-xs font-medium {{ $statusColor }}">{{ $statusLabel }}</span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-4 py-8 text-center text-gray-400">Tidak ada data
                                        invoice untuk bulan ini</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        @endif

        {{-- Tab 2: Tingkat Hunian --}}
        @if ($tab === 'occupancy' && isset($occupancyReport))
            {{-- Overall Summary --}}
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4">
                    <p class="text-xs text-gray-500 font-medium">Total Kamar</p>
                    <p class="text-2xl font-bold text-gray-800 mt-1">{{ $occupancyReport['total_rooms'] }}</p>
                </div>
                <div class="bg-white rounded-xl shadow-sm border border-blue-200 p-4">
                    <p class="text-xs text-blue-600 font-medium">Terisi</p>
                    <p class="text-2xl font-bold text-blue-600 mt-1">{{ $occupancyReport['total_occupied'] }}</p>
                </div>
                <div class="bg-white rounded-xl shadow-sm border border-green-200 p-4">
                    <p class="text-xs text-green-600 font-medium">Tersedia</p>
                    <p class="text-2xl font-bold text-green-600 mt-1">{{ $occupancyReport['total_available'] }}</p>
                </div>
                <div class="bg-white rounded-xl shadow-sm border border-orange-200 p-4">
                    <p class="text-xs text-orange-600 font-medium">Maintenance</p>
                    <p class="text-2xl font-bold text-orange-600 mt-1">{{ $occupancyReport['total_maintenance'] }}</p>
                </div>
            </div>

            {{-- Overall Rate --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 mb-6">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-sm font-medium text-gray-700">Tingkat Hunian Keseluruhan</span>
                    <span class="text-sm font-bold text-blue-600">{{ $occupancyReport['overall_rate'] }}%</span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-3">
                    <div class="bg-blue-500 h-3 rounded-full transition-all duration-500"
                        style="width: {{ $occupancyReport['overall_rate'] }}%"></div>
                </div>
            </div>

            {{-- Per Property --}}
            <div class="space-y-4">
                @foreach ($occupancyReport['properties'] as $item)
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
                        <div class="flex items-center justify-between mb-3">
                            <div>
                                <h3 class="text-sm font-semibold text-gray-800">{{ $item['property']->name }}</h3>
                                <p class="text-xs text-gray-400">{{ $item['property']->address }}</p>
                            </div>
                            <span
                                class="text-lg font-bold {{ $item['occupancy_rate'] >= 80 ? 'text-green-600' : ($item['occupancy_rate'] >= 50 ? 'text-yellow-600' : 'text-red-600') }}">
                                {{ $item['occupancy_rate'] }}%
                            </span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2 mb-3">
                            <div class="h-2 rounded-full transition-all {{ $item['occupancy_rate'] >= 80 ? 'bg-green-500' : ($item['occupancy_rate'] >= 50 ? 'bg-yellow-500' : 'bg-red-500') }}"
                                style="width: {{ $item['occupancy_rate'] }}%"></div>
                        </div>
                        <div class="flex gap-4 text-xs text-gray-500">
                            <span>Total: <strong class="text-gray-700">{{ $item['total_rooms'] }}</strong></span>
                            <span>Terisi: <strong class="text-blue-600">{{ $item['occupied'] }}</strong></span>
                            <span>Tersedia: <strong class="text-green-600">{{ $item['available'] }}</strong></span>
                            <span>Maintenance: <strong
                                    class="text-orange-600">{{ $item['maintenance'] }}</strong></span>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif

        {{-- Tab 3: Tagihan Belum Lunas --}}
        @if ($tab === 'outstanding' && isset($outstandingInvoices))
            {{-- Summary --}}
            <div class="grid grid-cols-2 lg:grid-cols-3 gap-4 mb-6">
                <div class="bg-white rounded-xl shadow-sm border border-red-200 p-4">
                    <p class="text-xs text-red-600 font-medium">Total Tagihan Belum Lunas</p>
                    <p class="text-2xl font-bold text-red-600 mt-1">{{ $outstandingInvoices->count() }}</p>
                </div>
                <div class="bg-white rounded-xl shadow-sm border border-red-200 p-4">
                    <p class="text-xs text-red-600 font-medium">Total Nilai</p>
                    <p class="text-2xl font-bold text-red-600 mt-1">Rp
                        {{ number_format($outstandingInvoices->sum('amount'), 0, ',', '.') }}</p>
                </div>
                <div class="bg-white rounded-xl shadow-sm border border-orange-200 p-4">
                    <p class="text-xs text-orange-600 font-medium">Sudah Lewat Jatuh Tempo</p>
                    <p class="text-2xl font-bold text-orange-600 mt-1">
                        {{ $outstandingInvoices->filter(fn($i) => $i->due_date && $i->due_date->isPast())->count() }}
                    </p>
                </div>
            </div>

            {{-- Table --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-gray-50 text-gray-600">
                            <tr>
                                <th class="px-4 py-3 text-left font-medium">No. Invoice</th>
                                <th class="px-4 py-3 text-left font-medium">Penyewa</th>
                                <th class="px-4 py-3 text-left font-medium">Kamar</th>
                                <th class="px-4 py-3 text-left font-medium">Properti</th>
                                <th class="px-4 py-3 text-left font-medium">Bulan</th>
                                <th class="px-4 py-3 text-right font-medium">Jumlah</th>
                                <th class="px-4 py-3 text-left font-medium">Jatuh Tempo</th>
                                <th class="px-4 py-3 text-center font-medium">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse ($outstandingInvoices as $invoice)
                                @php
                                    $isOverdue = $invoice->due_date && $invoice->due_date->isPast();
                                @endphp
                                <tr class="hover:bg-gray-50 {{ $isOverdue ? 'bg-red-50/50' : '' }}">
                                    <td class="px-4 py-3 font-mono text-xs">{{ $invoice->reference_number }}</td>
                                    <td class="px-4 py-3">{{ $invoice->lease?->tenant?->display_name ?? '-' }}</td>
                                    <td class="px-4 py-3">{{ $invoice->lease?->room?->room_number ?? '-' }}</td>
                                    <td class="px-4 py-3 text-xs">
                                        {{ $invoice->lease?->room?->roomType?->property?->name ?? '-' }}</td>
                                    <td class="px-4 py-3">{{ $invoice->month_year }}</td>
                                    <td class="px-4 py-3 text-right font-medium">Rp
                                        {{ number_format($invoice->amount, 0, ',', '.') }}</td>
                                    <td class="px-4 py-3">
                                        <span class="{{ $isOverdue ? 'text-red-600 font-semibold' : '' }}">
                                            {{ $invoice->due_date?->translatedFormat('d M Y') ?? '-' }}
                                        </span>
                                        @if ($isOverdue)
                                            <span
                                                class="block text-xs text-red-500">{{ $invoice->due_date->diffForHumans() }}</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        <span
                                            class="px-2 py-1 rounded-full text-xs font-medium {{ $invoice->status === 'pending' ? 'bg-yellow-100 text-yellow-700' : 'bg-red-100 text-red-700' }}">
                                            {{ $invoice->status === 'pending' ? 'Pending' : 'Belum Bayar' }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="px-4 py-8 text-center text-gray-400">Tidak ada tagihan
                                        yang belum lunas 🎉</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        @endif

        {{-- Tab 4: Riwayat Penyewa --}}
        @if ($tab === 'tenant' && isset($tenantReport))
            {{-- Summary --}}
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4">
                    <p class="text-xs text-gray-500 font-medium">Total Penyewa</p>
                    <p class="text-2xl font-bold text-gray-800 mt-1">{{ $tenantReport['total'] }}</p>
                </div>
                <div class="bg-white rounded-xl shadow-sm border border-green-200 p-4">
                    <p class="text-xs text-green-600 font-medium">Aktif</p>
                    <p class="text-2xl font-bold text-green-600 mt-1">{{ $tenantReport['active'] }}</p>
                </div>
                <div class="bg-white rounded-xl shadow-sm border border-gray-300 p-4">
                    <p class="text-xs text-gray-500 font-medium">Tidak Aktif</p>
                    <p class="text-2xl font-bold text-gray-500 mt-1">{{ $tenantReport['inactive'] }}</p>
                </div>
                <div class="bg-white rounded-xl shadow-sm border border-red-200 p-4">
                    <p class="text-xs text-red-600 font-medium">Dikeluarkan</p>
                    <p class="text-2xl font-bold text-red-600 mt-1">{{ $tenantReport['evicted'] }}</p>
                </div>
            </div>

            {{-- Tenant Table --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-gray-50 text-gray-600">
                            <tr>
                                <th class="px-4 py-3 text-left font-medium">Penyewa</th>
                                <th class="px-4 py-3 text-center font-medium">Status</th>
                                <th class="px-4 py-3 text-left font-medium">Kamar Aktif</th>
                                <th class="px-4 py-3 text-center font-medium">Jumlah Kontrak</th>
                                <th class="px-4 py-3 text-right font-medium">Total Bayar</th>
                                <th class="px-4 py-3 text-right font-medium">Tunggakan</th>
                                <th class="px-4 py-3 text-center font-medium">Tingkat Bayar</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse ($tenantReport['tenants'] as $item)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 py-3">
                                        <div class="font-medium text-gray-800">{{ $item['tenant']->display_name }}
                                        </div>
                                        <div class="text-xs text-gray-400">{{ $item['tenant']->phone ?? '-' }}</div>
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        @php
                                            $sColor = match ($item['tenant']->status) {
                                                'active' => 'bg-green-100 text-green-700',
                                                'inactive' => 'bg-gray-100 text-gray-600',
                                                default => 'bg-red-100 text-red-700',
                                            };
                                            $sLabel = match ($item['tenant']->status) {
                                                'active' => 'Aktif',
                                                'inactive' => 'Tidak Aktif',
                                                default => 'Dikeluarkan',
                                            };
                                        @endphp
                                        <span
                                            class="px-2 py-1 rounded-full text-xs font-medium {{ $sColor }}">{{ $sLabel }}</span>
                                    </td>
                                    <td class="px-4 py-3">{{ $item['active_lease']?->room?->room_number ?? '-' }}</td>
                                    <td class="px-4 py-3 text-center">{{ $item['lease_count'] }}</td>
                                    <td class="px-4 py-3 text-right text-green-600 font-medium">Rp
                                        {{ number_format($item['total_paid'], 0, ',', '.') }}</td>
                                    <td
                                        class="px-4 py-3 text-right {{ $item['total_outstanding'] > 0 ? 'text-red-600 font-semibold' : 'text-gray-400' }}">
                                        Rp {{ number_format($item['total_outstanding'], 0, ',', '.') }}
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        <span
                                            class="text-xs font-bold {{ $item['payment_rate'] >= 80 ? 'text-green-600' : ($item['payment_rate'] >= 50 ? 'text-yellow-600' : 'text-red-600') }}">
                                            {{ $item['payment_rate'] }}%
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-4 py-8 text-center text-gray-400">Belum ada data
                                        penyewa</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        @endif

    </div>
</div>
