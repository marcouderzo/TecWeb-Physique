<?php

require_once "../libs/DBaccess.php";
require_once "../libs/helper.php";

$input = json_decode(file_get_contents("php://input"), true);

$user = cleanFromTags($input['user'] ?? "");
$response = array();

$response['ok'] = false;

$DBaccess = new DBaccess();

if($DBaccess-> getConnection() && validateCredentials($user)){

    $response['ok'] = $DBaccess-> banUser($user);
}

$DBaccess-> closeConnection();

$response = json_encode($response);

header("Content-Type: application/json; charset=UTF-8");
header("Content-Length: " . strlen($response));

echo $response;