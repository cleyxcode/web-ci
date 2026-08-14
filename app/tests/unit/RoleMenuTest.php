<?php

declare(strict_types=1);

use CodeIgniter\Test\CIUnitTestCase;

/**
 * Menu & role alignment with use-case diagram.
 *
 * @internal
 */
final class RoleMenuTest extends CIUnitTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        helper('app');
    }

    public function testAdminMenuMatchesUseCaseWithoutPenilaian(): void
    {
        $urls = array_column(panel_menus('admin'), 'url');

        $this->assertContains('/admin/dashboard', $urls);
        $this->assertContains('/admin/mahasiswa', $urls);
        $this->assertContains('/admin/dpl', $urls);
        $this->assertContains('/admin/kkn', $urls);
        $this->assertContains('/admin/lokasi', $urls);
        $this->assertContains('/admin/laporan', $urls);

        $this->assertNotContains('/admin/analitik', $urls);
        $this->assertNotContains('/admin/export', $urls);
        $this->assertNotContains('/dpl/penilaian', $urls);
        $this->assertNotContains('/admin/export/nilai', $urls);

        foreach ($urls as $url) {
            $this->assertStringNotContainsString('penilaian', $url);
            $this->assertStringNotContainsString('nilai', $url);
            $this->assertStringNotContainsString('analitik', $url);
        }
    }

    public function testDplMenuHasPenilaianAndMonitoring(): void
    {
        $urls = array_column(panel_menus('dpl'), 'url');

        $this->assertContains('/dpl/dashboard', $urls);
        $this->assertContains('/dpl/monitoring', $urls);
        $this->assertContains('/dpl/logbook', $urls);
        $this->assertContains('/dpl/laporan', $urls);
        $this->assertContains('/dpl/penilaian', $urls);
        $this->assertContains('/dpl/evaluasi', $urls);
    }

    public function testMahasiswaMenuHasNilaiReadOnlyAndEvaluasi(): void
    {
        $urls = array_column(panel_menus('mahasiswa'), 'url');

        $this->assertContains('/mahasiswa/dashboard', $urls);
        $this->assertContains('/mahasiswa/logbook', $urls);
        $this->assertContains('/mahasiswa/laporan', $urls);
        $this->assertContains('/mahasiswa/nilai', $urls);
        $this->assertContains('/mahasiswa/evaluasi', $urls);
        $this->assertContains('/mahasiswa/tim', $urls);
        $this->assertNotContains('/mahasiswa/penilaian', $urls);
    }
}
