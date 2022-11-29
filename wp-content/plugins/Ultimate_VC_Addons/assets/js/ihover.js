( function ( $, window, undefined ) {
	// Hide until page load
	$( window ).on( 'load', function () {
		$( '.ult-ih-container' ).css( { visibility: 'visible', opacity: 1 } );
	} );
	$( document ).ready( function () {
		ult_ihover_init();
		$( document ).ajaxComplete( function ( e, xhr, settings ) {
			ult_ihover_init();
		} );
	} );
	$( window ).resize( function () {
		ult_ihover_init();
	} );
	function responsive_sizes( el, ww, h, w, rh, rw ) {
		if ( ww != '' ) {
			if ( ww >= 768 ) {
				if (
					$( el ).find( '.ult-ih-item' ).hasClass( 'ult-ih-effect20' )
				) {
					$( el )
						.find( '.spinner' )
						.css( { height: parseInt( h ), width: parseInt( w ) } );
				} else {
					$( el )
						.find(
							'.ult-ih-item, .ult-ih-img, .ult-ih-image-block, .ult-ih-list-item'
						)
						.css( { height: h, width: w } );
				}
			} else if (
				$( el ).find( '.ult-ih-item' ).hasClass( 'ult-ih-effect20' )
			) {
				$( el )
					.find( '.spinner' )
					.css( { height: parseInt( rh ), width: parseInt( rw ) } );
			} else {
				$( el )
					.find(
						'.ult-ih-item, .ult-ih-img, .ult-ih-image-block, .ult-ih-list-item'
					)
					.css( { height: rh, width: rw } );
			}
		}
	}
	function ult_ihover_init() {
		$( '.ult-ih-list' ).each( function ( index, el ) {
			const s = $( el ).attr( 'data-shape' );
			const h = $( el ).attr( 'data-height' );
			const w = $( el ).attr( 'data-width' );
			const rh = $( el ).attr( 'data-res_height' );
			const rw = $( el ).attr( 'data-res_width' );
			const ww = jQuery( window ).width() || '';

			$( el )
				.find( 'li' )
				.each( function () {
					// Shape
					$( el )
						.find( '.ult-ih-item' )
						.addClass( 'ult-ih-' + s );

					// Responsive & Normal
					responsive_sizes( el, ww, h, w, rh, rw );
				} );
		} );
	}
} )( jQuery, window );
