/*global jQuery, welaunch*/

(function( $ ) {
	'use strict';

	welaunch.field_objects      = welaunch.field_objects || {};
	welaunch.field_objects.date = welaunch.field_objects.date || {};

	welaunch.field_objects.date.init = function( selector ) {
		selector = $.welaunch.getSelector( selector, 'date' );

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

				el.find( '.welaunch-datepicker' ).each(
					function() {
						$( this ).datepicker(
							{
								'dateFormat': 'mm/dd/yy', beforeShow: function( input, instance ) {
									var el      = $( '#ui-datepicker-div' );
									var popover = instance.dpDiv;

									$( this ).parent().append( el );

									el.hide();
									setTimeout(
										function() {
											popover.position(
												{ my: 'left top', at: 'left bottom', collision: 'none', of: input }
											);
										},
										1
									);
								}
							}
						);
					}
				);
			}
		);
	};
})( jQuery );
