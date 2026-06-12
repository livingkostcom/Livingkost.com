<div>
    <!-- Header -->
    <div class="mb-8">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1
                    class="text-2xl sm:text-4xl font-bold bg-gradient-to-r from-blue-600 to-indigo-600 bg-clip-text text-transparent">
                    Analisis Pendapatan
                </h1>
                <p class="mt-2 text-gray-600">Dashboard analisis dan laporan pendapatan kos Anda</p>
            </div>
        </div>
    </div>

    <!-- Date Range Filter -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <div class="flex flex-col md:flex-row gap-4 items-end">
            <div class="flex-1">
                <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal Mulai</label>
                <input type="date" wire:model.live="startDate"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" />
            </div>
            <div class="flex-1">
                <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal Akhir</label>
                <input type="date" wire:model.live="endDate"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" />
            </div>
            <div class="flex flex-wrap gap-2">
                <button wire:click="setLast7Days()"
                    class="px-3 py-2 bg-blue-100 text-blue-700 rounded-lg hover:bg-blue-200 transition text-sm font-medium">
                    7 Hari
                </button>
                <button wire:click="setLast30Days()"
                    class="px-3 py-2 bg-blue-100 text-blue-700 rounded-lg hover:bg-blue-200 transition text-sm font-medium">
                    30 Hari
                </button>
                <button wire:click="setThisMonth()"
                    class="px-3 py-2 bg-blue-100 text-blue-700 rounded-lg hover:bg-blue-200 transition text-sm font-medium">
                    Bulan Ini
                </button>
                <button wire:click="resetDates()"
                    class="px-3 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition text-sm font-medium">
                    Reset
                </button>
            </div>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <!-- Total Received -->
        <div class="bg-gradient-to-br from-green-50 to-green-100 rounded-xl p-6 shadow-md border border-green-200">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-sm font-semibold text-green-900">Pendapatan Diterima</h3>
                <svg class="w-6 h-6 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            <p class="text-3xl font-bold text-green-900">
                Rp {{ number_format($summary['received'], 0, ',', '.') }}
            </p>
            <p class="text-sm text-green-700 mt-2">{{ $summary['paid_count'] }} invoice terverifikasi</p>
        </div>

        <!-- Pending Amount -->
        <div class="bg-gradient-to-br from-yellow-50 to-yellow-100 rounded-xl p-6 shadow-md border border-yellow-200">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-sm font-semibold text-yellow-900">Menunggu Verifikasi</h3>
                <svg class="w-6 h-6 text-yellow-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            <p class="text-3xl font-bold text-yellow-900">
                Rp {{ number_format($summary['pending_amount'], 0, ',', '.') }}
            </p>
            <p class="text-sm text-yellow-700 mt-2">{{ $summary['pending_count'] }} invoice pending</p>
        </div>

        <!-- Unpaid Amount -->
        <div class="bg-gradient-to-br from-red-50 to-red-100 rounded-xl p-6 shadow-md border border-red-200">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-sm font-semibold text-red-900">Belum Terbayar</h3>
                <svg class="w-6 h-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 8v4m0 4v.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            <p class="text-3xl font-bold text-red-900">
                Rp {{ number_format($summary['unpaid_amount'], 0, ',', '.') }}
            </p>
            <p class="text-sm text-red-700 mt-2">{{ $summary['unpaid_count'] }} invoice unpaid</p>
        </div>

        <!-- Total Amount -->
        <div class="bg-gradient-to-br from-blue-50 to-blue-100 rounded-xl p-6 shadow-md border border-blue-200">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-sm font-semibold text-blue-900">Total Invoice</h3>
                <svg class="w-6 h-6 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
            </div>
            <p class="text-3xl font-bold text-blue-900">
                Rp {{ number_format($summary['total_amount'], 0, ',', '.') }}
            </p>
            <p class="text-sm text-blue-700 mt-2">{{ $summary['total_invoices'] }} invoice</p>
        </div>
    </div>

    <!-- Charts -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">

        <!-- Revenue Trend Chart -->
        <div class="lg:col-span-2 bg-white rounded-lg shadow-md p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Tren Pendapatan Harian</h3>
            <div wire:ignore style="position: relative; height: 320px; width: 100%;">
                <canvas id="chartCanvas"></canvas>
            </div>
        </div>

        <!-- Payment Status Breakdown -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Status Pembayaran</h3>
            <div wire:ignore style="position: relative; height: 320px; width: 100%;">
                <canvas id="statusCanvas"></canvas>
            </div>
        </div>
    </div>

    <!-- Collection Rate -->
    <div class="bg-white rounded-lg shadow-md p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-6">Tingkat Koleksi</h3>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div>
                <div class="flex items-center justify-between mb-2">
                    <span class="text-sm font-medium text-gray-700">Diterima</span>
                    <span class="text-lg font-bold text-green-600">
                        @php
                            $collectionRate =
                                $summary['total_amount'] > 0
                                    ? round(($summary['received'] / $summary['total_amount']) * 100, 1)
                                    : 0;
                        @endphp
                        {{ $collectionRate }}%
                    </span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-2">
                    <div class="bg-green-500 h-2 rounded-full" style="width: {{ $collectionRate }}%"></div>
                </div>
            </div>
            <div>
                <div class="flex items-center justify-between mb-2">
                    <span class="text-sm font-medium text-gray-700">Pending</span>
                    <span class="text-lg font-bold text-yellow-600">
                        @php
                            $pendingRate =
                                $summary['total_amount'] > 0
                                    ? round(($summary['pending_amount'] / $summary['total_amount']) * 100, 1)
                                    : 0;
                        @endphp
                        {{ $pendingRate }}%
                    </span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-2">
                    <div class="bg-yellow-500 h-2 rounded-full" style="width: {{ $pendingRate }}%"></div>
                </div>
            </div>
            <div>
                <div class="flex items-center justify-between mb-2">
                    <span class="text-sm font-medium text-gray-700">Unpaid</span>
                    <span class="text-lg font-bold text-red-600">
                        @php
                            $unpaidRate =
                                $summary['total_amount'] > 0
                                    ? round(($summary['unpaid_amount'] / $summary['total_amount']) * 100, 1)
                                    : 0;
                        @endphp
                        {{ $unpaidRate }}%
                    </span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-2">
                    <div class="bg-red-500 h-2 rounded-full" style="width: {{ $unpaidRate }}%"></div>
                </div>
            </div>
        </div>
    </div>

    @assets
        <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.js"></script>
    @endassets

    @script
        <script>
            (function() {
                let revenueChart = null;
                let statusChart = null;

                function renderIncomeCharts(revenueData, statusData) {
                    if (typeof Chart === 'undefined') {
                        setTimeout(() => renderIncomeCharts(revenueData, statusData), 150);
                        return;
                    }

                    const revenueCanvas = document.getElementById('chartCanvas');
                    const statusCanvas = document.getElementById('statusCanvas');

                    if (!revenueCanvas || !statusCanvas) return;

                    if (revenueChart) revenueChart.destroy();
                    if (statusChart) statusChart.destroy();

                    revenueChart = new Chart(revenueCanvas, {
                        type: 'line',
                        data: {
                            labels: (revenueData || []).map((d) => d.date),
                            datasets: [{
                                label: 'Pendapatan (Rp)',
                                data: (revenueData || []).map((d) => d.total),
                                borderColor: '#10b981',
                                backgroundColor: 'rgba(16, 185, 129, 0.1)',
                                borderWidth: 2,
                                tension: 0.35,
                                fill: true,
                                pointRadius: 5,
                                pointHoverRadius: 7,
                            }],
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: {
                                    position: 'top'
                                },
                            },
                            scales: {
                                y: {
                                    beginAtZero: true,
                                    ticks: {
                                        callback: (value) => 'Rp ' + Number(value).toLocaleString('id-ID'),
                                    },
                                },
                            },
                        },
                    });

                    statusChart = new Chart(statusCanvas, {
                        type: 'doughnut',
                        data: {
                            labels: ['Sudah Bayar', 'Pending', 'Belum Bayar'],
                            datasets: [{
                                data: [statusData?.paid ?? 0, statusData?.pending ?? 0, statusData
                                    ?.unpaid ?? 0
                                ],
                                backgroundColor: ['#10b981', '#f59e0b', '#ef4444'],
                                borderColor: '#fff',
                                borderWidth: 2,
                            }],
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: {
                                    position: 'bottom'
                                },
                            },
                        },
                    });
                }

                // Listen for data updates from the Livewire component (fired on every render)
                $wire.on('charts-data-updated', (params) => {
                    setTimeout(() => renderIncomeCharts(params.revenueData, params.statusBreakdown), 100);
                });
            })();
        </script>
    @endscript
</div>
