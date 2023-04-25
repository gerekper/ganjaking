/**
 * Time frame Settings
 *
 * @package WC_OD
 * @since   1.5.0
 */

(function( $ ) {

	'use strict';

	$(function() {
		// Init timepickers.
		$( '.timepicker' ).timepicker({
			timeFormat: 'H:i',
			maxTime: '23:59'
		});
	});
})( jQuery );