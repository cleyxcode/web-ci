<?php

declare(strict_types=1);

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Memastikan kolom created_at ada di tabel laporan dan logbook.
 *
 * Migrasi ini aman dijalankan berulang kali (idempotent).
 * Kolom hanya ditambahkan jika belum ada.
 */
final class AddCreatedAtToLaporanAndLogbook extends Migration
{
    public function up(): void
    {
        // ── Tabel laporan ──────────────────────────────────────────────────────
        if ($this->db->tableExists('laporan')) {
            if (! $this->db->fieldExists('created_at', 'laporan')) {
                $this->forge->addColumn('laporan', [
                    'created_at' => [
                        'type'    => 'DATETIME',
                        'null'    => true,
                        'default' => null,
                        'after'   => 'reviewed_at',
                    ],
                ]);

                // Set nilai default untuk baris yang sudah ada
                $this->db->query("UPDATE `laporan` SET `created_at` = NOW() WHERE `created_at` IS NULL");
            }
        }

        // ── Tabel logbook ──────────────────────────────────────────────────────
        if ($this->db->tableExists('logbook')) {
            if (! $this->db->fieldExists('created_at', 'logbook')) {
                $this->forge->addColumn('logbook', [
                    'created_at' => [
                        'type'    => 'DATETIME',
                        'null'    => true,
                        'default' => null,
                        'after'   => 'validated_at',
                    ],
                ]);

                // Set nilai default untuk baris yang sudah ada
                $this->db->query("UPDATE `logbook` SET `created_at` = NOW() WHERE `created_at` IS NULL");
            }
        }
    }

    public function down(): void
    {
        // Hapus kolom created_at jika migrasi di-rollback
        if ($this->db->tableExists('laporan') && $this->db->fieldExists('created_at', 'laporan')) {
            $this->forge->dropColumn('laporan', 'created_at');
        }

        if ($this->db->tableExists('logbook') && $this->db->fieldExists('created_at', 'logbook')) {
            $this->forge->dropColumn('logbook', 'created_at');
        }
    }
}
