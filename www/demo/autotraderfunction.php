<?php
function at_bot($requestData){	
	$safeQuery = urlencode (utf8_encode($requestData['Query']));
	//$site  = $requestData['GlobalID'];
	$buyerPostalCode =$requestData['buyerPostalCode'];
  	$distanceMax = (string)$requestData['distanceMax'];
  	$priceRangeMin = $requestData['MinPrice'];
  	$priceRangeMax = $requestData['MaxPrice'];
  	$itemsPerRange = 60;//$requestData['ItemsPerRange'];
  	$url = "http://www.autotrader.co.uk/search/used/cars/keywords/$safeQuery/postcode/$buyerPostalCode/radius/$distanceMax/price-to/$priceRangeMax/price-from/$priceRangeMin/page/1/sort/locasc";
  	//echo $url;
  	$response= array();
  	$resultspage=scrape($url);
  	if($resultspage !=""){$response['ok']="1";} else {$response['ok']="0";}
  	$dom = new DOMDocument();
		@$dom->loadHTML($resultspage);
		$xpath = new DOMXPath($dom);
		$xtitles = $xpath->evaluate("//div[@class='vehicleTitle']/h2/a");
		$xprices = $xpath->evaluate("//div[@class='offerPrice']/span"); 
		$xmiles = $xpath->evaluate("//span[@class='mileage']"); 
		$xgears = $xpath->evaluate("//span[@class='transmission']"); 
		$xfuels = $xpath->evaluate("//span[@class='fuel']"); 
		$xengines = $xpath->evaluate("//span[@class='engine']"); 
		$xsubtitles = $xpath->evaluate("//div[@class='advertIconsPrice']/h3"); 
		$xdistances = $xpath->evaluate("//span[@class='distanceAmount']");
		$xdescs = $xpath->evaluate("//div[@class='searchResultMainText']");
		$xinsgrps = $xpath->evaluate("//div[@class='searchResultTools']/var");
		$xlinks = $xpath->evaluate("//div[@class='vehicleTitle']/h2/a");  
		$ximgs = $xpath->evaluate("//div[@class='advertMainImageContainer']/a/img");

		$x = 0;
		$c = $response['totalItems'] = (int)$xtitles->length;
		
		while ($x < $c) {  

			$xtitle = $xtitles->item($x);
			$response[$x]['title'] = $xtitle->textContent;
			
			$xlink = $xlinks->item($x);
			$response[$x]['link'] = $xlink->getAttribute('href');
			
			$xprice = $xprices->item($x);
			$response[$x]['price'] = $xprice->textContent;
			
			/*$xmile = $xmiles->item($x);
			$miles[$x] = $xmile->textContent;
			
			$xgear = $xgears->item($x);
			$gears[$x] = $xgear->textContent;
			
			$xfuel = $xfuels->item($x);
			$fuels[$x] = $xfuel->textContent;
			
			$xinsgrp = $xinsgrps->item($x);
			$xnsgrps[$x] = $xinsgrp->getAttribute('title');
			
			$xengine = $xengines->item($x);
			$engines[$x] = $xengine->textContent;*/
			
			$xsubtitle = $xsubtitles->item($x);
			$response[$x]['description'] = $xsubtitle->textContent;
			
			$xdistance = $xdistances->item($x);
			$response[$x]['location']['place'] = $xdistance->textContent;
			
			
			
			$xdesc = $xdescs->item($x);
			$response[$x]['description'] = $response[$x]['description']." ".trim($xdesc->textContent);
			
			
			$ximg = $ximgs->item($x);
			$response[$x]['imgurl'] = $ximg->getAttribute('src');
			$response[$x]['src'] = "at";
			$x++;
		
			}
			return $response;
		}
?>