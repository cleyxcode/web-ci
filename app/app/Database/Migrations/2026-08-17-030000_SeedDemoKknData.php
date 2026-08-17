<?php

declare(strict_types=1);

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/** Seeder demo idempotent: 3 DPL x 1 kelompok x 4 mahasiswa. */
final class SeedDemoKknData extends Migration
{
    public function up(): void
    {
        $db = $this->db;
        $password = password_hash('demo123', PASSWORD_BCRYPT);
        $criteria = $db->table('evaluasi_kriteria')->get()->getResultArray();

        for ($groupNo = 1; $groupNo <= 3; $groupNo++) {
            $dplUsername = 'demo.dpl' . $groupNo;
            $dplUser = $this->firstOrInsert('users', ['username' => $dplUsername], [
                'nama' => 'Dosen Demo ' . $groupNo, 'username' => $dplUsername, 'email' => $dplUsername . '@ukim.test',
                'password' => $password, 'role' => 'dpl', 'is_active' => 1,
            ]);
            $dplId = $this->firstOrInsert('dpl', ['user_id' => $dplUser['id']], [
                'user_id' => $dplUser['id'], 'nidn' => 'DEMO' . str_pad((string) $groupNo, 5, '0', STR_PAD_LEFT),
                'nama' => 'Dosen Demo ' . $groupNo, 'prodi' => 'Ilmu Komputer', 'no_hp' => '081230000' . $groupNo,
            ])['id'];

            $locationId = $this->firstOrInsert('lokasi_kkn', ['nama_desa' => 'Desa Demo ' . $groupNo], [
                'nama_desa' => 'Desa Demo ' . $groupNo, 'kecamatan' => 'Kecamatan Demo', 'kabupaten' => 'Ambon',
            ])['id'];
            $group = $this->firstOrInsert('kelompok_kkn', ['nama_kelompok' => 'Kelompok Demo ' . $groupNo], [
                'nama_kelompok' => 'Kelompok Demo ' . $groupNo, 'dpl_id' => $dplId, 'lokasi_id' => $locationId,
                'periode' => 'KKN Demo 2026', 'tanggal_mulai' => '2026-08-01', 'tanggal_selesai' => '2026-09-30',
                'alamat_penelitian' => 'Lokasi simulasi data KKN', 'latitude' => -3.695 + ($groupNo / 1000),
                'longitude' => 128.183 + ($groupNo / 1000), 'lokasi_gps_at' => date('Y-m-d H:i:s'),
            ]);

            for ($memberNo = 1; $memberNo <= 4; $memberNo++) {
                $username = 'demo.mhs' . $groupNo . $memberNo;
                $student = $this->firstOrInsert('users', ['username' => $username], [
                    'nama' => 'Mahasiswa Demo ' . $groupNo . '.' . $memberNo, 'username' => $username,
                    'email' => $username . '@ukim.test', 'password' => $password, 'role' => 'mahasiswa', 'is_active' => 1,
                ]);
                $student = $this->firstOrInsert('mahasiswa', ['user_id' => $student['id']], [
                    'user_id' => $student['id'], 'npm' => 'DEMO' . $groupNo . str_pad((string) $memberNo, 2, '0', STR_PAD_LEFT),
                    'nama' => 'Mahasiswa Demo ' . $groupNo . '.' . $memberNo, 'prodi' => 'Teknik Informatika',
                    'kelompok_id' => $group['id'], 'no_hp' => '082230000' . $groupNo . $memberNo,
                ]);
                $studentId = (int) $student['id'];
                if ($memberNo === 1) $db->table('kelompok_kkn')->where('id', $group['id'])->update(['ketua_mahasiswa_id' => $studentId]);

                if ((int) $db->table('logbook')->where('mahasiswa_id', $studentId)->countAllResults() === 0) {
                    $db->table('logbook')->insertBatch([
                        ['mahasiswa_id' => $studentId, 'tanggal' => '2026-08-10', 'kegiatan' => 'Observasi awal dan koordinasi dengan masyarakat.', 'lokasi_kegiatan' => 'Desa Demo ' . $groupNo, 'status' => 'divalidasi', 'catatan_dpl' => 'Kegiatan berjalan baik.', 'validated_by' => $dplId, 'validated_at' => '2026-08-11 10:00:00'],
                        ['mahasiswa_id' => $studentId, 'tanggal' => '2026-08-15', 'kegiatan' => 'Pelaksanaan program kerja dan dokumentasi lapangan.', 'lokasi_kegiatan' => 'Desa Demo ' . $groupNo, 'status' => $memberNo === 4 ? 'menunggu' : 'divalidasi', 'catatan_dpl' => $memberNo === 4 ? null : 'Sudah divalidasi.', 'validated_by' => $memberNo === 4 ? null : $dplId, 'validated_at' => $memberNo === 4 ? null : '2026-08-16 10:00:00'],
                    ]);
                }
                if ((int) $db->table('laporan')->where('mahasiswa_id', $studentId)->countAllResults() === 0) {
                    $db->table('laporan')->insert(['mahasiswa_id' => $studentId, 'judul' => 'Laporan kegiatan Kelompok Demo ' . $groupNo, 'deskripsi' => 'Laporan simulasi kegiatan KKN untuk pengujian dashboard.', 'status' => $memberNo === 4 ? 'menunggu' : 'diterima', 'catatan_dpl' => $memberNo === 4 ? null : 'Laporan diterima.', 'reviewed_by' => $memberNo === 4 ? null : $dplId, 'reviewed_at' => $memberNo === 4 ? null : '2026-08-16 11:00:00']);
                }
                if ((int) $db->table('penilaian')->where('mahasiswa_id', $studentId)->countAllResults() === 0) {
                    $db->table('penilaian')->insert(['mahasiswa_id' => $studentId, 'dpl_id' => $dplId, 'nilai_keaktifan' => 80 + $memberNo, 'nilai_logbook' => 82 + $memberNo, 'nilai_laporan' => 84 + $memberNo, 'nilai_akhir' => 82 + $memberNo, 'grade' => 'B', 'catatan' => 'Data penilaian demo.']);
                }
                if ((int) $db->table('evaluasi')->where('mahasiswa_id', $studentId)->where('tipe_evaluasi', 'dpl')->countAllResults() === 0) {
                    $detail = [];
                    foreach ($criteria as $criterion) $detail[] = ['id' => (int) $criterion['id'], 'nama' => $criterion['nama'], 'deskripsi' => $criterion['deskripsi'] ?? '', 'rating' => 4];
                    $db->table('evaluasi')->insert(['mahasiswa_id' => $studentId, 'tipe_evaluasi' => 'dpl', 'kelompok_id' => $group['id'], 'dpl_id' => $dplId, 'penilai_id' => $dplUser['id'], 'detail_evaluasi' => json_encode($detail, JSON_UNESCAPED_UNICODE), 'rating' => 4, 'aspek_bimbingan' => 4, 'aspek_lokasi' => 4, 'aspek_pelaksanaan' => 4, 'komentar' => 'Performa baik dan aktif dalam kegiatan kelompok demo.', 'skor_total' => 4, 'kategori' => 'Baik', 'rekomendasi' => 'Pertahankan kolaborasi.']);
                }
            }
        }
    }

    public function down(): void
    {
        // Seeder demo tidak dihapus otomatis agar tidak menyentuh data pengguna.
    }

    /** @return array<string, mixed> */
    private function firstOrInsert(string $table, array $where, array $data): array
    {
        $row = $this->db->table($table)->where($where)->get()->getRowArray();
        if ($row !== null) return $row;
        $this->db->table($table)->insert($data);
        return array_merge($data, ['id' => (int) $this->db->insertID()]);
    }
}
