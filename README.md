# Sistem Monitoring KKN Tematik - UKIM
**Clara Mustamu | NPM: 12155201220035**

## Cara Menjalankan

### Syarat
- Docker Desktop sudah terinstall dan berjalan
- Koneksi internet (untuk download CI4 pertama kali)

### Langkah

**1. Download/clone project ini ke komputer kamu**

**2. Buka terminal WSL di folder project, lalu jalankan:**
```bash
bash run.sh
```
Script ini membangun image, menyalakan semua service, memasukkan schema/data SQL otomatis, dan menampilkan URL aplikasi.
Container dan image aplikasi lama akan dibersihkan terlebih dahulu. Volume database dan upload tetap dipertahankan.

**3. Akses di browser:**
- App CI4     : http://localhost:8083
- Domain SEO  : https://slategray-skunk-297972.hostingersite.com
- phpMyAdmin  : http://localhost:8081

Konfigurasi SEO tersedia di halaman publik domain, termasuk robots.txt, sitemap.xml, canonical URL, Open Graph, Twitter Card, dan JSON-LD. Domain aktif: slategray-skunk-297972.hostingersite.com.

Untuk verifikasi Google Search Console dengan metode tag HTML, salin hanya nilai content dari tag Google ke googleSiteVerification pada app/app/Config/Seo.php. Jika Google memberikan file HTML, simpan file tersebut langsung di app/public/ dengan nama persis dari Google, lalu upload ke public_html. File harus dapat diakses melalui https://slategray-skunk-297972.hostingersite.com/nama-file-verifikasi.html.

## Deploy ke Hostinger Shared Hosting

Hostinger shared hosting tidak menjalankan Docker Compose. Untuk CodeIgniter 4, arahkan document root domain ke folder app/public jika pengaturan domain Hostinger mengizinkannya. Jangan arahkan domain ke folder project utama karena folder tersebut tidak memiliki index.php publik.

Jika document root tidak dapat diubah, gunakan struktur dua folder berikut:

~~~text
/home/akun/
├── app/          ← isi dari repository/app/app
├── vendor/       ← repository/app/vendor
├── writable/     ← repository/app/writable
└── public_html/  ← seluruh isi repository/app/public
~~~

File public_html/index.php sudah mencari ../app/Config/Paths.php. Atur PHP ke versi 8.2 atau lebih tinggi, import file SQL melalui phpMyAdmin Hostinger, lalu sesuaikan hostname, username, password, dan nama database di app/app/Config/Database.php. Database Hostinger biasanya memakai hostname localhost dan port 3306.

Pastikan public_html/index.php dan public_html/.htaccess ada, file memiliki permission 644, folder 755, dan folder writable dapat ditulis PHP. Jika muncul 403, cek file tersembunyi .htaccess di public_html dan log error hosting.

### Debug 403 melalui URL

Upload app/public/hostinger-debug.php ke public_html/Hostinger. Buka https://slategray-skunk-297972.hostingersite.com/hostinger-debug.php. File ini menampilkan status PHP, document root, permission, file CI4, folder writable, dan extension tanpa menampilkan password database. Hapus file tersebut setelah selesai debugging.

**4. Login phpMyAdmin:**
- Server  : db
- User    : root
- Password: root123

**5. Login App (default):**
- Username: admin
- Password: admin123

---

## Perintah Docker Berguna
```bash
# Setup lengkap dari WSL
bash run.sh

# Jalankan
docker compose up -d --build

# Stop
docker compose down

# Lihat log
docker compose logs -f app

# Masuk container
docker compose exec app bash
```

## Struktur Database
- users        - semua pengguna (admin, dpl, mahasiswa)
- dpl          - data Dosen Pembimbing Lapangan
- mahasiswa    - data mahasiswa KKN
- kelompok_kkn - data kelompok KKN
- lokasi_kkn   - data lokasi/desa KKN
- logbook      - logbook harian mahasiswa
- laporan      - laporan kegiatan
- penilaian    - nilai mahasiswa + prediksi KNN
- evaluasi     - evaluasi kegiatan

## Tech Stack
- CodeIgniter 4 (PHP 8.2)
- MySQL 8.0
- phpMyAdmin
- Docker + Docker Compose
