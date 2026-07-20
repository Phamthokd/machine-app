/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
DROP TABLE IF EXISTS `audit_criteria`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `audit_criteria` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `audit_template_id` bigint unsigned NOT NULL,
  `content` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `order_num` int NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `audit_criteria_audit_template_id_foreign` (`audit_template_id`),
  CONSTRAINT `audit_criteria_audit_template_id_foreign` FOREIGN KEY (`audit_template_id`) REFERENCES `audit_templates` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `audit_records`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `audit_records` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `audit_template_id` bigint unsigned NOT NULL,
  `auditor_id` bigint unsigned NOT NULL,
  `status` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'completed',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `audit_records_audit_template_id_foreign` (`audit_template_id`),
  KEY `audit_records_auditor_id_foreign` (`auditor_id`),
  CONSTRAINT `audit_records_audit_template_id_foreign` FOREIGN KEY (`audit_template_id`) REFERENCES `audit_templates` (`id`) ON DELETE CASCADE,
  CONSTRAINT `audit_records_auditor_id_foreign` FOREIGN KEY (`auditor_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `audit_results`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `audit_results` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `audit_record_id` bigint unsigned NOT NULL,
  `audit_criterion_id` bigint unsigned NOT NULL,
  `is_passed` tinyint(1) NOT NULL,
  `department_agreement` tinyint(1) DEFAULT NULL COMMENT 'true = đồng ý lỗi, false = phản đối lỗi',
  `department_reject_reason` text COLLATE utf8mb4_unicode_ci COMMENT 'lý do phản đối từ bộ phận',
  `audit_rejection_decision` tinyint(1) DEFAULT NULL COMMENT 'true = chấp nhận huỷ lỗi, false = bác bỏ phản đối phải cải thiện',
  `note` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `image_path` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `root_cause` text COLLATE utf8mb4_unicode_ci,
  `corrective_action` text COLLATE utf8mb4_unicode_ci,
  `improvement_deadline` date DEFAULT NULL,
  `improver_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_completed` tinyint(1) NOT NULL DEFAULT '0',
  `completed_at` timestamp NULL DEFAULT NULL,
  `completion_image_path` text COLLATE utf8mb4_unicode_ci,
  `completion_note` text COLLATE utf8mb4_unicode_ci,
  `reviewer_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `review_note` text COLLATE utf8mb4_unicode_ci,
  `review_image_path` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `reviewed_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `audit_results_audit_record_id_foreign` (`audit_record_id`),
  KEY `audit_results_audit_criterion_id_foreign` (`audit_criterion_id`),
  CONSTRAINT `audit_results_audit_criterion_id_foreign` FOREIGN KEY (`audit_criterion_id`) REFERENCES `audit_criteria` (`id`) ON DELETE CASCADE,
  CONSTRAINT `audit_results_audit_record_id_foreign` FOREIGN KEY (`audit_record_id`) REFERENCES `audit_records` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `audit_templates`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `audit_templates` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `department_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `cache`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cache` (
  `key` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` mediumtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL,
  PRIMARY KEY (`key`),
  KEY `cache_expiration_index` (`expiration`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `cache_locks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cache_locks` (
  `key` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `owner` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL,
  PRIMARY KEY (`key`),
  KEY `cache_locks_expiration_index` (`expiration`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `candidate_senior_manager`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `candidate_senior_manager` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `candidate_id` bigint unsigned NOT NULL,
  `user_id` bigint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `candidate_senior_manager_candidate_id_foreign` (`candidate_id`),
  KEY `candidate_senior_manager_user_id_foreign` (`user_id`),
  CONSTRAINT `candidate_senior_manager_candidate_id_foreign` FOREIGN KEY (`candidate_id`) REFERENCES `candidates` (`id`) ON DELETE CASCADE,
  CONSTRAINT `candidate_senior_manager_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `candidates`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `candidates` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `full_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `gender` enum('male','female') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'male',
  `dob` date DEFAULT NULL,
  `id_number` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `education` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `language_skills` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `position_applied` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `address` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `bank_account` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `photo_path` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `marital_status` enum('single','married','divorced') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'single',
  `children_dob` json DEFAULT NULL,
  `referral_source` json DEFAULT NULL,
  `referral_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `referral_department` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `referral_relation` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `emergency_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `emergency_address` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `emergency_relation` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `emergency_phone` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `work_experiences` json DEFAULT NULL,
  `expected_salary` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `submitted_by` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `candidates_submitted_by_foreign` (`submitted_by`),
  CONSTRAINT `candidates_submitted_by_foreign` FOREIGN KEY (`submitted_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `departments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `departments` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `code` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `departments_code_unique` (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `environment_report_entries`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `environment_report_entries` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `environment_report_id` bigint unsigned NOT NULL,
  `report_date` date NOT NULL,
  `day_number` tinyint unsigned NOT NULL,
  `humidity_0730` decimal(5,1) DEFAULT NULL,
  `humidity_1030` decimal(5,1) DEFAULT NULL,
  `humidity_1400` decimal(5,1) DEFAULT NULL,
  `humidity_1630` decimal(5,1) DEFAULT NULL,
  `temperature_0730` decimal(5,1) DEFAULT NULL,
  `temperature_1030` decimal(5,1) DEFAULT NULL,
  `temperature_1400` decimal(5,1) DEFAULT NULL,
  `temperature_1630` decimal(5,1) DEFAULT NULL,
  `weather` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `action_0730` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `action_1030` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `action_1400` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `action_1630` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `checked_by` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `environment_report_entries_unique_day` (`environment_report_id`,`day_number`),
  CONSTRAINT `environment_report_entries_environment_report_id_foreign` FOREIGN KEY (`environment_report_id`) REFERENCES `environment_reports` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `environment_reports`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `environment_reports` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `department_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `report_year` smallint unsigned NOT NULL,
  `report_month` tinyint unsigned NOT NULL,
  `creator_id` bigint unsigned NOT NULL,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'draft',
  `note` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `environment_reports_unique_period` (`department_name`,`report_year`,`report_month`),
  KEY `environment_reports_creator_id_foreign` (`creator_id`),
  CONSTRAINT `environment_reports_creator_id_foreign` FOREIGN KEY (`creator_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `failed_jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `failed_jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `it_repairs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `it_repairs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `code` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `department` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `reporter_id` bigint unsigned DEFAULT NULL,
  `machine_id` bigint unsigned DEFAULT NULL,
  `issue_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `location` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `priority` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'medium',
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `resolver_id` bigint unsigned DEFAULT NULL,
  `resolution_note` text COLLATE utf8mb4_unicode_ci,
  `resolved_at` timestamp NULL DEFAULT NULL,
  `images` json DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `it_repairs_code_unique` (`code`),
  KEY `it_repairs_reporter_id_foreign` (`reporter_id`),
  KEY `it_repairs_resolver_id_foreign` (`resolver_id`),
  KEY `it_repairs_machine_id_foreign` (`machine_id`),
  CONSTRAINT `it_repairs_machine_id_foreign` FOREIGN KEY (`machine_id`) REFERENCES `machines` (`id`) ON DELETE SET NULL,
  CONSTRAINT `it_repairs_reporter_id_foreign` FOREIGN KEY (`reporter_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `it_repairs_resolver_id_foreign` FOREIGN KEY (`resolver_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `job_batches`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `job_batches` (
  `id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `total_jobs` int NOT NULL,
  `pending_jobs` int NOT NULL,
  `failed_jobs` int NOT NULL,
  `failed_job_ids` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `options` mediumtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `cancelled_at` int DEFAULT NULL,
  `created_at` int NOT NULL,
  `finished_at` int DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `queue` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `attempts` tinyint unsigned NOT NULL,
  `reserved_at` int unsigned DEFAULT NULL,
  `available_at` int unsigned NOT NULL,
  `created_at` int unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `jobs_queue_index` (`queue`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `machine_movements`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `machine_movements` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `machine_id` bigint unsigned NOT NULL,
  `from_department_id` bigint unsigned NOT NULL,
  `to_department_id` bigint unsigned NOT NULL,
  `user_id` bigint unsigned DEFAULT NULL,
  `note` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `machine_movements_machine_id_foreign` (`machine_id`),
  KEY `machine_movements_from_department_id_foreign` (`from_department_id`),
  KEY `machine_movements_to_department_id_foreign` (`to_department_id`),
  KEY `machine_movements_user_id_foreign` (`user_id`),
  CONSTRAINT `machine_movements_from_department_id_foreign` FOREIGN KEY (`from_department_id`) REFERENCES `departments` (`id`),
  CONSTRAINT `machine_movements_machine_id_foreign` FOREIGN KEY (`machine_id`) REFERENCES `machines` (`id`),
  CONSTRAINT `machine_movements_to_department_id_foreign` FOREIGN KEY (`to_department_id`) REFERENCES `departments` (`id`),
  CONSTRAINT `machine_movements_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `machines`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `machines` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `ma_thiet_bi` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `ten_thiet_bi` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `brand` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `model` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `serial` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `invoice_cd` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `year` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `country` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `stock_in_date` date DEFAULT NULL,
  `vi_tri_text` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ngay_vao_kho` date DEFAULT NULL,
  `ngay_ra_kho` date DEFAULT NULL,
  `warranty_period` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `current_department_id` bigint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `country_of_origin` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `stock_in_raw` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `location_text` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ngay_vao_kho_raw` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ngay_ra_kho_raw` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `machines_ma_thiet_bi_unique` (`ma_thiet_bi`),
  KEY `machines_current_department_id_foreign` (`current_department_id`),
  CONSTRAINT `machines_current_department_id_foreign` FOREIGN KEY (`current_department_id`) REFERENCES `departments` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `migrations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `migrations` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `model_has_permissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `model_has_permissions` (
  `permission_id` bigint unsigned NOT NULL,
  `model_type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `model_id` bigint unsigned NOT NULL,
  PRIMARY KEY (`permission_id`,`model_id`,`model_type`),
  KEY `model_has_permissions_model_id_model_type_index` (`model_id`,`model_type`),
  CONSTRAINT `model_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `model_has_roles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `model_has_roles` (
  `role_id` bigint unsigned NOT NULL,
  `model_type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `model_id` bigint unsigned NOT NULL,
  PRIMARY KEY (`role_id`,`model_id`,`model_type`),
  KEY `model_has_roles_model_id_model_type_index` (`model_id`,`model_type`),
  CONSTRAINT `model_has_roles_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `notifications`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `notifications` (
  `id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `notifiable_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `notifiable_id` bigint unsigned NOT NULL,
  `data` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `read_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `notifications_notifiable_type_notifiable_id_index` (`notifiable_type`,`notifiable_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `password_reset_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `permissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `permissions` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `guard_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `permissions_name_guard_name_unique` (`name`,`guard_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `repair_tickets`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `repair_tickets` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `machine_id` bigint unsigned DEFAULT NULL,
  `department_id` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `ma_hang` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cong_doan` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `mo_ta_loi` text COLLATE utf8mb4_unicode_ci,
  `nguyen_nhan` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `images` json DEFAULT NULL,
  `noi_dung_sua_chua` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `nguoi_ho_tro` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `started_at` datetime DEFAULT NULL,
  `ended_at` datetime DEFAULT NULL,
  `created_by` bigint unsigned DEFAULT NULL,
  `status` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'submitted',
  `approval_status` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `approval_note` text COLLATE utf8mb4_unicode_ci,
  `approved_by` bigint unsigned DEFAULT NULL,
  `approved_at` timestamp NULL DEFAULT NULL,
  `type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'mechanic',
  `code` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `endline_qc_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `inline_qc_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `qa_supervisor_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `eval_response_time` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `eval_repair_speed` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `eval_error_rate` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `evaluated_at` timestamp NULL DEFAULT NULL,
  `mechanic_id` bigint unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `repair_tickets_machine_id_foreign` (`machine_id`),
  KEY `repair_tickets_department_id_foreign` (`department_id`),
  KEY `repair_tickets_created_by_foreign` (`created_by`),
  KEY `repair_tickets_mechanic_id_foreign` (`mechanic_id`),
  KEY `repair_tickets_approved_by_foreign` (`approved_by`),
  CONSTRAINT `repair_tickets_approved_by_foreign` FOREIGN KEY (`approved_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `repair_tickets_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`),
  CONSTRAINT `repair_tickets_department_id_foreign` FOREIGN KEY (`department_id`) REFERENCES `departments` (`id`),
  CONSTRAINT `repair_tickets_machine_id_foreign` FOREIGN KEY (`machine_id`) REFERENCES `machines` (`id`),
  CONSTRAINT `repair_tickets_mechanic_id_foreign` FOREIGN KEY (`mechanic_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `role_has_permissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `role_has_permissions` (
  `permission_id` bigint unsigned NOT NULL,
  `role_id` bigint unsigned NOT NULL,
  PRIMARY KEY (`permission_id`,`role_id`),
  KEY `role_has_permissions_role_id_foreign` (`role_id`),
  CONSTRAINT `role_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE,
  CONSTRAINT `role_has_permissions_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `roles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `roles` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `guard_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `roles_name_guard_name_unique` (`name`,`guard_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `sessions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `sessions` (
  `id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint unsigned DEFAULT NULL,
  `ip_address` varchar(45) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `payload` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_activity` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `sessions_user_id_index` (`user_id`),
  KEY `sessions_last_activity_index` (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `seven_s_checklists`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `seven_s_checklists` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `department` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `section` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `sort_order` smallint unsigned NOT NULL DEFAULT '0',
  `content` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `seven_s_records`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `seven_s_records` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `department` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `inspector_id` bigint unsigned NOT NULL,
  `score` int NOT NULL DEFAULT '0',
  `max_score` int NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `seven_s_records_inspector_id_foreign` (`inspector_id`),
  CONSTRAINT `seven_s_records_inspector_id_foreign` FOREIGN KEY (`inspector_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `seven_s_results`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `seven_s_results` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `record_id` bigint unsigned NOT NULL,
  `checklist_id` bigint unsigned NOT NULL,
  `grade` enum('B','C','D','E') COLLATE utf8mb4_unicode_ci NOT NULL,
  `note` text COLLATE utf8mb4_unicode_ci,
  `image_path` json DEFAULT NULL,
  `points` int NOT NULL,
  `department_agreement` tinyint(1) DEFAULT NULL COMMENT 'true = đồng ý lỗi, false = phản đối lỗi',
  `department_reject_reason` text COLLATE utf8mb4_unicode_ci COMMENT 'lý do phản đối từ bộ phận',
  `auditor_rejection_decision` tinyint(1) DEFAULT NULL COMMENT 'true = chấp nhận huỷ lỗi, false = bác bỏ phản đối phải cải thiện',
  `improvement_note` text COLLATE utf8mb4_unicode_ci,
  `improvement_image_path` json DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `improver_id` bigint unsigned DEFAULT NULL,
  `improved_at` timestamp NULL DEFAULT NULL,
  `review_status` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `review_note` text COLLATE utf8mb4_unicode_ci,
  `review_image_path` text COLLATE utf8mb4_unicode_ci,
  `reviewer_id` bigint unsigned DEFAULT NULL,
  `reviewed_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `seven_s_results_record_id_foreign` (`record_id`),
  KEY `seven_s_results_checklist_id_foreign` (`checklist_id`),
  KEY `seven_s_results_improver_id_foreign` (`improver_id`),
  KEY `seven_s_results_reviewer_id_foreign` (`reviewer_id`),
  CONSTRAINT `seven_s_results_checklist_id_foreign` FOREIGN KEY (`checklist_id`) REFERENCES `seven_s_checklists` (`id`) ON DELETE CASCADE,
  CONSTRAINT `seven_s_results_improver_id_foreign` FOREIGN KEY (`improver_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `seven_s_results_record_id_foreign` FOREIGN KEY (`record_id`) REFERENCES `seven_s_records` (`id`) ON DELETE CASCADE,
  CONSTRAINT `seven_s_results_reviewer_id_foreign` FOREIGN KEY (`reviewer_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `username` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `managed_department` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `managed_departments` json DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `remember_token` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`),
  UNIQUE KEY `users_username_unique` (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (1,'0001_01_01_000000_create_users_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (2,'0001_01_01_000001_create_cache_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (3,'0001_01_01_000002_create_jobs_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (4,'2026_01_27_075703_create_permission_tables',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (5,'2026_01_27_083004_create_departments_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (6,'2026_01_27_083011_create_machines_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (7,'2026_01_27_083017_create_repair_tickets_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (8,'2026_01_30_022755_fix_repair_tickets_columns',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (9,'2026_01_30_040511_add_machine_details_to_machines_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (10,'2026_01_30_044420_add_machine_import_columns_to_machines_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (11,'2026_01_30_060414_drop_ma_may_from_machines',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (12,'2026_01_31_093500_create_machine_movements_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (13,'2026_01_31_094500_add_username_to_users_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (14,'2026_01_31_095000_make_email_nullable_in_users_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (15,'2026_01_31_102500_make_qc_fields_nullable_in_repair_tickets',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (16,'2026_01_31_103000_change_qc_columns_to_strings_in_repair_tickets',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (17,'2026_01_31_153000_add_contractor_role',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (18,'2026_01_31_154500_add_nguoi_ho_tro_to_repair_tickets',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (19,'2026_02_03_134500_add_type_to_repair_tickets',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (20,'2026_02_13_103014_update_department_name_to_38',2);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (21,'2026_02_23_171840_add_audit_role',3);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (22,'2026_02_24_083500_add_mechanic_id_to_repair_tickets_table',3);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (23,'2026_02_24_091000_create_audit_templates_table',4);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (24,'2026_02_24_091001_create_audit_criteria_table',4);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (25,'2026_02_24_091002_create_audit_records_table',4);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (26,'2026_02_24_091003_create_audit_results_table',4);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (27,'2026_02_24_095800_add_image_path_to_audit_results_table',4);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (28,'2026_02_26_105000_add_managed_department_to_users_table',5);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (29,'2026_02_26_110600_add_improvements_to_audit_results_table',6);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (30,'2026_02_26_112200_add_improver_name_to_audit_results_table',7);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (31,'2026_02_27_092000_add_review_fields_to_audit_results_table',8);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (32,'2026_03_06_000000_add_7s_role',9);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (33,'2026_03_06_100001_create_seven_s_checklists_table',10);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (34,'2026_03_06_100002_create_seven_s_records_table',10);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (35,'2026_03_06_100003_create_seven_s_results_table',10);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (36,'2026_03_07_100000_add_improvement_columns_to_seven_s_results_table',11);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (37,'2026_03_11_100000_add_rejection_fields_to_audit_results_table',12);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (38,'2026_03_14_000001_create_notifications_table',13);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (39,'2026_03_16_160000_add_completion_fields_to_audit_results_table',14);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (40,'2026_03_17_000000_add_completion_note_to_audit_results_table',15);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (41,'2026_03_21_000000_add_review_fields_to_seven_s_results',16);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (42,'2026_03_21_000000_add_review_fields_to_seven_s_results_table',17);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (43,'2026_03_23_160000_add_disagreement_fields_to_seven_s_results_table',17);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (44,'2026_04_07_000000_create_environment_reports_table',18);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (45,'2026_04_10_000000_add_is_active_to_users_table',19);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (46,'2026_04_22_085613_add_evaluation_fields_to_repair_tickets_table',20);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (47,'2026_05_08_000000_add_managed_departments_to_users_table',21);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (48,'2026_05_28_092916_add_warranty_period_to_machines_table',22);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (49,'2026_06_02_135300_repair_corrupted_machine_data',23);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (50,'2026_06_02_143000_delete_machines_imported_today',24);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (51,'2026_06_16_130000_add_approval_fields_to_repair_tickets',25);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (52,'2026_06_20_000000_create_candidates_table',26);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (53,'2026_06_27_000000_add_images_to_repair_tickets_table',27);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (54,'2026_06_27_010000_add_bok_role',27);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (55,'2026_06_27_020000_add_mo_ta_loi_to_repair_tickets_table',28);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (56,'2026_07_01_165055_create_candidate_senior_manager_table',29);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (57,'2026_07_02_151258_delete_recently_imported_machines_on_vps',30);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (58,'2026_07_02_152214_delete_recently_imported_machines_range',31);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (59,'2026_07_06_153605_create_it_repairs_table',32);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (60,'2026_07_06_163357_add_machine_id_to_it_repairs_table',33);
