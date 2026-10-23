<!DOCTYPE html><html lang="id"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><meta name="robots" content="noindex,nofollow"><title>Dashboard Sales | Living Kost</title>@include('referrals.styles')</head>
<body class="rd" style="margin:0;background:#faf9f7">
<header class="ref-top"><div><div class="rd-brand"><span>Living</span>Kost <small class="rd-muted" style="font-size:13px;letter-spacing:0">Sales</small></div><form method="POST" action="{{ route('referrals.logout') }}">@csrf<button class="rd-button secondary" type="submit">Keluar</button></form></div></header>
<main class="rd-shell">
<div class="rd-top"><div><h1>Halo, {{ Auth::user()->name }} 👋</h1><p class="rd-muted" style="margin:0">Pantau calon penghuni dan hasil referral kamu.</p></div><span class="rd-stat">Reward baru: Rp {{ number_format($rate, 0, ',', '.') }} / penghuni</span></div>
@include('referrals.metrics')
<section class="rd-card"><h2>Tautan pendaftaran kamu</h2><p class="rd-muted">Kirim tautan ini ke calon penghuni. Pendaftaran yang dikirim melalui tautan ini akan tercatat sebagai referral kamu.</p><div class="rd-link">{{ $shareUrl }}</div><button class="rd-button" type="button" data-copy="{{ $shareUrl }}">Salin tautan referral</button><p class="rd-help">Reward tercapai sesudah sewa bulan pertama lunas dan terverifikasi. DP saja belum menghasilkan reward. Nominal mengikuti ketentuan saat calon penghuni mendaftar.</p></section>
<section class="ref-section"><h2>Referral kamu</h2>@include('referrals.leads', ['ownerView'=>false])</section>
<section class="ref-section"><div class="rd-top"><div><h2>Kamar dan harga sewa</h2><p class="rd-muted">Status mengikuti kontrak aktif dan kondisi kamar saat halaman dimuat.</p></div></div>
<div class="rd-tools"><input id="sales-room-search" type="search" placeholder="Cari kos, tipe, atau nomor kamar…" aria-label="Cari kamar"><select id="sales-room-status" aria-label="Status kamar"><option value="">Semua status</option><option value="available">Tersedia</option><option value="occupied">Terisi</option><option value="maintenance">Maintenance</option></select></div>
<div class="rd-table-wrap"><table class="rd-table ref-table"><thead><tr><th>Kos</th><th>Kamar</th><th>Tipe</th><th>Harga / bulan</th><th>Status</th></tr></thead><tbody>
@forelse($rooms as $room)<tr data-room-row data-status="{{ $room->availability }}" data-search="{{ $room->property_name }} {{ $room->room_number }} {{ $room->type_name }}"><td>{{ $room->property_name }}</td><td><strong>{{ $room->room_number }}</strong><small>Lantai {{ $room->floor }}</small></td><td>{{ $room->type_name }}</td><td>Rp {{ number_format($room->price, 0, ',', '.') }}</td><td><span class="ref-badge {{ $room->availability === 'available' ? 'ref-green' : ($room->availability === 'occupied' ? 'ref-red' : 'ref-orange') }}">{{ ['available'=>'Tersedia','occupied'=>'Terisi','maintenance'=>'Maintenance'][$room->availability] }}</span></td></tr>
@empty<tr><td class="rd-empty" colspan="5">Belum ada kamar untuk ditampilkan.</td></tr>@endforelse
</tbody></table></div></section>
<p class="rd-foot">Data KTP dan NIK penghuni tidak ditampilkan pada akun sales. Pembayaran reward dilakukan oleh owner.</p>
</main>@include('referrals.copy-script')
<script>
const search=document.getElementById('sales-room-search'), statusFilter=document.getElementById('sales-room-status');
function filterRooms(){document.querySelectorAll('[data-room-row]').forEach(row=>row.classList.toggle('rd-hidden', !row.dataset.search.toLocaleLowerCase('id').includes(search.value.trim().toLocaleLowerCase('id')) || (statusFilter.value && row.dataset.status!==statusFilter.value)));}
search.addEventListener('input',filterRooms);statusFilter.addEventListener('change',filterRooms);
</script></body></html>
