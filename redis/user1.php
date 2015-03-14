<?php

include_once __DIR__.DIRECTORY_SEPARATOR.'session.php';

$sessCls = new sessRedisCls();
$sessCls::register();

session_start();

echo session_id();
echo "\r\n";
$_SESSION['name'] = 'run';

echo $_SESSION['name'];
echo "\r\n";

