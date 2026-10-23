@extends('layouts.app')
@section('title', 'Referral Sales - Living Kost')
@section('page-title', 'Referral Sales')
@section('content')
@include('referrals.styles')
<div class="rd">
<h1>Referral Sales</h1><p class="rd-muted">Pantau pendaftaran, DP, pelunasan sewa pertama, dan reward sales.</p>
@if(session('referral_notice'))<div class="rd-success" role="status">{{ session('referral_notice') }}</div>@endif
@if($errors->any())<div class="ref-errors" role="alert"><ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif
@include('referrals.metrics')
<div class="ref-panels">
    <section class="rd-card"><h2>Pengaturan reward</h2><form method="POST" action="{{ route('referrals.rate') }}">@csrf
        @forelse($roomTypes as $type)
            <div class="ref-field">
                <label for="reward-type-{{ $type->id }}">{{ $type->name }} <span class="rd-muted rd-small">· {{ $type->property_name }}</span></label>
                <p class="rd-help">Sewa Rp {{ number_format($type->price, 0, ',', '.') }}/bulan</p>
                <input id="reward-type-{{ $type->id }}" type="number" name="rates[{{ $type->id }}]" min="0" max="10000000" step="1" value="{{ old('rates.'.$type->id, $rates[$type->id] ?? 0) }}" aria-label="Reward {{ $type->name }} di {{ $type->property_name }}" required>
            </div>
        @empty<p class="rd-muted">Tambahkan tipe ruangan terlebih dahulu untuk mengatur reward.</p>@endforelse
        <p class="rd-help">Isi reward dalam rupiah untuk setiap tipe kamar. Angka 0 berarti tanpa reward. Daftar nominal disimpan saat calon penghuni mendaftar; reward mengikuti tipe kamar pada kontraknya. Perubahan hanya berlaku untuk pendaftaran baru.</p>
        @if($roomTypes->isNotEmpty())<button class="rd-button" type="submit">Simpan reward per tipe</button>@endif
    </form><p class="rd-help" style="margin-top:18px!important">Reward tercapai setelah invoice bulan awal kontrak berstatus lunas dan terverifikasi. DP hanya diperhitungkan jika terverifikasi; pembayaran invoice + DP harus menutup harga sewa bulan pertama. Bukti transfer atau DP saja belum menghasilkan reward.</p></section>
    <section class="rd-card"><h2>Tambah akun sales</h2><form method="POST" action="{{ route('referrals.create-sales') }}">@csrf
        <div class="ref-field"><label for="sales-name">Nama sales</label><input id="sales-name" name="name" value="{{ old('name') }}" maxlength="100" required></div>
        <div class="ref-field"><label for="sales-email">Email login</label><input id="sales-email" type="email" name="email" value="{{ old('email') }}" maxlength="255" required></div>
        <div class="ref-field"><label for="sales-phone">Nomor HP</label><input id="sales-phone" type="tel" name="phone" value="{{ old('phone') }}" maxlength="20" required></div>
        <div class="ref-field"><label for="sales-password">Password awal (minimal 12 karakter)</label><input id="sales-password" type="password" name="password" minlength="12" maxlength="128" autocomplete="new-password" required></div>
        <div class="ref-field"><label for="sales-confirm">Ulangi password</label><input id="sales-confirm" type="password" name="password_confirmation" minlength="12" maxlength="128" autocomplete="new-password" required></div>
        <button class="rd-button" type="submit">Buat akun sales</button><p class="rd-help">Sales login melalui halaman login yang sama. Akun ini hanya mengakses dashboard sales, bukan data KTP atau pengelolaan kos.</p>
    </form></section>
</div>
<section class="ref-section"><h2>Akun dan tautan sales</h2><div class="rd-table-wrap"><table class="rd-table ref-table"><thead><tr><th>Sales</th><th>Tautan pendaftaran</th><th>Akses</th></tr></thead><tbody>
@forelse($agents as $agent)<tr><td><strong>{{ $agent->name }}</strong><small>{{ $agent->email }}</small></td><td><div class="ref-code">{{ route('tenant.register', $registrationToken) }}?ref={{ $agent->code }}</div><button class="rd-button secondary" type="button" data-copy="{{ route('tenant.register', $registrationToken) }}?ref={{ $agent->code }}" style="font-size:12px;padding:6px 10px;margin-top:6px">Salin tautan</button></td><td><span class="ref-badge {{ $agent->active ? 'ref-green' : 'ref-red' }}">{{ $agent->active ? 'Aktif' : 'Nonaktif' }}</span><form method="POST" action="{{ route('referrals.toggle', $agent->id) }}">@csrf<button type="submit" class="rd-button secondary" style="font-size:12px;padding:6px 10px;margin-top:6px">{{ $agent->active ? 'Nonaktifkan' : 'Aktifkan' }}</button></form></td></tr>
@empty<tr><td colspan="3" class="rd-empty">Belum ada akun sales. Atur reward lalu buat akun sales untuk mulai.</td></tr>@endforelse
</tbody></table></div></section>
<section class="ref-section"><h2>Perjalanan referral dan reward</h2><p class="rd-help">Pendaftaran → DP terverifikasi → kontrak & invoice bulan pertama lunas → reward tercapai. Pembayaran reward dicatat setelah Anda membayarnya di luar aplikasi.</p>@include('referrals.leads', ['ownerView'=>true])</section>
</div>
@include('referrals.copy-script')
@endsection
