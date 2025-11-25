SET
  SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";

START TRANSACTION;

SET
  time_zone = "+00:00";

;
;
;
;

CREATE TABLE
  `accounts` (
    `id` int (11) NOT NULL,
    `name` varchar(100) NOT NULL,
    `username` varchar(100) NOT NULL,
    `email` varchar(255) NOT NULL,
    `phone` varchar(20) NOT NULL,
    `role` enum ('admin', 'seller', 'buyer') NOT NULL DEFAULT 'buyer',
    `password` varchar(255) NOT NULL,
    `city` varchar(100) DEFAULT NULL,
    `address` text DEFAULT NULL,
    `profile_picture` varchar(255) DEFAULT NULL,
    `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
    `otp_code` varchar(6) NOT NULL,
    `otp_expired` datetime NOT NULL
  ) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci;

INSERT INTO
  `accounts` (
    `id`,
    `name`,
    `username`,
    `email`,
    `phone`,
    `role`,
    `password`,
    `city`,
    `address`,
    `profile_picture`,
    `created_at`,
    `otp_code`,
    `otp_expired`
  )
VALUES
  (
    1,
    'Admin System',
    'admin',
    'admin@marketplace.com',
    '08123456789',
    'admin',
    '$2y$10$jdu1ykJBq.ZHBu2MR0kMx.XVdFjMJIzblhPJJ1EiF9beNIg502Zb2',
    NULL,
    NULL,
    NULL,
    '2025-11-21 17:52:22',
    '',
    '0000-00-00 00:00:00'
  ),
  (
    2,
    'Hamid Argo Bromo',
    'bromo',
    'hamidbromo@gmail.com',
    '08123456789',
    'buyer',
    '$2y$10$tDmNiplrgSQJc9w3jVJxGeapEOD.FF1RVXfIj88U2M2AJVXixcDjG',
    NULL,
    NULL,
    NULL,
    '2025-11-21 18:26:40',
    '',
    '0000-00-00 00:00:00'
  );

CREATE TABLE
  `categories` (
    `id` int (11) NOT NULL,
    `name` varchar(255) NOT NULL,
    `slug` varchar(255) NOT NULL,
    `parent_id` int (11) DEFAULT NULL
  ) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci;

CREATE TABLE
  `chat_messages` (
    `id` int (11) NOT NULL,
    `room_id` int (11) NOT NULL,
    `sender_id` int (11) NOT NULL,
    `message` text DEFAULT NULL,
    `attachment_url` varchar(255) DEFAULT NULL,
    `created_at` timestamp NOT NULL DEFAULT current_timestamp()
  ) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci;

CREATE TABLE
  `chat_rooms` (
    `id` int (11) NOT NULL,
    `buyer_id` int (11) NOT NULL,
    `seller_id` int (11) NOT NULL,
    `product_id` int (11) DEFAULT NULL,
    `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
  ) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci;

CREATE TABLE
  `cod_transactions` (
    `id` int (11) NOT NULL,
    `buyer_id` int (11) NOT NULL,
    `seller_id` int (11) NOT NULL,
    `product_id` int (11) NOT NULL,
    `qty` int (11) NOT NULL DEFAULT 1,
    `price` decimal(15, 2) NOT NULL,
    `total` decimal(15, 2) NOT NULL,
    `meeting_location` text NOT NULL,
    `meeting_time` datetime NOT NULL,
    `buyer_phone` varchar(20) NOT NULL,
    `seller_phone` varchar(20) NOT NULL,
    `status` enum ('pending', 'approved', 'completed', 'cancelled') NOT NULL DEFAULT 'pending',
    `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
    `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
  ) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci;

CREATE TABLE
  `notifications` (
    `id` int (11) NOT NULL,
    `user_id` int (11) NOT NULL,
    `title` varchar(255) NOT NULL,
    `message` text NOT NULL,
    `link` varchar(255) DEFAULT NULL,
    `is_read` tinyint (1) DEFAULT 0,
    `created_at` timestamp NOT NULL DEFAULT current_timestamp()
  ) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci;

CREATE TABLE
  `products` (
    `id` int (11) NOT NULL,
    `seller_id` int (11) NOT NULL,
    `category_id` int (11) DEFAULT NULL,
    `name` varchar(255) NOT NULL,
    `slug` varchar(255) NOT NULL,
    `price` decimal(15, 2) NOT NULL,
    `stock` int (11) NOT NULL DEFAULT 0,
    `description` text DEFAULT NULL,
    `weight` int (11) DEFAULT NULL,
    `status` enum ('active', 'inactive') NOT NULL DEFAULT 'active',
    `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
    `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
  ) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci;

CREATE TABLE
  `product_images` (
    `id` int (11) NOT NULL,
    `product_id` int (11) NOT NULL,
    `url` varchar(255) NOT NULL,
    `is_primary` tinyint (1) DEFAULT 0
  ) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci;

CREATE TABLE
  `product_reviews` (
    `id` int (11) NOT NULL,
    `product_id` int (11) NOT NULL,
    `user_id` int (11) NOT NULL,
    `rating` int (11) NOT NULL CHECK (`rating` between 1 and 5),
    `review` text DEFAULT NULL,
    `created_at` timestamp NOT NULL DEFAULT current_timestamp()
  ) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci;

CREATE TABLE
  `wishes` (
    `id` int (11) NOT NULL,
    `user_id` int (11) NOT NULL,
    `product_id` int (11) NOT NULL,
    `created_at` timestamp NOT NULL DEFAULT current_timestamp()
  ) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci;

ALTER TABLE `accounts` ADD PRIMARY KEY (`id`),
ADD UNIQUE KEY `username` (`username`),
ADD UNIQUE KEY `email` (`email`),
ADD KEY `idx_role` (`role`);

ALTER TABLE `categories` ADD PRIMARY KEY (`id`),
ADD UNIQUE KEY `slug` (`slug`),
ADD KEY `parent_id` (`parent_id`);

ALTER TABLE `chat_messages` ADD PRIMARY KEY (`id`),
ADD KEY `sender_id` (`sender_id`),
ADD KEY `idx_room` (`room_id`);

ALTER TABLE `chat_rooms` ADD PRIMARY KEY (`id`),
ADD UNIQUE KEY `uq_room` (`buyer_id`, `seller_id`, `product_id`),
ADD KEY `seller_id` (`seller_id`),
ADD KEY `product_id` (`product_id`);

ALTER TABLE `cod_transactions` ADD PRIMARY KEY (`id`),
ADD KEY `buyer_id` (`buyer_id`),
ADD KEY `seller_id` (`seller_id`),
ADD KEY `product_id` (`product_id`),
ADD KEY `idx_status` (`status`);

ALTER TABLE `notifications` ADD PRIMARY KEY (`id`),
ADD KEY `user_id` (`user_id`);

ALTER TABLE `products` ADD PRIMARY KEY (`id`),
ADD UNIQUE KEY `slug` (`slug`),
ADD KEY `idx_seller` (`seller_id`),
ADD KEY `idx_category` (`category_id`);

ALTER TABLE `product_images` ADD PRIMARY KEY (`id`),
ADD KEY `idx_product` (`product_id`);

ALTER TABLE `product_reviews` ADD PRIMARY KEY (`id`),
ADD UNIQUE KEY `uq_review` (`product_id`, `user_id`),
ADD KEY `user_id` (`user_id`);

ALTER TABLE `wishes` ADD PRIMARY KEY (`id`),
ADD UNIQUE KEY `uq_wish` (`user_id`, `product_id`),
ADD KEY `product_id` (`product_id`);

ALTER TABLE `accounts` MODIFY `id` int (11) NOT NULL AUTO_INCREMENT,
AUTO_INCREMENT = 3;

ALTER TABLE `categories` MODIFY `id` int (11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `chat_messages` MODIFY `id` int (11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `chat_rooms` MODIFY `id` int (11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `cod_transactions` MODIFY `id` int (11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `notifications` MODIFY `id` int (11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `products` MODIFY `id` int (11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `product_images` MODIFY `id` int (11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `product_reviews` MODIFY `id` int (11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `wishes` MODIFY `id` int (11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `categories` ADD CONSTRAINT `categories_ibfk_1` FOREIGN KEY (`parent_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL;

ALTER TABLE `chat_messages` ADD CONSTRAINT `chat_messages_ibfk_1` FOREIGN KEY (`room_id`) REFERENCES `chat_rooms` (`id`) ON DELETE CASCADE,
ADD CONSTRAINT `chat_messages_ibfk_2` FOREIGN KEY (`sender_id`) REFERENCES `accounts` (`id`) ON DELETE CASCADE;

ALTER TABLE `chat_rooms` ADD CONSTRAINT `chat_rooms_ibfk_1` FOREIGN KEY (`buyer_id`) REFERENCES `accounts` (`id`) ON DELETE CASCADE,
ADD CONSTRAINT `chat_rooms_ibfk_2` FOREIGN KEY (`seller_id`) REFERENCES `accounts` (`id`) ON DELETE CASCADE,
ADD CONSTRAINT `chat_rooms_ibfk_3` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE SET NULL;

ALTER TABLE `cod_transactions` ADD CONSTRAINT `cod_transactions_ibfk_1` FOREIGN KEY (`buyer_id`) REFERENCES `accounts` (`id`) ON DELETE CASCADE,
ADD CONSTRAINT `cod_transactions_ibfk_2` FOREIGN KEY (`seller_id`) REFERENCES `accounts` (`id`) ON DELETE CASCADE,
ADD CONSTRAINT `cod_transactions_ibfk_3` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;

ALTER TABLE `notifications` ADD CONSTRAINT `notifications_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `accounts` (`id`) ON DELETE CASCADE;

ALTER TABLE `products` ADD CONSTRAINT `products_ibfk_1` FOREIGN KEY (`seller_id`) REFERENCES `accounts` (`id`) ON DELETE CASCADE,
ADD CONSTRAINT `products_ibfk_2` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL;

ALTER TABLE `product_images` ADD CONSTRAINT `product_images_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;

ALTER TABLE `product_reviews` ADD CONSTRAINT `product_reviews_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
ADD CONSTRAINT `product_reviews_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `accounts` (`id`) ON DELETE CASCADE;

ALTER TABLE `wishes` ADD CONSTRAINT `wishes_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `accounts` (`id`) ON DELETE CASCADE,
ADD CONSTRAINT `wishes_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;

COMMIT;
;
;
;