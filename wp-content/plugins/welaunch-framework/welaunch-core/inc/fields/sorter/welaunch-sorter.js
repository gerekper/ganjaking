/*global welaunch, welaunch_change */
/*
 * Field Sorter jquery function
 * Based on
 * [SMOF - Slightly Modded Options Framework](http://aquagraphite.com/2011/09/slightly-modded-options-framework/)
 * Version 1.4.2
 */

(function( $ ) {
	'use strict';

	var scrollDir = '';

	welaunch.field_objects        = welaunch.field_objects || {};
	welaunch.field_objects.sorter = welaunch.field_objects.sorter || {};

	welaunch.field_objects.sorter.init = function( selector ) {
		selector = $.welaunch.getSelector( selector, 'sorter' );

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

				/**    Sorter (Layout Manager) */
				el.find( '.welaunch-sorter' ).each(
					function() {
						var id = $( this ).attr( 'id' );

						el.find( '#' + id ).find( 'ul' ).sortable(
							{
								items: 'li',
								placeholder: 'placeholder',
								connectWith: '.sortlist_' + id,
								opacity: 0.8,
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

									welaunch.field_objects.sorter.scrolling( $( this ).parents( '.welaunch-field-container:first' ) );
								},
								over: function() {
									scrollDir = '';
								},
								deactivate: function() {
									scrollDir = '';
								},
								stop: function( event, ui ) {
									var sorter;
									var id;

									event = null;

									sorter = welaunch.optName.sorter[$( this ).attr( 'data-id' )];
									id     = $( this ).find( 'h3' ).text();

									if ( sorter.limits && id && sorter.limits[id] ) {
										if ( $( this ).children( 'li' ).length >= sorter.limits[id] ) {
											$( this ).addClass( 'filled' );
											if ( $( this ).children( 'li' ).length > sorter.limits[id] ) {
												$( ui.sender ).sortable( 'cancel' );
											}
										} else {
											$( this ).removeClass( 'filled' );
										}
									}
								},
								update: function( event, ui ) {
									var sorter;
									var id;

									event = null;

									sorter = welaunch.optName.sorter[$( this ).attr( 'data-id' )];
									id     = $( this ).find( 'h3' ).text();

									if ( sorter.limits && id && sorter.limits[id] ) {
										if ( $( this ).children( 'li' ).length >= sorter.limits[id] ) {
											$( this ).addClass( 'filled' );
											if ( $( this ).children( 'li' ).length > sorter.limits[id] ) {
												$( ui.sender ).sortable( 'cancel' );
											}
										} else {
											$( this ).removeClass( 'filled' );
										}
									}

									$( this ).find( '.position' ).each(
										function() {
											var optionID;

											var listID   = $( this ).parent().attr( 'data-id' );
											var parentID = $( this ).parent().parent().attr( 'data-group-id' );

											welaunch_change( $( this ) );

											optionID = $( this ).parent().parent().parent().attr( 'id' );

											$( this ).prop( 'name', welaunch.optName.args.opt_name + '[' + optionID + '][' + parentID + '][' + listID + ']' );
										}
									);
								}
							}
						);

						el.find( '.welaunch-sorter' ).disableSelection();
					}
				);
			}
		);
	};

	welaunch.field_objects.sorter.scrolling = function( selector ) {
		var scrollable;

		if ( undefined === selector ) {
			return;
		}

		scrollable = selector.find( '.welaunch-sorter' );

		if ( 'up' === scrollDir ) {
			scrollable.scrollTop( scrollable.scrollTop() - 20 );
			setTimeout( welaunch.field_objects.sorter.scrolling, 50 );
		} else if ( 'down' === scrollDir ) {
			scrollable.scrollTop( scrollable.scrollTop() + 20 );
			setTimeout( welaunch.field_objects.sorter.scrolling, 50 );
		}
	};
})( jQuery );
