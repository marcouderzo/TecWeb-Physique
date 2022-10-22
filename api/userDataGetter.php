<?php

require_once "../libs/DBaccess.php";
require_once "../libs/helper.php";

session_start();

$user = cleanFromTags($_SESSION['username'] ?? "");
$result = array();

$DBaccess = new DBaccess();

if($DBaccess-> getConnection()){

    $result = $DBaccess-> getUserData($user);
}

$DBaccess->closeConnection();

$response = array(

    'ok' => true,
    'name' => $result['Nome'] ?? "Qualcosa è andato storto",
    'surname' => $result['Cognome'] ?? "Qualcosa è andato storto",
    'email' => $result['Email'] ?? "Qualcosa è andato storto"
);

$response = json_encode($response);

header("Content-Type: application/json; charset=UTF-8");
header("Content-Length: " . strlen($response));

echo $response;


