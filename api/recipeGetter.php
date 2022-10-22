<?php

require_once "../libs/DBaccess.php";

session_start();

$result = array();

$DBaccess = new DBaccess();

if($DBaccess-> getConnection()){

    $result = $DBaccess-> getRecipeList();
}

$DBaccess->closeConnection();

$response = array('ok'=> true, 'result' => array());

foreach ($result as $item) {

    array_push($response['result'], $item);
}

$response = json_encode($response);

header("Content-Type: application/json; charset=UTF-8");
header("Content-Length: " . strlen($response));

echo $response;