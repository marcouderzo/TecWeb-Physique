<?php

require_once 'DBaccess.php';
require_once __DIR__ . "/../vendor/autoload.php";
use \Gobie\Regex\Wrappers\Pcre\PcreRegex;

function validateType(string $type): bool{

    $DBaccess = new DBAccess();
    $types = array();
    if($DBaccess-> getConnection()){

        $types = $DBaccess-> getNewsTypesList();
    }

    return in_array($type, $types);
}

function validateLink(string $link): bool{

    return filter_var($link, FILTER_VALIDATE_URL);
}

function validateText(string $text, array $options =null): bool{

    if($options['checkForFormat'] ?? false){

        $pos = strpos($text, '.');
        if($pos == false || $pos == strlen($text) - 1){

            return false;
        }
    }

    if($options['checkOnlyName'] ?? false){

        if(strpos($text, '/')){

            return false;
        }
    }

    return strlen($text) > 0;
}

function cleanFromTags(string $text): string{

    $prev = '';
    while($prev != $text){

        $prev = $text;
        $text = strip_tags($text);
    }
    return $text;
}


function validateCredentials(string $text):bool{

    return strlen($text) > 0;
}

function sqlInjectionTry(string $text): bool{

    if((strpos($text, '"') || strpos($text, "'")) != false){

        return true;
    }
    return false;
}

function validateEmail(string $text):bool{
    
    $atPos = strpos($text, '@');
    $dotPos = strrpos($text, '.');

    if($atPos === false || $dotPos === false || $dotPos == (strlen($text) - 1)){

        return false;

    }else{

        if(!filter_var($text, FILTER_VALIDATE_EMAIL) || ($dotPos - $atPos) < 3 || $atPos < 3){

            $text = filter_var($text, FILTER_SANITIZE_EMAIL);
            if(!filter_var($text, FILTER_VALIDATE_EMAIL)){

                return false;
            }
        }
    }
    return true;
}