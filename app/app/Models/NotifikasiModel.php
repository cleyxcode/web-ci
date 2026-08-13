<?php

namespace App\Models;

use CodeIgniter\Model;

class NotifikasiModel extends Model
{
    protected $table            = 'notifikasi';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $allowedFields    = ['user_id', 'judul', 'pesan', 'type', 'is_read'];
    protected $useTimestamps    = true;
    protected $createdField     = 'created_at';
    protected $updatedField     = null;

    public function getUnread(int $userId, int $limit = 10): array
    {
        return $this->where('user_id', $userId)
            ->where('is_read', 0)
            ->orderBy('created_at', 'DESC')
            ->findAll($limit);
    }

    public function getAllForUser(int $userId, int $limit = 20): array
    {
        return $this->where('user_id', $userId)
            ->orderBy('created_at', 'DESC')
            ->findAll($limit);
    }

    public function countUnread(int $userId): int
    {
        return $this->where('user_id', $userId)->where('is_read', 0)->countAllResults();
    }

    public function markAsRead(int $id, int $userId): void
    {
        $this->where('id', $id)->where('user_id', $userId)->set(['is_read' => 1])->update();
    }

    public function markAllRead(int $userId): void
    {
        $this->where('user_id', $userId)->where('is_read', 0)->set(['is_read' => 1])->update();
    }

    public function createNotif(int $userId, string $judul, string $pesan, string $type = 'info'): int
    {
        return (int) $this->insert([
            'user_id' => $userId,
            'judul'   => $judul,
            'pesan'   => $pesan,
            'type'    => $type,
        ]);
    }
}
