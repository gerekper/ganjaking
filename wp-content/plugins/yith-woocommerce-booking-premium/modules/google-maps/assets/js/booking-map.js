/* globals jQuery, google */
jQuery( function ( $ ) {

	var isGoogleMapsLoaded = function () {
		return typeof google !== 'undefined' && typeof google.maps !== 'undefined';
	};

	$.fn.yith_booking_map = function () {
		var self = $( this );

		if ( !isGoogleMapsLoaded() ) {
			return;
		}

		self.each( function () {
			var element   = $( this )[ 0 ],
				latitude  = $( this ).data( 'latitude' ),
				longitude = $( this ).data( 'longitude' ),
				zoom      = $( this ).data( 'zoom' ),
				type      = $( this ).data( 'type' ),
				latlng    = new google.maps.LatLng( latitude, longitude ),
				map_type  = google.maps.MapTypeId.ROADMAP;

			switch ( type ) {
				case 'HYBRID':
					map_type = google.maps.MapTypeId.HYBRID;
					break;
				case 'SATELLITE':
					map_type = google.maps.MapTypeId.SATELLITE;
					break;
				case 'TERRAIN':
					map_type = google.maps.MapTypeId.TERRAIN;
					break;
				default:
					break;
			}

			var mapOptions = {
				zoom     : zoom,
				center   : latlng,
				mapTypeId: map_type
			};
			var map        = new google.maps.Map( element, mapOptions );

			var marker = new google.maps.Marker( {
													 position: latlng,
													 map     : map
												 } );

			map.setCenter( latlng );
			marker.setPosition( latlng );


		} );
	};

	$( '.yith-wcbk-booking-map' ).yith_booking_map();

	// reload booking map in WooCommerce tabs to prevent display issue
	var wc_tabs_panel = $( '.woocommerce-Tabs-panel' );
	$( document ).on( 'click', '.woocommerce-tabs li a', function () {
		wc_tabs_panel.find( '.yith-wcbk-booking-map' ).yith_booking_map();
	} );

	// quick view
	$( document ).on( 'qv_loader_stop', function () {
		$( '.yith-quick-view .yith-wcbk-booking-map' ).yith_booking_map();
	} );

} );