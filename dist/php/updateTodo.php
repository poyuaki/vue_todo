<?php
session_start();

$body = json_decode(file_get_contents('php://input'), true);

//ユーザのデータを変数に代入
$userTodo = $body["update"];
$index = $body["index"];
$importance = $body["importance"];
$comment = $body["comment"];
$period = $body["period"];

include('./DBConnection.php');

$link = mysqli_connect($server,$user,$pw,$dbName);      //DBの接続

$userCookie = $_SESSION["userid"];

$sql = "UPDATE todo SET todo_contents = '".$userTodo."', importance = ".(int)$importance.", comment = '".$comment."', period = '".$period."'  WHERE todo_id = ".$index." AND user_id = '".$userCookie."'";

if($period == null)$sql = "UPDATE todo SET todo_contents = '".$userTodo."', importance = ".(int)$importance.", comment = '".$comment."', period = NULL  WHERE todo_id = ".$index." AND user_id = '".$userCookie."'";

mysqli_query($link, $sql);

?>