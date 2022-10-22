<?php

require_once "../libs/DBaccess.php";
require_once  "../libs/helper.php";
session_start();

$response = array();

$input = json_decode(file_get_contents("php://input"), true);

$user = cleanFromTags($input["user"] ?? "");

$response['ok'] = false;

$dbaccess = new DBaccess();

if($dbaccess-> getConnection() && validateCredentials($user)){

    $response['ok'] = $dbaccess-> deleteUser($user);
}

$dbaccess-> closeConnection();

$response = json_encode($response);

header("Content-Type: application/json; charset=UTF-8");
header("Content-Length: " . strlen($response));

echo $response;