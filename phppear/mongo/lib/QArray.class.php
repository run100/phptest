<?php

class QArray extends ArrayObject
{
  static function make($source = null, $to_array = false)
  {
    if (is_null($source))
      return new self();
    if ((is_array($source) || $source instanceof Traversable || $source instanceof MongoCursor) && is_bool($to_array))
      if ($to_array)
        return new self(ArrayUtils::to_array($source));
      else
        return new self($source);
    if (is_a($source, __CLASS__))
      return new self($source);

    return new self(func_get_args());
  }

  function unshift()
  {
    $arr = (array)$this;
    $params = func_get_args();
    array_unshift($params, $arr);
    call_user_func_array('array_unshift', $params);
    return self::make($arr);
  }

  function shift(&$item)
  {
    $arr = (array)$this;
    $item = array_shift($arr);
    return self::make($arr);
  }

  function push()
  {
    $arr = (array)$this;
    $params = func_get_args();
    array_unshift($params, $arr);
    call_user_func_array('array_push', $params);
    return self::make($arr);
  }

  function pop(&$item)
  {
    $arr = (array)$this;
    $item = array_pop($arr);
    return self::make($arr);
  }

  function obj_arr()
  {
    return new self(ArrayUtils::to_array($this));
  }

  function to_array()
  {
    return (array)$this;
  }

  function mask($keys)
  {
    return self::make(ArrayUtils::map((array)$this, $keys));
  }

  function combine($arr = array())
  {
    if(!$size = count((array)$this)) {
      return array();
    }

    if(is_array($arr) || $arr instanceof Traversable) {
      if(count((array)$arr) != $size) {
        $arr = array_slice(array_pad((array)$arr, $size, null), 0, $size);
      }
    } else {
      $arr = array_slice(array_pad(array(), $size, $arr), 0, $size);
    }
    return QArray::make(array_combine((array)$this, $arr));
  }

  function trim()
  {
    return $this->map('trim');
  }

  function format($format)
  {
    return $this->map(
      function($v) use($format)
      {
        return sprintf($format, $v);
      });
  }

  function unique($sort_flags= SORT_REGULAR)
  {
    return self::make(array_unique((array)$this, $sort_flags));
  }

  function str_join($sep = ', ')
  {
    return implode($sep, (array)$this);
  }

  function join($arr, $on, $alias = 'joined')
  {
    $arr = self::make($arr);
    if(!is_callable($on) && is_string($on) && !$arr->has_subkey($on))
      $on = self::make_func($on);
    return self::make(ArrayUtils::join((array)$this, (array)$arr, $on, $alias));
  }

  function ljoin($arr, $on, $alias = 'joined')
  {
    $arr = self::make($arr);
    if(!is_callable($on) && is_string($on) && !$arr->has_subkey($on))
      $on = self::make_func($on);
    return self::make(ArrayUtils::ljoin((array)$this, (array)$arr, $on, $alias));
  }

  function step($len)
  {
    return self::make(ArrayUtils::step((array)$this, $len));
  }

  function group($key)
  {
    return self::make(ArrayUtils::group((array)$this, $key));
  }

  function set_sort($keys, $ignore_null = true)
  {
    return self::make(ArrayUtils::fix_sort((array)$this, $keys, $ignore_null));
  }

  function set_keys($keys)
  {
    return self::make(ArrayUtils::setkeys((array)$this, $keys));
  }

  function set_pk($key)
  {
    return self::make(ArrayUtils::key_map((array)$this, $key));
  }

  function k_v($key, $val)
  {
    return self::make(ArrayUtils::key_and_map((array)$this, $key, $val));
  }

  function grep($pattern, $flags= false)
  {
    return self::make(preg_grep($pattern, (array)$this, $flags));
  }

  function grep_k($pattern, $flags= false)
  {
    $keys = preg_grep($pattern, (array)$this->keys(), $flags);
    return $this->set_sort($keys);
  }

  function swap()
  {
    return self::make(ArrayUtils::magic_extract((array)$this));
  }

  function build_offspring($keyname = 'parent_id')
  {
    return self::make(ArrayUtils::build_offspring((array)$this, $keyname));
  }

  function reverse($preserve_keys= false)
  {
    return self::make(array_reverse((array)$this, $preserve_keys));
  }

  function flip()
  {
    return self::make(array_flip((array)$this));
  }

  function sort_by($key, $sort = 'ASC')
  {
    if(strtoupper($sort) == 'ASC')
      $sorting =
        function ($_a, $_b) use($key){
          return (!$key ? $_a : ArrayUtils::fetchValue($_a, $key)) > (!$key ? $_b : ArrayUtils::fetchValue($_b, $key));
        };
    elseif(strtoupper($sort) == 'DESC')
      $sorting =
        function ($_a, $_b) use($key){
          return (!$key ? $_a : ArrayUtils::fetchValue($_a, $key)) < (!$key ? $_b : ArrayUtils::fetchValue($_b, $key));
        };
    return $this->usort($sorting);
  }

  function sort($sort = 'ASC', $sort_flags = SORT_REGULAR)
  {
    $ret = (array)$this;
    if(strtoupper($sort) == 'ASC')
      sort($ret, $sort_flags);
    elseif(strtoupper($sort) == 'DESC')
      rsort($ret, $sort_flags);
    return self::make($ret);
  }

  function ksort($sort = 'ASC', $sort_flags = SORT_REGULAR)
  {
    $keys = (array)$this->keys();
    if(strtoupper($sort) == 'ASC')
      ksort($keys, $sort_flags);
    elseif(strtoupper($sort) == 'DESC')
      krsort($keys, $sort_flags);
    return $this->set_sort($keys);
  }

  function usort($func)
  {
    if (is_string($func) && !is_callable($func))
      $func = self::make_func($func);

    $ret = (array)$this;
    usort($ret, $func);
    return self::make($ret);
  }

  function uksort($func)
  {
    if (is_string($func) && !is_callable($func))
      $func = self::make_func($func);

    $ret = (array)$this;
    uksort($ret, $func);
    return self::make($ret);
  }

  function keys()
  {
    return new self(array_keys((array)$this));
  }

  function values()
  {
    return new self(array_values((array)$this));
  }

  function get($key, $default = null)
  {
    return array_key_exists($key, (array)$this) ? $this[$key] : $default;
  }

  function has($value)
  {
    return in_array($value, (array)$this);
  }

  function get_key($value)
  {
    return array_search($value, (array)$this);
  }

  function has_key($key)
  {
    return array_key_exists($key, (array)$this);
  }

  function has_subkey()
  {
    if(count($this))
      return array_key_exists($key, $this[0]);
    else
      return false;
  }

  function has_key_value($key, $value)
  {
    return array_key_exists($key, (array)$this) && $this[$key] == $value;
  }

  function slice($offset, $length = null, $preserve_keys = false)
  {
    return new self(array_slice((array)$this, $offset, $length, $preserve_keys));
  }

  function skip($offset)
  {
    return $this->slice($offset);
  }

  function limit($length)
  {
    return $this->slice(0, $length);
  }

  function diff($arr)
  {
    return new self(array_diff((array)$this, (array)$arr));
  }

  function diff_key($arr)
  {
    return new self(array_diff_key((array)$this, (array)$arr));
  }

  function diff_k($keys)
  {
    return new self(ArrayUtils::diff_k((array)$this, (array)$keys));
  }

  function intersect($arr)
  {
    return new self(array_intersect((array)$this, (array)$arr));
  }

  function intersect_key($arr)
  {
    return new self(array_intersect_key((array)$this, (array)$arr));
  }

  /**
   * Wrapper for array_merge($this, $array)
   * @param self $array
   * @return self
   */
  function merge($array = null)
  {
    return new self(sfToolkit::arrayDeepMerge((array)$this, (array)$array));
  }

  function shuffle($preserve_keys = true)
  {
    return QArray::make(ArrayUtils::shuffle((array)$this, $preserve_keys));
  }

  /**
   * Wrapper for array_merge($array, $this) - reversed arguments
   * @param self $array
   * @return self
   */
  function merge_into($array = null)
  {
    return new self(array_merge((array)$array, (array)$this));
  }

  function implode($glue)
  {
    return implode($glue, (array)$this);
  }

  function flatten()
  {
    $res = self::make();
    foreach ($this as $elem)
      if (is_array($elem))
        $res = $res->merge(self::make($elem)->flatten());
      elseif (is_a($elem, __CLASS__))
        $res = $res->merge($elem->flatten());
      else
        $res[] = $elem;
    return $res;
  }

  function walk($func)
  {
    if (is_string($func) && !is_callable($func))
      $func = self::make_func($func, true);

    if(is_string($func) || is_array($func) || is_callable($func)) {
      $ret = (array)$this;
      foreach ($ret as $k => &$v)
        call_user_func_array($func, array(&$v));
      return self::make($ret);
    }
    return $this;
  }

  function each($func)
  {
    return self::walk($func);
  }

  function map($func)
  {
    $r = new self();
    foreach ($this as $k => $v)
      $r[$k] = $this->call_func($func, $v);
    return $r;
  }

  function filter($func = null)
  {
    if($func === null) {
      return QArray::make(array_filter((array)$this));
    }

    $r = new self();
    foreach ($this as $k => $v)
      if ($this->call_func($func, $v))
        $r[$k] = $v;
    return $r;
  }

  function reduce($init, $func)
  {
    $r = $init;
    foreach ($this as $k => $v)
      $r = $this->call_func($func, $v, $r);
    return $r;
  }

  function first($func)
  {
    foreach ($this as $k => $v)
      if ($this->call_func($func, $v))
        return $v;
    return null;
  }

  function first_key($func)
  {
    foreach ($this as $k => $v)
      if ($this->call_func($func, $v))
        return $k;
    return null;
  }

  function first_v($default = null)
  {
    $qarray = (array)$this;
    foreach ($qarray as $array) {
      foreach ($array as $value) {
        return $value;
      }
    }

    return $default;
  }


  /**
   * 将内部数组的前两个值以 key => value 形式返回.
   * @return array
   */
  function vv_kv()
  {
    $qarray = (array)$this;
    $na = array();
    foreach ($qarray as $array) {
      $vals = array_values($array);
      $na[$vals[0]] = $vals[1];
    }
    return $na;
  }

  function vv()
  {
    $qarray = (array)$this;
    $vals = array_values(array_shift($qarray));
    return $vals[0];
  }


  function for_any($func)
  {
    foreach ($this as $k => $v)
      if ($this->call_func($func, $v))
        return true;
    return false;
  }

  function for_all($func)
  {
    foreach ($this as $k => $v)
      if (! $this->call_func($func, $v))
        return false;
    return true;
  }

  function walk_k($func)
  {
    if (is_string($func) && !is_callable($func))
      $func = self::make_func($func, true);

    if(is_string($func) || is_array($func) || is_callable($func)) {
      $ret = array();
      foreach ($this as $k => &$v) {
        $_k = $k;
        call_user_func_array($func, array(&$_k, &$v));
        $ret[$_k] = $v;
      }
      return self::make($ret);
    }
  }

  function each_k($func)
  {
    return self::walk_k($func);
  }

  function map_k($func)
  {
    $r = new self();
    foreach ($this as $k => $v)
      $r[$k] = $this->call_func($func, $k, $v);
    return $r;
  }

  function filter_k($func)
  {
    $r = new self();
    foreach ($this as $k => $v)
      if ($this->call_func($func, $k, $v))
        $r[$k] = $v;
    return $r;
  }

  function reduce_k($init, $func)
  {
    $r = $init;
    foreach ($this as $k => $v)
      $r = $this->call_func($func, $k, $v, $r);
    return $r;
  }

  function first_k($func)
  {
    foreach ($this as $k => $v)
      if ($this->call_func($func, $k, $v))
        return $v;
    return null;
  }

  function first_key_k($func)
  {
    foreach ($this as $k => $v)
      if ($this->call_func($func, $k, $v))
        return $k;
    return null;
  }

  function for_any_k($func)
  {
    foreach ($this as $k => $v)
      if ($this->call_func($func, $k, $v))
        return true;
    return false;
  }

  function for_all_k($func)
  {
    foreach ($this as $k => $v)
      if (! $this->call_func($func, $k, $v))
        return false;
    return true;
  }

  private function call_func()
  {
    $args = func_get_args();
    $func = array_shift($args);
    if(is_callable($func))
      return call_user_func_array($func, $args);
    elseif(is_string($func)) {
      $func = self::make_func($func);
      return call_user_func_array($func, $args);
    }
    return false;
  }

  private static function make_func($body, $refer = false)
  {
    static $lamdas = array();
    $key = md5($body);
    if(isset($lamdas[$key]))
      return $lamdas[$key];

    $body = trim($body);
    if(!preg_match('#(?:;|\})$#', $body))
      $body .= ';';

    if(!preg_match('#^(:?\{|(?:return ))#', $body) && !$refer)
      $body = 'return ' . $body;

    $vars = '';

    if(strstr($body, '$_k') !== false)
      $vars .= ($refer ? '&' : '') . '$_k, ';

    if(strstr($body, '$_v') !== false)
      $vars .= ($refer ? '&' : '') . '$_v';

    if(strstr($body, '$_a') !== false)
      $vars .= ($refer ? '&' : '') . '$_a, ';

    if(strstr($body, '$_b') !== false)
      $vars .= ($refer ? '&' : '') . '$_b';

    return $lamdas[$key] = create_function($vars, $body);
  }
}
