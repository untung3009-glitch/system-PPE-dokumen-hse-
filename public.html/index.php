<?php
session_start();
 $isLoggedIn = isset($_SESSION['user_id']);
 $role = $_SESSION['role'] ?? '';
 $name = $_SESSION['name'] ?? '';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LMI PPE & Document Management System</title>
    <link rel="icon" href="https://z-cdn-media.chatglm.cn/files/342ceaed-a660-458e-b047-e67a5cbb28e5.png?auth_key=1885598233-b68f40e8beec4c8583a02cb62d86b7d5-0-a1c6bcf6e07beb253537467835f04c6f" type="image/png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- Memanggil CSS dari folder assets/css/ -->
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

    <!-- Loading Spinner Global -->
    <div id="globalSpinner" class="spinner-overlay">
        <div class="spinner-border text-warning" style="width: 3rem; height: 3rem;" role="status"></div>
    </div>

    <?php if (!$isLoggedIn): ?>
    <!-- LOGIN VIEW -->
    <div id="loginView" class="login-wrapper">
        <div class="login-card">
            <div class="login-logo">
                <img src="https://z-cdn-media.chatglm.cn/files/342ceaed-a660-458e-b047-e67a5cbb28e5.png?auth_key=1885598233-b68f40e8beec4c8583a02cb62d86b7d5-0-a1c6bcf6e07beb253537467835f04c6f" alt="LiuGong Logo">
                <h6 class="mt-2 text-secondary mb-0">PPE & Document Management System</h6>
            </div>
            <div class="card-body p-4">
                <form id="loginForm" onsubmit="doLogin(event)">
                    <div class="mb-3">
                        <label class="form-label">Email / Username</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light"><i class="bi bi-person"></i></span>
                            <input type="text" class="form-control" id="username" required placeholder="Masukkan username">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Password</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light"><i class="bi bi-lock"></i></span>
                            <input type="password" class="form-control" id="password" required placeholder="Masukkan password">
                        </div>
                    </div>
                    <button type="submit" class="btn btn-lmg w-100">LOGIN</button>
                </form>
            </div>
        </div>
    </div>
    <?php else: ?>
    <!-- MAIN APP VIEW -->
    <div id="appView" class="wrapper">
        <div class="sidebar-overlay" id="sidebarOverlay" onclick="toggleSidebar()"></div>
        <nav class="sidebar" id="sidebar">
            <div class="sidebar-header">
                <img src="https://z-cdn-media.chatglm.cn/files/342ceaed-a660-458e-b047-e67a5cbb28e5.png?auth_key=1885598233-b68f40e8beec4c8583a02cb62d86b7d5-0-a1c6bcf6e07beb253537467835f04c6f" alt="LiuGong Logo">
            </div>
            <div class="sidebar-menu">
                <a class="active" onclick="showPage('dashboard', this)"><i class="bi bi-speedometer2"></i> Dashboard</a>
                <a onclick="showPage('ppe_form', this)"><i class="bi bi-file-earmark-plus"></i> Pengajuan APD</a>
                <a onclick="showPage('ppe_approval', this)"><i class="bi bi-check2-square"></i> Approval APD</a>
                <a onclick="showPage('ppe_documentation', this)"><i class="bi bi-clipboard-data"></i> Dokumentasi APD</a>
                <?php if ($role === 'Administrator'): ?>
                <a onclick="showPage('master_ppe', this)"><i class="bi bi-gear-fill"></i> Master PPE</a>
                <?php endif; ?>
                <a href="api/logout.php" class="text-danger"><i class="bi bi-box-arrow-right"></i> Logout</a>
            </div>
        </nav>

        <div class="content" id="content">
            <div class="navbar-top">
                <button class="btn btn-light border" onclick="toggleSidebar()"><i class="bi bi-list"></i></button>
                <div class="d-flex align-items-center">
                    <span class="text-muted me-3" id="sessionTimer" style="font-size: 0.8rem;"></span>
                    <div class="dropdown">
                        <button class="btn btn-light border dropdown-toggle" data-bs-toggle="dropdown">
                            <i class="bi bi-person-circle text-lmg"></i> <span class="fw-bold"><?= htmlspecialchars($name) ?></span>
                        </button>
                    </div>
                </div>
            </div>

            <div class="p-4">
                <div id="page-dashboard" class="page-content">
                    <h2 class="mb-4 text-lmg fw-bold">Dashboard</h2>
                    <div class="row g-4">
                        <div class="col-md-4"><div class="card card-stat shadow-sm h-100"><div class="card-body"><h6>Total Pengajuan APD</h6><h3 id="dashTotal">0</h3></div></div></div>
                        <div class="col-md-4"><div class="card card-stat shadow-sm h-100"><div class="card-body"><h6>Pending Approval</h6><h3 id="dashPending">0</h3></div></div></div>
                        <div class="col-md-4"><div class="card card-stat shadow-sm h-100"><div class="card-body"><h6>Stock APD</h6><h3 id="dashStock">0</h3></div></div></div>
                    </div>
                </div>

                <div id="page-ppe_form" class="page-content" style="display:none;">
                    <h2 class="mb-4 text-lmg fw-bold">Form Pengajuan APD</h2>
                    <div class="card shadow-sm"><div class="card-body p-4">
                        <form id="ppeForm">
                            <input type="text" class="form-control mb-2" id="reqName" placeholder="Nama Pemohon" required>
                            <input type="text" class="form-control mb-2" id="apdName" placeholder="Jenis APD" required>
                            <input type="number" class="form-control mb-2" id="qty" placeholder="Jumlah" required>
                            <button type="submit" class="btn btn-lmg">Submit</button>
                        </form>
                    </div></div>
                </div>

                <div id="page-ppe_approval" class="page-content" style="display:none;">
                    <h2 class="mb-4 text-lmg fw-bold">Approval APD</h2>
                    <div id="approvalQueue"></div>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Memanggil JS dari folder assets/js/ -->
    <script src="assets/js/app.js"></script>
</body>
</html>