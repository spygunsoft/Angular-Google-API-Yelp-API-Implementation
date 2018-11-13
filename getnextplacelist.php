<?php

$pagetoken = $_GET[ "pagetoken" ];

$placeServer = "https://maps.googleapis.com/maps/api/place/nearbysearch/json?pagetoken=" . $pagetoken ."&key=AIzaSyDQkic1znJsDsLm9fPEFftxgrr-4xFRboM";


$placeListJson = file_get_contents( $placeServer );
$placeListArray = json_decode( $placeListJson, true );


$newplaceListJson = json_encode( $placeListArray );

header( 'Content-Type: application/json' );
echo $newplaceListJson;
?>