jQuery(document).ready( function ( e ) {
	// Hide the advanced settings on first load, toggled via the ".advanced-settings" button.
	jQuery( '#wooslider-advanced-settings' ).hide();
	jQuery( 'a.advanced-settings' ).click( function ( e ) {
		jQuery( '#wooslider-advanced-settings' ).toggle();
	});

	// Hide the conditional boxes on first load, and show only the relevant section.
	var currentType = jQuery( 'select#slider_type' ).val();
	if ( typeof( currentType ) != 'undefined' ) {
		jQuery( '.conditional:not(".conditional-' + currentType + '")' ).hide();
		jQuery( '.conditional-' + currentType + '' ).show();
	}

	jQuery( 'select#slider_type' ).change( function ( e ) {
		var currentType = jQuery( 'select#slider_type' ).val();
		if ( typeof( currentType ) != 'undefined' ) {
			jQuery( '.conditional:not(".conditional-' + currentType + '")' ).hide();
			jQuery( '.conditional-' + currentType + '' ).show();
		}
		if( currentType == 'slides'){
			if(jQuery('#imageslide').attr('checked')) {
				jQuery( '.conditional-slide-settings' ).show();
				jQuery( '.conditional-slide-settings--carousel' ).hide();
			} else {
				jQuery( '.conditional-slide-settings' ).hide();
				jQuery( '.conditional-slide-settings--carousel' ).show();
			}

			if(jQuery('#carousel').attr('checked')) {
				jQuery( '.conditional-slide-settings' ).hide();
				jQuery( '.conditional-slide-settings--imageslide' ).hide();
			} else {
				jQuery( '.conditional-slide-settings--imageslide' ).show();
			}

			jQuery('#imageslide').click(function () {
				if(jQuery('#imageslide').attr('checked')) {
					jQuery( '.conditional-slide-settings' ).show();
					jQuery( '.conditional-slide-settings--carousel' ).hide();
				} else {
					jQuery( '.conditional-slide-settings' ).hide();
					jQuery( '.conditional-slide-settings--carousel' ).show();
				}
			});
			jQuery('#carousel').click(function () {
				if(jQuery('#carousel').attr('checked')) {
					jQuery( '.conditional-slide-settings' ).hide();
					jQuery( '.conditional-slide-settings--imageslide' ).hide();
				} else {
					jQuery( '.conditional-slide-settings' ).hide();
					jQuery( '.conditional-slide-settings--imageslide' ).show();
				}
			});
		}
	});


	// Shortcode creator logic.
	jQuery( 'form#wooslider-insert' ).submit( function ( e ) {
	var shortcode_atts = '';

	for ( var prop in wooslider_settings ) {
		if ( wooslider_settings.hasOwnProperty( prop ) ) {
			var defaultValue = wooslider_settings[prop];
			var element = jQuery( this ).find( '#' + prop + ':visible, #' + prop + '.range-input' );
			// Ignore the form fields if they're in a conditional box and that box is hidden.
			if ( element.parents( '.conditional' ).length == 1 && element.parents( '.conditional' ).is( ':hidden' ) ) { continue; }

			if ( element ) {
				// Checkboxes.
				if ( element.is( 'input' ) && 'checkbox' == element.attr( 'type' ) ) {
					if ( element.attr( 'checked' ) && defaultValue != 1 ) {
						shortcode_atts += ' ' + prop + '="true"';
					}
					if ( element.attr( 'checked' ) != 'checked' && defaultValue != 0 ) {
						shortcode_atts += ' ' + prop + '="false"';
					}
				}

				// Select fields.
				if ( element.is( 'select' ) && element.val() != defaultValue ) {
					shortcode_atts += ' ' + prop + '="' + element.val() + '"';
				}

				// Radio buttons.
				if ( element.is( 'input' ) && 'radio' == element.attr( 'type' ) ) {
					shortcode_atts += ' ' + prop + '="' + element.val() + '"';
				}

				// Text input fields.
				if ( element.is( 'input' ) && ( 'text' == element.attr( 'type' ) ) && element.val() != defaultValue ) {
					shortcode_atts += ' ' + prop + '="' + element.val() + '"';
				}
			}
		}

		// Cater for multicheck fields and individual checkbox fields.
		if ( wooslider_settings.hasOwnProperty( prop ) ) {
			var defaultValue = wooslider_settings[prop];
			var element = jQuery( this ).find( 'input.multicheck.multicheck-' + prop + ':checked' );
			// Ignore the form fields if they're in a conditional box and that box is hidden.
			if ( element.parents( '.conditional' ).length == 1 && element.parents( '.conditional' ).is( ':hidden' ) ) { continue; }

			var options_string = '';
			if ( element.length && element.is( 'input[type="checkbox"]' ) && element.hasClass( 'multicheck' ) ) {
				element.each( function ( i, e ) {
					if ( i > 0 ) { options_string += ','; }
					options_string += jQuery( this ).val();
				});
			}

			if ( options_string != '' ) {
				shortcode_atts += ' ' + prop + '="' + options_string + '"';
			}
		}

		// Cater for radio inputs.
		if ( wooslider_settings.hasOwnProperty( prop ) ) {
			var defaultValue = wooslider_settings[prop];
			var element = jQuery( this ).find( 'input[type="radio"][name="' + prop + '"]:checked' );
			// Ignore the form fields if they're in a conditional box and that box is hidden.
			if ( element.parents( '.conditional' ).length == 1 && element.parents( '.conditional' ).is( ':hidden' ) ) { continue; }

			if ( element.length && element.is( 'input[type="radio"]' ) ) {
				shortcode_atts += ' ' + prop + '="' + element.val() + '"';
			}
		}
	}

	var shortcode = '[wooslider' + shortcode_atts + '] ';
	var win = window.dialogArguments || opener || parent || top;
	win.send_to_editor( shortcode );
	return false;
});
});