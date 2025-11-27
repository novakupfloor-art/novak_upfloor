-- SQL untuk membuat table promosi dan insert data gambar promosi yang ada
-- Jalankan query ini di phpMyAdmin atau MySQL client

-- Buat table promosi
CREATE TABLE IF NOT EXISTS `promosi` (
  `id_promosi` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `judul_promosi` varchar(200) NOT NULL,
  `deskripsi` text DEFAULT NULL,
  `gambar` varchar(255) NOT NULL,
  `link_url` varchar(255) DEFAULT NULL,
  `status_promosi` enum('Aktif','Tidak Aktif') NOT NULL DEFAULT 'Aktif',
  `urutan` int(11) NOT NULL DEFAULT 0,
  `tanggal_mulai` date DEFAULT NULL,
  `tanggal_selesai` date DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id_promosi`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert data promosi dari gambar yang ada di folder assets/upload/promosi
INSERT INTO `promosi` (`judul_promosi`, `deskripsi`, `gambar`, `link_url`, `status_promosi`, `urutan`, `tanggal_mulai`, `tanggal_selesai`, `created_at`, `updated_at`) VALUES
('Fitur Iklan Jual Waisaka Property', 'Promosikan properti Anda dengan fitur iklan jual terbaik di Waisaka Property', 'fitur iklan jual waisaka.png', '/search/jual?order=newest&limit=9', 'Aktif', 1, NOW(), DATE_ADD(NOW(), INTERVAL 3 MONTH), NOW(), NOW()),
('Fitur Iklan Renovasi & Pembangunan', 'Layanan renovasi dan pembangunan properti terpercaya', 'fitur iklan renovasi pembangunan.png', '/search2/done', 'Aktif', 2, NOW(), DATE_ADD(NOW(), INTERVAL 3 MONTH), NOW(), NOW()),
('Fitur Iklan Sewa Properti', 'Sewa properti impian Anda dengan mudah di Waisaka Property', 'fitur untuk iklan sewa.png', '/search/sewa?order=newest&limit=9', 'Aktif', 3, NOW(), DATE_ADD(NOW(), INTERVAL 3 MONTH), NOW(), NOW());

