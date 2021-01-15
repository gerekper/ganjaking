/**
 * weLaunch Background
 * Dependencies        : jquery, wp media uploader
 * Feature added by    : Dovy Paukstys
 * Date                : 07 Jan 2014
 */

/*global welaunch_change, wp, welaunch, colorValidate */

(function( $ ) {
	'use strict';

	welaunch.field_objects            = welaunch.field_objects || {};
	welaunch.field_objects.background = welaunch.field_objects.background || {};

	welaunch.field_objects.background.init = function( selector ) {
		selector = $.welaunch.getSelector( selector, 'background' );

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

				// Remove the image button.
				el.find( '.welaunch-remove-background' ).unbind( 'click' ).on(
					'click',
					function( e ) {
						e.preventDefault();
						welaunch.field_objects.background.removeImage( $( this ).parents( '.welaunch-container-background:first' ) );

						return false;
					}
				);

				// Upload media button.
				el.find( '.welaunch-background-upload' ).unbind().on(
					'click',
					function( event ) {
						welaunch.field_objects.background.addImage( event, $( this ).parents( '.welaunch-container-background:first' ) );
					}
				);

				el.find( '.welaunch-background-input' ).on(
					'change',
					function() {
						welaunch.field_objects.background.preview( $( this ) );
					}
				);

				el.find( '.welaunch-color' ).wpColorPicker(
					{
						change: function( e, ui ) {
							$( this ).val( ui.color.toString() );
							welaunch_change( $( this ) );
							$( '#' + e.target.id + '-transparency' ).removeAttr( 'checked' );
							welaunch.field_objects.background.preview( $( this ) );
						},

						clear: function( e ) {
							e = null;
							welaunch_change( $( this ).parent().find( '.welaunch-color-init' ) );
							welaunch.field_objects.background.preview( $( this ) );
						}
					}
				);

				// Replace and validate field on blur.
				el.find( '.welaunch-color' ).on(
					'blur',
					function() {
						var value = $( this ).val();
						var id    = '#' + $( this ).attr( 'id' );

						if ( 'transparent' === value ) {
							$( this ).parent().parent().find( '.wp-color-result' ).css( 'background-color', 'transparent' );

							el.find( id + '-transparency' ).attr( 'checked', 'checked' );
						} else {
							if ( colorValidate( this ) === value ) {
								if ( 0 !== value.indexOf( '#' ) ) {
									$( this ).val( $( this ).data( 'oldcolor' ) );
								}
							}

							el.find( id + '-transparency' ).removeAttr( 'checked' );
						}
					}
				);

				el.find( '.welaunch-color' ).on(
					'focus',
					function() {
						$( this ).data( 'oldcolor', $( this ).val() );
					}
				);

				el.find( '.welaunch-color' ).on(
					'keyup',
					function() {
						var value = $( this ).val();
						var color = colorValidate( this );
						var id    = '#' + $( this ).attr( 'id' );

						if ( 'transparent' === value ) {
							$( this ).parent().parent().find( '.wp-color-result' ).css( 'background-color', 'transparent' );
							el.find( id + '-transparency' ).attr( 'checked', 'checked' );
						} else {
							el.find( id + '-transparency' ).removeAttr( 'checked' );

							if ( color && color !== $( this ).val() ) {
								$( this ).val( color );
							}
						}
					}
				);

				// When transparency checkbox is clicked.
				el.find( '.color-transparency' ).on(
					'click',
					function() {
						var prevColor;

						if ( $( this ).is( ':checked' ) ) {
							el.find( '.welaunch-saved-color' ).val( $( '#' + $( this ).data( 'id' ) ).val() );
							el.find( '#' + $( this ).data( 'id' ) ).val( 'transparent' );
							el.find( '#' + $( this ).data( 'id' ) ).parents( '.welaunch-field-container' ).find( '.wp-color-result' ).css( 'background-color', 'transparent' );
						} else {
							prevColor =  $( this ).parents( '.welaunch-field-container' ).find( '.welaunch-saved-color' ).val();
							if ( '' === prevColor ) {
								prevColor = $( '#' + $( this ).data( 'id' ) ).data( 'default-color' );
							}
							el.find( '#' + $( this ).data( 'id' ) ).parents( '.welaunch-field-container' ).find( '.wp-color-result' ).css( 'background-color', prevColor );
							el.find( '#' + $( this ).data( 'id' ) ).val( prevColor );
						}

						welaunch_change( $( this ) );
					}
				);

				el.find( ' .welaunch-background-repeat, .welaunch-background-clip, .welaunch-background-origin, .welaunch-background-size, .welaunch-background-attachment, .welaunch-background-position' ).select2();
			}
		);
	};

	// Update the background preview.
	welaunch.field_objects.background.preview = function( selector ) {
		var css;

		var hide    = true;
		var parent  = $( selector ).parents( '.welaunch-container-background:first' );
		var preview = $( parent ).find( '.background-preview' );

		if ( ! preview ) { // No preview present.
			return;
		}

		css = 'height:' + preview.height() + 'px;';

		$( parent ).find( '.welaunch-background-input' ).each(
			function() {
				var data = $( this ).serializeArray();

				data = data[0];
				if ( data && data.name.indexOf( '[background-' ) !== - 1 ) {
					if ( '' !== data.value ) {
						hide = false;

						data.name = data.name.split( '[background-' );
						data.name = 'background-' + data.name[1].replace( ']', '' );

						if ( 'background-image' === data.name ) {
							css += data.name + ':url("' + data.value + '");';
						} else {
							css += data.name + ':' + data.value + ';';
						}
					}
				}
			}
		);

		if ( ! hide ) {
			preview.attr( 'style', css ).fadeIn();
		} else {
			preview.slideUp();
		}
	};

	// Add a file via the wp.media function.
	welaunch.field_objects.background.addImage = function( event, selector ) {
		var frame;
		var jQueryel = $( this );

		event.preventDefault();

		// If the media frame already exists, reopen it.
		if ( frame ) {
			frame.open();
			return;
		}

		// Create the media frame.
		frame = wp.media(
			{
				multiple: false,
				library: {

				},
				title: jQueryel.data( 'choose' ),
				button: {
					text: jQueryel.data( 'update' )

				}
			}
		);

		// When an image is selected, run a callback.
		frame.on(
			'select',
			function() {
				var thumbSrc;
				var height;
				var key;
				var object;

				// Grab the selected attachment.
				var attachment = frame.state().get( 'selection' ).first();
				frame.close();

				if ( 'image' !== attachment.attributes.type ) {
					return;
				}

				selector.find( '.upload' ).val( attachment.attributes.url );
				selector.find( '.upload-id' ).val( attachment.attributes.id );
				selector.find( '.upload-height' ).val( attachment.attributes.height );
				selector.find( '.upload-width' ).val( attachment.attributes.width );

				welaunch_change( $( selector ).find( '.upload-id' ) );

				thumbSrc = attachment.attributes.url;

				if ( 'undefined' !== typeof attachment.attributes.sizes && 'undefined' !== typeof attachment.attributes.sizes.thumbnail ) {
					thumbSrc = attachment.attributes.sizes.thumbnail.url;
				} else if ( 'undefined' !== typeof attachment.attributes.sizes ) {
					height = attachment.attributes.height;

					for ( key in attachment.attributes.sizes ) {
						if ( attachment.attributes.sizes.hasOwnProperty( key ) ) {
							object = attachment.attributes.sizes[key];
							if ( object.height < height ) {
								height   = object.height;
								thumbSrc = object.url;
							}
						}
					}
				} else {
					thumbSrc = attachment.attributes.icon;
				}

				selector.find( '.upload-thumbnail' ).val( thumbSrc );

				if ( ! selector.find( '.upload' ).hasClass( 'noPreview' ) ) {
					selector.find( '.screenshot' ).empty().hide().append( '<img class="welaunch-option-image" src="' + thumbSrc + '">' ).slideDown( 'fast' );
				}

				selector.find( '.welaunch-remove-background' ).removeClass( 'hide' );
				selector.find( '.welaunch-background-input-properties' ).slideDown();

				welaunch.field_objects.background.preview( selector.find( '.upload' ) );
			}
		);

		// Finally, open the modal.
		frame.open();
	};

	// Update the background preview.
	welaunch.field_objects.background.removeImage = function( selector ) {
		var screenshot;

		// This shouldn't have been run...
		if ( ! selector.find( '.welaunch-remove-background' ).addClass( 'hide' ) ) {
			return;
		}

		selector.find( '.welaunch-remove-background' ).addClass( 'hide' ); // Hide "Remove" button.
		selector.find( '.upload' ).val( '' );
		selector.find( '.upload-id' ).val( '' );
		selector.find( '.upload-height' ).val( '' );
		selector.find( '.upload-width' ).val( '' );

		welaunch_change( $( selector ).find( '.upload-id' ) );

		selector.find( '.welaunch-background-input-properties' ).hide();

		screenshot = selector.find( '.screenshot' );

		// Hide the screenshot.
		screenshot.slideUp();

		selector.find( '.remove-file' ).unbind();

		// We don't display the upload button if .upload-notice is present
		// This means the user doesn't have the WordPress 3.5 Media Library Support.
		if ( $( '.section-upload .upload-notice' ).length > 0 ) {
			$( '.welaunch-background-upload' ).remove();
		}
	};
})( jQuery );
