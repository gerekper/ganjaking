( function( $ ) {
	"use strict";
	var WidgetGoogleMapHandler = function ($scope, $) {
		var gmap = $scope.find('.pt-plus-adv-map');
			$(".pt-plus-overlay-map-content").each(function() {
				var uid= $(this).data('uid'),
					desc_color = $(this).data( 'desc_color'),
					toggle_btn_color=$(this).data('toggle-btn-color'),
					toggle_active_color=$(this).data('toggle-active-color');
				$('head').append('<style >.checked-'+uid+':not(checked) + .check-label-'+uid+':after,.checked-'+uid+' + .check-label-'+uid+':before{border-color: '+toggle_btn_color+';}.checked-'+uid+':checked + .check-label-'+uid+':after{    border-color: '+toggle_active_color+';}</style>');
			});
 
			var PlusMapMarkers = [],
				elements = document.querySelectorAll('.pt-plus-adv-map');
			Array.prototype.forEach.call(elements, function(el) {
				var $this=$(el),
					data_id=$this.data( 'id' ),
					data=$this.data( 'adv-maps' ),
					data_style=$this.data( 'map-style' ),
					map=null,
					bounds=null,
					infoWindow=null,
					position=null,
					styles1='';
 
				if( !$this.hasClass("map-loaded") ){
					if(data_style == 'style-1'){
						styles1 = '[{"featureType":"all","elementType":"all","stylers":[{"hue":"#ff0000"},{"saturation":-100},{"lightness":-30}]},{"featureType":"all","elementType":"labels.text.fill","stylers":[{"color":"#ffffff"}]},{"featureType":"all","elementType":"labels.text.stroke","stylers":[{"color":"#353535"}]},{"featureType":"landscape","elementType":"geometry","stylers":[{"color":"#656565"}]},{"featureType":"poi","elementType":"geometry.fill","stylers":[{"color":"#505050"}]},{"featureType":"poi","elementType":"geometry.stroke","stylers":[{"color":"#808080"}]},{"featureType":"road","elementType":"geometry","stylers":[{"color":"#454545"}]}]';
					}else if(data_style == 'style-2'){
						styles1 = '[{"featureType":"administrative","elementType":"all","stylers":[{"saturation":"-100"}]},{"featureType":"administrative.province","elementType":"all","stylers":[{"visibility":"off"}]},{"featureType":"landscape","elementType":"all","stylers":[{"saturation":-100},{"lightness":65},{"visibility":"on"}]},{"featureType":"poi","elementType":"all","stylers":[{"saturation":-100},{"lightness":"50"},{"visibility":"simplified"}]},{"featureType":"road","elementType":"all","stylers":[{"saturation":"-100"}]},{"featureType":"road.highway","elementType":"all","stylers":[{"visibility":"simplified"}]},{"featureType":"road.arterial","elementType":"all","stylers":[{"lightness":"30"}]},{"featureType":"road.local","elementType":"all","stylers":[{"lightness":"40"}]},{"featureType":"transit","elementType":"all","stylers":[{"saturation":-100},{"visibility":"simplified"}]},{"featureType":"water","elementType":"geometry","stylers":[{"hue":"#ffff00"},{"lightness":-25},{"saturation":-97}]},{"featureType":"water","elementType":"labels","stylers":[{"lightness":-25},{"saturation":-100}]}]';
					}else if(data_style == 'style-3'){
						styles1 = '[{"featureType":"water","elementType":"geometry","stylers":[{"color":"#e9e9e9"},{"lightness":17}]},{"featureType":"landscape","elementType":"geometry","stylers":[{"color":"#f5f5f5"},{"lightness":20}]},{"featureType":"road.highway","elementType":"geometry.fill","stylers":[{"color":"#ffffff"},{"lightness":17}]},{"featureType":"road.highway","elementType":"geometry.stroke","stylers":[{"color":"#ffffff"},{"lightness":29},{"weight":0.2}]},{"featureType":"road.arterial","elementType":"geometry","stylers":[{"color":"#ffffff"},{"lightness":18}]},{"featureType":"road.local","elementType":"geometry","stylers":[{"color":"#ffffff"},{"lightness":16}]},{"featureType":"poi","elementType":"geometry","stylers":[{"color":"#f5f5f5"},{"lightness":21}]},{"featureType":"poi.park","elementType":"geometry","stylers":[{"color":"#dedede"},{"lightness":21}]},{"elementType":"labels.text.stroke","stylers":[{"visibility":"on"},{"color":"#ffffff"},{"lightness":16}]},{"elementType":"labels.text.fill","stylers":[{"saturation":36},{"color":"#333333"},{"lightness":40}]},{"elementType":"labels.icon","stylers":[{"visibility":"off"}]},{"featureType":"transit","elementType":"geometry","stylers":[{"color":"#f2f2f2"},{"lightness":19}]},{"featureType":"administrative","elementType":"geometry.fill","stylers":[{"color":"#fefefe"},{"lightness":20}]},{"featureType":"administrative","elementType":"geometry.stroke","stylers":[{"color":"#fefefe"},{"lightness":17},{"weight":1.2}]}]';
					}else if(data_style == 'style-4'){
						styles1 = '[{"featureType":"administrative","elementType":"labels.text.fill","stylers":[{"color":"#444444"}]},{"featureType":"landscape","elementType":"all","stylers":[{"color":"#f2f2f2"}]},{"featureType":"poi","elementType":"all","stylers":[{"visibility":"off"}]},{"featureType":"road","elementType":"all","stylers":[{"saturation":-100},{"lightness":45}]},{"featureType":"road.highway","elementType":"all","stylers":[{"visibility":"simplified"}]},{"featureType":"road.arterial","elementType":"labels.icon","stylers":[{"visibility":"off"}]},{"featureType":"transit","elementType":"all","stylers":[{"visibility":"off"}]},{"featureType":"water","elementType":"all","stylers":[{"color":"#46bcec"},{"visibility":"on"}]}]';
					}else if(data_style == 'style-5'){
						styles1 = '[{"featureType":"all","elementType":"labels.text.fill","stylers":[{"saturation":36},{"color":"#000000"},{"lightness":40}]},{"featureType":"all","elementType":"labels.text.stroke","stylers":[{"visibility":"on"},{"color":"#000000"},{"lightness":16}]},{"featureType":"all","elementType":"labels.icon","stylers":[{"visibility":"off"}]},{"featureType":"administrative","elementType":"geometry.fill","stylers":[{"color":"#000000"},{"lightness":20}]},{"featureType":"administrative","elementType":"geometry.stroke","stylers":[{"color":"#000000"},{"lightness":17},{"weight":1.2}]},{"featureType":"landscape","elementType":"geometry","stylers":[{"color":"#000000"},{"lightness":20}]},{"featureType":"poi","elementType":"geometry","stylers":[{"color":"#000000"},{"lightness":21}]},{"featureType":"road.highway","elementType":"geometry.fill","stylers":[{"color":"#000000"},{"lightness":17}]},{"featureType":"road.highway","elementType":"geometry.stroke","stylers":[{"color":"#000000"},{"lightness":29},{"weight":0.2}]},{"featureType":"road.arterial","elementType":"geometry","stylers":[{"color":"#000000"},{"lightness":18}]},{"featureType":"road.local","elementType":"geometry","stylers":[{"color":"#000000"},{"lightness":16}]},{"featureType":"transit","elementType":"geometry","stylers":[{"color":"#000000"},{"lightness":19}]},{"featureType":"water","elementType":"geometry","stylers":[{"color":"#000000"},{"lightness":17}]}]';
					}else if(data_style == 'style-6'){
						styles1 = '[{"elementType":"geometry","stylers":[{"hue":"#ff4400"},{"saturation":-68},{"lightness":-4},{"gamma":0.72}]},{"featureType":"road","elementType":"labels.icon"},{"featureType":"landscape.man_made","elementType":"geometry","stylers":[{"hue":"#0077ff"},{"gamma":3.1}]},{"featureType":"water","stylers":[{"hue":"#00ccff"},{"gamma":0.44},{"saturation":-33}]},{"featureType":"poi.park","stylers":[{"hue":"#44ff00"},{"saturation":-23}]},{"featureType":"water","elementType":"labels.text.fill","stylers":[{"hue":"#007fff"},{"gamma":0.77},{"saturation":65},{"lightness":99}]},{"featureType":"water","elementType":"labels.text.stroke","stylers":[{"gamma":0.11},{"weight":5.6},{"saturation":99},{"hue":"#0091ff"},{"lightness":-86}]},{"featureType":"transit.line","elementType":"geometry","stylers":[{"lightness":-48},{"hue":"#ff5e00"},{"gamma":1.2},{"saturation":-23}]},{"featureType":"transit","elementType":"labels.text.stroke","stylers":[{"saturation":-64},{"hue":"#ff9100"},{"lightness":16},{"gamma":0.47},{"weight":2.7}]}]';
					}else if(data_style == 'style-7'){
						styles1 = '[{"featureType":"water","stylers":[{"color":"#0e171d"}]},{"featureType":"landscape","stylers":[{"color":"#1e303d"}]},{"featureType":"road","stylers":[{"color":"#1e303d"}]},{"featureType":"poi.park","stylers":[{"color":"#1e303d"}]},{"featureType":"transit","stylers":[{"color":"#182731"},{"visibility":"simplified"}]},{"featureType":"poi","elementType":"labels.icon","stylers":[{"color":"#f0c514"},{"visibility":"off"}]},{"featureType":"poi","elementType":"labels.text.stroke","stylers":[{"color":"#1e303d"},{"visibility":"off"}]},{"featureType":"transit","elementType":"labels.text.fill","stylers":[{"color":"#e77e24"},{"visibility":"off"}]},{"featureType":"road","elementType":"labels.text.fill","stylers":[{"color":"#94a5a6"}]},{"featureType":"administrative","elementType":"labels","stylers":[{"visibility":"simplified"},{"color":"#e84c3c"}]},{"featureType":"poi","stylers":[{"color":"#e84c3c"},{"visibility":"off"}]}]';
					}
 
					var map_toBuild = [];
					var build = function() {
						data.options.mapTypeId = google.maps.MapTypeId[data.options.mapTypeId];
						data.options.styles = data.style;
						if(styles1){
							data.options.styles = JSON.parse(styles1);
						}          
 
						bounds = new google.maps.LatLngBounds();
						map = new google.maps.Map(document.getElementById(data_id), data.options);
						infoWindow = new google.maps.InfoWindow();
 
						map.setOptions({
							panControl : data.options.panControl,
							draggable:  data.options.draggable,
							scrollwheel : data.options.scrollwheel,
							zoomControl:  data.options.zoomControl,
							mapTypeControl:  data.options.mapTypeControl,					
							fullscreenControl:  data.options.fullscreenControl,
							scaleControl:  data.options.scaleControl,
							streetViewControl: data.options.streetViewControl,
						});
 
						var marker, i;
							map.setTilt(45);
						google.maps.event.addListener(infoWindow , 'domready', function() {
							var iwOuter = $('.gm-style-iw');
							var iwBackground = iwOuter.prev();
							var parentdiv = iwOuter.parent('div');
								parentdiv.addClass('marker-icon');
							var iwCloseBtn = iwOuter.next();
								iwCloseBtn.hide();
								iwOuter.addClass('marker-title');				
						});
 
						for (i = 0; i < data.places.length; i++) {
							position = new google.maps.LatLng(data.places[i].latitude, data.places[i].longitude);
							bounds.extend(position);
 
                            var titleattr = data.places[i].address;
                            if(data.hidetitle[i] && data.hidetitle[i].hidetitlevalues=='hidetitlemap'){
                                titleattr = '';                               
                            }                            
 
							marker = new google.maps.Marker({
								position: position,
								map: map,
								title: titleattr,
								icon: data.places[i].pin_icon
							});
 
                            var hoverconattr = 'click';
                            if(data.onhover[i] && data.onhover[i].onhovervalues=='onhovercontent'){
                                hoverconattr = 'mouseover';                                
                            }
 
							google.maps.event.addListener(marker, hoverconattr, (function(marker, i) {
								return function() { 
									if(data.places[i].address.length > 1) {
										infoWindow.setContent('<div class="gmap_info_content"><p>'+ data.places[i].address +'</p></div>');
									}
									infoWindow.open(map, marker);
								};
							})(marker, i));
 
							PlusMapMarkers.push(marker);
							map.fitBounds(bounds);
						}
 
						var bounds_Listener = google.maps.event.addListener((map), 'idle', function(event) {
							this.setZoom(data.options.zoom);
							google.maps.event.removeListener(bounds_Listener);
						});
 
						var update = function() {
							google.maps.event.trigger(map, "resize");
							map.setCenter(position);
						};
						update();
					};
 
					var init_Map = function() {
						for( var i = 0, l = map_toBuild.length; i < l; i++ ) {
							map_toBuild[i]();
						}
					};
 
					var initialize = function() {
						init_Map();
					};
 
					map_toBuild.push( build );
					initialize();
					$this.addClass("map-loaded");
 
					if(data && data.options && data.options.marker_clustering){
						new MarkerClusterer(map, PlusMapMarkers, { imagePath:"https://developers.google.com/maps/documentation/javascript/examples/markerclusterer/m" });
					}
				}
			});
 
			$(".overlay-list-item").on('click',function() {      
				let $checkbox = $(this).find('input[type=checkbox]');
				if ($checkbox.is(':checked')) {
					$checkbox.attr('checked', false);
					$(this).parent('.pt-plus-overlay-map-content').removeClass("selected");
				} else {
					$checkbox.attr('checked', true);
					$(this).parent('.pt-plus-overlay-map-content').addClass("selected");
				}
			});
	};
 
	$(window).on('elementor/frontend/init', function () {
		elementorFrontend.hooks.addAction('frontend/element_ready/tp-google-map.default', WidgetGoogleMapHandler);		
		if (elementorFrontend.isEditMode()) {		
			elementorFrontend.hooks.addAction('frontend/element_ready/tp-google-map.default', WidgetGoogleMapHandler);
		}
	});
})(jQuery);