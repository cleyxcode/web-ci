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
- Domain SEO  : https://kkntematikukim.site
- phpMyAdmin  : http://localhost:8081

Konfigurasi SEO tersedia di halaman publik domain, termasuk robots.txt, sitemap.xml, canonical URL, Open Graph, Twitter Card, dan JSON-LD. Arahkan DNS kkntematikukim.site ke server aplikasi dan aktifkan sertifikat TLS pada reverse proxy atau hosting agar domain dapat diakses melalui HTTPS.

Untuk verifikasi Google Search Console dengan metode tag HTML, salin hanya nilai content dari tag Google ke googleSiteVerification pada app/app/Config/Seo.php. Jika Google memberikan file HTML, simpan file tersebut langsung di app/public/ dengan nama persis dari Google, lalu rebuild image Docker. File harus dapat diakses melalui https://kkntematikukim.site/nama-file-verifikasi.html.

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
