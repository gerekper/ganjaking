/* global jQuery, google */
( function ( $ ) {
	var initEvent          = 'yith-wcbk-init-fields:gm-places-autocomplete',
		selector           = '.yith-wcbk-google-maps-places-autocomplete:not(.yith-wcbk-google-maps-places-autocomplete--initialized)',
		initializedClass   = 'yith-wcbk-google-maps-places-autocomplete--initialized',
		isGoogleMapsLoaded = function () {
			return typeof google !== 'undefined' && typeof google.maps !== 'undefined' && typeof google.maps.places !== 'undefined' && typeof google.maps.places.Autocomplete !== 'undefined';
		},
		isAdmin            = function () {
			return 'adminpage' in window;
		},
		init               = function () {
			$( selector ).each( function () {
				var theField = $( this );

				if ( isGoogleMapsLoaded() ) {
					theField.addClass( initializedClass );

					if ( theField.hasClass( 'with-error' ) ) {
						theField.removeClass( 'with-error' );
						theField.removeAttr( 'readonly' );
					}

					new google.maps.places.Autocomplete( this );
				} else {
					if ( isAdmin() ) {
						theField.addClass( 'with-error' );
						theField.attr( 'readonly', 'readonly' );

						console.error( 'Google Maps Javascript API error while using Autocomplete. You have not set the Google Maps API keys correctly or other plugins (or your theme) include Google Maps Javascript API without support to the Places library.' );
					}
				}
			} );
		};

	$( document ).on( initEvent, init ).trigger( initEvent );

} )( jQuery );