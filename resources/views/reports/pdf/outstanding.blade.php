<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Tagihan Belum Lunas</title>
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
            color: #dc2626;
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
            width: 33.33%;
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

        .text-red {
            color: #dc2626;
        }

        .text-orange {
            color: #ea580c;
        }

        table.data-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        table.data-table th {
            background: #f3f4f6;
            color: #374151;
            font-size: 10px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.3px;
            padding: 8px 8px;
            text-align: left;
            border-bottom: 2px solid #e5e7eb;
        }

        table.data-table td {
            padding: 6px 8px;
            border-bottom: 1px solid #f3f4f6;
            font-size: 11px;
        }

        table.data-table tr:nth-child(even) {
            background: #f9fafb;
        }

        .row-overdue {
            background: #fef2f2 !important;
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

        .badge-yellow {
            background: #fef9c3;
            color: #ca8a04;
        }

        .badge-red {
            background: #fee2e2;
            color: #dc2626;
        }

        .overdue-note {
            color: #dc2626;
            font-size: 9px;
            font-style: italic;
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

        <div class="report-title">Tagihan Belum Lunas</div>
        <div class="report-period">Per tanggal
            {{ now()->translatedFormat('d F Y') }}{{ $propertyName ? ' — ' . $propertyName : '' }}</div>

        <!-- Summary -->
        <table class="summary-grid">
            <tr>
                <td>
                    <div class="summary-box">
                        <div class="label">Total Tagihan</div>
                        <div class="value text-red">{{ $invoices->count() }}</div>
                    </div>
                </td>
                <td>
                    <div class="summary-box">
                        <div class="label">Total Nilai</div>
                        <div class="value text-red" style="font-size: 14px;">Rp
                            {{ number_format($invoices->sum('amount'), 0, ',', '.') }}</div>
                    </div>
                </td>
                <td>
                    <div class="summary-box">
                        <div class="label">Lewat Jatuh Tempo</div>
                        <div class="value text-orange">
                            {{ $invoices->filter(fn($i) => $i->due_date && $i->due_date->isPast())->count() }}</div>
                    </div>
                </td>
            </tr>
        </table>

        <!-- Table -->
        <table class="data-table">
            <thead>
                <tr>
                    <th>No. Invoice</th>
                    <th>Penyewa</th>
                    <th>Kamar</th>
                    <th>Properti</th>
                    <th>Bulan</th>
                    <th class="text-right">Jumlah</th>
                    <th>Jatuh Tempo</th>
                    <th class="text-center">Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($invoices as $invoice)
                    @php
                        $isOverdue = $invoice->due_date && $invoice->due_date->isPast();
                    @endphp
                    <tr class="{{ $isOverdue ? 'row-overdue' : '' }}">
                        <td class="mono">{{ $invoice->reference_number }}</td>
                        <td>{{ $invoice->lease?->tenant?->display_name ?? '-' }}</td>
                        <td>{{ $invoice->lease?->room?->room_number ?? '-' }}</td>
                        <td style="font-size: 10px;">{{ $invoice->lease?->room?->roomType?->property?->name ?? '-' }}
                        </td>
                        <td>{{ $invoice->month_year }}</td>
                        <td class="text-right" style="font-weight: 600;">Rp
                            {{ number_format($invoice->amount, 0, ',', '.') }}</td>
                        <td>
                            {{ $invoice->due_date?->translatedFormat('d M Y') ?? '-' }}
                            @if ($isOverdue)
                                <br><span class="overdue-note">{{ $invoice->due_date->diffForHumans() }}</span>
                            @endif
                        </td>
                        <td class="text-center">
                            <span class="badge {{ $invoice->status === 'pending' ? 'badge-yellow' : 'badge-red' }}">
                                {{ $invoice->status === 'pending' ? 'Pending' : 'Belum Bayar' }}
                            </span>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center" style="padding: 20px; color: #999;">Tidak ada tagihan
                            yang belum lunas</td>
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
