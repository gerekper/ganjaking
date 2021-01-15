/*global jQuery, welaunch_change, welaunch*/

(function( $ ) {
	'use strict';

	var scrollDir = '';

	welaunch.field_objects          = welaunch.field_objects || {};
	welaunch.field_objects.sortable = welaunch.field_objects.sortable || {};

	welaunch.field_objects.sortable.init = function( selector ) {
		selector = $.welaunch.getSelector( selector, 'sortable' );

		$( selector ).each(
			function() {
				var el     = $( this );
				var parent = el;

				if ( ! el.hasClass( 'welaunch-field-container' ) ) {
					parent = el.parents( '.welaunch-field-container:first' );
				}

				if ( parent.is( ':hidden' ) ) {
					return;
				}

				if ( parent.hasClass( 'welaunch-field-init' ) ) {
					parent.removeClass( 'welaunch-field-init' );
				} else {
					return;
				}

				el.find( '.welaunch-sortable' ).sortable(
					{
						handle: '.drag',
						placeholder: 'placeholder',
						opacity: 0.7,
						scroll: false,
						out: function( event, ui ) {
							event = null;

							if ( ! ui.helper ) {
								return;
							}

							if ( ui.offset.top > 0 ) {
								scrollDir = 'down';
							} else {
								scrollDir = 'up';
							}

							welaunch.field_objects.sortable.scrolling( $( this ).parents( '.welaunch-field-container:first' ) );
						},
						over: function() {
							scrollDir = '';
						},
						deactivate: function() {
							scrollDir = '';
						},
						update: function() {
							welaunch_change( $( this ) );
						}
					}
				);

				el.find( '.welaunch-sortable i.visibility' ).on(
					'click',
					function() {
						var val;
						var hiddenInput;

						var li = $( this ).parents( 'li' );

						if ( li.hasClass( 'invisible' ) ) {
							li.removeClass( 'invisible' );
							val = 1;
						} else {
							li.addClass( 'invisible' );
							val = '';
						}

						hiddenInput = li.find( 'input[type="hidden"]' );

						hiddenInput.val( val );
					}
				);
			}
		);
	};

	welaunch.field_objects.sortable.scrolling = function( selector ) {
		var $scrollable;

		if ( undefined === selector ) {
			return;
		}

		$scrollable = selector.find( '.welaunch-sorter' );

		if ( 'up' === scrollDir ) {
			$scrollable.scrollTop( $scrollable.scrollTop() - 20 );
			setTimeout( welaunch.field_objects.sortable.scrolling, 50 );
		} else if ( 'down' === scrollDir ) {
			$scrollable.scrollTop( $scrollable.scrollTop() + 20 );
			setTimeout( welaunch.field_objects.sortable.scrolling, 50 );
		}
	};
})( jQuery );
