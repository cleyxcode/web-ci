<?php

declare(strict_types=1);

use CodeIgniter\Test\CIUnitTestCase;

/**
 * Smoke-check route definitions for each role use case via Routes.php source.
 *
 * @internal
 */
final class RoutesUseCaseTest extends CIUnitTestCase
{
    private string $routesSource = '';

    protected function setUp(): void
    {
        parent::setUp();
        $this->routesSource = (string) file_get_contents(APPPATH . 'Config/Routes.php');
    }

    public function testAdminCoreRoutesRegistered(): void
    {
        $this->assertStringContainsString("role:admin", $this->routesSource);
        $this->assertStringContainsString('Admin\DashboardController', $this->routesSource);
        $this->assertStringContainsString('Admin\MahasiswaController', $this->routesSource);
        $this->assertStringContainsString('Admin\DplController', $this->routesSource);
        $this->assertStringContainsString('Admin\KknController', $this->routesSource);
        $this->assertStringContainsString('Admin\LokasiController', $this->routesSource);
        $this->assertStringContainsString('Admin\LaporanController', $this->routesSource);

        $this->assertStringNotContainsString('Admin\AnalitikController', $this->routesSource);
        $this->assertStringNotContainsString('Admin\ExportController', $this->routesSource);
        $this->assertStringNotContainsString('KnnLib', $this->routesSource);
    }

    public function testDplPenilaianRoutesRegistered(): void
    {
        $this->assertStringContainsString("role:dpl", $this->routesSource);
        $this->assertStringContainsString('Dpl\MonitoringController', $this->routesSource);
        $this->assertStringContainsString('Dpl\LogbookController', $this->routesSource);
        $this->assertStringContainsString('Dpl\LaporanController', $this->routesSource);
        $this->assertStringContainsString('Dpl\PenilaianController', $this->routesSource);
        $this->assertStringContainsString('Dpl\EvaluasiController', $this->routesSource);
        $this->assertStringContainsString('Dpl\ExportController::nilai', $this->routesSource);
        $this->assertStringNotContainsString('KnnLib', $this->routesSource);
    }

    public function testMahasiswaRoutesRegistered(): void
    {
        $this->assertStringContainsString("role:mahasiswa", $this->routesSource);
        $this->assertStringContainsString('Mahasiswa\LogbookController', $this->routesSource);
        $this->assertStringContainsString('Mahasiswa\LaporanController', $this->routesSource);
        $this->assertStringContainsString('Mahasiswa\NilaiController', $this->routesSource);
        $this->assertStringContainsString('Mahasiswa\EvaluasiController', $this->routesSource);
        $this->assertStringContainsString('Mahasiswa\TimController', $this->routesSource);
        $this->assertStringContainsString("get('login'", $this->routesSource);
        $this->assertStringContainsString("get('logout'", $this->routesSource);
    }

    public function testDosenPendampingNotInKknPayloadSource(): void
    {
        $kkn = (string) file_get_contents(APPPATH . 'Controllers/Admin/KknController.php');
        $this->assertStringNotContainsString('dosen_pendamping', $kkn);
        $this->assertStringNotContainsString('no_hp_dosen_pendamping', $kkn);
    }
}
