<?php

use \Gobie\Regex\Wrappers\Pcre\PcreRegex;
require_once __DIR__ . "/../vendor/autoload.php";

class Renderer{

    public function __construct(){}


    public function render(string $html, array $variables = array()): string {

        $data = $this->replaceInclude(self::removeComments($html));
        $this->replaceBlocks($data);
        $this->replaceIfs($data, $variables);
        $this->replaceVariables($data, $variables);
        $this->replaceEnglish($data);
        return $data;
    }

    public function renderFile(string $file, array $variables = array()): string{

        return $this-> render(file_get_contents(__DIR__ . '/../html/' . $file  . '.xhtml'), $variables);
    }

    private static function removeComments(string $data): string {

        $matches = PcreRegex::getAll("/<!--.*-->/", $data);

        foreach (($matches[0] ?? array()) as $match) {
            $data = str_replace($match, '', $data);
        }
        return $data;
    }

    public static function clean(string $data): string {

        $matches = PcreRegex::getAll("/<[^\/<>]*Placeholder \/>/", $data);

        foreach (($matches[0] ?? array()) as $match) {
            $data = str_replace($match, '', $data);
        }

        return $data;
    }

    private function replaceInclude(string $data): string{

        $matches = PcreRegex::getAll("/<include(.)*Placeholder \/>/", $data);



        foreach (($matches[0] ?? array()) as $match) {

            $data = str_replace($match, $this->replaceInclude(self::removeComments(file_get_contents(
                __DIR__ . '/../html/' . strtolower(str_replace('<include', '', str_replace('Placeholder />', '', $match) . '.xhtml'))
            ))), $data);
        }

        return $data;
    }

    
    private function replaceBlocks(string &$data):void{

        $matches = PcreRegex::getAll("/<blockSet[^\/<>]*Placeholder \/>/", $data);
        foreach (($matches[0] ?? array()) as $match) {

            $blockName = strtolower(str_replace('<blockSet', '', str_replace('Placeholder />', '', $match)));
            $blockBeginString = '<blockDef' . ucfirst($blockName) . 'Placeholder />';
            $blockBegin = stripos($data, $blockBeginString);
            if($blockBegin === false){
                continue;
            }
            $blockEndString = '<blockEndPlaceholder />';
            $blockEnd = stripos($data, $blockEndString, $blockBegin);
            if($blockEnd === false){
                continue;
            }
            $codeToReplace = substr($data, $blockBegin + strlen($blockBeginString), $blockEnd - $blockBegin - strlen($blockBeginString));
            $data = str_replace('<blockSet' . ucfirst($blockName) . 'Placeholder />', $codeToReplace, $data);
            $data = str_replace($blockBeginString . $codeToReplace . '<blockEndPlaceholder />', '', $data);
        }
    }

    private function replaceIfs(string &$data, array $variables = array()){

        $matches = PcreRegex::getAll("/<if[^\/<>]*Placeholder \/>/", $data);
        foreach (($matches[0] ?? array()) as $match) {

            $ifVariableName = strtolower(str_replace('<if', '', str_replace('Placeholder />', '', $match)));
            $ifBlockBeginString = '<if' . ucfirst($ifVariableName) . 'Placeholder />';
            $ifBlockBegin = stripos($data, $ifBlockBeginString);

            $ifBlockEndString = '<endIfPlaceholder />';
            $ifBlockEnd = stripos($data, $ifBlockEndString, $ifBlockBegin);
            if($ifBlockEnd !== false){

                $codeToReplace = substr($data, $ifBlockBegin + strlen($ifBlockBeginString), $ifBlockEnd - $ifBlockBegin - strlen($ifBlockBeginString));


                if($variables[$ifVariableName] ?? false){

                    $data = str_replace($ifBlockBeginString . $codeToReplace . $ifBlockEndString, $codeToReplace, $data);
                }else{

                    $data = str_replace($ifBlockBeginString . $codeToReplace . $ifBlockEndString, '', $data);
                }
            }
        }

    }

    private function replaceVariables(string &$data, array $variables): void{


        $matches = PcreRegex::getAll("/<[^\/<>]*Placeholder \/>/", $data);
        foreach (($matches[0] ?? array()) as $match) {

            $variableName = strtolower(str_replace('<', '', str_replace('Placeholder />', '', $match)));
            if (array_key_exists($variableName, $variables)) {

                $data = str_replace($match, $variables[$variableName], $data);
            }
        }
    }

    private function replaceEnglish(string &$data){

        $matches = PcreRegex::getAll("/%%[^\/<>%%]*%%/", $data);
        foreach(($matches[0] ?? array()) as $match) {

            $word = substr($match, 2, strlen($match) - 4);
            $data = str_replace("%%".$word."%%", "<span xml:lang = 'en' lang = 'en'>".$word."</span>", $data);
        }
    }

}