<?php

declare(strict_types=1);

use CodeIgniter\Test\CIUnitTestCase;

/**
 * @internal
 */
final class HelperTest extends CIUnitTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        helper('app');
    }

    public function testStempelClassAndLabel(): void
    {
        $this->assertStringContainsString('stempel-menunggu', stempel_class('menunggu'));
        $this->assertStringContainsString('stempel-divalidasi', stempel_class('divalidasi'));
        $this->assertStringContainsString('stempel-diterima', stempel_class('diterima'));
        $this->assertStringContainsString('stempel-ditolak', stempel_class('ditolak'));

        $this->assertSame('Menunggu', stempel_label('menunggu'));
        $this->assertSame('Divalidasi', stempel_label('divalidasi'));
        $this->assertSame('Diterima', stempel_label('diterima'));
        $this->assertSame('Ditolak', stempel_label('ditolak'));
    }

    public function testGradeClass(): void
    {
        $this->assertStringContainsString('2D7A4F', grade_class('A'));
        $this->assertStringContainsString('C4920A', grade_class('BC'));
        $this->assertStringContainsString('B83232', grade_class('D'));
    }

    public function testPanelMenusPerRole(): void
    {
        $admin = panel_menus('admin');
        $dpl   = panel_menus('dpl');
        $mhs   = panel_menus('mahasiswa');

        $this->assertNotEmpty($admin);
        $this->assertNotEmpty($dpl);
        $this->assertNotEmpty($mhs);

        $adminUrls = array_column($admin, 'url');
        $this->assertContains('/admin/analitik', $adminUrls);
        $this->assertContains('/admin/export', $adminUrls);
        $this->assertContains('/admin/audit', $adminUrls);

        $dplUrls = array_column($dpl, 'url');
        $this->assertContains('/dpl/export', $dplUrls);

        $mhsUrls = array_column($mhs, 'url');
        $this->assertContains('/mahasiswa/tim', $mhsUrls);
    }

    public function testFormatAlamatAndTanggal(): void
    {
        $this->assertSame('-', format_alamat(null));
        $this->assertSame('Waai, Salahutu, Maluku Tengah', format_alamat([
            'nama_desa'  => 'Waai',
            'kecamatan'  => 'Salahutu',
            'kabupaten'  => 'Maluku Tengah',
        ]));
        $this->assertSame('-', format_tanggal(null));
        $this->assertNotSame('-', format_tanggal('2026-08-13'));
    }
}
