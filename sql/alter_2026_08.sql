-- Migrasi untuk database yang sudah berjalan (MySQL 8)
-- Aman dijalankan berulang: kolom/tabel yang sudah ada di-skip.

-- Contoh pemakaian:
--   docker exec -i kkn_db mysql -uroot -proot123 kkn_tematik < sql/alter_2026_08.sql

SET @db := DATABASE();

-- ketua_mahasiswa_id
SET @exists := (
  SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = @db AND TABLE_NAME = 'kelompok_kkn' AND COLUMN_NAME = 'ketua_mahasiswa_id'
);
SET @sql := IF(@exists = 0,
  'ALTER TABLE `kelompok_kkn` ADD COLUMN `ketua_mahasiswa_id` int(11) DEFAULT NULL AFTER `lokasi_id`',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- dosen_pendamping
SET @exists := (
  SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = @db AND TABLE_NAME = 'kelompok_kkn' AND COLUMN_NAME = 'dosen_pendamping'
);
SET @sql := IF(@exists = 0,
  'ALTER TABLE `kelompok_kkn` ADD COLUMN `dosen_pendamping` varchar(100) DEFAULT NULL AFTER `alamat_penelitian`',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- no_hp_dosen_pendamping
SET @exists := (
  SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = @db AND TABLE_NAME = 'kelompok_kkn' AND COLUMN_NAME = 'no_hp_dosen_pendamping'
);
SET @sql := IF(@exists = 0,
  'ALTER TABLE `kelompok_kkn` ADD COLUMN `no_hp_dosen_pendamping` varchar(20) DEFAULT NULL AFTER `dosen_pendamping`',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- latitude
SET @exists := (
  SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = @db AND TABLE_NAME = 'kelompok_kkn' AND COLUMN_NAME = 'latitude'
);
SET @sql := IF(@exists = 0,
  'ALTER TABLE `kelompok_kkn` ADD COLUMN `latitude` decimal(10,7) DEFAULT NULL AFTER `no_hp_dosen_pendamping`',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- longitude
SET @exists := (
  SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = @db AND TABLE_NAME = 'kelompok_kkn' AND COLUMN_NAME = 'longitude'
);
SET @sql := IF(@exists = 0,
  'ALTER TABLE `kelompok_kkn` ADD COLUMN `longitude` decimal(10,7) DEFAULT NULL AFTER `latitude`',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- lokasi_gps_at
SET @exists := (
  SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = @db AND TABLE_NAME = 'kelompok_kkn' AND COLUMN_NAME = 'lokasi_gps_at'
);
SET @sql := IF(@exists = 0,
  'ALTER TABLE `kelompok_kkn` ADD COLUMN `lokasi_gps_at` datetime DEFAULT NULL AFTER `longitude`',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

CREATE TABLE IF NOT EXISTS `audit_trail` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `user_nama` varchar(100) DEFAULT NULL,
  `user_role` varchar(20) DEFAULT NULL,
  `aksi` varchar(50) NOT NULL,
  `entitas` varchar(50) NOT NULL,
  `entitas_id` int(11) DEFAULT NULL,
  `deskripsi` varchar(255) NOT NULL,
  `data_lama` text DEFAULT NULL,
  `data_baru` text DEFAULT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_entitas` (`entitas`, `entitas_id`),
  KEY `idx_created` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;