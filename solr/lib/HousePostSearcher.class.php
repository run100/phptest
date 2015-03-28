<?php

class HousePostSearcher
{
  /**
   * Search house_post from solr 
   * Avaiable options:
   *   - query: the query string
   *   - exclude_id
   *   - qf: the query field, default to title
   *   - post_type: an array of catalog ids
   *   - show_all: include hidden records in the result, default to false
   *   - sort: e.g. 'updated_at desc'
   *   - return_ids: return ids only, default to true
   *   - price_range
   *
   * @param array $params
   * @return arary
   */
  public static function search($options)
  {
    $solr = LsSolr::getSolr('house_post');

    $q = self::createQuery($options);
    
    $q->setRows(isset($options['rows']) ? $options['rows'] : 10);
    $q->setStart(isset($options['start']) ? $options['start'] : 0);
    
    if (isset($options['return_propel_result']) && $options['return_propel_result']) {
      $q->addField('id');
    }


    $result = $solr->query($q)->getResponse()->response;
    
    
    if (isset($options['return_propel_result']) && $options['return_propel_result']) {
      return LsSolr::getPropelResult($result['docs'], 'HousePost');
    }
   
    if (isset($options['return_ids']) && !$options['return_ids']) {
      return $result;
    }

    if (!$result['numFound']) {
      return array();
    }
    
    
    $ids = array();
    foreach ($result['docs'] as $doc) {
      $ids[] = self::getId($doc['id']);
    }

    return $ids;
  }

  public static function getSolrId($news_id)
  {
    return "hp:{$news_id}";
  }

  public static function getId($solr_id)
  {
    return (int)str_replace('hp:', '', $solr_id);
  }
  

  /**
   * Search house_post from solr 
   * Avaiable options:
   *   - query: the query string
   *   - exclude_id
   *   - qf: the query field, default to title
   *   - post_type: an array of catalog ids
   *   - show_all: include hidden records in the result, default to false
   *   - sort: e.g. 'published_at desc'
   *   - return_ids: return ids only, default to true
   *   - price_range
   *
   * @param array $params
   * @return SolrQuery
   */

  public static function createQuery($options)
  {
    $q = new SolrQuery();
    $q->set('defType', 'edismax');

    $options['query'] = trim($options['query']);
    
    $query = $options['query'] ? LsSolr::phrasize($options['query']) : '*:*';

    //查询结果不包括此id
    if ($options['exclude_id']) {
      $query .= ' -id:"'.self::getSolrId($options['exclude_id']).'"';
    }


    $q->setQuery($query);

    $q->set('qf', isset($options['qf']) ? $options['qf'] : 'title');

    
    if (!isset($options['show_all']) || !$options['show_all']) {
      $q->addFilterQuery("hidden:0");
      $q->addFilterQuery("(expire_date:[NOW TO *])");
    }

    if (isset($options['sort'])) {
      LsSolr::addSortField($q, $options['sort']);
    } else {
      $q->addSortField('score', SolrQuery::ORDER_DESC);
    }

    if (isset($options['post_type']) && $options['post_type'] && intval($options['post_type'])) {
      $q->addFilterQuery('post_type:'.$options['post_type']);
    }
    
    if (isset($options['district']) && $options['district'] && intval($options['district'])) {
      $q->addFilterQuery('district:'.$options['district']);
    }
    
    if (isset($options['has_media']) && $options['has_media'] && intval($options['has_media'])) {
      $q->addFilterQuery('media_id:([1 TO *])');
    }
   
    if (isset($options['user_id']) && $options['user_id'] && intval($options['user_id'])) {
      $q->addFilterQuery('user_id:'.$options['user_id']);
    }   
    
    if (isset($options['cid']) && $options['cid'] && intval($options['cid'])) {
      $q->addFilterQuery('community_id:'.$options['cid']);
    } 
    
    if (isset($options['is_single']) && $options['is_single'] && intval($options['is_single'])) {
      $q->addFilterQuery('from_agent:0');
    }
    
    if (isset($options['from_agent']) && $options['from_agent'] && intval($options['from_agent'])) {
      $q->addFilterQuery('from_agent:1');
    }
    
    if (isset($options['decorating_degree']) && $options['decorating_degree'] && intval($options['decorating_degree'])) {
      $q->addFilterQuery('decorating_degree:'.$options['decorating_degree']);
    }
    
    if (isset($options['created_at']) && $options['created_at']) {
      if($date_from = gmdate('Y-m-d\TH:i:s\Z', time() - $options['created_at'] * 86400)) {
        $q->addFilterQuery("created_at:" . "([$date_from TO NOW])");
      }
    }
    
    
    $house_years = $options['house_years'];
    $type = $options['type'];
    if ($house_years && ($type == HousePostPeer::POST_TYPE_BUY || $type == HousePostPeer::POST_TYPE_SELL) && isset(HousePostPeer::$house_years_search[$house_years])){
      list($age_min, $age_max) = HousePostPeer::$house_years_search[$house_years];
    } elseif ($age_min || $age_max) {
      $age_min = $age_min ?: '*';
      $age_max = $age_max ?: '*';
    }
  
    if ($age_min) {
  		$q->addFilterQuery("building_age:([$age_min TO $age_max])");
    }
    
    if (isset($options['bedroom_num']) && $options['bedroom_num'] && intval($options['bedroom_num'])) {
      $q->addFilterQuery("bedroom_num:".$options['bedroom_num']);
    }
    
    $rental_type = $options['rental_type'];
    $gender_limit_filter = $options['gender_limit_filter'];
    
    if ($rental_type && intval($rental_type)) {
      $q->addFilterQuery("rental_type:".$rental_type);
    }
    
    if ($rental_type == HousePostPeer::RENTAL_TYPE_SHARING && $gender_limit_filter) {
      $q->addFilterQuery("gender_limit:".$gender_limit_filter);
    }
    
    $house_area = intval($options['house_area']);
    $floor_id = intval($options['floor_id']);
    
    //面积
    if ($house_area && isset(HousePostPeer::$house_area_rent_list[$house_area])) {
      list($area_min, $area_max) = HousePostPeer::$house_area_rent_list[$house_area];     
    } elseif ($area_min || $area_max) {
      $area_min = $area_min ?: '*';
      $area_max = $area_max ?: '*';
    }
    
    if ($area_max) {
      $q->addFilterQuery("area:"."([$area_min TO $area_max])");
    }
    
    //楼层
    if (intval($floor_id) && isset(HousePostPeer::$floor_range_search[$floor_id])) {
      	list($floor_in_min, $floor_in_max) = HousePostPeer::$floor_range_search[$floor_id];
    }
 
    
    if ($floor_in_min || $floor_in_max) {
      $floor_in_min = $floor_in_min ?: '*';
      $floor_in_max = $floor_in_max ?: '*';
    }
  	
    if ($floor_in_min && $floor_in_max) {
      $q->addFilterQuery("floor_in:" . "([$floor_in_min TO $floor_in_max])");
    }
    
    $agent = $options['agent'];
    $brokerIds = $options['brokerIds'];
    $brokerid = $options['brokerid'];
    if ($agent && intval($agent)) {
      $q->addFilterQuery("house_agency_id:" . $agent);
    }
    
    if ($brokerIds && intval($brokerIds)) {
    	$q->addFilterQuery("house_broker_id:" . $brokerIds);
    } elseif($brokerid && intval($brokerid)) {
    	$q->addFilterQuery("house_broker_id:" . $brokerid);
    }
    
   	$business_centre = $options['business_centre'];
   	if ($business_centre && intval($business_centre)) {
      $q->addFilterQuery("business_centre:" . $business_centre);
    }
    
    $property_type = $options['property_type'];
    if ($property_type && intval($property_type)) {
      $q->addFilterQuery("property_type:" . $property_type);
    }
    
    if (isset($options['geo'])) {
      $q->set('sfield', 'latlng');
      $q->set('pt', $options['geo']['point']);
      $q->addFilterQuery('{!geofilt}');
      $q->set('d', $options['geo']['distance']);
    }


    //价格区间应该是一个数组 array('min' => $min, 'max' => $max);
    
    if (isset($options['price_range']) && is_array($options['price_range'])) {
      $minPrice = $options['price_range']['min'] && intval($options['price_range']['min']) ? $options['price_range']['min'] : '*';
      $maxPrice = $options['price_range']['max'] && intval($options['price_range']['max']) ? $options['price_range']['max'] : '*';
      
      if ($minPrice !== '*' || $maxPrice !== '*') {
        $q->addFilterQuery("(price:[$minPrice TO $maxPrice])");
      }
    }
    
    if (isset($options['func']) && is_callable($options['func'])) {
      $func = $options['func'];
      $func($q);
    }

    if (isset($options['ids']) && is_array($options['ids'])) {
      $ids = array();
      foreach ($options['ids'] as &$id) {
        $ids[] = self::getSolrId($id);
      }

      LsSolr::addFilterQuery($q, 'id', $ids);
    }
    
    return $q;
  }
}
