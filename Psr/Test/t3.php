<?php
error_reporting(E_ALL);

//require_once 't1.php';
//require_once 't2.php';

/*spl_autoload_register('autoload1');
function autoload1($class){
    $file = __DIR__.DIRECTORY_SEPARATOR.$class.'.php';
    echo $file."\r\n";
    require_once $file;
}*/

#Test1::info();

Test1::info();



function __autoload($class)
{

    $file = __DIR__.'/'.$class.'.php';
    echo $file."\r\n";
    require_once $file;
}

