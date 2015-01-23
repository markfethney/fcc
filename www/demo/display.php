<!DOCTYPE html>
<html> 
<head> 
  <meta http-equiv="content-type" content="text/html; charset=UTF-8" />
  <script src="https://maps.googleapis.com/maps/api/js?v=3.exp&sensor=false"></script>
  <style>html, body{ width: 100%; height: 100%; margin:0; padding:0;}</style>
</head> 
<body >
<?php 
	require_once('functions.php');
	require_once('autotraderfunction.php');
	require_once('ebayfunction.php');
	require_once('gumtreefunction.php');
	require_once('pistonheadsfunction.php');
	if($_POST){
		$requestData = array(
			'buyerPostalCode'  => trim(strtoupper(str_replace(' ','',$_POST['postcode']))),
			'distanceMax' => trim($_POST['distance']),
			'Query' => trim($_POST['query']),
			'MinPrice' => trim($_POST['min-price']),
			'MaxPrice' => trim($_POST['max-price']),
			'Debug' => ''
	);
	}
	//$at = at_bot($requestData);
	$ebay = ebay_bot($requestData);
	$gt = gt_bot($requestData);
	$at = at_bot($requestData);
	$ph = ph_bot($requestData);
	$total=$ebay['totalItems']+$gt['totalItems']+$at['totalItems']+$ph['totalItems'];
	$cars=array_merge($ebay,$gt);
	$allcars=array_merge($ebay,$gt,$at,$ph);
	?><pre><?php //print_r($allcars);?></pre>
  <script type="text/javascript">
function initialize() {
	
		
        //add map, the type of map
        var map = new google.maps.Map(document.getElementById('map'), {
            zoom:9,
            center: new google.maps.LatLng(53.34, -3.41),
            mapTypeId: google.maps.MapTypeId.ROADMAP
        });

        var customIcons = {
      ebay: {
        icon: 'http://labs.google.com/ridefinder/images/mm_20_blue.png'
      },
      gt: {
        icon: 'http://labs.google.com/ridefinder/images/mm_20_yellow.png'
      },
      at: {
        icon: 'http://labs.google.com/ridefinder/images/mm_20_green.png'
      },
      Pistonheads: {
        icon: 'http://labs.google.com/ridefinder/images/mm_20_red.png'
      }
    };

        //add locations from php
        var total = <?php echo $total?>;
        var locations = [<?php for ($i=0; $i<$total; $i++): $loc = $cars[$i]['location'];$latlon = latLong($loc);?>['<?php echo $cars[$i]['title'];?>','<?php echo $cars[$i]['location']['place'];?>','<?php echo $cars[$i]['imgurl'];?>' ,'<?php echo $cars[$i]['price'];?>','<?php echo $cars[$i]['link'];?>','<?php echo $latlon[0]?>','<?php echo $latlon[1]?>','<?php echo $cars[$i]['src']?>']<?php if($i < $total-1){echo ",\n";}?><?php endfor;?>];
        
        var infoWindow = new google.maps.InfoWindow();

        //add marker to each locations
        for (i = 0; i < total; i++) {
        	var title = locations[i][0];
        	var address = locations[i][1];
        	var contentString = '<h3>'+ locations[i][0]+ '</h3><img  style="float:left;" src="' +locations[i][2]+ '" width="75"/><p style="float:left; padding-left:10px;word-wrap: break-word;">Location: '+locations[i][1]+'<p>'+locations[i][7]+'</p>'+'<br/>Price: '+locations[i][3]+'<br/><a href="'+locations[i][4]+'" target="_blank" title="Opens in a new tab">View Car</a></p>';
        	var latit = locations[i][5];
        	var longi = locations[i][6];
        	var carPosition = new google.maps.LatLng(latit,longi);
        	var type= locations[i][7];
        	var icon = customIcons[type] || {};

        	
        	var marker = new google.maps.Marker({
			    map: map,
			    position: carPosition,//results[0].geometry.location,
	        	title: title,
	        	icon: icon.icon,
	        	animation: google.maps.Animation.DROP,
	        	zIndex: i
	        	});
		        
	        (function (marker, contentString) {
				        // Attaching a click event to the current marker
				       	//console.log(data[6]);
				        google.maps.event.addListener(marker, "click", function(e) {
					        infoWindow.setContent(contentString);
					        infoWindow.open(map, marker);
					    });
					})(marker, contentString);
		}//endfor
    }

    google.maps.event.addDomListener(window, 'load', initialize);
  </script>
 <div id="header">
 	<form method="post" action="">
 		<input type="text" name="query" placeholder="Search...?" value="<?php echo trim($_POST['query'])?>"/>
 		<input type="text" name="postcode" placeholder="Your Postcode" value="<?php echo trim($_POST['postcode'])?>"/>
 		<input type="text" name="distance" placeholder="Max distance" value="<?php echo trim($_POST['distance'])?>"/>
 		<input type="text" name="min-price" placeholder="from £££" value="<?php echo trim($_POST['min-price'])?>"/>
 		<input type="text" name="max-price" placeholder="to £££" value="<?php echo trim($_POST['max-price'])?>"/>
 		<input type="submit" value="Go!"/>
 	</form>
 </div>
 <!--<div id="map" style="width: 100%; height: 100%;"></div>-->
 <div id="textresults">
<h3>Showing <?=$total?> results for: <?php echo $requestData['Query'];?></h3>
<style>
body {font: 12px lucida grande, helvetica, arial;}
ul#results li{
	width:130px;
	height:350px;
	margin:5px;
	padding:5px;
	float:left;
	list-style-type: none;
	word-wrap: break-word;
	overflow: hidden;
}

</style>
 	<ul id="results">
 	<?php
 	//$cars = usort($cars, function($a, $b) { return $a["price"] - $b["price"; });

 	for($i=0;$i<$total;$i++){
	 	echo '
	 	<li>
	 		<h5>'.$allcars[$i]['title'].'</h5>
	 		<img src="'.$allcars[$i]['imgurl'].'" width="125"/>
	 		<p>Price: '.$allcars[$i]['price'].'</p>
	 		<p>Location: '.$allcars[$i]['location']['place'].'</p>
	 		<p><a href="'.$allcars[$i]['link'].'" title="See this car">View Listing on '.$allcars[$i]['src'].'</a></p>
	 	</li>
	 	';
 	}
 	?>
 	</ul>
 </div>
</body>
</html>