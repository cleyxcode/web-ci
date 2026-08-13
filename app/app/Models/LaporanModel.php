<?php

namespace App\Models;

use CodeIgniter\Model;

class LaporanModel extends Model
{
    protected $table            = 'laporan';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $allowedFields    = [
        'mahasiswa_id', 'judul', 'deskripsi', 'file_laporan',
        'status', 'catatan_dpl', 'reviewed_by', 'reviewed_at',
    ];
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = null;

    public function getByMahasiswa(int $mahasiswaId): array
    {
        return $this->where('mahasiswa_id', $mahasiswaId)
            ->orderBy('created_at', 'DESC')
            ->findAll();
    }

    public function getAllWithMahasiswa(): array
    {
        return $this->select('laporan.*, mahasiswa.nama as nama_mahasiswa, mahasiswa.npm')
            ->join('mahasiswa', 'mahasiswa.id = laporan.mahasiswa_id')
            ->orderBy('laporan.created_at', 'DESC')
            ->findAll();
    }

    public function getPendingByDpl(int $dplId): array
    {
        return $this->select('laporan.*, mahasiswa.nama as nama_mahasiswa, mahasiswa.npm')
            ->join('mahasiswa', 'mahasiswa.id = laporan.mahasiswa_id')
            ->join('kelompok_kkn', 'kelompok_kkn.id = mahasiswa.kelompok_id')
            ->where('kelompok_kkn.dpl_id', $dplId)
            ->where('laporan.status', 'menunggu')
            ->orderBy('laporan.created_at', 'ASC')
            ->findAll();
    }

    public function getByDpl(int $dplId): array
    {
        return $this->select('laporan.*, mahasiswa.nama as nama_mahasiswa, mahasiswa.npm')
            ->join('mahasiswa', 'mahasiswa.id = laporan.mahasiswa_id')
            ->join('kelompok_kkn', 'kelompok_kkn.id = mahasiswa.kelompok_id')
            ->where('kelompok_kkn.dpl_id', $dplId)
            ->orderBy('laporan.created_at', 'DESC')
            ->findAll();
    }
}
