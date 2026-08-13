# SISTEM MONITORING KKN TEMATIK - UKIM
## Panduan Lengkap untuk Cursor AI

---

## 📋 IDENTITAS PROYEK

- **Judul**: Implementasi Sistem Monitoring KKN Tematik Berbasis Web pada Fakultas Ilmu Komputer
- **Penulis**: Clara Mustamu | NPM: 12155201220035
- **Institusi**: Universitas Kristen Indonesia Maluku (UKIM)
- **Fakultas**: Ilmu Komputer - Program Studi Informatika
- **Tahun**: 2026

---

## 🛠️ TECH STACK

### Backend
- **Framework**: CodeIgniter 4 (PHP 8.2)
- **Database**: MySQL 8.0
- **Realtime**: Pusher (WebSocket) + Laravel Echo / native JS
- **Email OTP**: PHPMailer + Gmail SMTP
- **Auth**: Session-based + CSRF Token
- **Algoritma**: KNN (K-Nearest Neighbor) untuk prediksi nilai mahasiswa

### Frontend
- **CSS Framework**: Tailwind CSS (CDN atau via NPM)
- **JS Realtime**: Pusher JS Client + Alpine.js
- **Icons**: Heroicons atau Tabler Icons
- **Charts**: Chart.js (untuk dashboard statistik)
- **Notifikasi**: Toast / SweetAlert2
- **Tabel**: DataTables.js

### DevOps
- **Container**: Docker + Docker Compose
- **Web Server**: Apache (dalam container PHP)
- **DB Admin**: phpMyAdmin

---

## 🗂️ STRUKTUR FOLDER CODEIGNITER 4

```
app/
├── Config/
│   ├── App.php
│   ├── Database.php
│   ├── Email.php          ← Konfigurasi Gmail SMTP
│   ├── Pusher.php         ← Konfigurasi Pusher
│   └── Routes.php
├── Controllers/
│   ├── Auth/
│   │   ├── LoginController.php
│   │   ├── ForgotPasswordController.php
│   │   └── OtpController.php
│   ├── Admin/
│   │   ├── DashboardController.php
│   │   ├── MahasiswaController.php
│   │   ├── DplController.php
│   │   ├── KknController.php
│   │   ├── LokasiController.php
│   │   └── LaporanController.php
│   ├── Dpl/
│   │   ├── DashboardController.php
│   │   ├── MonitoringController.php
│   │   ├── LogbookController.php
│   │   └── PenilaianController.php
│   └── Mahasiswa/
│       ├── DashboardController.php
│       ├── LogbookController.php
│       ├── LaporanController.php
│       ├── NilaiController.php
│       └── EvaluasiController.php
├── Models/
│   ├── UserModel.php
│   ├── MahasiswaModel.php
│   ├── DplModel.php
│   ├── KelompokKknModel.php
│   ├── LokasiKknModel.php
│   ├── LogbookModel.php
│   ├── LaporanModel.php
│   ├── PenilaianModel.php
│   ├── EvaluasiModel.php
│   └── OtpModel.php
├── Views/
│   ├── layouts/
│   │   ├── auth.php           ← Layout halaman login/register
│   │   ├── admin.php          ← Layout panel admin
│   │   ├── dpl.php            ← Layout panel DPL
│   │   └── mahasiswa.php      ← Layout panel mahasiswa
│   ├── auth/
│   │   ├── login.php
│   │   ├── forgot_password.php
│   │   ├── otp_verify.php
│   │   └── reset_password.php
│   ├── admin/
│   │   ├── dashboard.php
│   │   ├── mahasiswa/
│   │   ├── dpl/
│   │   ├── kkn/
│   │   ├── lokasi/
│   │   └── laporan/
│   ├── dpl/
│   │   ├── dashboard.php
│   │   ├── monitoring.php
│   │   ├── logbook/
│   │   └── penilaian/
│   └── mahasiswa/
│       ├── dashboard.php
│       ├── logbook/
│       ├── laporan/
│       ├── nilai.php
│       └── evaluasi.php
├── Filters/
│   ├── AuthFilter.php         ← Cek login
│   └── RoleFilter.php         ← Cek role (admin/dpl/mahasiswa)
├── Libraries/
│   ├── PusherLib.php          ← Wrapper Pusher
│   ├── KnnLib.php             ← Algoritma KNN
│   └── OtpLib.php             ← Generate & kirim OTP
└── Helpers/
    └── app_helper.php
```

---

## 🗄️ STRUKTUR DATABASE

```sql
-- TABEL USERS (semua role)
CREATE TABLE users (
  id INT PRIMARY KEY AUTO_INCREMENT,
  nama VARCHAR(100) NOT NULL,
  username VARCHAR(50) UNIQUE NOT NULL,
  email VARCHAR(100) UNIQUE NOT NULL,
  password VARCHAR(255) NOT NULL,
  role ENUM('admin','dpl','mahasiswa') NOT NULL,
  foto VARCHAR(255) DEFAULT NULL,
  is_active TINYINT(1) DEFAULT 1,
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- TABEL OTP
CREATE TABLE otp_codes (
  id INT PRIMARY KEY AUTO_INCREMENT,
  user_id INT NOT NULL,
  email VARCHAR(100) NOT NULL,
  otp_code VARCHAR(6) NOT NULL,
  type ENUM('reset_password','verify_email') DEFAULT 'reset_password',
  is_used TINYINT(1) DEFAULT 0,
  expired_at DATETIME NOT NULL,
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- TABEL DPL
CREATE TABLE dpl (
  id INT PRIMARY KEY AUTO_INCREMENT,
  user_id INT NOT NULL,
  nidn VARCHAR(20) NOT NULL,
  nama VARCHAR(100) NOT NULL,
  prodi VARCHAR(100),
  no_hp VARCHAR(20),
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- TABEL LOKASI KKN
CREATE TABLE lokasi_kkn (
  id INT PRIMARY KEY AUTO_INCREMENT,
  nama_desa VARCHAR(100) NOT NULL,
  kecamatan VARCHAR(100),
  kabupaten VARCHAR(100),
  koordinat VARCHAR(100),
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- TABEL KELOMPOK KKN
CREATE TABLE kelompok_kkn (
  id INT PRIMARY KEY AUTO_INCREMENT,
  nama_kelompok VARCHAR(100) NOT NULL,
  dpl_id INT,
  lokasi_id INT,
  periode VARCHAR(50),
  tanggal_mulai DATE,
  tanggal_selesai DATE,
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (dpl_id) REFERENCES dpl(id),
  FOREIGN KEY (lokasi_id) REFERENCES lokasi_kkn(id)
);

-- TABEL MAHASISWA
CREATE TABLE mahasiswa (
  id INT PRIMARY KEY AUTO_INCREMENT,
  user_id INT NOT NULL,
  npm VARCHAR(20) UNIQUE NOT NULL,
  nama VARCHAR(100) NOT NULL,
  prodi VARCHAR(100),
  kelompok_id INT,
  no_hp VARCHAR(20),
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
  FOREIGN KEY (kelompok_id) REFERENCES kelompok_kkn(id)
);

-- TABEL LOGBOOK
CREATE TABLE logbook (
  id INT PRIMARY KEY AUTO_INCREMENT,
  mahasiswa_id INT NOT NULL,
  tanggal DATE NOT NULL,
  kegiatan TEXT NOT NULL,
  lokasi_kegiatan VARCHAR(200),
  dokumentasi VARCHAR(255),
  status ENUM('menunggu','divalidasi','ditolak') DEFAULT 'menunggu',
  catatan_dpl TEXT,
  validated_by INT,
  validated_at DATETIME,
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (mahasiswa_id) REFERENCES mahasiswa(id) ON DELETE CASCADE,
  FOREIGN KEY (validated_by) REFERENCES dpl(id)
);

-- TABEL LAPORAN
CREATE TABLE laporan (
  id INT PRIMARY KEY AUTO_INCREMENT,
  mahasiswa_id INT NOT NULL,
  judul VARCHAR(200) NOT NULL,
  deskripsi TEXT,
  file_laporan VARCHAR(255),
  status ENUM('menunggu','diterima','ditolak') DEFAULT 'menunggu',
  catatan_dpl TEXT,
  reviewed_by INT,
  reviewed_at DATETIME,
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (mahasiswa_id) REFERENCES mahasiswa(id) ON DELETE CASCADE
);

-- TABEL PENILAIAN
CREATE TABLE penilaian (
  id INT PRIMARY KEY AUTO_INCREMENT,
  mahasiswa_id INT NOT NULL,
  dpl_id INT NOT NULL,
  nilai_keaktifan FLOAT DEFAULT 0,   -- bobot 30%
  nilai_logbook FLOAT DEFAULT 0,     -- bobot 30%
  nilai_laporan FLOAT DEFAULT 0,     -- bobot 40%
  nilai_akhir FLOAT DEFAULT 0,       -- hasil kalkulasi
  grade CHAR(2),                     -- A, B, C, D
  prediksi_knn CHAR(2),              -- prediksi dari algoritma KNN
  catatan TEXT,
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (mahasiswa_id) REFERENCES mahasiswa(id),
  FOREIGN KEY (dpl_id) REFERENCES dpl(id)
);

-- TABEL EVALUASI
CREATE TABLE evaluasi (
  id INT PRIMARY KEY AUTO_INCREMENT,
  mahasiswa_id INT NOT NULL,
  rating TINYINT(1),              -- 1-5 bintang
  komentar TEXT,
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (mahasiswa_id) REFERENCES mahasiswa(id)
);

-- TABEL NOTIFIKASI (untuk realtime)
CREATE TABLE notifikasi (
  id INT PRIMARY KEY AUTO_INCREMENT,
  user_id INT NOT NULL,
  judul VARCHAR(200) NOT NULL,
  pesan TEXT NOT NULL,
  type ENUM('info','success','warning','danger') DEFAULT 'info',
  is_read TINYINT(1) DEFAULT 0,
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- DATA DEFAULT ADMIN
INSERT INTO users (nama, username, email, password, role) VALUES
('Super Admin', 'admin', 'admin@ukim.ac.id', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin');
-- password default: password
```

---

## 🔐 FITUR AUTENTIKASI

### 1. Login
- Form: username/email + password
- Validasi role → redirect ke dashboard sesuai role
- Session dengan CI4 Session library
- CSRF protection
- Tampilan: Tailwind CSS, kartu centered, responsive

### 2. Lupa Password
- Input email → sistem cek apakah email terdaftar
- Generate OTP 6 digit → kirim ke Gmail via PHPMailer + SMTP
- OTP berlaku **5 menit**
- Halaman verifikasi OTP (6 kotak input digit)
- Jika OTP benar → form reset password baru
- Password di-hash dengan `password_hash()` bcrypt

### 3. Reset Password (dari panel)
- Admin bisa reset password user manapun
- User bisa reset password sendiri dari profil
- Wajib konfirmasi password baru

### 4. Konfigurasi Gmail SMTP (PHPMailer)
```php
// app/Config/Email.php
$config['protocol'] = 'smtp';
$config['SMTPHost'] = 'smtp.gmail.com';
$config['SMTPPort'] = 587;
$config['SMTPCrypto'] = 'tls';
$config['SMTPUser'] = 'emailkkn@gmail.com';  // ganti dengan email kamu
$config['SMTPPass'] = 'xxxx xxxx xxxx xxxx'; // Google App Password
$config['mailType'] = 'html';
$config['charset'] = 'utf-8';
```
> Aktifkan **2FA Gmail** lalu buat **App Password** di myaccount.google.com/apppasswords

### 5. Template Email OTP
```html
<!-- Template email OTP yang dikirim ke user -->
<div style="font-family:sans-serif;max-width:500px;margin:auto">
  <h2 style="color:#3B82F6">Kode OTP - Sistem KKN UKIM</h2>
  <p>Halo {nama},</p>
  <p>Kode OTP reset password Anda:</p>
  <div style="font-size:36px;font-weight:bold;letter-spacing:8px;
              text-align:center;background:#F3F4F6;padding:20px;
              border-radius:8px;color:#1F2937">
    {otp_code}
  </div>
  <p style="color:#6B7280">Kode berlaku selama <strong>5 menit</strong>.</p>
  <p style="color:#EF4444">Jangan bagikan kode ini kepada siapapun.</p>
</div>
```

---

## ⚡ FITUR REALTIME (Pusher + WebSocket)

### Setup Pusher
1. Daftar di https://pusher.com → buat app baru
2. Salin: App ID, Key, Secret, Cluster

### Konfigurasi CI4
```php
// app/Config/Pusher.php
define('PUSHER_APP_ID', 'your_app_id');
define('PUSHER_KEY', 'your_key');
define('PUSHER_SECRET', 'your_secret');
define('PUSHER_CLUSTER', 'ap1');
```

### Install Pusher PHP SDK
```bash
composer require pusher/pusher-php-server
```

### Channel & Events yang digunakan
| Event | Channel | Trigger |
|-------|---------|---------|
| `logbook.submitted` | `kkn-channel` | Mahasiswa submit logbook |
| `logbook.validated` | `mahasiswa-{id}` | DPL validasi logbook |
| `laporan.submitted` | `kkn-channel` | Mahasiswa upload laporan |
| `laporan.reviewed` | `mahasiswa-{id}` | DPL review laporan |
| `nilai.published` | `mahasiswa-{id}` | DPL publish nilai |
| `notifikasi.new` | `user-{id}` | Notifikasi baru masuk |

### Frontend Pusher JS
```html
<!-- Tambahkan di layout -->
<script src="https://js.pusher.com/8.2.0/pusher.min.js"></script>
<script>
  const pusher = new Pusher('YOUR_KEY', { cluster: 'ap1' });
  const channel = pusher.subscribe('kkn-channel');

  channel.bind('logbook.submitted', function(data) {
    // Update tabel logbook DPL tanpa refresh
    updateLogbookTable(data);
    showToast('Logbook baru masuk dari ' + data.nama_mahasiswa);
  });
</script>
```

---

## 👤 PANEL ADMIN

### Dashboard Admin
- Total mahasiswa aktif KKN
- Total DPL
- Total lokasi KKN
- Total kegiatan terlaporkan
- Grafik logbook per minggu (Chart.js)
- Grafik status laporan (pie chart)
- Tabel mahasiswa terbaru
- Notifikasi realtime

### Menu Admin
1. **Data Mahasiswa** - CRUD mahasiswa + assign kelompok
2. **Data DPL** - CRUD DPL
3. **Data KKN** - CRUD kelompok KKN + periode
4. **Lokasi KKN** - CRUD desa/lokasi penempatan
5. **Laporan** - Lihat semua laporan mahasiswa
6. **Pengumuman** - Buat pengumuman ke semua user
7. **Pengaturan** - Konfigurasi sistem
8. **Reset Password** - Reset password user manapun

---

## 👨‍🏫 PANEL DPL

### Dashboard DPL
- Jumlah mahasiswa bimbingan
- Logbook menunggu validasi (badge merah)
- Laporan menunggu review (badge merah)
- Evaluasi belum diisi
- Ringkasan kegiatan (selesai/proses/belum mulai)
- Kegiatan terbaru mahasiswa (realtime)

### Menu DPL
1. **Monitoring Kegiatan** - Pantau aktivitas semua mahasiswa bimbingan realtime
2. **Validasi Logbook** - Review & validasi/tolak logbook mahasiswa
3. **Review Laporan** - Terima/tolak laporan mahasiswa
4. **Penilaian Mahasiswa** - Input nilai + lihat prediksi KNN
5. **Lihat Laporan** - Arsip semua laporan

---

## 🎓 PANEL MAHASISWA

### Dashboard Mahasiswa
- Info lokasi KKN + nama DPL
- Progress KKN (persentase)
- Periode KKN (tanggal mulai - selesai)
- Menu cepat: Logbook, Upload, Penilaian, Evaluasi
- Pengumuman terbaru
- Logbook terbaru + status validasi

### Menu Mahasiswa
1. **Logbook Kegiatan** - Input kegiatan harian + upload foto dokumentasi
2. **Upload Laporan** - Upload file laporan (PDF)
3. **Lihat Nilai** - Lihat nilai dari DPL + prediksi KNN
4. **Evaluasi Kegiatan** - Isi evaluasi pelaksanaan KKN (rating + komentar)
5. **Profil** - Edit profil + ganti password

---

## 🤖 ALGORITMA KNN (Prediksi Nilai)

### Fitur (Input KNN)
| Fitur | Keterangan |
|-------|-----------|
| `jml_logbook` | Jumlah logbook yang diisi |
| `jml_logbook_valid` | Jumlah logbook yang divalidasi DPL |
| `jml_laporan` | Jumlah laporan yang diupload |
| `jml_laporan_diterima` | Jumlah laporan yang diterima |
| `nilai_keaktifan` | Nilai keaktifan dari DPL (0-100) |

### Label (Output KNN)
- **A** = nilai_akhir >= 85
- **B** = nilai_akhir >= 70
- **C** = nilai_akhir >= 55
- **D** = nilai_akhir < 55

### Implementasi KNN di PHP (KnnLib.php)
```php
class KnnLib {
    private $k = 3;
    private $trainingData = []; // ambil dari tabel penilaian historis

    public function predict(array $input): string {
        $distances = [];
        foreach ($this->trainingData as $data) {
            $dist = $this->euclideanDistance($input, $data['features']);
            $distances[] = ['label' => $data['grade'], 'distance' => $dist];
        }
        usort($distances, fn($a, $b) => $a['distance'] <=> $b['distance']);
        $kNearest = array_slice($distances, 0, $this->k);
        return $this->majorityVote($kNearest);
    }

    private function euclideanDistance(array $a, array $b): float {
        $sum = 0;
        foreach ($a as $i => $val) {
            $sum += pow($val - $b[$i], 2);
        }
        return sqrt($sum);
    }

    private function majorityVote(array $neighbors): string {
        $votes = array_count_values(array_column($neighbors, 'label'));
        arsort($votes);
        return array_key_first($votes);
    }
}
```

### Kalkulasi Nilai Akhir
```
nilai_akhir = (nilai_keaktifan × 30%) + (nilai_logbook × 30%) + (nilai_laporan × 40%)

Grade:
- A  = nilai_akhir >= 85
- B  = nilai_akhir >= 70
- BC = nilai_akhir >= 65
- C  = nilai_akhir >= 55
- D  = nilai_akhir < 55
```

---

## 🎨 PANDUAN UI (Tailwind CSS)

### Warna Utama
```css
/* Definisikan di tailwind.config.js atau gunakan class langsung */
primary:   blue-600   (#2563EB)  ← tombol utama, header
secondary: gray-600   (#4B5563)  ← teks sekunder
success:   green-500  (#22C55E)  ← status divalidasi/diterima
warning:   yellow-500 (#EAB308)  ← status menunggu
danger:    red-500    (#EF4444)  ← status ditolak
```

### Komponen Wajib

#### Sidebar Layout
```html
<!-- Sidebar dengan Tailwind -->
<div class="flex h-screen bg-gray-100">
  <!-- Sidebar -->
  <aside class="w-64 bg-blue-800 text-white flex flex-col">
    <div class="p-4 text-xl font-bold border-b border-blue-700">
      KKN TEMATIK
    </div>
    <nav class="flex-1 p-4 space-y-1">
      <a href="#" class="flex items-center gap-3 p-3 rounded-lg hover:bg-blue-700">
        <svg>...</svg> Dashboard
      </a>
      <!-- menu lainnya -->
    </nav>
  </aside>
  <!-- Main Content -->
  <main class="flex-1 overflow-auto">...</main>
</div>
```

#### Card Statistik Dashboard
```html
<div class="bg-white rounded-xl shadow p-6 flex items-center gap-4">
  <div class="bg-blue-100 p-3 rounded-full">
    <svg class="w-6 h-6 text-blue-600">...</svg>
  </div>
  <div>
    <p class="text-sm text-gray-500">Total Mahasiswa</p>
    <p class="text-2xl font-bold text-gray-800">128</p>
  </div>
</div>
```

#### Badge Status
```html
<!-- Status logbook -->
<span class="px-2 py-1 text-xs rounded-full bg-yellow-100 text-yellow-700">Menunggu</span>
<span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-700">Divalidasi</span>
<span class="px-2 py-1 text-xs rounded-full bg-red-100 text-red-700">Ditolak</span>
```

#### Form Input OTP (6 digit)
```html
<div class="flex gap-3 justify-center my-6">
  <input type="text" maxlength="1" class="w-12 h-12 text-center text-2xl font-bold border-2 border-gray-300 rounded-lg focus:border-blue-500 focus:outline-none" />
  <!-- ulangi 6x -->
</div>
<script>
// Auto focus ke input berikutnya
document.querySelectorAll('.otp-input').forEach((input, i, inputs) => {
  input.addEventListener('input', () => {
    if (input.value && i < inputs.length - 1) inputs[i+1].focus();
  });
});
</script>
```

#### Toast Notifikasi Realtime
```html
<div id="toast" class="fixed top-4 right-4 z-50 hidden">
  <div class="bg-white border-l-4 border-blue-500 shadow-lg rounded-lg p-4 flex items-center gap-3">
    <span id="toast-message">Pesan notifikasi</span>
  </div>
</div>
<script>
function showToast(msg, type='info') {
  const toast = document.getElementById('toast');
  document.getElementById('toast-message').textContent = msg;
  toast.classList.remove('hidden');
  setTimeout(() => toast.classList.add('hidden'), 4000);
}
</script>
```

---

## 🔄 ALUR SISTEM LENGKAP

### Alur Login & OTP Reset Password
```
User buka /login
  → Input username/email + password
  → CI4 validasi kredensial
  → Jika benar → redirect dashboard sesuai role
  → Jika salah → tampilkan error

User klik "Lupa Password"
  → Input email
  → Sistem cek email di database
  → Generate OTP 6 digit (random_int)
  → Simpan OTP ke tabel otp_codes (expired 5 menit)
  → Kirim email via PHPMailer + Gmail SMTP
  → Redirect ke halaman verifikasi OTP

User input OTP
  → Sistem cek OTP valid + belum expired + belum dipakai
  → Jika valid → tampilkan form reset password
  → Jika tidak → tampilkan error

User input password baru
  → Hash dengan bcrypt
  → Update tabel users
  → Mark OTP as used
  → Redirect ke login
```

### Alur Mahasiswa Input Logbook
```
Mahasiswa login → Dashboard
  → Klik menu Logbook
  → Isi form: tanggal, kegiatan, lokasi, upload foto
  → Submit → simpan ke tabel logbook (status: menunggu)
  → Trigger Pusher event 'logbook.submitted'
  → DPL menerima notifikasi realtime (tanpa refresh)
  → DPL buka menu Validasi Logbook
  → Review → klik Validasi / Tolak + catatan
  → Update status logbook di database
  → Trigger Pusher event 'logbook.validated' ke mahasiswa
  → Mahasiswa menerima notifikasi realtime
```

### Alur Penilaian + KNN
```
DPL buka menu Penilaian Mahasiswa
  → Pilih mahasiswa
  → Input: nilai_keaktifan, nilai_logbook, nilai_laporan
  → Sistem hitung nilai_akhir otomatis
  → Sistem jalankan KNN → tampilkan prediksi grade
  → DPL konfirmasi / ubah grade
  → Simpan ke tabel penilaian
  → Trigger Pusher → mahasiswa lihat nilai realtime
```

---

## 📁 FILE ENVIRONMENT (.env)

```env
CI_ENVIRONMENT = development

app.baseURL = 'http://localhost:9090/'

# Database
database.default.hostname = db
database.default.database = kkn_tematik
database.default.username = kkn_user
database.default.password = kkn_pass
database.default.DBDriver = MySQLi
database.default.port = 3306

# Email Gmail SMTP
email.protocol = smtp
email.SMTPHost = smtp.gmail.com
email.SMTPPort = 587
email.SMTPCrypto = tls
email.SMTPUser = emailkknukim@gmail.com
email.SMTPPass = xxxx xxxx xxxx xxxx

# Pusher
pusher.appId = YOUR_APP_ID
pusher.key = YOUR_KEY
pusher.secret = YOUR_SECRET
pusher.cluster = ap1
```

---

## 🚀 PERINTAH DOCKER

```bash
# Pertama kali / setelah ubah Dockerfile
docker compose up -d --build

# Jalankan (sudah pernah build)
docker compose up -d

# Stop
docker compose down

# Lihat log app
docker compose logs app -f

# Masuk ke container app
docker exec -it kkn_app bash

# Install package Composer di dalam container
docker exec -it kkn_app composer require pusher/pusher-php-server
docker exec -it kkn_app composer require phpmailer/phpmailer
```

---

## ✅ CHECKLIST FITUR

### Autentikasi
- [ ] Login (username/email + password)
- [ ] Logout
- [ ] Lupa Password (kirim OTP ke Gmail)
- [ ] Verifikasi OTP 6 digit
- [ ] Reset Password baru
- [ ] Ganti Password dari profil

### Panel Admin
- [ ] Dashboard statistik + grafik
- [ ] CRUD Mahasiswa + assign kelompok
- [ ] CRUD DPL
- [ ] CRUD Kelompok KKN
- [ ] CRUD Lokasi KKN
- [ ] Lihat semua laporan
- [ ] Buat pengumuman
- [ ] Reset password user
- [ ] Notifikasi realtime

### Panel DPL
- [ ] Dashboard + summary mahasiswa
- [ ] Monitoring kegiatan realtime
- [ ] Validasi/tolak logbook + catatan
- [ ] Review/tolak laporan
- [ ] Input penilaian mahasiswa
- [ ] Lihat prediksi KNN
- [ ] Notifikasi realtime

### Panel Mahasiswa
- [ ] Dashboard + info KKN
- [ ] Input logbook harian + foto
- [ ] Upload laporan PDF
- [ ] Lihat status validasi logbook
- [ ] Lihat nilai + grade
- [ ] Isi evaluasi kegiatan
- [ ] Edit profil + ganti password
- [ ] Notifikasi realtime

### Algoritma KNN
- [ ] Training data dari histori penilaian
- [ ] Prediksi grade mahasiswa baru
- [ ] Tampil di panel DPL saat penilaian

---

## 📌 CATATAN PENTING UNTUK CURSOR

1. **Semua controller wajib pakai filter auth** sebelum diakses
2. **Upload file** (foto logbook, PDF laporan) simpan di `public/uploads/` dengan validasi tipe dan ukuran
3. **Semua form** wajib ada CSRF token CI4
4. **Pusher trigger** dipanggil setiap ada perubahan data logbook/laporan/nilai
5. **OTP expired** harus dicek setiap validasi (bandingkan dengan waktu sekarang)
6. **KNN** dijalankan otomatis setiap DPL membuka halaman penilaian
7. **Password** selalu di-hash dengan `password_hash($pass, PASSWORD_BCRYPT)`
8. **Tailwind** bisa pakai CDN untuk development: `<script src="https://cdn.tailwindcss.com"></script>`
9. **Semua response Pusher** berbentuk JSON dan diproses di frontend dengan JS
10. **Database timezone** sesuaikan dengan WIB (UTC+7) atau WIT (UTC+9) untuk UKIM Ambon
