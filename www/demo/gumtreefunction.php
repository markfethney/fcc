<?php
function gt_bot($requestData) {

	$safeQuery = urlencode (utf8_encode($requestData['Query']));
	//$site  = $requestData['GlobalID'];
	$buyerPostalCode =$requestData['buyerPostalCode'];
  	$distanceMax = (string)$requestData['distanceMax'];
  	$priceRangeMin = $requestData['MinPrice'];
  	$priceRangeMax = $requestData['MaxPrice'];
  	$itemsPerRange = 60;//$requestData['ItemsPerRange'];
	$url = "http://www.gumtree.com/search?vehicle_make=&vehicle_mileage=&seller_type=&vehicle_body_type=&vehicle_fuel_type=&vehicle_age=&vehicle_transmission=&vehicle_engine_size=&min_price=$priceRangeMin&max_price=$priceRangeMax&photos_filter=Y&q=$safeQuery&search_location=$buyerPostalCode&distance=$distanceMax&amp;current_distance=0.0&category=cars&search_scope=title";
	
	//http://www.gumtree.com/search?q=mk4+golf&category=cars&search_location=ll198dn&distance=100.0&current_distance=100.0&vehicle_make=&vehicle_model=&vehicle_registration_year=&vehicle_mileage=&seller_type=&vehicle_body_type=&vehicle_fuel_type=&vehicle_transmission=&vehicle_engine_size=&min_price=&max_price=
	
	$page = scrape($url);
	//if($page !=""){$response['ok']=1} else {$response['ok']=0}
	//echo "$url<br>";
	$dom = new DOMDocument();
	@$dom->loadHTML($page);
	$xpath = new DOMXPath($dom);
	$xtitles_list = $xpath->evaluate("//h3/span[@class='ad-title-text']");
	$xlinks_list = $xpath->evaluate("//a[@class='description']");
	$xprices_list = $xpath->evaluate("//span[@class='price']");
	$xlocations_list = $xpath->evaluate("//span[@class='location']");
	$xdescriptions_list = $xpath->evaluate("//div[@class='ad-description']/span");
	$ximgurls_list = $xpath->evaluate("//a[@class='description']/img");
	
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
		$response[$x]['src'] = "gt";
		
		$x++;
	}
	return $response;
}
?><pre><?php //print_r($ebay);?></pre>