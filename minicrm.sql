-- MariaDB dump 10.19  Distrib 10.4.28-MariaDB, for osx10.10 (x86_64)
--
-- Host: localhost    Database: minicrm
-- ------------------------------------------------------
-- Server version	10.4.28-MariaDB

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `activity_logs`
--

DROP TABLE IF EXISTS `activity_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `activity_logs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `organization_id` int(10) unsigned DEFAULT NULL,
  `user_id` int(10) unsigned DEFAULT NULL,
  `action` varchar(50) NOT NULL,
  `module` varchar(100) DEFAULT NULL,
  `record_id` int(10) unsigned DEFAULT NULL,
  `description` varchar(500) NOT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` varchar(255) DEFAULT NULL,
  `meta` text DEFAULT NULL,
  `created_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_activity_org` (`organization_id`),
  KEY `idx_activity_user` (`user_id`),
  KEY `idx_activity_action` (`action`),
  KEY `idx_activity_created` (`created_at`)
) ENGINE=InnoDB AUTO_INCREMENT=26 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `activity_logs`
--

LOCK TABLES `activity_logs` WRITE;
/*!40000 ALTER TABLE `activity_logs` DISABLE KEYS */;
INSERT INTO `activity_logs` VALUES (1,1,1,'login','auth',1,'User logged in: owner@acme.com','::1','curl/8.7.1',NULL,'2026-07-15 11:06:25'),(2,1,4,'logout','auth',4,'User logged out','::1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36',NULL,'2026-07-15 11:07:03'),(3,1,4,'login','auth',4,'User logged in: sales@acme.com','::1','curl/8.7.1',NULL,'2026-07-15 11:07:04'),(4,1,1,'login','auth',1,'User logged in: owner@acme.com','::1','curl/8.7.1',NULL,'2026-07-15 11:07:05'),(5,1,1,'create','users',6,'Created user test.user@acme.com','::1','curl/8.7.1',NULL,'2026-07-15 11:07:05'),(6,1,1,'update','organization',1,'Updated organization settings','::1','curl/8.7.1',NULL,'2026-07-15 11:07:06'),(7,1,1,'login','auth',1,'User logged in: owner@acme.com','::1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36',NULL,'2026-07-15 11:07:12'),(8,1,1,'login','auth',1,'User logged in: owner@acme.com','::1','curl/8.7.1',NULL,'2026-07-15 11:28:34'),(9,1,4,'login','auth',4,'User logged in: sales@acme.com','::1','curl/8.7.1',NULL,'2026-07-15 11:29:11'),(10,1,4,'login','auth',4,'User logged in: sales@acme.com','::1','curl/8.7.1',NULL,'2026-07-15 11:34:16'),(11,1,1,'login','auth',1,'User logged in: owner@acme.com','::1','curl/8.7.1',NULL,'2026-07-15 11:34:17'),(12,1,1,'login','auth',1,'User logged in: owner@acme.com','::1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36',NULL,'2026-07-15 15:45:38'),(13,1,1,'logout','auth',1,'User logged out','::1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36',NULL,'2026-07-15 15:45:53'),(14,1,2,'login','auth',2,'User logged in: admin@acme.com','::1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36',NULL,'2026-07-15 15:45:56'),(15,1,1,'login','auth',1,'User logged in: owner@acme.com','::1','curl/8.7.1',NULL,'2026-07-15 15:57:49'),(16,1,1,'create','leads',1,'Created lead BrightSoft Lead','::1','curl/8.7.1',NULL,'2026-07-15 15:57:49'),(17,1,4,'login','auth',4,'User logged in: sales@acme.com','::1','curl/8.7.1',NULL,'2026-07-15 15:57:50'),(18,1,1,'create','leads',2,'Created lead Unassigned Lead','::1','curl/8.7.1',NULL,'2026-07-15 15:58:32'),(19,2,5,'login','auth',5,'User logged in: owner@beta.com','::1','curl/8.7.1',NULL,'2026-07-15 15:58:32'),(20,1,1,'delete','leads',2,'Soft-deleted lead Unassigned Lead','::1','curl/8.7.1',NULL,'2026-07-15 15:58:46'),(21,1,1,'create','leads',3,'Created lead Nexus Labs','::1','curl/8.7.1',NULL,'2026-07-15 15:59:16'),(22,1,1,'create','leads',4,'Created lead Peak Retail','::1','curl/8.7.1',NULL,'2026-07-15 15:59:16'),(23,1,1,'login','auth',1,'User logged in: owner@acme.com','::1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36',NULL,'2026-07-25 18:31:46'),(24,1,1,'login','auth',1,'User logged in: owner@acme.com','::1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36',NULL,'2026-07-27 18:40:17'),(25,1,1,'export','leads',NULL,'Exported 3 leads','::1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36',NULL,'2026-07-27 18:40:41');
/*!40000 ALTER TABLE `activity_logs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `custom_fields`
--

DROP TABLE IF EXISTS `custom_fields`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `custom_fields` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `organization_id` int(10) unsigned NOT NULL,
  `module` varchar(50) NOT NULL DEFAULT 'leads',
  `name` varchar(100) NOT NULL,
  `slug` varchar(100) NOT NULL,
  `field_type` varchar(30) NOT NULL DEFAULT 'text',
  `options` text DEFAULT NULL,
  `is_required` tinyint(1) NOT NULL DEFAULT 0,
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` datetime NOT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_cf_org_module_slug` (`organization_id`,`module`,`slug`),
  KEY `idx_cf_org` (`organization_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `custom_fields`
--

LOCK TABLES `custom_fields` WRITE;
/*!40000 ALTER TABLE `custom_fields` DISABLE KEYS */;
/*!40000 ALTER TABLE `custom_fields` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `deal_stages`
--

DROP TABLE IF EXISTS `deal_stages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `deal_stages` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `organization_id` int(10) unsigned NOT NULL,
  `pipeline_id` int(10) unsigned NOT NULL,
  `name` varchar(100) NOT NULL,
  `slug` varchar(100) NOT NULL,
  `color` varchar(20) NOT NULL DEFAULT '#0F766E',
  `probability` tinyint(3) unsigned NOT NULL DEFAULT 0,
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `is_won` tinyint(1) NOT NULL DEFAULT 0,
  `is_lost` tinyint(1) NOT NULL DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` datetime NOT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_stage_pipeline_slug` (`pipeline_id`,`slug`),
  KEY `idx_stage_org` (`organization_id`),
  KEY `idx_stage_pipeline` (`pipeline_id`)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `deal_stages`
--

LOCK TABLES `deal_stages` WRITE;
/*!40000 ALTER TABLE `deal_stages` DISABLE KEYS */;
INSERT INTO `deal_stages` VALUES (1,1,1,'New','new','#0284C7',10,1,0,0,1,'2026-07-15 18:49:55',NULL),(2,1,1,'Contacted','contacted','#0F766E',25,2,0,0,1,'2026-07-15 18:49:55',NULL),(3,1,1,'Qualified','qualified','#059669',40,3,0,0,1,'2026-07-15 18:49:55',NULL),(4,1,1,'Proposal','proposal','#D97706',60,4,0,0,1,'2026-07-15 18:49:55',NULL),(5,1,1,'Negotiation','negotiation','#7C3AED',75,5,0,0,1,'2026-07-15 18:49:55',NULL),(6,1,1,'Won','won','#16A34A',100,6,1,0,1,'2026-07-15 18:49:55',NULL),(7,1,1,'Lost','lost','#DC2626',0,7,0,1,1,'2026-07-15 18:49:55',NULL),(8,2,4,'New','new','#0284C7',10,1,0,0,1,'2026-07-15 18:49:55',NULL),(9,2,4,'Contacted','contacted','#0F766E',25,2,0,0,1,'2026-07-15 18:49:55',NULL),(10,2,4,'Qualified','qualified','#059669',40,3,0,0,1,'2026-07-15 18:49:55',NULL),(11,2,4,'Proposal','proposal','#D97706',60,4,0,0,1,'2026-07-15 18:49:55',NULL),(12,2,4,'Negotiation','negotiation','#7C3AED',75,5,0,0,1,'2026-07-15 18:49:55',NULL),(13,2,4,'Won','won','#16A34A',100,6,1,0,1,'2026-07-15 18:49:55',NULL),(14,2,4,'Lost','lost','#DC2626',0,7,0,1,1,'2026-07-15 18:49:55',NULL);
/*!40000 ALTER TABLE `deal_stages` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `import_mappings`
--

DROP TABLE IF EXISTS `import_mappings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `import_mappings` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `organization_id` int(10) unsigned NOT NULL,
  `module` varchar(50) NOT NULL DEFAULT 'leads',
  `name` varchar(100) NOT NULL,
  `mapping_json` text NOT NULL,
  `created_by` int(10) unsigned DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_import_map_org` (`organization_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `import_mappings`
--

LOCK TABLES `import_mappings` WRITE;
/*!40000 ALTER TABLE `import_mappings` DISABLE KEYS */;
INSERT INTO `import_mappings` VALUES (1,1,'leads','Default Lead CSV','{\"title\":\"Title\",\"first_name\":\"First Name\",\"last_name\":\"Last Name\",\"company_name\":\"Company\",\"email\":\"Email\",\"phone\":\"Phone\",\"mobile\":\"Mobile\",\"website\":\"Website\",\"city\":\"City\",\"status\":\"Status\",\"source\":\"Source\"}',1,'2026-07-15 18:49:55',NULL);
/*!40000 ALTER TABLE `import_mappings` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `lead_attachments`
--

DROP TABLE IF EXISTS `lead_attachments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `lead_attachments` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `organization_id` int(10) unsigned NOT NULL,
  `lead_id` int(10) unsigned NOT NULL,
  `user_id` int(10) unsigned NOT NULL,
  `original_name` varchar(255) NOT NULL,
  `file_path` varchar(255) NOT NULL,
  `file_type` varchar(100) DEFAULT NULL,
  `file_size` int(10) unsigned DEFAULT 0,
  `created_at` datetime NOT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_lead_att_lead` (`lead_id`),
  KEY `idx_lead_att_org` (`organization_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `lead_attachments`
--

LOCK TABLES `lead_attachments` WRITE;
/*!40000 ALTER TABLE `lead_attachments` DISABLE KEYS */;
/*!40000 ALTER TABLE `lead_attachments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `lead_custom_values`
--

DROP TABLE IF EXISTS `lead_custom_values`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `lead_custom_values` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `organization_id` int(10) unsigned NOT NULL,
  `lead_id` int(10) unsigned NOT NULL,
  `custom_field_id` int(10) unsigned NOT NULL,
  `value` text DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_lcv` (`lead_id`,`custom_field_id`),
  KEY `idx_lcv_org` (`organization_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `lead_custom_values`
--

LOCK TABLES `lead_custom_values` WRITE;
/*!40000 ALTER TABLE `lead_custom_values` DISABLE KEYS */;
/*!40000 ALTER TABLE `lead_custom_values` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `lead_notes`
--

DROP TABLE IF EXISTS `lead_notes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `lead_notes` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `organization_id` int(10) unsigned NOT NULL,
  `lead_id` int(10) unsigned NOT NULL,
  `user_id` int(10) unsigned NOT NULL,
  `body` mediumtext NOT NULL,
  `is_pinned` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` datetime NOT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_lead_notes_lead` (`lead_id`),
  KEY `idx_lead_notes_org` (`organization_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `lead_notes`
--

LOCK TABLES `lead_notes` WRITE;
/*!40000 ALTER TABLE `lead_notes` DISABLE KEYS */;
INSERT INTO `lead_notes` VALUES (1,1,1,1,'<p>Hello <b>note</b></p>',1,'2026-07-15 15:58:33',NULL,NULL);
/*!40000 ALTER TABLE `lead_notes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `lead_saved_filters`
--

DROP TABLE IF EXISTS `lead_saved_filters`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `lead_saved_filters` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `organization_id` int(10) unsigned NOT NULL,
  `user_id` int(10) unsigned NOT NULL,
  `name` varchar(100) NOT NULL,
  `filters_json` text NOT NULL,
  `is_shared` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` datetime NOT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_lsf_user` (`user_id`),
  KEY `idx_lsf_org` (`organization_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `lead_saved_filters`
--

LOCK TABLES `lead_saved_filters` WRITE;
/*!40000 ALTER TABLE `lead_saved_filters` DISABLE KEYS */;
/*!40000 ALTER TABLE `lead_saved_filters` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `lead_sources`
--

DROP TABLE IF EXISTS `lead_sources`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `lead_sources` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `organization_id` int(10) unsigned NOT NULL,
  `name` varchar(100) NOT NULL,
  `slug` varchar(100) NOT NULL,
  `color` varchar(20) NOT NULL DEFAULT '#0284C7',
  `icon` varchar(50) DEFAULT 'fa-globe',
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` datetime NOT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_lead_source_org_slug` (`organization_id`,`slug`),
  KEY `idx_lead_source_org` (`organization_id`)
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `lead_sources`
--

LOCK TABLES `lead_sources` WRITE;
/*!40000 ALTER TABLE `lead_sources` DISABLE KEYS */;
INSERT INTO `lead_sources` VALUES (1,1,'Website','website','#0284C7','fa-globe',1,1,'2026-07-15 18:49:55',NULL),(2,1,'Facebook','facebook','#1877F2','fa-facebook',2,1,'2026-07-15 18:49:55',NULL),(3,1,'Instagram','instagram','#E1306C','fa-instagram',3,1,'2026-07-15 18:49:55',NULL),(4,1,'Google Ads','google_ads','#EA4335','fa-google',4,1,'2026-07-15 18:49:55',NULL),(5,1,'WhatsApp','whatsapp','#25D366','fa-whatsapp',5,1,'2026-07-15 18:49:55',NULL),(6,1,'Referral','referral','#0F766E','fa-user-group',6,1,'2026-07-15 18:49:55',NULL),(7,1,'Manual','manual','#64748B','fa-pen',7,1,'2026-07-15 18:49:55',NULL),(8,1,'Import','import','#D97706','fa-file-import',8,1,'2026-07-15 18:49:55',NULL),(9,1,'Custom','custom','#7C3AED','fa-sliders',9,1,'2026-07-15 18:49:55',NULL),(10,2,'Website','website','#0284C7','fa-globe',1,1,'2026-07-15 18:49:55',NULL),(11,2,'Facebook','facebook','#1877F2','fa-facebook',2,1,'2026-07-15 18:49:55',NULL),(12,2,'Instagram','instagram','#E1306C','fa-instagram',3,1,'2026-07-15 18:49:55',NULL),(13,2,'Google Ads','google_ads','#EA4335','fa-google',4,1,'2026-07-15 18:49:55',NULL),(14,2,'WhatsApp','whatsapp','#25D366','fa-whatsapp',5,1,'2026-07-15 18:49:55',NULL),(15,2,'Referral','referral','#0F766E','fa-user-group',6,1,'2026-07-15 18:49:55',NULL),(16,2,'Manual','manual','#64748B','fa-pen',7,1,'2026-07-15 18:49:55',NULL),(17,2,'Import','import','#D97706','fa-file-import',8,1,'2026-07-15 18:49:55',NULL),(18,2,'Custom','custom','#7C3AED','fa-sliders',9,1,'2026-07-15 18:49:55',NULL);
/*!40000 ALTER TABLE `lead_sources` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `lead_statuses`
--

DROP TABLE IF EXISTS `lead_statuses`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `lead_statuses` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `organization_id` int(10) unsigned NOT NULL,
  `name` varchar(100) NOT NULL,
  `slug` varchar(100) NOT NULL,
  `color` varchar(20) NOT NULL DEFAULT '#0F766E',
  `icon` varchar(50) DEFAULT 'fa-circle',
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `is_won` tinyint(1) NOT NULL DEFAULT 0,
  `is_lost` tinyint(1) NOT NULL DEFAULT 0,
  `is_default` tinyint(1) NOT NULL DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` datetime NOT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_lead_status_org_slug` (`organization_id`,`slug`),
  KEY `idx_lead_status_org` (`organization_id`)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `lead_statuses`
--

LOCK TABLES `lead_statuses` WRITE;
/*!40000 ALTER TABLE `lead_statuses` DISABLE KEYS */;
INSERT INTO `lead_statuses` VALUES (1,1,'New','new','#0284C7','fa-sparkles',1,0,0,1,1,'2026-07-15 18:49:55',NULL),(2,1,'Contacted','contacted','#0F766E','fa-phone',2,0,0,0,1,'2026-07-15 18:49:55',NULL),(3,1,'Qualified','qualified','#059669','fa-star',3,0,0,0,1,'2026-07-15 18:49:55',NULL),(4,1,'Proposal Sent','proposal_sent','#D97706','fa-file-lines',4,0,0,0,1,'2026-07-15 18:49:55',NULL),(5,1,'Negotiation','negotiation','#7C3AED','fa-handshake',5,0,0,0,1,'2026-07-15 18:49:55',NULL),(6,1,'Won','won','#16A34A','fa-trophy',6,1,0,0,1,'2026-07-15 18:49:55',NULL),(7,1,'Lost','lost','#DC2626','fa-xmark',7,0,1,0,1,'2026-07-15 18:49:55',NULL),(8,2,'New','new','#0284C7','fa-sparkles',1,0,0,1,1,'2026-07-15 18:49:55',NULL),(9,2,'Contacted','contacted','#0F766E','fa-phone',2,0,0,0,1,'2026-07-15 18:49:55',NULL),(10,2,'Qualified','qualified','#059669','fa-star',3,0,0,0,1,'2026-07-15 18:49:55',NULL),(11,2,'Proposal Sent','proposal_sent','#D97706','fa-file-lines',4,0,0,0,1,'2026-07-15 18:49:55',NULL),(12,2,'Negotiation','negotiation','#7C3AED','fa-handshake',5,0,0,0,1,'2026-07-15 18:49:55',NULL),(13,2,'Won','won','#16A34A','fa-trophy',6,1,0,0,1,'2026-07-15 18:49:55',NULL),(14,2,'Lost','lost','#DC2626','fa-xmark',7,0,1,0,1,'2026-07-15 18:49:55',NULL);
/*!40000 ALTER TABLE `lead_statuses` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `lead_tag_map`
--

DROP TABLE IF EXISTS `lead_tag_map`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `lead_tag_map` (
  `lead_id` int(10) unsigned NOT NULL,
  `tag_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`lead_id`,`tag_id`),
  KEY `idx_ltm_tag` (`tag_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `lead_tag_map`
--

LOCK TABLES `lead_tag_map` WRITE;
/*!40000 ALTER TABLE `lead_tag_map` DISABLE KEYS */;
/*!40000 ALTER TABLE `lead_tag_map` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `lead_tags`
--

DROP TABLE IF EXISTS `lead_tags`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `lead_tags` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `organization_id` int(10) unsigned NOT NULL,
  `name` varchar(100) NOT NULL,
  `color` varchar(20) NOT NULL DEFAULT '#64748B',
  `description` varchar(255) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` datetime NOT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_lead_tag_org_name` (`organization_id`,`name`),
  KEY `idx_lead_tag_org` (`organization_id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `lead_tags`
--

LOCK TABLES `lead_tags` WRITE;
/*!40000 ALTER TABLE `lead_tags` DISABLE KEYS */;
INSERT INTO `lead_tags` VALUES (1,1,'Hot','#DC2626','High interest',1,'2026-07-15 18:49:55',NULL),(2,1,'Warm','#D97706','Engaged',1,'2026-07-15 18:49:55',NULL),(3,1,'Cold','#0284C7','Needs nurturing',1,'2026-07-15 18:49:55',NULL),(4,1,'VIP','#7C3AED','Priority account',1,'2026-07-15 18:49:55',NULL),(5,2,'Hot','#DC2626','High interest',1,'2026-07-15 18:49:55',NULL),(6,2,'Warm','#D97706','Engaged',1,'2026-07-15 18:49:55',NULL),(7,2,'Cold','#0284C7','Needs nurturing',1,'2026-07-15 18:49:55',NULL),(8,2,'VIP','#7C3AED','Priority account',1,'2026-07-15 18:49:55',NULL);
/*!40000 ALTER TABLE `lead_tags` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `lead_timeline`
--

DROP TABLE IF EXISTS `lead_timeline`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `lead_timeline` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `organization_id` int(10) unsigned NOT NULL,
  `lead_id` int(10) unsigned NOT NULL,
  `user_id` int(10) unsigned DEFAULT NULL,
  `event_type` varchar(50) NOT NULL,
  `title` varchar(200) NOT NULL,
  `description` varchar(500) DEFAULT NULL,
  `meta` text DEFAULT NULL,
  `created_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_lead_tl_lead` (`lead_id`),
  KEY `idx_lead_tl_org` (`organization_id`),
  KEY `idx_lead_tl_created` (`created_at`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `lead_timeline`
--

LOCK TABLES `lead_timeline` WRITE;
/*!40000 ALTER TABLE `lead_timeline` DISABLE KEYS */;
INSERT INTO `lead_timeline` VALUES (1,1,1,1,'created','Lead Created','BrightSoft Lead was added',NULL,'2026-07-15 15:57:49'),(2,1,1,1,'assigned','Lead Assigned','Assigned to user #4',NULL,'2026-07-15 15:57:49'),(3,1,2,1,'created','Lead Created','Unassigned Lead was added',NULL,'2026-07-15 15:58:32'),(4,1,1,1,'note_added','Note Added','A note was added',NULL,'2026-07-15 15:58:33'),(5,1,2,1,'deleted','Lead Deleted','Moved to trash',NULL,'2026-07-15 15:58:46'),(6,1,3,1,'created','Lead Created','Nexus Labs was added',NULL,'2026-07-15 15:59:16'),(7,1,3,1,'assigned','Lead Assigned','Assigned to user #3',NULL,'2026-07-15 15:59:16'),(8,1,4,1,'created','Lead Created','Peak Retail was added',NULL,'2026-07-15 15:59:16'),(9,1,4,1,'assigned','Lead Assigned','Assigned to user #4',NULL,'2026-07-15 15:59:16');
/*!40000 ALTER TABLE `lead_timeline` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `leads`
--

DROP TABLE IF EXISTS `leads`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `leads` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `organization_id` int(10) unsigned NOT NULL,
  `title` varchar(200) NOT NULL,
  `first_name` varchar(100) DEFAULT NULL,
  `last_name` varchar(100) DEFAULT NULL,
  `company_name` varchar(150) DEFAULT NULL,
  `email` varchar(150) DEFAULT NULL,
  `phone` varchar(50) DEFAULT NULL,
  `mobile` varchar(50) DEFAULT NULL,
  `website` varchar(255) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `city` varchar(100) DEFAULT NULL,
  `state` varchar(100) DEFAULT NULL,
  `country` varchar(100) DEFAULT NULL,
  `postal_code` varchar(30) DEFAULT NULL,
  `lead_source_id` int(10) unsigned DEFAULT NULL,
  `lead_status_id` int(10) unsigned DEFAULT NULL,
  `pipeline_id` int(10) unsigned DEFAULT NULL,
  `stage_id` int(10) unsigned DEFAULT NULL,
  `assigned_to` int(10) unsigned DEFAULT NULL,
  `priority_id` int(10) unsigned DEFAULT NULL,
  `estimated_value` decimal(15,2) DEFAULT NULL,
  `expected_close_date` date DEFAULT NULL,
  `description` text DEFAULT NULL,
  `created_by` int(10) unsigned DEFAULT NULL,
  `updated_by` int(10) unsigned DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `deleted_by` int(10) unsigned DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_leads_org` (`organization_id`),
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
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `leads`
--

LOCK TABLES `leads` WRITE;
/*!40000 ALTER TABLE `leads` DISABLE KEYS */;
INSERT INTO `leads` VALUES (1,1,'BrightSoft Lead','Emma','Watson','BrightSoft','emma@brightsoft.io','5551001',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,1,1,4,NULL,NULL,NULL,NULL,1,1,NULL,NULL,'2026-07-15 15:57:49',NULL),(2,1,'Unassigned Lead',NULL,NULL,NULL,'unique@acme.test',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,1,1,NULL,NULL,NULL,NULL,NULL,1,1,'2026-07-15 15:58:46',1,'2026-07-15 15:58:32','2026-07-15 15:58:46'),(3,1,'Nexus Labs','James','Lee','Nexus Labs','james@nexus.dev','5552002',NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,1,1,1,3,NULL,NULL,NULL,NULL,1,1,NULL,NULL,'2026-07-15 15:59:16',NULL),(4,1,'Peak Retail','Olivia','Park','Peak Retail','olivia@peak.co','5553003',NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,3,1,1,4,NULL,NULL,NULL,NULL,1,1,NULL,NULL,'2026-07-15 15:59:16',NULL);
/*!40000 ALTER TABLE `leads` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `login_attempts`
--

DROP TABLE IF EXISTS `login_attempts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `login_attempts` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `email` varchar(150) NOT NULL,
  `ip_address` varchar(45) NOT NULL,
  `attempted_at` datetime NOT NULL,
  `success` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `idx_login_attempts_email_ip` (`email`,`ip_address`),
  KEY `idx_login_attempts_time` (`attempted_at`)
) ENGINE=InnoDB AUTO_INCREMENT=23 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `login_attempts`
--

LOCK TABLES `login_attempts` WRITE;
/*!40000 ALTER TABLE `login_attempts` DISABLE KEYS */;
INSERT INTO `login_attempts` VALUES (1,'owner@acme.com','::1','2026-07-15 10:21:36',1),(2,'owner@acme.com','::1','2026-07-15 10:22:18',1),(4,'owner@acme.com','::1','2026-07-15 10:42:26',1),(5,'owner@acme.com','::1','2026-07-15 10:44:00',1),(6,'admin@acme.com','::1','2026-07-15 10:47:29',1),(7,'sales@acme.com','::1','2026-07-15 10:47:47',1),(8,'owner@acme.com','::1','2026-07-15 11:06:25',1),(9,'sales@acme.com','::1','2026-07-15 11:07:04',1),(10,'owner@acme.com','::1','2026-07-15 11:07:05',1),(11,'owner@acme.com','::1','2026-07-15 11:07:12',1),(12,'owner@acme.com','::1','2026-07-15 11:28:34',1),(13,'sales@acme.com','::1','2026-07-15 11:29:11',1),(14,'sales@acme.com','::1','2026-07-15 11:34:16',1),(15,'owner@acme.com','::1','2026-07-15 11:34:17',1),(16,'owner@acme.com','::1','2026-07-15 15:45:38',1),(17,'admin@acme.com','::1','2026-07-15 15:45:56',1),(18,'owner@acme.com','::1','2026-07-15 15:57:49',1),(19,'sales@acme.com','::1','2026-07-15 15:57:50',1),(20,'owner@beta.com','::1','2026-07-15 15:58:32',1),(21,'owner@acme.com','::1','2026-07-25 18:31:46',1),(22,'owner@acme.com','::1','2026-07-27 18:40:17',1);
/*!40000 ALTER TABLE `login_attempts` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `notifications`
--

DROP TABLE IF EXISTS `notifications`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `notifications` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `organization_id` int(10) unsigned NOT NULL,
  `user_id` int(10) unsigned NOT NULL,
  `title` varchar(200) NOT NULL,
  `message` varchar(500) NOT NULL,
  `type` varchar(50) NOT NULL DEFAULT 'info',
  `link` varchar(255) DEFAULT NULL,
  `is_read` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_notif_user` (`user_id`,`is_read`),
  KEY `idx_notif_org` (`organization_id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `notifications`
--

LOCK TABLES `notifications` WRITE;
/*!40000 ALTER TABLE `notifications` DISABLE KEYS */;
INSERT INTO `notifications` VALUES (1,1,1,'New user created','Test User was added to your organization.','info','http://localhost/minicrm/users',0,'2026-07-15 11:07:05'),(2,1,2,'New user created','Test User was added to your organization.','info','http://localhost/minicrm/users',0,'2026-07-15 11:07:05'),(3,1,1,'Organization updated','Your organization settings were saved successfully.','success','http://localhost/minicrm/organization',0,'2026-07-15 11:07:06'),(4,1,4,'Lead assigned','You were assigned to BrightSoft Lead','info','http://localhost/minicrm/leads/profile/1',0,'2026-07-15 15:57:49'),(5,1,3,'Lead assigned','You were assigned to Nexus Labs','info','http://localhost/minicrm/leads/profile/3',0,'2026-07-15 15:59:16'),(6,1,4,'Lead assigned','You were assigned to Peak Retail','info','http://localhost/minicrm/leads/profile/4',0,'2026-07-15 15:59:16');
/*!40000 ALTER TABLE `notifications` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `organizations`
--

DROP TABLE IF EXISTS `organizations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `organizations` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(150) NOT NULL,
  `slug` varchar(150) NOT NULL,
  `logo` varchar(255) DEFAULT NULL,
  `email` varchar(150) DEFAULT NULL,
  `phone` varchar(50) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `country` varchar(100) DEFAULT NULL,
  `timezone` varchar(64) NOT NULL DEFAULT 'UTC',
  `currency` varchar(10) NOT NULL DEFAULT 'USD',
  `website` varchar(255) DEFAULT NULL,
  `registration_number` varchar(100) DEFAULT NULL,
  `tax_number` varchar(100) DEFAULT NULL,
  `status` enum('active','inactive','suspended') NOT NULL DEFAULT 'active',
  `created_at` datetime NOT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_organizations_slug` (`slug`),
  KEY `idx_organizations_status` (`status`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `organizations`
--

LOCK TABLES `organizations` WRITE;
/*!40000 ALTER TABLE `organizations` DISABLE KEYS */;
INSERT INTO `organizations` VALUES (1,'Acme Corporation','acme',NULL,'hello@acme.test','+15550100','123 Market St','United States','America/New_York','USD','https://acme.test','REG-100','TAX-200','active','2026-07-15 13:14:17','2026-07-15 11:07:06'),(2,'Beta Industries','beta',NULL,'hello@beta.test','+1 555 0200',NULL,'Canada','America/Toronto','CAD',NULL,NULL,NULL,'active','2026-07-15 13:14:17',NULL);
/*!40000 ALTER TABLE `organizations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `password_resets`
--

DROP TABLE IF EXISTS `password_resets`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `password_resets` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `email` varchar(150) NOT NULL,
  `token` varchar(100) NOT NULL,
  `expires_at` datetime NOT NULL,
  `used_at` datetime DEFAULT NULL,
  `created_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_password_resets_token` (`token`),
  KEY `idx_password_resets_email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `password_resets`
--

LOCK TABLES `password_resets` WRITE;
/*!40000 ALTER TABLE `password_resets` DISABLE KEYS */;
INSERT INTO `password_resets` VALUES (1,'owner@acme.com','2c492091b09f91dea7fe3852db196f6a1eba650c98c1691aad8b895408425e91','2026-07-15 11:22:19',NULL,'2026-07-15 10:22:19');
/*!40000 ALTER TABLE `password_resets` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `permission_modules`
--

DROP TABLE IF EXISTS `permission_modules`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `permission_modules` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `slug` varchar(100) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_permission_modules_slug` (`slug`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `permission_modules`
--

LOCK TABLES `permission_modules` WRITE;
/*!40000 ALTER TABLE `permission_modules` DISABLE KEYS */;
INSERT INTO `permission_modules` VALUES (1,'Dashboard','dashboard','Dashboard access',1,1,'2026-07-15 13:53:28'),(2,'Users','users','User management',2,1,'2026-07-15 13:53:28'),(3,'Roles','roles','Roles & permissions',3,1,'2026-07-15 13:53:28'),(4,'Organization','organization','Organization settings',4,1,'2026-07-15 13:53:28'),(5,'Settings','settings','General settings',5,1,'2026-07-15 13:53:28'),(6,'Leads','leads','Lead management (future)',10,1,'2026-07-15 13:53:28'),(7,'Contacts','contacts','Contact management (future)',11,1,'2026-07-15 13:53:28'),(8,'Deals','deals','Deal pipeline (future)',12,1,'2026-07-15 13:53:28'),(9,'Tasks','tasks','Task management (future)',13,1,'2026-07-15 13:53:28'),(10,'Reports','reports','Reports & analytics (future)',14,1,'2026-07-15 13:53:28');
/*!40000 ALTER TABLE `permission_modules` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `permissions`
--

DROP TABLE IF EXISTS `permissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `permissions` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `slug` varchar(100) NOT NULL,
  `module` varchar(100) NOT NULL DEFAULT 'general',
  `description` varchar(255) DEFAULT NULL,
  `created_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_permissions_slug` (`slug`),
  KEY `idx_permissions_module` (`module`)
) ENGINE=InnoDB AUTO_INCREMENT=200 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `permissions`
--

LOCK TABLES `permissions` WRITE;
/*!40000 ALTER TABLE `permissions` DISABLE KEYS */;
INSERT INTO `permissions` VALUES (1,'View Dashboard','dashboard.view','dashboard','Access dashboard','2026-07-15 13:57:21'),(10,'View Users','users.view','users','View users list','2026-07-15 13:57:21'),(11,'Create Users','users.create','users','Create users','2026-07-15 13:57:21'),(12,'Edit Users','users.edit','users','Edit users','2026-07-15 13:57:21'),(13,'Delete Users','users.delete','users','Delete users','2026-07-15 13:57:21'),(14,'Export Users','users.export','users','Export users','2026-07-15 13:57:21'),(15,'Import Users','users.import','users','Import users','2026-07-15 13:57:21'),(20,'View Roles','roles.view','roles','View roles','2026-07-15 13:57:21'),(21,'Create Roles','roles.create','roles','Create roles','2026-07-15 13:57:21'),(22,'Edit Roles','roles.edit','roles','Edit roles & permissions','2026-07-15 13:57:21'),(23,'Delete Roles','roles.delete','roles','Delete roles','2026-07-15 13:57:21'),(24,'Export Roles','roles.export','roles','Export roles','2026-07-15 13:57:21'),(25,'Import Roles','roles.import','roles','Import roles','2026-07-15 13:57:21'),(30,'View Organization','organization.view','organization','View organization','2026-07-15 13:57:21'),(31,'Create Organization','organization.create','organization','Create organization','2026-07-15 13:57:21'),(32,'Edit Organization','organization.edit','organization','Edit organization settings','2026-07-15 13:57:21'),(33,'Delete Organization','organization.delete','organization','Delete organization','2026-07-15 13:57:21'),(34,'Export Organization','organization.export','organization','Export organization','2026-07-15 13:57:21'),(35,'Import Organization','organization.import','organization','Import organization','2026-07-15 13:57:21'),(40,'View Settings','settings.view','settings','View settings','2026-07-15 13:57:21'),(41,'Create Settings','settings.create','settings','Create settings','2026-07-15 13:57:21'),(42,'Edit Settings','settings.edit','settings','Edit settings','2026-07-15 13:57:21'),(43,'Delete Settings','settings.delete','settings','Delete settings','2026-07-15 13:57:21'),(44,'Export Settings','settings.export','settings','Export settings','2026-07-15 13:57:21'),(45,'Import Settings','settings.import','settings','Import settings','2026-07-15 13:57:21'),(50,'Manage Profile','profile.manage','profile','Update own profile','2026-07-15 13:57:21'),(51,'Change Password','profile.password','profile','Change own password','2026-07-15 13:57:21'),(60,'View Leads','leads.view','leads','View leads','2026-07-15 13:57:21'),(61,'Create Leads','leads.create','leads','Create leads','2026-07-15 13:57:21'),(62,'Edit Leads','leads.edit','leads','Edit leads','2026-07-15 13:57:21'),(63,'Delete Leads','leads.delete','leads','Delete leads','2026-07-15 13:57:21'),(64,'Export Leads','leads.export','leads','Export leads','2026-07-15 13:57:21'),(65,'Import Leads','leads.import','leads','Import leads','2026-07-15 13:57:21'),(70,'View Contacts','contacts.view','contacts','View contacts','2026-07-15 13:57:21'),(71,'Create Contacts','contacts.create','contacts','Create contacts','2026-07-15 13:57:21'),(72,'Edit Contacts','contacts.edit','contacts','Edit contacts','2026-07-15 13:57:21'),(73,'Delete Contacts','contacts.delete','contacts','Delete contacts','2026-07-15 13:57:21'),(74,'Export Contacts','contacts.export','contacts','Export contacts','2026-07-15 13:57:21'),(75,'Import Contacts','contacts.import','contacts','Import contacts','2026-07-15 13:57:21'),(80,'View Deals','deals.view','deals','View deals','2026-07-15 13:57:21'),(81,'Create Deals','deals.create','deals','Create deals','2026-07-15 13:57:21'),(82,'Edit Deals','deals.edit','deals','Edit deals','2026-07-15 13:57:21'),(83,'Delete Deals','deals.delete','deals','Delete deals','2026-07-15 13:57:21'),(84,'Export Deals','deals.export','deals','Export deals','2026-07-15 13:57:21'),(85,'Import Deals','deals.import','deals','Import deals','2026-07-15 13:57:21'),(90,'View Tasks','tasks.view','tasks','View tasks','2026-07-15 13:57:21'),(91,'Create Tasks','tasks.create','tasks','Create tasks','2026-07-15 13:57:21'),(92,'Edit Tasks','tasks.edit','tasks','Edit tasks','2026-07-15 13:57:21'),(93,'Delete Tasks','tasks.delete','tasks','Delete tasks','2026-07-15 13:57:21'),(94,'Export Tasks','tasks.export','tasks','Export tasks','2026-07-15 13:57:21'),(95,'Import Tasks','tasks.import','tasks','Import tasks','2026-07-15 13:57:21'),(100,'View Reports','reports.view','reports','View reports','2026-07-15 13:57:21'),(101,'Create Reports','reports.create','reports','Create reports','2026-07-15 13:57:21'),(102,'Edit Reports','reports.edit','reports','Edit reports','2026-07-15 13:57:21'),(103,'Delete Reports','reports.delete','reports','Delete reports','2026-07-15 13:57:21'),(104,'Export Reports','reports.export','reports','Export reports','2026-07-15 13:57:21'),(105,'Import Reports','reports.import','reports','Import reports','2026-07-15 13:57:21');
/*!40000 ALTER TABLE `permissions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pipelines`
--

DROP TABLE IF EXISTS `pipelines`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pipelines` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `organization_id` int(10) unsigned NOT NULL,
  `name` varchar(100) NOT NULL,
  `slug` varchar(100) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `is_default` tinyint(1) NOT NULL DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `created_at` datetime NOT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_pipeline_org_slug` (`organization_id`,`slug`),
  KEY `idx_pipeline_org` (`organization_id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pipelines`
--

LOCK TABLES `pipelines` WRITE;
/*!40000 ALTER TABLE `pipelines` DISABLE KEYS */;
INSERT INTO `pipelines` VALUES (1,1,'Sales','sales','Default sales pipeline',1,1,1,'2026-07-15 18:49:55',NULL),(2,1,'Support','support','Support pipeline',0,1,2,'2026-07-15 18:49:55',NULL),(3,1,'Marketing','marketing','Marketing pipeline',0,1,3,'2026-07-15 18:49:55',NULL),(4,2,'Sales','sales','Default sales pipeline',1,1,1,'2026-07-15 18:49:55',NULL),(5,2,'Support','support','Support pipeline',0,1,2,'2026-07-15 18:49:55',NULL),(6,2,'Marketing','marketing','Marketing pipeline',0,1,3,'2026-07-15 18:49:55',NULL);
/*!40000 ALTER TABLE `pipelines` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `remember_tokens`
--

DROP TABLE IF EXISTS `remember_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `remember_tokens` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL,
  `selector` varchar(32) NOT NULL,
  `token_hash` varchar(255) NOT NULL,
  `expires_at` datetime NOT NULL,
  `created_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_remember_selector` (`selector`),
  KEY `idx_remember_user` (`user_id`),
  CONSTRAINT `fk_remember_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `remember_tokens`
--

LOCK TABLES `remember_tokens` WRITE;
/*!40000 ALTER TABLE `remember_tokens` DISABLE KEYS */;
/*!40000 ALTER TABLE `remember_tokens` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `role_permissions`
--

DROP TABLE IF EXISTS `role_permissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `role_permissions` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `role_id` int(10) unsigned NOT NULL,
  `permission_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_role_permission` (`role_id`,`permission_id`),
  KEY `fk_rp_permission` (`permission_id`),
  CONSTRAINT `fk_rp_permission` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_rp_role` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=376 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `role_permissions`
--

LOCK TABLES `role_permissions` WRITE;
/*!40000 ALTER TABLE `role_permissions` DISABLE KEYS */;
INSERT INTO `role_permissions` VALUES (211,1,1),(261,1,10),(256,1,11),(258,1,12),(257,1,13),(259,1,14),(260,1,15),(243,1,20),(238,1,21),(240,1,22),(239,1,23),(241,1,24),(242,1,25),(229,1,30),(224,1,31),(226,1,32),(225,1,33),(227,1,34),(228,1,35),(249,1,40),(244,1,41),(246,1,42),(245,1,43),(247,1,44),(248,1,45),(230,1,50),(231,1,51),(223,1,60),(218,1,61),(220,1,62),(219,1,63),(221,1,64),(222,1,65),(210,1,70),(205,1,71),(207,1,72),(206,1,73),(208,1,74),(209,1,75),(217,1,80),(212,1,81),(214,1,82),(213,1,83),(215,1,84),(216,1,85),(255,1,90),(250,1,91),(252,1,92),(251,1,93),(253,1,94),(254,1,95),(237,1,100),(232,1,101),(234,1,102),(233,1,103),(235,1,104),(236,1,105),(274,2,1),(320,2,10),(315,2,11),(317,2,12),(316,2,13),(318,2,14),(319,2,15),(302,2,20),(299,2,22),(300,2,24),(301,2,25),(290,2,30),(287,2,32),(288,2,34),(289,2,35),(308,2,40),(303,2,41),(305,2,42),(304,2,43),(306,2,44),(307,2,45),(291,2,50),(292,2,51),(286,2,60),(281,2,61),(283,2,62),(282,2,63),(284,2,64),(285,2,65),(273,2,70),(268,2,71),(270,2,72),(269,2,73),(271,2,74),(272,2,75),(280,2,80),(275,2,81),(277,2,82),(276,2,83),(278,2,84),(279,2,85),(314,2,90),(309,2,91),(311,2,92),(310,2,93),(312,2,94),(313,2,95),(298,2,100),(293,2,101),(295,2,102),(294,2,103),(296,2,104),(297,2,105),(334,3,1),(349,3,10),(341,3,30),(345,3,40),(342,3,50),(343,3,51),(340,3,60),(338,3,61),(339,3,62),(333,3,70),(331,3,71),(332,3,72),(337,3,80),(335,3,81),(336,3,82),(348,3,90),(346,3,91),(347,3,92),(344,3,100),(364,4,1),(371,4,50),(372,4,51),(370,4,60),(368,4,61),(369,4,62),(363,4,70),(362,4,71),(367,4,80),(365,4,81),(366,4,82),(375,4,90),(373,4,91),(374,4,92);
/*!40000 ALTER TABLE `role_permissions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `roles`
--

DROP TABLE IF EXISTS `roles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `roles` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `slug` varchar(100) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `is_system` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` datetime NOT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_roles_slug` (`slug`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `roles`
--

LOCK TABLES `roles` WRITE;
/*!40000 ALTER TABLE `roles` DISABLE KEYS */;
INSERT INTO `roles` VALUES (1,'Owner','owner','Full access to organization',1,'2026-07-15 13:14:17',NULL),(2,'Admin','admin','Administrative access',1,'2026-07-15 13:14:17',NULL),(3,'Manager','manager','Manage team and pipeline',1,'2026-07-15 13:14:17',NULL),(4,'Sales Person','sales_person','Sales activities access',1,'2026-07-15 13:14:17',NULL);
/*!40000 ALTER TABLE `roles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `task_priorities`
--

DROP TABLE IF EXISTS `task_priorities`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `task_priorities` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `organization_id` int(10) unsigned NOT NULL,
  `name` varchar(50) NOT NULL,
  `slug` varchar(50) NOT NULL,
  `color` varchar(20) NOT NULL DEFAULT '#64748B',
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` datetime NOT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_priority_org_slug` (`organization_id`,`slug`),
  KEY `idx_priority_org` (`organization_id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `task_priorities`
--

LOCK TABLES `task_priorities` WRITE;
/*!40000 ALTER TABLE `task_priorities` DISABLE KEYS */;
INSERT INTO `task_priorities` VALUES (1,1,'Low','low','#64748B',1,1,'2026-07-15 18:49:55',NULL),(2,1,'Medium','medium','#0284C7',2,1,'2026-07-15 18:49:55',NULL),(3,1,'High','high','#D97706',3,1,'2026-07-15 18:49:55',NULL),(4,1,'Urgent','urgent','#DC2626',4,1,'2026-07-15 18:49:55',NULL),(5,2,'Low','low','#64748B',1,1,'2026-07-15 18:49:55',NULL),(6,2,'Medium','medium','#0284C7',2,1,'2026-07-15 18:49:55',NULL),(7,2,'High','high','#D97706',3,1,'2026-07-15 18:49:55',NULL),(8,2,'Urgent','urgent','#DC2626',4,1,'2026-07-15 18:49:55',NULL);
/*!40000 ALTER TABLE `task_priorities` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `users` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `organization_id` int(10) unsigned NOT NULL,
  `role_id` int(10) unsigned NOT NULL,
  `name` varchar(150) NOT NULL,
  `email` varchar(150) NOT NULL,
  `password` varchar(255) NOT NULL,
  `phone` varchar(50) DEFAULT NULL,
  `profile_image` varchar(255) DEFAULT NULL,
  `status` enum('active','inactive','suspended') NOT NULL DEFAULT 'active',
  `last_login` datetime DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_users_email` (`email`),
  KEY `idx_users_org` (`organization_id`),
  KEY `idx_users_role` (`role_id`),
  KEY `idx_users_status` (`status`),
  KEY `idx_users_deleted` (`deleted_at`),
  CONSTRAINT `fk_users_org` FOREIGN KEY (`organization_id`) REFERENCES `organizations` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_users_role` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,1,1,'Acme Owner','owner@acme.com','$2y$12$PezgLCx7eirPcuBHtPGhNeRePtGQBQxHtatOV9CJKEmXzGobVyStu','+1 555 0101',NULL,'active','2026-07-27 18:40:17','2026-07-15 13:14:17','2026-07-27 18:40:17',NULL),(2,1,2,'Acme Admin','admin@acme.com','$2y$12$PezgLCx7eirPcuBHtPGhNeRePtGQBQxHtatOV9CJKEmXzGobVyStu','+1 555 0102',NULL,'active','2026-07-15 15:45:56','2026-07-15 13:14:17','2026-07-15 15:45:56',NULL),(3,1,3,'Acme Manager','manager@acme.com','$2y$12$PezgLCx7eirPcuBHtPGhNeRePtGQBQxHtatOV9CJKEmXzGobVyStu','+1 555 0103',NULL,'active',NULL,'2026-07-15 13:14:17',NULL,NULL),(4,1,4,'Acme Sales','sales@acme.com','$2y$12$PezgLCx7eirPcuBHtPGhNeRePtGQBQxHtatOV9CJKEmXzGobVyStu','+1 555 0104',NULL,'active','2026-07-15 15:57:50','2026-07-15 13:14:17','2026-07-15 15:57:50',NULL),(5,2,1,'Beta Owner','owner@beta.com','$2y$12$PezgLCx7eirPcuBHtPGhNeRePtGQBQxHtatOV9CJKEmXzGobVyStu','+1 555 0201',NULL,'active','2026-07-15 15:58:32','2026-07-15 13:14:17','2026-07-15 15:58:32',NULL),(6,1,4,'Test User','test.user@acme.com','$2y$10$Drplthx.0IQd3sWIM3mbCO2zPFf08YNFHmTHJWU1EAnZPzGkDabZ6','+15550199',NULL,'active',NULL,'2026-07-15 11:07:05',NULL,NULL);
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-07-27 21:41:25
