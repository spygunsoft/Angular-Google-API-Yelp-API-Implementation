<?php
header( 'Access-Control-Allow-Origin: *' );
?>
<!doctype html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<title>HW8Spring2018</title>
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/open-iconic/1.1.1/font/css/open-iconic-bootstrap.min.css">
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/rateYo/2.3.2/jquery.rateyo.min.css">

	<style>
		.form-group.required .col-form-label:after {
			content: "*";
			color: red;
		}
		
		.fav-button {
			color: #FFE500;
		}
		
		#map,
		#pano {
			width: 100%;
			height: 500px;
		}
		
		#panelRoute {
			width: 100%;
		}
		
		#panelRoute select {
			width: 100%;
		}
		
		@media (min-width: 576px) {
			.card-columns {
				column-count: 4;
			}
		}
		
		@media (min-width: 768px) {
			.card-columns {
				column-count: 4;
			}
		}
		
		@media (min-width: 992px) {
			.card-columns {
				column-count: 4;
			}
		}
		
		@media (min-width: 1200px) {
			.card-columns {
				column-count: 4;
			}
		}
		
		.animation1 {
			transition: all linear 0.5s;
			position: relative;
			left: 0;
		}
		
		.animation2 {
			transition: all linear 0.5s;
			opacity: 1;
			left: 0;
		}
		
		.animation1.ng-hide {
			left: -200px;
		}
		
		.animation2.ng-hide {
			opacity: 0;
		}
	</style>

</head>

<body>

	<div ng-app="placeSearch" ng-controller="formController" class="container-fluid">


		<div class="row justify-content-center">
			<div class="col-md-7 col-11 bg-light border rounded mt-2 py-3">
				<div class="row justify-content-center">
					<div class="col-md-9">
						<div class="row">
							<div class="col-md-9 offset-md-3 mb-3">
								<h4>Travel and Entertainment Search</h4>
							</div>
						</div>
						<form class="form" novalidate>
							<div class="form-group row">
								<label for="keywordForm" class="col-form-label col-md-3">Keyword</label>
								<div class="col-md-9">
									<input type="text" class="form-control" id="keywordForm" ng-model="keywordForm" ng-change="keywordCheck()" ng-class="{'is-invalid': isKeywordInvalid}" ng-trim="false">
									<div class="invalid-feedback">Please enter a keyword</div>
								</div>

							</div>
							<div class="form-group row">
								<label for="categoryForm" class="col-form-label col-md-3">Category</label>
								<div class="col-md-6">
									<select class="form-control custom-select" id="categoryForm" ng-model="categoryList.selected" ng-options="x.label for x in categoryList.available track by x.label">
								</select>
								










								</div>
							</div>
							<div class="form-group row">
								<label for="distanceForm" class="col-form-label col-md-3">Distance (miles)</label>
								<div class="col-md-6"><input type="text" class="form-control" id="distanceForm" ng-model="distanceForm" placeholder="10">
								</div>
							</div>
							<div class="form-group row">
								<label for="fromForm" class="col-form-label col-md-3">From</label>
								<div class="col-md-6">
									<div class="custom-control custom-radio">
										<input class="custom-control-input" type="radio" ng-model="fromForm" name="fromForm" ng-change="fromCheck()" id="currentLocation" value="currentLocation">
										<label class="custom-control-label" for="currentLocation">Current Location</label>
									</div>
									<div class="custom-control custom-radio">
										<input class="custom-control-input" type="radio" ng-model="fromForm" name="fromForm" ng-change="fromCheck()" id="otherLocation" value="otherLocation">
										<label class="custom-control-label" for="otherLocation">Other, please specify</label>
										<input type="text" class="form-control" id="otherLocationForm" placeholder="Enter a location" ng-model="otherLocationForm" name="otherLocationForm" ng-readonly="disableOther" ng-change="otherLocationCheck()" ng-class="{'is-invalid': isOtherLocationInvalid}">
										<div class="invalid-feedback">Please enter a location</div>
									</div>

								</div>
							</div>
							<div class="form-group row">
								<div class="col-md-6">
									<button type="button" class="btn btn-primary" ng-disabled="disableSubmit" ng-click="getPlaceList()"><span class="oi oi-magnifying-glass"></span> Search</button>
									<button type="button" class="btn btn-outline-secondary" ng-click="clearAll()">Clear</button>
								</div>
							</div>
						</form>
					</div>
				</div>

			</div>
		</div>

		<div class="row justify-content-center">
			<div class="col-md-10 mt-4">
				<ul class="nav justify-content-center nav-pills mb-3" id="placeListTab" role="tablist">
					<li class="nav-item">
						<a class="nav-link active" id="results-tab" data-toggle="tab" href="#results" role="tab" aria-controls="results" aria-selected="true" ng-click="showPlaceList = true;showPlaceDetail = false;">Results</a>
					</li>
					<li class="nav-item">
						<a class="nav-link" id="favourites-tab" data-toggle="tab" href="#favourites" role="tab" aria-controls="favourites" aria-selected="false" ng-click="showBookmarkList = true;showPlaceDetail = false;">Favourites</a>
					</li>
				</ul>
			</div>
		</div>

		<div class="row justify-content-center" ng-show="showProgress" ng-hide="!(showProgress)">
			<div class="col-md-10 mt-4">
				<div class="progress">
					<div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" style="width: {{loadProgress}}%"></div>
				</div>
			</div>
		</div>

		<div class="row justify-content-center">
			<div class="col-md-10 mt-4">


				<div class="tab-content" id="myTabContent">
					<div class="tab-pane fade show active animation1" id="results" role="tabpanel" aria-labelledby="results-tab" ng-show="showPlaceList" ng-hide="!(showPlaceList)">
						<div class="alert alert-warning animation1" role="alert" ng-hide="(placeList).length">
							No Records.
						</div>


						<div class="text-right" ng-show="(placeList).length">
							<button type="button" class="btn btn-outline-secondary px-4 py-2 my-4" ng-click="showPlaceDetailTab()" ng-disabled="disabledDetails">Details <span class="oi oi-chevron-right"></button>
						</div>
						<div class="table-responsive hideanimate showanimate" ng-show="(placeList).length">
							<table class="table table-hover">
								<thead>
									<tr>
										<th>#</th>
										<th>Category</th>
										<th>Name</th>
										<th>Address</th>
										<th>Favourite</th>
										<th>Details</th>
									</tr>
								</thead>
								<tbody>
									<tr ng-repeat="x in placeList | limitTo : 20 : 0" ng-class="{'table-warning': x.selected}">
										<th>{{$index+1}}</th>
										<td><img class="img-responsive" ng-src="{{x.icon}}">
										</td>
										<td>{{x.name}}</td>
										<td>{{x.vicinity}}</td>

										<td><button type="button" class="btn btn-outline-secondary" ng-click="setBookmark(x.place_id,1)"><span class="oi oi-star" ng-if="!(x.bookmark)"></span><span class="oi oi-star av-button fav-button" ng-if="x.bookmark"></span></button>
										</td>
										<td><button type="button" class="btn btn-outline-secondary" ng-click="getPlaceDetail(x,$index,1)"><span class="oi oi-chevron-right pt-2 pl-1"></span></button>
										</td>
									</tr>
								</tbody>
							</table>
						</div>
						<div class="table-responsive hideanimate showanimate" ng-hide="page2Hide" ng-show="page2Show">
							<table class="table table-hover">
								<thead>
									<tr>
										<th>#</th>
										<th>Category</th>
										<th>Name</th>
										<th>Address</th>
										<th>Favourite</th>
										<th>Details</th>
									</tr>
								</thead>
								<tbody>
									<tr ng-repeat="x in placeList | limitTo : 20 : 20" ng-class="{'table-warning': x.selected}">
										<th>{{$index+21}}</th>
										<td><img class="img-responsive" ng-src="{{x.icon}}">
										</td>
										<td>{{x.name}}</td>
										<td>{{x.vicinity}}</td>

										<td><button type="button" class="btn btn-outline-secondary" ng-click="setBookmark(x.place_id,1)"><span class="oi oi-star" ng-if="!(x.bookmark)"></span><span class="oi oi-star av-button fav-button" ng-if="x.bookmark"></span></button>
										</td>
										<td><button type="button" class="btn btn-outline-secondary" ng-click="getPlaceDetail(x,($index+20),1)"><span class="oi oi-chevron-right"></span></button>
										</td>
									</tr>
								</tbody>
							</table>
						</div>
						<div class="table-responsive hideanimate showanimate" ng-hide="page3Hide" ng-show="page3Show">
							<table class="table table-hover">
								<thead>
									<tr>
										<th>#</th>
										<th>Category</th>
										<th>Name</th>
										<th>Address</th>
										<th>Favourite</th>
										<th>Details</th>
									</tr>
								</thead>
								<tbody>
									<tr ng-repeat="x in placeList  | limitTo : 20 : 39" ng-class="{'table-warning': x.selected}">
										<th>{{$index+41}}</th>
										<td><img class="img-responsive" ng-src="{{x.icon}}">
										</td>
										<td>{{x.name}}</td>
										<td>{{x.vicinity}}</td>

										<td><button type="button" class="btn btn-outline-secondary" ng-click="setBookmark(x.place_id,1)"><span class="oi oi-star" ng-if="!(x.bookmark)"></span><span class="oi oi-star av-button fav-button" ng-if="x.bookmark"></span></button>
										</td>
										<td><button type="button" class="btn btn-outline-secondary" ng-click="getPlaceDetail(x,($index+39),1)"><span class="oi oi-chevron-right"></span></button>
										</td>
									</tr>
								</tbody>
							</table>
						</div>
						<div class="text-center">
							<button type="button" class="btn btn-outline-secondary mr-3 px-4 py-2 hideanimate showanimate" ng-hide="prevButtonHide" ng-show="prevButtonShow" ng-click="prevPage()">Previous</button>
							<button type="button" class="btn btn-outline-secondary px-4 py-2 hideanimate showanimate" ng-hide="nextButtonHide" ng-show="nextButtonShow" ng-click="nextPage()">Next</button>
						</div>


					</div>
					<div class="tab-pane fade animation1" id="favourites" role="tabpanel" aria-labelledby="favourites-tab" ng-show="showBookmarkList" ng-hide="!(showBookmarkList)">
						<div class="alert alert-warning animation1" role="alert" ng-hide="(bookmarkList).length">
							No Records.
						</div>

						<div class="text-right" ng-show="(bookmarkList).length">
							<button type="button" class="btn btn-outline-secondary px-4 py-2 my-4" ng-disabled="disabledBookmarkDetails" ng-click="showPlaceDetailTab()">Details <span class="oi oi-chevron-right"></button>
						</div>
						<div class="table-responsive hideanimate showanimate" ng-show="(bookmarkList).length">
							<table class="table table-hover">
								<thead>
									<tr>
										<th>#</th>
										<th>Category</th>
										<th>Name</th>
										<th>Address</th>
										<th>Favourite</th>
										<th>Details</th>
									</tr>
								</thead>
								<tbody>
									<tr ng-repeat="x in bookmarkList | limitTo : 20 : 0" ng-class="{'table-warning': x.selected}">
										<th>{{$index+1}}</th>
										<td><img class="img-responsive" ng-src="{{x.icon}}">
										</td>
										<td>{{x.name}}</td>
										<td>{{x.vicinity}}</td>

										<td><button type="button" class="btn btn-outline-secondary" ng-click="removeBookmark(x.place_id)"><span class="oi oi-trash"></span></button>
										</td>
										<td><button type="button" class="btn btn-outline-secondary" ng-click="getPlaceDetail(x,$index,2)"><span class="oi oi-chevron-right pt-2 pl-1"></span></button>
										</td>
									</tr>
								</tbody>
							</table>
						</div>
						<div class="table-responsive hideanimate showanimate" ng-hide="bpage2Hide" ng-show="bpage2Show">
							<table class="table table-hover">
								<thead>
									<tr>
										<th>#</th>
										<th>Category</th>
										<th>Name</th>
										<th>Address</th>
										<th>Favourite</th>
										<th>Details</th>
									</tr>
								</thead>
								<tbody>
									<tr ng-repeat="x in bookmarkList | limitTo : 20 : 20" ng-class="{'table-warning': x.selected}">
										<th>{{$index+21}}</th>
										<td><img class="img-responsive" ng-src="{{x.icon}}">
										</td>
										<td>{{x.name}}</td>
										<td>{{x.vicinity}}</td>

										<td><button type="button" class="btn btn-outline-secondary" ng-click="removeBookmark(x.place_id)"><span class="oi oi-trash"></span></button>
										</td>
										<td><button type="button" class="btn btn-outline-secondary" ng-click="getPlaceDetail(x,($index+20),2)"><span class="oi oi-chevron-right"></span></button>
										</td>
									</tr>
								</tbody>
							</table>
						</div>
						<div class="table-responsive hideanimate showanimate" ng-hide="bpage3Hide" ng-show="bpage3Show">
							<table class="table table-hover">
								<thead>
									<tr>
										<th>#</th>
										<th>Category</th>
										<th>Name</th>
										<th>Address</th>
										<th>Favourite</th>
										<th>Details</th>
									</tr>
								</thead>
								<tbody>
									<tr ng-repeat="x in bookmarkList  | limitTo : 20 : 39" ng-class="{'table-warning': x.selected}">
										<th>{{$index+41}}</th>
										<td><img class="img-responsive" ng-src="{{x.icon}}">
										</td>
										<td>{{x.name}}</td>
										<td>{{x.vicinity}}</td>

										<td><button type="button" class="btn btn-outline-secondary" ng-click="removeBookmark(x.place_id)"><span class="oi oi-trash"></span></button>
										</td>
										<td><button type="button" class="btn btn-outline-secondary" ng-click="getPlaceDetail(x,($index+39),2)"><span class="oi oi-chevron-right"></span></button>
										</td>
									</tr>
								</tbody>
							</table>
						</div>
						<div class="text-center">
							<button type="button" class="btn btn-outline-secondary mr-3 px-4 py-2 hideanimate showanimate" ng-hide="bprevButtonHide" ng-show="bprevButtonShow" ng-click="bprevPage()">Previous</button>
							<button type="button" class="btn btn-outline-secondary px-4 py-2 hideanimate showanimate" ng-hide="bnextButtonHide" ng-show="bnextButtonShow" ng-click="bnextPage()">Next</button>
						</div>

					</div>
				</div>
			</div>
		</div>



		<div class="row justify-content-center animation1" ng-show="showPlaceDetail" ng-hide="!(showPlaceDetail)">
			<div class="col-md-10 mt-5">

				<div class="row">
					<div class="col-md-12 text-center">
						<h4>{{placeInfo.name}}</h4>
					</div>
				</div>
				<div class="row">
					<div class="col-md-1 ">
						<button type="button" class="btn btn-outline-secondary hideanimate showanimate" ng-click="showPlaceListTab()"><span class="oi oi-chevron-left"> List</button>
					</div>
					<div class="col-md-2 offset-md-9 text-right">
						<button type="button" class="btn btn-outline-secondary" ng-click="setBookmark(placeInfo.place_id,2)"><span class="oi oi-star" ng-if="!(starDetail)"></span><span class="oi oi-star av-button fav-button" ng-if="starDetail"></span></button>
						<a href="https://twitter.com/intent/tweet?text={{placeInfo.name}} located at {{placeInfo.formatted_address}}. Website: {{placeInfo.website}}"><img src="http://cs-server.usc.edu:45678/hw/hw8/images/Twitter.png" style="width:40px;height:40px"></a>
					</div>
				</div>
				<nav>
					<div class="nav nav-tabs justify-content-end" id="nav-tab" role="tablist">
						<a class="nav-item nav-link active" id="nav-info-tab" data-toggle="tab" href="#nav-info" role="tab" aria-controls="nav-info" aria-selected="true">Info</a>
						<a class="nav-item nav-link" id="nav-photos-tab" data-toggle="tab" href="#nav-photos" role="tab" aria-controls="nav-photos" aria-selected="false">Photos</a>
						<a class="nav-item nav-link" id="nav-map-tab" data-toggle="tab" href="#nav-map" role="tab" aria-controls="nav-map" aria-selected="false">Map</a>
						<a class="nav-item nav-link" id="nav-reviews-tab" data-toggle="tab" href="#nav-reviews" role="tab" aria-controls="nav-reviews" aria-selected="false">Reviews</a>
					</div>
				</nav>
				<div class="tab-content pt-3" id="nav-tabContent">
					<div class="tab-pane fade show active" id="nav-info" role="tabpanel" aria-labelledby="nav-info-tab">
						<div class="table-responsive">
							<table class="table table-striped">
								<tbody>
									<tr ng-if="placeInfo.formatted_address?true:false">
										<th>Address</th>
										<td id="placeDetailAddress">{{placeInfo.formatted_address}}</td>
									</tr>
									<tr ng-if="placeInfo.international_phone_number?true:false">
										<th>Phone Number</th>
										<td id="placeDetailPhone">{{placeInfo.international_phone_number}}</td>
									</tr>
									<tr ng-if="placeInfo.price_level?true:false">
										<th>Price Level</th>
										<th id="placeDetailPrice">{{placeInfo.price_level==1?'$':placeInfo.price_level==2?'$$':placeInfo.price_level==3?'$$$':placeInfo.price_level==4?'$$$$':placeInfo.price_level==5?'$$$$$':''}}</th>
									</tr>
									<tr ng-if="placeInfo.rating?true:false">
										<th>Rating</th>
										<td id="placeDetailRating">
											<div class="float-left">{{placeInfo.rating}} </div>
											<div class="float-left pt-1" id="rateYo"></div>
										</td>
									</tr>
									<tr ng-if="placeInfo.url?true:false">
										<th>Google Page</th>
										<td id="placeDetailPage"><a href="{{placeInfo.url}}" target="_blank">{{placeInfo.url}}</a>
										</td>
									</tr>
									<tr ng-if="placeInfo.website?true:false">
										<th>Website</th>
										<td id="placeDetail"><a href="{{placeInfo.website}}" target="_blank">{{placeInfo.website}}</a>
										</td>
									</tr>
									<tr ng-if="placeInfo.opening_hours?true:false">
										<th>Hours</th>
										<td id="placeHours">
											{{placeInfo.opening_hours.open_now == true?'Open now: ':'Closed'}} {{placeInfo.opening_hours.open_now == true?placeInfoHours[0].hours:' '}} <a href="" data-toggle="modal" data-target="#hoursModal">Daily open hours</a>
										</td>
									</tr>

								</tbody>
							</table>
						</div>
					</div>
					<div class="tab-pane fade" id="nav-photos" role="tabpanel" aria-labelledby="nav-photos-tab">
						<div class="alert alert-warning animation1" role="alert" ng-hide="(placeInfo.photos).length">
							No Records.
						</div>
						<div class="card-columns">
							<div class="card" ng-repeat="x in placeInfo.photos"><a href="{{x.getUrl({maxWidth: 1920})}}" target="_blank"><img ng-src="{{x.getUrl({maxWidth: 1920})}}" class="card-img-top img-thumbnail"></a>
							</div>
						</div>
					</div>
					<div class="tab-pane fade" id="nav-map" role="tabpanel" aria-labelledby="nav-map-tab">

						<form class="form" novalidate>
							<div class="form-row mb-3">
								<div class="col-md-4">
									<label for="mapFromForm">From</label>
									<input type="text" class="form-control" id="mapFromForm" ng-model="mapFromForm" ng-change="mapFromCheck()" ng-class="{'is-invalid': isMapFromInvalid}" ng-trim="false">
									<div class="invalid-feedback">Please enter a keyword</div>
								</div>
								<div class="col-md-4">
									<label for="mapToForm">To</label>
									<input type="text" class="form-control" id="mapToForm" ng-model="mapToForm" readonly>
								</div>
								<div class="col-md-2">
									<label for="mapTravelModeForm">Travel Mode</label>

									<select class="form-control custom-select" id="mapTravelModeForm" ng-model="travelMode">
										<option value="DRIVING" selected>Driving</option>
										<option value="BCYCLING">Bicycling</option>
										<option value="TRANSIT">Transit</option>
										<option value="WALKING">Walking</option>
									</select>

								</div>
								<div class="col-md-1">
									<label class="invisible" for="getDirectionButton">Button</label>
									<button type="button" class="form-control btn btn-primary" id="getDirectionButton" ng-click="getDirection()" ng-disabled="disableGetDirection">Get Directions</button>
								</div>
							</div>
							<div class="form-row mb-3">
								<div class="col-md-1" ng-class="{'d-none' : mapViewToggle }">
									<button type="button" class="btn btn-outline-secondary" id="toggleStreetView" ng-click="getStreetView()"><img class="img-responsive" style="width:50px;height:50px" src="http://cs-server.usc.edu:45678/hw/hw8/images/Pegman.png"></button>
								</div>
								<div class="cold-md-1" ng-class="{'d-none' : streetViewToggle }">
									<button type="button" class="btn btn-outline-secondary" id="toggleMapView" ng-click="getMapView()"><img class="img-responsive" style="width:50px;height:50px" src="http://cs-server.usc.edu:45678/hw/hw8/images/Map.png"></button>
								</div>
							</div>


						</form>

						<div class="row" ng-class="{'d-none' : mapViewToggle }">
							<div id="map"></div>
						</div>
						<div class="row" ng-class="{'d-none' : streetViewToggle }">
							<div id="pano"></div>
						</div>
						<div class="row" ng-class="{'d-none' : panelViewToggle }">
							<div id="panelRoute"></div>
						</div>
					</div>
					<div class="tab-pane fade" id="nav-reviews" role="tabpanel" aria-labelledby="nav-reviews-tab">
						<div class="row">
							<div class="col-md-12 mb-3">
								<div class="dropdown float-left mr-2">
									<button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownReviewType" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">{{reviewTabLabel}}</button>
									<div class="dropdown-menu" aria-labelledby="dropdownReviewType">
										<button class="dropdown-item" type="button" ng-click="getReview('google')">Google Reviews</button>
										<button class="dropdown-item" type="button" ng-click="getReview('yelp')">Yelp Reviews</button>
									</div>
								</div>

								<div class="dropdown float-left">
									<button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownOrderType" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">{{sortName}}</button>
									<div class="dropdown-menu" aria-labelledby="dropdownOrderType">
										<button class="dropdown-item" type="button" ng-click="sortBy('default',false)">Default Order</button>
										<button class="dropdown-item" type="button" ng-click="sortBy('rating',true)">Highest Rating</button>
										<button class="dropdown-item" type="button" ng-click="sortBy('rating',false)">Lowest Rating</button>
										<button class="dropdown-item" type="button" ng-click="sortBy('time',true)">Most Recent</button>
										<button class="dropdown-item" type="button" ng-click="sortBy('time',false)">Least Recent</button>
									</div>
								</div>
							</div>
						</div>




						<ul class="list-unstyled showanimate2 hideanimate2" ng-show="googleReviewTab" ng-hide="!(googleReviewTab)">
							<div class="alert alert-warning animation1" role="alert" ng-hide="(placeInfo.reviews).length">
								No Records.
							</div>
							<li class="media px-3 py-3 mb-3 border border-secondary" ng-repeat="x in placeInfo.reviews | orderBy:propertyName:reverse">
								<a href="{{x.author_url}}" target="_blank"><img class="img-responsive float-right mr-3" ng-src="{{x.profile_photo_url}}" style="width:100px;height:100px"></a>
								<div class="media-body">
									<p><a href="{{x.author_url}}" target="_blank">{{x.author_name}}</a>

									</p>
									<p>
										<div class="float-left mr-2 pt-1" id="reviewRatingGoogle{{$index}}">{{x.rating}}</div>
										<div class=" text-muted" id="reviewDateGoogle{{$index}}">{{x.time}}</div>
									</p>
									<p>{{x.text}}
									</p>
								</div>
							</li>

						</ul>

						<ul class="list-unstyled showanimate2 hideanimate2" ng-show="yelpReviewTab" ng-hide="!(yelpReviewTab)">
							<div class="alert alert-warning animation1" role="alert" ng-hide="(yelpReviewList.reviews).length">
								No Records.
							</div>
							<li class="media px-3 py-3 mb-3 border border-secondary" ng-repeat="x in yelpReviewList.reviews | orderBy:(propertyName=='time'?'time_created':propertyName):reverse">
								<a href="{{x.url}}" target="_blank"><img class="img-responsive rounded-circle float-right mr-3" ng-src="{{x.user.image_url}}" style="width:100px;height:100px"></a>
								<div class="media-body">
									<p><a href="{{x.url}}" target="_blank">{{x.user.name}}</a>

									</p>
									<p>
										<div class="float-left mr-2 pt-1" id="reviewRatingYelp{{$index}}">{{x.rating}}</div>
										<div class="text-muted" id="reviewDateYelp{{$index}}">{{x.time_created}}</div>
									</p>
									<p>{{x.text}}
									</p>
								</div>
							</li>

						</ul>






					</div>
				</div>
			</div>
		</div>
		<div class="row justify-content-center">
			<div class="col-md-10 mt-4">
				<div class="alert alert-danger animation1" role="alert" ng-show="showError">
					{{errorMessage}}
				</div>
				<div class="alert alert-warning animation1" role="alert" ng-show="showWarning">
					{{warningMessage}}
				</div>
			</div>
		</div>

		<div class="modal fade" id="hoursModal" tabindex="-1" role="dialog" aria-labelledby="hoursModalLabel" aria-hidden="true">
			<div class="modal-dialog" role="document">
				<div class="modal-content">
					<div class="modal-header">
						<h4 class="modal-title" id="hoursModalLabel">Open hours</h4>
						<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>



					</div>
					<div class="modal-body">
						<table class="table">
							<tbody>
								<tr ng-repeat="x in placeInfoHours">
									<td ng-if="$index==0?false:true">{{x.day | replaceDay}}</td>
									<th ng-if="$index==0?true:false">{{x.day | replaceDay}}</th>
									<td ng-if="$index==0?false:true">{{x.hours}}</td>
									<th ng-if="$index==0?true:false">{{x.hours}}</th>
								</tr>
							</tbody>
						</table>
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
					</div>
				</div>
			</div>
		</div>

	</div>



	<script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
	<script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.6.9/angular.min.js"></script>
	<script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.6.4/angular-animate.js"></script>
	<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDQkic1znJsDsLm9fPEFftxgrr-4xFRboM&libraries=places&callback=initAutocomplete" async defer></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.22.0/moment-with-locales.min.js"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/rateYo/2.3.2/jquery.rateyo.min.js"></script>
	<script type="text/javascript" async src="https://platform.twitter.com/widgets.js"></script>
	</script>
	<script>
		var lati;
		var long;
		var placeLati;
		var placeLong;
		var latiHere;
		var longHere;
		var globalmap;

		var otherlocationautocomplete, keywordautocomplete, mapfromautocomplete;
		var mainkeyword;

		var serverlocation = "http://csci571-anugroho-php.us-east-2.elasticbeanstalk.com/";




		function initialize() {
			initAutocomplete();

		}


		function initAutocomplete() {
			keywordautocomplete = new google.maps.places.Autocomplete(
				/** @type {!HTMLInputElement} */
				( document.getElementById( 'keywordForm' ) ), {
					types: [ 'geocode' ]
				} );
			otherlocationautocomplete = new google.maps.places.Autocomplete(
				/** @type {!HTMLInputElement} */
				( document.getElementById( 'otherLocationForm' ) ), {
					types: [ 'geocode' ]
				} );

			mapfromautocomplete = new google.maps.places.Autocomplete(
				/** @type {!HTMLInputElement} */
				( document.getElementById( 'mapFromForm' ) ), {
					types: [ 'geocode' ]
				} );

			keywordautocomplete.addListener( 'place_changed', fillInAddress );

		}

		function fillInAddress() {
			// Get the place details from the autocomplete object.
			var place = keywordautocomplete.getPlace();
			mainkeyword = place.name + ", " + place.formatted_address;
		}

		function geolocate() {
			if ( navigator.geolocation ) {
				navigator.geolocation.getCurrentPosition( function ( position ) {
					var geolocation = {
						lat: position.coords.latitude,
						lng: position.coords.longitude
					};
					var circle = new google.maps.Circle( {
						center: geolocation,
						radius: position.coords.accuracy
					} );
					keywordautocomplete.setBounds( circle.getBounds() );
				} );
			}
		}







		var app = angular.module( "placeSearch", [ 'ngAnimate' ] );

		app.filter( 'replaceSpace', function () {
			return function ( input ) {
				return input.replace( / /g, '_' );
			};
		} );

		app.filter( 'replaceDay', function () {
			return function ( input ) {
				if ( input == 0 ) {
					return 'Sunday';
				}
				if ( input == 1 ) {
					return 'Monday';
				}
				if ( input == 2 ) {
					return 'Tuesday';
				}
				if ( input == 3 ) {
					return 'Wednesday';
				}
				if ( input == 4 ) {
					return 'Thursday';
				}
				if ( input == 5 ) {
					return 'Friday';
				}
				if ( input == 6 ) {
					return 'Saturday';
				}
			};
		} );

		app.controller( 'formController', function ( $scope, $http, $location, $q ) {
			$scope.travelMode = "DRIVING";
			$scope.disableSubmit = true;
			$scope.disableOther = true;
			$scope.page1 = false;
			$scope.page2 = false;
			$scope.page3 = false;
			$scope.bookmarkList = JSON.parse( localStorage.myBookmark || "null" );
			$scope.disabledBookmarkDetails = true;
			$scope.showBookmarkList = true;
			$scope.starDetail = false;
			$scope.categoryList = {
				available: [ {
					label: "Default",
					value: "default"
				}, {
					label: "Airport",
					value: "airport"
				}, {
					label: "Amusement Park",
					value: "amusement_park"
				}, {
					label: "Aquarium",
					value: "aquarium"
				}, {
					label: "Art Gallery",
					value: "art_gallery"
				}, {
					label: "Bakery",
					value: "bakery"
				}, {
					label: "Bar",
					value: "bar"
				}, {
					label: "Beauty Salon",
					value: "beauty_salon"
				}, {
					label: "Bowling Alley",
					value: "bowling_alley"
				}, {
					label: "Bus Station",
					value: "bus_station"
				}, {
					label: "Cafe",
					value: "cafe"
				}, {
					label: "Campground",
					value: "campground"
				}, {
					label: "Car Rental",
					value: "car_rental"
				}, {
					label: "Casino",
					value: "casino"
				}, {
					label: "Lodging",
					value: "lodging"
				}, {
					label: "Movie Theater",
					value: "movie_theater"
				}, {
					label: "Museum",
					value: "museum"
				}, {
					label: "Night Club",
					value: "night_club"
				}, {
					label: "Park",
					value: "park"
				}, {
					label: "Parking",
					value: "parking"
				}, {
					label: "Restaurant",
					value: "restaurant"
				}, {
					label: "Shopping Mall",
					value: "shopping_mall"
				}, {
					label: "Stadium",
					value: "stadium"
				}, {
					label: "Subway Station",
					value: "subway_station"
				}, {
					label: "Taxi Stand",
					value: "taxi_stand"
				}, {
					label: "Train Station",
					value: "train_station"
				}, {
					label: "Transit Station",
					value: "transit_station"
				}, {
					label: "Travel Agency",
					value: "travel_agency"
				}, {
					label: "Zoo",
					value: "zoo"
				} ],
				selected: {
					label: "Default",
					value: "default"
				}
			};
			$scope.submitValidation = function () {
				var keyword = $scope.keywordForm;
				keyword = keyword.replace( /\s/g, '' );
				if ( ( lati != null ) && ( lati != "" ) && ( keyword != "" ) ) {
					$scope.disableSubmit = false;
				} else {
					$scope.disableSubmit = true;
				}

				if ( ( $scope.fromForm == "otherLocation" ) && ( $scope.otherLocationForm == "" ) ) {
					$scope.disableSubmit = true;
				}

				if ( ( $scope.fromForm == "otherLocation" ) && ( $scope.otherLocationForm == null ) ) {
					$scope.disableSubmit = true;
				}
			};
			$scope.keywordCheck = function () {
				var keyword = $scope.keywordForm;
				keyword = keyword.replace( /\s/g, '' );
				if ( keyword.length == 0 ) {
					$scope.isKeywordInvalid = true;
				} else {
					$scope.isKeywordInvalid = false;
				};
				$scope.submitValidation();
			};
			$scope.fromForm = "currentLocation";
			$scope.fromCheck = function () {
				if ( $scope.fromForm == "otherLocation" ) {
					$scope.disableOther = false;
				} else {
					$scope.disableOther = true;
					$scope.isOtherLocationInvalid = false;
				};
				$scope.submitValidation();
			};
			$scope.otherLocationCheck = function () {
				var keyword = $scope.otherLocationForm;
				keyword = keyword.replace( /\s/g, '' );
				if ( keyword.length == 0 ) {
					$scope.isOtherLocationInvalid = true;
				} else {
					$scope.isOtherLocationInvalid = false;
				};
				$scope.submitValidation();
			};
			$scope.isMapFromInvalid = false;
			$scope.mapFromForm = "Your Location";
			$scope.mapFromCheck = function () {
				var keyword = $scope.mapFromForm;
				keyword = keyword.replace( /\s/g, '' );
				if ( keyword.length == 0 ) {
					$scope.isMapFromInvalid = true;
				} else {
					$scope.isMapFromInvalid = false;
				};
				$scope.submitMapFromValidation();
			};
			$scope.disableGetDirection = false;
			$scope.submitMapFromValidation = function () {
				var keyword = $scope.mapFromForm;
				keyword = keyword.replace( /\s/g, '' );
				if ( keyword.length == 0 ) {
					$scope.disableGetDirection = true;
				} else {
					$scope.disableGetDirection = false;
				};

			};
			$http.get( "http://ip-api.com/json" ).then( function ( response ) {
				$scope.locationSuccess = response.data.status;
				latiHere = response.data.lat;
				longHere = response.data.lon;
				lati = latiHere;
				long = longHere;
			} );
			$scope.getPlaceList = function () {
				$scope.prevButtonHide = true;
				$scope.prevButtonShow = false;
				$scope.nextButtonHide = true;
				$scope.nextButtonShow = false;
				$scope.showPlaceList = false;

				$scope.showProgress = true;
				$scope.loadProgress = 20;

				$scope.showError = false;
				$scope.showWarning = false;



				$scope.disabledDetails = true;

				lati = latiHere;
				long = longHere;
				var sendData = "lat=" + String( lati ) + "&lon=" + String( long );
				var keyword = $( "#keywordForm" ).val();
				sendData += "&keyword=" + keyword;
				sendData += "&location=" + ( $scope.fromForm == 'currentLocation' ? '' : $( "#otherLocationForm" ).val() );
				sendData += "&category=" + $scope.categoryList.selected.value;
				sendData += "&distance=" + ( $scope.distanceForm == null ? 10 : $scope.distanceForm );
				sendData += "&pagetoken=";


				$scope.serverAddress = serverlocation + "getplacelist.php" + "?" + sendData;

				$http.get( $scope.serverAddress ).then( function ( response ) {
					$scope.placeList1 = response.data.results;
					lati = response.data.lat;
					long = response.data.lon;
					pagetoken = String( response.data.next_page_token );
					$scope.loadProgress = 50;

					$scope.serverAddress2 = serverlocation + "getnextplacelist.php?pagetoken=" + pagetoken;
					setTimeout( function () {
						$http.get( $scope.serverAddress2 ).then( function ( response ) {
							$scope.placeList2 = response.data.results;
							pagetoken = response.data.next_page_token;
							$scope.loadProgress = 50;

							$scope.loadProgress = 75;
							$scope.serverAddress3 = serverlocation + "getnextplacelist.php" + "?pagetoken=" + pagetoken;
							setTimeout( function () {
								$http.get( $scope.serverAddress3 ).then( function ( response ) {
									$scope.placeList3 = response.data.results;
									$scope.placeList = ( ( $scope.placeList1 ).concat( $scope.placeList2 ) ).concat( $scope.placeList3 );

									if ( ( $scope.placeList ).length > 0 ) {
										$scope.page1 = true;
									};
									if ( ( $scope.placeList ).length > 20 ) {
										$scope.page2 = true;
										$scope.nextButtonHide = false;
										$scope.nextButtonShow = true;
									};
									if ( ( $scope.placeList ).length > 40 ) {
										$scope.page3 = true;
									};
									for ( i = 0; i < ( $scope.placeList ).length; i++ ) {
										( $scope.placeList )[ i ].bookmark = false;
										( $scope.placeList )[ i ].selected = false;
										( $scope.placeList )[ i ].cindex = i;
										if ( $scope.bookmarkList != null ) {
											for ( j = 0; j < ( $scope.bookmarkList ).length; j++ ) {
												if ( ( $scope.placeList )[ i ].place_id == ( $scope.bookmarkList )[ j ].place_id ) {
													( $scope.placeList )[ i ].bookmark = true;
												}
											}
										}
									}
									$scope.loadProgress = 100;
									$scope.showPlaceDetail = false;
									$scope.showPlaceList = true;
									$scope.showProgress = false;
								}, function () {
									$scope.showError = true;
									$scope.errorMessage = "Failed to get search result";
									$scope.showProgress = false;
								} )
							}, 2000 );


						}, function () {
							$scope.showError = true;
							$scope.errorMessage = "Failed to get search result";
							$scope.showProgress = false;
						} )
					}, 2000 );




				}, function () {
					$scope.showError = true;
					$scope.errorMessage = "Failed to get search result";
					$scope.showProgress = false;
				} );



			};




			$scope.getPlaceDetail = function ( place_id, index, vtab ) {

				$scope.showProgress = true;
				$scope.loadProgress = 20;
				$scope.showPlaceDetail = false;
				$scope.showWarning = false;

				$scope.mapPosition = {
					center: {
						lat: -33.866,
						lng: 151.196
					},
					zoom: 15
				};
				$scope.map = new google.maps.Map( document.getElementById( 'map' ), $scope.mapPosition );

				var map = $scope.map;


				$( "#panelRoute" ).html( "" );


				var infowindow = new google.maps.InfoWindow();
				var service = new google.maps.places.PlacesService( map );

				var request = {};

				request.placeId = place_id.place_id;

				function callback( placeResult, status ) {
					if ( status === google.maps.places.PlacesServiceStatus.OK ) {
						var marker = new google.maps.Marker( {
							map: map,
							position: placeResult.geometry.location
						} );


						placeLati = placeResult.geometry.location.lat();
						placeLong = placeResult.geometry.location.lng();

						var placeCoordinate = {};
						placeCoordinate.lat = placeLati;
						placeCoordinate.lng = placeLong;
						map.setCenter( placeCoordinate );

						var panorama = new google.maps.StreetViewPanorama(
							document.getElementById( 'pano' ), {
								position: placeCoordinate,
								pov: {
									heading: 34,
									pitch: 10
								}
							} );
						map.setStreetView( panorama );



						$scope.placeInfo = placeResult;
						if ( $scope.bookmarkList != null ) {

							for ( i = 0; i < ( $scope.bookmarkList ).length; i++ ) {
								if ( place_id.place_id == ( $scope.bookmarkList )[ i ].place_id ) {
									$scope.starDetail = true;
								}
							}
						}





						$scope.$apply( function () {

							if ( $scope.placeInfo.opening_hours != null ) {

								$scope.placeInfoHours = [];

								var firstDay = moment().utcOffset( $scope.placeInfo.utc_offset ).format( "e" );

								for ( i = firstDay; i < 7; i++ ) {
									var placeInfoHours = $scope.placeInfo.opening_hours.periods[ i ];
									if ( placeInfoHours != null ) {
										$scope.placeInfoHours.push( {
											day: placeInfoHours.open.day,
											hours: moment( placeInfoHours.open.time, "HHmm" ).format( "hh:mm A" ) + ' - ' + moment( placeInfoHours.close.time, "HHmm" ).format( "hh:mm A" )
										} );
									}

								}

								for ( i = 0; i < firstDay; i++ ) {
									var placeInfoHours = $scope.placeInfo.opening_hours.periods[ i ];
									if ( placeInfoHours != null ) {
										$scope.placeInfoHours.push( {
											day: placeInfoHours.open.day,
											hours: moment( placeInfoHours.open.time, "HHmm" ).format( "hh:mm A" ) + ' - ' + moment( placeInfoHours.close.time, "HHmm" ).format( "hh:mm A" )
										} );
									}

								}
							}

							$scope.loadProgress = 50;


							var vName = $scope.placeInfo.name;
							var vCity;
							var vState;
							var vCountry;
							var vStreetNumber;
							var vRoute;

							for ( i = 0; i < ( $scope.placeInfo.address_components ).length; i++ ) {
								if ( $scope.placeInfo.address_components[ i ].types[ 0 ] == "country" ) {
									vCountry = $scope.placeInfo.address_components[ i ].short_name;
								}
								if ( $scope.placeInfo.address_components[ i ].types[ 0 ] == "administrative_area_level_2" ) {
									vCity = $scope.placeInfo.address_components[ 4 ].short_name;
								}
								if ( $scope.placeInfo.address_components[ i ].types[ 0 ] == "administrative_area_level_1" ) {
									vState = $scope.placeInfo.address_components[ 5 ].short_name;
								}
								if ( $scope.placeInfo.address_components[ i ].types[ 0 ] == "street_number" ) {
									vStreetNumber = $scope.placeInfo.address_components[ i ].short_name;
								}
								if ( $scope.placeInfo.address_components[ i ].types[ 0 ] == "route" ) {
									vRoute = $scope.placeInfo.address_components[ i ].short_name;
								}
							}


							var vAddress = ( vStreetNumber + " " + vRoute );


							var sendData = "name=" + String( vName ) + "&city=" + String( vCity ) + "&state=" + String( vState ) + "&country=" + String( vCountry ) + "&address=" + String( vAddress );

							$scope.serverAddress = serverlocation + "getyelpreview.php" + "?" + sendData;

							$http.get( $scope.serverAddress ).then( function ( response ) {
								$scope.yelpReviewList = response.data;

							}, function () {} );

							$scope.mapToForm = $scope.placeInfo.name + ", " + $scope.placeInfo.formatted_address;

							$scope.showPlaceList = false;
							$scope.showPlaceDetail = true;



						} );

						var $rateYo = $( "#rateYo" ).rateYo();
						$rateYo.rateYo( 'option', 'rating', $scope.placeInfo.rating );
						$rateYo.rateYo( 'option', 'readOnly', true );
						$rateYo.rateYo( 'option', 'starWidth', "15px" );

						for ( i = 0; i < 5; i++ ) {
							var val = parseFloat( $( "#reviewRatingGoogle" + i ).text() );
							var $rateYo = $( "#reviewRatingGoogle" + i ).rateYo();
							$rateYo.rateYo( 'option', 'rating', val );
							$rateYo.rateYo( 'option', 'readOnly', true );
							$rateYo.rateYo( 'option', 'starWidth', "15px" );

							var date = $( "#reviewDateGoogle" + i ).text();
							$( "#reviewDateGoogle" + i ).html( ( moment.unix( date ) ).format( "YYYY-MM-DD HH:mm:ss" ) );
						}

						$scope.googleReviewTab = true;
						$scope.yelpReviewTab = false;
						$scope.reviewTabLabel = "Google Reviews";


						google.maps.event.addListener( marker, 'click', function () {
							infowindow.setContent( '<div><strong>' + placeInfo.name + '</strong><br>' +
								placeInfo.formatted_address + '</div>' );
							infowindow.open( map, this );
						} );
						$scope.loadProgress = 100;
						$scope.showProgress = false;

					} else if ( status === google.maps.places.PlacesServiceStatus.ERROR ) {
						$scope.showError = true;
						$scope.errorMessage = "There was a problem contacting the Google servers.";
						$scope.showPlaceDetail = false;
					} else if ( status === google.maps.places.PlacesServiceStatus.INVALID_REQUEST ) {
						$scope.showError = true;
						$scope.errorMessage = "This request was invalid.";
						$scope.showPlaceDetail = false;
					} else if ( status === google.maps.places.PlacesServiceStatus.OVER_QUERY_LIMIT ) {
						$scope.showError = true;
						$scope.errorMessage = "The webpage has gone over its request quota.";
						$scope.showPlaceDetail = false;
					} else if ( status === google.maps.places.PlacesServiceStatus.NOT_FOUND ) {
						$scope.showError = true;
						$scope.errorMessage = "The referenced location was not found in the Places database.";
						$scope.showPlaceDetail = false;
					} else if ( status === google.maps.places.PlacesServiceStatus.REQUEST_DENIED ) {
						$scope.showError = true;
						$scope.errorMessage = "The webpage is not allowed to use the PlacesService.";
						$scope.showPlaceDetail = false;
					} else if ( status === google.maps.places.PlacesServiceStatus.UNKNOWN_ERROR ) {
						$scope.showError = true;
						$scope.errorMessage = "The PlacesService request could not be processed due to a server error. The request may succeed if you try again.";
						$scope.showPlaceDetail = false;
					} else if ( status === google.maps.places.PlacesServiceStatus.ZERO_RESULTS ) {
						$scope.showError = true;
						$scope.errorMessage = "No result was found for this request.";
						$scope.showPlaceDetail = false;
					}


				}

				service.getDetails( request, callback );


				if ( vtab == 1 ) {
					for ( i = 0; i < ( $scope.placeList ).length; i++ ) {
						( $scope.placeList )[ i ].selected = false;
					}
					( $scope.placeList )[ index ].selected = true;
					$scope.disabledDetails = false;
				} else {
					if ( $scope.bookmarkList != null ) {
						for ( i = 0; i < ( $scope.bookmarkList ).length; i++ ) {
							( $scope.bookmarkList )[ i ].selected = false;
						}
					}
					( $scope.bookmarkList )[ index ].selected = true;
					$scope.disabledBookmarkDetails = false;
					$scope.showBookmarkList = false;
				}



			};
			$scope.mapViewToggle = false;
			$scope.panelViewToggle = false;
			$scope.streetViewToggle = true;
			$scope.getStreetView = function () {
				$scope.mapViewToggle = true;
				$scope.streetViewToggle = false;

				var map = $scope.map;

				var placeCoordinate = {};
				placeCoordinate.lat = placeLati;
				placeCoordinate.lng = placeLong;

				var panorama = new google.maps.StreetViewPanorama(
					document.getElementById( 'pano' ), {
						position: placeCoordinate,
						pov: {
							heading: 34,
							pitch: 10
						}
					} );
				map.setStreetView( panorama );
			}
			$scope.getMapView = function () {
				$scope.mapViewToggle = false;
				$scope.streetViewToggle = true;
			}
			$scope.getDirection = function () {

				$( "#panelRoute" ).html( "" );

				var directionsService = new google.maps.DirectionsService;
				var directionsDisplay = new google.maps.DirectionsRenderer;

				$scope.map = new google.maps.Map( document.getElementById( 'map' ), $scope.mapPosition );
				var map = $scope.map;

				var placeCoordinate = {};
				placeCoordinate.lat = placeLati;
				placeCoordinate.lng = placeLong;
				map.setCenter( placeCoordinate );

				directionsDisplay.setMap( map );
				directionsDisplay.setPanel( document.getElementById( 'panelRoute' ) );

				var vOrigin;

				if ( ( ( $( '#mapFromForm' ).val() ).toLowerCase() == "your location" ) || ( ( $( '#mapFromForm' ).val() ).toLowerCase() == "my location" ) ) {
					vOrigin = {
						lat: latiHere,
						lng: longHere
					};
				} else {
					vOrigin = $( '#mapFromForm' ).val();
				}

				directionsService.route( {
					origin: vOrigin,
					destination: $( '#mapToForm' ).val(),
					travelMode: $( '#mapTravelModeForm' ).val(),
					provideRouteAlternatives: true
				}, function ( response, status ) {
					if ( status === 'OK' ) {
						directionsDisplay.setDirections( response );
					} else {
						window.alert( 'Directions request failed due to ' + status );
					}
				} );

			}

			$scope.googleReviewTab = true;
			$scope.yelpReviewTab = false;
			$scope.reviewTabLabel = "Google Reviews";
			$scope.getReview = function ( vTab ) {
				if ( vTab == 'google' ) {
					$scope.googleReviewTab = true;
					$scope.yelpReviewTab = false;
					$scope.reviewTabLabel = "Google Reviews";
				}
				if ( vTab == 'yelp' ) {
					$scope.googleReviewTab = false;
					$scope.yelpReviewTab = true;
					$scope.reviewTabLabel = "Yelp Reviews";
					for ( i = 0; i < 3; i++ ) {
						var val = $scope.yelpReviewList.reviews[ i ].rating;
						var $rateYoYelp = $( "#reviewRatingYelp" + i ).rateYo();
						$rateYoYelp.rateYo( 'option', 'rating', val );
						$rateYoYelp.rateYo( 'option', 'readOnly', true );
						$rateYoYelp.rateYo( 'option', 'starWidth', "15px" );
					}
				}


			};
			$scope.sortName = "Default Order";
			$scope.sortBy = function ( propertyName, vreverse ) {
				if ( propertyName == 'default' && vreverse == false ) {
					$scope.sortName = "Default Order";
				}
				if ( propertyName == 'rating' && vreverse == true ) {
					$scope.sortName = "Highest Rating";
				}
				if ( propertyName == 'rating' && vreverse == false ) {
					$scope.sortName = "Lowest Rating";
				}
				if ( propertyName == 'time' && vreverse == true ) {
					$scope.sortName = "Most Recent";
				}
				if ( propertyName == 'time' && vreverse == false ) {
					$scope.sortName = "Least Recent";
				}
				propertyName = propertyName == 'default' ? '' : propertyName;
				$scope.propertyName = propertyName;
				$scope.reverse = vreverse;
			};
			$scope.pagePosition = 1;
			$scope.page1Hide = false;
			$scope.page1Show = true;
			$scope.page2Hide = true;
			$scope.page2Show = false;
			$scope.page3Hide = true;
			$scope.page3Show = false;
			$scope.prevButtonHide = true;
			$scope.prevButtonShow = false;
			$scope.nextButtonHide = true;
			$scope.nextButtonShow = false;
			$scope.nextPage = function () {
				if ( $scope.pagePosition == 1 ) {
					$scope.pagePosition = 2;
					$scope.page1Hide = true;
					$scope.page1Show = false;
					$scope.page2Hide = false;
					$scope.page2Show = true;
					$scope.prevButtonHide = false;
					$scope.prevButtonShow = true;
					if ( $scope.page3 == false ) {

						$scope.nextButtonHide = true;
						$scope.nextButtonShow = false;
					}
				} else if ( $scope.pagePosition == 2 ) {
					$scope.pagePosition = 3
					$scope.page2Hide = true;
					$scope.page2Show = false;
					$scope.page3Hide = false;
					$scope.page3Show = true;
					$scope.prevButtonHide = false;
					$scope.prevButtonShow = true;
					$scope.nextButtonHide = true;
					$scope.nextButtonShow = false;
				} else {

				}
			}
			$scope.prevPage = function () {
				if ( $scope.pagePosition == 1 ) {

				} else if ( $scope.pagePosition == 2 ) {
					$scope.pagePosition = 1;
					$scope.page1Hide = false;
					$scope.page1Show = true;
					$scope.page2Hide = true;
					$scope.page2Show = false;
					$scope.prevButtonHide = true;
					$scope.prevButtonShow = false;
					$scope.nextButtonHide = false;
					$scope.nextButtonShow = true;
				} else {
					$scope.pagePosition = 2;
					$scope.page2Hide = false;
					$scope.page2Show = true;
					$scope.page3Hide = true;
					$scope.page3Show = false;
					$scope.prevButtonHide = false;
					$scope.prevButtonShow = true;
					$scope.nextButtonHide = false;
					$scope.nextButtonShow = true;
				}
			}

			$scope.bpagePosition = 1;
			$scope.bpage1Hide = false;
			$scope.bpage1Show = true;
			$scope.bpage2Hide = true;
			$scope.bpage2Show = false;
			$scope.bpage3Hide = true;
			$scope.bpage3Show = false;
			$scope.bprevButtonHide = true;
			$scope.bprevButtonShow = false;
			$scope.bnextButtonHide = true;
			$scope.bnextButtonShow = false;
			$scope.bnextPage = function () {
				if ( $scope.bpagePosition == 1 ) {
					$scope.bpagePosition = 2;
					$scope.bpage1Hide = true;
					$scope.bpage1Show = false;
					$scope.bpage2Hide = false;
					$scope.bpage2Show = true;
					$scope.bprevButtonHide = false;
					$scope.bprevButtonShow = true;
					if ( $scope.bpage3 == false ) {

						$scope.bnextButtonHide = true;
						$scope.bnextButtonShow = false;
					}
				} else if ( $scope.bpagePosition == 2 ) {
					$scope.bpagePosition = 3
					$scope.bpage2Hide = true;
					$scope.bpage2Show = false;
					$scope.bpage3Hide = false;
					$scope.bpage3Show = true;
					$scope.bprevButtonHide = false;
					$scope.bprevButtonShow = true;
					$scope.bnextButtonHide = true;
					$scope.bnextButtonShow = false;
				} else {

				}
			}
			$scope.bprevPage = function () {
				if ( $scope.bpagePosition == 1 ) {

				} else if ( $scope.bpagePosition == 2 ) {
					$scope.bpagePosition = 1;
					$scope.bpage1Hide = false;
					$scope.bpage1Show = true;
					$scope.bpage2Hide = true;
					$scope.bpage2Show = false;
					$scope.bprevButtonHide = true;
					$scope.bprevButtonShow = false;
					$scope.bnextButtonHide = false;
					$scope.bnextButtonShow = true;
				} else {
					$scope.bpagePosition = 2;
					$scope.bpage2Hide = false;
					$scope.bpage2Show = true;
					$scope.bpage3Hide = true;
					$scope.bpage3Show = false;
					$scope.bprevButtonHide = false;
					$scope.bprevButtonShow = true;
					$scope.bnextButtonHide = false;
					$scope.bnextButtonShow = true;
				}
			}

			$scope.setBookmark = function ( vplace_id, vpage ) {
				//localStorage.removeItem( "myBookmark" );

				if ( $scope.placeList != null ) {
					for ( i = 0; i < ( $scope.placeList ).length; i++ ) {
						if ( ( $scope.placeList )[ i ].place_id == vplace_id ) {
							if ( ( $scope.placeList )[ i ].bookmark == true ) {
								( $scope.placeList )[ i ].bookmark = false;
							} else {
								( $scope.placeList )[ i ].bookmark = true;
								( $scope.placeList )[ i ].selected = false;

								myBookmarkArray = JSON.parse( localStorage.myBookmark || [ "null" ] )
								if ( ( myBookmarkArray ) == null ) {
									myBookmarkArray = []
								}
								myBookmarkArray.push( $scope.placeList[ i ] );
								$scope.bookmarkList = myBookmarkArray;

							}
						}
					}
				} else {

				}

				if ( vpage == 2 ) {
					if ( $scope.starDetail == true ) {
						$scope.starDetail = false;
						for ( i = 0; i < ( $scope.bookmarkList ).length; i++ ) {
							if ( $scope.bookmarkList[ i ].place_id == vplace_id ) {	
								$scope.bookmarkList.splice( i, 1 );
							}
						}
					} else {
						$scope.starDetail = true;
					}
				}



				for ( i = 0; i < ( $scope.bookmarkList ).length; i++ ) {
					if ( $scope.bookmarkList[ i ].place_id == vplace_id ) {
						if ( $scope.bookmarkList[ i ].bookmark == false ) {
							$scope.bookmarkList.splice( i, 1 );
						} else {


						}
					}
				}

				localStorage.setItem( "myBookmark", JSON.stringify( $scope.bookmarkList ) );



			}
			$scope.removeBookmark = function ( vplace_id ) {
				//localStorage.removeItem("myBookmark");
				if ( $scope.placeList != null ) {
					for ( i = 0; i < ( $scope.placeList ).length; i++ ) {
						if ( ( $scope.placeList )[ i ].place_id == vplace_id ) {
							( $scope.placeList )[ i ].bookmark = false;
						}
					}
				}

				for ( i = 0; i < ( $scope.bookmarkList ).length; i++ ) {
					if ( $scope.bookmarkList[ i ].place_id == vplace_id ) {
						$scope.bookmarkList.splice( i, 1 );
					}
				}
				localStorage.setItem( "myBookmark", JSON.stringify( $scope.bookmarkList ) );
			}
			$scope.showPlaceList = false;
			$scope.showPlaceDetail = false;
			$scope.showPlaceListTab = function () {
				$scope.showPlaceList = true;
				$scope.showBookmarkList = true;
				$scope.showPlaceDetail = false;
			};
			$scope.showPlaceDetailTab = function () {
				$scope.showPlaceList = false;
				$scope.showBookmarkList = false;
				$scope.showPlaceDetail = true;
			};
			$scope.clearAll = function () {
				$scope.showPlaceList = false;
				$scope.keywordForm = "";
				$scope.distanceForm = "";
				$scope.fromForm = "currentLocation";
				$scope.otherLocationForm = "";
				localStorage.removeItem( "myBookmark" );
				$scope.categoryList.selected = {
					label: "Default",
					value: "default"
				};
			}


		} );
	</script>
</body>
</html>