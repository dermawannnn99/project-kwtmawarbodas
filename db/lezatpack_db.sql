-- =========================================================================
-- LezatPack / KWT Mawar Bodas II — Database Schema
-- Versi ini sudah sinkron dengan semua kolom aktual di aplikasi.
-- Import file ini ke phpMyAdmin atau jalankan via: mysql -u root lezatpack_db < lezatpack_db.sql
-- =========================================================================

CREATE DATABASE IF NOT EXISTS `lezatpack_db` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `lezatpack_db`;

-- =========================================================================
-- Tabel: login
-- [DB-1] Tambah kolom last_login_at dan last_login_ip untuk audit trail
-- =========================================================================
DROP TABLE IF EXISTS `login`;
CREATE TABLE `login` (
  `id`            int(11)      NOT NULL AUTO_INCREMENT,
  `username`      varchar(50)  NOT NULL,
  `password`      varchar(255) NOT NULL,
  `last_login_at` timestamp    NULL DEFAULT NULL,
  `last_login_ip` varchar(45)  NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Password default: admin123 (bcrypt) — GANTI SEGERA di production!
INSERT INTO `login` (`id`, `username`, `password`) VALUES
(1, 'admin', '$2y$10$6eldp9DjZLrJOmMxzp.XnuatD3hu9iMVARl/OUwg3yFZGyLhTFm3i');

-- =========================================================================
-- Tabel: login_attempts
-- [SEC-5] Rate limiting persisten berbasis DB (username + IP)
-- =========================================================================
DROP TABLE IF EXISTS `login_attempts`;
CREATE TABLE `login_attempts` (
  `id`              int(11)      NOT NULL AUTO_INCREMENT,
  `identifier`      varchar(100) NOT NULL,
  `ip_address`      varchar(45)  NOT NULL,
  `attempt_count`   int(11)      NOT NULL DEFAULT 0,
  `last_attempt_at` timestamp    NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `locked_until`    timestamp    NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_identifier_ip` (`identifier`, `ip_address`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =========================================================================
-- Tabel: products
-- [BUG-3] Kolom is_visible sudah masuk ke definisi — tidak perlu ALTER per-request
-- =========================================================================
DROP TABLE IF EXISTS `products`;
CREATE TABLE `products` (
  `id`          int(11)        NOT NULL AUTO_INCREMENT,
  `name`        varchar(255)   NOT NULL,
  `price`       decimal(10,2)  NOT NULL,
  `image_url`   text           DEFAULT NULL,
  `badge`       varchar(50)    DEFAULT NULL,
  `description` text           DEFAULT NULL,
  `batch_code`  varchar(50)    DEFAULT NULL,
  `prod_date`   date           DEFAULT NULL,
  `exp_date`    date           DEFAULT NULL,
  `is_visible`  tinyint(1)     NOT NULL DEFAULT 1,
  `created_at`  timestamp      NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `batch_code` (`batch_code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Seed data produk (gambar pakai placeholder lokal, bukan URL Unsplash eksternal)
INSERT INTO `products` (`id`, `name`, `price`, `image_url`, `badge`, `description`, `batch_code`, `prod_date`, `exp_date`, `is_visible`, `created_at`) VALUES
(3, 'Rendang Daging Sapi', 65000.00, 'assets/img/gambarhero.png', 'Best Seller',
 'Daging sapi pilihan berbalut bumbu rempah asli Minang yang pekat. Dimasak perlahan untuk memastikan bumbu meresap sempurna.',
 'LZT-8742', '2026-05-01', '2026-11-01', 1, '2026-05-19 15:14:09'),
(4, 'Ayam Woku Belanga', 45000.00, 'assets/img/gambarhero.png', 'Pedas Gurih',
 'Potongan daging ayam lembut dengan balutan bumbu kuning pedas kemangi khas Manado yang menggugah selera.',
 'LZT-3129', '2026-05-10', '2026-11-10', 1, '2026-05-19 15:14:09'),
(5, 'Sambal Goreng Ati', 35000.00, 'assets/img/gambarhero.png', 'Favorit',
 'Kombinasi ati ampela ayam segar dan kentang dadu yang digoreng dengan bumbu balado tradisional bercita rasa manis-pedas.',
 'LZT-1094', '2026-05-15', '2026-11-15', 1, '2026-05-19 15:14:09');

ALTER TABLE `login`    MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
ALTER TABLE `products` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;
