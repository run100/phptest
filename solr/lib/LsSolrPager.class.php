<?php

class LsSolrPager extends sfPager
{

  protected $solr;

  protected $solrQuery;

  protected $docs;
  
  /**
   * 
   * @var SolrResponse
   */
  protected $response;

  public function __construct($solr, $query, $perpage, $page)
  {
    $this->setSolr($solr);
    $this->setSolrQuery($query);
    $this->setPage($page);
    $this->setMaxPerPage($perpage);
  }

  public function setSolr($solr)
  {
    $this->solr = $solr;
  }

  public function setSolrQuery($query)
  {
    $this->solrQuery = $query;
  }

  public function init()
  {
    $this->solrQuery->setRows($this->getMaxPerPage());
    $this->solrQuery->setStart($this->getMaxPerPage() * ($this->getPage() - 1));
    
    $query_response = $this->solr->query($this->solrQuery);
    $this->response = $query_response->getResponse();
    $this->docs = (array) $this->response->response['docs'];
    
    $hasMaxRecordLimit = ($this->getMaxRecordLimit() !== false);
    $maxRecordLimit = $this->getMaxRecordLimit();
    $count = $this->response->response['numFound'];
    $this->setNbResults($hasMaxRecordLimit ? min($maxRecordLimit, $count) : $count);
    
    $this->setLastPage(ceil($this->getNbResults() / $this->getMaxPerPage()));
  }

  public function getResults()
  {
    return $this->docs;
  }

  protected function retrieveObject($offset)
  {
    throw new sfException("Not implemented");
  }

  /**
   * For compatibility with PropelPager
   * 
   * @return number
   */
  public function getTotalPages()
  {
    return $this->getLastPage();
  }

  /**
   * For compatibility with PropelPager
   * Alias of getResults()
   */
  public function getResult()
  {
    return $this->getResults();
  }

  /**
   * For compatibility with PropelPager
   * alias of getNbResults()
   */
  public function getTotalRecordCount()
  {
    return $this->getNbResults();
  }

  public function getPrev()
  {
    return $this->getPreviousPage();
  }

  public function getNext()
  {
    return $this->getNextPage();
  }

  public function getRowsPerpage()
  {
    return $this->getMaxPerPage();
  }

  private function autoPager(& $pager = NULL)
  {
    if (! $pager) {
      if ($this->phone_pager) {
        $pager = new LsPhonePager($this->getNbResults(), $this->getMaxPerPage());
      } else {
        $pager = new LsPager($this->getNbResults(), $this->getMaxPerPage());
      }
    }
    $pager->set_page($this->getPage());
    if ($this->ls_url) {
      $pager->set_url($this->ls_url);
    }
    if ($this->group_size) {
      $pager->group_size($this->group_size);
    }
    if ($this->ls_template) {
      $pager->set_template($this->ls_template);
    }
    $pager->is_selected($this->is_selected);
    return $pager;
  }

  /**
   * 模板输出
   */
  public function __toString()
  {
    return strval($this->autoPager());
  }

  public function getNewPagelinks($url)
  {
    return $this->pagerStart() . $this->pagerNewMain($url) . $this->pagerEnd();
  }

  private function getUrl($url, $page)
  {
    return sprintf($url, $page);
  }

  public function pagerNewMain($url)
  {
    $page = $this->getPage();
    $perpage = $this->getRowsPerPage();
    $totalPages = $this->getTotalPages();
    $totalCount = $this->getTotalRecordCount();
    
    if ($totalPages > 50) {
      if ($page > 50) {
        $last50Page = $page - 50;
        $minPage = $last50Page + 1;
      } else {
        $last50Page = false;
        $minPage = 1;
      }
      if ($page + 50 < $totalPages) {
        $next50Page = $page + 50;
        $maxPage = $next50Page - 1;
      } else {
        $next50Page = false;
        $maxPage = $totalPages;
      }
    } else {
      $minPage = 1;
      $maxPage = $totalPages;
    }
    
    $html = '<div class="PropelPagerNav">';
    $html .= "共 <span class='cor_page'>" . $totalCount . "</span> 条 ";
    $html .= " 每页 <span class='cor_page'>" . $perpage . "</span> 条 ";
    $html .= " 共 <span class='cor_page'>" . $totalPages . "</span> 页 ";
    if ($page != 1) {
      $html .= '<a href="' . $this->getUrl($url, 1) . '" target="_self">首页</a> | ';
    }
    if (isset($last50Page) && $last50Page) {
      $html .= ' <a href="' . $this->getUrl($url, $last50Page) . '" target="_self">前50页</a> | ';
    }
    if ($lastPage = $page - 1) {
      $html .= '<a href="' . $this->getUrl($url, $lastPage) . '" target="_self">上页</a>  ';
    }
    
    $nextPage = $page + 1;
    if ($nextPage <= $totalPages) {
      $html .= " | ";
      $html .= '<a href="' . $this->getUrl($url, $nextPage) . '" target="_self">下页</a>';
    }
    
    if (isset($next50Page) && $next50Page) {
      $html .= ' | <a href="' . $this->getUrl($url, $next50Page) . '" target="_self">后50页</a> ';
    }
    if ($page != $totalPages) {
      $html .= ' |  <a href="' . $this->getUrl($url, $totalPages) . '" target="_self">末页</a>';
    }
    $html .= " | ";
    $html .= ' 第 ';
    if (stripos($url, 'javascript') !== false) {
      $jumpUrl = str_replace("%d", "this.value", $url);
      $html .= '<select name="pp" class="PropelPagerForm" onchange="' . $jumpUrl . '">';
    } else {
      $tmpUrl = str_replace("%d", "'+this.value+'", $url);
      $html .= '<select name="pp"  class="PropelPagerForm" onchange="location=\'' . $tmpUrl . '\'">';
    }
    
    $html .= FormUtils::numberOptions($minPage, $maxPage, $page, false);
    $html .= '</select> 页';
    $html .= '</div>';
    
    return $html;
  }

  public function pagerStart()
  {
    return '<div class="PropelPager">';
  }

  public function pagerEnd()
  {
    return '</div>';
  }

  /**
   * 将结果集转换成propel对象
   */
  public function getPropelResult($modelClass, Criteria $c = null, $peerSelectMethod = 'doSelect', $idKey='id')
  {
    return LsSolr::getPropelResult($this->getResult(), $modelClass, $c, $peerSelectMethod, $idKey);
  }
  
  public function getSolrResponse()
  {
    return $this->response;
  }
}
