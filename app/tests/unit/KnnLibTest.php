<?php

declare(strict_types=1);

use App\Libraries\KnnLib;
use CodeIgniter\Test\CIUnitTestCase;

/**
 * @internal
 */
final class KnnLibTest extends CIUnitTestCase
{
    public function testHitungNilaiAkhirBobot(): void
    {
        // 30% keaktifan + 30% logbook + 40% laporan
        $this->assertSame(79.0, KnnLib::hitungNilaiAkhir(100, 50, 85));
        $this->assertSame(70.0, KnnLib::hitungNilaiAkhir(70, 70, 70));
        // (80*0.3) + (80*0.3) + (91.25*0.4) = 24 + 24 + 36.5 = 84.5
        $this->assertSame(84.5, KnnLib::hitungNilaiAkhir(80, 80, 91.25));
        // nilai tepat 85 → Grade A
        $this->assertSame(85.0, KnnLib::hitungNilaiAkhir(100, 100, 62.5));
    }

    public function testGradeFromScore(): void
    {
        $this->assertSame('A', KnnLib::gradeFromScore(85));
        $this->assertSame('A', KnnLib::gradeFromScore(100));
        $this->assertSame('B', KnnLib::gradeFromScore(70));
        $this->assertSame('BC', KnnLib::gradeFromScore(65));
        $this->assertSame('C', KnnLib::gradeFromScore(55));
        $this->assertSame('D', KnnLib::gradeFromScore(54.9));
    }

    public function testPredictFallsBackWhenNoTraining(): void
    {
        $knn = new KnnLib();
        // features[4] = keaktifan dipakai fallback
        $this->assertSame('A', $knn->predict([0, 0, 0, 0, 90.0]));
        $this->assertSame('D', $knn->predict([0, 0, 0, 0, 40.0]));
    }

    public function testPredictMajorityVoteFromNeighbors(): void
    {
        $knn = (new KnnLib())->setTrainingData([
            ['features' => [5, 4, 2, 1, 90], 'grade' => 'A'],
            ['features' => [5, 4, 2, 1, 88], 'grade' => 'A'],
            ['features' => [1, 0, 0, 0, 40], 'grade' => 'D'],
            ['features' => [5, 4, 2, 1, 87], 'grade' => 'A'],
        ]);

        $pred = $knn->predict([5, 4, 2, 1, 89]);
        $this->assertSame('A', $pred);
    }

    public function testPredictChoosesNearestDifferentCluster(): void
    {
        $knn = (new KnnLib())->setTrainingData([
            ['features' => [0, 0, 0, 0, 40], 'grade' => 'D'],
            ['features' => [1, 0, 0, 0, 42], 'grade' => 'D'],
            ['features' => [10, 10, 10, 10, 95], 'grade' => 'A'],
        ]);

        $this->assertSame('D', $knn->predict([0, 0, 0, 0, 41]));
    }
}
