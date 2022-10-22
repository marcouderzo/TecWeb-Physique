<?php

require_once "../libs/DBaccess.php";

session_start();

$input = json_decode(file_get_contents("php://input"), true);

$idPost = intval($input['id'] ?? -1);

$result = array();

$DBaccess = new DBaccess();
if($DBaccess -> getConnection()){

    $result = $DBaccess-> getText($idPost);
}

$response = array('ok'=> true, 'result' => $result);

$response = json_encode($response);

header("Content-Type: application/json; charset=UTF-8");
header("Content-Length: " . strlen($response));

echo $response;