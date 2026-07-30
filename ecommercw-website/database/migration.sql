-- =========================================================
-- Migration: Add new features to existing ShopEase database
-- Run this file to add new columns, tables, and indexes
-- without dropping existing data.
-- =========================================================

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

USE `shop`;

-- -------------------------------------------------
-- Add stock, sku, status to products
-- -------------------------------------------------
ALTER TABLE `products`
  ADD COLUMN `stock` INT NOT NULL DEFAULT 0 AFTER `featured`,
  ADD COLUMN `sku` VARCHAR(100) DEFAULT NULL AFTER `stock`,
  ADD COLUMN `status` ENUM('active','hidden') NOT NULL DEFAULT 'active' AFTER `sku`,
  ADD INDEX `idx_products_status` (`status`);

-- -------------------------------------------------
-- Table: carts
-- -------------------------------------------------
CREATE TABLE IF NOT EXISTS `carts` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` INT UNSIGNED NOT NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_carts_user_id` (`user_id`),
  CONSTRAINT `fk_carts_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -------------------------------------------------
-- Table: cart_items
-- -------------------------------------------------
CREATE TABLE IF NOT EXISTS `cart_items` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `cart_id` INT UNSIGNED NOT NULL,
  `product_id` INT UNSIGNED NOT NULL,
  `quantity` INT NOT NULL DEFAULT 1,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_cart_items_cart_product` (`cart_id`, `product_id`),
  KEY `idx_cart_items_cart_id` (`cart_id`),
  KEY `idx_cart_items_product_id` (`product_id`),
  CONSTRAINT `fk_cart_items_cart` FOREIGN KEY (`cart_id`) REFERENCES `carts` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_cart_items_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -------------------------------------------------
-- Table: tickets
-- -------------------------------------------------
CREATE TABLE IF NOT EXISTS `tickets` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `ticket_number` VARCHAR(20) NOT NULL,
  `user_id` INT UNSIGNED NOT NULL,
  `subject` VARCHAR(255) NOT NULL,
  `status` ENUM('open','waiting_admin','waiting_user','closed') NOT NULL DEFAULT 'open',
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_tickets_ticket_number` (`ticket_number`),
  KEY `idx_tickets_user_id` (`user_id`),
  KEY `idx_tickets_status` (`status`),
  CONSTRAINT `fk_tickets_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -------------------------------------------------
-- Table: ticket_messages
-- -------------------------------------------------
CREATE TABLE IF NOT EXISTS `ticket_messages` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `ticket_id` INT UNSIGNED NOT NULL,
  `user_id` INT UNSIGNED NOT NULL,
  `message` TEXT NOT NULL,
  `image` VARCHAR(255) DEFAULT NULL,
  `read_at` DATETIME DEFAULT NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_ticket_messages_ticket_id` (`ticket_id`),
  KEY `idx_ticket_messages_user_id` (`user_id`),
  CONSTRAINT `fk_ticket_messages_ticket` FOREIGN KEY (`ticket_id`) REFERENCES `tickets` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_ticket_messages_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -------------------------------------------------
-- Table: notifications
-- -------------------------------------------------
CREATE TABLE IF NOT EXISTS `notifications` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` INT UNSIGNED NOT NULL,
  `type` VARCHAR(50) NOT NULL,
  `message` VARCHAR(255) NOT NULL,
  `ticket_id` INT UNSIGNED DEFAULT NULL,
  `is_read` TINYINT(1) NOT NULL DEFAULT 0,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_notifications_user_id` (`user_id`),
  KEY `idx_notifications_is_read` (`is_read`),
  CONSTRAINT `fk_notifications_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_notifications_ticket` FOREIGN KEY (`ticket_id`) REFERENCES `tickets` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -------------------------------------------------
-- Update existing products with sample stock/SKU/status
-- -------------------------------------------------
UPDATE `products` SET `stock` = 50, `sku` = 'ELEC-BT-001', `status` = 'active' WHERE `title` = 'Wireless Bluetooth Headphones';
UPDATE `products` SET `stock` = 30, `sku` = 'ELEC-FW-002', `status` = 'active' WHERE `title` = 'Smart Fitness Watch';
UPDATE `products` SET `stock` = 3,  `sku` = 'FASH-DJ-003', `status` = 'active' WHERE `title` = 'Men''s Denim Jacket';
UPDATE `products` SET `stock` = 0,  `sku` = 'FASH-RS-004', `status` = 'active' WHERE `title` = 'Women''s Running Shoes';
UPDATE `products` SET `stock` = 20, `sku` = 'HOME-CS-005', `status` = 'active' WHERE `title` = 'Stainless Steel Cookware Set';
UPDATE `products` SET `stock` = 100,`sku` = 'HOME-CM-006', `status` = 'active' WHERE `title` = 'Ceramic Coffee Mug Set';
UPDATE `products` SET `stock` = 10, `sku` = 'SPRT-YM-007', `status` = 'active' WHERE `title` = 'Yoga Mat with Carry Strap';
UPDATE `products` SET `stock` = 0,  `sku` = 'SPRT-DS-008', `status` = 'hidden' WHERE `title` = 'Adjustable Dumbbell Set';
UPDATE `products` SET `stock` = 5,  `sku` = 'BOOK-AP-009', `status` = 'active' WHERE `title` = 'The Art of Programming';
UPDATE `products` SET `stock` = 15, `sku` = 'BOOK-MC-010', `status` = 'active' WHERE `title` = 'Mystery Novel Collection';
UPDATE `products` SET `stock` = 45, `sku` = 'BEAU-VC-011', `status` = 'active' WHERE `title` = 'Vitamin C Serum';
UPDATE `products` SET `stock` = 0,  `sku` = 'BEAU-ET-012', `status` = 'active' WHERE `title` = 'Electric Toothbrush';

-- -------------------------------------------------
-- Table: cart_activity (log of items added to carts)
-- -------------------------------------------------
CREATE TABLE IF NOT EXISTS `cart_activity` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` INT UNSIGNED NOT NULL,
  `product_id` INT UNSIGNED NOT NULL,
  `quantity` INT NOT NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_cart_activity_user_id` (`user_id`),
  KEY `idx_cart_activity_product_id` (`product_id`),
  KEY `idx_cart_activity_created_at` (`created_at`),
  CONSTRAINT `fk_cart_activity_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_cart_activity_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -------------------------------------------------
-- Table: cart_activity_replies (admin replies to cart activity)
-- -------------------------------------------------
CREATE TABLE IF NOT EXISTS `cart_activity_replies` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `cart_activity_id` INT UNSIGNED NOT NULL,
  `admin_id` INT UNSIGNED NOT NULL,
  `message` TEXT NOT NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_car_cart_activity_id` (`cart_activity_id`),
  KEY `idx_car_admin_id` (`admin_id`),
  CONSTRAINT `fk_car_cart_activity` FOREIGN KEY (`cart_activity_id`) REFERENCES `cart_activity` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_car_admin` FOREIGN KEY (`admin_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -------------------------------------------------
-- Add cart_activity_id to notifications
-- -------------------------------------------------
ALTER TABLE `notifications`
  ADD COLUMN `cart_activity_id` INT UNSIGNED DEFAULT NULL AFTER `ticket_id`,
  ADD KEY `idx_notifications_cart_activity_id` (`cart_activity_id`);

SET FOREIGN_KEY_CHECKS = 1;
