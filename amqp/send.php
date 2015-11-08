<?php

// 入队列

//连接RabbitMQ
$conn_args = array( 'host'=>'localhost' , 'port'=> '5672', 'login'=>'guest' , 'password'=> 'guest','vhost' =>'/');
$conn = new AMQPConnection($conn_args);

$conn->connect();

//创建通道Channel
$channel = new AMQPChannel($conn);

// 创建交换 exchange名称和类型
$ex = new AMQPExchange($channel);
$ex->setName('direct_exchange_name');
$ex->setType(AMQP_EX_TYPE_DIRECT);
$ex->setFlags(AMQP_DURABLE | AMQP_AUTODELETE);
$ex->declare();

//创建queue名称，使用exchange，绑定routingkey
$q = new AMQPQueue($channel);
$q->setName('queue_name');
$q->setFlags(AMQP_DURABLE | AMQP_AUTODELETE);
$q->declare();
$q->bind('direct_exchange_name', 'routingkey_name');

//消息发布
$channel->startTransaction();
$message = json_encode(array('Hello World!','DIRECT'));
$ex->publish($message, 'routingkey_name');

// 通道关闭
$channel->commitTransaction();


