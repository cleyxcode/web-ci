<?php

namespace App\Models;

use CodeIgniter\Model;

class MahasiswaModel extends Model
{
    protected $table            = 'mahasiswa';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $allowedFields    = ['user_id', 'npm', 'nama', 'prodi', 'kelompok_id', 'no_hp'];
    protected $useTimestamps    = true;
    protected $createdField     = 'created_at';
    protected $updatedField     = null;

    public function getWithRelations(int $id): ?array
    {
        return $this->select('mahasiswa.*, users.username, users.email, users.is_active,
            kelompok_kkn.nama_kelompok, kelompok_kkn.periode, kelompok_kkn.tanggal_mulai, kelompok_kkn.tanggal_selesai,
            lokasi_kkn.nama_desa, lokasi_kkn.kecamatan, lokasi_kkn.kabupaten,
            dpl.nama as nama_dpl, dpl.id as dpl_id,
            kelompok_kkn.alamat_penelitian, kelompok_kkn.dosen_pendamping, kelompok_kkn.no_hp_dosen_pendamping,
            kelompok_kkn.latitude, kelompok_kkn.longitude, kelompok_kkn.lokasi_gps_at, kelompok_kkn.ketua_mahasiswa_id')
            ->join('users', 'users.id = mahasiswa.user_id')
            ->join('kelompok_kkn', 'kelompok_kkn.id = mahasiswa.kelompok_id', 'left')
            ->join('lokasi_kkn', 'lokasi_kkn.id = kelompok_kkn.lokasi_id', 'left')
            ->join('dpl', 'dpl.id = kelompok_kkn.dpl_id', 'left')
            ->where('mahasiswa.id', $id)
            ->first();
    }

    public function getAllWithRelations(): array
    {
        return $this->select('mahasiswa.*, users.email, kelompok_kkn.nama_kelompok, lokasi_kkn.nama_desa')
            ->join('users', 'users.id = mahasiswa.user_id')
            ->join('kelompok_kkn', 'kelompok_kkn.id = mahasiswa.kelompok_id', 'left')
            ->join('lokasi_kkn', 'lokasi_kkn.id = kelompok_kkn.lokasi_id', 'left')
            ->orderBy('mahasiswa.nama', 'ASC')
            ->findAll();
    }

    public function findByUserId(int $userId): ?array
    {
        return $this->where('user_id', $userId)->first();
    }

    public function getByDplId(int $dplId): array
    {
        return $this->select('mahasiswa.*, users.email')
            ->join('users', 'users.id = mahasiswa.user_id')
            ->join('kelompok_kkn', 'kelompok_kkn.id = mahasiswa.kelompok_id')
            ->where('kelompok_kkn.dpl_id', $dplId)
            ->findAll();
    }

    public function getByKelompokId(int $kelompokId): array
    {
        return $this->select('mahasiswa.*, users.email')
            ->join('users', 'users.id = mahasiswa.user_id')
            ->where('mahasiswa.kelompok_id', $kelompokId)
            ->orderBy('mahasiswa.nama', 'ASC')
            ->findAll();
    }

    public function getUnassigned(): array
    {
        return $this->select('mahasiswa.*, users.email')
            ->join('users', 'users.id = mahasiswa.user_id')
            ->groupStart()
                ->where('mahasiswa.kelompok_id', null)
                ->orWhere('mahasiswa.kelompok_id', 0)
            ->groupEnd()
            ->orderBy('mahasiswa.nama', 'ASC')
            ->findAll();
    }
}
