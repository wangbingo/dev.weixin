<?php
function rad($d)
{
       return $d * 3.1415926535898 / 180.0;
}
function getdistance($lat1, $lng1, $lat2, $lng2)
{
    $EARTH_RADIUS = 6378.137;
    $radLat1 = rad($lat1);
    //echo $radLat1;
   $radLat2 = rad($lat2);
   $a = $radLat1 - $radLat2;
   $b = rad($lng1) - rad($lng2);
   $s = 2 * asin(sqrt(pow(sin($a/2),2) +
    cos($radLat1)*cos($radLat2)*pow(sin($b/2),2)));
   $s = $s *$EARTH_RADIUS;
   $s = round($s * 10000) / 10000;
   return $s;
}