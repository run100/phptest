#!/usr/local/webserver/php/bin/php -q

<?php
# 参数solr  --write-initd
#error_reporting(E_ALL ^ E_NOTICE ^ E_STRICT);
error_reporting(E_ERROR | E_WARNING | E_PARSE);

define('SF_DEBUG', true);

$iniFile = __DIR__ . '/queue.ini';

if(!isset($argv[1])){
    usage();
}

$queue_name = strtolower($argv[1]);

$iniParam = parse_ini_file($iniFile);

if(!isset($iniParam[$queue_name])){
    die("not exists $queue_name\r\n");
}

$srcDir = realpath(dirname(__FILE__).'/../');

define('SF_ROOT_DIR', $srcDir );

define('PEAR_DIR', SF_ROOT_DIR . '/pear');
define('PEAR_SRC', PEAR_DIR . '/share/pear');
define('PEAR_DATA_DIR', PEAR_DIR . '/data');

#echo PEAR_DIR,PEAR_SRC,PEAR_DATA_DIR;

set_include_path(get_include_path().":".PEAR_SRC);


#echo "UID ".posix_getuid()."\r\n";

if (posix_getuid() != 0) {
    die("This script must be run as root\n");
}

$user = posix_getpwnam('daemon');

//print_r($user);

require_once "System/Daemon.php";

// Setup
$options = array(
    'appName' => 'queue_'.$queue_name,
    'queueName' => $queue_name,
    'appDir' => __DIR__,
    'appDescription' => 'Consumer daemon for the queue ' . $queue_name,
    'authorName' => 'run',
    'authorEmail' => '727271755@qq.com',
    'sysMaxExecutionTime' => '0',
    'sysMaxInputTime' => '0',
    'sysMemoryLimit' => '800M',
    'appRunAsGID' => $user['uid'],
    'appRunAsUID' => $user['gid'],
    'logLocation' => SF_ROOT_DIR . '/phppear/log/'.$queue_name.'.log',
    'logVerbosity' => System_Daemon::LOG_DEBUG,
    'runTemplateLocation' => __DIR__ . '/init.tpl',
);

System_Daemon::setOptions($options);

if (in_array('--write-initd', $argv)) {

    $initdPath = '/etc/rc.d/init.d/'.$options['appName'];
    #echo $initdPath;
    if(file_exists($initdPath)){
        @unlink($initdPath);
    }

    $path = System_Daemon::writeAutoRun();
    file_put_contents($path, str_replace('@queue_name@', $queue_name, file_get_contents($path)));
    die;
}

// 加载类文件
require_once 'amqpcls.php';
require_once 'consumer.php';



if (!in_array('--fg', $argv)) {
    //Run in foreground
    System_Daemon::start();
    $fg = false;
} else {
    $fg = true;
}

$debug = in_array('--debug', $argv);


$q = AmqpCls::getQueue($queue_name);
$consumer = AmqpCls::getConsumer($queue_name);

/*
exit();*/

while (!System_Daemon::isDying()) {
    try{
        //consume the queue
        $message = AmqpCls::getMessage($q);
        #print_r($message);

        if (is_null($message)) {
            System_Daemon::iterate(1);
            continue;
        }

        if ($fg) {
            print_r($message."\r\n"); // ['data']['i']
        }

        if ($debug) {
            System_Daemon::debug(my_print_r($message));
            $timer = microtime(true);
        }

        $result = $consumer::doTask($message); //process the message

        if ($debug) {
            $time = microtime(true) - $timer;
            System_Daemon::debug($time."s");
            if ($result !== null) {
                System_Daemon::debug($result);
            }

        }
    } catch(Exception $e){
        System_Daemon::err($e->getMessage());
        System_Daemon::err($e->getTraceAsString());
        break;
    }
}

function usage()
{
    global $argv;
    echo 'Usage: ' . $argv[0] . ' queue_name [--write-initd] [--fg]' . "\n";
    die();
}

function my_print_r($array)
{
    return preg_replace("/\s+/", " ", str_replace("\n", '', print_r($array, true)));
}
