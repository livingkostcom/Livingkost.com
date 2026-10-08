@extends('legal.layout')
@section('title', 'Kebijakan Penghapusan Data Pengguna')
@section('description', 'Cara meminta penghapusan akun dan data pribadi di Living Kost, verifikasi permintaan, serta penjelasan data yang masih perlu disimpan.')
@section('content')
<p>Pengguna Living Kost dapat mengajukan permintaan penghapusan akun atau data pribadinya melalui admin. Halaman ini menjelaskan langkah pengajuan, pemeriksaan permintaan, serta kondisi penyimpanan data yang masih diperlukan.</p>
<div class="callout">
    <strong>Ajukan permintaan melalui WhatsApp admin</strong>
    <p style="margin-top:8px">Hubungi <a href="https://wa.me/6285161180441">+62 851-6118-0441</a> dengan judul pesan <strong>“Permintaan Penghapusan Data Pengguna”</strong>.</p>
    <a class="button" href="https://wa.me/6285161180441?text=Halo%20admin%20Living%20Kost.%20Saya%20ingin%20mengajukan%20Permintaan%20Penghapusan%20Data%20Pengguna.%20Mohon%20informasi%20langkah%20verifikasinya.">Ajukan penghapusan data</a>
</div>
<h2>1. Cara mengajukan permintaan</h2>
<ol>
    <li>Hubungi admin melalui nomor WhatsApp di atas, atau sampaikan permintaan kepada owner kos yang mengelola data Anda.</li>
    <li>Sebutkan nama, nomor HP atau email yang terdaftar, nama kos, dan apakah Anda meminta penghapusan akun, data tertentu, atau seluruh data yang dapat dihapus.</li>
    <li>Admin akan memverifikasi bahwa permintaan berasal dari pemilik data. Sebisa mungkin gunakan nomor HP yang sudah terdaftar.</li>
    <li>Setelah verifikasi, admin meninjau data yang dapat dihapus dan data yang masih perlu disimpan, kemudian menginformasikan tindak lanjut dan status permintaan.</li>
</ol>
<p class="muted">Jangan mengirim password, OTP, atau foto KTP tambahan dalam pesan awal. Jika verifikasi tambahan diperlukan, admin akan menjelaskan informasi minimum yang dibutuhkan.</p>
<h2>2. Data yang dapat dimintakan penghapusan</h2>
<p>Permintaan dapat mencakup akun login, informasi profil dan kontak, NIK dan foto KTP, data pendaftaran, serta lampiran atau data layanan lain yang tidak lagi diperlukan. Penghapusan dilakukan setelah pemeriksaan kepemilikan data dan kebutuhan penyimpanan yang masih berlaku.</p>
<h2>3. Data yang masih perlu disimpan</h2>
<p>Sebagian catatan dapat tetap diperlukan untuk administrasi kontrak, pencatatan dan pembuktian pembayaran, penyelesaian kewajiban atau sengketa, keamanan, maupun kewajiban hukum. Jika ada data yang belum dapat dihapus, admin akan menjelaskan alasannya dan tindak lanjutnya. Kewajiban yang masih berlaku bukan alasan untuk menyimpan semua data tanpa pemeriksaan.</p>
<p>Permintaan penghapusan akun tidak secara otomatis membatalkan kontrak sewa, menghapus tagihan, atau mengubah hak dan kewajiban yang masih berlaku.</p>
<h2>4. Proses, cadangan, dan layanan pihak ketiga</h2>
<p>Pengajuan melalui WhatsApp tidak langsung menghapus data secara otomatis. Proses ditangani admin setelah verifikasi. Waktu penyelesaian bergantung pada jenis data, kebutuhan verifikasi, dan ketentuan yang berlaku; admin akan memberikan informasi tindak lanjut dan konfirmasi hasilnya.</p>
<p>Salinan cadangan dapat tetap tersimpan sampai siklus penghapusan atau pembaruan cadangan dilakukan. Data yang sudah diproses oleh penyedia pembayaran, email, atau WhatsApp dapat tunduk pada kebijakan dan kewajiban penyimpanan penyedia tersebut.</p>
<h2>5. Dampak penghapusan dan kontak</h2>
<p>Penghapusan akun dapat menghentikan akses Anda ke layanan penghuni dan riwayat yang terkait dengan akun tersebut. Sampaikan pertanyaan atau permintaan tindak lanjut kepada admin Living Kost melalui <a href="https://wa.me/6285161180441">+62 851-6118-0441</a>.</p>
<p>Informasi penggunaan data lainnya tersedia pada <a href="{{ route('legal.privacy') }}">Kebijakan Privasi</a>.</p>
@endsection
