<?php

class LsSolr
{
  protected static $solrs = array();

  /**
   * Return an solr client instance of given core
   * @param string $core 
   * @return Solr
   */
  public static function getSolr($core)
  {
    if (!isset(self::$solrs[$core])) {
       $options = array(
          'hostname' => 'solr',
          'port'     => '8983',
          'path'     => 'solr/'.$core,
        );

      self::$solrs[$core] = new SolrClient($options);
    }
    return self::$solrs[$core];
  }

  /**
   * do a delta import for given core
   */
  public static function deltaImport($core)
  {
    @file_get_contents("http://solr:8983/solr/$core/dataimport?command=delta-import");
  }

  /**
   * Get all solr cores
   * @return array
   */
  public static function getCores()
  {
    $xml = file_get_contents("http://solr:8983/solr/admin/cores?action=STATUS");
    $doc = simplexml_load_string($xml);
    $cores = array();
    foreach ($doc->lst[1]->lst as $core) {
      $cores[] = (string)$core['name'];
    }

    return $cores;
  }

  /**
   * Reload given core
   */
  public static function reloadCore($core)
  {
    @file_get_contents("http://solr:8983/solr/admin/cores?action=RELOAD&core=$core");
  }

  /**
   * Reload all cores
   */
  public static function reloadAllCores()
  {
    $cores = self::getCores();
    foreach ($cores as $core) {
      self::reloadCore($core);
    }
  }

  /**
   * turn keywords into phrases
   */
  public static function phrasize($keywords)
  {
    $tmp = preg_split("/\s+/", str_replace('"', '', trim($keywords)));

    $phrases = implode('"~3 "', $tmp);
    if ($phrases) {
      $phrases = '"'.$phrases.'"~3';
    }


    return $phrases;

  }

  /**
   * @param SolrQuery $query
   * @param string $field
   * @param mixed $value
   */
  public static function addFilterQuery(SolrQuery $query, $field, $value)
  {
    if (is_array($value)) {
      if (!count($value)) {
        return;
      }
      $value = '(' . implode(' ', $value) . ')';
    }

    if (!$value) return;
    $query->addFilterQuery("$field:$value");
    return $query;
  }
  
  /**
   * Transform given time to solr format
   * @param int|string $time
   * @return string
   */
  public static function solrTime($time)
  {
    if (!is_int($time)) {
      $time = strtotime($time);
    }
    
    return gmdate('Y-m-d\TH:i:s\Z', $time); 
  }
  
  /**
   * Add sort field from given field string with the format 'sort_field desc/asc'
   * @param SolrQuery $query
   * @param string $field
   */
  public static function addSortField(SolrQuery $query, $field)
  {
    list($f, $order) = explode(' ', $field);
    $order = constant('SolrQuery::ORDER_'.strtoupper($order));
    $query->addSortField($f, $order);
    return $query;
  }

  /**
   * 将solr结果集转换成propel对象
   */
  public static function getPropelResult($docs, $modelClass, Criteria $c = null, $peerSelectMethod = 'doSelect', $idKey='id')
  {
    if (!$docs || !count($docs)) {
      return null;
    }
    
    if (is_null($c)) {
      $c = new Criteria();
    }
    
    $ids = array();
    foreach ($docs as $doc) {
      $doc_id = $doc[$idKey];
      if (false !== strstr($doc_id, ':')) {
        // 转换id, news:222 => 222
        $ids[] = substr(strstr($doc_id, ':'), 1);
      } else {
        $ids[] = $doc_id;
      }
    }
    
    $pk = PropelToolkit::getPkColumn($modelClass);
    
    $c->add($pk, $ids, Criteria::IN);
    
    $peerClass = $modelClass . 'Peer';
    
    $result = call_user_func_array(array($peerClass, $peerSelectMethod), array($c));
    
    $data = array();
    foreach ($result as $obj) {
      $data[$obj->getPrimaryKey()] = $obj;
    }
    
    $result = array();
    foreach ($ids as $id) {
      if (isset($data[$id])) {
        $result[] = $data[$id];
      }
    }
    return $result;
  }
}

