(function ($) {

	var DyncontEl_GoogleMapsHandler = function ($scope, $) {
		const initMap = () => {
			var $map = $scope.find('.map');
			var map = $map[0];
			var bounds;

			// Positions
			if (!$map[0]) {
				console.error('Missing params');
				return;
			}
			let positions = $map[0].dataset.positions;
			try {
				positions = JSON.parse(positions);
			} catch (err) {
				console.error(err);
				positions = [];
			}

			// Exit if the map doesn't have positions
			if( ! positions.length ) {
				return;
			}

			var latitude = parseFloat( positions[0].lat ) || 0;
			var longitude = parseFloat( positions[0].lng ) || 0;
			var lastWindow = null;
			var elementSettings = dceGetElementSettings($scope);
			var markerWidth = elementSettings.marker_width || 0;
			var markerHeight = elementSettings.marker_height || 0;
			var zoom = $map.data('zoom') || 10;
			var imageMarker = positions[0].marker || '';

			if (markerWidth && markerHeight && imageMarker) {
				imageMarker = {
					url: imageMarker,
					scaledSize: new google.maps.Size(markerWidth, markerHeight),
				};
			}

			// Map Parameters
			var mapParams = {
				zoom: zoom,
				scrollwheel: Boolean( elementSettings.prevent_scroll ),
				mapTypeControl: Boolean( elementSettings.maptypecontrol ),
				panControl: Boolean( elementSettings.pancontrol ),
				rotateControl: Boolean( elementSettings.rotaterontrol ),
				scaleControl: Boolean( elementSettings.scalecontrol ),
				streetViewControl: Boolean( elementSettings.streetviewcontrol ),
				zoomControl: Boolean( elementSettings.zoomcontrol ),
				fullscreenControl: Boolean( elementSettings.fullscreenControl ),
				center: {
					lat: latitude,
					lng: longitude,
				},
			};

			// Map Type (Roadmap, satellite, etc.)
			if (elementSettings.map_type && elementSettings.map_type !== "acfmap") {
				mapParams['mapTypeId'] = elementSettings.map_type;
			}

			// Zoom Minimum and Maximum
			if (elementSettings.zoom_custom ) {
				minZoom = elementSettings.zoom_minimum.size || 0;
				maxZoom = elementSettings.zoom_maximum.size || 20;
				if( minZoom > maxZoom ) {
					minZoom = maxZoom;
				}
				mapParams['minZoom'] = minZoom;
				mapParams['maxZoom'] = maxZoom;
			}

			if (elementSettings.style_select === 'prestyle') {
				var fileStyle = elementSettings.snazzy_select;
				$.getJSON(fileStyle + ".json", function (json) {
					mapParams['styles'] = json;
					_initMap(map, mapParams);
				});
			} else {
				if (elementSettings.style_select === 'custom') {
					mapParams['styles'] = JSON.parse(elementSettings.style_map);
				}
				_initMap(map, mapParams);
			}

			function _initMap(mapElement, mapParameters) {
				map = new google.maps.Map(mapElement, mapParameters);
				var markers = [];
				var mapDataType = elementSettings.map_data_type;

				// Geolocation
				if(elementSettings.geolocation == 'yes') {
					const locationButton = document.createElement("button");
					locationButton.textContent = elementSettings.geolocation_button_text;
					locationButton.classList.add("custom-map-control-button");
					map.controls[google.maps.ControlPosition.TOP_CENTER].push(locationButton);
					locationButton.addEventListener("click", () => {
						// HTML5 geolocation
						if (navigator.geolocation) {
							navigator.geolocation.getCurrentPosition(
								(position) => {
									const pos = {
										lat: position.coords.latitude,
										lng: position.coords.longitude,
									};
									map.setCenter(pos);
									if ( elementSettings.geolocation_change_zoom ) {
										map.setZoom( elementSettings.geolocation_zoom.size || 10 );
									}
								},
								() => {
									handleLocationError(true, new google.maps.InfoWindow(), map.getCenter());
								}
							);
						} else {
							// Browser doesn't support Geolocation
							handleLocationError(false, new google.maps.InfoWindow(), map.getCenter());
						}
					});
				}

				if (elementSettings.use_query) {
					bounds = new google.maps.LatLngBounds();
					for (let i = 0; i < positions.length; i++) {

						if (mapDataType == 'address') {
							addressToLocation(
								positions[i]['address'],
								positions[i]['marker'],
								positions[i]['infowindow'],
								positions[i]['link'],
								changeMapLocation,
								markerWidth,
								markerHeight
							);
						} else if ( mapDataType == 'latlng' || mapDataType == 'acfmap' ) {

							var latLng = new google.maps.LatLng(positions[i]['lat'], positions[i]['lng']); //Makes a latlng
							map.panTo(latLng); //Make map global

							var imageMarkerList = positions[i]['marker'];
							var markerWidth = elementSettings.marker_width;
							var markerHeight = elementSettings.marker_height;
							if (markerWidth && markerHeight && imageMarkerList) {
								imageMarkerList = {
									url: positions[i]['marker'],
									scaledSize: new google.maps.Size(markerWidth, markerHeight)
								};
							}

							new google.maps.LatLng('0', '0');

							var marker = new google.maps.Marker({
								position: latLng,
								map: map,
								icon: imageMarkerList,
								animation: google.maps.Animation.DROP,
							});

							markers.push(marker);
							bounds.extend(marker.position);

							if (elementSettings.enable_infoWindow) {
								google.maps.event.addListener(marker, 'click', (function (marker, k) {
									return function () {
										if (elementSettings.infoWindow_click_to_post) {
											window.location = positions[k]['link'];
										} else {
											var iwOptions = {
												content: positions[k]['infowindow'],
											}
											if(elementSettings.infoWindow_panel_maxwidth.size){
												iwOptions['maxWidth'] = elementSettings.infoWindow_panel_maxwidth.size;
											}
											var infoWindowMap = new google.maps.InfoWindow(iwOptions);

											if (lastWindow) {
												lastWindow.close();
											}
											infoWindowMap.open(map, marker);
											lastWindow = infoWindowMap;
										}
									}
								})(marker, i));
							}
						}
						// Center the map
						map.fitBounds(bounds);
						if ( ! elementSettings.auto_zoom ) {
							var listener = google.maps.event.addListenerOnce(map, "idle", function () {
								// Set Zoom after centered the map
								map.setZoom(zoom);
							});
						}
					}
					if( elementSettings.markerclustererControl ){
						// Add a marker clusterer to manage the markers.
						new markerClusterer.MarkerClusterer({
							map,
							markers,
							imagePath: '/wp-content/plugins/dynamic-content-for-elementor/assets/lib/gmap/markerclusterer/img/m'
						});
					}
				} else {
					var marker;
					if (mapDataType == 'address') {
						let address = positions[0].address || '';
						let geocoder = new google.maps.Geocoder();
						geocoder.geocode( { 'address': address}, function(results, status) {
							if (status == 'OK') {
								map.setCenter(results[0].geometry.location);
								marker = new google.maps.Marker({
									map: map,
									position: results[0].geometry.location,
									icon: imageMarker,
									animation: google.maps.Animation.DROP,
								});
								infoWindow(marker);
							}
						});
					} else if (mapDataType == 'latlng' || mapDataType == 'acfmap' || mapDataType == 'metabox_google_maps' ) {
						var latLng = new google.maps.LatLng(latitude, longitude); // Makes a latlng
						map.panTo(latLng); // Make map global

						marker = new google.maps.Marker({
							map: map,
							position: latLng,
							icon: imageMarker,
							animation: google.maps.Animation.DROP,
						});
						infoWindow(marker);
					}


				}
			}

			function infoWindow(marker) {
				if (elementSettings.enable_infoWindow ) {
					if( elementSettings.infoWindow_click_to_url && elementSettings.infoWindow_url ) {
						marker.addListener('click', function () {
							window.location = elementSettings.infoWindow_url.url;
						});
					} else if( positions[0].infowindow ) {
						var iwOptions = {
							content: positions[0].infowindow,
						}
						if(elementSettings.infoWindow_panel_maxwidth.size){
							iwOptions['maxWidth'] = elementSettings.infoWindow_panel_maxwidth.size;
						}
						var infoWindowMap = new google.maps.InfoWindow(iwOptions);

						marker.addListener('click', function () {
							infoWindowMap.open(map, marker);
						});
					}
				}
			}

			function changeMapLocation(locations) {
				if (locations && locations.length >= 1) {

					// Image Marker
					if (locations[0].marker != "") {
						let imageMarker;
						if( locations[0].markerWidth && locations[0].markerHeight && locations[0].marker ) {
							imageMarker = {
								url: locations[0].marker,
								scaledSize: new google.maps.Size(markerWidth, markerHeight),
							};
						} else {
							imageMarker = {
								url: locations[0].marker,
							};
						}
					}

					// New marker
					var marker = new google.maps.Marker({
						map: map,
						position: locations[0].location,
						icon: imageMarker,
					});

					map.panTo(locations[0].location);

					// Infowindow
					if (elementSettings.enable_infoWindow && locations[0].infoWindow) {
						var iwOptions = {
							content: locations[0].infoWindow,
						}
						if(elementSettings.infoWindow_panel_maxwidth.size){
							iwOptions['maxWidth'] = elementSettings.infoWindow_panel_maxwidth.size;
						}
						var infoWindowMap = new google.maps.InfoWindow(iwOptions);
						marker.addListener('click', function () {
							if (elementSettings.infoWindow_click_to_post) {
								window.location = locations[0].postLink;
							} else {
								infoWindowMap.open(map, marker);
							}
						});
					}
					if (elementSettings.use_query) {
						bounds.extend(marker.position);
						map.fitBounds(bounds);
					}
				}
			}
			function addressToLocation(address, markerimg, iw, pl, callback, markerWidth, markerHeight) {

				// Geocoder converts addresses to latitude-longitude positions
				var geocoder = new google.maps.Geocoder();

				geocoder.geocode(
					{
						address: address
					},
					function (results, status) {

						var resultLocations = [];

						if (status == google.maps.GeocoderStatus.OK) {
							if (results) {
								var numOfResults = results.length;
								for (var i = 0; i < numOfResults; i++) {
									var result = results[i];
									resultLocations.push(
										{
											text: result.formatted_address,
											addressStr: result.formatted_address,
											location: result.geometry.location,
											marker: markerimg,
											postLink: pl,
											infoWindow: iw,
											markerWidth: markerWidth,
											markerHeight: markerHeight,
										}
									);
								}
							}
						}

						if (resultLocations.length > 0) {
							callback(resultLocations);
						} else {
							callback(null);
						}
					}
				);

			}

			function handleLocationError(browserHasGeolocation, infoWindow, pos) {
				infoWindow.setPosition(pos);
				infoWindow.setContent(
					browserHasGeolocation
						? "Error: The Geolocation service failed."
						: "Error: Your browser doesn't support geolocation."
				);
				infoWindow.open(map);
			}
		}
		// google api might loaded before or after this script based on third
		// party plugins. So we take both cases into account:
		if ( typeof google !== "undefined" ) {
			initMap();
		} else {
			window.addEventListener( 'dce-google-maps-api-loaded', initMap);
		}

	};

	// The dynamicooo/google-maps/init event is for GDPR plugins like Borlabs:
	$(window).on('elementor/frontend/init dynamicooo/google-maps-init', function () {
		elementorFrontend.hooks.addAction('frontend/element_ready/dyncontel-acf-google-maps.default', DyncontEl_GoogleMapsHandler);
		elementorFrontend.hooks.addAction('frontend/element_ready/dce-metabox-google-maps.default', DyncontEl_GoogleMapsHandler);
	});

})(jQuery);

// Re init layout after ajax request on Search&Filter Pro
(function ( $ ) {
	"use strict";
	$(function () {
		$(document).on("sf:ajaxfinish", ".searchandfilter", function( e, data ) {
			if ( elementorFrontend) {
				if ( elementorFrontend.elementsHandler.runReadyTrigger && SF_LDATA.extensions.indexOf('search-filter-elementor') < 0 ) {
					elementorFrontend.elementsHandler.runReadyTrigger(data.targetSelector);
				}
			}
		});
	});
}(jQuery));
