<?php

declare(strict_types=1);

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

final class SeedEvaluasiKriteria extends Migration
{
    public function up(): void
    {
        if (! $this->db->tableExists('evaluasi_kriteria')) {
            return;
        }

        if ((int) $this->db->table('evaluasi_kriteria')->countAllResults() > 0) {
            return;
        }

        $this->db->table('evaluasi_kriteria')->insertBatch([
            ['nama' => 'Kehadiran dan kedisiplinan', 'deskripsi' => 'Konsistensi hadir dan mematuhi jadwal KKN.', 'urutan' => 1, 'aktif' => 1, 'cakupan' => 'semua'],
            ['nama' => 'Keaktifan dan tanggung jawab', 'deskripsi' => 'Inisiatif, tanggung jawab, dan kontribusi mahasiswa.', 'urutan' => 2, 'aktif' => 1, 'cakupan' => 'semua'],
            ['nama' => 'Kerja sama dan komunikasi', 'deskripsi' => 'Kemampuan bekerja sama dengan tim dan masyarakat.', 'urutan' => 3, 'aktif' => 1, 'cakupan' => 'semua'],
            ['nama' => 'Pelaksanaan program kerja', 'deskripsi' => 'Kualitas pelaksanaan program kerja di lapangan.', 'urutan' => 4, 'aktif' => 1, 'cakupan' => 'semua'],
            ['nama' => 'Etika dan sikap', 'deskripsi' => 'Sikap, etika, dan kemampuan beradaptasi di lokasi KKN.', 'urutan' => 5, 'aktif' => 1, 'cakupan' => 'semua'],
        ]);
    }

    public function down(): void
    {
        // Data kriteria tidak dihapus otomatis karena dapat sudah dipakai riwayat evaluasi.
    }
}
