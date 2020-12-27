/*----------------------------------------------------------------------------*\
	MPC_PRESET PARAM
\*----------------------------------------------------------------------------*/
( function( $ ) {
	"use strict";

/* New preset */
	function new_preset( _name, $preset, $fields, _wp_nonce ) {
		set_ajax_state( $preset, true );

		$.post( ajaxurl, {
			action:    'mpc_new_typography_preset',
			name:      _name,
			values:    get_fields_values( $fields ),
			wp_nonce:  _wp_nonce
		}, function( response ) {
			if ( response != 'not set' ) {
				$presets
					.append( response.markup );

				$preset
					.val( response.id )
					.trigger( 'change' )
					.trigger( 'mpc.updated' );

				$body.trigger( 'mpc.font-added', [ response.id ] );
			} else {
				display_error( $preset );
			}

			set_ajax_state( $preset, false );
		}, 'json' );
	}

/* Load preset */
	function load_preset( $preset, $fields, $typography_wrap, $preset_name, _wp_nonce ) {
		if ( $preset.val() != '' ) {
			set_ajax_state( $preset, true );
			$preset.removeClass( 'mpc-empty' );

			$.post( ajaxurl, {
				action:   'mpc_load_typography_preset',
				id:       $preset.val(),
				wp_nonce: _wp_nonce
			}, function( response ) {
				if ( response != 'not set' ) {
					for ( var _name in response ) {
						update_single_field( $fields.filter( '[name="' + _name + '"]' ), response[ _name ] );
					}
				} else {
					display_error( $preset );
				}

				$typography_wrap.stop( true ).slideDown();

				$preset_name.val( $preset.find( ':selected' ).text() ).trigger( 'focus' );

				set_ajax_state( $preset, false );
			}, 'json' );
		} else {
			$preset.addClass( 'mpc-empty' );
		}
	}

	function update_single_field( $field, response_value ) {
		var _current_value = $field.val();

		$field.val( response_value );

		if ( response_value != _current_value ) {
			$field.trigger( 'change' ).trigger( 'mpc.change' );
		}
	}

/* Edit preset */
	function edit_preset( _name, _id, $preset, $fields, _wp_nonce ) {
		if ( $preset.val() != '' ) {
			if ( confirm( _mpc_lang.mpc_typography.save_confirm + '"' + $preset.find( ':selected' ).text() + '"' ) ) {
				set_ajax_state( $preset, true );

				$.post( ajaxurl, {
					action:   'mpc_edit_typography_preset',
					id:       _id,
					name:     _name,
					values:   get_fields_values( $fields ),
					wp_nonce: _wp_nonce
				}, function( response ) {
					if ( response != 'not set' ) {
						$presets
							.find( '.' + _id )
								.text( _name );

						$body.trigger( 'mpc.font-edited', [ _id ] );
					} else {
						display_error( $preset );
					}

					set_ajax_state( $preset, false );
				} );
			}
		} else {
			$preset.addClass( 'mpc-empty' );
		}
	}

/* Delete preset */
	function delete_preset( $preset, _wp_nonce ) {
		if ( $preset.val() != '' ) {
			if ( confirm( _mpc_lang.mpc_typography.delete_confirm + '"' + $preset.find( ':selected' ).text() + '"' ) ) {
				set_ajax_state( $preset, true );

				var _id = $preset.val();

				$.post( ajaxurl, {
					action:    'mpc_delete_typography_preset',
					id:        _id,
					wp_nonce:  _wp_nonce
				}, function( response ) {
					if ( response != 'not set' ) {
						$presets
							.find( '.' + _id )
								.remove();

						$preset.val( '' );
					} else {
						display_error( $preset );
					}

					set_ajax_state( $preset, false );
				} );
			}
		} else {
			$preset.addClass( 'mpc-empty' );
		}
	}

/* Typography form - load */
	function load_typography_form( $preset, $caller, _wp_nonce ) {
		set_ajax_state( $preset, true );

		$.post( ajaxurl, {
			action:    'mpc_get_typography_form',
			wp_nonce:  _wp_nonce
		}, function( response ) {
			if ( response != 'not set' ) {
				$( 'body' ).append( response );

				$global_form = $( '#mpc_typography_form' );

				$presets.trigger( 'mpc.form-loaded' );
				$caller.trigger( 'click' );
			} else {
				display_error( $preset );
			}

			set_ajax_state( $preset, false );
		} );
	}

/* Typography form - setup */
	function set_up_typography_form( $preset ) {
		var $typography_wrap = $preset.siblings( '.mpc-typography' );
		$typography_wrap.append( $global_form.clone() );

		var $form           = $typography_wrap.find( '.mpc-typography-form' ),
			$fields         = $form.find( '.mpc-typography-value' ),
			$font_family    = $fields.filter( '[name="font-family"]' ),
			$style          = $fields.filter( '[name="style"]' ),
			$subset         = $fields.filter( '[name="subset"]' ),
			$font_weight    = $fields.filter( '[name="font-weight"]' ),
			$font_style     = $fields.filter( '[name="font-style"]' ),
			$text_align     = $fields.filter( '[name="text-align"]' ),
			$text_transform = $fields.filter( '[name="text-transform"]' ),
			$font_size      = $fields.filter( '[name="font-size"]' ),
			$line_height    = $fields.filter( '[name="line-height"]' ),
			$color          = $fields.filter( '[name="color"]' ),
			$url            = $fields.filter( '[name="url"]' ),
			$preview_wrap   = $form.find( '.mpc-preview-wrap' ),
			$preview        = $preview_wrap.find( '.mpc-preview' ),
			_refresh_timer,
			_styles = {
				'100':     'Ultra-Light (100)',
				'200':     'Light (200)',
				'300':     'Book (300)',
				'regular': 'Normal (400)',
				'500':     'Medium (500)',
				'600':     'Semi-Bold (600)',
				'700':     'Bold (700)',
				'800':     'Extra-Bold (800)',
				'900':     'Ultra-Bold (900)',

				'100italic': 'Ultra-Light Italic (100)',
				'200italic': 'Light Italic (200)',
				'300italic': 'Book Italic (300)',
				'italic':    'Normal Italic (400)',
				'500italic': 'Medium Italic (500)',
				'600italic': 'Semi-Bold Italic (600)',
				'700italic': 'Bold Italic (700)',
				'800italic': 'Extra-Bold Italic (800)',
				'900italic': 'Ultra-Bold Italic (900)'
			},
			_subsets = {
				'arabic':       'Arabic',
				'cyrillic':     'Cyrillic',
				'cyrillic-ext': 'Cyrillic Extended',
				'devanagari':   'Devanagari',
				'greek':        'Greek',
				'greek-ext':    'Greek Extended',
				'hebrew':       'Hebrew',
				'khmer':        'Khmer',
				'latin':        'Latin',
				'latin-ext':    'Latin Extended',
				'telugu':       'Telugu',
				'vietnamese':   'Vietnamese'
			};

		// Font family select
		$font_family.MPCselect2( {
			data:        _mpc_fonts,
			width:       '100%',
			allowClear:  true,
			placeholder: ' '
		} ).on( 'change', function( event, b, c ) {
			if ( $font_family.val() == '' ) {
				$style.html( '<option value=""></option>' ).trigger( 'change' );
				$subset.html( '<option value=""></option>' );
				$url.val( '' );

				$preview_wrap.fadeOut();
			} else {
				var _data = $font_family.MPCselect2( 'data' ),
					_index,
					_selected;

				if ( _data === null ){
					$style.html( '<option value=""></option>' ).trigger( 'change' );
					$subset.html( '<option value=""></option>' );
					$preview_wrap.stop( true ).fadeIn();

					return;
				}

				if ( _data.id == '' ) {
					return;
				}

				if ( _data.url == undefined ){
					$url.val( '' );
				} else {
					$url.val( _data.url );
				}

				$subset.html( '' );
				if ( _data.subsets == undefined ) {
					$subset.closest( '.mpc-form-element' ).css( 'display', 'none' );

					$subset.append( '<option value="" selected="selected"></option>' );
				} else {
					$subset.closest( '.mpc-form-element' ).css( 'display', '' );

					for ( _index = 0; _index < _data.subsets.length; _index++ ) {
						_selected = _data.subsets[ _index ] == 'latin' ? 'selected="selected"' : '';
						$subset.append( '<option value="' + _data.subsets[ _index ] + '" ' + _selected + '>' + _subsets[ _data.subsets[ _index ] ] + '</option>' );
					}
				}

				$style.html( '' );
				if ( _data.variants == undefined ) {
					$style.closest( '.mpc-form-element' ).css( 'display', 'none' );

					$style.append( '<option value="" selected="selected"></option>' );
				} else {
					$style.closest( '.mpc-form-element' ).css( 'display', '' );

					for ( _index = 0; _index < _data.variants.length; _index++ ) {
						_selected = _data.variants[ _index ] == 'regular' ? 'selected="selected"' : '';
						$style.append( '<option value="' + _data.variants[ _index ] + '" ' + _selected + '>' + _styles[ _data.variants[ _index ] ] + '</option>' );
					}
				}

				$style.trigger( 'change' );

				$preview_wrap.stop( true ).fadeIn();
			}
		} );

		// Font style select
		$style.on( 'change', function() {
			var _style = $style.val();

			if ( _style == null ) {
				return;
			}

			if ( _style.indexOf( 'italic' ) != -1 ) {
				$font_style.val( 'italic' );
			} else {
				$font_style.val( 'normal' );
			}

			_style = _style.replace( 'italic', '' );
			$font_weight.val( _style == '' || _style == 'regular' ? '400' : _style );
		} );

		// Font size input
		$font_size.on( 'blur', function() {
			var _value = parseInt( $font_size.val() );

			if ( _value != '' ) {
				$font_size.val( isNaN( _value ) ? '' : _value );
			}
		} );

		// Line height input
		$line_height.on( 'blur', function() {
			var _value = parseFloat( $line_height.val() );

			if ( _value != '' ) {
				$line_height.val( isNaN( _value ) ? '' : _value );
			}
		} );

		// Color picker
		$color.wpColorPicker( {
			defaultColor: $color.val(),
			hide:         true,
			palettes:     true,
			change: function() {
				$color.trigger( 'mpc.change' );
			}
		} );

		// Refresh preview delayed event
		$fields.on( 'change keyup mpc.change', function() {
			clearTimeout( _refresh_timer );

			_refresh_timer = setTimeout( function() {
				$preview.trigger( 'mpc.refresh' );
			}, 250 );
		} );

		// Refresh preview
		$preview.on( 'mpc.refresh', function() {
			var _data        = $font_family.MPCselect2( 'data' ),
				_font_family = '';

			if ( $font_family.val() != '' ) {
				if ( _data !== null && _data.subsets != undefined && $url.val() === '' ) {
					var _link = '<link href="//fonts.googleapis.com/css?family=';
					_link += $font_family.val().replace( / /g, '+' );
					_link += ':' + $style.val();
					_link += '" rel="stylesheet" type="text/css">';

					$( 'head' ).append( _link );

					_font_family = '"' + $font_family.val() + '"';
				} else if ( _data !== null && $url.val() !== '' ) {
					var _link = '<link href="';
					_link += $url.val();
					_link += '" rel="stylesheet" type="text/css">';

					$( 'head' ).append( _link );

					_font_family = '"' + $font_family.val() + '"';
					$subset.val( '' );
				} else {
					$subset.val( '' );
					$url.val( '' );

					_font_family = $font_family.val();
				}
			}

			var _style = '';
			_style += $font_family.val() != '' ? 'font-family: ' + _font_family + ';' : '';
			_style += $font_style.val() != '' ? 'font-style: ' + $font_style.val() + ';' : '';
			_style += $font_weight.val() != '' ? 'font-weight: ' + $font_weight.val() + ';' : '';
			_style += $text_transform.val() != '' ? 'text-transform: ' + $text_transform.val() + ';' : '';
			_style += $text_align.val() != '' ? 'text-align: ' + $text_align.val() + ';' : '';
			_style += $font_size.val() != '' ? 'font-size: ' + $font_size.val() + 'px;' : '';
			_style += $line_height.val() != '' ? 'line-height: ' + $line_height.val() + ';' : '';
			_style += $color.val() != '' ? 'color: ' + $color.val() + ';' : '';

			$preview.attr( 'style', _style );
		} );
	}

/* Helpers */
	function get_fields_values( $fields ) {
		var $field,
			_values = {};

		$fields.each( function() {
			$field = $( this );

			_values[ $field.attr( 'name' ) ] = $field.val();
		} );

		return _values;
	}

	function set_ajax_state( $preset, state ) {
		if ( state ) {
			$preset.siblings( '.mpc-ajax' ).addClass( 'mpc-active' );
			$preset.siblings( '.mpc-init-overlay' ).fadeIn();
		} else {
			$preset.siblings( '.mpc-ajax' ).removeClass( 'mpc-active' );
			$preset.siblings( '.mpc-init-overlay' ).fadeOut();
		}
	}

	function display_error( $preset ) {
		$preset.siblings( '.mpc-error' ).stop( true ).fadeIn().delay( 3000 ).fadeOut();
	}

/* Typography form */
	var $global_form = $( '#mpc_typography_form' );

/* Preset field */
	var $popup        = $( '#vc_ui-panel-edit-element' ),
		$presets      = $( '.mpc-typography-select' ),
		$body         = $( 'body' ),
		_presets_list = [];

/* Get typography presets */
	$popup.one( 'mpc.render', function() {
		if ( $presets.length == 0 ) {
			return;
		}

		$.post( ajaxurl, {
			action:   'mpc_get_typography_presets',
			wp_nonce: $presets.attr( 'data-wp_nonce' )
		}, function( response ) {
			if ( response != 'not set' ) {
				$presets.append( response ).trigger( 'mpc.loaded' );

				$presets.each( function() {
					var $preset = $( this );

					$preset.val( $preset.attr( 'data-selected' ) ).trigger( 'change' );
				} );
			} else {
				display_error( $presets );
			}

			$presets.siblings( '.mpc-init-overlay' ).fadeOut();
			$presets.siblings( '.mpc-ajax' ).removeClass( 'mpc-active' );
		} );
	} );

/* Update presets names */
	$presets.on( 'mpc.updated', function() {
		var $preset = $( this );

		_presets_list = [];
		$preset.children().each( function() {
			_presets_list.push( $( this ).val() );
		} );
	} ).on( 'mpc.loaded', function() {
		_presets_list = [];
		$presets.first().children().each( function() {
			_presets_list.push( $( this ).val() );
		} );
	} );

/* Set up all presets fields */
	$presets.each( function() {
		var $preset          = $( this ),
			$preset_wrap     = $preset.parent(),
			$new_preset      = $preset.siblings( '.mpc-new.mpc-vc-button' ),
			$edit_preset     = $preset.siblings( '.mpc-edit.mpc-vc-button' ),
			$delete_preset   = $preset.siblings( '.mpc-delete.mpc-vc-button' ),
			$typography_wrap = $preset.siblings( '.mpc-typography' ),
			$buttons         = $preset.siblings( '.mpc-vc-button:not(.mpc-new)' ),
			$dynamic_buttons = $preset.siblings( '.mpc-buttons' ),
			$preset_name     = $typography_wrap.find( '.mpc-typography-name' ),
			$accept          = $typography_wrap.children( '.mpc-accept.mpc-vc-button' ),
			$cancel          = $typography_wrap.children( '.mpc-cancel.mpc-vc-button' ),
			$fields          = $typography_wrap.find( '.mpc-typography-value' ).filter( 'input, select' ),
			_is_form_loaded  = $global_form.length != 0,
			_is_form_added   = false,
			_action          = '',
			_wp_nonce        = $preset.attr( 'data-wp_nonce' ),
			_current         = '';

		// Presets select
		$preset.on( 'change', function() {
			_current = $preset.val();

			$preset.removeClass( 'mpc-empty' );
			$preset.attr( 'data-option', $preset.val() );

			$cancel.trigger( 'click' );
			if ( $preset.val() == '' ) {
				$buttons.addClass( 'mpc-hidden' );
			} else {
				$buttons.removeClass( 'mpc-hidden' );
			}
		} ).on( 'mpc.form-loaded', function() {
			_is_form_loaded = true;
		} );

		// Preset name
		$preset_name.on( 'keyup', function() {
			$preset_name.removeClass( 'mpc-empty' );
		} );

		// New preset button
		$new_preset.on( 'click', function( event ) {
			if ( _is_form_loaded ) {
				if ( ! _is_form_added ) {
					set_up_typography_form( $preset );

					$preset_name = $typography_wrap.find( '.mpc-typography-name' );
					$accept      = $typography_wrap.find( '.mpc-accept.mpc-vc-button' );
					$cancel      = $typography_wrap.find( '.mpc-cancel.mpc-vc-button' );
					$fields      = $typography_wrap.find( '.mpc-typography-value' ).filter( 'input, select' );

					_is_form_added = true;
				}

				if ( _action == 'edit' ) {
					$preset_name.val( '' );
					$fields.val( '' ).trigger( 'change' );
				}

				_action = 'new';

				$typography_wrap
					.removeClass( 'mpc-state--edit' )
					.addClass( 'mpc-state--new' )
					.stop( true )
					.slideDown();

				$dynamic_buttons
					.removeClass( 'mpc-state--edit mpc-hidden' )
					.addClass( 'mpc-state--new' );

				$preset_name.trigger( 'focus' );
			} else {
				load_typography_form( $preset, $new_preset, _wp_nonce );
			}

			event.preventDefault();
		} );

		// Edit preset button
		$edit_preset.on( 'click', function( event ) {
			if ( _is_form_loaded ) {
				if ( ! _is_form_added ) {
					set_up_typography_form( $preset );

					$preset_name = $typography_wrap.find( '.mpc-typography-name' );
					$accept      = $typography_wrap.find( '.mpc-accept.mpc-vc-button' );
					$cancel      = $typography_wrap.find( '.mpc-cancel.mpc-vc-button' );
					$fields      = $typography_wrap.find( '.mpc-typography-value' ).filter( 'input, select' );

					_is_form_added = true;
				}

				if ( _action == 'new' ) {
					$preset_name.val( '' );
					$fields.val( '' ).trigger( 'change' );
				}

				_action = 'edit';

				$typography_wrap
					.removeClass( 'mpc-state--new' )
					.addClass( 'mpc-state--edit' );

				$dynamic_buttons
					.removeClass( 'mpc-state--new mpc-hidden' )
					.addClass( 'mpc-state--edit' );

				load_preset( $preset, $fields, $typography_wrap, $preset_name, _wp_nonce );
			} else {
				load_typography_form( $preset, $edit_preset, _wp_nonce );
			}

			event.preventDefault();
		} );

		// Delete preset button
		$delete_preset.on( 'click', function( event ) {
			delete_preset( $preset, _wp_nonce );

			event.preventDefault();
		} );

		// Accept action button
		$preset_wrap.on( 'click', '.mpc-accept.mpc-vc-button', function( event ) {
			if ( _action == '' ) {
				return;
			}

			var _name = $preset_name.val().trim();

			if ( _action == 'new' ) {
				if ( _name != '' && _presets_list.indexOf( _name ) == -1 ) {
					new_preset( _name, $preset, $fields, _wp_nonce );

					$cancel.trigger( 'click' );
				} else {
					$preset_name.addClass( 'mpc-empty' );
				}
			} else if ( _action == 'edit' ) {
				if ( _name != '' && ( _name == _current || _presets_list.indexOf( _name ) == -1 ) ) {
					edit_preset( _name, _current, $preset, $fields, _wp_nonce );

					$cancel.trigger( 'click' );
				} else {
					$preset_name.addClass( 'mpc-empty' );
				}
			}

			$dynamic_buttons.addClass( 'mpc-hidden' );

			event.preventDefault();
		} );

		// Cancel action button
		$preset_wrap.on( 'click', '.mpc-cancel.mpc-vc-button', function( event ) {
			$typography_wrap.slideUp();
			$preset_name.val( '' );
			$fields.val( '' ).trigger( 'change' );
			_action = '';

			$dynamic_buttons.addClass( 'mpc-hidden' );

			event.preventDefault();
		} );
	} );

} )( jQuery );
