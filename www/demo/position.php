<?php
$location['place']="";
$location['postcode']="ch65rz";
latLong($location);
function latLong($location){
	$dbuser= "root"; $dbpw = "root";
	$dbconn = mysql_connect('localhost', $dbuser, $dbpw);
	if (!$dbconn) {echo "sql connection fail ".mysql_error();}
	$dbase = mysql_select_db('uktowns');

	if ($location['place']){
		$place = $location['place'];
		$qry = "select `latitude`,`longitude` from `places` where `city` like '$place'";
	} else {
		$place = $location['postcode'];
		$qry = "select `latitude`,`longitude` from `postcodes` where `postcode` like '$place'";
	}
	
	$rslt = mysql_query($qry);
	if(!$rslt)echo mysql_error();
	$latlong = mysql_fetch_array($rslt);
	$pos = array();
	$pos[0] = $latlong['latitude'];
	$pos[1] = $latlong['longitude'];
	
	print_r($pos);
	//return $pos;
}
?>
