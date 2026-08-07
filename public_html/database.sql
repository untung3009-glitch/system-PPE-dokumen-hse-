-- Database: u684817258_safetymineppe

CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama VARCHAR(100) NOT NULL,
    nik VARCHAR(20) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('Admin', 'Supervisor', 'Safety', 'Warehouse', 'Karyawan') NOT NULL,
    departemen VARCHAR(50),
    jabatan VARCHAR(50)
);

CREATE TABLE IF NOT EXISTS ppe_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama_ppe VARCHAR(100) NOT NULL,
    stok INT NOT NULL DEFAULT 0
);

CREATE TABLE IF NOT EXISTS ppe_requests (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    nama VARCHAR(100) NOT NULL,
    nik VARCHAR(20) NOT NULL,
    departemen VARCHAR(50) NOT NULL,
    jabatan VARCHAR(50) NOT NULL,
    area_kerja VARCHAR(100) NOT NULL,
    jenis_ppe VARCHAR(100) NOT NULL,
    jumlah INT NOT NULL,
    alasan TEXT NOT NULL,
    foto VARCHAR(255) DEFAULT NULL,
    status ENUM('Pending Supervisor', 'Pending Safety', 'Pending Warehouse', 'Approved', 'Rejected') DEFAULT 'Pending Supervisor',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS audit_trail (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    aksi TEXT NOT NULL,
    waktu TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Data User Awal (Password: admin123)
INSERT INTO users (nama, nik, password, role, departemen, jabatan) VALUES
('Admin Utama', '1001', '$2y$10$eO./lA.B7rL0F6T7.915y.WpD9qHwE0Z7C2Qz8/M8Y8G6Z1qK2yS.', 'Admin', 'IT', 'Administrator'),
('Supervisor Tambang', '1002', '$2y$10$eO./lA.B7rL0F6T7.915y.WpD9qHwE0Z7C2Qz8/M8Y8G6Z1qK2yS.', 'Supervisor', 'Produksi', 'Supervisor'),
('Safety Officer', '1003', '$2y$10$eO./lA.B7rL0F6T7.915y.WpD9qHwE0Z7C2Qz8/M8Y8G6Z1qK2yS.', 'Safety', 'HSE', 'Safety Officer'),
('Warehouse Staff', '1004', '$2y$10$eO./lA.B7rL0F6T7.915y.WpD9qHwE0Z7C2Qz8/M8Y8G6Z1qK2yS.', 'Warehouse', 'Logistik', 'Warehouse Staff'),
('Karyawan Tambang', '1005', '$2y$10$eO./lA.B7rL0F6T7.915y.WpD9qHwE0Z7C2Qz8/M8Y8G6Z1qK2yS.', 'Karyawan', 'Produksi', 'Operator');

-- Data PPE Awal
INSERT INTO ppe_items (nama_ppe, stok) VALUES
('Helm Safety', 100),
('Safety Shoes', 50),
('Safety Glasses', 200),
('Ear Plug', 500),
('Respirator/Masker', 300);

-- Catatan: Password hash di atas adalah 'admin123'. 
-- Jika hash tidak terbaca, buat user lewat phpMyAdmin atau ubah query di atas.