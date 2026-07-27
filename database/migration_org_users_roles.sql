-- Mino CRM — Organization / Users / Roles / Activity / Notifications
USE `minicrm`;

-- Organization extra fields
ALTER TABLE `organizations`
  ADD COLUMN `address` TEXT NULL AFTER `phone`,
  ADD COLUMN `website` VARCHAR(255) NULL AFTER `currency`,
  ADD COLUMN `registration_number` VARCHAR(100) NULL AFTER `website`,
  ADD COLUMN `tax_number` VARCHAR(100) NULL AFTER `registration_number`;

-- Soft delete on users
ALTER TABLE `users`
  ADD COLUMN `deleted_at` DATETIME NULL DEFAULT NULL AFTER `updated_at`,
  ADD KEY `idx_users_deleted` (`deleted_at`);

-- Permission modules registry (future modules register here)
CREATE TABLE IF NOT EXISTS `permission_modules` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(100) NOT NULL,
  `slug` VARCHAR(100) NOT NULL,
  `description` VARCHAR(255) DEFAULT NULL,
  `sort_order` INT NOT NULL DEFAULT 0,
  `is_active` TINYINT(1) NOT NULL DEFAULT 1,
  `created_at` DATETIME NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_permission_modules_slug` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Activity logs
CREATE TABLE IF NOT EXISTS `activity_logs` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `organization_id` INT UNSIGNED NULL,
  `user_id` INT UNSIGNED NULL,
  `action` VARCHAR(50) NOT NULL,
  `module` VARCHAR(100) DEFAULT NULL,
  `record_id` INT UNSIGNED NULL,
  `description` VARCHAR(500) NOT NULL,
  `ip_address` VARCHAR(45) DEFAULT NULL,
  `user_agent` VARCHAR(255) DEFAULT NULL,
  `meta` TEXT NULL,
  `created_at` DATETIME NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_activity_org` (`organization_id`),
  KEY `idx_activity_user` (`user_id`),
  KEY `idx_activity_action` (`action`),
  KEY `idx_activity_created` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Notifications
CREATE TABLE IF NOT EXISTS `notifications` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `organization_id` INT UNSIGNED NOT NULL,
  `user_id` INT UNSIGNED NOT NULL,
  `title` VARCHAR(200) NOT NULL,
  `message` VARCHAR(500) NOT NULL,
  `type` VARCHAR(50) NOT NULL DEFAULT 'info',
  `link` VARCHAR(255) DEFAULT NULL,
  `is_read` TINYINT(1) NOT NULL DEFAULT 0,
  `created_at` DATETIME NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_notif_user` (`user_id`,`is_read`),
  KEY `idx_notif_org` (`organization_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Seed permission modules
INSERT INTO `permission_modules` (`name`,`slug`,`description`,`sort_order`,`is_active`,`created_at`) VALUES
('Dashboard','dashboard','Dashboard access',1,1,NOW()),
('Users','users','User management',2,1,NOW()),
('Roles','roles','Roles & permissions',3,1,NOW()),
('Organization','organization','Organization settings',4,1,NOW()),
('Settings','settings','General settings',5,1,NOW()),
('Leads','leads','Lead management (future)',10,1,NOW()),
('Contacts','contacts','Contact management (future)',11,1,NOW()),
('Deals','deals','Deal pipeline (future)',12,1,NOW()),
('Tasks','tasks','Task management (future)',13,1,NOW()),
('Reports','reports','Reports & analytics (future)',14,1,NOW())
ON DUPLICATE KEY UPDATE `name`=VALUES(`name`);

-- Expand permissions to View/Create/Edit/Delete/Export/Import matrix
-- Keep existing IDs where possible; add new ones
DELETE FROM `role_permissions`;
DELETE FROM `permissions`;

INSERT INTO `permissions` (`id`,`name`,`slug`,`module`,`description`,`created_at`) VALUES
-- Dashboard
(1,'View Dashboard','dashboard.view','dashboard','Access dashboard',NOW()),
-- Users
(10,'View Users','users.view','users','View users list',NOW()),
(11,'Create Users','users.create','users','Create users',NOW()),
(12,'Edit Users','users.edit','users','Edit users',NOW()),
(13,'Delete Users','users.delete','users','Delete users',NOW()),
(14,'Export Users','users.export','users','Export users',NOW()),
(15,'Import Users','users.import','users','Import users',NOW()),
-- Roles
(20,'View Roles','roles.view','roles','View roles',NOW()),
(21,'Create Roles','roles.create','roles','Create roles',NOW()),
(22,'Edit Roles','roles.edit','roles','Edit roles & permissions',NOW()),
(23,'Delete Roles','roles.delete','roles','Delete roles',NOW()),
(24,'Export Roles','roles.export','roles','Export roles',NOW()),
(25,'Import Roles','roles.import','roles','Import roles',NOW()),
-- Organization
(30,'View Organization','organization.view','organization','View organization',NOW()),
(31,'Create Organization','organization.create','organization','Create organization',NOW()),
(32,'Edit Organization','organization.edit','organization','Edit organization settings',NOW()),
(33,'Delete Organization','organization.delete','organization','Delete organization',NOW()),
(34,'Export Organization','organization.export','organization','Export organization',NOW()),
(35,'Import Organization','organization.import','organization','Import organization',NOW()),
-- Settings
(40,'View Settings','settings.view','settings','View settings',NOW()),
(41,'Create Settings','settings.create','settings','Create settings',NOW()),
(42,'Edit Settings','settings.edit','settings','Edit settings',NOW()),
(43,'Delete Settings','settings.delete','settings','Delete settings',NOW()),
(44,'Export Settings','settings.export','settings','Export settings',NOW()),
(45,'Import Settings','settings.import','settings','Import settings',NOW()),
-- Profile (personal)
(50,'Manage Profile','profile.manage','profile','Update own profile',NOW()),
(51,'Change Password','profile.password','profile','Change own password',NOW()),
-- Future: Leads
(60,'View Leads','leads.view','leads','View leads',NOW()),
(61,'Create Leads','leads.create','leads','Create leads',NOW()),
(62,'Edit Leads','leads.edit','leads','Edit leads',NOW()),
(63,'Delete Leads','leads.delete','leads','Delete leads',NOW()),
(64,'Export Leads','leads.export','leads','Export leads',NOW()),
(65,'Import Leads','leads.import','leads','Import leads',NOW()),
-- Future: Contacts
(70,'View Contacts','contacts.view','contacts','View contacts',NOW()),
(71,'Create Contacts','contacts.create','contacts','Create contacts',NOW()),
(72,'Edit Contacts','contacts.edit','contacts','Edit contacts',NOW()),
(73,'Delete Contacts','contacts.delete','contacts','Delete contacts',NOW()),
(74,'Export Contacts','contacts.export','contacts','Export contacts',NOW()),
(75,'Import Contacts','contacts.import','contacts','Import contacts',NOW()),
-- Future: Deals
(80,'View Deals','deals.view','deals','View deals',NOW()),
(81,'Create Deals','deals.create','deals','Create deals',NOW()),
(82,'Edit Deals','deals.edit','deals','Edit deals',NOW()),
(83,'Delete Deals','deals.delete','deals','Delete deals',NOW()),
(84,'Export Deals','deals.export','deals','Export deals',NOW()),
(85,'Import Deals','deals.import','deals','Import deals',NOW()),
-- Future: Tasks
(90,'View Tasks','tasks.view','tasks','View tasks',NOW()),
(91,'Create Tasks','tasks.create','tasks','Create tasks',NOW()),
(92,'Edit Tasks','tasks.edit','tasks','Edit tasks',NOW()),
(93,'Delete Tasks','tasks.delete','tasks','Delete tasks',NOW()),
(94,'Export Tasks','tasks.export','tasks','Export tasks',NOW()),
(95,'Import Tasks','tasks.import','tasks','Import tasks',NOW()),
-- Future: Reports
(100,'View Reports','reports.view','reports','View reports',NOW()),
(101,'Create Reports','reports.create','reports','Create reports',NOW()),
(102,'Edit Reports','reports.edit','reports','Edit reports',NOW()),
(103,'Delete Reports','reports.delete','reports','Delete reports',NOW()),
(104,'Export Reports','reports.export','reports','Export reports',NOW()),
(105,'Import Reports','reports.import','reports','Import reports',NOW());

-- Owner: ALL permissions
INSERT INTO `role_permissions` (`role_id`,`permission_id`)
SELECT 1, id FROM permissions;

-- Admin: everything except roles.create/delete and organization.delete
INSERT INTO `role_permissions` (`role_id`,`permission_id`)
SELECT 2, id FROM permissions
WHERE slug NOT IN ('roles.create','roles.delete','organization.delete','organization.create');

-- Manager: view-focused + profile + limited leads/contacts/deals/tasks (future ready)
INSERT INTO `role_permissions` (`role_id`,`permission_id`)
SELECT 3, id FROM permissions WHERE slug IN (
  'dashboard.view',
  'users.view',
  'organization.view',
  'settings.view',
  'profile.manage','profile.password',
  'leads.view','leads.create','leads.edit',
  'contacts.view','contacts.create','contacts.edit',
  'deals.view','deals.create','deals.edit',
  'tasks.view','tasks.create','tasks.edit',
  'reports.view'
);

-- Sales Person: assigned-records ready (view/create/edit own modules later)
INSERT INTO `role_permissions` (`role_id`,`permission_id`)
SELECT 4, id FROM permissions WHERE slug IN (
  'dashboard.view',
  'profile.manage','profile.password',
  'leads.view','leads.create','leads.edit',
  'contacts.view','contacts.create',
  'deals.view','deals.create','deals.edit',
  'tasks.view','tasks.create','tasks.edit'
);

-- Fix AUTO_INCREMENT
ALTER TABLE `permissions` AUTO_INCREMENT = 200;
