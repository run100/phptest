<?php


//连接RabbitMQ
$conn_args = array( 'host'=>'localhost' , 'port'=> '5672', 'login'=>'guest' , 'password'=> 'guest','vhost' =>'/');
$conn = new AMQPConnection($conn_args);
$conn->connect();

//设置queue名称，使用exchange，绑定routingkey

$channel = new AMQPChannel($conn);
$q = new AMQPQueue($channel);
$q->setName('queue_name');
$q->setFlags(AMQP_DURABLE | AMQP_AUTODELETE);
$q->declare();
$q->bind('direct_exchange_name', 'routingkey_name');

//消息获取
$messages = $q->get(AMQP_AUTOACK) ;
if ($messages){
      var_dump(json_d ecode($messages->getBody(), true ));
}
$conn->disconnect();

