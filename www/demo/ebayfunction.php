<?php
function ebay_bot($requestData){
	require_once('DisplayUtils.php');  // functions to aid with display of information
	
	//error_reporting(E_ALL);  // turn on all errors, warnings and notices for easier debugging
	
	$results = '';
	
  $endpoint = 'http://svcs.ebay.com/services/search/FindingService/v1';  // URL to call
  $responseEncoding = 'XML';   // Format of the response

  $safeQuery = urlencode (utf8_encode($requestData['Query']));
  //$site  = $requestData['GlobalID'];
  $buyerPostalCode =$requestData['buyerPostalCode'];
  $distanceMax =$requestData['distanceMax'];
  $priceRangeMin = $requestData['MinPrice'];
  $priceRangeMax = $requestData['MaxPrice'];
  $itemsPerRange = 60;//$requestData['ItemsPerRange'];
  $debug = (boolean) $requestData['Debug'];
  $apicall = "$endpoint?OPERATION-NAME=findItemsAdvanced"
         . "&SERVICE-VERSION=1.0.0"
         . "&GLOBAL-ID=EBAY-GB"
         . "&SECURITY-APPNAME=MarkFeth-068e-4f15-8a4e-538e9c1e9beb" //replace with your app id
         . "&keywords=$safeQuery"
        . "&categoryId=9800"
         . "&buyerPostalCode=$buyerPostalCode"
         . "&paginationInput.entriesPerPage=$itemsPerRange"
         . "&paginationInput.pageNumber=1"
         . "&sortOrder=BestMatch"
        /* ."&aspectFilter(0).aspectName=Colour"
         ."&aspectFilter(0).aspectValueName=Red"
         ."&aspectFilter(0).aspectName=Fuel"
         ."&aspectFilter(0).aspectValueName=Diesel"
         ."&aspectFilter(0).aspectName=Doors"
         ."&aspectFilter(0).aspectValueName=3"
         ."&aspectFilter(0).aspectName=Engine+Size"
         ."&aspectFilter(0).aspectValueName=1896"*/
         . "&itemFilter(0).name=ListingType"
         . "&itemFilter(0).value=FixedPrice"
         . "&itemFilter(1).name=MinPrice"
         . "&itemFilter(1).value=$priceRangeMin"
         . "&itemFilter(2).name=MaxPrice"
         . "&itemFilter(2).value=$priceRangeMax"
         . "&itemFilter(3).name=MaxDistance"
         . "&itemFilter(3).value=$distanceMax"
         . "&affiliate.networkId=9"  // fill in your information in next 3 lines
         . "&affiliate.trackingId=1234567890"
         . "&affiliate.customId=456"
         . "&RESPONSE-DATA-FORMAT=$responseEncoding";

    if ($debug) {
      print "GET call = $apicall <br>";  // see GET request generated
    }
    // Load the call and capture the document returned by the Finding API
    $resp = simplexml_load_file($apicall);
    
    ?><pre><?php //print_r($resp);?></pre><?php

    // Check to see if the response was loaded, else print an error
    // Probably best to split into two different tests, but have as one for brevity
    if ($resp && $resp->paginationOutput->totalEntries > 0) {
      $response['totalItems'] = (int)$resp->paginationOutput->totalEntries;
      $response['ok'] = 1;

      // If the response was loaded, parse it and build links
      $i=0;
      foreach($resp->searchResult->item as $item) {
        $response[$i]['title'] = cleanHTML((string)$item->title);
        $response[$i]['link']  = cleanHTML((string)$item->viewItemURL);
        $response[$i]['price'] = sprintf("%01.2f", $item->sellingStatus->convertedCurrentPrice);
        //$response['item'.$i]['postcode'] = (string)$item->postalCode;
        $response[$i]['location']['postcode'] = (string)$item->postalCode;
        $response[$i]['location']['place'] = (string)$item->location;
        $response[$i]['timeLeft'] = getPrettyTimeFromEbayTime($item->sellingStatus->timeLeft);
        if ($item->galleryURL) {
        	$response[$i]['imgurl'] = (string)$item->galleryURL;
        } else {
          	$response[$i]['imgurl'] = "http://pics.ebaystatic.com/aw/pics/express/icons/iconPlaceholder_96x96.gif";
        }
        $response[$i]['description'] = "";
        $response[$i]['src'] = "ebay";
        $i++;
    }
    }
    // If there was no response, print an error
    else {
    	$response['totalItems'] = $resp->paginationOutput->totalEntries;
      	$response['ok'] = 0;
    }
	return $response;
	} // fn
?>