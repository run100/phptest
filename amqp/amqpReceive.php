<?php
/**
 * PHP amqp(RabbitMQ) Receive
  */

$dbhand = null;
conn();

$exchangeName = 'demo';
$queueName = 'hello';
$routeKey = 'hello';

$connection = new AMQPConnection(array('host' => '127.0.0.1', 'port' => '5672', 'vhost' => '/', 'login' => 'guest', 'password' => 'guest'));
$connection->connect() or die("Cannot connect to the broker!\n");
$channel = new AMQPChannel($connection);
$exchange = new AMQPExchange($channel);
$exchange->setName($exchangeName);
$exchange->setType(AMQP_EX_TYPE_DIRECT);
$exchange->declare();
$queue = new AMQPQueue($channel);
$queue->setName($queueName);
$queue->declare();
$queue->bind($exchangeName, $routeKey);

var_dump('[*] Waiting for messages. To exit press CTRL+C');
while (TRUE) {
        $queue->consume('callback');
}
$connection->disconnect();

function callback($envelope, $queue) {
        $msg = $envelope->getBody();
        var_dump(" [x] Received:" . $msg);
        insert($msg);
        $queue->nack($envelope->getDeliveryTag());
}

function conn(){
  global $dbhand;
  $dsn = "mysql:host=127.0.0.1;dbname=test";
  try{
    $dbhand = new PDO($dsn, "root", '');
  }catch(PDOexception $e){
    var_dump($e->getMessage()); exit;
  }
}

function insert($msg){
  global $dbhand;
  usleep(100000); // 0.1秒
  $addtimestr = date('Y-m-d H:i:s', time());
  $sql = "INSERT INTO `test`.`amqp`(`msg`, `addtimestr`) VALUES('$msg', '$addtimestr') ";
  if( $dbhand->exec($sql) ){
    var_dump('Log Id Is '.$dbhand->lastinsertid());
  }else{
    var_dump('Insert Error');
  }

  return true;
}
