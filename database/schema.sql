-- Young Masters LTD — application schema (MySQL 5.7+/MariaDB 10.4+)
-- Safe to run repeatedly: every statement is CREATE TABLE IF NOT EXISTS.
-- Install:  php database/install.php            (see that file for options)

SET NAMES utf8mb4;

CREATE TABLE IF NOT EXISTS `users` (
  `id`            INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name`          VARCHAR(120) NOT NULL,
  `email`         VARCHAR(190) NOT NULL,
  `password_hash` VARCHAR(255) NOT NULL,
  `status`        ENUM('active','disabled') NOT NULL DEFAULT 'active',
  `totp_secret`         VARCHAR(64) NULL,
  `totp_enabled_at`     DATETIME NULL,
  `totp_last_step`      INT UNSIGNED NOT NULL DEFAULT 0,
  `recovery_codes`      TEXT NULL,
  `password_changed_at` DATETIME NULL,
  `last_login_at` DATETIME NULL,
  `created_at`    DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_users_email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- What each account may do in each application ('site' = website admin, 'ump' = production system).
CREATE TABLE IF NOT EXISTS `user_access` (
  `user_id` INT UNSIGNED NOT NULL,
  `app`     VARCHAR(20) NOT NULL,
  `role`    VARCHAR(20) NOT NULL,
  `notify`  TINYINT(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`user_id`, `app`),
  CONSTRAINT `fk_access_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `login_attempts` (
  `id`         INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `email`      VARCHAR(190) NOT NULL,
  `ip`         VARCHAR(45) NOT NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_attempts_email` (`email`, `created_at`),
  KEY `idx_attempts_ip` (`ip`, `created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `password_resets` (
  `id`         INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id`    INT UNSIGNED NOT NULL,
  `token_hash` CHAR(64) NOT NULL,
  `expires_at` DATETIME NOT NULL,
  `used_at`    DATETIME NULL,
  PRIMARY KEY (`id`),
  KEY `idx_resets_token` (`token_hash`),
  CONSTRAINT `fk_resets_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `rate_limits` (
  `bucket`       VARCHAR(120) NOT NULL,
  `hits`         INT UNSIGNED NOT NULL DEFAULT 1,
  `window_start` DATETIME NOT NULL,
  PRIMARY KEY (`bucket`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `services` (
  `id`           INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `title`        VARCHAR(160) NOT NULL,
  `slug`         VARCHAR(160) NOT NULL,
  `icon`         VARCHAR(60) NOT NULL DEFAULT 'bi-gear',
  `summary`      VARCHAR(300) NOT NULL DEFAULT '',
  `body`         MEDIUMTEXT NULL,
  `cover_image`  VARCHAR(190) NULL,
  `sort_order`   INT NOT NULL DEFAULT 0,
  `is_published` TINYINT(1) NOT NULL DEFAULT 1,
  `created_at`   DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`   DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_services_slug` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `projects` (
  `id`           INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `title`        VARCHAR(160) NOT NULL,
  `slug`         VARCHAR(160) NOT NULL,
  `category`     VARCHAR(80) NOT NULL DEFAULT '',
  `client`       VARCHAR(160) NOT NULL DEFAULT '',
  `location`     VARCHAR(120) NOT NULL DEFAULT '',
  `completed_on` DATE NULL,
  `summary`      VARCHAR(300) NOT NULL DEFAULT '',
  `body`         MEDIUMTEXT NULL,
  `cover_image`  VARCHAR(190) NULL,
  `is_featured`  TINYINT(1) NOT NULL DEFAULT 0,
  `is_published` TINYINT(1) NOT NULL DEFAULT 1,
  `notify_subscribers` TINYINT(1) NOT NULL DEFAULT 0,
  `notified_at` DATETIME NULL,
  `created_at`   DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`   DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_projects_slug` (`slug`),
  KEY `idx_projects_list` (`is_published`, `completed_on`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `posts` (
  `id`           INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `title`        VARCHAR(190) NOT NULL,
  `slug`         VARCHAR(190) NOT NULL,
  `category`     VARCHAR(80) NOT NULL DEFAULT 'News',
  `excerpt`      VARCHAR(300) NOT NULL DEFAULT '',
  `body`         MEDIUMTEXT NULL,
  `cover_image`  VARCHAR(190) NULL,
  `author_id`    INT UNSIGNED NULL,
  `is_published` TINYINT(1) NOT NULL DEFAULT 1,
  `notify_subscribers` TINYINT(1) NOT NULL DEFAULT 0,
  `notified_at` DATETIME NULL,
  `published_at` DATETIME NULL,
  `created_at`   DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`   DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_posts_slug` (`slug`),
  KEY `idx_posts_list` (`is_published`, `published_at`),
  CONSTRAINT `fk_posts_author` FOREIGN KEY (`author_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `gallery_items` (
  `id`           INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `title`        VARCHAR(160) NOT NULL DEFAULT '',
  `category`     VARCHAR(80) NOT NULL DEFAULT '',
  `image`        VARCHAR(190) NOT NULL,
  `sort_order`   INT NOT NULL DEFAULT 0,
  `is_published` TINYINT(1) NOT NULL DEFAULT 1,
  `created_at`   DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_gallery_list` (`is_published`, `sort_order`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `messages` (
  `id`         INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name`       VARCHAR(120) NOT NULL,
  `email`      VARCHAR(190) NOT NULL,
  `phone`      VARCHAR(40) NOT NULL DEFAULT '',
  `subject`    VARCHAR(190) NOT NULL DEFAULT '',
  `body`       TEXT NOT NULL,
  `status`     ENUM('new','read','replied','archived') NOT NULL DEFAULT 'new',
  `ip`         VARCHAR(45) NOT NULL DEFAULT '',
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_messages_status` (`status`, `created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `subscribers` (
  `id`         INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `email`      VARCHAR(190) NOT NULL,
  `token`      CHAR(32) NOT NULL,
  `status`     ENUM('pending','active','unsubscribed') NOT NULL DEFAULT 'pending',
  `want_news`     TINYINT(1) NOT NULL DEFAULT 1,
  `want_projects` TINYINT(1) NOT NULL DEFAULT 1,
  `confirmed_at`  DATETIME NULL,
  `unsubscribed_at` DATETIME NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_subscribers_email` (`email`),
  UNIQUE KEY `uq_subscribers_token` (`token`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `site_settings` (
  `skey`  VARCHAR(60) NOT NULL,
  `value` TEXT NOT NULL,
  PRIMARY KEY (`skey`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `audit_logs` (
  `id`         INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `app`        VARCHAR(20) NOT NULL DEFAULT 'site',
  `user_id`    INT UNSIGNED NULL,
  `action`     VARCHAR(60) NOT NULL,
  `entity`     VARCHAR(60) NOT NULL DEFAULT '',
  `entity_id`  INT UNSIGNED NULL,
  `detail`     VARCHAR(255) NOT NULL DEFAULT '',
  `ip`         VARCHAR(45) NOT NULL DEFAULT '',
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_audit_created` (`created_at`),
  KEY `idx_audit_app` (`app`, `id`),
  CONSTRAINT `fk_audit_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `schema_migrations` (
  `id`         VARCHAR(120) NOT NULL,
  `applied_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Emails sent to subscribers when news / projects are published. One campaign per announcement; one delivery row per recipient (resumable).
CREATE TABLE IF NOT EXISTS `newsletter_campaigns` (
  `id`          INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `kind`        ENUM('news','project') NOT NULL,
  `item_id`     INT UNSIGNED NOT NULL,
  `subject`     VARCHAR(190) NOT NULL,
  `status`      ENUM('sending','done','cancelled') NOT NULL DEFAULT 'sending',
  `total`       INT UNSIGNED NOT NULL DEFAULT 0,
  `sent`        INT UNSIGNED NOT NULL DEFAULT 0,
  `failed`      INT UNSIGNED NOT NULL DEFAULT 0,
  `created_by`  INT UNSIGNED NULL,
  `created_at`  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `finished_at` DATETIME NULL,
  PRIMARY KEY (`id`),
  KEY `idx_campaign_status` (`status`, `id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `newsletter_deliveries` (
  `campaign_id`   INT UNSIGNED NOT NULL,
  `subscriber_id` INT UNSIGNED NOT NULL,
  `status`        ENUM('sent','failed') NOT NULL,
  `attempted_at`  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`campaign_id`, `subscriber_id`),
  KEY `idx_delivery_day` (`status`, `attempted_at`),
  CONSTRAINT `fk_delivery_campaign` FOREIGN KEY (`campaign_id`) REFERENCES `newsletter_campaigns` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_delivery_subscriber` FOREIGN KEY (`subscriber_id`) REFERENCES `subscribers` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
