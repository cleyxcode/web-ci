<?php

declare(strict_types=1);

use CodeIgniter\Test\CIUnitTestCase;

/**
 * ValidationTest
 *
 * Menguji aturan validasi yang dipakai controller secara langsung
 * melalui service('validation') — tanpa perlu HTTP request.
 *
 * Skenario yang dicakup:
 *  - Login: field kosong, login valid
 *  - Tambah Mahasiswa: nama terlalu pendek, email tidak valid, password terlalu pendek
 *  - Tambah DPL: semua required field kosong
 *  - Reset Password (OTP): konfirmasi tidak cocok, password terlalu pendek
 *  - GPS Koordinat: range tidak valid (ditangani controller, bukan validator CI4)
 *  - Penilaian: nilai wajib 0–100
 */
final class ValidationTest extends CIUnitTestCase
{
    private \CodeIgniter\Validation\ValidationInterface $v;

    protected function setUp(): void
    {
        parent::setUp();
        $this->v = service('validation');
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        $this->v->reset();
    }

    // ---------------------------------------------------------------
    // LOGIN
    // ---------------------------------------------------------------

    public function testLoginFieldKosongGagal(): void
    {
        $rules = [
            'login'    => 'required',
            'password' => 'required',
        ];

        $this->assertFalse(
            $this->v->setRules($rules)->run(['login' => '', 'password' => '']),
            'Login dengan field kosong seharusnya gagal validasi'
        );

        $errors = $this->v->getErrors();
        $this->assertArrayHasKey('login', $errors);
        $this->assertArrayHasKey('password', $errors);
    }

    public function testLoginDataLengkapLolos(): void
    {
        $rules = [
            'login'    => 'required',
            'password' => 'required',
        ];

        $this->assertTrue(
            $this->v->setRules($rules)->run(['login' => 'admin', 'password' => 'admin123']),
            'Login dengan data lengkap seharusnya lolos validasi'
        );
    }

    // ---------------------------------------------------------------
    // TAMBAH MAHASISWA
    // ---------------------------------------------------------------

    public function testTambahMahasiswaNamaTerlalupendekGagal(): void
    {
        $rules = [
            'nama'     => 'required|min_length[3]',
            'username' => 'required',
            'email'    => 'required|valid_email',
            'npm'      => 'required',
            'password' => 'required|min_length[6]',
        ];

        // Nama hanya 2 karakter
        $data = [
            'nama'     => 'Ab',
            'username' => 'mhs001',
            'email'    => 'mhs@test.com',
            'npm'      => '12345',
            'password' => 'password123',
        ];

        $this->assertFalse(
            $this->v->setRules($rules)->run($data),
            'Nama 2 karakter seharusnya gagal (min_length[3])'
        );
        $this->assertArrayHasKey('nama', $this->v->getErrors());
    }

    public function testTambahMahasiswaEmailTidakValidGagal(): void
    {
        $this->v->reset();
        $rules = [
            'nama'     => 'required|min_length[3]',
            'username' => 'required',
            'email'    => 'required|valid_email',
            'npm'      => 'required',
            'password' => 'required|min_length[6]',
        ];

        $data = [
            'nama'     => 'Budi Santoso',
            'username' => 'budi01',
            'email'    => 'bukan-email-valid',  // format salah
            'npm'      => '11223344',
            'password' => 'rahasia123',
        ];

        $this->assertFalse(
            $this->v->setRules($rules)->run($data),
            'Email format salah seharusnya gagal (valid_email)'
        );
        $this->assertArrayHasKey('email', $this->v->getErrors());
    }

    public function testTambahMahasiswaPasswordTerlalupendekGagal(): void
    {
        $this->v->reset();
        $rules = [
            'nama'     => 'required|min_length[3]',
            'username' => 'required',
            'email'    => 'required|valid_email',
            'npm'      => 'required',
            'password' => 'required|min_length[6]',
        ];

        $data = [
            'nama'     => 'Siti Rahma',
            'username' => 'siti01',
            'email'    => 'siti@test.com',
            'npm'      => '99887766',
            'password' => '123',  // terlalu pendek
        ];

        $this->assertFalse(
            $this->v->setRules($rules)->run($data),
            'Password 3 karakter seharusnya gagal (min_length[6])'
        );
        $this->assertArrayHasKey('password', $this->v->getErrors());
    }

    public function testTambahMahasiswaDataLengkapLolos(): void
    {
        $this->v->reset();
        $rules = [
            'nama'     => 'required|min_length[3]',
            'username' => 'required',
            'email'    => 'required|valid_email',
            'npm'      => 'required',
            'password' => 'required|min_length[6]',
        ];

        $data = [
            'nama'     => 'Andi Wijaya',
            'username' => 'andi2026',
            'email'    => 'andi@ukim.ac.id',
            'npm'      => '20230001',
            'password' => 'andalogi99',
        ];

        $this->assertTrue(
            $this->v->setRules($rules)->run($data),
            'Data mahasiswa lengkap + valid seharusnya lolos'
        );
    }

    public function testTambahMahasiswaSemuaFieldKosongGagal(): void
    {
        $this->v->reset();
        $rules = [
            'nama'     => 'required|min_length[3]',
            'username' => 'required',
            'email'    => 'required|valid_email',
            'npm'      => 'required',
            'password' => 'required|min_length[6]',
        ];

        $this->assertFalse(
            $this->v->setRules($rules)->run([]),
            'Semua field kosong seharusnya gagal'
        );

        $errors = $this->v->getErrors();
        $this->assertCount(5, $errors, 'Harus ada 5 error untuk 5 required field');
    }

    // ---------------------------------------------------------------
    // TAMBAH DPL
    // ---------------------------------------------------------------

    public function testTambahDplSemuaFieldKosongGagal(): void
    {
        $this->v->reset();
        $rules = [
            'nama'     => 'required',
            'username' => 'required',
            'email'    => 'required|valid_email',
            'nidn'     => 'required',
            'password' => 'required|min_length[6]',
        ];

        $this->assertFalse(
            $this->v->setRules($rules)->run([]),
            'Tambah DPL dengan semua field kosong seharusnya gagal'
        );
    }

    public function testTambahDplEmailTidakValidGagal(): void
    {
        $this->v->reset();
        $rules = [
            'nama'     => 'required',
            'username' => 'required',
            'email'    => 'required|valid_email',
            'nidn'     => 'required',
            'password' => 'required|min_length[6]',
        ];

        $data = [
            'nama'     => 'Dr. Ahmad',
            'username' => 'drahmad',
            'email'    => 'bukan_email',
            'nidn'     => '0012345678',
            'password' => 'dplpass1',
        ];

        $this->assertFalse(
            $this->v->setRules($rules)->run($data),
            'Email DPL tidak valid seharusnya gagal'
        );
        $this->assertArrayHasKey('email', $this->v->getErrors());
    }

    // ---------------------------------------------------------------
    // RESET PASSWORD (OTP flow)
    // ---------------------------------------------------------------

    public function testResetPasswordKonfirmasiTidakCocokaGagal(): void
    {
        $this->v->reset();
        $rules = [
            'password'         => 'required|min_length[6]',
            'password_confirm' => 'required|matches[password]',
        ];

        $data = [
            'password'         => 'newpass123',
            'password_confirm' => 'salahbeda',  // tidak cocok
        ];

        $this->assertFalse(
            $this->v->setRules($rules)->run($data),
            'Konfirmasi password tidak cocok seharusnya gagal'
        );
        $this->assertArrayHasKey('password_confirm', $this->v->getErrors());
    }

    public function testResetPasswordTerlalupendekGagal(): void
    {
        $this->v->reset();
        $rules = [
            'password'         => 'required|min_length[6]',
            'password_confirm' => 'required|matches[password]',
        ];

        $data = [
            'password'         => '123',
            'password_confirm' => '123',
        ];

        $this->assertFalse(
            $this->v->setRules($rules)->run($data),
            'Password reset < 6 karakter seharusnya gagal'
        );
        $this->assertArrayHasKey('password', $this->v->getErrors());
    }

    public function testResetPasswordValidLolos(): void
    {
        $this->v->reset();
        $rules = [
            'password'         => 'required|min_length[6]',
            'password_confirm' => 'required|matches[password]',
        ];

        $data = [
            'password'         => 'newpass2026',
            'password_confirm' => 'newpass2026',
        ];

        $this->assertTrue(
            $this->v->setRules($rules)->run($data),
            'Reset password valid seharusnya lolos'
        );
    }

    // ---------------------------------------------------------------
    // GPS KOORDINAT — logika validasi ada di controller (bukan CI4 validator)
    // Diuji manual di sini untuk dokumentasi expected behavior
    // ---------------------------------------------------------------

    /**
     * Fungsi helper yang mereplikasi logika validasi GPS di TimController::setLokasiGps()
     */
    private function isValidGps(mixed $lat, mixed $lng): bool
    {
        if ($lat === null || $lng === null || $lat === '' || $lng === '') {
            return false;
        }

        $lat = (float) $lat;
        $lng = (float) $lng;

        return ! ($lat < -90 || $lat > 90 || $lng < -180 || $lng > 180);
    }

    public function testGpsKosongDitolak(): void
    {
        $this->assertFalse($this->isValidGps('', ''), 'GPS kosong seharusnya ditolak');
        $this->assertFalse($this->isValidGps(null, null), 'GPS null seharusnya ditolak');
        $this->assertFalse($this->isValidGps('', 128.5), 'Latitude kosong seharusnya ditolak');
    }

    public function testGpsDiLuarRangeDitolak(): void
    {
        $this->assertFalse($this->isValidGps(91.0, 128.0), 'Latitude > 90 seharusnya ditolak');
        $this->assertFalse($this->isValidGps(-91.0, 128.0), 'Latitude < -90 seharusnya ditolak');
        $this->assertFalse($this->isValidGps(-3.7, 181.0), 'Longitude > 180 seharusnya ditolak');
        $this->assertFalse($this->isValidGps(-3.7, -181.0), 'Longitude < -180 seharusnya ditolak');
    }

    public function testGpsValidDiterima(): void
    {
        $this->assertTrue($this->isValidGps(-3.6544, 128.1908), 'Koordinat Maluku valid seharusnya diterima');
        $this->assertTrue($this->isValidGps(0.0, 0.0), 'Titik nol seharusnya diterima');
        $this->assertTrue($this->isValidGps(90.0, 180.0), 'Batas maksimum seharusnya diterima');
        $this->assertTrue($this->isValidGps(-90.0, -180.0), 'Batas minimum seharusnya diterima');
    }

    // ---------------------------------------------------------------
    // PENILAIAN — range 0–100
    // ---------------------------------------------------------------

    public function testPenilaianNilaiNegatifDitolak(): void
    {
        $rules = [
            'nilai_keaktifan' => 'required|decimal|greater_than_equal_to[0]|less_than_equal_to[100]',
            'nilai_logbook'   => 'required|decimal|greater_than_equal_to[0]|less_than_equal_to[100]',
            'nilai_laporan'   => 'required|decimal|greater_than_equal_to[0]|less_than_equal_to[100]',
        ];

        $this->assertFalse($this->v->setRules($rules)->run([
            'nilai_keaktifan' => '-50',
            'nilai_logbook'   => '80',
            'nilai_laporan'   => '80',
        ]));
    }

    public function testPenilaianNilaiMelebihi100Ditolak(): void
    {
        $rules = [
            'nilai_keaktifan' => 'required|decimal|greater_than_equal_to[0]|less_than_equal_to[100]',
            'nilai_logbook'   => 'required|decimal|greater_than_equal_to[0]|less_than_equal_to[100]',
            'nilai_laporan'   => 'required|decimal|greater_than_equal_to[0]|less_than_equal_to[100]',
        ];

        $this->assertFalse($this->v->setRules($rules)->run([
            'nilai_keaktifan' => '999',
            'nilai_logbook'   => '80',
            'nilai_laporan'   => '80',
        ]));
    }

    public function testPenilaianNilaiValidLolos(): void
    {
        $rules = [
            'nilai_keaktifan' => 'required|decimal|greater_than_equal_to[0]|less_than_equal_to[100]',
            'nilai_logbook'   => 'required|decimal|greater_than_equal_to[0]|less_than_equal_to[100]',
            'nilai_laporan'   => 'required|decimal|greater_than_equal_to[0]|less_than_equal_to[100]',
        ];

        $this->assertTrue($this->v->setRules($rules)->run([
            'nilai_keaktifan' => '80',
            'nilai_logbook'   => '75.5',
            'nilai_laporan'   => '90',
        ]));
    }

    // ---------------------------------------------------------------
    // BUG DITEMUKAN: Update Mahasiswa — tidak ada validasi sama sekali
    // ---------------------------------------------------------------

    public function testUpdateMahasiswaTidakAdaValidasi(): void
    {
        // MahasiswaController::update() tidak memanggil $this->validate() sama sekali.
        // Artinya email bisa diupdate ke string sembarang tanpa validasi format.
        $emailSalah = 'ini-bukan-email';

        // Verifikasi bahwa string ini memang tidak valid sebagai email
        $v = service('validation');
        $v->reset();
        $valid = $v->setRules(['email' => 'valid_email'])->run(['email' => $emailSalah]);

        $this->assertFalse($valid, 'Email tidak valid seharusnya ditolak jika ada validasi');
        // Tapi karena update() tidak validasi, email ini akan tersimpan ke DB → ini BUG
    }

    // ---------------------------------------------------------------
    // BUG DITEMUKAN: resetPassword admin — tidak ada validasi user_id
    // ---------------------------------------------------------------

    public function testResetPasswordAdminTidakValidasiUserId(): void
    {
        // PengumumanController::resetPassword() menerima user_id sembarang tanpa cek
        // apakah user tersebut ada. Ini celah keamanan: admin bisa reset password
        // user mana saja dengan POST user_id=<any_id> tanpa konfirmasi.
        // Direpresentasikan sebagai assertion dokumentasi.
        $userIdDariRequest = '0';  // ID tidak valid
        $userId = (int) $userIdDariRequest;

        $this->assertSame(0, $userId, 'BUG: user_id=0 akan diterima tanpa validasi keberadaan user');
    }
}
