<?php

require_once "../libs/DBaccess.php";
require_once "../libs/helper.php";

session_start();

$response = array(
    'user' => 'ok',
    'password' => 'ok'
);

$input = json_decode(file_get_contents("php://input"), true);

$keys = array(

    'user' => $input['username'] ?? "",
    'password' => $input['password'] ?? "",
);

if((strpos($keys['user'], '"') || strpos($keys['user'], "'")) != false){

    $response['user'] = 'that_was_a_SQL_injection_try';
}

if($keys['user'] == ""){

    $response['user'] = 'empty';
}

if($keys['password'] == ""){

    $response['password'] = 'empty';
}

$response = json_encode($response);

header("Content-Type: application/json; charset=UTF-8");
header("Content-Length: " . strlen($response));

echo $response;
