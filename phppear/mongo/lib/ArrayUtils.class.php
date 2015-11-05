<?php

class ArrayUtils
{

  /**
   * 将Model对象或Model对象数组转换为数组形式
   */
  public static function to_array($e)
  {
    if ($e instanceof MongoCursor) {
      $ret = array();
      foreach($e as $i){
        $ret[] = self::to_array($i);
      }
      return $ret;
    } else if(is_array($e) || $e instanceof Traversable){
      $ret = array();
      foreach($e as $k => $i){
        $ret[$k] = self::to_array($i);
      }
      return $ret;
    }

    if(!$e instanceof Persistent)
      return $e;

    $peer_class = get_class($e->getPeer());
    $names = call_user_func(array($peer_class, 'getPhpNameMap'));

    $values = array();
    foreach($names as $key => $col){
     $method = "get$key";
     $values[strtolower($col)] = $e->$method();
    }
    return $values;
  }

  /**
   * 类似于数据库JOIN操作，
   * $on为字符串时表示外键，为回调函数时表示Join判断逻辑
   * $alias表示相关项的存取键
   */
  public static function join($arr1, $arr2, $on, $alias = 'joined')
  {
    $flag = is_callable($on);
    foreach($arr1 as &$item){
      if(!$flag && isset($arr2[$item[$on]]))
        $item[$alias] = &$arr2[$item[$on]];
      elseif($flag){
        foreach($arr2 as &$item2){
          if(call_user_func($on, $item, $item2)){
            $item[$alias] = $item2;
            break;
          }
        }
      }
    }
    return $arr1;
  }

  /**
   * 类似于数据库JOIN操作，
   * $on为字符串时表示外键，为回调函数时表示Join判断逻辑
   * $alias表示相关项的存取键
   */
  public static function ljoin($arr1, $arr2, $on, $alias = 'joined')
  {
    $flag = is_callable($on);
    foreach($arr1 as $id => &$item){
      $item[$alias] = array();
      if(!$flag)
        foreach($arr2 as $id2 => &$item2){
          if($item2[$on] == $id)
            $item[$alias][$id2] = $item2;
        }
      elseif($flag){
        foreach($arr2 as $id2 => &$item2){
          if(call_user_func($on, $item, $item2))
            $item[$alias][$id2] = $item2;
        }
      }
    }
    return $arr1;
  }

  /**
   * 数组项的某一子键作为数组键，构造新的键值关系数组。
   * 数组项可以为对象，也可以为数组，$keyname必须以匈牙利命名格式书写，如: id, type_name
   */
  public static function key_map($e, $keyname = 'id')
  {
    $keys = array();

    foreach($e as $i)
      $keys[] = self::fetchValue($i, $keyname);

    if(!count($e))
      return array();

    return array_combine($keys, (array)$e);
  }

  /**
   * 数组映射
   */
  public static function map($e, $keyname)
  {
    if(is_string($keyname))
      $keyname = self::trim(explode(',', $keyname));
    //if(is_array($keyname))
    //  $keyname = self::lower($keyname);

    if(is_array($keyname) && count($keyname) <= 1)
      $keyname = $keyname[0];

    $ret = array();
    foreach($e as $k => $i){
      if(!is_array($keyname)){
        $ret[$k] = self::fetchValue($i, $keyname);
      } else {
        $itm = array();
        foreach($keyname as $k2)
          $itm[$k2] = self::fetchValue($i, $k2);
        $ret[$k] = $itm;
      }
    }
    return $ret;
  }

  /**
   * 同map，但只对单个项有效
   */
  public static function mask($e, $keyname)
  {
    if(is_string($keyname))
      $keyname = self::trim(explode(',', $keyname));
    if(is_array($keyname))
      $keyname = self::lower($keyname);

    if(count($keyname) <= 1)
      $keyname = $keyname[0];

    if(!is_array($keyname)){
      return self::fetchValue($e, $keyname);
    } else {
      $ret = array();
      foreach($keyname as $k)
        $ret[$k] = self::fetchValue($e, $k);
      return $ret;
    }
  }

  /**
   * $e为传入键值关系数组，该函数返回以$keyname指定子键构造的父子关系数组
   * 返回项中子节点存于_children中
   */
  public static function build_offspring($e, $keyname = 'parent_id')
  {
    $nroot_keys = array();
    foreach($e as $k => &$i){
      $children = array();
      foreach($e as $k2 => $i2){
        $key2 = self::fetchValue($i2, $keyname);
        if($key2 === $i2)
          continue;

        if($key2 == $k){
          $children[$k2] = &$e[$k2];
          $nroot_keys[] = $k2;
        }
      }
      if(is_array($i) || $arr instanceof Traversable)
        $i['_children'] = $children;
      else
        $i->_children = $children;
    }

    $nroot_keys = array_unique($nroot_keys);
    foreach($nroot_keys as $key){
      unset($e[$key]);
    }

    return $e;
  }

  /**
   * 将传入值当做数组使用，若不是数组，则看做空数组。
   * 传入参数$offset和$length用于数组裁剪
   */
  public static function safety($arr, $offset = null, $length = null, $preserve_keys= true)
  {
    if(isset($arr) && $arr && (is_array($arr) || $arr instanceof Traversable))
      return array_slice($arr, $offset, $length, $preserve_keys);
    return array();
  }

  /**
   * 按顺序关联多个数组，关联的结果集大小与传入数组中最小的相等
   */
  public static function magic_link()
  {
    $args = func_get_args();
    if(!$args)
      return array();

    $count = count($args[0]);
    foreach($args as $arg)
      $count = ($ncount = count(self::safety($arg))) < $count ? $ncount : $count;

    $ret = array();
    for($i = 0; $i < $count; $i ++){
      $item = array();
      foreach($args as $arg)
        $item[] = $arg[$i];
      $ret[] = $item;
    }
    return $ret;
  }

  /**
   * 将二维数组一阶key与二阶key对换，可简单理解为magic_link的逆过程，传入数组项可以是Symfony Model对象
   */
  public static function magic_extract($arr)
  {
    $arr = self::to_array($arr);
    $ret = array();
    foreach($arr as $k => $itm)
      foreach($itm as $k2 => $val){
        if(!array_key_exists($k2, $ret))
          $ret[$k2] = array();
        $ret[$k2][$k] = $val;
      }
    return $ret;
  }

  /**
   * 确保传入数组元素数能被$length整除，多余的将被排除
   */
  public static function step($arr, $length)
  {
    $ret = array();
    foreach(array_chunk($arr, $length, true) as $subarr){
      if(count($subarr) == $length)
        $ret += $subarr;
    }

    return $ret;
  }

  /**
   * 在$arr的$key键上搜索$needle关键字
   */
  public static function search($arr, $key, $needle, $preg = null)
  {
    if($preg == null){
      $needle = preg_quote($needle, '#');
      $preg = "#$needle#";
    }

    return array_intersect_key(
      $arr,
      preg_grep($preg, ArrayUtils::map($arr, $key)));
  }

  public static function search_one($arr, $key, $needle, $preg = null)
  {
    $ret = self::search($arr, $key, $needle, $preg);
    if($ret)
      foreach($ret as $item)
        return $item;
    else
      return null;
  }

  /**
   * 取数组某项，同$arr[$key]，但更安全，且一些时候不用另写一行代码
   */
  public static function get(&$arr, $key)
  {
    if(array_key_exists($key, $arr))
      return $arr[$key];
    return null;
  }

  /**
   * 类似SQL GROUP BY语法
   */
  public static function group($arr, $key)
  {
    $ret = array();
    foreach($arr as $k => $item){
      $val = self::fetchValue($item, $key);
      if(!isset($ret[$val]))
        $ret[$val] = array();
      $ret[$val][$k] = $item;
    }
    return $ret;
  }

  public static function format($arr, $format)
  {
    $ret = array();
    foreach($arr as $key => $item)
      $ret[$key] = sprintf($format, $item);
    return $ret;
  }

  public static function trim($arr)
  {
    $ret = array();
    foreach($arr as $key => $item)
      $ret[$key] = trim($item);
    return $ret;
  }

  public static function intval($arr)
  {
    $ret = array();
    foreach($arr as $key => $item)
      $ret[$key] = intval($item);
    return $ret;
  }

  public static function strval($arr)
  {
    $ret = array();
    foreach($arr as $key => $item)
      $ret[$key] = strval($item);
    return $ret;
  }

  public static function floatval($arr)
  {
    $ret = array();
    foreach($arr as $key => $item)
      $ret[$key] = floatval($item);
    return $ret;
  }

  public static function lower($arr)
  {
    $ret = array();
    foreach($arr as $key => $item)
      $ret[$key] = strtolower($item);
    return $ret;
  }

  public static function upper($arr)
  {
    $ret = array();
    foreach($arr as $key => $item)
      $ret[$key] = strtoupper($item);
    return $ret;
  }

  public static function fix_sort($arr, $keys, $ignore_null = true)
  {
    $ret = array();
    foreach($keys as $key)
      if(!$ignore_null || isset($arr[$key]))
        $ret[$key] = $arr[$key];
    return $ret;
  }

  public static function diff_k($arr, $keys)
  {
    $ret = array();
    foreach ($arr as $k => $v) {
      if (!in_array($k, $keys)) {
        $ret[$k] = $v;
      }
    }
    
    return array_filter($ret);
  }

  public static function key_and_map($arr, $key, $val)
  {
    return self::map(self::key_map($arr, $key), $val);
  }

  public static function setkeys($arr, $key)
  {
    if(is_string($key))
      $key = self::trim(explode(',', $key));

    foreach($arr as &$itm)
      $itm = array_combine($key, $itm);
    return $arr;
  }

  public static function shuffle($arr, $preserve_keys = true)
  {
    if($preserve_keys) {
      $ret = ArrayUtils::magic_link(array_keys($arr), array_values($arr));
      shuffle($ret);
      return ArrayUtils::key_and_map($ret, 0, 1);
    } else {
      shuffle($arr);
      return $arr;
    }
  }

  public static function fetchValue(&$arr, $key)
  {

    if ((is_array($arr) || $arr instanceof Traversable)) {
      return $arr[$key];
    } elseif (is_object($arr) && method_exists($arr, $method = 'get' . str_replace(' ', '', ucwords(str_replace('_', ' ', strtolower($key)))))){
      return $arr->$method();
    } elseif (($arr instanceof stdClass || $arr instanceof SolrObject) && isset($arr->$key)) {
      return $arr->$key;
    } else {
      return $arr;
    }
  }
}
