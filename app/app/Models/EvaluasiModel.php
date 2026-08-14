<?php

declare(strict_types=1);

namespace App\Models;

use CodeIgniter\Model;

class EvaluasiModel extends Model
{
    protected $table            = 'evaluasi';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $allowedFields    = [
        'mahasiswa_id',
        'kelompok_id',
        'dpl_id',
        'rating',
        'aspek_bimbingan',
        'aspek_lokasi',
        'aspek_pelaksanaan',
        'komentar',
        'skor_total',
        'kategori',
        'rekomendasi',
    ];
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    public function findByMahasiswa(int $mahasiswaId): ?array
    {
        return $this->where('mahasiswa_id', $mahasiswaId)->first();
    }

    /**
     * @return list<array<string, mixed>>
     */
    public function getAllWithMahasiswa(): array
    {
        return $this->select('evaluasi.*, mahasiswa.npm, mahasiswa.nama as nama_mahasiswa, mahasiswa.prodi,
                kelompok_kkn.nama_kelompok, dpl.nama as nama_dpl')
            ->join('mahasiswa', 'mahasiswa.id = evaluasi.mahasiswa_id')
            ->join('kelompok_kkn', 'kelompok_kkn.id = mahasiswa.kelompok_id', 'left')
            ->join('dpl', 'dpl.id = kelompok_kkn.dpl_id', 'left')
            ->orderBy('evaluasi.created_at', 'DESC')
            ->findAll();
    }

    /**
     * @return list<array<string, mixed>>
     */
    public function getByDpl(int $dplId): array
    {
        return $this->select('evaluasi.*, mahasiswa.npm, mahasiswa.nama as nama_mahasiswa, mahasiswa.prodi,
                kelompok_kkn.nama_kelompok')
            ->join('mahasiswa', 'mahasiswa.id = evaluasi.mahasiswa_id')
            ->join('kelompok_kkn', 'kelompok_kkn.id = mahasiswa.kelompok_id')
            ->where('kelompok_kkn.dpl_id', $dplId)
            ->orderBy('evaluasi.created_at', 'DESC')
            ->findAll();
    }

    public function averageRating(?int $dplId = null): ?float
    {
        $builder = $this->builder();
        $builder->selectAvg('evaluasi.rating', 'avg_rating');

        if ($dplId !== null) {
            $builder->join('mahasiswa', 'mahasiswa.id = evaluasi.mahasiswa_id')
                ->join('kelompok_kkn', 'kelompok_kkn.id = mahasiswa.kelompok_id')
                ->where('kelompok_kkn.dpl_id', $dplId);
        }

        $row = $builder->get()->getRowArray();

        if (! $row || $row['avg_rating'] === null) {
            return null;
        }

        return round((float) $row['avg_rating'], 2);
    }

    public function countAllEvaluasi(?int $dplId = null): int
    {
        if ($dplId === null) {
            return $this->countAllResults();
        }

        return (int) $this->select('evaluasi.id')
            ->join('mahasiswa', 'mahasiswa.id = evaluasi.mahasiswa_id')
            ->join('kelompok_kkn', 'kelompok_kkn.id = mahasiswa.kelompok_id')
            ->where('kelompok_kkn.dpl_id', $dplId)
            ->countAllResults();
    }

    public static function hitungSkor(int $rating, int $bimbingan, int $lokasi, int $pelaksanaan): float
    {
        return round(($rating + $bimbingan + $lokasi + $pelaksanaan) / 4, 2);
    }

    public static function kategoriFromSkor(float $skorTotal): string
    {
        return match (true) {
            $skorTotal >= 4.5 => 'Sangat Baik',
            $skorTotal >= 3.5 => 'Baik',
            $skorTotal >= 2.5 => 'Cukup',
            default           => 'Kurang',
        };
    }
}
