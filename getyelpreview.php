<?php


$address = rawurlencode($_GET[ "address" ]);
$name = rawurlencode( $_GET[ "name" ] );
$city = rawurlencode( $_GET[ "city" ] );
$state = rawurlencode( $_GET[ "state" ] );
$country = rawurlencode( $_GET[ "country" ] );

$url = "https://api.yelp.com/v3/businesses/matches/best?name=".$name."&city=".$city."&state=".$state."&country=".$country."&address1=".$address;

$options = array(
  'http'=>array(
    'method'=>"GET",
    'header'=>"Authorization: Bearer YZ0AjrhkT2PiJTZyPd8yEcSnutVdkqKToG-7zmIYtE_tclrAzmrmgME6n1bsixTXuxher6Y5Lm1qYIX_DiO4K43hlKqJbUQbKB77iJEPiFDXrer9FMuPZ82v_9LNWnYx"
  )
);

$context = stream_context_create($options);
$yelpIdList = file_get_contents($url, false, $context);


$yelpIdListArray = json_decode( $yelpIdList );
$yelpId = $yelpIdListArray->businesses[ 0 ]->id;


$url = "https://api.yelp.com/v3/businesses/".$yelpId."/reviews";

$options = array(
  'http'=>array(
    'method'=>"GET",
    'header'=>"Authorization: Bearer YZ0AjrhkT2PiJTZyPd8yEcSnutVdkqKToG-7zmIYtE_tclrAzmrmgME6n1bsixTXuxher6Y5Lm1qYIX_DiO4K43hlKqJbUQbKB77iJEPiFDXrer9FMuPZ82v_9LNWnYx"
  )
);

$context = stream_context_create($options);
$yelpReviews = file_get_contents($url, false, $context);


header( 'Content-Type: application/json' );
echo $yelpReviews;
?>