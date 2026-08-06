        <!-- Sidebar -->
        <nav id="sidebar" class="bg-primary text-white">
            <div class="sidebar-header p-3 text-center">
                <img src="uploads/setting/<?= e($settings['logo']) ?>" alt="Logo" height="40" class="mb-2">
                <h5>HSE System</h5>
            </div>
            <ul class="list-unstyled components ps-2">
                <li><a href="index.php"><i class="bi bi-speedometer2 me-2"></i> Dashboard</a></li>
                <li><a href="pengajuan.php"><i class="bi bi-file-earmark-plus me-2"></i> Pengajuan APD</a></li>
                
                <?php if(in_array($_SESSION['role_name'], ['Safety', 'Admin'])): ?>
                <li><a href="review_safety.php"><i class="bi bi-shield-check me-2"></i> Review Safety</a></li>
                <li><a href="approval_safety.php"><i class="bi bi-check2-circle me-2"></i> Approval Safety</a></li>
                <?php endif; ?>

                <?php if(in_array($_SESSION['role_name'], ['Project Manager', 'Admin'])): ?>
                <li><a href="review_pm.php"><i class="bi bi-clipboard-check me-2"></i> Review PM</a></li>
                <li><a href="approval_pm.php"><i class="bi bi-check2-all me-2"></i> Approval PM</a></li>
                <?php endif; ?>

                <li><a href="stock_apd.php"><i class="bi bi-box-seam me-2"></i> Stock APD</a></li>
                <li><a href="master_apd.php"><i class="bi bi-database me-2"></i> Master APD</a></li>
                
                <?php if($_SESSION['role_name'] === 'Admin'): ?>
                <li><a href="master_user.php"><i class="bi bi-people me-2"></i> Master User</a></li>
                <li><a href="pengaturan.php"><i class="bi bi-gear me-2"></i> Pengaturan Sistem</a></li>
                <?php endif; ?>

                <li><a href="laporan.php"><i class="bi bi-graph-up me-2"></i> Laporan</a></li>
                <li><a href="audit_log.php"><i class="bi bi-clock-history me-2"></i> Audit Log</a></li>
                <li><a href="logout.php"><i class="bi bi-box-arrow-right me-2"></i> Logout</a></li>
            </ul>
        </nav>

        <!-- Page Content -->
        <div id="content">
            <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm">
                <div class="container-fluid">
                    <button class="btn btn-primary d-md-inline" id="sidebarToggle"><i class="bi bi-list"></i></button>
                    <span class="navbar-brand ms-3 fw-bold text-primary">HSE Management System</span>
                    <div class="ms-auto">
                        <span class="me-3 text-muted">Halo, <strong><?= e($_SESSION['name']) ?></strong></span>
                    </div>
                </div>
            </nav>
            <div class="container-fluid mt-4">