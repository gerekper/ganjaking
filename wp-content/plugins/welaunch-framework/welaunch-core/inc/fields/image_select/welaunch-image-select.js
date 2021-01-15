/* global welaunch, welaunch_change */

(function( $ ) {
	'use strict';

	welaunch.field_objects = welaunch.field_objects || {};
	welaunch.field_objects.image_select = welaunch.field_objects.image_select || {};

	welaunch.field_objects.image_select.init = function( selector ) {
		selector = $.welaunch.getSelector( selector, 'image_select' );

		$( selector ).each(
			function() {
				var el = $( this );
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

				// On label click, change the input and class.
				el.find( '.welaunch-image-select label img, .welaunch-image-select label .tiles' ).click(
					function( e ) {
						var presets;
						var data;
						var merge;
						var importCodeValue;

						var id = $( this ).closest( 'label' ).attr( 'for' );

						$( this ).parents( 'fieldset:first' ).find( '.welaunch-image-select-selected' ).removeClass(
							'welaunch-image-select-selected' ).find( 'input[type="radio"]' ).prop( 'checked', false );

						$( this ).closest( 'label' ).find( 'input[type="radio"]' ).prop( 'checked' );

						if ( $( this ).closest( 'label' ).hasClass( 'welaunch-image-select-preset-' + id ) ) { // If they clicked on a preset, import!
							e.preventDefault();

							presets = $( this ).closest( 'label' ).find( 'input' );
							data = presets.data( 'presets' );
							merge = presets.data( 'merge' );

							if ( undefined !== merge && null !== merge ) {
								if ( 'string' === $.type( merge ) ) {
									merge = merge.split( '|' );
								}

								$.each(
									data,
									function( index ) {
										if ( 'object' === $.type( welaunch.optName.options[index] ) && (
											true === merge || -1 !== $.inArray( index, merge ) )
										) {
											data[index] = $.extend( welaunch.optName.options[index], data[index] );
										}
									}
								);
							}

							if ( undefined !== presets && null !== presets ) {
								el.find( 'label[for="' + id + '"]' ).addClass( 'welaunch-image-select-selected' ).find(
									'input[type="radio"]' ).attr( 'checked', true );
								window.onbeforeunload = null;

								importCodeValue = $(
									'textarea[name="' + welaunch.optName.args.opt_name + '[import_code]"' );

								if ( 0 === importCodeValue.length ) {
									$( this ).append(
										'<textarea id="import-code-value" style="display:none;" name="' + welaunch.optName.args.opt_name + '[import_code]">' + JSON.stringify(
										data ) + '</textarea>' );
								} else {
									importCodeValue.val( JSON.stringify( data ) );
								}

								if ( 0 !== $( '#publishing-action #publish' ).length ) {
									$( '#publish' ).click();
								} else {
									$( '#welaunch-import' ).click();
								}
							}

							return false;
						} else {
							el.find( 'label[for="' + id + '"]' ).addClass( 'welaunch-image-select-selected' ).find(
								'input[type="radio"]' ).prop( 'checked', true ).trigger( 'change' );

							welaunch_change( $( this ).closest( 'label' ).find( 'input[type="radio"]' ) );
						}
					}
				);

				// Used to display a full image preview of a tile/pattern.
				el.find( '.tiles' ).qtip(
					{
						content: {
							text: function() {
								return '<img src="' + $( this ).attr( 'rel' ) + '" style="max-width:150px;" alt=" />';
							}
						}, style: 'qtip-tipsy', position: {
							my: 'top center', // Position my top left...
							at: 'bottom center' // At the bottom right of...
						}
					}
				);
			}
		);
	};
})( jQuery );
