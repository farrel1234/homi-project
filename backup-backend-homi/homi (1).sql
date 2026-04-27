-- phpMyAdmin SQL Dump
-- version 6.0.0-dev+20260305.a34bb65806
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Apr 15, 2026 at 09:38 AM
-- Server version: 8.4.3
-- PHP Version: 8.3.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `homi`
--

-- --------------------------------------------------------

--
-- Table structure for table `announcements`
--

CREATE TABLE `announcements` (
  `id` bigint UNSIGNED NOT NULL,
  `tenant_id` bigint UNSIGNED DEFAULT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `category` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `content` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `image_path` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `published_at` timestamp NULL DEFAULT NULL,
  `start_at` timestamp NULL DEFAULT NULL,
  `end_at` timestamp NULL DEFAULT NULL,
  `created_by` bigint UNSIGNED NOT NULL,
  `is_pinned` tinyint(1) NOT NULL DEFAULT '0',
  `is_public` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `app_notifications`
--

CREATE TABLE `app_notifications` (
  `id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `sent_by` bigint UNSIGNED DEFAULT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `message` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'general',
  `data` json DEFAULT NULL,
  `read_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `app_notification_reads`
--

CREATE TABLE `app_notification_reads` (
  `id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `notification_id` bigint UNSIGNED NOT NULL,
  `read_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cache`
--

CREATE TABLE `cache` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cache_locks`
--

CREATE TABLE `cache_locks` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `owner` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `complaints`
--

CREATE TABLE `complaints` (
  `id` bigint UNSIGNED NOT NULL,
  `nama_pelapor` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tanggal_pengaduan` date NOT NULL,
  `tempat_kejadian` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `perihal` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `foto_path` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` enum('baru','diproses','selesai') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'baru',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `user_id` bigint UNSIGNED DEFAULT NULL,
  `assigned_to` bigint UNSIGNED DEFAULT NULL,
  `resolved_at` timestamp NULL DEFAULT NULL,
  `rt_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `rt_number` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `rw_number` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint UNSIGNED NOT NULL,
  `uuid` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `fee_invoices`
--

CREATE TABLE `fee_invoices` (
  `id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `fee_type_id` bigint UNSIGNED NOT NULL,
  `period` date NOT NULL,
  `amount` int UNSIGNED NOT NULL,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'unpaid',
  `trx_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `issued_by` bigint UNSIGNED DEFAULT NULL,
  `due_date` date DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `fee_payments`
--

CREATE TABLE `fee_payments` (
  `id` bigint UNSIGNED NOT NULL,
  `invoice_id` bigint UNSIGNED NOT NULL,
  `payer_user_id` bigint UNSIGNED NOT NULL,
  `proof_path` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `note` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `review_status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `reviewed_by` bigint UNSIGNED DEFAULT NULL,
  `reviewed_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `fee_types`
--

CREATE TABLE `fee_types` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `amount` decimal(15,2) NOT NULL DEFAULT '0.00',
  `is_recurring` tinyint(1) NOT NULL DEFAULT '0',
  `description` text COLLATE utf8mb4_unicode_ci,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `fee_types`
--

INSERT INTO `fee_types` (`id`, `name`, `amount`, `is_recurring`, `description`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'Iuran Sampah', 0.00, 0, NULL, 1, '2026-04-04 09:43:09', '2026-04-04 09:43:09'),
(2, 'Iuran Keamanan', 0.00, 0, NULL, 1, '2026-04-04 09:43:09', '2026-04-04 09:43:09'),
(3, 'Iuran Lingkungan', 0.00, 0, NULL, 1, '2026-04-04 09:43:09', '2026-04-04 09:43:09'),
(4, 'Iuran Fasilitas Umum', 0.00, 0, NULL, 1, '2026-04-04 09:43:09', '2026-04-04 09:43:09');

-- --------------------------------------------------------

--
-- Table structure for table `jobs`
--

CREATE TABLE `jobs` (
  `id` bigint UNSIGNED NOT NULL,
  `queue` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `attempts` tinyint UNSIGNED NOT NULL,
  `reserved_at` int UNSIGNED DEFAULT NULL,
  `available_at` int UNSIGNED NOT NULL,
  `created_at` int UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `job_batches`
--

CREATE TABLE `job_batches` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `total_jobs` int NOT NULL,
  `pending_jobs` int NOT NULL,
  `failed_jobs` int NOT NULL,
  `failed_job_ids` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `options` mediumtext COLLATE utf8mb4_unicode_ci,
  `cancelled_at` int DEFAULT NULL,
  `created_at` int NOT NULL,
  `finished_at` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `letter_types`
--

CREATE TABLE `letter_types` (
  `id` int UNSIGNED NOT NULL,
  `name` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `template_html` longtext COLLATE utf8mb4_unicode_ci,
  `required_json` json DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `letter_types`
--

INSERT INTO `letter_types` (`id`, `name`, `description`, `template_html`, `required_json`, `created_at`, `updated_at`) VALUES
(1, 'Surat Pengantar', 'Surat pengantar untuk berbagai keperluan warga ke RT/RW.', '<h1>Surat Pengantar</h1><p>Diberikan kepada {{nama}}...</p>', NULL, '2026-04-07 05:25:00', '2026-04-07 05:25:00'),
(2, 'Surat Keterangan Domisili', 'Surat keterangan tempat tinggal warga.', '<h1>Surat Keterangan Domisili</h1><p>Menerangkan bahwa {{nama}} tinggal di {{alamat}}...</p>', NULL, '2026-04-07 05:25:00', '2026-04-07 05:25:00'),
(3, 'Surat Keterangan Kematian', 'Surat keterangan pelaporan kematian warga.', '<h1>Surat Keterangan Kematian</h1><p>Menerangkan bahwa {{nama}} telah wafat...</p>', NULL, '2026-04-07 05:25:00', '2026-04-07 05:25:00'),
(4, 'Surat Keterangan Usaha', 'Surat keterangan untuk pembukaan atau kepemilikan usaha.', '<h1>Surat Keterangan Usaha</h1><p>Menerangkan bahwa {{nama}} memiliki usaha {{namaUsaha}}...</p>', NULL, '2026-04-07 05:25:00', '2026-04-07 05:25:00'),
(5, 'Surat Keterangan Belum Menikah', 'Surat pernyataan status pernikahan warga.', '<h1>Surat Keterangan Belum Menikah</h1><p>Menerangkan bahwa {{nama}} belum pernah menikah...</p>', NULL, '2026-04-07 05:25:00', '2026-04-07 05:25:00');

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int UNSIGNED NOT NULL,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '0001_01_01_000001_create_cache_table', 1),
(2, '0001_01_01_000000_create_users_table', 2),
(3, '0001_01_01_000002_create_jobs_table', 2),
(4, '2025_12_02_090105_create_personal_access_tokens_table', 2),
(5, '2025_12_04_154911_add_otp_fields_to_users_table', 2),
(6, '2025_12_05_024012_create_complaints_table', 2),
(7, '2025_12_05_040657_add_user_id_to_complaints_table', 2),
(8, '2025_12_05_161444_add_role_to_users_table', 2),
(9, '2025_12_05_161649_create_announcements_table', 2),
(10, '2025_12_06_163935_add_image_to_announcements_table', 2),
(11, '2025_12_12_091139_add_otp_purpose_to_users_table', 2),
(12, '2025_12_12_141646_add_reset_token_to_users_table', 2),
(13, '2025_12_12_161225_create_resident_profiles_table', 2),
(14, '2025_12_13_060147_create_request_types_table', 2),
(15, '2025_12_13_060224_create_service_request_table', 2),
(16, '2025_12_15_144600_create_fee_types_table', 2),
(17, '2025_12_15_144810_create_payment_qr_codes_table', 2),
(18, '2025_12_15_144905_create_fee_invoices_table', 2),
(19, '2025_12_15_144952_create_fee_payments_table', 2),
(20, '2025_12_24_235959_create_letter_types_table', 2),
(21, '2025_12_25_000001_add_pdf_fields_to_service_requests_table', 2),
(22, '2025_12_25_000002_add_letter_type_id_to_request_types_table', 2),
(23, '2026_01_04_000001_create_app_notifications_table', 2),
(24, '2026_01_06_000001_create_ml_nb_models_table', 2),
(25, '2026_01_06_000004_create_payment_risk_scores_table', 2),
(26, '2026_01_06_000005_create_app_notification_reads_table', 2),
(27, '2026_01_06_182033_ensure_ml_nb_models_table_exists', 2),
(28, '2026_01_10_210220_add_google_id_to_users_table', 2),
(29, '2026_03_23_120000_sync_users_table_for_admin_ui', 2),
(30, '2026_03_23_170500_add_admin_fields_to_complaints_table', 2),
(31, '2026_03_24_000100_create_tenants_table', 2),
(32, '2026_03_25_230000_add_fcm_token_to_users_table', 2),
(33, '2026_03_26_000000_add_registration_code_to_tenants', 2),
(34, '2026_03_27_000000_add_extra_fields_to_resident_profiles', 2),
(35, '2026_03_27_000001_add_rt_rw_to_resident_profiles_table', 2),
(36, '2026_03_27_000002_add_signature_info_to_service_requests_table', 2),
(37, '2026_03_27_000003_add_signature_info_to_complaints_table', 2),
(38, '2026_03_28_000000_add_tenant_id_to_users_table', 2),
(39, '2026_03_28_164155_add_house_type_to_resident_profiles_table', 2),
(40, '2026_03_28_164559_add_profile_photo_path_to_users_table', 2),
(41, '2026_03_28_170000_add_tenant_id_to_announcements_table', 2),
(42, '2026_03_28_171000_refactor_birth_fields_in_resident_profiles', 2),
(43, '2026_04_03_000000_add_subscription_fields_to_tenants_table', 2),
(44, '2026_04_04_000000_create_tenant_requests_table', 2),
(45, '2026_04_08_014424_add_visibility_fields_to_announcements_table', 3),
(46, '2026_04_08_141308_align_tenant_schemas', 4);

-- --------------------------------------------------------

--
-- Table structure for table `ml_nb_models`
--

CREATE TABLE `ml_nb_models` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(120) COLLATE utf8mb4_unicode_ci NOT NULL,
  `model_json` json NOT NULL,
  `trained_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `payment_qr_codes`
--

CREATE TABLE `payment_qr_codes` (
  `id` bigint UNSIGNED NOT NULL,
  `image_path` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '0',
  `updated_by` bigint UNSIGNED DEFAULT NULL,
  `notes` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `payment_risk_scores`
--

CREATE TABLE `payment_risk_scores` (
  `id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `period` varchar(7) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `risk` decimal(6,4) NOT NULL DEFAULT '0.0000',
  `predicted_delinquent` tinyint(1) NOT NULL DEFAULT '0',
  `features_json` json DEFAULT NULL,
  `computed_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `personal_access_tokens`
--

CREATE TABLE `personal_access_tokens` (
  `id` bigint UNSIGNED NOT NULL,
  `tokenable_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tokenable_id` bigint UNSIGNED NOT NULL,
  `name` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `abilities` text COLLATE utf8mb4_unicode_ci,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `request_types`
--

CREATE TABLE `request_types` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `icon` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `letter_type_id` int UNSIGNED DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `request_types`
--

INSERT INTO `request_types` (`id`, `name`, `description`, `icon`, `letter_type_id`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'Surat Pengantar', NULL, NULL, 1, 1, '2026-04-04 09:43:09', '2026-04-07 05:25:01'),
(2, 'Perbaikan Fasilitas', NULL, NULL, NULL, 1, '2026-04-04 09:43:09', '2026-04-07 05:25:01'),
(3, 'Peminjaman Fasilitas', NULL, NULL, NULL, 1, '2026-04-04 09:43:09', '2026-04-07 05:25:01'),
(4, 'Pengajuan Layanan', NULL, NULL, NULL, 1, '2026-04-04 09:43:09', '2026-04-07 05:25:01'),
(5, 'Surat Domisili', NULL, NULL, 2, 1, '2026-04-07 05:25:01', '2026-04-07 05:25:01'),
(6, 'Surat Kematian', NULL, NULL, 3, 1, '2026-04-07 05:25:01', '2026-04-07 05:25:01'),
(7, 'Surat Keterangan Usaha', NULL, NULL, 4, 1, '2026-04-07 05:25:01', '2026-04-07 05:25:01'),
(8, 'Surat Belum Menikah', NULL, NULL, 5, 1, '2026-04-07 05:25:01', '2026-04-07 05:25:01');

-- --------------------------------------------------------

--
-- Table structure for table `resident_profiles`
--

CREATE TABLE `resident_profiles` (
  `id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
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
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `service_requests`
--

CREATE TABLE `service_requests` (
  `id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `request_type_id` bigint UNSIGNED NOT NULL,
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
  `verified_by` bigint UNSIGNED DEFAULT NULL,
  `verified_at` timestamp NULL DEFAULT NULL,
  `pdf_path` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `sessions`
--

CREATE TABLE `sessions` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text COLLATE utf8mb4_unicode_ci,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_activity` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tenants`
--

CREATE TABLE `tenants` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `registration_code` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `code` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `domain` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `db_driver` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'mysql',
  `db_host` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `db_port` int UNSIGNED NOT NULL DEFAULT '3306',
  `db_database` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `db_username` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `db_password` text COLLATE utf8mb4_unicode_ci,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `plan` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'trial',
  `trial_ends_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `tenants`
--

INSERT INTO `tenants` (`id`, `name`, `registration_code`, `code`, `domain`, `db_driver`, `db_host`, `db_port`, `db_database`, `db_username`, `db_password`, `is_active`, `plan`, `trial_ends_at`, `created_at`, `updated_at`) VALUES
(1, 'Hawaii Garden', 'HAWAII', 'hawaii-garden', NULL, 'mysql', '127.0.0.1', 3306, 'homi_hawaii_db', 'root', NULL, 1, 'elite', NULL, '2026-04-04 09:44:22', '2026-04-07 03:42:20'),
(2, 'Taman Lembah Hijau', 'LEMBAH', 'lembah-hijau', NULL, 'mysql', '127.0.0.1', 3306, 'homi_hijau_db', 'root', NULL, 1, 'elite', NULL, '2026-04-04 09:44:22', '2026-04-07 03:42:14');

-- --------------------------------------------------------

--
-- Table structure for table `tenant_requests`
--

CREATE TABLE `tenant_requests` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `manager_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint UNSIGNED NOT NULL,
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
  `role_id` bigint UNSIGNED DEFAULT NULL,
  `tenant_id` bigint UNSIGNED DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `full_name`, `username`, `email`, `phone`, `google_id`, `email_verified_at`, `is_verified`, `otp_code`, `otp_purpose`, `reset_token`, `reset_token_expires_at`, `otp_expires_at`, `password`, `fcm_token`, `profile_photo_path`, `password_hash`, `remember_token`, `created_at`, `updated_at`, `role`, `role_id`, `tenant_id`, `is_active`) VALUES
(1, 'Super Admin Homi', NULL, NULL, 'admin@homi.id', NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, '$2y$12$01VVM//Hi9r8TxLBEc4EaeLIsZOkHZ.trB6e0UBYxru5/gwMmgjt.', NULL, NULL, '$2y$12$01VVM//Hi9r8TxLBEc4EaeLIsZOkHZ.trB6e0UBYxru5/gwMmgjt.', NULL, '2026-04-04 09:43:08', '2026-04-04 09:43:08', 'superadmin', NULL, NULL, 1),
(3, 'cindy', 'cindy anggraeni', 'cynraeny', 'cindyanggraeni133@gmail.com', NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, '$2y$12$3bPd3skzq9hdzQ8PR60X4Ot37ImgH3upd0AIux6C42oFUKRKlrqSK', NULL, NULL, '$2y$12$3bPd3skzq9hdzQ8PR60X4Ot37ImgH3upd0AIux6C42oFUKRKlrqSK', NULL, '2026-04-09 07:56:10', '2026-04-09 07:56:10', 'admin', 1, 1, 1),
(4, 'hanif', 'hanif abyad', 'hanifbyad', 'hanifabyad71@gmail.com', NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, '$2y$12$SF2faG2inNfyWcQrY1eV1.wVCdL1EiE9.znxlphCCssYefUho453i', NULL, NULL, '$2y$12$SF2faG2inNfyWcQrY1eV1.wVCdL1EiE9.znxlphCCssYefUho453i', NULL, '2026-04-09 08:08:05', '2026-04-09 08:08:05', 'admin', 1, 2, 1);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `announcements`
--
ALTER TABLE `announcements`
  ADD PRIMARY KEY (`id`),
  ADD KEY `announcements_created_by_foreign` (`created_by`),
  ADD KEY `announcements_tenant_id_foreign` (`tenant_id`);

--
-- Indexes for table `app_notifications`
--
ALTER TABLE `app_notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `app_notifications_sent_by_foreign` (`sent_by`),
  ADD KEY `app_notifications_user_id_read_at_index` (`user_id`,`read_at`),
  ADD KEY `app_notifications_user_id_created_at_index` (`user_id`,`created_at`);

--
-- Indexes for table `app_notification_reads`
--
ALTER TABLE `app_notification_reads`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `app_notification_reads_user_id_notification_id_unique` (`user_id`,`notification_id`),
  ADD KEY `app_notification_reads_user_id_index` (`user_id`),
  ADD KEY `app_notification_reads_notification_id_index` (`notification_id`);

--
-- Indexes for table `cache`
--
ALTER TABLE `cache`
  ADD PRIMARY KEY (`key`);

--
-- Indexes for table `cache_locks`
--
ALTER TABLE `cache_locks`
  ADD PRIMARY KEY (`key`);

--
-- Indexes for table `complaints`
--
ALTER TABLE `complaints`
  ADD PRIMARY KEY (`id`),
  ADD KEY `complaints_user_id_foreign` (`user_id`),
  ADD KEY `complaints_assigned_to_foreign` (`assigned_to`);

--
-- Indexes for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Indexes for table `fee_invoices`
--
ALTER TABLE `fee_invoices`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `fee_invoices_user_id_fee_type_id_period_unique` (`user_id`,`fee_type_id`,`period`),
  ADD KEY `fee_invoices_fee_type_id_foreign` (`fee_type_id`),
  ADD KEY `fee_invoices_issued_by_foreign` (`issued_by`),
  ADD KEY `fee_invoices_status_period_index` (`status`,`period`);

--
-- Indexes for table `fee_payments`
--
ALTER TABLE `fee_payments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fee_payments_invoice_id_foreign` (`invoice_id`),
  ADD KEY `fee_payments_payer_user_id_foreign` (`payer_user_id`),
  ADD KEY `fee_payments_reviewed_by_foreign` (`reviewed_by`),
  ADD KEY `fee_payments_review_status_index` (`review_status`);

--
-- Indexes for table `fee_types`
--
ALTER TABLE `fee_types`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `fee_types_name_unique` (`name`);

--
-- Indexes for table `jobs`
--
ALTER TABLE `jobs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `jobs_queue_index` (`queue`);

--
-- Indexes for table `job_batches`
--
ALTER TABLE `job_batches`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `letter_types`
--
ALTER TABLE `letter_types`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `ml_nb_models`
--
ALTER TABLE `ml_nb_models`
  ADD PRIMARY KEY (`id`),
  ADD KEY `ml_nb_models_name_index` (`name`);

--
-- Indexes for table `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`email`);

--
-- Indexes for table `payment_qr_codes`
--
ALTER TABLE `payment_qr_codes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `payment_qr_codes_updated_by_foreign` (`updated_by`),
  ADD KEY `payment_qr_codes_is_active_index` (`is_active`);

--
-- Indexes for table `payment_risk_scores`
--
ALTER TABLE `payment_risk_scores`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `payment_risk_scores_user_id_period_unique` (`user_id`,`period`),
  ADD KEY `payment_risk_scores_user_id_index` (`user_id`),
  ADD KEY `payment_risk_scores_period_index` (`period`);

--
-- Indexes for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  ADD KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`),
  ADD KEY `personal_access_tokens_expires_at_index` (`expires_at`);

--
-- Indexes for table `request_types`
--
ALTER TABLE `request_types`
  ADD PRIMARY KEY (`id`),
  ADD KEY `request_types_letter_type_id_foreign` (`letter_type_id`);

--
-- Indexes for table `resident_profiles`
--
ALTER TABLE `resident_profiles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `resident_profiles_user_id_unique` (`user_id`);

--
-- Indexes for table `service_requests`
--
ALTER TABLE `service_requests`
  ADD PRIMARY KEY (`id`),
  ADD KEY `service_requests_verified_by_foreign` (`verified_by`),
  ADD KEY `service_requests_user_id_status_index` (`user_id`,`status`),
  ADD KEY `service_requests_request_type_id_status_index` (`request_type_id`,`status`);

--
-- Indexes for table `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sessions_user_id_index` (`user_id`),
  ADD KEY `sessions_last_activity_index` (`last_activity`);

--
-- Indexes for table `tenants`
--
ALTER TABLE `tenants`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `tenants_code_unique` (`code`),
  ADD UNIQUE KEY `tenants_domain_unique` (`domain`),
  ADD KEY `tenants_is_active_index` (`is_active`);

--
-- Indexes for table `tenant_requests`
--
ALTER TABLE `tenant_requests`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`),
  ADD KEY `users_role_id_index` (`role_id`),
  ADD KEY `users_tenant_id_index` (`tenant_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `announcements`
--
ALTER TABLE `announcements`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `app_notifications`
--
ALTER TABLE `app_notifications`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `app_notification_reads`
--
ALTER TABLE `app_notification_reads`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `complaints`
--
ALTER TABLE `complaints`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `fee_invoices`
--
ALTER TABLE `fee_invoices`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `fee_payments`
--
ALTER TABLE `fee_payments`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `fee_types`
--
ALTER TABLE `fee_types`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `letter_types`
--
ALTER TABLE `letter_types`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=47;

--
-- AUTO_INCREMENT for table `ml_nb_models`
--
ALTER TABLE `ml_nb_models`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `payment_qr_codes`
--
ALTER TABLE `payment_qr_codes`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `payment_risk_scores`
--
ALTER TABLE `payment_risk_scores`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `request_types`
--
ALTER TABLE `request_types`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `resident_profiles`
--
ALTER TABLE `resident_profiles`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `service_requests`
--
ALTER TABLE `service_requests`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tenants`
--
ALTER TABLE `tenants`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `tenant_requests`
--
ALTER TABLE `tenant_requests`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `announcements`
--
ALTER TABLE `announcements`
  ADD CONSTRAINT `announcements_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `announcements_tenant_id_foreign` FOREIGN KEY (`tenant_id`) REFERENCES `tenants` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `app_notifications`
--
ALTER TABLE `app_notifications`
  ADD CONSTRAINT `app_notifications_sent_by_foreign` FOREIGN KEY (`sent_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `app_notifications_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `complaints`
--
ALTER TABLE `complaints`
  ADD CONSTRAINT `complaints_assigned_to_foreign` FOREIGN KEY (`assigned_to`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `complaints_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `fee_invoices`
--
ALTER TABLE `fee_invoices`
  ADD CONSTRAINT `fee_invoices_fee_type_id_foreign` FOREIGN KEY (`fee_type_id`) REFERENCES `fee_types` (`id`) ON DELETE RESTRICT,
  ADD CONSTRAINT `fee_invoices_issued_by_foreign` FOREIGN KEY (`issued_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fee_invoices_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `fee_payments`
--
ALTER TABLE `fee_payments`
  ADD CONSTRAINT `fee_payments_invoice_id_foreign` FOREIGN KEY (`invoice_id`) REFERENCES `fee_invoices` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fee_payments_payer_user_id_foreign` FOREIGN KEY (`payer_user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fee_payments_reviewed_by_foreign` FOREIGN KEY (`reviewed_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `payment_qr_codes`
--
ALTER TABLE `payment_qr_codes`
  ADD CONSTRAINT `payment_qr_codes_updated_by_foreign` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `request_types`
--
ALTER TABLE `request_types`
  ADD CONSTRAINT `request_types_letter_type_id_foreign` FOREIGN KEY (`letter_type_id`) REFERENCES `letter_types` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `resident_profiles`
--
ALTER TABLE `resident_profiles`
  ADD CONSTRAINT `resident_profiles_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `service_requests`
--
ALTER TABLE `service_requests`
  ADD CONSTRAINT `service_requests_request_type_id_foreign` FOREIGN KEY (`request_type_id`) REFERENCES `request_types` (`id`),
  ADD CONSTRAINT `service_requests_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `service_requests_verified_by_foreign` FOREIGN KEY (`verified_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
