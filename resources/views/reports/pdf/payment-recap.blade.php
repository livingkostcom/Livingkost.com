<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Rekap Pembayaran - {{ $monthLabel }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            color: #333;
            font-size: 12px;
        }

        .container {
            padding: 30px;
        }

        .header {
            text-align: center;
            border-bottom: 3px solid #ea580c;
            padding-bottom: 15px;
            margin-bottom: 25px;
        }

        .header h1 {
            color: #ea580c;
            font-size: 22px;
            margin-bottom: 3px;
        }

        .header p {
            color: #666;
            font-size: 11px;
        }

        .report-title {
            text-align: center;
            font-size: 16px;
            font-weight: bold;
            color: #1e40af;
            margin-bottom: 5px;
        }

        .report-period {
            text-align: center;
            color: #666;
            font-size: 12px;
            margin-bottom: 20px;
        }

        .summary-grid {
            width: 100%;
            margin-bottom: 20px;
        }

        .summary-grid td {
            width: 25%;
            padding: 10px;
            text-align: center;
            vertical-align: top;
        }

        .summary-box {
            border: 1px solid #e5e7eb;
            border-radius: 6px;
            padding: 12px 8px;
        }

        .summary-box .label {
            font-size: 10px;
            color: #666;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .summary-box .value {
            font-size: 18px;
            font-weight: bold;
            margin: 4px 0;
        }

        .summary-box .amount {
            font-size: 10px;
            color: #888;
        }

        .text-green {
            color: #16a34a;
        }

        .text-yellow {
            color: #ca8a04;
        }

        .text-red {
            color: #dc2626;
        }

        .text-gray {
            color: #374151;
        }

        .progress-bar {
            width: 100%;
            margin-bottom: 20px;
        }

        .progress-bar td {
            padding: 8px 0;
        }

        .progress-outer {
            width: 100%;
            background: #e5e7eb;
            border-radius: 4px;
            height: 12px;
        }

        .progress-inner {
            height: 12px;
            border-radius: 4px;
        }

        .bg-green {
            background-color: #16a34a;
        }

        .bg-yellow {
            background-color: #ca8a04;
        }

        .bg-red {
            background-color: #dc2626;
        }

        table.data-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        table.data-table th {
            background: #f3f4f6;
            color: #374151;
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.3px;
            padding: 8px 10px;
            text-align: left;
            border-bottom: 2px solid #e5e7eb;
        }

        table.data-table td {
            padding: 7px 10px;
            border-bottom: 1px solid #f3f4f6;
            font-size: 11px;
        }

        table.data-table tr:nth-child(even) {
            background: #f9fafb;
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        .badge {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 10px;
            font-size: 10px;
            font-weight: 600;
        }

        .badge-green {
            background: #dcfce7;
            color: #16a34a;
        }

        .badge-yellow {
            background: #fef9c3;
            color: #ca8a04;
        }

        .badge-red {
            background: #fee2e2;
            color: #dc2626;
        }

        .footer {
            margin-top: 30px;
            padding-top: 15px;
            border-top: 1px solid #e5e7eb;
            text-align: center;
            color: #999;
            font-size: 10px;
        }

        .mono {
            font-family: 'Courier New', monospace;
            font-size: 10px;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <h1>{{ \App\Models\Setting::getValue('app_name') }}</h1>
            <p>{{ \App\Models\Setting::getValue('app_tagline') }}</p>
        </div>

        <div class="report-title">Rekap Pembayaran Bulanan</div>
        <div class="report-period">Periode: {{ $monthLabel }}{{ $propertyName ? ' — ' . $propertyName : '' }}</div>

        <!-- Summary -->
        <table class="summary-grid">
            <tr>
                <td>
                    <div class="summary-box">
                        <div class="label">Total Invoice</div>
                        <div class="value text-gray">
                            {{ $recap['paid_count'] + $recap['pending_count'] + $recap['unpaid_count'] }}</div>
                        <div class="amount">Rp {{ number_format($recap['total_amount'], 0, ',', '.') }}</div>
                    </div>
                </td>
                <td>
                    <div class="summary-box">
                        <div class="label">Lunas</div>
                        <div class="value text-green">{{ $recap['paid_count'] }}</div>
                        <div class="amount">Rp {{ number_format($recap['paid_amount'], 0, ',', '.') }}</div>
                    </div>
                </td>
                <td>
                    <div class="summary-box">
                        <div class="label">Pending</div>
                        <div class="value text-yellow">{{ $recap['pending_count'] }}</div>
                        <div class="amount">Rp {{ number_format($recap['pending_amount'], 0, ',', '.') }}</div>
                    </div>
                </td>
                <td>
                    <div class="summary-box">
                        <div class="label">Belum Bayar</div>
                        <div class="value text-red">{{ $recap['unpaid_count'] }}</div>
                        <div class="amount">Rp {{ number_format($recap['unpaid_amount'], 0, ',', '.') }}</div>
                    </div>
                </td>
            </tr>
        </table>

        <!-- Collection Rate -->
        <table class="progress-bar">
            <tr>
                <td style="width: 150px; font-size: 11px; font-weight: 600; color: #374151;">Tingkat Koleksi</td>
                <td>
                    <div class="progress-outer">
                        <div class="progress-inner {{ $recap['collection_rate'] >= 80 ? 'bg-green' : ($recap['collection_rate'] >= 50 ? 'bg-yellow' : 'bg-red') }}"
                            style="width: {{ $recap['collection_rate'] }}%"></div>
                    </div>
                </td>
                <td style="width: 60px; text-align: right; font-weight: bold; font-size: 12px;"
                    class="{{ $recap['collection_rate'] >= 80 ? 'text-green' : ($recap['collection_rate'] >= 50 ? 'text-yellow' : 'text-red') }}">
                    {{ $recap['collection_rate'] }}%
                </td>
            </tr>
        </table>

        <!-- Invoice Table -->
        <table class="data-table">
            <thead>
                <tr>
                    <th>No. Invoice</th>
                    <th>Penyewa</th>
                    <th>Kamar</th>
                    <th class="text-right">Jumlah</th>
                    <th>Jatuh Tempo</th>
                    <th class="text-center">Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($recap['invoices'] as $invoice)
                    <tr>
                        <td class="mono">{{ $invoice->reference_number }}</td>
                        <td>{{ $invoice->lease?->tenant?->display_name ?? '-' }}</td>
                        <td>{{ $invoice->lease?->room?->room_number ?? '-' }}</td>
                        <td class="text-right" style="font-weight: 600;">Rp
                            {{ number_format($invoice->amount, 0, ',', '.') }}</td>
                        <td>{{ $invoice->due_date?->translatedFormat('d M Y') ?? '-' }}</td>
                        <td class="text-center">
                            @php
                                $badgeClass = match ($invoice->status) {
                                    'paid' => 'badge-green',
                                    'pending' => 'badge-yellow',
                                    default => 'badge-red',
                                };
                                $label = match ($invoice->status) {
                                    'paid' => 'Lunas',
                                    'pending' => 'Pending',
                                    default => 'Belum Bayar',
                                };
                            @endphp
                            <span class="badge {{ $badgeClass }}">{{ $label }}</span>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center" style="padding: 20px; color: #999;">Tidak ada data
                            invoice</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div class="footer">
            Dicetak pada {{ now()->translatedFormat('d F Y, H:i') }} — {{ \App\Models\Setting::getValue('app_name') }}
        </div>
    </div>
</body>

</html>
