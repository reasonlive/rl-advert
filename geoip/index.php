<?php

$ip =  $_SERVER['REMOTE_ADDR'];

include("geoipcity.inc");
include("geoipregionvars.php");


$gi = geoip_open("GeoLiteCity.dat",GEOIP_STANDARD);
$record = geoip_record_by_addr($gi,$ip);
geoip_close($gi);

$country_code =  $record->country_code;
$country_name =  $record->country_name;

echo "<b>$country_code</b><br>";
echo "<b>$country_name</b><br>";

?>