SET FOREIGN_KEY_CHECKS = 0;
DROP TABLE IF EXISTS `photos`;
DROP TABLE IF EXISTS `ads`;
DROP TABLE IF EXISTS `categories`;
DROP TABLE IF EXISTS `users`;
SET FOREIGN_KEY_CHECKS = 1;

-- 1. Users
CREATE TABLE `users` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `name` VARCHAR(100) NOT NULL,
    `email` VARCHAR(150) NOT NULL,
    `password` VARCHAR(255) NOT NULL,
    `role` ENUM('admin', 'user') DEFAULT 'user',
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

ALTER TABLE `ads` ADD CONSTRAINT `fk_category` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE;
ALTER TABLE `ads` ADD CONSTRAINT `fk_seller` FOREIGN KEY (`seller_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
ALTER TABLE `ads` ADD CONSTRAINT `fk_buyer` FOREIGN KEY (`buyer_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;
ALTER TABLE `photos` ADD CONSTRAINT `fk_ad_photos` FOREIGN KEY (`ad_id`) REFERENCES `ads` (`id`) ON DELETE CASCADE;

-- Seed data: Admin user (email: admin@ebazar.fr, password: admin123)
INSERT INTO `users` (`name`, `email`, `password`, `role`) VALUES 
('Administrator', 'admin@ebazar.fr', '$2y$12$pe8b9tzdu8z/Qh6Ve4T2I.x7UUcZYE/GsEewSFvoXQVk8gmqYvzAy', 'admin');

-- Seed data: Default categories
INSERT INTO `categories` (`name`) VALUES 
('Électronique'), 
('Vêtements'), 
('Maison'), 
('Loisirs'), 
('Autres');