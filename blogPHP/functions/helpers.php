<?php

use Soap\Url;

define("BASE" , "http://localhost/PHP-PROJECT/");


function redirect($url){

    header("Location: ". trim(BASE , "/ ") ."/" . trim($url , "/ "));

    exit;

}

function asset($file){

    return trim(BASE , "/ ") . "/" . trim($file , '/ ');

}

function url($url){

    return trim(BASE , "/ ") . "/" . trim($url , '/ ');

}

function dd($var)
{

    var_dump($var);

    exit;
}





?>