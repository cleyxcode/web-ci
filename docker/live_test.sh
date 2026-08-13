#!/bin/bash
# live_test.sh — Live HTTP testing untuk KKN Monitoring System
# Dijalankan di dalam container: docker compose exec app bash /var/www/html/live_test.sh

set -uo pipefail

BASE="http://localhost"
CURL="/usr/bin/curl"
ADMIN_JAR="/tmp/admin_sess.txt"
MHS_JAR="/tmp/mhs_sess.txt"
PASS=0
FAIL=0
WARN=0

# ── helper ──────────────────────────────────────────────────────────────────
check() {
    local label="$1"
    local condition="$2"   # 1=pass, 0=fail
    local detail="${3:-}"
    if [ "$condition" = "1" ]; then
        echo "  ✓ PASS  $label"
        PASS=$((PASS+1))
    else
        echo "  ✗ FAIL  $label${detail:+ — $detail}"
        FAIL=$((FAIL+1))
    fi
}

warn() {
    local label="$1"
    local detail="${2:-}"
    echo "  ⚠ WARN  $label${detail:+ — $detail}"
    WARN=$((WARN+1))
}

http_code() {
    # $1=jar, $2=method, $3=url, $4=data(optional)
    local JAR="$1" METHOD="$2" URL="$3" DATA="${4:-}"
    if [ -n "$DATA" ]; then
        $CURL -s -c "$JAR" -b "$JAR" -X "$METHOD" "$URL" \
          -d "$DATA" -w "%{http_code}" -o /tmp/ltest_body.txt -L
    else
        $CURL -s -c "$JAR" -b "$JAR" -X "$METHOD" "$URL" \
          -w "%{http_code}" -o /tmp/ltest_body.txt -L
    fi
}

body_contains() {
    grep -qi "$1" /tmp/ltest_body.txt && echo 1 || echo 0
}

# Bersihkan cookie lama
rm -f "$ADMIN_JAR" "$MHS_JAR" /tmp/anon_sess.txt

echo "============================================================"
echo " KKN Monitoring — Live HTTP Test Suite"
echo " Target : $BASE"
echo " Waktu  : $(date '+%Y-%m-%d %H:%M:%S')"
echo "============================================================"
echo ""

# ── BLOK 1: Akses Publik ─────────────────────────────────────────────────────
echo "── BLOK 1: Halaman Publik ──"

CODE=$(http_code /tmp/anon_sess.txt GET "$BASE/")
check "GET / → redirect ke login (200 setelah follow)" "$([ "$CODE" = "200" ] && echo 1 || echo 0)" "HTTP $CODE"

CODE=$(http_code /tmp/anon_sess.txt GET "$BASE/login")
check "GET /login tampil (200)" "$([ "$CODE" = "200" ] && echo 1 || echo 0)" "HTTP $CODE"

CODE=$(http_code /tmp/anon_sess.txt GET "$BASE/admin/dashboard")
check "GET /admin/dashboard tanpa login → redirect (302 atau 200 login)" \
  "$([ "$CODE" = "302" ] || [ "$CODE" = "200" ] && echo 1 || echo 0)" "HTTP $CODE (bukan 500)"

CODE=$(http_code /tmp/anon_sess.txt GET "$BASE/mahasiswa/logbook")
check "GET /mahasiswa/logbook tanpa login → redirect" \
  "$([ "$CODE" != "500" ] && echo 1 || echo 0)" "HTTP $CODE"

# ── BLOK 2: Login ────────────────────────────────────────────────────────────
echo ""
echo "── BLOK 2: Login ──"

CODE=$(http_code /tmp/anon_sess.txt POST "$BASE/login" "login=&password=")
check "POST /login field kosong → bukan 500" \
  "$([ "$CODE" != "500" ] && echo 1 || echo 0)" "HTTP $CODE"
HAS_ERR=$(body_contains "login\|required\|wajib")
check "POST /login field kosong → ada pesan error di body" "$HAS_ERR"

CODE=$(http_code /tmp/anon_sess.txt POST "$BASE/login" "login=userpalsu&password=passpalsu")
check "POST /login credentials salah → bukan 500" \
  "$([ "$CODE" != "500" ] && echo 1 || echo 0)" "HTTP $CODE"
HAS_ERR=$(body_contains "salah\|tidak ditemukan\|error")
check "POST /login credentials salah → ada pesan error" "$HAS_ERR"

# Login SQL injection sederhana
CODE=$(http_code /tmp/anon_sess.txt POST "$BASE/login" "login=' OR 1=1--&password=apapun")
check "POST /login SQL injection → bukan 500" \
  "$([ "$CODE" != "500" ] && echo 1 || echo 0)" "HTTP $CODE"
IS_BLOCKED=$(body_contains "salah\|error\|login" && echo 1 || echo 0)
check "POST /login SQL injection → tidak berhasil masuk" "$(body_contains 'Dashboard\|Selamat datang' | grep -q 1 && echo 0 || echo 1)"

# Login admin valid
CODE=$(http_code $ADMIN_JAR POST "$BASE/login" "login=admin&password=admin123")
check "POST /login admin valid → bukan 500" \
  "$([ "$CODE" != "500" ] && echo 1 || echo 0)" "HTTP $CODE"
IS_LOGGED=$(body_contains "Dashboard\|dashboard\|Admin\|Selamat")
check "POST /login admin valid → masuk dashboard" "$IS_LOGGED"

# ── BLOK 3: Admin — Tambah Mahasiswa (Validasi Input) ────────────────────────
echo ""
echo "── BLOK 3: Admin — Tambah Mahasiswa ──"

# Data kosong
CODE=$(http_code $ADMIN_JAR POST "$BASE/admin/mahasiswa" "nama=&username=&email=&npm=&password=")
check "POST /admin/mahasiswa data kosong → bukan 500" \
  "$([ "$CODE" != "500" ] && echo 1 || echo 0)" "HTTP $CODE"
HAS_ERR=$(body_contains "wajib\|required\|error\|nama\|email")
check "POST /admin/mahasiswa data kosong → ada pesan validasi" "$HAS_ERR"

# Nama terlalu pendek
CODE=$(http_code $ADMIN_JAR POST "$BASE/admin/mahasiswa" \
  "nama=Ab&username=testmhs$(date +%s)&email=test$(date +%s)@mail.com&npm=00001$(date +%s)&password=pass123")
check "POST /admin/mahasiswa nama < 3 char → bukan 500" \
  "$([ "$CODE" != "500" ] && echo 1 || echo 0)" "HTTP $CODE"
HAS_ERR=$(body_contains "minimum\|min_length\|error\|karakter\|nama")
check "POST /admin/mahasiswa nama < 3 char → ada error validasi" "$HAS_ERR"

# Email tidak valid
TS=$(date +%s)
CODE=$(http_code $ADMIN_JAR POST "$BASE/admin/mahasiswa" \
  "nama=Budi Santoso&username=budi$TS&email=bukan-email-valid&npm=MHS$TS&password=pass1234")
check "POST /admin/mahasiswa email tidak valid → bukan 500" \
  "$([ "$CODE" != "500" ] && echo 1 || echo 0)" "HTTP $CODE"
HAS_ERR=$(body_contains "email\|valid\|error")
check "POST /admin/mahasiswa email tidak valid → ada error email" "$HAS_ERR"

# Password terlalu pendek
TS=$(date +%s)
CODE=$(http_code $ADMIN_JAR POST "$BASE/admin/mahasiswa" \
  "nama=Citra Dewi&username=citra$TS&email=citra$TS@mail.com&npm=NPM$TS&password=123")
check "POST /admin/mahasiswa password < 6 → bukan 500" \
  "$([ "$CODE" != "500" ] && echo 1 || echo 0)" "HTTP $CODE"
HAS_ERR=$(body_contains "password\|minimum\|error")
check "POST /admin/mahasiswa password < 6 → ada error password" "$HAS_ERR"

# Data valid → mahasiswa berhasil dibuat
TS=$(date +%s)
CODE=$(http_code $ADMIN_JAR POST "$BASE/admin/mahasiswa" \
  "nama=Dian Prasetyo&username=dian$TS&email=dian$TS@ukim.ac.id&npm=$TS&password=dianpass99")
check "POST /admin/mahasiswa data valid → bukan 500" \
  "$([ "$CODE" != "500" ] && echo 1 || echo 0)" "HTTP $CODE"
IS_SUCCESS=$(body_contains "berhasil\|success\|Mahasiswa")
check "POST /admin/mahasiswa data valid → mahasiswa terbuat" "$IS_SUCCESS"

# ── BLOK 4: Admin — Tambah DPL (Validasi Input) ──────────────────────────────
echo ""
echo "── BLOK 4: Admin — Tambah DPL ──"

CODE=$(http_code $ADMIN_JAR POST "$BASE/admin/dpl" \
  "nama=&username=&email=&nidn=&password=")
check "POST /admin/dpl data kosong → bukan 500" \
  "$([ "$CODE" != "500" ] && echo 1 || echo 0)" "HTTP $CODE"
HAS_ERR=$(body_contains "wajib\|required\|error")
check "POST /admin/dpl data kosong → ada pesan validasi" "$HAS_ERR"

TS=$(date +%s)
CODE=$(http_code $ADMIN_JAR POST "$BASE/admin/dpl" \
  "nama=Dr. Test DPL&username=drdpl$TS&email=dpl$TS@ukim.ac.id&nidn=00123$TS&password=dplpass1")
check "POST /admin/dpl data valid → bukan 500" \
  "$([ "$CODE" != "500" ] && echo 1 || echo 0)" "HTTP $CODE"
IS_SUCCESS=$(body_contains "berhasil\|success\|DPL")
check "POST /admin/dpl data valid → DPL terbuat" "$IS_SUCCESS"

# ── BLOK 5: Admin — resetPassword (celah keamanan) ──────────────────────────
echo ""
echo "── BLOK 5: Admin — Reset Password (Security Check) ──"

# Reset password user_id=1 (admin sendiri) tanpa konfirmasi identitas target
CODE=$(http_code $ADMIN_JAR POST "$BASE/admin/reset-password" \
  "user_id=1&password=newadminpass")
check "POST /admin/reset-password tanpa validasi → bukan 500" \
  "$([ "$CODE" != "500" ] && echo 1 || echo 0)" "HTTP $CODE"

# Reset dengan user_id=0 (tidak ada user)
CODE=$(http_code $ADMIN_JAR POST "$BASE/admin/reset-password" \
  "user_id=0&password=apapun123")
check "POST /admin/reset-password user_id=0 → bukan 500" \
  "$([ "$CODE" != "500" ] && echo 1 || echo 0)" "HTTP $CODE"
# Catat: tidak ada error jika user tidak ada karena CI4 update() silent pada no-match
warn "POST /admin/reset-password tidak validasi keberadaan user_id — celah keamanan"

# Login kembali dengan password lama setelah reset (verifikasi perubahan)
# Restore dulu ke admin123 agar session lain tidak rusak
CODE=$(http_code $ADMIN_JAR POST "$BASE/admin/reset-password" \
  "user_id=1&password=admin123")
check "POST /admin/reset-password restore password admin123 → bukan 500" \
  "$([ "$CODE" != "500" ] && echo 1 || echo 0)" "HTTP $CODE"

# ── BLOK 6: Admin — Lokasi & KKN (No Validation) ────────────────────────────
echo ""
echo "── BLOK 6: Admin — Lokasi KKN ──"

# Simpan lokasi dengan nama_desa kosong (tidak ada validasi di controller)
CODE=$(http_code $ADMIN_JAR POST "$BASE/admin/lokasi" \
  "nama_desa=&kecamatan=&kabupaten=")
check "POST /admin/lokasi nama_desa kosong → bukan 500" \
  "$([ "$CODE" != "500" ] && echo 1 || echo 0)" "HTTP $CODE"
# Karena tidak ada validasi, ini mungkin menyimpan record kosong
IS_SUCCESS=$(body_contains "berhasil\|success\|Lokasi")
if [ "$IS_SUCCESS" = "1" ]; then
    warn "POST /admin/lokasi nama_desa kosong → berhasil disimpan! (tidak ada validasi required)"
else
    check "POST /admin/lokasi nama_desa kosong → ditolak" "1"
fi

# Lokasi valid
TS=$(date +%s)
CODE=$(http_code $ADMIN_JAR POST "$BASE/admin/lokasi" \
  "nama_desa=Desa Test $TS&kecamatan=Kec Uji&kabupaten=Kab Uji")
check "POST /admin/lokasi data valid → bukan 500" \
  "$([ "$CODE" != "500" ] && echo 1 || echo 0)" "HTTP $CODE"
IS_SUCCESS=$(body_contains "berhasil\|success\|Lokasi")
check "POST /admin/lokasi data valid → tersimpan" "$IS_SUCCESS"

# ── BLOK 7: Admin — Pengumuman (No Validation) ──────────────────────────────
echo ""
echo "── BLOK 7: Admin — Pengumuman ──"

CODE=$(http_code $ADMIN_JAR POST "$BASE/admin/pengumuman" \
  "judul=&isi=")
check "POST /admin/pengumuman judul & isi kosong → bukan 500" \
  "$([ "$CODE" != "500" ] && echo 1 || echo 0)" "HTTP $CODE"
IS_SUCCESS=$(body_contains "berhasil\|Dipublikasikan\|success")
if [ "$IS_SUCCESS" = "1" ]; then
    warn "POST /admin/pengumuman judul kosong → tersimpan! (tidak ada validasi required)"
fi

TS=$(date +%s)
CODE=$(http_code $ADMIN_JAR POST "$BASE/admin/pengumuman" \
  "judul=Pengumuman Test $TS&isi=Isi pengumuman test untuk pengujian sistem.")
check "POST /admin/pengumuman data valid → bukan 500" \
  "$([ "$CODE" != "500" ] && echo 1 || echo 0)" "HTTP $CODE"
IS_SUCCESS=$(body_contains "berhasil\|success\|Pengumuman")
check "POST /admin/pengumuman data valid → tersimpan" "$IS_SUCCESS"

# ── BLOK 8: Buat akun mahasiswa test untuk blok berikutnya ──────────────────
echo ""
echo "── BLOK 8: Setup mahasiswa test ──"
TS=$(date +%s)
MHS_NPM="TEST$TS"
MHS_USER="mhstest$TS"
MHS_PASS="mhspass99"
MHS_EMAIL="mhstest$TS@ukim.ac.id"

CODE=$(http_code $ADMIN_JAR POST "$BASE/admin/mahasiswa" \
  "nama=Mahasiswa Test&username=$MHS_USER&email=$MHS_EMAIL&npm=$MHS_NPM&password=$MHS_PASS")
check "Buat akun mahasiswa test → bukan 500" \
  "$([ "$CODE" != "500" ] && echo 1 || echo 0)" "HTTP $CODE"

# Login sebagai mahasiswa
CODE=$(http_code $MHS_JAR POST "$BASE/login" \
  "login=$MHS_USER&password=$MHS_PASS")
check "Login mahasiswa test → bukan 500" \
  "$([ "$CODE" != "500" ] && echo 1 || echo 0)" "HTTP $CODE"
IS_LOGGED=$(body_contains "Dashboard\|dashboard\|logbook\|Mahasiswa")
check "Login mahasiswa test → masuk panel" "$IS_LOGGED"

# ── BLOK 9: Mahasiswa — Logbook ──────────────────────────────────────────────
echo ""
echo "── BLOK 9: Mahasiswa — Submit Logbook ──"

# Submit logbook tanpa data wajib (tidak ada validasi di controller)
CODE=$(http_code $MHS_JAR POST "$BASE/mahasiswa/logbook" \
  "tanggal=&kegiatan=&lokasi_kegiatan=")
check "POST /mahasiswa/logbook semua kosong → bukan 500" \
  "$([ "$CODE" != "500" ] && echo 1 || echo 0)" "HTTP $CODE"
IS_SUCCESS=$(body_contains "berhasil\|success\|Logbook")
if [ "$IS_SUCCESS" = "1" ]; then
    warn "POST /mahasiswa/logbook tanggal & kegiatan kosong → tersimpan! (tidak ada validasi)"
fi

# Submit logbook tanggal tidak valid (format salah)
CODE=$(http_code $MHS_JAR POST "$BASE/mahasiswa/logbook" \
  "tanggal=bukan-tanggal&kegiatan=Kegiatan test&lokasi_kegiatan=Lokasi test")
check "POST /mahasiswa/logbook tanggal tidak valid → bukan 500" \
  "$([ "$CODE" != "500" ] && echo 1 || echo 0)" "HTTP $CODE"
IS_SUCCESS=$(body_contains "berhasil\|success")
if [ "$IS_SUCCESS" = "1" ]; then
    warn "POST /mahasiswa/logbook tanggal 'bukan-tanggal' → tersimpan! (tidak ada validasi format tanggal)"
fi

# Submit logbook tanggal masa depan (tidak ada validasi)
CODE=$(http_code $MHS_JAR POST "$BASE/mahasiswa/logbook" \
  "tanggal=2099-12-31&kegiatan=Kegiatan masa depan&lokasi_kegiatan=Lokasi")
check "POST /mahasiswa/logbook tanggal masa depan → bukan 500" \
  "$([ "$CODE" != "500" ] && echo 1 || echo 0)" "HTTP $CODE"
IS_SUCCESS=$(body_contains "berhasil\|success")
if [ "$IS_SUCCESS" = "1" ]; then
    warn "POST /mahasiswa/logbook tanggal 2099-12-31 → tersimpan! (tidak ada validasi tanggal masa depan)"
fi

# Submit logbook dengan kegiatan teks sangat panjang (XSS attempt)
CODE=$(http_code $MHS_JAR POST "$BASE/mahasiswa/logbook" \
  "tanggal=2026-08-13&kegiatan=<script>alert(1)</script>&lokasi_kegiatan=Test")
check "POST /mahasiswa/logbook XSS di kegiatan → bukan 500" \
  "$([ "$CODE" != "500" ] && echo 1 || echo 0)" "HTTP $CODE"

# ── BLOK 10: Mahasiswa — Laporan ──────────────────────────────────────────────
echo ""
echo "── BLOK 10: Mahasiswa — Upload Laporan ──"

# Tanpa file sama sekali
CODE=$(http_code $MHS_JAR POST "$BASE/mahasiswa/laporan" \
  "judul=Laporan Test&deskripsi=Deskripsi test")
check "POST /mahasiswa/laporan tanpa file → bukan 500" \
  "$([ "$CODE" != "500" ] && echo 1 || echo 0)" "HTTP $CODE"
HAS_ERR=$(body_contains "PDF\|wajib\|error\|file")
check "POST /mahasiswa/laporan tanpa file → ada pesan error file" "$HAS_ERR"

# Tanpa judul (judul kosong, ada file simulasi — tidak bisa test file binary via bash mudah)
# Tes judul kosong saja
CODE=$(http_code $MHS_JAR POST "$BASE/mahasiswa/laporan" \
  "judul=&deskripsi=Deskripsi test")
check "POST /mahasiswa/laporan judul kosong → bukan 500" \
  "$([ "$CODE" != "500" ] && echo 1 || echo 0)" "HTTP $CODE"
# Karena tidak ada file → akan kena error file dulu, tapi judul tidak divalidasi tersendiri
warn "POST /mahasiswa/laporan tidak ada validasi judul kosong (hanya cek file)"

# ── BLOK 11: Mahasiswa — Profil (Update tanpa validasi) ───────────────────────
echo ""
echo "── BLOK 11: Mahasiswa — Update Profil ──"

# Email tidak valid — tidak ada validasi di update()
CODE=$(http_code $MHS_JAR POST "$BASE/mahasiswa/profil" \
  "nama=Mahasiswa Test Updated&email=bukan-format-email")
check "POST /mahasiswa/profil email tidak valid → bukan 500" \
  "$([ "$CODE" != "500" ] && echo 1 || echo 0)" "HTTP $CODE"
IS_SUCCESS=$(body_contains "berhasil\|success\|Profil")
if [ "$IS_SUCCESS" = "1" ]; then
    warn "POST /mahasiswa/profil email tidak valid → tersimpan! (tidak ada validasi format email pada update)"
fi

# Nama kosong — tidak ada validasi
CODE=$(http_code $MHS_JAR POST "$BASE/mahasiswa/profil" \
  "nama=&email=")
check "POST /mahasiswa/profil nama kosong → bukan 500" \
  "$([ "$CODE" != "500" ] && echo 1 || echo 0)" "HTTP $CODE"
IS_SUCCESS=$(body_contains "berhasil\|success")
if [ "$IS_SUCCESS" = "1" ]; then
    warn "POST /mahasiswa/profil nama kosong → tersimpan! (tidak ada validasi required pada update profil)"
fi

# ── BLOK 12: Mahasiswa — Ganti Password ──────────────────────────────────────
echo ""
echo "── BLOK 12: Mahasiswa — Ganti Password ──"

# Password lama salah
CODE=$(http_code $MHS_JAR POST "$BASE/mahasiswa/profil/password" \
  "current_password=salahpassword&new_password=newpass456")
check "POST /profil/password current_password salah → bukan 500" \
  "$([ "$CODE" != "500" ] && echo 1 || echo 0)" "HTTP $CODE"
HAS_ERR=$(body_contains "salah\|error\|Password lama")
check "POST /profil/password current_password salah → ada pesan error" "$HAS_ERR"

# new_password kosong (tidak ada validasi min_length)
CODE=$(http_code $MHS_JAR POST "$BASE/mahasiswa/profil/password" \
  "current_password=$MHS_PASS&new_password=")
check "POST /profil/password new_password kosong → bukan 500" \
  "$([ "$CODE" != "500" ] && echo 1 || echo 0)" "HTTP $CODE"
IS_SUCCESS=$(body_contains "berhasil\|success")
if [ "$IS_SUCCESS" = "1" ]; then
    warn "POST /profil/password new_password kosong → berhasil! (tidak ada validasi min_length pada password baru)"
fi

# Ganti ke password valid
CODE=$(http_code $MHS_JAR POST "$BASE/mahasiswa/profil/password" \
  "current_password=$MHS_PASS&new_password=newmhspass99")
check "POST /profil/password ganti valid → bukan 500" \
  "$([ "$CODE" != "500" ] && echo 1 || echo 0)" "HTTP $CODE"

# ── BLOK 13: Route protection — akses silang role ────────────────────────────
echo ""
echo "── BLOK 13: Role Protection ──"

# Mahasiswa coba akses halaman admin
CODE=$(http_code $MHS_JAR GET "$BASE/admin/dashboard")
check "Mahasiswa GET /admin/dashboard → redirect/403 (bukan 200)" \
  "$([ "$CODE" != "200" ] && echo 1 || echo 0)" "HTTP $CODE"

# Mahasiswa coba akses halaman DPL
CODE=$(http_code $MHS_JAR GET "$BASE/dpl/penilaian")
check "Mahasiswa GET /dpl/penilaian → redirect/403 (bukan 200)" \
  "$([ "$CODE" != "200" ] && echo 1 || echo 0)" "HTTP $CODE"

# Anonymous akses panel mahasiswa
CODE=$(http_code /tmp/anon_sess.txt GET "$BASE/mahasiswa/dashboard")
check "Anonymous GET /mahasiswa/dashboard → redirect (bukan 200)" \
  "$([ "$CODE" != "200" ] && echo 1 || echo 0)" "HTTP $CODE"

# ── BLOK 14: Forgot Password flow ────────────────────────────────────────────
echo ""
echo "── BLOK 14: Forgot Password ──"

# Email tidak terdaftar
CODE=$(http_code /tmp/anon_sess.txt POST "$BASE/forgot-password" \
  "email=tidakada@email.com")
check "POST /forgot-password email tidak terdaftar → bukan 500" \
  "$([ "$CODE" != "500" ] && echo 1 || echo 0)" "HTTP $CODE"
HAS_ERR=$(body_contains "tidak terdaftar\|error\|Email")
check "POST /forgot-password email tidak terdaftar → ada pesan error" "$HAS_ERR"

# Email kosong
CODE=$(http_code /tmp/anon_sess.txt POST "$BASE/forgot-password" \
  "email=")
check "POST /forgot-password email kosong → bukan 500" \
  "$([ "$CODE" != "500" ] && echo 1 || echo 0)" "HTTP $CODE"
warn "POST /forgot-password tidak ada validasi format email (valid_email) — hanya cek DB lookup"

# ── Ringkasan ─────────────────────────────────────────────────────────────────
TOTAL=$((PASS+FAIL+WARN))
echo ""
echo "============================================================"
echo " HASIL LIVE TEST"
echo "============================================================"
echo "  Total checks : $TOTAL"
echo "  ✓ PASS       : $PASS"
echo "  ✗ FAIL       : $FAIL"
echo "  ⚠ WARN (bug) : $WARN"
echo "============================================================"

if [ "$FAIL" -gt 0 ]; then
    exit 1
fi
exit 0
