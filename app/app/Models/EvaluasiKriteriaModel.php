<?php

declare(strict_types=1);

namespace App\Models;

use CodeIgniter\Model;

final class EvaluasiKriteriaModel extends Model
{
    protected $table = 'evaluasi_kriteria';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $allowedFields = ['nama', 'deskripsi', 'urutan', 'aktif', 'created_by'];
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    /**
     * @return list<array<string, mixed>>
     */
    public function getActiveOrdered(): array
    {
        return $this->where('aktif', 1)
            ->orderBy('urutan', 'ASC')
            ->orderBy('id', 'ASC')
            ->findAll();
    }

    /**
     * @return list<array<string, mixed>>
     */
    public function getAllOrdered(): array
    {
        return $this->orderBy('urutan', 'ASC')
            ->orderBy('id', 'ASC')
            ->findAll();
    }

    public function nextOrder(): int
    {
        $row = $this->selectMax('urutan', 'last_order')->first();

        return ((int) ($row['last_order'] ?? 0)) + 1;
    }
}
