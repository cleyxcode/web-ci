# Sistem Monitoring KKN Tematik - UKIM
**Clara Mustamu | NPM: 12155201220035**

## Cara Menjalankan

### Syarat
- Docker Desktop sudah terinstall dan berjalan
- Koneksi internet (untuk download CI4 pertama kali)

### Langkah

**1. Download/clone project ini ke komputer kamu**

**2. Buka terminal di folder project, lalu jalankan:**
```bash
docker-compose up -d --build
```
> Tunggu sekitar 3-5 menit (download CI4 otomatis)

**3. Akses di browser:**
- App CI4     : http://localhost:8080
- phpMyAdmin  : http://localhost:8081

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
# Jalankan
docker-compose up -d --build

# Stop
docker-compose down

# Lihat log
docker-compose logs app

# Masuk container
docker exec -it kkn_app bash
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
