-- Kolom aspek evaluasi kegiatan (MySQL 8)
-- docker exec -i kkn_db mysql -uroot -proot123 kkn_tematik < sql/alter_evaluasi.sql

SET @db := DATABASE();

SET @exists := (
  SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = @db AND TABLE_NAME = 'evaluasi' AND COLUMN_NAME = 'aspek_bimbingan'
);
SET @sql := IF(@exists = 0,
  'ALTER TABLE `evaluasi` ADD COLUMN `aspek_bimbingan` tinyint(1) DEFAULT NULL AFTER `rating`',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @exists := (
  SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = @db AND TABLE_NAME = 'evaluasi' AND COLUMN_NAME = 'aspek_lokasi'
);
SET @sql := IF(@exists = 0,
  'ALTER TABLE `evaluasi` ADD COLUMN `aspek_lokasi` tinyint(1) DEFAULT NULL AFTER `aspek_bimbingan`',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @exists := (
  SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = @db AND TABLE_NAME = 'evaluasi' AND COLUMN_NAME = 'aspek_pelaksanaan'
);
SET @sql := IF(@exists = 0,
  'ALTER TABLE `evaluasi` ADD COLUMN `aspek_pelaksanaan` tinyint(1) DEFAULT NULL AFTER `aspek_lokasi`',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @exists := (
  SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = @db AND TABLE_NAME = 'evaluasi' AND COLUMN_NAME = 'updated_at'
);
SET @sql := IF(@exists = 0,
  'ALTER TABLE `evaluasi` ADD COLUMN `updated_at` datetime DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP AFTER `created_at`',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @exists := (
  SELECT COUNT(*) FROM information_schema.STATISTICS
  WHERE TABLE_SCHEMA = @db AND TABLE_NAME = 'evaluasi' AND INDEX_NAME = 'uq_evaluasi_mahasiswa'
);
SET @sql := IF(@exists = 0,
  'ALTER TABLE `evaluasi` ADD UNIQUE KEY `uq_evaluasi_mahasiswa` (`mahasiswa_id`)',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Memisahkan evaluasi yang ditulis mahasiswa dan evaluasi yang dibuat admin.
SET @exists := (
  SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = @db AND TABLE_NAME = 'evaluasi' AND COLUMN_NAME = 'tipe_evaluasi'
);
SET @sql := IF(@exists = 0,
  'ALTER TABLE `evaluasi` ADD COLUMN `tipe_evaluasi` varchar(20) NOT NULL DEFAULT ''mahasiswa'' AFTER `mahasiswa_id`',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @exists := (
  SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = @db AND TABLE_NAME = 'evaluasi' AND COLUMN_NAME = 'penilai_id'
);
SET @sql := IF(@exists = 0,
  'ALTER TABLE `evaluasi` ADD COLUMN `penilai_id` int(11) DEFAULT NULL AFTER `dpl_id`',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @exists := (
  SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = @db AND TABLE_NAME = 'evaluasi' AND COLUMN_NAME = 'detail_evaluasi'
);
SET @sql := IF(@exists = 0,
  'ALTER TABLE `evaluasi` ADD COLUMN `detail_evaluasi` text DEFAULT NULL AFTER `penilai_id`',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

ALTER TABLE `evaluasi` MODIFY COLUMN `kategori` varchar(100) DEFAULT NULL;

SET @exists := (
  SELECT COUNT(*) FROM information_schema.STATISTICS
  WHERE TABLE_SCHEMA = @db AND TABLE_NAME = 'evaluasi' AND INDEX_NAME = 'uq_evaluasi_mahasiswa_tipe'
);
SET @sql := IF(@exists = 0,
  'ALTER TABLE `evaluasi` ADD UNIQUE KEY `uq_evaluasi_mahasiswa_tipe` (`mahasiswa_id`, `tipe_evaluasi`)',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Buat indeks pengganti lebih dahulu karena foreign key evaluasi memakai mahasiswa_id.
SET @exists := (
  SELECT COUNT(*) FROM information_schema.STATISTICS
  WHERE TABLE_SCHEMA = @db AND TABLE_NAME = 'evaluasi' AND INDEX_NAME = 'uq_evaluasi_mahasiswa'
);
SET @sql := IF(@exists > 0,
  'ALTER TABLE `evaluasi` DROP INDEX `uq_evaluasi_mahasiswa`',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;
