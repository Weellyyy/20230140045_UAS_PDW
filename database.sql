CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nama` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('mahasiswa','asisten') NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `mata_praktikum` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nama_praktikum` varchar(255) NOT NULL,
  `deskripsi` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `modul` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_praktikum` int(11) NOT NULL,
  `nama_modul` varchar(255) NOT NULL,
  `deskripsi` text DEFAULT NULL,
  `file_materi` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  FOREIGN KEY (`id_praktikum`) REFERENCES `mata_praktikum`(`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `pendaftaran_praktikum` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_mahasiswa` int(11) NOT NULL,
  `id_praktikum` int(11) NOT NULL,
  `tanggal_daftar` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `pendaftaran_unik` (`id_mahasiswa`, `id_praktikum`),
  FOREIGN KEY (`id_mahasiswa`) REFERENCES `users`(`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  FOREIGN KEY (`id_praktikum`) REFERENCES `mata_praktikum`(`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `laporan` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_modul` int(11) NOT NULL,
  `id_mahasiswa` int(11) NOT NULL,
  `file_laporan` varchar(255) NOT NULL,
  `tanggal_kumpul` timestamp NOT NULL DEFAULT current_timestamp(),
  `nilai` int(3) DEFAULT NULL,
  `feedback` text DEFAULT NULL,
  `status` enum('dikumpulkan','dinilai') NOT NULL DEFAULT 'dikumpulkan',
  PRIMARY KEY (`id`),
  FOREIGN KEY (`id_modul`) REFERENCES `modul`(`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  FOREIGN KEY (`id_mahasiswa`) REFERENCES `users`(`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

