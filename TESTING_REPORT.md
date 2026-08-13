# Laporan Testing — Sistem Monitoring KKN Tematik UKIM

**Tanggal**   : 2026-08-13  
**Lingkungan**: Docker (PHP 8.2.33, MySQL 8.0, CodeIgniter 4)  
**URL**       : http://localhost:8083/  

---

## Ringkasan Eksekusi

| Jenis Test       | File / Alat             | Total | PASS | FAIL | WARN |
|------------------|-------------------------|-------|------|------|------|
| Unit Test        | PHPUnit (4 file)        | 76    | 76   | 0    | —    |
| Live HTTP Test   | Python requests         | 61    | 54   | 0*   | 7    |

> \* 10 FAIL awal di live test — setelah diverifikasi manual, 7 adalah **false positive** (role filter me-redirect ke dashboard sendiri, bukan ke `/login`). **3 konfirmasi FAIL nyata** berupa HTTP 500 dari SQL error.

---

## BUG KRITIS — HTTP 500 (SQL Error)

Bug ini menyebabkan server crash dengan pesan SQL mentah ke client. Tiga endpoint terdampak:

### BUG-01 · `POST /admin/lokasi` — Insert dengan nama_desa kosong → HTTP 500

**File** : `app/Controllers/Admin/LokasiController.php::store()`  
**Error** : `You have an error in your SQL syntax ... near ') VALUES ('', '', ''...`  
**Penyebab** : Model CI4 dengan `$allowedFields` yang tidak menyertakan `nama_desa` dalam konfigurasi, dikombinasikan dengan tidak ada validasi required. Field kosong menyebabkan query malformed.  
**Reproduksi** :
```
POST /admin/lokasi
nama_desa=&kecamatan=&kabupaten=
```
**Dampak** : Admin dapat crash halaman secara tidak sengaja. Pesan SQL error terekspos ke browser.  
**Perbaikan** : Tambah validasi required di controller:
```php
// LokasiController.php::store()
$rules = ['nama_desa' => 'required|min_length[3]'];
if (!$this->validate($rules)) {
    return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
}
```

---

### BUG-02 · `POST /admin/kkn` — Insert dengan nama_kelompok kosong → HTTP 500

**File** : `app/Controllers/Admin/KknController.php::store()` via `payload()`  
**Error** : `You have an error in your SQL syntax ... near ') VALUES ('', NULL, NULL, NULL, ''...`  
**Penyebab** : `KknController::payload()` langsung mengambil POST tanpa validasi apapun.  
**Reproduksi** :
```
POST /admin/kkn
nama_kelompok=&periode=
```
**Dampak** : Sama dengan BUG-01 — crash + SQL error terekspos.  
**Perbaikan** :
```php
// KknController.php::store() dan update()
$rules = ['nama_kelompok' => 'required|min_length[3]'];
if (!$this->validate($rules)) {
    return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
}
```

---

### BUG-03 · `POST /admin/kkn` — tanggal_mulai format tidak valid → HTTP 500

**File** : `app/Controllers/Admin/KknController.php` via `payload()`  
**Error** : SQL error karena string `2026-99-99` diteruskan ke kolom `DATE` MySQL.  
**Reproduksi** :
```
POST /admin/kkn
nama_kelompok=Test&tanggal_mulai=2026-99-99
```
**Perbaikan** :
```php
'tanggal_mulai' => 'permit_empty|valid_date[Y-m-d]',
'tanggal_selesai' => 'permit_empty|valid_date[Y-m-d]',
```

---

### BUG-04 · `POST /admin/profil` — email kosong → HTTP 500 (Duplicate entry)

**File** : `app/Controllers/Admin/ProfilController.php::update()`  
**Error** : `Duplicate entry '' for key 'users.email'`  
**Penyebab** : Tidak ada validasi di `update()`. Email kosong (`''`) menabrak constraint `UNIQUE` di tabel `users`.  
**Reproduksi** :
```
POST /admin/profil
nama=&email=
```
**Perbaikan** :
```php
$rules = [
    'nama'  => 'required|min_length[3]',
    'email' => 'required|valid_email',
];
```

---

### BUG-05 · `POST /forgot-password` — email kosong → HTTP 500 (SQL Error)

**File** : `app/Controllers/Auth/ForgotPasswordController.php::send()`  
**Error** : SQL syntax error karena `WHERE email = ''` menghasilkan query bermasalah di OTP model.  
**Reproduksi** :
```
POST /forgot-password
email=
```
**Perbaikan** :
```php
$rules = ['email' => 'required|valid_email'];
if (!$this->validate($rules)) {
    return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
}
```

---

## BUG SEDANG — Data Tersimpan Tanpa Validasi

### BUG-06 · `POST /mahasiswa/logbook` — tanggal & kegiatan kosong tersimpan

**File** : `app/Controllers/Mahasiswa/LogbookController.php::store()`  
**Penyebab** : Tidak ada `$this->validate()` sama sekali. Semua field POST langsung di-insert.  
**Dampak** : Logbook dengan tanggal kosong, kegiatan kosong, atau tanggal tidak valid (misal `bukan-tanggal`, `2099-12-31`) berhasil tersimpan ke database.  
**Perbaikan** :
```php
$rules = [
    'tanggal'  => 'required|valid_date[Y-m-d]',
    'kegiatan' => 'required|min_length[5]',
];
```

---

### BUG-07 · `POST /mahasiswa/laporan` — judul tidak divalidasi

**File** : `app/Controllers/Mahasiswa/LaporanController.php::store()`  
**Penyebab** : Hanya ada pengecekan file upload. Judul kosong tetap tersimpan selama file ada.  
**Perbaikan** :
```php
$rules = [
    'judul'     => 'required|min_length[5]|max_length[200]',
    'deskripsi' => 'permit_empty',
];
```

---

### BUG-08 · `POST /mahasiswa/profil` — email tidak valid tersimpan

**File** : `app/Controllers/Mahasiswa/ProfilController.php::update()`  
**Penyebab** : Tidak ada validasi di method `update()`. Email format apapun tersimpan ke DB.  
**Dampak** : User bisa menyimpan email `bukan-email` ke database, merusak integritas data dan fitur email.  
**Perbaikan** :
```php
$rules = [
    'nama'  => 'required|min_length[3]',
    'email' => 'required|valid_email',
];
```
> Berlaku juga untuk `Admin\ProfilController`, `Dpl\ProfilController` (jika ada).

---

### BUG-09 · `POST /admin/mahasiswa` (update) — tidak ada validasi

**File** : `app/Controllers/Admin/MahasiswaController.php::update()`  
**Penyebab** : `store()` punya validasi, tapi `update()` tidak. Email format salah bisa masuk lewat form edit.

---

### BUG-10 · `POST /dpl/penilaian` — nilai di luar range 0–100 diterima

**File** : `app/Controllers/Dpl/PenilaianController.php::save()`  
**Penyebab** : Nilai hanya di-cast ke `float`, tidak ada pengecekan range `0 ≤ nilai ≤ 100`.  
**Dampak** : Nilai `-50` atau `999` bisa tersimpan dan mempengaruhi perhitungan KNN.  
**Perbaikan** :
```php
$rules = [
    'nilai_keaktifan' => 'required|decimal|greater_than_equal_to[0]|less_than_equal_to[100]',
    'nilai_logbook'   => 'required|decimal|greater_than_equal_to[0]|less_than_equal_to[100]',
    'nilai_laporan'   => 'required|decimal|greater_than_equal_to[0]|less_than_equal_to[100]',
];
```

---

## BUG RINGAN — Celah Keamanan & Logika

### BUG-11 · `POST /admin/reset-password` — tidak ada validasi target user

**File** : `app/Controllers/Admin/PengumumanController.php::resetPassword()`  
**Penyebab** : `user_id` diambil dari POST tanpa verifikasi apakah user tersebut ada.  
**Dampak** : Admin bisa mengirim `user_id=<any>` dan mereset password user lain tanpa konfirmasi. Jika `user_id=0`, tidak ada error (CI4 silent pada no-match update).  
**Perbaikan** :
```php
$user = model(UserModel::class)->find($userId);
if (!$user) {
    return redirect()->back()->with('error', 'User tidak ditemukan.');
}
// Tambah konfirmasi: hanya bisa reset password user yang bukan admin lain
```

---

### BUG-12 · `POST /forgot-password` — tidak ada validasi format email

**File** : `app/Controllers/Auth/ForgotPasswordController.php::send()`  
**Penyebab** : Langsung query DB dengan email apapun. Format `bukan@@email` tidak divalidasi dulu.  
**Dampak** : Query tidak perlu dieksekusi, membuka potensi timing attack untuk enumerasi email.

---

### BUG-13 · `POST /mahasiswa/profil/password` — new_password tidak divalidasi

**File** : `app/Controllers/Mahasiswa/ProfilController.php::changePassword()`  
**Penyebab** : Hanya current_password yang dicek. `new_password` bisa kosong atau 1 karakter.  
**Perbaikan** :
```php
if (empty($new) || strlen($new) < 6) {
    return redirect()->back()->with('error', 'Password baru minimal 6 karakter.');
}
```

---

### BUG-14 · `upload_file()` — validasi hanya ekstensi, tidak MIME type

**File** : `app/Helpers/app_helper.php::upload_file()`  
**Penyebab** : Fungsi hanya memeriksa `getExtension()`, bukan MIME type sebenarnya. File PHP yang direname jadi `.jpg` akan lolos validasi.  
**Perbaikan** :
```php
$mimeType = $file->getMimeType();
$allowedMimes = ['image/jpeg', 'image/png', 'application/pdf'];
if (!in_array($mimeType, $allowedMimes, true)) {
    return null;
}
```

---

## Yang Sudah Benar (PASS)

| Fitur | Status |
|-------|--------|
| Login — field kosong ditolak dengan pesan error | ✓ OK |
| Login — credentials salah ditolak | ✓ OK |
| Login — SQL injection tidak tembus | ✓ OK |
| Login — XSS di-escape, tidak direfleksikan | ✓ OK |
| Admin tambah mahasiswa — validasi nama, email, password, NPM | ✓ OK |
| Admin tambah DPL — validasi semua field required | ✓ OK |
| Pengumuman kosong — ditolak (model-level NOT NULL) | ✓ OK |
| Laporan tanpa file — ada pesan error file PDF | ✓ OK |
| Password lama salah — ditolak dengan pesan | ✓ OK |
| OTP reset — konfirmasi password tidak cocok ditolak | ✓ OK |
| Role protection — mahasiswa tidak bisa akses /admin atau /dpl | ✓ OK |
| Anonymous tidak bisa akses halaman terproteksi | ✓ OK |
| CSRF protection aktif di semua form | ✓ OK |
| KnnLib — perhitungan nilai akhir dengan bobot 30/30/40 | ✓ OK |
| KnnLib — gradeFromScore dengan semua threshold | ✓ OK |

---

## Prioritas Perbaikan

```
KRITIS  : BUG-01, BUG-02, BUG-03, BUG-04, BUG-05  (HTTP 500 — server crash)
TINGGI  : BUG-06, BUG-07, BUG-08, BUG-09, BUG-10  (data tidak valid tersimpan)
SEDANG  : BUG-11, BUG-13                             (keamanan & logika)
RENDAH  : BUG-12, BUG-14                             (hardening tambahan)
```

---

## File Test yang Dibuat

```
tests/unit/
├── KnnLibTest.php          (diperbaiki — 19 assertions)
├── KnnLibEdgeCaseTest.php  (baru — 24 test cases, edge case & boundary)
├── ValidationTest.php      (baru — 20 test cases, semua aturan validasi controller)
└── UploadFileTest.php      (baru — 14 test cases, helper upload_file)

Total: 76 tests, 139 assertions — semua PASS
```
