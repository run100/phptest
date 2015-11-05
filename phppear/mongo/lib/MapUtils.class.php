<?php

class MapUtils
{
  const R = 6370996.81;
  const MARS_FIX_DATA = '/lib/data/offset.dat';

  public function range($location, $distance)
  {
    $delta = (($location['accuracy'] + $distance) * 180) / (self::R * pi());
    $location1 = array(
      'lat'  => $location['lat'] - $delta,
      'lng'  => $location['lng'] - $delta);
    $location2 = array(
      'lat'  => $location['lat'] + $delta,
      'lng'  => $location['lng'] + $delta);

    return array(
      'from'    => $location1,
      'to'      => $location2);
  }

  public static function distance($pt1, $pt2)
  {
    $lat1 = $pt1['lat'];
    $lon1 = $pt1['lng'];
  
    $lat2 = $pt2['lat'];
    $lon2 = $pt2['lng'];

    $delta_lat = $lat2 - $lat1 ;
    $delta_lon = $lon2 - $lon1 ;

    $alpha    = $delta_lat/2;
    $beta     = $delta_lon/2;
    $a        = sin(deg2rad($alpha)) * sin(deg2rad($alpha)) + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin(deg2rad($beta)) * sin(deg2rad($beta)) ;
    $c        = asin(min(1, sqrt($a)));
    $distance = 2* self::R * $c;
    $distance = round($distance, 4);

    return round($distance);
 
  }
  public function fixloc($x)
  {
    return -24.023+170.7046*cos(($x*6.7815-275.21)*3.1415926/180)+93.513*cos((15.284*$x-795.88)*3.1415926/180);
  }

  function getOffset($loc) {
    if(!$loc) return array();
    extract($loc);


    $lat = round($lat * 100);
    $lng = round($lng * 100);

    if($lng > 13479 || $lng < 7350) {
      return array(0, 0);
    }

    $struct_size = 8;
    $size = filesize(SF_ROOT_DIR . self::MARS_FIX_DATA) / $struct_size;

    $fp = fopen(SF_ROOT_DIR . self::MARS_FIX_DATA, 'rb');
    $start = 0;
    $mid = 0;
    $end = $size;
    do{
      $mid = intval(($start + $end) / 2);
      fseek($fp, $mid * 8);
      $data = unpack('s', fread($fp, 2));
      $lng_seeked = $data[1];

      if($lng_seeked < $lng) {
        $start = $mid + 1;
      } elseif ($lng_seeked > $lng) {
        $end = $mid - 1;
      } else {
        break;
      }

      if($start >= $end) {
        break;
      }

    } while(1);

    if($start < $end) {
      fseek($fp, $mid * 8);
      $data = unpack('s*', fread($fp, 8));
      fseek($fp, ($mid + $lat - $data[2]) * 8);
      $data = unpack('s*', fread($fp, 8));
    }

    fclose($fp);

    if($data[1] == $lng && $data[2] == $lat) {
      return array($data[3], $data[4]);
    } else {
      return array(0, 0);
    }
  }

  function lng2px($lng, $zoom)
  {
    return ($lng+180)*(256<<$zoom)/360;;
  }

  function lat2px($lat, $zoom)
  {
    $siny = sin($lat * pi() / 180);
    $y=log((1+$siny)/(1-$siny));
    return (128<<$zoom)*(1-$y/(2*pi()));
  }

  function px2lng($px, $zoom)
  {
    return $px*360/(256<<$zoom)-180;
  }

  function px2lat($px, $zoom)
  {
    $y = 2*pi()*(1-$px /(128 << $zoom));
    $z = pow(M_E, $y);
    $siny = ($z -1)/($z +1);
    return asin($siny) * 180/pi();
  }

  public static function getMars($loc)
  {
    if(!$loc) return null;
    $offset = self::getOffset($loc);
    $loc['lng'] = self::px2lng(self::lng2px($loc['lng'], 18) + $offset[0], 18);
    $loc['lat'] = self::px2lat(self::lat2px($loc['lat'], 18) + $offset[1], 18);
    return $loc;
  }

  public static function getCircle($loc, $radius, $points = 4)
  {
    if (!$loc || !$loc['lat']) return array();

    $d2r = M_PI / 180;
    $r2d = 180 / M_PI;
    $Clat = $radius / 1000 * 0.008983;  // Convert statute miles into degrees latitude
    $Clng = $Clat / cos($loc['lat'] * $d2r);
    $Cpoints = array();

    $half = (int)($points / 2);
    for ($i = 0; $i < $points; $i++) {
      $theta = M_PI * ($i / $half);
      $Cy = $loc['lat'] + ($Clat * sin($theta));
      $Cx = $loc['lng'] + ($Clng * cos($theta));
      $Cpoints[] = array(
        'lat' => $Cy,
        'lng' => $Cx
      );
    }

    return $Cpoints;

  }

  public static function getBoxRange($loc, $radius)
  {
    if (!$loc || !$loc['lat']) return array();

    $p = self::getCircle($loc, $radius);
    return array(
      array(
        'lat' => $p[3]['lat'],
        'lng' => $p[2]['lng']
      ),
      array(
        'lat' => $p[1]['lat'],
        'lng' => $p[0]['lng']
      )
    );
  }


  public static function getZoomCenter($locs, $w, $h)
  {
    $locs = QArray::make($locs);
    $lats = (array)$locs->mask('lat');
    $lngs = (array)$locs->mask('lng');

    $lat0 = min($lats);
    $lat1 = max($lats);
    $lng0 = min($lngs);
    $lng1 = max($lngs);

    $center = array(
      'lat'   =>  ($lat0 + $lat1) / 2,
      'lng'   =>  ($lng0 + $lng1) / 2);

    $zoom = 18;
    while($zoom > 0 && ((self::lat2px($lat0, $zoom) - self::lat2px($lat1, $zoom)) * 0.7 > 0.9 * $h || (self::lng2px($lng1, $zoom) - self::lng2px($lng0, $zoom)) * 0.7 > 0.9 * $w)) {
      $zoom --;
    }

    $ret = array(
      'center'  =>  $center,
      'zoom'    => $zoom);

    return $ret;
  }
  
  public static function getRawAddress($loc) {
    $query = array(
      "version"=> "1.1.0",
      "host"=> "maps.google.com",
      "request_address"=> true,
      "address_language"=> "zh_CN",
      "location"=> array(
        "latitude" => (double)$loc['lat'],
        "longitude" => (double)$loc['lng']
      ) 
    );
    $ch = curl_init("http://www.google.com/loc/json");
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($query));
    $json = curl_exec($ch);
    $json = json_decode($json, 1);
    curl_close($ch);
    return $json['location']['address'];
  }
  
  public static function getDistanceString($distance) {
    if($distance <= 0) return '';

    if($distance < 1000) {
      $distance = round($distance);
      $distance =  "{$distance}m";
    } elseif($distance > 1000000) {
      $distance = '';
    } else {
      $distance = round($distance / 1000, 1);
      $distance =  "{$distance}km";
    }

    return $distance;
  }

  public static function fillDistance(&$data, $location, $map_api = 'google') {
    foreach ($data as &$item) {
      if ($item['bd_latlng']) {
        $point = $item['bd_latlng']; $item['bd_point'] = array();
        list($item['bd_point']['lat'], $item['bd_point']['lng']) = explode(',', $point);
        $item['bd_point']['lat'] = (double)$item['bd_point']['lat'];
        $item['bd_point']['lng'] = (double)$item['bd_point']['lng'];
      }

      if ($item['latlng']) {
        $point = $item['latlng']; $item['gg_point'] = array();
        list($item['gg_point']['lat'], $item['gg_point']['lng']) = explode(',', $point);
        $item['gg_point']['lat'] = (double)$item['gg_point']['lat'];
        $item['gg_point']['lng'] = (double)$item['gg_point']['lng'];
      }

      switch ($map_api) {
        case 'baidu':
          $item['point'] = $item['bd_point'];
          break;
        default:
          $item['point'] = $item['gg_point'];
      }

      $item['distance'] = MapUtils::getDistanceString(MapUtils::distance($location, $item['point']));
      if (!$location) $item['distance'] = '';
    }
  }
}
