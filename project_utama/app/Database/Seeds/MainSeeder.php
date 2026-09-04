<?php

declare(strict_types=1);

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

/**
 * Seeder utama — jalankan semua seeder dalam urutan yang benar.
 * Jalankan: php spark db:seed MainSeeder
 */
class MainSeeder extends Seeder
{
    public function run(): void
    {
        $this->call('AdminSeeder');
        $this->call('DemoSeeder');
    }
}
