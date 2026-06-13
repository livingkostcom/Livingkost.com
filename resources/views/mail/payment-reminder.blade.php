<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<style>
  body { font-family: 'Segoe UI', Arial, sans-serif; background: #f9fafb; margin: 0; padding: 0; }
  .wrap { max-width: 560px; margin: 32px auto; background: #fff; border-radius: 16px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,.08); }
  .header { background: #ea580c; padding: 28px 32px; }
  .header h1 { color: #fff; margin: 0; font-size: 22px; font-weight: 700; }
  .header p  { color: rgba(255,255,255,.85); margin: 4px 0 0; font-size: 13px; }
  .body { padding: 32px; }
  .greeting { font-size: 16px; font-weight: 600; margin-bottom: 12px; color: #111; }
  .info-box { background: #fff7ed; border: 1px solid #fed7aa; border-radius: 12px; padding: 20px; margin: 20px 0; }
  .info-row { display: flex; justify-content: space-between; margin-bottom: 10px; font-size: 14px; }
  .info-row:last-child { margin-bottom: 0; }
  .info-label { color: #6b7280; }
  .info-value { color: #111; font-weight: 600; }
  .amount { font-size: 22px; font-weight: 700; color: #ea580c; }
  .badge { display: inline-block; padding: 4px 12px; border-radius: 999px; font-size: 12px; font-weight: 700; text-transform: uppercase; }
  .badge-red    { background: #fee2e2; color: #dc2626; }
  .badge-orange { background: #ffedd5; color: #ea580c; }
  .badge-blue   { background: #dbeafe; color: #2563eb; }
  .note { font-size: 13px; color: #6b7280; margin: 20px 0 0; line-height: 1.6; }
  .footer { background: #f3f4f6; padding: 18px 32px; text-align: center; font-size: 12px; color: #9ca3af; }
</style>
</head>
<body>
<div class="wrap">
  <div class="header">
    <h1>Living<span style="color:#fdba74">Kost</span></h1>
    <p>Sistem Manajemen Kos</p>
  </div>
  <div class="body">
    <p class="greeting">Halo, {{ $invoice->lease->tenant->display_name ?? $invoice->lease->tenant->name }}</p>

    @php
      $today = now()->startOfDay();
      $typeLabel = match($reminderType) {
        'overdue'   => ['text' => 'Melewati Jatuh Tempo', 'class' => 'badge-red'],
        'due_today' => ['text' => 'Jatuh Tempo Hari Ini', 'class' => 'badge-orange'],
        default     => ['text' => 'Akan Jatuh Tempo', 'class' => 'badge-blue'],
      };
    @endphp

    <p style="font-size:14px;color:#374151;margin:0 0 4px;">
      Ini adalah pengingat untuk tagihan kos Anda:
    </p>
    <span class="badge {{ $typeLabel['class'] }}">{{ $typeLabel['text'] }}</span>

    <div class="info-box">
      <div class="info-row">
        <span class="info-label">No. Invoice</span>
        <span class="info-value">{{ $invoice->reference_number }}</span>
      </div>
      <div class="info-row">
        <span class="info-label">Periode</span>
        <span class="info-value">{{ \Carbon\Carbon::createFromFormat('Y-m', $invoice->month_year)->translatedFormat('F Y') }}</span>
      </div>
      <div class="info-row">
        <span class="info-label">Kamar</span>
        <span class="info-value">{{ $invoice->lease->room->room_number ?? '-' }}</span>
      </div>
      <div class="info-row">
        <span class="info-label">Jatuh Tempo</span>
        <span class="info-value">{{ $invoice->due_date->translatedFormat('d F Y') }}</span>
      </div>
      <div class="info-row" style="margin-top:12px;padding-top:12px;border-top:1px solid #fed7aa;">
        <span class="info-label" style="font-size:15px;">Total Tagihan</span>
        <span class="amount">Rp {{ number_format($invoice->amount, 0, ',', '.') }}</span>
      </div>
    </div>

    @if (!empty($bank['number']))
      <p style="font-size:14px;color:#374151;margin:24px 0 4px;font-weight:600;">Transfer ke rekening berikut:</p>
      <div class="info-box" style="background:#f0fdf4;border-color:#bbf7d0;">
        <div class="info-row">
          <span class="info-label">Bank</span>
          <span class="info-value">{{ $bank['name'] ?: '-' }}</span>
        </div>
        <div class="info-row">
          <span class="info-label">No. Rekening</span>
          <span class="info-value" style="letter-spacing:.5px;">{{ $bank['number'] }}</span>
        </div>
        <div class="info-row">
          <span class="info-label">Atas Nama</span>
          <span class="info-value">{{ $bank['holder'] ?: '-' }}</span>
        </div>
        <div class="info-row" style="margin-top:12px;padding-top:12px;border-top:1px solid #bbf7d0;">
          <span class="info-label" style="font-size:15px;">Jumlah Transfer</span>
          <span class="amount" style="color:#16a34a;">Rp {{ number_format($invoice->amount, 0, ',', '.') }}</span>
        </div>
      </div>
      @if (!empty($bank['instructions']))
        <p class="note" style="margin-top:0;">{{ $bank['instructions'] }}</p>
      @endif
    @endif

    @if ($reminderType === 'overdue')
      <p class="note">Pembayaran Anda telah melewati jatuh tempo. Mohon segera lakukan pembayaran untuk menghindari denda keterlambatan. Hubungi kami jika ada pertanyaan.</p>
    @elseif ($reminderType === 'due_today')
      <p class="note">Tagihan Anda jatuh tempo hari ini. Pastikan pembayaran sudah dilakukan sebelum akhir hari ini.</p>
    @else
      <p class="note">Tagihan Anda akan jatuh tempo dalam waktu dekat. Silakan lakukan pembayaran sebelum tanggal jatuh tempo.</p>
    @endif

    <p class="note" style="margin-top:16px;">Sudah melakukan pembayaran? Unggah bukti transfer Anda melalui tombol di bawah, lalu tunggu verifikasi pengelola.</p>
    <p style="text-align:center;margin:16px 0 4px;">
      <a href="https://www.livingkost.com/tenant/invoices"
         style="display:inline-block;background:#ea580c;color:#fff;text-decoration:none;padding:12px 28px;border-radius:10px;font-weight:700;font-size:14px;">
        Upload Bukti Pembayaran
      </a>
    </p>
  </div>
  <div class="footer">
    &copy; {{ date('Y') }} LivingKost &mdash; Email ini dikirim otomatis, jangan balas pesan ini.
  </div>
</div>
</body>
</html>
