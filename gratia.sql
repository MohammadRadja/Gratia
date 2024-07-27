-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Waktu pembuatan: 27 Jul 2024 pada 06.53
-- Versi server: 8.0.30
-- Versi PHP: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `gratia`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `appointment`
--

CREATE TABLE `appointment` (
  `id_appointment` char(36) NOT NULL,
  `id_pasien` char(36) NOT NULL,
  `id_dokter` char(36) NOT NULL,
  `id_treatment` char(36) NOT NULL,
  `jadwal_appointment` date NOT NULL,
  `catatan` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `dokter`
--

CREATE TABLE `dokter` (
  `id_dokter` char(36) NOT NULL,
  `nama_dokter` varchar(100) NOT NULL,
  `spesialisasi` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data untuk tabel `dokter`
--

INSERT INTO `dokter` (`id_dokter`, `nama_dokter`, `spesialisasi`) VALUES
('5806c896-4b3b-11ef-8725-5405db937996', 'Dr. John Doe', 'Dokter Umum'),
('5806d126-4b3b-11ef-8725-5405db937996', 'Dr. Jane Smith', 'Spesialis Ibu'),
('5806d3f7-4b3b-11ef-8725-5405db937996', 'Dr. Sarah Johnson', 'Spesialis Kandungan'),
('5806d587-4b3b-11ef-8725-5405db937996', 'Dr. Michael Brown', 'Spesialis Bedah'),
('5806d6cd-4b3b-11ef-8725-5405db937996', 'Dr. Emily Davis', 'Spesialis Jantung');

-- --------------------------------------------------------

--
-- Struktur dari tabel `pasien`
--

CREATE TABLE `pasien` (
  `id_pasien` char(36) NOT NULL,
  `nama` varchar(100) NOT NULL,
  `alamat` text NOT NULL,
  `jenis_kelamin` enum('Laki-laki','Perempuan') CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `no_telp` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `status_pembayaran` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `transaksi`
--

CREATE TABLE `transaksi` (
  `id_transaksi` char(36) NOT NULL,
  `id_pasien` char(36) NOT NULL,
  `id_dokter` char(36) NOT NULL,
  `id_treatment` char(36) NOT NULL,
  `tanggal_bayar` date DEFAULT NULL,
  `jumlah_bayar` decimal(10,2) DEFAULT NULL,
  `bukti_pembayaran` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `treatment`
--

CREATE TABLE `treatment` (
  `id_treatment` char(36) NOT NULL,
  `nama_treatment` varchar(100) NOT NULL,
  `biaya` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data untuk tabel `treatment`
--

INSERT INTO `treatment` (`id_treatment`, `nama_treatment`, `biaya`) VALUES
('526e5aa2-4b3b-11ef-8725-5405db937996', 'Pemeriksaan Kesehatan Umum', 500.00),
('526e6457-4b3b-11ef-8725-5405db937996', 'Vaksinasi Anak', 50000.00),
('526e6765-4b3b-11ef-8725-5405db937996', 'USG Kehamilan', 300000.00),
('526e6915-4b3b-11ef-8725-5405db937996', 'Operasi Kecil', 2500000.00),
('526e6a29-4b3b-11ef-8725-5405db937996', 'Konsultasi Jantung', 20000000.00);

-- --------------------------------------------------------

--
-- Struktur dari tabel `user`
--

CREATE TABLE `user` (
  `id_user` char(36) NOT NULL,
  `username` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `level` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data untuk tabel `user`
--

INSERT INTO `user` (`id_user`, `username`, `password`, `level`) VALUES
('1589aecc-4b3b-11ef-8725-5405db937996', 'admin', '21232f297a57a5a743894a0e4a801fc3', 'admin');

-- --------------------------------------------------------

--
-- Stand-in struktur untuk tampilan `view_pembayaran`
-- (Lihat di bawah untuk tampilan aktual)
--
CREATE TABLE `view_pembayaran` (
`biaya` decimal(10,2)
,`bukti_pembayaran` varchar(255)
,`id_dokter` char(36)
,`id_pasien` char(36)
,`id_transaksi` char(36)
,`id_treatment` char(36)
,`jadwal_appointment` date
,`jumlah_bayar` decimal(10,2)
,`nama_dokter` varchar(100)
,`nama_pasien` varchar(100)
,`nama_treatment` varchar(100)
,`status_pembayaran` varchar(20)
,`tanggal_bayar` date
);

-- --------------------------------------------------------

--
-- Struktur untuk view `view_pembayaran`
--
DROP TABLE IF EXISTS `view_pembayaran`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `view_pembayaran`  AS SELECT `t`.`id_transaksi` AS `id_transaksi`, `p`.`id_pasien` AS `id_pasien`, `p`.`nama` AS `nama_pasien`, `d`.`id_dokter` AS `id_dokter`, `d`.`nama_dokter` AS `nama_dokter`, `tr`.`id_treatment` AS `id_treatment`, `tr`.`nama_treatment` AS `nama_treatment`, `tr`.`biaya` AS `biaya`, `a`.`jadwal_appointment` AS `jadwal_appointment`, `p`.`status_pembayaran` AS `status_pembayaran`, `t`.`tanggal_bayar` AS `tanggal_bayar`, `t`.`jumlah_bayar` AS `jumlah_bayar`, `t`.`bukti_pembayaran` AS `bukti_pembayaran` FROM ((((`transaksi` `t` join `pasien` `p` on((`t`.`id_pasien` = `p`.`id_pasien`))) join `dokter` `d` on((`t`.`id_dokter` = `d`.`id_dokter`))) join `treatment` `tr` on((`t`.`id_treatment` = `tr`.`id_treatment`))) join `appointment` `a` on(((`t`.`id_pasien` = `a`.`id_pasien`) and (`t`.`id_dokter` = `a`.`id_dokter`) and (`t`.`id_treatment` = `a`.`id_treatment`)))) ;

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `appointment`
--
ALTER TABLE `appointment`
  ADD PRIMARY KEY (`id_appointment`),
  ADD KEY `id_pasien` (`id_pasien`),
  ADD KEY `id_dokter` (`id_dokter`),
  ADD KEY `id_treatment` (`id_treatment`);

--
-- Indeks untuk tabel `dokter`
--
ALTER TABLE `dokter`
  ADD PRIMARY KEY (`id_dokter`);

--
-- Indeks untuk tabel `pasien`
--
ALTER TABLE `pasien`
  ADD PRIMARY KEY (`id_pasien`);

--
-- Indeks untuk tabel `transaksi`
--
ALTER TABLE `transaksi`
  ADD PRIMARY KEY (`id_transaksi`),
  ADD KEY `id_pasien` (`id_pasien`),
  ADD KEY `id_dokter` (`id_dokter`),
  ADD KEY `id_treatment` (`id_treatment`);

--
-- Indeks untuk tabel `treatment`
--
ALTER TABLE `treatment`
  ADD PRIMARY KEY (`id_treatment`);

--
-- Indeks untuk tabel `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id_user`);

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `appointment`
--
ALTER TABLE `appointment`
  ADD CONSTRAINT `appointment_ibfk_1` FOREIGN KEY (`id_pasien`) REFERENCES `pasien` (`id_pasien`) ON DELETE CASCADE,
  ADD CONSTRAINT `appointment_ibfk_2` FOREIGN KEY (`id_dokter`) REFERENCES `dokter` (`id_dokter`) ON DELETE CASCADE,
  ADD CONSTRAINT `appointment_ibfk_3` FOREIGN KEY (`id_treatment`) REFERENCES `treatment` (`id_treatment`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `transaksi`
--
ALTER TABLE `transaksi`
  ADD CONSTRAINT `transaksi_ibfk_1` FOREIGN KEY (`id_pasien`) REFERENCES `pasien` (`id_pasien`) ON DELETE CASCADE,
  ADD CONSTRAINT `transaksi_ibfk_2` FOREIGN KEY (`id_dokter`) REFERENCES `dokter` (`id_dokter`) ON DELETE CASCADE,
  ADD CONSTRAINT `transaksi_ibfk_3` FOREIGN KEY (`id_treatment`) REFERENCES `treatment` (`id_treatment`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
