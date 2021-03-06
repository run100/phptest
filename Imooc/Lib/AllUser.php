<?php
/**
 * User: Run
 * Date: 上午11:58 15-4-11
 * File: AllUser.php
 * Desc: 
 */

namespace Imooc\Lib;

class AllUser implements \Iterator
{
    protected $index;
    protected $data = array();
    protected $ids;

    public function __construct(){
        $db = Factory::createDatabase('mysqli');
        //$db = Imooc\Lib\Database\Mysqli;
        //$db = new Database\Mysqli('127.0.0.1');
        $rs = $db->query("SELECT id FROM user");
        $this->ids = $rs->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Return the current element
     * @link http://php.net/manual/en/iterator.current.php
     * @return mixed Can return any type.
     */
    public function current(){
        $id = $this->ids[$this->index]['id'];
        return Factory::getMapping($id);
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Move forward to next element
     * @link http://php.net/manual/en/iterator.next.php
     * @return void Any returned value is ignored.
     */
    public function next(){
        $this->index ++;
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Return the key of the current element
     * @link http://php.net/manual/en/iterator.key.php
     * @return mixed scalar on success, or null on failure.
     */
    public function key(){
        return $this->index;
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Checks if current position is valid
     * @link http://php.net/manual/en/iterator.valid.php
     * @return boolean The return value will be casted to boolean and then evaluated.
     * Returns true on success or false on failure.
     */
    public function valid(){
        return  $this->index < count($this->ids);
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Rewind the Iterator to the first element
     * @link http://php.net/manual/en/iterator.rewind.php
     * @return void Any returned value is ignored.
     */
    public function rewind(){
        $this->index = 0;
    }
}