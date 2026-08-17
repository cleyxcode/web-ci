<?php

declare(strict_types=1);

use App\Libraries\NilaiLib;
use CodeIgniter\Test\CIUnitTestCase;

/**
 * @internal
 */
final class NilaiLibEdgeCaseTest extends CIUnitTestCase
{
    public function testNilaiAkhirSemuaNol(): void
    {
        $this->assertSame(0.0, NilaiLib::hitungNilaiAkhir(0, 0, 0));
    }

    public function testNilaiAkhirSemuaSeratus(): void
    {
        $this->assertSame(100.0, NilaiLib::hitungNilaiAkhir(100, 100, 100));
    }

    public function testBobotLaporanLebihBerat(): void
    {
        $tinggi = NilaiLib::hitungNilaiAkhir(50, 50, 100);
        $rendah = NilaiLib::hitungNilaiAkhir(50, 50, 0);
        $this->assertSame(70.0, $tinggi);
        $this->assertSame(30.0, $rendah);
    }

    public function testGradeBoundaries(): void
    {
        $this->assertSame('A', NilaiLib::gradeFromScore(85.0));
        $this->assertSame('B', NilaiLib::gradeFromScore(84.99));
        $this->assertSame('B', NilaiLib::gradeFromScore(70.0));
        $this->assertSame('BC', NilaiLib::gradeFromScore(69.99));
        $this->assertSame('BC', NilaiLib::gradeFromScore(65.0));
        $this->assertSame('C', NilaiLib::gradeFromScore(55.0));
        $this->assertSame('D', NilaiLib::gradeFromScore(54.99));
        $this->assertSame('D', NilaiLib::gradeFromScore(0.0));
    }
}
