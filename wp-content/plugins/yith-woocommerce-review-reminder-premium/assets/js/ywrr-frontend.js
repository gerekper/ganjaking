/**
 * Frontend JS scripts
 *
 * @package YITH\ReviewReminder
 */

jQuery(
	function ( $ ) {

		if ( window.location.hash === ywrr.reviews_tab ) {

			var tab_content = ywrr.reviews_tab.replace( '#' ).replace( 'tab-', '' );

			$( '.' + tab_content + '_tab a' ).trigger('click');

			if ( ywrr.reviews_form !== '' ) {
				$( 'html, body' ).animate(
					{
						scrollTop: $( ywrr.reviews_form ).offset().top + parseInt( ywrr.offset )
					},
					500
				);
			}

		}

	}
);
