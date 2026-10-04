-- ============================================================================
-- COMBINED FULL DATABASE DUMP: RBK STUDIO x RBK KONSTRUKSI
-- Database: rbk_db
-- ============================================================================
CREATE DATABASE IF NOT EXISTS `rbk_db` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `rbk_db`;

-- ============================================================================
-- MASTER DATABASE SCHEMA: RBK STUDIO × RBK KONSTRUKSI
-- MySQL 8.0 / MariaDB 10.6+ (InnoDB, utf8mb4_unicode_ci)
-- File: database/schema.sql
-- ============================================================================

SET FOREIGN_KEY_CHECKS = 0;
DROP TABLE IF EXISTS `rate_limits`;
DROP TABLE IF EXISTS `activity_logs`;
DROP TABLE IF EXISTS `wa_clicks`;
DROP TABLE IF EXISTS `lead_activities`;
DROP TABLE IF EXISTS `leads`;
DROP TABLE IF EXISTS `articles`;
DROP TABLE IF EXISTS `faqs`;
DROP TABLE IF EXISTS `stats`;
DROP TABLE IF EXISTS `testimonials`;
DROP TABLE IF EXISTS `process_steps`;
DROP TABLE IF EXISTS `advantages`;
DROP TABLE IF EXISTS `portfolio_images`;
DROP TABLE IF EXISTS `portfolios`;
DROP TABLE IF EXISTS `portfolio_categories`;
DROP TABLE IF EXISTS `package_price_history`;
DROP TABLE IF EXISTS `package_specs`;
DROP TABLE IF EXISTS `packages`;
DROP TABLE IF EXISTS `service_items`;
DROP TABLE IF EXISTS `services`;
DROP TABLE IF EXISTS `media`;
DROP TABLE IF EXISTS `sections`;
DROP TABLE IF EXISTS `settings`;
DROP TABLE IF EXISTS `login_attempts`;
DROP TABLE IF EXISTS `users`;
SET FOREIGN_KEY_CHECKS = 1;

-- 1. USERS (Super Admin Accounts)
CREATE TABLE `users` (
  `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(100) NOT NULL,
  `email` VARCHAR(150) NOT NULL UNIQUE,
  `password_hash` VARCHAR(255) NOT NULL,
  `role` ENUM('super_admin') NOT NULL DEFAULT 'super_admin',
  `is_active` TINYINT(1) NOT NULL DEFAULT 1,
  `must_change_password` TINYINT(1) NOT NULL DEFAULT 0,
  `last_login_at` DATETIME NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` DATETIME NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 2. LOGIN ATTEMPTS (Throttle & Security)
CREATE TABLE `login_attempts` (
  `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `email` VARCHAR(150) NOT NULL,
  `ip_hash` VARCHAR(64) NOT NULL,
  `success` TINYINT(1) NOT NULL DEFAULT 0,
  `attempted_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  INDEX `idx_login_ip_email` (`ip_hash`, `email`, `attempted_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 3. SETTINGS (General, SEO, Tracking, SMTP, Contacts)
CREATE TABLE `settings` (
  `key` VARCHAR(80) PRIMARY KEY,
  `value` LONGTEXT NULL,
  `group` VARCHAR(50) NOT NULL DEFAULT 'general',
  `updated_by` BIGINT UNSIGNED NULL,
  `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT `fk_settings_updated_by` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 4. SECTIONS (Landing Page Dynamic Sections S0-S21)
CREATE TABLE `sections` (
  `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `key` VARCHAR(50) NOT NULL UNIQUE,
  `eyebrow` VARCHAR(255) NULL,
  `title` VARCHAR(255) NULL,
  `subtitle` TEXT NULL,
  `body` LONGTEXT NULL,
  `cta_label` VARCHAR(100) NULL,
  `cta_target` VARCHAR(255) NULL,
  `image_media_id` BIGINT UNSIGNED NULL,
  `is_visible` TINYINT(1) NOT NULL DEFAULT 1,
  `sort_order` INT NOT NULL DEFAULT 0,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` DATETIME NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 5. MEDIA (Uploaded Images & WebP Variants)
CREATE TABLE `media` (
  `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `filename` VARCHAR(255) NOT NULL,
  `path` VARCHAR(255) NOT NULL,
  `mime` VARCHAR(50) NOT NULL,
  `width` INT UNSIGNED NOT NULL DEFAULT 0,
  `height` INT UNSIGNED NOT NULL DEFAULT 0,
  `size_bytes` BIGINT UNSIGNED NOT NULL DEFAULT 0,
  `alt_text` VARCHAR(255) NOT NULL DEFAULT '',
  `variants` JSON NULL,
  `uploaded_by` BIGINT UNSIGNED NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` DATETIME NULL,
  CONSTRAINT `fk_media_uploaded_by` FOREIGN KEY (`uploaded_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

ALTER TABLE `sections` ADD CONSTRAINT `fk_sections_image` FOREIGN KEY (`image_media_id`) REFERENCES `media` (`id`) ON DELETE SET NULL;

-- 6. SERVICES (RBK Studio, RBK Konstruksi, RenoVancy, RBK Kreasi)
CREATE TABLE `services` (
  `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `brand` ENUM('studio', 'konstruksi', 'renovancy', 'kreasi') NOT NULL,
  `title` VARCHAR(150) NOT NULL,
  `short_desc` TEXT NULL,
  `icon` VARCHAR(100) NULL,
  `link_url` VARCHAR(255) NULL,
  `sort_order` INT NOT NULL DEFAULT 0,
  `is_published` TINYINT(1) NOT NULL DEFAULT 1,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` DATETIME NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 7. SERVICE ITEMS (Child Items / Scope)
CREATE TABLE `service_items` (
  `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `service_id` BIGINT UNSIGNED NOT NULL,
  `title` VARCHAR(150) NOT NULL,
  `description` TEXT NULL,
  `sort_order` INT NOT NULL DEFAULT 0,
  CONSTRAINT `fk_service_items_service` FOREIGN KEY (`service_id`) REFERENCES `services` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 8. PACKAGES (Design & Build Pricing Tiers)
CREATE TABLE `packages` (
  `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `service_type` ENUM('desain', 'bangun') NOT NULL,
  `name` VARCHAR(100) NOT NULL,
  `tagline` VARCHAR(255) NULL,
  `price_min` BIGINT UNSIGNED NOT NULL,
  `price_max` BIGINT UNSIGNED NULL,
  `unit` VARCHAR(20) NOT NULL DEFAULT '/m²',
  `badge` VARCHAR(50) NULL,
  `is_highlighted` TINYINT(1) NOT NULL DEFAULT 0,
  `description` TEXT NULL,
  `suitable_for` JSON NULL,
  `cta_label` VARCHAR(100) NOT NULL DEFAULT 'Pilih Paket',
  `sort_order` INT NOT NULL DEFAULT 0,
  `is_published` TINYINT(1) NOT NULL DEFAULT 1,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` DATETIME NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 9. PACKAGE SPECS (Material Specifications per Package)
CREATE TABLE `package_specs` (
  `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `package_id` BIGINT UNSIGNED NOT NULL,
  `label` VARCHAR(100) NOT NULL,
  `value` TEXT NOT NULL,
  `sort_order` INT NOT NULL DEFAULT 0,
  CONSTRAINT `fk_package_specs_package` FOREIGN KEY (`package_id`) REFERENCES `packages` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 10. PACKAGE PRICE HISTORY (Audit Price Changes)
CREATE TABLE `package_price_history` (
  `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `package_id` BIGINT UNSIGNED NOT NULL,
  `old_min` BIGINT UNSIGNED NOT NULL,
  `old_max` BIGINT UNSIGNED NULL,
  `new_min` BIGINT UNSIGNED NOT NULL,
  `new_max` BIGINT UNSIGNED NULL,
  `changed_by` BIGINT UNSIGNED NULL,
  `changed_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT `fk_pph_package` FOREIGN KEY (`package_id`) REFERENCES `packages` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_pph_user` FOREIGN KEY (`changed_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 11. PORTFOLIO CATEGORIES
CREATE TABLE `portfolio_categories` (
  `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(100) NOT NULL,
  `slug` VARCHAR(100) NOT NULL UNIQUE,
  `sort_order` INT NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 12. PORTFOLIOS (Projects & Before/After)
CREATE TABLE `portfolios` (
  `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `title` VARCHAR(150) NOT NULL,
  `slug` VARCHAR(150) NOT NULL UNIQUE,
  `category_id` BIGINT UNSIGNED NOT NULL,
  `service_type` ENUM('studio', 'konstruksi', 'design_build', 'renovasi') NOT NULL DEFAULT 'design_build',
  `location` VARCHAR(150) NULL,
  `year` VARCHAR(20) NULL,
  `status` VARCHAR(50) NOT NULL DEFAULT 'selesai',
  `short_desc` TEXT NULL,
  `before_image` VARCHAR(255) NULL,
  `after_image` VARCHAR(255) NULL,
  `cover_media_id` BIGINT UNSIGNED NULL,
  `before_media_id` BIGINT UNSIGNED NULL,
  `after_media_id` BIGINT UNSIGNED NULL,
  `is_featured` TINYINT(1) NOT NULL DEFAULT 0,
  `sort_order` INT NOT NULL DEFAULT 0,
  `is_published` TINYINT(1) NOT NULL DEFAULT 1,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` DATETIME NULL,
  CONSTRAINT `fk_portfolios_category` FOREIGN KEY (`category_id`) REFERENCES `portfolio_categories` (`id`) ON DELETE RESTRICT,
  CONSTRAINT `fk_portfolios_cover` FOREIGN KEY (`cover_media_id`) REFERENCES `media` (`id`) ON DELETE SET NULL,
  CONSTRAINT `fk_portfolios_before` FOREIGN KEY (`before_media_id`) REFERENCES `media` (`id`) ON DELETE SET NULL,
  CONSTRAINT `fk_portfolios_after` FOREIGN KEY (`after_media_id`) REFERENCES `media` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 13. PORTFOLIO IMAGES (Gallery)
CREATE TABLE `portfolio_images` (
  `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `portfolio_id` BIGINT UNSIGNED NOT NULL,
  `media_id` BIGINT UNSIGNED NOT NULL,
  `caption` VARCHAR(255) NULL,
  `sort_order` INT NOT NULL DEFAULT 0,
  CONSTRAINT `fk_pi_portfolio` FOREIGN KEY (`portfolio_id`) REFERENCES `portfolios` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_pi_media` FOREIGN KEY (`media_id`) REFERENCES `media` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 14. ADVANTAGES (8 Curated Value Propositions)
CREATE TABLE `advantages` (
  `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `title` VARCHAR(150) NOT NULL,
  `description` TEXT NOT NULL,
  `icon` VARCHAR(100) NULL,
  `sort_order` INT NOT NULL DEFAULT 0,
  `is_published` TINYINT(1) NOT NULL DEFAULT 1,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` DATETIME NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 15. PROCESS STEPS (8 Timeline Steps)
CREATE TABLE `process_steps` (
  `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `step_no` INT NOT NULL,
  `title` VARCHAR(150) NOT NULL,
  `description` TEXT NOT NULL,
  `applies_to` ENUM('studio', 'konstruksi', 'both') NOT NULL DEFAULT 'both',
  `sort_order` INT NOT NULL DEFAULT 0,
  `is_published` TINYINT(1) NOT NULL DEFAULT 1,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` DATETIME NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 16. TESTIMONIALS (Client Reviews with Consent Requirement)
CREATE TABLE `testimonials` (
  `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(100) NOT NULL,
  `city` VARCHAR(100) NULL,
  `project_name` VARCHAR(150) NULL,
  `service_type` VARCHAR(100) NULL,
  `rating` TINYINT UNSIGNED NOT NULL DEFAULT 5,
  `quote` TEXT NOT NULL,
  `photo_media_id` BIGINT UNSIGNED NULL,
  `video_url` VARCHAR(255) NULL,
  `has_consent` TINYINT(1) NOT NULL DEFAULT 0,
  `sort_order` INT NOT NULL DEFAULT 0,
  `is_published` TINYINT(1) NOT NULL DEFAULT 0,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` DATETIME NULL,
  CONSTRAINT `fk_testimonials_photo` FOREIGN KEY (`photo_media_id`) REFERENCES `media` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 17. STATS (Trust Metrics & Verified Flag)
CREATE TABLE `stats` (
  `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `value` VARCHAR(50) NOT NULL,
  `label` VARCHAR(150) NOT NULL,
  `is_verified` TINYINT(1) NOT NULL DEFAULT 0,
  `source_note` TEXT NULL,
  `sort_order` INT NOT NULL DEFAULT 0,
  `is_published` TINYINT(1) NOT NULL DEFAULT 1,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` DATETIME NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 18. FAQS (Questions & Answers with Placeholders)
CREATE TABLE `faqs` (
  `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `question` TEXT NOT NULL,
  `answer` LONGTEXT NOT NULL,
  `sort_order` INT NOT NULL DEFAULT 0,
  `is_published` TINYINT(1) NOT NULL DEFAULT 1,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` DATETIME NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 19. ARTICLES (Blog Cards & External Links)
CREATE TABLE `articles` (
  `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `title` VARCHAR(255) NOT NULL,
  `category` VARCHAR(100) NULL,
  `url` VARCHAR(255) NOT NULL,
  `image_media_id` BIGINT UNSIGNED NULL,
  `sort_order` INT NOT NULL DEFAULT 0,
  `is_published` TINYINT(1) NOT NULL DEFAULT 1,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` DATETIME NULL,
  CONSTRAINT `fk_articles_image` FOREIGN KEY (`image_media_id`) REFERENCES `media` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 20. LEADS (2-Step Submissions & Attribution Data)
CREATE TABLE `leads` (
  `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `code` VARCHAR(30) NOT NULL UNIQUE,
  `name` VARCHAR(100) NOT NULL,
  `phone` VARCHAR(30) NOT NULL,
  `phone_normalized` VARCHAR(30) NOT NULL,
  `need` VARCHAR(150) NOT NULL,
  `location` VARCHAR(150) NULL,
  `land_size` VARCHAR(50) NULL,
  `building_size_m2` INT UNSIGNED NULL,
  `floors` VARCHAR(20) NULL,
  `building_type` VARCHAR(100) NULL,
  `style` VARCHAR(100) NULL,
  `budget_range` VARCHAR(100) NULL,
  `package_choice` VARCHAR(100) NULL,
  `notes` TEXT NULL,
  `consent_at` DATETIME NULL,
  `is_complete` TINYINT(1) NOT NULL DEFAULT 0,
  `status` ENUM('baru', 'dihubungi', 'survei_dijadwalkan', 'survei_selesai', 'penawaran_dikirim', 'deal', 'batal') NOT NULL DEFAULT 'baru',
  `lost_reason` VARCHAR(255) NULL,
  `estimated_value` BIGINT UNSIGNED NULL,
  `final_service` VARCHAR(150) NULL,
  `assigned_to` BIGINT UNSIGNED NULL,
  `first_contacted_at` DATETIME NULL,
  `follow_up_at` DATETIME NULL,
  `is_duplicate` TINYINT(1) NOT NULL DEFAULT 0,
  `duplicate_of` BIGINT UNSIGNED NULL,
  `cta_origin` VARCHAR(100) NULL,
  `calc_snapshot` JSON NULL,
  `utm_source` VARCHAR(100) NULL,
  `utm_medium` VARCHAR(100) NULL,
  `utm_campaign` VARCHAR(100) NULL,
  `utm_term` VARCHAR(100) NULL,
  `utm_content` VARCHAR(100) NULL,
  `gclid` VARCHAR(100) NULL,
  `fbclid` VARCHAR(100) NULL,
  `ttclid` VARCHAR(100) NULL,
  `referrer` TEXT NULL,
  `landing_url` TEXT NULL,
  `first_touch` JSON NULL,
  `device` VARCHAR(50) NULL,
  `ip_hash` VARCHAR(64) NOT NULL,
  `user_agent` TEXT NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` DATETIME NULL,
  CONSTRAINT `fk_leads_assigned_to` FOREIGN KEY (`assigned_to`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `fk_leads_duplicate_of` FOREIGN KEY (`duplicate_of`) REFERENCES `leads` (`id`) ON DELETE SET NULL,
  
  -- MANDATORY INDEXES (Section 9)
  INDEX `idx_leads_status_created` (`status`, `created_at`),
  INDEX `idx_leads_phone_normalized` (`phone_normalized`),
  INDEX `idx_leads_utm_source` (`utm_source`),
  INDEX `idx_leads_follow_up_at` (`follow_up_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 21. LEAD ACTIVITIES (Pipeline Audit Trail)
CREATE TABLE `lead_activities` (
  `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `lead_id` BIGINT UNSIGNED NOT NULL,
  `user_id` BIGINT UNSIGNED NULL,
  `type` ENUM('status_change', 'note', 'call', 'whatsapp', 'survey', 'system') NOT NULL DEFAULT 'system',
  `from_status` VARCHAR(50) NULL,
  `to_status` VARCHAR(50) NULL,
  `note` TEXT NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT `fk_la_lead` FOREIGN KEY (`lead_id`) REFERENCES `leads` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_la_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 22. WA CLICKS (Direct WhatsApp Click Analytics)
CREATE TABLE `wa_clicks` (
  `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `cta_location` VARCHAR(100) NOT NULL,
  `utm_source` VARCHAR(100) NULL,
  `utm_medium` VARCHAR(100) NULL,
  `utm_campaign` VARCHAR(100) NULL,
  `page_url` TEXT NULL,
  `device` VARCHAR(50) NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  
  -- MANDATORY INDEX (Section 9)
  INDEX `idx_wa_clicks_created` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 23. ACTIVITY LOGS (Admin Audit Trail)
CREATE TABLE `activity_logs` (
  `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `user_id` BIGINT UNSIGNED NULL,
  `action` VARCHAR(100) NOT NULL,
  `entity` VARCHAR(100) NOT NULL,
  `entity_id` BIGINT UNSIGNED NULL,
  `before_json` JSON NULL,
  `after_json` JSON NULL,
  `ip_hash` VARCHAR(64) NOT NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT `fk_al_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 24. RATE LIMITS (Spam Prevention)
CREATE TABLE `rate_limits` (
  `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `ip_hash` VARCHAR(64) NOT NULL,
  `action` VARCHAR(50) NOT NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  INDEX `idx_rl_ip_action` (`ip_hash`, `action`, `created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- ============================================================================
-- SEED DATA: RBK STUDIO × RBK KONSTRUKSI
-- File: database/seed.sql
-- Strictly generated based on Section 11 seed content requirements
-- ============================================================================

SET FOREIGN_KEY_CHECKS = 0;

TRUNCATE TABLE `settings`;
TRUNCATE TABLE `sections`;
TRUNCATE TABLE `services`;
TRUNCATE TABLE `service_items`;
TRUNCATE TABLE `packages`;
TRUNCATE TABLE `package_specs`;
TRUNCATE TABLE `portfolio_categories`;
TRUNCATE TABLE `portfolios`;
TRUNCATE TABLE `advantages`;
TRUNCATE TABLE `process_steps`;
TRUNCATE TABLE `stats`;
TRUNCATE TABLE `faqs`;
TRUNCATE TABLE `articles`;

SET FOREIGN_KEY_CHECKS = 1;

-- 1. SETTINGS (General, Contact, SEO, Tracking, WA Template)
INSERT INTO `settings` (`key`, `value`, `group`) VALUES
('brand_name', 'Rancang Bangun Kreasi (RBK)', 'general'),
('company_pt_name', 'PT Rancang Bangun Sedaya', 'general'),
('phone_number', '+62 812-3459-3742', 'contact'),
('whatsapp_number', '6281234593742', 'contact'),
('contact_email', 'rancangbangunkreasi.official@gmail.com', 'contact'),
('office_address', 'Pasirmulya, Kota Bogor 16118', 'contact'),
('office_hours', 'Senin–Sabtu, 08.00–17.00 WIB', 'contact'),
('service_areas', 'Jakarta, Bogor, Depok, Tangerang, Bekasi (Jabodetabek)', 'contact'),
('meta_title', 'Jasa Arsitek & Kontraktor Rumah Bogor | RBK Studio & RBK Konstruksi', 'seo'),
('meta_description', 'Jasa arsitek dan kontraktor rumah di Bogor & Jabodetabek. Desain mulai Rp60.000/m², bangun mulai Rp4.000.000/m². Konsultasi & survei gratis.', 'seo'),
('wa_message_template', 'Halo RBK, saya {nama}. Kode: {kode}. Kebutuhan: {kebutuhan}. Lokasi: {lokasi}. Luas tanah: {luas_tanah}. Rencana: {lantai} lantai. Budget: {budget}. Paket: {paket}.', 'whatsapp'),
('gtm_id', '', 'tracking'),
('ga4_measurement_id', '', 'tracking'),
('meta_pixel_id', '', 'tracking'),
('tiktok_pixel_id', '', 'tracking'),
('gads_conversion_id', '', 'tracking'),
('gads_conversion_label', '', 'tracking'),
('announcement_enabled', '1', 'announcement'),
('announcement_text', 'Desain mulai {harga_desain_min}/m² · Bangun mulai {harga_bangun_min}/m² · Survei gratis Jabodetabek', 'announcement');

-- 2. SECTIONS (Landing Page Sections S0 - S21)
INSERT INTO `sections` (`key`, `eyebrow`, `title`, `subtitle`, `body`, `cta_label`, `cta_target`, `is_visible`, `sort_order`) VALUES
('s0_announcement', '', 'Desain mulai {harga_desain_min}/m² · Bangun mulai {harga_bangun_min}/m² · Survei gratis Jabodetabek', '', '', '', '', 1, 0),
('s1_navbar', '', 'RBK Studio × RBK Konstruksi', '', '', 'Konsultasi Gratis', '#konsultasi', 1, 1),
('s2_hero', 'ARCHITECTURE & PLANNING JABODETABEK', 'Jasa Arsitek & Kontraktor *Terbaik & Terlengkap* untuk Mewujudkan Bangunan Impian Anda', '<strong>Rencanakan bersama RBK Studio</strong>, <strong>bangun bersama RBK Konstruksi</strong>. Satu tim, satu alur, dari konsep hingga serah terima.', '', 'Konsultasi & Survei Gratis', '#konsultasi', 1, 2),
('s3_trust', 'PENGALAMAN & REPUTASI', 'Bukti Komitmen Kami dalam *Perencanaan & Pembangunan*', '', '', '', '', 1, 3),
('s4_problem', 'Rencanakan Sebelum Membangun', 'Bangun sekali. *Rencanakan dengan benar sejak awal.*', 'Banyak pemilik bangunan mengalami kendala akibat kurangnya perencanaan matang di awal.', '', 'Konsultasi & Survei Gratis', '#konsultasi', 1, 4),
('s5_two_paths', 'Dua Jalur Utama', 'Satu tim untuk *merencanakan dan membangun*', 'Pilih layanan yang paling sesuai dengan kebutuhan proyek Anda saat ini.', '', 'Konsultasi Sekarang', '#konsultasi', 1, 5),
('s6_studio_scope', 'RBK Studio', 'Lingkup Layanan *Arsitektur & Perencanaan*', 'Perencanaan lengkap untuk hasil bangunan yang presisi, estetis, dan sadar biaya.', '', 'Konsultasi Desain', '#konsultasi', 1, 6),
('s7_tropical_bogor', 'Kearifan Lokal Bogor', '*Desain rumah tropis* yang tangguh di cuaca Bogor', 'Didesain khusus menghadapi curah hujan tinggi, kelembaban, dan pencahayaan matahari tropis.', '', 'Diskusi Desain Tropis', '#konsultasi', 1, 7),
('s8_portfolio', 'Karya Terbaik', 'Portofolio Proyek *RBK Studio & RBK Konstruksi*', 'Jelajahi hasil transformasi sebelum-sesudah dan karya hunian serta bangunan komersial kami.', '', 'Lihat Semua Proyek', '#portofolio', 1, 8),
('s9_advantages', 'Mengapa Memilih RBK', '8 Keunggulan Utama *Rancang Bangun Kreasi*', 'Nilai tambah yang membuat proses perencanaan dan pembangunan Anda aman serta tenang.', '', '', '', 1, 9),
('s10_pricing', 'Transparansi Biaya', 'Pilihan Paket *Desain & Bangun Rumah*', 'Harga terbuka tanpa biaya tersembunyi, disesuaikan dengan kebutuhan dan anggaran Anda.', '', 'Pilih Paket', '#konsultasi', 1, 10),
('s11_calculator', 'Simulasi Anggaran', 'Kalkulator *Estimasi Biaya*', 'Hitung perkiraan awal biaya jasa desain dan pembangunan proyek Anda secara mandiri.', '', 'Kirim Estimasi & Minta RAB Gratis', '#konsultasi', 1, 11),
('s12_process', 'Tahapan Kerja', '8 Langkah Alur Kerja *Dari Konsep Hingga Serah Terima*', 'Proses terstruktur yang memastikan setiap detail proyek terpantau dengan jelas.', '', '', '', 1, 12),
('s13_survey', 'Survei Gratis', 'Tim kami *datang langsung ke lokasi*', 'Pengukuran lahan, pengecekan akses, dan diskusi awal mengenai konsep serta anggaran.', '', 'Jadwalkan Survei Gratis', '#konsultasi', 1, 13),
('s14_testimonials', 'Kata Klien', 'Pengalaman Mereka *Bersama RBK*', 'Ulasan jujur dari pemilik rumah dan pengembang bangunan komersial.', '', '', '', 0, 14),
('s15_cta_band', 'PLAN FIRST. BUILD ONCE.', 'Investasi kecil pada desain. *Dampaknya besar pada pembangunan.*', 'Jangan biarkan kesalahan di lapangan membengkakkan anggaran pembangunan Anda.', '', 'Konsultasi & Survei Gratis', '#konsultasi', 1, 15),
('s16_lead_form', 'Langkah Pertama', 'Mulai Diskusi *Proyek Impian Anda*', 'Isi formulir berikut untuk penjadwalan konsultasi dan survei gratis tanpa komitmen.', '', 'Kirim Informasi', '#', 1, 16),
('s17_about', 'Tentang Kami', 'Profil Rancang Bangun Kreasi *(& PT Rancang Bangun Sedaya)*', 'Pengalaman grup di bidang properti sejak 2007 membangun hunian berkualitas di Bogor.', '', '', '', 1, 17),
('s18_articles', 'Edukasi & Artikel', 'Wawasan Seputar *Arsitektur & Konstruksi*', 'Tips dan panduan praktis untuk Anda yang sedang merencanakan pembangunan.', '', '', '', 1, 18),
('s19_faq', 'Pertanyaan Umum', 'Frequently Asked Questions *(FAQ)*', 'Jawaban atas pertanyaan yang sering diajukan seputar layanan dan biaya RBK.', '', '', '', 1, 19),
('s20_closer', 'Penutup', 'Anda hanya perlu membangunnya *sekali.*', 'Pastikan dibangun bersama tim profesional yang berpengalaman.', '', 'Konsultasi & Survei Gratis', '#konsultasi', 1, 20),
('s21_footer', '', 'Rancang Bangun Kreasi', '', '', '', '', 1, 21);

-- 3. SERVICES (4 Sub-brands)
INSERT INTO `services` (`brand`, `title`, `short_desc`, `icon`, `link_url`, `sort_order`, `is_published`) VALUES
('studio', 'RBK Studio', 'Jasa arsitek & perencanaan lengkap. Menghasilkan konsep arsitektur, visualisasi 3D, gambar kerja, hingga RAB yang realistis.', 'architecture', '#layanan', 1, 1),
('konstruksi', 'RBK Konstruksi', 'Jasa bangun rumah & bangunan komersial. Pembangunan sesuai spesifikasi material pilihan dengan pengawasan penuh.', 'construction', '#layanan', 2, 1),
('renovancy', 'RenoVancy', 'Layanan khusus renovasi rumah total, penambahan lantai, rooftop, dan perbaikan struktur bangunan.', 'home_repair_service', '#layanan', 3, 1),
('kreasi', 'RBK Kreasi', 'Layanan perancangan interior custom, lanskap taman tropis, dan fasad estetis.', 'decor', '#layanan', 4, 1);

-- 4. PACKAGES (Section 11.1 & 11.2)
INSERT INTO `packages` (`service_type`, `name`, `tagline`, `price_min`, `price_max`, `unit`, `badge`, `is_highlighted`, `description`, `suitable_for`, `cta_label`, `sort_order`, `is_published`) VALUES
('desain', 'Basic', 'Konsep Arsitektur & Perencanaan Dasar', 60000, NULL, '/m²', NULL, 0, 'Sangat cocok untuk rumah tinggal sederhana, renovasi, dan pemetaan denah awal.', '["Rumah Tinggal Sederhana", "Renovasi Rumah", "Konsep Denah Dasar"]', 'Pilih Paket Basic', 1, 1),
('desain', 'Standard', 'Best Balance Between Design × Detail × Investment', 80000, NULL, '/m²', 'Recommended', 1, 'Paket paling diminati. Kelengkapan gambar kerja detail dan 3D visualisasi presisi.', '["Rumah Premium", "Ruko Komersial", "Renovasi Besar", "Bangunan Kost"]', 'Pilih Paket Standard', 2, 1),
('desain', 'Premium', 'Signature Service for Custom & Complex Architecture', 150000, NULL, '/m²', 'Signature Service', 0, 'Perencanaan eksklusif untuk bangunan berarsitektur tinggi dan fasilitas kompleks.', '["Luxury Residential", "Villa Eksklusif", "Commercial Building", "Premium Ruko"]', 'Pilih Paket Premium', 3, 1),

('bangun', 'Basic', 'Struktur Praktis & Material Pilihan Berkualitas', 4000000, 4500000, '/m²', NULL, 0, 'Paket efisien untuk pembangunan hunian aman, nyaman, dan tahan lama.', '["Rumah Sederhana 1-2 Lantai", "Bangunan Efisien"]', 'Pilih Paket Bangun Basic', 4, 1),
('bangun', 'Standard', 'Material Berstandar Tinggi & Finishing Rapi', 4500000, 5000000, '/m²', 'Paling seimbang', 1, 'Paket terfavorit dengan spesifikasi material modern, aluminium Alexindo, dan sanitair bergaransi.', '["Rumah Standard 2-3 Lantai", "Kost Modern", "Ruko Komersial"]', 'Pilih Paket Bangun Standard', 5, 1),
('bangun', 'Premium', 'Finishing Mewah, Granit, Bata Merah & Sanitair Toto', 6000000, 7500000, '/m²', 'Mewah & Kompleks', 0, 'Spesifikasi material kelas atas dengan pengerjaan ketat dan detail eksklusif.', '["Hunian Mewah", "Villa Tropis", "Headquarter Office"]', 'Pilih Paket Bangun Premium', 6, 1);

-- 5. PACKAGE SPECS (Section 11.2 Specification Tables)
INSERT INTO `package_specs` (`package_id`, `label`, `value`, `sort_order`) VALUES
-- Bangun Basic (ID 4)
(4, 'Struktur', 'Kolom praktis, pondasi batu kali', 1),
(4, 'Lantai Utama', 'Keramik Roman 40x40 / 50x50', 2),
(4, 'Kusen & Pintu', 'Kayu Meranti / Aluminium standard', 3),
(4, 'Cat & Finishing', 'Cat interior/eksterior Vinilex', 4),
(4, 'Rangka & Atap', 'Baja ringan, genteng metal berpasir', 5),
(4, 'Sanitair', 'Setara INA', 6),

-- Bangun Standard (ID 5)
(5, 'Struktur', 'Kolom beton bertulang, pondasi batu kali', 1),
(5, 'Dinding', 'Bata hebel, plester aci rapi', 2),
(5, 'Kusen & Jendela', 'Aluminium 3" / 4" Powder Coating', 3),
(5, 'Plafon', 'Gypsum board + rangka hollow galvanis', 4),
(5, 'Rangka & Atap', 'Baja ringan SNI, genteng metal/beton', 5),
(5, 'Sanitair', 'Setara American Standard / Onda', 6),

-- Bangun Premium (ID 6)
(6, 'Struktur', 'Footplate (cakar ayam) / batu kali / beton bertulang berat', 1),
(6, 'Dinding', 'Bata merah press ex-garansi', 2),
(6, 'Lantai Utama', 'Granit Tile 60x60 / 80x80 / Marmer', 3),
(6, 'Kusen & Jendela', 'Aluminium 4" Alexindo / Kayu Kamper Samarinda', 4),
(6, 'Cat & Listrik', 'Cat Dulux Weathershield, instalasi kabel Supreme & Saklar Panasonic', 5),
(6, 'Sanitair', 'Setara TOTO lengkap', 6);

-- 6. PORTFOLIO CATEGORIES
INSERT INTO `portfolio_categories` (`id`, `name`, `slug`, `sort_order`) VALUES
(1, 'Rumah', 'rumah', 1),
(2, 'Kost', 'kost', 2),
(3, 'Ruko & Komersial', 'ruko-komersial', 3),
(4, 'Kantor & Gudang', 'kantor-gudang', 4),
(5, 'Renovasi', 'renovasi', 5);

-- 7. PORTFOLIOS (Section 11.3)
INSERT INTO `portfolios` (`title`, `slug`, `category_id`, `service_type`, `location`, `year`, `status`, `short_desc`, `is_featured`, `sort_order`, `is_published`) VALUES
('RenoVancy Rumah Mr. Putra', 'renovancy-rumah-mr-putra', 5, 'renovasi', 'Bogor', '2023', 'selesai', 'Transformasi total rumah tinggal menjadi hunian tropis modern.', 1, 1, 1),
('RenoVancy Rumah Mrs. Sela', 'renovancy-rumah-mrs-sela', 5, 'renovasi', 'Bogor', '2023', 'selesai', 'Penambahan area rooftop dan renovasi fasad lantai 2.', 1, 2, 1),
('Ruko Cigiringsing', 'ruko-cigiringsing', 3, 'design_build', 'Bogor', '2022', 'selesai', 'Pembangunan ruko commercial dari lahan kosong hingga serah terima kunci.', 1, 3, 1),
('Casa Nawasena Cluster Kost', 'casa-nawasena-cluster-kost', 2, 'design_build', 'Dramaga, Bogor', '2023', 'selesai', 'Desain dan pembangunan kawasan kost modern produktif.', 1, 4, 1),
('Chillax Kost Dramaga', 'chillax-kost-dramaga', 2, 'design_build', 'Dramaga, Bogor', '2023', 'selesai', 'Kost eksklusif dekat kampus dengan efisiensi tata ruang tinggi.', 1, 5, 1),
('AB House', 'ab-house', 1, 'studio', 'Bogor', '2023', 'selesai', 'Perencanaan desain rumah tinggal tropis 2 lantai.', 1, 6, 1),
('Arsya House', 'arsya-house', 1, 'studio', 'Bogor', '2022', 'selesai', 'Desain rumah 1 lantai gaya modern minimalis.', 0, 7, 1),
('AR\' House', 'ar-house', 1, 'studio', 'Bogor', '2022', 'selesai', 'Perencanaan hunian keluarga berkonsep terbukanya sirkulasi udara.', 0, 8, 1),
('La Bella Office & Warehouse', 'la-bella-office-warehouse', 4, 'design_build', 'Bogor', '2023', 'selesai', 'Pembangunan kompleks kantor dan pergudangan terpadu.', 1, 9, 1),
('Sinergy Office & Warehouse', 'sinergy-office-warehouse', 4, 'design_build', 'Bogor', '2023', 'selesai', 'Desain dan pelaksanaan konstruksi fasilitas kantor serta pergudangan.', 0, 10, 1);

-- 8. STATS (Section 11.4)
INSERT INTO `stats` (`value`, `label`, `is_verified`, `source_note`, `sort_order`, `is_published`) VALUES
('2007', 'Awal pengalaman grup di bidang properti', 1, 'Pengalaman grup induk sejak 2007', 1, 1),
('1.350+', 'Project dikerjakan', 0, '[VERIFIKASI] Perlu konfirmasi total invoice', 2, 1),
('3 + 1', 'Perumahan & area komersial Eltama Property di Bogor', 0, '[VERIFIKASI] Perumahan Eltama Property', 3, 1),
('Gratis', 'Konsultasi & survei (Jabodetabek)', 1, 'Kebijakan layanan bebas biaya survei awal', 4, 1);

-- 9. ADVANTAGES (Section 11.5)
INSERT INTO `advantages` (`title`, `description`, `icon`, `sort_order`, `is_published`) VALUES
('Satu tim, desain sampai bangun', 'RBK Studio, RBK Konstruksi, dan RBK Kreasi bekerja dalam satu alur yang terintegrasi.', 'integration_instructions', 1, 1),
('Desain yang siap dibangun', 'Bukan sekadar gambar bagus di render, tetapi benar-benar dihitung agar siap direalisasikan.', 'architecture', 2, 1),
('Harga per m² terbuka', 'Paket harga desain dan bangun ditulis transparan sejak awal sesuai anggaran Anda.', 'payments', 3, 1),
('Spesifikasi material jelas', 'Merek dan jenis material tiap paket ditulis detail, dari pondasi batu kali hingga sanitair.', 'fact_check', 4, 1),
('Konsultasi & survei gratis', 'Bebas biaya survei lokasi untuk wilayah Jabodetabek sebelum Anda mengambil keputusan.', 'center_focus_strong', 5, 1),
('Perencanaan sadar biaya', 'Estimasi kebutuhan biaya dipetakan presisi lewat RAB sebelum konstruksi dimulai.', 'calculate', 6, 1),
('Pengalaman developer', 'Grup induk kami berpengalaman membangun perumahan Eltama Property di Kota & Kab. Bogor.', 'domain', 7, 1),
('Kantor di Bogor & tim profesional', 'Dekat dengan lokasi proyek Anda dengan pengawas lapangan dan tukang berpengalaman.', 'location_city', 8, 1);

-- 10. PROCESS STEPS (Section 11.7)
INSERT INTO `process_steps` (`step_no`, `title`, `description`, `applies_to`, `sort_order`, `is_published`) VALUES
(1, 'Konsultasi Gratis', 'Memahami kebutuhan, kondisi lahan, fungsi bangunan, gaya arsitektur, dan anggaran Anda.', 'both', 1, 1),
(2, 'Survei Lokasi Gratis', 'Tim kami mendatangi lokasi di Jabodetabek untuk mengukur lahan, mengecek tanah dan akses jalan.', 'both', 2, 1),
(3, 'Konsep & Layout', 'Penyusunan zoning ruang, flow sirkulasi, dan denah 2D awal sesuai keinginan Anda.', 'studio', 3, 1),
(4, 'Pengembangan Desain & 3D', 'Pembuatan visualisasi 3D exterior dan interior presisi tinggi untuk memberikan gambaran wujud bangunan.', 'studio', 4, 1),
(5, 'Gambar Kerja & MEP', 'Penyusunan dokumen teknis arsitektur, rencana struktur beton/baja, serta instalasi mekanikal, elektrikal, dan plumbing.', 'studio', 5, 1),
(6, 'RAB & Penawaran', 'Penyusunan Rencana Anggaran Biaya detail berdasar spesifikasi material resmi.', 'both', 6, 1),
(7, 'Konstruksi & Pengawasan', 'Pelaksanaan pembangunan fisik di lapangan oleh tim spesialis di bawah pengawasan berkala tim pengawas.', 'konstruksi', 7, 1),
(8, 'Serah Terima', 'Pemeriksaan akhir bersama klien dan penyerahan kunci bangunan beserta masa garansi pemeliharaan.', 'konstruksi', 8, 1);

-- 11. FAQS (Section 11.8 - Placeholders Replacement System)
INSERT INTO `faqs` (`question`, `answer`, `sort_order`, `is_published`) VALUES
('Berapa biaya jasa desain di RBK Studio?', 'Biaya jasa desain kami terbagi menjadi 3 pilihan paket: Paket Basic {{harga_desain_basic}}/m², Paket Standard {{harga_desain_standard}}/m², dan Paket Premium {{harga_desain_premium}}/m².', 1, 1),
('Berapa biaya bangun rumah di Bogor?', 'Estimasi biaya bangun di wilayah Bogor mulai dari Paket Basic {{harga_bangun_basic}}/m², Paket Standard {{harga_bangun_standard}}/m², hingga Paket Premium {{harga_bangun_premium}}/m², tergantung spesifikasi material yang dipilih. Biaya final ditetapkan dalam RAB setelah survei lokasi.', 2, 1),
('Apakah konsultasi dan survei benar-benar gratis?', 'Ya, konsultasi awal dan survei pengukuran lokasi benar-benar gratis (bebas biaya) untuk wilayah Jakarta, Bogor, Depok, Tangerang, dan Bekasi (Jabodetabek).', 3, 1),
('Kalau saya hanya butuh gambar desain saja, apakah bisa?', 'Bisa. Layanan RBK Studio melayani pembuatan desain arsitektur saja mulai dari {{harga_desain_min}}/m². Sebagai gambaran, rumah dengan luas 120 m² Paket Basic berkisar {{contoh_desain_120}}.', 4, 1),
('Bisakah desain di RBK Studio lalu dibangun oleh RBK Konstruksi?', 'Sangat bisa. RBK Studio dan RBK Konstruksi berada dalam satu tim dan satu alur kerja, sehingga dokumen perencanaan desain langsung diteruskan ke tim konstruksi tanpa perlu adaptasi ulang.', 5, 1),
('Apakah RBK melayani renovasi dan bangunan komersial?', 'Ya, kami melayani proyek renovasi rumah (RenoVancy), serta pembangunan bangunan komersial seperti kost-kostan, ruko, kantor, dan gudang. Contohnya proyek Casa Nawasena Cluster Kost dan La Bella Office & Warehouse.', 6, 1),
('Apakah harga paket bangun berlaku di luar wilayah Bogor?', 'Harga paket per m² yang tertera berlaku utama untuk wilayah Bogor. Untuk lokasi Jabodetabek lainnya (Jakarta, Depok, Tangerang, Bekasi), angka final ditetapkan setelah survei lokasi. [VERIFIKASI]', 7, 1),
('Di mana alamat kantor RBK dan bagaimana jam operasionalnya?', 'Kantor kami berlokasi di Pasirmulya, Kota Bogor 16118. Kami buka setiap hari Senin hingga Sabtu dari pukul 08.00 sampai 17.00 WIB.', 8, 1);

-- 12. ARTICLES (Section 11.6)
INSERT INTO `articles` (`title`, `category`, `url`, `sort_order`, `is_published`) VALUES
('Tips Merencanakan Anggaran Biaya Bangun Rumah Agar Tidak Pembengkakan', 'Perencanaan', 'https://rancangbangunkreasi.id/artikel/tips-rencana-biaya-bangun', 1, 1),
('Panduan Memilih Desain Rumah Tropis Modern yang Tepat untuk Iklim Bogor', 'Desain', 'https://rancangbangunkreasi.id/artikel/desain-rumah-tropis-bogor', 2, 1),
('Mengenal Alur Kerja Jasa Arsitek dari Konsep Hingga Gambar Kerja Detail', 'Edukasi', 'https://rancangbangunkreasi.id/artikel/alur-kerja-jasa-arsitek', 3, 1);


-- ============================================================================
-- INITIAL SUPER ADMIN ACCOUNT
-- ============================================================================
INSERT INTO `users` (`id`, `name`, `email`, `password_hash`, `role`, `is_active`, `must_change_password`, `created_at`) VALUES
(1, 'Super Admin RBK', 'admin@rancangbangunkreasi.id', '$2y$12$hgWsSqFZ0ZKq0PENQlXcyuNGoHN9nrxZcPgHpaeY1cQyrnfbZQnB.', 'super_admin', 1, 0, NOW())
ON DUPLICATE KEY UPDATE `password_hash` = VALUES(`password_hash`), `is_active` = 1;
