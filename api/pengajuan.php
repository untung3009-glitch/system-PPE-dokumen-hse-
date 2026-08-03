<?php
require_once 'config.php';

header("Content-Type: application/json; charset=UTF-8");

try {

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {

        $reqNo      = $_POST['reqNo'] ?? '';
        $reqDate    = $_POST['reqDate'] ?? '';
        $reqName    = $_POST['reqName'] ?? '';
        $reqNik     = $_POST['reqNik'] ?? '';
        $reqDept    = $_POST['reqDept'] ?? '';
        $reqLoc     = $_POST['reqLoc'] ?? '';
        $ppeSelect  = $_POST['ppeSelect'] ?? '';
        $ppeSize    = $_POST['ppeSize'] ?? '';
        $ppeQty     = $_POST['ppeQty'] ?? 0;
        $reqNote    = $_POST['reqNote'] ?? '';

        $fotoApd = "";

        // Upload Foto
        if(isset($_FILES['photoApd']) && $_FILES['photoApd']['error']==0){

            if(!is_dir("../uploads")){
                mkdir("../uploads",0777,true);
            }

            $ext = pathinfo($_FILES["photoApd"]["name"], PATHINFO_EXTENSION);

            $namaFile = time()."_".uniqid().".".$ext;

            $tujuan = "../uploads/".$namaFile;

            if(move_uploaded_file($_FILES["photoApd"]["tmp_name"],$tujuan)){
                $fotoApd = "uploads/".$namaFile;
            }

        }

        $sql = $pdo->prepare("

            INSERT INTO pengajuan_apd

            (
                no_doc,
                tanggal,
                nama,
                nik,
                dept,
                lokasi,
                jenis_apd,
                ukuran,
                jumlah,
                foto_apd,
                catatan,
                status
            )

            VALUES

            (
                :no_doc,
                :tanggal,
                :nama,
                :nik,
                :dept,
                :lokasi,
                :jenis_apd,
                :ukuran,
                :jumlah,
                :foto_apd,
                :catatan,
                'Pending Safety'
            )

        ");

        $sql->execute([

            ':no_doc'=>$reqNo,
            ':tanggal'=>$reqDate,
            ':nama'=>$reqName,
            ':nik'=>$reqNik,
            ':dept'=>$reqDept,
            ':lokasi'=>$reqLoc,
            ':jenis_apd'=>$ppeSelect,
            ':ukuran'=>$ppeSize,
            ':jumlah'=>$ppeQty,
            ':foto_apd'=>$fotoApd,
            ':catatan'=>$reqNote

        ]);

        echo json_encode([
            "status"=>"success",
            "message"=>"Data berhasil disimpan"
        ]);

        exit;

    }

    if($_SERVER['REQUEST_METHOD']=="GET"){

        $sql=$pdo->query("SELECT * FROM pengajuan_apd ORDER BY id DESC");

        $data=$sql->fetchAll(PDO::FETCH_ASSOC);

        echo json_encode([
            "status"=>"success",
            "data"=>$data
        ]);

        exit;

    }

}catch(PDOException $e){

    http_response_code(500);

    echo json_encode([
        "status"=>"error",
        "message"=>$e->getMessage()
    ]);

}