<?php

class LsMongoPager extends sfPager
{
  /**
   * The current mongo collection
   * @var MongoCollection
   */
  private $collection;
  
  /**
   * Fields to search
   * @var array
   */
  private $query;

  private $sort;
  
  private $offset;
  
  private $limit;
  
  private $total;
  
  
  
  public function __construct($collectionName, $query = array(), $sort = array(), $maxPerPage = 10)
  {
    $this->collection = LsMongo::getDB()->selectCollection($collectionName);
    $this->query = $query;
    $this->sort = $sort;
    parent::__construct($collectionName, $maxPerPage);
  }
  
  public function init()
  {
    $hasMaxRecordLimit = ($this->getMaxRecordLimit() !== false);
    $maxRecordLimit = $this->getMaxRecordLimit();
    $count = $this->collection->count($this->query);
    $this->total = $count;
    $this->setNbResults($hasMaxRecordLimit ? min($count, $maxRecordLimit) : $count);
    
    if (($this->getPage() == 0 || $this->getMaxPerPage() == 0)) {
      $this->setLastPage(0);
    } else {
      $this->setLastPage(ceil($this->getNbResults() / $this->getMaxPerPage()));

      $offset = ($this->getPage() - 1) * $this->getMaxPerPage();

      if ($hasMaxRecordLimit) {
        $maxRecordLimit = $maxRecordLimit - $offset;
        $limit = $maxRecordLimit > $this->getMaxPerPage()  ? $this->getMaxPerPage() : $maxRecordLimit;
      } else {
        $limit = $this->getMaxPerPage();
      }
    }
    
    $this->offset = $offset;
    $this->limit = $limit;
  }

  /**
   * @return MongoCursor
   */
  public function getResults()
  {
    return $this->collection->find($this->query)
                            ->sort($this->sort)
                            ->skip($this->offset)
                            ->limit($this->limit);
  }

  // used internally by getCurrent()
  protected function retrieveObject($offset)
  {
    throw new sfException('Not implemented');
  }
  
  public function getTotalRecordCount(){
     return $this->total;
  }
}
