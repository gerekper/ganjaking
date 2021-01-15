/* global welaunch_check_intro */

/**
 * Description
 */

( function( $ ) {
	'use strict';

	$(function() {
			$( '#theme-check > h2' ).html( $( '#theme-check > h2' ).html() + ' with weLaunch Theme-Check' );

			if ( 'undefined' !== typeof welaunch_check_intro ) {
				$( '#theme-check .theme-check' ).append( welaunch_check_intro.text );
			}

			$( '#theme-check form' ).append(
				'&nbsp;&nbsp;<input name="welaunch_wporg" type="checkbox">  Extra WP.org Requirements.'
			);
		}
	);
}( jQuery ) );
