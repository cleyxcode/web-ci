<?php

namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table            = 'users';
    protected $primaryKey         = 'id';
    protected $useAutoIncrement   = true;
    protected $returnType         = 'array';
    protected $allowedFields      = ['nama', 'username', 'email', 'password', 'role', 'foto', 'is_active'];
    protected $useTimestamps      = true;
    protected $createdField       = 'created_at';
    protected $updatedField       = 'updated_at';

    public function findByLogin(string $login): ?array
    {
        $login = trim($login);

        if ($login === '') {
            return null;
        }

        return $this->select('users.*')
            ->join('mahasiswa', 'mahasiswa.user_id = users.id', 'left')
            ->join('dpl', 'dpl.user_id = users.id', 'left')
            ->groupStart()
            ->where('users.username', $login)
            ->orWhere('users.email', $login)
            ->orWhere('mahasiswa.npm', $login)
            ->orWhere('dpl.nidn', $login)
            ->groupEnd()
            ->where('users.is_active', 1)
            ->first();
    }
}
