-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Waktu pembuatan: 03 Agu 2026 pada 01.11
-- Versi server: 11.8.8-MariaDB-log
-- Versi PHP: 7.2.34

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `u684817258_DataSystemPPE`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `approval_apd`
--

CREATE TABLE `approval_apd` (
  `id` int(11) NOT NULL,
  `pengajuan_id` int(11) NOT NULL,
  `approver_id` int(11) NOT NULL,
  `level_approval` enum('Safety Coordinator','Project Manager') DEFAULT NULL,
  `status` enum('Pending','Approved','Rejected') DEFAULT 'Pending',
  `catatan` text DEFAULT NULL,
  `approved_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `audit_trail`
--

CREATE TABLE `audit_trail` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `aktivitas` varchar(255) DEFAULT NULL,
  `ip_address` varchar(50) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `detail_pengajuan`
--

CREATE TABLE `detail_pengajuan` (
  `id` int(11) NOT NULL,
  `pengajuan_id` int(11) NOT NULL,
  `ppe_id` int(11) NOT NULL,
  `jumlah` int(11) NOT NULL,
  `ukuran` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `dokumentasi_apd`
--

CREATE TABLE `dokumentasi_apd` (
  `id` int(11) NOT NULL,
  `pengajuan_id` int(11) NOT NULL,
  `nama_file` varchar(255) DEFAULT NULL,
  `lokasi_file` varchar(255) DEFAULT NULL,
  `uploaded_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `master_ppe`
--

CREATE TABLE `master_ppe` (
  `id` int(11) NOT NULL,
  `kode_ppe` varchar(30) NOT NULL,
  `nama_ppe` varchar(100) NOT NULL,
  `kategori` varchar(100) DEFAULT NULL,
  `ukuran` varchar(20) DEFAULT NULL,
  `stok` int(11) DEFAULT 0,
  `satuan` varchar(20) DEFAULT NULL,
  `lokasi` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `notifikasi`
--

CREATE TABLE `notifikasi` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `judul` varchar(150) DEFAULT NULL,
  `pesan` text DEFAULT NULL,
  `status` enum('Belum Dibaca','Sudah Dibaca') DEFAULT 'Belum Dibaca',
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `pengajuan_apd`
--

CREATE TABLE `pengajuan_apd` (
  `id` int(11) NOT NULL,
  `no_pengajuan` varchar(30) NOT NULL,
  `user_id` int(11) NOT NULL,
  `tanggal` date NOT NULL,
  `status` enum('Pending','Safety Coordinator','Project Manager','Disetujui','Ditolak','Selesai') DEFAULT 'Pending',
  `keterangan` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `nama` varchar(100) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `role` enum('Administrator','Safety Coordinator','Project Manager','Employee') NOT NULL,
  `status` enum('Aktif','Nonaktif') DEFAULT 'Aktif',
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `approval_apd`
--
ALTER TABLE `approval_apd`
  ADD PRIMARY KEY (`id`),
  ADD KEY `pengajuan_id` (`pengajuan_id`),
  ADD KEY `approver_id` (`approver_id`);

--
-- Indeks untuk tabel `audit_trail`
--
ALTER TABLE `audit_trail`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indeks untuk tabel `detail_pengajuan`
--
ALTER TABLE `detail_pengajuan`
  ADD PRIMARY KEY (`id`),
  ADD KEY `pengajuan_id` (`pengajuan_id`),
  ADD KEY `ppe_id` (`ppe_id`);

--
-- Indeks untuk tabel `dokumentasi_apd`
--
ALTER TABLE `dokumentasi_apd`
  ADD PRIMARY KEY (`id`),
  ADD KEY `pengajuan_id` (`pengajuan_id`);

--
-- Indeks untuk tabel `master_ppe`
--
ALTER TABLE `master_ppe`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `kode_ppe` (`kode_ppe`);

--
-- Indeks untuk tabel `notifikasi`
--
ALTER TABLE `notifikasi`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indeks untuk tabel `pengajuan_apd`
--
ALTER TABLE `pengajuan_apd`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `no_pengajuan` (`no_pengajuan`),
  ADD KEY `user_id` (`user_id`);

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
-- AUTO_INCREMENT untuk tabel `approval_apd`
--
ALTER TABLE `approval_apd`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `audit_trail`
--
ALTER TABLE `audit_trail`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `detail_pengajuan`
--
ALTER TABLE `detail_pengajuan`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `dokumentasi_apd`
--
ALTER TABLE `dokumentasi_apd`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `master_ppe`
--
ALTER TABLE `master_ppe`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `notifikasi`
--
ALTER TABLE `notifikasi`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `pengajuan_apd`
--
ALTER TABLE `pengajuan_apd`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `approval_apd`
--
ALTER TABLE `approval_apd`
  ADD CONSTRAINT `approval_apd_ibfk_1` FOREIGN KEY (`pengajuan_id`) REFERENCES `pengajuan_apd` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `approval_apd_ibfk_2` FOREIGN KEY (`approver_id`) REFERENCES `users` (`id`);

--
-- Ketidakleluasaan untuk tabel `audit_trail`
--
ALTER TABLE `audit_trail`
  ADD CONSTRAINT `audit_trail_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Ketidakleluasaan untuk tabel `detail_pengajuan`
--
ALTER TABLE `detail_pengajuan`
  ADD CONSTRAINT `detail_pengajuan_ibfk_1` FOREIGN KEY (`pengajuan_id`) REFERENCES `pengajuan_apd` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `detail_pengajuan_ibfk_2` FOREIGN KEY (`ppe_id`) REFERENCES `master_ppe` (`id`);

--
-- Ketidakleluasaan untuk tabel `dokumentasi_apd`
--
ALTER TABLE `dokumentasi_apd`
  ADD CONSTRAINT `dokumentasi_apd_ibfk_1` FOREIGN KEY (`pengajuan_id`) REFERENCES `pengajuan_apd` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `notifikasi`
--
ALTER TABLE `notifikasi`
  ADD CONSTRAINT `notifikasi_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Ketidakleluasaan untuk tabel `pengajuan_apd`
--
ALTER TABLE `pengajuan_apd`
  ADD CONSTRAINT `pengajuan_apd_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
