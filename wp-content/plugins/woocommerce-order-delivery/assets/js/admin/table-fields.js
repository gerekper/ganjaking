/**
 * Table fields
 *
 * @package WC_OD
 * @since   {version}
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