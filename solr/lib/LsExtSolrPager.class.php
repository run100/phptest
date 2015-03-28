<?php

/**
 * 与ExtPropelPager保持兼容接口
 * FIXME: this sucks...
 */
class LsExtSolrPager extends LsSolrPager
{  

  /**
   * print page links
   *
   * @param string $url          
   */
  public function printPageLinks($url)
  {
    echo $this->getPagelinks($url);
  }

  /**
   * Returns pager links html
   * 
   * @param string $url          
   * @return string
   */
  public function getPagelinks($url)
  {
    $html = $this->pagerStart() . $this->pagerSummary() . $this->pagerMain($url) .
         $this->pagerEnd();
    // for xhtml
    $html = str_replace('selected>', 'selected="selected">', $html);
    return $html;
  }

  /**
   * Returns pager new links html change all css like this drop getPagelinks()
   * Returns pager links html
   * 
   * @param string $url          
   * @return string
   */
  public function getNewPagelinks($url)
  {
    return $this->pagerStart() . $this->pagerNewMain($url) . $this->pagerEnd();
  }

  /**
   * pager start tag
   *
   * @return
   *
   */
  public function getPagelink($url)
  {
    return $this->pagerMain($url) . $this->pagerEnd();
  }

  public function pagerStart()
  {
    return '<div class="PropelPager">';
  }

  public function pagerSummary()
  {
    $html = '<div class="PropelPagerSummary"> 共 <span class="PropelPagerDigit">' .
         $this->getTotalRecordCount() . '</span> 条记录，';
    $html .= '分 <span class="PropelPagerDigit">' . $this->getTotalPages() .
         '</span> 页</div>';
    return $html;
  }

  /**
   * For pagerMain()
   * 
   * @return int
   */
  public function getPageCount()
  {
    return $this->getTotalPages();
  }

  public function getLinks($url, $num = 5)
  {
    $html = '';
    
    $page = $this->getPage();
    $totalPages = $this->getPageCount();
    if ($lastPage = $page - 1) {
      $html .= '<a href="' . $this->getUrl($url, $lastPage) .
           '" target="_self">上一页</a> ';
    }
    
    $start = $page - 1;
    if (($start + $num - 1) > $totalPages)
      $start = $totalPages - $num + 1;
    $end = $start + $num - 1;
    if ($start <= 0)
      $start = 1;
    if ($end > $totalPages)
      $end = $totalPages;
    for ($i = $start; $i <= $end; $i ++) {
      if ($i == $page)
        $html .= $i;
      else
        $html .= '<a href="' . $this->getUrl($url, $i) . '">' . $i . '</a>';
    }
    
    $nextPage = $page + 1;
    if ($nextPage <= $totalPages) {
      $html .= '<a href="' . $this->getUrl($url, $nextPage) .
           '" target="_self">下一页</a>';
    }
    
    return $html;
  }

  public function pagerMain($url)
  {
    $page = $this->getPage();
    $perpage = $this->getRowsPerPage();
    $totalPages = $this->getPageCount();
    
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
    if ($page != 1) {
      $html .= '<a href="' . $this->getUrl($url, 1) .
           '" target="_self">首页</a> | ';
    }
    if (isset($last50Page) && $last50Page) {
      $html .= ' <a href="' . $this->getUrl($url, $last50Page) .
           '" target="_self">前50页</a> | ';
    }
    if ($lastPage = $page - 1) {
      $html .= '<a href="' . $this->getUrl($url, $lastPage) .
           '" target="_self">上一页</a> | ';
    }
    $html .= ' 第 ';
    
    if (stripos($url, 'javascript') !== false) {
      $jumpUrl = str_replace("%d", "this.value", $url);
      $html .= '<select name="pp" class="PropelPagerForm" onchange="' . $jumpUrl .
           '">';
    } else {
      $tmpUrl = str_replace("%d", '', $url);
      $html .= '<select name="pp"  class="PropelPagerForm" onchange="location=\'' .
           $tmpUrl . '\' + this.value">';
    }
    
    $html .= FormUtils::numberOptions($minPage, $maxPage, $page, false);
    $html .= '</select> 页';
    
    $nextPage = $page + 1;
    if ($nextPage <= $totalPages) {
      $html .= " | ";
      $html .= '<a href="' . $this->getUrl($url, $nextPage) .
           '" target="_self">下一页</a>';
    }
    
    if (isset($next50Page) && $next50Page) {
      $html .= ' | <a href="' . $this->getUrl($url, $next50Page) .
           '" target="_self">后50页</a> ';
    }
    if ($page != $totalPages) {
      $html .= ' |  <a href="' . $this->getUrl($url, $totalPages) .
           '" target="_self">末页</a>';
    }
    $html .= '</div>';
    
    return $html;
  }

  /**
   *
   *
   * Returns pager new links html change all css like this drop pagerMain()
   * 
   * @param string $url          
   * @return string
   */
  public function pagerNewMain($url)
  {
    $page = $this->getPage();
    $perpage = $this->getRowsPerPage();
    $totalPages = $this->getPageCount();
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
      $html .= '<a href="' . $this->getUrl($url, 1) .
           '" target="_self">首页</a> | ';
    }
    if (isset($last50Page) && $last50Page) {
      $html .= ' <a href="' . $this->getUrl($url, $last50Page) .
           '" target="_self">前50页</a> | ';
    }
    if ($lastPage = $page - 1) {
      $html .= '<a href="' . $this->getUrl($url, $lastPage) .
           '" target="_self">上页</a>  ';
    }
    
    $nextPage = $page + 1;
    if ($nextPage <= $totalPages) {
      $html .= " | ";
      $html .= '<a href="' . $this->getUrl($url, $nextPage) .
           '" target="_self">下页</a>';
    }
    
    if (isset($next50Page) && $next50Page) {
      $html .= ' | <a href="' . $this->getUrl($url, $next50Page) .
           '" target="_self">后50页</a> ';
    }
    if ($page != $totalPages) {
      $html .= ' |  <a href="' . $this->getUrl($url, $totalPages) .
           '" target="_self">末页</a>';
    }
    $html .= " | ";
    $html .= ' 第 ';
    if (stripos($url, 'javascript') !== false) {
      $jumpUrl = str_replace("%d", "this.value", $url);
      $html .= '<select name="pp" class="PropelPagerForm" onchange="' . $jumpUrl .
           '">';
    } else {
      $tmpUrl = str_replace("%d", "'+this.value+'", $url);
      $html .= '<select name="pp"  class="PropelPagerForm" onchange="location=\'' .
           $tmpUrl . '\'">';
    }
    
    $html .= FormUtils::numberOptions($minPage, $maxPage, $page, false);
    $html .= '</select> 页';
    $html .= '</div>';
    
    return $html;
  }

  public function getNextPageUrl($url)
  {
    $page = $this->getPage();
    $totalPages = $this->getPageCount();
    $nextPage = $page + 1;
    if ($nextPage <= $totalPages) {
      return $this->getUrl($url, $nextPage);
    }
  }

  public function getPrevPageUrl($url)
  {
    $page = $this->getPrev();
    if ($page) {
      return $this->getUrl($url, $page);
    }
  }

  public function pagerEnd()
  {
    return '</div>';
  }

  private function getUrl($url, $page)
  {
    return sprintf($url, $page);
  }
}