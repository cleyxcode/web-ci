<?php

declare(strict_types=1);

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Perluas lokasi_kegiatan ke VARCHAR(255) agar selaras validasi form,
 * dan pastikan folder writable/uploads siap pakai.
 */
final class ExpandLokasiKegiatanAndEnsureUploadDirs extends Migration
{
    public function up(): void
    {
        if ($this->db->tableExists('logbook') && $this->db->fieldExists('lokasi_kegiatan', 'logbook')) {
            $this->forge->modifyColumn('logbook', [
                'lokasi_kegiatan' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 255,
                    'null'       => true,
                ],
            ]);
        }

        foreach (['logbook', 'laporan'] as $folder) {
            $path = rtrim(WRITEPATH, '/\\') . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR . $folder;
            if (! is_dir($path)) {
                mkdir($path, 0755, true);
            }
        }
    }

    public function down(): void
    {
        // Tidak mengecilkan kolom agar data produksi aman.
    }
}
