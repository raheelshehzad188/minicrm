-- Mino CRM — Lead types (Clinic / Academy) + webhook API logging
-- Safe to re-run. Apply on existing databases that already have leads.

USE `minicrm`;
SET NAMES utf8mb4;

-- ============================================================
-- Leads: type + clinic/academy fields
-- ============================================================

SET @col_exists := (
  SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'leads' AND COLUMN_NAME = 'lead_type'
);
SET @sql := IF(@col_exists = 0,
  'ALTER TABLE `leads`
     ADD COLUMN `lead_type` VARCHAR(50) NOT NULL DEFAULT ''clinic'' AFTER `organization_id`,
     ADD COLUMN `branch` VARCHAR(150) DEFAULT NULL AFTER `description`,
     ADD COLUMN `treatment` VARCHAR(150) DEFAULT NULL AFTER `branch`,
     ADD COLUMN `course` VARCHAR(150) DEFAULT NULL AFTER `treatment`,
     ADD COLUMN `preferred_batch` VARCHAR(100) DEFAULT NULL AFTER `course`,
     ADD COLUMN `appointment_date` DATE DEFAULT NULL AFTER `preferred_batch`,
     ADD COLUMN `appointment_time` TIME DEFAULT NULL AFTER `appointment_date`,
     ADD KEY `idx_leads_type` (`organization_id`, `lead_type`)',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

UPDATE `leads` SET `lead_type` = 'clinic' WHERE `lead_type` IS NULL OR `lead_type` = '';

-- ============================================================
-- Organizations: API key for webhook auth
-- ============================================================

SET @col_exists := (
  SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'organizations' AND COLUMN_NAME = 'api_key'
);
SET @sql := IF(@col_exists = 0,
  'ALTER TABLE `organizations` ADD COLUMN `api_key` VARCHAR(64) DEFAULT NULL AFTER `status`, ADD UNIQUE KEY `uq_organizations_api_key` (`api_key`)',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

UPDATE `organizations`
SET `api_key` = LOWER(CONCAT(
  SUBSTRING(MD5(CONCAT(id, UNIX_TIMESTAMP(), RAND())), 1, 32),
  SUBSTRING(MD5(CONCAT(slug, RAND(), UUID())), 1, 32)
))
WHERE `api_key` IS NULL OR `api_key` = '';

-- ============================================================
-- Webhook / API request logs
-- ============================================================

CREATE TABLE IF NOT EXISTS `webhook_logs` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `organization_id` INT UNSIGNED DEFAULT NULL,
  `endpoint` VARCHAR(255) NOT NULL,
  `method` VARCHAR(10) NOT NULL DEFAULT 'POST',
  `request_payload` MEDIUMTEXT NULL,
  `response_payload` MEDIUMTEXT NULL,
  `response_code` SMALLINT UNSIGNED DEFAULT NULL,
  `ip_address` VARCHAR(45) DEFAULT NULL,
  `user_agent` VARCHAR(500) DEFAULT NULL,
  `error_message` TEXT NULL,
  `request_time` DATETIME NOT NULL,
  `created_at` DATETIME NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_webhook_org` (`organization_id`),
  KEY `idx_webhook_endpoint` (`endpoint`),
  KEY `idx_webhook_time` (`request_time`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
