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
