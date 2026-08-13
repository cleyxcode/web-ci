<?php

namespace App\Models;

use CodeIgniter\Model;

class LogbookModel extends Model
{
    protected $table            = 'logbook';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $allowedFields    = [
        'mahasiswa_id', 'tanggal', 'kegiatan', 'lokasi_kegiatan', 'dokumentasi',
        'status', 'catatan_dpl', 'validated_by', 'validated_at',
    ];
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = null;

    public function getByMahasiswa(int $mahasiswaId): array
    {
        return $this->where('mahasiswa_id', $mahasiswaId)
            ->orderBy('tanggal', 'DESC')
            ->findAll();
    }

    public function getPendingByDpl(int $dplId): array
    {
        return $this->select('logbook.*, mahasiswa.nama as nama_mahasiswa, mahasiswa.npm')
            ->join('mahasiswa', 'mahasiswa.id = logbook.mahasiswa_id')
            ->join('kelompok_kkn', 'kelompok_kkn.id = mahasiswa.kelompok_id')
            ->where('kelompok_kkn.dpl_id', $dplId)
            ->where('logbook.status', 'menunggu')
            ->orderBy('logbook.created_at', 'ASC')
            ->findAll();
    }

    public function getByDpl(int $dplId): array
    {
        return $this->select('logbook.*, mahasiswa.nama as nama_mahasiswa, mahasiswa.npm')
            ->join('mahasiswa', 'mahasiswa.id = logbook.mahasiswa_id')
            ->join('kelompok_kkn', 'kelompok_kkn.id = mahasiswa.kelompok_id')
            ->where('kelompok_kkn.dpl_id', $dplId)
            ->orderBy('logbook.created_at', 'DESC')
            ->findAll();
    }

    public function countByMahasiswaStatus(int $mahasiswaId, string $status): int
    {
        return $this->where('mahasiswa_id', $mahasiswaId)->where('status', $status)->countAllResults();
    }
}
