-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3310
-- Waktu pembuatan: 03 Des 2025 pada 05.58
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
-- Database: `cs_ticketing`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `kategori_keluhan`
--

CREATE TABLE `kategori_keluhan` (
  `id` int(10) UNSIGNED NOT NULL,
  `nama_kategori` varchar(100) DEFAULT NULL,
  `deskripsi` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `kategori_keluhan`
--

INSERT INTO `kategori_keluhan` (`id`, `nama_kategori`, `deskripsi`) VALUES
(1, 'Sinyal Lemah / Hilang', 'Gangguan sinyal di area pelanggan'),
(2, 'Internet Lambat', 'Kecepatan data di bawah standar'),
(3, 'Gangguan Fiber / ODP Rusak', 'ODP/ONT/FO bermasalah'),
(4, 'Tidak Bisa Telepon/SMS', 'Voice/SMS gagal'),
(5, 'Keluhan Paket Tidak Masuk', 'Paket belum aktif'),
(6, 'Aplikasi MyTelkomsel Error', 'Aplikasi tidak bisa digunakan'),
(7, 'Billing / Tagihan Tidak Sesuai', 'Tagihan dianggap salah'),
(8, 'dasdds', 'dasdsssss');

-- --------------------------------------------------------

--
-- Struktur dari tabel `keluhan`
--

CREATE TABLE `keluhan` (
  `id` int(10) UNSIGNED NOT NULL,
  `kode_keluhan` varchar(50) DEFAULT NULL,
  `pelanggan_id` int(10) UNSIGNED DEFAULT NULL,
  `kategori_id` int(10) UNSIGNED DEFAULT NULL,
  `channel` enum('Call Center','Grapari','WhatsApp','Aplikasi','Live Chat','Media Sosial','Email','Lainnya') DEFAULT NULL,
  `deskripsi_keluhan` text DEFAULT NULL,
  `status_keluhan` enum('Open','On Progress','Pending','Solved','Closed') DEFAULT NULL,
  `prioritas` enum('Low','Medium','High','Critical') DEFAULT NULL,
  `tanggal_lapor` datetime DEFAULT NULL,
  `tanggal_update_terakhir` datetime DEFAULT NULL,
  `tanggal_selesai` datetime DEFAULT NULL,
  `created_by` int(10) UNSIGNED DEFAULT NULL,
  `updated_by` int(10) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `keluhan`
--

INSERT INTO `keluhan` (`id`, `kode_keluhan`, `pelanggan_id`, `kategori_id`, `channel`, `deskripsi_keluhan`, `status_keluhan`, `prioritas`, `tanggal_lapor`, `tanggal_update_terakhir`, `tanggal_selesai`, `created_by`, `updated_by`) VALUES
(1, 'CMP-2025-A1B2C3', 1, 2, 'WhatsApp', 'Internet sangat lambat sejak pagi.', 'Closed', 'Low', '2025-11-29 08:30:00', '2025-12-01 15:40:40', '2025-11-30 15:43:10', 2, 2),
(2, 'CMP-2025-D4E5F6', 2, 5, 'Call Center', 'Paket data yang dibeli belum aktif.', 'On Progress', 'Medium', '2025-11-29 08:45:00', '2025-12-02 18:01:36', NULL, 2, 2),
(3, 'CMP-2025-G7H8I9', 3, 7, 'Aplikasi', 'Tagihan bulan ini melonjak tidak wajar.', 'Solved', 'High', '2025-11-28 15:10:00', '2025-11-28 16:00:00', '2025-11-28 16:00:00', 2, 1),
(6, 'KEL-20251202-1395', 1, 1, 'Call Center', 'Tes via curl 2', 'Open', 'Medium', '2025-12-02 17:58:54', '2025-12-02 17:58:54', NULL, 2, 2),
(7, 'KEL-20251202-8990', 1, 6, 'Call Center', 'dsda', 'Open', 'Medium', '2025-12-02 18:02:22', '2025-12-02 18:02:22', NULL, 2, 2);

-- --------------------------------------------------------

--
-- Struktur dari tabel `keluhan_log`
--

CREATE TABLE `keluhan_log` (
  `id` int(10) UNSIGNED NOT NULL,
  `keluhan_id` int(10) UNSIGNED DEFAULT NULL,
  `status_log` enum('Open','On Progress','Pending','Solved','Closed') DEFAULT NULL,
  `catatan` text DEFAULT NULL,
  `tanggal_log` datetime DEFAULT NULL,
  `user_id` int(10) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `keluhan_log`
--

INSERT INTO `keluhan_log` (`id`, `keluhan_id`, `status_log`, `catatan`, `tanggal_log`, `user_id`) VALUES
(1, 1, 'On Progress', 'Sedang dicek ke jaringan, kemungkinan congested.', '2025-11-29 09:00:00', 2),
(2, 2, 'Open', 'Keluhan diterima, menunggu verifikasi paket.', '2025-11-29 08:45:00', 2),
(3, 3, 'Solved', 'Tagihan dikoreksi, pelanggan sudah diinformasikan.', '2025-11-28 16:00:00', 1),
(4, 2, 'On Progress', 'Ntah', '2025-11-30 11:55:14', 1),
(5, 2, 'Open', 'dasd', '2025-11-30 11:55:30', 1),
(6, 2, 'Pending', 'Menunggu antrian', '2025-11-30 12:01:00', 1),
(13, 2, 'Closed', 'bulk: ditutup test', '2025-11-30 13:04:06', 1),
(14, 1, 'Closed', 'bulk: ditutup test', '2025-11-30 13:04:06', 1),
(15, 1, 'Closed', 'Test', '2025-11-30 15:43:10', 1),
(16, 2, 'Open', 'xsxds', '2025-12-01 10:39:42', 1),
(17, 2, 'On Progress', 'm', '2025-12-02 18:01:36', 2);

-- --------------------------------------------------------

--
-- Struktur dari tabel `notifications`
--

CREATE TABLE `notifications` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `title` varchar(255) NOT NULL,
  `message` text DEFAULT NULL,
  `is_read` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `notifications`
--

INSERT INTO `notifications` (`id`, `user_id`, `title`, `message`, `is_read`, `created_at`) VALUES
(1, 1, 'Keluhan baru KEL-20251202-1395', 'Prioritas Medium via Call Center', 1, '2025-12-03 00:58:54'),
(2, 5, 'Keluhan baru KEL-20251202-1395', 'Prioritas Medium via Call Center', 0, '2025-12-03 00:58:54'),
(3, 6, 'Keluhan baru KEL-20251202-1395', 'Prioritas Medium via Call Center', 0, '2025-12-03 00:58:54'),
(4, 1, 'Keluhan baru KEL-20251202-8990', 'Prioritas Medium via Call Center', 1, '2025-12-03 01:02:22'),
(5, 5, 'Keluhan baru KEL-20251202-8990', 'Prioritas Medium via Call Center', 0, '2025-12-03 01:02:22'),
(6, 6, 'Keluhan baru KEL-20251202-8990', 'Prioritas Medium via Call Center', 0, '2025-12-03 01:02:22');

-- --------------------------------------------------------

--
-- Struktur dari tabel `password_resets`
--

CREATE TABLE `password_resets` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `reset_by` int(10) UNSIGNED NOT NULL,
  `temp_password_hash` varchar(255) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `used_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `password_resets`
--

INSERT INTO `password_resets` (`id`, `user_id`, `reset_by`, `temp_password_hash`, `created_at`, `used_at`) VALUES
(12, 5, 1, '$2y$10$O2Zsjqcc3vX0g0KbExdxkulyWpLSOSfnVSibpzbiFiqTny9PMykmi', '2025-12-01 21:11:22', '2025-12-01 22:12:53'),
(13, 5, 1, '$2y$10$ULovc05QJ/z.g.1P56AoVuwk5JP9s0V2RgwQTaAU3PrzYZYgxFppS', '2025-12-01 21:11:59', NULL);

-- --------------------------------------------------------

--
-- Struktur dari tabel `pelanggan`
--

CREATE TABLE `pelanggan` (
  `id` int(10) UNSIGNED NOT NULL,
  `nama_pelanggan` varchar(100) DEFAULT NULL,
  `no_hp` varchar(20) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `kota` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `pelanggan`
--

INSERT INTO `pelanggan` (`id`, `nama_pelanggan`, `no_hp`, `email`, `kota`) VALUES
(1, 'Budi Santoso', '081234567890', 'budi@example.com', 'Makassar'),
(2, 'Siti Aminah', '082233445566', 'siti@example.com', 'Jakarta'),
(3, 'Andi Wijaya', '081345678901', 'andi@example.com', 'Bandung'),
(5, 'David', '082339239299', 'david22@gmail.com', 'Manado'),
(6, 'dasd', '08233232', 'dasd@gmail.com', 'dsad'),
(7, 'Test Agent New', '081234567891', NULL, 'Bandung');

-- --------------------------------------------------------

--
-- Struktur dari tabel `users`
--

CREATE TABLE `users` (
  `id` int(10) UNSIGNED NOT NULL,
  `nama` varchar(100) DEFAULT NULL,
  `kontak` varchar(100) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password_hash` varchar(255) DEFAULT NULL,
  `role` enum('agent','supervisor','admin') DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `must_change_password` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `users`
--

INSERT INTO `users` (`id`, `nama`, `kontak`, `username`, `password_hash`, `role`, `is_active`, `must_change_password`) VALUES
(1, 'Admin', '081234567890', 'admin', '$2y$10$jhkaoJnggbtcp3WsREXgbOd7ipth1SPkXZptiWcdmYfv8V8e2WpeK', 'admin', 1, 0),
(2, 'Agent ABCD', '08123454333', 'agent', '$2y$10$K3YIAC.klQqYN/xIMb43dennczocgFgNJhE.enK9iJFghZHDjU48G', 'agent', 1, 0),
(5, 'Yefta Asyel', 'yefta@gmail.com', 'yefta', '$2y$10$g8mjsNos40TTU15B3sVhU.dX4.VhvWRZxSwyRfoHhNiERYO7zR0wO', 'supervisor', 1, 0),
(6, 'Admin2', 'admin@gmail.com', 'admin2', '$2y$10$mfjPygQnPaonWgNXa0NWSuALmA9eZkVBR9fo489IhEdgsIfdfLJ4i', 'admin', 1, 0);

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `kategori_keluhan`
--
ALTER TABLE `kategori_keluhan`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `keluhan`
--
ALTER TABLE `keluhan`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `kode_keluhan` (`kode_keluhan`),
  ADD KEY `pelanggan_id` (`pelanggan_id`),
  ADD KEY `kategori_id` (`kategori_id`),
  ADD KEY `created_by` (`created_by`),
  ADD KEY `updated_by` (`updated_by`);

--
-- Indeks untuk tabel `keluhan_log`
--
ALTER TABLE `keluhan_log`
  ADD PRIMARY KEY (`id`),
  ADD KEY `keluhan_id` (`keluhan_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indeks untuk tabel `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indeks untuk tabel `password_resets`
--
ALTER TABLE `password_resets`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `reset_by` (`reset_by`);

--
-- Indeks untuk tabel `pelanggan`
--
ALTER TABLE `pelanggan`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `kategori_keluhan`
--
ALTER TABLE `kategori_keluhan`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT untuk tabel `keluhan`
--
ALTER TABLE `keluhan`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT untuk tabel `keluhan_log`
--
ALTER TABLE `keluhan_log`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT untuk tabel `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT untuk tabel `password_resets`
--
ALTER TABLE `password_resets`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT untuk tabel `pelanggan`
--
ALTER TABLE `pelanggan`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT untuk tabel `users`
--
ALTER TABLE `users`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `keluhan`
--
ALTER TABLE `keluhan`
  ADD CONSTRAINT `keluhan_ibfk_1` FOREIGN KEY (`pelanggan_id`) REFERENCES `pelanggan` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `keluhan_ibfk_2` FOREIGN KEY (`kategori_id`) REFERENCES `kategori_keluhan` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `keluhan_ibfk_3` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `keluhan_ibfk_4` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`) ON UPDATE CASCADE;

--
-- Ketidakleluasaan untuk tabel `keluhan_log`
--
ALTER TABLE `keluhan_log`
  ADD CONSTRAINT `keluhan_log_ibfk_1` FOREIGN KEY (`keluhan_id`) REFERENCES `keluhan` (`id`),
  ADD CONSTRAINT `keluhan_log_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON UPDATE CASCADE;

--
-- Ketidakleluasaan untuk tabel `notifications`
--
ALTER TABLE `notifications`
  ADD CONSTRAINT `notifications_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ketidakleluasaan untuk tabel `password_resets`
--
ALTER TABLE `password_resets`
  ADD CONSTRAINT `password_resets_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `password_resets_ibfk_2` FOREIGN KEY (`reset_by`) REFERENCES `users` (`id`) ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
