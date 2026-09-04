<?php

declare(strict_types=1);

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Membuat seluruh tabel dasar aplikasi KKN Monitoring.
 * Idempotent: CREATE TABLE IF NOT EXISTS — aman dijalankan berkali-kali.
 */
final class CreateCoreTables extends Migration
{
    public function up(): void
    {
        $this->db->query(
            'CREATE TABLE IF NOT EXISTS `users` (
                `id`         INT(11) NOT NULL AUTO_INCREMENT,
                `nama`       VARCHAR(100) NOT NULL,
                `username`   VARCHAR(50) NOT NULL,
                `email`      VARCHAR(100) NOT NULL,
                `password`   VARCHAR(255) NOT NULL,
                `role`       ENUM("admin","dpl","mahasiswa") NOT NULL DEFAULT "mahasiswa",
                `foto`       VARCHAR(255) DEFAULT NULL,
                `is_active`  TINYINT(1) NOT NULL DEFAULT 1,
                `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
                `updated_at` DATETIME DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
                PRIMARY KEY (`id`),
                UNIQUE KEY `uq_users_username` (`username`),
                UNIQUE KEY `uq_users_email` (`email`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4'
        );

        $this->db->query(
            'CREATE TABLE IF NOT EXISTS `otp_codes` (
                `id`         INT(11) NOT NULL AUTO_INCREMENT,
                `user_id`    INT(11) DEFAULT NULL,
                `email`      VARCHAR(100) NOT NULL,
                `otp_code`   VARCHAR(10) NOT NULL,
                `type`       VARCHAR(30) NOT NULL DEFAULT "forgot_password",
                `is_used`    TINYINT(1) NOT NULL DEFAULT 0,
                `expired_at` DATETIME NOT NULL,
                `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
                PRIMARY KEY (`id`),
                KEY `idx_otp_email` (`email`),
                KEY `idx_otp_user` (`user_id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4'
        );

        $this->db->query(
            'CREATE TABLE IF NOT EXISTS `lokasi_kkn` (
                `id`         INT(11) NOT NULL AUTO_INCREMENT,
                `nama_desa`  VARCHAR(100) NOT NULL,
                `kecamatan`  VARCHAR(100) DEFAULT NULL,
                `kabupaten`  VARCHAR(100) DEFAULT NULL,
                PRIMARY KEY (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4'
        );

        $this->db->query(
            'CREATE TABLE IF NOT EXISTS `dpl` (
                `id`      INT(11) NOT NULL AUTO_INCREMENT,
                `user_id` INT(11) NOT NULL,
                `nidn`    VARCHAR(20) DEFAULT NULL,
                `nama`    VARCHAR(100) NOT NULL,
                `prodi`   VARCHAR(100) DEFAULT NULL,
                `no_hp`   VARCHAR(20) DEFAULT NULL,
                PRIMARY KEY (`id`),
                UNIQUE KEY `uq_dpl_user` (`user_id`),
                KEY `idx_dpl_nidn` (`nidn`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4'
        );

        $this->db->query(
            'CREATE TABLE IF NOT EXISTS `kelompok_kkn` (
                `id`                     INT(11) NOT NULL AUTO_INCREMENT,
                `nama_kelompok`          VARCHAR(100) NOT NULL,
                `dpl_id`                 INT(11) DEFAULT NULL,
                `lokasi_id`              INT(11) DEFAULT NULL,
                `ketua_mahasiswa_id`     INT(11) DEFAULT NULL,
                `periode`                VARCHAR(50) DEFAULT NULL,
                `tanggal_mulai`          DATE DEFAULT NULL,
                `tanggal_selesai`        DATE DEFAULT NULL,
                `alamat_penelitian`      TEXT DEFAULT NULL,
                `dosen_pendamping`       VARCHAR(100) DEFAULT NULL,
                `no_hp_dosen_pendamping` VARCHAR(20) DEFAULT NULL,
                `latitude`               DECIMAL(10,7) DEFAULT NULL,
                `longitude`              DECIMAL(10,7) DEFAULT NULL,
                `lokasi_gps_at`          DATETIME DEFAULT NULL,
                PRIMARY KEY (`id`),
                KEY `idx_kelompok_dpl` (`dpl_id`),
                KEY `idx_kelompok_lokasi` (`lokasi_id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4'
        );

        $this->db->query(
            'CREATE TABLE IF NOT EXISTS `mahasiswa` (
                `id`          INT(11) NOT NULL AUTO_INCREMENT,
                `user_id`     INT(11) NOT NULL,
                `npm`         VARCHAR(20) DEFAULT NULL,
                `nama`        VARCHAR(100) NOT NULL,
                `prodi`       VARCHAR(100) DEFAULT NULL,
                `kelompok_id` INT(11) DEFAULT NULL,
                `no_hp`       VARCHAR(20) DEFAULT NULL,
                PRIMARY KEY (`id`),
                UNIQUE KEY `uq_mahasiswa_user` (`user_id`),
                KEY `idx_mahasiswa_kelompok` (`kelompok_id`),
                KEY `idx_mahasiswa_npm` (`npm`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4'
        );

        $this->db->query(
            'CREATE TABLE IF NOT EXISTS `logbook` (
                `id`              INT(11) NOT NULL AUTO_INCREMENT,
                `mahasiswa_id`    INT(11) NOT NULL,
                `tanggal`         DATE NOT NULL,
                `kegiatan`        TEXT NOT NULL,
                `lokasi_kegiatan` VARCHAR(150) DEFAULT NULL,
                `dokumentasi`     VARCHAR(255) DEFAULT NULL,
                `status`          ENUM("menunggu","divalidasi","ditolak") NOT NULL DEFAULT "menunggu",
                `catatan_dpl`     TEXT DEFAULT NULL,
                `validated_by`    INT(11) DEFAULT NULL,
                `validated_at`    DATETIME DEFAULT NULL,
                `created_at`      DATETIME DEFAULT CURRENT_TIMESTAMP,
                PRIMARY KEY (`id`),
                KEY `idx_logbook_mahasiswa` (`mahasiswa_id`),
                KEY `idx_logbook_status` (`status`),
                KEY `idx_logbook_tanggal` (`tanggal`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4'
        );

        $this->db->query(
            'CREATE TABLE IF NOT EXISTS `laporan` (
                `id`           INT(11) NOT NULL AUTO_INCREMENT,
                `mahasiswa_id` INT(11) NOT NULL,
                `judul`        VARCHAR(255) NOT NULL,
                `deskripsi`    TEXT DEFAULT NULL,
                `file_laporan` VARCHAR(255) DEFAULT NULL,
                `status`       ENUM("menunggu","diterima","ditolak") NOT NULL DEFAULT "menunggu",
                `catatan_dpl`  TEXT DEFAULT NULL,
                `reviewed_by`  INT(11) DEFAULT NULL,
                `reviewed_at`  DATETIME DEFAULT NULL,
                `created_at`   DATETIME DEFAULT CURRENT_TIMESTAMP,
                `updated_at`   DATETIME DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
                PRIMARY KEY (`id`),
                KEY `idx_laporan_mahasiswa` (`mahasiswa_id`),
                KEY `idx_laporan_status` (`status`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4'
        );

        $this->db->query(
            'CREATE TABLE IF NOT EXISTS `penilaian` (
                `id`              INT(11) NOT NULL AUTO_INCREMENT,
                `mahasiswa_id`    INT(11) NOT NULL,
                `dpl_id`          INT(11) DEFAULT NULL,
                `nilai_keaktifan` DECIMAL(5,2) DEFAULT NULL,
                `nilai_logbook`   DECIMAL(5,2) DEFAULT NULL,
                `nilai_laporan`   DECIMAL(5,2) DEFAULT NULL,
                `nilai_akhir`     DECIMAL(5,2) DEFAULT NULL,
                `grade`           VARCHAR(5) DEFAULT NULL,
                `catatan`         TEXT DEFAULT NULL,
                `created_at`      DATETIME DEFAULT CURRENT_TIMESTAMP,
                `updated_at`      DATETIME DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
                PRIMARY KEY (`id`),
                UNIQUE KEY `uq_penilaian_mahasiswa` (`mahasiswa_id`),
                KEY `idx_penilaian_dpl` (`dpl_id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4'
        );

        $this->db->query(
            'CREATE TABLE IF NOT EXISTS `evaluasi` (
                `id`                INT(11) NOT NULL AUTO_INCREMENT,
                `mahasiswa_id`      INT(11) NOT NULL,
                `tipe_evaluasi`     VARCHAR(20) NOT NULL DEFAULT "mahasiswa",
                `kelompok_id`       INT(11) DEFAULT NULL,
                `dpl_id`            INT(11) DEFAULT NULL,
                `penilai_id`        INT(11) DEFAULT NULL,
                `detail_evaluasi`   TEXT DEFAULT NULL,
                `rating`            TINYINT(1) DEFAULT NULL,
                `aspek_bimbingan`   TINYINT(1) DEFAULT NULL,
                `aspek_lokasi`      TINYINT(1) DEFAULT NULL,
                `aspek_pelaksanaan` TINYINT(1) DEFAULT NULL,
                `komentar`          TEXT DEFAULT NULL,
                `skor_total`        DECIMAL(5,2) DEFAULT NULL,
                `kategori`          VARCHAR(100) DEFAULT NULL,
                `rekomendasi`       TEXT DEFAULT NULL,
                `created_at`        DATETIME DEFAULT CURRENT_TIMESTAMP,
                `updated_at`        DATETIME DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
                PRIMARY KEY (`id`),
                UNIQUE KEY `uq_evaluasi_mahasiswa_tipe` (`mahasiswa_id`, `tipe_evaluasi`),
                KEY `idx_evaluasi_kelompok` (`kelompok_id`),
                KEY `idx_evaluasi_dpl` (`dpl_id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4'
        );

        $this->db->query(
            'CREATE TABLE IF NOT EXISTS `evaluasi_kriteria` (
                `id`         INT(11) NOT NULL AUTO_INCREMENT,
                `nama`       VARCHAR(150) NOT NULL,
                `deskripsi`  VARCHAR(255) DEFAULT NULL,
                `urutan`     INT(11) NOT NULL DEFAULT 0,
                `aktif`      TINYINT(1) NOT NULL DEFAULT 1,
                `cakupan`    VARCHAR(20) NOT NULL DEFAULT "semua",
                `target_id`  INT(11) DEFAULT NULL,
                `created_by` INT(11) DEFAULT NULL,
                `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
                `updated_at` DATETIME DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
                PRIMARY KEY (`id`),
                KEY `idx_evaluasi_kriteria_aktif` (`aktif`, `urutan`),
                KEY `idx_evaluasi_kriteria_created_by` (`created_by`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4'
        );

        $this->db->query(
            'CREATE TABLE IF NOT EXISTS `pengumuman` (
                `id`         INT(11) NOT NULL AUTO_INCREMENT,
                `judul`      VARCHAR(255) NOT NULL,
                `isi`        TEXT NOT NULL,
                `created_by` INT(11) DEFAULT NULL,
                `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
                `updated_at` DATETIME DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
                PRIMARY KEY (`id`),
                KEY `idx_pengumuman_created_by` (`created_by`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4'
        );

        $this->db->query(
            'CREATE TABLE IF NOT EXISTS `notifikasi` (
                `id`         INT(11) NOT NULL AUTO_INCREMENT,
                `user_id`    INT(11) NOT NULL,
                `judul`      VARCHAR(255) NOT NULL,
                `pesan`      TEXT NOT NULL,
                `type`       VARCHAR(50) DEFAULT NULL,
                `is_read`    TINYINT(1) NOT NULL DEFAULT 0,
                `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
                PRIMARY KEY (`id`),
                KEY `idx_notifikasi_user` (`user_id`),
                KEY `idx_notifikasi_read` (`is_read`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4'
        );

        $this->db->query(
            'CREATE TABLE IF NOT EXISTS `audit_trail` (
                `id`          INT(11) NOT NULL AUTO_INCREMENT,
                `user_id`     INT(11) DEFAULT NULL,
                `user_nama`   VARCHAR(100) DEFAULT NULL,
                `user_role`   VARCHAR(20) DEFAULT NULL,
                `aksi`        VARCHAR(50) NOT NULL,
                `entitas`     VARCHAR(50) NOT NULL,
                `entitas_id`  INT(11) DEFAULT NULL,
                `deskripsi`   VARCHAR(255) NOT NULL,
                `data_lama`   TEXT DEFAULT NULL,
                `data_baru`   TEXT DEFAULT NULL,
                `created_at`  DATETIME DEFAULT CURRENT_TIMESTAMP,
                PRIMARY KEY (`id`),
                KEY `idx_entitas` (`entitas`, `entitas_id`),
                KEY `idx_created` (`created_at`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4'
        );
    }

    public function down(): void
    {
        // Tabel produksi tidak dihapus otomatis.
    }
}
