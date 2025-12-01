CREATE TABLE users (
    id INT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    nama VARCHAR(100),
    username VARCHAR(50) UNIQUE NOT NULL,
    password_hash VARCHAR(255),
    role ENUM('agent','supervisor','admin'),
    is_active TINYINT(1) NOT NULL DEFAULT 1
) ENGINE=INNODB;

CREATE TABLE kategori_keluhan (
    id INT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    nama_kategori VARCHAR(100),
    deskripsi TEXT
) ENGINE=INNODB;

CREATE TABLE pelanggan (
    id INT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    nama_pelanggan VARCHAR(100),
    no_hp VARCHAR(20),
    email VARCHAR(100),
    kota VARCHAR(100)
) ENGINE=INNODB;

CREATE TABLE keluhan (
    id INT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    kode_keluhan VARCHAR(50) UNIQUE,
    pelanggan_id INT UNSIGNED,
    kategori_id INT UNSIGNED,
    channel ENUM('Call Center', 'Grapari', 'WhatsApp', 'Aplikasi', 'Live Chat', 'Media Sosial', 'Email', 'Lainnya'),
    deskripsi_keluhan TEXT,
    status_keluhan ENUM('Open', 'On Progress', 'Pending', 'Solved', 'Closed'),
    prioritas ENUM('Low', 'Medium', 'High', 'Critical'),
    tanggal_lapor DATETIME,
    tanggal_update_terakhir DATETIME,
    tanggal_selesai DATETIME,
    created_by INT UNSIGNED,
    updated_by INT UNSIGNED,
    FOREIGN KEY (pelanggan_id) REFERENCES pelanggan(id)
        ON DELETE RESTRICT ON UPDATE CASCADE,
    FOREIGN KEY (kategori_id) REFERENCES kategori_keluhan(id)
        ON DELETE RESTRICT ON UPDATE CASCADE,
    FOREIGN KEY (created_by) REFERENCES users(id)
        ON DELETE RESTRICT ON UPDATE CASCADE,
    FOREIGN KEY (updated_by) REFERENCES users(id)
        ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=INNODB;

CREATE TABLE keluhan_log (
    id INT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    keluhan_id INT UNSIGNED,
    status_log ENUM('Open', 'On Progress', 'Pending', 'Solved', 'Closed'),
    catatan TEXT,
    tanggal_log DATETIME,
    user_id INT UNSIGNED,
    FOREIGN KEY (keluhan_id) REFERENCES keluhan(id)
        ON DELETE RESTRICT ON UPDATE RESTRICT,
    FOREIGN KEY (user_id) REFERENCES users(id)
        ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=INNODB;

-- Perubahan

-- Tambah kolom must_change_password pada tabel users
ALTER TABLE users
  ADD COLUMN must_change_password TINYINT(1) NOT NULL DEFAULT 0
  AFTER is_active;

-- (Opsional) tabel password_resets untuk audit reset
CREATE TABLE password_resets (
    id INT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    user_id INT UNSIGNED NOT NULL,
    reset_by INT UNSIGNED NOT NULL,
    temp_password_hash VARCHAR(255) NOT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    used_at DATETIME NULL,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (reset_by) REFERENCES users(id) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
