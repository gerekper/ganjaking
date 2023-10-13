/* global jQuery */
( function ( $ ) {

	$( '.yith-wcbk-product-sync-imported-calendars-table' )
		.on( 'click', '.insert', function ( e ) {
			e.preventDefault();
			var button = $( e.target ),
				row    = button.data( 'row' ),
				table  = button.closest( '.yith-wcbk-product-sync-imported-calendars-table' ),
				tbody  = table.find( 'tbody' ), index;

			if ( table.data( 'last-index' ) ) {
				index = table.data( 'last-index' ) + 1;
			} else {
				index = tbody.find( 'tr' ).length || 0;
				index += 1;
			}

			table.data( 'last-index', index );
			row = row.replace( new RegExp( '{{INDEX}}', 'g' ), index );
			tbody.append( $( row ) );
		} )
		.on( 'click', '.delete', function ( e ) {
			e.preventDefault();
			var button = $( e.target ),
				row    = button.closest( 'tr' ),
				table  = button.closest( '.yith-wcbk-product-sync-imported-calendars-table' ),
				tbody  = table.find( 'tbody' );

			row.remove();
			if ( !tbody.find( 'tr' ).length ) {
				table.find( '.insert' ).trigger( 'click' );
			}
		} )
		.on( 'keyup change', 'tbody tr input[type=text]', function () {
			var row     = $( this ).closest( 'tr' ),
				inputs  = row.find( 'input[type=text]' ),
				isEmpty = true;

			inputs.each( function () {
				if ( $( this ).val() ) {
					isEmpty = false;
				}
			} );

			if ( isEmpty ) {
				row.addClass( 'is-empty' );
			} else {
				row.removeClass( 'is-empty' );
			}
		} );

} )( jQuery );