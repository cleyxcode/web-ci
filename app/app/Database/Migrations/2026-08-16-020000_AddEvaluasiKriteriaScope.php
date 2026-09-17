<?php

declare(strict_types=1);

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

final class AddEvaluasiKriteriaScope extends Migration
{
    public function up(): void
    {
        if (! $this->db->tableExists('evaluasi_kriteria')) {
            return;
        }

        if (! $this->db->fieldExists('cakupan', 'evaluasi_kriteria')) {
            $this->forge->addColumn('evaluasi_kriteria', [
                'cakupan' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 20,
                    'default'    => 'semua',
                    'null'       => false,
                ],
            ]);
        }

        if (! $this->db->fieldExists('target_id', 'evaluasi_kriteria')) {
            $this->forge->addColumn('evaluasi_kriteria', [
                'target_id' => [
                    'type'       => 'INT',
                    'constraint' => 11,
                    'null'       => true,
                ],
            ]);
        }

        $this->db->table('evaluasi_kriteria')
            ->where('cakupan', null)
            ->update(['cakupan' => 'semua']);
    }

    public function down(): void
    {
        // Kolom target tidak dihapus otomatis agar konfigurasi produksi aman.
    }
}
