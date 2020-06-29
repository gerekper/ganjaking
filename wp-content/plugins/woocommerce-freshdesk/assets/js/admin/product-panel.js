(function ( $ ) {
	'use strict';

	$(function () {
		function switch_category( type ) {
			var category                   = $( '#_' + type + '_category' ),
				category_id_field          = $( '._' + type + '_category_id_field' ),
				category_title_field       = $( '._' + type + '_category_title_field' ),
				category_description_field = $( '._' + type + '_category_description_field' );

			// Hidden fields.
			function hide_all() {
				category_id_field.hide();
				category_title_field.hide();
				category_description_field.hide();
			}

			function show_hide_fields( type ) {
				if ( 'create' === type ) {
					hide_all();
					category_title_field.show();
					category_description_field.show();
				} else if ( 'sync' === type ) {
					hide_all();
					category_id_field.show();
				} else {
					hide_all();
				}
			}

			if ( category.length ) {
				hide_all();
				show_hide_fields( category.val() );

				$( category ).on( 'change', function () {
					show_hide_fields( $( this ).val() );
				});
			}
		}

		switch_category( 'forum' );
		switch_category( 'solutions' );
	});

}( jQuery ));
