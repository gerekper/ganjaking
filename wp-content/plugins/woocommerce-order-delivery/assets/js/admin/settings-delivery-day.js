/**
 * Delivery Day Settings
 *
 * @package WC_OD
 * @since   1.5.0
 */

(function( $ ) {

	'use strict';

	$(function() {
		$( '.time_frames' ).on( 'click', 'tbody tr a.wc-od-time-frame-delete', function( event ) {
			event.preventDefault();

			$( this ).closest( 'tr' ).remove();
		});
	});
})( jQuery );