/**
 * Javascript for event map
 * @version  1.4.4
 */
jQuery(document).ready(function($){
	var geocoder;
	var map;
	var bounds, markerCluster;
	var MAPmarkers = [];
	var LOCATIONSARRAY = [],
			langlong = [];

	// INITIATION
		function initializer(map_canvas_id,mapformat, scrollwheel){			
		    geocoder = new google.maps.Geocoder();
			bounds = new google.maps.LatLngBounds ();

			// default lat long	
				MAP = 	$('#'+map_canvas_id);	
				mapData = MAP.siblings('.evomap_data').data('d');
				dlat = mapData.dlat;
				dlon = mapData.dlon;
				zlevel = parseInt(MAP.zl);
				MAPSTYLES = mapData.mapstyles;

			// map styles

				styles = '';
				if(typeof gmapstyles !== 'undefined'){
					if( MAPSTYLES && gmapstyles != 'default' ){
						styles = $.parseJSON(gmapstyles);
					}
				}
		    
		    var latlng = new google.maps.LatLng(dlat, dlon);
		    
		   	if(scrollwheel=='false' ){
				var myOptions = {			
					center: latlng,	
					mapTypeId:mapformat,	
					zoom: zlevel,	
					scrollwheel: false,
					styles: 	styles
				}
			}else{
				var myOptions = {	
					center: latlng,	
					mapTypeId:mapformat,	
					zoom: zlevel,
					styles: 	styles 
				}
			}

			map = new google.maps.Map(document.getElementById(map_canvas_id), myOptions);
			markerCluster = new MarkerClusterer(map);
		}
	
	// Initial Variables
		var infowindow = new google.maps.InfoWindow();
		var ibTXT ='';
		var address_pos = 0;
		var timeout = 200;

	// LIGHTBOX functionsx
		function prepair_popup(){
			$('.evoem_lightbox_body').html('');
		}			
		function show_popup(cal_id){
			rtl = ($('#'+cal_id).find('.evo-data').attr('data-rtl')=='yes')?'evortl':'';
			$('.evoem_lightbox').addClass('show '+rtl).attr('data-cal_id',cal_id);
			$('body').trigger('evolightbox_show');
		}			
		function appendTo_popup(content){
			$('.evoem_lightbox_body').html(content);
		}

	// EVENT MAP ON LOAD
		$('.ajde_evcal_calendar.eventmap').each(function(){							
			var cal_id = $(this).attr('id');
			var calObj = $('#'+cal_id);

			process_events_list(cal_id,'initial');

			// hide the events list
			events_list_display(calObj);			
		});	
		
	// MONTH JUMPER
		$('.ajde_evcal_calendar.eventmap').on('click','.evo_j_container a',function(){
			var container = $(this).closest('.evo_j_container');
			if(container.attr('data-m')!==undefined && container.attr('data-y')!==undefined){ // check month and day present on jumper
				var calid = container.closest('.ajde_evcal_calendar').attr('id');
				run_redo_map_upon_AJAX(calid);
			}
		});

	// MONTH SWITCHING
		$('.eventmap').on('click', '.evcal_arrows', function(){			
			var this_cal_id = $(this).closest('.eventmap').attr('id');					
			run_redo_map_upon_AJAX(this_cal_id);
		});

	// SORT and FIltering
		$('.eventon_filter_dropdown').on('click','p',function(){
			var this_cal_id = $(this).closest('.eventmap').attr('id');
			run_redo_map_upon_AJAX(this_cal_id);
		});

	// PROCESS event map
		var MARKERTYPE;
		

		// Process Event List for map markers
			function process_events_list(cal_id, type){
				var calendar = CAL = $('#'+cal_id),
					mapELEM = calendar.find('.evoGEO_map'),

				MAPDATA = CAL.find('.evomap_data').data('d');

				// Initial run
					if(type=='initial'){
						var evo_data = calendar.find('.evo-data'),
							mapscroll = evo_data.attr('data-mapscroll'),
							mapformat = evo_data.attr('data-mapformat');

						//run gmap
						initializer(mapELEM.attr('id'), mapformat, mapscroll);
					}

				// ALL EVENTS
				var events = calendar.find('.eventon_events_list').children('.eventon_list_event.event');

				if(events.length>0){					

					// INITIALS
						mapELEM.parent().addClass('loading');
						langlong = []; 
						var NEWLOCATIONS_ = {}, NEWLOCATIONS = {}; // arrays
						clearDebug(mapELEM);

						hideNoEventMSG(mapELEM); // hide no events message if visible
						events_list_display(calendar); // hide or show the events list
										
					// foreach Event
						count = 0;
						events.each(function(){
							var obj = $(this),
								evoInfo = obj.find('.evo_info'),
								eventidarray = [];

							// if event have location information
							if( evoInfo.attr('data-location_status')!='true') return true;

							var location_type = evoInfo.attr('data-location_type');

							coordinates = evoInfo.attr('data-latlng');
							if(coordinates === 'undefined' || coordinates == undefined) return true; // skip no latlng events

							locationDataStr = encodeURIComponent(coordinates);
							address = evoInfo.attr('data-location_address');

							var location_name = evoInfo.attr('data-location_name');
							var eventid = obj.attr('data-event_id');

							eventidarray.push(eventid);

							//if location exists in array
							if( locationDataStr in NEWLOCATIONS_){
								NEWLOCATIONS_[locationDataStr].events.push(eventid);
							}else{

								NEWLOCATIONS_[locationDataStr] = {
									'events':[eventid], 
									'coordinates':coordinates, 
									'name':location_name, 
									'address':address,
									'url': evoInfo.data('location_url')
								};
								count++;
							}

						});// end each
	
					// redo the locations array with clean keys
						x = 0;
						for( var key in NEWLOCATIONS_ ){
							var obj = NEWLOCATIONS_[key];
							NEWLOCATIONS[x] = obj;
							x++;
						}


					// if there are event locations
					if(count > 0){						
						if(type !='initial') clearMapMarkers();	
					
						// Variables	
							ICONURL = '';		
							if(MAPDATA.markertype == 'custom')	ICONURL = decodeURIComponent(MAPDATA.markerurl);
							locations_count = Object.keys(NEWLOCATIONS).length;
							cluster_markers = [];


						// EACH LOCATION
						$.each(NEWLOCATIONS, function(ind, V){

							// marker image 
							if(MAPDATA.markertype =='dynamic'){
								ICONURL = decodeURIComponent(MAPDATA.markerurl)+'/image.php?number='+ V.events.length +'&url='+MAPDATA.markerurl;
							}

							// cordinates
							cord = V.coordinates;
                			cord = cord.split(",");
                			location_cords = new google.maps.LatLng(cord[0], cord[1] );

                			// init markers
		                    var marker = new google.maps.Marker({
		                        position: location_cords,
		                        map: map,
		                        zoom:MAPDATA.zoomlevel,
								icon: ICONURL
		                    });

		                    // re-center map
		                    	bounds.extend(location_cords);

		                    // info window	
								locationOBJ = $('#'+cal_id).find('.evoGEO_locations');					
								showloclink = locationOBJ.attr('data-loclink');
								location_nameX = (V.name)? '<p>'+V.name+'</p>':'';

								// if there is link for location page
									if(MAPDATA.loclink == 'yes'){

										var locationSLUG = V.name.toLowerCase();
										locationSLUG = locationSLUG.split(' ').join('-');
										locationSLUG = locationSLUG.split(',').join('');
																			
										locationURL = MAPDATA.locurl+'/event-location/' + encodeURI(locationSLUG);
										if( 'url' in V && V.url !== undefined) locationURL = V.url;

										location_nameX = '<p><a href="'+locationURL+'">'+ V.name+'</a></p>';
									}			


								var infobox_content = "<div class='evoIW'><div class='evoIWl'><p>"+ V.events.length +"</p><span>"+ MAPDATA.txt  +"</span></div><div class='evoIWr'>"+location_nameX+ V.address+'</div><div class="clear"></div></div>';
														
								// info window listener
									google.maps.event.addListener(marker, 'click', function() {
										
										if (!infowindow) {
											infowindow = new google.maps.InfoWindow();
										}
										infowindow.setContent(infobox_content);
										infowindow.open(map, marker);
										
										show_event(V.events, cal_id);
									});

									google.maps.event.addListener(marker, 'mouseover', function() {
										if (!infowindow) {
											infowindow = new google.maps.InfoWindow();
										}
										infowindow.setContent(infobox_content);
										infowindow.open(map, marker);
																		
									});

									google.maps.event.addListener(infowindow,'closeclick',function(){
									   show_all_events(cal_id);
									}); 
		                    
		                    // Cluster
		                   	cluster_markers.push(marker);

		                    // last one
		                    if( ind == (locations_count-1)){
		                    	map.fitBounds(bounds);	
		                    	// remove loading animation
								mapELEM.parent().removeClass('loading');

								// create clusters
								if( MAPDATA.clusters == 'yes'){
									markerCluster.addMarkers(cluster_markers);
								}
		                    }

						});

					// No locations
					}else{ 
						mapELEM.parent().removeClass('loading');
						showNoEventMSG(mapELEM);
						if(type !='initial') clearMapMarkers();
						setMaptoDefault(mapELEM);	
					}	

				// there are no events in the current location
				}else{ 
					
					if(type !='initial'){
						clearMapMarkers();
						setMaptoDefault(mapELEM);
						events_list_display(calendar);
					}
					showNoEventMSG(mapELEM);
				}
			}


		// re-build the event map with markers
			function run_redo_map_upon_AJAX(calid){
				// hide new events list on months
				if( $('#'+calid).hasClass('eventmap')){
					$( document ).ajaxComplete(function(event, xhr, settings) {
						
						var data = settings.data;
						if( data.indexOf('action=the_ajax_hook') != -1){						
							//calObj.find('.eventon_list_event').hide();				
							
							process_events_list(calid,'redo');
							$('.eventmap').off('click', '.evcal_arrows');
						}
					});
				}
			}
	
		// remove markers
			function clearMapMarkers(){
				markerCluster.clearMarkers();

				for(var i =0; i< MAPmarkers.length; i++){
					MAPmarkers[i].setMap(null);
				}
				MAPmarkers = [];
				MAPmarkers.length = 0;
				address_pos = 0;

				bounds = new google.maps.LatLngBounds (); // declare new bounds for map
			}
			function setMaptoDefault(mapELEM){

				mapData = mapELEM.siblings('.evoGEO_locations');
				dlat = mapData.attr('data-dlat');
				dlon = mapData.attr('data-dlon');

				bounds = new google.maps.LatLngBounds ();
				newlatlng = new google.maps.LatLng(dlat, dlon);
				bounds.extend(newlatlng);
				if(newlatlng) map.setCenter(newlatlng);
				map.fitBounds(bounds);
				map.setZoom(14);
			}
		// display no event message
			function showNoEventMSG(mapELEM){
				mapELEM.parent().find('.evomap_noloc').fadeIn();
			}
			function hideNoEventMSG(mapELEM){
				mapELEM.parent().find('.evomap_noloc').fadeOut();
			}

		// debug record status
			function recordDebug(mapELEM, elm){
				var debug = mapELEM.siblings('.evomap_debug'),
					debugtext = debug.html();

				debug.html( debugtext +' '+elm);
			}
			function clearDebug(mapELEM){
				mapELEM.siblings('.evomap_debug').html('');
			}
		
		// re-fit markers into map
			$('body').on('click','.evo-mapfocus',function(){
				map.fitBounds(bounds);
			});
			
		// Show events for a location marker
			function show_event(eventsARRAY, cal_id){
				var calendar =$('#'+cal_id);
				calendar.find('.eventon_events_list').slideUp(function(){
					
					eventList = $('#'+cal_id).find('.evoEM_list');
					eventList.hide();
					calendar.find('.eventon_list_event').hide();

					// open as lightbox events
					if(eventList.attr('data-lightbox')=='yes'){

						prepair_popup();						

						//append_popup_codes();
						eventslist = calendar.find('.eventon_events_list').html();
						//$('body').find('.evoEM_pop_body').html(eventslist);

						appendTo_popup( eventslist );
						show_popup(cal_id);

						//popbody = $('body').find('.evoEM_pop_body');
						popbody = $('.evoem_lightbox_body');

						for(i=0; i< eventsARRAY.length; i++){
							popbody.find('.eventon_list_event[data-event_id='+eventsARRAY[i]+']').show();
						}

						//$('body').find('.evoem_lightbox').fadeIn();
					}else{
					// none lightbox approach						
						for(i=0; i< eventsARRAY.length; i++){
							$('#'+cal_id).find('.eventon_list_event[data-event_id='+eventsARRAY[i]+']').show();
						}					
						$(this).delay(400).show();
						eventList.slideDown('slow');
					}					
				});
			}
			function show_all_events(cal_id){
				var calendar =$('#'+cal_id);
				calendar.find('.eventon_events_list').slideUp(function(){

					$(this).hide();
					//calendar.find('.eventon_list_event').show();				
					//$(this).delay(400).slideDown('slow');
				});
			}

		// hide or show events list
			function events_list_display(cal){
				eventlist = cal.find('.evoEM_list');
				if(eventlist.attr('data-showe')=='yes'){
					eventlist.show();
				}else{
					eventlist.hide();
				}
			}	
		
		// SUPPORT
			// return in km
			function get_straight_distance( LA1, LO1, LA2, LO2){
				var R = 6371; // Radius of the earth in km
			  	var dLat = deg2rad(LA2-LA1);  // deg2rad below
			  	var dLon = deg2rad(LO2-LO1); 
			  	var a = 
			    	Math.sin(dLat/2) * Math.sin(dLat/2) +
			    	Math.cos(deg2rad(LA1)) * Math.cos(deg2rad(LA2)) * 
			    	Math.sin(dLon/2) * Math.sin(dLon/2)
			    ; 
			  	var c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a)); 
			  	var d = R * c; // Distance in km
			  	return d;
			}
			function deg2rad(deg) {
			  	return deg * (Math.PI/180)
			}
			function km_to_mile(KM){
				return KM * 0.62137;
			}

			function stringCount(haystack) {
			    if (!haystack) {
			        return false;
			    }
			    else {
			        var words = haystack.split(','),
			            count = 1;

			        words.pop();

			        for (var i = 0, len = words.length; i < len; i++) {
			            count = parseInt(count) + 1;
			            //console.log(count);
			        }		        
			        return count;
			    }
			}
});