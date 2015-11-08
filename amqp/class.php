<?php
/**
 * LsSimpleQueue is a simple interface for
 * @author joe
 *
 */


class LsSimpleQueue
{
  const QUEUE_NEWS_STATS = 'news';
  const QUEUE_CONTENT_STATS = 'content';
  const QUEUE_ACCESS_LOG = 'access_log';
  const QUEUE_SPHINX_RE_INDEX = 'sphinx_re_index';
  const QUEUE_EVENT = 'event';
  const QUEUE_SHOP_STATS = 'shop';
  const QUEUE_SHOP_VISTOR_STATS = 'shop_vistor';
  const QUEUE_NEWS_SPECIAL_STATS = 'news_special';
  const QUEUE_WAP_GA = 'wap_ga'; //Google analytics for WAP
  const QUEUE_IPHONE_PUSH_NOTIFICATION = 'iphone_push_notification';
  const QUEUE_JPUSH = 'jpush';
  const QUEUE_MOBILE_REALTIME_STATS = 'mobile_realtime_stats';
  const QUEUE_SOLR = 'solr';
  const QUEUE_VARNISH = 'va';
  const QUEUE_CRM_WEBSERVICE = 'crm_webservice';
  const QUEUE_RANKING_API = 'ranking_api';
  const QUEUE_ORDER = 'order';
  const QUEUE_ORDER_1 = 'order_1';
  const QUEUE_ORDER_3 = 'order_3';
  const QUEUE_ORDER_7 = 'order_7';
  const QUEUE_YX = 'yx';

  /**
   * The queue class map
   */
  static $map = array(
    self::QUEUE_YX                       => 'YxBaomingConsumer',
    self::QUEUE_ORDER_7                  => 'MiaoShaConsumer',
    self::QUEUE_ORDER_3                  => 'BargainCashConsumer',
    self::QUEUE_ORDER                    => 'LsOrderConsumer',
    self::QUEUE_ORDER_1                  => 'LsOrderConsumer1',
    self::QUEUE_NEWS_STATS               => 'LsNewsStatsConsumer',
    self::QUEUE_CONTENT_STATS            => 'LsContentStatsConsumer',
    self::QUEUE_ACCESS_LOG               => 'LsAccessLogConsumer',
    self::QUEUE_EVENT                    => 'EventConsumer',
    self::QUEUE_SPHINX_RE_INDEX          => 'LsQueueSphinxReIndexConsumer',
    self::QUEUE_SHOP_STATS               => 'LsShopStatsConsumer',
    self::QUEUE_SHOP_VISTOR_STATS        => 'LsShopVistorStatsConsumer',
    self::QUEUE_NEWS_SPECIAL_STATS       => 'LsNewsSpecialStatsConsumer',
    self::QUEUE_WAP_GA                   => 'WapGoogleAnalyticsConsumer',
    self::QUEUE_IPHONE_PUSH_NOTIFICATION => 'MobileIphonePushConsumer',
    self::QUEUE_SOLR                     => 'SolrConsumer',
    self::QUEUE_VARNISH                  => 'VarnishConsumer',
    self::QUEUE_JPUSH                    => 'JPushConsumer',
    self::QUEUE_MOBILE_REALTIME_STATS    => 'MobileRealtimeStatsConsumer',
    self::QUEUE_CRM_WEBSERVICE           => 'CrmWebService',
    self::QUEUE_RANKING_API              => 'LsRankingAPI'
  );

  static $durable_queues = array(
    'order_6'
  );

  static $remote_queues = array(
    'order_6'    => 'rabbitmq-365lin'
  );

  /**
   * The queue holder
   * @var array
   */
  static $queue = array();

  /**
   * @var AMQPConnection
   */
  static $cons = array();

  public static function getConnection($queue = null)
  {
    $host = self::$remote_queues[$queue] ?: 'rabbitmq';
    $con = self::$cons[$host] ?: null;

    if (null === $con) {
      $con = new AMQPConnection();
      $con->setHost($host);
      self::$cons[$host] = $con;
    }

    if (!$con->isConnected()) {
      $con->connect();
    }

    return $con;
  }

  /**
   * @param string $name Name of the queue
   * @param mixed $message
   */
  public static function put($name, $message)
  {
    $con = self::getConnection($name);

    // Declare a new exchange
    $ex = new AMQPExchange($con);
    $ex->declare($name, AMQP_EX_TYPE_FANOUT);

    $q = new AMQPQueue($con, $name);
    if (in_array($name, self::$durable_queues)) {
      $q->declare(AMQP_DURABLE);
    } else {
      $q->declare();
    }

    $ex->bind($name, 'default');

    $ex->publish(serialize($message), 'default');
  }


  public static function getQueue($name)
  {
    $con = self::getConnection($name);
    $q = new AMQPQueue($con, $name);
    if (in_array($name, self::$durable_queues)) {
      $q->declare(AMQP_DURABLE);
    } else {
      $q->declare();
    }
    return $q;
  }

  /**
   * Get message from a queue
   * @param string $queue Nmae of the queue
   * @return mixed
   */
  public static function getMessage(AMQPQueue $queue)
  {
    $result = $queue->get();
    if ($result['count'] < 0) {
      return null;
    }

    return unserialize($result['msg']);
  }


  /**
   * Get the class name of the consumer
   * @param string $queue
   * @return string
   */
  public static function getConsumer($queue)
  {
    return self::$map[$queue];
  }
}
