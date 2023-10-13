/* global jQuery, bk */

// Make sure the yith object exists.
window.yith_booking = window.yith_booking || {};

( function ( $, yith_booking ) {

	yith_booking.ajax = function ( data, options ) {
		data    = typeof data !== 'undefined' ? data : {};
		options = typeof options !== 'undefined' ? options : {};

		data.action  = bk.frontendAjaxAction;
		data.context = 'frontend';

		if ( 'block' in options ) {
			options.block.block( bk.blockParams );
		}

		return $.ajax(
			{
				type    : 'POST',
				data    : data,
				url     : bk.ajaxurl,
				complete: function () {
					if ( 'block' in options ) {
						options.block.unblock();
					}
				}
			}
		);
	};

} )( jQuery, window.yith_booking );