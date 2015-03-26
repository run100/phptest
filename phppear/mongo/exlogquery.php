<?php

class newsLogActions extends sfActions
{
  public function executeIndex()
  {
    if ($this->getRequest()->hasParameter('filter')) {
      $filters = $this->getRequestParameter('filters');
      $this->getUser()->getAttributeHolder()->removeNamespace('sf_admin/newsLog');
      $this->getUser()->getAttributeHolder()->removeNamespace('sf_admin/newsLog/filters');
      $this->getUser()->getAttributeHolder()->add($filters, 'sf_admin/newsLog/filters');
    }

    $this->filters = $this->getUser()->getAttributeHolder()->getAll('sf_admin/newsLog/filters');

    $query = array();

    if (isset($this->filters['range']['from']) && ($from = $this->filters['range']['from'])) {
      $date['$gte'] = $from . ' 00:00:00';
    }

    if (isset($this->filters['range']['to']) && ($to = $this->filters['range']['to'])) {
      $date['$lte'] = $to . ' 23:59:59';
    }

    if (!$from && !$to) { // 如果不按日期检索的话，默认取最近2个月的操作记录
      $date['$gte'] = date('Y-m-d', time() - 86400 * 60) . ' 00:00:00';
    }

    if (count($date)) {
      $query['time'] = $date;
    }

    $keyword = isset($this->filters['keyword']) ? $this->filters['keyword'] : null;

    if ($keyword) {
      $query['operation'] = new MongoRegex("/$keyword/");
    }

    $editor = isset($this->filters['editor']) ? $this->filters['editor'] : null;

    if ($editor) {
      if(is_numeric(trim($editor))) {
        $query['uid'] = (int)trim($editor);
      } else {
        $c = new Criteria();
        $c->add(UserPeer::EDITOR_REALNAME, $editor, Criteria::LIKE);
        $c->setLimit(100);
        $uids = (array)QArray::make(UserPeer::doSelect($c))->mask('id')->values();
        $query['uid'] = array('$in' => $uids);
      }
    }

    $sort = array('time' => -1);
    $this->pager = new LsMongoPager('log.editor', $query, $sort, 20);
    $this->pager->setPage($this->getRequestParameter('page', 1));
    $this->pager->init();

    $this->results = $this->pager->getResults();
    $uids = QArray::make($this->results, true)->mask('uid')->values();
    $this->editors = (array)QArray::make(UserPeer::retrieveByPKs($uids))->k_v('id', 'editor_realname');

  }
}