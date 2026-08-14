<?php

namespace App\Models;

use CodeIgniter\Model;

class DplModel extends Model
{
    protected $table            = 'dpl';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $allowedFields    = ['user_id', 'nidn', 'nama', 'prodi', 'no_hp'];
    protected $useTimestamps = false;

    public function getWithUser(int $id): ?array
    {
        return $this->select('dpl.*, users.username, users.email, users.is_active')
            ->join('users', 'users.id = dpl.user_id')
            ->where('dpl.id', $id)
            ->first();
    }

    public function getAllWithUser(): array
    {
        return $this->select('dpl.*, users.username, users.email')
            ->join('users', 'users.id = dpl.user_id')
            ->orderBy('dpl.nama', 'ASC')
            ->findAll();
    }

    public function findByUserId(int $userId): ?array
    {
        return $this->where('user_id', $userId)->first();
    }
}
