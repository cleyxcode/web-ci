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

        return $this->groupStart()
            ->where('username', $login)
            ->orWhere('email', $login)
            ->groupEnd()
            ->where('is_active', 1)
            ->first();
    }
}
