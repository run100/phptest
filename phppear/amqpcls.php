<?php
/**
 * User: Run
 * Date: 上午9:52 15-3-24
 * File: amqpcls.php
 * Desc: 
 */

class amqpCls
{
    const QUEUE_SOLR = 'solr';

    // 队列对应程序处理文件，即消费者
    static $map = array(
        self::QUEUE_SOLR => 'SolrConsumer'
    );

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
        $con = self::$cons[$queue] ?: 'null';

        if($con === null){
            $host = array('host' => '127.0.0.1',
                          'port' => '5672',
                          'vhost' => '/',
                          'login' => 'guest',
                          'password' => 'guest');
            $con = new AMQPConnection($host);
            // $con->connect() or die("Cannot connect to the broker!\n");
            self::$cons[$queue] = $con;
        }

        if(!$con->isConnected()){
            $con->connect() or die("Cannot connect to the broker!\n");
        }

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
            // Declare a new exchange
            $ex = new AMQPExchange($con);
            $ex->declare($queueName, AMQP_EX_TYPE_FANOUT);

            $q = new AMQPQueue($con, $queueName);
            $q->declare();

            $ex->bind($queueName, 'default');
            $ex->publish(serialize($message), 'default');
        }catch(AMQPConnectionException $e){
            var_dump($e);
            exit();
        }

    }

    /**
     * @param $queueName
     */
    public static function getQueue($queueName){
        $con = self::getConnection($queueName);
        $q = new AMQPQueue($con, $queueName);
        $q->declare();
        return $q;
    }

    /**
     * 获取某个队列信息
     * @param AMQPQueue $queue
     * @return mixed|null
     */
    public static function getMessage(AMQPQueue $queue){
        $result = $queue->get();
        if($result['count'] < 0){
            return null;
        }

        return unserialize($result['msg']);
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