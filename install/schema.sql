SET FOREIGN_KEY_CHECKS = 0;
DROP TABLE IF EXISTS `photos`;
DROP TABLE IF EXISTS `ads`;
DROP TABLE IF EXISTS `categories`;
DROP TABLE IF EXISTS `users`;
DROP TABLE IF EXISTS `transactions`;
SET FOREIGN_KEY_CHECKS = 1;

-- 1. Users
CREATE TABLE `users` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `name` VARCHAR(100) NOT NULL,
    `email` VARCHAR(150) NOT NULL,
    `password` VARCHAR(255) NOT NULL,
    `role` ENUM('admin', 'user') DEFAULT 'user',
    `balance` DECIMAL(10, 2) NOT NULL DEFAULT 0.00,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uniq_email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 2. Categories
CREATE TABLE `categories` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `name` VARCHAR(50) NOT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 3. Ads
CREATE TABLE `ads` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `title` VARCHAR(30) NOT NULL,
    `description` TEXT NOT NULL,
    `price` DECIMAL(10, 2) NOT NULL DEFAULT 0.00,
    `delivery_type` VARCHAR(100) NOT NULL,
    `category_id` INT UNSIGNED NOT NULL,
    `seller_id` INT UNSIGNED NOT NULL,
    `buyer_id` INT UNSIGNED DEFAULT NULL,
    `is_sold` TINYINT(1) DEFAULT 0,
    `is_received` TINYINT(1) DEFAULT 0,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    INDEX (`category_id`),
    INDEX (`seller_id`),
    INDEX (`buyer_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 4. Photos
CREATE TABLE `photos` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `ad_id` INT UNSIGNED NOT NULL,
    `filename` VARCHAR(255) NOT NULL,
    `is_primary` TINYINT(1) DEFAULT 0,
    PRIMARY KEY (`id`),
    INDEX (`ad_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 5. Transactions
CREATE TABLE `transactions` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `ad_id` INT UNSIGNED DEFAULT NULL,
    `user_id` INT UNSIGNED DEFAULT NULL,
    `buyer_id` INT UNSIGNED DEFAULT NULL,
    `seller_id` INT UNSIGNED DEFAULT NULL,
    `amount` DECIMAL(10, 2) NOT NULL,
    `type` ENUM('purchase', 'topup') NOT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    INDEX (`user_id`),
    INDEX (`buyer_id`),
    INDEX (`seller_id`),
    INDEX (`ad_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

ALTER TABLE `ads` ADD CONSTRAINT `fk_category` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE;
ALTER TABLE `ads` ADD CONSTRAINT `fk_seller` FOREIGN KEY (`seller_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
ALTER TABLE `ads` ADD CONSTRAINT `fk_buyer` FOREIGN KEY (`buyer_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;
ALTER TABLE `photos` ADD CONSTRAINT `fk_ad_photos` FOREIGN KEY (`ad_id`) REFERENCES `ads` (`id`) ON DELETE CASCADE;