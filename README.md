# Fluty Kos

**Sistem Manajemen Kos-Kosan** — Aplikasi web untuk mengelola properti kos, kamar, penyewa, kontrak sewa, tagihan pembayaran, pemeliharaan, pengeluaran, dan pengumuman.

## Tech Stack

| Komponen | Teknologi                               |
| -------- | --------------------------------------- |
| Backend  | Laravel 12 · PHP 8.2+                   |
| Frontend | Livewire 4 · Tailwind CSS 4 · Alpine.js |
| Build    | Vite 7                                  |
| Auth     | Spatie Laravel Permission (RBAC)        |
| PDF      | DomPDF                                  |
| Testing  | Pest PHP (156 tests, 283 assertions)    |
| Database | SQLite (dev) · MySQL/MariaDB (prod)     |

## Fitur Utama

- **Manajemen Properti & Kamar** — CRUD properti, tipe kamar (dengan fasilitas & harga), dan kamar
- **Manajemen Penyewa** — Data penyewa lengkap dengan NIK, foto KTP, kontak darurat
- **Kontrak Sewa (Lease)** — Lifecycle lengkap: pending → active → completed/terminated, otomatis update status kamar
- **Tagihan & Pembayaran** — Generate invoice otomatis/manual, upload bukti bayar, verifikasi oleh admin, cetak kuitansi PDF + kirim email
- **Pemeliharaan** — Tenant ajukan request, admin proses & update status, notifikasi real-time
- **Pengeluaran** — Catat pengeluaran per properti dengan kategori dan bukti
- **Pengumuman** — Broadcast ke semua tenant atau per properti, tracking sudah dibaca
- **Laporan & Analitik** — Dashboard metrik, chart pendapatan, laporan PDF (outstanding, rekap pembayaran)
- **Pengaturan** — Nama aplikasi, info bank, denda keterlambatan
- **Notifikasi** — Reminder pembayaran otomatis, update maintenance, pengumuman baru
- **3 Role** — Owner (full access), Manager (operasional), Tenant (self-service)

## Instalasi

### Prasyarat

- PHP >= 8.2
- Composer >= 2.x
- Node.js >= 18.x & npm
- SQLite (dev) atau MySQL/MariaDB (prod)

### Setup

```bash
# Masuk direktori
cd fluty-kos

# Install dependencies, generate key, migrate, build frontend
composer setup

# Seed database dengan data contoh
php artisan db:seed
```

### Akun Default (setelah seed)

| Email                | Password   | Role    |
| -------------------- | ---------- | ------- |
| `owner@fluty.test`   | `password` | Owner   |
| `manager@fluty.test` | `password` | Manager |

## Development

```bash
# Jalankan semua dev server sekaligus (web, queue, logs, vite)
composer dev
```

Ini menjalankan 4 proses via `concurrently`:

- `php artisan serve` — Web server
- `php artisan queue:listen` — Queue worker
- `php artisan pail` — Log viewer
- `npm run dev` — Vite HMR

### Testing

```bash
# Jalankan semua test
composer test

# Test spesifik
php artisan test --filter=InvoiceTest
```

### Code Formatting

```bash
./vendor/bin/pint
```

## Struktur Proyek

```
app/
├── Console/Commands/     # Artisan commands (generate invoice, send reminders)
├── Livewire/             # 27 Livewire components (no controllers)
│   ├── Admin/            # Admin-only: analytics, payment, maintenance, dll
│   ├── Auth/             # Login, Logout
│   ├── Invoice/          # CRUD invoice
│   ├── Lease/            # CRUD lease
│   ├── Property/         # CRUD property
│   ├── Room/             # CRUD room
│   ├── RoomType/         # CRUD room type
│   └── Tenant/           # CRUD tenant + tenant self-service
├── Mail/                 # ReceiptSent (PDF attachment)
├── Models/               # 12 Eloquent models
├── Notifications/        # 4 notification classes
├── Observers/            # InvoiceObserver, LeaseObserver
└── Policies/             # 6 authorization policies
database/
├── factories/            # 10 factories
├── migrations/           # Semua migrasi
└── seeders/              # 10 seeders
resources/views/          # ~62 Blade templates
tests/
├── Feature/              # Livewire, Auth, Policy tests
└── Unit/                 # Model tests
```

> Proyek ini **tidak menggunakan Controller** — seluruh logika ditangani oleh Livewire components.

## Scheduled Commands

| Command                   | Jadwal       | Fungsi                    |
| ------------------------- | ------------ | ------------------------- |
| `invoices:send-reminders` | Harian 08:00 | Kirim reminder pembayaran |
| `invoices:generate`       | Manual       | Generate invoice bulanan  |

Untuk production, tambahkan crontab:

```
* * * * * cd /path/to/fluty-kos && php artisan schedule:run >> /dev/null 2>&1
```

## Dokumentasi

| Dokumen                                  | Deskripsi                                                |
| ---------------------------------------- | -------------------------------------------------------- |
| [FEATURES.md](FEATURES.md)               | Daftar lengkap semua fitur sistem                        |
| [USER-GUIDE.md](USER-GUIDE.md)           | Panduan pengguna per role (Owner, Manager, Tenant)       |
| [DEVELOPER-GUIDE.md](DEVELOPER-GUIDE.md) | Panduan teknis: arsitektur, schema, patterns, deployment |

## License

[MIT License](https://opensource.org/licenses/MIT)
