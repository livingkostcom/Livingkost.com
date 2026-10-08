<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="@yield('description')">
    <title>@yield('title') | Living Kost</title>
    <style>
        *{box-sizing:border-box}body{margin:0;background:#faf9f7;color:#1f2937;font:16px/1.8 system-ui,-apple-system,BlinkMacSystemFont,"Segoe UI",sans-serif}a{color:#d94b00;text-underline-offset:4px}a:hover{color:#a83a00}header{background:#fff;border-bottom:1px solid #e5e7eb}.nav{max-width:1120px;margin:auto;padding:22px 24px;display:flex;gap:20px;align-items:center;justify-content:space-between}.brand{font-size:25px;font-weight:800;color:#111827;text-decoration:none;letter-spacing:-1px}.brand span{color:#f65b05}.back{font-size:14px;font-weight:600}main{max-width:900px;margin:44px auto;padding:0 24px 44px}.eyebrow{font-size:13px;text-transform:uppercase;letter-spacing:1px;font-weight:700;color:#d94b00}h1{font-size:clamp(30px,5vw,42px);line-height:1.25;letter-spacing:-1px;margin:10px 0 16px}h2{font-size:22px;line-height:1.4;margin:32px 0 12px}p{margin:0 0 16px}ul,ol{padding-left:24px;margin:0 0 18px}li{margin-bottom:8px}.updated{font-size:14px;color:#6b7280;margin-bottom:28px}.content{padding:32px;background:white;border:1px solid #e5e7eb;border-radius:20px}.callout{padding:20px 22px;border-radius:12px;background:#fff6ec;border:1px solid #fed7aa;margin:24px 0}.button{display:inline-block;padding:12px 18px;border-radius:10px;background:#f65b05;color:#fff;text-decoration:none;font-weight:700}.button:hover{background:#d94b00;color:#fff}.muted{font-size:14px;color:#6b7280}footer{padding:30px 24px;background:#111827;color:#9ca3af}footer .inner{max-width:1120px;margin:auto;display:flex;gap:20px;justify-content:space-between;flex-wrap:wrap}footer a{color:#d1d5db}footer a:hover{color:white}.links{display:flex;gap:20px;flex-wrap:wrap}@media(max-width:600px){main{margin-top:28px;padding:0 16px 30px}.nav{padding:18px 16px}.content{padding:22px 20px}h2{font-size:20px}.links{gap:12px}}
    </style>
</head>
<body>
<header><nav class="nav" aria-label="Navigasi utama"><a class="brand" href="/"><span>Living</span>Kost</a><a class="back" href="/">← Kembali ke beranda</a></nav></header>
<main>
    <div class="eyebrow">Informasi pengguna</div>
    <h1>@yield('title')</h1>
    <p class="updated">Terakhir diperbarui: 8 Oktober 2026</p>
    <article class="content">@yield('content')</article>
</main>
<footer><div class="inner"><span>© {{ date('Y') }} Living Kost</span><nav class="links" aria-label="Kebijakan pengguna"><a href="{{ route('legal.privacy') }}">Kebijakan Privasi</a><a href="{{ route('legal.deletion') }}">Kebijakan Penghapusan Data Pengguna</a></nav></div></footer>
</body>
</html>
