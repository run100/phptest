<?php

$col = LsMongo::getCollection('client.channel_stats');
$rs = $col->find($query);
$ret = array();
foreach($rs as $item) {
  $ret[] = $item;
}

$ret = (array)QArray::make($ret)->group('channel')->map_k(function($channel, $items) {
  $ret = array();
  foreach($items as $item) {
	foreach($item as $k=>$v) {
	  if(in_array($k, array('date', 'channel', '_id'))) continue;
	  $ret[$k] += $v;
	}
  }
  return $ret;
})->ksort('ASC');


$this->results = $ret;