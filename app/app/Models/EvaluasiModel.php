<?php

namespace App\Models;

use CodeIgniter\Model;

class EvaluasiModel extends Model
{
    protected $table            = 'evaluasi';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $allowedFields    = ['mahasiswa_id', 'rating', 'komentar'];
    protected $useTimestamps    = true;
    protected $createdField     = 'created_at';
    protected $updatedField     = null;

    public function findByMahasiswa(int $mahasiswaId): ?array
    {
        return $this->where('mahasiswa_id', $mahasiswaId)->first();
    }
}
