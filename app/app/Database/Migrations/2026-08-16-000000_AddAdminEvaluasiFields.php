<?php

declare(strict_types=1);

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;
use RuntimeException;

/**
 * Menyiapkan struktur tabel evaluasi untuk evaluasi yang dibuat admin.
 *
 * Migrasi ini aman bila sebagian kolom sudah ditambahkan sebelumnya.
 * Tidak ada data evaluasi yang dihapus.
 */
final class AddAdminEvaluasiFields extends Migration
{
    public function up(): void
    {
        if (! $this->db->tableExists('evaluasi')) {
            throw new RuntimeException('Tabel evaluasi tidak ditemukan. Import skema database dasar terlebih dahulu.');
        }

        $this->addColumnIfMissing('tipe_evaluasi', [
            'type'       => 'VARCHAR',
            'constraint' => 20,
            'default'    => 'mahasiswa',
            'null'       => false,
        ]);
        $this->addColumnIfMissing('penilai_id', [
            'type'       => 'INT',
            'constraint' => 11,
            'null'       => true,
        ]);
        $this->addColumnIfMissing('detail_evaluasi', [
            'type' => 'TEXT',
            'null' => true,
        ]);
        $this->addColumnIfMissing('aspek_bimbingan', [
            'type'       => 'TINYINT',
            'constraint' => 1,
            'null'       => true,
        ]);
        $this->addColumnIfMissing('aspek_lokasi', [
            'type'       => 'TINYINT',
            'constraint' => 1,
            'null'       => true,
        ]);
        $this->addColumnIfMissing('aspek_pelaksanaan', [
            'type'       => 'TINYINT',
            'constraint' => 1,
            'null'       => true,
        ]);
        $this->addColumnIfMissing('updated_at', [
            'type' => 'DATETIME',
            'null' => true,
        ]);

        if ($this->db->fieldExists('kategori', 'evaluasi')) {
            $this->forge->modifyColumn('evaluasi', [
                'kategori' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 100,
                    'null'       => true,
                ],
            ]);
        }

        $this->db->table('evaluasi')
            ->groupStart()
            ->where('tipe_evaluasi', null)
            ->orWhere('tipe_evaluasi', '')
            ->groupEnd()
            ->update(['tipe_evaluasi' => 'mahasiswa']);

        if ($this->hasDuplicateEvaluationTypes()) {
            throw new RuntimeException(
                'Terdapat evaluasi ganda untuk mahasiswa dan tipe yang sama. '
                . 'Perbaiki data ganda sebelum menjalankan migrasi kembali.'
            );
        }

        if (! $this->hasCompositeUniqueIndex()) {
            $this->db->query(
                'ALTER TABLE `evaluasi` '
                . 'ADD UNIQUE KEY `uq_evaluasi_mahasiswa_tipe` (`mahasiswa_id`, `tipe_evaluasi`)'
            );
        }

        foreach ($this->singleMahasiswaUniqueIndexes() as $indexName) {
            $this->db->query(
                'ALTER TABLE `evaluasi` DROP INDEX ' . $this->db->escapeIdentifiers($indexName)
            );
        }
    }

    /**
     * Pembatalan sengaja tidak menghapus kolom agar data evaluasi produksi aman.
     */
    public function down(): void
    {
        throw new RuntimeException(
            'Migrasi Evaluasi Admin tidak dapat di-rollback otomatis karena dapat menghapus data produksi.'
        );
    }

    /**
     * @param array<string, bool|int|string> $definition
     */
    private function addColumnIfMissing(string $column, array $definition): void
    {
        if (! $this->db->fieldExists($column, 'evaluasi')) {
            $this->forge->addColumn('evaluasi', [$column => $definition]);
        }
    }

    private function hasDuplicateEvaluationTypes(): bool
    {
        return $this->db->table('evaluasi')
            ->select('mahasiswa_id, tipe_evaluasi')
            ->groupBy(['mahasiswa_id', 'tipe_evaluasi'])
            ->having('COUNT(*) >', 1, false)
            ->limit(1)
            ->get()
            ->getRowArray() !== null;
    }

    private function hasCompositeUniqueIndex(): bool
    {
        foreach ($this->uniqueIndexes() as $columns) {
            if ($columns === ['mahasiswa_id', 'tipe_evaluasi']) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return list<string>
     */
    private function singleMahasiswaUniqueIndexes(): array
    {
        $indexes = [];

        foreach ($this->uniqueIndexes() as $name => $columns) {
            if ($name !== 'PRIMARY' && $columns === ['mahasiswa_id']) {
                $indexes[] = $name;
            }
        }

        return $indexes;
    }

    /**
     * @return array<string, list<string>>
     */
    private function uniqueIndexes(): array
    {
        $rows = $this->db->query('SHOW INDEX FROM `evaluasi`')->getResultArray();
        $indexes = [];

        foreach ($rows as $row) {
            if ((int) ($row['Non_unique'] ?? 1) !== 0) {
                continue;
            }

            $name = (string) ($row['Key_name'] ?? '');
            $column = (string) ($row['Column_name'] ?? '');
            $position = (int) ($row['Seq_in_index'] ?? 0);

            if ($name === '' || $column === '' || $position < 1) {
                continue;
            }

            $indexes[$name][$position] = $column;
        }

        foreach ($indexes as &$columns) {
            ksort($columns);
            $columns = array_values($columns);
        }
        unset($columns);

        return $indexes;
    }
}
