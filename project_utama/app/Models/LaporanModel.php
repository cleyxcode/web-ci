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
    protected $useTimestamps = false;

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
        return $this->getByDpl($dplId, 'menunggu', 'ASC');
    }

    public function getByDpl(int $dplId, ?string $status = null, string $direction = 'DESC'): array
    {
        $builder = $this->select('laporan.*, mahasiswa.nama as nama_mahasiswa, mahasiswa.npm,
                kelompok_kkn.nama_kelompok, kelompok_kkn.periode,
                lokasi_kkn.nama_desa, lokasi_kkn.kecamatan, lokasi_kkn.kabupaten')
            ->join('mahasiswa', 'mahasiswa.id = laporan.mahasiswa_id')
            ->join('kelompok_kkn', 'kelompok_kkn.id = mahasiswa.kelompok_id')
            ->join('lokasi_kkn', 'lokasi_kkn.id = kelompok_kkn.lokasi_id', 'left')
            ->where('kelompok_kkn.dpl_id', $dplId);

        if ($status !== null) {
            $builder->where('laporan.status', $status);
        }

        return $builder
            ->orderBy('laporan.created_at', strtoupper($direction) === 'ASC' ? 'ASC' : 'DESC')
            ->findAll();
    }
}
