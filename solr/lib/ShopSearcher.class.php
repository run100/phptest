<?php
class ShopSearcher
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
    $solr = LsSolr::getSolr('shop');
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
    $keyword = trim($options['query']);
    $options['query'] = $keyword;
    $query = $options['query'] ? LsSolr::phrasize($options['query']) : '*:*';
    $q->setQuery($query);
    $q->set('qf', isset($options['qf']) ? $options['qf'] : 'title');
    $q->addFilterQuery('hidden:0');
    $q->addFilterQuery('cata1:8');
    if(isset($options['vip']) && $options['vip']) {
      $q->addFilterQuery('(member_level:[3 TO *])');
    }
    //排序相关
    //可选排序选项
    $sortMethods = array(
    'default' => array('title' => '默认排序', 'direction' => 'desc', 'solr_field' => 'potential_buyers', 'reversable' => false),
    'comments' => array('title' => '按点评数高低', 'direction' => 'desc', 'solr_field' => 'comments_amount', 'reversable' => true),
    'items' => array('title' => '商品数高低', 'direction' => 'desc', 'solr_field' => 'products_amount', 'reversable' => true),
    'rating' => array('title' => '按星级高低', 'direction' => 'desc', 'solr_field' => 'grade_amount', 'reversable' => true)
    );
    $sorted = $options['sort'];
    if (!isset($sortMethods[$sorted])) {
      $sorted = $options['keyword'] ? 'score': 'default';
    }
    
    
    
    $sortField = isset($sortMethods[$sorted]['solr_field']) ? $sortMethods[$sorted]['solr_field'] : $sorted;
    
   
   
    
    $direction = $options['direction'];
    
   
    
    if ($direction != 'asc' && $direction != 'desc') {
      $direction = $sortMethods[$sorted]['direction'];
    }
//    if (!isset($options['keyword']) && $sorted == 'default') {
//      $q->addSortField('member_level', SolrQuery::ORDER_DESC);
//      $q->addSortField('is_card', SolrQuery::ORDER_DESC);
//    }
    
    if (isset($options['area']) && $options['area']) {
      $distemp = Newshop::getAllDistrict();
      $disname = $distemp[$options['area']];
      $q->addFilterQuery("area:$disname");
    }
    
    if (isset($options['market']) && $options['market']) {
      $marketid = $options['market'];
      $q->addFilterQuery("market_id:".$marketid);
    }
    
  
    
    $q->addSortField($sortField, constant('SolrQuery::ORDER_'.strtoupper($direction)));
    
    $view['sort_methods'] = $sortMethods;
    $view['sort_params'] = array('sort' => $sorted, 'direction' => $direction);
    //faceted search and breadcrumbs
    $q->setFacet(true);
    if(isset($options['cat']) && $options['cat']) {
      $level = self::checkCatLevel($options['cat']);
      $cat = $options['cat'];
      $q->addFilterQuery("cata$level:$cat");
      if($level == 2) $level = 1;
      $q->addFacetField('cata'.($level+1));
    }
    $q->addFacetField('area');
    $q->addFacetField('market_id');
    return $q;
  }

  //根据类别返回层级
  protected static function checkCatLevel($catid) {
    $level = 0;
    $decorCats = array_keys(NewshopPeer::getHomeCategory());
    if(in_array($catid, $decorCats)) {
      $level = 2;
    } else if($catid == NewshopPeer::SORT_HOME) {
      $level = 1;
    }
    return $level;
  }

  /**
     *  所有城市信息
     *
     *  @access public
     *  @return array
     */
  protected function getAnhuiCitys()
  {
    return array_keys(self::getAnhuiCityAndArea());
  }

  /**
     *  城市拼音缩写
     *
     *  @access public
     *  @return array
     */
  protected function getEngCitys()
  {
    return array('合肥'=>'hefei','安庆'=>'anqing','滁州'=>'chuzhou','淮南'=>'huainan','马鞍山'=>'maanshan','宿州'=>'suzhou','芜湖'=>'wuhu','宣城'=>'xuancheng','蚌埠'=>'bengbu','池州'=> 'chizhou','阜阳'=>'fuyang','淮北'=> 'huaibei','黄山'=> 'huangshan','六安'=> 'luan','铜陵'=> 'tongling','亳州'=> 'bozhou');
  }

  /**
     * 返回所有城市
     * @return array
     */
  protected function getCities()
  {
    return array_keys(self::getEngCitys());
  }

  /**
     *  取全部地区拼音缩写
     *
     *  @access public
     *  @return array
     */
  protected function getAllCityAreas()
  {
    return array(
    '合肥'=>'hefei','庐阳区'=>'luyangqu','瑶海区'=>'yaohaiqu','蜀山区'=>'shushanqu','包河区'=>'baohequ','新站区'=>'xinzhanqu','政务区'=>'zhengwuqu','高新区'=>'gaoxinqu','经开区'=>'jingkaiqu','滨湖区'=>'binhuqu','肥东县'=>'feidongxian','肥西县'=>'feixixian','长丰县'=>'changfengxian','庐江县'=>'lujiangxian','巢湖市'=>'chaohushi',
    '芜湖'=>'wuhu','镜湖区'=>'jinghuqu','弋江区'=>'jiangqu','鸠江区'=>'jiangqu','三山区'=>'sanshanqu','芜湖县'=>'wuhuxian','繁昌县'=>'fanchangxian','南陵县'=>'nanlingxian','无为县'=>'wuweixian',
    '蚌埠'=>'bangbu','龙子湖区'=>'longzihuqu','蚌山区'=>'bangshanqu','禹会区'=>'yuhuiqu','淮上区'=>'huaishangqu','怀远县'=>'huaiyuanxian','五河县'=>'wuhexian','固镇县'=>'guzhenxian',
    '淮南'=>'huainan','大通区'=>'datongqu','田家庵区'=>'tianjiaqu','谢家集区'=>'xiejiajiqu','八公山区'=>'bagongshanqu','潘集区'=>'panjiqu','凤台县'=>'fengtaixian',
    '马鞍山'=>'maanshan','金家庄区'=>'jinjiazhuangqu','花山区'=>'huashanqu','雨山区'=>'yushanqu','当涂县'=>'dangtuxian','和县'=>'hexian','含山县'=>'hanshanxian',
    '淮北'=>'huaibei','杜集区'=>'dujiqu','相山区'=>'xiangshanqu','烈山区'=>'lieshanqu','濉溪县'=>'xixian',
    '铜陵'=>'tongling','铜官山区'=>'tongguanshanqu','狮子山区'=>'shizishanqu','郊区'=>'jiaoqu','铜陵县'=>'tonglingxian',
    '安庆'=>'anqing','迎江区'=>'yingjiangqu','大观区'=>'daguanqu','宜秀区'=>'yixiuqu','怀宁县'=>'huainingxian','枞阳县'=>'yangxian','潜山县'=>'qianshanxian','太湖县'=>'taihuxian','宿松县'=>'susongxian','望江县'=>'wangjiangxian','岳西县'=>'yexixian','桐城'=>'tongcheng',
    '黄山'=>'huangshan','屯溪区'=>'tunxiqu','黄山区'=>'huangshanqu','徽州区'=>'huizhouqu','歙县'=>'xian','休宁县'=>'xiuningxian','黟县'=>'xian','祁门县'=>'qimenxian',
    '滁州'=>'chuzhou','琅琊区'=>'langqu','南谯区'=>'nanqu','来安县'=>'laianxian','全椒县'=>'quanjiaoxian','定远县'=>'dingyuanxian','凤阳县'=>'fengyangxian','天长市'=>'tianchangshi','明光市'=>'mingguangshi',
    '阜阳'=>'fuyang','颍州区'=>'zhouqu','颍东区'=>'dongqu','颍泉区'=>'quanqu','临泉县'=>'linquanxian','太和县'=>'taihexian','阜南县'=>'funanxian','颍上县'=>'shangxian','界首市'=>'jieshoushi',
    '宿州'=>'suzhou','埇桥区'=>'qiaoqu','砀山县'=>'shanxian','萧县'=>'xiaoxian','灵璧县'=>'lingxian','泗县'=>'xian',
    '六安'=>'liuan','金安区'=>'jinanqu','裕安区'=>'yuanqu','寿县'=>'shouxian','霍邱县'=>'huoqiuxian','舒城县'=>'shuchengxian','金寨县'=>'jinzhaixian','霍山县'=>'huoshanxian',
    '亳州'=>'zhou','谯城区'=>'chengqu','涡阳县'=>'woyangxian','蒙城县'=>'mengchengxian','利辛县'=>'lixinxian',
    '池州'=>'chizhou','贵池区'=>'guichiqu','东至县'=>'dongzhixian','石台县'=>'shitaixian','青阳县'=>'qingyangxian',
    '宣城'=>'xuancheng','宣州区'=>'xuanzhouqu','郎溪县'=>'langxixian','广德县'=>'guangdexian','泾县'=>'xian','绩溪县'=>'jixixian','旌德县'=>'dexian','宁国市'=>'ningguoshi');
  }
  
  protected function processFilters(SolrQuery $query)
    {
        $filtersConfig = array(
            '万家卡优惠'    => array('field' => 'is_card'),
            '推荐商家'      => array('field' => 'recommended'),
            '有促销'        => array('field' => 'promotion_news_id', 'int' => true), 
            '有优惠券'      => array('field' => 'coupon_id', 'int' => true),
            '售卡点'        => array('field' => 'wan_card_sale'),
            '信誉商家'      => array('field' => 'is_adv'),
            '亲子联盟'      => array('field' => 'is_parenting_alliance'),
            '装修联盟'      => array('field' => 'is_decoration_alliance'),
            '结婚联盟'      => array('field' => 'is_marriage_alliance'),
        );
        
        $extraFilters = array('可刷卡', '可预订', '有包厢', '有停车位', '可外送', '可送货',
            '可分期付款', '可安装', '有儿童座椅','有连锁店', '有自助餐点', '可医保刷卡',
            '有自助设备', '可上网');
        
        $filters = $this->getRequest()->get('filters');
        if (!count($filters)) {
            return;
        }
        
        $fqs = array();
        $extra = array();
        foreach ($filters as $filter) {
            if (isset($filtersConfig[$filter])) {
                $conf = $filtersConfig[$filter];
                $fq = $conf['field'].':';
                $fq .= isset($conf['int']) ? '[1 TO *]' : '1';
                $fqs[] = $fq;
            } elseif (in_array($filter, $extraFilters)) {
                $extra[] = $filter;
            }
        }
        
        if (count($extra)) {
            $fqs[] = 'extra:('.implode(' OR ', $extra).')';
        }
        
        if (count($fqs)) {
            $query->addFilterQuery(implode(' OR ', $fqs));
        }
    }
  
}