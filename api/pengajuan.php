<?php
require_once 'config.php';
header("Content-Type: application/json; charset=UTF-8");

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'POST') {
    // Menerima data JSON dari Frontend
    $input = json_decode(file_get_contents('php://input'), true);

    if (!$input) {
        // Jika data dikirim lewat FormData (karena ada upload file foto)
        $reqNo = $_POST['reqNo'] ?? '';
        $reqDate = $_POST['reqDate'] ?? '';
        $reqName = $_POST['reqName'] ?? '';
        $reqNik = $_POST['reqNik'] ?? '';
        $reqDept = $_POST['reqDept'] ?? '';
        $reqLoc = $_POST['reqLoc'] ?? '';
        $ppeSelect = $_POST['ppeSelect'] ?? '';
        $ppeSize = $_POST['ppeSize'] ?? '';
        $ppeQty = $_POST['ppeQty'] ?? 0;
        $reqNote = $_POST['reqNote'] ?? '';
        
        // Handle upload foto jika ada
        $fotoApd = '';
        if (isset($_FILES['photoApd']) && $_FILES['photoApd']['error'] === UPLOAD_ERR_OK) {
            $ext = pathinfo($_FILES['photoApd']['name'], PATHINFO_EXTENSION);
            $fotoApd = 'uploads/' . time() . '_' . uniqid() . '.' . $ext;
            move_uploaded_file($_FILES['photoApd']['tmp_name'], '../' . $fotoApd);
        }

        try {
            $stmt = $db->prepare("INSERT INTO pengajuan_apd (no_doc, tanggal, nama, nik, dept, lokasi, jenis_apd, ukuran, jumlah, foto_apd, catatan, status) 
                VALUES (:no_doc, :tanggal, :nama, :nik, :dept, :lokasi, :jenis_apd, :ukuran, :jumlah, :foto_apd, :catatan, 'Pending Safety')");
            
            $stmt->execute([
                'no_doc' => $reqNo,
                'tanggal' => $reqDate,
                'nama' => $reqName,
                'nik' => $reqNik,
                'dept' => $reqDept,
                'lokasi' => $reqLoc,
                'jenis_apd' => $ppeSelect,
                'ukuran' => $ppeSize,
                'jumlah' => $ppeQty,
                'foto_apd' => $fotoApd,
                'catatan' => $reqNote
            ]);

            echo json_encode(["status" => "success", "message" => "Pengajuan berhasil dikirim!"]);
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(["status" => "error", "message" => $e->getMessage()]);
        }
    }
} elseif ($method === 'GET') {
    // Mengambil data untuk Riwayat & Dokumentasi
    try {
        $stmt = $db->query("SELECT * FROM pengajuan_apd ORDER BY id DESC");
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode(["status" => "success", "data" => $data]);
    } catch (PDOException $e) {
        echo json_encode(["status" => "error", "message" => $e->getMessage()]);
    }
}
?>