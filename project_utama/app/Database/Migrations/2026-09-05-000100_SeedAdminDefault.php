<?php

declare(strict_types=1);

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Seeder akun admin default.
 * Idempotent: tidak membuat ulang jika username sudah ada.
 * Password default: admin123 (ganti segera setelah login pertama).
 */
final class SeedAdminDefault extends Migration
{
    public function up(): void
    {
        $db = $this->db;

        $exists = $db->table('users')
            ->where('username', 'admin')
            ->countAllResults();

        if ($exists > 0) {
            return;
        }

        $db->table('users')->insert([
            'nama'      => 'Administrator',
            'username'  => 'admin',
            'email'     => 'admin@kkn.ukim.ac.id',
            'password'  => password_hash('admin123', PASSWORD_BCRYPT),
            'role'      => 'admin',
            'is_active' => 1,
        ]);
    }

    public function down(): void
    {
        // Akun admin tidak dihapus otomatis.
    }
}
