-- --------------------------------------------------------
-- Host:                         127.0.0.1
-- Server version:               8.0.46 - MySQL Community Server - GPL
-- Server OS:                    Linux
-- HeidiSQL Version:             12.17.0.7270
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


-- Dumping database structure for homi
CREATE DATABASE IF NOT EXISTS `homi` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci */ /*!80016 DEFAULT ENCRYPTION='N' */;
USE `homi`;

-- Dumping structure for table homi.announcements
DROP TABLE IF EXISTS `announcements`;
CREATE TABLE IF NOT EXISTS `announcements` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `tenant_id` bigint unsigned DEFAULT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `category` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `content` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `image_path` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `published_at` timestamp NULL DEFAULT NULL,
  `start_at` timestamp NULL DEFAULT NULL,
  `end_at` timestamp NULL DEFAULT NULL,
  `created_by` bigint unsigned NOT NULL,
  `is_pinned` tinyint(1) NOT NULL DEFAULT '0',
  `is_public` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `announcements_created_by_foreign` (`created_by`),
  KEY `announcements_tenant_id_foreign` (`tenant_id`),
  CONSTRAINT `announcements_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `announcements_tenant_id_foreign` FOREIGN KEY (`tenant_id`) REFERENCES `tenants` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table homi.announcements: ~0 rows (approximately)
DELETE FROM `announcements`;

-- Dumping structure for table homi.app_notification_reads
DROP TABLE IF EXISTS `app_notification_reads`;
CREATE TABLE IF NOT EXISTS `app_notification_reads` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `notification_id` bigint unsigned NOT NULL,
  `read_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `app_notification_reads_user_id_notification_id_unique` (`user_id`,`notification_id`),
  KEY `app_notification_reads_user_id_index` (`user_id`),
  KEY `app_notification_reads_notification_id_index` (`notification_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table homi.app_notification_reads: ~0 rows (approximately)
DELETE FROM `app_notification_reads`;

-- Dumping structure for table homi.app_notifications
DROP TABLE IF EXISTS `app_notifications`;
CREATE TABLE IF NOT EXISTS `app_notifications` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `sent_by` bigint unsigned DEFAULT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `message` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'general',
  `data` json DEFAULT NULL,
  `read_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `app_notifications_sent_by_foreign` (`sent_by`),
  KEY `app_notifications_user_id_read_at_index` (`user_id`,`read_at`),
  KEY `app_notifications_user_id_created_at_index` (`user_id`,`created_at`),
  CONSTRAINT `app_notifications_sent_by_foreign` FOREIGN KEY (`sent_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `app_notifications_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table homi.app_notifications: ~0 rows (approximately)
DELETE FROM `app_notifications`;

-- Dumping structure for table homi.cache
DROP TABLE IF EXISTS `cache`;
CREATE TABLE IF NOT EXISTS `cache` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table homi.cache: ~0 rows (approximately)
DELETE FROM `cache`;

-- Dumping structure for table homi.cache_locks
DROP TABLE IF EXISTS `cache_locks`;
CREATE TABLE IF NOT EXISTS `cache_locks` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `owner` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table homi.cache_locks: ~0 rows (approximately)
DELETE FROM `cache_locks`;

-- Dumping structure for table homi.complaints
DROP TABLE IF EXISTS `complaints`;
CREATE TABLE IF NOT EXISTS `complaints` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `nama_pelapor` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tanggal_pengaduan` date NOT NULL,
  `tempat_kejadian` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `perihal` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `foto_path` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` enum('baru','diproses','selesai') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'baru',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `user_id` bigint unsigned DEFAULT NULL,
  `assigned_to` bigint unsigned DEFAULT NULL,
  `resolved_at` timestamp NULL DEFAULT NULL,
  `rt_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `rt_number` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `rw_number` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `complaints_user_id_foreign` (`user_id`),
  KEY `complaints_assigned_to_foreign` (`assigned_to`),
  CONSTRAINT `complaints_assigned_to_foreign` FOREIGN KEY (`assigned_to`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `complaints_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table homi.complaints: ~0 rows (approximately)
DELETE FROM `complaints`;

-- Dumping structure for table homi.failed_jobs
DROP TABLE IF EXISTS `failed_jobs`;
CREATE TABLE IF NOT EXISTS `failed_jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table homi.failed_jobs: ~0 rows (approximately)
DELETE FROM `failed_jobs`;

-- Dumping structure for table homi.fee_invoices
DROP TABLE IF EXISTS `fee_invoices`;
CREATE TABLE IF NOT EXISTS `fee_invoices` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `fee_type_id` bigint unsigned NOT NULL,
  `period` date NOT NULL,
  `amount` int unsigned NOT NULL,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'unpaid',
  `trx_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `issued_by` bigint unsigned DEFAULT NULL,
  `due_date` date DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `fee_invoices_user_id_fee_type_id_period_unique` (`user_id`,`fee_type_id`,`period`),
  KEY `fee_invoices_fee_type_id_foreign` (`fee_type_id`),
  KEY `fee_invoices_issued_by_foreign` (`issued_by`),
  KEY `fee_invoices_status_period_index` (`status`,`period`),
  CONSTRAINT `fee_invoices_fee_type_id_foreign` FOREIGN KEY (`fee_type_id`) REFERENCES `fee_types` (`id`) ON DELETE RESTRICT,
  CONSTRAINT `fee_invoices_issued_by_foreign` FOREIGN KEY (`issued_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `fee_invoices_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table homi.fee_invoices: ~0 rows (approximately)
DELETE FROM `fee_invoices`;

-- Dumping structure for table homi.fee_payments
DROP TABLE IF EXISTS `fee_payments`;
CREATE TABLE IF NOT EXISTS `fee_payments` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `invoice_id` bigint unsigned NOT NULL,
  `payer_user_id` bigint unsigned NOT NULL,
  `proof_path` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `note` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `review_status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `reviewed_by` bigint unsigned DEFAULT NULL,
  `reviewed_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fee_payments_invoice_id_foreign` (`invoice_id`),
  KEY `fee_payments_payer_user_id_foreign` (`payer_user_id`),
  KEY `fee_payments_reviewed_by_foreign` (`reviewed_by`),
  KEY `fee_payments_review_status_index` (`review_status`),
  CONSTRAINT `fee_payments_invoice_id_foreign` FOREIGN KEY (`invoice_id`) REFERENCES `fee_invoices` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fee_payments_payer_user_id_foreign` FOREIGN KEY (`payer_user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fee_payments_reviewed_by_foreign` FOREIGN KEY (`reviewed_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table homi.fee_payments: ~0 rows (approximately)
DELETE FROM `fee_payments`;

-- Dumping structure for table homi.fee_types
DROP TABLE IF EXISTS `fee_types`;
CREATE TABLE IF NOT EXISTS `fee_types` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `amount` decimal(15,2) NOT NULL DEFAULT '0.00',
  `is_recurring` tinyint(1) NOT NULL DEFAULT '0',
  `description` text COLLATE utf8mb4_unicode_ci,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `fee_types_name_unique` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table homi.fee_types: ~4 rows (approximately)
DELETE FROM `fee_types`;
INSERT INTO `fee_types` (`id`, `name`, `amount`, `is_recurring`, `description`, `is_active`, `created_at`, `updated_at`) VALUES
	(1, 'Iuran Sampah', 0.00, 0, NULL, 1, '2026-05-01 13:56:14', '2026-05-01 13:56:14'),
	(2, 'Iuran Keamanan', 0.00, 0, NULL, 1, '2026-05-01 13:56:14', '2026-05-01 13:56:14'),
	(3, 'Iuran Lingkungan', 0.00, 0, NULL, 1, '2026-05-01 13:56:14', '2026-05-01 13:56:14'),
	(4, 'Iuran Fasilitas Umum', 0.00, 0, NULL, 1, '2026-05-01 13:56:14', '2026-05-01 13:56:14');

-- Dumping structure for table homi.job_batches
DROP TABLE IF EXISTS `job_batches`;
CREATE TABLE IF NOT EXISTS `job_batches` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `total_jobs` int NOT NULL,
  `pending_jobs` int NOT NULL,
  `failed_jobs` int NOT NULL,
  `failed_job_ids` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `options` mediumtext COLLATE utf8mb4_unicode_ci,
  `cancelled_at` int DEFAULT NULL,
  `created_at` int NOT NULL,
  `finished_at` int DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table homi.job_batches: ~0 rows (approximately)
DELETE FROM `job_batches`;

-- Dumping structure for table homi.jobs
DROP TABLE IF EXISTS `jobs`;
CREATE TABLE IF NOT EXISTS `jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `queue` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `attempts` tinyint unsigned NOT NULL,
  `reserved_at` int unsigned DEFAULT NULL,
  `available_at` int unsigned NOT NULL,
  `created_at` int unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `jobs_queue_index` (`queue`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table homi.jobs: ~0 rows (approximately)
DELETE FROM `jobs`;

-- Dumping structure for table homi.letter_types
DROP TABLE IF EXISTS `letter_types`;
CREATE TABLE IF NOT EXISTS `letter_types` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `template_html` longtext COLLATE utf8mb4_unicode_ci,
  `required_json` json DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table homi.letter_types: ~5 rows (approximately)
DELETE FROM `letter_types`;
INSERT INTO `letter_types` (`id`, `name`, `description`, `template_html`, `required_json`, `created_at`, `updated_at`) VALUES
	(1, 'Surat Pengantar', 'Surat pengantar untuk berbagai keperluan warga ke RT/RW.', '<div style="text-align: center; font-family: sans-serif;">\n    <h1 style="text-decoration: underline; margin-bottom: 0; font-size: 18pt;">SURAT PENGANTAR</h1>\n    <div style="margin-top: 5px; font-size: 11pt;">Nomor: {{nomor_surat}}</div>\n</div>\n<div style="margin-top: 30px; line-height: 1.6; font-family: sans-serif; font-size: 11pt;">\n    Yang bertanda tangan di bawah ini Ketua RT {{rt}} RW {{rw}} {{nama_perumahan}}, dengan ini menerangkan bahwa:\n    <table style="width: 100%; margin-top: 15px; margin-left: 20px;">\n        <tr><td style="width: 180px;">Nama Lengkap</td><td>: <b>{{nama}}</b></td></tr>\n        <tr><td>NIK</td><td>: {{nik}}</td></tr>\n        <tr><td>Tempat/Tgl Lahir</td><td>: {{tmpt_tgl_lahir}}</td></tr>\n        <tr><td>Jenis Kelamin</td><td>: {{jenis_kelamin}}</td></tr>\n        <tr><td>Alamat</td><td>: {{alamat}}</td></tr>\n    </table>\n    <p style="text-indent: 40px;">Orang tersebut di atas merupakan warga kami di lingkungan RT {{rt}} RW {{rw}} {{nama_perumahan}}, dan berdasarkan pantauan kami yang bersangkutan berkelakuan baik.</p>\n    <p>Demikian surat pengantar ini dibuat dengan sebenar-benarnya untuk dipergunakan sebagai kelengkapan administrasi ke <b>{{tujuan_instansi}}</b> guna keperluan: <b>{{keperluan}}</b>.</p>\n</div>\n<div style="margin-top: 50px; float: right; text-align: center; font-family: sans-serif; font-size: 11pt;">\n    Batam, {{tanggal_surat}}<br>\n    <b>{{pj_label}} {{rt}}</b>\n    <br><br><br><br>\n    <b><u>{{nama_rt}}</u></b>\n</div>', NULL, '2026-05-01 13:56:14', '2026-05-01 13:56:14'),
	(2, 'Surat Keterangan Domisili', 'Surat keterangan tempat tinggal warga.', '<div style="text-align: center; font-family: sans-serif;">\n    <h1 style="text-decoration: underline; margin-bottom: 0; font-size: 18pt;">SURAT KETERANGAN DOMISILI</h1>\n    <div style="margin-top: 5px; font-size: 11pt;">Nomor: {{nomor_surat}}</div>\n</div>\n<div style="margin-top: 30px; line-height: 1.6; font-family: sans-serif; font-size: 11pt;">\n    Yang bertanda tangan di bawah ini Ketua RT {{rt}} RW {{rw}} {{nama_perumahan}}, dengan ini menerangkan bahwa:\n    <table style="width: 100%; margin-top: 15px; margin-left: 20px;">\n        <tr><td style="width: 180px;">Nama Lengkap</td><td>: <b>{{nama}}</b></td></tr>\n        <tr><td>NIK</td><td>: {{nik}}</td></tr>\n        <tr><td>Tempat/Tgl Lahir</td><td>: {{tmpt_tgl_lahir}}</td></tr>\n        <tr><td>Jenis Kelamin</td><td>: {{jenis_kelamin}}</td></tr>\n        <tr><td>Alamat Tinggal</td><td>: {{alamat}}</td></tr>\n    </table>\n    <p style="text-indent: 40px;">Nama tersebut di atas adalah benar warga kami dan bertempat tinggal di alamat tersebut (Domisili) di wilayah RT {{rt}} RW {{rw}} {{nama_perumahan}}.</p>\n    <p>Demikian surat keterangan ini dibuat dengan sebenar-benarnya agar dapat dipergunakan sebagaimana mestinya untuk keperluan: <strong>{{keperluan}}</strong>.</p>\n</div>\n<div style="margin-top: 50px; float: right; text-align: center; font-family: sans-serif; font-size: 11pt;">\n    Batam, {{tanggal_surat}}<br>\n    <b>{{pj_label}} {{rt}}</b>\n    <br><br><br><br>\n    <b><u>{{nama_rt}}</u></b>\n</div>', NULL, '2026-05-01 13:56:14', '2026-05-01 13:56:14'),
	(3, 'Surat Keterangan Kematian', 'Surat keterangan pelaporan kematian warga.', '<div style="text-align: center; font-family: sans-serif;">\n    <h1 style="text-decoration: underline; margin-bottom: 0; font-size: 18pt;">SURAT KETERANGAN KEMATIAN</h1>\n    <div style="margin-top: 5px; font-size: 11pt;">Nomor: {{nomor_surat}}</div>\n</div>\n<div style="margin-top: 30px; line-height: 1.6; font-family: sans-serif; font-size: 11pt;">\n    Ketua RT {{rt}} RW {{rw}} {{nama_perumahan}}, menerangkan bahwa telah meninggal dunia:\n    <table style="width: 100%; margin-top: 15px; margin-left: 20px;">\n        <tr><td style="width: 180px;">Nama Almarhum/ah</td><td>: <b>{{nama_alm}}</b></td></tr>\n        <tr><td>NIK</td><td>: {{nik_alm}}</td></tr>\n        <tr><td>Tempat/Tgl Meninggal</td><td>: {{tmpt_tgl_meninggal_alm}}</td></tr>\n        <tr><td>Penyebab</td><td>: {{penyebab}}</td></tr>\n        <tr><td>Alamat Terakhir</td><td>: {{alamat_alm}}</td></tr>\n    </table>\n    <p style="text-indent: 40px;">Demikian surat keterangan kematian ini dibuat dengan sebenar-benarnya berdasarkan pelaporan dari <strong>{{nama_pelapor}}</strong> ({{hubungan}}) agar dapat dipergunakan sebagaimana mestinya.</p>\n</div>\n<div style="margin-top: 50px; float: right; text-align: center; font-family: sans-serif; font-size: 11pt;">\n    Batam, {{tanggal_surat}}<br>\n    <b>{{pj_label}} {{rt}}</b>\n    <br><br><br><br>\n    <b><u>{{nama_rt}}</u></b>\n</div>', NULL, '2026-05-01 13:56:14', '2026-05-01 13:56:14'),
	(4, 'Surat Keterangan Usaha', 'Surat keterangan untuk pembukaan atau kepemilikan usaha.', '<div style="text-align: center; font-family: sans-serif;">\n    <h1 style="text-decoration: underline; margin-bottom: 0; font-size: 18pt;">SURAT KETERANGAN USAHA</h1>\n    <div style="margin-top: 5px; font-size: 11pt;">Nomor: {{nomor_surat}}</div>\n</div>\n<div style="margin-top: 30px; line-height: 1.6; font-family: sans-serif; font-size: 11pt;">\n    Yang bertanda tangan di bawah ini Ketua RT {{rt}} RW {{rw}} {{nama_perumahan}}, dengan ini menerangkan bahwa:\n    <table style="width: 100%; margin-top: 15px; margin-left: 20px;">\n        <tr><td style="width: 180px;">Nama Lengkap</td><td>: <b>{{nama}}</b></td></tr>\n        <tr><td>NIK</td><td>: {{nik}}</td></tr>\n        <tr><td>Tempat/Tgl Lahir</td><td>: {{tmpt_tgl_lahir}}</td></tr>\n        <tr><td>Alamat</td><td>: {{alamat}}</td></tr>\n    </table>\n    <p>Bahwa yang bersangkutan benar memiliki/menjalankan usaha dengan rincian berikut:</p>\n    <table style="width: 100%; margin-left: 20px;">\n        <tr><td style="width: 180px;">Nama Usaha</td><td>: <b>{{nama_usaha}}</b></td></tr>\n        <tr><td>Bidang Usaha</td><td>: {{bidang_usaha}}</td></tr>\n        <tr><td>Alamat Usaha</td><td>: {{alamat_usaha}}</td></tr>\n    </table>\n    <p>Demikian surat keterangan ini dibuat agar dapat dipergunakan sebagaimana mestinya untuk keperluan: <b>{{keperluan}}</b>.</p>\n</div>\n<div style="margin-top: 50px; float: right; text-align: center; font-family: sans-serif; font-size: 11pt;">\n    Batam, {{tanggal_surat}}<br>\n    <b>{{pj_label}} {{rt}}</b>\n    <br><br><br><br>\n    <b><u>{{nama_rt}}</u></b>\n</div>', NULL, '2026-05-01 13:56:14', '2026-05-01 13:56:14'),
	(5, 'Surat Keterangan Belum Menikah', 'Surat pernyataan status pernikahan warga.', '<div style="text-align: center; font-family: sans-serif;">\n    <h1 style="text-decoration: underline; margin-bottom: 0; font-size: 18pt;">SURAT KETERANGAN BELUM MENIKAH</h1>\n    <div style="margin-top: 5px; font-size: 11pt;">Nomor: {{nomor_surat}}</div>\n</div>\n<div style="margin-top: 30px; line-height: 1.6; font-family: sans-serif; font-size: 11pt;">\n    Yang bertanda tangan di bawah ini Ketua RT {{rt}} RW {{rw}} {{nama_perumahan}}, dengan ini menerangkan bahwa:\n    <table style="width: 100%; margin-top: 15px; margin-left: 20px;">\n        <tr><td style="width: 180px;">Nama Lengkap</td><td>: <b>{{nama}}</b></td></tr>\n        <tr><td>NIK</td><td>: {{nik}}</td></tr>\n        <tr><td>Tempat/Tgl Lahir</td><td>: {{tmpt_tgl_lahir}}</td></tr>\n        <tr><td>Jenis Kelamin</td><td>: {{jenis_kelamin}}</td></tr>\n        <tr><td>Agama</td><td>: {{agama}}</td></tr>\n        <tr><td>Alamat</td><td>: {{alamat}}</td></tr>\n    </table>\n    <p style="text-indent: 40px;">Berdasarkan keterangan yang ada pada kami dan sepanjang sepengetahuan kami, nama tersebut di atas adalah benar warga kami yang sampai saat ini <strong>Belum Pernah Menikah</strong> (Jejaka/Perawan).</p>\n    <p>Demikian surat keterangan ini dibuat dengan sebenar-benarnya agar dapat dipergunakan sebagaimana mestinya untuk keperluan: <strong>{{keperluan}}</strong> ke <strong>{{tujuan_instansi}}</strong>.</p>\n</div>\n<div style="margin-top: 50px; float: right; text-align: center; font-family: sans-serif; font-size: 11pt;">\n    Batam, {{tanggal_surat}}<br>\n    <b>{{pj_label}} {{rt}}</b>\n    <br><br><br><br>\n    <b><u>{{nama_rt}}</u></b>\n</div>', NULL, '2026-05-01 13:56:14', '2026-05-01 13:56:14');

-- Dumping structure for table homi.migrations
DROP TABLE IF EXISTS `migrations`;
CREATE TABLE IF NOT EXISTS `migrations` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=49 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table homi.migrations: ~48 rows (approximately)
DELETE FROM `migrations`;
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
	(1, '0001_01_01_000000_create_users_table', 1),
	(2, '0001_01_01_000001_create_cache_table', 1),
	(3, '0001_01_01_000002_create_jobs_table', 1),
	(4, '2025_12_02_090105_create_personal_access_tokens_table', 1),
	(5, '2025_12_04_154911_add_otp_fields_to_users_table', 1),
	(6, '2025_12_05_024012_create_complaints_table', 1),
	(7, '2025_12_05_040657_add_user_id_to_complaints_table', 1),
	(8, '2025_12_05_161444_add_role_to_users_table', 1),
	(9, '2025_12_05_161649_create_announcements_table', 1),
	(10, '2025_12_06_163935_add_image_to_announcements_table', 1),
	(11, '2025_12_12_091139_add_otp_purpose_to_users_table', 1),
	(12, '2025_12_12_141646_add_reset_token_to_users_table', 1),
	(13, '2025_12_12_161225_create_resident_profiles_table', 1),
	(14, '2025_12_13_060147_create_request_types_table', 1),
	(15, '2025_12_13_060224_create_service_request_table', 1),
	(16, '2025_12_15_144600_create_fee_types_table', 1),
	(17, '2025_12_15_144810_create_payment_qr_codes_table', 1),
	(18, '2025_12_15_144905_create_fee_invoices_table', 1),
	(19, '2025_12_15_144952_create_fee_payments_table', 1),
	(20, '2025_12_24_235959_create_letter_types_table', 1),
	(21, '2025_12_25_000001_add_pdf_fields_to_service_requests_table', 1),
	(22, '2025_12_25_000002_add_letter_type_id_to_request_types_table', 1),
	(23, '2026_01_04_000001_create_app_notifications_table', 1),
	(24, '2026_01_06_000001_create_ml_nb_models_table', 1),
	(25, '2026_01_06_000004_create_payment_risk_scores_table', 1),
	(26, '2026_01_06_000005_create_app_notification_reads_table', 1),
	(27, '2026_01_06_182033_ensure_ml_nb_models_table_exists', 1),
	(28, '2026_01_10_210220_add_google_id_to_users_table', 1),
	(29, '2026_03_23_120000_sync_users_table_for_admin_ui', 1),
	(30, '2026_03_23_170500_add_admin_fields_to_complaints_table', 1),
	(31, '2026_03_25_230000_add_fcm_token_to_users_table', 1),
	(32, '2026_03_27_000000_add_extra_fields_to_resident_profiles', 1),
	(33, '2026_03_27_000001_add_rt_rw_to_resident_profiles_table', 1),
	(34, '2026_03_27_000002_add_signature_info_to_service_requests_table', 1),
	(35, '2026_03_27_000003_add_signature_info_to_complaints_table', 1),
	(36, '2026_03_28_164155_add_house_type_to_resident_profiles_table', 1),
	(37, '2026_03_28_164559_add_profile_photo_path_to_users_table', 1),
	(38, '2026_03_28_171000_refactor_birth_fields_in_resident_profiles', 1),
	(39, '2026_04_08_014424_add_visibility_fields_to_announcements_table', 1),
	(40, '2026_04_08_141308_align_tenant_schemas', 1),
	(41, '2026_04_23_134616_add_notified_at_to_payment_risk_scores', 1),
	(42, '2026_04_23_135135_change_period_to_date_in_payment_risk_scores', 1),
	(43, '2026_03_24_000100_create_tenants_table', 2),
	(44, '2026_03_26_000000_add_registration_code_to_tenants', 2),
	(45, '2026_03_28_000000_add_tenant_id_to_users_table', 2),
	(46, '2026_03_28_170000_add_tenant_id_to_announcements_table', 2),
	(47, '2026_04_03_000000_add_subscription_fields_to_tenants_table', 2),
	(48, '2026_04_04_000000_create_tenant_requests_table', 2);

-- Dumping structure for table homi.ml_nb_models
DROP TABLE IF EXISTS `ml_nb_models`;
CREATE TABLE IF NOT EXISTS `ml_nb_models` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(120) COLLATE utf8mb4_unicode_ci NOT NULL,
  `model_json` json NOT NULL,
  `trained_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `ml_nb_models_name_index` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table homi.ml_nb_models: ~0 rows (approximately)
DELETE FROM `ml_nb_models`;

-- Dumping structure for table homi.password_reset_tokens
DROP TABLE IF EXISTS `password_reset_tokens`;
CREATE TABLE IF NOT EXISTS `password_reset_tokens` (
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table homi.password_reset_tokens: ~0 rows (approximately)
DELETE FROM `password_reset_tokens`;

-- Dumping structure for table homi.payment_qr_codes
DROP TABLE IF EXISTS `payment_qr_codes`;
CREATE TABLE IF NOT EXISTS `payment_qr_codes` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `image_path` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '0',
  `updated_by` bigint unsigned DEFAULT NULL,
  `notes` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `payment_qr_codes_updated_by_foreign` (`updated_by`),
  KEY `payment_qr_codes_is_active_index` (`is_active`),
  CONSTRAINT `payment_qr_codes_updated_by_foreign` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table homi.payment_qr_codes: ~0 rows (approximately)
DELETE FROM `payment_qr_codes`;

-- Dumping structure for table homi.payment_risk_scores
DROP TABLE IF EXISTS `payment_risk_scores`;
CREATE TABLE IF NOT EXISTS `payment_risk_scores` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `period` date NOT NULL,
  `risk` decimal(6,4) NOT NULL DEFAULT '0.0000',
  `predicted_delinquent` tinyint(1) NOT NULL DEFAULT '0',
  `features_json` json DEFAULT NULL,
  `computed_at` timestamp NULL DEFAULT NULL,
  `notified_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `payment_risk_scores_user_id_period_unique` (`user_id`,`period`),
  KEY `payment_risk_scores_user_id_index` (`user_id`),
  KEY `payment_risk_scores_period_index` (`period`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table homi.payment_risk_scores: ~0 rows (approximately)
DELETE FROM `payment_risk_scores`;

-- Dumping structure for table homi.personal_access_tokens
DROP TABLE IF EXISTS `personal_access_tokens`;
CREATE TABLE IF NOT EXISTS `personal_access_tokens` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `tokenable_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tokenable_id` bigint unsigned NOT NULL,
  `name` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `abilities` text COLLATE utf8mb4_unicode_ci,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`),
  KEY `personal_access_tokens_expires_at_index` (`expires_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table homi.personal_access_tokens: ~0 rows (approximately)
DELETE FROM `personal_access_tokens`;

-- Dumping structure for table homi.request_types
DROP TABLE IF EXISTS `request_types`;
CREATE TABLE IF NOT EXISTS `request_types` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `icon` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `letter_type_id` int unsigned DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `request_types_letter_type_id_foreign` (`letter_type_id`),
  CONSTRAINT `request_types_letter_type_id_foreign` FOREIGN KEY (`letter_type_id`) REFERENCES `letter_types` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table homi.request_types: ~8 rows (approximately)
DELETE FROM `request_types`;
INSERT INTO `request_types` (`id`, `name`, `description`, `icon`, `letter_type_id`, `is_active`, `created_at`, `updated_at`) VALUES
	(1, 'Surat Pengantar', NULL, NULL, 1, 1, '2026-05-01 13:56:14', '2026-05-01 13:56:14'),
	(2, 'Perbaikan Fasilitas', NULL, NULL, NULL, 1, '2026-05-01 13:56:14', '2026-05-01 13:56:14'),
	(3, 'Peminjaman Fasilitas', NULL, NULL, NULL, 1, '2026-05-01 13:56:14', '2026-05-01 13:56:14'),
	(4, 'Pengajuan Layanan', NULL, NULL, NULL, 1, '2026-05-01 13:56:14', '2026-05-01 13:56:14'),
	(5, 'Surat Domisili', NULL, NULL, 2, 1, '2026-05-01 13:56:14', '2026-05-01 13:56:14'),
	(6, 'Surat Kematian', NULL, NULL, 3, 1, '2026-05-01 13:56:14', '2026-05-01 13:56:14'),
	(7, 'Surat Keterangan Usaha', NULL, NULL, 4, 1, '2026-05-01 13:56:14', '2026-05-01 13:56:14'),
	(8, 'Surat Belum Menikah', NULL, NULL, 5, 1, '2026-05-01 13:56:14', '2026-05-01 13:56:14');

-- Dumping structure for table homi.resident_profiles
DROP TABLE IF EXISTS `resident_profiles`;
CREATE TABLE IF NOT EXISTS `resident_profiles` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `nik` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `house_type` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `blok` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `no_rumah` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `rt` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `rw` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `alamat` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `nama_rt` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `no_rt` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `no_rw` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `pekerjaan` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tempat_lahir` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tanggal_lahir` date DEFAULT NULL,
  `tempat_tanggal_lahir` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `jenis_kelamin` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_public` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `resident_profiles_user_id_unique` (`user_id`),
  CONSTRAINT `resident_profiles_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table homi.resident_profiles: ~0 rows (approximately)
DELETE FROM `resident_profiles`;

-- Dumping structure for table homi.service_requests
DROP TABLE IF EXISTS `service_requests`;
CREATE TABLE IF NOT EXISTS `service_requests` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `request_type_id` bigint unsigned NOT NULL,
  `reporter_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `request_date` date NOT NULL,
  `place` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `subject` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `data_input` json DEFAULT NULL,
  `status` enum('submitted','processed','approved','rejected') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'submitted',
  `admin_note` text COLLATE utf8mb4_unicode_ci,
  `rt_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `rt_number` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `rw_number` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `verified_by` bigint unsigned DEFAULT NULL,
  `verified_at` timestamp NULL DEFAULT NULL,
  `pdf_path` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `service_requests_verified_by_foreign` (`verified_by`),
  KEY `service_requests_user_id_status_index` (`user_id`,`status`),
  KEY `service_requests_request_type_id_status_index` (`request_type_id`,`status`),
  CONSTRAINT `service_requests_request_type_id_foreign` FOREIGN KEY (`request_type_id`) REFERENCES `request_types` (`id`),
  CONSTRAINT `service_requests_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `service_requests_verified_by_foreign` FOREIGN KEY (`verified_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table homi.service_requests: ~0 rows (approximately)
DELETE FROM `service_requests`;

-- Dumping structure for table homi.sessions
DROP TABLE IF EXISTS `sessions`;
CREATE TABLE IF NOT EXISTS `sessions` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint unsigned DEFAULT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text COLLATE utf8mb4_unicode_ci,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_activity` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `sessions_user_id_index` (`user_id`),
  KEY `sessions_last_activity_index` (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table homi.sessions: ~0 rows (approximately)
DELETE FROM `sessions`;

-- Dumping structure for table homi.tenant_requests
DROP TABLE IF EXISTS `tenant_requests`;
CREATE TABLE IF NOT EXISTS `tenant_requests` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `manager_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table homi.tenant_requests: ~1 rows (approximately)
DELETE FROM `tenant_requests`;
INSERT INTO `tenant_requests` (`id`, `name`, `manager_name`, `email`, `phone`, `status`, `notes`, `created_at`, `updated_at`) VALUES
	(1, 'Central Melati', 'abyadhanif', 'hanipabyad79@gmail.com', '081992440287', 'approved', '2000 unit', '2026-05-02 13:47:46', '2026-05-02 13:55:15');

-- Dumping structure for table homi.tenants
DROP TABLE IF EXISTS `tenants`;
CREATE TABLE IF NOT EXISTS `tenants` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `registration_code` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `code` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `domain` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `db_driver` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'mysql',
  `db_host` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `db_port` int unsigned NOT NULL DEFAULT '3306',
  `db_database` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `db_username` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `db_password` text COLLATE utf8mb4_unicode_ci,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `plan` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'trial',
  `trial_ends_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `tenants_code_unique` (`code`),
  UNIQUE KEY `tenants_domain_unique` (`domain`),
  KEY `tenants_is_active_index` (`is_active`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table homi.tenants: ~2 rows (approximately)
DELETE FROM `tenants`;
INSERT INTO `tenants` (`id`, `name`, `registration_code`, `code`, `domain`, `db_driver`, `db_host`, `db_port`, `db_database`, `db_username`, `db_password`, `is_active`, `plan`, `trial_ends_at`, `created_at`, `updated_at`) VALUES
	(1, 'Hawaii Garden', 'HWG123', 'hawaii-garden', NULL, 'mysql', '127.0.0.1', 3306, 'homi_hawaii_db', 'root', NULL, 1, 'elite', NULL, '2026-05-01 13:56:14', '2026-05-02 13:57:19'),
	(5, 'Central Melati', 'CENTRAL', 'central-melati', NULL, 'mysql', 'db', 3306, 'homi_central_melati_db', 'homi', 'homipassword', 1, 'trial', '2026-06-01 14:08:22', '2026-05-02 14:08:22', '2026-05-02 14:08:22');

-- Dumping structure for table homi.users
DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `full_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `username` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `google_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `is_verified` tinyint(1) NOT NULL DEFAULT '0',
  `otp_code` varchar(6) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `otp_purpose` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `reset_token` varchar(64) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `reset_token_expires_at` datetime DEFAULT NULL,
  `otp_expires_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `fcm_token` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `profile_photo_path` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `password_hash` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `role` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'resident',
  `tenant_id` bigint unsigned DEFAULT NULL,
  `role_id` bigint unsigned DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`),
  KEY `users_role_id_index` (`role_id`),
  KEY `users_tenant_id_index` (`tenant_id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table homi.users: ~3 rows (approximately)
DELETE FROM `users`;
INSERT INTO `users` (`id`, `name`, `full_name`, `username`, `email`, `phone`, `google_id`, `email_verified_at`, `is_verified`, `otp_code`, `otp_purpose`, `reset_token`, `reset_token_expires_at`, `otp_expires_at`, `password`, `fcm_token`, `profile_photo_path`, `password_hash`, `remember_token`, `created_at`, `updated_at`, `role`, `tenant_id`, `role_id`, `is_active`) VALUES
	(1, 'Super Admin Homi', NULL, NULL, 'admin@homi.id', NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, '$2y$12$XCxW2qd5nxIze8ahjyER0u6CVndwegzJDai/DuAFuB8o1ogQQj0cW', NULL, NULL, '$2y$12$XCxW2qd5nxIze8ahjyER0u6CVndwegzJDai/DuAFuB8o1ogQQj0cW', NULL, '2026-05-01 13:55:41', '2026-05-01 13:56:14', 'superadmin', NULL, NULL, 1),
	(2, 'HANIF', 'HANIF ABYAD', 'abyadhanif2@gmail.com', 'abyadhanif2@gmail.com', NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, '$2y$12$x6alc6Q1m4Wttw0MUTlNFObl7q6.bL5qHcIotNnUjXFw/20r.mwv6', NULL, NULL, '$2y$12$x6alc6Q1m4Wttw0MUTlNFObl7q6.bL5qHcIotNnUjXFw/20r.mwv6', NULL, '2026-05-02 13:45:16', '2026-05-02 14:13:07', 'admin', 1, 1, 1),
	(3, 'hanifbayad71', 'hanip', 'hanifabyad71@gmail.com', 'hanifabyad71@gmail.com', NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, '$2y$12$Kux1RC9wgJtIMo6dl4yZrui1gU4zgFicaaVJHkewGKyecQ.M89jZy', NULL, NULL, '$2y$12$Kux1RC9wgJtIMo6dl4yZrui1gU4zgFicaaVJHkewGKyecQ.M89jZy', NULL, '2026-05-02 13:56:29', '2026-05-02 14:12:05', 'admin', 5, 1, 1);

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;