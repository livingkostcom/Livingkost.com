<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Receipt - {{ $receipt->receipt_number }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            color: #333;
            background-color: #f9f9f9;
        }

        .container {
            max-width: 800px;
            margin: 20px auto;
            background: white;
            padding: 40px;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        .header {
            text-align: center;
            border-bottom: 3px solid #2563eb;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }

        .header h1 {
            color: #2563eb;
            font-size: 28px;
            margin-bottom: 5px;
        }

        .header p {
            color: #666;
            font-size: 13px;
        }

        .receipt-title {
            text-align: center;
            font-size: 24px;
            font-weight: bold;
            color: #1e40af;
            margin-bottom: 10px;
        }

        .receipt-number {
            text-align: center;
            background: #dbeafe;
            color: #1e40af;
            padding: 8px;
            border-radius: 4px;
            font-weight: bold;
            margin-bottom: 20px;
            font-size: 14px;
        }

        .section {
            margin-bottom: 25px;
        }

        .section-title {
            font-weight: bold;
            color: #2563eb;
            border-bottom: 2px solid #e5e7eb;
            padding-bottom: 8px;
            margin-bottom: 12px;
            font-size: 13px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .info-row {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            border-bottom: 1px solid #f3f4f6;
            font-size: 13px;
        }

        .info-row:last-child {
            border-bottom: none;
        }

        .info-label {
            color: #666;
            font-weight: 500;
        }

        .info-value {
            text-align: right;
            color: #1f2937;
            font-weight: 500;
        }

        .amount-section {
            background: #f0f9ff;
            border-left: 4px solid #2563eb;
            padding: 15px;
            border-radius: 4px;
            margin: 20px 0;
        }

        .amount-row {
            display: flex;
            justify-content: space-between;
            padding: 10px 0;
            font-size: 14px;
        }

        .amount-label {
            color: #666;
        }

        .amount-value {
            font-weight: bold;
            color: #1e40af;
        }

        .total-row {
            display: flex;
            justify-content: space-between;
            padding: 15px 0;
            border-top: 2px solid #2563eb;
            margin-top: 10px;
            font-size: 16px;
            font-weight: bold;
            color: #1e40af;
        }

        .payment-proof-section {
            background: #f8fafc;
            border: 1px dashed #cbd5e1;
            padding: 12px;
            border-radius: 4px;
            margin: 15px 0;
            font-size: 12px;
            color: #475569;
        }

        .payment-proof-section strong {
            display: block;
            margin-bottom: 5px;
            color: #1e293b;
        }

        .footer {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 2px solid #e5e7eb;
            text-align: center;
            color: #666;
            font-size: 12px;
        }

        .footer p {
            margin: 5px 0;
        }

        .thank-you {
            text-align: center;
            color: #2563eb;
            font-size: 14px;
            font-weight: bold;
            margin-top: 20px;
            padding: 15px;
            background: #f0f9ff;
            border-radius: 4px;
            margin-bottom: 20px;
        }

        .date-issued {
            text-align: right;
            font-size: 12px;
            color: #666;
            margin-top: 15px;
        }
    </style>
</head>

<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <h1>{{ strtoupper(\App\Models\Setting::getValue('app_name')) }}</h1>
            <p>{{ \App\Models\Setting::getValue('app_tagline') }}</p>
        </div>

        <!-- Receipt Title -->
        <div class="receipt-title">BUKTI PEMBAYARAN</div>
        <div class="receipt-number">No. Receipt: {{ $receipt->receipt_number }}</div>

        <!-- Tenant Information -->
        <div class="section">
            <div class="section-title">Informasi Penghuni</div>
            <div class="info-row">
                <span class="info-label">Nama Penghuni</span>
                <span class="info-value">{{ $receipt->invoice->lease->tenant->display_name }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Nomor Kamar</span>
                <span class="info-value">{{ $receipt->invoice->lease->room->room_number }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Tipe Kamar</span>
                <span class="info-value">{{ $receipt->invoice->lease->room->roomType->name ?? '-' }}</span>
            </div>
        </div>

        <!-- Invoice Information -->
        <div class="section">
            <div class="section-title">Informasi Invoice</div>
            <div class="info-row">
                <span class="info-label">Nomor Invoice</span>
                <span class="info-value">{{ $receipt->invoice->reference_number }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Periode</span>
                <span
                    class="info-value">{{ \Carbon\Carbon::createFromFormat('Y-m', $receipt->invoice->month_year)->translatedFormat('F Y') }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Tanggal Invoice</span>
                <span class="info-value">{{ $receipt->invoice->created_at->translatedFormat('d F Y') }}</span>
            </div>
        </div>

        <!-- Payment Amount -->
        <div class="amount-section">
            <div class="amount-row">
                <span class="amount-label">Jumlah Pembayaran</span>
                <span class="amount-value">Rp {{ number_format($receipt->invoice->amount, 0, ',', '.') }}</span>
            </div>
            <div class="total-row">
                <span>TOTAL</span>
                <span>Rp {{ number_format($receipt->invoice->amount, 0, ',', '.') }}</span>
            </div>
        </div>

        <!-- Payment Proof -->
        @if ($receipt->invoice->proof_of_payment)
            <div class="payment-proof-section">
                <strong>Bukti Pembayaran Diterima</strong>
                File: {{ basename($receipt->invoice->proof_of_payment) }}
            </div>
        @endif

        <!-- Verification Information -->
        <div class="section">
            <div class="section-title">Informasi Verifikasi</div>
            <div class="info-row">
                <span class="info-label">Diverifikasi Oleh</span>
                <span class="info-value">{{ $receipt->creator->name }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Tanggal Verifikasi</span>
                <span class="info-value">{{ $receipt->invoice->verified_at->translatedFormat('d F Y H:i') }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Status</span>
                <span class="info-value">{{ ucfirst($receipt->invoice->status) }}</span>
            </div>
        </div>

        <!-- Thank You -->
        <div class="thank-you">
            Terima Kasih Atas Pembayaran Anda
        </div>

        <!-- Date Issued -->
        <div class="date-issued">
            Diterbitkan: {{ $receipt->issued_at->translatedFormat('d F Y H:i') }}
        </div>

        <!-- Footer -->
        <div class="footer">
            <p>Dokumen ini adalah bukti pembayaran yang sah</p>
            <p>{{ strtoupper(\App\Models\Setting::getValue('app_name')) }} -
                {{ \App\Models\Setting::getValue('app_tagline') }}</p>
        </div>
    </div>
</body>

</html>
