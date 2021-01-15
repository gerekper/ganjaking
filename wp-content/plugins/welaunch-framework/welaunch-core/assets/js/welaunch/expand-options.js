(function( $ ) {
	'use strict';

	$.welaunch = $.welaunch || {};

	$.welaunch.expandOptions = function( parent ) {
		var trigger = parent.find( '.expand_options' );
		var width   = parent.find( '.welaunch-sidebar' ).width() - 1;
		var id      = $( '.welaunch-group-menu .active a' ).data( 'rel' ) + '_section_group';

		if ( trigger.hasClass( 'expanded' ) ) {
			trigger.removeClass( 'expanded' );
			parent.find( '.welaunch-main' ).removeClass( 'expand' );

			parent.find( '.welaunch-sidebar' ).stop().animate(
				{ 'margin-left': '0px' },
				500
			);

			parent.find( '.welaunch-main' ).stop().animate(
				{ 'margin-left': width },
				500,
				function() {
					parent.find( '.welaunch-main' ).attr( 'style', '' );
				}
			);

			parent.find( '.welaunch-group-tab' ).each(
				function() {
					if ( $( this ).attr( 'id' ) !== id ) {
						$( this ).fadeOut( 'fast' );
					}
				}
			);

			// Show the only active one.
		} else {
			trigger.addClass( 'expanded' );
			parent.find( '.welaunch-main' ).addClass( 'expand' );

			parent.find( '.welaunch-sidebar' ).stop().animate(
				{ 'margin-left': - width - 113 },
				500
			);

			parent.find( '.welaunch-main' ).stop().animate(
				{ 'margin-left': '-1px' },
				500
			);

			parent.find( '.welaunch-group-tab' ).fadeIn(
				'medium',
				function() {
					$.welaunch.initFields();
				}
			);
		}

		return false;
	};
})( jQuery );
