CREATE DATABASE IF NOT EXISTS web_perpus CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE web_perpus;

SET FOREIGN_KEY_CHECKS = 0;
DROP TABLE IF EXISTS pinjam_buku;
DROP TABLE IF EXISTS buku;
DROP TABLE IF EXISTS anggota;
DROP TABLE IF EXISTS petugas;
SET FOREIGN_KEY_CHECKS = 1;

-- Tabel anggota: atribut class diagram + kolom akun untuk login/registrasi.
CREATE TABLE anggota (
    id_anggota INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nama VARCHAR(120) NOT NULL,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(150) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    status ENUM('aktif','nonaktif') NOT NULL DEFAULT 'aktif',
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Tabel buku mengikuti class diagram. Satu baris merepresentasikan satu eksemplar.
CREATE TABLE buku (
    id_buku INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    judul_buku VARCHAR(180) NOT NULL,
    pengarang_buku VARCHAR(120) NOT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Tabel petugas menyimpan akun admin dan petugas; pembeda akses melalui kolom role.
CREATE TABLE petugas (
    id_petugas INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nama VARCHAR(120) NOT NULL,
    role ENUM('admin','petugas') NOT NULL DEFAULT 'petugas',
    username VARCHAR(50) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    status ENUM('aktif','nonaktif') NOT NULL DEFAULT 'aktif',
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Tabel pinjam_buku mengikuti class diagram + status dan tanggal aktual pengembalian.
-- tanggal_kembali berarti tanggal batas pengembalian sesuai diagram aktivitas.
CREATE TABLE pinjam_buku (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    tanggal_pinajaman DATE NOT NULL,
    id_buku INT UNSIGNED NOT NULL,
    tanggal_kembali DATE NOT NULL,
    id_anggota INT UNSIGNED NOT NULL,
    id_petugas INT UNSIGNED NULL,
    status ENUM('dipinjam','dikembalikan') NOT NULL DEFAULT 'dipinjam',
    tanggal_dikembalikan DATE NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_pinjam_buku FOREIGN KEY (id_buku) REFERENCES buku(id_buku)
        ON UPDATE CASCADE ON DELETE RESTRICT,
    CONSTRAINT fk_pinjam_anggota FOREIGN KEY (id_anggota) REFERENCES anggota(id_anggota)
        ON UPDATE CASCADE ON DELETE RESTRICT,
    CONSTRAINT fk_pinjam_petugas FOREIGN KEY (id_petugas) REFERENCES petugas(id_petugas)
        ON UPDATE CASCADE ON DELETE SET NULL,
    INDEX idx_pinjam_status (status),
    INDEX idx_pinjam_tanggal_kembali (tanggal_kembali),
    INDEX idx_pinjam_anggota (id_anggota)
) ENGINE=InnoDB;

-- Akun demo. Segera ganti password setelah login pertama.
-- Admin: username admin, password admin123
-- Petugas: username petugas, password petugas123
INSERT INTO petugas (nama, role, username, password_hash) VALUES
('Administrator', 'admin', 'admin', '$2y$12$07pyxWYPq0B2PU/JU3Q4AOwTzNVUKdvByKjln9IXjqAf3MYvr.ngu'),
('Petugas Perpustakaan', 'petugas', 'petugas', '$2y$12$OKQ5Ghw29g1pZJ59JpO/mueKi11h8Mty/8AbFftgW333VoqL9fCJ2');

-- Contoh data buku (boleh dihapus/diganti melalui halaman Kelola Buku).
INSERT INTO buku (judul_buku, pengarang_buku) VALUES
('Laskar Pelangi', 'Andrea Hirata'),
('Bumi', 'Tere Liye'),
('Filosofi Teras', 'Henry Manampiring'),
('Negeri 5 Menara', 'Ahmad Fuadi'),
('Atomic Habits', 'James Clear');
