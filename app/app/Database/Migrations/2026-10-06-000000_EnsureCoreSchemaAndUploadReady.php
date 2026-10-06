<?php

declare(strict_types=1);

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Pastikan skema inti lengkap (idempotent) untuk DB lama/baru:
 * - audit_trail
 * - created_at laporan/logbook
 * - dokumentasi logbook = TEXT
 * - file_laporan cukup untuk path relatif
 * - kolom GPS / ketua pada kelompok_kkn
 * - folder writable/uploads siap pakai
 */
final class EnsureCoreSchemaAndUploadReady extends Migration
{
    public function up(): void
    {
        $this->ensureAuditTrail();
        $this->ensureLaporanSchema();
        $this->ensureLogbookSchema();
        $this->ensureKelompokSchema();
        $this->ensureStorageDirectories();
    }

    public function down(): void
    {
        // Tidak menghapus kolom/tabel agar data produksi aman.
    }

    private function ensureAuditTrail(): void
    {
        if ($this->db->tableExists('audit_trail')) {
            return;
        }

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

    private function ensureLaporanSchema(): void
    {
        if (! $this->db->tableExists('laporan')) {
            return;
        }

        if (! $this->db->fieldExists('created_at', 'laporan')) {
            $this->forge->addColumn('laporan', [
                'created_at' => [
                    'type'    => 'DATETIME',
                    'null'    => true,
                    'default' => null,
                    'after'   => 'reviewed_at',
                ],
            ]);
            $this->db->query('UPDATE `laporan` SET `created_at` = NOW() WHERE `created_at` IS NULL');
        }

        if ($this->db->fieldExists('file_laporan', 'laporan')) {
            $this->forge->modifyColumn('laporan', [
                'file_laporan' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 255,
                    'null'       => true,
                ],
            ]);
        }
    }

    private function ensureLogbookSchema(): void
    {
        if (! $this->db->tableExists('logbook')) {
            return;
        }

        if (! $this->db->fieldExists('created_at', 'logbook')) {
            $this->forge->addColumn('logbook', [
                'created_at' => [
                    'type'    => 'DATETIME',
                    'null'    => true,
                    'default' => null,
                    'after'   => 'validated_at',
                ],
            ]);
            $this->db->query('UPDATE `logbook` SET `created_at` = NOW() WHERE `created_at` IS NULL');
        }

        if ($this->db->fieldExists('dokumentasi', 'logbook')) {
            $this->forge->modifyColumn('logbook', [
                'dokumentasi' => [
                    'type' => 'TEXT',
                    'null' => true,
                ],
            ]);
        }

        if ($this->db->fieldExists('lokasi_kegiatan', 'logbook')) {
            $this->forge->modifyColumn('logbook', [
                'lokasi_kegiatan' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 255,
                    'null'       => true,
                ],
            ]);
        }
    }

    private function ensureKelompokSchema(): void
    {
        if (! $this->db->tableExists('kelompok_kkn')) {
            return;
        }

        $columns = [
            'ketua_mahasiswa_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => true,
                'after' => 'lokasi_id',
            ],
            'dosen_pendamping' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => true,
                'after' => 'alamat_penelitian',
            ],
            'no_hp_dosen_pendamping' => [
                'type' => 'VARCHAR',
                'constraint' => 20,
                'null' => true,
                'after' => 'dosen_pendamping',
            ],
            'latitude' => [
                'type' => 'DECIMAL',
                'constraint' => '10,7',
                'null' => true,
                'after' => 'no_hp_dosen_pendamping',
            ],
            'longitude' => [
                'type' => 'DECIMAL',
                'constraint' => '10,7',
                'null' => true,
                'after' => 'latitude',
            ],
            'lokasi_gps_at' => [
                'type' => 'DATETIME',
                'null' => true,
                'after' => 'longitude',
            ],
        ];

        foreach ($columns as $name => $definition) {
            if (! $this->db->fieldExists($name, 'kelompok_kkn')) {
                $this->forge->addColumn('kelompok_kkn', [$name => $definition]);
            }
        }
    }

    private function ensureStorageDirectories(): void
    {
        foreach (['logbook', 'laporan'] as $folder) {
            $path = rtrim(WRITEPATH, '/\\') . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR . $folder;
            if (! is_dir($path)) {
                mkdir($path, 0755, true);
            }
        }
    }
}
