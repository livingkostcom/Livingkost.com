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
  .info-value { color: #111; font-weight: 600; text-align: right; }
  .desc { background: #f9fafb; border: 1px solid #e5e7eb; border-radius: 10px; padding: 14px 16px; font-size: 14px; color: #374151; line-height: 1.6; margin: 16px 0; white-space: pre-wrap; }
  .badge { display: inline-block; padding: 4px 12px; border-radius: 999px; font-size: 12px; font-weight: 700; text-transform: uppercase; }
  .badge-red    { background: #fee2e2; color: #dc2626; }
  .badge-orange { background: #ffedd5; color: #ea580c; }
  .badge-blue   { background: #dbeafe; color: #2563eb; }
  .note { font-size: 13px; color: #6b7280; margin: 20px 0 0; line-height: 1.6; }
  .footer { background: #f3f4f6; padding: 18px 32px; text-align: center; font-size: 12px; color: #9ca3af; }
</style>
</head>
<body>
@php
  $priLabel = match($request->priority) {
    'high'   => ['text' => 'Prioritas Tinggi', 'class' => 'badge-red'],
    'medium' => ['text' => 'Prioritas Sedang', 'class' => 'badge-orange'],
    default  => ['text' => 'Prioritas Rendah', 'class' => 'badge-blue'],
  };
  $catLabel = match($request->category) {
    'electrical' => 'Listrik',
    'plumbing'   => 'Saluran Air',
    'furniture'  => 'Perabot',
    'cleaning'   => 'Kebersihan',
    default      => 'Lainnya',
  };
@endphp
<div class="wrap">
  <div class="header">
    <h1>Living<span style="color:#fdba74">Kost</span></h1>
    <p>Sistem Manajemen Kos</p>
  </div>
  <div class="body">
    <p class="greeting">Permintaan Perbaikan Baru</p>

    <p style="font-size:14px;color:#374151;margin:0 0 12px;">
      Seorang penghuni mengajukan permintaan perbaikan. Berikut detailnya:
    </p>
    <span class="badge {{ $priLabel['class'] }}">{{ $priLabel['text'] }}</span>

    <div class="info-box">
      <div class="info-row">
        <span class="info-label">Judul</span>
        <span class="info-value">{{ $request->title }}</span>
      </div>
      <div class="info-row">
        <span class="info-label">Penghuni</span>
        <span class="info-value">{{ $request->tenant?->display_name ?? '-' }}</span>
      </div>
      <div class="info-row">
        <span class="info-label">Kamar</span>
        <span class="info-value">{{ $request->room?->room_number ?? '-' }}</span>
      </div>
      <div class="info-row">
        <span class="info-label">Kategori</span>
        <span class="info-value">{{ $catLabel }}</span>
      </div>
      <div class="info-row">
        <span class="info-label">Tanggal</span>
        <span class="info-value">{{ $request->created_at?->translatedFormat('d F Y, H:i') }}</span>
      </div>
    </div>

    <p style="font-size:13px;color:#6b7280;margin:0 0 4px;font-weight:600;">Deskripsi:</p>
    <div class="desc">{{ $request->description }}</div>

    <p class="note">Silakan masuk ke dashboard Living Kost untuk menindaklanjuti permintaan ini.</p>
  </div>
  <div class="footer">
    &copy; {{ date('Y') }} LivingKost &mdash; Email ini dikirim otomatis, jangan balas pesan ini.
  </div>
</div>
</body>
</html>
