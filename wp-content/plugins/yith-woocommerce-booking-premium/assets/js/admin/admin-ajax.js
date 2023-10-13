/* global jQuery, bk, wcbk_admin, ajaxurl */

// Make sure the yith object exists.
window.yith_booking = window.yith_booking || {};

( function ( $, yith_booking ) {

	yith_booking.adminAjax = function ( data, options ) {
		data    = typeof data !== 'undefined' ? data : {};
		options = typeof options !== 'undefined' ? options : {};

		data.action   = wcbk_admin.adminAjaxAction;
		data.security = wcbk_admin.nonces.adminAjax;

		if ( 'block' in options ) {
			options.block.block( bk.blockParams );
		}

		return $.ajax(
			{
				type    : 'POST',
				data    : data,
				url     : ajaxurl,
				complete: function () {
					if ( 'block' in options ) {
						options.block.unblock();
					}
				}
			}
		);
	};

} )( jQuery, window.yith_booking );