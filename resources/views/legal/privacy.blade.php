@extends('legal.layout')
@section('title', 'Kebijakan Privasi')
@section('description', 'Informasi mengenai data pribadi yang digunakan Living Kost, tujuan penggunaan, akses, dan cara mengajukan permintaan terkait data Anda.')
@section('content')
<p>Kebijakan ini menjelaskan penggunaan data pribadi dalam layanan Living Kost di <strong>livingkost.com</strong>, termasuk pendaftaran calon penghuni, pengelolaan sewa, pembayaran, dan layanan penghuni. Untuk pertanyaan mengenai data Anda, hubungi admin Living Kost atau owner kos yang mengelola tempat tinggal Anda.</p>
<h2>1. Data yang digunakan</h2>
<ul>
    <li><strong>Identitas dan kontak:</strong> nama, alamat email, nomor handphone, NIK, foto KTP, dan kontak darurat yang diberikan saat pendaftaran atau pengelolaan penghuni.</li>
    <li><strong>Akun dan sewa:</strong> informasi akun, kos dan nomor kamar, tanggal masuk, kontrak sewa, serta status penghuni.</li>
    <li><strong>Pembayaran:</strong> invoice, nominal pembayaran dan deposit, status transaksi, referensi transaksi, serta bukti pembayaran yang diunggah.</li>
    <li><strong>Layanan penghuni:</strong> pengaduan, permintaan perbaikan, lampiran, dan komunikasi dengan pengelola.</li>
    <li><strong>Data teknis:</strong> sesi login, cookie yang diperlukan agar layanan berfungsi, serta catatan akses atau kesalahan pada server.</li>
</ul>
<h2>2. Tujuan penggunaan</h2>
<p>Data digunakan untuk memproses pendaftaran dan identitas penghuni, menyediakan akun, mengelola kamar dan kontrak, mencatat serta memverifikasi pembayaran, menangani permintaan layanan, mengirim pemberitahuan, dan menjaga keamanan layanan. Pemrosesan dilakukan sesuai kebutuhan layanan, persetujuan yang relevan, dan kewajiban yang berlaku.</p>
<h2>3. Pihak yang dapat menerima atau mengakses data</h2>
<ul>
    <li>Owner dan petugas pengelola yang memiliki akses sesuai perannya untuk menjalankan layanan kos.</li>
    <li>Penerima dokumen penghuni yang diberi akses oleh owner. Fitur dokumen penghuni menggunakan tautan dan nomor HP yang didaftarkan owner, tanpa OTP. Orang yang mengetahui keduanya dapat membuka dokumen; owner dapat mencabut akses tersebut.</li>
    <li>Penyedia layanan yang digunakan untuk operasional, termasuk hosting, email, Fonnte/WhatsApp untuk pemberitahuan, dan DOKU untuk pembayaran online. Data yang diteruskan mengikuti kebutuhan layanan tersebut.</li>
    <li>Pihak yang berwenang apabila terdapat kewajiban atau permintaan yang sah sesuai hukum.</li>
</ul>
<p>Penggunaan layanan pihak ketiga juga tunduk pada kebijakan privasi penyedia masing-masing.</p>
<h2>4. Penyimpanan dan perlindungan</h2>
<p>Data disimpan selama diperlukan untuk layanan, administrasi sewa dan pembayaran, penanganan sengketa, atau kewajiban hukum. Penghapusan diproses berdasarkan jenis data dan kebutuhan penyimpanan yang masih berlaku. Salinan dalam cadangan dapat tetap ada sampai siklus pembaruan atau penghapusan cadangan dilakukan.</p>
<p>Layanan menggunakan pemeriksaan akses sesuai fitur dan peran pengguna. Jangan membagikan password, tautan dokumen penghuni, atau nomor pembuka dokumen kepada orang yang tidak berkepentingan.</p>
<h2>5. Cookie dan tautan eksternal</h2>
<p>Cookie sesi membantu mempertahankan login dan menjalankan fitur website. Anda dapat mengatur cookie melalui browser, tetapi pembatasannya dapat menyebabkan sebagian fitur tidak berfungsi. Tautan ke WhatsApp, peta, pembayaran, atau layanan lain membawa Anda ke layanan pihak ketiga.</p>
<h2>6. Permintaan terkait data pribadi</h2>
<p>Anda dapat menghubungi admin untuk meminta informasi atau akses atas data Anda, perbaikan data yang tidak akurat, penarikan persetujuan yang relevan, atau penghapusan data sesuai ketentuan yang berlaku. Kami dapat meminta verifikasi kepemilikan akun sebelum menindaklanjuti permintaan.</p>
<p>Langkah penghapusan dijelaskan pada <a href="{{ route('legal.deletion') }}">Kebijakan Penghapusan Data Pengguna</a>. Rujukan mengenai hak atas data pribadi tersedia dalam <a href="https://peraturan.bpk.go.id/Details/229798/uu-no-27-" target="_blank" rel="noopener noreferrer">UU Nomor 27 Tahun 2022 tentang Pelindungan Data Pribadi</a>.</p>
<h2>7. Kontak dan perubahan kebijakan</h2>
<p>Hubungi admin Living Kost melalui WhatsApp <a href="https://wa.me/6285161180441">+62 851-6118-0441</a> atau owner kos Anda untuk pertanyaan mengenai kebijakan ini. Jika kebijakan diperbarui, versi terbaru dan tanggal pembaruannya akan ditampilkan pada halaman ini.</p>
@endsection
