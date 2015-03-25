#!/usr/local/webserver/php/bin/php -q

<?php
error_reporting(E_ALL ^ E_NOTICE);

define('SF_DEBUG', true);

if(!isset($argv[1])){
#usage();
}

$srcDir = realpath(dirname(__FILE__).'/../');

define('SF_ROOT_DIR', $srcDir );

define('PEAR_DIR', SF_ROOT_DIR . '/pear');
define('PEAR_SRC', PEAR_DIR . '/share/pear');
define('PEAR_DATA_DIR', PEAR_DIR . '/data');


#echo PEAR_DIR,PEAR_SRC,PEAR_DATA_DIR;

set_include_path(get_include_path().":".PEAR_SRC);


echo "UID ".posix_getuid()."\r\n";

if (posix_getuid() != 0) {
  die("This script must be run as root\n");
}

$user = posix_getpwnam('daemon');

#print_r($user);

require_once "System/Daemon.php";

#System_Daemon::writeAutoRun();

System_Daemon::setOption("appName", "check_daemon");
System_Daemon::setOption("authorEmail", "727271755@qq.com");

// System_Daemon::setOption("appDir", dirname(__FILE__));
#System_Daemon::log(System_Daemon::LOG_INFO, "Daemon not yet started so "."this will be written on-screen");

echo  System_Daemon::getOption("logLocation"); exit;
// Spawn Deamon!
// 開始產生為 Daemon 的部份
System_Daemon::start();

// 寫入紀錄.
// System_Daemon::getOption(); 可以取得一些定義好的環境.
// 像是 System_Daemon::getOption("logLocation") 可以取得 log 的位置跟名稱.
System_Daemon::log(System_Daemon::LOG_INFO, "Daemon: '".
    System_Daemon::getOption("appName").
    "' spawned! This will be written to ".
    System_Daemon::getOption("logLocation"));

// Your normal PHP code goes here. Only the code will run in the background
// so you can close your terminal session, and the application will
// still run.
// 接著把你要放在背景一直跑得程式碼寫在這裡... 就會在背景一直跑....
if(!file_exists('/tmp/darkhero_md5.txt'))
    touch('/tmp/darkhero_md5.txt');
while(1){
    if(file_exists('/tmp/darkhero.txt') and md5_file('/tmp/darkhero.txt') != file_get_contents('/tmp/darkhero_md5.txt')){
        System_Daemon::log(System_Daemon::LOG_INFO, "發現檔案內容變動, 更新 md5!!!");
        $md5_string = md5_file('/tmp/darkhero.txt');
        file_put_contents('/tmp/darkhero_md5.txt',$md5_string);
    }
    usleep(100);
}

System_Daemon::stop();


function usage()
{
  global $argv;
  echo 'Usage: ' . $argv[0] . ' queue_name [--write-initd] [--fg]' . "\n";
  die();  
}



