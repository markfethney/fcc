<?php
//http://www.pistonheads.com/classifieds?Category=used-cars&Keyword=golf+tdi&KeywordCleanedValue=golf+tdi&isExperiment=True
function ph_bot($requestData) {

	$safeQuery = str_replace(' ','+',$requestData['Query']);//urlencode (utf8_encode($requestData['Query']));
	//$site  = $requestData['GlobalID'];
	$buyerPostalCode =$requestData['buyerPostalCode'];
  	$distanceMax = (string)$requestData['distanceMax'];
  	$priceRangeMin = $requestData['MinPrice'];
  	$priceRangeMax = $requestData['MaxPrice'];
  	$itemsPerRange = 60;//$requestData['ItemsPerRange'];
	$url = "http://www.pistonheads.com/classifieds?Category=used-cars&Keyword=$safeQuery&KeywordCleanedValue=$safeQuery&MaxPrice=$priceRangeMax&MinPrice=$priceRangeMin&Page=1&isExperiment=True";
	
	//http://www.gumtree.com/search?q=mk4+golf&category=cars&search_location=ll198dn&distance=100.0&current_distance=100.0&vehicle_make=&vehicle_model=&vehicle_registration_year=&vehicle_mileage=&seller_type=&vehicle_body_type=&vehicle_fuel_type=&vehicle_transmission=&vehicle_engine_size=&min_price=&max_price=
	
	$page = scrape($url);
	//if($page !=""){$response['ok']=1} else {$response['ok']=0}
	//echo "$url<br>";
	$dom = new DOMDocument();
	@$dom->loadHTML($page);
	$xpath = new DOMXPath($dom);
	$xtitles_list = $xpath->evaluate("//div[@class='listing-headline']/a/h3");
	$xlinks_list = $xpath->evaluate("//div[@class='listing-headline']/a");
	$xprices_list = $xpath->evaluate("//div[@class='price']/span");
	$xlocations_list = $xpath->evaluate("//p[@class='location']");
	$xdescriptions_list = $xpath->evaluate("//div[@class='description']");
	$ximgurls_list = $xpath->evaluate("//div[@class='mainimg']/a/img");
	
	$n = $xtitles_list->length;
	$response['totalItems']= (string)$n;
	
	$x=0;
	while ($x < $n){
		$xtitle = $xtitles_list->item($x);
		$response[$x]['title'] = cleanHTML($xtitle->textContent);
		
		$xlink = $xlinks_list->item($x);
		$response[$x]['link'] = $xlink->getAttribute('href');
		
		$xprice = $xprices_list->item($x);
		$response[$x]['price'] = preg_replace('/[^£\,0-9]/', '', $xprice->textContent) ;
	
		$xlocation = $xlocations_list->item($x);
		$response[$x]['location']['place'] = cleanHTML((string)$xlocation->textContent);
		$response[$x]['location']['postcode'] = '';
		
		$xdescription = $xdescriptions_list->item($x);
		$response[$x]['description'] = cleanHTML($xdescription->textContent);
		
		$ximgurl = $ximgurls_list->item($x);
		$response[$x]['imgurl'] = $ximgurl->getAttribute('src');
		$response[$x]['src'] = "Pistonheads";
		
		$x++;
	}
	return $response;
}
/*
$requestData['Query'] ="golf tdi";
$requestData['buyerPostalCode'] = "ll198dn";
$requestData['distanceMax']= "100";
$requestData['MinPrice']="500";
$requestData['MaxPrice']="2000";
$response = ph_bot($requestData);
*/