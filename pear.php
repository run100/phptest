#!/usr/bin/php -q

<?php

define('SF_DEBUG', true);

if(!isset($argv[1])){
#usage();
}

define('SF_ROOT_DIR', dirname(__FILE__) );
#echo SF_ROOT_DIR;
define('PEAR_DIR', SF_ROOT_DIR . '/pear');
define('PEAR_SRC', PEAR_DIR . '/share/pear');
define('PEAR_DATA_DIR', PEAR_DIR . '/data');


#echo PEAR_DIR,PEAR_SRC,PEAR_DATA_DIR;

set_include_path(get_include_path().":".PEAR_SRC);


echo posix_getuid();

if (posix_getuid() != 0) {
  die("This script must be run as root\n");
}

$user = posix_getpwnam('daemon');

print_r($user);


function usage()
{
  global $argv;
  echo 'Usage: ' . $argv[0] . ' queue_name [--write-initd] [--fg]' . "\n";
  die();  
}



