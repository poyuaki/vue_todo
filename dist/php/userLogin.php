<?php
session_start();

include('./DBConnection.php');

$body = json_decode(file_get_contents('php://input'), true);

$link = mysqli_connect($server,$user,$pw,$dbName);      //DBの接続

$id_token = $body["idtoken"];
$userImg = $body["userimg"];

$_SESSION["canLogin"] = true;
$_SESSION["userid"] = $id_token;
$_SESSION["userImg"] = $userImg;

$jsonArray["success"] = $id_token;

$sql = "INSERT INTO `users`(`user_id`) VALUES ('".$id_token."')";
mysqli_query($link,$sql);


header('Content-Type: application/json');

echo json_encode($jsonArray);
?>