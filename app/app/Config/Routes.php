<?php

use CodeIgniter\Router\RouteCollection;

/** @var RouteCollection $routes */

$routes->get('/', 'Home::index');

// Auth
$routes->get('login', 'Auth\LoginController::index');
$routes->post('login', 'Auth\LoginController::authenticate');
$routes->get('logout', 'Auth\LoginController::logout');
$routes->get('forgot-password', 'Auth\ForgotPasswordController::index');
$routes->post('forgot-password', 'Auth\ForgotPasswordController::send');
$routes->get('otp-verify', 'Auth\OtpController::verifyForm');
$routes->post('otp-verify', 'Auth\OtpController::verify');
$routes->get('reset-password', 'Auth\OtpController::resetForm');
$routes->post('reset-password', 'Auth\OtpController::reset');

// Registrasi publik ditutup: seluruh akun mahasiswa dibuat oleh admin.
$routes->get('register', 'Auth\LoginController::registrationDisabled');
$routes->post('register', 'Auth\LoginController::registrationDisabled');

// Notifikasi (semua role)
$routes->group('', ['filter' => 'auth'], static function ($routes) {
    $routes->get('notifikasi', 'NotifikasiController::index');
    $routes->get('notifikasi/api', 'NotifikasiController::apiList');
    $routes->post('notifikasi/read-all', 'NotifikasiController::markAllRead');
    $routes->post('notifikasi/(:num)/read', 'NotifikasiController::markRead/$1');
});

// Admin
$routes->group('admin', ['filter' => ['auth', 'role:admin']], static function ($routes) {
    $routes->get('/', 'Admin\DashboardController::index');
    $routes->get('dashboard', 'Admin\DashboardController::index');

    $routes->get('mahasiswa', 'Admin\MahasiswaController::index');
    $routes->get('mahasiswa/create', 'Admin\MahasiswaController::create');
    $routes->post('mahasiswa', 'Admin\MahasiswaController::store');
    $routes->get('mahasiswa/(:num)/edit', 'Admin\MahasiswaController::edit/$1');
    $routes->post('mahasiswa/(:num)', 'Admin\MahasiswaController::update/$1');
    $routes->post('mahasiswa/(:num)/delete', 'Admin\MahasiswaController::delete/$1');

    $routes->get('dpl', 'Admin\DplController::index');
    $routes->get('dpl/create', 'Admin\DplController::create');
    $routes->post('dpl', 'Admin\DplController::store');
    $routes->get('dpl/(:num)/edit', 'Admin\DplController::edit/$1');
    $routes->post('dpl/(:num)', 'Admin\DplController::update/$1');
    $routes->post('dpl/(:num)/delete', 'Admin\DplController::delete/$1');

    $routes->get('kkn', 'Admin\KknController::index');
    $routes->get('kkn/create', 'Admin\KknController::create');
    $routes->post('kkn', 'Admin\KknController::store');
    $routes->get('kkn/(:num)', 'Admin\KknController::show/$1');
    $routes->post('kkn/(:num)/anggota', 'Admin\KknController::assignAnggota/$1');
    $routes->post('kkn/(:num)/anggota/(:num)/remove', 'Admin\KknController::removeAnggota/$1/$2');
    $routes->post('kkn/(:num)/ketua', 'Admin\KknController::setKetua/$1');
    $routes->get('kkn/(:num)/edit', 'Admin\KknController::edit/$1');
    $routes->post('kkn/(:num)', 'Admin\KknController::update/$1');
    $routes->post('kkn/(:num)/delete', 'Admin\KknController::delete/$1');

    $routes->get('lokasi', 'Admin\LokasiController::index');
    $routes->get('lokasi/create', 'Admin\LokasiController::create');
    $routes->post('lokasi', 'Admin\LokasiController::store');
    $routes->get('lokasi/(:num)/edit', 'Admin\LokasiController::edit/$1');
    $routes->post('lokasi/(:num)', 'Admin\LokasiController::update/$1');
    $routes->post('lokasi/(:num)/delete', 'Admin\LokasiController::delete/$1');

    $routes->get('laporan', 'Admin\LaporanController::index');
    $routes->get('audit', 'Admin\AuditController::index');

    $routes->get('evaluasi', 'Admin\EvaluasiController::index');
    $routes->post('evaluasi/kriteria', 'Admin\EvaluasiController::storeCriteria');
    $routes->post('evaluasi/kriteria/(:num)', 'Admin\EvaluasiController::updateCriteria/$1');
    $routes->post('evaluasi/kriteria/(:num)/delete', 'Admin\EvaluasiController::deleteCriteria/$1');
    $routes->get('evaluasi/export', 'Admin\EvaluasiController::export');

    $routes->get('pengumuman', 'Admin\PengumumanController::index');
    $routes->get('pengumuman/create', 'Admin\PengumumanController::create');
    $routes->post('pengumuman', 'Admin\PengumumanController::store');
    $routes->post('pengumuman/(:num)/delete', 'Admin\PengumumanController::delete/$1');
    $routes->post('reset-password', 'Admin\PengumumanController::resetPassword');

    $routes->get('profil', 'Admin\ProfilController::index');
    $routes->post('profil', 'Admin\ProfilController::update');
    $routes->post('profil/password', 'Admin\ProfilController::changePassword');
});

// DPL
$routes->group('dpl', ['filter' => ['auth', 'role:dpl']], static function ($routes) {
    $routes->get('/', 'Dpl\DashboardController::index');
    $routes->get('dashboard', 'Dpl\DashboardController::index');
    $routes->get('monitoring', 'Dpl\MonitoringController::index');
    $routes->get('logbook', 'Dpl\LogbookController::index');
    $routes->post('logbook/(:num)/proses', 'Dpl\LogbookController::proses/$1');
    $routes->get('laporan', 'Dpl\LaporanController::index');
    $routes->post('laporan/(:num)/review', 'Dpl\LaporanController::review/$1');
    $routes->get('penilaian', 'Dpl\PenilaianController::index');
    $routes->get('penilaian/(:num)', 'Dpl\PenilaianController::form/$1');
    $routes->post('penilaian/(:num)', 'Dpl\PenilaianController::save/$1');
    $routes->get('evaluasi', 'Dpl\EvaluasiController::index');
    $routes->get('evaluasi/(:num)', 'Dpl\EvaluasiController::form/$1');
    $routes->post('evaluasi/(:num)', 'Dpl\EvaluasiController::save/$1');
    $routes->get('export', 'Dpl\ExportController::index');
    $routes->get('export/logbook', 'Dpl\ExportController::logbook');
    $routes->get('export/laporan', 'Dpl\ExportController::laporan');
    $routes->get('export/nilai', 'Dpl\ExportController::nilai');
    $routes->get('profil', 'Dpl\ProfilController::index');
    $routes->post('profil', 'Dpl\ProfilController::update');
    $routes->post('profil/password', 'Dpl\ProfilController::changePassword');
});

// Mahasiswa
$routes->group('mahasiswa', ['filter' => ['auth', 'role:mahasiswa']], static function ($routes) {
    $routes->get('/', 'Mahasiswa\DashboardController::index');
    $routes->get('dashboard', 'Mahasiswa\DashboardController::index');
    $routes->get('logbook', 'Mahasiswa\LogbookController::index');
    $routes->get('logbook/create', 'Mahasiswa\LogbookController::create');
    $routes->post('logbook', 'Mahasiswa\LogbookController::store');
    $routes->get('laporan', 'Mahasiswa\LaporanController::index');
    $routes->get('laporan/create', 'Mahasiswa\LaporanController::create');
    $routes->post('laporan', 'Mahasiswa\LaporanController::store');
    $routes->get('nilai', 'Mahasiswa\NilaiController::index');
    $routes->get('evaluasi', 'Mahasiswa\EvaluasiController::index');
    $routes->get('tim', 'Mahasiswa\TimController::index');
    $routes->post('tim/gps', 'Mahasiswa\TimController::setLokasiGps');
    $routes->get('profil', 'Mahasiswa\ProfilController::index');
    $routes->post('profil', 'Mahasiswa\ProfilController::update');
    $routes->post('profil/data', 'Mahasiswa\ProfilController::updateData');
    $routes->post('profil/password', 'Mahasiswa\ProfilController::changePassword');
});
