# DEVELOPER-GUIDE.md — Panduan Developer Fluty Kos

Dokumen ini berisi panduan teknis lengkap untuk developer yang ingin berkontribusi, mempelajari arsitektur, atau melanjutkan pengembangan sistem **Fluty Kos** — Sistem Manajemen Kos-Kosan berbasis Laravel.

---

## Daftar Isi

1. [Tech Stack & Versi](#1-tech-stack--versi)
2. [Prasyarat & Instalasi](#2-prasyarat--instalasi)
3. [Menjalankan Proyek](#3-menjalankan-proyek)
4. [Struktur Direktori](#4-struktur-direktori)
5. [Database Schema](#5-database-schema)
6. [Model & Relationship](#6-model--relationship)
7. [Arsitektur Livewire Component](#7-arsitektur-livewire-component)
8. [Sistem Otorisasi (Roles & Permissions)](#8-sistem-otorisasi-roles--permissions)
9. [Policy](#9-policy)
10. [Observer](#10-observer)
11. [Notification](#11-notification)
12. [Mail](#12-mail)
13. [Console Command & Scheduling](#13-console-command--scheduling)
14. [Routing](#14-routing)
15. [Frontend (Blade, Tailwind, Vite)](#15-frontend-blade-tailwind-vite)
16. [Service Provider](#16-service-provider)
17. [Testing](#17-testing)
18. [Factory & Seeder](#18-factory--seeder)
19. [Alur Bisnis Utama](#19-alur-bisnis-utama)
20. [Konvensi & Code Style](#20-konvensi--code-style)
21. [Deployment](#21-deployment)

---

## 1. Tech Stack & Versi

| Komponen            | Teknologi                 | Versi       |
| ------------------- | ------------------------- | ----------- |
| Backend Framework   | Laravel                   | ^12.0       |
| PHP                 | PHP                       | ^8.2        |
| Realtime Components | Livewire                  | ^4.2        |
| CSS Framework       | Tailwind CSS              | ^4.2.1      |
| Build Tool          | Vite                      | ^7.0.7      |
| JS HTTP Client      | Axios                     | ^1.11.0     |
| Role & Permission   | Spatie Laravel Permission | ^6.24       |
| PDF Generator       | DomPDF (barryvdh)         | ^3.1        |
| Testing Framework   | Pest PHP                  | ^3.8        |
| Code Formatter      | Laravel Pint              | ^1.24       |
| Log Viewer          | Laravel Pail              | ^1.2.2      |
| Concurrent Runner   | Concurrently (npm)        | ^9.0.1      |
| Chart               | Chart.js                  | 4.4.0 (CDN) |
| Database (Dev)      | SQLite                    | -           |
| Database (Prod)     | MySQL / MariaDB           | -           |

---

## 2. Prasyarat & Instalasi

### Prasyarat

- PHP >= 8.2 dengan ekstensi: `pdo_sqlite`, `mbstring`, `openssl`, `tokenizer`, `xml`, `ctype`, `json`, `bcmath`, `fileinfo`
- Composer >= 2.x
- Node.js >= 18.x & npm >= 9.x
- SQLite (development) atau MySQL/MariaDB (production)

### Langkah Instalasi

```bash
# 1. Clone repository
git clone <repo-url> fluty-kos
cd fluty-kos

# 2. Jalankan setup script (install deps, generate key, migrate, build)
composer setup
```

Script `composer setup` menjalankan:

1. `composer install` — install PHP dependencies
2. Copy `.env.example` → `.env` (jika belum ada)
3. `php artisan key:generate` — generate app key
4. `php artisan migrate --force` — jalankan migrasi
5. `npm install` — install JS dependencies
6. `npm run build` — build frontend assets

### Setelah Instalasi

```bash
# Seed database dengan data contoh
php artisan db:seed

# Atau seed spesifik
php artisan db:seed --class=RoleAndPermissionSeeder
```

### Konfigurasi Environment (.env)

```env
APP_NAME="Fluty Kos"
APP_ENV=local
APP_DEBUG=true
APP_URL=http://fluty-kos.test

DB_CONNECTION=sqlite
# Untuk production:
# DB_CONNECTION=mysql
# DB_HOST=127.0.0.1
# DB_PORT=3306
# DB_DATABASE=fluty_kos
# DB_USERNAME=root
# DB_PASSWORD=

MAIL_MAILER=smtp
MAIL_HOST=mailpit
MAIL_PORT=1025
MAIL_FROM_ADDRESS="hello@flutykos.com"

QUEUE_CONNECTION=database
```

---

## 3. Menjalankan Proyek

### Development Server (Rekomendasi)

```bash
composer dev
```

Script ini menjalankan 4 proses secara bersamaan via `concurrently`:

| Proses     | Perintah                                         | Warna Terminal |
| ---------- | ------------------------------------------------ | -------------- |
| **server** | `php artisan serve`                              | Biru           |
| **queue**  | `php artisan queue:listen --tries=1 --timeout=0` | Ungu           |
| **logs**   | `php artisan pail --timeout=0`                   | Merah Muda     |
| **vite**   | `npm run dev`                                    | Oranye         |

### Perintah Manual

```bash
# Web server saja
php artisan serve

# Vite dev server (HMR)
npm run dev

# Queue worker
php artisan queue:listen

# Log viewer
php artisan pail
```

### Build Production

```bash
npm run build
```

---

## 4. Struktur Direktori

```
fluty-kos/
├── app/
│   ├── Console/Commands/           # Artisan commands
│   │   ├── GenerateMonthlyInvoices.php
│   │   └── SendPaymentReminders.php
│   ├── Http/Controllers/           # (kosong — semua via Livewire)
│   ├── Livewire/                   # Livewire components
│   │   ├── Auth/                   # Login, Logout
│   │   ├── Admin/                  # Admin-only components
│   │   │   ├── Analytics/          # IncomeAnalyticsIndex
│   │   │   ├── Payment/            # PaymentVerificationIndex
│   │   │   ├── AnnouncementIndex.php
│   │   │   ├── ExpenseIndex.php
│   │   │   ├── MaintenanceIndex.php
│   │   │   ├── ReportIndex.php
│   │   │   └── SettingIndex.php
│   │   ├── Invoice/                # InvoiceIndex, InvoiceForm
│   │   ├── Lease/                  # LeaseIndex, LeaseForm
│   │   ├── Property/               # PropertyIndex, PropertyForm
│   │   ├── Room/                   # RoomIndex, RoomForm
│   │   ├── RoomType/               # RoomTypeIndex, RoomTypeForm
│   │   ├── Tenant/                 # TenantIndex, TenantForm
│   │   │   └── Invoice/            # TenantInvoiceIndex
│   │   │   └── MaintenanceRequestIndex.php
│   │   │   └── AnnouncementList.php
│   │   ├── Dashboard.php
│   │   ├── NotificationDropdown.php
│   │   └── Sidebar.php
│   ├── Mail/                       # Mailable classes
│   │   └── ReceiptSent.php
│   ├── Models/                     # Eloquent models
│   ├── Notifications/              # Notification classes
│   ├── Observers/                  # Model observers
│   ├── Policies/                   # Authorization policies
│   └── Providers/
│       └── AppServiceProvider.php
├── bootstrap/
│   └── app.php                     # Application bootstrap
├── config/                         # Laravel config files
├── database/
│   ├── factories/                  # 10 factory classes
│   ├── migrations/                 # Semua migrasi database
│   └── seeders/                    # 10 seeder classes
├── public/
│   ├── index.php                   # Entry point
│   └── build/                      # Compiled assets (Vite)
├── resources/
│   ├── css/app.css                 # Tailwind CSS entry
│   ├── js/
│   │   ├── app.js                  # JS entry
│   │   └── bootstrap.js            # Axios setup
│   └── views/                      # ~62 Blade templates
│       ├── layouts/                # app.blade.php, guest.blade.php
│       ├── livewire/               # Livewire component views
│       ├── receipts/               # PDF template
│       ├── reports/pdf/            # PDF report templates
│       └── emails/                 # Email templates
├── routes/
│   ├── web.php                     # Web routes
│   └── console.php                 # Scheduled commands
├── storage/                        # Logs, cache, uploads
├── tests/
│   ├── Pest.php                    # Pest configuration
│   ├── TestCase.php                # Base test case
│   ├── Feature/                    # Feature tests (Livewire, Auth, Policies)
│   └── Unit/                       # Unit tests (Models)
├── composer.json
├── package.json
├── vite.config.js
└── phpunit.xml
```

> **Catatan**: Proyek ini **tidak menggunakan Controller** konvensional. Seluruh logika request/response ditangani oleh **Livewire Components**.

---

## 5. Database Schema

### Overview Tabel

Sistem memiliki **17 tabel** (termasuk tabel Spatie Permission dan Laravel default):

| #   | Tabel                                  | Deskripsi                  | Soft Delete |
| --- | -------------------------------------- | -------------------------- | ----------- |
| 1   | `users`                                | Akun pengguna              | Tidak       |
| 2   | `password_reset_tokens`                | Reset password             | Tidak       |
| 3   | `sessions`                             | Session driver database    | Tidak       |
| 4   | `permissions`                          | Daftar permission (Spatie) | Tidak       |
| 5   | `roles`                                | Daftar role (Spatie)       | Tidak       |
| 6   | `model_has_permissions`                | Pivot user↔permission      | Tidak       |
| 7   | `model_has_roles`                      | Pivot user↔role            | Tidak       |
| 8   | `role_has_permissions`                 | Pivot role↔permission      | Tidak       |
| 9   | `properties`                           | Properti kos               | Tidak       |
| 10  | `room_types`                           | Tipe kamar                 | Tidak       |
| 11  | `rooms`                                | Kamar                      | Tidak       |
| 12  | `tenants`                              | Penyewa                    | Ya          |
| 13  | `leases`                               | Kontrak sewa               | Ya          |
| 14  | `invoices`                             | Tagihan/invoice            | Ya          |
| 15  | `receipts`                             | Kuitansi pembayaran        | Ya          |
| 16  | `maintenance_requests`                 | Permintaan perbaikan       | Tidak       |
| 17  | `expenses`                             | Pengeluaran                | Ya          |
| 18  | `announcements`                        | Pengumuman                 | Ya          |
| 19  | `announcement_reads`                   | Pivot baca pengumuman      | Tidak       |
| 20  | `settings`                             | Konfigurasi aplikasi       | Tidak       |
| 21  | `notifications`                        | Notifikasi (Laravel)       | Tidak       |
| 22  | `cache`                                | Cache driver database      | Tidak       |
| 23  | `jobs` / `job_batches` / `failed_jobs` | Queue system               | Tidak       |

### Detail Skema Per Tabel

#### `properties`

```
id            BIGINT PK AUTO_INCREMENT
name          VARCHAR(255) NOT NULL
address       TEXT NOT NULL
description   TEXT NULL
status        VARCHAR DEFAULT 'active'     -- active | inactive
created_at    TIMESTAMP
updated_at    TIMESTAMP
```

#### `room_types`

```
id            BIGINT PK AUTO_INCREMENT
property_id   BIGINT FK → properties(id) ON DELETE CASCADE
name          VARCHAR NOT NULL
price         DECIMAL(12,2) NOT NULL
facilities    JSON NULL                     -- Array fasilitas
created_at    TIMESTAMP
updated_at    TIMESTAMP
INDEX(property_id)
```

#### `rooms`

```
id            BIGINT PK AUTO_INCREMENT
room_type_id  BIGINT FK → room_types(id) ON DELETE CASCADE
room_number   VARCHAR(50) NOT NULL
floor         INTEGER NOT NULL              -- CHECK: >= 1
status        ENUM('available','occupied','maintenance') NOT NULL
created_at    TIMESTAMP
updated_at    TIMESTAMP
UNIQUE(room_type_id, room_number)
INDEX(room_type_id), INDEX(status)
```

#### `tenants`

```
id                BIGINT PK AUTO_INCREMENT
user_id           BIGINT FK → users(id) ON DELETE CASCADE NULL
name              VARCHAR NULL
email             VARCHAR NULL UNIQUE
nik               VARCHAR(20) NULL UNIQUE   -- Nomor KTP
phone             VARCHAR(20) NOT NULL
emergency_contact VARCHAR NULL
avatar            VARCHAR NULL              -- Path file
ktp_photo         VARCHAR NULL              -- Path file
status            ENUM('active','inactive','evicted') DEFAULT 'active'
created_by        VARCHAR NULL              -- Nama user pembuat
updated_by        VARCHAR NULL              -- Nama user pengubah
created_at        TIMESTAMP
updated_at        TIMESTAMP
deleted_at        TIMESTAMP NULL            -- SOFT DELETE
INDEX(user_id), INDEX(status)
```

#### `leases`

```
id                  BIGINT PK AUTO_INCREMENT
tenant_id           BIGINT FK → tenants(id) ON DELETE CASCADE
room_id             BIGINT FK → rooms(id) ON DELETE CASCADE
start_date          DATE NOT NULL
end_date            DATE NOT NULL
due_date_per_month  INTEGER NOT NULL        -- Tanggal jatuh tempo (1-31)
deposit_amount      DECIMAL(12,2) NOT NULL  -- CHECK: >= 0
status              ENUM('pending','active','completed','terminated','cancelled')
                    DEFAULT 'pending'
created_by          VARCHAR NULL
updated_by          VARCHAR NULL
created_at          TIMESTAMP
updated_at          TIMESTAMP
deleted_at          TIMESTAMP NULL          -- SOFT DELETE
UNIQUE(tenant_id) WHERE status='active'     -- 1 lease aktif per tenant
INDEX(tenant_id), INDEX(room_id), INDEX(status), INDEX(tenant_id, status)
```

#### `invoices`

```
id                  BIGINT PK AUTO_INCREMENT
lease_id            BIGINT FK → leases(id) ON DELETE CASCADE
amount              DECIMAL(12,2) NOT NULL  -- CHECK: >= 0
month_year          VARCHAR NOT NULL        -- Format: YYYY-MM
status              ENUM('unpaid','pending','paid') DEFAULT 'unpaid'
reference_number    VARCHAR NULL UNIQUE     -- Format: INV-YYYYMMDD-XXXX
due_date            DATE NOT NULL
proof_of_payment    VARCHAR NULL            -- Path file bukti bayar
verified_at         TIMESTAMP NULL          -- Waktu verifikasi
created_by          BIGINT FK → users(id) ON DELETE SET NULL
verified_by         BIGINT FK → users(id) ON DELETE SET NULL
created_at          TIMESTAMP
updated_at          TIMESTAMP
deleted_at          TIMESTAMP NULL          -- SOFT DELETE
UNIQUE(lease_id, month_year)                -- 1 invoice per bulan per lease
INDEX(lease_id), INDEX(status), INDEX(verified_at)
INDEX(lease_id, status), INDEX(status, verified_at)
```

#### `receipts`

```
id                BIGINT PK AUTO_INCREMENT
invoice_id        BIGINT FK → invoices(id) ON DELETE CASCADE
receipt_number    VARCHAR NOT NULL UNIQUE   -- Nomor kuitansi
pdf_path          VARCHAR NOT NULL          -- Path file PDF
issued_at         TIMESTAMP DEFAULT NOW()
created_by        BIGINT FK → users(id) ON DELETE RESTRICT
created_at        TIMESTAMP
updated_at        TIMESTAMP
deleted_at        TIMESTAMP NULL            -- SOFT DELETE
```

#### `maintenance_requests`

```
id              BIGINT PK AUTO_INCREMENT
tenant_id       BIGINT FK → tenants(id) ON DELETE CASCADE
room_id         BIGINT FK → rooms(id) ON DELETE CASCADE
title           VARCHAR NOT NULL
description     TEXT NOT NULL
category        ENUM('electrical','plumbing','furniture','cleaning','other')
                DEFAULT 'other'
priority        ENUM('low','medium','high') DEFAULT 'medium'
status          ENUM('pending','in_progress','completed','rejected')
                DEFAULT 'pending'
admin_notes     TEXT NULL
resolved_at     TIMESTAMP NULL
resolved_by     BIGINT FK → users(id) ON DELETE SET NULL
created_at      TIMESTAMP
updated_at      TIMESTAMP
```

#### `expenses`

```
id              BIGINT PK AUTO_INCREMENT
property_id     BIGINT FK → properties(id) ON DELETE SET NULL
title           VARCHAR NOT NULL
description     TEXT NULL
amount          DECIMAL(12,2) NOT NULL      -- CHECK: >= 0
expense_date    DATE NOT NULL
category        ENUM('maintenance','utility','cleaning','supplies',
                     'salary','tax','insurance','other')
receipt_image   VARCHAR NULL                -- Path file
created_by      BIGINT FK → users(id) ON DELETE SET NULL
created_at      TIMESTAMP
updated_at      TIMESTAMP
deleted_at      TIMESTAMP NULL              -- SOFT DELETE
```

#### `announcements`

```
id              BIGINT PK AUTO_INCREMENT
property_id     BIGINT FK → properties(id) ON DELETE SET NULL
created_by      BIGINT FK → users(id) ON DELETE CASCADE
title           VARCHAR NOT NULL
content         TEXT NOT NULL
priority        ENUM('normal','important','urgent') DEFAULT 'normal'
target          ENUM('all','property') DEFAULT 'all'
published_at    DATE NOT NULL
expires_at      DATE NULL
is_active       BOOLEAN DEFAULT TRUE
created_at      TIMESTAMP
updated_at      TIMESTAMP
deleted_at      TIMESTAMP NULL              -- SOFT DELETE
```

#### `announcement_reads`

```
id                BIGINT PK AUTO_INCREMENT
announcement_id   BIGINT FK → announcements(id) ON DELETE CASCADE
user_id           BIGINT FK → users(id) ON DELETE CASCADE
read_at           TIMESTAMP NOT NULL
UNIQUE(announcement_id, user_id)
```

#### `settings`

```
id              BIGINT PK AUTO_INCREMENT
key             VARCHAR NOT NULL UNIQUE     -- Identifier setting
value           TEXT NULL                   -- Nilai (bisa JSON)
group           VARCHAR NOT NULL            -- Grup: general, payment, late_fee
created_at      TIMESTAMP
updated_at      TIMESTAMP
```

**Default Settings:**
| Key | Default | Group |
|-----|---------|-------|
| `app_name` | Fluty Kos | general |
| `app_tagline` | Sistem Manajemen Kos | general |
| `app_address` | - | general |
| `app_phone` | - | general |
| `app_email` | - | general |
| `bank_name` | - | payment |
| `bank_account_number` | - | payment |
| `bank_account_holder` | - | payment |
| `payment_instructions` | - | payment |
| `late_fee_enabled` | false | late_fee |
| `late_fee_type` | fixed | late_fee |
| `late_fee_amount` | 0 | late_fee |
| `late_fee_grace_days` | 0 | late_fee |

### Diagram Relasi (ERD Ringkas)

```
Property ──1:N──▶ RoomType ──1:N──▶ Room ──1:N──▶ Lease ──1:N──▶ Invoice ──1:1──▶ Receipt
                                      │              │
                                      │              └── BelongsTo ──▶ Tenant ──BelongsTo──▶ User
                                      │
                                      └── HasMany ──▶ MaintenanceRequest ──BelongsTo──▶ Tenant

Property ──1:N──▶ Expense
Property ──1:N──▶ Announcement ──M:N──▶ User (via announcement_reads)
Invoice ──BelongsTo──▶ User (created_by)
Invoice ──BelongsTo──▶ User (verified_by)
```

---

## 6. Model & Relationship

### Peta Relasi Lengkap

#### User (`app/Models/User.php`)

```php
// Traits
use HasFactory, Notifiable, HasRoles;

// Fillable
['name', 'email', 'password']

// Hidden
['password', 'remember_token']

// Casts
'email_verified_at' => 'datetime',
'password' => 'hashed'

// Relationships
tenant()            → HasOne(Tenant)           // Profil tenant user
createdInvoices()   → HasMany(Invoice, 'created_by')
verifiedInvoices()  → HasMany(Invoice, 'verified_by')
createdLeases()     → HasMany(Lease, 'created_by')
updatedLeases()     → HasMany(Lease, 'updated_by')
```

#### Property (`app/Models/Property.php`)

```php
// Fillable
['name', 'address', 'description', 'status']

// Relationships
roomTypes()         → HasMany(RoomType)

// Accessors
getTotalRoomsAttribute()  // Hitung total kamar dari semua room types
```

#### RoomType (`app/Models/RoomType.php`)

```php
// Fillable
['property_id', 'name', 'price', 'facilities']

// Casts
'facilities' => 'json',
'price' => 'decimal:2'

// Relationships
property()          → BelongsTo(Property)
rooms()             → HasMany(Room)
```

#### Room (`app/Models/Room.php`)

```php
// Fillable
['room_type_id', 'room_number', 'floor', 'status']

// Relationships
roomType()          → BelongsTo(RoomType)
leases()            → HasMany(Lease)

// Methods
activeLease()       // Return lease aktif saat ini (jika ada)
```

#### Tenant (`app/Models/Tenant.php`)

```php
// Traits
use HasFactory, SoftDeletes;

// Fillable
['name', 'email', 'nik', 'phone', 'emergency_contact',
 'avatar', 'ktp_photo', 'status', 'user_id', 'created_by', 'updated_by']

// Relationships
user()              → BelongsTo(User)
leases()            → HasMany(Lease)

// Accessors
getDisplayNameAttribute()  // Return user.name atau tenant.name
```

#### Lease (`app/Models/Lease.php`)

```php
// Traits
use HasFactory, SoftDeletes;

// Fillable
['tenant_id', 'room_id', 'start_date', 'end_date',
 'due_date_per_month', 'deposit_amount', 'status', 'created_by', 'updated_by']

// Casts
'start_date' => 'date',
'end_date' => 'date',
'deposit_amount' => 'decimal:2'

// Relationships
tenant()            → BelongsTo(Tenant)
room()              → BelongsTo(Room)
invoices()          → HasMany(Invoice)
creator()           → BelongsTo(User, 'created_by')
updater()           → BelongsTo(User, 'updated_by')
```

#### Invoice (`app/Models/Invoice.php`)

```php
// Traits
use HasFactory, SoftDeletes;

// Fillable
['lease_id', 'amount', 'month_year', 'status', 'reference_number',
 'due_date', 'proof_of_payment', 'verified_at', 'created_by', 'verified_by']

// Casts
'amount' => 'decimal:2',
'due_date' => 'date',
'verified_at' => 'datetime'

// Relationships
lease()             → BelongsTo(Lease)
creator()           → BelongsTo(User, 'created_by')
verifier()          → BelongsTo(User, 'verified_by')
receipt()           → HasOne(Receipt)

// Methods
static generateInvoiceNumber()  // Format: INV-YYYYMMDD-XXXX
generateReceipt($userId)        // Buat PDF + kirim email ke tenant
```

#### Receipt (`app/Models/Receipt.php`)

```php
// Traits
use SoftDeletes;

// Fillable
['invoice_id', 'receipt_number', 'pdf_path', 'issued_at', 'created_by']

// Casts
'issued_at' => 'datetime'

// Relationships
invoice()           → BelongsTo(Invoice)
creator()           → BelongsTo(User, 'created_by')
```

#### MaintenanceRequest (`app/Models/MaintenanceRequest.php`)

```php
// Traits
use HasFactory;

// Fillable
['tenant_id', 'room_id', 'title', 'description', 'category',
 'priority', 'status', 'admin_notes', 'resolved_at', 'resolved_by']

// Casts
'resolved_at' => 'datetime'

// Relationships
tenant()            → BelongsTo(Tenant)
room()              → BelongsTo(Room)
resolver()          → BelongsTo(User, 'resolved_by')
```

#### Expense (`app/Models/Expense.php`)

```php
// Traits
use HasFactory, SoftDeletes;

// Fillable
['property_id', 'title', 'description', 'amount', 'expense_date',
 'category', 'receipt_image', 'created_by']

// Casts
'amount' => 'decimal:2',
'expense_date' => 'date'

// Relationships
property()          → BelongsTo(Property)
creator()           → BelongsTo(User, 'created_by')

// Static Methods
getCategoryLabel($category)  // Label Indonesia untuk kategori
```

#### Announcement (`app/Models/Announcement.php`)

```php
// Traits
use HasFactory, SoftDeletes;

// Fillable
['property_id', 'created_by', 'title', 'content', 'priority',
 'target', 'published_at', 'expires_at', 'is_active']

// Casts
'published_at' => 'date',
'expires_at' => 'date',
'is_active' => 'boolean'

// Relationships
property()          → BelongsTo(Property)
creator()           → BelongsTo(User, 'created_by')
readByUsers()       → BelongsToMany(User, 'announcement_reads')->withPivot('read_at')

// Methods
isReadBy($user)     // Cek apakah sudah dibaca user tertentu

// Scopes
scopeActive($query) // Filter: is_active=true, published, belum expired

// Static Methods
getPriorityLabel($priority)  // Label Indonesia
```

#### Setting (`app/Models/Setting.php`)

```php
// Fillable
['key', 'value', 'group']

// Static Methods
get($key, $default = null)   // Ambil dengan cache 3600 detik
set($key, $value, $group)    // Set + clear cache
getGroup($group)             // Ambil semua setting per grup
defaults()                   // Return semua default settings
getValue($key)               // Ambil dengan fallback ke defaults
```

---

## 7. Arsitektur Livewire Component

### Pola Umum

Proyek ini menggunakan **Livewire v4** untuk seluruh interaksi UI. Tidak ada controller — semua logika ada di Livewire component.

#### Pola Index Component

Setiap resource memiliki `*Index.php` dengan pola standar:

- **Pagination**: `WithPagination` trait, 10 item per halaman
- **Search**: Property `$search` dengan `updatedSearch()` → `resetPage()`
- **Filter**: Property `$filter*` untuk filtering
- **Modal CRUD**: `$showModal`, `$showDeleteModal`, `$editingId`
- **Events**: Listen `*-saved` event untuk refresh data
- **Authorization**: Pengecekan `$this->authorize()` atau `Gate::check()`

#### Pola Form Component

Setiap resource memiliki `*Form.php` dengan pola standar:

- **Properties**: Setiap field form = public property
- **Validation**: Menggunakan `#[Validate]` attribute
- **Mount**: `mount($id = null)` — load data jika edit, kosong jika create
- **Save**: `save()` — validate, create/update, dispatch event
- **Events**: Dispatch `*-saved` dan `close-modal`

#### Daftar Semua Livewire Components (26 total)

| #   | Component                | Path                                                  | Fungsi                        |
| --- | ------------------------ | ----------------------------------------------------- | ----------------------------- |
| 1   | Dashboard                | `Livewire/Dashboard.php`                              | Metrik & chart dashboard      |
| 2   | Sidebar                  | `Livewire/Sidebar.php`                                | Navigasi sidebar dinamis      |
| 3   | NotificationDropdown     | `Livewire/NotificationDropdown.php`                   | Dropdown notifikasi           |
| 4   | Login                    | `Livewire/Auth/Login.php`                             | Form login                    |
| 5   | Logout                   | `Livewire/Auth/Logout.php`                            | Aksi logout                   |
| 6   | PropertyIndex            | `Livewire/Property/PropertyIndex.php`                 | CRUD properti                 |
| 7   | PropertyForm             | `Livewire/Property/PropertyForm.php`                  | Form properti                 |
| 8   | RoomTypeIndex            | `Livewire/RoomType/RoomTypeIndex.php`                 | CRUD tipe kamar               |
| 9   | RoomTypeForm             | `Livewire/RoomType/RoomTypeForm.php`                  | Form tipe kamar               |
| 10  | RoomIndex                | `Livewire/Room/RoomIndex.php`                         | CRUD kamar                    |
| 11  | RoomForm                 | `Livewire/Room/RoomForm.php`                          | Form kamar                    |
| 12  | TenantIndex              | `Livewire/Tenant/TenantIndex.php`                     | CRUD penyewa                  |
| 13  | TenantForm               | `Livewire/Tenant/TenantForm.php`                      | Form penyewa + upload         |
| 14  | LeaseIndex               | `Livewire/Lease/LeaseIndex.php`                       | CRUD kontrak sewa             |
| 15  | LeaseForm                | `Livewire/Lease/LeaseForm.php`                        | Form kontrak sewa             |
| 16  | InvoiceIndex             | `Livewire/Invoice/InvoiceIndex.php`                   | CRUD invoice + generate       |
| 17  | InvoiceForm              | `Livewire/Invoice/InvoiceForm.php`                    | Form invoice                  |
| 18  | PaymentVerificationIndex | `Livewire/Admin/Payment/PaymentVerificationIndex.php` | Verifikasi pembayaran         |
| 19  | IncomeAnalyticsIndex     | `Livewire/Admin/Analytics/IncomeAnalyticsIndex.php`   | Analitik pendapatan           |
| 20  | MaintenanceIndex         | `Livewire/Admin/MaintenanceIndex.php`                 | Kelola pemeliharaan           |
| 21  | ExpenseIndex             | `Livewire/Admin/ExpenseIndex.php`                     | CRUD pengeluaran              |
| 22  | AnnouncementIndex        | `Livewire/Admin/AnnouncementIndex.php`                | CRUD pengumuman               |
| 23  | ReportIndex              | `Livewire/Admin/ReportIndex.php`                      | Generate laporan PDF          |
| 24  | SettingIndex             | `Livewire/Admin/SettingIndex.php`                     | Pengaturan aplikasi           |
| 25  | TenantInvoiceIndex       | `Livewire/Tenant/Invoice/TenantInvoiceIndex.php`      | Invoice tenant (self-service) |
| 26  | MaintenanceRequestIndex  | `Livewire/Tenant/MaintenanceRequestIndex.php`         | Request maintenance tenant    |
| 27  | AnnouncementList         | `Livewire/Tenant/AnnouncementList.php`                | Daftar pengumuman tenant      |

### Event Bus (Dispatch / Listen)

| Event Name        | Dispatched By   | Listened By      |
| ----------------- | --------------- | ---------------- |
| `property-saved`  | PropertyForm    | PropertyIndex    |
| `room-type-saved` | RoomTypeForm    | RoomTypeIndex    |
| `room-saved`      | RoomForm        | RoomIndex        |
| `tenant-saved`    | TenantForm      | TenantIndex      |
| `lease-saved`     | LeaseForm       | LeaseIndex       |
| `invoice-saved`   | InvoiceForm     | InvoiceIndex     |
| `close-modal`     | Form components | Index components |
| `show-error`      | Various         | Index components |

---

## 8. Sistem Otorisasi (Roles & Permissions)

### Package

Menggunakan **Spatie Laravel Permission v6.24** dengan guard `web`.

### Roles

| Role      | Deskripsi                       | Jumlah Permission |
| --------- | ------------------------------- | ----------------- |
| `owner`   | Pemilik kos — akses penuh       | 28 (semua)        |
| `manager` | Pengelola — operasional harian  | 22                |
| `tenant`  | Penyewa — self-service terbatas | 5                 |

### Daftar Permission (28 total)

```php
// Property Management
'view-properties', 'create-property', 'edit-property', 'delete-property'

// Room Type Management
'view-room-types', 'create-room-type', 'edit-room-type', 'delete-room-type'

// Room Management
'view-rooms', 'create-room', 'edit-room', 'delete-room',
'check-in-tenant', 'check-out-tenant'

// Tenant Management
'view-tenants', 'create-tenant', 'edit-tenant', 'delete-tenant'

// Lease Management
'view-leases', 'create-lease', 'edit-lease', 'delete-lease'

// Invoice/Payment
'view-invoices', 'create-invoice', 'verify-payment', 'upload-payment-proof'

// Reports
'view-reports', 'view-income-report'

// Maintenance
'view-maintenance', 'create-maintenance', 'manage-maintenance'

// Expenses
'view-expenses', 'manage-expenses'

// Announcements
'view-announcements', 'manage-announcements'

// Settings & Users
'manage-settings', 'manage-users'
```

### Matriks Akses Per Role

| Permission           | Owner | Manager | Tenant |
| -------------------- | :---: | :-----: | :----: |
| view-properties      |  ✅   |   ✅    |   ❌   |
| create-property      |  ✅   |   ✅    |   ❌   |
| edit-property        |  ✅   |   ✅    |   ❌   |
| delete-property      |  ✅   |   ❌    |   ❌   |
| view-room-types      |  ✅   |   ✅    |   ❌   |
| create-room-type     |  ✅   |   ✅    |   ❌   |
| edit-room-type       |  ✅   |   ✅    |   ❌   |
| delete-room-type     |  ✅   |   ❌    |   ❌   |
| view-rooms           |  ✅   |   ✅    |   ❌   |
| create-room          |  ✅   |   ✅    |   ❌   |
| edit-room            |  ✅   |   ✅    |   ❌   |
| delete-room          |  ✅   |   ✅    |   ❌   |
| check-in-tenant      |  ✅   |   ✅    |   ❌   |
| check-out-tenant     |  ✅   |   ✅    |   ❌   |
| view-tenants         |  ✅   |   ✅    |   ❌   |
| create-tenant        |  ✅   |   ✅    |   ❌   |
| edit-tenant          |  ✅   |   ✅    |   ❌   |
| delete-tenant        |  ✅   |   ✅    |   ❌   |
| view-leases          |  ✅   |   ✅    | ⚡ own |
| create-lease         |  ✅   |   ✅    |   ❌   |
| edit-lease           |  ✅   |   ✅    |   ❌   |
| delete-lease         |  ✅   |   ❌    |   ❌   |
| view-invoices        |  ✅   |   ✅    | ⚡ own |
| create-invoice       |  ✅   |   ✅    |   ❌   |
| verify-payment       |  ✅   |   ✅    |   ❌   |
| upload-payment-proof |  ✅   |   ✅    |   ✅   |
| view-reports         |  ✅   |   ✅    |   ❌   |
| view-income-report   |  ✅   |   ✅    |   ❌   |
| view-maintenance     |  ✅   |   ✅    |   ✅   |
| create-maintenance   |  ✅   |   ✅    |   ✅   |
| manage-maintenance   |  ✅   |   ✅    |   ❌   |
| view-expenses        |  ✅   |   ✅    |   ❌   |
| manage-expenses      |  ✅   |   ✅    |   ❌   |
| view-announcements   |  ✅   |   ✅    |   ✅   |
| manage-announcements |  ✅   |   ✅    |   ❌   |
| manage-settings      |  ✅   |   ❌    |   ❌   |
| manage-users         |  ✅   |   ❌    |   ❌   |

> ⚡ = Hanya data milik sendiri (diimplementasikan di Policy)

---

## 9. Policy

Semua policy ada di `app/Policies/` dan terdaftar secara otomatis oleh Laravel.

### PropertyPolicy

| Method          | Logic                    |
| --------------- | ------------------------ |
| `viewAny()`     | `can('view-properties')` |
| `view()`        | `can('view-properties')` |
| `create()`      | `can('create-property')` |
| `update()`      | `can('edit-property')`   |
| `delete()`      | `can('delete-property')` |
| `restore()`     | `can('delete-property')` |
| `forceDelete()` | `can('delete-property')` |

### RoomTypePolicy

Sama dengan PropertyPolicy — mapping ke permission `*-room-type`.

### RoomPolicy

| Method                | Logic                                                 |
| --------------------- | ----------------------------------------------------- |
| `viewAny/view/create` | Permission-based                                      |
| `update()`            | `can('edit-room')` **DAN** room status ≠ `occupied`   |
| `delete()`            | `can('delete-room')` **DAN** room status ≠ `occupied` |
| `restore()`           | `return false`                                        |
| `forceDelete()`       | `return false`                                        |

### TenantPolicy

Permission-based: `*-tenant` permissions.

### LeasePolicy

| Method          | Logic                                                      |
| --------------- | ---------------------------------------------------------- |
| `viewAny()`     | Tenant: lihat milik sendiri. Lainnya: `can('view-leases')` |
| `view()`        | Tenant: hanya lease sendiri. Lainnya: permission           |
| `update()`      | **Tolak** jika status completed/terminated/cancelled       |
| `delete()`      | **Hanya** soft delete pending/cancelled; role owner only   |
| `restore()`     | Role owner only                                            |
| `forceDelete()` | Role owner only                                            |

### InvoicePolicy

| Method          | Logic                                                  |
| --------------- | ------------------------------------------------------ |
| `viewAny()`     | `can('view-invoices')`                                 |
| `view()`        | Tenant: hanya invoice sendiri. Lainnya: permission     |
| `create()`      | `can('create-invoice')`                                |
| `update()`      | **Tolak** jika verified. Perlu `can('verify-payment')` |
| `delete()`      | **Tolak** jika verified. Role owner only               |
| `restore()`     | Role owner only                                        |
| `forceDelete()` | Role owner only                                        |

---

## 10. Observer

### InvoiceObserver (`app/Observers/InvoiceObserver.php`)

| Hook         | Aksi                                                                                          |
| ------------ | --------------------------------------------------------------------------------------------- |
| `creating()` | Auto-set `created_by` dari `Auth::id()`                                                       |
| `updating()` | **Cegah update** jika status sebelumnya `paid` (sudah terverifikasi)                          |
| `updated()`  | Jika status berubah ke `paid`: set `verified_at` dan `verified_by` via `DB::table()` (atomic) |
| `deleting()` | **Throw exception** jika `verified_at` terisi (cegah hapus invoice terverifikasi)             |

### LeaseObserver (`app/Observers/LeaseObserver.php`)

| Hook         | Aksi                                                                        |
| ------------ | --------------------------------------------------------------------------- |
| `creating()` | Auto-set `created_by` dari `Auth::id()`                                     |
| `created()`  | Set room status → `occupied`                                                |
| `updating()` | Auto-set `updated_by`. **Throw exception** jika lease sudah closed          |
| `updated()`  | Jika status berubah ke terminated/completed → set room status → `available` |
| `deleting()` | **Throw exception** jika force delete (hanya soft delete diizinkan)         |
| `restored()` | Jika status `active` → set room status → `occupied`                         |

---

## 11. Notification

Semua notification menggunakan **database channel** (tersimpan di tabel `notifications`).

### PaymentReminderNotification

**Trigger**: Command `invoices:send-reminders` (harian 08:00)  
**Target**: User tenant yang punya invoice jatuh tempo  
**Data**:

```php
[
    'type' => 'payment_reminder',
    'reminder_type' => 'upcoming|due_today|overdue',
    'invoice_id' => int,
    'reference_number' => string,
    'amount' => decimal,
    'due_date' => 'Y-m-d',
    'month_year' => 'Y-m',
    'message' => string  // Pesan dalam Bahasa Indonesia
]
```

### MaintenanceStatusNotification

**Trigger**: Admin mengubah status maintenance request  
**Target**: User tenant yang mengajukan request  
**Data**:

```php
[
    'type' => 'maintenance_status',
    'maintenance_request_id' => int,
    'title' => string,
    'status' => 'in_progress|completed|rejected|pending',
    'message' => string,
    'admin_notes' => string|null
]
```

### NewMaintenanceRequestNotification

**Trigger**: Tenant membuat maintenance request baru  
**Target**: Semua user dengan permission `manage-maintenance`  
**Data**:

```php
[
    'type' => 'new_maintenance_request',
    'maintenance_request_id' => int,
    'title' => string,
    'priority' => 'low|medium|high',
    'category' => string,
    'room_number' => string,
    'tenant_name' => string,
    'message' => string
]
```

### NewAnnouncementNotification

**Trigger**: Admin membuat pengumuman baru  
**Target**: Seluruh user tenant (atau tenant di properti tertentu)  
**Data**:

```php
[
    'type' => 'new_announcement',
    'announcement_id' => int,
    'title' => string,
    'priority' => 'normal|important|urgent',
    'message' => string
]
```

---

## 12. Mail

### ReceiptSent (`app/Mail/ReceiptSent.php`)

- **Implements**: `ShouldQueue` (dikirim via queue)
- **Subject**: `Bukti Pembayaran - Receipt [receipt_number]`
- **View**: `emails.receipt-sent`
- **Attachment**: PDF receipt dari storage
- **Parameter**: `$receipt`, `$tenant`
- **Trigger**: Dipanggil oleh `Invoice::generateReceipt($userId)` setelah verifikasi pembayaran

---

## 13. Console Command & Scheduling

### SendPaymentReminders

```
php artisan invoices:send-reminders {--days-before=3}
```

- Cari invoice `unpaid` dengan `due_date <= today + days_before`
- Tentukan tipe reminder: `overdue` (lewat jatuh tempo), `due_today`, `upcoming`
- Kirim `PaymentReminderNotification` ke user tenant
- Skip jika tenant tidak punya akun user
- Default: 3 hari sebelum jatuh tempo

### GenerateMonthlyInvoices

```
php artisan invoices:generate {--month=}
```

- Format bulan: `YYYY-MM` (default: bulan berjalan)
- Cari semua lease aktif yang overlap dengan bulan target
- Skip jika invoice sudah ada untuk kombinasi lease+bulan tersebut
- Hitung due_date: `min(lease.due_date_per_month, daysInMonth)`
- Buat invoice dengan status `unpaid` dan reference_number otomatis

### Scheduling (`routes/console.php`)

```php
Schedule::command('invoices:send-reminders')->dailyAt('08:00');
```

Untuk mengaktifkan scheduler di production:

```bash
# Crontab entry
* * * * * cd /path/to/fluty-kos && php artisan schedule:run >> /dev/null 2>&1
```

---

## 14. Routing

### Konfigurasi

Semua route didefinisikan di `routes/web.php`. Tidak ada API routes — semua interaksi via Livewire (websocket-like).

### Route Map

```
Public (guest middleware):
  GET  /login                    → Livewire\Auth\Login

Public:
  GET  /                         → Redirect ke dashboard/login

Authenticated (auth middleware):
  GET  /dashboard                → view: dashboard
  GET  /properties               → view: properties.index       [can:viewAny,Property]
  GET  /room-types               → view: room-types.index       [can:viewAny,RoomType]
  GET  /rooms                    → view: rooms.index             [can:viewAny,Room]
  GET  /tenants                  → view: tenants.index           [can:viewAny,Tenant]
  GET  /leases                   → view: leases.index            [can:viewAny,Lease]
  GET  /invoices                 → view: invoices.index           [can:viewAny,Invoice]
  GET  /invoices/create          → view: invoices.create          [can:create,Invoice]
  GET  /invoices/{invoice}/edit  → view: invoices.edit            [can:update,invoice]
  GET  /payment-verifications    → view: admin.payment.verification
  GET  /receipts/{receipt}/download → Download PDF               [auth + ownership check]
  GET  /analytics/income         → view: admin.analytics.income
  GET  /tenant/invoices          → view: tenant.invoices.index   [can:viewAny,Invoice]
  GET  /reports                  → view: reports.index
  GET  /maintenance              → view: admin/tenant (by role)
  GET  /expenses                 → view: admin.expenses.index
  GET  /announcements            → view: admin/tenant (by role)
  GET  /settings                 → view: admin.settings.index
```

### Pola Routing

- Route hanya mengembalikan **view** — logic ada di Livewire component
- Authorization via **middleware** (`can:`) yang mengacu ke **Policy**
- Beberapa route menggunakan **role-conditional view** (maintenance, announcements)
- Download receipt memiliki **manual auth check** (ownership verification)

---

## 15. Frontend (Blade, Tailwind, Vite)

### Layout System

#### Layout Utama (`resources/views/layouts/app.blade.php`)

- Sidebar navigasi (Livewire component)
- Notification dropdown (Livewire component)
- Header dengan info user
- Main content area (`{{ $slot }}`)
- Livewire scripts & styles
- Chart.js CDN inclusion

#### Layout Guest (`resources/views/layouts/guest.blade.php`)

- Layout minimal untuk halaman login
- Tanpa sidebar/header

### Tailwind CSS v4

- Entry point: `resources/css/app.css`
- Menggunakan `@import "tailwindcss"` (v4 syntax)
- Plugin: `@tailwindcss/vite` untuk integrasi build
- Font: Poppins (Google Fonts) — digunakan di layout
- Custom theme via CSS variables
- Include pagination views dari Laravel framework

### Vite Configuration

```javascript
// vite.config.js
import { defineConfig } from "vite";
import laravel from "laravel-vite-plugin";
import tailwindcss from "@tailwindcss/vite";

export default defineConfig({
    plugins: [
        laravel({
            input: ["resources/css/app.css", "resources/js/app.js"],
            refresh: true,
        }),
        tailwindcss(),
    ],
});
```

### JavaScript

- `resources/js/app.js` — Import bootstrap.js
- `resources/js/bootstrap.js` — Setup Axios dengan `X-Requested-With: XMLHttpRequest` header
- Alpine.js — Disertakan via Livewire (sidebar toggle, modal, dropdown)
- Chart.js 4.4.0 — Via CDN, digunakan di dashboard untuk income chart

### Daftar Blade Views (~62 file)

```
resources/views/
├── layouts/
│   ├── app.blade.php               # Layout utama
│   └── guest.blade.php             # Layout login
├── dashboard.blade.php             # Container dashboard
├── welcome.blade.php               # Halaman publik
├── properties/index.blade.php
├── room-types/index.blade.php
├── rooms/index.blade.php
├── tenants/index.blade.php
├── leases/index.blade.php
├── invoices/
│   ├── index.blade.php
│   ├── create.blade.php
│   └── edit.blade.php
├── reports/
│   ├── index.blade.php
│   └── pdf/
│       ├── outstanding.blade.php    # Template PDF laporan
│       └── payment-recap.blade.php  # Template PDF rekap
├── receipts/
│   └── pdf.blade.php               # Template PDF kuitansi
├── emails/
│   └── receipt-sent.blade.php       # Template email kuitansi
├── admin/
│   ├── payment/verification.blade.php
│   ├── analytics/income.blade.php
│   ├── maintenance/index.blade.php
│   ├── expenses/index.blade.php
│   ├── announcements/index.blade.php
│   └── settings/index.blade.php
├── tenant/
│   ├── invoices/index.blade.php
│   ├── maintenance/index.blade.php
│   └── announcements/index.blade.php
└── livewire/                        # Livewire component views
    ├── dashboard.blade.php
    ├── sidebar.blade.php
    ├── notification-dropdown.blade.php
    ├── auth/login.blade.php, logout.blade.php
    ├── property/*.blade.php
    ├── room-type/*.blade.php
    ├── room/*.blade.php
    ├── tenant/*.blade.php
    ├── lease/*.blade.php
    ├── invoice/*.blade.php
    ├── admin/*.blade.php
    └── tenant/*.blade.php
```

---

## 16. Service Provider

### AppServiceProvider (`app/Providers/AppServiceProvider.php`)

```php
public function boot(): void
{
    // 1. Register model observers
    Invoice::observe(InvoiceObserver::class);
    Lease::observe(LeaseObserver::class);

    // 2. Gunakan Tailwind pagination views
    Paginator::useTailwind();

    // 3. Share variabel ke semua views
    View::composer('*', function ($view) {
        if (Schema::hasTable('settings')) {
            $view->with('appName', Setting::getValue('app_name'));
            $view->with('appTagline', Setting::getValue('app_tagline'));
        } else {
            $view->with('appName', 'Fluty Kos');
            $view->with('appTagline', 'Sistem Manajemen Kos');
        }
    });
}
```

---

## 17. Testing

### Framework & Konfigurasi

- **Framework**: Pest PHP ^3.8 + pest-plugin-laravel ^3.2
- **Test runner**: `composer test` atau `php artisan test`
- **Total**: 156 test, 283 assertion (semua passing)
- **Database**: SQLite in-memory (`:memory:`)
- **Trait**: `RefreshDatabase` — migrate setiap test

### Environment Testing (`phpunit.xml`)

```xml
<env name="APP_ENV" value="testing"/>
<env name="DB_CONNECTION" value="sqlite"/>
<env name="DB_DATABASE" value=":memory:"/>
<env name="CACHE_STORE" value="array"/>
<env name="QUEUE_CONNECTION" value="sync"/>
<env name="SESSION_DRIVER" value="array"/>
```

### Struktur Test

```
tests/
├── Pest.php                         # Konfigurasi Pest
├── TestCase.php                     # Base test case (Laravel)
├── Feature/
│   ├── ExampleTest.php
│   ├── Auth/
│   │   ├── LoginTest.php            # Test login flow
│   │   └── AuthorizationTest.php    # Test authorization
│   ├── Livewire/
│   │   ├── DashboardTest.php        # Test dashboard component
│   │   ├── MaintenanceIndexTest.php
│   │   ├── SettingIndexTest.php
│   │   ├── AnnouncementIndexTest.php
│   │   └── ExpenseIndexTest.php
│   └── Policies/
│       └── PolicyTest.php           # Test semua policy
└── Unit/
    ├── ExampleTest.php
    └── Models/
        ├── UserTest.php
        ├── PropertyTest.php
        ├── RoomTypeTest.php
        ├── RoomTest.php
        ├── TenantTest.php
        ├── LeaseTest.php
        ├── InvoiceTest.php
        ├── ExpenseTest.php
        ├── AnnouncementTest.php
        ├── MaintenanceRequestTest.php
        └── SettingTest.php
```

### Menjalankan Test

```bash
# Semua test
composer test

# Atau langsung
php artisan test

# Test spesifik
php artisan test --filter=InvoiceTest

# Test dengan coverage
php artisan test --coverage

# Test paralel
php artisan test --parallel
```

### Pola Test

**Unit Test (Model)**:

```php
it('has correct fillable attributes', function () {
    $model = new Invoice();
    expect($model->getFillable())->toBe([...]);
});

it('belongs to lease', function () {
    $invoice = Invoice::factory()->create();
    expect($invoice->lease)->toBeInstanceOf(Lease::class);
});
```

**Feature Test (Livewire)**:

```php
it('renders dashboard for owner', function () {
    $user = User::factory()->create();
    $user->assignRole('owner');

    Livewire::actingAs($user)
        ->test(Dashboard::class)
        ->assertStatus(200);
});
```

**Policy Test**:

```php
it('allows owner to delete property', function () {
    $owner = User::factory()->create();
    $owner->assignRole('owner');
    $property = Property::factory()->create();

    expect($owner->can('delete', $property))->toBeTrue();
});
```

---

## 18. Factory & Seeder

### Factories (10 factory)

| Factory                     | Fields                                                                               | States                                     |
| --------------------------- | ------------------------------------------------------------------------------------ | ------------------------------------------ |
| `UserFactory`               | name, email, password (`password`), verified                                         | `unverified()`                             |
| `PropertyFactory`           | name (Company + Kos), address, description, status=active                            | -                                          |
| `RoomTypeFactory`           | property_id, name, price, facilities                                                 | -                                          |
| `RoomFactory`               | room_type_id, room_number, floor, status=available                                   | -                                          |
| `TenantFactory`             | name, email, nik (16 digit), phone, emergency_contact, status=active                 | `withUser(?User)`                          |
| `LeaseFactory`              | tenant_id, room_id, start_date, end_date, due_date_per_month, deposit, status=active | `pending()`, `completed()`, `terminated()` |
| `InvoiceFactory`            | lease_id, amount, month_year, status=unpaid, reference_number, due_date              | -                                          |
| `ExpenseFactory`            | property_id, title, description, amount, expense_date, category                      | -                                          |
| `MaintenanceRequestFactory` | tenant_id, room_id, title, description, category, priority, status                   | -                                          |
| `AnnouncementFactory`       | property_id, created_by, title, content, priority, target, published_at              | -                                          |

### Seeders (10 seeder)

#### Urutan Eksekusi (DatabaseSeeder)

```php
// 1. Roles & Permissions
RoleAndPermissionSeeder::class

// 2. Test Users
User::create(['name' => 'Owner', 'email' => 'owner@fluty.test', ...])
    ->assignRole('owner');
User::create(['name' => 'Manager', 'email' => 'manager@fluty.test', ...])
    ->assignRole('manager');

// 3-10. Data seeders (urutan)
PropertySeeder::class       // Properti kos
RoomSeeder::class           // Kamar
TenantSeeder::class         // Penyewa + user account
LeaseSeeder::class          // Kontrak sewa
InvoiceSeeder::class        // Tagihan
ExpenseSeeder::class        // Pengeluaran
AnnouncementSeeder::class   // Pengumuman
SettingSeeder::class        // Konfigurasi default

// Clear permission cache
app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
```

#### Akun Default Setelah Seed

| Email                 | Password   | Role    |
| --------------------- | ---------- | ------- |
| `owner@fluty.test`    | `password` | owner   |
| `manager@fluty.test`  | `password` | manager |
| _(dari TenantSeeder)_ | `password` | tenant  |

---

## 19. Alur Bisnis Utama

### 1. Alur Pembuatan Invoice

```
1. Admin buat lease (tenant + room + periode)
   └── Observer: Room status → 'occupied'

2. Admin generate invoice (manual atau via command)
   ├── Hitung amount dari room_type.price
   ├── Generate reference_number: INV-YYYYMMDD-XXXX
   ├── Set due_date berdasarkan lease.due_date_per_month
   └── Status: 'unpaid'

3. Tenant upload bukti bayar
   └── Status: 'pending'

4. Admin verifikasi pembayaran
   ├── Approve → Status: 'paid', set verified_at & verified_by
   │   └── Observer: Generate receipt PDF + kirim email
   └── Reject → Status: 'unpaid', hapus proof_of_payment
```

### 2. Alur Lifecycle Lease

```
pending → active → completed     (normal: masa sewa habis)
                 → terminated    (abnormal: diakhiri lebih awal)
                 → cancelled     (dibatalkan sebelum mulai)

Saat lease dibuat     → Room status: 'occupied'
Saat lease berakhir   → Room status: 'available'
Saat lease di-restore → Room status: 'occupied' (jika aktif)
```

### 3. Alur Maintenance Request

```
1. Tenant buat request (title, description, category, priority)
   └── Notifikasi ke semua admin/manager

2. Admin proses request
   ├── Set status: 'in_progress' + admin_notes
   │   └── Notifikasi ke tenant
   ├── Set status: 'completed' + resolved_at/resolved_by
   │   └── Notifikasi ke tenant
   └── Set status: 'rejected' + admin_notes
       └── Notifikasi ke tenant
```

### 4. Alur Pengumuman

```
1. Admin buat announcement (title, content, priority, target)
   └── Notifikasi ke semua tenant (atau tenant di properti tertentu)

2. Tenant lihat daftar pengumuman
   └── Saat dibuka → Tandai sudah dibaca (announcement_reads)

3. Announcement otomatis tidak tampil jika:
   - is_active = false
   - published_at > today
   - expires_at < today
```

### 5. Alur Payment Reminder (Otomatis)

```
Scheduler (08:00 setiap hari):
  └── Command: invoices:send-reminders --days-before=3

1. Cari invoice unpaid dengan due_date <= today + 3 hari
2. Klasifikasi:
   - due_date < today     → 'overdue' (terlambat)
   - due_date == today    → 'due_today' (jatuh tempo hari ini)
   - due_date > today     → 'upcoming' (akan jatuh tempo)
3. Kirim PaymentReminderNotification ke tenant
4. Skip jika tenant tidak punya akun user
```

---

## 20. Konvensi & Code Style

### Penamaan

| Elemen             | Konvensi            | Contoh                                     |
| ------------------ | ------------------- | ------------------------------------------ |
| Model              | PascalCase singular | `Invoice`, `MaintenanceRequest`            |
| Tabel              | snake_case plural   | `invoices`, `maintenance_requests`         |
| Livewire Component | PascalCase          | `InvoiceIndex`, `PaymentVerificationIndex` |
| Livewire View      | kebab-case          | `invoice-index.blade.php`                  |
| Event              | kebab-case          | `invoice-saved`, `close-modal`             |
| Permission         | kebab-case          | `view-invoices`, `manage-maintenance`      |
| Role               | lowercase           | `owner`, `manager`, `tenant`               |
| Migration          | timestamp prefix    | `0001_01_01_000000_create_*`               |

### Code Formatter

```bash
# Format kode dengan Laravel Pint
./vendor/bin/pint

# Atau via composer
composer pint
```

Laravel Pint menggunakan preset **Laravel** (PSR-12 + Laravel conventions).

### Arsitektur Patterns

1. **No Controllers** — Semua logika di Livewire components
2. **Observer Pattern** — Business rules di observer (InvoiceObserver, LeaseObserver)
3. **Policy Pattern** — Authorization di Policy classes
4. **Event-Driven** — Komunikasi antar component via Livewire events
5. **Service Model** — Setting model sebagai service layer (cache + defaults)
6. **Soft Deletes** — Data penting tidak benar-benar dihapus
7. **Audit Trail** — `created_by`, `updated_by`, `verified_by` tracking

### Bahasa

- **UI / Label**: Bahasa Indonesia
- **Kode / Variable**: Bahasa Inggris
- **Database column**: Bahasa Inggris
- **Permission & Role names**: Bahasa Inggris
- **Pesan notifikasi**: Bahasa Indonesia

---

## 21. Deployment

### Production Checklist

```bash
# 1. Set environment
APP_ENV=production
APP_DEBUG=false

# 2. Install dependencies (tanpa dev)
composer install --optimize-autoloader --no-dev

# 3. Build frontend
npm ci
npm run build

# 4. Generate cache
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache

# 5. Jalankan migrasi
php artisan migrate --force

# 6. Seed (pertama kali saja)
php artisan db:seed --class=RoleAndPermissionSeeder
php artisan db:seed --class=SettingSeeder

# 7. Setup storage link
php artisan storage:link

# 8. Set permission
chmod -R 775 storage bootstrap/cache
```

### Scheduler (Crontab)

```
* * * * * cd /path/to/fluty-kos && php artisan schedule:run >> /dev/null 2>&1
```

### Queue Worker (Supervisor)

```ini
[program:fluty-kos-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /path/to/fluty-kos/artisan queue:work --sleep=3 --tries=3 --max-time=3600
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=www-data
numprocs=1
redirect_stderr=true
stdout_logfile=/path/to/fluty-kos/storage/logs/worker.log
stopwaitsecs=3600
```

### Database Production

Ubah `.env` ke MySQL/MariaDB:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=fluty_kos
DB_USERNAME=fluty_user
DB_PASSWORD=<secure_password>
```

### Web Server (Nginx)

```nginx
server {
    listen 80;
    server_name flutykos.com;
    root /path/to/fluty-kos/public;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    index index.php;

    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
        fastcgi_hide_header X-Powered-By;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

---

## Referensi Cepat

### Perintah Berguna

```bash
# Development
composer dev                          # Jalankan semua dev server
composer test                         # Jalankan semua test
./vendor/bin/pint                     # Format kode

# Artisan
php artisan migrate                   # Jalankan migrasi
php artisan migrate:fresh --seed      # Reset + seed database
php artisan db:seed                   # Seed semua
php artisan tinker                    # REPL interaktif

# Invoice Commands
php artisan invoices:generate         # Generate invoice bulan ini
php artisan invoices:generate --month=2025-01  # Generate bulan tertentu
php artisan invoices:send-reminders   # Kirim reminder
php artisan invoices:send-reminders --days-before=7

# Cache
php artisan cache:clear               # Clear cache
php artisan config:clear              # Clear config cache
php artisan permission:cache-reset    # Reset Spatie permission cache

# Debug
php artisan route:list                # Lihat semua routes
php artisan model:show Invoice        # Detail model
php artisan pail                      # Tail logs
```

### File Penting untuk Developer Baru

| File                                           | Fungsi                                                   |
| ---------------------------------------------- | -------------------------------------------------------- |
| `routes/web.php`                               | Semua route definitions                                  |
| `app/Providers/AppServiceProvider.php`         | Observer registration, view composers                    |
| `database/seeders/RoleAndPermissionSeeder.php` | Roles & permissions definition                           |
| `app/Observers/InvoiceObserver.php`            | Business rules invoice                                   |
| `app/Observers/LeaseObserver.php`              | Business rules lease                                     |
| `app/Models/Setting.php`                       | Setting management with cache                            |
| `app/Models/Invoice.php`                       | Invoice methods (generateReceipt, generateInvoiceNumber) |

---

_Dokumen ini di-generate berdasarkan kode sumber aktual. Update dokumen ini setiap kali ada perubahan arsitektur signifikan._
