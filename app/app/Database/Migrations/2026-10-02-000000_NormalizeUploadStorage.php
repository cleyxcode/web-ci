<?php

declare(strict_types=1);

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Pastikan skema & penyimpanan upload siap untuk Hostinger:
 * - dokumentasi logbook = TEXT (JSON max 3 path)
 * - file_laporan cukup untuk path relatif
 * - folder writable/uploads/{logbook,laporan} ada
 * - salin file lama dari public/uploads jika masih ada
 * - normalisasi path DB (buang prefix uploads/, seragamkan format)
 */
final class NormalizeUploadStorage extends Migration
{
    public function up(): void
    {
        $this->ensureSchema();
        $this->ensureStorageDirectories();
        $this->migrateLegacyFiles();
        $this->normalizeDatabasePaths();
    }

    public function down(): void
    {
        // Data path & file yang sudah dipindah tidak di-rollback.
        // Skema dokumentasi dikembalikan ke VARCHAR hanya jika perlu rollback penuh.
        if ($this->db->tableExists('logbook') && $this->db->fieldExists('dokumentasi', 'logbook')) {
            $this->forge->modifyColumn('logbook', [
                'dokumentasi' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 255,
                    'null'       => true,
                ],
            ]);
        }
    }

    private function ensureSchema(): void
    {
        if ($this->db->tableExists('logbook') && $this->db->fieldExists('dokumentasi', 'logbook')) {
            $this->forge->modifyColumn('logbook', [
                'dokumentasi' => [
                    'type' => 'TEXT',
                    'null' => true,
                ],
            ]);
        }

        if ($this->db->tableExists('laporan') && $this->db->fieldExists('file_laporan', 'laporan')) {
            $this->forge->modifyColumn('laporan', [
                'file_laporan' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 255,
                    'null'       => true,
                ],
            ]);
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

    private function migrateLegacyFiles(): void
    {
        $legacyRoot = rtrim(FCPATH, '/\\') . DIRECTORY_SEPARATOR . 'uploads';
        $targetRoot = rtrim(WRITEPATH, '/\\') . DIRECTORY_SEPARATOR . 'uploads';

        if (! is_dir($legacyRoot)) {
            return;
        }

        foreach (['logbook', 'laporan'] as $folder) {
            $from = $legacyRoot . DIRECTORY_SEPARATOR . $folder;
            $to   = $targetRoot . DIRECTORY_SEPARATOR . $folder;

            if (! is_dir($from)) {
                continue;
            }

            if (! is_dir($to)) {
                mkdir($to, 0755, true);
            }

            $entries = scandir($from);
            if ($entries === false) {
                continue;
            }

            foreach ($entries as $entry) {
                if ($entry === '.' || $entry === '..' || $entry === '.gitkeep') {
                    continue;
                }

                $source = $from . DIRECTORY_SEPARATOR . $entry;
                $dest   = $to . DIRECTORY_SEPARATOR . $entry;

                if (! is_file($source) || is_file($dest)) {
                    continue;
                }

                @copy($source, $dest);
            }
        }
    }

    private function normalizeDatabasePaths(): void
    {
        if ($this->db->tableExists('laporan') && $this->db->fieldExists('file_laporan', 'laporan')) {
            $rows = $this->db->table('laporan')
                ->select('id, file_laporan')
                ->where('file_laporan IS NOT NULL')
                ->where('file_laporan !=', '')
                ->get()
                ->getResultArray();

            foreach ($rows as $row) {
                $normalized = $this->normalizeSinglePath((string) $row['file_laporan'], 'laporan');
                if ($normalized !== null && $normalized !== $row['file_laporan']) {
                    $this->db->table('laporan')->where('id', $row['id'])->update([
                        'file_laporan' => $normalized,
                    ]);
                }
            }
        }

        if ($this->db->tableExists('logbook') && $this->db->fieldExists('dokumentasi', 'logbook')) {
            $rows = $this->db->table('logbook')
                ->select('id, dokumentasi')
                ->where('dokumentasi IS NOT NULL')
                ->where('dokumentasi !=', '')
                ->get()
                ->getResultArray();

            foreach ($rows as $row) {
                $normalized = $this->normalizeDokumentasi((string) $row['dokumentasi']);
                if ($normalized !== null && $normalized !== $row['dokumentasi']) {
                    $this->db->table('logbook')->where('id', $row['id'])->update([
                        'dokumentasi' => $normalized,
                    ]);
                }
            }
        }
    }

    private function normalizeSinglePath(string $path, string $defaultFolder): ?string
    {
        $path = trim(str_replace('\\', '/', $path));
        $path = ltrim($path, '/');

        if ($path === '' || str_contains($path, '..')) {
            return null;
        }

        if (str_starts_with($path, 'uploads/')) {
            $path = substr($path, strlen('uploads/'));
        }

        if (! str_contains($path, '/')) {
            $path = $defaultFolder . '/' . $path;
        }

        return $path;
    }

    private function normalizeDokumentasi(string $value): ?string
    {
        $value = trim($value);
        if ($value === '') {
            return null;
        }

        $decoded = json_decode($value, true);
        $paths   = is_array($decoded) ? $decoded : [$value];
        $clean   = [];

        foreach ($paths as $path) {
            if (! is_string($path)) {
                continue;
            }
            $normalized = $this->normalizeSinglePath($path, 'logbook');
            if ($normalized !== null) {
                $clean[] = $normalized;
            }
        }

        $clean = array_values(array_unique($clean));

        if ($clean === []) {
            return null;
        }

        return json_encode($clean, JSON_UNESCAPED_SLASHES);
    }
}
