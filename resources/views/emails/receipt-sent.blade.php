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
  .info-box { background: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 12px; padding: 20px; margin: 20px 0; }
  .info-row { display: flex; justify-content: space-between; margin-bottom: 10px; font-size: 14px; }
  .info-row:last-child { margin-bottom: 0; }
  .info-label { color: #6b7280; }
  .info-value { color: #111; font-weight: 600; text-align: right; }
  .amount { font-size: 22px; font-weight: 700; color: #16a34a; }
  .badge { display: inline-block; padding: 4px 12px; border-radius: 999px; font-size: 12px; font-weight: 700; text-transform: uppercase; background: #dcfce7; color: #16a34a; }
  .attach { background: #fff7ed; border: 1px solid #fed7aa; border-radius: 12px; padding: 16px; margin: 20px 0; font-size: 13px; color: #9a3412; }
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
    <p class="greeting">Halo, {{ $tenant->display_name ?? $tenant->name }}</p>

    <p style="font-size:14px;color:#374151;margin:0 0 12px;">
      Terima kasih atas pembayaran Anda. Kami dengan senang hati mengonfirmasi bahwa pembayaran Anda telah <strong>diterima dan diverifikasi</strong>.
    </p>
    <span class="badge">Terbayar / Paid</span>

    <div class="info-box">
      <div class="info-row">
        <span class="info-label">No. Receipt</span>
        <span class="info-value">{{ $receipt->receipt_number }}</span>
      </div>
      <div class="info-row">
        <span class="info-label">No. Invoice</span>
        <span class="info-value">{{ $receipt->invoice->reference_number }}</span>
      </div>
      <div class="info-row">
        <span class="info-label">Periode</span>
        <span class="info-value">{{ \Carbon\Carbon::createFromFormat('Y-m', $receipt->invoice->month_year)->translatedFormat('F Y') }}</span>
      </div>
      <div class="info-row">
        <span class="info-label">Kamar</span>
        <span class="info-value">{{ $receipt->invoice->lease->room->room_number ?? '-' }}{{ $receipt->invoice->lease->room->roomType?->name ? ' · ' . $receipt->invoice->lease->room->roomType->name : '' }}</span>
      </div>
      <div class="info-row">
        <span class="info-label">Tanggal Verifikasi</span>
        <span class="info-value">{{ $receipt->invoice->verified_at?->translatedFormat('d F Y H:i') }}</span>
      </div>
      <div class="info-row" style="margin-top:12px;padding-top:12px;border-top:1px solid #bbf7d0;">
        <span class="info-label" style="font-size:15px;">Jumlah Pembayaran</span>
        <span class="amount">Rp {{ number_format($receipt->invoice->amount, 0, ',', '.') }}</span>
      </div>
    </div>

    <div class="attach">
      📎 Receipt (bukti pembayaran) terlampir pada email ini sebagai file PDF: <strong>{{ $receipt->receipt_number }}.pdf</strong>. Silakan unduh dan simpan untuk referensi Anda.
    </div>

    <p class="note">Jika ada pertanyaan tentang pembayaran ini, silakan hubungi pengelola kos Anda.</p>
  </div>
  <div class="footer">
    &copy; {{ date('Y') }} LivingKost &mdash; Email ini dikirim otomatis, jangan balas pesan ini.
  </div>
</div>
</body>
</html>
