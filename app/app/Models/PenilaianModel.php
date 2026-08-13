<?php

namespace App\Models;

use CodeIgniter\Model;

class PenilaianModel extends Model
{
    protected $table            = 'penilaian';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $allowedFields    = [
        'mahasiswa_id', 'dpl_id', 'nilai_keaktifan', 'nilai_logbook', 'nilai_laporan',
        'nilai_akhir', 'grade', 'prediksi_knn', 'catatan',
    ];
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    public function getTrainingData(): array
    {
        return $this->select('penilaian.*')
            ->where('grade IS NOT NULL')
            ->where('nilai_akhir >', 0)
            ->findAll();
    }

    public function findByMahasiswa(int $mahasiswaId): ?array
    {
        return $this->where('mahasiswa_id', $mahasiswaId)->first();
    }
}
