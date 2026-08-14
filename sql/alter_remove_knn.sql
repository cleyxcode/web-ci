-- Hapus KNN + rapikan relasi penilaian (MySQL 8)
-- docker exec -i kkn_db mysql -uroot -proot123 kkn_tematik < sql/alter_remove_knn.sql

SET @db := DATABASE();

-- Drop prediksi_knn
SET @exists := (
  SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = @db AND TABLE_NAME = 'penilaian' AND COLUMN_NAME = 'prediksi_knn'
);
SET @sql := IF(@exists > 0,
  'ALTER TABLE `penilaian` DROP COLUMN `prediksi_knn`',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Unique 1 penilaian per mahasiswa
SET @exists := (
  SELECT COUNT(*) FROM information_schema.STATISTICS
  WHERE TABLE_SCHEMA = @db AND TABLE_NAME = 'penilaian' AND INDEX_NAME = 'uq_penilaian_mahasiswa'
);
SET @sql := IF(@exists = 0,
  'ALTER TABLE `penilaian` ADD UNIQUE KEY `uq_penilaian_mahasiswa` (`mahasiswa_id`)',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Index dpl
SET @exists := (
  SELECT COUNT(*) FROM information_schema.STATISTICS
  WHERE TABLE_SCHEMA = @db AND TABLE_NAME = 'penilaian' AND INDEX_NAME = 'idx_penilaian_dpl'
);
SET @sql := IF(@exists = 0,
  'ALTER TABLE `penilaian` ADD KEY `idx_penilaian_dpl` (`dpl_id`)',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- FK laporan.reviewed_by -> dpl (opsional, skip jika sudah ada)
SET @exists := (
  SELECT COUNT(*) FROM information_schema.TABLE_CONSTRAINTS
  WHERE TABLE_SCHEMA = @db AND TABLE_NAME = 'laporan' AND CONSTRAINT_NAME = 'fk_laporan_reviewed_by'
);
SET @sql := IF(@exists = 0,
  'ALTER TABLE `laporan` ADD CONSTRAINT `fk_laporan_reviewed_by` FOREIGN KEY (`reviewed_by`) REFERENCES `dpl`(`id`) ON DELETE SET NULL',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;
