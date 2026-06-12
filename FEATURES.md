# Fluty Kos — Dokumentasi Fitur Lengkap

> Sistem Manajemen Kos-Kosan berbasis web untuk pengelolaan properti, penghuni, kontrak sewa, tagihan, pembayaran, perawatan, dan laporan keuangan.

---

## Daftar Isi

1. [Tentang Sistem](#1-tentang-sistem)
2. [Peran Pengguna & Hak Akses](#2-peran-pengguna--hak-akses)
3. [Dashboard](#3-dashboard)
4. [Manajemen Properti](#4-manajemen-properti)
5. [Manajemen Tipe Kamar](#5-manajemen-tipe-kamar)
6. [Manajemen Kamar](#6-manajemen-kamar)
7. [Manajemen Penyewa (Tenant)](#7-manajemen-penyewa-tenant)
8. [Manajemen Kontrak Sewa (Lease)](#8-manajemen-kontrak-sewa-lease)
9. [Manajemen Invoice (Tagihan)](#9-manajemen-invoice-tagihan)
10. [Verifikasi Pembayaran](#10-verifikasi-pembayaran)
11. [Kwitansi / Receipt (PDF)](#11-kwitansi--receipt-pdf)
12. [Permintaan Perbaikan (Maintenance)](#12-permintaan-perbaikan-maintenance)
13. [Pencatatan Pengeluaran (Expense)](#13-pencatatan-pengeluaran-expense)
14. [Pengumuman](#14-pengumuman)
15. [Laporan (Report)](#15-laporan-report)
16. [Analitik Pendapatan](#16-analitik-pendapatan)
17. [Pengaturan Sistem (Settings)](#17-pengaturan-sistem-settings)
18. [Notifikasi](#18-notifikasi)
19. [Keamanan & Audit](#19-keamanan--audit)
20. [Tabel Hak Akses Lengkap](#20-tabel-hak-akses-lengkap)
21. [Alur Kerja Utama](#21-alur-kerja-utama)
22. [Teknologi yang Digunakan](#22-teknologi-yang-digunakan)

---

## 1. Tentang Sistem

**Fluty Kos** adalah aplikasi web untuk mengelola bisnis kos-kosan secara lengkap. Mulai dari pencatatan properti, kamar, penghuni, kontrak sewa, tagihan bulanan, pembayaran, hingga laporan keuangan — semua bisa dikelola dari satu tempat.

Sistem ini dirancang untuk **3 jenis pengguna**:

- **Owner (Pemilik)** — Kontrol penuh atas seluruh sistem
- **Manager (Pengelola)** — Operasional harian pengelolaan kos
- **Tenant (Penyewa)** — Akses untuk membayar tagihan, request perbaikan, dan melihat pengumuman

---

## 2. Peran Pengguna & Hak Akses

### Owner (Pemilik)

- Akses penuh ke semua fitur tanpa terkecuali
- Satu-satunya yang bisa mengelola pengaturan sistem (nama aplikasi, rekening bank, denda keterlambatan)
- Bisa menghapus properti, kontrak sewa, dan invoice yang sudah diverifikasi
- Bisa mengelola data pengguna

### Manager (Pengelola)

- Mengelola operasional harian: tipe kamar, kamar, penyewa, kontrak, invoice
- Verifikasi pembayaran dan generate kwitansi
- Membuat dan mengelola pengumuman
- Melihat laporan keuangan dan analitik
- **Tidak bisa**: menghapus properti, menghapus kontrak, mengubah pengaturan sistem

### Tenant (Penyewa)

- Melihat dan membayar tagihan sendiri (upload bukti transfer)
- Download kwitansi pembayaran (PDF)
- Membuat permintaan perbaikan kamar
- Melihat pengumuman dari pengelola
- **Hanya bisa melihat data milik sendiri**

---

## 3. Dashboard

### Dashboard Owner / Manager

Halaman utama yang menampilkan ringkasan kondisi bisnis kos secara real-time:

| Informasi                   | Keterangan                                      |
| --------------------------- | ----------------------------------------------- |
| Total Properti              | Jumlah gedung/bangunan kos                      |
| Total Kamar                 | Seluruh kamar di semua properti                 |
| Kamar Terisi                | Kamar yang sedang disewa                        |
| Kamar Tersedia              | Kamar siap disewakan                            |
| Tingkat Hunian (%)          | Persentase kamar terisi vs total                |
| Pendapatan Bulan Ini        | Total invoice yang sudah dibayar & diverifikasi |
| Invoice Terlambat           | Tagihan yang belum dibayar lewat jatuh tempo    |
| Invoice Menunggu Verifikasi | Pembayaran yang sudah diupload, belum dicek     |
| Penyewa Aktif               | Jumlah penghuni aktif saat ini                  |
| Kontrak Aktif               | Jumlah kontrak sewa berjalan                    |
| Pengeluaran Bulan Ini       | Total biaya operasional bulan ini               |
| Maintenance Pending         | Permintaan perbaikan yang belum diproses        |

**Fitur tambahan:**

- **Grafik Pendapatan 6 Bulan Terakhir** — Tren pemasukan bulanan dalam bentuk chart
- **Invoice Terbaru** — 5 tagihan terakhir yang dibuat
- **Maintenance Terbaru** — 5 permintaan perbaikan terakhir
- **Kontrak Segera Berakhir** — Kontrak yang akan habis dalam 30 hari ke depan

### Dashboard Tenant (Penyewa)

- Info kontrak aktif (properti, kamar, tanggal mulai/selesai)
- Jumlah tagihan yang belum dibayar
- Invoice terdekat atau yang sudah jatuh tempo
- 3 permintaan perbaikan terakhir beserta statusnya

---

## 4. Manajemen Properti

Mengelola data gedung atau bangunan kos.

**Data yang disimpan:**
| Field | Keterangan |
|-------|------------|
| Nama | Nama properti (contoh: "Kos Melati Indah") |
| Alamat | Alamat lengkap |
| Deskripsi | Informasi tambahan, aturan kos, dll |
| Status | Aktif atau Nonaktif |

**Fitur:**

- Tambah, edit, dan hapus properti
- Pencarian berdasarkan nama atau alamat
- Pagination (10 data per halaman)
- Soft delete — properti yang dihapus bisa dipulihkan

**Akses:** Owner (CRUD penuh), Manager (lihat saja)

---

## 5. Manajemen Tipe Kamar

Mengategorikan kamar berdasarkan jenis, harga, dan fasilitas.

**Data yang disimpan:**
| Field | Keterangan |
|-------|------------|
| Properti | Tipe kamar ini milik properti mana |
| Nama | Nama tipe (contoh: "Standard", "Deluxe", "VIP") |
| Harga per Bulan | Tarif sewa bulanan |
| Fasilitas | Daftar fasilitas (AC, WiFi, Kamar Mandi Dalam, dll) |

**Fitur:**

- Tambah, edit, dan hapus tipe kamar
- Fasilitas disimpan sebagai daftar (bisa banyak item)
- Harga tipe kamar otomatis dipakai saat membuat invoice
- Tidak bisa dihapus jika masih ada kamar yang menggunakan tipe ini

**Akses:** Owner & Manager

---

## 6. Manajemen Kamar

Mengelola unit kamar individual di setiap properti.

**Data yang disimpan:**
| Field | Keterangan |
|-------|------------|
| Tipe Kamar | Terhubung ke tipe kamar (dan properti) |
| Nomor Kamar | Contoh: "101", "A-05" |
| Lantai | Lantai berapa |
| Status | Tersedia, Terisi, atau Maintenance |

**Alur Status Kamar:**

```
Tersedia → Terisi (saat kontrak sewa dibuat)
Terisi → Tersedia (saat kontrak selesai/dihentikan)
Tersedia/Terisi → Maintenance (admin mengubah manual)
Maintenance → Tersedia (setelah perbaikan selesai)
```

**Aturan Penting:**

- Kamar yang berstatus "Terisi" tidak bisa diedit — harus selesaikan kontrak sewa dulu
- Filter berdasarkan properti, status, dan lantai
- Pencarian berdasarkan nomor kamar

**Akses:** Owner & Manager (CRUD), Owner (hapus)

---

## 7. Manajemen Penyewa (Tenant)

Mengelola data penghuni kos.

**Data yang disimpan:**
| Field | Keterangan |
|-------|------------|
| Nama | Nama lengkap penyewa |
| Email | Email untuk login dan komunikasi |
| NIK | Nomor Induk Kependudukan (unik) |
| No. Telepon | Nomor HP |
| Kontak Darurat | Nama/nomor kontak darurat |
| Foto Profil | Foto penyewa (opsional) |
| Foto KTP | Foto identitas (opsional, disimpan aman) |
| Status | Aktif, Nonaktif, atau Dikeluarkan (Evicted) |

**Fitur:**

- Tambah, edit, hapus penyewa
- Pencarian multi-field: NIK, nama, email, telepon
- Filter berdasarkan status
- Soft delete — data penyewa yang dihapus bisa dipulihkan
- **Tidak bisa dihapus jika memiliki kontrak sewa aktif**
- Bisa dihubungkan ke akun user (untuk login) atau dicatat manual tanpa akun

**Akses:** Owner & Manager (kelola), Tenant (lihat profil sendiri)

---

## 8. Manajemen Kontrak Sewa (Lease)

Mencatat perjanjian sewa antara penyewa dan kamar tertentu.

**Data yang disimpan:**
| Field | Keterangan |
|-------|------------|
| Penyewa | Siapa yang menyewa |
| Kamar | Kamar mana yang disewa |
| Tanggal Mulai | Awal kontrak |
| Tanggal Selesai | Akhir kontrak (harus setelah tanggal mulai) |
| Tanggal Jatuh Tempo | Tanggal bayar tiap bulan (1-31) |
| Deposit | Jumlah uang jaminan |
| Status | Pending, Aktif, Selesai, Dihentikan, Dibatalkan |
| Dibuat oleh | Nama admin yang membuat (audit trail) |
| Diubah oleh | Nama admin terakhir yang mengubah |

**Alur Status Kontrak:**

```
Pending → Aktif (diaktifkan oleh admin)
Aktif → Selesai (kontrak habis masa berlakunya)
Aktif → Dihentikan (diberhentikan di tengah jalan)
Pending → Dibatalkan (kontrak dibatalkan sebelum dimulai)
```

**Aturan Penting:**

- Satu penyewa hanya bisa memiliki 1 kontrak aktif/pending pada saat yang sama
- Kamar harus berstatus "Tersedia" saat membuat kontrak baru
- Tanggal selesai harus lebih lambat dari tanggal mulai
- Soft delete — kontrak yang dihapus bisa dipulihkan

**Fitur:**

- Tambah dengan alur bertahap: Pilih Properti → Pilih Penyewa → Pilih Kamar → Isi Detail
- Pencarian berdasarkan nama penyewa, NIK, atau nomor kamar
- Filter berdasarkan status
- Modal detail untuk melihat informasi lengkap termasuk jejak audit

**Akses:** Owner & Manager (kelola), Tenant (lihat kontrak sendiri), Owner (hapus)

---

## 9. Manajemen Invoice (Tagihan)

Membuat dan mengelola tagihan bulanan untuk penyewa.

**Data yang disimpan:**
| Field | Keterangan |
|-------|------------|
| Kontrak Sewa | Terhubung ke kontrak (penyewa + kamar) |
| Jumlah Tagihan | Nominal yang harus dibayar |
| Periode | Bulan dan tahun tagihan (contoh: 2026-03) |
| Nomor Referensi | Nomor unik otomatis (format: INV-YYYYMMDD-XXXX) |
| Jatuh Tempo | Tanggal batas pembayaran |
| Status | Belum Bayar, Menunggu Verifikasi, Sudah Bayar |
| Bukti Pembayaran | File yang diupload penyewa |
| Diverifikasi pada | Tanggal verifikasi oleh admin |
| Diverifikasi oleh | Admin yang memverifikasi |

**Alur Status Invoice:**

```
Belum Bayar → Penyewa upload bukti → Menunggu Verifikasi
Menunggu Verifikasi → Admin setujui → Sudah Bayar (kwitansi dibuat + diemail)
Menunggu Verifikasi → Admin tolak → Kembali ke Belum Bayar
Sudah Bayar → TERKUNCI (tidak bisa diedit atau dihapus)
```

**Fitur:**

#### Buat Invoice Manual

1. Pilih kontrak sewa
2. Harga otomatis terisi dari harga tipe kamar
3. Atur bulan, jumlah, dan tanggal jatuh tempo
4. Nomor referensi di-generate otomatis

#### Generate Invoice Massal (Bulk)

1. Pilih bulan target (contoh: Maret 2026)
2. Klik "Preview" — sistem menampilkan semua kontrak aktif di bulan tersebut
3. Sistem otomatis menghitung tanggal jatuh tempo berdasarkan pengaturan kontrak
4. Kontrak yang sudah punya invoice di bulan itu akan dilewati
5. Klik "Generate" — semua invoice dibuat sekaligus dengan nomor referensi berurutan

**Perlindungan Data:**

- Invoice yang sudah diverifikasi **tidak bisa diedit atau dihapus** (dilindungi oleh InvoiceObserver)
- Hanya Owner yang bisa menghapus invoice yang belum diverifikasi

**Akses:** Owner & Manager (buat & kelola), Tenant (lihat & upload bukti bayar milik sendiri)

---

## 10. Verifikasi Pembayaran

Halaman khusus untuk admin mereview dan memverifikasi bukti pembayaran dari penyewa.

**Alur Kerja:**

1. Penyewa upload bukti pembayaran (foto transfer / bukti bayar)
2. Status invoice berubah menjadi "Menunggu Verifikasi"
3. Admin/Manager membuka halaman Verifikasi Pembayaran
4. Admin melihat detail invoice + preview bukti pembayaran
5. Admin memilih:
    - **Setujui** → Invoice menjadi "Sudah Bayar", kwitansi PDF dibuat otomatis, email dikirim ke penyewa
    - **Tolak** → Invoice kembali ke "Belum Bayar", penyewa diminta upload ulang

**Fitur:**

- Pencarian berdasarkan nomor referensi atau nama penyewa
- Filter berdasarkan status dan rentang tanggal
- Preview bukti pembayaran (gambar/PDF)
- Indikator invoice yang sudah lewat jatuh tempo

**Akses:** Owner & Manager

---

## 11. Kwitansi / Receipt (PDF)

Bukti pembayaran resmi dalam format PDF yang bisa didownload.

**Alur Pembuatan:**

1. Admin menyetujui pembayaran di halaman Verifikasi
2. Kwitansi dibuat otomatis dengan nomor unik (format: RCP-YYYYMMDD-XXXX)
3. PDF di-generate menggunakan DomPDF dan disimpan di server
4. Email dikirim ke penyewa dengan lampiran PDF
5. Penyewa bisa download kapan saja melalui link aman (butuh login + verifikasi kepemilikan)

**Keamanan:**

- Link download dilindungi autentikasi
- Penyewa hanya bisa download kwitansi milik sendiri
- Admin/Manager bisa download kwitansi siapapun

**Akses:** Owner & Manager (generate otomatis), Tenant (download milik sendiri)

---

## 12. Permintaan Perbaikan (Maintenance)

Sistem tiket untuk melaporkan kerusakan atau masalah di kamar.

**Data yang disimpan:**
| Field | Keterangan |
|-------|------------|
| Judul | Ringkasan masalah (contoh: "AC Tidak Dingin") |
| Deskripsi | Penjelasan detail masalah |
| Kategori | Listrik, Pipa/Air, Furnitur, Kebersihan, Lainnya |
| Prioritas | Rendah, Sedang (default), Tinggi |
| Status | Pending, Sedang Diproses, Selesai, Ditolak |
| Catatan Admin | Catatan dari pengelola selama proses perbaikan |
| Diselesaikan pada | Tanggal penyelesaian |
| Diselesaikan oleh | Admin yang menyelesaikan |

**Alur Kerja:**

```
Penyewa membuat laporan (PENDING)
       ↓
Owner/Manager menerima notifikasi
       ↓
Manager mengubah status → SEDANG DIPROSES
       ↓
Penyewa menerima notifikasi status
       ↓
Manager menambah catatan, menyelesaikan pekerjaan
       ↓
Manager mengubah status → SELESAI / DITOLAK
       ↓
Penyewa menerima notifikasi final
```

**Aturan:**

- Penyewa harus memiliki kontrak sewa aktif untuk membuat permintaan
- Kamar otomatis terisi dari kontrak aktif penyewa
- Notifikasi dikirim ke admin saat ada permintaan baru
- Notifikasi dikirim ke penyewa setiap ada perubahan status

**Akses:**

- Tenant: buat permintaan baru, lihat permintaan sendiri
- Owner & Manager: lihat semua permintaan, ubah status, tambah catatan

---

## 13. Pencatatan Pengeluaran (Expense)

Mencatat biaya operasional pengelolaan kos.

**Data yang disimpan:**
| Field | Keterangan |
|-------|------------|
| Judul | Nama pengeluaran (contoh: "Perbaikan AC Kamar 101") |
| Deskripsi | Detail pengeluaran |
| Jumlah | Nominal biaya |
| Tanggal | Tanggal pengeluaran terjadi |
| Kategori | Perawatan, Utilitas, Kebersihan, Perlengkapan, Gaji, Pajak, Asuransi, Lainnya |
| Properti | Pengeluaran untuk properti tertentu (opsional, bisa umum) |
| Foto Nota/Kwitansi | Upload bukti pengeluaran (gambar, maks 2MB) |

**Fitur:**

- Tambah, edit, hapus pengeluaran
- Pencarian berdasarkan judul
- Filter berdasarkan kategori, properti, dan bulan
- Upload foto nota sebagai bukti
- Soft delete — data pengeluaran yang dihapus bisa dipulihkan
- Ringkasan: total pengeluaran bulan ini, per kategori

**Akses:** Owner & Manager

---

## 14. Pengumuman

Menyebarkan informasi penting ke penghuni kos.

**Data yang disimpan:**
| Field | Keterangan |
|-------|------------|
| Judul | Judul pengumuman |
| Isi | Konten pesan lengkap |
| Prioritas | Normal, Penting, Darurat |
| Target | Semua penyewa atau penyewa di properti tertentu |
| Properti | Jika target spesifik properti |
| Tanggal Terbit | Kapan pengumuman mulai ditampilkan |
| Tanggal Kadaluarsa | Kapan pengumuman otomatis hilang (opsional) |
| Status Aktif | Bisa diaktifkan/nonaktifkan oleh admin |

**Fitur Khusus:**

#### Pelacakan Sudah Dibaca (Read Tracking)

- Sistem mencatat siapa saja yang sudah membaca pengumuman
- Admin bisa melihat persentase: berapa dari total penyewa yang sudah baca
- Penyewa melihat badge "Belum Dibaca" untuk pengumuman baru

#### Aturan Tampil

- Hanya pengumuman aktif yang ditampilkan
- Pengumuman yang tanggal terbitnya belum tiba tidak ditampilkan
- Pengumuman yang sudah kadaluarsa otomatis tidak tampil
- Penyewa hanya melihat pengumuman untuk properti mereka atau pengumuman umum

#### Notifikasi

- Saat admin membuat pengumuman baru, notifikasi dikirim ke semua penyewa yang relevan

**Akses:**

- Owner & Manager: buat, edit, hapus, aktifkan/nonaktifkan
- Tenant: lihat pengumuman yang relevan, status baca otomatis tercatat

---

## 15. Laporan (Report)

Halaman laporan dengan 4 tab untuk analisis bisnis.

### Tab 1: Rekap Pembayaran Bulanan

- Pilih bulan dan properti (opsional)
- Ringkasan: total tagihan, sudah bayar, menunggu verifikasi, belum bayar
- Tingkat koleksi/penagihan (%)
- Tabel detail seluruh invoice di bulan tersebut

### Tab 2: Laporan Hunian

- Data per properti: total kamar, terisi, tersedia, maintenance
- Tingkat hunian (%) per properti
- Ringkasan keseluruhan dari semua properti
- Berguna untuk identifikasi properti yang kurang terisi

### Tab 3: Invoice Tertunggak

- Daftar semua invoice yang belum dibayar atau menunggu verifikasi
- Diurutkan berdasarkan tanggal jatuh tempo (paling lama dulu)
- Menampilkan berapa hari keterlambatan
- Filter berdasarkan properti
- Membantu fokus penagihan ke invoice yang paling mendesak

### Tab 4: Laporan Penyewa

- Jumlah penyewa aktif, nonaktif, dan dikeluarkan
- Detail per penyewa: nama, NIK, status, info kontrak, kamar, properti
- Riwayat pembayaran: jumlah invoice, jumlah yang dibayar, tingkat pembayaran (%)
- Berguna untuk identifikasi penyewa yang sering telat bayar

**Akses:** Owner & Manager

---

## 16. Analitik Pendapatan

Analisis pendapatan dengan visualisasi grafik interaktif.

**Fitur:**

- Pilih rentang tanggal (mulai - selesai)
- Tombol cepat: "7 Hari Terakhir", "30 Hari Terakhir", "Reset ke Bulan Ini"

**Ringkasan yang ditampilkan:**
| Metrik | Keterangan |
|--------|------------|
| Pendapatan Diterima | Total invoice yang sudah dibayar & diverifikasi |
| Pending | Jumlah & nominal invoice menunggu verifikasi |
| Belum Dibayar | Jumlah & nominal invoice belum dibayar |
| Total Invoice | Jumlah seluruh invoice dalam periode |
| Total Nominal | Jumlah seluruh tagihan dalam periode |

**Grafik:**

- **Grafik Pendapatan Harian** — Line chart menampilkan pemasukan per hari dalam rentang waktu yang dipilih
- **Komposisi Status Pembayaran** — Pie chart menampilkan proporsi Sudah Bayar vs Pending vs Belum Bayar

**Akses:** Owner & Manager

---

## 17. Pengaturan Sistem (Settings)

Konfigurasi sistem yang hanya bisa diakses oleh Owner.

### Tab Umum

| Setting       | Keterangan                                        |
| ------------- | ------------------------------------------------- |
| Nama Aplikasi | Nama yang tampil di sistem (default: "Fluty Kos") |
| Tagline       | Sub-judul aplikasi                                |
| Alamat        | Alamat pengelola kos                              |
| Telepon       | Nomor telepon pengelola                           |
| Email         | Email pengelola                                   |

### Tab Pembayaran

| Setting              | Keterangan                                     |
| -------------------- | ---------------------------------------------- |
| Nama Bank            | Bank tujuan transfer                           |
| Nomor Rekening       | Nomor rekening tujuan                          |
| Atas Nama            | Nama pemilik rekening                          |
| Instruksi Pembayaran | Panduan cara bayar yang ditampilkan ke penyewa |

### Tab Denda Keterlambatan

| Setting       | Keterangan                                                        |
| ------------- | ----------------------------------------------------------------- |
| Denda Aktif   | Aktifkan/nonaktifkan perhitungan denda                            |
| Jenis Denda   | Nominal tetap atau persentase                                     |
| Jumlah Denda  | Besaran denda                                                     |
| Masa Tenggang | Berapa hari setelah jatuh tempo sebelum denda berlaku (0-30 hari) |

**Caching:** Pengaturan di-cache selama 1 jam untuk performa. Cache otomatis di-refresh saat pengaturan diubah.

**Akses:** Owner saja

---

## 18. Notifikasi

Sistem notifikasi real-time melalui database dan email.

| Jenis Notifikasi          | Penerima             | Pemicu                               |
| ------------------------- | -------------------- | ------------------------------------ |
| Permintaan Perbaikan Baru | Owner & Manager      | Penyewa membuat permintaan perbaikan |
| Update Status Perbaikan   | Penyewa              | Admin mengubah status perbaikan      |
| Pengumuman Baru           | Penyewa yang relevan | Admin membuat pengumuman             |
| Kwitansi Pembayaran       | Penyewa (via email)  | Admin menyetujui pembayaran          |

**Fitur:**

- Notifikasi ditampilkan di dropdown notification bell di header
- Badge angka menunjukkan jumlah notifikasi belum dibaca
- Klik untuk membaca detail
- Notifikasi email untuk transaksi penting (kwitansi pembayaran)

---

## 19. Keamanan & Audit

### Soft Delete (Hapus Lunak)

Data berikut tidak benar-benar dihapus dari database, bisa dipulihkan:

- Penyewa, Kontrak Sewa, Invoice, Kwitansi, Pengeluaran, Pengumuman

### Jejak Audit

Sistem mencatat secara otomatis:

- **Siapa** yang membuat, mengubah, memverifikasi, atau menyelesaikan setiap data
- **Kapan** aksi tersebut dilakukan (timestamp)
- Field yang dicatat: `created_by`, `updated_by`, `verified_by`, `resolved_by`, `verified_at`, `resolved_at`

### Proteksi Invoice

- Invoice yang sudah diverifikasi (sudah bayar) **tidak bisa diubah atau dihapus**
- Dilindungi oleh InvoiceObserver di level kode

### Otorisasi Berlapis

- Setiap aksi dicek hak aksesnya berdasarkan peran pengguna
- Penyewa hanya bisa mengakses data milik sendiri (row-level security)
- Password di-hash dengan standar keamanan Laravel

### Download Aman

- Link download kwitansi memerlukan login
- Sistem memverifikasi bahwa user adalah pemilik kwitansi atau admin

---

## 20. Tabel Hak Akses Lengkap

| Fitur               | Owner | Manager |    Tenant     |
| ------------------- | :---: | :-----: | :-----------: |
| **Properti**        |       |         |               |
| Lihat               |  ✅   |   ✅    |       —       |
| Tambah/Edit         |  ✅   |    —    |       —       |
| Hapus               |  ✅   |    —    |       —       |
| **Tipe Kamar**      |       |         |               |
| Lihat               |  ✅   |   ✅    |       —       |
| Kelola              |  ✅   |   ✅    |       —       |
| **Kamar**           |       |         |               |
| Lihat               |  ✅   |   ✅    |       —       |
| Tambah/Edit         |  ✅   |   ✅    |       —       |
| Hapus               |  ✅   |    —    |       —       |
| **Penyewa**         |       |         |               |
| Lihat               |  ✅   |   ✅    |       —       |
| Tambah/Edit         |  ✅   |   ✅    |       —       |
| Hapus               |  ✅   |    —    |       —       |
| **Kontrak Sewa**    |       |         |               |
| Lihat               |  ✅   |   ✅    | Milik sendiri |
| Tambah/Edit         |  ✅   |   ✅    |       —       |
| Hapus               |  ✅   |    —    |       —       |
| **Invoice**         |       |         |               |
| Lihat               |  ✅   |   ✅    | Milik sendiri |
| Buat                |  ✅   |   ✅    |       —       |
| Generate Massal     |  ✅   |   ✅    |       —       |
| Upload Bukti Bayar  |   —   |    —    |      ✅       |
| Verifikasi          |  ✅   |   ✅    |       —       |
| Hapus (belum verif) |  ✅   |    —    |       —       |
| **Kwitansi**        |       |         |               |
| Download            |  ✅   |   ✅    | Milik sendiri |
| **Maintenance**     |       |         |               |
| Buat Permintaan     |   —   |    —    |      ✅       |
| Lihat               |  ✅   |   ✅    | Milik sendiri |
| Kelola Status       |  ✅   |   ✅    |       —       |
| **Pengeluaran**     |       |         |               |
| Kelola              |  ✅   |   ✅    |       —       |
| **Pengumuman**      |       |         |               |
| Kelola              |  ✅   |   ✅    |       —       |
| Lihat               |  ✅   |   ✅    |      ✅       |
| **Laporan**         |       |         |               |
| Lihat               |  ✅   |   ✅    |       —       |
| **Analitik**        |       |         |               |
| Lihat               |  ✅   |   ✅    |       —       |
| **Pengaturan**      |       |         |               |
| Kelola              |  ✅   |    —    |       —       |

---

## 21. Alur Kerja Utama

### Alur 1: Pendaftaran Penyewa Baru & Mulai Sewa

```
1. Admin mendaftarkan data penyewa (nama, NIK, kontak, foto KTP)
2. Admin membuat kontrak sewa:
   Pilih Properti → Pilih Penyewa → Pilih Kamar (tersedia) → Isi Detail Kontrak
3. Status kamar otomatis berubah: Tersedia → Terisi
4. Admin generate invoice untuk bulan pertama
5. Penyewa login → lihat invoice → upload bukti transfer
6. Manager mereview → setujui pembayaran
7. Kwitansi PDF dibuat otomatis → diemail ke penyewa
```

### Alur 2: Siklus Tagihan Bulanan

```
1. Admin buka halaman Invoice → tab "Generate Massal"
2. Pilih bulan → klik "Preview" → lihat daftar kontrak aktif
3. Klik "Generate" → semua invoice dibuat sekaligus
4. Penyewa menerima notifikasi / melihat di dashboard
5. Penyewa upload bukti pembayaran
6. Admin verifikasi → kwitansi dikirim
```

### Alur 3: Laporan Kerusakan & Perbaikan

```
1. Penyewa membuat permintaan perbaikan ("AC Bocor", kategori: Listrik, prioritas: Tinggi)
2. Owner/Manager menerima notifikasi
3. Manager ubah status → "Sedang Diproses"
4. Penyewa menerima notifikasi
5. Setelah diperbaiki → Manager ubah status → "Selesai" + catatan
6. Penyewa menerima notifikasi bahwa masalah sudah ditangani
```

### Alur 4: Akhir Kontrak & Checkout

```
1. Dashboard menampilkan kontrak yang akan berakhir dalam 30 hari
2. Penyewa memutuskan perpanjang atau tidak
3. Jika tidak perpanjang:
   - Admin ubah status kontrak → "Selesai"
   - Status kamar otomatis → "Tersedia"
   - Kamar siap disewakan ke penyewa baru
4. Jika perpanjang:
   - Admin buat kontrak baru dengan tanggal baru
```

### Alur 5: Analisis Keuangan Akhir Bulan

```
1. Buka Laporan → Tab "Rekap Pembayaran" → pilih bulan
2. Lihat tingkat koleksi (berapa % yang sudah bayar)
3. Buka Tab "Invoice Tertunggak" → follow up tagihan yang telat
4. Buka "Analitik Pendapatan" → lihat tren pemasukan harian
5. Review pengeluaran di halaman Expense
6. Hitung laba: Pendapatan - Pengeluaran
```

---

## 22. Teknologi yang Digunakan

| Komponen            | Teknologi                 |
| ------------------- | ------------------------- |
| Framework Backend   | Laravel (PHP)             |
| Frontend Interaktif | Livewire + Alpine.js      |
| Template Engine     | Blade                     |
| Styling             | Tailwind CSS              |
| Grafik/Chart        | Chart.js                  |
| Generate PDF        | DomPDF                    |
| Hak Akses / Role    | Spatie Laravel Permission |
| Database            | MySQL                     |
| Testing             | Pest PHP                  |
| Build Tool          | Vite                      |

---

_Dokumen ini mencakup seluruh fitur yang tersedia di Fluty Kos per Maret 2026._
