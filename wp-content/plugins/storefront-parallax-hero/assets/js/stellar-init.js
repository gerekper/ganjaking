jQuery(document).ready(function($){

	if ( $( window ).width() >= 768 || $( 'body' ).hasClass( 'sph-do-mobile' ) ) {
		$.stellar({
			horizontalScrolling : false,
			positionProperty    : 'transform',
			hideDistantElements	: false,
		});
	}

});