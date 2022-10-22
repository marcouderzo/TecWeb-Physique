<?php

require_once "../libs/DBaccess.php";

session_start();

$input = json_decode(file_get_contents("php://input"), true);

$idPost = intval($input['id'] ?? -1);

$result = false;

$DBaccess = new DBaccess();

if($DBaccess-> getConnection()){

    
    $result = $DBaccess-> deletePost($idPost) ?? false;
}

$response = array('ok'=> $result);

$response = json_encode($response);

header("Content-Type: application/json; charset=UTF-8");
header("Content-Length: " . strlen($response));

echo $response;