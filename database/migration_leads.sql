-- Mino CRM — Leads module + required lookup master tables
-- Organization-scoped. Safe to re-run (CREATE IF NOT EXISTS).

USE `minicrm`;
SET NAMES utf8mb4;

-- ============================================================
-- Master lookups (minimal support layer for Leads)
-- ============================================================

CREATE TABLE IF NOT EXISTS `lead_statuses` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `organization_id` INT UNSIGNED NOT NULL,
  `name` VARCHAR(100) NOT NULL,
  `slug` VARCHAR(100) NOT NULL,
  `color` VARCHAR(20) NOT NULL DEFAULT '#0F766E',
  `icon` VARCHAR(50) DEFAULT 'fa-circle',
  `sort_order` INT NOT NULL DEFAULT 0,
  `is_won` TINYINT(1) NOT NULL DEFAULT 0,
  `is_lost` TINYINT(1) NOT NULL DEFAULT 0,
  `is_default` TINYINT(1) NOT NULL DEFAULT 0,
  `is_active` TINYINT(1) NOT NULL DEFAULT 1,
  `created_at` DATETIME NOT NULL,
  `updated_at` DATETIME DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_lead_status_org_slug` (`organization_id`,`slug`),
  KEY `idx_lead_status_org` (`organization_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `lead_sources` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `organization_id` INT UNSIGNED NOT NULL,
  `name` VARCHAR(100) NOT NULL,
  `slug` VARCHAR(100) NOT NULL,
  `color` VARCHAR(20) NOT NULL DEFAULT '#0284C7',
  `icon` VARCHAR(50) DEFAULT 'fa-globe',
  `sort_order` INT NOT NULL DEFAULT 0,
  `is_active` TINYINT(1) NOT NULL DEFAULT 1,
  `created_at` DATETIME NOT NULL,
  `updated_at` DATETIME DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_lead_source_org_slug` (`organization_id`,`slug`),
  KEY `idx_lead_source_org` (`organization_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `lead_tags` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `organization_id` INT UNSIGNED NOT NULL,
  `name` VARCHAR(100) NOT NULL,
  `color` VARCHAR(20) NOT NULL DEFAULT '#64748B',
  `description` VARCHAR(255) DEFAULT NULL,
  `is_active` TINYINT(1) NOT NULL DEFAULT 1,
  `created_at` DATETIME NOT NULL,
  `updated_at` DATETIME DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_lead_tag_org_name` (`organization_id`,`name`),
  KEY `idx_lead_tag_org` (`organization_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `pipelines` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `organization_id` INT UNSIGNED NOT NULL,
  `name` VARCHAR(100) NOT NULL,
  `slug` VARCHAR(100) NOT NULL,
  `description` VARCHAR(255) DEFAULT NULL,
  `is_default` TINYINT(1) NOT NULL DEFAULT 0,
  `is_active` TINYINT(1) NOT NULL DEFAULT 1,
  `sort_order` INT NOT NULL DEFAULT 0,
  `created_at` DATETIME NOT NULL,
  `updated_at` DATETIME DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_pipeline_org_slug` (`organization_id`,`slug`),
  KEY `idx_pipeline_org` (`organization_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `deal_stages` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `organization_id` INT UNSIGNED NOT NULL,
  `pipeline_id` INT UNSIGNED NOT NULL,
  `name` VARCHAR(100) NOT NULL,
  `slug` VARCHAR(100) NOT NULL,
  `color` VARCHAR(20) NOT NULL DEFAULT '#0F766E',
  `probability` TINYINT UNSIGNED NOT NULL DEFAULT 0,
  `sort_order` INT NOT NULL DEFAULT 0,
  `is_won` TINYINT(1) NOT NULL DEFAULT 0,
  `is_lost` TINYINT(1) NOT NULL DEFAULT 0,
  `is_active` TINYINT(1) NOT NULL DEFAULT 1,
  `created_at` DATETIME NOT NULL,
  `updated_at` DATETIME DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_stage_org` (`organization_id`),
  KEY `idx_stage_pipeline` (`pipeline_id`),
  UNIQUE KEY `uq_stage_pipeline_slug` (`pipeline_id`,`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `task_priorities` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `organization_id` INT UNSIGNED NOT NULL,
  `name` VARCHAR(50) NOT NULL,
  `slug` VARCHAR(50) NOT NULL,
  `color` VARCHAR(20) NOT NULL DEFAULT '#64748B',
  `sort_order` INT NOT NULL DEFAULT 0,
  `is_active` TINYINT(1) NOT NULL DEFAULT 1,
  `created_at` DATETIME NOT NULL,
  `updated_at` DATETIME DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_priority_org_slug` (`organization_id`,`slug`),
  KEY `idx_priority_org` (`organization_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `custom_fields` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `organization_id` INT UNSIGNED NOT NULL,
  `module` VARCHAR(50) NOT NULL DEFAULT 'leads',
  `name` VARCHAR(100) NOT NULL,
  `slug` VARCHAR(100) NOT NULL,
  `field_type` VARCHAR(30) NOT NULL DEFAULT 'text',
  `options` TEXT NULL,
  `is_required` TINYINT(1) NOT NULL DEFAULT 0,
  `sort_order` INT NOT NULL DEFAULT 0,
  `is_active` TINYINT(1) NOT NULL DEFAULT 1,
  `created_at` DATETIME NOT NULL,
  `updated_at` DATETIME DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_cf_org_module_slug` (`organization_id`,`module`,`slug`),
  KEY `idx_cf_org` (`organization_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `import_mappings` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `organization_id` INT UNSIGNED NOT NULL,
  `module` VARCHAR(50) NOT NULL DEFAULT 'leads',
  `name` VARCHAR(100) NOT NULL,
  `mapping_json` TEXT NOT NULL,
  `created_by` INT UNSIGNED DEFAULT NULL,
  `created_at` DATETIME NOT NULL,
  `updated_at` DATETIME DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_import_map_org` (`organization_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- Leads core
-- ============================================================

CREATE TABLE IF NOT EXISTS `leads` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `organization_id` INT UNSIGNED NOT NULL,
  `lead_type` VARCHAR(50) NOT NULL DEFAULT 'clinic',
  `title` VARCHAR(200) NOT NULL,
  `first_name` VARCHAR(100) DEFAULT NULL,
  `last_name` VARCHAR(100) DEFAULT NULL,
  `company_name` VARCHAR(150) DEFAULT NULL,
  `email` VARCHAR(150) DEFAULT NULL,
  `phone` VARCHAR(50) DEFAULT NULL,
  `mobile` VARCHAR(50) DEFAULT NULL,
  `website` VARCHAR(255) DEFAULT NULL,
  `address` VARCHAR(255) DEFAULT NULL,
  `city` VARCHAR(100) DEFAULT NULL,
  `state` VARCHAR(100) DEFAULT NULL,
  `country` VARCHAR(100) DEFAULT NULL,
  `postal_code` VARCHAR(30) DEFAULT NULL,
  `lead_source_id` INT UNSIGNED DEFAULT NULL,
  `lead_status_id` INT UNSIGNED DEFAULT NULL,
  `pipeline_id` INT UNSIGNED DEFAULT NULL,
  `stage_id` INT UNSIGNED DEFAULT NULL,
  `assigned_to` INT UNSIGNED DEFAULT NULL,
  `priority_id` INT UNSIGNED DEFAULT NULL,
  `estimated_value` DECIMAL(15,2) DEFAULT NULL,
  `expected_close_date` DATE DEFAULT NULL,
  `description` TEXT NULL,
  `branch` VARCHAR(150) DEFAULT NULL,
  `treatment` VARCHAR(150) DEFAULT NULL,
  `course` VARCHAR(150) DEFAULT NULL,
  `preferred_batch` VARCHAR(100) DEFAULT NULL,
  `appointment_date` DATE DEFAULT NULL,
  `appointment_time` TIME DEFAULT NULL,
  `created_by` INT UNSIGNED DEFAULT NULL,
  `updated_by` INT UNSIGNED DEFAULT NULL,
  `deleted_at` DATETIME DEFAULT NULL,
  `deleted_by` INT UNSIGNED DEFAULT NULL,
  `created_at` DATETIME NOT NULL,
  `updated_at` DATETIME DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_leads_org` (`organization_id`),
  KEY `idx_leads_type` (`organization_id`,`lead_type`),
  KEY `idx_leads_status` (`lead_status_id`),
  KEY `idx_leads_source` (`lead_source_id`),
  KEY `idx_leads_pipeline` (`pipeline_id`),
  KEY `idx_leads_stage` (`stage_id`),
  KEY `idx_leads_assigned` (`assigned_to`),
  KEY `idx_leads_email` (`organization_id`,`email`),
  KEY `idx_leads_phone` (`organization_id`,`phone`),
  KEY `idx_leads_mobile` (`organization_id`,`mobile`),
  KEY `idx_leads_deleted` (`organization_id`,`deleted_at`),
  KEY `idx_leads_created` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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

CREATE TABLE IF NOT EXISTS `lead_tag_map` (
  `lead_id` INT UNSIGNED NOT NULL,
  `tag_id` INT UNSIGNED NOT NULL,
  PRIMARY KEY (`lead_id`,`tag_id`),
  KEY `idx_ltm_tag` (`tag_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `lead_notes` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `organization_id` INT UNSIGNED NOT NULL,
  `lead_id` INT UNSIGNED NOT NULL,
  `user_id` INT UNSIGNED NOT NULL,
  `body` MEDIUMTEXT NOT NULL,
  `is_pinned` TINYINT(1) NOT NULL DEFAULT 0,
  `created_at` DATETIME NOT NULL,
  `updated_at` DATETIME DEFAULT NULL,
  `deleted_at` DATETIME DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_lead_notes_lead` (`lead_id`),
  KEY `idx_lead_notes_org` (`organization_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `lead_attachments` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `organization_id` INT UNSIGNED NOT NULL,
  `lead_id` INT UNSIGNED NOT NULL,
  `user_id` INT UNSIGNED NOT NULL,
  `original_name` VARCHAR(255) NOT NULL,
  `file_path` VARCHAR(255) NOT NULL,
  `file_type` VARCHAR(100) DEFAULT NULL,
  `file_size` INT UNSIGNED DEFAULT 0,
  `created_at` DATETIME NOT NULL,
  `deleted_at` DATETIME DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_lead_att_lead` (`lead_id`),
  KEY `idx_lead_att_org` (`organization_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `lead_timeline` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `organization_id` INT UNSIGNED NOT NULL,
  `lead_id` INT UNSIGNED NOT NULL,
  `user_id` INT UNSIGNED DEFAULT NULL,
  `event_type` VARCHAR(50) NOT NULL,
  `title` VARCHAR(200) NOT NULL,
  `description` VARCHAR(500) DEFAULT NULL,
  `meta` TEXT NULL,
  `created_at` DATETIME NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_lead_tl_lead` (`lead_id`),
  KEY `idx_lead_tl_org` (`organization_id`),
  KEY `idx_lead_tl_created` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `lead_custom_values` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `organization_id` INT UNSIGNED NOT NULL,
  `lead_id` INT UNSIGNED NOT NULL,
  `custom_field_id` INT UNSIGNED NOT NULL,
  `value` TEXT NULL,
  `created_at` DATETIME NOT NULL,
  `updated_at` DATETIME DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_lcv` (`lead_id`,`custom_field_id`),
  KEY `idx_lcv_org` (`organization_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `lead_saved_filters` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `organization_id` INT UNSIGNED NOT NULL,
  `user_id` INT UNSIGNED NOT NULL,
  `name` VARCHAR(100) NOT NULL,
  `filters_json` TEXT NOT NULL,
  `is_shared` TINYINT(1) NOT NULL DEFAULT 0,
  `created_at` DATETIME NOT NULL,
  `updated_at` DATETIME DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_lsf_user` (`user_id`),
  KEY `idx_lsf_org` (`organization_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- Seed defaults per organization
-- ============================================================

DROP PROCEDURE IF EXISTS seed_lead_master_for_org;
DELIMITER $$
CREATE PROCEDURE seed_lead_master_for_org(IN p_org INT)
BEGIN
  IF (SELECT COUNT(*) FROM lead_statuses WHERE organization_id = p_org) = 0 THEN
    INSERT INTO lead_statuses (organization_id,name,slug,color,icon,sort_order,is_won,is_lost,is_default,is_active,created_at) VALUES
    (p_org,'New','new','#0284C7','fa-sparkles',1,0,0,1,1,NOW()),
    (p_org,'Contacted','contacted','#0F766E','fa-phone',2,0,0,0,1,NOW()),
    (p_org,'Qualified','qualified','#059669','fa-star',3,0,0,0,1,NOW()),
    (p_org,'Proposal Sent','proposal_sent','#D97706','fa-file-lines',4,0,0,0,1,NOW()),
    (p_org,'Negotiation','negotiation','#7C3AED','fa-handshake',5,0,0,0,1,NOW()),
    (p_org,'Won','won','#16A34A','fa-trophy',6,1,0,0,1,NOW()),
    (p_org,'Lost','lost','#DC2626','fa-xmark',7,0,1,0,1,NOW());
  END IF;

  IF (SELECT COUNT(*) FROM lead_sources WHERE organization_id = p_org) = 0 THEN
    INSERT INTO lead_sources (organization_id,name,slug,color,icon,sort_order,is_active,created_at) VALUES
    (p_org,'Website','website','#0284C7','fa-globe',1,1,NOW()),
    (p_org,'Facebook','facebook','#1877F2','fa-facebook',2,1,NOW()),
    (p_org,'Instagram','instagram','#E1306C','fa-instagram',3,1,NOW()),
    (p_org,'Google Ads','google_ads','#EA4335','fa-google',4,1,NOW()),
    (p_org,'WhatsApp','whatsapp','#25D366','fa-whatsapp',5,1,NOW()),
    (p_org,'Referral','referral','#0F766E','fa-user-group',6,1,NOW()),
    (p_org,'Manual','manual','#64748B','fa-pen',7,1,NOW()),
    (p_org,'Import','import','#D97706','fa-file-import',8,1,NOW()),
    (p_org,'Custom','custom','#7C3AED','fa-sliders',9,1,NOW());
  END IF;

  IF (SELECT COUNT(*) FROM task_priorities WHERE organization_id = p_org) = 0 THEN
    INSERT INTO task_priorities (organization_id,name,slug,color,sort_order,is_active,created_at) VALUES
    (p_org,'Low','low','#64748B',1,1,NOW()),
    (p_org,'Medium','medium','#0284C7',2,1,NOW()),
    (p_org,'High','high','#D97706',3,1,NOW()),
    (p_org,'Urgent','urgent','#DC2626',4,1,NOW());
  END IF;

  IF (SELECT COUNT(*) FROM pipelines WHERE organization_id = p_org) = 0 THEN
    INSERT INTO pipelines (organization_id,name,slug,description,is_default,is_active,sort_order,created_at) VALUES
    (p_org,'Sales','sales','Default sales pipeline',1,1,1,NOW()),
    (p_org,'Support','support','Support pipeline',0,1,2,NOW()),
    (p_org,'Marketing','marketing','Marketing pipeline',0,1,3,NOW());

    SET @pipe := (SELECT id FROM pipelines WHERE organization_id = p_org AND slug = 'sales' LIMIT 1);
    INSERT INTO deal_stages (organization_id,pipeline_id,name,slug,color,probability,sort_order,is_won,is_lost,is_active,created_at) VALUES
    (p_org,@pipe,'New','new','#0284C7',10,1,0,0,1,NOW()),
    (p_org,@pipe,'Contacted','contacted','#0F766E',25,2,0,0,1,NOW()),
    (p_org,@pipe,'Qualified','qualified','#059669',40,3,0,0,1,NOW()),
    (p_org,@pipe,'Proposal','proposal','#D97706',60,4,0,0,1,NOW()),
    (p_org,@pipe,'Negotiation','negotiation','#7C3AED',75,5,0,0,1,NOW()),
    (p_org,@pipe,'Won','won','#16A34A',100,6,1,0,1,NOW()),
    (p_org,@pipe,'Lost','lost','#DC2626',0,7,0,1,1,NOW());
  END IF;

  IF (SELECT COUNT(*) FROM lead_tags WHERE organization_id = p_org) = 0 THEN
    INSERT INTO lead_tags (organization_id,name,color,description,is_active,created_at) VALUES
    (p_org,'Hot','#DC2626','High interest',1,NOW()),
    (p_org,'Warm','#D97706','Engaged',1,NOW()),
    (p_org,'Cold','#0284C7','Needs nurturing',1,NOW()),
    (p_org,'VIP','#7C3AED','Priority account',1,NOW());
  END IF;
END$$
DELIMITER ;

CALL seed_lead_master_for_org(1);
CALL seed_lead_master_for_org(2);
DROP PROCEDURE IF EXISTS seed_lead_master_for_org;

-- Sample import mapping structure
INSERT INTO import_mappings (organization_id, module, name, mapping_json, created_by, created_at)
SELECT 1, 'leads', 'Default Lead CSV', '{"title":"Title","first_name":"First Name","last_name":"Last Name","company_name":"Company","email":"Email","phone":"Phone","mobile":"Mobile","website":"Website","city":"City","status":"Status","source":"Source"}', 1, NOW()
FROM DUAL WHERE NOT EXISTS (
  SELECT 1 FROM import_mappings WHERE organization_id = 1 AND name = 'Default Lead CSV'
);
