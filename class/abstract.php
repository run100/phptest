<?php

include_once 'mongodb.php';

abstract class Comment
{
  public $uid;
  public $content;
  public $type;

  abstract public function save();
  abstract public function delete($arr);
  abstract public function find($arr);
  abstract public function update($arr);
}

abstract class NewsComment extends Comment
{
  public $newsid;
  public $uname;
}

abstract class productComment extends Comment
{
}

class News extends NewsComment
{
  public function save( )
  {
    $arr = array(
      'uid' => $this->uid,
      'uname' => $this->uname,
      'newsid' => $this->newsid,
      'content' => $this->content,
      'type' => 0,
      'time' => time()
    );
    MongoCli::getDb()->comment->news->insert($arr);
  }

  public function delete( $arr )
  {
    // echo MongoCli::getDb()->comment->count($arr);
    $collection = MongoCli::getDb()->comment->user;
    print_r($collection->find());
    exit;
    $rs = $collection->remove($arr);
    print_r($rs);
  }

  public function find( $arr )
  {
  }

  public function update( $arr )
  {
  }
}

$new = new News();
/*$new->uid = 0;
$new->uname = 'run1';
$new->newsid = 1;
$new->content = '第二次';
$new->save();
*/

$new->delete( array('uid'=>8) );


//MongoCli::getCollection('comment.news');
/*$arr = array(
  'uid' => 8,
  'uname' => 'run',
  'newsid' => 1,
  'content' => '不错',
  'time' => time()
);
MongoCli::getDb()->comment->news->insert($arr);
*/
