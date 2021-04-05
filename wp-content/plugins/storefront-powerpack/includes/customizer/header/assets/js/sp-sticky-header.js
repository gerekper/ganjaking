( function( $ ) {
	stickyHeader();

	function stickyHeader() {
		if ( ! $( '.site-header' ).length ) {
			return;
		}

		if ( $( window ).width() > 767 ) {
			$( 'body' ).addClass( 'sp-header-sticky' );
			$( '.site' ).css( 'padding-top', $( '.site-header' ).outerHeight() );
		} else {
			$( 'body' ).removeClass( 'sp-header-sticky' );
			$( '.site' ).css( 'padding-top', '' );
		}
	}

	$( document.body ).on( 'checkout_error', function() {
		var headerHeight = $( '.site-header' ).outerHeight();

		$( 'html, body' ).animate({
			scrollTop: ( $( '#primary' ).offset().top - headerHeight )
		}, 1000 );
	});

	$( window ).resize( function() {
		stickyHeader();
	});
})( jQuery );