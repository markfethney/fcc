<?php
function scrape($url){
	
		// make the request to $url via cURL
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_USERAGENT, 'Firefox (WindowsXP) - Mozilla/5.0 (Windows; U; Windows NT 5.1; en-GB; rv:1.8.1.6) Gecko/20070725 Firefox/2.0.0.6');
		curl_setopt($ch, CURLOPT_URL,$url);
		curl_setopt($ch, CURLOPT_FAILONERROR, true);
		curl_setopt($ch, CURLOPT_AUTOREFERER, true);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER,true);
		curl_setopt($ch, CURLOPT_TIMEOUT, 40);
		$html= curl_exec($ch);
		if (!$html) {
			echo "<br />cURL error number:" .curl_errno($ch);
			echo "<br />cURL error:" . curl_error($ch);
			exit;
		}
		curl_close($ch);
		return $html;
	}
	
function latLong($location){
	$dbuser= "root"; $dbpw = "root";
	$dbconn = mysql_connect('localhost', $dbuser, $dbpw);
	if (!$dbconn) {echo "sql connection fail ".mysql_error();}
	$dbase = mysql_select_db('uktowns');

	if ($location['postcode']){
		$place = $location['postcode'];
		$qry = "select `latitude`,`longitude` from `postcodes` where `postcode` like '$place'";
		
	} else {
		$p = $location['place'];
		$pl=explode(",",$p);
		$place=$pl[0];
		$qry = "select `latitude`,`longitude` from `places` where `city` like '$place'";	}
	
	$rslt = mysql_query($qry);
	if(!$rslt)echo mysql_error();
	$latlong = mysql_fetch_array($rslt);
	$pos = array();
	$pos[0] = $latlong['latitude'];
	$pos[1] = $latlong['longitude'];
	
	//print_r($pos);
	return $pos;
}

function cleanHTML($htmlStr){
	$xmlStr=str_replace('<','&lt;',$htmlStr);
	$xmlStr=str_replace('>','&gt;',$xmlStr);
	$xmlStr=str_replace('"','&quot;',$xmlStr);
	$xmlStr=str_replace("'",'&#39;',$xmlStr);
	$xmlStr=str_replace("&",'&amp;',$xmlStr);
return $xmlStr;
}

?>