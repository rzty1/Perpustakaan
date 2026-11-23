-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 23 Nov 2026 pada 08.51
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
-- Database: `web_perpus`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `anggota`
--

CREATE TABLE `anggota` (
  `id_anggota` int(10) UNSIGNED NOT NULL,
  `nama` varchar(120) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(150) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `status` enum('aktif','nonaktif') NOT NULL DEFAULT 'aktif',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `anggota`
--

INSERT INTO `anggota` (`id_anggota`, `nama`, `username`, `email`, `password_hash`, `status`, `created_at`) VALUES
(1, 'Reza', 'reza', 'rzty18@gmail.com', '$2y$10$WqCIfvFxC0flJjpFjb0OHu46MATEFNC4y6iSforFiX3usUtGGovKi', 'aktif', '2026-11-23 07:29:24');

-- --------------------------------------------------------

--
-- Struktur dari tabel `buku`
--

CREATE TABLE `buku` (
  `id_buku` int(10) UNSIGNED NOT NULL,
  `judul_buku` varchar(180) NOT NULL,
  `pengarang_buku` varchar(120) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `buku`
--

INSERT INTO `buku` (`id_buku`, `judul_buku`, `pengarang_buku`, `created_at`) VALUES
(1, 'Laskar Pelangi', 'Andrea Hirata', '2026-11-23 07:25:03'),
(2, 'Bumi', 'Tere Liye', '2026-11-23 07:25:03'),
(3, 'Filosofi Teras', 'Henry Manampiring', '2026-11-23 07:25:03'),
(4, 'Negeri 5 Menara', 'Ahmad Fuadi', '2026-11-23 07:25:03'),
(5, 'Atomic Habits', 'James Clear', '2026-11-23 07:25:03'),
(6, 'Anas tanpa Ipung', 'Manusia', '2026-11-23 07:28:18');

-- --------------------------------------------------------

--
-- Struktur dari tabel `petugas`
--

CREATE TABLE `petugas` (
  `id_petugas` int(10) UNSIGNED NOT NULL,
  `nama` varchar(120) NOT NULL,
  `role` enum('admin','petugas') NOT NULL DEFAULT 'petugas',
  `username` varchar(50) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `status` enum('aktif','nonaktif') NOT NULL DEFAULT 'aktif',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `petugas`
--

INSERT INTO `petugas` (`id_petugas`, `nama`, `role`, `username`, `password_hash`, `status`, `created_at`) VALUES
(1, 'Administrator', 'admin', 'admin', '$2y$12$07pyxWYPq0B2PU/JU3Q4AOwTzNVUKdvByKjln9IXjqAf3MYvr.ngu', 'aktif', '2026-11-23 07:25:03'),
(2, 'Petugas Perpustakaan', 'petugas', 'petugas', '$2y$12$OKQ5Ghw29g1pZJ59JpO/mueKi11h8Mty/8AbFftgW333VoqL9fCJ2', 'aktif', '2026-11-23 07:25:03');

-- --------------------------------------------------------

--
-- Struktur dari tabel `pinjam_buku`
--

CREATE TABLE `pinjam_buku` (
  `id` int(10) UNSIGNED NOT NULL,
  `tanggal_pinajaman` date NOT NULL,
  `id_buku` int(10) UNSIGNED NOT NULL,
  `tanggal_kembali` date NOT NULL,
  `id_anggota` int(10) UNSIGNED NOT NULL,
  `id_petugas` int(10) UNSIGNED DEFAULT NULL,
  `status` enum('dipinjam','dikembalikan') NOT NULL DEFAULT 'dipinjam',
  `tanggal_dikembalikan` date DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `pinjam_buku`
--

INSERT INTO `pinjam_buku` (`id`, `tanggal_pinajaman`, `id_buku`, `tanggal_kembali`, `id_anggota`, `id_petugas`, `status`, `tanggal_dikembalikan`, `created_at`) VALUES
(1, '2026-11-23', 6, '2026-11-30', 1, 1, 'dikembalikan', '2026-11-23', '2026-11-23 07:29:38');

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `anggota`
--
ALTER TABLE `anggota`
  ADD PRIMARY KEY (`id_anggota`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indeks untuk tabel `buku`
--
ALTER TABLE `buku`
  ADD PRIMARY KEY (`id_buku`);

--
-- Indeks untuk tabel `petugas`
--
ALTER TABLE `petugas`
  ADD PRIMARY KEY (`id_petugas`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indeks untuk tabel `pinjam_buku`
--
ALTER TABLE `pinjam_buku`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_pinjam_buku` (`id_buku`),
  ADD KEY `fk_pinjam_petugas` (`id_petugas`),
  ADD KEY `idx_pinjam_status` (`status`),
  ADD KEY `idx_pinjam_tanggal_kembali` (`tanggal_kembali`),
  ADD KEY `idx_pinjam_anggota` (`id_anggota`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `anggota`
--
ALTER TABLE `anggota`
  MODIFY `id_anggota` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT untuk tabel `buku`
--
ALTER TABLE `buku`
  MODIFY `id_buku` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT untuk tabel `petugas`
--
ALTER TABLE `petugas`
  MODIFY `id_petugas` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT untuk tabel `pinjam_buku`
--
ALTER TABLE `pinjam_buku`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `pinjam_buku`
--
ALTER TABLE `pinjam_buku`
  ADD CONSTRAINT `fk_pinjam_anggota` FOREIGN KEY (`id_anggota`) REFERENCES `anggota` (`id_anggota`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_pinjam_buku` FOREIGN KEY (`id_buku`) REFERENCES `buku` (`id_buku`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_pinjam_petugas` FOREIGN KEY (`id_petugas`) REFERENCES `petugas` (`id_petugas`) ON DELETE SET NULL ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
