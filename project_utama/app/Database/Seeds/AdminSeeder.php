<?php

declare(strict_types=1);

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

/**
 * Seeder admin default.
 * Jalankan: php spark db:seed AdminSeeder
 *
 * Username : admin
 * Password : admin123  ← ganti segera setelah login pertama
 */
class AdminSeeder extends Seeder
{
    public function run(): void
    {
        $db = $this->db;

        $exists = $db->table('users')
            ->where('username', 'admin')
            ->countAllResults();

        if ($exists > 0) {
            echo "Admin sudah ada, dilewati.\n";
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

        echo "Admin berhasil dibuat. Username: admin | Password: admin123\n";
    }
}
