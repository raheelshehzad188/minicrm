-- Mino CRM — Authentication & Multi-Tenant Foundation
-- Database: minicrm

CREATE DATABASE IF NOT EXISTS `minicrm` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `minicrm`;

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

DROP TABLE IF EXISTS `remember_tokens`;
DROP TABLE IF EXISTS `password_resets`;
DROP TABLE IF EXISTS `login_attempts`;
DROP TABLE IF EXISTS `role_permissions`;
DROP TABLE IF EXISTS `permissions`;
DROP TABLE IF EXISTS `users`;
DROP TABLE IF EXISTS `roles`;
DROP TABLE IF EXISTS `organizations`;

SET FOREIGN_KEY_CHECKS = 1;

CREATE TABLE `organizations` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(150) NOT NULL,
  `slug` VARCHAR(150) NOT NULL,
  `logo` VARCHAR(255) DEFAULT NULL,
  `email` VARCHAR(150) DEFAULT NULL,
  `phone` VARCHAR(50) DEFAULT NULL,
  `country` VARCHAR(100) DEFAULT NULL,
  `timezone` VARCHAR(64) NOT NULL DEFAULT 'UTC',
  `currency` VARCHAR(10) NOT NULL DEFAULT 'USD',
  `status` ENUM('active','inactive','suspended') NOT NULL DEFAULT 'active',
  `created_at` DATETIME NOT NULL,
  `updated_at` DATETIME DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_organizations_slug` (`slug`),
  KEY `idx_organizations_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `roles` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(100) NOT NULL,
  `slug` VARCHAR(100) NOT NULL,
  `description` VARCHAR(255) DEFAULT NULL,
  `is_system` TINYINT(1) NOT NULL DEFAULT 1,
  `created_at` DATETIME NOT NULL,
  `updated_at` DATETIME DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_roles_slug` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `permissions` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(100) NOT NULL,
  `slug` VARCHAR(100) NOT NULL,
  `module` VARCHAR(100) NOT NULL DEFAULT 'general',
  `description` VARCHAR(255) DEFAULT NULL,
  `created_at` DATETIME NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_permissions_slug` (`slug`),
  KEY `idx_permissions_module` (`module`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `role_permissions` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `role_id` INT UNSIGNED NOT NULL,
  `permission_id` INT UNSIGNED NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_role_permission` (`role_id`,`permission_id`),
  CONSTRAINT `fk_rp_role` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_rp_permission` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `users` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `organization_id` INT UNSIGNED NOT NULL,
  `role_id` INT UNSIGNED NOT NULL,
  `name` VARCHAR(150) NOT NULL,
  `email` VARCHAR(150) NOT NULL,
  `password` VARCHAR(255) NOT NULL,
  `phone` VARCHAR(50) DEFAULT NULL,
  `profile_image` VARCHAR(255) DEFAULT NULL,
  `status` ENUM('active','inactive','suspended') NOT NULL DEFAULT 'active',
  `last_login` DATETIME DEFAULT NULL,
  `created_at` DATETIME NOT NULL,
  `updated_at` DATETIME DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_users_email` (`email`),
  KEY `idx_users_org` (`organization_id`),
  KEY `idx_users_role` (`role_id`),
  KEY `idx_users_status` (`status`),
  CONSTRAINT `fk_users_org` FOREIGN KEY (`organization_id`) REFERENCES `organizations` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_users_role` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `login_attempts` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `email` VARCHAR(150) NOT NULL,
  `ip_address` VARCHAR(45) NOT NULL,
  `attempted_at` DATETIME NOT NULL,
  `success` TINYINT(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `idx_login_attempts_email_ip` (`email`,`ip_address`),
  KEY `idx_login_attempts_time` (`attempted_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `password_resets` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `email` VARCHAR(150) NOT NULL,
  `token` VARCHAR(100) NOT NULL,
  `expires_at` DATETIME NOT NULL,
  `used_at` DATETIME DEFAULT NULL,
  `created_at` DATETIME NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_password_resets_token` (`token`),
  KEY `idx_password_resets_email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `remember_tokens` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` INT UNSIGNED NOT NULL,
  `selector` VARCHAR(32) NOT NULL,
  `token_hash` VARCHAR(255) NOT NULL,
  `expires_at` DATETIME NOT NULL,
  `created_at` DATETIME NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_remember_selector` (`selector`),
  KEY `idx_remember_user` (`user_id`),
  CONSTRAINT `fk_remember_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Roles
INSERT INTO `roles` (`id`,`name`,`slug`,`description`,`is_system`,`created_at`) VALUES
(1,'Owner','owner','Full access to organization',1,NOW()),
(2,'Admin','admin','Administrative access',1,NOW()),
(3,'Manager','manager','Manage team and pipeline',1,NOW()),
(4,'Sales Person','sales_person','Sales activities access',1,NOW());

-- Permissions
INSERT INTO `permissions` (`id`,`name`,`slug`,`module`,`description`,`created_at`) VALUES
(1,'View Dashboard','dashboard.view','dashboard','Access dashboard',NOW()),
(2,'Manage Users','users.manage','users','Create/update/delete users',NOW()),
(3,'View Users','users.view','users','View users',NOW()),
(4,'Manage Roles','roles.manage','roles','Manage roles & permissions',NOW()),
(5,'Manage Organization','organization.manage','organization','Update organization settings',NOW()),
(6,'View Organization','organization.view','organization','View organization profile',NOW()),
(7,'Manage Profile','profile.manage','profile','Update own profile',NOW()),
(8,'Change Password','profile.password','profile','Change own password',NOW());

-- Role → Permission mapping
-- Owner: all
INSERT INTO `role_permissions` (`role_id`,`permission_id`)
SELECT 1, id FROM permissions;

-- Admin: all except roles.manage
INSERT INTO `role_permissions` (`role_id`,`permission_id`)
SELECT 2, id FROM permissions WHERE slug != 'roles.manage';

-- Manager
INSERT INTO `role_permissions` (`role_id`,`permission_id`)
SELECT 3, id FROM permissions WHERE slug IN (
  'dashboard.view','users.view','organization.view','profile.manage','profile.password'
);

-- Sales Person
INSERT INTO `role_permissions` (`role_id`,`permission_id`)
SELECT 4, id FROM permissions WHERE slug IN (
  'dashboard.view','profile.manage','profile.password'
);

-- Organizations
INSERT INTO `organizations` (`id`,`name`,`slug`,`email`,`phone`,`country`,`timezone`,`currency`,`status`,`created_at`) VALUES
(1,'Acme Corporation','acme','hello@acme.test','+1 555 0100','United States','America/New_York','USD','active',NOW()),
(2,'Beta Industries','beta','hello@beta.test','+1 555 0200','Canada','America/Toronto','CAD','active',NOW());

-- Users (password for all: Password123!)
INSERT INTO `users` (`organization_id`,`role_id`,`name`,`email`,`password`,`phone`,`status`,`created_at`) VALUES
(1,1,'Acme Owner','owner@acme.com','$2y$12$PezgLCx7eirPcuBHtPGhNeRePtGQBQxHtatOV9CJKEmXzGobVyStu','+1 555 0101','active',NOW()),
(1,2,'Acme Admin','admin@acme.com','$2y$12$PezgLCx7eirPcuBHtPGhNeRePtGQBQxHtatOV9CJKEmXzGobVyStu','+1 555 0102','active',NOW()),
(1,3,'Acme Manager','manager@acme.com','$2y$12$PezgLCx7eirPcuBHtPGhNeRePtGQBQxHtatOV9CJKEmXzGobVyStu','+1 555 0103','active',NOW()),
(1,4,'Acme Sales','sales@acme.com','$2y$12$PezgLCx7eirPcuBHtPGhNeRePtGQBQxHtatOV9CJKEmXzGobVyStu','+1 555 0104','active',NOW()),
(2,1,'Beta Owner','owner@beta.com','$2y$12$PezgLCx7eirPcuBHtPGhNeRePtGQBQxHtatOV9CJKEmXzGobVyStu','+1 555 0201','active',NOW());
