<?php

require_once "../libs/DBaccess.php";
require_once "../libs/helper.php";

session_start();

$response = array();

$input = json_decode(file_get_contents("php://input"), true);

$username = cleanFromTags($_SESSION["username"] ?? "");
$text = cleanFromTags($input["val"] ?? "");
$idPost = intval($input["idPost"] ?? -1);

$response['ok'] = false;

$DBaccess = new DBaccess();

if(validateCredentials($username) && validateText($text) && $idPost > -1 && $DBaccess->getConnection()) {

    $response['ok'] = $DBaccess->insertAnswer($username, $text, $idPost);
}
$DBaccess-> closeConnection();

$response = json_encode($response);

header("Content-Type: application/json; charset=UTF-8");
header("Content-Length: " . strlen($response));

echo $response;