<?php
// File: api/master_ppe.php
header("Content-Type: application/json; charset=UTF-8");
include_once 'config.php';

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    // Ambil data PPE
    $query = "SELECT * FROM master_ppe ORDER BY id DESC";
    $stmt = $db->prepare($query);
    $stmt->execute();
    $records = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode(["records" => $records]);

} elseif ($method === 'POST') {
    // Tambah atau Update Data PPE
    $id = $_POST['id'] ?? null;
    $nama_alat = $_POST['nama_alat'] ?? '';
    $kategori = $_POST['kategori'] ?? '';
    $stok = $_POST['stok'] ?? 0;
    $satuan = $_POST['satuan'] ?? 'Pcs';
    
    $nama_file_foto = 'default.jpg';

    // Tangani Upload File Foto jika ada
    if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
        $fileTmpPath = $_FILES['foto']['tmp_name'];
        $fileName = $_FILES['foto']['name'];
        $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
        
        $allowedExtensions = ['jpg', 'jpeg', 'png', 'webp'];
        if (in_array($fileExtension, $allowedExtensions)) {
            $newFileName = md5(time() . $fileName) . '.' . $fileExtension;
            $uploadFileDir = '../uploads/';
            
            if(!is_dir($uploadFileDir)) {
                mkdir($uploadFileDir, 0755, true);
            }
            
            $dest_path = $uploadFileDir . $newFileName;
            if(move_uploaded_file($fileTmpPath, $dest_path)) {
                $nama_file_foto = $newFileName;
            }
        }
    }

    if (!empty($id)) {
        // Proses Update
        if(isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
            $query = "UPDATE master_ppe SET nama_alat = :nama_alat, kategori = :kategori, stok = :stok, satuan = :satuan, foto = :foto WHERE id = :id";
        } else {
            // Jika foto tidak diubah
            $query = "UPDATE master_ppe SET nama_alat = :nama_alat, kategori = :kategori, stok = :stok, satuan = :satuan WHERE id = :id";
        }
        
        $stmt = $db->prepare($query);
        $stmt->bindParam(':nama_alat', $nama_alat);
        $stmt->bindParam(':kategori', $kategori);
        $stmt->bindParam(':stok', $stok);
        $stmt->bindParam(':satuan', $satuan);
        if(isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
            $stmt->bindParam(':foto', $nama_file_foto);
        }
        $stmt->bindParam(':id', $id);
        
        if($stmt->execute()) {
            echo json_encode(["status" => "success", "message" => "Data PPE berhasil diperbarui."]);
        } else {
            echo json_encode(["status" => "error", "message" => "Gagal memperbarui data."]);
        }

    } else {
        // Proses Insert Data Baru
        $query = "INSERT INTO master_ppe (nama_alat, kategori, stok, satuan, foto) VALUES (:nama_alat, :kategori, :stok, :satuan, :foto)";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':nama_alat', $nama_alat);
        $stmt->bindParam(':kategori', $kategori);
        $stmt->bindParam(':stok', $stok);
        $stmt->bindParam(':satuan', $satuan);
        $stmt->bindParam(':foto', $nama_file_foto);
        
        if($stmt->execute()) {
            echo json_encode(["status" => "success", "message" => "Data PPE baru berhasil ditambahkan."]);
        } else {
            echo json_encode(["status" => "error", "message" => "Gagal menambahkan data baru."]);
        }
    }
}
?>