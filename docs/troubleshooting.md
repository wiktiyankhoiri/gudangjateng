# Troubleshooting

Dokumentasi masalah yang pernah terjadi dan solusinya. Gunakan sebagai referensi jika menghadapi error serupa.

---

## 1. Audit Log Gagal: Sequence Tidak Sinkron

### Gejala

```
SQLSTATE[23505]: Unique violation: duplicate key value violates unique constraint "audit_log_pkey"
DETAIL: Key (id)=(10) already exists.
```

Audit log (dan tabel lain) gagal INSERT karena sequence PostgreSQL menghasilkan ID yang sudah terpakai.

### Penyebab

Setelah restore database, sequence PostgreSQL tidak otomatis mengikuti nilai `MAX(id)` dari data yang di-restore. Contoh:
- `audit_log` memiliki `MAX(id) = 153`
- `audit_log_id_seq` masih bernilai `9`
- INSERT baru mencoba `id = 10` → konflik

### Solusi

```bash
# Sinkronisasi manual
cd /var/www/gudangjateng
php artisan db:sync-sequences
```

Atau via SQL langsung:

```sql
SELECT setval('audit_log_id_seq', COALESCE((SELECT MAX(id) FROM audit_log), 1));
```

### Verifikasi

```sql
SELECT MAX(id) FROM audit_log;
SELECT currval('audit_log_id_seq');
-- Kedua nilai harus sama
```

### Pencegahan

Proses restore sekarang otomatis menyinkronkan sequence di dalam transaction. Masalah ini tidak seharusnya terjadi lagi setelah update.

---

## 2. Restore Gagal: session_replication_role

### Gejala

```
SQLSTATE[42501]: Insufficient privilege
permission denied to set parameter "session_replication_role"
SQL: SET session_replication_role = 'replica'
```

### Penyebab

`SET session_replication_role = 'replica'` memerlukan privilege **superuser** di PostgreSQL. User database aplikasi bukan superuser, sehingga perintah ini ditolak.

Command ini sebelumnya digunakan untuk menonaktifkan pengecekan foreign key selama restore.

### Solusi

Kode telah diperbaiki. Restore sekarang menggunakan urutan fase yang tidak memerlukan penonaktifan FK:

1. DROP TABLE
2. CREATE TABLE (tanpa FK)
3. INSERT DATA (belum ada FK, jadi tidak ada violation)
4. ALTER TABLE ADD CONSTRAINT (FK ditambahkan setelah data ada)
5. CREATE INDEX
6. SYNC SEQUENCES

### File yang Diubah

- `app/Services/DatabaseBackupService.php` — hapus `session_replication_role`, restructure fase restore

---

## 3. Restore Gagal: DROP TABLE Tidak Terdeteksi

### Gejala

```
Restore CREATE failed: ERROR: relation "users" already exists
```

CREATE TABLE dijalankan padahal tabel lama belum dihapus.

### Penyebab

File backup dimulai dengan header comment:

```sql
-- Database Backup
-- Created: 2025-01-01 12:00:00
-- Database: gudangjateng

DROP TABLE IF EXISTS users CASCADE;
```

Parser SQL menggunakan `preg_split` pada semicolon. Chunk pertama berisi header comment + DROP TABLE pertama:

```
-- Database Backup\n...\nDROP TABLE IF EXISTS users CASCADE
```

Karena chunk dimulai dengan `--`, seluruh chunk (termasuk DROP TABLE) di-skip oleh filter comment.

### Solusi

Comment di-strip **sebelum** splitting, bukan per-chunk:

```php
// BEFORE (bug)
foreach ($rawQueries as $query) {
    if (str_starts_with($query, '--')) continue; // DROP ikut ter-skip!
}

// AFTER (fix)
$sql = preg_replace('/^--.*$/m', '', $sql);  // strip comments dulu
$rawQueries = preg_split('/;(?:\s*[\r\n]|$)/', $sql);
```

### Verifikasi

Cek log setelah restore:

```
Restore phases: DROP=18 CREATE=18 ALTER=25 INDEX=12 INSERT=1543
```

Jika `DROP > 0`, parser berjalan benar.

---

## 4. Restore Gagal: DISABLE TRIGGER ALL Butuh Superuser

### Gejala

```
SQLSTATE[42501]: Insufficient privilege
permission denied: "RI_ConstraintTrigger_xxx" is a system trigger
SQL: ALTER TABLE barang DISABLE TRIGGER ALL
```

### Penyebab

`ALTER TABLE ... DISABLE TRIGGER ALL` juga memerlukan privilege superuser (atau minimal table owner untuk trigger non-system). Foreign key triggers di PostgreSQL adalah system triggers yang tidak bisa dinonaktifkan oleh user biasa.

### Solusi

Mekanisme DISABLE/ENABLE TRIGGER dihapus seluruhnya. Diganti dengan urutan fase yang benar:

```
BEFORE (butuh superuser)          AFTER (non-superuser OK)
─────────────────────────         ─────────────────────────
1. DROP TABLE                     1. DROP TABLE
2. CREATE TABLE                   2. CREATE TABLE (tanpa FK)
3. ALTER TABLE (FK)               3. INSERT DATA
4. CREATE INDEX                   4. ALTER TABLE (FK + UNIQUE)
5. DISABLE TRIGGER ALL ← ❌       5. CREATE INDEX
6. INSERT DATA                    6. SYNC SEQUENCES
7. ENABLE TRIGGER ALL  ← ❌
8. SYNC SEQUENCES
```

Dengan urutan baru, INSERT dilakukan **sebelum** FK constraints ditambahkan, sehingga tidak ada constraint yang perlu dinonaktifkan.

### File yang Diubah

- `app/Services/DatabaseBackupService.php` — reorder fase restore, hapus DISABLE/ENABLE TRIGGER

---

## 5. Delete Backup Gagal: POST vs DELETE Route

### Gejala

```
Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException
The POST method is not supported for route: pengaturan/backup/delete/{filename}
Supported methods: DELETE
```

### Penyebab

Route delete backup terdaftar sebagai `DELETE`:

```php
Route::delete('backup/delete/{filename}', ...)->name('backup.delete');
```

Tapi form di Blade menggunakan `method="post"` tanpa `@method('DELETE')`:

```html
<form method="post" action="{{ route('pengaturan.backup.delete', ...) }}">
    @csrf
    <!-- Missing: @method('DELETE') -->
</form>
```

### Solusi

Tambahkan `@method('DELETE')` di form:

```html
<form method="post" action="{{ route('pengaturan.backup.delete', ...) }}">
    @csrf
    @method('DELETE')
</form>
```

### File yang Diubah

- `resources/views/pengaturan/backup-restore/backup.blade.php` — tambah `@method('DELETE')`

---

## 6. Permission PostgreSQL Setelah Restore

### Gejala

Tabel berhasil di-restore tapi aplikasi tidak bisa read/write.

### Penyebab

Owner tabel berubah setelah CREATE TABLE (misalnya restore dijalankan oleh user yang berbeda).

### Solusi

```sql
-- Cek owner tabel
SELECT tablename, tableowner FROM pg_tables WHERE schemaname = 'public';

-- Perbaiki owner (ganti 'gudangjateng' dengan username DB yang benar)
DO $$
DECLARE
    r RECORD;
BEGIN
    FOR r IN SELECT tablename FROM pg_tables WHERE schemaname = 'public'
    LOOP
        EXECUTE 'ALTER TABLE ' || quote_ident(r.tablename) || ' OWNER TO gudangjateng';
    END LOOP;
END $$;
```

### Pencegahan

Pastikan restore selalu dijalankan menggunakan user database yang sama dengan user aplikasi.

---

## 7. Langkah Pengecekan Sequence PostgreSQL

Gunakan langkah ini untuk memverifikasi bahwa semua sequence sudah sinkron setelah restore atau operasi lainnya.

### Cek Semua Sequence

```sql
SELECT
    schemaname,
    sequencename,
    last_value,
    CASE
        WHEN last_value IS NULL THEN 'EMPTY'
        ELSE last_value::text
    END AS status
FROM pg_sequences
WHERE schemaname = 'public'
ORDER BY sequencename;
```

### Bandingkan Sequence vs MAX(id)

```sql
-- Untuk setiap tabel yang punya sequence, bandingkan:
SELECT 'audit_log' AS table_name,
       MAX(id) AS max_id,
       (SELECT last_value FROM pg_sequences WHERE sequencename = 'audit_log_id_seq') AS seq_value
FROM audit_log

UNION ALL

SELECT 'users',
       MAX(id),
       (SELECT last_value FROM pg_sequences WHERE sequencename = 'users_id_seq')
FROM users

UNION ALL

SELECT 'barang',
       MAX(id),
       (SELECT last_value FROM pg_sequences WHERE sequencename = 'barang_id_seq')
FROM barang;

-- max_id dan seq_value harus sama untuk setiap tabel
```

### Fix Manual Per-Tabel

```sql
-- Ganti 'nama_tabel' dan 'nama_tabel_id_seq' sesuai kebutuhan
SELECT setval('nama_tabel_id_seq', COALESCE((SELECT MAX(id) FROM nama_tabel), 1));
```

### Fix Semua Sequence Sekaligus

```bash
cd /var/www/gudangjateng
php artisan db:sync-sequences
```

---

## Log Error

Semua error dicatat di:

```
storage/logs/laravel-YYYY-MM-DD.log
```

### Cek Error Terbaru

```bash
# Error hari ini
tail -100 storage/logs/laravel-$(date +%Y-%m-%d).log

# Cari error spesifik
grep -i "restore failed" storage/logs/laravel-*.log

# Cari error sequence
grep -i "sequence" storage/logs/laravel-*.log
```

### Cek Error di Database

```bash
# Log restore/backup ada di tabel audit_log
SELECT * FROM audit_log
WHERE action IN ('restore', 'backup')
ORDER BY created_at DESC
LIMIT 10;
```

---

## 8. Reset Password: Email Tidak Terkirim

### Gejala

User klik "Kirim Link Reset" tapi email tidak pernah masuk ke inbox.

### Penyebab

`MAIL_MAILER=log` — email hanya ditulis ke file log, tidak benar-benar dikirim. Ini setting default untuk development local.

### Solusi: Setup Gmail SMTP di Production

#### Langkah 1: Buat App Password di Google

1. Login ke akun Gmail yang akan digunakan
2. Buka https://myaccount.google.com/security
3. Aktifkan **2-Step Verification** (jika belum)
4. Buka https://myaccount.google.com/apppasswords
5. Buat App Password baru → pilih "Mail" → copy 16 karakter password

#### Langkah 2: Update `.env` di VPS

```bash
cd /var/www/gudangjateng
nano .env
```

Ubah bagian MAIL:

```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-email@gmail.com
MAIL_PASSWORD=abcdefghijklmnop
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=your-email@gmail.com
MAIL_FROM_NAME="GudangJateng"
```

#### Langkah 3: Clear config cache

```bash
php artisan config:cache
```

#### Langkah 4: Test kirim email

```bash
php artisan tinker
>>> $user = App\Models\User::first();
>>> $user->notify(new Illuminate\Auth\Notifications\ResetPassword('test-token'));
```

Cek inbox Gmail tujuan. Jika email masuk, setup berhasil.

### Troubleshooting SMTP

| Masalah | Solusi |
|---------|--------|
| Connection refused | Pastikan port 587 tidak diblokir firewall VPS |
| Authentication failed | Pastikan App Password benar (bukan password Gmail biasa) |
| Email masuk spam | Verifikasi domain pengirim di Gmail |
| Timeout | Coba `MAIL_PORT=465` dan `MAIL_ENCRYPTION=ssl` |

