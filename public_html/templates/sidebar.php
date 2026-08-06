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
                <li><a href="review_approval.php"><i class="bi bi-shield-check me-2"></i> Review Safety</a></li>
                <?php endif; ?>

                <?php if(in_array($_SESSION['role_name'], ['Project Manager', 'Admin'])): ?>
                <li><a href="review_approval.php"><i class="bi bi-clipboard-check me-2"></i> Review PM</a></li>
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
                    <span class="navbar-brand ms-3 fw-bold text-primary d-none d-md-block">HSE Management System</span>
                    
                    <div class="ms-auto d-flex align-items-center">
                        <!-- Notifikasi Dropdown -->
                        <div class="dropdown me-3">
                            <a href="#" class="btn btn-light position-relative" id="notifDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="bi bi-bell-fill text-primary fs-5"></i>
                                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" id="notifCount" style="display: none;">0</span>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end p-0" style="width: 300px; max-height: 400px; overflow-y: auto;">
                                <li class="dropdown-header bg-primary text-white rounded-top"><strong>Notifikasi</strong></li>
                                <div id="notifList">
                                    <li class="dropdown-item text-center text-muted py-3">Loading...</li>
                                </div>
                            </ul>
                        </div>
                        
                        <span class="me-3 text-muted d-none d-md-block">Halo, <strong><?= e($_SESSION['name']) ?></strong></span>
                    </div>
                </div>
            </nav>
            <div class="container-fluid mt-4">