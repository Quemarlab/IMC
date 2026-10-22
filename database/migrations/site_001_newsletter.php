<?php
declare(strict_types=1);

use YM\Core\Migrator;

/** Newsletter: double opt-in, topic preferences, and automatic emails when news / projects are published. */
return function (PDO $pdo): void {
    $add = static function (string $table, string $col, string $def) use ($pdo): void {
        if (!Migrator::hasColumn($table, $col)) {
            $pdo->exec("ALTER TABLE `$table` ADD COLUMN `$col` $def");
        }
    };

    $pdo->exec("ALTER TABLE subscribers MODIFY `status` ENUM('pending','active','unsubscribed') NOT NULL DEFAULT 'pending'");
    $add('subscribers', 'want_news', 'TINYINT(1) NOT NULL DEFAULT 1');
    $add('subscribers', 'want_projects', 'TINYINT(1) NOT NULL DEFAULT 1');
    $fresh = !Migrator::hasColumn('subscribers', 'confirmed_at');
    $add('subscribers', 'confirmed_at', 'DATETIME NULL');
    $add('subscribers', 'unsubscribed_at', 'DATETIME NULL');
    if ($fresh) {
        // Everyone who subscribed before double opt-in existed stays subscribed.
        $pdo->exec("UPDATE subscribers SET confirmed_at = created_at WHERE status = 'active'");
        $pdo->exec("UPDATE subscribers SET unsubscribed_at = created_at WHERE status = 'unsubscribed'");
    }

    foreach (['posts', 'projects'] as $t) {
        $add($t, 'notify_subscribers', 'TINYINT(1) NOT NULL DEFAULT 0');
        $add($t, 'notified_at', 'DATETIME NULL');
    }

    $pdo->exec("CREATE TABLE IF NOT EXISTS `newsletter_campaigns` (
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
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

    $pdo->exec("CREATE TABLE IF NOT EXISTS `newsletter_deliveries` (
        `campaign_id`   INT UNSIGNED NOT NULL,
        `subscriber_id` INT UNSIGNED NOT NULL,
        `status`        ENUM('sent','failed') NOT NULL,
        `attempted_at`  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (`campaign_id`, `subscriber_id`),
        KEY `idx_delivery_day` (`status`, `attempted_at`),
        CONSTRAINT `fk_delivery_campaign` FOREIGN KEY (`campaign_id`) REFERENCES `newsletter_campaigns` (`id`) ON DELETE CASCADE,
        CONSTRAINT `fk_delivery_subscriber` FOREIGN KEY (`subscriber_id`) REFERENCES `subscribers` (`id`) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
};