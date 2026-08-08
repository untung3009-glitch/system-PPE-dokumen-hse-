<?php
require_once 'config/config.php';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PPE Mining System - Area Tambang</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <!-- Chart.js CDN untuk Dashboard Grafik -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>

    <!-- Header Navigation -->
    <nav class="navbar">
        <div class="brand">
            ⛏️ MINE-PPE SYSTEM <span class="brand-badge">HSE & LOGISTICS</span>
        </div>
        <div id="user-info" style="color: var(--accent-yellow); font-weight: bold;"></div>
        <a href="logout.php" class="btn btn-danger" style="padding: 0.4rem 0.8rem; text-decoration: none;">Logout</a>
    </nav>

    <!-- SECTION LOGIN -->
    <div id="login-section" class="container" style="max-width: 450px; margin-top: 5rem;">
        <div class="card">
            <h2 style="text-align: center; margin-bottom: 1.5rem; color: var(--accent-yellow);">LOGIN SYSTEM</h2>
            <form id="form-login">
                <div class="form-group">
                    <label>NIK Karyawan</label>
                    <input type="text" name="nik" placeholder="Contoh: EMP-001" required>
                </div>
                <div class="form-group">
                    <label>Password</label>
                    <input type="password" name="password" placeholder="Password Anda" required>
                </div>
                <button type="submit" class="btn btn-primary" style="width: 100%;">MASUK SISTEM</button>
            </form>
        </div>
    </div>

    <!-- SECTION MAIN APP DASHBOARD & FORM -->
    <div id="app-section" class="container" style="display: none;">
        
        <!-- Top Stats Summary -->
        <div class="stats-grid">
            <div class="card">
                <div class="card-title">Total Order PPE</div>
                <div class="card-value" id="stat-total">0</div>
            </div>
            <div class="card" style="border-left: 4px solid var(--success);">
                <div class="card-title">Disetujui (Approved)</div>
                <div class="card-value" style="color: var(--success);" id="stat-approved">0</div>
            </div>
            <div class="card" style="border-left: 4px solid var(--warning);">
                <div class="card-title">Proses Approval</div>
                <div class="card-value" style="color: var(--warning);" id="stat-pending">0</div>
            </div>
            <div class="card" style="border-left: 4px solid var(--danger);">
                <div class="card-title">Ditolak (Rejected)</div>
                <div class="card-value" style="color: var(--danger);" id="stat-rejected">0</div>
            </div>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 2fr; gap: 2rem;">
            
            <!-- Form Pengajuan PPE -->
            <div class="card">
                <h3 style="margin-bottom: 1rem; color: var(--accent-yellow);">Form Request PPE / APD</h3>
                <form id="form-request" enctype="multipart/form-data">
                    <div class="form-group">
                        <label>Pilih Jenis PPE</label>
                        <select id="select-ppe-item" name="ppe_item_id" required></select>
                    </div>
                    <div class="form-group">
                        <label>Jumlah (Quantity)</label>
                        <input type="number" name="jumlah" min="1" value="1" required>
                    </div>
                    <div class="form-group">
                        <label>Alasan Permintaan / Kerusakan</label>
                        <textarea name="alasan" rows="3" placeholder="Contoh: Helm pecah akibat benturan, rompi lusuh..." required></textarea>
                    </div>
                    <div class="form-group">
                        <label>Upload Foto APD Lama / Rusak (Opsional)</label>
                        <input type="file" name="foto_bukti" accept="image/*">
                    </div>
                    <button type="submit" class="btn btn-primary" style="width: 100%;">KIRIM PENGAJUAN</button>
                </form>
            </div>

            <!-- Tabel Monitoring & Approval -->
            <div class="card">
                <h3 style="margin-bottom: 1rem; color: var(--accent-yellow);">Riwayat & Status Approval</h3>
                <div class="table-responsive">
                    <table>
                        <thead>
                            <tr>
                                <th>No REQ</th>
                                <th>Pemohon</th>
                                <th>Item PPE</th>
                                <th>Qty</th>
                                <th>Area</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="table-requests-body">
                            <!-- Data dimuat dinamis via JavaScript -->
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>

    <script src="assets/js/app.js"></script>
</body>
</html>