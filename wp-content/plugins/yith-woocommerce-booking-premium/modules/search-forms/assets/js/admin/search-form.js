jQuery( function ( $ ) {
	"use strict";
	var field_container = $( '.yith-wcbk-admin-search-form-fields' );

	field_container.sortable(
		{
			items               : '.yith-wcbk-admin-search-form-field',
			cursor              : 'move',
			handle              : '.yith-wcbk-admin-search-form-field-title',
			scrollSensitivity   : 40,
			forcePlaceholderSize: true,
			revert              : 200,
			axis                : 'y'
		}
	);

	$( document ).on( 'click', '.yith-wcbk-admin-search-form-field-title__toggle', function ( event ) {
		event.stopPropagation();
		var target = $( this ),
			parent = target.closest( '.yith-wcbk-admin-search-form-field' );

		parent.toggleClass( 'yith-wcbk-admin-search-form-field--opened' );
	} );
} );