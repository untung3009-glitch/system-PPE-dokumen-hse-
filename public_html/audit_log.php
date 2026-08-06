<?php 
include 'templates/header.php'; include 'templates/sidebar.php'; 

if($_SESSION['role_name'] !== 'Admin') {
    echo "<div class='alert alert-danger'>Akses Ditolak. Halaman ini khusus Admin.</div>"; 
    include 'templates/footer.php'; 
    exit;
}
?>

<div class="card shadow border-0">
    <div class="card-header bg-white py-3">
        <h5 class="mb-0 text-primary fw-bold"><i class="bi bi-clock-history me-2"></i>Audit Log Sistem</h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-sm table-hover">
                <thead class="table-light">
                    <tr>
                        <th>Waktu</th>
                        <th>User</th>
                        <th>Aktivitas</th>
                        <th>IP Address</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // Karena ini halaman sederhana, kita bisa load data langsung via PHP (tanpa Fetch API)
                    $logs = $pdo->query("SELECT a.*, u.name as user_name FROM audit_log a LEFT JOIN users u ON a.user_id = u.id ORDER BY a.id DESC LIMIT 100")->fetchAll();
                    foreach($logs as $log):
                    ?>
                    <tr>
                        <td class="text-muted small"><?= date('d M Y H:i', strtotime($log['created_at'])) ?></td>
                        <td><?= e($log['user_name'] ?? 'System') ?></td>
                        <td><?= e($log['action']) ?></td>
                        <td><?= e($log['ip_address']) ?></td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if(empty($logs)): ?>
                    <tr><td colspan="4" class="text-center text-muted">Belum ada aktivitas tercatat.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include 'templates/footer.php'; ?>