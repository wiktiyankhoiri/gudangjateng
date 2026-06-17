# Deployment

## Workflow Deployment

Deployment dilakukan secara manual melalui SSH ke VPS dengan git pull dan clear cache.

### Langkah-langkah

```bash
# 1. SSH ke VPS
ssh ubuntu@gudangjateng.web.id

# 2. Masuk ke direktori project
cd /var/www/gudangjateng

# 3. Pull perubahan dari repository
git pull origin main

# 4. Install dependencies (jika ada perubahan composer.json / package.json)
composer install --no-dev --optimize-autoloader --no-interaction
npm ci --ignore-scripts
npm run build

# 5. Jalankan migrasi (jika ada migration baru)
php artisan migrate --force

# 6. Clear dan rebuild cache Laravel
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache

# 7. Set permission
sudo chown -R www-data:www-data storage bootstrap/cache
sudo chmod -R 775 storage bootstrap/cache

# 8. Restart PHP-FPM (jika perlu)
sudo systemctl restart php8.4-fpm
```

### Quick Deploy (tanpa perubahan dependencies)

Jika hanya ada perubahan kode PHP/Blade (tidak ada perubahan `composer.json`, `package.json`, atau migration):

```bash
cd /var/www/gudangjateng
git pull origin main
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### Quick Deploy (dengan perubahan frontend)

Jika ada perubahan file di `resources/`:

```bash
cd /var/www/gudangjateng
git pull origin main
npm run build
php artisan view:cache
```

## Checklist Setelah Deploy

- [ ] `git status` — pastikan working tree clean
- [ ] `php artisan migrate:status` — semua migration sudah dijalankan
- [ ] Buka `https://gudangjateng.web.id` — halaman login muncul
- [ ] Login dengan akun admin — dashboard terbuka
- [ ] Cek menu Backup Database — bisa membuat backup
- [ ] Cek `storage/logs/laravel.log` — tidak ada error baru
- [ ] Cek sidebar menu — semua menu berfungsi

## Rollback Deployment

Jika deployment menyebabkan masalah:

### Opsi 1: Git Revert

```bash
cd /var/www/gudangjateng

# Lihat commit terakhir
git log --oneline -5

# Revert ke commit sebelumnya
git reset --hard <commit-hash>

# Rebuild cache
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### Opsi 2: Rollback Migration

Jika migration baru menyebabkan masalah:

```bash
# Rollback migration terakhir
php artisan migrate:rollback --step=1

# Atau rollback spesifik migration
php artisan migrate:rollback --path=database/migrations/2026_06_xx_xxxxx_nama_migration.php
```

### Opsi 3: Restore Database

Jika data sudah corrupt, gunakan fitur Restore Database melalui UI atau restore dari safety backup:

```bash
# Lihat file backup tersedia
ls -la storage/app/backups/

# Restore melalui UI:
# Pengaturan > Restore Database > Pilih file > Ketik RESTORE
```

Lihat [backup-restore.md](backup-restore.md) untuk detail lengkap.

## Deployment dengan Migration + Data Change

Untuk deployment yang melibatkan migration dan perubahan data penting:

```bash
cd /var/www/gudangjateng

# 1. Backup dulu!
php artisan tinker
>>> app(App\Services\DatabaseBackupService::class)->generateDump();
# Copy output ke file, atau gunakan UI backup

# 2. Pull code
git pull origin main

# 3. Install dependencies
composer install --no-dev --optimize-autoloader --no-interaction

# 4. Migrasi
php artisan migrate --force

# 5. Sync sequences (jaga-jaga)
php artisan db:sync-sequences

# 6. Rebuild cache
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

## Environment Variables Production

Pastikan `.env` di VPS memiliki nilai production:

```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://gudangjateng.web.id

DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=gudangjateng
DB_USERNAME=gudangjateng
DB_PASSWORD=<strong-password>

SESSION_DRIVER=database
SESSION_ENCRYPT=true

CACHE_STORE=database
QUEUE_CONNECTION=database

LOG_CHANNEL=daily
LOG_LEVEL=warning

BCRYPT_ROUNDS=12

MAIL_MAILER=log
```
