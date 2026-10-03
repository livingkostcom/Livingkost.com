@extends('layouts.app')
@section('title', 'Dokumen Penghuni - Living Kost')
@section('page-title', 'Dokumen Penghuni')
@section('content')
@include('resident-directory.styles')
<div class="rd" style="max-width:900px;margin:auto">
    <h1>Dokumen Penghuni Aktif</h1>
    <p class="rd-muted">Bagikan data penghuni kos kepada orang yang kamu izinkan.</p>
    @if(session('directory_saved'))<div class="rd-success" role="status">{{ session('directory_saved') }}</div>@endif
    <div class="rd-card">
        <h2>Nomor HP pembuka dokumen</h2>
        <p class="rd-muted">Daftarkan nomor HP yang boleh melihat tanggal masuk, nama, nomor HP, NIK, foto KTP, dan nomor kamar penghuni aktif.</p>
        <form method="POST" action="{{ route('resident-directory.save') }}">
            @csrf
            <label for="directory-phones">Nomor HP yang diizinkan</label>
            <textarea id="directory-phones" name="phones" rows="5" maxlength="2000" placeholder="081234567890&#10;081298765432">{{ old('phones', $phones) }}</textarea>
            <p class="rd-help">Satu nomor per baris. Format 08…, 628…, dan +628… diterima. Hapus nomor dari daftar untuk mencabut akses; kosongkan daftar untuk menutup akses semua orang.</p>
            @error('phones')<div class="rd-error" role="alert">{{ $message }}</div>@enderror
            <p class="rd-help">Akses tanpa OTP: siapa pun yang mengetahui nomor terdaftar dan tautan ini dapat membuka data. Perubahan daftar akan menutup sesi sebelumnya.</p>
            <button class="rd-button" type="submit" style="margin-top:16px">Simpan nomor pembuka</button>
        </form>
    </div>
    <div class="rd-card">
        <h2>Tautan dokumen</h2>
        @if($shareUrl)
            <a class="rd-link" href="{{ $shareUrl }}" target="_blank" rel="noopener noreferrer">{{ $shareUrl }}</a>
            <div class="rd-actions">
                <button class="rd-button secondary" type="button" id="copy-directory-link">Salin tautan</button>
                <a class="rd-button" href="{{ $shareUrl }}" target="_blank" rel="noopener noreferrer">Buka halaman</a>
            </div>
            <p class="rd-help">Penerima membuka tautan lalu memasukkan nomor HP terdaftar. Sesi akses berlaku 30 menit. Data mengikuti kontrak sewa aktif yang sudah dimulai.</p>
            <script>
                document.getElementById('copy-directory-link').addEventListener('click', async function () {
                    try { await navigator.clipboard.writeText(@js($shareUrl)); this.textContent = 'Tautan tersalin ✓'; }
                    catch (_) { this.textContent = 'Salin tautan di atas secara manual'; }
                });
            </script>
        @else
            <p class="rd-muted">Simpan nomor pembuka terlebih dahulu untuk membuat tautan dokumen.</p>
        @endif
    </div>
</div>
@endsection
