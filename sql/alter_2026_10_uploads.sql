-- Migrasi skema upload & kolom inti (MySQL 8)
-- Aman dijalankan berulang: kolom/tabel yang sudah ada di-skip.
--
-- Contoh:
--   docker exec -i kkn_db mysql -uroot -proot123 kkn_tematik < sql/alter_2026_10_uploads.sql

SET @db := DATABASE();

-- dokumentasi logbook → TEXT (JSON max 3 path)
SET @exists := (
  SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = @db AND TABLE_NAME = 'logbook' AND COLUMN_NAME = 'dokumentasi'
);
SET @sql := IF(@exists > 0,
  'ALTER TABLE `logbook` MODIFY COLUMN `dokumentasi` TEXT NULL',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- created_at laporan
SET @exists := (
  SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = @db AND TABLE_NAME = 'laporan' AND COLUMN_NAME = 'created_at'
);
SET @sql := IF(@exists = 0,
  'ALTER TABLE `laporan` ADD COLUMN `created_at` datetime DEFAULT CURRENT_TIMESTAMP AFTER `reviewed_at`',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- created_at logbook
SET @exists := (
  SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = @db AND TABLE_NAME = 'logbook' AND COLUMN_NAME = 'created_at'
);
SET @sql := IF(@exists = 0,
  'ALTER TABLE `logbook` ADD COLUMN `created_at` datetime DEFAULT CURRENT_TIMESTAMP AFTER `validated_at`',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- file_laporan tetap VARCHAR(255)
SET @exists := (
  SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = @db AND TABLE_NAME = 'laporan' AND COLUMN_NAME = 'file_laporan'
);
SET @sql := IF(@exists > 0,
  'ALTER TABLE `laporan` MODIFY COLUMN `file_laporan` varchar(255) DEFAULT NULL',
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

-- Normalisasi path lama (buang prefix uploads/ jika ada)
UPDATE `laporan`
SET `file_laporan` = TRIM(LEADING '/' FROM REPLACE(`file_laporan`, 'uploads/', ''))
WHERE `file_laporan` IS NOT NULL
  AND `file_laporan` != ''
  AND (`file_laporan` LIKE 'uploads/%' OR `file_laporan` LIKE '/uploads/%');
