<?php

namespace App\Models;

use CodeIgniter\Model;

class KelompokKknModel extends Model
{
    protected $table            = 'kelompok_kkn';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $allowedFields    = [
        'nama_kelompok', 'dpl_id', 'lokasi_id', 'ketua_mahasiswa_id',
        'periode', 'tanggal_mulai', 'tanggal_selesai', 'alamat_penelitian',
        'dosen_pendamping', 'no_hp_dosen_pendamping',
        'latitude', 'longitude', 'lokasi_gps_at',
    ];
    protected $useTimestamps    = true;
    protected $createdField     = 'created_at';
    protected $updatedField     = null;

    public function getAllWithRelations(): array
    {
        return $this->select('kelompok_kkn.*, dpl.nama as nama_dpl, lokasi_kkn.nama_desa, lokasi_kkn.kecamatan, lokasi_kkn.kabupaten,
            ketua.nama as nama_ketua,
            (SELECT COUNT(*) FROM mahasiswa WHERE mahasiswa.kelompok_id = kelompok_kkn.id) as jumlah_anggota')
            ->join('dpl', 'dpl.id = kelompok_kkn.dpl_id', 'left')
            ->join('lokasi_kkn', 'lokasi_kkn.id = kelompok_kkn.lokasi_id', 'left')
            ->join('mahasiswa ketua', 'ketua.id = kelompok_kkn.ketua_mahasiswa_id', 'left')
            ->orderBy('kelompok_kkn.id', 'DESC')
            ->findAll();
    }

    public function getDetail(int $id): ?array
    {
        return $this->select('kelompok_kkn.*, dpl.nama as nama_dpl, dpl.no_hp as no_hp_dpl,
            lokasi_kkn.nama_desa, lokasi_kkn.kecamatan, lokasi_kkn.kabupaten,
            ketua.nama as nama_ketua, ketua.npm as npm_ketua')
            ->join('dpl', 'dpl.id = kelompok_kkn.dpl_id', 'left')
            ->join('lokasi_kkn', 'lokasi_kkn.id = kelompok_kkn.lokasi_id', 'left')
            ->join('mahasiswa ketua', 'ketua.id = kelompok_kkn.ketua_mahasiswa_id', 'left')
            ->where('kelompok_kkn.id', $id)
            ->first();
    }

    public function getWithGps(?int $dplId = null): array
    {
        $builder = $this->select('kelompok_kkn.id, kelompok_kkn.nama_kelompok, kelompok_kkn.latitude, kelompok_kkn.longitude,
            kelompok_kkn.lokasi_gps_at, kelompok_kkn.periode,
            lokasi_kkn.nama_desa, lokasi_kkn.kecamatan, lokasi_kkn.kabupaten,
            dpl.nama as nama_dpl, ketua.nama as nama_ketua')
            ->join('lokasi_kkn', 'lokasi_kkn.id = kelompok_kkn.lokasi_id', 'left')
            ->join('dpl', 'dpl.id = kelompok_kkn.dpl_id', 'left')
            ->join('mahasiswa ketua', 'ketua.id = kelompok_kkn.ketua_mahasiswa_id', 'left')
            ->where('kelompok_kkn.latitude IS NOT NULL')
            ->where('kelompok_kkn.longitude IS NOT NULL');

        if ($dplId !== null) {
            $builder->where('kelompok_kkn.dpl_id', $dplId);
        }

        return $builder->findAll();
    }
}
