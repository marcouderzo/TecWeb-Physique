<?php

require_once "../libs/DBaccess.php";
require_once "../libs/helper.php";

session_start();

$dbaccess = new DBaccess();

$response = array();

$input = json_decode(file_get_contents("php://input"), true);

$nomeUtente = cleanFromTags($_SESSION["username"] ?? "");
$leavingLike = $input["leavingLike"] ?? false;
$idPost = intval($input["idPost"] ?? -1);

$response['ok'] = false;

if(validateCredentials($nomeUtente) && is_bool($leavingLike) && $idPost > -1 && $_SESSION['username'] ?? false && !$_SESSION['banned']) {

    if($dbaccess-> getConnection()){

        $response['ok'] = $dbaccess-> leaveLike($nomeUtente, $leavingLike, $idPost);
    }
}

$dbaccess-> closeConnection();

$response = json_encode($response);

header("Content-Type: application/json; charset=UTF-8");
header("Content-Length: " . strlen($response));

echo $response;