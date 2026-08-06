-- Roles
CREATE TABLE roles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) UNIQUE NOT NULL
);
INSERT INTO roles (name) VALUES ('Admin'), ('User'), ('Safety'), ('Project Manager');

-- Settings (Logo & Background)
CREATE TABLE settings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    logo VARCHAR(255) DEFAULT 'default-logo.png',
    background VARCHAR(255) DEFAULT 'default-bg.jpg',
    app_name VARCHAR(100) DEFAULT 'HSE Management System'
);

-- Users
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    role_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (role_id) REFERENCES roles(id) ON DELETE CASCADE
);

-- Master APD
CREATE TABLE apd (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    size VARCHAR(50),
    stock INT DEFAULT 0
);
INSERT INTO apd (name, size, stock) VALUES 
('Wearpack', 'L', 50),
('Wearpack', 'XL', 40),
('Safety Glass', 'Standard', 100),
('Safety Shoes', '42', 20),
('Inner Helm', 'Standard', 50),
('Helm', 'Standard', 30),
('LOTO', 'Kit', 10);

-- Pengajuan APD
CREATE TABLE pengajuan (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    request_date DATE NOT NULL,
    status ENUM('Pending Review Safety', 'Pending Approval Safety', 'Pending Review PM', 'Pending Approval PM', 'Approved', 'Rejected') DEFAULT 'Pending Review Safety',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Detail Pengajuan
CREATE TABLE detail_pengajuan (
    id INT AUTO_INCREMENT PRIMARY KEY,
    pengajuan_id INT NOT NULL,
    apd_id INT NOT NULL,
    quantity INT NOT NULL,
    FOREIGN KEY (pengajuan_id) REFERENCES pengajuan(id) ON DELETE CASCADE,
    FOREIGN KEY (apd_id) REFERENCES apd(id) ON DELETE CASCADE
);

-- Reviews & Approvals (Workflow Log)
CREATE TABLE workflow_log (
    id INT AUTO_INCREMENT PRIMARY KEY,
    pengajuan_id INT NOT NULL,
    user_id INT NOT NULL,
    role_action VARCHAR(50) NOT NULL,
    action ENUM('Review', 'Approve', 'Reject', 'Revise') NOT NULL,
    comment TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (pengajuan_id) REFERENCES pengajuan(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Audit Log
CREATE TABLE audit_log (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    action VARCHAR(255) NOT NULL,
    ip_address VARCHAR(45),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Notifications
CREATE TABLE notifications (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    message TEXT NOT NULL,
    is_read BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Default Admin Account (Password: admin123)
INSERT INTO settings (logo, background) VALUES ('logo.png', 'bg.jpg');
INSERT INTO users (name, email, password, role_id) VALUES ('Super Admin', 'admin@hse.com', '$2y$10$eO./lE.EQKfS7T2Q0T2Z2.2.2.2.2.2.2.2.2.2.2.2.2.2.2.2.2.2', 1);
-- Note: Hash above is dummy. Run PHP to generate real hash or use password_hash.