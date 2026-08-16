<?php

declare(strict_types=1);

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Ensures the audit trail exists on databases that were created before the
 * audit feature was introduced. The table is also present in the initial SQL
 * schema, so this migration is intentionally idempotent.
 */
final class CreateAuditTrail extends Migration
{
    public function up(): void
    {
        $this->db->query(
            'CREATE TABLE IF NOT EXISTS `audit_trail` ('
            . '`id` int(11) NOT NULL AUTO_INCREMENT,'
            . '`user_id` int(11) DEFAULT NULL,'
            . '`user_nama` varchar(100) DEFAULT NULL,'
            . '`user_role` varchar(20) DEFAULT NULL,'
            . '`aksi` varchar(50) NOT NULL,'
            . '`entitas` varchar(50) NOT NULL,'
            . '`entitas_id` int(11) DEFAULT NULL,'
            . '`deskripsi` varchar(255) NOT NULL,'
            . '`data_lama` text DEFAULT NULL,'
            . '`data_baru` text DEFAULT NULL,'
            . '`created_at` datetime DEFAULT CURRENT_TIMESTAMP,'
            . 'PRIMARY KEY (`id`),'
            . 'KEY `idx_entitas` (`entitas`, `entitas_id`),'
            . 'KEY `idx_created` (`created_at`)'
            . ') ENGINE=InnoDB DEFAULT CHARSET=utf8mb4'
        );
    }

    public function down(): void
    {
        // Audit history is retained intentionally and is not removed by rollback.
    }
}
