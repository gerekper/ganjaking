/*global welaunch_change, welaunch, welaunch_typography_ajax, WebFont */

/**
 * Typography
 * Dependencies:        google.com, jquery, select2
 * Feature added by:    Dovy Paukstys - http://simplerain.com/
 * Date:                06.14.2013
 *
 * Rewrite:             Kevin Provance (kprovance)
 * Date:                May 25, 2014
 * And again on:        April 4, 2017 for v4.0
 */
(function( $ ) {
	'use strict';

	var selVals     = [];
	var isSelecting = false;
	var proLoaded   = true;

	welaunch.field_objects            = welaunch.field_objects || {};
	welaunch.field_objects.typography = welaunch.field_objects.typography || {};

	welaunch.field_objects.typography.init = function( selector ) {
		selector = $.welaunch.getSelector( selector, 'typography' );

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

				if ( undefined === welaunch.field_objects.pro ) {
					proLoaded = false;
				}

				el.each(
					function() {

						// Init each typography field.
						$( this ).find( '.welaunch-typography-container' ).each(
							function() {
								var el     = $( this );
								var parent = el;
								var key;
								var obj;
								var prop;
								var fontData;
								var val;
								var xx;
								var welaunchTypography;

								var family           = $( this ).find( '.welaunch-typography-family' );
								var familyData       = family.data( 'value' );
								var data             = [{ id: 'none', text: 'none' }];
								var thisID           = $( this ).find( '.welaunch-typography-family' ).parents( '.welaunch-container-typography:first' ).data( 'id' );
								var usingGoogleFonts = $( '#' + thisID + ' .welaunch-typography-google' ).val();

								// Set up data array.
								var buildData = [];
								var fontKids  = [];

								// User included fonts?
								var isUserFonts = $( '#' + thisID + ' .welaunch-typography-font-family' ).data( 'user-fonts' );

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

								if ( undefined === familyData ) {
									family = $( this );
								} else if ( '' !== familyData ) {
									$( family ).val( familyData );
								}

								isUserFonts = isUserFonts ? 1 : 0;

								// Google font isn use?
								usingGoogleFonts = usingGoogleFonts ? 1 : 0;

								// If custom fonts, push onto array.
								if ( undefined !== welaunch.customfonts ) {
									buildData.push( welaunch.customfonts );
								}

								// If typekit fonts, push onto array.
								if ( undefined !== welaunch.typekitfonts ) {
									buildData.push( welaunch.typekitfonts );
								}

								// If standard fonts, push onto array.
								if ( undefined !== welaunch.stdfonts && 0 === isUserFonts ) {
									buildData.push( welaunch.stdfonts );
								}

								// If user fonts, pull from localize and push into array.
								if ( 1 === isUserFonts ) {

									// <option>
									for ( key in welaunch.optName.typography[thisID] ) {
										if ( welaunch.optName.typography[thisID].hasOwnProperty( key ) ) {
											obj = welaunch.optName.typography[thisID].std_font;

											for ( prop in obj ) {
												if ( obj.hasOwnProperty( prop ) ) {
													fontKids.push(
														{
															id: prop,
															text: prop,
															'data-google': 'false'
														}
													);
												}
											}
										}
									}

									// <optgroup>
									fontData = {
										text: 'Standard Fonts',
										children: fontKids
									};

									buildData.push( fontData );
								}

								// If googfonts on and had data, push into array.
								if ( 1 === usingGoogleFonts || true === usingGoogleFonts && undefined !== welaunch.googlefonts ) {
									buildData.push( welaunch.googlefonts );
								}

								// Output data to drop down.
								data = buildData;

								val = $( this ).find( '.welaunch-typography-family' ).data( 'value' );

								$( this ).find( '.welaunch-typography-family' ).addClass( 'ignore-change' );

								$( this ).find( '.welaunch-typography-family' ).select2( { data: data } );
								$( this ).find( '.welaunch-typography-family' ).val( val ).trigger( 'change' );

								$( this ).find( '.welaunch-typography-family' ).removeClass( 'ignore-change' );

								xx = el.find( '.welaunch-typography-family' );
								if ( ! xx.hasClass( 'welaunch-typography-family' ) ) {
									el.find( '.welaunch-typography-style' ).select2();
								}

								$( this ).find( '.welaunch-typography-align' ).select2();
								$( this ).find( '.welaunch-typography-family-backup' ).select2();
								$( this ).find( '.welaunch-typography-transform' ).select2();
								$( this ).find( '.welaunch-typography-font-variant' ).select2();
								$( this ).find( '.welaunch-typography-decoration' ).select2();

								$( this ).find( '.welaunch-insights-data-we-collect-typography' ).on( 'click', function( e ) {
									e.preventDefault();
									$( this ).parent().find( '.description' ).toggle();
								});

								// Init select2 for indicated fields.
								welaunch.field_objects.typography.select( family, true, false, null, true );

								// Init when value is changed.
								$( this ).find( '.welaunch-typography-family, .welaunch-typography-family-backup, .welaunch-typography-style, .welaunch-typography-subsets, .welaunch-typography-align' ).on(
									'change',
									function( val ) {
										var getVals;
										var fontName;

										var thisID = $( this ).attr( 'id' ), that = $( '#' + thisID );

										if ( $( this ).hasClass( 'welaunch-typography-family' ) ) {
											if ( that.val() ) {
												getVals = $( this ).select2( 'data' );

												if ( getVals ) {
													fontName = getVals[0].text;
												} else {
													fontName = null;
												}

												that.data( 'value', fontName );

												selVals = getVals[0];

												isSelecting = true;

												welaunch.field_objects.typography.select( that, true, false, fontName, true );
											}
										} else {
											val = that.val();

											that.data( 'value', val );

											if ( $( this ).hasClass( 'welaunch-typography-align' ) ||
												$( this ).hasClass( 'welaunch-typography-subsets' ) ||
												$( this ).hasClass( 'welaunch-typography-family-backup' ) ||
												$( this ).hasClass( 'welaunch-typography-transform' ) ||
												$( this ).hasClass( 'welaunch-typography-font-variant' ) ||
												$( this ).hasClass( 'welaunch-typography-decoration' ) ) {
												that.find( 'option[selected="selected"]' ).removeAttr( 'selected' );
												that.find( 'option[value="' + val + '"]' ).attr( 'selected', 'selected' );
											}

											if ( $( this ).hasClass( 'welaunch-typography-subsets' ) ) {
												that.siblings( '.typography-subsets' ).val( val );
											}

											welaunch.field_objects.typography.select( $( this ), true, false, null, false );
										}
									}
								);

								// Init when value is changed.
								$( this ).find( '.welaunch-typography-size, .welaunch-typography-height, .welaunch-typography-word, .welaunch-typography-letter' ).keyup(
									function() {
										welaunch.field_objects.typography.select( $( this ).parents( '.welaunch-container-typography:first' ) );
									}
								);

								if ( proLoaded ) {
									welaunch.field_objects.pro.typography.fieldChange( $( this ) );
									welaunch.field_objects.pro.typography.colorPicker( $( this ) );
								}

								// Have to redeclare the wpColorPicker to get a callback function.
								$( this ).find( '.welaunch-typography-color' ).wpColorPicker(
									{
										change: function( e, ui ) {
											e = null;
											$( this ).val( ui.color.toString() );
											welaunch.field_objects.typography.select( $( this ).parents( '.welaunch-container-typography:first' ) );
										}
									}
								);

								// Don't allow negative numbers for size field.
								$( this ).find( '.welaunch-typography-size' ).numeric( { allowMinus: false } );

								// Allow negative numbers for indicated fields.
								$( this ).find( '.welaunch-typography-height, .welaunch-typography-word, .welaunch-typography-letter' ).numeric( { allowMinus: true } );

								welaunchTypography = $( this ).find( '.welaunch-typography' );

								welaunchTypography.on(
									'select2:unselecting',
									function() {
										var thisID;
										var that;

										var opts = $( this ).data( 'select2' ).options;

										opts.set( 'disabled', true );
										setTimeout(
											function() {
												opts.set( 'disabled', false );
											},
											1
										);

										thisID = $( this ).attr( 'id' );
										that   = $( '#' + thisID );

										that.data( 'value', '' );

										if ( $( this ).hasClass( 'welaunch-typography-family' ) ) {
											$( this ).find( '.welaunch-typography-family' ).addClass( 'ignore-change' );
											$( this ).val( null ).trigger( 'change' );
											$( this ).find( '.welaunch-typography-family' ).removeClass( 'ignore-change' );

											welaunch.field_objects.typography.select( that, true, false, null, true );
										} else {
											if ( $( this ).hasClass( 'welaunch-typography-align' ) ||
												$( this ).hasClass( 'welaunch-typography-subsets' ) ||
												$( this ).hasClass( 'welaunch-typography-family-backup' ) ||
												$( this ).hasClass( 'welaunch-typography-transform' ) ||
												$( this ).hasClass( 'welaunch-typography-font-variant' ) ||
												$( this ).hasClass( 'welaunch-typography-decoration' ) ) {
												$( '#' + thisID + ' option[selected="selected"]' ).removeAttr( 'selected' );
											}

											if ( $( this ).hasClass( 'welaunch-typography-subsets' ) ) {
												that.siblings( '.typography-subsets' ).val( '' );
											}

											if ( $( this ).hasClass( 'welaunch-typography-family-backup' ) ) {
												$( this ).find( '.welaunch-typography-family-backup' ).addClass( 'ignore-change' );
												that.val( null ).trigger( 'change' );
												$( this ).find( '.welaunch-typography-family-backup' ).removeClass( 'ignore-change' );
											}

											welaunch.field_objects.typography.select( $( this ), true, false, null, false );
										}
									}
								);

								welaunch.field_objects.typography.updates( $( this ) );

								window.onbeforeunload = null;
								parent.removeClass( 'welaunch-field-init' );
							}
						);
					}
				);
			}
		);
	};

	welaunch.field_objects.typography.updates = function( obj ) {
		obj.find( '.update-google-fonts' ).bind(
			'click',
			function( e ) {
				var $action        = $( this ).data( 'action' );
				var $update_parent = $( this ).parent().parent();
				var $nonce         = $update_parent.attr( 'data-nonce' );

				$update_parent.find( 'p' ).text( welaunch_typography_ajax.update_google_fonts.updating );
				$update_parent.find( 'p' ).attr( 'aria-label', welaunch_typography_ajax.update_google_fonts.updating );

				$update_parent.removeClass( 'updating-message updated-message notice-success notice-warning notice-error' ).addClass( 'update-message notice-warning updating-message' );

				$.ajax(
					{
						type: 'post',
						dataType: 'json',
						url: welaunch_typography_ajax.ajaxurl,
						data: {
							action: 'welaunch_update_google_fonts',
							nonce: $nonce,
							data: $action
						},
						error: function( response ) {
							var msg;

							console.log( response );
							$update_parent.removeClass( 'notice-warning updating-message updated-message notice-success' ).addClass( 'notice-error' );

							msg = response.error;

							if ( msg ) {
								msg = ': "' + msg + '"';
							}

							$update_parent.find( 'p' ).html( welaunch_typography_ajax.update_google_fonts.error.replace( '%s', $action ).replace( '|msg', msg ) );
							$update_parent.find( 'p' ).attr( 'aria-label', welaunch_typography_ajax.update_google_fonts.error );
							welaunch.field_objects.typography.updates( obj );
						},
						success: function( response ) {
							var msg;

							console.log( response );

							if ( 'success' === response.status ) {
								$update_parent.find( 'p' ).html( welaunch_typography_ajax.update_google_fonts.success );
								$update_parent.find( 'p' ).attr( 'aria-label', welaunch_typography_ajax.update_google_fonts.success );
								$update_parent.removeClass( 'updating-message notice-warning' ).addClass( 'updated-message notice-success' );
								$( '.welaunch-update-google-fonts' ).not( '.notice-success' ).remove();
							} else {
								$update_parent.removeClass( 'notice-warning updating-message updated-message notice-success' ).addClass( 'notice-error' );

								msg = response.error;

								if ( msg ) {
									msg = ': "' + msg + '"';
								}

								$update_parent.find( 'p' ).html( welaunch_typography_ajax.update_google_fonts.error.replace( '%s', $action ).replace( '|msg', msg ) );
								$update_parent.find( 'p' ).attr( 'aria-label', welaunch_typography_ajax.update_google_fonts.error );

								welaunch.field_objects.typography.updates( obj );
							}
						}
					}
				);

				e.preventDefault();

				return false;
			}
		);
	};

	// Return font size.
	welaunch.field_objects.typography.size = function( obj ) {
		var size = 0;
		var key;

		for ( key in obj ) {
			if ( obj.hasOwnProperty( key ) ) {
				size += 1;
			}
		}

		return size;
	};

	// Return proper bool value.
	welaunch.field_objects.typography.makeBool = function( val ) {
		if ( 'false' === val || '0' === val || false === val || 0 === val ) {
			return false;
		} else if ( 'true' === val || '1' === val || true === val || 1 === val ) {
			return true;
		}
	};

	welaunch.field_objects.typography.contrastColour = function( hexcolour ) {
		var r;
		var b;
		var g;
		var res;

		// Default value is black.
		var retVal = '#444444';

		// In case - for some reason - a blank value is passed.
		// This should *not* happen.  If a function passing a value
		// is canceled, it should pass the current value instead of
		// a blank.  This is how the Windows Common Controls do it.  :P .
		if ( '' !== hexcolour ) {

			// Replace the hash with a blank.
			hexcolour = hexcolour.replace( '#', '' );

			r   = parseInt( hexcolour.substr( 0, 2 ), 16 );
			g   = parseInt( hexcolour.substr( 2, 2 ), 16 );
			b   = parseInt( hexcolour.substr( 4, 2 ), 16 );
			res = ( ( r * 299 ) + ( g * 587 ) + ( b * 114 ) ) / 1000;

			// Instead of pure black, I opted to use WP 3.8 black, so it looks uniform.  :) - kp.
			retVal = ( res >= 128 ) ? '#444444' : '#ffffff';
		}

		return retVal;
	};

	// Sync up font options.
	welaunch.field_objects.typography.select = function( selector, skipCheck, destroy, fontName, active ) {
		var mainID;
		var that;
		var family;
		var google;
		var familyBackup;
		var size;
		var height;
		var word;
		var letter;
		var align;
		var transform;
		var fontVariant;
		var decoration;
		var style;
		var script;
		var color;
		var units;
		var _linkclass;
		var the_font;
		var link;
		var isPreviewSize;

		var typekit              = false;
		var details              = '';
		var html                 = '<option value=""></option>';
		var selected             = '';
		var allowEmptyLineHeight = false;
		var default_font_weights = {
			'400': 'Normal 400',
			'700': 'Bold 700',
			'400italic': 'Normal 400 Italic',
			'700italic': 'Bold 700 Italic'
		};

		// Main id for selected field.
		mainID = $( selector ).parents( '.welaunch-container-typography:first' ).data( 'id' );
		if ( undefined === mainID ) {
			mainID = $( selector ).data( 'id' );
		}

		that   = $( '#' + mainID );
		family = $( '#' + mainID + '-family' ).val();

		if ( ! family ) {
			family = null; // 'inherit';
		}

		if ( fontName ) {
			family = fontName;
		}

		familyBackup = that.find( 'select.welaunch-typography-family-backup' ).val();
		size         = that.find( '.welaunch-typography-size' ).val();
		height       = that.find( '.welaunch-typography-height' ).val();
		word         = that.find( '.welaunch-typography-word' ).val();
		letter       = that.find( '.welaunch-typography-letter' ).val();
		align        = that.find( 'select.welaunch-typography-align' ).val();
		transform    = that.find( 'select.welaunch-typography-transform' ).val();
		fontVariant  = that.find( 'select.welaunch-typography-font-variant' ).val();
		decoration   = that.find( 'select.welaunch-typography-decoration' ).val();
		style        = that.find( 'select.welaunch-typography-style' ).val();
		script       = that.find( 'select.welaunch-typography-subsets' ).val();
		color        = that.find( '.welaunch-typography-color' ).val();
		units        = that.data( 'units' );

		// Is selected font a google font?
		if ( true === isSelecting ) {
			google = welaunch.field_objects.typography.makeBool( selVals['data-google'] );
			that.find( '.welaunch-typography-google-font' ).val( google );
		} else {
			google = welaunch.field_objects.typography.makeBool( that.find( '.welaunch-typography-google-font' ).val() ); // Check if font is a google font.
		}

		if ( active ) {

			// Page load. Speeds things up memory wise to offload to client.
			if ( ! that.hasClass( 'typography-initialized' ) ) {
				style  = that.find( 'select.welaunch-typography-style' ).data( 'value' );
				script = that.find( 'select.welaunch-typography-subsets' ).data( 'value' );

				if ( '' !== style ) {
					style = String( style );
				}

				if ( undefined !== typeof ( script ) ) {
					script = String( script );
				}
			}

			// Something went wrong trying to read google fonts, so turn google off.
			if ( undefined === welaunch.fonts.google ) {
				google = false;
			}

			// Get font details.
			if ( true === google && ( family in welaunch.fonts.google ) ) {
				details = welaunch.fonts.google[family];
			} else {
				if ( undefined !== welaunch.fonts.typekit && ( family in welaunch.fonts.typekit ) ) {
					typekit = true;
					details = welaunch.fonts.typekit[family];
				} else {
					details = default_font_weights;
				}
			}

			if ( $( selector ).hasClass( 'welaunch-typography-subsets' ) ) {
				that.find( 'input.typography-subsets' ).val( script );
			}

			// If we changed the font.
			if ( $( selector ).hasClass( 'welaunch-typography-family' ) ) {

				// Google specific stuff.
				if ( true === google ) {

					// STYLES.
					$.each(
						details.variants,
						function( index, variant ) {
							index = null;
							if ( variant.id === style || 1 === welaunch.field_objects.typography.size( details.variants ) ) {
								selected = ' selected="selected"';
								style    = variant.id;
							} else {
								selected = '';
							}

							html += '<option value="' + variant.id + '"' + selected + '>' + variant.name.replace( /\+/g, ' ' ) + '</option>';
						}
					);

					// Destroy select2.
					if ( destroy ) {
						that.find( '.welaunch-typography-style' ).select2( 'destroy' );
					}

					// Instert new HTML.
					that.find( '.welaunch-typography-style' ).html( html ).select2();

					// SUBSETS.
					selected = '';
					html     = '<option value=""></option>';

					$.each(
						details.subsets,
						function( index, subset ) {
							index = null;
							if ( script === subset.id || 1 === welaunch.field_objects.typography.size( details.subsets ) ) {
								selected = ' selected="selected"';
								script   = subset.id;
								that.find( 'input.typography-subsets' ).val( script );
							} else {
								selected = '';
							}
							html += '<option value="' + subset.id + '"' + selected + '>' + subset.name.replace( /\+/g, ' ' ) + '</option>';
						}
					);

					// Destroy select2.
					if ( destroy ) {
						that.find( '.welaunch-typography-subsets' ).select2( 'destroy' );
					}

					// Inset new HTML.
					that.find( '.welaunch-typography-subsets' ).html( html ).select2( { width:'100%' } );

					that.find( '.welaunch-typography-subsets' ).parent().fadeIn( 'fast' );
					that.find( '.typography-family-backup' ).fadeIn( 'fast' );
				} else if ( true === typekit ) {
					$.each(
						details.variants,
						function( index, variant ) {
							index = null;
							if ( style === variant.id || 1 === welaunch.field_objects.typography.size( details.variants ) ) {
								selected = ' selected="selected"';
								style    = variant.id;
							} else {
								selected = '';
							}

							html += '<option value="' + variant.id + '"' + selected + '>' + variant.name.replace( /\+/g, ' ' ) + '</option>';
						}
					);

					// Destroy select2.
					that.find( '.welaunch-typography-style' ).select2( 'destroy' );

					// Instert new HTML.
					that.find( '.welaunch-typography-style' ).html( html ).select2();

					// Prettify things.
					that.find( '.welaunch-typography-subsets' ).parent().fadeOut( 'fast' );
					that.find( '.typography-family-backup' ).fadeOut( 'fast' );
				} else {
					if ( that.find( '.welaunch-typography-style' ) ) {
						$.each(
							default_font_weights,
							function( index, value ) {
								if ( style === index || 'normal' === index ) {
									selected = ' selected="selected"';
									that.find( '.typography-style select2-selection__rendered' ).text( value );
								} else {
									selected = '';
								}

								html += '<option value="' + index + '"' + selected + '>' + value.replace( '+', ' ' ) + '</option>';
							}
						);

						// Destory select2.
						if ( destroy ) {
							that.find( '.welaunch-typography-style' ).select2( 'destroy' );
						}

						// Insert new HTML.
						that.find( '.welaunch-typography-style' ).html( html ).select2();
					}
				}

				that.find( '.welaunch-typography-font-family' ).val( family );
			} else if ( $( selector ).hasClass( 'welaunch-typography-family-backup' ) && '' !== familyBackup ) {
				that.find( '.welaunch-typography-font-family-backup' ).val( familyBackup );
			} else {
				details = default_font_weights;
				if ( details ) {
					$.each(
						details,
						function( index, value ) {
							if ( style === index || 'normal' === index ) {
								selected = ' selected="selected"';
								that.find( '.typography-style select2-selection__rendered' ).text( value );
							} else {
								selected = '';
							}

							html += '<option value="' + index + '"' + selected + '>' + value.replace( '+', ' ' ) + '</option>';
						}
					);

					// Destory select2.
					if ( destroy ) {
						that.find( '.welaunch-typography-style' ).select2( 'destroy' );
					}

					// Insert new HTML.
					that.find( '.welaunch-typography-style' ).html( html ).select2();

					// Prettify things.
					that.find( '.welaunch-typography-subsets' ).parent().fadeOut( 'fast' );
					that.find( '.typography-family-backup' ).fadeOut( 'fast' );
				}
			}
		}

		if ( active ) {

			that.find( '.welaunch-typography-style' ).addClass( 'ignore-change' );

			// Check if the selected value exists. If not, empty it. Else, apply it.
			if ( 0 === that.find( 'select.welaunch-typography-style option[value=\'' + style + '\']' ).length ) {
				style = '';
				that.find( 'select.welaunch-typography-style' ).val( '' ).trigger( 'change' );
			} else if ( '400' === style ) {
				that.find( 'select.welaunch-typography-style' ).val( style ).trigger( 'change' );
			}

			that.find( '.welaunch-typography-style' ).removeClass( 'ignore-change' );

			// Handle empty subset select.
			if ( 0 === that.find( 'select.welaunch-typography-subsets option[value=\'' + script + '\']' ).length ) {
				script = '';

				that.find( '.welaunch-typography-style' ).addClass( 'ignore-change' );
				that.find( 'select.welaunch-typography-subsets' ).val( '' ).trigger( 'change' );
				that.find( 'input.typography-subsets' ).val( script );
				that.find( '.welaunch-typography-style' ).removeClass( 'ignore-change' );
			}
		}

		_linkclass = 'style_link_' + mainID;

		// Remove other elements crested in <head>.
		$( '.' + _linkclass ).remove();

		if ( null !== family && 'inherit' !== family && that.hasClass( 'typography-initialized' ) ) {

			// Replace spaces with "+" sign.
			the_font = family.replace( /\s+/g, '+' );

			if ( true === google ) {

				// Add reference to google font family.
				link = the_font;

				if ( style && '' !== style ) {
					link += ':' + style.replace( /\-/g, ' ' );
				}

				if ( script && '' !== script ) {
					link += '&subset=' + script;
				}

				if ( false === isSelecting ) {
					if ( 'undefined' !== typeof ( WebFont ) && WebFont ) {
						WebFont.load( { google: { families: [link] } } );
					}
				}

				that.find( '.welaunch-typography-google' ).val( true );
			} else {
				that.find( '.welaunch-typography-google' ).val( false );
			}
		}

		// Weight and italic.
		if ( style && - 1 !== style.indexOf( 'italic' ) ) {
			that.find( '.typography-preview' ).css( 'font-style', 'italic' );
			that.find( '.typography-font-style' ).val( 'italic' );
			style = style.replace( 'italic', '' );
		} else {
			that.find( '.typography-preview' ).css( 'font-style', 'normal' );
			that.find( '.typography-font-style' ).val( '' );
		}

		that.find( '.typography-font-weight' ).val( style );

		allowEmptyLineHeight = Boolean( that.find( '.welaunch-typography-height' ).data( 'allow-empty' ) );

		if ( ! allowEmptyLineHeight ) {
			if ( ! height ) {
				height = size;
			}
		}

		if ( '' === size || undefined === size ) {
			that.find( '.typography-font-size' ).val( '' );
		} else {
			that.find( '.typography-font-size' ).val( size + units );
		}

		if ( '' === height || undefined === height ) {
			that.find( '.typography-line-height' ).val( '' );
		} else {
			that.find( '.typography-line-height' ).val( height + units );
		}

		if ( '' === word || undefined === word ) {
			that.find( '.typography-word-spacing' ).val( '' );
		} else {
			that.find( '.typography-word-spacing' ).val( word + units );
		}

		if ( '' === letter || undefined === letter ) {
			that.find( '.typography-letter-spacing' ).val( '' );
		} else {
			that.find( '.typography-letter-spacing' ).val( letter + units );
		}

		if ( proLoaded ) {
			welaunch.field_objects.pro.typography.select( mainID );
		}

		// Show more preview stuff.
		if ( that.hasClass( 'typography-initialized' ) ) {
			isPreviewSize = that.find( '.typography-preview' ).data( 'preview-size' );

			if ( 0 === isPreviewSize ) {
				that.find( '.typography-preview' ).css( 'font-size', size + units );
			}

			that.find( '.typography-preview' ).css(
				{
					'font-weight': style,
					'text-align': align,
					'font-family': family + ', sans-serif'
				}
			);

			if ( 'none' === family && '' === family ) {

				// If selected is not a font remove style 'font-family' at preview box.
				that.find( '.typography-preview' ).css( 'font-family', 'inherit' );
			}

			that.find( '.typography-preview' ).css(
				{
					'line-height': height + units,
					'word-spacing': word + units,
					'letter-spacing': letter + units
				}
			);

			if ( color ) {
				that.find( '.typography-preview' ).css( 'color', color );
			}

			if ( proLoaded ) {
				welaunch.field_objects.typography.previewShadow( mainID );
			}

			that.find( '.typography-style select2-selection__rendered' ).text( that.find( '.welaunch-typography-style option:selected' ).text() );

			that.find( '.typography-script select2-selection__rendered' ).text( that.find( '.welaunch-typography-subsets option:selected' ).text() );

			if ( align ) {
				that.find( '.typography-preview' ).css( 'text-align', align );
			}

			if ( transform ) {
				that.find( '.typography-preview' ).css( 'text-transform', transform );
			}

			if ( fontVariant ) {
				that.find( '.typography-preview' ).css( 'font-variant', fontVariant );
			}

			if ( decoration ) {
				that.find( '.typography-preview' ).css( 'text-decoration', decoration );
			}
			that.find( '.typography-preview' ).slideDown();
		}

		// End preview stuff.
		// If not preview showing, then set preview to show.
		if ( ! that.hasClass( 'typography-initialized' ) ) {
			that.addClass( 'typography-initialized' );
		}

		isSelecting = false;

		if ( ! skipCheck ) {
			welaunch_change( selector );
		}
	};
})( jQuery );
