<?php

namespace App\Models;

use CodeIgniter\Model;

class PengumumanModel extends Model
{
    protected $table            = 'pengumuman';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $allowedFields    = ['judul', 'isi', 'created_by'];
    protected $useTimestamps    = true;
    protected $createdField     = 'created_at';
    protected $updatedField     = null;

    public function getLatest(int $limit = 5): array
    {
        return $this->orderBy('created_at', 'DESC')->findAll($limit);
    }
}
