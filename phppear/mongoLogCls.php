<?php
/**
 * User: Run
 * Date: 下午4:01 15-3-25
 * File: mongoLogCls.php
 * Desc: 
 */

require_once 'mongoCls.php';

class MongoLogCls
{
    // Log priorities
    const INFO = 'info';
    const DEBUG = 'debug';
    const WARN = 'warn';
    const ERROR = 'err';
    const NOTICE = 'notice';


    public static function getAccessLogCollection($date = null){
        if(!$date){
            $d = date('Ymd');
        }else{
            $d = date('Ymd', strtotime($date));
        }


        $c = mongoLogCls::getAccessLogCollection('log.access_log.d'.$d);

        $c->ensureIndex(array(
            'url' => 1,
            'app' => 1,
            'module' => 1,
            'action' => 1,
            'request_time' => -1
        ), array('background' => true));

        return $c;
    }

    /**
     * Log user actions such login and logout
     * @param int $uid
     * @param string $action
     */
    public static function logUser($uid, $action)
    {
        $log = array(
            'uid' => $uid,
            'operation' => $action,
            'time' => date('Y-m-d H:i:s')
        );

        MongoCls::getDB()->log->user->insert($log);
    }

    public static function logSolr( $msg){
        $log = array(
            'i'    => $msg['i'],
            'type' => $msg['event'],
            'time' => date('Y-m-d H:i:s'),
            'a'  => $msg['a'],
            'b'  => $msg['b']
        );
        MongoCls::getDB()->log->solr->insert($log);
    }
}

MongoLogCls::logUser(1, 'insert');

#$rs = MongoCls::getCollection('log.user')->findOne(array('uid'=>1));
var_dump($rs);

/*

$q = LsMongo::getCollection('user.fakeids');
$query = array(
'uid' => (int)$uid//马甲所有者
);
$fakes = $q->find($query);

$query = array( "i" => array( "\$gt" => 20, "\$lte" => 30 ) );
$cursor = $coll->find( $query );
while( $cursor->hasNext() ) {
   var_dump( $cursor->getNext() );
}

我们非常容易漏掉$美元符号，你也可以选择你自定义的符号来代替美元符号，选择一个不会在你的建里面出现的符号例如”:”,在php.ini中加上这么一句话
mongo.cmd = “:”

查询
$ret = $buslog->find(array('clicks' => array('$gt' => 80)))
      ->sort(array('clicks' => -1))
      ->limit(35);
循环数据
$this->clicks = array();
while($ret->hasNext())
  $this->clicks[] = $ret->getNext();

//修改
$newdata = array('$set' => array("email" => "test@test.com"));
$collection->update(array("name" => "caleng"), $newdata);

添加
$data = array(
    'uid' => 1,
    'op'  => 'insert',
    'time' => date('Y-m-d H:i:s')
);
MongoCls::getDB()->log->user->insert($data);

LsMongoLog::logUser($user->getId(), 'REGISTER');
LsMongoLog::logUser($user->getId(), 'REGISTER_NONE_CODE');

// 搜索条件
$qcode = array(
  'operation' => 'REGISTER_NONE_CODE',
  'time' => array(
     '$gte' => $today . ' 00:00:00',
     '$lte' => $today . ' 23:59:59'
  ),
  'ip' => $ip,
);

if($c->count($qcode) >= 5){
  $this->isMaxCode = 1;
}

*/