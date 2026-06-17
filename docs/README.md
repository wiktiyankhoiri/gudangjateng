# Dokumentasi GudangJateng

GudangJateng adalah aplikasi manajemen stok gudang berbasis web yang dibangun dengan Laravel dan PostgreSQL. Aplikasi ini mengelola data barang, transaksi masuk/keluar, mutasi antar gudang, stok opname, serta pelaporan.

## Teknologi

| Komponen | Teknologi |
|----------|-----------|
| Framework | Laravel 13 |
| PHP | 8.4 |
| Database | PostgreSQL |
| Web Server | Nginx |
| Frontend | Tailwind CSS 4, Alpine.js, Vite |
| Charts | ApexCharts |
| Export | PhpSpreadsheet (Excel) |
| Auth | Custom (session-based) |
| SSL | Let's Encrypt |
| OS | Ubuntu 24.04 |

## Repository

```
https://github.com/wiktiyankhoiri/gudangjateng.git
```

## Domain

```
https://gudangjateng.web.id
```

## Struktur Dokumentasi

| File | Isi |
|------|-----|
| [server.md](server.md) | Informasi server, domain, SSL, folder, service |
| [deployment.md](deployment.md) | Workflow deployment, rollback, checklist |
| [backup-restore.md](backup-restore.md) | Backup manual/otomatis, restore, verifikasi |
| [troubleshooting.md](troubleshooting.md) | Masalah yang pernah terjadi dan solusinya |

## Role User

| Role | Akses |
|------|-------|
| `super_admin` | Semua fitur |
| `admin` | Master data, transaksi, backup/restore, user management |
| `audit` | Penyesuaian stok, stok opname, audit log, laporan |
| `manager` | Laporan, detail transaksi |
| `sales` | Laporan sales stok, detail barang masuk/keluar |

## Setup Lokal (Development)

```bash
# 1. Clone repository
git clone https://github.com/wiktiyankhoiri/gudangjateng.git
cd gudangjateng

# 2. Install dependencies
composer install
npm install --ignore-scripts

# 3. Setup environment
cp .env.example .env
php artisan key:generate

# 4. Setup database PostgreSQL
createdb gudangjateng
# Edit .env: DB_DATABASE, DB_USERNAME, DB_PASSWORD

# 5. Migrasi
php artisan migrate

# 6. Build frontend
npm run build

# 7. Jalankan development server
composer dev
```

Atau gunakan script otomatis:

```bash
composer setup
```

## Artisan Commands

| Command | Deskripsi | Jadwal |
|---------|-----------|--------|
| `app:auto-backup` | Backup database otomatis | Harian, 16:00 |
| `app:prune-audit-logs` | Hapus audit log lama (>90 hari) | Harian |
| `app:cleanup-old-logs` | Hapus file log Laravel lama (>30 hari) | Mingguan |
| `db:sync-sequences` | Sinkronisasi sequence PostgreSQL | Manual |
