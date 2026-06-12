# Panduan Pengguna Fluty Kos

> Panduan lengkap penggunaan aplikasi **Fluty Kos** — Sistem Manajemen Kos-Kosan.  
> Dokumen ini ditujukan untuk semua pengguna: **Pemilik (Owner)**, **Pengelola (Manager)**, dan **Penyewa (Tenant)**.

---

## Daftar Isi

- [Memulai Aplikasi](#memulai-aplikasi)
    - [Login](#login)
    - [Navigasi Utama](#navigasi-utama)
    - [Notifikasi](#notifikasi)
    - [Logout](#logout)
- [Panduan untuk Pemilik & Pengelola](#panduan-untuk-pemilik--pengelola)
    - [Dashboard](#dashboard)
    - [Kelola Properti](#kelola-properti)
    - [Kelola Tipe Ruangan](#kelola-tipe-ruangan)
    - [Kelola Ruangan](#kelola-ruangan)
    - [Kelola Penyewa](#kelola-penyewa)
    - [Kelola Kontrak Sewa](#kelola-kontrak-sewa)
    - [Kelola Invoice](#kelola-invoice)
    - [Verifikasi Pembayaran](#verifikasi-pembayaran)
    - [Analisis Pendapatan](#analisis-pendapatan)
    - [Kelola Permintaan Perbaikan](#kelola-permintaan-perbaikan)
    - [Kelola Pengeluaran](#kelola-pengeluaran)
    - [Kelola Pengumuman](#kelola-pengumuman)
    - [Laporan](#laporan)
    - [Pengaturan Sistem](#pengaturan-sistem)
- [Panduan untuk Penyewa](#panduan-untuk-penyewa)
    - [Dashboard Penyewa](#dashboard-penyewa)
    - [Invoices Saya](#invoices-saya)
    - [Permintaan Perbaikan](#permintaan-perbaikan)
    - [Pengumuman](#pengumuman)

---

## Memulai Aplikasi

### Login

Untuk masuk ke aplikasi, buka halaman login di browser Anda.

**Langkah-langkah:**

1. Buka aplikasi Fluty Kos melalui browser
2. Anda akan melihat halaman login dengan logo **Fluty Kos** dan tagline _"Sistem Manajemen Kos"_
3. Isi kolom berikut:
    - **Email Address** — Masukkan email yang terdaftar (contoh: `nama@email.com`)
    - **Password** — Masukkan kata sandi Anda (minimal 8 karakter)
4. (Opsional) Centang **"Remember me"** agar tidak perlu login ulang setiap kali
5. Klik tombol **"Masuk"**
6. Jika berhasil, Anda akan diarahkan ke halaman Dashboard
7. Jika gagal, akan muncul pesan error: _"Email atau password tidak sesuai."_

> **Catatan:** Akun pengguna dibuat oleh pemilik/pengelola kos. Jika belum punya akun, hubungi pengelola kos Anda.

---

### Navigasi Utama

Setelah login, Anda akan melihat sidebar (panel navigasi) di sebelah kiri layar.

**Di Desktop:**

- Sidebar selalu terlihat di sisi kiri
- Klik nama menu untuk berpindah halaman

**Di HP / Tablet:**

- Sidebar tersembunyi secara default
- Ketuk ikon **☰** (hamburger menu) di pojok kiri atas untuk membuka sidebar
- Ketuk menu yang diinginkan
- Sidebar akan otomatis tertutup setelah Anda memilih menu

**Daftar Menu yang Tersedia:**

| No  | Menu                  | Terlihat Oleh          | Keterangan                    |
| --- | --------------------- | ---------------------- | ----------------------------- |
| 1   | Dashboard             | Semua                  | Halaman utama/ringkasan       |
| 2   | Properti              | Owner, Manager         | Kelola gedung/bangunan kos    |
| 3   | Tipe Ruangan          | Owner, Manager         | Kelola jenis kamar & harga    |
| 4   | Ruangan               | Owner, Manager         | Kelola unit kamar             |
| 5   | Penyewa               | Owner, Manager         | Kelola data penghuni          |
| 6   | Kontrak Sewa          | Owner, Manager         | Kelola perjanjian sewa        |
| 7   | Invoice               | Owner, Manager, Tenant | Tagihan bulanan               |
| 8   | Verifikasi Pembayaran | Owner, Manager         | Cek bukti bayar               |
| 9   | Analisis Pendapatan   | Owner, Manager         | Grafik & statistik pendapatan |
| 10  | Perbaikan             | Owner, Manager, Tenant | Laporan kerusakan kamar       |
| 11  | Laporan               | Owner, Manager         | Rekap & laporan lengkap       |
| 12  | Pengeluaran           | Owner, Manager         | Catat biaya operasional       |
| 13  | Pengumuman            | Owner, Manager, Tenant | Info & pengumuman             |
| 14  | Pengaturan            | Owner saja             | Konfigurasi sistem            |

> Menu yang muncul di sidebar Anda tergantung peran (role) akun Anda. Penyewa hanya melihat Dashboard, Invoice, Perbaikan, dan Pengumuman.

**Identitas Pengguna:**

- Di bagian bawah sidebar, terlihat nama dan inisial Anda dalam lingkaran avatar
- Di atas halaman (header), terlihat nama Anda dan ikon lonceng notifikasi

---

### Notifikasi

Ikon **lonceng (🔔)** di pojok kanan atas menampilkan notifikasi Anda.

**Cara membuka:**

1. Klik ikon lonceng di header
2. Dropdown notifikasi akan muncul
3. Angka merah di ikon menunjukkan jumlah notifikasi belum dibaca (maksimal tampil `9+`)

**Isi dropdown:**

- Judul: **"Notifikasi"**
- Tombol **"Tandai semua dibaca"** untuk membaca semua sekaligus
- Daftar notifikasi terbaru dengan ikon, pesan, dan waktu relatif (contoh: "2 jam yang lalu")
- Notifikasi belum dibaca ditandai dengan **titik biru** dan latar lebih terang
- Jika kosong: _"Belum ada notifikasi"_

**Jenis notifikasi yang diterima:**

| Notifikasi                | Penerima       | Kapan Muncul                           |
| ------------------------- | -------------- | -------------------------------------- |
| Permintaan perbaikan baru | Owner, Manager | Penyewa membuat laporan kerusakan      |
| Update status perbaikan   | Penyewa        | Admin mengubah status perbaikan        |
| Pengumuman baru           | Penyewa        | Admin membuat pengumuman               |
| Pengingat pembayaran      | Penyewa        | Invoice mendekati/melewati jatuh tempo |

---

### Logout

1. Klik area profil Anda di bagian bawah sidebar
2. Klik tombol **Logout**
3. Anda akan keluar dari sistem dan kembali ke halaman login

---

## Panduan untuk Pemilik & Pengelola

### Dashboard

**Menu:** Dashboard  
**Subtitle:** Halaman utama yang menampilkan ringkasan kondisi bisnis kos

Saat membuka Dashboard, Anda melihat kartu-kartu informasi penting:

| Kartu                     | Keterangan                                                          |
| ------------------------- | ------------------------------------------------------------------- |
| **Total Properti**        | Jumlah gedung/bangunan kos yang Anda kelola                         |
| **Total Ruangan**         | Jumlah seluruh kamar di semua properti                              |
| **Ruangan Terisi**        | Kamar yang sedang disewa penghuni                                   |
| **Ruangan Tersedia**      | Kamar yang siap disewakan                                           |
| **Occupancy Rate**        | Persentase tingkat hunian (kamar terisi ÷ total kamar × 100%)       |
| **Pendapatan Bulan Ini**  | Total pembayaran yang sudah diverifikasi bulan ini (dalam Rupiah)   |
| **Invoice Overdue**       | Jumlah tagihan yang sudah melewati jatuh tempo dan belum dibayar    |
| **Pembayaran Tertunda**   | Jumlah pembayaran yang sudah diupload buktinya, menunggu verifikasi |
| **Perbaikan Pending**     | Jumlah permintaan perbaikan yang belum diproses                     |
| **Penyewa Aktif**         | Jumlah penghuni yang statusnya aktif                                |
| **Kontrak Aktif**         | Jumlah kontrak sewa yang sedang berjalan                            |
| **Pengeluaran Bulan Ini** | Total biaya operasional yang sudah dicatat bulan ini                |

**Grafik Pendapatan:**

- Di bawah kartu informasi, terdapat grafik garis **"Pendapatan Bulan-Bulanan"**
- Menampilkan tren pendapatan 6 bulan terakhir
- Sumbu X: nama bulan (contoh: Jan 2026, Feb 2026, ...)
- Sumbu Y: jumlah dalam Rupiah

---

### Kelola Properti

**Menu:** Properti  
**Subtitle:** _"Kelola semua properti Anda dengan mudah"_  
**Hak Akses:** Owner (CRUD penuh), Manager (lihat saja)

#### Melihat Daftar Properti

Halaman ini menampilkan tabel semua properti Anda dengan kolom:

| Kolom       | Isi                                                       |
| ----------- | --------------------------------------------------------- |
| **Nama**    | Ikon lingkaran dengan huruf pertama + nama properti       |
| **Alamat**  | Alamat lengkap properti                                   |
| **Ruangan** | Badge biru menampilkan jumlah kamar (contoh: "5 Ruangan") |
| **Status**  | Badge hijau "Aktif" atau abu-abu "Non-Aktif"              |
| **Aksi**    | Tombol **Edit** (biru) dan **Hapus** (merah)              |

**Pencarian:**  
Ketik di kotak pencarian _"Cari nama atau alamat property..."_ untuk mencari berdasarkan nama atau alamat. Hasil langsung berubah saat Anda mengetik.

**Halaman (Pagination):** Menampilkan 10 properti per halaman. Gunakan tombol navigasi di bawah tabel untuk berpindah halaman.

**Jika kosong:** _"Tidak ada property ditemukan"_

#### Menambah Properti Baru

1. Klik tombol **"Tambah Properti"** di pojok kanan atas
2. Modal (jendela popup) akan muncul dengan judul **"Tambah Properti Baru"**
3. Isi formulir:
    - **Nama Property** (wajib) — contoh: "Griya Nyaman"
    - **Alamat** (wajib) — contoh: "Jl. Sudirman No. 123"
    - **Deskripsi** (opsional) — informasi tambahan tentang properti
    - **Status** (wajib) — pilih "Aktif" atau "Non-Aktif"
4. Klik tombol **Simpan**
5. Jika berhasil, muncul pesan hijau: _"Property berhasil dibuat!"_
6. Klik **Batal** untuk membatalkan

#### Mengedit Properti

1. Pada baris properti yang ingin diedit, klik tombol **"Edit"** (ikon pensil, warna biru)
2. Modal **"Edit Properti"** akan muncul dengan data yang sudah terisi
3. Ubah data sesuai kebutuhan
4. Klik **Simpan**
5. Pesan sukses: _"Property berhasil diperbarui!"_

#### Menghapus Properti

1. Klik tombol **"Hapus"** (ikon tempat sampah, warna merah)
2. Modal konfirmasi muncul: _"Apakah Anda yakin ingin menghapus property berikut?"_
3. Detail properti yang akan dihapus ditampilkan
4. Peringatan merah: _"Tindakan ini tidak dapat dibatalkan..."_
5. Klik **Hapus** untuk konfirmasi, atau **Batal** untuk membatalkan
6. Pesan sukses: _"Property berhasil dihapus!"_

> **Catatan:** Properti yang dihapus sebenarnya masih tersimpan di database (soft delete) dan bisa dipulihkan oleh developer jika diperlukan.

---

### Kelola Tipe Ruangan

**Menu:** Tipe Ruangan  
**Subtitle:** _"Kelola semua tipe ruangan di properti Anda"_  
**Hak Akses:** Owner & Manager

Tipe ruangan digunakan untuk mengelompokkan kamar berdasarkan jenis, harga, dan fasilitas. Contoh: "Standard", "Deluxe", "VIP".

#### Melihat Daftar Tipe Ruangan

Tabel menampilkan kolom:

| Kolom           | Isi                                                                               |
| --------------- | --------------------------------------------------------------------------------- |
| **Nama Tipe**   | Ikon + nama tipe ruangan                                                          |
| **Property**    | Properti tempat tipe ini berada                                                   |
| **Harga/Bulan** | Harga sewa per bulan (contoh: Rp 500.000)                                         |
| **Fasilitas**   | Badge biru untuk setiap fasilitas (contoh: AC, WiFi, dll). Tampil "-" jika kosong |
| **Aksi**        | Tombol **Edit** (biru) dan **Hapus** (merah)                                      |

**Pencarian:** Ketik di kotak _"Cari nama tipe ruangan..."_  
**Filter Properti:** Dropdown _"-- Semua Property --"_ untuk filter berdasarkan properti  
**Halaman:** 10 data per halaman

#### Menambah Tipe Ruangan

1. Klik **"Tambah Tipe Ruangan"**
2. Isi formulir:
    - **Nama Tipe Ruangan** (wajib) — contoh: "Standard Room"
    - **Property** (wajib) — pilih dari dropdown
    - **Harga/Bulan** (wajib) — masukkan harga sewa dalam Rupiah
    - **Fasilitas** (opsional) — masukkan fasilitas yang tersedia, contoh: AC, WiFi, Kamar Mandi Dalam
3. Klik **Simpan**

#### Mengedit & Menghapus

- **Edit:** Klik tombol Edit → ubah data → Simpan
- **Hapus:** Klik Hapus → konfirmasi. **Tidak bisa dihapus jika masih ada kamar yang menggunakan tipe ini.**

---

### Kelola Ruangan

**Menu:** Ruangan  
**Subtitle:** _"Kelola semua ruangan di properti Anda"_  
**Hak Akses:** Owner & Manager (CRUD), Owner (hapus)

#### Melihat Daftar Ruangan

Tabel menampilkan kolom:

| Kolom           | Isi                                                          |
| --------------- | ------------------------------------------------------------ |
| **Ruangan**     | Ikon + nomor kamar                                           |
| **Lantai**      | Badge ungu, contoh: "Lt. 1"                                  |
| **Tipe**        | Nama tipe ruangan                                            |
| **Property**    | Nama properti                                                |
| **Harga/Bulan** | Harga sewa bulanan                                           |
| **Status**      | Badge: hijau "Tersedia", biru "Terisi", kuning "Maintenance" |
| **Aksi**        | Tombol **Detail** (ungu), **Edit** (biru), **Hapus** (merah) |

**Pencarian:** Ketik di kotak _"Cari nomor ruangan..."_  
**Filter Properti:** _"-- Semua Property --"_  
**Filter Status:** _"-- Semua Status --"_ / Tersedia / Terisi / Maintenance  
**Halaman:** 10 data per halaman

#### Menambah Ruangan

1. Klik **"Tambah Ruangan"**
2. Isi formulir:
    - **Property** (wajib) — pilih properti
    - **Tipe Ruangan** (wajib) — pilih tipe kamar (otomatis hanya menampilkan tipe dari properti yang dipilih)
    - **Nomor Ruangan** (wajib) — contoh: "101", "A-05"
    - **Lantai** (wajib) — nomor lantai
    - **Status** (wajib) — Tersedia / Terisi / Maintenance
3. Klik **Simpan**

#### Aturan Penting

- **Kamar berstatus "Terisi" tidak bisa diedit.** Anda harus menyelesaikan kontrak sewa terlebih dahulu. Pesan yang muncul: _"Room yang terisi tidak dapat diubah. Harus melalui penyelesaian kontrak."_
- Status kamar otomatis berubah saat kontrak sewa dibuat atau diselesaikan

---

### Kelola Penyewa

**Menu:** Penyewa  
**Subtitle:** _"Kelola semua data penyewa Anda"_  
**Hak Akses:** Owner & Manager (CRUD), Owner (hapus)

#### Melihat Daftar Penyewa

Tabel menampilkan kolom:

| Kolom       | Isi                                                        |
| ----------- | ---------------------------------------------------------- |
| **Nama**    | Ikon avatar + nama penyewa                                 |
| **NIK**     | Nomor identitas (format mono)                              |
| **Email**   | Alamat email                                               |
| **Telepon** | Nomor telepon                                              |
| **Status**  | Badge: hijau "Aktif", kuning "Tidak Aktif", merah "Keluar" |
| **Aksi**    | Tombol **Edit** (biru) dan **Hapus** (merah)               |

**Pencarian:** Ketik di kotak _"Cari NIK, nama, atau email..."_  
Pencarian bisa berdasarkan NIK, nama, email, atau nomor telepon.

**Filter Status:** _"-- Semua Status --"_ / Aktif / Tidak Aktif / Keluar  
**Halaman:** 10 data per halaman

#### Menambah Penyewa

1. Klik **"Tambah Penyewa"**
2. Modal **"Tambah Penyewa Baru"** muncul
3. Isi formulir:
    - **Nama** (wajib) — nama lengkap penyewa
    - **Email** (wajib) — harus unik (tidak boleh sama dengan penyewa lain)
    - **Telepon** (wajib) — nomor HP
    - **NIK** (wajib) — Nomor Induk Kependudukan (harus unik, maksimal 20 karakter)
    - **Kontak Darurat** (opsional) — nama/nomor kontak darurat
    - **Status** (wajib) — Aktif / Tidak Aktif / Keluar
    - **Foto Profil** (opsional) — upload foto penyewa
    - **Foto KTP** (opsional) — upload foto identitas
4. Klik **Simpan**

#### Menghapus Penyewa

- Klik **Hapus** → konfirmasi
- **Tidak bisa dihapus jika memiliki kontrak sewa aktif.** Pesan error: _"Penyewa ini memiliki kontrak aktif. Akhiri kontrak terlebih dahulu."_
- Penyewa yang dihapus masih bisa dipulihkan (soft delete)

---

### Kelola Kontrak Sewa

**Menu:** Kontrak Sewa  
**Subtitle:** _"Kelola semua kontrak sewa penyewa"_  
**Hak Akses:** Owner & Manager (CRUD), Owner (hapus)

#### Melihat Daftar Kontrak

Tabel menampilkan kolom:

| Kolom               | Isi                                                                         |
| ------------------- | --------------------------------------------------------------------------- |
| **Penyewa**         | Avatar + nama penyewa                                                       |
| **Ruangan**         | Nomor kamar                                                                 |
| **Tanggal Mulai**   | Format: dd/mm/yyyy                                                          |
| **Tanggal Selesai** | Format: dd/mm/yyyy                                                          |
| **Deposit**         | Jumlah deposit dalam Rupiah                                                 |
| **Status**          | Badge: kuning "Tertunda", hijau "Aktif", biru "Selesai", merah "Dibatalkan" |
| **Aksi**            | Tombol **Detail** (ungu), **Edit** (biru), **Hapus** (merah)                |

**Pencarian:** _"Cari nama penyewa atau ruangan..."_  
**Filter Status:** _"-- Semua Status --"_ / Tertunda / Aktif / Selesai / Dibatalkan  
**Halaman:** 10 data per halaman

#### Membuat Kontrak Baru

1. Klik **"Buat Kontrak Baru"**
2. Modal **"Buat Kontrak Baru"** muncul
3. Isi formulir dengan alur bertahap:
    - **Penyewa** (wajib) — pilih dari dropdown. Hanya menampilkan penyewa aktif yang **belum memiliki kontrak aktif/pending**
    - **Ruangan** (wajib) — pilih kamar. Hanya menampilkan kamar berstatus **"Tersedia"**
    - **Tanggal Mulai** (wajib) — tanggal kontrak dimulai
    - **Tanggal Selesai** (wajib) — tanggal kontrak berakhir (harus setelah tanggal mulai)
    - **Jatuh Tempo Per Bulan** (wajib) — tanggal bayar tiap bulan (1-31), contoh: tanggal 10 setiap bulan
    - **Deposit** (wajib) — jumlah uang jaminan dalam Rupiah
    - **Status** (wajib) — Tertunda / Aktif / Selesai / Dihentikan / Dibatalkan
4. Klik **Simpan**
5. Saat kontrak dibuat dengan status Aktif, **status kamar otomatis berubah menjadi "Terisi"**

#### Melihat Detail Kontrak

- Klik tombol **Detail** (ikon mata) pada baris kontrak
- Modal detail menampilkan semua informasi kontrak secara lengkap:
    - Info penyewa, ruangan, properti
    - Tanggal mulai & selesai, jatuh tempo
    - Jumlah deposit
    - Status kontrak
    - Informasi audit: dibuat oleh siapa dan kapan, diubah oleh siapa dan kapan

#### Aturan Penting

- Satu penyewa hanya bisa memiliki **1 kontrak aktif/pending** pada waktu bersamaan
- Kamar harus berstatus **"Tersedia"** untuk bisa dibuat kontrak
- Saat kontrak berakhir/dihentikan, **status kamar otomatis kembali menjadi "Tersedia"**

---

### Kelola Invoice

**Menu:** Invoice  
**Subtitle:** _"Kelola daftar invoice penghuni"_  
**Hak Akses:** Owner & Manager (buat & kelola), Owner (hapus)

#### Melihat Daftar Invoice

Di bagian atas halaman terdapat 3 tombol aksi:

- **"Kirim Pengingat"** (kuning-oranye) — kirim pengingat ke semua invoice yang belum bayar & mendekati/melewati jatuh tempo
- **"Generate Bulanan"** (hijau) — buat invoice massal untuk semua kontrak aktif
- **"Buat Invoice"** (biru) — buat invoice satu-satu secara manual

Tabel menampilkan kolom:

| Kolom             | Isi                                                               |
| ----------------- | ----------------------------------------------------------------- |
| **Nomor Invoice** | Nomor referensi otomatis (contoh: INV-20260308-0001)              |
| **Penghuni**      | Nama penyewa                                                      |
| **Bulan**         | Periode tagihan (contoh: Maret 2026)                              |
| **Jumlah**        | Nominal tagihan dalam Rupiah                                      |
| **Jatuh Tempo**   | Tanggal batas pembayaran                                          |
| **Status**        | Badge: merah "Belum Bayar", kuning "Pending", hijau "Sudah Bayar" |
| **Aksi**          | Tombol Detail, Edit, Hapus                                        |

**Pencarian:** _"Cari nama penghuni, email, atau nomor referensi..."_  
**Filter Status:** _"-- Semua Status --"_ / Belum Bayar / Pending / Sudah Bayar  
**Halaman:** 10 data per halaman

#### Membuat Invoice Manual

1. Klik **"Buat Invoice"**
2. Anda diarahkan ke halaman formulir
3. Isi:
    - **Sewa** (wajib) — pilih kontrak sewa dari dropdown. Format tampilan: "Nama Penyewa - Nomor Kamar (Tipe Kamar)"
    - **Bulan** (wajib) — pilih bulan dan tahun tagihan (format: YYYY-MM)
    - **Jumlah Tagihan** (wajib) — nominal harga otomatis terisi dari harga tipe kamar, bisa diubah
    - **Tanggal Jatuh Tempo** (wajib) — tanggal batas bayar
    - **Status** (wajib) — Belum Bayar / Pending / Sudah Bayar
    - **Catatan** (opsional) — catatan tambahan
4. Klik **"Buat Invoice"**
5. Pesan sukses: _"Invoice berhasil dibuat!"_
6. Nomor referensi otomatis digenerate: format INV-YYYYMMDD-XXXX

#### Generate Invoice Massal (Bulk)

Fitur ini memungkinkan Anda membuat invoice untuk **semua kontrak aktif sekaligus** dalam satu bulan.

1. Klik tombol **"Generate Bulanan"** (hijau)
2. Modal muncul: **"Generate Invoice Bulanan"**
3. Pilih **Bulan** yang ingin ditagihkan (contoh: 2026-03)
4. Klik **"Lihat Preview"**
5. Sistem menampilkan tabel preview:
    - Nama penyewa, nomor kamar, tipe kamar
    - Harga bulanan yang akan ditagihkan
    - Jatuh tempo yang dihitung otomatis berdasarkan pengaturan kontrak
    - Badge **kuning** jika invoice sudah ada (akan dilewati)
    - Jumlah: _"X invoice siap dibuat"_
6. Klik **"Generate X Invoice"** untuk membuat semuanya
7. Pesan sukses: _"{jumlah} invoice berhasil dibuat"_
8. Invoice yang sudah ada untuk kontrak & bulan yang sama **tidak akan dibuat ulang**

#### Kirim Pengingat Pembayaran

1. Klik **"Kirim Pengingat"** (kuning)
2. Konfirmasi: _"Kirim pengingat ke semua invoice yang belum bayar & mendekati/melewati jatuh tempo?"_
3. Sistem mengirim notifikasi pengingat ke penyewa yang memiliki tagihan belum dibayar
4. Saat pengiriman berlangsung, tombol berubah menjadi: _"Mengirim..."_

#### Melihat Detail Invoice

Klik tombol **Detail** pada baris invoice. Modal detail menampilkan:

| Bagian                 | Isi                                                                                |
| ---------------------- | ---------------------------------------------------------------------------------- |
| **Informasi Invoice**  | Nomor invoice, status, bulan, jatuh tempo                                          |
| **Informasi Penghuni** | Nama dan email penyewa                                                             |
| **Informasi Ruangan**  | Properti, tipe ruangan, nomor ruangan, harga                                       |
| **Jumlah Tagihan**     | Nominal dalam format besar                                                         |
| **Audit**              | Dibuat oleh (nama + tanggal), Diverifikasi oleh (nama + tanggal, jika sudah bayar) |

#### Aturan Penting

- Invoice yang sudah diverifikasi (status "Sudah Bayar") **tidak bisa diedit atau dihapus**
- Hanya Owner yang bisa menghapus invoice yang belum diverifikasi
- Nomor referensi bersifat unik dan tidak bisa diubah

---

### Verifikasi Pembayaran

**Menu:** Verifikasi Pembayaran  
**Subtitle:** _"Kelola dan verifikasi pembayaran dari penghuni"_  
**Hak Akses:** Owner & Manager

Halaman ini digunakan untuk mereview dan menyetujui/menolak bukti pembayaran yang diupload oleh penyewa.

#### Melihat Daftar

Tabel menampilkan kolom:

| Kolom          | Isi                                                                           |
| -------------- | ----------------------------------------------------------------------------- |
| **Invoice**    | Nomor referensi invoice                                                       |
| **Penghuni**   | Nama penyewa                                                                  |
| **Jumlah**     | Nominal tagihan                                                               |
| **Status**     | Badge: merah "Belum Bayar", kuning "Menunggu Verifikasi", hijau "Sudah Bayar" |
| **Tgl Upload** | Tanggal penyewa mengupload bukti bayar                                        |
| **Aksi**       | Tombol Detail, Approve, Tolak                                                 |

**Pencarian:** _"Nomor invoice, nama tenant, email..."_  
**Filter Status:** _"Semua Status"_ / Menunggu Verifikasi / Sudah Terverifikasi / Belum Bayar

#### Menyetujui Pembayaran (Approve)

1. Klik tombol **"Approve"** (hijau) pada invoice yang berstatus "Menunggu Verifikasi"
2. Modal konfirmasi muncul: _"Anda yakin ingin menyetujui pembayaran ini? Status invoice akan berubah menjadi PAID."_
3. Klik **"Approve"** untuk konfirmasi
4. Sistem secara otomatis:
    - Mengubah status invoice menjadi **"Sudah Bayar"**
    - Mencatat tanggal dan admin yang memverifikasi
    - **Membuat kwitansi (receipt) PDF** dengan nomor unik (contoh: RCP-20260308-0001)
    - **Mengirim email** ke penyewa dengan lampiran kwitansi PDF
5. Pesan sukses akan muncul

#### Menolak Pembayaran

1. Klik tombol **"Tolak"** (merah)
2. Modal konfirmasi muncul
3. Klik **"Tolak"** untuk konfirmasi
4. Status invoice kembali menjadi **"Belum Bayar"**
5. Penyewa perlu mengupload bukti pembayaran yang baru

#### Melihat Detail Invoice

Klik **"Detail"** untuk melihat:

- **Informasi Invoice** — nomor, tanggal, jumlah, status
- **Informasi Penghuni** — nama, email
- **Informasi Kamar** — nomor kamar, tipe kamar
- **Bukti Pembayaran** — preview file yang diupload penyewa (gambar/PDF)
- **Info Verifikasi** — tanggal dan nama admin yang memverifikasi (jika sudah disetujui)

#### Download Kwitansi

- Pada invoice yang sudah diverifikasi, muncul link **"Download Receipt"** (ungu)
- Klik untuk mengunduh file PDF kwitansi pembayaran

---

### Analisis Pendapatan

**Menu:** Analisis Pendapatan  
**Subtitle:** _"Dashboard analisis dan laporan pendapatan kos Anda"_  
**Hak Akses:** Owner & Manager

#### Filter Tanggal

Di bagian atas terdapat pengaturan rentang waktu:

- **Tanggal Mulai** — pilih tanggal awal
- **Tanggal Akhir** — pilih tanggal akhir

**Tombol Filter Cepat:**

- **"7 Hari"** — data 7 hari terakhir
- **"30 Hari"** — data 30 hari terakhir
- **"Bulan Ini"** — data bulan berjalan
- **"Reset"** — kembali ke default

#### Kartu Ringkasan

| Kartu                   | Warna  | Keterangan                                                                       |
| ----------------------- | ------ | -------------------------------------------------------------------------------- |
| **Pendapatan Diterima** | Hijau  | Total invoice yang sudah dibayar & diverifikasi. Info: "X invoice terverifikasi" |
| **Menunggu Verifikasi** | Kuning | Total nominal invoice menunggu verifikasi. Info: "X invoice pending"             |
| **Belum Terbayar**      | Merah  | Total nominal invoice belum dibayar. Info: "X invoice unpaid"                    |
| **Total Invoice**       | Biru   | Jumlah seluruh invoice dalam periode. Info: "X invoice"                          |

#### Grafik

1. **Tren Pendapatan Harian**  
   Grafik garis menampilkan pendapatan per hari selama rentang waktu yang dipilih. Sumbu X: tanggal, Sumbu Y: jumlah dalam Rupiah.

2. **Status Pembayaran**  
   Grafik donat menampilkan proporsi: Sudah Bayar (hijau) vs Pending (kuning) vs Belum Bayar (merah)

#### Tingkat Koleksi

Menampilkan persentase koleksi pembayaran:

- **Diterima** (hijau) — persentase yang sudah dibayar
- **Pending** (kuning) — persentase menunggu verifikasi
- **Unpaid** (merah) — persentase belum dibayar

---

### Kelola Permintaan Perbaikan

**Menu:** Perbaikan  
**Subtitle:** _"Kelola permintaan perbaikan dari penyewa"_  
**Hak Akses:** Owner & Manager (kelola), Tenant (buat & lihat milik sendiri)

#### Melihat Daftar Permintaan

Tabel menampilkan kolom:

| Kolom         | Isi                                                                                                |
| ------------- | -------------------------------------------------------------------------------------------------- |
| **Judul**     | Ringkasan masalah yang dilaporkan                                                                  |
| **Penyewa**   | Nama penyewa yang melapor                                                                          |
| **Kamar**     | Nomor kamar yang bermasalah                                                                        |
| **Kategori**  | Badge warna: oranye "Listrik", biru "Pipa/Air", ungu "Furnitur", hijau "Kebersihan", abu "Lainnya" |
| **Prioritas** | Badge: merah "Tinggi", kuning "Sedang", hijau "Rendah"                                             |
| **Status**    | Badge: kuning "Menunggu", biru "Diproses", hijau "Selesai", merah "Ditolak"                        |
| **Tanggal**   | Tanggal permintaan dibuat                                                                          |
| **Aksi**      | Tombol **Detail** (mata) dan **Proses** (pensil)                                                   |

**Pencarian:** _"Cari judul, penyewa, kamar..."_  
**Filter Status:** Semua / Menunggu / Diproses / Selesai / Ditolak  
**Filter Kategori:** Semua / Listrik / Pipa/Air / Furnitur / Kebersihan / Lainnya  
**Filter Prioritas:** Semua / Tinggi / Sedang / Rendah

#### Melihat Detail

Klik **"Detail"** untuk melihat:

- Judul dan deskripsi lengkap
- Informasi: penyewa, kamar, kategori, prioritas, tanggal, status
- Catatan admin (jika sudah diproses)
- Info penyelesaian (jika sudah selesai): tanggal dan nama admin yang menyelesaikan

#### Memproses Permintaan

1. Klik tombol **"Proses"** (ikon pensil) pada permintaan yang belum selesai
2. Modal **"Proses Permintaan"** muncul
3. Isi formulir:
    - **Status** (wajib) — ubah ke: Menunggu / Diproses / Selesai / Ditolak
    - **Catatan Admin** (opsional) — tulis catatan untuk penyewa, placeholder: _"Catatan untuk penyewa..."_
4. Klik **"Simpan"**
5. Penyewa otomatis menerima **notifikasi** bahwa status permintaannya berubah
6. Jika status diubah ke "Selesai" atau "Ditolak", tanggal dan nama admin yang menyelesaikan akan tercatat

---

### Kelola Pengeluaran

**Menu:** Pengeluaran  
**Subtitle:** _"Kelola semua pengeluaran operasional kos"_  
**Hak Akses:** Owner & Manager

#### Kartu Ringkasan

Di bagian atas halaman terdapat 3 kartu:

- **Total Pengeluaran** (merah) — jumlah total pengeluaran yang tercatat
- **Jumlah Transaksi** (biru) — berapa banyak pengeluaran yang dicatat
- **Per Kategori** (kuning) — breakdown pengeluaran per kategori

#### Melihat Daftar Pengeluaran

Tabel menampilkan kolom:

| Kolom        | Isi                                         |
| ------------ | ------------------------------------------- |
| **Tanggal**  | Tanggal pengeluaran terjadi                 |
| **Judul**    | Nama pengeluaran                            |
| **Kategori** | Badge warna sesuai kategori                 |
| **Properti** | Untuk properti mana (bisa kosong jika umum) |
| **Jumlah**   | Nominal dalam Rupiah                        |
| **Aksi**     | Tombol Edit (biru) dan Hapus (merah)        |

**Kategori yang tersedia:**

| Kategori               | Warna Badge |
| ---------------------- | ----------- |
| Perbaikan              | Oranye      |
| Utilitas (Listrik/Air) | Biru        |
| Kebersihan             | Teal        |
| Perlengkapan           | Ungu        |
| Gaji                   | Hijau       |
| Pajak                  | Merah       |
| Asuransi               | Indigo      |
| Lainnya                | Abu-abu     |

**Pencarian:** _"Cari judul atau deskripsi..."_  
**Filter Bulan:** Dropdown pemilih bulan  
**Filter Kategori:** Dropdown semua kategori di atas  
**Filter Properti:** Dropdown _"Semua"_ + daftar properti

**Jika kosong:** _"Belum ada pengeluaran"_ dengan petunjuk: _"Klik tombol 'Tambah Pengeluaran' untuk mulai mencatat"_

#### Menambah Pengeluaran

1. Klik **"Tambah Pengeluaran"**
2. Modal muncul: **"Tambah Pengeluaran"**
3. Isi formulir:
    - **Judul** (wajib) — contoh: "Beli lampu kamar 5"
    - **Jumlah (Rp)** (wajib, minimal 1) — nominal pengeluaran
    - **Tanggal** (wajib) — tanggal pengeluaran terjadi
    - **Kategori** (wajib) — pilih dari dropdown
    - **Properti** (opsional) — pilih properti terkait, atau kosongkan untuk pengeluaran umum
    - **Deskripsi** (opsional) — detail pengeluaran
    - **Bukti / Nota** (opsional) — upload foto nota/kwitansi (format gambar, maks 2MB)
4. Klik **"Simpan"**

#### Mengedit & Menghapus

- **Edit:** Klik ikon Edit → ubah data → klik **"Simpan Perubahan"**
- **Hapus:** Klik ikon Hapus → konfirmasi penghapusan

---

### Kelola Pengumuman

**Menu:** Pengumuman  
**Subtitle:** _"Kelola pengumuman untuk penyewa"_  
**Hak Akses:** Owner & Manager

#### Kartu Ringkasan

Di bagian atas halaman:

- **Total Pengumuman** (indigo) — jumlah total pengumuman
- **Aktif** (hijau) — yang sedang aktif/ditampilkan
- **Darurat Aktif** (merah) — pengumuman prioritas darurat yang aktif

#### Melihat Daftar Pengumuman

Tabel menampilkan kolom:

| Kolom         | Isi                                                        |
| ------------- | ---------------------------------------------------------- |
| **Judul**     | Judul pengumuman                                           |
| **Prioritas** | Badge: biru "Normal", kuning "Penting", merah "Darurat"    |
| **Target**    | Badge: indigo "Semua" atau ungu (nama properti)            |
| **Tanggal**   | Tanggal terbit                                             |
| **Status**    | Toggle aktif/nonaktif (hijau = aktif, abu = nonaktif)      |
| **Aksi**      | Tombol Detail (mata), Edit (pensil), Hapus (tempat sampah) |

**Pencarian:** _"Cari pengumuman..."_  
**Filter Prioritas:** Semua Prioritas / Normal / Penting / Darurat  
**Filter Status:** Semua Status / Aktif / Nonaktif

**Jika kosong:** _"Belum ada pengumuman"_ — _"Buat pengumuman pertama untuk penyewa"_

#### Membuat Pengumuman

1. Klik **"Buat Pengumuman"**
2. Modal muncul: **"Buat Pengumuman Baru"**
3. Isi formulir:
    - **Judul** (wajib) — judul pengumuman
    - **Isi Pengumuman** (wajib) — teks lengkap pengumuman
    - **Prioritas** (wajib) — Normal / Penting / Darurat
    - **Target** (wajib) — "Semua Penyewa" (untuk semua) atau "Per Properti" (untuk properti tertentu)
    - **Properti** (wajib jika target Per Properti) — pilih properti mana
    - **Tanggal Terbit** (wajib) — kapan pengumuman mulai tampil
    - **Kedaluwarsa** (opsional) — kapan pengumuman otomatis hilang
4. Klik **"Kirim Pengumuman"**
5. Notifikasi otomatis dikirim ke semua penyewa yang relevan

#### Mengaktifkan / Menonaktifkan

- Klik toggle **Status** pada baris pengumuman untuk mengaktifkan atau menonaktifkan
- Pengumuman yang dinonaktifkan tidak akan tampil untuk penyewa

#### Pelacakan Sudah Dibaca

- Klik **Detail** untuk melihat berapa penyewa yang sudah membaca pengumuman
- Persentase pembaca ditampilkan (jumlah yang membaca ÷ total penyewa yang berhak)

---

### Laporan

**Menu:** Laporan  
**Subtitle:** _"Ringkasan data properti, pembayaran, dan penyewa"_  
**Hak Akses:** Owner & Manager

Halaman laporan memiliki **4 tab** yang bisa diklik:

#### Tab 1: Rekap Pembayaran

Ringkasan pembayaran pada bulan tertentu.

**Filter:**

- **Bulan** — pilih bulan dan tahun
- **Properti** — pilih properti tertentu atau semua

**Kartu ringkasan:**

- Total Tagihan, Sudah Bayar, Pending, Belum Bayar
- Jumlah invoice per status
- Tingkat Koleksi (%)

**Tabel:** Daftar semua invoice lengkap dengan detail status per penyewa

**Export:** Tombol **Export PDF** untuk mengunduh laporan dalam format PDF

#### Tab 2: Tingkat Hunian

Menampilkan data hunian per properti.

**Informasi per properti:**

- Nama properti
- Total kamar
- Jumlah kamar terisi, tersedia, maintenance
- Tingkat hunian (%)

**Ringkasan keseluruhan:** Total semua properti (gabungan)

#### Tab 3: Tagihan Belum Lunas

Daftar semua invoice yang belum dibayar atau menunggu verifikasi.

**Filter:** Properti (opsional)  
**Urutan:** Berdasarkan tanggal jatuh tempo (paling lama dulu)  
**Informasi:** Penyewa, kamar, nominal, status, berapa hari terlambat

#### Tab 4: Riwayat Penyewa

Ringkasan data semua penyewa dan rekam jejak pembayaran mereka.

**Ringkasan:**

- Total penyewa aktif, nonaktif, dikeluarkan

**Detail per penyewa:**

- Nama, NIK, status
- Info kontrak aktif (kamar, properti, tanggal)
- Total invoice dan yang sudah dibayar
- Tingkat pembayaran (%)

---

### Pengaturan Sistem

**Menu:** Pengaturan  
**Subtitle:** _"Konfigurasi aplikasi dan pengaturan kos"_  
**Hak Akses:** Owner saja

Halaman ini memiliki **3 tab:**

#### Tab Umum

Informasi dasar tentang kos Anda.

| Field                | Keterangan                 | Contoh                 |
| -------------------- | -------------------------- | ---------------------- |
| **Nama Kos** (wajib) | Nama yang tampil di sistem | "Kos Melati Indah"     |
| **Tagline**          | Sub-judul aplikasi         | "Sistem Manajemen Kos" |
| **Alamat**           | Alamat lengkap kos         | "Jl. Sudirman No. 123" |
| **No. Telepon**      | Nomor telepon pengelola    | "08xx-xxxx-xxxx"       |
| **Email**            | Email pengelola            | "info@kos.com"         |

Klik **"Simpan Pengaturan Umum"** setelah mengubah data.  
Pesan sukses: _"Pengaturan umum berhasil disimpan."_

#### Tab Pembayaran

Informasi rekening bank dan cara bayar yang ditampilkan ke penyewa.

| Field                    | Keterangan                       | Contoh                               |
| ------------------------ | -------------------------------- | ------------------------------------ |
| **Nama Bank**            | Bank tujuan transfer             | "BCA", "BNI", "Mandiri"              |
| **No. Rekening**         | Nomor rekening tujuan            | "1234567890"                         |
| **Atas Nama**            | Nama pemilik rekening            | "Ahmad Pemilik"                      |
| **Instruksi Pembayaran** | Panduan cara bayar untuk penyewa | "Transfer via ATM/Mobile Banking..." |

Di bawah formulir terdapat **Preview Informasi Pembayaran** — menampilkan tampilan yang akan dilihat penyewa.

Klik **"Simpan Pengaturan Pembayaran"** setelah mengubah data.  
Pesan sukses: _"Pengaturan pembayaran berhasil disimpan."_

#### Tab Denda

Pengaturan denda otomatis untuk pembayaran yang terlambat.

1. **Aktifkan Denda Keterlambatan** — toggle on/off  
   Keterangan: _"Denda akan dihitung otomatis untuk invoice yang melewati jatuh tempo"_

2. Jika diaktifkan, muncul field tambahan:
    - **Tipe Denda** — pilih: "Nominal Tetap (Rp)" atau "Persentase (%)"
    - **Nominal Denda (Rp)** / **Persentase Denda (%)** — sesuai tipe yang dipilih
    - **Masa Tenggang (hari)** — berapa hari setelah jatuh tempo sebelum denda berlaku (0-30 hari)  
      Keterangan: _"Jumlah hari setelah jatuh tempo sebelum denda mulai berlaku"_

3. Sistem menampilkan **Contoh Perhitungan** berdasarkan setting yang diisi

Klik **"Simpan Pengaturan Denda"** setelah mengubah data.  
Pesan sukses: _"Pengaturan denda keterlambatan berhasil disimpan."_

---

## Panduan untuk Penyewa

> Bagian ini khusus untuk pengguna dengan peran **Penyewa (Tenant)**. Menu yang Anda lihat di sidebar hanya: Dashboard, Invoice, Perbaikan, dan Pengumuman.

### Dashboard Penyewa

Saat login, Anda melihat Dashboard dengan informasi berikut:

- **Info Kontrak Aktif** — properti, kamar, dan tanggal kontrak Anda
- **Tagihan Belum Bayar** — jumlah invoice yang belum Anda bayar
- **Invoice Terdekat** — tagihan terdekat yang harus dibayar (atau yang sudah jatuh tempo)
- **3 Permintaan Perbaikan Terakhir** — status permintaan perbaikan Anda

---

### Invoices Saya

**Menu:** Invoice  
**Subtitle:** _"Lihat dan bayar tagihan bulan ini"_

#### Melihat Daftar Tagihan

Anda hanya melihat tagihan milik Anda sendiri. Tabel menampilkan:

| Kolom             | Isi                                                                    |
| ----------------- | ---------------------------------------------------------------------- |
| **Nomor Invoice** | Nomor referensi tagihan                                                |
| **Bulan**         | Periode tagihan (contoh: Maret 2026)                                   |
| **Jumlah**        | Nominal yang harus dibayar (Rp)                                        |
| **Jatuh Tempo**   | Tanggal batas pembayaran                                               |
| **Status**        | Merah "Belum Bayar", Kuning "Menunggu Verifikasi", Hijau "Sudah Bayar" |
| **Aksi**          | Tombol aksi sesuai status                                              |

**Pencarian:** _"Cari nomor invoice atau bulan..."_  
**Filter Status:** _"-- Semua Status --"_ / Belum Bayar / Menunggu Verifikasi / Sudah Bayar

#### Cara Membayar Tagihan

1. Cari invoice dengan status **"Belum Bayar"** (badge merah)
2. Klik tombol **"Unggah Bukti"** pada baris invoice tersebut
3. Modal **"Upload Bukti Pembayaran"** muncul
4. Klik area upload dan pilih file bukti transfer Anda
    - Format yang diterima: **PDF, JPG, JPEG, atau PNG**
    - Ukuran maksimal: **5 MB**
5. Klik tombol **"Unggah"**
6. Jika berhasil, muncul pesan hijau: _"Bukti pembayaran berhasil diunggah! Menunggu verifikasi dari manager."_
7. Status invoice berubah menjadi **"Menunggu Verifikasi"** (badge kuning)
8. Tunggu pengelola mereview dan menyetujui pembayaran Anda

**Jika upload gagal:**

- _"Pilih file bukti pembayaran terlebih dahulu"_ — Anda belum memilih file
- _"File harus berformat PDF, JPG, atau PNG"_ — format file tidak didukung
- _"Ukuran file tidak boleh lebih dari 5MB"_ — file terlalu besar

#### Download Kwitansi

Setelah pembayaran disetujui oleh pengelola:

1. Status invoice berubah menjadi **"Sudah Bayar"** (badge hijau)
2. Tombol **"Unduh"** muncul pada baris invoice
3. Klik **"Unduh"** untuk mengunduh kwitansi dalam format **PDF**
4. Anda juga akan menerima email berisi lampiran kwitansi PDF

---

### Permintaan Perbaikan

**Menu:** Perbaikan  
**Subtitle:** _"Ajukan permintaan perbaikan untuk kamar Anda"_

#### Melihat Permintaan Anda

Halaman menampilkan daftar semua permintaan perbaikan yang pernah Anda buat, dalam format kartu.

Setiap kartu menampilkan:

- Judul permintaan
- Tanggal pengajuan
- Kategori (Listrik / Pipa-Air / Furnitur / Kebersihan / Lainnya)
- Prioritas (Tinggi / Sedang / Rendah)
- Status saat ini

**Status permintaan:**

| Status   | Warna Badge | Keterangan                        |
| -------- | ----------- | --------------------------------- |
| Menunggu | Kuning      | Baru diajukan, belum diproses     |
| Diproses | Biru        | Sedang dikerjakan oleh pengelola  |
| Selesai  | Hijau       | Sudah selesai diperbaiki          |
| Ditolak  | Merah       | Permintaan ditolak oleh pengelola |

**Filter:** Klik tab status di atas untuk menyaring (Semua / Menunggu / Diproses / Selesai / Ditolak)

**Jika kosong:** _"Belum ada permintaan perbaikan"_

#### Membuat Permintaan Baru

1. Klik tombol **"Buat Permintaan"** (biru)
2. Modal formulir muncul
3. Isi:
    - **Judul** (wajib, minimal 5 karakter, maksimal 255) — ringkasan masalah, contoh: "AC Tidak Dingin"
    - **Deskripsi** (wajib, minimal 10 karakter) — penjelasan detail masalah yang Anda alami
    - **Kategori** (wajib) — pilih salah satu:
        - Listrik
        - Pipa/Air
        - Furnitur
        - Kebersihan
        - Lainnya
    - **Prioritas** (wajib) — pilih sesuai tingkat urgensi:
        - Rendah — tidak mendesak
        - Sedang — cukup mengganggu (default)
        - Tinggi — sangat mendesak, butuh penanganan segera
4. Klik **"Kirim Permintaan"** atau **"Batal"** untuk membatalkan
5. Pesan sukses: _"Permintaan perbaikan berhasil dikirim"_
6. Pengelola kos akan menerima notifikasi tentang permintaan Anda

**Catatan penting:**

- Anda **harus memiliki kontrak sewa aktif** untuk bisa membuat permintaan
- Jika tidak punya kontrak aktif: _"Anda tidak memiliki kontrak sewa aktif"_
- Kamar dan identitas Anda otomatis terisi berdasarkan kontrak aktif

#### Melihat Detail Permintaan

Klik kartu permintaan untuk melihat detail lengkap:

- Judul dan deskripsi
- Info kamar
- Status saat ini
- Tanggal pengajuan
- **Catatan dari Admin** — catatan/komentar dari pengelola (jika ada)
- **Diselesaikan pada** — tanggal dan nama admin yang menyelesaikan (jika sudah selesai)

#### Menerima Update Status

- Setiap kali pengelola mengubah status permintaan Anda, Anda akan menerima **notifikasi**
- Cek ikon lonceng di header untuk melihat update terbaru
- Status yang mungkin berubah:
    - Menunggu → Diproses (sedang ditangani)
    - Diproses → Selesai (sudah diperbaiki)
    - Menunggu/Diproses → Ditolak (permintaan tidak diproses)

---

### Pengumuman

**Menu:** Pengumuman  
**Subtitle:** _"Informasi terbaru dari pengelola kos"_

#### Melihat Pengumuman

Halaman menampilkan daftar pengumuman dari pengelola kos dalam format kartu.

Setiap kartu menampilkan:

- Judul pengumuman
- Isi (dibatasi 120 karakter, klik untuk baca lengkap)
- **Badge prioritas:** biru "Normal", kuning "Penting", merah "Darurat"
- Tanggal terbit
- Nama properti (jika pengumuman khusus properti)
- Nama pembuat pengumuman
- **Titik biru berkedip** = pengumuman belum dibaca

Anda hanya melihat pengumuman yang:

- Ditargetkan untuk **semua penyewa**, ATAU
- Ditargetkan untuk **properti tempat Anda tinggal**
- Berstatus **aktif** dan belum kadaluarsa

**Filter:**

- **Filter Prioritas:** Semua Prioritas / Normal / Penting / Darurat
- **Pencarian:** Ketik di kotak _"Cari pengumuman..."_ untuk mencari berdasarkan judul atau isi

**Jika kosong:** _"Belum ada pengumuman"_ — _"Pengumuman dari pengelola akan muncul di sini"_

#### Membaca Pengumuman

1. Klik kartu pengumuman yang ingin dibaca
2. Modal detail muncul menampilkan:
    - Judul lengkap
    - Badge prioritas
    - Nama pembuat dan tanggal terbit
    - **Isi pengumuman lengkap**
    - Tanggal kadaluarsa (jika ada)
3. Klik **"Tutup"** untuk menutup

> Pengumuman yang sudah Anda buka akan otomatis ditandai sebagai "sudah dibaca". Titik biru berkedip akan hilang.

---

## Tips Umum

### Pesan Sukses & Error

- **Pesan hijau** (sukses) = aksi berhasil. Otomatis hilang setelah 5 detik, atau klik ✕ untuk menutup.
- **Pesan merah** (error) = ada kesalahan. Baca pesan dan perbaiki sesuai petunjuk.

### Format Data

- **Mata uang:** Ditampilkan dalam Rupiah (contoh: Rp 500.000)
- **Tanggal:** Format Indonesia (contoh: 08 Mar 2026)
- **Waktu relatif:** Notifikasi menampilkan waktu seperti "2 jam yang lalu", "kemarin"

### Tampilan di HP & Tablet

- Semua halaman sudah dioptimalkan untuk tampilan mobile
- Sidebar bisa dibuka/ditutup dengan tombol ☰ di pojok kiri atas
- Tabel yang lebar bisa digeser ke kanan-kiri (horizontal scroll)
- Formulir menyesuaikan ukuran layar secara otomatis

### Pagination (Halaman Data)

- Setiap tabel menampilkan **10 data per halaman**
- Gunakan tombol navigasi di bawah tabel untuk berpindah halaman
- Angka halaman aktif ditampilkan dengan warna biru

---

_Panduan ini mencakup seluruh fitur Fluty Kos yang tersedia per Maret 2026._
