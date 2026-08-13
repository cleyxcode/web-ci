<?php

declare(strict_types=1);

use App\Libraries\KnnLib;
use CodeIgniter\Test\CIUnitTestCase;

/**
 * KnnLibEdgeCaseTest
 *
 * Menguji edge case dan skenario ekstrem pada KnnLib:
 *  - Nilai negatif
 *  - Nilai melebihi 100
 *  - Semua nilai nol
 *  - Nilai pecahan / presisi floating point
 *  - Bobot grade boundary (tepat di batas threshold)
 *  - KNN dengan k neighbor dan tie (seri suara)
 *  - Input features dengan panjang tidak konsisten
 */
final class KnnLibEdgeCaseTest extends CIUnitTestCase
{
    // ---------------------------------------------------------------
    // hitungNilaiAkhir — edge cases
    // ---------------------------------------------------------------

    public function testNilaiAkhirSemuaNol(): void
    {
        $this->assertSame(0.0, KnnLib::hitungNilaiAkhir(0, 0, 0));
    }

    public function testNilaiAkhirSemuaSeratus(): void
    {
        // 100*0.3 + 100*0.3 + 100*0.4 = 100
        $this->assertSame(100.0, KnnLib::hitungNilaiAkhir(100, 100, 100));
    }

    public function testNilaiAkhirNegatif(): void
    {
        // BUG DOKUMENTASI: tidak ada guard terhadap nilai negatif
        // hitungNilaiAkhir menerima dan menghitung nilai negatif
        $result = KnnLib::hitungNilaiAkhir(-100, -100, -100);
        $this->assertSame(-100.0, $result, 'BUG: Nilai negatif -100 menghasilkan nilai akhir -100 (tidak divalidasi)');
    }

    public function testNilaiAkhirMelebihi100(): void
    {
        // BUG DOKUMENTASI: tidak ada guard terhadap nilai > 100
        $result = KnnLib::hitungNilaiAkhir(200, 200, 200);
        $this->assertSame(200.0, $result, 'BUG: Nilai 200 menghasilkan nilai akhir 200 (tidak divalidasi)');
    }

    public function testNilaiAkhirHanyaSatuKomponenTerisi(): void
    {
        // Hanya laporan yang ada nilainya
        $this->assertSame(40.0, KnnLib::hitungNilaiAkhir(0, 0, 100));
        // Hanya keaktifan
        $this->assertSame(30.0, KnnLib::hitungNilaiAkhir(100, 0, 0));
        // Hanya logbook
        $this->assertSame(30.0, KnnLib::hitungNilaiAkhir(0, 100, 0));
    }

    public function testNilaiAkhirPembulatanDuaDesimal(): void
    {
        // (33.33*0.3) + (33.33*0.3) + (33.33*0.4) = 9.999 + 9.999 + 13.332 = 33.33
        $result = KnnLib::hitungNilaiAkhir(33.33, 33.33, 33.33);
        $this->assertSame(33.33, $result, 'Pembulatan 2 desimal harus konsisten');
    }

    public function testNilaiAkhirPecahan(): void
    {
        // (50.5*0.3) + (60.25*0.3) + (70.75*0.4)
        // = 15.15 + 18.075 + 28.3 = 61.525 → round ke 2 desimal = 61.52 (PHP banker rounding)
        $result = KnnLib::hitungNilaiAkhir(50.5, 60.25, 70.75);
        $this->assertEqualsWithDelta(61.52, $result, 0.01, 'Nilai pecahan harus dibulatkan 2 desimal');
    }

    // ---------------------------------------------------------------
    // gradeFromScore — boundary values (tepat di batas threshold)
    // ---------------------------------------------------------------

    public function testGradeBoundaryTepat85AdalahA(): void
    {
        $this->assertSame('A', KnnLib::gradeFromScore(85.0), 'Nilai tepat 85 harus A');
    }

    public function testGradeBoundary84_99AdalahB(): void
    {
        $this->assertSame('B', KnnLib::gradeFromScore(84.99), 'Nilai 84.99 harus B (< 85)');
    }

    public function testGradeBoundaryTepat70AdalahB(): void
    {
        $this->assertSame('B', KnnLib::gradeFromScore(70.0), 'Nilai tepat 70 harus B');
    }

    public function testGradeBoundary69_99AdalahBc(): void
    {
        $this->assertSame('BC', KnnLib::gradeFromScore(69.99), 'Nilai 69.99 harus BC (< 70)');
    }

    public function testGradeBoundaryTepat65AdalahBc(): void
    {
        $this->assertSame('BC', KnnLib::gradeFromScore(65.0), 'Nilai tepat 65 harus BC');
    }

    public function testGradeBoundaryTepat55AdalahC(): void
    {
        $this->assertSame('C', KnnLib::gradeFromScore(55.0), 'Nilai tepat 55 harus C');
    }

    public function testGradeBoundary54_99AdalahD(): void
    {
        $this->assertSame('D', KnnLib::gradeFromScore(54.99), 'Nilai 54.99 harus D');
    }

    public function testGradeBoundaryNol(): void
    {
        $this->assertSame('D', KnnLib::gradeFromScore(0.0), 'Nilai 0 harus D');
    }

    public function testGradeBoundaryNilaiNegatif(): void
    {
        // BUG DOKUMENTASI: nilai negatif tidak di-handle secara eksplisit → jatuh ke D
        $this->assertSame('D', KnnLib::gradeFromScore(-10.0), 'BUG: Nilai negatif mendapat grade D (tidak diblokir)');
    }

    public function testGradeBoundaryNilaiDiAtas100(): void
    {
        // BUG DOKUMENTASI: nilai > 100 mendapat grade A tanpa error
        $this->assertSame('A', KnnLib::gradeFromScore(150.0), 'BUG: Nilai 150 mendapat grade A (tidak diblokir)');
    }

    // ---------------------------------------------------------------
    // predict — edge cases
    // ---------------------------------------------------------------

    public function testPredictInputFeaturesKosong(): void
    {
        $knn    = new KnnLib();
        $result = $knn->predict([]);
        // Fallback: estimateScore mengambil $input[4] ?? 0 → 0 → grade D
        $this->assertSame('D', $result, 'Input kosong harus fallback ke grade D (keaktifan=0)');
    }

    public function testPredictTieBreakDimenangkanOlehUrutan(): void
    {
        // Seri 1:1:1 antara A, B, D — arsort() mengambil yang pertama muncul
        $knn = (new KnnLib())->setTrainingData([
            ['features' => [5, 5, 5, 5, 85], 'grade' => 'A'],
            ['features' => [5, 5, 5, 5, 70], 'grade' => 'B'],
            ['features' => [5, 5, 5, 5, 40], 'grade' => 'D'],
        ]);

        $pred = $knn->predict([5, 5, 5, 5, 65]);
        // Tidak ada majority — test memverifikasi tidak crash dan mengembalikan string valid
        $this->assertContains($pred, ['A', 'B', 'BC', 'C', 'D'], 'Prediksi harus mengembalikan grade valid');
    }

    public function testPredictSatuDataTraining(): void
    {
        $knn = (new KnnLib())->setTrainingData([
            ['features' => [10, 10, 10, 10, 90], 'grade' => 'A'],
        ]);

        $this->assertSame('A', $knn->predict([10, 10, 10, 10, 90]), 'Satu data training harus mengembalikan gradenya');
    }

    public function testPredictFeaturesLebihPendekDariTraining(): void
    {
        // Input hanya 3 feature, training punya 5 — euclideanDistance gunakan ?? 0
        $knn = (new KnnLib())->setTrainingData([
            ['features' => [1, 2, 3, 4, 5], 'grade' => 'A'],
            ['features' => [9, 8, 7, 6, 5], 'grade' => 'D'],
        ]);

        // Tidak boleh crash
        $result = $knn->predict([1, 2, 3]);
        $this->assertContains($result, ['A', 'B', 'BC', 'C', 'D'], 'Tidak boleh crash dengan features pendek');
    }

    public function testPredictFiturSangatJauh(): void
    {
        $knn = (new KnnLib())->setTrainingData([
            ['features' => [0, 0, 0, 0, 0],     'grade' => 'D'],
            ['features' => [100, 100, 100, 100, 100], 'grade' => 'A'],
        ]);

        // Input dekat ke D
        $this->assertSame('D', $knn->predict([1, 1, 1, 1, 5]), 'Input dekat cluster D harus prediksi D');
        // Input dekat ke A
        $this->assertSame('A', $knn->predict([99, 99, 99, 99, 95]), 'Input dekat cluster A harus prediksi A');
    }

    // ---------------------------------------------------------------
    // Konsistensi bobot hitungNilaiAkhir
    // ---------------------------------------------------------------

    public function testBobotTotalHarus100Persen(): void
    {
        // 30% + 30% + 40% = 100% → hitungNilaiAkhir(X,X,X) = X
        $this->assertSame(75.0, KnnLib::hitungNilaiAkhir(75, 75, 75), 'Bobot total harus 100% (30+30+40)');
        $this->assertSame(50.0, KnnLib::hitungNilaiAkhir(50, 50, 50));
    }

    public function testBobotLaporanLebihBerat(): void
    {
        // Laporan 40% > keaktifan 30% = logbook 30%
        // Sama keaktifan & logbook, laporan lebih tinggi → nilai lebih tinggi
        $denganLaporanTinggi = KnnLib::hitungNilaiAkhir(50, 50, 100); // 15+15+40 = 70
        $denganLaporanRendah = KnnLib::hitungNilaiAkhir(50, 50, 0);   // 15+15+0  = 30

        $this->assertGreaterThan($denganLaporanRendah, $denganLaporanTinggi, 'Laporan bobot 40% harus punya dampak terbesar');
        $this->assertSame(70.0, $denganLaporanTinggi);
        $this->assertSame(30.0, $denganLaporanRendah);
    }
}
