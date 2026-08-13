<?php

namespace App\Libraries;

class KnnLib
{
    private int $k = 3;

    /** @var list<array{features: list<float>, grade: string}> */
    private array $trainingData = [];

    /**
     * @param list<array{features: list<float>, grade: string}> $data
     */
    public function setTrainingData(array $data): self
    {
        $this->trainingData = $data;

        return $this;
    }

    /**
     * @param list<float> $input
     */
    public function predict(array $input): string
    {
        if ($this->trainingData === []) {
            return $this->gradeFromScore($this->estimateScore($input));
        }

        $distances = [];

        foreach ($this->trainingData as $data) {
            $distances[] = [
                'label'    => $data['grade'],
                'distance' => $this->euclideanDistance($input, $data['features']),
            ];
        }

        usort($distances, static fn ($a, $b) => $a['distance'] <=> $b['distance']);

        $kNearest = array_slice($distances, 0, min($this->k, count($distances)));

        return $this->majorityVote($kNearest);
    }

    /**
     * @param list<float> $a
     * @param list<float> $b
     */
    private function euclideanDistance(array $a, array $b): float
    {
        $sum = 0.0;

        foreach ($a as $i => $val) {
            $sum += pow($val - ($b[$i] ?? 0), 2);
        }

        return sqrt($sum);
    }

    /**
     * @param list<array{label: string, distance: float}> $neighbors
     */
    private function majorityVote(array $neighbors): string
    {
        $votes = array_count_values(array_column($neighbors, 'label'));
        arsort($votes);

        return (string) array_key_first($votes);
    }

    /**
     * @param list<float> $input
     */
    private function estimateScore(array $input): float
    {
        $keaktifan = $input[4] ?? 0;

        return (float) $keaktifan;
    }

    public static function hitungNilaiAkhir(float $keaktifan, float $logbook, float $laporan): float
    {
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
}
