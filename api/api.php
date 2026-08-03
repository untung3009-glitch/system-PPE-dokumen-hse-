<?php
// Izinkan akses dari frontend (CORS)
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json; charset=UTF-8");

// Konfigurasi Database Hostinger
$host = "localhost";
$db_name = "NAMA_DATABASE_ANDA";     // Ganti dengan nama database Anda
$username = "USERNAME_DATABASE_ANDA"; // Ganti dengan username database Anda
$password = "PASSWORD_ANDA";          // Ganti dengan password database Anda

try {
    $conn = new PDO("mysql:host=" . $host . ";dbname=" . $db_name, $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $exception) {
    echo json_encode(["success" => false, "message" => "Koneksi gagal: " . $exception->getMessage()]);
    exit();
}

// Tangkap metode HTTP yang dikirim (GET, POST, PUT, DELETE)
$method = $_SERVER['REQUEST_METHOD'];

switch($method) {
    case 'GET':
        // READ: Ambil semua data APD
        try {
            $stmt = $conn->prepare("SELECT * FROM ppe_items ORDER BY id DESC");
            $stmt->execute();
            $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
            echo json_encode(["success" => true, "data" => $data]);
        } catch(Exception $e) {
            echo json_encode(["success" => false, "message" => $e->getMessage()]);
        }
        break;

    case 'POST':
        // CREATE: Tambah data APD baru
        $input = json_decode(file_get_contents("php://input"), true);
        if(isset($input['name'], $input['category'], $input['stock'], $input['status'], $input['location'], $input['last_checked'])) {
            try {
                $stmt = $conn->prepare("INSERT INTO ppe_items (name, category, stock, status, location, last_checked) VALUES (:name, :category, :stock, :status, :location, :last_checked)");
                $stmt->execute([
                    ':name' => $input['name'],
                    ':category' => $input['category'],
                    ':stock' => $input['stock'],
                    ':status' => $input['status'],
                    ':location' => $input['location'],
                    ':last_checked' => $input['last_checked']
                ]);
                echo json_encode(["success" => true, "message" => "Data berhasil ditambahkan"]);
            } catch(Exception $e) {
                echo json_encode(["success" => false, "message" => $e->getMessage()]);
            }
        } else {
            echo json_encode(["success" => false, "message" => "Data tidak lengkap"]);
        }
        break;

    case 'PUT':
        // UPDATE: Ubah data APD berdasarkan ID
        $input = json_decode(file_get_contents("php://input"), true);
        if(isset($input['id'])) {
            try {
                $stmt = $conn->prepare("UPDATE ppe_items SET name=:name, category=:category, stock=:stock, status=:status, location=:location, last_checked=:last_checked WHERE id=:id");
                $stmt->execute([
                    ':id' => $input['id'],
                    ':name' => $input['name'],
                    ':category' => $input['category'],
                    ':stock' => $input['stock'],
                    ':status' => $input['status'],
                    ':location' => $input['location'],
                    ':last_checked' => $input['last_checked']
                ]);
                echo json_encode(["success" => true, "message" => "Data berhasil diperbarui"]);
            } catch(Exception $e) {
                echo json_encode(["success" => false, "message" => $e->getMessage()]);
            }
        } else {
            echo json_encode(["success" => false, "message" => "ID tidak ditemukan"]);
        }
        break;

    case 'DELETE':
        // DELETE: Hapus data berdasarkan ID
        $input = json_decode(file_get_contents("php://input"), true);
        if(isset($input['id'])) {
            try {
                $stmt = $conn->prepare("DELETE FROM ppe_items WHERE id = :id");
                $stmt->execute([':id' => $input['id']]);
                echo json_encode(["success" => true, "message" => "Data berhasil dihapus"]);
            } catch(Exception $e) {
                echo json_encode(["success" => false, "message" => $e->getMessage()]);
            }
        } else {
            echo json_encode(["success" => false, "message" => "ID tidak ditemukan"]);
        }
        break;

    default:
        echo json_encode(["success" => false, "message" => "Metode tidak didukung"]);
        break;
}
?>