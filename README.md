# Inventory Buah Cahaya Baru

Aplikasi Laravel untuk pencatatan stok buah, cetak laporan, serta notifikasi dan komunikasi tim. Database produksi diarahkan ke PostgreSQL Neon.

## Fitur

- Pencatatan master buah, stok masuk, stok keluar, dan koreksi stok.
- Laporan periode dengan halaman cetak browser.
- Notifikasi stok rendah dan buah yang mendekati kadaluarsa.
- Log komunikasi antar tim gudang, pembelian, penjualan, dan manajemen.
- Docker Compose dengan PHP-FPM, Nginx, worker queue, dan Traefik.

## Konfigurasi Neon PostgreSQL

Isi `.env` dari `.env.example`, lalu ganti nilai berikut sesuai connection string Neon:

```env
DB_CONNECTION=pgsql
DB_URL="postgresql://USER:PASSWORD@HOST/neondb?sslmode=require&channel_binding=require"
DB_HOST=your-neon-host.neon.tech
DB_PORT=5432
DB_DATABASE=neondb
DB_USERNAME=your-neon-user
DB_PASSWORD=your-neon-password
DB_SSLMODE=require
DB_CHANNEL_BINDING=require
```

## Jalan Lokal

```bash
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate --seed
php artisan serve
```

## Jalan Dengan Docker + Traefik

```bash
cp .env.example .env
docker compose up -d --build
docker compose exec app php artisan migrate --seed --force
```

Set `APP_HOST=localhost` di `.env`, lalu buka `http://localhost`. Dashboard Traefik tersedia di `http://localhost:8080`.
