/*----------------------------------------------------------------------------*\
	MPC_PRESET PARAM
\*----------------------------------------------------------------------------*/
( function( $ ) {
	"use strict";

/* New preset */
	function new_preset( _name, $preset, $fields, _shortcode, _prefix, _wp_nonce ) {
		var _values = get_fields_values( $fields.filter( ':not(.mpc-ignored-field)' ), _prefix );

		if ( $.isEmptyObject( _values ) ) {
			display_warning( $preset );

			return;
		}

		set_ajax_state( $preset, true );

		$.post( ajaxurl, {
			action:    'mpc_new_shortcode_preset',
			shortcode: _shortcode,
			name:      _name,
			values:    _values,
			wp_nonce:  _wp_nonce
		}, function( response ) {
			if ( response.error === undefined ) {
				if ( ! $preset.find( '.mpc-presets--user' ).length ) {
					$preset.find( '.mpc-base-option' )
						.after( '<optgroup class="mpc-presets--user" label="' + $previews_box_users.text() + '">' )
						.after( $preset.find( '.default' ) );
				}

				$preset.find( '.mpc-presets--user' ).append( response.markup );

				$preset
					.val( response.id )
					.trigger( 'mpc.update' );

				if ( _shortcode == 'mpc_navigation' ) {
					$body.trigger( 'mpc.navigation-added', [ _name ] );
				} else if ( _shortcode == 'mpc_pagination' ) {
					$body.trigger( 'mpc.pagination-added', [ _name ] );
				}

				_listen_for_changes = true;
			} else {
				display_error( $preset );
			}

			set_ajax_state( $preset, false );
		}, 'json' );
	}

/* Load preset */
	function load_preset( $preset, $fields, _shortcode, _prefix, _wp_nonce ) {
		if ( $preset.val() != '' ) {
			set_ajax_state( $preset, true );

			$preset.removeClass( 'mpc-empty' );

			$.post( ajaxurl, {
				action:    'mpc_load_shortcode_preset',
				shortcode: _shortcode,
				id:        $preset.val(),
				wp_nonce:  _wp_nonce
			}, function( response ) {
				if ( response.error === undefined ) {
					update_all_fields( $fields.filter( ':not(.mpc-ignored-field)' ), response, _prefix );
				} else {
					display_error( $preset );
				}

				set_ajax_state( $preset, false );

				if ( $preset.attr( 'data-sub_type' ) == 'navigation' || $preset.attr( 'data-sub_type' ) == 'pagination' ) {
					setTimeout( function() {
						_listen_for_changes = true;
					}, 1000 );
				}

				_mpc_vars.$body.trigger( 'mpc.preset_loaded' );
			}, 'json' );
		} else {
			$preset.addClass( 'mpc-empty' );
		}
	}

	function update_all_fields( $fields, values, _prefix ) {
		var _name           = '',
			_field_settings = '',
			_value          = '',
			_default_value  = '';

		$fields.each( function() {
			var $field  = $( this ),
				$parent = $field.closest( '[data-vc-ui-element="panel-shortcode-param"]' );

			_name = $field.attr( 'name' );

			if ( _prefix != '' ) {
				_name = _name.replace( _prefix, '' );
			}

			if ( $parent.is( '.wpb_el_type_checkbox, .wpb_el_type_textarea_raw_html, .wpb_el_type_attach_image' ) ) {
				$parent.data( 'vcInitParam', true );
			}

			if ( values[ _name ] === undefined ) {
				_field_settings = $field.closest( '.vc_shortcode-param' ).data( 'param_settings' );

				if ( _field_settings == undefined ) {
					return;
				}

				if ( $field.is( '[type="checkbox"]' ) ) {
					_value = $field.is( ':checked' );
				} else if ( $field.is( '.vc_textarea_html_content' ) ) {
					if ( typeof tinyMCE !== 'undefined' && tinyMCE.get( 'wpb_tinymce_content' ) ) {
						_value = tinyMCE.get( 'wpb_tinymce_content' ).getContent();
					} else {
						_value = $field.siblings( '.wp-editor-wrap' ).find( '.wpb-textarea' ).val();
					}
				} else {
					_value = $field.val();
				}

				if ( _field_settings.std !== undefined ) {
					_default_value = _field_settings.std;
				} else if ( _field_settings.value !== undefined ) {
					_default_value = _field_settings.value
				} else {
					if ( $popup.is( '[data-vc-shortcode^="vc_column"], [data-vc-shortcode^="vc_row"]' ) ) {
						_default_value = '';
					} else {
						return;
					}
				}

				if ( _default_value == _value ) {
					return;
				}

				if ( $field.is( '[type="checkbox"]' ) ) {
					$field.prop( 'checked', _value );
				} else if ( $field.is( '.vc_textarea_html_content' ) ) {
					if ( typeof tinyMCE !== 'undefined' && tinyMCE.get( 'wpb_tinymce_content' ) ) {
						tinyMCE.get( 'wpb_tinymce_content' ).setContent( _default_value );
					} else {
						$field.siblings( '.wp-editor-wrap' ).find( '.wpb-textarea' ).val( _default_value );
					}
				} else if ( $field.is( '.attach_images' ) || $field.is( '.vc_link_field' ) || ( $field.is( '.attach_image' ) && $popup.is( '[data-vc-shortcode="mpc_image"]' ) ) || $field.is( '.autocomplete_field' ) ) {
					// Leave as is...
				} else if ( $parent.is( '[data-param_type="css_editor"]' ) ) {
					clear_css_editor( $parent );
				} else if ( $parent.is( '[data-param_type="column_offset"]' ) ) {
					clear_column_offset( $parent );
				} else {
					if ( $field.is( 'textarea' ) ) {
						$field.html( _default_value );
					}

					$field.val( _default_value );
				}

				$field.trigger( 'change' ).trigger( 'mpc.change' );
			} else {
				update_single_field( $field, values[ _name ] );
			}
		} );
	}

	function update_single_field( $field, response_value ) {
		var _current_value = $field.is( '[type="checkbox"]' ) ? $field.is( ':checked' ) : $field.val();

		if ( typeof response_value == 'object' ) {
			if ( ! response_value.length ) {
				return;
			}

			setTimeout( function() {
				var $list  = $field.siblings( '.vc_param_group-list' ),
					$items = $list.find( '> .vc_param' ),
					_name  = $field.attr( 'name' ),
					_index = 0,
					_field;

				if ( $items.length > response_value.length ) {
					var $part = $items.slice( 0, response_value.length );

					$items.not( $part ).remove();
				} else if ( $items.length < response_value.length ) {
					var $new = $list.find( '> .vc_param_group-add_content' );

					for ( _index = 0; _index < response_value.length - $items.length; _index++ ) {
						$new.trigger( 'click' );
					}
				}

				$items = $list.find( '> .vc_param' );

				for ( _index = 0; _index < response_value.length; _index++ ) {
					for ( _field in response_value[ _index ] ) {
						update_single_field( $items.eq( _index ).find( '.' + _name + '_' + _field ), response_value[ _index ][ _field ] );
					}
				}
			}, 10 );
		} else if ( $field.is( '[type="checkbox"]' ) ) {
			response_value = response_value == 'true';
			$field.prop( 'checked', response_value );
		} else if ( $field.is( '.vc_textarea_html_content' ) ) {
			if ( typeof tinyMCE !== 'undefined' && tinyMCE.get( 'wpb_tinymce_content' ) ) {
				tinyMCE.get( 'wpb_tinymce_content' ).setContent( response_value );
			} else {
				$field.siblings( '.wp-editor-wrap' ).find( '.wpb-textarea' ).val( response_value );
			}
		} else if ( $field.is( '.vc_link_field' ) ) {
			var _values = response_value.split( '|' ),
				_link   = {};

			for ( var _value in _values ) {
				_value = _values[ _value ].split( ':' );
				_link[ _value[ 0 ] ] = decodeURIComponent( _value[ 1 ] );
			}

			if ( ! _link[ 'target' ] ) {
				_link[ 'target' ] = '';
			}

			$field.siblings( '.title-label' ).text( _link[ 'title' ] );
			$field.siblings( '.url-label' ).text( _link[ 'url' ] + _link[ 'target' ] );

			$field.val( response_value );
		} else if ( $field.is( '.attach_images' ) || ( $field.is( '.attach_image' ) && $popup.is( '[data-vc-shortcode="mpc_image"]' ) ) || $field.is( '.autocomplete_field' ) ) {
			// Leave as is...
		} else if ( $field.is( 'select' ) ) {
			if ( $field.find( 'option[value="' + response_value + '"]' ).length > 0 ) {
				$field.val( response_value );
			} else {
				$field[ 0 ].selectedIndex = 0;
			}
		} else if ( $field.parents( '.vc_shortcode-param' ).is( '.mpc-validate-int' ) ) {
			response_value = response_value != '' ? parseInt( response_value ) : '';
			$field.val( response_value );
		} else if ( $field.parents( '.vc_shortcode-param' ).is( '.wpb_el_type_attach_image' ) ) {
			if ( response_value != '' ) {
				$.post( ajaxurl, {
					action: 'mpc_get_image_url',
					id:     response_value
				}, function( response ) {
					$field.siblings( '.gallery_widget_attached_images' ).find( '.gallery_widget_attached_images_list' ).html( response );
				} );
			} else {
				$field.siblings( '.gallery_widget_attached_images' ).find( '.icon-remove' ).trigger( 'click' );
			}

			$field.val( response_value );
		} else if ( _vc_shortcode && $field.is( '[name="css"]' ) ) {
			var $css_param = $field.closest( '[data-vc-ui-element="panel-shortcode-param"]' );

			clear_css_editor( $css_param );

			$field.data( 'vcFieldManager' ).parse( response_value );
			$field.val( $field.data( 'vcFieldManager' ).save() );
			$css_param.find( 'input.wp-color-picker' ).trigger( 'change' );
		} else if ( _vc_shortcode && $field.is( '[name="offset"]' ) ) {
			var $offset_param = $field.closest( '[data-vc-ui-element="panel-shortcode-param"]' );

			clear_column_offset( $offset_param );

			response_value.replace( /\s+/g, ' ' ).trim().split( ' ' ).forEach( function( elem ) {
				if ( /vc_hidden/i.test( elem ) ) {
					$offset_param.find( 'input[name="' + elem + '"]' ).prop( 'checked', true );
				} else {
					$offset_param.find( 'option[value="' + elem + '"]' ).closest( 'select' ).val( elem );
				}
			} );

			$field.val( $field.data( 'vcColumnOffset' ).save().join( ' ' ) );
		} else {
			if ( $field.is( 'textarea' ) ) {
				$field.html( response_value );
			}

			$field.val( response_value );
		}

		if ( response_value != _current_value ) {
			$field.trigger( 'change' ).trigger( 'mpc.change' );
		}
	}

	function clear_css_editor( $param ) {
		$param.find( 'input[type="checkbox"]:checked, .vc_icon-remove' ).trigger( 'click' );
		$param.find( 'input:not([type="checkbox"]):not(.wp-color-picker), select' ).val( '' );
		$param.find( 'input.wp-color-picker' )
			.val( '#' + ( 16777215 * Math.random() >> 0 ).toString( 16 ) ).trigger( 'change' )
			.val( '' ).trigger( 'change' );
	}

	function clear_column_offset( $param ) {
		$param.find( 'input:not([type="checkbox"]), select' ).val( '' );
		$param.find( 'input[type="checkbox"]' ).prop( 'checked', false );
	}

/* Save preset */
	function save_preset( $preset, $fields, _shortcode, _prefix, _wp_nonce ) {
		if ( $preset.val() != '' ) {
			if ( _vc_shortcode || confirm( _mpc_lang.mpc_preset.save_confirm + '"' + $preset.find( ':selected' ).text() + '"' ) ) {
				var _values = get_fields_values( $fields.filter( ':not(.mpc-ignored-field)' ), _prefix );

				if ( $.isEmptyObject( _values ) ) {
					display_warning( $preset );

					return;
				}

				set_ajax_state( $preset, true );

				var _id = $preset.val();

				$.post( ajaxurl, {
					action:    'mpc_save_shortcode_preset',
					shortcode: _shortcode,
					id:        _id,
					values:    _values,
					name:      $preset.siblings( '.mpc-preset-dynamic-name' ).val(),
					wp_nonce:  _wp_nonce
				}, function( response ) {
					if ( response == 'not set' ) {
						display_error( $preset );
					}

					set_ajax_state( $preset, false );

					if ( _shortcode == 'mpc_navigation' || _shortcode == 'mpc_pagination' ) {
						if ( _shortcode == 'mpc_navigation' ) {
							$body.trigger( 'mpc.navigation-edited', [ _id ] );
						} else {
							$body.trigger( 'mpc.pagination-edited', [ _id ] );
						}

						$preset.siblings( '.mpc-vc-buttons .mpc-save' ).addClass( 'mpc-hidden' );
					}
				} );

				$dynamic_save.remove();
			}
		} else {
			$preset.addClass( 'mpc-empty' );
		}
	}

/* Rename preset */
	function rename_preset( _name, _id, $preset, _shortcode, _wp_nonce ) {
		set_ajax_state( $preset, true );

		$.post( ajaxurl, {
			action:    'mpc_rename_shortcode_preset',
			shortcode: _shortcode,
			id:        _id,
			name:      _name,
			wp_nonce:  _wp_nonce
		}, function( response ) {
			if ( response != 'not set' ) {
				$preset
					.find( ':selected' )
						.text( _name );

				if ( _shortcode == 'mpc_navigation' ) {
					$body.trigger( 'mpc.navigation-edited', [ _name ] );
				} else if ( _shortcode == 'mpc_pagination' ) {
					$body.trigger( 'mpc.pagination-edited', [ _name ] );
				}
			} else {
				display_error( $preset );
			}

			set_ajax_state( $preset, false );
		} );
	}

/* Delete preset */
	function delete_preset( $preset, _shortcode, _wp_nonce ) {
		if ( $preset.val() != '' ) {
			if ( _vc_shortcode || confirm( _mpc_lang.mpc_preset.delete_confirm + '"' + $preset.find( ':selected' ).text() + '"' ) ) {
				set_ajax_state( $preset, true );

				$.post( ajaxurl, {
					action:    'mpc_delete_shortcode_preset',
					shortcode: _shortcode,
					id:        $preset.val(),
					wp_nonce:  _wp_nonce
				}, function( response ) {
					if ( response != 'not set' ) {
						$preset
							.find( ':selected' )
								.remove()
								.end()
							.val( '' )
							.trigger( 'mpc.update' );
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

/* Clear preset */
	function clear_preset( $preset, $fields, _auto ) {
		if ( _auto || confirm( _mpc_lang.mpc_preset.clear_confirm ) ) {
			set_ajax_state( $preset, true );

			var $field,
				_settings;

			$fields.each( function() {
				$field = $( this );
				_settings = $field.parents( '.vc_shortcode-param' ).data( 'param_settings' );

				if ( typeof _settings.std != 'undefined' ) {
					if ( $field.is( '[type="checkbox"]' ) ) {
						$field.prop( 'checked', _settings.std );
					} else {
						$field.val( _settings.std );
					}

					$field.trigger( 'change' ).trigger( 'mpc.change' );
				} else if ( typeof _settings.value != 'undefined' ) {
					if ( $field.is( '[type="checkbox"]' ) ) {
						$field.prop( 'checked', false );
					} else {
						$field.val( _settings.value );
					}

					$field.trigger( 'change' ).trigger( 'mpc.change' );
				}
			} );

			set_ajax_state( $preset, false );
		}
	}

/* Helpers */
	function get_fields_values( $fields, _prefix ) {
		var $field,
			_name,
			_value,
			_field_settings,
			_default_value,
			_values = {};

		$fields.each( function() {
			$field = $( this );

			if ( $field.is( '[type="checkbox"]' ) ) {
				_value = $field.is( ':checked' );
			} else if ( $field.is( '.vc_textarea_html_content' ) ) {
				if ( typeof tinyMCE !== 'undefined' && tinyMCE.get( 'wpb_tinymce_content' ) ) {
					_value = tinyMCE.get( 'wpb_tinymce_content' ).getContent();
				} else {
					_value = $field.siblings( '.wp-editor-wrap' ).find( '.wpb-textarea' ).val();
				}
			} else if ( $field.is( '.attach_images, .autocomplete_field' ) ) {
				_value = '';
			} else if ( _vc_shortcode && $field.is( '[name="css"]' ) ) {
				_value = $field.data( 'vcFieldManager' ).save();
			} else if ( _vc_shortcode && $field.is( '[name="offset"]' ) ) {
				_value = $field.data( 'vcColumnOffset' ).save().join( ' ' );
			} else {
				_value = $field.val();
			}

			_field_settings = $field.closest( '.vc_shortcode-param' ).data( 'param_settings' );

			if ( _field_settings == undefined ) {
				return;
			}

			if ( _field_settings.std !== undefined ) {
				_default_value = _field_settings.std;
			} else if ( _field_settings.value !== undefined ) {
				_default_value = _field_settings.value
			} else {
				_default_value = '';
			}

			if ( _default_value == _value ) {
				return;
			}

			if ( _prefix != '' ) {
				_name = $field.attr( 'name' ).replace( _prefix, '' );
			} else {
				_name = $field.attr( 'name' );
			}

			_values[ _name ] = _value;
		} );

		return _values;
	}

	function set_ajax_state( $preset, state ) {
		if ( state ) {
			$preset.siblings( '.mpc-ajax' ).addClass( 'mpc-active' );
			$ajax_overlay.fadeIn();
		} else {
			$preset.siblings( '.mpc-ajax' ).removeClass( 'mpc-active' );
			$ajax_overlay.fadeOut();
		}
	}

	function display_error( $preset ) {
		$preset.siblings( '.mpc-error' ).stop( true ).fadeIn().delay( 3000 ).fadeOut();
	}
	function display_warning( $preset ) {
		$preset.siblings( '.mpc-warning' ).stop( true ).fadeIn().delay( 3000 ).fadeOut();
	}

	function toggle_sub_fields( $sub_fields, _sub_type, _is_sub_fields_visible, _state ) {
		if ( _sub_type == 'navigation' || _sub_type == 'pagination' ) {
			if ( ! _is_sub_fields_visible && _state ) {
				$sub_fields.stop( true ).slideDown();
			} else if ( _is_sub_fields_visible && ! _state ) {
				$sub_fields.slideUp();
			}
		}

		return _state;
	}

	function toggle_preset_name( $name_wrap, _is_preset_name_visible, _state ) {
		if ( ! _is_preset_name_visible && _state ) {
			$name_wrap.stop( true ).slideDown();
		} else if ( _is_preset_name_visible && ! _state ) {
			$name_wrap.slideUp();
		}

		return _state;
	}

/* Ajax overlay */
	var $ajax_overlay = $( '.mpc-ajax-overlay' );
	if ( $ajax_overlay.length == 0 ) {
		$ajax_overlay = $( '<div class="mpc-ajax-overlay" />' );
		$ajax_overlay.appendTo( '#vc_ui-panel-edit-element .vc_ui-panel-content-container' );
	}

/* Preset field */
	var $popup                = $( '#vc_ui-panel-edit-element' ),
		$save_popup           = $popup.find( '.vc_ui-button[data-vc-ui-element="button-save"]' ),
		$presets              = $( '.mpc-preset-select' ),
		$body                 = $( 'body' ),
		$previews_box         = $( '#mpc_presets_previews' ),
		$previews_box_users   = $previews_box.find( '.mpc-presets-section.mpc-presets--user' ),
		$previews_box_premade = $previews_box.find( '.mpc-presets-section.mpc-presets--premade' ),
		$dynamic_save         = $( '<div class="mpc-vc-dynamic-save"></div>' ),
		_vc_shortcode         = $popup.is( '[data-vc-shortcode^="vc_column"], [data-vc-shortcode^="vc_row"]' ),
		_nav_shortcode        = $popup.is( '[data-vc-shortcode="mpc_pagination"], [data-vc-shortcode="mpc_navigation"]' ),
		_listen_for_changes   = false;

	$popup.one( 'mpc.render', function() {
		$presets.each( function() {
			var $preset    = $( this ),
				_shortcode = $preset.attr( 'data-shortcode' ),
				_sub_type  = $preset.attr( 'data-sub_type' ),
				_wp_nonce  = $preset.attr( 'data-wp_nonce' ),
				_no_prefix = _shortcode != $popup.attr( 'data-vc-shortcode' ) && $preset.attr( 'name' ).indexOf( _shortcode ) == -1;

			if ( _nav_shortcode ) {
				_sub_type = '';
			}

			$.post( ajaxurl, {
				action:    'mpc_get_shortcode_presets',
				shortcode: _shortcode,
				wp_nonce:  _wp_nonce
			}, function( response ) {
				$preset.append( response ).trigger( 'mpc.loaded' );

				$preset.val( $preset.attr( 'data-selected' ) ).trigger( 'change' );

				if ( ! _no_prefix || _vc_shortcode ) {
					$preset.siblings( '.mpc-init-overlay' ).fadeOut();
					$preset.siblings( '.mpc-ajax' ).removeClass( 'mpc-active' );
				}
			} );

			if ( _no_prefix && ! _vc_shortcode ) {
				$.post( ajaxurl, {
					action:    'mpc_get_shortcode_fields',
					shortcode: _shortcode,
					wp_nonce:  _wp_nonce
				}, function( response ) {
					if ( response.error === undefined ) {
						var $all_fields    = $popup.find( 'input, textarea, select' ).not( $preset ).filter( '.wpb_vc_param_value' ),
							$preset_fields = $();

						for ( var _field in response ) {
							var $field = $all_fields.filter( '[name="' + response[ _field ] + '"]' );

							if ( $field.length ) {
								$preset_fields = $preset_fields.add( $field );
							}
						}

						$preset.trigger( 'mpc.refresh', [ $preset_fields ] );
					}

					$preset.siblings( '.mpc-init-overlay' ).fadeOut();
					$preset.siblings( '.mpc-ajax' ).removeClass( 'mpc-active' );
				}, 'json' );
			}

			if ( _sub_type == 'navigation' || _sub_type == 'pagination' ) {
				var $fields = $preset.parents( '.vc_edit_form_elements' ).find( 'input, textarea, select' ).not( $preset ).filter( '.wpb_vc_param_value[name^="' + _shortcode + '"]' );

				$save_popup.one( 'click', function() {
					clear_preset( $preset, $fields, true );
				} );
			}
		} );
	} ).one( 'mpc.close', function() {
		_listen_for_changes = false;

		$dynamic_save.remove();
	} );

	$presets.each( function() {
		var $preset                 = $( this ),
			$preset_wrap            = $preset.parent(),
			$previews               = $preset.siblings( '.mpc-preview' ),
			$load_preset            = $preset_wrap.find( '.mpc-load.mpc-vc-button' ),
			$save_preset            = $preset_wrap.find( '.mpc-save.mpc-vc-button' ),
			$new_preset             = $preset_wrap.find( '.mpc-new.mpc-vc-button' ),
			$rename_preset          = $preset_wrap.find( '.mpc-rename.mpc-vc-button' ),
			$delete_preset          = $preset_wrap.find( '.mpc-delete.mpc-vc-button' ),
			$clear_preset           = $preset_wrap.find( '.mpc-clear.mpc-vc-button' ),
			$name_wrap              = $preset.siblings( '.mpc-name' ),
			$accept                 = $name_wrap.children( '.mpc-accept.mpc-vc-button' ),
			$cancel                 = $name_wrap.children( '.mpc-cancel.mpc-vc-button' ),
			$preset_name            = $name_wrap.find( '.mpc-preset-name' ),
			$fields                 = $preset.parents( '.vc_edit_form_elements' ).find( 'input, textarea, select' ).not( $preset ).filter( '.wpb_vc_param_value:not(.mpc-content-select)' ),
			$buttons                = $preset_wrap.find( '.mpc-vc-button:not(.mpc-new, .mpc-clear, .mpc-preview, .mpc-accept, .mpc-cancel)' ),
			_action                 = '',
			_sub_type               = $preset.attr( 'data-sub_type' ),
			_shortcode              = $preset.attr( 'data-shortcode' ),
			_wp_nonce               = $preset.attr( 'data-wp_nonce' ),
			_placeholder            = $preset.attr( 'data-placeholder' ),
			_baseurl                = $preset.attr( 'data-baseurl' ),
			_prefix                 = $preset.is( '[name^="' + _shortcode + '"]' ) ? _shortcode + '__' : '',
			_current                = '',
			_current_name           = '',
			_all                    = [],
			_is_preset_name_visible = false,
			_is_sub_fields_visible  = false,
			$sub_fields,
			$close;

		if ( _nav_shortcode ) {
			_sub_type = '';
		}

		if ( _prefix != '' ) {
			$fields = $fields.filter( '[name^="' + _prefix + '"]' );
		}

		if ( _sub_type != 'navigation' && _sub_type != 'pagination' ) {
			$fields = $fields.filter( ':not([name^="mpc_navigation__"]):not([name^="mpc_pagination__"])' ).add( $fields.filter( '[name="mpc_navigation__preset"], [name="mpc_pagination__preset"]' ) );
		}

		// Presets select
		$preset.on( 'change', function() {
			_current = $preset.val();

			$preset.removeClass( 'mpc-empty' );

			$cancel.trigger( 'click' );
			if ( $preset.val() == '' ) {
				$buttons.addClass( 'mpc-hidden' );
			} else {
				$buttons.removeClass( 'mpc-hidden' );
			}

			if ( _sub_type == 'navigation' || _sub_type == 'pagination' ) {
				$save_preset.addClass( 'mpc-hidden' );

				_is_sub_fields_visible = toggle_sub_fields( $sub_fields, _sub_type, _is_sub_fields_visible, false );
			}
		} ).on( 'mpc.refresh', function( event, $preset_fields ) {
			$fields = $preset_fields;
		} ).on( 'mpc.update', function() {
			_current = $preset.val();

			$preset.removeClass( 'mpc-empty' );

			_all = [];
			$preset.children().each( function() {
				_all.push( $( this ).text() );
			} );

			if ( $preset.val() == '' ) {
				$buttons.addClass( 'mpc-hidden' );
			} else {
				$buttons.removeClass( 'mpc-hidden' );
			}
		} ).on( 'mpc.loaded', function() {
			$preset.addClass( 'mpc-loaded-values' );

			$preset.children().each( function() {
				_all.push( $( this ).text() );
			} );

			$preset.children( '[value^="preset_"]' ).wrapAll( '<optgroup class="mpc-presets--user" label="' + $previews_box_users.text() + '">' );
			$preset.children( '[value^="mpc_preset_"]' ).wrapAll( '<optgroup label="' + $previews_box_premade.text() + '">' );

			if ( _sub_type == 'navigation' || _sub_type == 'pagination' ) {
				$sub_fields = $( '<div class="mpc-vc-sub-fields" />' ).insertAfter( $preset.parents( '.vc_shortcode-param' ) );
				$close = $( '<a href="#close" class="mpc-close">&times;</a>' );

				$dynamic_save.html( _mpc_lang.mpc_preset[ 'save_' + _sub_type + '_preset' ] );

				$sub_fields
					.append( $close )
					.append( $preset.parents( '.vc_shortcode-param' ).siblings() );

				$close.on( 'click', function( event ) {
					event.preventDefault();

					_is_sub_fields_visible = toggle_sub_fields( $sub_fields, _sub_type, _is_sub_fields_visible, false );

					$save_preset.addClass( 'mpc-hidden' );

					_listen_for_changes = false;

					$dynamic_save.remove();

					if ( _action == 'new' ) {
						_is_preset_name_visible = toggle_preset_name( $name_wrap, _is_preset_name_visible, false );
					}
				} );

				$sub_fields.find( 'input, textarea, select' ).on( 'change', function() {
					if ( ( _sub_type == 'navigation' || _sub_type == 'pagination' ) && _listen_for_changes ) {
						_listen_for_changes = false;

						$dynamic_save.insertAfter( $save_popup );
					}
				} );

				$popup.on( 'click', '.mpc-vc-dynamic-save', function() {
					if ( _action == 'new' ) {
						$accept.trigger( 'click' );
					} else {
						$save_preset.trigger( 'click' );
					}
				} );
			}
		} );

		// Previews
		function lazy_load( $images, $context, _offset ) {
			var _inited   = false,
				_waypoint = $images.eq( _offset ).MPCwaypoint( {
				handler: function() {
					if ( _inited ) {
						return;
					} else {
						_inited = true;
					}

					$images.slice( _offset, _offset + 8 ).each( function() {
						var $image = $( this );

						$image.attr( 'src', _baseurl + $image.attr( 'data-src' ) );
					} );

					if ( $images.length > _offset ) {
						setTimeout( function() {
							lazy_load( $images, $context, _offset + 8 );
						}, 250 );
					}
				},
				context: $context,
				offset:  '100%'
			} );
		}

		$previews.on( 'click', function() {
			if ( $previews_box.is( '.mpc-modal-init' ) ) {
				$previews_box.removeClass( 'mpc-modal-init' );

				$previews_box.dialog( {
					title: $previews_box.attr( 'data-title' ),
					dialogClass: 'mpc-presets-modal',
					show: true,
					hide: true,
					modal: true,
					resizable: false,
					width: 510,
					height: 600,
					autoOpen: false,
					closeOnEscape: true
				} );

				$previews_box.on( 'click', '.mpc-preset-preview', function( event ) {
					event.preventDefault();

					$previews_box.dialog( 'option', 'target' ).val( $( this ).attr( 'data-preset-name' ) ).trigger( 'change' );

					$previews_box.dialog( 'close' );

					$previews_box.dialog( 'option', 'load_button' ).trigger( 'click' );
				} );
			}

			if ( $preset.attr( 'data-wide-modal' ) == '1' ) {
				$previews_box.addClass( 'mpc-wide-modal' );
			} else {
				$previews_box.removeClass( 'mpc-wide-modal' );
			}

			$previews_box.dialog( 'option', 'target', $preset );

			$previews_box.dialog( 'option', 'load_button', $load_preset );

			$previews_box.find( '.mpc-preset-preview' ).remove();
			$preset.find( 'option:not(.mpc-base-option)' ).each( function() {
				var $option = $( this ),
					_markup = '',
					_image  = $option.attr( 'data-preset-image' );

				if ( _image ) {
					_image = '<img src="' + _placeholder + '" data-src="' + _image + '">';
				} else {
					_image = '';
				}

				_markup += '<a href="#" class="mpc-preset-preview" data-preset-name="' + $option.val() + '">';
					_markup += _image;
					_markup += '<p>' + $option.text() + '</p>';
				_markup += '</a>';

				$previews_box.append( _markup );
			} );

			var $preset_previews = $previews_box.find( '.mpc-preset-preview[data-preset-name^="preset"]' );
			if ( $preset_previews.length ) {
				$previews_box_users.removeClass( 'mpc-hidden' );
				$preset_previews.first().before( $previews_box_users );
			} else {
				$previews_box_users.addClass( 'mpc-hidden' );
			}

			$preset_previews = $previews_box.find( '.mpc-preset-preview[data-preset-name^="mpc_preset"]' );
			if ( $preset_previews.length ) {
				$previews_box_premade.removeClass( 'mpc-hidden' );
				$preset_previews.first().before( $previews_box_premade );
			} else {
				$previews_box_premade.addClass( 'mpc-hidden' );
			}

			$previews_box.dialog( 'open' );

			var $images = $previews_box.find( '.mpc-preset-preview > img' );

			lazy_load( $images, $previews_box, 0 );
		} );

		// Preset name
		$preset_name.on( 'keyup', function() {
			$preset_name.removeClass( 'mpc-empty' );
		} );

		// New preset button
		$new_preset.on( 'click', function( event ) {
			if ( _action == 'rename' ) {
				$preset_name.val( '' );
			}

			_action = 'new';

			_is_sub_fields_visible = toggle_sub_fields( $sub_fields, _sub_type, _is_sub_fields_visible, true );
			_is_preset_name_visible = toggle_preset_name( $name_wrap, _is_preset_name_visible, true );

			if ( _sub_type == 'navigation' || _sub_type == 'pagination' ) {
				$save_preset.addClass( 'mpc-hidden' );

				_listen_for_changes = true;

				$dynamic_save.remove();
			}

			$preset_name.trigger( 'focus' );

			event.preventDefault();
		} );

		// Load preset button
		$load_preset.on( 'click', function( event ) {
			load_preset( $preset, $fields, _shortcode, _prefix, _wp_nonce );

			_is_sub_fields_visible = toggle_sub_fields( $sub_fields, _sub_type, _is_sub_fields_visible, true );
			_is_preset_name_visible = toggle_preset_name( $name_wrap, _is_preset_name_visible, false );

			if ( _sub_type == 'navigation' || _sub_type == 'pagination' ) {
				$save_preset.removeClass( 'mpc-hidden' );

				$dynamic_save.remove();
			}

			event.preventDefault();
		} );

		// Save preset button
		$save_preset.on( 'click', function( event ) {
			save_preset( $preset, $fields, _shortcode, _prefix, _wp_nonce );

			_is_preset_name_visible = toggle_preset_name( $name_wrap, _is_preset_name_visible, false );

			if ( _sub_type == 'navigation' || _sub_type == 'pagination' ) {
				_listen_for_changes = true;
			}

			event.preventDefault();
		} );

		// Rename preset button
		$rename_preset.on( 'click', function( event ) {
			if ( _action == 'new' ) {
				$preset_name.val( '' );
			}

			_action = 'rename';
			_current_name = $preset.find( ':selected' ).text();

			_is_sub_fields_visible = toggle_sub_fields( $sub_fields, _sub_type, _is_sub_fields_visible, false );
			_is_preset_name_visible = toggle_preset_name( $name_wrap, _is_preset_name_visible, true );

			_listen_for_changes = false;

			$dynamic_save.remove();

			$preset_name
				.val( _current_name )
				.trigger( 'focus' );

			event.preventDefault();
		} );

		// Delete preset button
		$delete_preset.on( 'click', function( event ) {
			delete_preset( $preset, _shortcode, _wp_nonce );

			_is_sub_fields_visible = toggle_sub_fields( $sub_fields, _sub_type, _is_sub_fields_visible, false );
			_is_preset_name_visible = toggle_preset_name( $name_wrap, _is_preset_name_visible, false );

			_listen_for_changes = false;

			$dynamic_save.remove();

			event.preventDefault();
		} );

		// Clear preset button
		$clear_preset.on( 'click', function( event ) {
			clear_preset( $preset, $fields, false );

			_is_preset_name_visible = toggle_preset_name( $name_wrap, _is_preset_name_visible, false );

			$dynamic_save.remove();

			event.preventDefault();
		} );

		// Accept new preset name or rename
		$accept.on( 'click', function( event ) {
			if ( _action == '' ) {
				return;
			}

			var _name = $preset_name.val().trim();

			if ( _action == 'new' ) {
				if ( _name != '' && _all.indexOf( _name ) == -1 ) {
					new_preset( _name, $preset, $fields, _shortcode, _prefix, _wp_nonce );

					_listen_for_changes = false;

					$dynamic_save.remove();

					_action = '';

					$cancel.trigger( 'click' );
				} else {
					$preset_name.addClass( 'mpc-empty' );
				}
			} else if ( _action == 'rename' ) {
				if ( _name != '' && _name != _current && _all.indexOf( _name ) == -1 ) {
					rename_preset( _name, _current, $preset, _shortcode, _wp_nonce );

					_listen_for_changes = false;

					$dynamic_save.remove();

					$cancel.trigger( 'click' );
				} else {
					$preset_name.addClass( 'mpc-empty' );
				}
			}

			event.preventDefault();
		} );

		// Cancel new name/rename
		$cancel.on( 'click', function( event ) {
			_is_preset_name_visible = toggle_preset_name( $name_wrap, _is_preset_name_visible, false );

			if ( _action == 'new' && ( _sub_type == 'navigation' || _sub_type == 'pagination' ) ) {
				_is_sub_fields_visible = toggle_sub_fields( $sub_fields, _sub_type, _is_sub_fields_visible, false );

				_listen_for_changes = false;

				$dynamic_save.remove();
			}

			$preset_name.val( '' );
			_action = '';

			event.preventDefault();
		} );
	} );
} )( jQuery );
