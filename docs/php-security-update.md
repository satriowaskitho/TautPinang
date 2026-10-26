# Tindak lanjut php2shell

Tanggal perubahan: 23 Oktober 2026.

## Ruang lingkup

Advisori PHP tanggal 21 Oktober 2026 menyatakan bahwa CVE-2026-109618 dan
CVE-2026-109619 dapat menyebabkan RCE tanpa autentikasi. PHP-FPM juga terdampak.
Perubahan aplikasi, autentikasi, atau konfigurasi saja tidak menambal runtime.

Versi pertama yang diperbaiki menurut advisori: PHP 8.2.35, 8.3.36, 8.4.27,
dan 8.5.12. Project ini tetap memakai cabang 8.3 yang sebelumnya digunakan
Dockerfile, dengan minimum **8.3.36**, untuk membatasi perubahan kompatibilitas.
Upgrade cabang PHP atau framework berikutnya memerlukan pengujian tersendiri.

## Perubahan repository

- Image CLI dan FPM-NGINX dikunci ke PHP 8.3.36 dengan digest multi-architecture.
- Composer menolak PHP di bawah 8.3.36 serta cabang selain 8.3. Pemeriksaan
  platform dijalankan terhadap runtime build dan produksi yang sebenarnya.
- `session.upload_progress.enabled=0` dimuat melalui PHP INI sebelum parsing
  request sebagai pertahanan tambahan, bukan pengganti upgrade.
- `.dockerignore` mengecualikan `.env`, kredensial Composer, dependensi lokal,
  dan cache agar tidak ikut masuk ke image hasil build.
- Instalasi Composer menunda script Artisan sampai source aplikasi tersedia;
  aset frontend dibangun dari lockfile dengan `npm ci`.

## Deployment berbasis container

1. Catat versi image dan PHP yang sedang berjalan serta siapkan backup database
   dan file unggahan. Simpan konfigurasi dan volume persisten yang sudah ada.
2. Ambil commit perbaikan, lalu bangun image baru dengan base image segar:

   ```sh
   docker build --pull --no-cache -t tautpinang:php-8.3.36 .
   docker run --rm --entrypoint php tautpinang:php-8.3.36 -v
   docker run --rm --entrypoint php-fpm tautpinang:php-8.3.36 -v
   docker run --rm --entrypoint composer tautpinang:php-8.3.36 check-platform-reqs --no-dev
   ```

3. Deploy image baru melalui platform yang biasa dipakai, dengan environment,
   port, jaringan, database, dan volume unggahan yang sama. Gunakan
   `APP_ENV=production` dan `APP_DEBUG=false`. Jangan jalankan migrasi reset.
4. Ganti seluruh container PHP-FPM dan restart/recreate semua queue worker,
   scheduler, serta instance/replica lain yang memakai runtime lama.
5. Periksa **container yang benar-benar melayani trafik**, bukan hanya image baru:

   ```sh
   docker exec <container-aplikasi> php -v
   docker exec <container-aplikasi> php-fpm -v
   docker exec <container-aplikasi> composer check-platform-reqs --no-dev
   docker exec <container-aplikasi> php-fpm -i
   ```

   CLI dan FPM harus menunjukkan PHP 8.3.36 atau patch 8.3 lebih baru. Pada
   output FPM, pastikan `session.upload_progress.enabled` bernilai `Off`.
   Periksa juga konfigurasi pool atau mount yang dapat menimpa PHP INI.
6. Uji login, halaman tautan publik, unggah logo, pembuatan dan pengubahan
   tautan, serta queue. Periksa health check dan log error sebelum menyatakan
   deployment selesai. Jangan rollback ke image PHP yang rentan.

Jika menggunakan Docker Compose untuk membangun Dockerfile ini, jalankan
`docker compose build --pull --no-cache <service-aplikasi>` lalu
`docker compose up -d --force-recreate <service-aplikasi>`, dan lakukan langkah
yang sama pada service worker terkait. Sesuaikan nama service di server.

Untuk pembaruan PHP berikutnya, ubah kedua tag dan digest Dockerfile bersama-sama,
perbarui batas Composer bila diperlukan, lalu ulangi build dan verifikasi.

## Deployment VM atau hosting tanpa container

Pilih PHP 8.3.36 atau patch 8.3 lebih baru dari paket vendor resmi. Perbarui
binary yang digunakan web server (Apache/mod_php atau PHP-FPM), CLI, dan worker.
Pastikan virtual host menunjuk ke runtime baru, lalu restart seluruh proses
terkait. `php -v` di terminal saja tidak membuktikan versi web server.

Jalankan `composer install --no-dev --prefer-dist --optimize-autoloader` dan
`composer check-platform-reqs --no-dev` dengan PHP yang baru. Terapkan
`session.upload_progress.enabled=0` pada konfigurasi PHP server sebelum request
diproses, lalu ulangi pengujian aplikasi. Jangan gunakan `--ignore-platform-reqs`
atau `config.platform.php` untuk menyamarkan runtime yang masih rentan.

Setelah pembaruan framework pada instalasi VM, jalankan
`php artisan optimize:clear` untuk membuang cache konfigurasi, route, dan view
dari versi sebelumnya. Buat kembali cache konfigurasi sesuai proses deployment
yang biasa digunakan setelah environment produksi dipastikan benar.

## Catatan inventaris dan pelaporan

Status runtime server produksi tidak dapat ditentukan dari source repository.
Isi data berikut setelah deployment dan pemeriksaan server selesai:

| Data | Hasil pemeriksaan |
| --- | --- |
| Nama aplikasi / URL / aset | TautPinang / isi sesuai server |
| Runtime dan versi sebelum update | Belum diverifikasi di server |
| Versi CLI dan web SAPI setelah update | Belum diverifikasi di server |
| Commit dan image digest yang dideploy | Isi dari hasil deployment |
| Tanggal, waktu, serta PIC deployment | Isi setelah deployment |
| Semua worker dan replica sudah diganti | Belum diverifikasi di server |
| Hasil uji aplikasi dan pemeriksaan log | Isi setelah pengujian |

Gunakan template BPS-CSIRT yang disebut dalam pemberitahuan untuk pelaporan.
Perubahan repository ini tidak mengirim laporan atau menyatakan server sudah
ditambal.

## Hasil verifikasi setelah pembaruan dependensi

Pembaruan lanjutan pada 23 Oktober 2026 menaikkan Laravel ke **12.70.1**,
Jetstream ke **5.5.3**, Sanctum ke **4.3.3**, Socialite ke **5.31.0**, dan
Livewire ke **3.8.11**. PHPUnit diperbarui ke **12.5.40**. Dependensi transitif
termasuk Symfony, Guzzle, CommonMark, Flysystem, JWT, serta phpseclib diperbarui
melalui Composer tanpa pengecualian advisori atau platform requirement.

Hasil pengujian lokal menggunakan PHP 8.3.36:

- `composer audit --locked`: **0 advisori, 0 paket abandoned**, termasuk paket
  development. Audit awal sebelumnya mencatat 53 advisori pada 16 paket.
- `composer validate --strict --no-check-publish`: lulus; constraint Socialite
  kini dibatasi pada major yang kompatibel.
- `composer check-platform-reqs`: lulus untuk seluruh paket.
- `php vendor/bin/phpunit --fail-on-warning`: **40 test, 90 assertions,
  33 lulus, 7 dilewati, 0 gagal**. Test yang dilewati mengikuti konfigurasi
  fitur Jetstream/Fortify, termasuk API token, verifikasi email, dan cabang
  registrasi yang tidak berlaku.
- `npm ci` dan `npm run build`: lulus. Build masih memberi warning terkait
  data Browserslist lama serta konfigurasi `darkMode` Tailwind.
- Uji browser lokal: login, live preview, menyimpan tautan, serta membuka dialog
  detail berhasil. Pengujian browser memakai database SQLite sementara.

Tiga test yang sebelumnya gagal kini lulus. Default model user disamakan dengan
schema database (`status=active`, `role=user`) agar objek baru tidak langsung
ditolak middleware; akun inactive dan disabled tetap ditolak. Test beranda
mengikuti redirect login yang memang didefinisikan aplikasi, dan test password
memastikan timestamp konfirmasi hanya dibuat untuk password yang benar.

Pengujian tambahan mencakup halaman login dan halaman setelah login,
pembuatan/perubahan tautan Livewire, halaman tautan publik, unggah logo,
pagination, akses admin, dan request Livewire nyata setelah role admin dicabut.
Custom RoleMiddleware kini diperiksa ulang pada request Livewire berikutnya.
Migrasi Livewire juga memperbarui event, binding, hook JavaScript, entanglement
modal, dan integrasi Alpine. Literal JSON-LD `@context` di-escape untuk compiler
Blade baru. Tidak ada perubahan schema atau migrasi reset database.

Dockerfile mempertahankan PHP yang sudah ditambal dan menyediakan target
`testing` serta `production`. Node.js build diperbarui ke 24. Stack Compose
menggunakan MySQL 8.4 dan volume persisten untuk database serta unggahan.
Lihat [panduan Docker](docker.md) dan workflow
[PHP runtime security](../.github/workflows/php-runtime.yml) untuk audit,
suite test, build produksi, pengujian MySQL, dan penggantian container.

[CI perubahan kode 30d2c52](https://github.com/albertassidiq/TautPinang/actions/runs/39937212011)
lulus: audit lengkap, validasi Composer, suite PHPUnit, build produksi,
verifikasi CLI/FPM, migrasi MySQL, halaman login NGINX/PHP-FPM, dan file
unggahan yang tetap tersedia setelah container aplikasi diganti.

Verifikasi repository tidak menggantikan pemeriksaan server produksi. Rebuild,
redeploy, penggantian seluruh worker, dan uji pada server tetap wajib dilakukan.

## Referensi

- [Advisori resmi PHP](https://www.php.net/archive/2026.php#2026-10-21-5)
- [CVE-2026-109618 / GHSA-w8hw-w9fx-j79w](https://github.com/php/php-src/security/advisories/GHSA-w8hw-w9fx-j79w)
- [CVE-2026-109619 / GHSA-6wmf-4rv5-p3rp](https://github.com/php/php-src/security/advisories/GHSA-6wmf-4rv5-p3rp)
- [Image serversideup/php](https://serversideup.net/open-source/docker-php/docs/getting-started/choosing-an-image)
