<?php
session_start();

include('./DBConnection.php');

$link = mysqli_connect($server,$user,$pw,$dbName);      //DBの接続

function getTodo($link,$userCookie){
    $sql = "SELECT todo_contents AS contents, todo_id AS id, importance,comment, period FROM todo WHERE user_id = '".$userCookie."' ORDER BY importance DESC, period ASC, todo_id DESC";
    $result = mysqli_query($link,$sql);
    $userTodoList = [[],[]];
    while($row = $result->fetch_assoc()){
        $userTodoList[0][] = $row["contents"];
        $userTodoList[1][] = $row["id"];
        $userTodoList[2][] = $row["importance"];
        $userTodoList[3][] = $row["comment"];
        $userTodoList[4][] = $row["period"];
    }
    return $userTodoList;
}

$userid = $_SESSION["userid"];
$todoList = getTodo($link,$userid);
$jsonArray["todo"] = $todoList[0];
$jsonArray["todo_id"] = $todoList[1];
$jsonArray["importance"] = $todoList[2];
$jsonArray["comment"] = $todoList[3];
$jsonArray["period"] = $todoList[4];

header('Content-Type: application/json');

echo json_encode($jsonArray);

?>