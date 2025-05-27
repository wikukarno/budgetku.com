/*M!999999\- enable the sandbox mode */ 
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*M!100616 SET @OLD_NOTE_VERBOSITY=@@NOTE_VERBOSITY, NOTE_VERBOSITY=0 */;
DROP TABLE IF EXISTS `abouts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `abouts` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `description` text NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `bills`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `bills` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `users_id` bigint(20) NOT NULL,
  `nama_tagihan` varchar(255) NOT NULL,
  `harga_tagihan` varchar(255) NOT NULL,
  `siklus_tagihan` varchar(255) NOT NULL,
  `jatuh_tempo_tagihan` datetime NOT NULL,
  `metode_pembayaran` varchar(255) NOT NULL,
  `keterangan_tagihan` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `bills_nama_tagihan_unique` (`nama_tagihan`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `category_finances`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `category_finances` (
  `id` bigint(20) unsigned DEFAULT NULL,
  `uuid` char(36) NOT NULL,
  `name_category_finances` varchar(255) NOT NULL,
  `users_id` bigint(20) unsigned NOT NULL,
  `users_uuid` char(36) DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`uuid`),
  KEY `category_finances_uuid_index` (`uuid`),
  KEY `category_finances_users_uuid_index` (`users_uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `category_incomes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `category_incomes` (
  `id` bigint(20) unsigned NOT NULL,
  `uuid` char(36) NOT NULL,
  `users_id` bigint(20) unsigned NOT NULL,
  `users_uuid` char(36) DEFAULT NULL,
  `name_category_incomes` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`uuid`),
  KEY `category_incomes_uuid_index` (`uuid`),
  KEY `category_incomes_users_uuid_index` (`users_uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `documents`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `documents` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `file` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `failed_jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `failed_jobs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(255) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `finances`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `finances` (
  `id` bigint(20) unsigned DEFAULT NULL,
  `uuid` char(36) NOT NULL,
  `users_id` varchar(255) DEFAULT NULL,
  `users_uuid` char(36) DEFAULT NULL,
  `category_finances_id` varchar(255) DEFAULT NULL,
  `category_finances_uuid` char(36) DEFAULT NULL,
  `name_item` varchar(255) NOT NULL,
  `price` varchar(255) NOT NULL,
  `purchase_date` date NOT NULL,
  `purchase_by` varchar(255) DEFAULT NULL,
  `payment_methods_uuid` char(36) DEFAULT NULL,
  `bukti_pembayaran` varchar(255) DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`uuid`),
  KEY `finances_uuid_index` (`uuid`),
  KEY `finances_users_uuid_index` (`users_uuid`),
  KEY `finances_category_finances_uuid_index` (`category_finances_uuid`),
  KEY `finances_payment_methods_uuid_index` (`payment_methods_uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `health_check_result_history_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `health_check_result_history_items` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `check_name` varchar(255) NOT NULL,
  `check_label` varchar(255) NOT NULL,
  `status` varchar(255) NOT NULL,
  `notification_message` text DEFAULT NULL,
  `short_summary` varchar(255) DEFAULT NULL,
  `meta` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`meta`)),
  `ended_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `batch` char(36) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `health_check_result_history_items_created_at_index` (`created_at`),
  KEY `health_check_result_history_items_batch_index` (`batch`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `jobs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `queue` varchar(255) NOT NULL,
  `payload` longtext NOT NULL,
  `attempts` tinyint(3) unsigned NOT NULL,
  `reserved_at` int(10) unsigned DEFAULT NULL,
  `available_at` int(10) unsigned NOT NULL,
  `created_at` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `jobs_queue_index` (`queue`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `migrations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `migrations` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `password_resets`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `password_resets` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  KEY `password_resets_email_index` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `payment_methods`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `payment_methods` (
  `id` bigint(20) unsigned NOT NULL,
  `uuid` char(36) DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `icon` varchar(255) DEFAULT NULL,
  `users_id` bigint(20) unsigned NOT NULL,
  `users_uuid` char(36) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `payment_methods_users_id_foreign` (`users_id`),
  KEY `payment_methods_uuid_index` (`uuid`),
  KEY `payment_methods_users_uuid_index` (`users_uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `personal_access_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `personal_access_tokens` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `tokenable_type` varchar(255) NOT NULL,
  `tokenable_id` bigint(20) unsigned NOT NULL,
  `name` varchar(255) NOT NULL,
  `token` varchar(64) NOT NULL,
  `abilities` text DEFAULT NULL,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `portofolios`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `portofolios` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `kategori` varchar(255) NOT NULL,
  `url` varchar(255) NOT NULL,
  `thumbnail` varchar(255) NOT NULL,
  `published` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `salaries`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `salaries` (
  `id` bigint(20) unsigned NOT NULL,
  `uuid` char(36) NOT NULL,
  `users_id` varchar(255) NOT NULL,
  `users_uuid` char(36) DEFAULT NULL,
  `tipe` varchar(255) NOT NULL,
  `category_incomes_uuid` char(36) DEFAULT NULL,
  `salary` varchar(255) NOT NULL,
  `date` date NOT NULL,
  `description` text NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `id` bigint(20) unsigned DEFAULT NULL,
  `uuid` char(36) NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `email_parrent` varchar(255) DEFAULT NULL,
  `saldo` bigint(20) NOT NULL DEFAULT 0,
  `roles` varchar(255) NOT NULL DEFAULT 'Customer',
  `avatar` varchar(255) DEFAULT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `last_login_at` timestamp NULL DEFAULT NULL,
  `last_login_ip` varchar(255) DEFAULT NULL,
  `telegram_id` varchar(255) DEFAULT NULL,
  `telegram_username` varchar(255) DEFAULT NULL,
  `api_token` varchar(255) DEFAULT NULL,
  `auth_token` varchar(255) DEFAULT NULL,
  `notifications` tinyint(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`uuid`),
  UNIQUE KEY `users_email_unique` (`email`),
  UNIQUE KEY `users_uuid_unique` (`uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*M!100616 SET NOTE_VERBOSITY=@OLD_NOTE_VERBOSITY */;

/*M!999999\- enable the sandbox mode */ 
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (1,'2014_10_12_000000_create_users_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (2,'2014_10_12_100000_create_password_resets_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (3,'2019_08_19_000000_create_failed_jobs_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (4,'2019_12_14_000001_create_personal_access_tokens_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (5,'2022_10_12_052601_create_portofolios_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (6,'2022_10_12_052732_create_abouts_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (7,'2022_11_11_102137_create_documents_table',2);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (8,'2022_11_20_164229_add_last_login_to_users_table',3);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (10,'2022_12_30_093926_create_category_finances_table',4);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (12,'2022_12_03_092704_create_finances_table',5);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (13,'2023_01_03_091948_create_salaries_table',6);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (14,'2023_01_07_094311_add_telegram_to_users_table',7);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (15,'2023_02_16_204307_create_bills_table',8);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (16,'2024_01_07_153730_create_jobs_table',9);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (17,'2024_01_19_230306_add_auth_token_to_users_table',10);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (18,'2024_03_17_015108_create_sessions_table',11);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (19,'2024_08_24_223814_add_bukti_pembayaran_to_finances_table',12);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (20,'2024_08_24_224924_add_users_id_to_category_finances_table',13);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (21,'2024_08_24_225702_create_category_incomes_table',13);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (22,'2024_09_14_154029_add_users_id_to_category_finances_table',14);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (23,'2024_09_16_150322_change_field_tipe_uang_masuk_to_int_table',14);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (25,'2024_09_16_161353_change_field_password_to_nullable_table',15);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (26,'2024_09_22_183434_add_saldo_to_users_table',16);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (27,'2024_10_08_195554_add_email_parrent_to_users_table',17);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (28,'2024_12_08_134734_add_notifications_to_users_table',18);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (29,'2025_04_07_173744_create_payment_methods_table',19);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (30,'2025_05_19_102233_change_icon_nullable_in_payment_methods_table',20);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (31,'2025_05_19_115307_create_health_tables',21);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (32,'2025_05_27_110859_add_uuid_to_users_table',22);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (33,'2025_05_27_140819_switch_primary_key_to_uuid_on_users_table',23);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (34,'2025_05_27_141653_make_id_nullable_in_users_table',23);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (35,'2025_05_27_150309_add_uuid_and_users_uuid_to_category_incomes_table',23);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (36,'2025_05_27_154726_add_uuid_and_users_uuid_to_category_finances_table',24);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (37,'2025_05_27_160721_add_uuid_users_uuid_category_finances_uuid_to_finances_table',25);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (38,'2025_05_27_185901_add_uuid_and_users_uuid_to_payment_methods_table',26);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (39,'2025_05_27_191144_add_payment_methods_uuid_to_finances_table',27);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (40,'2025_05_27_192246_make_purchase_by_nullable_in_finances_table',28);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (41,'2025_05_27_193457_make_users_id_nullable_in_finances_table',29);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (42,'2025_05_27_193643_make_category_finances_id_nullable_in_finances_table',30);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (43,'2025_05_27_205421_add_uuid_users_uuid_and_category_incomes_uuid_to_salaries_table',31);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (44,'2025_05_27_213021_change_salary_primary_key_to_uuid',32);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (45,'2025_05_27_213622_change_finance_primary_key_to_uuid',33);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (46,'2025_05_27_215500_change_category_finance_primary_key_to_uuid',34);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (47,'2025_05_27_220042_change_category_income_primary_key_to_uuid',35);
