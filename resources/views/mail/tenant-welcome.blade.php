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
  .info-row { margin-bottom: 14px; font-size: 14px; }
  .info-row:last-child { margin-bottom: 0; }
  .info-label { color: #6b7280; display: block; margin-bottom: 2px; }
  .info-value { color: #111; font-weight: 700; font-size: 16px; letter-spacing: .3px; }
  .btn { display: inline-block; background: #ea580c; color: #fff; text-decoration: none; padding: 12px 28px; border-radius: 10px; font-weight: 700; font-size: 14px; }
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
    <p class="greeting">Selamat datang, {{ $tenantName }}!</p>

    <p style="font-size:14px;color:#374151;margin:0 0 4px;">
      Akun login Anda telah berhasil dibuat. Gunakan informasi berikut untuk masuk ke portal penghuni Living Kost:
    </p>

    <div class="info-box">
      <div class="info-row">
        <span class="info-label">Email</span>
        <span class="info-value">{{ $loginEmail }}</span>
      </div>
      <div class="info-row">
        <span class="info-label">Password</span>
        <span class="info-value">{{ $plainPassword }}</span>
      </div>
    </div>

    <p style="text-align:center;margin:24px 0;">
      <a href="{{ $loginUrl }}" class="btn">Masuk ke Portal Penghuni</a>
    </p>

    <p class="note">
      Demi keamanan, segera ganti password Anda setelah berhasil login pertama kali.
      Jangan bagikan password ini kepada siapa pun.
    </p>
  </div>
  <div class="footer">
    &copy; {{ date('Y') }} LivingKost &mdash; Email ini dikirim otomatis, jangan balas pesan ini.
  </div>
</div>
</body>
</html>
