( function ( $ ) {
	$( document ).ready( function () {
		$( document ).on(
			'mouseenter',
			'.ultimate-call-to-action',
			function () {
				$( this ).addClass( 'ultimate-call-to-action-hover' );
				const hover = $( this ).data( 'background-hover' );
				$( this ).css( { 'background-color': hover } );
			}
		);

		$( document ).on(
			'mouseleave',
			'.ultimate-call-to-action',
			function () {
				$( this ).removeClass( 'ultimate-call-to-action-hover' );
				const background = $( this ).data( 'background' );
				$( this ).css( { 'background-color': background } );
			}
		);

		resize_call_to_action();

		$( window ).resize( function () {
			resize_call_to_action();
		} );
	} );

	function resize_call_to_action() {
		$( '.ultimate-call-to-action' ).each( function ( i, element ) {
			const override = $( element ).data( 'override' );
			if ( override != 0 ) {
				$( element ).css( { 'margin-left': 0 } );
				let is_relative = 'true';
				if ( $( element ).parents( '.wpb_row' ).length > 0 )
					var ancenstor = $( element ).parents( '.wpb_row' );
				else if ( $( element ).parents( '.wpb_column' ).length > 0 )
					var ancenstor = $( element ).parents( '.wpb_column' );
				else var ancenstor = $( element ).parent();

				const parent = ancenstor;
				if ( override == 'full' ) {
					ancenstor = $( 'body' );
					is_relative = 'false';
				}
				if ( override == 'ex-full' ) {
					ancenstor = $( 'html' );
					is_relative = 'false';
				}
				if ( ! isNaN( override ) ) {
					for ( var i = 1; i <= override; i++ ) {
						if ( ancenstor.prop( 'tagName' ) != 'HTML' ) {
							ancenstor = ancenstor.parent();
						} else {
							break;
						}
					}
				}

				const w = ancenstor.outerWidth();

				const element_left = $( element ).offset().left;
				const element_left_pos = $( element ).position().left;
				const holder_left = ancenstor.offset().left;
				const holder_left_pos = ancenstor.position().left;
				let calculate_left = holder_left - element_left;

				if ( override != 'full' && override != 'ex-full' ) {
					calculate_left = holder_left - holder_left_pos;
				}

				$( element ).css( { width: w, 'margin-left': calculate_left } );
			}
		} );
	}
} )( jQuery );
