<?php

declare(strict_types=1);

namespace App\Libraries;

/**
 * Perhitungan nilai KKN Tematik (tanpa KNN).
 * Bobot: keaktifan 30% + logbook 30% + laporan 40%.
 */
class NilaiLib
{
    public static function hitungNilaiAkhir(float $keaktifan, float $logbook, float $laporan): float
    {
        $keaktifan = self::clamp($keaktifan);
        $logbook   = self::clamp($logbook);
        $laporan   = self::clamp($laporan);

        return round(($keaktifan * 0.3) + ($logbook * 0.3) + ($laporan * 0.4), 2);
    }

    public static function gradeFromScore(float $nilai): string
    {
        if ($nilai >= 85) {
            return 'A';
        }
        if ($nilai >= 70) {
            return 'B';
        }
        if ($nilai >= 65) {
            return 'BC';
        }
        if ($nilai >= 55) {
            return 'C';
        }

        return 'D';
    }

    private static function clamp(float $nilai): float
    {
        if ($nilai < 0) {
            return 0.0;
        }
        if ($nilai > 100) {
            return 100.0;
        }

        return $nilai;
    }
}
