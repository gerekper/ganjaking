/**
 * Table fields
 *
 * @package WC_OD
 * @since   1.7.0
 */

(function( $ ) {

	'use strict';

	$(function() {
		$( '.wc-od-field-table' ).on( 'click', 'tbody tr .row-actions .delete', function( event ) {
			event.preventDefault();

			$( this ).closest( 'tr' ).remove();
		});

		$( 'table.wc-od-field-table.sortable tbody' ).sortable({
			items: 'tr:not(.unsortable)',
			cursor: 'move',
			axis: 'y',
			handle: 'td.sort',
			scrollSensitivity: 40
		});
	});
})( jQuery );