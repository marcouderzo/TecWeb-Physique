<?php

require_once "../libs/DBaccess.php";
require_once "../libs/helper.php";

session_start();

$response = array();

$input = json_decode(file_get_contents("php://input"), true);

$username = cleanFromTags($_SESSION["username"] ?? "");
$text = cleanFromTags($input["val"] ?? "");

$DBaccess = new DBaccess();

if(array_key_exists('submit', $input)) {

    if(validateCredentials($username) && validateText($text) && $DBaccess-> getConnection()) {

        $response['ok'] = $DBaccess->insertPost($username,$text);
    }
}
$DBaccess-> closeConnection();
$response = json_encode($response);

header("Content-Type: application/json; charset=UTF-8");
header("Content-Length: " . strlen($response));

echo $response;