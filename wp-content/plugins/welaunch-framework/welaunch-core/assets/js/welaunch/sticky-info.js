(function( $ ) {
	'use strict';

	$.welaunch = $.welaunch || {};

	$.welaunch.stickyInfo = function() {
		var stickyWidth = $( '.welaunch-main' ).innerWidth() - 20;
		var $width      = $( '#welaunch-sticky' ).offset().left;

		$( '.welaunch-save-warn' ).css( 'left', $width + 'px' );

		if ( ! $( '#info_bar' ).isOnScreen() && ! $( '#welaunch-footer-sticky' ).isOnScreen() ) {
			$( '#welaunch-footer' ).css(
				{ position: 'fixed', bottom: '0', width: stickyWidth, right: 21 }
			);

			$( '#welaunch-footer' ).addClass( 'sticky-footer-fixed' );
			$( '#welaunch-sticky-padder' ).show();
		} else {
			$( '#welaunch-footer' ).css(
				{ background: '#eee', position: 'inherit', bottom: 'inherit', width: 'inherit' }
			);

			$( '#welaunch-sticky-padder' ).hide();
			$( '#welaunch-footer' ).removeClass( 'sticky-footer-fixed' );
		}
		if ( ! $( '#info_bar' ).isOnScreen() ) {
			$( '#welaunch-sticky' ).addClass( 'sticky-save-warn' );
		} else {
			$( '#welaunch-sticky' ).removeClass( 'sticky-save-warn' );
		}
	};
})( jQuery );
