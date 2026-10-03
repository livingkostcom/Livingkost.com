<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
    <meta name="robots" content="noindex,nofollow,noarchive">
    <title>Dokumen Penghuni Aktif | Living Kost</title>
    @include('resident-directory.styles')
</head>
<body class="rd" style="margin:0;background:#faf9f7;min-height:100vh">
<div class="rd-shell">
    <header class="rd-top">
        <div class="rd-brand"><span>Living</span>Kost</div>
        @if($opened)
            <form method="POST" action="{{ route('resident-directory.close', $token) }}">@csrf<button class="rd-button secondary" type="submit">Tutup dokumen</button></form>
        @else
            <span class="rd-muted rd-small">Dokumen penghuni</span>
        @endif
    </header>
    @if(!$opened)
        <main class="rd-gate rd-card">
            <div class="rd-lock"><svg width="28" height="28" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24" aria-hidden="true"><rect x="5" y="10" width="14" height="11" rx="3"/><path d="M8 10V7a4 4 0 018 0v3m-4 5v2"/></svg></div>
            <h1>Penghuni Aktif</h1>
            <p class="rd-muted">Masukkan nomor HP yang sudah didaftarkan oleh owner untuk membuka dokumen.</p>
            <form method="POST" action="{{ route('resident-directory.unlock', $token) }}">
                @csrf
                <label for="phone">Nomor handphone</label>
                <input id="phone" name="phone" type="tel" inputmode="tel" autocomplete="tel" placeholder="Contoh: 081234567890" maxlength="30" required value="{{ old('phone') }}">
                @error('phone')<div class="rd-error" role="alert">{{ $message }}</div>@enderror
                <button class="rd-button" type="submit" style="width:100%;margin-top:20px">Buka dokumen</button>
            </form>
            <p class="rd-help" style="margin-top:18px!important">Belum mendapat akses? Hubungi owner kos untuk mendaftarkan nomor HP kamu.</p>
        </main>
    @else
        <main>
            <div class="rd-top">
                <div><h1>Daftar Penghuni Aktif</h1><p class="rd-muted" style="margin:0">Penghuni dengan kontrak sewa aktif yang sudah dimulai.</p></div>
                <span class="rd-stat">{{ $residents->count() }} kontrak aktif</span>
            </div>
            <div class="rd-tools">
                <input id="resident-search" type="search" aria-label="Cari nama atau nomor kamar" placeholder="Cari nama atau nomor kamar…">
                <select id="resident-property" aria-label="Filter kos"><option value="">Semua kos</option>@foreach($residents->pluck('property_name')->unique() as $property)<option value="{{ $property }}">{{ $property }}</option>@endforeach</select>
            </div>
            <div class="rd-table-wrap">
                <table class="rd-table">
                    <thead><tr><th scope="col">Tanggal masuk</th><th scope="col">Nama penghuni</th><th scope="col">No. HP</th><th scope="col">NIK</th><th scope="col">Foto KTP</th><th scope="col">No. kamar</th><th scope="col">Kos</th></tr></thead>
                    <tbody>
                    @forelse($residents as $resident)
                        <tr data-resident data-name="{{ $resident->name }}" data-room="{{ $resident->room_number }}" data-property="{{ $resident->property_name }}">
                            <td>{{ \Illuminate\Support\Carbon::parse($resident->start_date)->format('d/m/Y') }}</td>
                            <td><strong>{{ $resident->name }}</strong></td>
                            <td>{{ $resident->phone ?: '—' }}</td><td>{{ $resident->nik ?: '—' }}</td>
                            <td>@if($resident->ktp_photo)<a href="{{ route('resident-directory.photo', [$token, $resident->lease_id]) }}" target="_blank" rel="noopener noreferrer"><img class="rd-photo" src="{{ route('resident-directory.photo', [$token, $resident->lease_id]) }}" loading="lazy" alt="Foto KTP {{ $resident->name }}"><span class="rd-small">Lihat foto KTP ↗</span></a>@else<span class="rd-muted">Belum tersedia</span>@endif</td>
                            <td class="rd-room">{{ $resident->room_number }}</td><td>{{ $resident->property_name }}</td>
                        </tr>
                    @empty
                        <tr><td class="rd-empty" colspan="7">Belum ada penghuni aktif yang sudah mulai tinggal.</td></tr>
                    @endforelse
                    @if($residents->isNotEmpty())<tr id="resident-no-results" class="rd-hidden"><td colspan="7" class="rd-empty">Tidak ada penghuni yang sesuai pencarian.</td></tr>@endif
                    </tbody>
                </table>
            </div>
            <p class="rd-foot">Tanggal masuk mengikuti awal kontrak sewa. Dokumen ini berisi data pribadi; gunakan hanya sesuai izin owner. Akses berakhir setelah 30 menit.</p>
        </main>
        <script>
            const search = document.getElementById('resident-search');
            const property = document.getElementById('resident-property');
            function filterResidents() {
                const query = search.value.trim().toLocaleLowerCase('id'); let count = 0;
                document.querySelectorAll('[data-resident]').forEach(row => {
                    const match = (!property.value || row.dataset.property === property.value) && (row.dataset.name + ' ' + row.dataset.room).toLocaleLowerCase('id').includes(query);
                    row.classList.toggle('rd-hidden', !match); if (match) count++;
                });
                document.getElementById('resident-no-results')?.classList.toggle('rd-hidden', count !== 0);
            }
            search.addEventListener('input', filterResidents); property.addEventListener('change', filterResidents);
        </script>
    @endif
</div>
</body>
</html>
