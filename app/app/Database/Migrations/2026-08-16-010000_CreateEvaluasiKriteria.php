<?php

declare(strict_types=1);

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

final class CreateEvaluasiKriteria extends Migration
{
    public function up(): void
    {
        $this->db->query(
            'CREATE TABLE IF NOT EXISTS `evaluasi_kriteria` ('
            . '`id` int(11) NOT NULL AUTO_INCREMENT,'
            . '`nama` varchar(150) NOT NULL,'
            . '`deskripsi` varchar(255) DEFAULT NULL,'
            . '`urutan` int(11) NOT NULL DEFAULT 0,'
            . '`aktif` tinyint(1) NOT NULL DEFAULT 1,'
            . '`created_by` int(11) DEFAULT NULL,'
            . '`created_at` datetime DEFAULT CURRENT_TIMESTAMP,'
            . '`updated_at` datetime DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,'
            . 'PRIMARY KEY (`id`),'
            . 'KEY `idx_evaluasi_kriteria_aktif` (`aktif`, `urutan`),'
            . 'KEY `idx_evaluasi_kriteria_created_by` (`created_by`)'
            . ') ENGINE=InnoDB DEFAULT CHARSET=utf8mb4'
        );
    }

    public function down(): void
    {
        // Kriteria sengaja tidak dihapus otomatis agar riwayat JSON evaluasi tetap aman.
    }
}
