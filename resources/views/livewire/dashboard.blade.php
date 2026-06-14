<div>
    <!-- Header -->
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div>
                <h1
                    class="text-2xl sm:text-4xl font-bold bg-gradient-to-r from-orange-600 to-orange-600 bg-clip-text text-transparent">
                    Selamat Datang, {{ Auth::user()->name }}!</h1>
                <p class="mt-2 text-gray-600">Ringkasan manajemen KOS Anda hari ini</p>
            </div>
            <div class="text-right hidden sm:block">
                <p class="text-sm text-gray-500">{{ now()->translatedFormat('l, d F Y') }}</p>
            </div>
        </div>
    </div>

    @if ($isSuperAdmin)
        {{-- ======================== SUPER ADMIN DASHBOARD ======================== --}}
        @php $p = $superadmin['platform']; @endphp

        <!-- Platform Metric Cards -->
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
            <!-- Owner -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 hover:shadow-md transition">
                <div class="flex items-center justify-between mb-3">
                    <div class="p-2.5 bg-orange-100 rounded-xl">
                        <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                    </div>
                    <span class="text-xs font-semibold px-2 py-1 rounded-full bg-blue-100 text-blue-700">{{ $p['total_managers'] }} manajer</span>
                </div>
                <p class="text-3xl font-bold text-gray-900">{{ $p['total_owners'] }}</p>
                <p class="text-sm text-gray-500 mt-1">Total Owner</p>
            </div>

            <!-- Penyewa Aktif -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 hover:shadow-md transition">
                <div class="flex items-center justify-between mb-3">
                    <div class="p-2.5 bg-green-100 rounded-xl">
                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a4 4 0 00-3-3.87M9 20H4v-2a4 4 0 013-3.87m6-1.13a4 4 0 10-4-4 4 4 0 004 4zm6-4a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                    </div>
                </div>
                <p class="text-3xl font-bold text-gray-900">{{ $p['total_tenants'] }}</p>
                <p class="text-sm text-gray-500 mt-1">Penyewa Aktif</p>
            </div>

            <!-- Properti -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 hover:shadow-md transition">
                <div class="flex items-center justify-between mb-3">
                    <div class="p-2.5 bg-purple-100 rounded-xl">
                        <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0H5m14 0h2M5 21H3m4-14h.01M11 7h.01M7 11h.01M11 11h.01M7 15h.01M11 15h.01"/></svg>
                    </div>
                </div>
                <p class="text-3xl font-bold text-gray-900">{{ $p['total_properties'] }}</p>
                <p class="text-sm text-gray-500 mt-1">Total Properti</p>
            </div>

            <!-- Total Kamar -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 hover:shadow-md transition">
                <div class="flex items-center justify-between mb-3">
                    <div class="p-2.5 bg-orange-100 rounded-xl">
                        <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-4 7 4M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                    </div>
                    <span class="text-xs font-semibold px-2 py-1 rounded-full bg-green-100 text-green-700">{{ $p['occupied_rooms'] }} terisi</span>
                </div>
                <p class="text-3xl font-bold text-gray-900">{{ $p['total_rooms'] }}</p>
                <p class="text-sm text-gray-500 mt-1">Total Kamar</p>
            </div>
        </div>

        <!-- Second row: occupancy, income, attention -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 mb-8">
            <!-- Tingkat Hunian -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
                <p class="text-sm text-gray-500 mb-1">Tingkat Hunian Platform</p>
                <p class="text-3xl font-bold text-gray-900">{{ $p['occupancy_rate'] }}%</p>
                <div class="mt-3 w-full bg-gray-200 rounded-full h-2">
                    <div class="bg-green-500 h-2 rounded-full transition-all duration-500" style="width: {{ $p['occupancy_rate'] }}%"></div>
                </div>
                <p class="text-xs text-gray-400 mt-2">{{ $p['occupied_rooms'] }} dari {{ $p['total_rooms'] }} kamar terisi</p>
            </div>

            <!-- Pendapatan Platform Bulan Ini -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
                <p class="text-sm text-gray-500 mb-1">Pendapatan Platform (Bulan Ini)</p>
                <p class="text-2xl font-bold text-emerald-600">Rp {{ number_format($p['income_this_month'], 0, ',', '.') }}</p>
                <p class="text-xs text-gray-400 mt-2">Total pembayaran terverifikasi seluruh owner</p>
            </div>

            <!-- Perlu Perhatian -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
                <p class="text-sm text-gray-500 mb-3">Perlu Perhatian</p>
                <div class="space-y-2">
                    <div class="flex items-center justify-between text-sm">
                        <span class="text-gray-600">Menunggu verifikasi</span>
                        <span class="font-bold px-2 py-0.5 rounded-full {{ $p['pending_payments'] > 0 ? 'bg-yellow-100 text-yellow-700' : 'bg-gray-100 text-gray-500' }}">{{ $p['pending_payments'] }}</span>
                    </div>
                    <div class="flex items-center justify-between text-sm">
                        <span class="text-gray-600">Invoice jatuh tempo</span>
                        <span class="font-bold px-2 py-0.5 rounded-full {{ $p['overdue_invoices'] > 0 ? 'bg-red-100 text-red-700' : 'bg-gray-100 text-gray-500' }}">{{ $p['overdue_invoices'] }}</span>
                    </div>
                    <div class="flex items-center justify-between text-sm">
                        <span class="text-gray-600">Perbaikan pending</span>
                        <span class="font-bold px-2 py-0.5 rounded-full {{ $p['pending_maintenance'] > 0 ? 'bg-orange-100 text-orange-700' : 'bg-gray-100 text-gray-500' }}">{{ $p['pending_maintenance'] }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Pendapatan Bersih Platform (super-admin's own earnings) -->
        <div class="bg-gradient-to-br from-emerald-50 to-white rounded-2xl shadow-sm border border-emerald-200 p-6 mb-8">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-5">
                <div>
                    <p class="text-sm text-gray-500">Pendapatan Bersih Platform (Bulan Ini)</p>
                    <p class="text-3xl sm:text-4xl font-bold {{ $p['net_income'] >= 0 ? 'text-emerald-600' : 'text-red-600' }}">
                        Rp {{ number_format($p['net_income'], 0, ',', '.') }}
                    </p>
                    <p class="text-xs text-gray-400 mt-1">Fee dari owner + pendapatan lain − pengeluaran perusahaan</p>
                </div>
                <div class="grid grid-cols-3 gap-4 sm:gap-6 text-sm">
                    <div>
                        <p class="text-gray-500">Fee Platform</p>
                        <p class="font-bold text-emerald-600 whitespace-nowrap">Rp {{ number_format($p['fee_income'], 0, ',', '.') }}</p>
                    </div>
                    <div>
                        <p class="text-gray-500">Pendapatan Lain</p>
                        <p class="font-bold text-emerald-600 whitespace-nowrap">+ Rp {{ number_format($p['other_income'], 0, ',', '.') }}</p>
                    </div>
                    <div>
                        <p class="text-gray-500">Pengeluaran</p>
                        <p class="font-bold text-red-500 whitespace-nowrap">− Rp {{ number_format($p['company_expense'], 0, ',', '.') }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Platform Income Chart -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mb-8">
            <h3 class="text-lg font-bold text-gray-900 mb-4">Pendapatan Kotor Platform (6 Bulan Terakhir)</h3>
            <div class="h-64"><canvas id="incomeChart"></canvas></div>
        </div>

        <!-- Per-Owner Breakdown -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden mb-8">
            <div class="px-6 py-4 border-b border-gray-100">
                <h3 class="text-lg font-bold text-gray-900">Rincian per Owner</h3>
                <p class="text-sm text-gray-500">Ringkasan performa tiap owner</p>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wide whitespace-nowrap">Owner</th>
                            <th class="px-6 py-3 text-center text-xs font-semibold text-gray-700 uppercase tracking-wide whitespace-nowrap">Properti</th>
                            <th class="px-6 py-3 text-center text-xs font-semibold text-gray-700 uppercase tracking-wide whitespace-nowrap">Kamar</th>
                            <th class="px-6 py-3 text-center text-xs font-semibold text-gray-700 uppercase tracking-wide whitespace-nowrap">Hunian</th>
                            <th class="px-6 py-3 text-center text-xs font-semibold text-gray-700 uppercase tracking-wide whitespace-nowrap">Penyewa</th>
                            <th class="px-6 py-3 text-right text-xs font-semibold text-gray-700 uppercase tracking-wide whitespace-nowrap">Pendapatan (Bln Ini)</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse ($superadmin['owners'] as $o)
                            <tr class="hover:bg-orange-50 transition">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <p class="font-semibold text-gray-900">{{ $o['name'] }}</p>
                                    <p class="text-xs text-gray-500">{{ $o['email'] }}</p>
                                </td>
                                <td class="px-6 py-4 text-center text-gray-700">{{ $o['properties'] }}</td>
                                <td class="px-6 py-4 text-center text-gray-700">{{ $o['occupied'] }}/{{ $o['rooms'] }}</td>
                                <td class="px-6 py-4 text-center">
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium {{ $o['occupancy'] >= 70 ? 'bg-green-100 text-green-700' : ($o['occupancy'] >= 40 ? 'bg-yellow-100 text-yellow-700' : 'bg-red-100 text-red-700') }}">{{ $o['occupancy'] }}%</span>
                                </td>
                                <td class="px-6 py-4 text-center text-gray-700">{{ $o['tenants'] }}</td>
                                <td class="px-6 py-4 text-right font-semibold text-gray-900 whitespace-nowrap">Rp {{ number_format($o['income'], 0, ',', '.') }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="px-6 py-8 text-center text-gray-500">Belum ada owner terdaftar.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    @elseif ($isOwner || $isManager)
        {{-- ======================== OWNER / MANAGER DASHBOARD ======================== --}}

        <!-- Metric Cards -->
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
            <!-- Total Kamar -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 hover:shadow-md transition">
                <div class="flex items-center justify-between mb-3">
                    <div class="p-2.5 bg-orange-100 rounded-xl">
                        <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 12l2-2m0 0l7-4 7 4M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6">
                            </path>
                        </svg>
                    </div>
                    <span
                        class="text-xs font-semibold px-2 py-1 rounded-full {{ $metrics['available_rooms'] > 0 ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-600' }}">
                        {{ $metrics['available_rooms'] }} tersedia
                    </span>
                </div>
                <p class="text-3xl font-bold text-gray-900">{{ $metrics['total_rooms'] }}</p>
                <p class="text-sm text-gray-500 mt-1">Total Kamar</p>
            </div>

            <!-- Tingkat Hunian -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 hover:shadow-md transition">
                <div class="flex items-center justify-between mb-3">
                    <div class="p-2.5 bg-green-100 rounded-xl">
                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z">
                            </path>
                        </svg>
                    </div>
                </div>
                <p class="text-3xl font-bold text-gray-900">{{ $metrics['occupancy_rate'] }}%</p>
                <p class="text-sm text-gray-500 mt-1">Tingkat Hunian</p>
                <div class="mt-2 w-full bg-gray-200 rounded-full h-2">
                    <div class="bg-green-500 h-2 rounded-full transition-all duration-500"
                        style="width: {{ $metrics['occupancy_rate'] }}%"></div>
                </div>
            </div>

            <!-- Pendapatan Bersih Bulan Ini -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 hover:shadow-md transition">
                <div class="flex items-center justify-between mb-3">
                    <div class="p-2.5 bg-emerald-100 rounded-xl">
                        <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z">
                            </path>
                        </svg>
                    </div>
                </div>
                <p class="text-xl sm:text-2xl font-bold {{ $metrics['net_income_this_month'] >= 0 ? 'text-gray-900' : 'text-red-600' }}">Rp
                    {{ number_format($metrics['net_income_this_month'], 0, ',', '.') }}</p>
                <p class="text-sm text-gray-500 mt-1">Pendapatan Bersih Bulan Ini</p>
                <div class="mt-3 pt-3 border-t border-gray-100 space-y-1 text-xs">
                    <div class="flex justify-between"><span class="text-gray-500">Pendapatan kotor</span><span class="font-medium text-gray-700">Rp {{ number_format($metrics['income_this_month'], 0, ',', '.') }}</span></div>
                    <div class="flex justify-between"><span class="text-gray-500">Pengeluaran</span><span class="font-medium text-red-500">− Rp {{ number_format($metrics['expense_this_month'], 0, ',', '.') }}</span></div>
                    @if ($metrics['platform_fee_this_month'] > 0)
                        <div class="flex justify-between"><span class="text-gray-500">Fee platform</span><span class="font-medium text-red-500">− Rp {{ number_format($metrics['platform_fee_this_month'], 0, ',', '.') }}</span></div>
                    @endif
                </div>
            </div>

            <!-- Tagihan Tertunggak -->
            <div
                class="bg-white rounded-2xl shadow-sm border {{ $metrics['overdue_invoices'] > 0 ? 'border-red-200' : 'border-gray-100' }} p-5 hover:shadow-md transition">
                <div class="flex items-center justify-between mb-3">
                    <div class="p-2.5 {{ $metrics['overdue_invoices'] > 0 ? 'bg-red-100' : 'bg-gray-100' }} rounded-xl">
                        <svg class="w-6 h-6 {{ $metrics['overdue_invoices'] > 0 ? 'text-red-600' : 'text-gray-600' }}"
                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    @if ($metrics['pending_payments'] > 0)
                        <span class="text-xs font-semibold px-2 py-1 rounded-full bg-amber-100 text-amber-700">
                            {{ $metrics['pending_payments'] }} menunggu
                        </span>
                    @endif
                </div>
                <p
                    class="text-3xl font-bold {{ $metrics['overdue_invoices'] > 0 ? 'text-red-600' : 'text-gray-900' }}">
                    {{ $metrics['overdue_invoices'] }}</p>
                <p class="text-sm text-gray-500 mt-1">Tagihan Tertunggak</p>
            </div>
        </div>

        <!-- Secondary Stats Row -->
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-4 mb-8">
            <div class="bg-white rounded-xl border border-gray-100 px-5 py-4 flex items-center gap-4">
                <div class="p-2 bg-orange-100 rounded-lg">
                    <svg class="w-5 h-5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M19 21H5a2 2 0 01-2-2V5a2 2 0 012-2h11l5 5v11a2 2 0 01-2 2z"></path>
                    </svg>
                </div>
                <div>
                    <p class="text-xl font-bold text-gray-900">{{ $metrics['total_properties'] }}</p>
                    <p class="text-xs text-gray-500">Properti</p>
                </div>
            </div>
            <div class="bg-white rounded-xl border border-gray-100 px-5 py-4 flex items-center gap-4">
                <div class="p-2 bg-teal-100 rounded-lg">
                    <svg class="w-5 h-5 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17 20h5v-2a3 3 0 00-5.856-1.487M15 10a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                </div>
                <div>
                    <p class="text-xl font-bold text-gray-900">{{ $metrics['active_tenants'] }}</p>
                    <p class="text-xs text-gray-500">Penyewa Aktif</p>
                </div>
            </div>
            <div class="bg-white rounded-xl border border-gray-100 px-5 py-4 flex items-center gap-4">
                <div class="p-2 bg-amber-100 rounded-lg">
                    <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                        </path>
                    </svg>
                </div>
                <div>
                    <p class="text-xl font-bold text-gray-900">{{ $metrics['active_leases'] }}</p>
                    <p class="text-xs text-gray-500">Kontrak Aktif</p>
                </div>
            </div>
            <div class="bg-white rounded-xl border border-gray-100 px-5 py-4 flex items-center gap-4">
                <div
                    class="p-2 {{ $metrics['pending_maintenance'] > 0 ? 'bg-orange-100' : 'bg-gray-100' }} rounded-lg">
                    <svg class="w-5 h-5 {{ $metrics['pending_maintenance'] > 0 ? 'text-orange-600' : 'text-gray-500' }}"
                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.066 2.573c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.573 1.066c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.066-2.573c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z M15 12a3 3 0 11-6 0 3 3 0 016 0z">
                        </path>
                    </svg>
                </div>
                <div>
                    <p
                        class="text-xl font-bold {{ $metrics['pending_maintenance'] > 0 ? 'text-orange-600' : 'text-gray-900' }}">
                        {{ $metrics['pending_maintenance'] }}</p>
                    <p class="text-xs text-gray-500">Perbaikan Menunggu</p>
                </div>
            </div>
            <div class="bg-white rounded-xl border border-gray-100 px-5 py-4 flex items-center gap-4">
                <div class="p-2 bg-red-100 rounded-lg">
                    <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z">
                        </path>
                    </svg>
                </div>
                <div>
                    <p class="text-xl font-bold text-red-600">Rp
                        {{ number_format($metrics['expense_this_month'], 0, ',', '.') }}</p>
                    <p class="text-xs text-gray-500">Pengeluaran Bulan Ini</p>
                </div>
            </div>
        </div>

        <!-- Chart + Activity Row -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
            <!-- Income Chart -->
            <div class="lg:col-span-2 bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                <div class="flex items-center justify-between mb-6">
                    <div>
                        <h2 class="text-lg font-bold text-gray-800">Pendapatan 6 Bulan Terakhir</h2>
                        <p class="text-sm text-gray-500">Grafik pendapatan yang sudah terverifikasi</p>
                    </div>
                </div>
                <div wire:ignore>
                    <canvas id="incomeChart" height="220"></canvas>
                </div>
            </div>

            <!-- Recent Maintenance -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-lg font-bold text-gray-800">Perbaikan Terbaru</h2>
                    <a href="{{ route('maintenance.index') }}" wire:navigate
                        class="text-sm text-orange-600 hover:text-orange-700 font-medium">Lihat Semua</a>
                </div>
                <div class="space-y-3">
                    @forelse ($recentMaintenance as $req)
                        <div class="flex items-start gap-3 p-3 rounded-xl bg-gray-50 hover:bg-gray-100 transition">
                            <div
                                class="mt-0.5 p-1.5 rounded-lg
                                {{ $req->status === 'pending' ? 'bg-amber-100' : ($req->status === 'in_progress' ? 'bg-orange-100' : ($req->status === 'completed' ? 'bg-green-100' : 'bg-red-100')) }}">
                                @if ($req->status === 'pending')
                                    <svg class="w-4 h-4 text-amber-600" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                @elseif($req->status === 'in_progress')
                                    <svg class="w-4 h-4 text-orange-600" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15">
                                        </path>
                                    </svg>
                                @elseif($req->status === 'completed')
                                    <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M5 13l4 4L19 7"></path>
                                    </svg>
                                @else
                                    <svg class="w-4 h-4 text-red-600" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                @endif
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-gray-800 truncate">{{ $req->title }}</p>
                                <p class="text-xs text-gray-500">
                                    {{ $req->room?->room_number ? 'Kamar ' . $req->room->room_number : '' }}
                                    &middot; {{ $req->created_at->diffForHumans() }}
                                </p>
                            </div>
                            <span
                                class="text-xs px-2 py-0.5 rounded-full font-medium
                                {{ $req->priority === 'high' ? 'bg-red-100 text-red-700' : ($req->priority === 'medium' ? 'bg-amber-100 text-amber-700' : 'bg-gray-100 text-gray-600') }}">
                                {{ $req->priority === 'high' ? 'Tinggi' : ($req->priority === 'medium' ? 'Sedang' : 'Rendah') }}
                            </span>
                        </div>
                    @empty
                        <div class="text-center py-6 text-gray-400">
                            <svg class="w-10 h-10 mx-auto mb-2 text-gray-300" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <p class="text-sm">Belum ada permintaan</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Recent Invoices + Expiring Leases Row -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
            <!-- Recent Invoices -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-lg font-bold text-gray-800">Invoice Terbaru</h2>
                    <a href="{{ route('invoices.index') }}" wire:navigate
                        class="text-sm text-orange-600 hover:text-orange-700 font-medium">Lihat Semua</a>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="text-left text-xs text-gray-500 uppercase border-b border-gray-100">
                                <th class="pb-3 font-medium">Penyewa</th>
                                <th class="pb-3 font-medium">Kamar</th>
                                <th class="pb-3 font-medium">Jumlah</th>
                                <th class="pb-3 font-medium">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @forelse ($recentInvoices as $inv)
                                <tr class="hover:bg-gray-50 transition">
                                    <td class="py-3">
                                        <p class="font-medium text-gray-800">
                                            {{ $inv->lease?->tenant?->display_name ?? '-' }}</p>
                                    </td>
                                    <td class="py-3 text-gray-600">
                                        {{ $inv->lease?->room?->room_number ?? '-' }}</td>
                                    <td class="py-3 text-gray-800 font-medium">
                                        Rp {{ number_format($inv->amount, 0, ',', '.') }}</td>
                                    <td class="py-3">
                                        @php
                                            $statusColor = match ($inv->status) {
                                                'paid' => 'bg-green-100 text-green-700',
                                                'pending' => 'bg-amber-100 text-amber-700',
                                                'unpaid' => 'bg-red-100 text-red-700',
                                                default => 'bg-gray-100 text-gray-600',
                                            };
                                            $statusLabel = match ($inv->status) {
                                                'paid' => 'Lunas',
                                                'pending' => 'Menunggu',
                                                'unpaid' => 'Belum Bayar',
                                                default => $inv->status,
                                            };
                                        @endphp
                                        <span
                                            class="text-xs px-2 py-1 rounded-full font-medium {{ $statusColor }}">{{ $statusLabel }}</span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="py-6 text-center text-gray-400 text-sm">Belum ada
                                        invoice
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Expiring Leases -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-lg font-bold text-gray-800">Kontrak Akan Berakhir</h2>
                    <a href="{{ route('leases.index') }}" wire:navigate
                        class="text-sm text-orange-600 hover:text-orange-700 font-medium">Lihat Semua</a>
                </div>
                <div class="space-y-3">
                    @forelse ($expiringLeases as $lease)
                        @php
                            $daysLeft = (int) now()->diffInDays($lease->end_date, false);
                        @endphp
                        <div
                            class="flex items-center justify-between p-3 rounded-xl {{ $daysLeft <= 7 ? 'bg-red-50 border border-red-100' : 'bg-gray-50' }}">
                            <div class="flex items-center gap-3">
                                <div
                                    class="w-10 h-10 rounded-full {{ $daysLeft <= 7 ? 'bg-red-100' : 'bg-amber-100' }} flex items-center justify-center">
                                    <span
                                        class="text-sm font-bold {{ $daysLeft <= 7 ? 'text-red-600' : 'text-amber-600' }}">{{ $daysLeft }}</span>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-800">
                                        {{ $lease->tenant?->display_name ?? '-' }}</p>
                                    <p class="text-xs text-gray-500">Kamar
                                        {{ $lease->room?->room_number ?? '-' }} &middot;
                                        {{ $lease->end_date->translatedFormat('d M Y') }}</p>
                                </div>
                            </div>
                            <span
                                class="text-xs font-medium {{ $daysLeft <= 7 ? 'text-red-600' : 'text-amber-600' }}">
                                {{ $daysLeft }} hari lagi
                            </span>
                        </div>
                    @empty
                        <div class="text-center py-6 text-gray-400">
                            <svg class="w-10 h-10 mx-auto mb-2 text-gray-300" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <p class="text-sm">Tidak ada kontrak yang akan berakhir</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <h2 class="text-lg font-bold text-gray-800 mb-4">Aksi Cepat</h2>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                @if ($isOwner)
                    <a href="{{ route('properties.index') }}" wire:navigate
                        class="group flex flex-col items-center gap-2 px-4 py-5 bg-orange-50 hover:bg-orange-100 rounded-xl transition">
                        <div class="p-2.5 bg-orange-200 group-hover:bg-orange-300 rounded-xl transition">
                            <svg class="w-5 h-5 text-orange-700" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 21H5a2 2 0 01-2-2V5a2 2 0 012-2h11l5 5v11a2 2 0 01-2 2z"></path>
                            </svg>
                        </div>
                        <span class="text-sm font-medium text-gray-700">Properti</span>
                    </a>
                @endif
                <a href="{{ route('rooms.index') }}" wire:navigate
                    class="group flex flex-col items-center gap-2 px-4 py-5 bg-orange-50 hover:bg-orange-100 rounded-xl transition">
                    <div class="p-2.5 bg-orange-200 group-hover:bg-orange-300 rounded-xl transition">
                        <svg class="w-5 h-5 text-orange-700" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 12l2-2m0 0l7-4 7 4M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6">
                            </path>
                        </svg>
                    </div>
                    <span class="text-sm font-medium text-gray-700">Ruangan</span>
                </a>
                <a href="{{ route('invoices.index') }}" wire:navigate
                    class="group flex flex-col items-center gap-2 px-4 py-5 bg-green-50 hover:bg-green-100 rounded-xl transition">
                    <div class="p-2.5 bg-green-200 group-hover:bg-green-300 rounded-xl transition">
                        <svg class="w-5 h-5 text-green-700" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z">
                            </path>
                        </svg>
                    </div>
                    <span class="text-sm font-medium text-gray-700">Invoice</span>
                </a>
                <a href="{{ route('reports.index') }}" wire:navigate
                    class="group flex flex-col items-center gap-2 px-4 py-5 bg-purple-50 hover:bg-purple-100 rounded-xl transition">
                    <div class="p-2.5 bg-purple-200 group-hover:bg-purple-300 rounded-xl transition">
                        <svg class="w-5 h-5 text-purple-700" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z">
                            </path>
                        </svg>
                    </div>
                    <span class="text-sm font-medium text-gray-700">Laporan</span>
                </a>
            </div>
        </div>
    @elseif ($isTenant)
        {{-- ======================== TENANT DASHBOARD ======================== --}}

        @if ($tenant['lease'])
            <!-- Tenant Metric Cards -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8">
                <!-- Status Kontrak -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 hover:shadow-md transition">
                    <div class="flex items-center justify-between mb-3">
                        <div class="p-2.5 bg-green-100 rounded-xl">
                            <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <span class="text-xs font-semibold px-2 py-1 rounded-full bg-green-100 text-green-700">
                            Aktif
                        </span>
                    </div>
                    <p class="text-lg font-bold text-gray-900">Kamar {{ $tenant['lease']->room?->room_number }}</p>
                    <p class="text-sm text-gray-500 mt-1">
                        {{ $tenant['lease']->room?->roomType?->name ?? 'Ruangan' }}
                    </p>
                </div>

                <!-- Kontrak Period -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 hover:shadow-md transition">
                    <div class="flex items-center justify-between mb-3">
                        <div class="p-2.5 bg-orange-100 rounded-xl">
                            <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z">
                                </path>
                            </svg>
                        </div>
                    </div>
                    <p class="text-sm font-bold text-gray-900">
                        {{ $tenant['lease']->start_date->translatedFormat('d M Y') }}</p>
                    <p class="text-xs text-gray-500 my-1">sampai</p>
                    <p class="text-sm font-bold text-gray-900">
                        {{ $tenant['lease']->end_date->translatedFormat('d M Y') }}</p>
                    <p class="text-sm text-gray-500 mt-1">Masa Kontrak</p>
                </div>

                <!-- Tagihan Belum Bayar -->
                <div
                    class="bg-white rounded-2xl shadow-sm border {{ $tenant['unpaid'] > 0 ? 'border-red-200' : 'border-gray-100' }} p-5 hover:shadow-md transition">
                    <div class="flex items-center justify-between mb-3">
                        <div class="p-2.5 {{ $tenant['unpaid'] > 0 ? 'bg-red-100' : 'bg-gray-100' }} rounded-xl">
                            <svg class="w-6 h-6 {{ $tenant['unpaid'] > 0 ? 'text-red-600' : 'text-gray-500' }}"
                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z">
                                </path>
                            </svg>
                        </div>
                    </div>
                    <p class="text-3xl font-bold {{ $tenant['unpaid'] > 0 ? 'text-red-600' : 'text-gray-900' }}">
                        {{ $tenant['unpaid'] }}</p>
                    <p class="text-sm text-gray-500 mt-1">Tagihan Belum Bayar</p>
                </div>
            </div>

            <!-- Next Payment & Maintenance -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
                <!-- Next Payment -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                    <h2 class="text-lg font-bold text-gray-800 mb-4">Pembayaran Berikutnya</h2>
                    @if ($tenant['next_invoice'])
                        <div
                            class="p-4 rounded-xl {{ $tenant['next_invoice']->due_date < now() ? 'bg-red-50 border border-red-100' : 'bg-orange-50 border border-orange-100' }}">
                            <div class="flex items-center justify-between mb-3">
                                <span
                                    class="text-xs font-semibold px-2 py-1 rounded-full {{ $tenant['next_invoice']->due_date < now() ? 'bg-red-100 text-red-700' : 'bg-orange-100 text-orange-700' }}">
                                    {{ $tenant['next_invoice']->due_date < now() ? 'Terlambat' : 'Menunggu' }}
                                </span>
                                <p class="text-xs text-gray-500">Jatuh tempo:
                                    {{ $tenant['next_invoice']->due_date->translatedFormat('d M Y') }}</p>
                            </div>
                            <p class="text-2xl font-bold text-gray-900">
                                Rp {{ number_format($tenant['next_invoice']->amount, 0, ',', '.') }}</p>
                            <p class="text-sm text-gray-600 mt-1">Periode:
                                {{ \Carbon\Carbon::parse($tenant['next_invoice']->month_year)->translatedFormat('F Y') }}
                            </p>
                        </div>
                    @else
                        <div class="text-center py-8 text-gray-400">
                            <svg class="w-12 h-12 mx-auto mb-2 text-gray-300" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <p class="text-sm">Semua tagihan sudah lunas!</p>
                        </div>
                    @endif
                </div>

                <!-- Recent Maintenance -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="text-lg font-bold text-gray-800">Permintaan Perbaikan</h2>
                        <a href="{{ route('maintenance.index') }}" wire:navigate
                            class="text-sm text-orange-600 hover:text-orange-700 font-medium">Lihat Semua</a>
                    </div>
                    <div class="space-y-3">
                        @forelse ($tenant['maintenance'] as $req)
                            <div class="flex items-center gap-3 p-3 rounded-xl bg-gray-50">
                                @php
                                    $mColor = match ($req->status) {
                                        'pending' => 'amber',
                                        'in_progress' => 'blue',
                                        'completed' => 'green',
                                        'rejected' => 'red',
                                        default => 'gray',
                                    };
                                    $mLabel = match ($req->status) {
                                        'pending' => 'Menunggu',
                                        'in_progress' => 'Diproses',
                                        'completed' => 'Selesai',
                                        'rejected' => 'Ditolak',
                                        default => $req->status,
                                    };
                                @endphp
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium text-gray-800 truncate">{{ $req->title }}</p>
                                    <p class="text-xs text-gray-500">{{ $req->created_at->diffForHumans() }}</p>
                                </div>
                                <span
                                    class="text-xs px-2 py-1 rounded-full font-medium bg-{{ $mColor }}-100 text-{{ $mColor }}-700">
                                    {{ $mLabel }}
                                </span>
                            </div>
                        @empty
                            <div class="text-center py-6 text-gray-400">
                                <p class="text-sm">Belum ada permintaan perbaikan</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        @else
            <!-- No Active Lease -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8 text-center mb-8">
                <svg class="w-16 h-16 mx-auto mb-4 text-gray-300" fill="none" stroke="currentColor"
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M3 12l2-2m0 0l7-4 7 4M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6">
                    </path>
                </svg>
                <h2 class="text-xl font-bold text-gray-700 mb-2">Belum Ada Kontrak Aktif</h2>
                <p class="text-gray-500">Hubungi pemilik kos untuk informasi lebih lanjut.</p>
            </div>
        @endif

        <!-- Tenant Quick Actions -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <h2 class="text-lg font-bold text-gray-800 mb-4">Aksi Cepat</h2>
            <div class="grid grid-cols-2 gap-3">
                <a href="{{ route('tenant.invoices.index') }}" wire:navigate
                    class="group flex flex-col items-center gap-2 px-4 py-5 bg-orange-50 hover:bg-orange-100 rounded-xl transition">
                    <div class="p-2.5 bg-orange-200 group-hover:bg-orange-300 rounded-xl transition">
                        <svg class="w-5 h-5 text-orange-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z">
                            </path>
                        </svg>
                    </div>
                    <span class="text-sm font-medium text-gray-700">Invoice Saya</span>
                </a>
                <a href="{{ route('maintenance.index') }}" wire:navigate
                    class="group flex flex-col items-center gap-2 px-4 py-5 bg-amber-50 hover:bg-amber-100 rounded-xl transition">
                    <div class="p-2.5 bg-amber-200 group-hover:bg-amber-300 rounded-xl transition">
                        <svg class="w-5 h-5 text-amber-700" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.066 2.573c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.573 1.066c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.066-2.573c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z M15 12a3 3 0 11-6 0 3 3 0 016 0z">
                            </path>
                        </svg>
                    </div>
                    <span class="text-sm font-medium text-gray-700">Perbaikan</span>
                </a>
            </div>
        </div>
    @endif

    {{-- Chart.js for income chart --}}
    @if ($isSuperAdmin || $isOwner || $isManager)
        @assets
            <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
        @endassets
        @script
            <script>
                const ctx = document.getElementById('incomeChart');
                if (ctx) {
                    const chartData = @json($incomeChart);
                    new Chart(ctx, {
                        type: 'bar',
                        data: {
                            labels: chartData.labels,
                            datasets: [{
                                label: 'Pendapatan (Rp)',
                                data: chartData.data,
                                backgroundColor: 'rgba(249, 115, 22, 0.7)',
                                borderColor: 'rgba(249, 115, 22, 1)',
                                borderWidth: 1,
                                borderRadius: 8,
                                barPercentage: 0.6,
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: {
                                    display: false
                                },
                                tooltip: {
                                    callbacks: {
                                        label: (ctx) => 'Rp ' + ctx.parsed.y.toLocaleString('id-ID'),
                                    }
                                }
                            },
                            scales: {
                                y: {
                                    beginAtZero: true,
                                    ticks: {
                                        callback: (v) => 'Rp ' + (v / 1000000).toFixed(1) + 'jt',
                                        font: {
                                            size: 11
                                        }
                                    },
                                    grid: {
                                        color: 'rgba(0,0,0,0.05)'
                                    }
                                },
                                x: {
                                    ticks: {
                                        font: {
                                            size: 11
                                        }
                                    },
                                    grid: {
                                        display: false
                                    }
                                }
                            }
                        }
                    });
                }
            </script>
        @endscript
    @endif
</div>
