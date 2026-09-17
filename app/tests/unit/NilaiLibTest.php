<?php

declare(strict_types=1);

use App\Libraries\NilaiLib;
use CodeIgniter\Test\CIUnitTestCase;

/**
 * @internal
 */
final class NilaiLibTest extends CIUnitTestCase
{
    public function testHitungNilaiAkhirBobot(): void
    {
        $this->assertSame(79.0, NilaiLib::hitungNilaiAkhir(100, 50, 85));
        $this->assertSame(70.0, NilaiLib::hitungNilaiAkhir(70, 70, 70));
        $this->assertSame(84.5, NilaiLib::hitungNilaiAkhir(80, 80, 91.25));
        $this->assertSame(85.0, NilaiLib::hitungNilaiAkhir(100, 100, 62.5));
    }

    public function testGradeFromScore(): void
    {
        $this->assertSame('A', NilaiLib::gradeFromScore(85));
        $this->assertSame('A', NilaiLib::gradeFromScore(100));
        $this->assertSame('B', NilaiLib::gradeFromScore(70));
        $this->assertSame('BC', NilaiLib::gradeFromScore(65));
        $this->assertSame('C', NilaiLib::gradeFromScore(55));
        $this->assertSame('D', NilaiLib::gradeFromScore(54.9));
    }

    public function testClampNilaiDiLuarRange(): void
    {
        $this->assertSame(100.0, NilaiLib::hitungNilaiAkhir(200, 200, 200));
        $this->assertSame(0.0, NilaiLib::hitungNilaiAkhir(-10, -5, -1));
    }
}
