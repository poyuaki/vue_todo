<?php

session_start();

$jsonArray["canLogin"] = $_SESSION["canLogin"];

$jsonArray["userid"] = $_SESSION["userid"];

$jsonArray["userImg"] = $_SESSION["userImg"];

header('Content-Type: application/json');

echo json_encode($jsonArray);

?>