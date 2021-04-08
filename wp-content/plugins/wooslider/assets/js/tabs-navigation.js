jQuery(document).ready( function($) {
	// Make sure each heading has a unique ID.
	jQuery( 'ul#settings-sections.subsubsub' ).find( 'a' ).each( function ( i ) {
		var id_value = jQuery( this ).attr( 'href' ).replace( '#', '' );
		jQuery( 'h2:contains("' + jQuery( this ).text() + '")' ).attr( 'id', id_value ).addClass( 'section-heading' );
	});

	jQuery( '#wooslider .subsubsub a.tab' ).click( function ( e ) {
		// Move the "current" CSS class.
		jQuery( this ).parents( '.subsubsub' ).find( '.current' ).removeClass( 'current' );
		jQuery( this ).addClass( 'current' );

		// If "All" is clicked, show all.
		if ( jQuery( this ).hasClass( 'all' ) ) {
			jQuery( '#wooslider h2, #wooslider form p, #wooslider table.form-table, p.submit' ).show();

			return false;
		}

		// If the link is a tab, show only the specified tab.
		var toShow = jQuery( this ).attr( 'href' );

		// Remove the first occurance of # from the selected string (will be added manually below).
		toShow = toShow.replace( '#', '', toShow );

		jQuery( '#wooslider h2, #wooslider form > p:not(".submit"), #wooslider table' ).hide();
		jQuery( 'h2#' + toShow ).show().nextUntil( 'h2.section-heading', 'p, table, table p' ).show();

		return false;
	});
});