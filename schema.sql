-- =====================================================================
-- DATABASE SCHEMA FOR APLIKASI PERANGKAT PEMBELAJARAN SMK
-- =====================================================================
-- Deskripsi: Skema database MySQL lengkap dengan relasi untuk sistem
--            manajemen pembelajaran (LMS) tingkat SMK.
-- DBMS: MySQL / MariaDB (InnoDB Engine)
-- =====================================================================

CREATE DATABASE IF NOT EXISTS `perangkat_pembelajaran` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `perangkat_pembelajaran`;

-- 1. Tabel Tahun Pelajaran
CREATE TABLE IF NOT EXISTS `tahun_pelajaran` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `tahun` VARCHAR(9) NOT NULL, -- Contoh: "2025/2026"
  `semester` ENUM('Ganjil', 'Genap') NOT NULL,
  `status` ENUM('Aktif', 'Tidak Aktif') DEFAULT 'Tidak Aktif',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- 2. Tabel Users (Entitas Otentikasi Utama)
CREATE TABLE IF NOT EXISTS `users` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `username` VARCHAR(50) NOT NULL UNIQUE,
  `password` VARCHAR(255) NOT NULL,
  `nama_lengkap` VARCHAR(100) NOT NULL,
  `email` VARCHAR(100) DEFAULT NULL UNIQUE,
  `role` ENUM('Admin', 'Guru', 'Siswa') NOT NULL,
  `foto` VARCHAR(255) DEFAULT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` DATETIME DEFAULT NULL -- Soft Delete support
) ENGINE=InnoDB;

-- 3. Tabel Guru (Informasi Profil Guru)
CREATE TABLE IF NOT EXISTS `gurus` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `user_id` INT NOT NULL,
  `nip` VARCHAR(20) DEFAULT NULL UNIQUE,
  `jenis_kelamin` ENUM('L', 'P') NOT NULL,
  `tempat_lahir` VARCHAR(50) DEFAULT NULL,
  `tanggal_lahir` DATE DEFAULT NULL,
  `alamat` TEXT DEFAULT NULL,
  `no_telp` VARCHAR(15) DEFAULT NULL,
  CONSTRAINT `fk_guru_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB;

-- 4. Tabel Siswa (Informasi Profil Siswa)
CREATE TABLE IF NOT EXISTS `siswas` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `user_id` INT NOT NULL,
  `nis` VARCHAR(20) NOT NULL UNIQUE,
  `nisn` VARCHAR(10) DEFAULT NULL UNIQUE,
  `jenis_kelamin` ENUM('L', 'P') NOT NULL,
  `tempat_lahir` VARCHAR(50) DEFAULT NULL,
  `tanggal_lahir` DATE DEFAULT NULL,
  `alamat` TEXT DEFAULT NULL,
  `no_telp` VARCHAR(15) DEFAULT NULL,
  CONSTRAINT `fk_siswa_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB;

-- 5. Tabel Kelas
CREATE TABLE IF NOT EXISTS `kelas` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `nama_kelas` VARCHAR(50) NOT NULL, -- Contoh: "X RPL 1", "XI TKJ 2"
  `jurusan` VARCHAR(100) NOT NULL,    -- Contoh: "Rekayasa Perangkat Lunak"
  `wali_kelas_id` INT DEFAULT NULL,
  CONSTRAINT `fk_kelas_wali` FOREIGN KEY (`wali_kelas_id`) REFERENCES `gurus` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB;

-- 6. Tabel Anggota Kelas (Relasi Siswa ke Kelas per Tahun Pelajaran)
CREATE TABLE IF NOT EXISTS `anggota_kelas` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `kelas_id` INT NOT NULL,
  `siswa_id` INT NOT NULL,
  `tahun_pelajaran_id` INT NOT NULL,
  UNIQUE KEY `unique_siswa_tahun` (`siswa_id`, `tahun_pelajaran_id`),
  CONSTRAINT `fk_anggota_kelas` FOREIGN KEY (`kelas_id`) REFERENCES `kelas` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_anggota_siswa` FOREIGN KEY (`siswa_id`) REFERENCES `siswas` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_anggota_tahun` FOREIGN KEY (`tahun_pelajaran_id`) REFERENCES `tahun_pelajaran` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB;

-- 7. Tabel Mata Pelajaran
CREATE TABLE IF NOT EXISTS `mata_pelajaran` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `kode_mapel` VARCHAR(20) NOT NULL UNIQUE, -- Contoh: "MP001", "C3-RPL-02"
  `nama_mapel` VARCHAR(100) NOT NULL,
  `kelompok` ENUM('Nasional', 'Kewilayahan', 'Kejuruan') NOT NULL
) ENGINE=InnoDB;

-- 8. Tabel Jadwal Pelajaran
CREATE TABLE IF NOT EXISTS `jadwal` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `kelas_id` INT NOT NULL,
  `guru_id` INT NOT NULL,
  `mapel_id` INT NOT NULL,
  `tahun_pelajaran_id` INT NOT NULL,
  `hari` ENUM('Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu') NOT NULL,
  `jam_mulai` TIME NOT NULL,
  `jam_selesai` TIME NOT NULL,
  CONSTRAINT `fk_jadwal_kelas` FOREIGN KEY (`kelas_id`) REFERENCES `kelas` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_jadwal_guru` FOREIGN KEY (`guru_id`) REFERENCES `gurus` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_jadwal_mapel` FOREIGN KEY (`mapel_id`) REFERENCES `mata_pelajaran` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_jadwal_tahun` FOREIGN KEY (`tahun_pelajaran_id`) REFERENCES `tahun_pelajaran` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB;

-- 9. Tabel Materi Pembelajaran (Bank Materi oleh Guru)
CREATE TABLE IF NOT EXISTS `materi` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `guru_id` INT NOT NULL,
  `mapel_id` INT NOT NULL,
  `judul` VARCHAR(255) NOT NULL,
  `deskripsi` TEXT DEFAULT NULL,
  `file_path` VARCHAR(255) DEFAULT NULL, -- Menyimpan path file PDF, slide, atau e-book
  `link_external` VARCHAR(255) DEFAULT NULL, -- Menyimpan link YouTube, Google Drive, dsb.
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT `fk_materi_guru` FOREIGN KEY (`guru_id`) REFERENCES `gurus` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_materi_mapel` FOREIGN KEY (`mapel_id`) REFERENCES `mata_pelajaran` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB;

-- 10. Tabel Distribusi Materi ke Kelas (Relasi Materi - Kelas - Tahun Pelajaran)
CREATE TABLE IF NOT EXISTS `materi_kelas` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `materi_id` INT NOT NULL,
  `kelas_id` INT NOT NULL,
  `tahun_pelajaran_id` INT NOT NULL,
  UNIQUE KEY `unique_materi_kelas_tahun` (`materi_id`, `kelas_id`, `tahun_pelajaran_id`),
  CONSTRAINT `fk_dist_materi` FOREIGN KEY (`materi_id`) REFERENCES `materi` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_dist_kelas` FOREIGN KEY (`kelas_id`) REFERENCES `kelas` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_dist_tahun` FOREIGN KEY (`tahun_pelajaran_id`) REFERENCES `tahun_pelajaran` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB;

-- 11. Tabel Presensi (Sesi Absensi Kelas)
CREATE TABLE IF NOT EXISTS `presensi` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `jadwal_id` INT NOT NULL,
  `tanggal` DATE NOT NULL,
  `pertemuan_ke` INT NOT NULL,
  `status_pertemuan` ENUM('Terlaksana', 'Batal') DEFAULT 'Terlaksana',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT `fk_presensi_jadwal` FOREIGN KEY (`jadwal_id`) REFERENCES `jadwal` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB;

-- 12. Tabel Presensi Detail (Status Absen Siswa per Sesi)
CREATE TABLE IF NOT EXISTS `presensi_detail` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `presensi_id` INT NOT NULL,
  `siswa_id` INT NOT NULL,
  `status` ENUM('Hadir', 'Sakit', 'Izin', 'Alfa') NOT NULL DEFAULT 'Hadir',
  `keterangan` VARCHAR(255) DEFAULT NULL, -- Contoh: "Sakit demam", "Izin nikahan kakak"
  UNIQUE KEY `unique_presensi_siswa` (`presensi_id`, `siswa_id`),
  CONSTRAINT `fk_detail_presensi` FOREIGN KEY (`presensi_id`) REFERENCES `presensi` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_detail_siswa` FOREIGN KEY (`siswa_id`) REFERENCES `siswas` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB;

-- 13. Tabel Ulangan (Quiz / Ujian)
CREATE TABLE IF NOT EXISTS `ulangan` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `guru_id` INT NOT NULL,
  `mapel_id` INT NOT NULL,
  `judul` VARCHAR(255) NOT NULL,
  `deskripsi` TEXT DEFAULT NULL,
  `tipe` ENUM('UH', 'UTS', 'UAS', 'Kuis') NOT NULL DEFAULT 'UH',
  `waktu_mulai` DATETIME DEFAULT NULL,   -- Waktu akses dibuka
  `waktu_selesai` DATETIME DEFAULT NULL, -- Waktu akses ditutup
  `durasi` INT NOT NULL,                 -- Durasi pengerjaan dalam satuan Menit
  `kkm` INT DEFAULT 75,                  -- Kriteria Ketuntasan Minimal
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT `fk_ulangan_guru` FOREIGN KEY (`guru_id`) REFERENCES `gurus` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_ulangan_mapel` FOREIGN KEY (`mapel_id`) REFERENCES `mata_pelajaran` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB;

-- 14. Tabel Distribusi Ulangan ke Kelas
CREATE TABLE IF NOT EXISTS `ulangan_kelas` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `ulangan_id` INT NOT NULL,
  `kelas_id` INT NOT NULL,
  `tahun_pelajaran_id` INT NOT NULL,
  UNIQUE KEY `unique_ulangan_kelas_tahun` (`ulangan_id`, `kelas_id`, `tahun_pelajaran_id`),
  CONSTRAINT `fk_dist_ulangan` FOREIGN KEY (`ulangan_id`) REFERENCES `ulangan` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_dist_ulangan_kelas` FOREIGN KEY (`kelas_id`) REFERENCES `kelas` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_dist_ulangan_tahun` FOREIGN KEY (`tahun_pelajaran_id`) REFERENCES `tahun_pelajaran` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB;

-- 15. Tabel Soal Ulangan
CREATE TABLE IF NOT EXISTS `soal` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `ulangan_id` INT NOT NULL,
  `pertanyaan` TEXT NOT NULL,
  `tipe_soal` ENUM('Pilihan Ganda', 'Essay') NOT NULL DEFAULT 'Pilihan Ganda',
  `opsi_a` TEXT DEFAULT NULL,
  `opsi_b` TEXT DEFAULT NULL,
  `opsi_c` TEXT DEFAULT NULL,
  `opsi_d` TEXT DEFAULT NULL,
  `opsi_e` TEXT DEFAULT NULL,
  `kunci_jawaban` TEXT NOT NULL, -- Jika PG diisi 'A'/'B'/'C'/'D'/'E', jika Essay diisi kata kunci/pedoman nilai
  `bobot` INT DEFAULT 1,         -- Bobot nilai soal
  CONSTRAINT `fk_soal_ulangan` FOREIGN KEY (`ulangan_id`) REFERENCES `ulangan` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB;

-- 16. Tabel Jawaban Siswa (Rekap Ujian / Pengerjaan Kuis)
CREATE TABLE IF NOT EXISTS `jawaban_siswa` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `ulangan_id` INT NOT NULL,
  `siswa_id` INT NOT NULL,
  `nilai_akhir` DECIMAL(5,2) DEFAULT 0.00,
  `status` ENUM('Mengerjakan', 'Selesai') DEFAULT 'Mengerjakan',
  `waktu_mulai` DATETIME NOT NULL,
  `waktu_selesai` DATETIME DEFAULT NULL,
  UNIQUE KEY `unique_attempt` (`ulangan_id`, `siswa_id`),
  CONSTRAINT `fk_jawaban_ulangan` FOREIGN KEY (`ulangan_id`) REFERENCES `ulangan` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_jawaban_siswa` FOREIGN KEY (`siswa_id`) REFERENCES `siswas` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB;

-- 17. Tabel Detail Jawaban Siswa (Per Soal)
CREATE TABLE IF NOT EXISTS `jawaban_siswa_detail` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `jawaban_siswa_id` INT NOT NULL,
  `soal_id` INT NOT NULL,
  `jawaban` TEXT NOT NULL,
  `is_benar` TINYINT(1) DEFAULT 0, -- 1 = Benar, 0 = Salah (otomatisasi untuk PG)
  `skor` INT DEFAULT 0,            -- Skor riil yang didapatkan
  CONSTRAINT `fk_detail_jawaban` FOREIGN KEY (`jawaban_siswa_id`) REFERENCES `jawaban_siswa` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_detail_soal` FOREIGN KEY (`soal_id`) REFERENCES `soal` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB;

-- 18. Tabel Nilai (Buku Nilai Guru / Rekapitulasi)
CREATE TABLE IF NOT EXISTS `nilai` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `siswa_id` INT NOT NULL,
  `mapel_id` INT NOT NULL,
  `kelas_id` INT NOT NULL,
  `tahun_pelajaran_id` INT NOT NULL,
  `jenis_nilai` ENUM('Harian', 'Tugas', 'UTS', 'UAS', 'Sikap', 'Rapor') NOT NULL,
  `nilai` DECIMAL(5,2) NOT NULL,
  `keterangan` VARCHAR(255) DEFAULT NULL,
  `input_oleh` INT NOT NULL, -- ID User (Guru / Admin) yang menginputkan
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT `fk_nilai_siswa` FOREIGN KEY (`siswa_id`) REFERENCES `siswas` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_nilai_mapel` FOREIGN KEY (`mapel_id`) REFERENCES `mata_pelajaran` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_nilai_kelas` FOREIGN KEY (`kelas_id`) REFERENCES `kelas` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_nilai_tahun` FOREIGN KEY (`tahun_pelajaran_id`) REFERENCES `tahun_pelajaran` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_nilai_input` FOREIGN KEY (`input_oleh`) REFERENCES `users` (`id`) ON DELETE NO ACTION ON UPDATE CASCADE
) ENGINE=InnoDB;
