<?php

include_once __DIR__.DIRECTORY_SEPARATOR.'session.php';

sessRedisCls::register();

session_start();


echo $_SESSION['name'];