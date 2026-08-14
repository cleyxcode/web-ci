<?php

declare(strict_types=1);

namespace App\Models;

use CodeIgniter\Model;

class PenilaianModel extends Model
{
    protected $table            = 'penilaian';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $allowedFields    = [
        'mahasiswa_id',
        'dpl_id',
        'nilai_keaktifan',
        'nilai_logbook',
        'nilai_laporan',
        'nilai_akhir',
        'grade',
        'catatan',
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
    public function getByDpl(int $dplId): array
    {
        return $this->select('penilaian.*, mahasiswa.npm, mahasiswa.nama as nama_mahasiswa')
            ->join('mahasiswa', 'mahasiswa.id = penilaian.mahasiswa_id')
            ->where('penilaian.dpl_id', $dplId)
            ->orderBy('penilaian.updated_at', 'DESC')
            ->findAll();
    }
}
