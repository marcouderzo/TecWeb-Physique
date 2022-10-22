<?php

require_once "../libs/DBaccess.php";;
require_once  "../libs/helper.php";

session_start();

$input = json_decode(file_get_contents("php://input"), true);

$user = cleanFromTags($input['user'] ?? "");

$DBaccess = new DBaccess();

$result = false;

if($DBaccess-> getConnection() && validateCredentials($user)){

    $result = $DBaccess-> promoteToAdmin($user);
}

$DBaccess->closeConnection();

$response = array();

$response['ok'] = $result;

$response = json_encode($response);

header("Content-Type: application/json; charset=UTF-8");
header("Content-Length: " . strlen($response));

echo $response;