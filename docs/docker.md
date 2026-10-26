# Menjalankan TautPinang dengan Docker

Dockerfile menggunakan PHP 8.3.36, NGINX/PHP-FPM, Node.js 24 untuk build aset,
dan dependensi Composer dari lockfile. Image produksi berjalan sebagai
`www-data`. Target `testing` memasang dependensi pengujian dan SQLite.

## Menjalankan stack baru

Prasyarat: Docker Engine/Desktop dengan Docker Compose v2.

1. Salin `.env.docker.example` menjadi `.env.docker`:

   ```sh
   cp .env.docker.example .env.docker
   ```

   Di PowerShell: `Copy-Item .env.docker.example .env.docker`.

2. Buat kunci aplikasi melalui PHP yang sudah ditambal:

   ```sh
   docker run --rm --entrypoint php serversideup/php:8.3.36-cli -r 'echo "base64:".base64_encode(random_bytes(32)).PHP_EOL;'
   ```

   Isi hasilnya pada `APP_KEY`. Isi juga `DB_PASSWORD` dan `DB_ROOT_PASSWORD`
   dengan password kuat yang berbeda. Compose menolak berjalan jika ketiga
   nilai ini kosong. File `.env.docker` diabaikan oleh Git dan build Docker.

3. Bangun dan jalankan:

   ```sh
   docker compose --env-file .env.docker config --quiet
   docker compose --env-file .env.docker build --pull
   docker compose --env-file .env.docker up -d --wait
   docker compose --env-file .env.docker exec app php artisan migrate --force
   ```

   Aplikasi tersedia di `http://localhost:8080`. Contoh environment memakai
   `APP_ENV=local` untuk akses localhost melalui HTTP; `APP_DEBUG` tetap `false`.
   Untuk deployment produksi gunakan `APP_ENV=production`, `APP_URL=https://...`,
   dan reverse proxy HTTPS. Port app secara default hanya terikat ke loopback.

4. Buat akun melalui halaman registrasi. Konfigurasikan SMTP dan Google OAuth
   pada `.env.docker` bila dipakai. Default mailer `log` hanya untuk pengujian;
   pilih `smtp` dan isi kredensial untuk pengiriman email sungguhan.

## Data persisten dan pembaruan

Compose menyimpan MySQL di volume `database` serta file publik/unggahan di volume
`uploads`. Symlink `public/storage` disiapkan dalam image. Sesi disimpan di
container dan pengguna dapat perlu login kembali setelah container diganti.

Untuk memperbarui stack yang dibuat oleh Compose ini:

```sh
docker compose --env-file .env.docker build --pull --no-cache
docker compose --env-file .env.docker up -d --force-recreate --wait
docker compose --env-file .env.docker exec app php artisan migrate --force
docker compose --env-file .env.docker exec app php -v
docker compose --env-file .env.docker exec app php-fpm -v
docker compose --env-file .env.docker exec app composer check-platform-reqs --no-dev
```

Backup database dan unggahan sebelum memperbarui produksi. Untuk instalasi
lama di luar Compose ini, petakan konfigurasi serta data yang sudah ada terlebih
dahulu; volume Compose baru tidak otomatis mengimpor database atau unggahan lama.
Pertahankan `APP_KEY` yang sudah digunakan produksi agar data terenkripsi dan
sesi tetap kompatibel. Kunci baru hanya dibuat untuk instalasi baru.

`docker compose --env-file .env.docker down` menghentikan stack sambil menyimpan
volume. Opsi `down --volumes` menghapus database dan unggahan; jangan gunakan
opsi itu pada deployment yang datanya perlu disimpan.

## Pengujian image

```sh
docker build --pull --target testing -t tautpinang:tests .
docker run --rm --entrypoint composer tautpinang:tests audit --locked
docker run --rm --entrypoint php tautpinang:tests vendor/bin/phpunit --colors=never --fail-on-warning
```

GitHub Actions menjalankan audit seluruh dependensi, validasi Composer, suite
PHPUnit, build produksi, serta uji Compose dengan MySQL dan halaman login.
Database dan password CI bersifat sementara, terpisah dari produksi.
