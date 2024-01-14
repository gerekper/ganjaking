jQuery(function ($) {
	'use strict';
	try {
		$(document.body).on('srp-enhanced-lightcase', function () {
			var lightcases = $('.srp-popup-point-lightcase');

			if ( ! lightcases.length ) {
				return;
			}

			lightcases.each( function ( ) {
				$( this ).lightcase( {
					href : $( this ).data( 'popup' ) ,
					onFinish : {
						foo : function ( ) {
							lightcase.resize( ) ;
						}
					} ,
				} ) ;
			} ) ;
		});
		$(document.body).trigger('srp-enhanced-lightcase');
	} catch (err) {
		window.console.log(err);
	}

});
