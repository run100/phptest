<?php

class NewsSearcher
{
  /**
   * Search news from solr
   * Avaiable options:
   *   - query: the query string
   *   - qf: the query field, default to title
   *   - rows: how many rows would be retrieved
   *   - start
   *   - catalogs: an array of catalog ids
   *   - show_all: include hidden records in the result, default to false
   *   - sort: e.g. 'published_at desc'
   *   - return_ids: return ids only, default to true
   *   - func: a custom function to process SolrQuery
   *   - return_propel_result: return propel objects
   *
   * @param array $params
   * @return arary
   */
  public static function search($options)
  {
    $solr = LsSolr::getSolr('news');

    $q = self::createQuery($options);

    $q->setRows(isset($options['rows']) ? $options['rows'] : 10);
    $q->setStart(isset($options['start']) ? $options['start'] : 0);

    if (isset($options['return_propel_result']) && $options['return_propel_result']) {
      $q->addField('id');
    }

    $result = $solr->query($q)->getResponse()->response;

    if (isset($options['return_propel_result']) && $options['return_propel_result']) {
      return LsSolr::getPropelResult($result['docs'], 'News');
    }

    if (isset($options['return_ids']) && !$options['return_ids']) {
      return $result;
    }

    if (!$result['numFound']) {
      return array();
    }
    $ids = array();
    foreach ($result['docs'] as $doc) {
      $ids[] = self::getNewsId($doc['id']);
    }

    return $ids;
  }

  public static function getSolrId($news_id)
  {
    return "\"news:{$news_id}\"";
  }
  public static function getNewsId($solr_id)
  {
    return (int)str_replace(array('news:', '"'), array('', ''), $solr_id);
  }


  /**
   * Search news from solr
   * Avaiable options:
   *   - query: the query string
   *   - qf: the query field, default to title
   *   - catalogs: an array of catalog ids
   *   - show_all: include hidden records in the result, default to false
   *   - sort: e.g. 'published_at desc'
   *   - return_ids: return ids only, default to true
   *   - func: a custom function to process SolrQuery
   *
   * @param array $params
   * @return SolrQuery
   */

  public static function createQuery($options)
  {
    $q = new SolrQuery();
    $q->set('defType', 'edismax');
    $query = @trim($options['query']) ?: '*:*';

    //查询结果不包括此id
    if (isset($options['exclude_id']) && $options['exclude_id']) {
    	$query .= ' -id:'.self::getSolrId($options['exclude_id']);
    }


    $q->setQuery($query);

    $q->set('qf', isset($options['qf']) ? $options['qf'] : 'title');


    if (!isset($options['show_all']) || !$options['show_all']) {
      $q->addFilterQuery("hidden:0");
    }

    //推荐、精华
    if (isset($options['digest']) && $options['digest']) {
      $q->addFilterQuery("digest:1");
    }
    if (isset($options['keyword']) && $options['keyword']){
      $q->setQuery("keyword:".$options['keyword']);
    }

    if (isset($options['catalogs'])) {
      $options['catalogs'] = (array)$options['catalogs'];
      array_walk($options['catalogs'], function(&$v, $k) { $v = intval($v); });
      LsSolr::addFilterQuery($q, 'catalog_id', $options['catalogs']);
    }

    if (isset($options['shop_ids'])) {
      $options['shop_ids'] = (array)$options['shop_ids'];
      array_walk($options['shop_ids'], function(&$v, $k) { $v = intval($v); });
      LsSolr::addFilterQuery($q, 'shop_id', $options['shop_ids']);
    }

    if (isset($options['sort'])) {
      if (is_array($options['sort'])) {
        foreach ($options['sort'] as $sort) {
          LsSolr::addSortField($q, $sort);
        }
      } else {
        LsSolr::addSortField($q, $options['sort']);
      }
    } else {
      $q->addSortField('score', SolrQuery::ORDER_DESC);
    }

    if (isset($options['is_video_news'])) {
      $q->addFilterQuery('is_video_news:'.($options['is_video_news'] ? 1 : 0));
    }

    if (isset($options['is_discount_news'])) {
      $q->addFilterQuery('is_discount_news:'.($options['is_discount_news'] ? 1 : 0));
    }

    if (isset($options['from_shop_admin'])) {
      $q->addFilterQuery('from_shop_admin:'.($options['from_shop_admin'] ? 1 : 0));
    }

    if (isset($options['is_img_news']) && $options['is_img_news']) {
      $q->addFilterQuery('is_img_news:1');
      $q->addFilterQuery('(media_id:[1 TO *])');
    }

    if (isset($options['is_special']) && $options['is_special']) {
      $q->addFilterQuery('(special_id:[1 TO *])');
    }

    if (isset($options['house_id'])) {
      $q->addFilterQuery('house_id:'.intval($options['house_id']));
    }
    
    if (isset($options['market_id'])) {
      $q->addFilterQuery('market_id:'.intval($options['market_id']));
    }
    
    if (isset($options['decormarket_id'])) {
      $q->addFilterQuery('decormarket_id:'.intval($options['decormarket_id']));
    }
    
    if (isset($options['func']) && is_callable($options['func'])) {
      $func = $options['func'];
      $func($q);
    }
    
    if (isset($options['not_news_ids']) && $options['not_news_ids']) {   
      $fqs[] = '-news_id:('.implode(' OR ', $options['not_news_ids']).')';
      $q->addFilterQuery(implode(' OR ', $fqs));
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
