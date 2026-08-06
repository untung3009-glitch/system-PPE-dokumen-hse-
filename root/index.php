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
    <title>LMI HSE System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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
                <h4>LMI HSE System</h4>
            </div>
            <div class="card-body p-4">
                <form id="loginForm">
                    <div class="mb-3">
                        <label>Username</label>
                        <input type="text" class="form-control" id="username" required>
                    </div>
                    <div class="mb-3">
                        <label>Password</label>
                        <input type="password" class="form-control" id="password" required>
                    </div>
                    <button type="submit" class="btn btn-lmg w-100">LOGIN</button>
                </form>
            </div>
        </div>
    </div>
    <?php else: ?>
    <!-- MAIN APP VIEW -->
    <div id="appView" class="wrapper">
        <nav class="sidebar">
            <div class="sidebar-header"><h4>LMI HSE</h4></div>
            <div class="sidebar-menu">
                <a class="active" onclick="showPage('dashboard')"><i class="bi bi-speedometer2"></i> Dashboard</a>
                <a onclick="showPage('ppe_form')"><i class="bi bi-file-earmark-plus"></i> Pengajuan APD</a>
                <a onclick="showPage('ppe_approval')"><i class="bi bi-check2-square"></i> Approval</a>
                <?php if ($role === 'Administrator'): ?>
                <a onclick="showPage('stock')"><i class="bi bi-boxes"></i> Stock APD</a>
                <?php endif; ?>
                <a href="api/logout.php" class="text-danger"><i class="bi bi-box-arrow-right"></i> Logout</a>
            </div>
        </nav>

        <div class="content">
            <div class="navbar-top">
                <h5 id="pageTitle">Dashboard</h5>
                <div class="dropdown">
                    <button class="btn btn-light dropdown-toggle" data-bs-toggle="dropdown">
                        <i class="bi bi-person-circle"></i> <?= htmlspecialchars($name) ?> (<?= $role ?>)
                    </button>
                </div>
            </div>

            <div class="p-4">
                <div id="page-dashboard" class="page-content">
                    <div class="row g-4">
                        <div class="col-md-4"><div class="card card-stat"><div class="card-body"><h6>Total Pengajuan</h6><h3 id="dashTotal">0</h3></div></div></div>
                        <div class="col-md-4"><div class="card card-stat"><div class="card-body"><h6>Pending Approval</h6><h3 id="dashPending">0</h3></div></div></div>
                        <div class="col-md-4"><div class="card card-stat"><div class="card-body"><h6>Stock APD</h6><h3 id="dashStock">0</h3></div></div></div>
                    </div>
                </div>

                <div id="page-ppe_form" class="page-content" style="display:none;">
                    <form id="ppeForm" enctype="multipart/form-data">
                        <h5>Data Pemohon</h5>
                        <input type="text" class="form-control mb-2" id="reqName" placeholder="Nama" required>
                        <input type="text" class="form-control mb-2" id="apdName" placeholder="Jenis APD" required>
                        <input type="number" class="form-control mb-2" id="qty" placeholder="Jumlah" required>
                        <input type="file" class="form-control mb-2" id="photoApd" required>
                        <button type="submit" class="btn btn-lmg">Submit</button>
                    </form>
                </div>

                <div id="page-ppe_approval" class="page-content" style="display:none;">
                    <div id="approvalQueue"></div>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/app.js"></script>
</body>
</html>