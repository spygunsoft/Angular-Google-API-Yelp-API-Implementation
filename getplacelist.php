<?php
$lat = $_GET[ "lat" ];
$lon = $_GET[ "lon" ];
if ( $_GET[ "location" ] != "" ) {

	$locationServer = "https://maps.googleapis.com/maps/api/geocode/json?address=" . rawurlencode($_GET[ "location" ] ) . "&key=AIzaSyDQkic1znJsDsLm9fPEFftxgrr-4xFRboM";
	$location = file_get_contents( $locationServer );
	$locationArray = json_decode( $location );
	$lat = $locationArray->results[ 0 ]->geometry->location->lat;
	$lon = $locationArray->results[ 0 ]->geometry->location->lng;

}
$keyword = $_GET[ "keyword" ];
$radius = $_GET[ "distance" ] * 1609.34;

$type = $_GET[ "category" ] != "default" ? ( "&type=" . $_GET[ "category" ] ) : ( "" );


$placeServer = "https://maps.googleapis.com/maps/api/place/nearbysearch/json?location=" . $lat . "," . $lon . "&radius=" . $radius . $type . "&keyword=" . rawurlencode( $keyword ) . "&key=AIzaSyDQkic1znJsDsLm9fPEFftxgrr-4xFRboM";


$placeListJson = file_get_contents( $placeServer );
$placeListArray = json_decode( $placeListJson, true );


$placeListArray[ "lat" ] = $lat;
$placeListArray[ "lon" ] = $lon;
$newplaceListJson = json_encode( $placeListArray );

header( 'Content-Type: application/json' );
echo $newplaceListJson;
?>