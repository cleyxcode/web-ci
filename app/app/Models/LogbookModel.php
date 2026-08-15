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
    protected $useTimestamps = false;

    public function getByMahasiswa(int $mahasiswaId): array
    {
        return $this->where('mahasiswa_id', $mahasiswaId)
            ->orderBy('tanggal', 'DESC')
            ->findAll();
    }

    public function getPendingByDpl(int $dplId): array
    {
        return $this->getByDpl($dplId, 'menunggu', 'ASC');
    }

    public function getByDpl(int $dplId, ?string $status = null, string $direction = 'DESC'): array
    {
        $builder = $this->select('logbook.*, mahasiswa.nama as nama_mahasiswa, mahasiswa.npm,
                kelompok_kkn.nama_kelompok, kelompok_kkn.periode,
                lokasi_kkn.nama_desa, lokasi_kkn.kecamatan, lokasi_kkn.kabupaten')
            ->join('mahasiswa', 'mahasiswa.id = logbook.mahasiswa_id')
            ->join('kelompok_kkn', 'kelompok_kkn.id = mahasiswa.kelompok_id')
            ->join('lokasi_kkn', 'lokasi_kkn.id = kelompok_kkn.lokasi_id', 'left')
            ->where('kelompok_kkn.dpl_id', $dplId);

        if ($status !== null) {
            $builder->where('logbook.status', $status);
        }

        return $builder
            ->orderBy('logbook.created_at', strtoupper($direction) === 'ASC' ? 'ASC' : 'DESC')
            ->findAll();
    }

    public function countByMahasiswaStatus(int $mahasiswaId, string $status): int
    {
        return $this->where('mahasiswa_id', $mahasiswaId)->where('status', $status)->countAllResults();
    }
}
