<?php
/**
 * Created by PhpStorm.
 * User: run
 * Date: 15/10/24
 * Time: 上午9:41
 */


interface  DbAdapter
{
  /**
   * 数据库连接
   * @param $config 配置数组
   * @return resource
   */
  public function connect($config);

  /**
   * 执行查询
   * @param $query SQL字符串
   * @param $handle 连接对象
   * @return resource
   */
  public function query($query, $handle);
}

class MysqlDbAdapter implements DbAdapter
{

  private $dbLink;

  public function connect($config)
  {
    // $this->dbLink = @mysqli_connect($config->host, $config->);
  }

  public function query($query, $handle)
  {

  }
}
