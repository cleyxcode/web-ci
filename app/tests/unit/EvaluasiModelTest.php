<?php

declare(strict_types=1);

use App\Models\EvaluasiModel;
use CodeIgniter\Test\CIUnitTestCase;

/**
 * @internal
 */
final class EvaluasiModelTest extends CIUnitTestCase
{
    public function testHitungSkorRataRataAspek(): void
    {
        $this->assertSame(5.0, EvaluasiModel::hitungSkor(5, 5, 5, 5));
        $this->assertSame(3.5, EvaluasiModel::hitungSkor(4, 3, 4, 3));
        $this->assertSame(1.0, EvaluasiModel::hitungSkor(1, 1, 1, 1));
    }

    public function testKategoriFromSkor(): void
    {
        $this->assertSame('Sangat Baik', EvaluasiModel::kategoriFromSkor(4.5));
        $this->assertSame('Sangat Baik', EvaluasiModel::kategoriFromSkor(5.0));
        $this->assertSame('Baik', EvaluasiModel::kategoriFromSkor(3.5));
        $this->assertSame('Baik', EvaluasiModel::kategoriFromSkor(4.49));
        $this->assertSame('Cukup', EvaluasiModel::kategoriFromSkor(2.5));
        $this->assertSame('Kurang', EvaluasiModel::kategoriFromSkor(2.49));
        $this->assertSame('Kurang', EvaluasiModel::kategoriFromSkor(1.0));
    }
}
