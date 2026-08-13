<?php

namespace App\Models;

use CodeIgniter\Model;

class AuditTrailModel extends Model
{
    protected $table            = 'audit_trail';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $allowedFields    = [
        'user_id', 'user_nama', 'user_role', 'aksi', 'entitas',
        'entitas_id', 'deskripsi', 'data_lama', 'data_baru',
    ];
    protected $useTimestamps    = true;
    protected $createdField     = 'created_at';
    protected $updatedField     = null;

    public function getLatest(int $limit = 50): array
    {
        return $this->orderBy('created_at', 'DESC')->findAll($limit);
    }

    public function getByEntitas(string $entitas, ?int $entitasId = null, int $limit = 30): array
    {
        $builder = $this->where('entitas', $entitas);

        if ($entitasId !== null) {
            $builder->where('entitas_id', $entitasId);
        }

        return $builder->orderBy('created_at', 'DESC')->findAll($limit);
    }
}
