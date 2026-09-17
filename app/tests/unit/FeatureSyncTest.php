<?php

declare(strict_types=1);

use CodeIgniter\Test\CIUnitTestCase;

/**
 * Dokumentasi & assertion sinkronisasi fitur antar role (tanpa KNN).
 *
 * @internal
 */
final class FeatureSyncTest extends CIUnitTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        helper('app');
    }

    public function testAdminDoesNotOwnPenilaian(): void
    {
        $urls = array_column(panel_menus('admin'), 'url');
        foreach ($urls as $url) {
            $this->assertStringNotContainsString('penilaian', $url);
            $this->assertStringNotContainsString('analitik', $url);
            $this->assertStringNotContainsString('knn', strtolower($url));
        }
    }

    public function testDplOwnsPenilaianMahasiswaReadsNilai(): void
    {
        $dpl = array_column(panel_menus('dpl'), 'url');
        $mhs = array_column(panel_menus('mahasiswa'), 'url');

        $this->assertContains('/dpl/penilaian', $dpl);
        $this->assertContains('/mahasiswa/nilai', $mhs);
        $this->assertNotContains('/mahasiswa/penilaian', $mhs);
    }

    public function testLogbookLaporanFlowMenusExist(): void
    {
        $dpl = array_column(panel_menus('dpl'), 'url');
        $mhs = array_column(panel_menus('mahasiswa'), 'url');

        $this->assertContains('/mahasiswa/logbook', $mhs);
        $this->assertContains('/dpl/logbook', $dpl);
        $this->assertContains('/mahasiswa/laporan', $mhs);
        $this->assertContains('/dpl/laporan', $dpl);
        $this->assertContains('/mahasiswa/evaluasi', $mhs);
        $this->assertContains('/dpl/evaluasi', $dpl);
    }

    public function testNoKnnLibraryFile(): void
    {
        $this->assertFileDoesNotExist(APPPATH . 'Libraries/KnnLib.php');
        $this->assertFileExists(APPPATH . 'Libraries/NilaiLib.php');
        $this->assertFileDoesNotExist(APPPATH . 'Controllers/Admin/AnalitikController.php');
    }

    public function testPenilaianModelHasNoKnnField(): void
    {
        $src = (string) file_get_contents(APPPATH . 'Models/PenilaianModel.php');
        $this->assertStringNotContainsString('prediksi_knn', $src);
        $this->assertStringNotContainsString('getTrainingData', $src);
    }
}
