<?php
/**
 * User: Run
 * Date: 上午11:23 15-3-26
 * File: out.php
 * Desc: 
 */

require_once '../amqpcls.php';
require_once '../consumer.php';

// solr

$queue_name = $argv[1];
if(!$queue_name){
    exit('请填写队列名称');
}

$q = AmqpCls::getQueue($queue_name);

var_dump('[*] Waiting for messages. To exit press CTRL+C');
while (TRUE) {
    //$q->consume('callback');
    $message = AmqpCls::getMessage($q);
    #var_dump(" [x] Received:" . $message);
    " [x] Received:" .var_dump($message);
}

function callback($envelope, $queue) {
    $msg = $envelope->getBody();
    var_dump(" [x] Received:" . $msg);
    $queue->nack($envelope->getDeliveryTag());
}

//$consumer = AmqpCls::getConsumer($queue_name);

/*while(true){
    $message = AmqpCls::getMessage2($q);
    echo(my_print_r($message)."\r\n");
    //$consumer::doTask($message);
}*/

function my_print_r($array)
{
    return preg_replace("/\s+/", " ", str_replace("\n", '', print_r($array, true)));
}