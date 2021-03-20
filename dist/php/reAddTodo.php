<?php

session_start();

$body = json_decode(file_get_contents('php://input'), true);

$userTodo = $body["contents"];
$importance = $body["importance"];
$comment = $body["comment"];
$period = $body["period"];
$key = $body["keys"];
$jsonArray["userTodo"] = $userTodo;

include('./DBConnection.php');

$link = mysqli_connect($server,$user,$pw,$dbName);      //DBの接続

$userid = $_SESSION["userid"];

if($period == null) $sql = "INSERT INTO todo(todo_id, todo_contents, user_id, importance, comment, period) VALUES (".$key.", '".$userTodo."', '".$userid."',".$importance.",'".$comment."',".$period.")";
else $sql = "INSERT INTO todo(todo_id, todo_contents, user_id, importance, comment, period) VALUES (".$key.", '".$userTodo."', '".$userid."',".$importance.",'".$comment."','".$period."')";
$result = mysqli_query($link, $sql);

$jsonArray["success"] = $result;

header('Content-Type: application/json');

echo json_encode($jsonArray);


?>