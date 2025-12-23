-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 17 Des 2025 pada 17.53
-- Versi server: 10.4.32-MariaDB
-- Versi PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `marketmm`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `accounts`
--

CREATE TABLE `accounts` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `username` varchar(100) NOT NULL,
  `email` varchar(255) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `role` enum('admin','seller','buyer') NOT NULL DEFAULT 'buyer',
  `password` varchar(255) NOT NULL,
  `city` varchar(100) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `profile_picture` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `otp_code` varchar(6) NOT NULL,
  `otp_expired` datetime NOT NULL,
  `description` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `accounts`
--

INSERT INTO `accounts` (`id`, `name`, `username`, `email`, `phone`, `role`, `password`, `city`, `address`, `profile_picture`, `created_at`, `otp_code`, `otp_expired`, `description`) VALUES
(1, 'Admin System', 'admin', 'admin@marketplace.com', '08123456789', 'admin', '$2y$10$jdu1ykJBq.ZHBu2MR0kMx.XVdFjMJIzblhPJJ1EiF9beNIg502Zb2', NULL, NULL, NULL, '2025-11-21 17:52:22', '', '0000-00-00 00:00:00', NULL),
(2, 'Hamid Argo Bromo', 'bromo', 'hamidbromo@gmail.com', '08123456789', 'buyer', '$2y$10$4VC6deuCYewjsGDu9kxqXe.fvALnPhuWj7lykaI0dvTxdrQOCgSuC', 'Semarang', 'SEMARANGASDASDSAD', '1764572844_6b79a5b98e43.png', '2025-11-21 18:26:40', '', '0000-00-00 00:00:00', 'WOI IRENG'),
(3, 'jawaShop', 'jawaShop', 'naparti@gmail.com', '08123456789', 'seller', '$2y$10$yhXkoRFauEAnc3cWX0rqreLyjArrPhRd5WityGCoZ1pFk36yEARla', 'Semarang', 'Patemon, Gunungpati, Semarang', '1764280779_b580ceb0a54bcf0b68f5919c32da8144~tplv-tiktokx-cropcenter_720_720.jpeg', '2025-11-21 23:25:16', '', '0000-00-00 00:00:00', NULL),
(4, 'azkal', 'azkal123', 'azkal@gmail.com', '08123456789', 'buyer', '$2y$10$s.jdYRfWbGxnCukM64nTm.0e25hFKvGNxHzE86RZ0ahZ8GstgjBbO', '', '', '1765986598_652dd8772273.png', '2025-11-22 18:07:03', '', '0000-00-00 00:00:00', ''),
(5, 'Angga', 'angga123', 'angga@gmail.com', '081234747472', 'buyer', '$2y$10$l1Ab1biHWZ5IPR/QkYlSVuDdlKsqyoBr.N2hgrVom6e6CKOVUVucu', 'Boja', 'Kendal Boja', '1764571037_6b4c6c9bd381.jpg', '2025-12-01 06:35:19', '', '0000-00-00 00:00:00', 'AWOKWAOKWAOK'),
(6, 'SeCloth', 'SeCloth', 'sechloth@gmail.com', '083142651424', 'seller', '$2y$10$Cv8mO3zomJSuRE84LO35LOC5WzfY16MAshoqcNEXYINWuyXgePhp2', 'Semarang', 'Patemon', '1765146430_21f57a0a0d9d.jpg', '2025-12-07 22:10:08', '', '0000-00-00 00:00:00', 'Jual Beli Pakaian');

-- --------------------------------------------------------

--
-- Struktur dari tabel `categories`
--

CREATE TABLE `categories` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `parent_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `categories`
--

INSERT INTO `categories` (`id`, `name`, `slug`, `parent_id`) VALUES
(1, 'Elektronik', 'elektronik', NULL),
(2, 'Fashion Pria', 'fashion-pria', NULL),
(3, 'Fashion Wanita', 'fashion-wanita', NULL),
(4, 'Olahraga', 'olahraga', NULL),
(5, 'Aksesoris', 'aksesoris', NULL),
(6, 'Otomotif', 'otomotif', NULL),
(7, 'Tech', 'tech', NULL);

-- --------------------------------------------------------

--
-- Struktur dari tabel `chat_messages`
--

CREATE TABLE `chat_messages` (
  `id` int(11) NOT NULL,
  `room_id` int(11) NOT NULL,
  `sender_id` int(11) NOT NULL,
  `receiver_id` int(11) NOT NULL,
  `message` text NOT NULL,
  `is_read` tinyint(1) DEFAULT 0,
  `attachment_url` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `chat_rooms`
--

CREATE TABLE `chat_rooms` (
  `id` int(11) NOT NULL,
  `buyer_id` int(11) NOT NULL,
  `seller_id` int(11) NOT NULL,
  `product_id` int(11) DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `chat_rooms`
--

INSERT INTO `chat_rooms` (`id`, `buyer_id`, `seller_id`, `product_id`, `updated_at`, `created_at`) VALUES
(1, 4, 6, NULL, '2025-12-17 16:44:51', '2025-12-17 23:44:51');

-- --------------------------------------------------------

--
-- Struktur dari tabel `cod_transactions`
--

CREATE TABLE `cod_transactions` (
  `id` int(11) NOT NULL,
  `buyer_id` int(11) NOT NULL,
  `seller_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `qty` int(11) NOT NULL DEFAULT 1,
  `price` decimal(15,2) NOT NULL,
  `total` decimal(15,2) NOT NULL,
  `meeting_location` text NOT NULL,
  `meeting_time` datetime NOT NULL,
  `buyer_phone` varchar(20) NOT NULL,
  `seller_phone` varchar(20) NOT NULL,
  `status` enum('pending','approved','completed','cancelled') NOT NULL DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `cod_transactions`
--

INSERT INTO `cod_transactions` (`id`, `buyer_id`, `seller_id`, `product_id`, `qty`, `price`, `total`, `meeting_location`, `meeting_time`, `buyer_phone`, `seller_phone`, `status`, `created_at`, `updated_at`) VALUES
(8, 2, 3, 4, 1, 30000.00, 30000.00, 'Gerbang Utama Kampus', '2025-12-01 14:30:00', '08123456789', '08123456789', 'pending', '2025-12-01 06:37:45', '2025-12-01 06:37:45'),
(9, 2, 3, 7, 2, 15000.00, 30000.00, 'Perpustakaan Teknik', '2025-12-02 10:00:00', '08123456789', '08123456789', 'approved', '2025-12-01 06:37:45', '2025-12-01 06:37:45'),
(10, 4, 3, 1, 1, 20000.00, 20000.00, 'Lapangan', '2025-12-03 09:00:00', '08123456789', '08123456789', 'completed', '2025-12-01 06:37:45', '2025-12-09 14:30:31'),
(11, 4, 3, 2, 3, 10000.00, 30000.00, 'Masjid Kampus', '2025-12-04 15:00:00', '08123456789', '08123456789', 'completed', '2025-12-01 06:37:45', '2025-12-01 06:37:45'),
(12, 2, 3, 3, 1, 25000.00, 25000.00, 'Kantin Fakultas Teknik', '2025-12-05 16:30:00', '08123456789', '08123456789', 'pending', '2025-12-01 06:37:45', '2025-12-01 06:37:45'),
(13, 4, 3, 5, 2, 18000.00, 36000.00, 'Parkiran FEB', '2025-12-06 11:00:00', '08123456789', '08123456789', 'cancelled', '2025-12-01 06:37:45', '2025-12-01 06:37:45'),
(14, 2, 3, 6, 1, 40000.00, 40000.00, 'Toilet L1 Teknik', '2025-12-07 13:00:00', '08123456789', '08123456789', 'pending', '2025-12-01 06:37:45', '2025-12-01 06:37:45');

-- --------------------------------------------------------

--
-- Struktur dari tabel `notifications`
--

CREATE TABLE `notifications` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `link` varchar(255) DEFAULT NULL,
  `is_read` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `seller_id` int(11) NOT NULL,
  `category_id` int(11) DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `price` decimal(15,2) NOT NULL,
  `stock` int(11) NOT NULL DEFAULT 0,
  `description` text DEFAULT NULL,
  `weight` int(11) DEFAULT NULL,
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `products`
--

INSERT INTO `products` (`id`, `seller_id`, `category_id`, `name`, `slug`, `price`, `stock`, `description`, `weight`, `status`, `created_at`, `updated_at`) VALUES
(1, 3, 1, 'Laptop ASUS', 'laptop-asus-1764107044', 10000000.00, 3, 'LAPTOP ASUS BARU MASIH MULUS ', NULL, 'active', '2025-11-25 21:44:04', '2025-12-10 09:59:27'),
(2, 3, 5, 'Kacamata Anti Radiasi', 'kacamata-anti-radiasi-1764569790', 50000.00, 10, 'Kenakan kacamata anti radiasi blue light Korean fashion yang stylish ini untuk memberikan perlindungan pada mata Anda dari sinar biru yang berbahaya. Dengan bentuk bulat yang cocok untuk pria dan wanita, frame transparan, dan lensa anti-UV, kacamata ini dapat digunakan oleh siapa saja.\r\n', NULL, 'active', '2025-12-01 06:16:30', '2025-12-01 06:16:39'),
(3, 3, 5, '6 Pcs Cincin Unik Unisex', '6-pcs-cincin-unik-unisex-1764569834', 39899.00, 40, '- ini perhiasan imitasi ya tapi walaupun imitasi akan awet jika pemakaian dan perawatan benar ,\r\n- Saat mandi dilepas aja terlebih dahulu\r\n- Hindari terkena body lotion, parfume dan keringat berlebih\r\n- Hindari pakai terlalu lama, Tidur jangan lupa dilepas\r\n- setelah pemakaian bisa di lap kembali dengan tissue kering dan simpan di tempat aksesoris dalam kondisi bersih dan kering ya', NULL, 'active', '2025-12-01 06:17:14', '2025-12-01 06:17:14'),
(4, 3, 5, 'Coppia Bracelet', 'coppia-bracelet-1764569870', 189999.00, 20, 'Bahan Gelang: Stainless Steel + Parachute Cord\r\nPanjang gelang: 17cm - 28cm (BISA DIBESAR KECILKAN SENDIRI)\r\nUkuran bar: 0,5 & 0,7 cm x 4,7cm\r\n\r\nFREE UKIR 2 SISI ( PADA BAGIAN DEPAN ATAU HANYA BELAKANG SAJA)\r\nTAMABHAN UKIR 2 SISI ( UKIR PADA BAGIAN DEPAN DAN BELAKANG JUGA)\r\n\r\nHARGA SUDAH SEPASANG *PERHATIAN*', NULL, 'active', '2025-12-01 06:17:50', '2025-12-01 06:17:50'),
(5, 3, 1, 'ACER NITRO V ', 'acer-nitro-v--1764569902', 12748000.00, 5, 'ACER NITRO V 15 I5 13420H RTX2050 8GB 512GB 15.6FHD 144HZ IPS,lagi butuh duit,nego tipis', NULL, 'active', '2025-12-01 06:18:22', '2025-12-01 06:18:22'),
(6, 3, 1, 'APPLE iPhone 13 ', 'apple-iphone-13--1764569996', 8399000.00, 1, '128GB 256GB 512GB A15 Bionic CPU 6C / GPU 4C Resmi IBOX / GDN / DIGIMAP - PROMO-BLACK, 128 GB', NULL, 'active', '2025-12-01 06:19:56', '2025-12-01 06:19:56'),
(7, 3, 1, 'Logitech B175 Mouse Wireless untuk Windows, Mac, Linux dan ChromeOS', 'logitech-b175-mouse-wireless-untuk-windows,-mac,-linux-dan-chromeos-1764570044', 199000.00, 5, 'Logitech B175 Anda bisa merasakan keunggulan dan kenyamanan dari sistem nirkabel pada mouse ini, dengan transmisi data yang sangat cepat membuat Anda tidak perlu khawatir akan terjadi delay atau sinyal terputus.\r\nPelacakan Optikal\r\nJelajahi web seperti navigasi pada Facebook dengan presisi, tunjuk dan drag, segalanya bisa dilakukan pada laptop dengan mudah berkat teknologi pelacakan optik melalui teknologi laser, yang membuat Anda tidak membutuhkan mouse pad.\r\n', NULL, 'active', '2025-12-01 06:20:44', '2025-12-01 06:20:44'),
(8, 6, 2, 'The North Face 700 Nuptse 1996', 'the-north-face-700-nuptse-1996-1765176766', 1995000.00, 2, 'TNF The North Face 700 Nuptse 1996 Mountain Jacket Gunung Bulang - 18 Warna (Size XS-XXL) - Black, XS ,nasih mulus dan seperti baru no nego yang serius serius aja', NULL, 'active', '2025-12-08 06:52:46', '2025-12-08 06:52:46'),
(9, 6, 2, 'FRED PERRY', 'fred-perry-1765176804', 300000.00, 1, 'FRED PERRY Jacquard Printed Tennis Blue Polo Shirt Authentic / Kaos Polo FP Original - S ,minus ada noda dikit dan bolong boleh nego', NULL, 'active', '2025-12-08 06:53:24', '2025-12-08 06:53:24'),
(10, 6, 2, 'Celana Jeans Pria', 'celana-jeans-pria-1765176830', 520000.00, 1, 'Celana Jeans Pria 505 Original Reguler Fit Stonewash 505-4891 IDN - 28 ini bekas kak boleh di nego', NULL, 'active', '2025-12-08 06:53:50', '2025-12-08 06:53:50');

-- --------------------------------------------------------

--
-- Struktur dari tabel `product_images`
--

CREATE TABLE `product_images` (
  `id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `url` varchar(255) NOT NULL,
  `is_primary` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `product_images`
--

INSERT INTO `product_images` (`id`, `product_id`, `url`, `is_primary`) VALUES
(1, 1, 'product_1_69262324bd954_0.jpeg', 1),
(3, 2, 'product_2_692d32be336bb_0.jpeg', 1),
(4, 3, 'product_3_692d32eac6a8a_0.webp', 1),
(5, 4, 'product_4_692d330ef02b9_0.jpeg', 1),
(6, 5, 'product_5_692d332f0050f_0.jpeg', 1),
(7, 6, 'product_6_692d338c6de99_0.jpeg', 1),
(8, 7, 'product_7_692d33bc79714_0.jpeg', 1),
(9, 8, 'product_8_693675bee3d7d_0.jpeg', 1),
(10, 9, 'product_9_693675e49920d_0.jpeg', 1),
(11, 10, 'product_10_693675fe5129b_0.jpeg', 1);

-- --------------------------------------------------------

--
-- Struktur dari tabel `product_reviews`
--

CREATE TABLE `product_reviews` (
  `id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `rating` int(11) NOT NULL CHECK (`rating` between 1 and 5),
  `review` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `product_reviews`
--

INSERT INTO `product_reviews` (`id`, `product_id`, `user_id`, `rating`, `review`, `created_at`) VALUES
(1, 1, 2, 5, 'Produk bagus dan penjual ramah!', '2025-11-30 20:56:40');

-- --------------------------------------------------------

--
-- Struktur dari tabel `wishes`
--

CREATE TABLE `wishes` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `wishes`
--

INSERT INTO `wishes` (`id`, `user_id`, `product_id`, `quantity`, `created_at`) VALUES
(1, 1, 3, 1, '2025-12-12 12:25:49'),
(2, 1, 5, 1, '2025-12-12 12:25:49'),
(3, 2, 1, 1, '2025-12-12 12:25:49'),
(6, 4, 6, 1, '2025-12-17 06:06:12'),
(7, 3, 10, 1, '2025-12-17 13:07:50'),
(9, 4, 5, 1, '2025-12-17 15:35:07'),
(10, 4, 4, 1, '2025-12-17 15:50:35'),
(11, 4, 10, 1, '2025-12-17 16:44:50');

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `accounts`
--
ALTER TABLE `accounts`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `idx_role` (`role`);

--
-- Indeks untuk tabel `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`),
  ADD KEY `parent_id` (`parent_id`);

--
-- Indeks untuk tabel `chat_messages`
--
ALTER TABLE `chat_messages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sender_id` (`sender_id`),
  ADD KEY `receiver_id` (`receiver_id`),
  ADD KEY `idx_room` (`room_id`);

--
-- Indeks untuk tabel `chat_rooms`
--
ALTER TABLE `chat_rooms`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_room` (`buyer_id`,`seller_id`,`product_id`),
  ADD KEY `seller_id` (`seller_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indeks untuk tabel `cod_transactions`
--
ALTER TABLE `cod_transactions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `buyer_id` (`buyer_id`),
  ADD KEY `seller_id` (`seller_id`),
  ADD KEY `product_id` (`product_id`),
  ADD KEY `idx_status` (`status`);

--
-- Indeks untuk tabel `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indeks untuk tabel `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`),
  ADD KEY `idx_seller` (`seller_id`),
  ADD KEY `idx_category` (`category_id`);

--
-- Indeks untuk tabel `product_images`
--
ALTER TABLE `product_images`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_product` (`product_id`);

--
-- Indeks untuk tabel `product_reviews`
--
ALTER TABLE `product_reviews`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_review` (`product_id`,`user_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indeks untuk tabel `wishes`
--
ALTER TABLE `wishes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_wish` (`user_id`,`product_id`),
  ADD KEY `product_id` (`product_id`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `accounts`
--
ALTER TABLE `accounts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT untuk tabel `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT untuk tabel `chat_messages`
--
ALTER TABLE `chat_messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `chat_rooms`
--
ALTER TABLE `chat_rooms`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT untuk tabel `cod_transactions`
--
ALTER TABLE `cod_transactions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT untuk tabel `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT untuk tabel `product_images`
--
ALTER TABLE `product_images`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT untuk tabel `product_reviews`
--
ALTER TABLE `product_reviews`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT untuk tabel `wishes`
--
ALTER TABLE `wishes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `categories`
--
ALTER TABLE `categories`
  ADD CONSTRAINT `categories_ibfk_1` FOREIGN KEY (`parent_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL;

--
-- Ketidakleluasaan untuk tabel `chat_messages`
--
ALTER TABLE `chat_messages`
  ADD CONSTRAINT `chat_messages_ibfk_1` FOREIGN KEY (`room_id`) REFERENCES `chat_rooms` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `chat_messages_ibfk_2` FOREIGN KEY (`sender_id`) REFERENCES `accounts` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `chat_messages_ibfk_3` FOREIGN KEY (`receiver_id`) REFERENCES `accounts` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `chat_rooms`
--
ALTER TABLE `chat_rooms`
  ADD CONSTRAINT `chat_rooms_ibfk_1` FOREIGN KEY (`buyer_id`) REFERENCES `accounts` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `chat_rooms_ibfk_2` FOREIGN KEY (`seller_id`) REFERENCES `accounts` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `chat_rooms_ibfk_3` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE SET NULL;

--
-- Ketidakleluasaan untuk tabel `cod_transactions`
--
ALTER TABLE `cod_transactions`
  ADD CONSTRAINT `cod_transactions_ibfk_1` FOREIGN KEY (`buyer_id`) REFERENCES `accounts` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `cod_transactions_ibfk_2` FOREIGN KEY (`seller_id`) REFERENCES `accounts` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `cod_transactions_ibfk_3` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `notifications`
--
ALTER TABLE `notifications`
  ADD CONSTRAINT `notifications_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `accounts` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `products_ibfk_1` FOREIGN KEY (`seller_id`) REFERENCES `accounts` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `products_ibfk_2` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL;

--
-- Ketidakleluasaan untuk tabel `product_images`
--
ALTER TABLE `product_images`
  ADD CONSTRAINT `product_images_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `product_reviews`
--
ALTER TABLE `product_reviews`
  ADD CONSTRAINT `product_reviews_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `product_reviews_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `accounts` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `wishes`
--
ALTER TABLE `wishes`
  ADD CONSTRAINT `wishes_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `accounts` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `wishes_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
