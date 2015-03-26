<?php
/**
 * User: Run
 * Date: 上午9:52 15-3-24
 * File: amqpcls.php
 * Desc: 
 */

class AmqpCls
{
    const QUEUE_SOLR = 'solr';

    // 队列对应程序处理文件，即消费者
    static $map = array(
        self::QUEUE_SOLR => 'SolrConsumer'
    );

    /**
     * @var amqp 相关的参数 交换机,队列名称,路由key
     */
    static $exchangeName = 'queue';
    static $qName = 'default';
    static $routeKey = 'default';

    /**
     * The queue holder
     * @var array
     */
    static $queue = array();

    /**
     * AMQPConnection
     */
    static $cons = array();



    /**
     * @param null $queue
     */
    public static function getConnection($queue = null){
        $con = isset(self::$cons[$queue]) ? self::$cons[$queue] : null;

        if($con === null){
            $con = new AMQPConnection(array('host' => '127.0.0.1', 'port' => '5672', 'vhost' => '/', 'login' => 'guest', 'password' => 'guest'));
            // $con->connect() or die("Cannot connect to the broker!\n");
            self::$cons[$queue] = $con;
        }


        //if(!$con->isConnected()){
        $con->connect() or die("Cannot connect to the broker!\n");
        //}

        return $con;
    }

    /**
     * @param $queueName 队列名称
     * @param $message 信息
        $queueArray =array('event' => 'regist', 'data' => array());
        amqpCls::put(amqpCls::solr, $queueArray);
     */
    public static function put($queueName, $message){
        $con = self::getConnection($queueName);

        try{

            $channel = new AMQPChannel($con);
            $exchange = new AMQPExchange($channel);
            $exchange->setName(self::$exchangeName);
            $exchange->setType(AMQP_EX_TYPE_FANOUT);
            $exchange->setFlags(AMQP_DURABLE); //持久化
            #$exchange->declare();


            #echo "Exchange Status:".$exchange->declare()."\n";

            $queue = new AMQPQueue($channel);
            $queue->setName($queueName);
            //$queue->declare(AMQP_DURABLE);
            //$queue->bind(self::$exchangeName, self::$routeKey);

            $exchange->publish(serialize($message), self::$routeKey);

            /* $ex = new AMQPExchange($con);
            $ex->declare($queueName, AMQP_EX_TYPE_FANOUT);

            $q = new AMQPQueue($con, $queueName);
            $q->declare();

            $ex->bind($queueName, 'default');
            $ex->publish(serialize($message), 'default'); */

        }catch(AMQPConnectionException $e){
            var_dump($e);
            exit();
        }

    }

    /**
     * @param $queueName
     */
    public static function getQueue($queueName){
        try{
            $con = self::getConnection($queueName);
            /*$q = new AMQPQueue($con, $queueName);
            $q->declare();
            return $q;*/
            $channel = new AMQPChannel($con);
            $exchange = new AMQPExchange($channel);
            $exchange->setName(self::$exchangeName);

            $q = new AMQPQueue($channel);
            $q->setName($queueName);
            $q->declare();
            $q->bind(self::$exchangeName, self::$routeKey);
        }catch(AMQPConnectionException $e){
            var_dump($e);
            exit();
        }

        return $q;
    }

    /**
     * 获取某个队列信息
     * @param AMQPQueue $queue
     * @return mixed|null
     */
    public static function getMessage(AMQPQueue $queue){
        $result = $queue->get();
        #var_dump($result);
        if(!is_object($result)){
            return null;
        }
        if( $result->getBody() == ''){
            return null;
        }
        return unserialize($result->getBody());
        // return $queue->consume("callback");
    }


    /**
     * 获取队列名称对应的队列处理类
     * @param $queueName
     * @return mixed
     */
    public static function getConsumer($queueName){
        return self::$map[$queueName];
    }

}

function callback($envelope, $queue){
    return $envelope->getBody();
}