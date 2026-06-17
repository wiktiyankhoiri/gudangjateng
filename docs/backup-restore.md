# Backup & Restore Database

## Gambaran Umum

GudangJateng memiliki dua mekanisme backup:

1. **Backup Aplikasi** — melalui UI atau artisan command, menghasilkan file `.sql` di `storage/app/backups/`
2. **Backup VPS** — melalui `pg_dump` langsung di server, menghasilkan file `.sql` di `/home/ubuntu/backups/`

Kedua mekanisme saling melengkapi. Backup aplikasi lebih mudah diakses melalui UI, sementara backup VPS lebih lengkap (termasuk schema, indexes, dan constraints secara native).

## Backup Manual

### Melalui UI

1. Login ke aplikasi
2. Menu **Pengaturan > Backup Database**
3. Klik tombol **Backup Sekarang**
4. File backup muncul di tabel dengan nama `backup-YYYY-MM-DD-HHMMSS.sql`

### Melalui Artisan / Tinker

```bash
cd /var/www/gudangjateng

# Via tinker
php artisan tinker
>>> $sql = app(App\Services\DatabaseBackupService::class)->generateDump();
>>> file_put_contents(storage_path('app/backups/manual-backup.sql'), $sql);
```

### Melalui pg_dump (VPS-level)

```bash
# Backup penuh
pg_dump -U gudangjateng gudangjateng > /home/ubuntu/backups/backup-$(date +%Y%m%d-%H%M%S).sql

# Backup dengan format custom (lebih kecil, bisa selective restore)
pg_dump -U gudangjateng -Fc gudangjateng > /home/ubuntu/backups/backup-$(date +%Y%m%d).dump
```

## Backup Otomatis

Backup otomatis dijadwalkan melalui Laravel Scheduler:

| Command | Jadwal | Keterangan |
|---------|--------|------------|
| `app:auto-backup` | Harian, 16:00 WIB | Backup database ke `storage/app/backups/` |
| `app:prune-audit-logs` | Harian | Hapus audit log >90 hari |
| `app:cleanup-old-logs` | Mingguan | Hapus file log Laravel >30 hari |

### Rotasi Backup

- Backup otomatis menyimpan maksimal **15 file** terakhir (via UI)
- Backup otomatis via cron menghapus file **>30 hari**
- Backup manual tidak terhapus otomatis

### Verifikasi Crontab

```bash
crontab -l
# Harus ada:
# * * * * * cd /var/www/gudangjateng && php artisan schedule:run >> /dev/null 2>&1
```

## Restore Database

### Melalui UI

1. Login ke aplikasi sebagai admin
2. Menu **Pengaturan > Restore Database**
3. Pilih file backup dari dropdown
4. Centang "Saya sudah melakukan backup terbaru"
5. Ketik **RESTORE** di kolom konfirmasi
6. Klik **Restore Database**
7. Konfirmasi di modal dialog

### Alur Restore

```
1. Validasi input (file, konfirmasi)
2. Buat safety backup otomatis (safety-backup-TIMESTAMP.sql)
3. Parse file SQL backup
4. Jalankan dalam SATU TRANSACTION:
   ├── Phase 1: DROP TABLE (hapus semua tabel)
   ├── Phase 2: CREATE TABLE (buat tabel baru, tanpa FK)
   ├── Phase 3: INSERT DATA (masukkan data)
   ├── Phase 4: ALTER TABLE (tambah FK + UNIQUE constraints)
   ├── Phase 5: CREATE INDEX
   └── Phase 6: SYNC SEQUENCES
5. COMMIT jika semua berhasil
6. ROLLBACK jika ada query yang gagal
```

### Safety Backup

Sebelum restore, sistem otomatis membuat safety backup dengan nama `safety-backup-YYYY-MM-DD-HHMMSS.sql`. File ini berisi dump database **sebelum** restore dilakukan. Jika restore bermasalah, file ini bisa digunakan untuk mengembalikan ke kondisi sebelumnya.

### Atomic Restore

Restore dijalankan dalam **satu transaction PostgreSQL**. Jika ada query yang gagal:
- Seluruh restore di-**rollback**
- Database kembali ke kondisi sebelum restore
- User mendapat pesan error dengan detail query yang gagal

## Sinkronisasi Sequence PostgreSQL

Setelah restore, sequence PostgreSQL disinkronkan secara otomatis di dalam transaction restore. Sequence di-set ke nilai `MAX(id)` dari masing-masing tabel.

### Manual Sync

Jika perlu sinkronisasi sequence secara manual:

```bash
cd /var/www/gudangjateng
php artisan db:sync-sequences
```

Output sukses:
```
Memulai sinkronisasi sequence...
Sinkronisasi selesai. N sequence diperbarui.
```

## Lokasi File Backup

| Lokasi | Keterangan |
|--------|------------|
| `storage/app/backups/backup-*.sql` | Backup dari aplikasi (UI + auto) |
| `storage/app/backups/safety-backup-*.sql` | Safety backup sebelum restore |
| `/home/ubuntu/backups/` | Backup VPS-level (pg_dump) |

## Verifikasi Restore Berhasil

### 1. Cek melalui UI

- Login ke aplikasi
- Buka beberapa menu (Master Data, Transaksi, Laporan)
- Pastikan data tampil dengan benar

### 2. Cek Sequence PostgreSQL

```sql
-- Bandingkan last_value sequence dengan MAX(id) tabel
SELECT schemaname, sequencename, last_value
FROM pg_sequences
ORDER BY sequencename;

-- Contoh cek spesifik
SELECT MAX(id) FROM audit_log;           -- misal: 153
SELECT currval('audit_log_id_seq');      -- harus: 153
```

### 3. Cek Foreign Key Constraints

```sql
-- Pastikan FK constraints masih ada
SELECT conname, conrelid::regclass AS table_name,
       pg_get_constraintdef(oid) AS definition
FROM pg_constraint
WHERE contype = 'f'
ORDER BY conrelid::regclass::text;
```

### 4. Cek Indexes

```sql
-- Pastikan indexes masih ada
SELECT indexname, tablename
FROM pg_indexes
WHERE schemaname = 'public'
ORDER BY tablename, indexname;
```

### 5. Cek Log Laravel

```bash
tail -50 storage/logs/laravel-$(date +%Y-%m-%d).log
```

Harus ada baris:
```
Restore berhasil: N queries executed
```

## Troubleshooting Backup/Restore

Lihat [troubleshooting.md](troubleshooting.md) untuk masalah yang pernah terjadi.
