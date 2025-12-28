SET FOREIGN_KEY_CHECKS = 0;
DROP TABLE IF EXISTS `photos`;
DROP TABLE IF EXISTS `ads`;
DROP TABLE IF EXISTS `categories`;
DROP TABLE IF EXISTS `users`;
SET FOREIGN_KEY_CHECKS = 1;

-- 1. Users
CREATE TABLE `users` (
    `id` INT NOT NULL AUTO_INCREMENT,
    `email` VARCHAR(150) NOT NULL,
    `password` VARCHAR(255) NOT NULL,
    `role` ENUM('admin', 'user') DEFAULT 'user',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 2. Categories
CREATE TABLE `categories` (
    `id` INT NOT NULL AUTO_INCREMENT,
    `name` VARCHAR(50) NOT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 3. Ads
CREATE TABLE `ads` (
    `id` INT NOT NULL AUTO_INCREMENT,
    `title` VARCHAR(30) NOT NULL,
    `description` TEXT NOT NULL,
    `price` DECIMAL(10, 2) NOT NULL DEFAULT 0.00,
    `delivery_type` ENUM('postal', 'hand', 'both') NOT NULL,
    `category_id` INT NOT NULL,
    `seller_id` INT NOT NULL,
    `buyer_id` INT DEFAULT NULL,
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
    `id` INT NOT NULL AUTO_INCREMENT,
    `ad_id` INT NOT NULL,
    `filename` VARCHAR(255) NOT NULL,
    `is_primary` TINYINT(1) DEFAULT 0,
    PRIMARY KEY (`id`),
    INDEX (`ad_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

ALTER TABLE `ads` ADD CONSTRAINT `fk_category` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE;
ALTER TABLE `ads` ADD CONSTRAINT `fk_seller` FOREIGN KEY (`seller_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
ALTER TABLE `ads` ADD CONSTRAINT `fk_buyer` FOREIGN KEY (`buyer_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;
ALTER TABLE `photos` ADD CONSTRAINT `fk_ad_photos` FOREIGN KEY (`ad_id`) REFERENCES `ads` (`id`) ON DELETE CASCADE;

INSERT INTO `categories` (`name`) VALUES ('Électronique'), ('Vêtements'), ('Maison'), ('Loisirs'), ('Autres');
-- admin@ebazar.fr / admin123
INSERT INTO `users` (`email`, `password`, `role`) VALUES ('admin@ebazar.fr', '$2y$10$8K9p/.0H9fN6ID7Yp7Yp7u.p7Yp7Yp7Yp7Yp7Yp7Yp7Yp7Yp7Yp7Y', 'admin');