/* global welaunch */

(function( $ ) {
	'use strict';

	$.welaunch = $.welaunch || {};

	$.welaunch.initFields = function() {
		$( '.welaunch-group-tab:visible' ).find( '.welaunch-field-init:visible' ).each(
			function() {
				var tr;
				var th;

				var type = $( this ).attr( 'data-type' );

				if ( 'undefined' !== typeof welaunch.field_objects && welaunch.field_objects[type] && welaunch.field_objects[type] ) {
					welaunch.field_objects[type].init();
				}

				if ( 'undefined' !== typeof welaunch.field_objects.pro && ! $.isEmptyObject( welaunch.field_objects.pro[type] ) && welaunch.field_objects.pro[type] ) {
					welaunch.field_objects.pro[type].init();
				}

				if ( ! welaunch.customizer && $( this ).hasClass( 'welaunch_remove_th' ) ) {
					tr = $( this ).parents( 'tr:first' );
					th = tr.find( 'th:first' );

					if ( th.html() && th.html().length > 0 ) {
						$( this ).prepend( th.html() );
						$( this ).find( '.welaunch_field_th' ).css( 'padding', '0 0 10px 0' );
					}

					$( this ).parent().attr( 'colspan', '2' );

					th.remove();
				}
			}
		);
	};
})( jQuery );
