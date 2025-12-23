-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Dec 19, 2025 at 02:24 PM
-- Server version: 8.4.3
-- PHP Version: 8.3.16

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `homi_backend`
--

-- --------------------------------------------------------

--
-- Table structure for table `announcements`
--

CREATE TABLE `announcements` (
  `id` bigint UNSIGNED NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `content` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `image_path` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `published_at` timestamp NULL DEFAULT NULL,
  `created_by` bigint UNSIGNED NOT NULL,
  `is_pinned` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `announcements`
--

INSERT INTO `announcements` (`id`, `title`, `content`, `image_path`, `published_at`, `created_by`, `is_pinned`, `created_at`, `updated_at`) VALUES
(1, 'Pemadaman Listrik Sementara', 'Akan dilakukan pemadaman listrik di Blok A–D pada tanggal 10 Desember 2025 pukul 09.00–12.00. Mohon maaf atas ketidaknyamanannya.', NULL, '2025-12-05 09:56:02', 12, 1, '2025-12-05 09:56:02', '2025-12-05 09:56:02'),
(2, 'lambang liquidator chernobyl', 'ada kebocoran radiasi nuklir', 'announcements/EgYFWjX7oa4kmU1OQiaekqiDdgVsPNZc3tWNxTF1.png', '2025-12-06 10:01:05', 12, 1, '2025-12-06 10:01:05', '2025-12-06 10:01:05'),
(3, 'lambang liquidator chernobyl', 'ada kebocoran radiasi nuklir', NULL, '2025-12-12 23:37:09', 12, 1, '2025-12-12 23:37:09', '2025-12-12 23:37:09'),
(4, 'lambang liquidator chernobyl', 'ada kebocoran radiasi nuklir', 'announcements/0vx6z1YOWooXFUGJzmU0WTf5W99MxLpZbXeECWkq.png', '2025-12-12 23:39:10', 12, 1, '2025-12-12 23:39:10', '2025-12-12 23:39:10'),
(5, 'lambang liquidator chernobyl', 'ada kebocoran radiasi nuklir', 'announcements/8WvTv6pPNMkwyvnqBgM5iAkdi5GcEdNTEPS8ebE4.png', '2025-12-12 23:39:15', 12, 1, '2025-12-12 23:39:15', '2025-12-12 23:39:15'),
(6, 'lambang liquidator chernobyl', 'ada kebocoran radiasi nuklir', NULL, '2025-12-17 07:13:21', 12, 1, '2025-12-17 07:13:21', '2025-12-17 07:13:21'),
(7, 'lambang liquidator chernobyl', 'ada kebocoran radiasi nuklir', 'announcements/KiZDJyE9f3DW6uBukt1F2HbAbWpTpbpNjA7qQZJF.png', '2025-12-17 07:35:22', 12, 1, '2025-12-17 07:35:22', '2025-12-17 07:35:22');

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
  `user_id` bigint UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `complaints`
--

INSERT INTO `complaints` (`id`, `nama_pelapor`, `tanggal_pengaduan`, `tempat_kejadian`, `perihal`, `foto_path`, `status`, `created_at`, `updated_at`, `user_id`) VALUES
(1, 'juan', '2025-12-05', 'bengkong dalam', 'sampah belum di angkut', 'complaints/KiYHX1keEdvU6dt7wqHzrQdOn1R31Q63x8m0uaMs.png', 'baru', '2025-12-04 20:48:44', '2025-12-04 20:48:44', NULL),
(2, 'juan', '2025-12-05', 'bengkong dalam', 'sampah belum di angkut', 'complaints/QOdNTY9DvrR71J2r9jusPqdDFyziolt1S4BmRxEI.png', 'baru', '2025-12-04 21:00:02', '2025-12-04 21:00:02', NULL),
(3, 'juan', '2025-12-05', 'bengkong dalam', 'sampah belum di angkut', 'complaints/Me2toeVFI4VcuTHgdJI5XBejcUA6aRCk5u8eQto9.png', 'baru', '2025-12-04 21:00:43', '2025-12-04 21:00:43', NULL),
(4, 'juan', '2025-12-05', 'bengkong dalam', 'sampah belum di angkut', 'complaints/TefQcE8WssFt5jrzK74F57w7uX2VhG9w0o5kiXnK.png', 'baru', '2025-12-04 21:03:40', '2025-12-04 21:03:40', NULL),
(5, 'juan', '2025-12-05', 'bengkong dalam', 'sampah belum di angkut', 'complaints/T7RBTWGGIcYlHKCqF9x9ARmyLdVGJU7vbnrSCPNc.png', 'baru', '2025-12-04 21:03:53', '2025-12-04 21:03:53', NULL),
(6, 'juan', '2025-12-01', 'bengkong dalam', 'sampah belum di angkut', 'complaints/4gCgYYm4KgFOFrkvkdZ7FZHeMHKWnpaXjL9bzJIh.png', 'baru', '2025-12-04 21:05:47', '2025-12-04 21:05:47', NULL),
(7, 'juan', '2025-12-01', 'bengkong dalam', 'sampah belum di angkut', 'complaints/9PG73Nms1lCtUqN1bHyVfjPnVRxiNEpfKhcHGDxq.png', 'baru', '2025-12-04 21:06:03', '2025-12-04 21:06:03', NULL),
(8, 'juan', '2025-12-01', 'bengkong dalam', 'sampah belum di angkut', 'complaints/nOUrib4aArP8ws8ZWBWHgH3ZgbAvI4PEHKPxTOb4.png', 'baru', '2025-12-04 21:06:11', '2025-12-04 21:06:11', NULL),
(9, 'juan', '2025-12-01', 'bengkong dalam', 'sampah belum di angkut', 'complaints/QBUd0OIvUhxJZT1tkBkQgaLZ6RM3XULpG3jMlMYa.png', 'baru', '2025-12-04 21:06:28', '2025-12-04 21:06:28', NULL),
(11, 'hanif', '2025-12-01', 'bengkong laut', 'sampah belum di angkut', 'complaints/GL1LGFqSApUmS2fhKtzLmYoUIeHspqztIvv8uoZz.png', 'baru', '2025-12-04 21:27:41', '2025-12-04 21:27:41', 10),
(12, 'hanif', '2025-11-03', 'bengkong laut', 'sampah belum di angkut', 'complaints/r2W0USc8b6twNEarSdROy7FDA7HsoGMDsa7yya19.png', 'baru', '2025-12-12 01:04:29', '2025-12-12 01:04:29', 10);

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

--
-- Dumping data for table `fee_invoices`
--

INSERT INTO `fee_invoices` (`id`, `user_id`, `fee_type_id`, `period`, `amount`, `status`, `trx_id`, `issued_by`, `due_date`, `created_at`, `updated_at`) VALUES
(1, 10, 1, '2025-08-01', 150000, 'unpaid', 'IPL-XWD3YF5KD1', 12, '2025-08-31', '2025-12-17 01:31:04', '2025-12-17 01:31:04'),
(2, 14, 1, '2025-08-01', 150000, 'paid', 'IPL-WXXRRVIINS', 12, '2025-08-31', '2025-12-17 01:31:04', '2025-12-17 02:12:35'),
(3, 15, 1, '2025-08-01', 150000, 'unpaid', 'IPL-JYPU9FDLHF', 12, '2025-08-31', '2025-12-17 01:31:04', '2025-12-17 01:31:04'),
(4, 16, 1, '2025-08-01', 150000, 'unpaid', 'IPL-C6GEKPUUVB', 12, '2025-08-31', '2025-12-17 01:31:04', '2025-12-17 01:31:04'),
(5, 17, 1, '2025-08-01', 150000, 'rejected', 'IPL-5VDHMRD2VK', 12, '2025-08-31', '2025-12-17 01:31:04', '2025-12-17 03:05:34'),
(6, 18, 1, '2025-08-01', 150000, 'unpaid', 'IPL-ZZJULP6BEK', 12, '2025-08-31', '2025-12-17 01:31:04', '2025-12-17 01:31:04');

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

--
-- Dumping data for table `fee_payments`
--

INSERT INTO `fee_payments` (`id`, `invoice_id`, `payer_user_id`, `proof_path`, `note`, `review_status`, `reviewed_by`, `reviewed_at`, `created_at`, `updated_at`) VALUES
(1, 2, 14, 'payment_proofs/AX0H813Hq4hNMBd0DpQ7CP1JaPEjBkar1LvXM8XT.png', NULL, 'approved', 12, '2025-12-17 02:12:35', '2025-12-17 01:55:22', '2025-12-17 02:12:35'),
(3, 5, 17, 'payment_proofs/37UltmsDEtJzkUV75cxSWLzRwJq4dHk5Ku0aJduh.png', NULL, 'rejected', 12, '2025-12-17 03:05:34', '2025-12-17 03:04:33', '2025-12-17 03:05:34');

-- --------------------------------------------------------

--
-- Table structure for table `fee_types`
--

CREATE TABLE `fee_types` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `fee_types`
--

INSERT INTO `fee_types` (`id`, `name`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'Iuran Sampah', 1, '2025-12-15 08:12:02', '2025-12-15 08:12:02'),
(2, 'Iuran Keamanan', 1, '2025-12-15 08:12:02', '2025-12-15 08:12:02'),
(3, 'Iuran Lingkungan', 1, '2025-12-15 08:12:02', '2025-12-15 08:12:02'),
(4, 'Iuran Fasilitas Umum', 1, '2025-12-15 08:12:02', '2025-12-15 08:12:02');

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
(1, '0001_01_01_000000_create_users_table', 1),
(2, '0001_01_01_000001_create_cache_table', 1),
(3, '0001_01_01_000002_create_jobs_table', 1),
(4, '2025_12_02_090105_create_personal_access_tokens_table', 2),
(5, '2025_12_04_154911_add_otp_fields_to_users_table', 3),
(6, '2025_12_05_024012_create_complaints_table', 4),
(7, '2025_12_05_040657_add_user_id_to_complaints_table', 5),
(8, '2025_12_05_161444_add_role_to_users_table', 6),
(9, '2025_12_05_161649_create_announcements_table', 7),
(10, '2025_12_06_163935_add_image_to_announcements_table', 8),
(11, '2025_12_12_091139_add_otp_purpose_to_users_table', 9),
(12, '2025_12_12_141646_add_reset_token_to_users_table', 10),
(14, '2025_12_12_161225_create_resident_profiles_table', 11),
(15, '2025_12_13_055336_create_request_types__table', 11),
(16, '2025_12_13_060147_create_request_types_table', 12),
(17, '2025_12_13_060224_create_service_request_table', 12),
(18, '2025_12_15_144600_create_fee_types_table', 13),
(19, '2025_12_15_144810_create_payment_qr_codes_table', 13),
(20, '2025_12_15_144905_create_fee_invoices_table', 13),
(21, '2025_12_15_144952_create_fee_payments_table', 13);

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

--
-- Dumping data for table `payment_qr_codes`
--

INSERT INTO `payment_qr_codes` (`id`, `image_path`, `is_active`, `updated_by`, `notes`, `created_at`, `updated_at`) VALUES
(1, 'payment_qr_codes/jjBjWkLriw0TpeoxVcn3VtMhilAbmGqHQwhkD0dG.png', 1, 12, 'QRIS Hawai Garden', '2025-12-17 01:22:32', '2025-12-17 01:22:32');

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

--
-- Dumping data for table `personal_access_tokens`
--

INSERT INTO `personal_access_tokens` (`id`, `tokenable_type`, `tokenable_id`, `name`, `token`, `abilities`, `last_used_at`, `expires_at`, `created_at`, `updated_at`) VALUES
(1, 'App\\Models\\User', 1, 'mobile', '94aaac83bd9f17e3ca527a6f66d704449ca3941afbea945db382b977a8ae533b', '[\"*\"]', NULL, NULL, '2025-12-04 07:59:37', '2025-12-04 07:59:37'),
(3, 'App\\Models\\User', 1, 'mobile', 'd77a8437f2ae05487549199dc57136f31973643630c70c459be7d2ff3bac5eba', '[\"*\"]', '2025-12-04 08:13:14', NULL, '2025-12-04 08:10:38', '2025-12-04 08:13:14'),
(4, 'App\\Models\\User', 2, 'mobile', '1b12e42d58df15ef2b2f62a977c22ac7f4678c36d59f25c5cb7bc2976bfedbe1', '[\"*\"]', NULL, NULL, '2025-12-04 08:27:10', '2025-12-04 08:27:10'),
(5, 'App\\Models\\User', 1, 'mobile', 'ddf760ad33ad3099589b397f92b934ce215f53c8f141784ef288124c99803c57', '[\"*\"]', '2025-12-04 08:29:52', NULL, '2025-12-04 08:28:06', '2025-12-04 08:29:52'),
(6, 'App\\Models\\User', 3, 'mobile', 'b0e1a2568495b352490d9930b4ce687583195694f0e7c48b4da4be1ef1e71685', '[\"*\"]', NULL, NULL, '2025-12-04 08:58:35', '2025-12-04 08:58:35'),
(7, 'App\\Models\\User', 3, 'mobile', '01c5ff839b2558f4eda84d832fbff0eab19155e2ee81734b775834226018666a', '[\"*\"]', NULL, NULL, '2025-12-04 08:58:58', '2025-12-04 08:58:58'),
(8, 'App\\Models\\User', 4, 'mobile', 'c4f1bd2d0470bf2c0313c84ca81a68c9eeb6d2c8f67064e1bb244505498483b4', '[\"*\"]', NULL, NULL, '2025-12-04 09:00:27', '2025-12-04 09:00:27'),
(9, 'App\\Models\\User', 4, 'mobile', 'a297bf24aaa84c354328e078354ba924029ac1bee5fa9450e9d7d7a29878672e', '[\"*\"]', NULL, NULL, '2025-12-04 09:00:31', '2025-12-04 09:00:31'),
(10, 'App\\Models\\User', 9, 'mobile', '765f017143e972251cdc919b47e09f88d13d899fad664c6ea14dd0f2859c8953', '[\"*\"]', NULL, NULL, '2025-12-04 20:41:53', '2025-12-04 20:41:53'),
(11, 'App\\Models\\User', 9, 'mobile', '65c0350ec545a85edd6c3245714d574667b7d03aa19052c171e3d25f437a0f80', '[\"*\"]', '2025-12-04 21:18:09', NULL, '2025-12-04 20:42:01', '2025-12-04 21:18:09'),
(12, 'App\\Models\\User', 10, 'mobile', 'f3c27829614d51625b0658e2bb08ea1ac00dfc4d5752adbd8d4239d6575160b6', '[\"*\"]', '2025-12-12 01:04:28', NULL, '2025-12-04 21:26:08', '2025-12-12 01:04:28'),
(13, 'App\\Models\\User', 9, 'mobile', '7e69de32461b3f4aa13aa5aa70b0f308db4fde0aa5db090bf3878e80d6485020', '[\"*\"]', '2025-12-04 22:23:00', NULL, '2025-12-04 21:28:53', '2025-12-04 22:23:00'),
(14, 'App\\Models\\User', 11, 'mobile', '05276a094a1084e71d2f34dcc303e0ea45f6d13750e24e7822ef4005ecd2c163', '[\"*\"]', NULL, NULL, '2025-12-05 01:41:17', '2025-12-05 01:41:17'),
(15, 'App\\Models\\User', 11, 'mobile', '96002f298ce38b3b2bf4d14225502fa2702a732acf51c725cc071098efbe70f7', '[\"*\"]', NULL, NULL, '2025-12-05 01:41:40', '2025-12-05 01:41:40'),
(20, 'App\\Models\\User', 10, 'mobile', 'dbe8f37dcf5fff121721b3ae330a832f0bbfb72215a1cf3244db99d347a41079', '[\"*\"]', '2025-12-05 09:57:12', NULL, '2025-12-05 09:56:36', '2025-12-05 09:57:12'),
(22, 'App\\Models\\User', 10, 'mobile', '0de1b1a9077726e18780535883f74a9dfca1614819461b7f369bd7c222fd552f', '[\"*\"]', '2025-12-19 00:54:38', NULL, '2025-12-06 10:03:54', '2025-12-19 00:54:38'),
(35, 'App\\Models\\User', 15, 'mobile', '14528e174567369efe4d4108bbc1386639159a813cad148fc0a2695990ed3f76', '[\"*\"]', NULL, NULL, '2025-12-12 08:32:52', '2025-12-12 08:32:52'),
(36, 'App\\Models\\User', 15, 'mobile', '880b41a22aec3b7a601976e04a3c66316035ce88e974c724806f1ef49df3b397', '[\"*\"]', NULL, NULL, '2025-12-12 08:33:16', '2025-12-12 08:33:16'),
(37, 'App\\Models\\User', 16, 'mobile', '8e231d5c71bde16bfa188a118da128370fbea2eb59d5af3c2dc08f9706aac3ca', '[\"*\"]', NULL, NULL, '2025-12-12 08:38:23', '2025-12-12 08:38:23'),
(38, 'App\\Models\\User', 16, 'mobile', 'a41f3fee286f03f92af2e45e3b9cb8941f2b04422c8aa832680be0fe1d4ad430', '[\"*\"]', NULL, NULL, '2025-12-12 08:38:54', '2025-12-12 08:38:54'),
(45, 'App\\Models\\User', 12, 'mobile', '2b58057f8cbf351ed2e5f591b0a0fe2825d00ff4b9fee590fd2f60e2a1877909', '[\"*\"]', NULL, NULL, '2025-12-12 23:30:43', '2025-12-12 23:30:43'),
(46, 'App\\Models\\User', 12, 'mobile', '4d1f7bcea43e35e9fa8146beb80ba5777b748c74aa974f6714118d9cf2d510a2', '[\"*\"]', '2025-12-17 07:35:21', NULL, '2025-12-12 23:31:46', '2025-12-17 07:35:21'),
(47, 'App\\Models\\User', 17, 'mobile', 'efc36521f4a62bb17aafdb4eb2f3a6324844ed384b3df588f26a1f5f2850796e', '[\"*\"]', '2025-12-12 23:45:28', NULL, '2025-12-12 23:44:35', '2025-12-12 23:45:28'),
(48, 'App\\Models\\User', 17, 'mobile', '16fd6baef29f8692384a477d979b70fc6fc63b4a99b4cc923b55487cba92c54e', '[\"*\"]', '2025-12-12 23:46:55', NULL, '2025-12-12 23:46:08', '2025-12-12 23:46:55'),
(49, 'App\\Models\\User', 14, 'mobile', '7d46f20beeadd38be8d50deeddadd679fb36f95ec88bebfe8e4543fc02319acd', '[\"*\"]', '2025-12-17 03:08:49', NULL, '2025-12-15 05:53:16', '2025-12-17 03:08:49'),
(50, 'App\\Models\\User', 12, 'mobile', 'b030f3d46267ed23d5eb860a94832f3a6961142dbe2263028c33e284340cb047', '[\"*\"]', '2025-12-17 03:09:33', NULL, '2025-12-15 06:10:29', '2025-12-17 03:09:33'),
(51, 'App\\Models\\User', 12, 'mobile', '2601c3126b150ee82cf7a89e1c3db4b91180cf80c7cb0f998e81c206c1a33060', '[\"*\"]', '2025-12-15 10:13:04', NULL, '2025-12-15 09:41:22', '2025-12-15 10:13:04'),
(52, 'App\\Models\\User', 12, 'mobile', '9fb12dd1c294b95e0fc5344c7766a9a49d4884d16c82f80b654f8e9eaea5710b', '[\"*\"]', '2025-12-17 01:31:04', NULL, '2025-12-15 10:13:36', '2025-12-17 01:31:04'),
(53, 'App\\Models\\User', 12, 'mobile', 'e2968c647c0948d9532f3dd111213cd8536a1117a5be331f10f7e468481a5687', '[\"*\"]', '2025-12-17 03:05:34', NULL, '2025-12-17 01:03:07', '2025-12-17 03:05:34'),
(54, 'App\\Models\\User', 14, 'mobile', '3b122df10679759376ec05c860389156a450d3c5636424588f951c584eec6cbd', '[\"*\"]', '2025-12-17 02:26:45', NULL, '2025-12-17 01:24:22', '2025-12-17 02:26:45'),
(55, 'App\\Models\\User', 17, 'mobile', '937c2824574cb0e0b4b82f92aedcdfc584b4de13789a44fa03d230373b2131a9', '[\"*\"]', '2025-12-17 03:04:33', NULL, '2025-12-17 03:02:19', '2025-12-17 03:04:33'),
(56, 'App\\Models\\User', 14, 'mobile', '37f9ca835d20294a29c9bbd1031c6376ae55028d37990801350f0b1109cbda7e', '[\"*\"]', NULL, NULL, '2025-12-17 04:46:36', '2025-12-17 04:46:36'),
(57, 'App\\Models\\User', 14, 'mobile', '607ae44219ca74184be435442529fade40e6f6aa46a3ba6df304b1340609cce0', '[\"*\"]', NULL, NULL, '2025-12-17 04:50:39', '2025-12-17 04:50:39'),
(58, 'App\\Models\\User', 14, 'mobile', '2e328624c27be2ecc09e9d95accd3333add3992c2a3d5058aa6ffaac72f0d806', '[\"*\"]', NULL, NULL, '2025-12-17 04:54:29', '2025-12-17 04:54:29'),
(59, 'App\\Models\\User', 17, 'mobile', '2170d6f5a7dc4c5c2dfafbb35e0147770999aa9011ce250c57e49012f1af09fa', '[\"*\"]', NULL, NULL, '2025-12-17 07:17:54', '2025-12-17 07:17:54'),
(60, 'App\\Models\\User', 14, 'mobile', 'bf874214f28245bb0d5e4750b879cc3ac06d0ee5cd51c10b8a633445d3b1df3d', '[\"*\"]', '2025-12-17 08:43:07', NULL, '2025-12-17 08:42:42', '2025-12-17 08:43:07'),
(61, 'App\\Models\\User', 14, 'mobile', '1aaad2e1175b562bb104a1d28acedd116fe986484edbd5b0786caa9458dabfd0', '[\"*\"]', '2025-12-17 08:48:08', NULL, '2025-12-17 08:48:07', '2025-12-17 08:48:08'),
(62, 'App\\Models\\User', 14, 'mobile', 'c1d5a769a572edd2b6a75b80e90d1ed6798eb4ff135e22bac0a77d2f1d25bb2d', '[\"*\"]', NULL, NULL, '2025-12-19 00:26:50', '2025-12-19 00:26:50'),
(63, 'App\\Models\\User', 14, 'mobile', '8188b135706f229c704ca7eb16661295177f7671498ff0619d15148266fb85f2', '[\"*\"]', '2025-12-19 00:27:55', NULL, '2025-12-19 00:27:44', '2025-12-19 00:27:55'),
(64, 'App\\Models\\User', 14, 'mobile', 'e49843830b6be372ebff87d1a35c41de0d2e36be5bd209e90d27b0f2030b72ed', '[\"*\"]', '2025-12-19 00:52:39', NULL, '2025-12-19 00:51:16', '2025-12-19 00:52:39'),
(65, 'App\\Models\\User', 14, 'mobile', '7b4cd6356d6a7c6daaacd68ccb1f70805bd194f45f2949071af7683aefe16e38', '[\"*\"]', '2025-12-19 03:19:48', NULL, '2025-12-19 03:19:46', '2025-12-19 03:19:48'),
(66, 'App\\Models\\User', 14, 'mobile', '2d75603d5fdc414a3ca02db52cd4daf5909e2b91e3cd1e301597721da3b9f135', '[\"*\"]', '2025-12-19 03:39:44', NULL, '2025-12-19 03:39:43', '2025-12-19 03:39:44'),
(67, 'App\\Models\\User', 14, 'mobile', '380b0ef404fa3fdfe3f56fcb88b6c9c03d96f8bdd3f892703bedb4a4d2693455', '[\"*\"]', '2025-12-19 03:47:40', NULL, '2025-12-19 03:47:39', '2025-12-19 03:47:40'),
(68, 'App\\Models\\User', 14, 'mobile', '4b3b8eb72d52af070cc7d4b1f6e76042f15e5782b9322473f64a703c551b2cb3', '[\"*\"]', '2025-12-19 04:10:15', NULL, '2025-12-19 04:10:14', '2025-12-19 04:10:15'),
(69, 'App\\Models\\User', 14, 'mobile', '814e1bd63aa9854640b0b4903faee26115d690c516ee5e750ad952b705d23849', '[\"*\"]', '2025-12-19 05:01:15', NULL, '2025-12-19 05:01:14', '2025-12-19 05:01:15'),
(70, 'App\\Models\\User', 14, 'mobile', '1656e0b7a764bc48b11fbd4698baedc75dcbc21dc38ea84c6d7671256b3516b2', '[\"*\"]', '2025-12-19 05:25:31', NULL, '2025-12-19 05:23:15', '2025-12-19 05:25:31'),
(71, 'App\\Models\\User', 14, 'mobile', '7d94b898e8253de2f4e287b88e51e5eee7889e3340d828ee78af9d474c34b9cb', '[\"*\"]', '2025-12-19 05:29:50', NULL, '2025-12-19 05:29:29', '2025-12-19 05:29:50'),
(72, 'App\\Models\\User', 14, 'mobile', '09631d781aff0f48c4ae486350fb79953056cb3b04afeebebfa4c44f3b9c4098', '[\"*\"]', '2025-12-19 05:31:31', NULL, '2025-12-19 05:30:24', '2025-12-19 05:31:31'),
(73, 'App\\Models\\User', 14, 'mobile', 'b950c5cbba84f71294b9d04d3cd54a7fb55f8eec53e85dfb485034a0d3452264', '[\"*\"]', '2025-12-19 05:34:27', NULL, '2025-12-19 05:33:49', '2025-12-19 05:34:27'),
(74, 'App\\Models\\User', 14, 'mobile', '892025881c5262d439c84c71bfcbbb56d82dcffade0fe8eb918ccc440d4ab79a', '[\"*\"]', '2025-12-19 05:37:09', NULL, '2025-12-19 05:36:47', '2025-12-19 05:37:09'),
(75, 'App\\Models\\User', 14, 'mobile', '51ed8ee05d67283953aa4129abf4fecf33a5870a736e8c9f5a762c62e21c0f03', '[\"*\"]', '2025-12-19 05:39:58', NULL, '2025-12-19 05:38:11', '2025-12-19 05:39:58'),
(76, 'App\\Models\\User', 14, 'mobile', '0856461fd04a2d42053f663a1cec752f71fde175a34126a70a47ba20cc3ab793', '[\"*\"]', '2025-12-19 05:42:43', NULL, '2025-12-19 05:42:43', '2025-12-19 05:42:43'),
(77, 'App\\Models\\User', 14, 'mobile', '88fd6514d7a508ecc79efbb3903de3d39ae6c21a6217fa527d215bffe74b76ca', '[\"*\"]', '2025-12-19 05:45:39', NULL, '2025-12-19 05:44:31', '2025-12-19 05:45:39');

-- --------------------------------------------------------

--
-- Table structure for table `request_types`
--

CREATE TABLE `request_types` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `request_types`
--

INSERT INTO `request_types` (`id`, `name`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'Surat Pengantar', 1, '2025-12-15 06:03:18', '2025-12-15 06:03:18'),
(2, 'Perbaikan Fasilitas', 1, '2025-12-15 06:03:18', '2025-12-15 06:03:18'),
(3, 'Peminjaman Fasilitas', 1, '2025-12-15 06:03:18', '2025-12-15 06:03:18'),
(4, 'Pengajuan Layanan', 1, '2025-12-15 06:03:18', '2025-12-15 06:03:18');

-- --------------------------------------------------------

--
-- Table structure for table `resident_profiles`
--

CREATE TABLE `resident_profiles` (
  `id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `blok` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `no_rumah` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `alamat` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_public` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `resident_profiles`
--

INSERT INTO `resident_profiles` (`id`, `user_id`, `blok`, `no_rumah`, `alamat`, `is_public`, `created_at`, `updated_at`) VALUES
(1, 10, 'AA1', '10', 'Blok AA1 No 15, Ngawi', 1, '2025-12-12 23:25:09', '2025-12-12 23:25:09'),
(2, 14, NULL, NULL, NULL, 1, '2025-12-12 23:25:13', '2025-12-12 23:26:17'),
(3, 16, 'AA1', '10', 'Blok AA1 No 15, Ngawi', 1, '2025-12-12 23:27:40', '2025-12-12 23:27:40'),
(4, 17, 'AA1', '10', 'Blok AA1 No 15, Ngawi', 1, '2025-12-12 23:27:46', '2025-12-12 23:27:46');

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
  `status` enum('submitted','processed','approved','rejected') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'submitted',
  `admin_note` text COLLATE utf8mb4_unicode_ci,
  `verified_by` bigint UNSIGNED DEFAULT NULL,
  `verified_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `service_requests`
--

INSERT INTO `service_requests` (`id`, `user_id`, `request_type_id`, `reporter_name`, `request_date`, `place`, `subject`, `status`, `admin_note`, `verified_by`, `verified_at`, `created_at`, `updated_at`) VALUES
(1, 14, 1, 'Lumine', '2025-12-15', 'Blok A No 7', 'Pengurusan Surat Domisili', 'approved', 'Data lengkap dan valid', 12, '2025-12-15 06:18:28', '2025-12-15 06:03:53', '2025-12-15 06:18:28'),
(2, 14, 4, 'Lumine', '2025-12-15', 'Blok A No 7', 'Pengajuan layanan makan siang gratis', 'rejected', 'Data lengkap dan valid', 12, '2025-12-15 06:20:08', '2025-12-15 06:15:24', '2025-12-15 06:20:08'),
(3, 14, 2, 'Lumine', '2025-12-15', 'Blok A No 7', 'Perbaikan hutan sumatra', 'processed', 'Data Antek antek asheng', 12, '2025-12-17 03:09:33', '2025-12-15 06:22:54', '2025-12-17 03:09:33'),
(4, 14, 3, 'Lumine', '2025-12-15', 'Blok A No 7', 'Perbaikan hutan sumatra', 'submitted', NULL, NULL, NULL, '2025-12-17 03:08:33', '2025-12-17 03:08:33');

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

--
-- Dumping data for table `sessions`
--

INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES
('aQGbfhPvJM5hxWJN704nAiqGUM8t5iPdHBXU7WkL', NULL, '127.0.0.1', 'PostmanRuntime/7.49.1', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiV1ltcDRocW5xelZ2QkVBYTZvSkwxRTI5QTdCME1TcFlEaHRma1RKOCI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MjE6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMCI7czo1OiJyb3V0ZSI7Tjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1764861957),
('nB1x55EgS8g9uEj6sCE4Un3xX1s2AuYZInZMi1k5', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiYlVOZnJZY0ZiUUVDQ3RqdWpsODJUMHhxdlUxcUZJaWY5OXF3TGZuayI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MjE6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMCI7czo1OiJyb3V0ZSI7Tjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1764859484);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `is_verified` tinyint(1) NOT NULL DEFAULT '0',
  `otp_code` varchar(6) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `otp_purpose` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `reset_token` varchar(64) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `reset_token_expires_at` datetime DEFAULT NULL,
  `otp_expires_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `role` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'resident'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `email_verified_at`, `is_verified`, `otp_code`, `otp_purpose`, `reset_token`, `reset_token_expires_at`, `otp_expires_at`, `password`, `remember_token`, `created_at`, `updated_at`, `role`) VALUES
(10, 'Muhammad Hanif', 'hanif@gmail.com', NULL, 1, NULL, NULL, NULL, NULL, NULL, '$2y$12$vTa7F3kmyqtJP63Y0MJQSeSulbQ10e8eYxOJ3kJ5WtdDUzZBJqK86', NULL, '2025-12-04 21:25:19', '2025-12-12 00:58:04', 'resident'),
(12, 'juan', 'juanhutapea22@gmail.com', NULL, 1, NULL, NULL, NULL, NULL, NULL, '$2y$12$Gk4nP9R0ocm8BxOcbyuGquvOnchSbM3XCKPu66UASTbkxeWAbaEXm', NULL, '2025-12-05 01:45:26', '2025-12-12 23:30:13', 'admin'),
(14, 'Lumine', 'Lumine@example.com', NULL, 1, NULL, NULL, NULL, NULL, NULL, '$2y$12$arvPZYVPY16yya1MPIo0zufw6Q.3zKGePS2E7EzpgubgaMdjrHbve', NULL, '2025-12-12 08:24:34', '2025-12-15 05:52:14', 'resident'),
(15, 'Ayaya', 'ayaya@example.com', NULL, 1, NULL, NULL, NULL, NULL, NULL, '$2y$12$1OGOrb/XVYhita9NHdPsx.qu8CFIOb08KhnTOJWmd/f8l1yPpQUde', NULL, '2025-12-12 08:30:21', '2025-12-12 08:32:52', 'resident'),
(16, 'osama', 'osama@example.com', NULL, 1, NULL, NULL, NULL, NULL, NULL, '$2y$12$toXLGvTubGxtRmsr.foRXOCaRF9KZrDXfCs4ALHES75i7kJJd2GF2', NULL, '2025-12-12 08:37:33', '2025-12-12 08:38:22', 'resident'),
(17, 'Ambatunut', 'Amba@example.com', NULL, 1, NULL, NULL, NULL, NULL, NULL, '$2y$12$sGgSP.8VzhVUsZXFDSXPgeXoBrkceRNvNCucCtjM1ZrfcaSH4cH6S', NULL, '2025-12-12 08:44:31', '2025-12-12 23:46:55', 'resident'),
(18, 'Test User', 'test@example.com', '2025-12-15 06:03:18', 0, NULL, NULL, NULL, NULL, NULL, '$2y$12$.n9pvCEDe7HmaLqDgm.T4.trpbZQ.zvHi8YEvNryH2hhOJ8pHvPPG', 'ZDwdDoIXpK', '2025-12-15 06:03:18', '2025-12-15 06:03:18', 'resident');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `announcements`
--
ALTER TABLE `announcements`
  ADD PRIMARY KEY (`id`),
  ADD KEY `announcements_created_by_foreign` (`created_by`);

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
  ADD KEY `complaints_user_id_foreign` (`user_id`);

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
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

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
  ADD PRIMARY KEY (`id`);

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
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `announcements`
--
ALTER TABLE `announcements`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `complaints`
--
ALTER TABLE `complaints`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `fee_invoices`
--
ALTER TABLE `fee_invoices`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `fee_payments`
--
ALTER TABLE `fee_payments`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

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
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `payment_qr_codes`
--
ALTER TABLE `payment_qr_codes`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=78;

--
-- AUTO_INCREMENT for table `request_types`
--
ALTER TABLE `request_types`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `resident_profiles`
--
ALTER TABLE `resident_profiles`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `service_requests`
--
ALTER TABLE `service_requests`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `announcements`
--
ALTER TABLE `announcements`
  ADD CONSTRAINT `announcements_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `complaints`
--
ALTER TABLE `complaints`
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
