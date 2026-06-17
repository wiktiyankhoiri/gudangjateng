# Server & Infrastruktur

## Informasi Server

| Item | Detail |
|------|--------|
| OS | Ubuntu 24.04 LTS |
| Domain | gudangjateng.web.id |
| SSL | Let's Encrypt (auto-renew) |
| Web Server | Nginx |
| PHP | 8.4 (FPM) |
| Database | PostgreSQL |
| Node.js | 20.x (untuk build frontend) |

## Lokasi Folder

| Path | Keterangan |
|------|------------|
| `/var/www/gudangjateng` | Root aplikasi |
| `/var/www/gudangjateng/public` | Document root Nginx |
| `/var/www/gudangjateng/storage/app/backups` | Backup database (aplikasi) |
| `/var/www/gudangjateng/storage/logs` | Log Laravel |
| `/var/www/gudangjateng/.env` | Konfigurasi environment |
| `/home/ubuntu/backups` | Backup PostgreSQL (VPS-level) |
| `/etc/nginx/sites-available/gudangjateng` | Konfigurasi Nginx |
| `/etc/letsencrypt/live/gudangjateng.web.id` | Sertifikat SSL |

## PostgreSQL

### Koneksi

```bash
# Masuk ke PostgreSQL
sudo -u postgres psql

# Masuk ke database aplikasi
psql -U gudangjateng -d gudangjateng -h 127.0.0.1
```

### Konfigurasi di `.env`

```env
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=gudangjateng
DB_USERNAME=gudangjateng
DB_PASSWORD=<password>
```

### Catatan Penting

- User PostgreSQL aplikasi **bukan superuser**
- User harus memiliki hak: CREATE TABLE, INSERT, ALTER TABLE, CREATE INDEX pada schema `public`
- Operasi yang **tidak bisa** dilakukan: `SET session_replication_role`, `ALTER TABLE ... DISABLE TRIGGER ALL`

### Backup VPS-level (pg_dump)

```bash
# Backup manual
pg_dump -U gudangjateng gudangjateng > /home/ubuntu/backups/backup-$(date +%Y%m%d).sql

# Restore dari pg_dump
psql -U gudangjateng gudangjateng < /home/ubuntu/backups/backup-20250617.sql
```

## Nginx

### Konfigurasi Dasar

```nginx
server {
    listen 80;
    listen 443 ssl http2;
    server_name gudangjateng.web.id;

    root /var/www/gudangjateng/public;
    index index.php;

    # SSL (Let's Encrypt)
    ssl_certificate /etc/letsencrypt/live/gudangjateng.web.id/fullchain.pem;
    ssl_certificate_key /etc/letsencrypt/live/gudangjateng.web.id/privkey.pem;

    # Redirect HTTP ke HTTPS
    if ($scheme = http) {
        return 301 https://$server_name$request_uri;
    }

    # Laravel routing
    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    # PHP-FPM
    location ~ \.php$ {
        fastcgi_pass unix:/run/php/php8.4-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    # Deny access to hidden files
    location ~ /\. {
        deny all;
    }
}
```

### Restart Nginx

```bash
sudo nginx -t          # Test konfigurasi
sudo systemctl reload nginx
```

## PHP

### Versi & Extension

PHP 8.4 FPM dengan extension yang diperlukan:

```
pdo_pgsql, pgsql, mbstring, zip, intl, gd, bcmath, opcache, fileinfo
```

### Restart PHP-FPM

```bash
sudo systemctl restart php8.4-fpm
```

### php.ini Production

File custom PHP config berada di:

```
docker/php/custom.ini
docker/php/opcache.ini
```

## Service yang Berjalan

| Service | Status | Port |
|---------|--------|------|
| Nginx | active | 80, 443 |
| PHP 8.4 FPM | active | unix socket |
| PostgreSQL | active | 5432 |
| Certbot (SSL renew) | timer | - |

### Cek Status Service

```bash
sudo systemctl status nginx
sudo systemctl status php8.4-fpm
sudo systemctl status postgresql
```

## Crontab

Scheduler Laravel harus terdaftar di crontab:

```bash
crontab -l
```

Harus ada baris:

```cron
* * * * * cd /var/www/gudangjateng && php artisan schedule:run >> /dev/null 2>&1
```

### Jadwal Otomatis

| Waktu | Command | Keterangan |
|-------|---------|------------|
| Harian 16:00 | `app:auto-backup` | Backup database otomatis |
| Harian | `app:prune-audit-logs` | Hapus audit log >90 hari |
| Mingguan | `app:cleanup-old-logs` | Hapus log Laravel >30 hari |
