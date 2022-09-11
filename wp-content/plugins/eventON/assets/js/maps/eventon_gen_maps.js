/**
 * EventON Generate Google Maps Function
 * @version  0.2
 */
(function($){
	$.fn.evoGenmaps = function(opt){

		var defaults = {
			delay:	0,
			fnt:	1,
			cal:	'',
			SC: 	'',
			map_canvas_id:	'',
			location_type:'',
			address:'',
			styles:'',
			zoomlevel:'',
			mapformat:'',
			scroll:false,
			iconURL:'',
		};
		var options = $.extend({}, defaults, opt); 

		
		var geocoder;

		var code = {};
		obj = this;
		code.obj = this;

		code = {	
			init:function(){	

				// set calendar for event
				if( obj.closest('.ajde_evcal_calendar').length>0){
					options.cal = obj.closest('.ajde_evcal_calendar');
				}

				if( options.cal === undefined){
					console.log('Cannot find the calendar'); return false;
				}

				code.process_SC();

				// various methods to draw map
					// multiple maps at same time
					if(options.fnt==1){
						code.load_gmap();
					}
					
					if(options.fnt==2){
						if(options.delay==0){	code.load_gmap();	}else{
							setTimeout(code.load_gmap, options.delay, this);
						}					
					} 
					if(options.fnt==3){	code.load_gmap();	}
					
					// gmaps on popup
					if(options.fnt==4){
						// check if gmaps should run
						if( this.attr('data-gmtrig')=='1' && this.attr('data-gmap_status')!='null'){	
							code.load_gmap();			
						}	
					}

					// load map on specified elements directly
					// eg. location archive page
					if(options.fnt==5){
						code.draw_map();
					}
			},
			// add unique id for map area
			process_unique_map_id: function(){
				var map_element = obj.closest('.eventon_list_event').find('.evo_metarow_gmap');

				if(map_element === undefined ) return false;

				var old_map_canvas_id = map_element.attr('id');	
				if(old_map_canvas_id === undefined) return false;

				// GEN
				maximum = 99;
				minimum = 10;
				var randomnumber = Math.floor(Math.random() * (maximum - minimum + 1)) + minimum;

				map_canvas_id = old_map_canvas_id+'_'+randomnumber;
				map_element.attr('id', map_canvas_id).addClass('test');
				
				options.map_canvas_id = map_canvas_id;

				return map_canvas_id;
			},
			process_SC: function(){
				CAL = options.cal;
				if( options.SC !== '') return;
				if(CAL == '') return false;
				options.SC = CAL.evo_shortcode_data();
			},

			// load google map
			load_gmap: function(){
				SC = options.SC;
						
				var ev_location = obj.find('.event_location_attrs');

				var location_type = ev_location.attr('data-location_type');
				if(location_type=='address'){
					options.address = ev_location.attr('data-location_address');
					options.location_type = 'add';
				}else{			
					options.address = ev_location.attr('data-latlng');
					options.location_type = 'latlng';				
				}

				// marker icons
				if( 'mapiconurl' in SC) options.iconURL = SC.mapiconurl;

				// make sure there is address present to draw map
				if( options.address === undefined || options.address == '') return false;

				map_canvas_id = code.process_unique_map_id();

				if(!map_canvas_id || $('#'+map_canvas_id).length == 0) return false;

				var zoom = SC.mapzoom;
				options.zoomlevel = (typeof zoom !== 'undefined' && zoom !== false)? parseInt(zoom):12;				
				options.scroll = SC.mapscroll;	
				options.mapformat = SC.mapformat;	

													
				code.draw_map();
			},

			// final draw
			draw_map: function(){

				if(!options.map_canvas_id || $('#'+options.map_canvas_id).length == 0){
					console.log( 'Map element with id missing in page'); return false;
				}

				// map styles
				if( typeof gmapstyles !== 'undefined' && gmapstyles != 'default'){
					options.styles = JSON.parse(gmapstyles);
				}

				geocoder = new google.maps.Geocoder();					
				//var latlng = new google.maps.LatLng(45.524732, -122.677031);

				var latlng = 0;
				
				if(options.scroll == 'false' || options.scroll == false){

					var myOptions = {			
						//center: latlng,	
						mapTypeId: 	options.mapformat,	
						zoom: 		options.zoomlevel,	
						scrollwheel: false,
						styles: options.styles,
						zoomControl:true,
						draggable:false
					}
				}else{
					var myOptions = {	
						//center: latlng,	
						mapTypeId: options.mapformat,	
						zoom: options.zoomlevel,
						styles: options.styles,
						zoomControl:true,
						scrollwheel: true,
					}
				}
				
				var map_canvas = document.getElementById(options.map_canvas_id);
				map = new google.maps.Map(map_canvas, myOptions);
		
				// address from latlng
				if(options.location_type=='latlng' && options.address !== undefined){
					var latlngStr = options.address.split(",",2);
					var lat = parseFloat(latlngStr[0]);
					var lng = parseFloat(latlngStr[1]);
					var latlng = new google.maps.LatLng(lat, lng);

					geocoder.geocode({'latLng': latlng}, function(results, status) {
						if (status == google.maps.GeocoderStatus.OK) {				
							var marker = new google.maps.Marker({
								map: map,
								position: latlng,
								icon: options.iconURL
							});
							//map.setCenter(results[0].geometry.location);
							map.setCenter(marker.getPosition());

						} else {				
							document.getElementById(options.map_canvas_id).style.display='none';
						}
					});
					
				}else if(options.address==''){
					//console.log('t');
				}else{
					geocoder.geocode( { 'address': options.address}, function(results, status) {
						if (status == google.maps.GeocoderStatus.OK) {		
							//console.log('map '+results[0].geometry.location);
							map.setCenter(results[0].geometry.location);
							var marker = new google.maps.Marker({
								map: map,
								position: results[0].geometry.location,
								icon: options.iconURL
							});				
							
						} else {
							document.getElementById(options.map_canvas_id).style.display='none';				
						}
					});
				}
			}
		}


		// INIT
		code.init();

		
	};
}(jQuery));