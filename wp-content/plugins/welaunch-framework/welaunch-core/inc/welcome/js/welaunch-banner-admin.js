/* global console:true, ajaxurl */

(function( $ ) {
    $.welaunch_banner = $.welaunch_banner || {};
    $( document ).ready( function() {
    	var post_data = {
		    'action': 'welaunch_activation',
		    'nonce': $( '#welaunch-connect-message' ).data( 'nonce' )
	    };
		$( '.welaunch-connection-banner-action' ).on( 'click', function ( e ) {
			$( '#welaunch-connect-message' ).hide();
			e.preventDefault();
			post_data['activate'] = $(this).data( 'activate' );
			$.post( $( this ).data('url'), post_data );
		});
		jQuery('.welaunch-insights-data-we-collect').on('click', function( e ) {
			e.preventDefault();
			jQuery( this ).parents('.updated').find('p.description').slideToggle('fast');
		});
    });
})( jQuery );
