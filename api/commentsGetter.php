<?php

require_once "../libs/DBaccess.php";
require_once "../libs/helper.php";

session_start();

$user = cleanFromTags($_SESSION['username'] ?? "");
$result = array();

$DBaccess = new DBaccess();

if($DBaccess-> getConnection()) {

    $result = $DBaccess->getPostList($user);
}

$DBaccess-> closeConnection();

$response = array('ok'=> true, 'result' => array(),'logged' => $_SESSION['username'] ?? false, 'banned' => $_SESSION['banned'] ?? false);

foreach ($result as $item) {

    array_push($response['result'], $item);
}

$response = json_encode($response);

header("Content-Type: application/json; charset=UTF-8");
header("Content-Length: " . strlen($response));

echo $response;
