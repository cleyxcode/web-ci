<?php

declare(strict_types=1);

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Dokumentasi logbook dapat berisi maksimal tiga path gambar dalam JSON.
 * TEXT memberi ruang aman dan tetap kompatibel dengan path tunggal lama.
 */
final class ExpandLogbookDocumentation extends Migration
{
    public function up(): void
    {
        if ($this->db->tableExists('logbook') && $this->db->fieldExists('dokumentasi', 'logbook')) {
            $this->forge->modifyColumn('logbook', [
                'dokumentasi' => [
                    'type' => 'TEXT',
                    'null' => true,
                ],
            ]);
        }
    }

    public function down(): void
    {
        if ($this->db->tableExists('logbook') && $this->db->fieldExists('dokumentasi', 'logbook')) {
            $this->forge->modifyColumn('logbook', [
                'dokumentasi' => [
                    'type' => 'VARCHAR',
                    'constraint' => 255,
                    'null' => true,
                ],
            ]);
        }
    }
}
