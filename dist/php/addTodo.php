<?php

session_start();

$body = json_decode(file_get_contents('php://input'), true);

$userTodo = $body["todo"];

include('./DBConnection.php');

$link = mysqli_connect($server,$user,$pw,$dbName);      //DBの接続

$userid = $_SESSION["userid"];

$sql = "INSERT INTO todo(todo_id, todo_contents, user_id, importance, comment, period) VALUES (NULL, '".$userTodo."', '".$userid."', 0,'',NULL)";
mysqli_query($link, $sql);

$sql = "SELECT MAX(todo_id) AS id FROM todo WHERE user_id = '".$userid."'";

$result = mysqli_query($link, $sql);

$row = $result->fetch_assoc();

$jsonArray["id"] = $row["id"];

header('Content-Type: application/json');

echo json_encode($jsonArray);

?>