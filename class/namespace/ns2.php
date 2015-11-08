<?php
use my\name as name;
include 'ns1.php';

echo get_include_path(), PHP_EOL;


$a = new name\A();
$a->print1();
$a::show();
echo name\A;