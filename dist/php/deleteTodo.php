<?php
session_start();

$body = json_decode(file_get_contents('php://input'), true);

$todoId = $body["index"];

$todoId = (int)$todoId;

include('./DBConnection.php');

$link = mysqli_connect($server,$user,$pw,$dbName);      //DBの接続

$userid = $_SESSION["userid"];

$sql = "DELETE FROM todo WHERE user_id = '".$userid."' AND todo_id = ".$todoId;

mysqli_query($link, $sql);

header('Content-Type: application/json');

echo json_encode($jsonArray);

?>