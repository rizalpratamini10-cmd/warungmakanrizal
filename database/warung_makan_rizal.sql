-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 08 Jun 2026 pada 01.24
-- Versi server: 10.4.32-MariaDB
-- Versi PHP: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `warung_makan_rizal`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `cart`
--

CREATE TABLE `cart` (
  `id` int(11) NOT NULL,
  `customer_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `categories`
--

CREATE TABLE `categories` (
  `id` int(11) NOT NULL,
  `nama_kategori` varchar(50) NOT NULL,
  `icon` varchar(50) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `categories`
--

INSERT INTO `categories` (`id`, `nama_kategori`, `icon`, `created_at`) VALUES
(1, 'Makanan', 'fa-utensils', '2026-06-07 21:14:43'),
(2, 'Minuman', 'fa-mug-hot', '2026-06-07 21:14:43'),
(3, 'Snack', 'fa-cookie-bite', '2026-06-07 21:14:43'),
(4, 'Paket Hemat', 'fa-gift', '2026-06-07 21:14:43');

-- --------------------------------------------------------

--
-- Struktur dari tabel `customer_addresses`
--

CREATE TABLE `customer_addresses` (
  `id` int(11) NOT NULL,
  `customer_id` int(11) NOT NULL,
  `label` varchar(50) NOT NULL,
  `alamat` text NOT NULL,
  `is_primary` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `gallery`
--

CREATE TABLE `gallery` (
  `id` int(11) NOT NULL,
  `judul` varchar(100) NOT NULL,
  `gambar` varchar(255) NOT NULL,
  `deskripsi` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `kurir_earnings`
--

CREATE TABLE `kurir_earnings` (
  `id` int(11) NOT NULL,
  `kurir_id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `jarak_km` decimal(10,2) NOT NULL,
  `upah` decimal(12,0) NOT NULL,
  `status` enum('pending','paid') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `kurir_locations`
--

CREATE TABLE `kurir_locations` (
  `id` int(11) NOT NULL,
  `kurir_id` int(11) NOT NULL,
  `latitude` decimal(10,8) DEFAULT NULL,
  `longitude` decimal(11,8) DEFAULT NULL,
  `last_update` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `is_online` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `kurir_tarif`
--

CREATE TABLE `kurir_tarif` (
  `id` int(11) NOT NULL,
  `jarak_min_km` decimal(10,2) NOT NULL,
  `jarak_max_km` decimal(10,2) NOT NULL,
  `tarif` decimal(12,0) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `kurir_tarif`
--

INSERT INTO `kurir_tarif` (`id`, `jarak_min_km`, `jarak_max_km`, `tarif`, `created_at`) VALUES
(1, 0.00, 1.00, 5000, '2026-06-07 21:14:43'),
(2, 1.00, 2.00, 8000, '2026-06-07 21:14:43'),
(3, 2.00, 3.00, 10000, '2026-06-07 21:14:43'),
(4, 3.00, 4.00, 12000, '2026-06-07 21:14:43'),
(5, 4.00, 5.00, 15000, '2026-06-07 21:14:43'),
(6, 5.00, 7.00, 20000, '2026-06-07 21:14:43'),
(7, 7.00, 10.00, 25000, '2026-06-07 21:14:43'),
(8, 10.00, 999.00, 5000, '2026-06-07 21:14:43'),
(9, 0.00, 1.00, 5000, '2026-06-07 23:23:41'),
(10, 1.00, 2.00, 7000, '2026-06-07 23:23:41'),
(11, 2.00, 3.00, 10000, '2026-06-07 23:23:41'),
(12, 3.00, 4.00, 12000, '2026-06-07 23:23:41'),
(13, 4.00, 5.00, 15000, '2026-06-07 23:23:41'),
(14, 5.00, 7.00, 20000, '2026-06-07 23:23:41'),
(15, 7.00, 10.00, 25000, '2026-06-07 23:23:41'),
(16, 10.00, 999.00, 5000, '2026-06-07 23:23:41'),
(17, 0.00, 1.00, 5000, '2026-06-07 23:24:00'),
(18, 1.00, 2.00, 7000, '2026-06-07 23:24:00'),
(19, 2.00, 3.00, 10000, '2026-06-07 23:24:00'),
(20, 3.00, 4.00, 12000, '2026-06-07 23:24:00'),
(21, 4.00, 5.00, 15000, '2026-06-07 23:24:00'),
(22, 5.00, 7.00, 20000, '2026-06-07 23:24:00'),
(23, 7.00, 10.00, 25000, '2026-06-07 23:24:00'),
(24, 10.00, 999.00, 5000, '2026-06-07 23:24:00');

-- --------------------------------------------------------

--
-- Struktur dari tabel `kurir_withdrawals`
--

CREATE TABLE `kurir_withdrawals` (
  `id` int(11) NOT NULL,
  `kurir_id` int(11) NOT NULL,
  `jumlah` decimal(12,0) NOT NULL,
  `bank_name` varchar(50) DEFAULT NULL,
  `account_number` varchar(50) DEFAULT NULL,
  `account_name` varchar(100) DEFAULT NULL,
  `status` enum('pending','processed','completed','rejected') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `processed_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `notifications`
--

CREATE TABLE `notifications` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `title` varchar(100) NOT NULL,
  `message` text NOT NULL,
  `type` enum('order','payment','delivery','system') DEFAULT 'system',
  `is_read` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `order_number` varchar(20) NOT NULL,
  `customer_id` int(11) NOT NULL,
  `kurir_id` int(11) DEFAULT NULL,
  `total_harga` decimal(12,0) NOT NULL,
  `biaya_pengiriman` decimal(12,0) DEFAULT 0,
  `status` enum('pending','diproses','dikirim','selesai','dibatalkan') DEFAULT 'pending',
  `payment_method` enum('cash','transfer','qris') DEFAULT 'cash',
  `payment_status` enum('belum_bayar','sudah_bayar') DEFAULT 'belum_bayar',
  `catatan` text DEFAULT NULL,
  `alamat_pengiriman` text NOT NULL,
  `latitude` decimal(10,8) DEFAULT NULL,
  `longitude` decimal(11,8) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `order_details`
--

CREATE TABLE `order_details` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `harga_per_item` decimal(12,0) NOT NULL,
  `subtotal` decimal(12,0) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `nama_produk` varchar(100) NOT NULL,
  `kategori_id` int(11) DEFAULT NULL,
  `deskripsi` text DEFAULT NULL,
  `harga` decimal(12,0) NOT NULL,
  `stok` int(11) DEFAULT 0,
  `gambar` varchar(255) DEFAULT NULL,
  `is_available` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `products`
--

INSERT INTO `products` (`id`, `nama_produk`, `kategori_id`, `deskripsi`, `harga`, `stok`, `gambar`, `is_available`, `created_at`, `updated_at`) VALUES
(1, 'Ayam Goreng Kremes', 1, 'Ayam goreng dengan kremesan renyah dan sambal terasi', 15000, 50, NULL, 1, '2026-06-07 21:14:43', '2026-06-07 21:14:43'),
(2, 'Nasi Goreng Kampung', 1, 'Nasi goreng dengan bumbu rempah tradisional', 13000, 40, NULL, 1, '2026-06-07 21:14:43', '2026-06-07 21:14:43'),
(3, 'Ikan Bakar Rica', 1, 'Ikan kakap bakar dengan bumbu rica kemangi', 18000, 30, NULL, 1, '2026-06-07 21:14:43', '2026-06-07 21:14:43'),
(4, 'Soto Ayam', 1, 'Soto ayam bening dengan pelengkap telur dan perkedel', 12000, 35, NULL, 1, '2026-06-07 21:14:43', '2026-06-07 21:14:43'),
(5, 'Es Teh Manis', 2, 'Teh manis segar dengan es batu', 4000, 100, NULL, 1, '2026-06-07 21:14:43', '2026-06-07 21:14:43'),
(6, 'Es Jeruk', 2, 'Jeruk peras segar dengan es batu', 6000, 100, NULL, 1, '2026-06-07 21:14:43', '2026-06-07 21:14:43'),
(7, 'Es Kelapa Muda', 2, 'Kelapa muda segar dengan sirup cocopandan', 8000, 50, NULL, 1, '2026-06-07 21:14:43', '2026-06-07 21:14:43'),
(8, 'Tempe Mendoan', 3, 'Tempe tepung krispi dengan cabe rawit', 5000, 60, NULL, 1, '2026-06-07 21:14:43', '2026-06-07 21:14:43'),
(9, 'Tahu Isi', 3, 'Tahu goreng isi sayur dan mie', 5000, 60, NULL, 1, '2026-06-07 21:14:43', '2026-06-07 21:14:43'),
(10, 'Pisang Goreng', 3, 'Pisang kepok goreng dengan topping keju', 7000, 40, NULL, 1, '2026-06-07 21:14:43', '2026-06-07 21:14:43'),
(11, 'Paket Nasi + Ayam + Es Teh', 4, 'Paket hemat nasi, ayam goreng, dan es teh', 20000, 99, NULL, 1, '2026-06-07 21:14:43', '2026-06-07 21:14:43'),
(12, 'Paket Nasi + Ikan + Es Jeruk', 4, 'Paket hemat nasi, ikan bakar, dan es jeruk', 25000, 99, NULL, 1, '2026-06-07 21:14:43', '2026-06-07 21:14:43');

-- --------------------------------------------------------

--
-- Struktur dari tabel `settings`
--

CREATE TABLE `settings` (
  `id` int(11) NOT NULL,
  `setting_key` varchar(50) NOT NULL,
  `setting_value` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `settings`
--

INSERT INTO `settings` (`id`, `setting_key`, `setting_value`, `created_at`, `updated_at`) VALUES
(1, 'nama_toko', 'Warung Makan Rizal', '2026-06-07 21:14:43', '2026-06-07 21:14:43'),
(2, 'alamat_toko', 'Samping Kantor Camat Lubuk Baja, Kota Batam, Kepulauan Riau', '2026-06-07 21:14:43', '2026-06-07 21:14:43'),
(3, 'no_telp', '+62 812 3456 7890', '2026-06-07 21:14:43', '2026-06-07 21:14:43'),
(4, 'email_toko', 'info@warungrizal.com', '2026-06-07 21:14:43', '2026-06-07 21:14:43'),
(5, 'instagram', '@warungrizal', '2026-06-07 21:14:43', '2026-06-07 21:14:43'),
(6, 'wa_number', '6281234567890', '2026-06-07 21:14:43', '2026-06-07 21:14:43');

-- --------------------------------------------------------

--
-- Struktur dari tabel `testimonials`
--

CREATE TABLE `testimonials` (
  `id` int(11) NOT NULL,
  `customer_id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `rating` int(11) DEFAULT NULL CHECK (`rating` >= 1 and `rating` <= 5),
  `komentar` text DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `nama_lengkap` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `no_hp` varchar(15) NOT NULL,
  `alamat` text DEFAULT NULL,
  `role` enum('admin','customer','kurir') DEFAULT 'customer',
  `foto` varchar(255) DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `nama_lengkap`, `email`, `no_hp`, `alamat`, `role`, `foto`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Administrator', 'admin@warungrizal.com', '081234567890', 'Batam, Kepulauan Riau', 'admin', NULL, 1, '2026-06-07 21:14:43', '2026-06-07 21:14:43'),
(2, 'kurir1', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Budi Kurir', 'kurir@warungrizal.com', '081234567891', 'Batam Center, Batam', 'kurir', NULL, 1, '2026-06-07 21:14:43', '2026-06-07 21:14:43'),
(3, 'kurir2', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Agus Kurir', 'agus@warungrizal.com', '081234567892', 'Nagoya, Batam', 'kurir', NULL, 1, '2026-06-07 21:14:43', '2026-06-07 21:14:43'),
(4, 'rizalpratamini10@gmail.com', '$2y$10$mj7lHzhvGQ.US9T3hU6Q1OQVUxIaNHn9uceXDjz2pYdsaTYM7VwVK', 'Rizal', 'rizalpratamini10@gmail.com', '085669049294', 'Perumahan happy garden blok c no 33 a', 'customer', NULL, 1, '2026-06-07 21:33:35', '2026-06-07 21:33:35');

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `cart`
--
ALTER TABLE `cart`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_cart` (`customer_id`,`product_id`),
  ADD KEY `product_id` (`product_id`),
  ADD KEY `idx_customer` (`customer_id`);

--
-- Indeks untuk tabel `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_nama` (`nama_kategori`);

--
-- Indeks untuk tabel `customer_addresses`
--
ALTER TABLE `customer_addresses`
  ADD PRIMARY KEY (`id`),
  ADD KEY `customer_id` (`customer_id`);

--
-- Indeks untuk tabel `gallery`
--
ALTER TABLE `gallery`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `kurir_earnings`
--
ALTER TABLE `kurir_earnings`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `idx_kurir` (`kurir_id`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_created` (`created_at`);

--
-- Indeks untuk tabel `kurir_locations`
--
ALTER TABLE `kurir_locations`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_kurir` (`kurir_id`),
  ADD KEY `idx_online` (`is_online`),
  ADD KEY `idx_last_update` (`last_update`);

--
-- Indeks untuk tabel `kurir_tarif`
--
ALTER TABLE `kurir_tarif`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_jarak` (`jarak_min_km`,`jarak_max_km`);

--
-- Indeks untuk tabel `kurir_withdrawals`
--
ALTER TABLE `kurir_withdrawals`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_kurir` (`kurir_id`),
  ADD KEY `idx_status` (`status`);

--
-- Indeks untuk tabel `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_user` (`user_id`),
  ADD KEY `idx_unread` (`user_id`,`is_read`),
  ADD KEY `idx_created` (`created_at`);

--
-- Indeks untuk tabel `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `order_number` (`order_number`),
  ADD KEY `idx_customer` (`customer_id`),
  ADD KEY `idx_kurir` (`kurir_id`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_created` (`created_at`),
  ADD KEY `idx_order_number` (`order_number`);

--
-- Indeks untuk tabel `order_details`
--
ALTER TABLE `order_details`
  ADD PRIMARY KEY (`id`),
  ADD KEY `product_id` (`product_id`),
  ADD KEY `idx_order` (`order_id`);

--
-- Indeks untuk tabel `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_kategori` (`kategori_id`),
  ADD KEY `idx_harga` (`harga`),
  ADD KEY `idx_is_available` (`is_available`);
ALTER TABLE `products` ADD FULLTEXT KEY `idx_search` (`nama_produk`,`deskripsi`);

--
-- Indeks untuk tabel `settings`
--
ALTER TABLE `settings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `setting_key` (`setting_key`);

--
-- Indeks untuk tabel `testimonials`
--
ALTER TABLE `testimonials`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `idx_customer` (`customer_id`),
  ADD KEY `idx_rating` (`rating`),
  ADD KEY `idx_active` (`is_active`);

--
-- Indeks untuk tabel `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `idx_role` (`role`),
  ADD KEY `idx_email` (`email`),
  ADD KEY `idx_is_active` (`is_active`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `cart`
--
ALTER TABLE `cart`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT untuk tabel `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT untuk tabel `customer_addresses`
--
ALTER TABLE `customer_addresses`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `gallery`
--
ALTER TABLE `gallery`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `kurir_earnings`
--
ALTER TABLE `kurir_earnings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `kurir_locations`
--
ALTER TABLE `kurir_locations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `kurir_tarif`
--
ALTER TABLE `kurir_tarif`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT untuk tabel `kurir_withdrawals`
--
ALTER TABLE `kurir_withdrawals`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `order_details`
--
ALTER TABLE `order_details`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT untuk tabel `settings`
--
ALTER TABLE `settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT untuk tabel `testimonials`
--
ALTER TABLE `testimonials`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT untuk tabel `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `cart`
--
ALTER TABLE `cart`
  ADD CONSTRAINT `cart_ibfk_1` FOREIGN KEY (`customer_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `cart_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `customer_addresses`
--
ALTER TABLE `customer_addresses`
  ADD CONSTRAINT `customer_addresses_ibfk_1` FOREIGN KEY (`customer_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `kurir_earnings`
--
ALTER TABLE `kurir_earnings`
  ADD CONSTRAINT `kurir_earnings_ibfk_1` FOREIGN KEY (`kurir_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `kurir_earnings_ibfk_2` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `kurir_locations`
--
ALTER TABLE `kurir_locations`
  ADD CONSTRAINT `kurir_locations_ibfk_1` FOREIGN KEY (`kurir_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `kurir_withdrawals`
--
ALTER TABLE `kurir_withdrawals`
  ADD CONSTRAINT `kurir_withdrawals_ibfk_1` FOREIGN KEY (`kurir_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `notifications`
--
ALTER TABLE `notifications`
  ADD CONSTRAINT `notifications_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`customer_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `orders_ibfk_2` FOREIGN KEY (`kurir_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Ketidakleluasaan untuk tabel `order_details`
--
ALTER TABLE `order_details`
  ADD CONSTRAINT `order_details_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `order_details_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `products_ibfk_1` FOREIGN KEY (`kategori_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL;

--
-- Ketidakleluasaan untuk tabel `testimonials`
--
ALTER TABLE `testimonials`
  ADD CONSTRAINT `testimonials_ibfk_1` FOREIGN KEY (`customer_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `testimonials_ibfk_2` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
