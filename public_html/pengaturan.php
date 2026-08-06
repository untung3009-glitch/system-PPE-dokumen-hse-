<?php
include 'templates/header.php';
include 'templates/sidebar.php';

if($_SESSION['role_name'] !== 'Admin') {
    echo "<div class='alert alert-danger'>Akses Ditolak</div>"; exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $app_name = $_POST['app_name'];
    $logo = $_FILES['logo'];
    $bg = $_FILES['background'];
    $current_settings = get_settings($pdo);

    $logo_name = $current_settings['logo'];
    $bg_name = $current_settings['background'];

    if ($logo['size'] > 0) {
        $logo_name = 'logo_' . time() . '.png';
        move_uploaded_file($logo['tmp_name'], 'uploads/setting/' . $logo_name);
    }
    if ($bg['size'] > 0) {
        $bg_name = 'bg_' . time() . '.jpg';
        move_uploaded_file($bg['tmp_name'], 'uploads/setting/' . $bg_name);
    }

    $stmt = $pdo->prepare("UPDATE settings SET app_name = ?, logo = ?, background = ? WHERE id = 1");
    $stmt->execute([$app_name, $logo_name, $bg_name]);
    log_audit($pdo, 'Update Settings');
    echo "<script>Swal.fire('Sukses!', 'Pengaturan diperbarui', 'success').then(()=>window.location='pengaturan.php');</script>";
}
?>
<div class="card shadow">
    <div class="card-header bg-white"><h5 class="mb-0 text-primary"><i class="bi bi-gear"></i> Pengaturan Sistem</h5></div>
    <div class="card-body">
        <form method="POST" enctype="multipart/form-data">
            <div class="mb-3">
                <label class="form-label">Nama Aplikasi</label>
                <input type="text" name="app_name" class="form-control" value="<?= e($settings['app_name']) ?>" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Logo Perusahaan</label><br>
                <img src="uploads/setting/<?= e($settings['logo']) ?>" height="50" class="mb-2"><br>
                <input type="file" name="logo" accept="image/*" class="form-control">
            </div>
            <div class="mb-3">
                <label class="form-label">Background Halaman Login</label><br>
                <img src="uploads/setting/<?= e($settings['background']) ?>" height="100" class="mb-2 rounded"><br>
                <input type="file" name="background" accept="image/*" class="form-control">
            </div>
            <button type="submit" class="btn btn-primary"><i class="bi bi-save"></i> Simpan Perubahan</button>
        </form>
    </div>
</div>
<?php include 'templates/footer.php'; ?>