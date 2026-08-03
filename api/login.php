<?php
session_start();
require_once "config.php";

$data=json_decode(file_get_contents("php://input"),true);

$username=$data["username"] ?? "";
$password=$data["password"] ?? "";

$sql=$pdo->prepare("SELECT * FROM users WHERE username=? LIMIT 1");
$sql->execute([$username]);

$user=$sql->fetch(PDO::FETCH_ASSOC);

if(!$user){

    echo json_encode([
        "success"=>false,
        "message"=>"Username tidak ditemukan"
    ]);
    exit;
}

if(!password_verify($password,$user["password"])){

    echo json_encode([
        "success"=>false,
        "message"=>"Password salah"
    ]);
    exit;
}

$_SESSION["user_id"]=$user["id"];
$_SESSION["role"]=$user["role"];

echo json_encode([
    "success"=>true,
    "user"=>$user
]);