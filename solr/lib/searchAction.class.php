<?php

sfLoader::loadHelpers('LsUrl');

class searchAction extends sfAction
{
  public function execute()
  {
    //取分类id与名称数组
    $this->cates = TypeUtils::getSubTypes(FuwuCategoryPeer::FL_ROOT);
    $this->has_media = htmlspecialchars($this->getRequestParameter('has_media', 0));
    $rootCategory = intval($this->getRequestParameter('rootCategory'));

    

    $subCategory = intval($this->getRequestParameter('subCategory'));
    $thirdCategory = intval(trim($this->getRequestParameter('thirdCategory')));
    $page = intval($this->getRequestParameter('page', 1));
    
    $demand = htmlspecialchars($this->getRequestParameter('demand'));
    $district = htmlspecialchars($this->getRequestParameter('district'));
    $searchTime = htmlspecialchars($this->getRequestParameter('searchTime'));
    $role = htmlspecialchars($this->getRequestParameter('role'));
    $user_id = intval($this->getRequestParameter('user'));
    $keyword = htmlspecialchars($this->getRequestParameter('keyword'));
    $show = htmlspecialchars($this->getRequestParameter('show'));
    $pos_keyword = htmlspecialchars(urldecode($this->getRequestParameter('pos_keyword')));
    $phone = htmlspecialchars($this->getRequestParameter('phone'));
    if($phone && !preg_match('/^\d{11}$/', $phone)) {
      $phone = "null";
    }

    $photo = htmlspecialchars($this->getRequestParameter('photo'));//交友首页搜索用
    $age = htmlspecialchars($this->getRequestParameter('age'));//交友首页搜索用
    $cert = htmlspecialchars($this->getRequestParameter('cert'));//交友首页搜索用
    
    //二手车相关参数
    $this->autobrand = htmlspecialchars($this->getRequestParameter('auto_brand'));//品牌
    $this->autoseries = htmlspecialchars($this->getRequestParameter('auto_series'));
    $this->autoprice = htmlspecialchars($this->getRequestParameter('auto_price'));
    $this->autoage = htmlspecialchars($this->getRequestParameter('auto_age'));
    $this->automileage = htmlspecialchars($this->getRequestParameter('auto_mileage'));
    
    $s = htmlspecialchars($this->getRequestParameter('s'));
    if($s) {
      $params = explode('_', $s);
      $i = 1;
      $var_key = array();
      $val_val = array();
      foreach($params as $value) {
        if($i%2==0) $val_val[] = $value;
        else  $var_key[] = $value;
        $i++;
      }
      if(count($var_key) != count($val_val)) array_pop($var_key);
      
      if(count($var_key)>=1) {
        $vals =  array_combine($var_key, $val_val);
        $s_var = sfConfig::get('s_var');
        foreach($s_var as $v=>$sv) {
          if ($sv != 'k'){
            if($vals[$sv]) $$v = strip_tags($vals[$sv]);
          }elseif (!$keyword) {
            if($vals[$sv]) $$v = strip_tags($vals[$sv]);
          }
          
        }
      }
    }
    if(!$page) $page = 1;
    if($autobrand) $this->autobrand = $autobrand;
    if($autoseries) $this->autoseries = $autoseries;
    if($autoprice) $this->autoprice = $autoprice;
    if($autoage) $this->autoage = $autoage;
    if($automileage) $this->automileage = $automileage;
    
    if ($rootCategory) $this->cates = $this->cates + TypeUtils::getSubTypes($rootCategory);
    if ($subCategory) $this->cates = $this->cates + TypeUtils::getSubTypes($subCategory);
    
    //{{{ 处理类别不匹配的情况
    $error_url = false;
    if($thirdCategory) {
      $sub_cates = TypeUtils::getSubTypes($subCategory);
      if(!$sub_cates[$thirdCategory]) {
        $error_url = true;
        $type = CommonTypePeer::retrieveByPK($thirdCategory);
        if($type) $real_sub_category = $type->getParentId();
      }
    }
    
    if ($subCategory) {
      if($real_sub_category) $subCategory = $real_sub_category;
      $sub_cates = TypeUtils::getSubTypes($rootCategory);
      if($error_url || !$sub_cates[$subCategory]) {
        $error_url = true;
        $type = CommonTypePeer::retrieveByPK($subCategory);
        if($type) $real_root_category = $type->getParentId();
        $real_sub_category = $subCategory;
      }
    }
    
    if($error_url) {
      return $this->redirect('@homepage');
      /*
      $this->redirectUnless($real_root_category, '@homepage');
      $url = sfConfig::get('sub_site_fl_url')."/cates";
      $url .= "/$real_root_category";
      $url .= "/$real_sub_category";
      if($thirdCategory) $url .= "/$thirdCategory";
      $url .= ".html";
      $this->redirect($url);
      */
    }
    //}}} 处理类别不匹配的情况-结束
    
    
    if ($subCategory == 6916 && !$role) {
      $role = 1;
    }
    
    if($rootCategory == 6738 && $show != 'all' && !$role) $role = 1;
    


    if(empty($subCategory) && empty($age) && empty($cert) && empty($photo) && empty($demand) && empty($district) && empty($role) && empty($keyword) && $rootCategory == 6987 ) {
      $this->forward('default','jiaoyou');
      return;
    }

    if(empty($subCategory) && empty($demand) && empty($district) && empty($role) && empty($keyword) && $rootCategory == 7450 ) {
      $this->forward('default','zhaopin');
      return;
    }

    $demand_skip = false;
    if (empty($subCategory) && empty($demand) && empty($district) && empty($role) && empty($keyword) && $rootCategory == 6501 ) {
      $demand = 1;
      $demand_skip = true;
    }
    
        
    $index_page = false;
    if(empty($subCategory) && (empty($demand) || $demand_skip) && empty($district) && empty($role) && (empty($page) || $page == 1) && empty($keyword)) {
      $index_page = true;
    }
    
    LsBreadcrumb::disable();
    $dead_time = 0;
    if ($searchTime == 1) {
      //一天以内
      $dead_time = strtotime('-1 days');
    } elseif ($searchTime == 2) {
      //二天以内
      $dead_time = strtotime('-3 days');
    } elseif ($searchTime == 3) {
      $dead_time = strtotime('-7 days');
    } elseif ($searchTime == 4) {
      $dead_time = strtotime('-30 days');
    }
    
    $dead_time = LsSolr::solrTime($dead_time);

    $this->rootName = ($rootCategory && isset($this->cates[$rootCategory])) ? $this->cates[$rootCategory] : '';
    $this->subName = ($subCategory && isset($this->cates[$subCategory])) ? $this->cates[$subCategory] : '';
    $this->thirdName = ($thirdCategory && isset($this->cates[$thirdCategory])) ? $this->cates[$thirdCategory] : '';

    $this->rootCategory = $rootCategory;
    $this->subCategory = $subCategory;
    $this->thirdCategory = $thirdCategory;
    $this->demand = $demand;
    $this->district = $district;
    $this->keyword = $keyword;
    //查询近期发布的内容
    $this->searchTime = $searchTime;
    $this->role = $role;
    $this->user_id = $user_id;

    //查询
    $q = new SolrQuery();
    
    $q->addFilterQuery('hidden:0');
    
    /* end */
    if ($this->has_media) {
      $q->addFilterQuery('media_id:([1 TO *])');
    }
    
    if (!$thirdCategory) {
      $q->setFacet(true);
    }
    
   
    if ($thirdCategory) {
      $q = LsSolr::addFilterQuery($q, 'cata3', $thirdCategory);
    } elseif ($subCategory) {
      $q->addFacetField('cata3');
      $q = LsSolr::addFilterQuery($q, 'cata2', $subCategory);
    } elseif ($rootCategory) {
      $q->addFacetField('cata2');
      $q = LsSolr::addFilterQuery($q, 'cata1', $rootCategory);
    } else {
      $q->addFacetField('cata1');
    }
    
    
    if ($demand) {
      $q = LsSolr::addFilterQuery($q, 'demand', $demand);
    }

    
    if ($dead_time) {
      $q = LsSolr::addFilterQuery($q, 'created_at', "([$dead_time TO NOW])");
     
    }
    if ($role) {
      $q = LsSolr::addFilterQuery($q, 'role', $role);
    }
    if ($user_id) {
      $q = LsSolr::addFilterQuery($q, 'user_id', $user_id);
    }
    
    if ($keyword && $keyword != "点此输入关键字") {
      if(preg_match('$1[3,4,5,8]{1}[0-9]{9}$', $keyword, $matches)) {
        if($phone = $matches[0]) {
           $q->setQuery("phone:$phone");
        }
      } else {
         $q->setQuery(LsSolr::phrasize($keyword));
      }
    } else {
      $q->setQuery('*:*');
    }

    
    if ($pos_keyword && $subCategory == 7038) {
      $q->setQuery(str_replace(':', ' ',$pos_keyword));
      $q->setQuery(str_replace('!', ' ',$pos_keyword));
      $q->set('qf', 'pos_start pos_end');
    }
    
    if ($phone) {
      $q = LsSolr::addFilterQuery($q, 'phone', $phone);
    }
    

    //按发布日期分段排序
    $q = LsSolr::addSortField($q, 'published_at desc');
    
    //{{{ 二手车搜索
    if ($subCategory == 7021 && ($this->autobrand || $this->autoseries || $this->autoage || $this->automileage)) {
      if ($this->autobrand) {
        $q = LsSolr::addFilterQuery($q, 'auto_brand', $this->autobrand);
      }
      
      if ($this->autoseries) {
        $q = LsSolr::addFilterQuery($q, 'auto_series', $this->autoseries);
      }
      
      if ($this->autoage) {
        $q = LsSolr::addFilterQuery($q, 'auto_age', $this->autoage);
      }
      if ($this->automileage) {
        $q = LsSolr::addFilterQuery($q, 'auto_mileage', $this->automileage);
      }
    }
    //}}}
    
    if ($this->autoprice) {
      $price_data = array(
          '93022' => array(0, 3),
          '93023' => array(3, 6),
          '93024' => array(6, 10),
          '93025' => array(10, 15),
          '93026' => array(15, 20),
          '93027' => array(20, 30),
          '93028' => array(30, 50),
          '93029' => array(50, 100),
          '93030' => array(100, '*'),
      );
      
      if ($this->autoprice == '93021') {
        $q->addFilterQuery('auto_price:0');
      } elseif (isset($price_data[$this->autoprice])) {
        list($min, $max) = $price_data[$this->autoprice];
        $q->addFilterQuery("auto_price:([$min TO $max])");
      }
    }

    if($rootCategory==6987){ 
    	 $this->forward404(); 
    }

    //{{{ 交友首页的搜索
    if($rootCategory && empty($subCategory) && ($age || $photo || $cert)) {
      
      $age_range = null;
      switch ($age) {
        case 1:
          $age_range = array(15, 25);
          break;
        case 2:
          $age_range = array(26, 35);
          break;
        case 3:
          $age_range = array(36, 45);
          break;
        case 4:
          $age_range = array(46, 59);
          break;
        case 5:
          $age_range = array(60, 80);
          break;
        default:
      }
      
      if (!is_null($age_range)) {
        list($age_min, $age_max) = $age_range;
        $q->addFilterQuery("age:([$age_min TO $age_max])");
      }
      
      if($cert) {
        $q->addFilterQuery('certified:1');
      }
    }
    //}}}


    /* 只针对跳蚕市场， 二级或者三级分类，列表，商家信息与个人信息分开显示(包括分页) */
    
    if ($district) {
      $q = LsSolr::addFilterQuery($q, 'district', $district);
    }

    $solr = LsSolr::getSolr('fuwu');
  
    $pager = new LsExtSolrPager($solr, $q, 20, $page);
    $pager->init();
    $response = $pager->getSolrResponse();
    
    //facet data
    if ($q->getFacet()) {
      $facet_data = array();
      foreach ($response->facet_counts->facet_fields as $field => $facet) {
        foreach ($facet as $fk => $fv) {
          $facet_data[intval($fk)] = $fv;
        } 
      }
      $this->facet_data = $facet_data;
    } else {
      $this->facet_data = array();
    }
    
    $this->categoryInfo = $pager->getPropelResult('Fuwu', null, 'doSelectJoinMedia');
    
    $this->hasList = $pager->getTotalRecordCount();

    if ($this->hasList) {
      /*||$subCategory||$thirdCategory||$demand||$district||$searchTime||$role*/
      if ($rootCategory || $subCategory || $thirdCategory || $demand || $district || $searchTime || $role || $keyword || $this->has_media) {
        $url = ls_url_for("@search",
                                      array(
                                        'rootCategory'  =>  $rootCategory,
                                        'subCategory'   =>  $subCategory,
                                        'thirdCategory' =>  $thirdCategory,
                                        'role'          =>  $role,
                                        'demand'        =>  $demand,
                                        'district'      =>  $district,
                                        'searchTime'    =>  $searchTime,
                                        'keyword'       =>  $keyword,
                                        'age'           =>  $age,
                                        'cert'          =>  $cert,
                                        'photo'         =>  $photo,
                                        'phone'         =>  $phone,
                                        'has_media'     =>  $this->has_media,
                                        'page'          =>  '%d'));
        $url = urldecode($url);
      } else {
        $url = '/search/page/%d/result.html';
      }

      $this->pageLinks = preg_replace_callback('/option value="(\d+)"/', function($m) use($url) {
        return 'option value="'. sprintf($url ,$m[1]) . '"';
      }, $pager->getNewPagelinks($url));
      $this->pageLinks = preg_replace('/onchange="[^"]+"/', 'onchange="location.href=$(this).val()"', $this->pageLinks);
    }

    //{{{取用户是否认证
    $fuwu_uid = array();

    $this->cert_user = array();
    if(count($fuwu_uid)>0) {
      $c = new Criteria;
      $c->add(FuwuCertPeer::USER_ID,$fuwu_uid,Criteria::IN);
      $cert_user = FuwuCertPeer::doSelect($c);
      foreach($cert_user as $cert) {
        $this->cert_user[$cert->getUserId()] = $cert;
      }
    }
    //}}}

    //设置网页title, metas title
    $str_meta_title = "";
    LsBreadcrumb::add(array (
        'url' => '@cates0', 'name' => '所有类目'
      ));
    $this->breadcrumb_params = array();
    if ($this->rootName) {
      LsBreadcrumb::add(array (
        'url' => '@search2', 'name' => $this->rootName
      ));
      $this->breadcrumb_params[$this->rootName] = array( 'r' => 1 );
    }
    if ($this->subName){
      LsBreadcrumb::add(array (
        'url' => '@search2', 'name' => $this->subName
      ));
      $this->breadcrumb_params[$this->subName] = array( 'r' => 1, 's' => 1 );
    }
    if ($this->thirdName){
      LsBreadcrumb::add(array (
        'url' => '@search2', 'name' => $this->thirdName
      ));
      $this->breadcrumb_params[$this->thirdName] = array( 'r' => 1, 's' => 1, 't' => 1 );
    }
    $this->realCrumbs = array_reverse(LsBreadcrumb::getRealCrumb($this->getContext()));

    //取置顶服务
    if ($page == 1  && !$keyword && !$user_id) {
      $c = new Criteria();
      $c->add(FuwuTopPeer::ACTIVE, true);
    
      $c->add(FuwuTopPeer::OVER_DATE, time(), Criteria::GREATER_THAN);
      
      //总置顶
      $c1 = $c->getNewCriterion(FuwuTopPeer::TOP_TYPE, 3);
      
      if ($rootCategory && !$subCategory) {
        //全部一级置顶
        $c2 = $c->getNewCriterion(FuwuTopPeer::TOP_TYPE, 2);
        //一级置顶
        $c3 = $c->getNewCriterion(FuwuTopPeer::TOP_TYPE, 6);
        $c3->addAnd($c->getNewCriterion(FuwuTopPeer::ROOT_CATEGORY, $rootCategory));
      }
      
      if ($rootCategory && $subCategory) {
        //全部二级置顶
        $c4 = $c->getNewCriterion(FuwuTopPeer::TOP_TYPE, 1);
        //二级置顶
        $c5 = $c->getNewCriterion(FuwuTopPeer::TOP_TYPE, 5);
        $c5->addAnd($c->getNewCriterion(FuwuTopPeer::SUB_CATEGORY, $subCategory));
      }
      
      if($c2) $c1->addOr($c2);
      if($c3) $c1->addOr($c3);
      if($c4) $c1->addOr($c4);
      if($c5) $c1->addOr($c5);
      $c->add($c1);
     

      $c->setLimit(10);
      
      $top_fuwu = FuwuTopPeer::doSelectFuwu($c);
      
      $this->categoryInfo = $this->categoryInfo ? array_merge($top_fuwu, $this->categoryInfo) : $top_fuwu;
    }

    //Meta设置
    $meta_cates = array();
    if($rootCategory && isset($this->cates[$rootCategory]))
      $meta_cates[] = $this->cates[$rootCategory];
    if($subCategory && isset($this->cates[$subCategory]))
      $meta_cates[] = $this->cates[$subCategory];

    
    if ($role) {
      $role_title = '';
      if ($role == 1) $role_title .= '个人';
      if ($role == 2) {
        if($subCategory == '6916'){
          $role_title .= '机构';
        } else {
          $role_title .= '商家';
        }
      }
    }
    if(!in_array($rootCategory, array(6738, 7450, 7020, 6987)) || $subCategory){
      $meta_title = "";
      $meta_keyword = "";

      if($keyword){    
        $meta_title .= $keyword . "的搜索结果_";
        $meta_keywords .= $keyword . "的搜索结果_";
      }

      $tmp = array_reverse($meta_cates);
      $tmp = array_map(create_function('$x', '{ return "合肥'.$role_title.'{$x}"; }'), $tmp);
        
      $meta_title .= implode('_', $tmp) . '_';
      $meta_keywords .= implode('、', $tmp) . '、';

      if(trim($meta_title) == '-'){
        $meta_title = '';
      }elseif(trim($meta_title) == ($keyword . "的搜索结果__")){
        $meta_title = $keyword . "的搜索结果_";
      }

      if(trim($meta_keywords) == '-'){
        $meta_keywords = '';
      }elseif(trim($meta_keywords) == ($keyword . "的搜索结果__")){
        $meta_keywords = $keyword . "的搜索结果_";
      }
    } else{
      $meta_title = '';
      $meta_keywords = '';
    }

    switch($rootCategory){
      case 6738:  //跳蚤市场
        $meta_title .= '合肥论坛'.$role_title.'跳蚤市场-合肥跳蚤市场-合肥物品交易-万家热线-安徽第一门户';
        $meta_keywords .= '合肥二手笔记本,合肥二手家电,合肥二手手机,合肥二手数码,合肥旧书,合肥二手电脑,合肥二手网,合肥二手市场,合肥跳蚤市场';
        $meta_description = "合肥跳蚤市场是最大的网上合肥二手市场,主要交易商品涵盖合肥二手笔记本、二手电脑、二手家电、二手手机、二手数码、旧书、合肥二手家具、合肥二手办公用品等，跳蚤市场提供免费的合肥二手信息发布，是闲置物品旧货出售求购交换，进行二手物品交易的最佳选择！";
        break;
      case 6501:  //跳蚤市场
        $meta_title .= '合肥'.$role_title.'生活服务-合肥分类信息网-万家热线-安徽第一门户';
        $meta_keywords .= '合肥生活服务,合肥开锁,家电维修,合肥保洁,合肥搬家,合肥水电维修,合肥房屋装修';
        $meta_description = "万家热线合肥生活服务频道免费为用户提供本地生活服务信息查询和发布服务，每天都有大量的合肥生活服务信息供您浏览，是寻找和发布合肥生活服务信息的最佳平台。";
        break;

      case 7450:  //求职
        $meta_title .= '合肥招聘信息_合肥兼职信息_合肥分类信息网-万家热线-安徽第一门户';
        $meta_keywords .= '合肥招聘,合肥兼职,合肥招聘信息,合肥求职,合肥大学生求职';
        $meta_description = "合肥兼职/招聘信息栏目是万家热线分类信息频道重要的组成部分，内容涵盖了合肥招聘、合肥兼职、合肥求职、合肥大学生就业等信息，全面服务于合肥市企业单位及求职者。";
        break;

      case 7020:  //车辆买卖
        $meta_title .= '合肥'.$role_title.'二手车市场_合肥拼车_合肥驾校网点_合肥分类信息网-万家热线-安徽第一门户';
        $meta_keywords .= '合肥二手车交易,合肥二手车市场,合肥汽车租赁,合肥驾校,合肥拼车';
        $meta_description = "合肥二手车网是合肥最大的二手车辆交易市场,内容涵盖合肥二手车辆买卖、汽车租赁、驾校陪练、拼车、二手自行车摩托车电动车交易等,是合肥二手车辆买卖最大的查询交易平台,欢迎网民朋友发布和查询二手车辆交易信息。";
        break;

      case 6987:  //征婚交友
        $meta_title .= '合肥交友网_合肥征婚网_合肥分类信息网 - 万家热线-安徽第一门户';
        $meta_keywords .= '合肥交友网,合肥征婚网,合肥异性交友,合肥征婚,合肥恋爱交友,兴趣交友,合肥驴友';
        $meta_description = "合肥最大的征婚交友信息发布和查询网站，内容涵盖异性交友、征婚、恋爱交友、同乡会、兴趣交友、驴友等。欢迎网民朋友们发布和查询感兴趣的内容。";
        break;
      case 6730:  //征婚交友
        $meta_title .= '合肥'.$role_title.'优惠打折_合肥分类信息网-万家热线-安徽第一门户';
        $meta_keywords .= '合肥优惠打折,合肥打折卡转让,合肥商场打折';
        $meta_description = "合肥优惠打折栏目是合肥最火爆的打折信息平台,每天最新最全最快的合肥优惠打折信息,欢迎免费查看、发布合肥优惠打折相关内容。";
        break;
      case 7116:  //征婚交友
        $meta_title .= '合肥'.$role_title.'投资理财_合肥商务服务_合肥分类信息网-万家热线-安徽第一门户';
        $meta_keywords .= '合肥投资理财,合肥商务服务';
        $meta_description = "合肥投资理财栏目是合肥最火爆的投资/理财/商务服务信息交流平台,每天提供最新最全最快的合肥投资/理财/商务服务信息,欢迎免费查看、发布合肥投资/理财/商务服务相关内容。";
        break;

      case 7099:  //旅游/景点/游乐园
        $meta_title .= '合肥'.$role_title.'旅游景点,合肥一日游_合肥分类信息网-万家热线-安徽第一门户';
        $meta_keywords .= '合肥旅游景点,合肥一日游,合肥旅游景点';
        $meta_description = "合肥旅游景点为您提供合肥旅游景点介绍、合肥著名旅游景点推荐 三河古镇,紫蓬山,徽园,欢乐岛,合肥丰乐生态园,合肥到全省各地如芜湖,黄山,宣城等城市的旅游景点 北京、昆明、珠海、深圳、上海等全国各地景点的精品旅游线路。";
        break;
      case 7067:  //旅游/景点/游乐园
        $meta_title .= '合肥'.$role_title.'票务出行_合肥分类信息网-万家热线-安徽第一门户';
        $meta_keywords .= '合肥特价机票,合肥演出票务转让,合肥汽车票转让,合肥火车票转让';
        $meta_description = "合肥票务出行栏目是合肥最火爆的票务/出行网上交易平台,每天最新最全最快的合肥票务/出行信息,欢迎免费查看、发布合肥票务/出行相关内容。";
        break;
      case 6818:  //旅游/景点/游乐园
        $meta_title .= '合肥'.$role_title.'咨询服务-合肥法律咨询-合肥律师事务所-合肥分类信息网-万家热线-安徽第一门户';
        $meta_keywords .= '合肥咨询服务,合肥法律咨询,合肥律师事务所';
        $meta_description = "合肥咨询服务栏目为合肥市民提供每天最新最全最快的合肥咨询服务、合肥法律咨询、合肥律师事务所信息,欢迎免费查看、发布相关内容。";
        break;
      case 6849:  //旅游/景点/游乐园
        $meta_title .= '合肥'.$role_title.'健康健身-合肥美容保健-合肥分类信息网 - 万家热线-安徽第一门户';
        $meta_keywords .= '合肥健康健身,合肥美容保健';
        $meta_description = "合肥健康健身栏目提供每天最新最全最快的合肥健康/健身信息,欢迎免费查看、发布合肥健康健身、合肥美容保健相关内容。";
        break;

      default:
        $str_cates1 = '（' . implode('、', array_reverse($meta_cates)) . '）';
        $str_cates2 = '（' . implode('、',
          array_map(
            create_function('$x',
            '{ return "合肥{$x}"; }'), $meta_cates)) . '）';

        $meta_title .= '合肥分类信息网 - 万家热线-安徽第一门户';
        $meta_description = "合肥最火爆的{$str_cates1}网上交易平台，每天最新最全最快的{$str_cates2}信息，欢迎免费查看、发布{$str_cates2}相关内容。";
        break;

    }


    switch ($subCategory) {
      case 7038:
      $meta_title = '合肥'.$role_title.'拼车网_合肥车辆买卖与服务_合肥二手车网_合肥二手车市场_合肥汽车租赁_合肥驾校_合肥分类信息网-万家热线-安徽第一门户';
      break;

      case 8105:
      $meta_title = '合肥'.$role_title.'兼职网_合肥大学生兼职网_合肥兼职/实习_合肥人才网_合肥招聘网_合肥分类信息网_万家热线_安徽第一门户';
      break;
      
      case 7057:
      $meta_title = '合肥'.$role_title.'二手自行车网_合肥二手自行车市场_合肥二手自行车转让_合肥二手车网_合肥二手车交易市场_合肥分类信息网_万家热线_安徽第一门户';
      break;

      case 7063:
      $meta_title = '合肥'.$role_title.'二手电动车交易网_合肥二手摩托车交易网_合肥二手摩托车交易市场_合肥二手电动车交易市场_合肥二手车网_合肥二手车交易市场_万家热线_安徽第一门户';
      break;

      case 7028:
      $meta_title = '合肥'.$role_title.'汽车租赁_合肥租车网_合肥租车公司_合肥婚庆租车_合肥分类信息网_万家热线_安徽第一门户';
      break;

      case 6747:
      $meta_title = '合肥'.$role_title.'二手书网_合肥二手书市场_合肥二手网_合肥二手市场_合肥分类信息_万家热线_安徽第一门户';
      break;



      case 7064:
      $meta_title = '合肥'.$role_title.'二手摩托车交易网_合肥二手摩托车交易市场_合肥二手摩托车网_合肥二手车网_合肥二手车交易市场_万家热线_安徽第一门户';
      break;

      case 7065:
      $meta_title = '合肥'.$role_title.'二手电动车交易网_合肥二手电动车交易市场_合肥二手电动车网_合肥二手车网_合肥二手车交易市场_万家热线_安徽第一门户';
      break;
      case 6501:
    	$meta_title = "合肥'.$role_title.'家政网_".$meta_title;
    	break;
    	
      default:
    
    }

    if(!$subCategory && !$rootCategory) {
      $meta_title = '合肥分类信息大全_万家分类网-万家热线';
      $meta_keywords = '合肥二手市场,合肥跳蚤市场,合肥家政服务,合肥同城交友,合肥兼职招聘,合肥拼车,合肥家电维修';
      $meta_description = '万家分类网是合肥最大最火的分类信息网站,内容涵盖：合肥二手商品、征婚交友、兼职招聘、家政服务、搬家公司、拼车服务、商务服务等等全面的生活信息，充分满足您免费查看/发布信息的需求。万家分类网，合肥最好的分类信息网。';
    }
    if(!$meta_title) {
      
      $str = '';
      if ($role) {
        if ($role == 1) $str .= '个人';
        if ($role == 2) {
          if($subCategory == '6916')
            $str .= '机构';
          } else {
            $str .= '商家';
          }
      }
      if($this->thirdName) $str .= $this->thirdName;
      if($this->subName) $str .= $this->subName;
      if($this->rootName) $str .= $this->rootName;
      $meta_title = "合肥{$str}-合肥分类信息网-万家热线-安徽第一门户";
      
    }
    
    $this->getResponse()->setTitle($meta_title);
    $this->getResponse()->addMeta('keywords', $meta_keywords);
    $this->getResponse()->addMeta('description', $meta_description);

    $this->cate_tpl = array('6738','6501', '7450', '7020', '7099', '6987'); //显示项目有变化的类别
    $this->sub_cate_tpl = array('7021','6988','6989','7038', '6916', '6622', '6502');
    $this->commend_photo_tpl = array('6738','6501','7099','7020');//有图片推荐的类别

    if (in_array($subCategory, $this->sub_cate_tpl)) $this->tpl = '_search'.$subCategory.'.php';
    elseif (in_array($rootCategory,$this->cate_tpl)) $this->tpl = '_search'.$rootCategory.'.php';
    else $this->tpl = '_search.php';
    if($search_district) $this->tpl = '_search_district.php';
    if($index_page){
      $flink_type = array(
          '6738'=>26,
          '6915'=>27,
          '6501'=>28,
          '6730'=>30,
          '6849'=>31,
          '7020'=>32,
          '7067'=>33,
          '7099'=>34,
          '7116'=>35,
          '6818'=>37,
          );
       
      if($flink_type[$rootCategory]) {
        $this->root_Category = $flink_type[$rootCategory];
      }
    }


    if ($rootCategory) {
      LsListDailyStats::update($rootCategory);
    }

    if ($subCategory) {
      LsListDailyStats::update($subCategory);
    }

    if ($thirdCategory) {
      LsListDailyStats::update($thirdCategory);
    }
    return sfView::SUCCESS;
  }
}
