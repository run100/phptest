#!/usr/local/webserver/php/bin/php

<?php
/**
 * User: Run
 * Date: 上午8:49 15-3-25
 * File: in.php
 * Desc: 
 */
error_reporting(E_ALL ^ E_NOTICE);


define('WEB_ROOT_DIR', realpath(__DIR__.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR));

require_once WEB_ROOT_DIR.DIRECTORY_SEPARATOR.'amqpcls.php';

// 写入队列
$queue = 'solr';
$amqp = AmqpCls::getConnection($queue);

$i = 1;
while(true){
    $data = array('a'=>date('Y-m-d H:i:s', time()),'b'=>uniqid(), 'i'=>$i);
    AmqpCls::put($queue, array('event'=>'insert',  'data'=>$data));

    echo $i."\r\n";
    $i++;
    /*$q = AmqpCls::getQueue('solr');
    $message = AmqpCls::getMessage($q);
    print_r($message); exit;*/
}


