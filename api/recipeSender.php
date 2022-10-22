<?php

require_once "../libs/DBaccess.php";
require_once "../libs/helper.php";

session_start();

$response = array();

$input = json_decode(file_get_contents("php://input"), true);

$name = cleanFromTags($input['name']);
$description = cleanFromTags($input['description']);
$imageName = cleanFromTags($input['imageName']);
$alt = cleanFromTags($input['alt']);
$ingredients = cleanFromTags($input['ingredients']);
$method = cleanFromTags($input['method']);
$hints = cleanFromTags($input['hints']);
$people = cleanFromTags($input['people']);

$opts = array(

    'checkForFormat' => true,
    'checkOnlyName' => true
);

$validName = validateText($name);
$validDescription = validateText($description);
$validImageName = validateText($imageName, $opts);
$validAlt = validateText($alt);
$validIngredients = validateText($ingredients);
$validMethod = validateText($method);
$validHints = cleanFromTags($hints);
$validPeople = validateText($people);

if($validAlt && $validDescription && $validHints && $validIngredients && $validImageName && $validMethod && $validName && $validPeople){

    $DBaccess = new DBaccess();

    if($DBaccess-> getConnection()){

        $response['ok'] = $DBaccess-> insertRecipe($name, $description, $imageName, $alt, $ingredients, $method, $hints, $people);
    }else{

        $response['ok'] = false;
    }

    $response['red'] = '/mtesser/?r=adminPanel';

}else{

    $elements = array(
        'r' => 'adminPanel'
    );

    if(!$validPeople){

        $elements['erp'] = 'error';
    }

    if(!$validName){

        $elements['ern'] = 'error';
    }

    if(!$validMethod){

        $elements['erm'] = 'error';
    }

    if(!$validImageName){

        $elements['erim'] = 'error';
    }

    if(!$validIngredients){

        $elements['eri'] = 'error';
    }

    if(!$validHints){

        $elements['erh'] = 'error';
    }

    if(!$validDescription){

        $elements['erd'] = 'error';
    }

    if(!$validAlt){

        $elements['era'] = 'error';
    }

    $redirect = '/mtesser/?' . http_build_query($elements);

    $response['red'] = $redirect;
}

$response = json_encode($response);

header("Content-Type: application/json; charset=UTF-8");
header("Content-Length: " . strlen($response));

echo $response;