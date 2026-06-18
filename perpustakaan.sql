-- ============================================
-- SISTEM PEMINJAMAN BUKU PERPUSTAKAAN
-- Dibuat oleh: Anggota 1 (Database Architect)
-- ============================================

CREATE DATABASE IF NOT EXISTS perpustakaan;
USE perpustakaan;

-- Tabel Petugas (untuk login)
CREATE TABLE IF NOT EXISTS petugas (
    id_petugas INT AUTO_INCREMENT PRIMARY KEY,
    nama VARCHAR(100) NOT NULL,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabel Buku
CREATE TABLE IF NOT EXISTS buku (
    id_buku INT AUTO_INCREMENT PRIMARY KEY,
    kode_buku VARCHAR(20) NOT NULL UNIQUE,
    judul VARCHAR(200) NOT NULL,
    pengarang VARCHAR(100) NOT NULL,
    penerbit VARCHAR(100),
    tahun_terbit YEAR,
    kategori VARCHAR(50),
    stok INT DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabel Anggota
CREATE TABLE IF NOT EXISTS anggota (
    id_anggota INT AUTO_INCREMENT PRIMARY KEY,
    kode_anggota VARCHAR(20) NOT NULL UNIQUE,
    nama VARCHAR(100) NOT NULL,
    alamat TEXT,
    no_hp VARCHAR(15),
    email VARCHAR(100),
    tgl_daftar DATE,
    status ENUM('aktif','nonaktif') DEFAULT 'aktif',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabel Peminjaman
CREATE TABLE IF NOT EXISTS peminjaman (
    id_pinjam INT AUTO_INCREMENT PRIMARY KEY,
    id_anggota INT NOT NULL,
    id_buku INT NOT NULL,
    tgl_pinjam DATE NOT NULL,
    tgl_kembali_rencana DATE NOT NULL,
    tgl_kembali_nyata DATE NULL,
    denda INT DEFAULT 0,
    status ENUM('dipinjam','dikembalikan','terlambat') DEFAULT 'dipinjam',
    id_petugas INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_anggota) REFERENCES anggota(id_anggota),
    FOREIGN KEY (id_buku) REFERENCES buku(id_buku),
    FOREIGN KEY (id_petugas) REFERENCES petugas(id_petugas)
);

-- ============================================
-- DATA SAMPLE
-- ============================================

-- Data Petugas (username: admin | password: admin123)
INSERT INTO petugas (nama, username, password) VALUES
('Administrator', 'admin', '$2y$10$TKh8H1.PfbuNz0HvWVnLeumSySpDa1HbmHUMYp9QJfbL0I9J0Jz8W');

-- Data Buku
INSERT INTO buku (kode_buku, judul, pengarang, penerbit, tahun_terbit, kategori, stok) VALUES
('BK001', 'Pemrograman Web dengan PHP', 'Ahmad Fauzi', 'Andi Publisher', 2022, 'Teknologi', 3),
('BK002', 'Algoritma dan Struktur Data', 'Rinaldi Munir', 'Informatika', 2021, 'Teknologi', 2),
('BK003', 'Database MySQL Lengkap', 'Budi Raharjo', 'Modula', 2023, 'Teknologi', 4),
('BK004', 'Kecerdasan Buatan', 'Sri Kusumadewi', 'Graha Ilmu', 2020, 'Teknologi', 1),
('BK005', 'Jaringan Komputer', 'Forouzan', 'McGraw-Hill', 2019, 'Teknologi', 2);

-- Data Anggota
INSERT INTO anggota (kode_anggota, nama, alamat, no_hp, email, tgl_daftar, status) VALUES
('AG001', 'Budi Santoso', 'Jl. Malioboro No. 10, Yogyakarta', '081234567890', 'budi@email.com', '2024-01-15', 'aktif'),
('AG002', 'Siti Rahayu', 'Jl. Kaliurang KM 5, Sleman', '082345678901', 'siti@email.com', '2024-02-20', 'aktif'),
('AG003', 'Eko Prasetyo', 'Jl. Parangtritis No. 45, Bantul', '083456789012', 'eko@email.com', '2024-03-10', 'aktif');

-- Data Peminjaman Contoh
INSERT INTO peminjaman (id_anggota, id_buku, tgl_pinjam, tgl_kembali_rencana, status) VALUES
(1, 1, '2025-06-01', '2025-06-08', 'dipinjam'),
(2, 3, '2025-06-02', '2025-06-09', 'dipinjam');
