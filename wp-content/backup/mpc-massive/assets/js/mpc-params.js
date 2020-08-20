/*----------------------------------------------------------------------------*\
	MPC_ALIGN PARAM
\*----------------------------------------------------------------------------*/
( function( $ ) {
	"use strict";

	var $aligns = $( '.mpc-vc-align' );

	$aligns.each( function() {
		var $align  = $( this ),
			$input  = $align.siblings( '.mpc-value' ),
			$radios = $align.find( '.mpc-align-radio' );

		$align.on( 'change', '.mpc-align-radio', function() {
			$input.val( $( this ).val() );
		} );

		$input.on( 'change', function() {
			$radios.filter( '[value="' + $input.val() + '"]' ).click();
		} );
	} );
} )( jQuery );

/*----------------------------------------------------------------------------*\
	MPC_ANIMATION PARAM
\*----------------------------------------------------------------------------*/
( function( $ ) {
	"use strict";

	var $animations = $( '.wpb_el_type_mpc_animation' );

	$animations.each( function() {
		var $animation_wrap = $( this ),
		    $animation = $animation_wrap.find( '.mpc-vc-animation' ),
		    $box = $animation_wrap.find( '.mpc-inner-box' ),
		    $replay = $animation_wrap.find( '.mpc-animation-replay' ),
		    _value;

		$replay.on( 'click', function() {
			var _field_name = $animation_wrap.attr( 'data-vc-shortcode-param-name' ).replace( '_type', '' ),
			    _duration   = parseInt( $( 'input[name="' + _field_name + '_duration"]' ).val() );

			_value = $animation.val();

			if ( _value != '' ) {
				if ( _value === "fadeIn" || /In$/.test( _value ) ) {
					$box.velocity( 'stop' ).velocity( { opacity: 0 }, { duration: _duration } );
				}

				$box.velocity( 'stop' ).velocity( _value, { duration: _duration } );

				if ( _value === "fadeOut" || /Out$/.test( _value ) ) {
					$box.velocity( 'stop' ).velocity( { opacity: 1 }, { display: "block", duration: _duration } );
				}
			}
		} );

		$animation.on( 'change', function() {
			var _field_name = $animation_wrap.attr( 'data-vc-shortcode-param-name' ).replace( '_type', '' ),
			    _duration   = parseInt( $( 'input[name="' + _field_name + '_duration"]' ).val() );

			_value = $animation.val();

			if ( _value != '' ) {
				if ( _value === "fadeIn" || /In$/.test( _value ) ) {
					$box.velocity( 'stop' ).velocity( { opacity: 0 }, { duration: _duration } );
				}

				$box.velocity( 'stop' ).velocity( _value, { duration: _duration } );

				if ( _value === "fadeOut" || /Out$/.test( _value ) ) {
					$box.velocity( 'stop' ).velocity( { opacity: 1 }, { display: "block", duration: _duration } );
				}
			}
		} );
	} );
} )( jQuery );

/*----------------------------------------------------------------------------*\
	MPC_COLORPICKER Param
\*----------------------------------------------------------------------------*/
(function( $ ) {
	"use strict";

	var $popup = $( '#vc_ui-panel-edit-element' );

	$popup.one( 'mpc.render', function() {
		var $mpc_colorpickers = $( '.vc_wrapper-param-type-mpc_colorpicker input' );

		$mpc_colorpickers.each( function() {
			var $field = $( this );

			$field.colorPicker();
		} );
	} );
})( jQuery );

/*----------------------------------------------------------------------------*\
	MPC_CONTENT PARAM
\*----------------------------------------------------------------------------*/
( function( $ ) {
	"use strict";

/* New content */
	function new_content( _name, $content, _shortcode, _wp_nonce ) {
		var _content = get_shortcode_content();

		if ( _content == '' ) {
			display_warning( $content );

			return;
		}

		set_ajax_state( $content, true );

		$.post( ajaxurl, {
			action:    'mpc_new_shortcode_content',
			shortcode: _shortcode,
			name:      _name,
			content:   _content,
			wp_nonce:  _wp_nonce
		} ).done( function( response ) {
			if ( response.success ) {
				if ( ! $content.find( '.mpc-contents--user' ).length ) {
					$content.find( '.mpc-base-option' )
						.after( '<optgroup class="mpc-contents--user" label="' + $previews_box_users.text() + '">' )
						.after( $content.find( '.default' ) );
				}

				$content.find( '.mpc-contents--user' ).append( response.markup );

				$content
					.val( response.id )
					.trigger( 'mpc.update' );
			} else {
				display_error( $content );
			}
		} ).fail( function() {
			display_error( $content );
		} ).always( function() {
			set_ajax_state( $content, false );
		} );
	}

/* Load content */
	function load_content( $content, _shortcode, _placement, _wp_nonce ) {
		var _value = $content.val();

		if ( _value != '' ) {
			set_ajax_state( $content, true );

			$content.removeClass( 'mpc-empty' );

			$.post( ajaxurl, {
				action:    'mpc_load_shortcode_content',
				shortcode: _shortcode,
				id:        _value,
				wp_nonce:  _wp_nonce
			} ).done( function( response ) {
				if ( ! response.success || ! set_shortcode_content( response.content, _placement ) ) {
					display_error( $content );
				}
			} ).fail( function() {
				display_error( $content );
			} ).always( function() {
				set_ajax_state( $content, false );

				_mpc_vars.$body.trigger( 'mpc.content_loaded' );
			} );
		} else {
			$content.addClass( 'mpc-empty' );
		}
	}

/* Save content */
	function save_content( $content, _shortcode, _wp_nonce ) {
		if ( $content.val() != '' ) {
			if ( confirm( _mpc_lang.mpc_content.save_confirm + '"' + $content.find( ':selected' ).text() + '"' ) ) {
				var _content = get_shortcode_content();

				if ( _content == '' ) {
					display_warning( $content );

					return;
				}

				set_ajax_state( $content, true );

				var _id = $content.val();

				$.post( ajaxurl, {
					action:    'mpc_save_shortcode_content',
					shortcode: _shortcode,
					id:        _id,
					content:   _content,
					wp_nonce:  _wp_nonce
				} ).done( function( response ) {
					if ( ! response.success ) {
						display_error( $content );
					}
				} ).fail( function() {
					display_error( $content );
				} ).always( function() {
					set_ajax_state( $content, false );
				} );
			}
		} else {
			$content.addClass( 'mpc-empty' );
		}
	}

/* Rename content */
	function rename_content( _name, _id, $content, _shortcode, _wp_nonce ) {
		set_ajax_state( $content, true );

		$.post( ajaxurl, {
			action:    'mpc_rename_shortcode_content',
			shortcode: _shortcode,
			id:        _id,
			name:      _name,
			wp_nonce:  _wp_nonce
		} ).done( function( response ) {
			if ( response.success ) {
				$content.find( ':selected' ).text( _name );
			} else {
				display_error( $content );
			}
		} ).fail( function() {
			display_error( $content );
		} ).always( function() {
			set_ajax_state( $content, false );
		} );
	}

/* Delete content */
	function delete_content( $content, _shortcode, _wp_nonce ) {
		if ( $content.val() != '' ) {
			if ( confirm( _mpc_lang.mpc_content.delete_confirm + '"' + $content.find( ':selected' ).text() + '"' ) ) {
				set_ajax_state( $content, true );

				$.post( ajaxurl, {
					action:    'mpc_delete_shortcode_content',
					shortcode: _shortcode,
					id:        $content.val(),
					wp_nonce:  _wp_nonce
				} ).done( function( response ) {
					if ( response.success ) {
						$content
							.find( ':selected' )
								.remove()
								.end()
							.val( '' )
							.trigger( 'mpc.update' );
					} else {
						display_error( $content );
					}
				} ).fail( function() {
					display_error( $content );
				} ).always( function() {
					set_ajax_state( $content, false );
				} );
			}
		} else {
			$content.addClass( 'mpc-empty' );
		}
	}

/* Helpers */
	function set_shortcode_content( content, placement ) {
		var _is_shortcode = /^\[[\s\S]*]$/m,
			_have_tabs    = /tab_id="/g,
			_tab_ids      = /tab_id="\d*-/g;

		if ( ! _is_shortcode.test( content ) || typeof vc.active_panel == 'undefined' || typeof vc.active_panel.model == 'undefined' ) {
			return false;
		}

		if ( _have_tabs.test( content ) ) {
			content = content.replace( _tab_ids, 'tab_id="' + time() + '-' );
		}

		var _shortcodes = vc.storage.parseContent( {}, content, _.isObject( vc.active_panel.model ) ? vc.active_panel.model.toJSON() : false );

		if ( placement == 'prepend' ) {
			var _reverse_order = vc.add_element_block_view.getFirstPositionIndex() - _.size( _shortcodes );
		} else if ( placement == 'replace' ) {
			var _inner_shortcodes = vc.shortcodes.where( { parent_id: vc.active_panel.model.get( 'id' ) } );

			if ( [ 'mpc_tabs', 'mpc_flipbox', 'mpc_cubebox' ].indexOf( vc.active_panel.model.get( 'shortcode' ) ) != -1 ) {
				vc.active_panel.model.view.clearTabs();
			}

			_inner_shortcodes.forEach( function( model ) {
				model.destroy();
			} );
		}

		_.each( _.values( _shortcodes ), function ( model ) {
			if ( placement == 'prepend' ) {
				model.order = _reverse_order++;
			}

			vc.shortcodes.create( model );
		}, this );

		return true;
	}

	function get_shortcode_content() {
		var _content = '';

		if ( typeof vc.active_panel != 'undefined' && typeof vc.active_panel.model != 'undefined' ) {
			_content = vc.shortcodes._getShortcodeContent( vc.active_panel.model );
		}

		return _content;
	}

	function set_ajax_state( $content, state ) {
		if ( state ) {
			$content.siblings( '.mpc-ajax' ).addClass( 'mpc-active' );
			$ajax_overlay.fadeIn();
		} else {
			$content.siblings( '.mpc-ajax' ).removeClass( 'mpc-active' );
			$ajax_overlay.fadeOut();
		}
	}

	function display_error( $content ) {
		$content.siblings( '.mpc-error' ).stop( true ).fadeIn().delay( 3000 ).fadeOut();
	}
	function display_warning( $content ) {
		$content.siblings( '.mpc-warning' ).stop( true ).fadeIn().delay( 3000 ).fadeOut();
	}

	function toggle_content_name( $name_wrap, _is_content_name_visible, _state ) {
		if ( ! _is_content_name_visible && _state ) {
			$name_wrap.stop( true ).slideDown();
		} else if ( _is_content_name_visible && ! _state ) {
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

/* Content field */
	var $popup                = $( '#vc_ui-panel-edit-element' ),
		$contents             = $( '.mpc-content-select' ),
		$previews_box         = $( '#mpc_contents_previews' ),
		$previews_box_users   = $previews_box.find( '.mpc-contents-section.mpc-contents--user' ),
		$previews_box_premade = $previews_box.find( '.mpc-contents-section.mpc-contents--premade' ),
		_vc_shortcode         = $popup.is( '[data-vc-shortcode^="vc_column"], [data-vc-shortcode^="vc_row"]' );

	$popup.one( 'mpc.render', function() {
		$contents.each( function() {
			var $content    = $( this ),
				_shortcode = $content.attr( 'data-shortcode' ),
				_wp_nonce  = $content.attr( 'data-wp_nonce' );

			$.post( ajaxurl, {
				action:    'mpc_get_shortcode_contents',
				shortcode: _shortcode,
				wp_nonce:  _wp_nonce
			}, function( response ) {
				if ( response != '' ) {
					$content.append( response );
				}

				$content
					.trigger( 'mpc.loaded' )
					.val( $content.attr( 'data-selected' ) )
					.trigger( 'change' );

				$content.siblings( '.mpc-init-overlay' ).fadeOut();
				$content.siblings( '.mpc-ajax' ).removeClass( 'mpc-active' );
			} );
		} );
	} );

	$contents.each( function() {
		var $content                 = $( this ),
			$content_wrap            = $content.parent(),
			$previews                = $content.siblings( '.mpc-preview' ),
			$load_content            = $content_wrap.find( '.mpc-load.mpc-vc-button' ),
			$save_content            = $content_wrap.find( '.mpc-save.mpc-vc-button' ),
			$new_content             = $content_wrap.find( '.mpc-new.mpc-vc-button' ),
			$rename_content          = $content_wrap.find( '.mpc-rename.mpc-vc-button' ),
			$delete_content          = $content_wrap.find( '.mpc-delete.mpc-vc-button' ),
			$name_wrap               = $content.siblings( '.mpc-name' ),
			$accept                  = $name_wrap.children( '.mpc-accept.mpc-vc-button' ),
			$cancel                  = $name_wrap.children( '.mpc-cancel.mpc-vc-button' ),
			$content_name            = $name_wrap.find( '.mpc-content-name' ),
			$buttons                 = $content_wrap.find( '.mpc-rename.mpc-vc-button, .mpc-delete.mpc-vc-button, .mpc-save.mpc-vc-button, .mpc-load.mpc-vc-button' ),
			$placement               = $content.siblings( '.mpc-placement' ),
			_action                  = '',
			_shortcode               = $content.attr( 'data-shortcode' ),
			_wp_nonce                = $content.attr( 'data-wp_nonce' ),
			_placeholder             = $content.attr( 'data-placeholder' ),
			_baseurl                 = $content.attr( 'data-baseurl' ),
			_extended                = $content.attr( 'data-extended' ) == '1',
			_current                 = '',
			_current_name            = '',
			_all                     = [],
			_is_content_name_visible = false;

		if ( _vc_shortcode ) {
			var $style_param = $popup.find( '.vc_wrapper-param-type-mpc_preset' );

			$content.style_select   = $style_param.find( '.mpc-preset-select' );
			$content.style_new      = $style_param.find( '.mpc-vc-button.mpc-new' );
			$content.style_save     = $style_param.find( '.mpc-vc-button.mpc-save' );
			$content.style_load     = $style_param.find( '.mpc-vc-button.mpc-load' );
			$content.style_delete   = $style_param.find( '.mpc-vc-button.mpc-delete' );
			$content.style_accept   = $style_param.find( '.mpc-vc-button.mpc-accept' );
			$content.style_cancel   = $style_param.find( '.mpc-vc-button.mpc-cancel' );
			$content.style_name     = $style_param.find( '.mpc-preset-name' );
			$content.style_dyn_name = $style_param.find( '.mpc-preset-dynamic-name' );
		}

		// contents select
		$content.on( 'change', function() {
			_current = $content.val();

			$content.removeClass( 'mpc-empty' );

			$cancel.trigger( 'click' );
			if ( $content.val() == '' ) {
				$buttons.addClass( 'mpc-hidden' );
			} else {
				$buttons.removeClass( 'mpc-hidden' );
			}

			if ( _vc_shortcode && _current !== null ) {
				if ( $content.style_select.is( '.mpc-loaded-values' ) ) {
					if ( $content.style_select.find( 'option[value="' + _current.substr( 1 ) + '"]' ).length == 0 ) {
						$content.find( 'option[value="' + _current + '"]' )
							.clone()
							.val( _current.substr( 1 ) )
							.attr( 'class', _current.substr( 1 ) )
							.appendTo( $content.style_select );
					}

					$content.style_dyn_name.val( $content.find( 'option[value="' + _current + '"]' ).text() );
					$content.style_select.val( _current.substr( 1 ) ).trigger( 'change' );
				} else {
					var _iterate;

					_iterate = setInterval( function() {
						if ( $content.style_select.is( '.mpc-loaded-values' ) ) {
							if ( $content.style_select.find( 'option[value="' + _current.substr( 1 ) + '"]' ).length == 0 ) {
								$content.find( 'option[value="' + _current + '"]' )
									.clone()
									.val( _current.substr( 1 ) )
									.attr( 'class', _current.substr( 1 ) )
									.appendTo( $content.style_select );
							}

							$content.style_dyn_name.val( $content.find( 'option[value="' + _current + '"]' ).text() );
							$content.style_select.val( _current.substr( 1 ) ).trigger( 'change' );

							clearInterval( _iterate );
						}
					}, 250 );
				}
			}
		} ).on( 'mpc.update', function() {
			_current = $content.val();

			$content.removeClass( 'mpc-empty' );

			_all = [];
			$content.children().each( function() {
				_all.push( $( this ).text() );
			} );

			if ( $content.val() == '' ) {
				$buttons.addClass( 'mpc-hidden' );
			} else {
				$buttons.removeClass( 'mpc-hidden' );
			}
		} ).on( 'mpc.loaded', function() {
			$content.children().each( function() {
				_all.push( $( this ).text() );
			} );

			$content.children( '[value^="_preset_"]' ).wrapAll( '<optgroup class="mpc-contents--user" label="' + $previews_box_users.text() + '">' );
			$content.children( '[value^="_mpc_preset_"]' ).wrapAll( '<optgroup label="' + $previews_box_premade.text() + '">' );
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
					dialogClass: 'mpc-contents-modal',
					show: true,
					hide: true,
					modal: true,
					resizable: false,
					width: 510,
					height: 600,
					autoOpen: false,
					closeOnEscape: true
				} );

				$previews_box.on( 'click', '.mpc-content-preview', function( event ) {
					event.preventDefault();

					$previews_box.dialog( 'option', 'target' ).val( $( this ).attr( 'data-preset-name' ) ).trigger( 'change' );

					$previews_box.dialog( 'close' );

					$previews_box.dialog( 'option', 'load_button' ).trigger( 'click' );
				} );
			}

			if ( $content.attr( 'data-wide-modal' ) == '1' ) {
				$previews_box.addClass( 'mpc-wide-modal' );
			} else {
				$previews_box.removeClass( 'mpc-wide-modal' );
			}

			$previews_box.dialog( 'option', 'target', $content );

			$previews_box.dialog( 'option', 'load_button', $load_content );

			$previews_box.find( '.mpc-content-preview' ).remove();
			$content.find( 'option:not(.mpc-base-option)' ).each( function() {
				var $option = $( this ),
					_markup = '',
					_image  = $option.attr( 'data-content-image' );

				if ( _image ) {
					_image = '<img src="' + _placeholder + '" data-src="' + _image + '">';
				} else {
					_image = '';
				}

				_markup += '<a href="#" class="mpc-content-preview" data-preset-name="' + $option.val() + '">';
					_markup += _image;
					_markup += '<p>' + $option.text() + '</p>';
				_markup += '</a>';

				$previews_box.append( _markup );
			} );

			var $content_previews = $previews_box.find( '.mpc-content-preview[data-preset-name^="_preset"]' );
			if ( $content_previews.length ) {
				$previews_box_users.removeClass( 'mpc-hidden' );
				$content_previews.first().before( $previews_box_users );
			} else {
				$previews_box_users.addClass( 'mpc-hidden' );
			}

			$content_previews = $previews_box.find( '.mpc-content-preview[data-preset-name^="_mpc_preset"]' );
			if ( $content_previews.length ) {
				$previews_box_premade.removeClass( 'mpc-hidden' );
				$content_previews.first().before( $previews_box_premade );
			} else {
				$previews_box_premade.addClass( 'mpc-hidden' );
			}

			$previews_box.dialog( 'open' );

			var $images = $previews_box.find( '.mpc-content-preview > img' );

			lazy_load( $images, $previews_box, 0 );
		} );

		// content name
		$content_name.on( 'keyup', function() {
			$content_name.removeClass( 'mpc-empty' );
		} );

		// New content button
		$new_content.on( 'click', function( event ) {
			if ( _action == 'rename' ) {
				$content_name.val( '' );
			}

			_action = 'new';

			if ( _vc_shortcode ) {
				$content.style_new.trigger( 'click' );
			}

			_is_content_name_visible = toggle_content_name( $name_wrap, _is_content_name_visible, true );

			$content_name.trigger( 'focus' );

			event.preventDefault();
		} );

		// Load content button
		$load_content.on( 'click', function( event ) {
			if ( _extended ) {
				$placement.removeClass( 'mpc-hidden' );
			} else {
				load_content( $content, _shortcode, 'replace', _wp_nonce );

				if ( _vc_shortcode ) {
					$content.style_load.trigger( 'click' );
				}
			}

			_is_content_name_visible = toggle_content_name( $name_wrap, _is_content_name_visible, false );

			event.preventDefault();
		} );

		$placement.on( 'click', '.mpc-vc-button', function( event ) {
			$placement.addClass( 'mpc-hidden' );

			var _placement = this.getAttribute( 'href' ).substr( 1 );

			if ( _placement != 'close' ) {
				load_content( $content, _shortcode, _placement, _wp_nonce );

				if ( _vc_shortcode ) {
					$content.style_load.trigger( 'click' );
				}
			}

			event.preventDefault();
		} );

		// Save content button
		$save_content.on( 'click', function( event ) {
			save_content( $content, _shortcode, _wp_nonce );

			if ( _vc_shortcode ) {
				$content.style_save.trigger( 'click' );
			}

			_is_content_name_visible = toggle_content_name( $name_wrap, _is_content_name_visible, false );

			event.preventDefault();
		} );

		// Rename content button
		$rename_content.on( 'click', function( event ) {
			if ( _action == 'new' ) {
				$content_name.val( '' );
			}

			_action = 'rename';
			_current_name = $content.find( ':selected' ).text();

			_is_content_name_visible = toggle_content_name( $name_wrap, _is_content_name_visible, true );

			$content_name
				.val( _current_name )
				.trigger( 'focus' );

			event.preventDefault();
		} );

		// Delete content button
		$delete_content.on( 'click', function( event ) {
			delete_content( $content, _shortcode, _wp_nonce );

			if ( _vc_shortcode ) {
				$content.style_delete.trigger( 'click' );
			}

			_is_content_name_visible = toggle_content_name( $name_wrap, _is_content_name_visible, false );

			event.preventDefault();
		} );

		// Accept new content name or rename
		$accept.on( 'click', function( event ) {
			if ( _action == '' ) {
				return;
			}

			var _name = $content_name.val().trim();

			if ( _action == 'new' ) {
				if ( _name != '' && _all.indexOf( _name ) == -1 ) {
					new_content( _name, $content, _shortcode, _wp_nonce );

					_action = '';

					if ( _vc_shortcode ) {
						$content.style_name.val( _name );
						$content.style_accept.trigger( 'click' );
					}

					$cancel.trigger( 'click' );
				} else {
					$content_name.addClass( 'mpc-empty' );
				}
			} else if ( _action == 'rename' ) {
				if ( _name != '' && _name != _current && _all.indexOf( _name ) == -1 ) {
					rename_content( _name, _current, $content, _shortcode, _wp_nonce );

					$cancel.trigger( 'click' );
				} else {
					$content_name.addClass( 'mpc-empty' );
				}
			}

			event.preventDefault();
		} );

		// Cancel new name/rename
		$cancel.on( 'click', function( event ) {
			_is_content_name_visible = toggle_content_name( $name_wrap, _is_content_name_visible, false );

			$content_name.val( '' );
			_action = '';

			if ( _vc_shortcode ) {
				$content.style_cancel.trigger( 'click' );
			}

			event.preventDefault();
		} );
	} );
} )( jQuery );

/*----------------------------------------------------------------------------*\
	MPC_CSS PARAM
\*----------------------------------------------------------------------------*/
( function( $ ) {
	"use strict";

	function fields_to_css( $css_field, $fields, _section, _selector, _advanced ) {
		var _styles = '',
			_unit = '',
			_value = '';

		if ( _section == 'border' || _section == 'inner_border' ) {
			_unit = 'px';
		} else if ( _section == 'padding' || _section == 'margin' ) {
			_unit = $fields.filter( _selector + 'unit' ).val();
		}

		if ( _advanced ) {
			if ( $fields.filter( _selector + 'top' ).val() != '' && ! isNaN( _value = parseFloat( $fields.filter( _selector + 'top' ).val() ) ) ) {
				_styles += _section + '-top:' + _value + _unit + ';';
			}
			if ( $fields.filter( _selector + 'right' ).val() != '' && ! isNaN( _value = parseFloat( $fields.filter( _selector + 'right' ).val() ) ) ) {
				_styles += _section + '-right:' + _value + _unit + ';';
			}
			if ( $fields.filter( _selector + 'bottom' ).val() != '' && ! isNaN( _value = parseFloat( $fields.filter( _selector + 'bottom' ).val() ) ) ) {
				_styles += _section + '-bottom:' + _value + _unit + ';';
			}
			if ( $fields.filter( _selector + 'left' ).val() != '' && ! isNaN( _value = parseFloat( $fields.filter( _selector + 'left' ).val() ) ) ) {
				_styles += _section + '-left:' + _value + _unit + ';';
			}
		} else {
			if ( _section == 'border' ) {
				if ( $fields.filter( _selector + 'all' ).val() != '' && ! isNaN( _value = parseFloat( $fields.filter( _selector + 'all' ).val() ) ) ) {
					_styles += _section + '-width:' + _value + _unit + ';';
				}
			} else if ( _section == 'padding' || _section == 'margin' ) {
				if ( $fields.filter( _selector + 'all' ).val() != '' && ! isNaN( _value = parseFloat( $fields.filter( _selector + 'all' ).val() ) ) ) {
					_styles += _section + ':' + _value + _unit + ';';
				}
			}
		}

		if ( _section == 'border' ) {
			if ( $fields.filter( _selector + 'color' ).val() != '' ) {
				_styles += 'border-color:' + $fields.filter( _selector + 'color' ).val() + ';';
			}

			if ( $fields.filter( _selector + 'style' ).val() != '' ) {
				_styles += 'border-style:' + $fields.filter( _selector + 'style' ).val() + ';';
			}

			if ( $fields.filter( _selector + 'radius' ).val() != '' && ! isNaN( _value = parseFloat( $fields.filter( _selector + 'radius' ).val() ) ) ) {
				_styles += 'border-radius:' + _value + _unit + ';';
			}
		}

		if ( _section == 'inner_border' ) {
			if ( $fields.filter( _selector + 'color' ).val() != '' &&  $fields.filter( _selector + 'width' ).val() != '' ) {
				_styles += 'box-shadow: inset 0px 0px 0px ' + $fields.filter( _selector + 'width' ).val() + _unit + ' ' + $fields.filter( _selector + 'color' ).val() + ';';
			}
		}

		$css_field.val( _styles );
	}

	function css_to_fields( $css_field, $fields, _section, _selector ) {
		var _css    = $css_field.val(),
			_prefix = $css_field.attr( 'data-prefix' );

		_prefix = typeof _prefix == 'undefined' ? '' : _prefix;

		$fields.val( '' );
		$fields.filter( _selector + 'unit' ).val( 'px' );

		if ( _css != '' ) {
			_css = _css.split( ';' );

			for ( var _index = 0; _index < _css.length; _index++ ) {
				var _pair = _css[ _index ].split( ':' );

				if ( _pair.length == 2 ) {
					_pair[ 0 ] = _pair[ 0 ].replace( '-', '_' );

					if ( _section == 'border' ) {
						$fields.filter( '.' + _prefix + _pair[ 0 ] ).val( _pair[ 1 ] ).trigger( 'change' ).trigger( 'blur' );

						if ( _pair[ 0 ] == 'border_width' ) {
							$fields.filter( _selector + 'all' ).val( _pair[ 1 ] ).trigger( 'blur' );
						}
					}

					if ( _section == 'padding' || _section == 'margin' ) {
						$fields.filter( '.' + _prefix + _pair[ 0 ] ).val( _pair[ 1 ] ).trigger( 'change' ).trigger( 'blur' );
						$fields.filter( _selector + 'unit' ).val( _pair[ 1 ].replace( parseFloat( _pair[ 1 ] ), '' ) ).trigger( 'change' );

						if ( _pair[ 0 ] == 'padding' || _pair[ 0 ] == 'margin' ) {
							$fields.filter( _selector + 'all' ).val( _pair[ 1 ] ).trigger( 'blur' );
						}
					}
				}
			}
		}
	}

	function inner_border_css_to_fields( $css_field, $fields ) {
		var _css    = $css_field.val(),
		    _prefix = $css_field.attr( 'data-prefix' );

		_prefix = typeof _prefix == 'undefined' ? '' : _prefix;

		if ( _css != '' ) {
			_css = /box-shadow: inset 0px 0px 0px (.*?);/.exec( _css );

			if( _css[ 1 ] == 'undefined' )
				return;

			_css = _css[ 1 ].split( ' ' );

			$fields.filter( '.' + _prefix + 'inner_border_width' ).val( parseFloat( _css[ 0 ] ) ).trigger( 'blur' );
			$fields.filter( '.' + _prefix + 'inner_border_color'  ).val( _css[ 1 ] ).trigger( 'change' );
		}
	}

	var $css_fields = $( '.mpc-vc-css' ),
		$popup      = $( '#vc_ui-panel-edit-element' ),
		$save_panel = $popup.find( '.vc_ui-button[data-vc-ui-element="button-save"]' );

	$popup.one( 'mpc.render', function() {
		$css_fields.each( function() {
			var $css_field       = $( this ),
				_prefix          = $css_field.attr( 'data-prefix' ),
				_section         = $css_field.attr( 'data-section' ),
				_selector        = '.' + _prefix + _section + '_',
				$advanced        = $popup.find( _selector + 'divider' ),
				$separate_fields = $( _selector + 'top,' + _selector + 'right,' + _selector + 'bottom,' + _selector + 'left' ),
				$compact_field   = $( _selector + 'all' ),
				$radius_field    = $( _selector + 'radius' ),
				$other_fields    = $css_field.parents( '.mpc-vc-indent' ).find( '.wpb_vc_param_value' ).not( $css_field );

			$advanced.on( 'change', function() {
				if ( $advanced.is( ':checked' ) ) {
					$compact_field.parents( '.vc_shortcode-param' ).stop( true ).fadeOut( 100, function() {
						$compact_field.parents( '.vc_shortcode-param' ).css( 'display', 'none' );

						$separate_fields.parents( '.vc_shortcode-param' ).stop( true ).fadeIn( 100, function() {
							$separate_fields.parents( '.vc_shortcode-param' ).css( 'display', '' );
						} );
					} );
				} else {
					$separate_fields.parents( '.vc_shortcode-param' ).stop( true ).fadeOut( 100, function() {
						$separate_fields.parents( '.vc_shortcode-param' ).css( 'display', 'none' );

						$compact_field.parents( '.vc_shortcode-param' ).stop( true ).fadeIn( 100, function() {
							$compact_field.parents( '.vc_shortcode-param' ).css( 'display', '' );
						} );
					} );
				}
			} ).trigger( 'change' );

			$compact_field.add( $separate_fields ).add( $radius_field ).on( 'blur mpc.change', function() {
				var $field = $( this ),
					_value = parseFloat( $field.val() );

				$field.val( isNaN( _value ) ? '' : _value );
			} );

			$compact_field.on( 'change', function() {
				$separate_fields.val( $compact_field.val() );
			} );

			$other_fields.on( 'blur change', function() {
				fields_to_css( $css_field, $other_fields, _section, _selector, $advanced.is( ':checked' ) );
			} );

			$save_panel.one( 'click', function() {
				if ( ! $advanced.is( ':checked' ) ) {
					$advanced.val( '' );

					if ( window.vc_mode == 'admin_frontend_editor' ) {
						setTimeout( function() {
							$advanced.val( 'true' );
						}, 500 );
					}
				}

				$other_fields.each( function() {
					var $field    = $( this ),
						_settings = JSON.parse( $field.parents( '.vc_shortcode-param' ).attr( 'data-param_settings' ) );

					if ( window.vc_mode != 'admin_frontend_editor' ) {
						if ( typeof _settings.std != 'undefined' ) {
							$field.val( _settings.std );
						} else if ( typeof _settings.value != 'undefined' ) {
							$field.val( _settings.value );
						}
					}
				} );
			} );

			if( _section == 'inner_border' ) {
				inner_border_css_to_fields( $css_field, $other_fields );
			} else {
				css_to_fields( $css_field, $other_fields, _section, _selector );
			}

			$css_field.on( 'mpc.change', function() {
				if( _section == 'inner_border' ) {
					inner_border_css_to_fields( $css_field, $other_fields );
				} else {
					css_to_fields( $css_field, $other_fields, _section, _selector );
				}
			} );

			$advanced.on( 'change', function() {
				fields_to_css( $css_field, $other_fields, _section, _selector, $advanced.is( ':checked' ) );
			} )
		} );
	} );
} )( jQuery );

/*----------------------------------------------------------------------------*\
	MPC_DATETIME Param
\*----------------------------------------------------------------------------*/
(function( $ ) {
	"use strict";

	var $popup = $( '#vc_ui-panel-edit-element' );

	$popup.one( 'mpc.render', function() {
		$( '.vc_wrapper-param-type-mpc_datetime input' ).MPCdatetimepicker( {
			format:  'd/m/Y H:i',
			minDate: 0 // today
		});
	} );
})( jQuery );

/*----------------------------------------------------------------------------*\
	MPC_GRADIENT Param
\*----------------------------------------------------------------------------*/
( function( $ ) {
	"use strict";

	var $popup = $( '#vc_ui-panel-edit-element' );

	$popup.one( 'mpc.render', function() {
		var $gradient_wraps  = $( '.vc_wrapper-param-type-mpc_gradient' );

		$gradient_wraps.each( function() {
			var $gradient_wrap  = $( this ),
				$gradient_value = $gradient_wrap.find( '.mpc-value' ),
				$color_pickers  = $gradient_wrap.find( '.mpc-color-picker' ),
				$range_slider   = $gradient_wrap.find( '.mpc-range-slider' ),
				$angle_slider   = $gradient_wrap.find( '.mpc-angle-slider' ),
				$gradient_type  = $gradient_wrap.find( '.mpc-gradient-type' ),
				_color_picker   = {
					defaultColor: $( this ).val(),
					change: function() {
						setTimeout( function() {
							gradient_picker_change( $gradient_wrap, $angle_slider, $range_slider, $gradient_type );
						}, 50 );
					},
					clear: function() {
						setTimeout( function() {
							gradient_picker_change( $gradient_wrap, $angle_slider, $range_slider, $gradient_type );
						}, 50 );
					},
					hide: true,
					palettes: true
				};

			$color_pickers.wpColorPicker( _color_picker );

			$gradient_wrap.on( 'mpc.update', function() {
				gradient_picker_change( $gradient_wrap, $angle_slider, $range_slider, $gradient_type );
			} );

			$range_slider.slider( {
				animate:  'fast',
				range:    true,
				min:      parseInt( $range_slider.attr( 'data-min' ) ),
				max:      parseInt( $range_slider.attr( 'data-max' ) ),
				step:     parseInt( $range_slider.attr( 'data-step' ) ),
				values:   [ parseInt( $range_slider.attr( 'data-start-value' ) ), parseInt( $range_slider.attr( 'data-end-value' ) ) ],
				disabled: $gradient_type.is( ':checked' )
			} ).on( 'slide', function( event, ui ) {
				var $this = $( this );

				$this.attr( 'data-start-value', ui.values[ 0 ] ).attr( 'data-end-value', ui.values[ 1 ] );
				$this.parent().siblings( 'label' ).find( 'em' ).text( ui.values[ 0 ] + ' - ' + ui.values[ 1 ] );

				gradient_picker_change( $gradient_wrap, $angle_slider, $range_slider, $gradient_type );
			} );

			$angle_slider.slider( {
				animate:  'fast',
				min:      parseInt( $angle_slider.attr( 'data-min' ) ),
				max:      parseInt( $angle_slider.attr( 'data-max' ) ),
				step:     parseInt( $angle_slider.attr( 'data-step' ) ),
				value:    parseInt( $angle_slider.attr( 'data-value' ) ),
				disabled: $gradient_type.is( ':checked' )
			} ).on( 'slide', function( event, ui ) {
				var $this = $( this );

				$this.attr( 'data-value', ui.value );
				$this.parent().siblings( 'label' ).find( 'em' ).text( ui.value );

				gradient_picker_change( $gradient_wrap, $angle_slider, $range_slider, $gradient_type );
			} );

			$gradient_type.on( 'change', function() {
				var $this = $( this );

				if ( $this.is( ':checked' ) )
					$angle_slider.slider( 'disable' ).closest( '.mpc-gradient-slider' ).addClass( 'mpc-hidden' );
				else
					$angle_slider.slider( 'enable' ).closest( '.mpc-gradient-slider' ).removeClass( 'mpc-hidden' );

				gradient_picker_change( $gradient_wrap, $angle_slider, $range_slider, $gradient_type );
			} );

			$gradient_value.on( 'mpc.change', function() {
				gradient_value_change( $gradient_wrap, $angle_slider, $range_slider, $gradient_type );
			} );
		} );
	} );

	function gradient_value_change( $gradient, $angle_slider, $range_slider, $gradient_type ) {
		var $gradient_value    = $gradient.find( '.mpc-value' ),
			_gradient_value    = $gradient_value.val(),
			_gradient_values   = _gradient_value.split( '||' );

		if ( _gradient_values.length == 5 ) {
			$gradient.find( '.mpc-gradient-start' ).val( _gradient_values[ 0 ] ).trigger( 'change' );
			$gradient.find( '.mpc-gradient-end' ).val( _gradient_values[ 1 ] ).trigger( 'change' );
			$range_slider.slider( 'values', _gradient_values[ 2 ].split( ';' ) );
			$angle_slider.slider( 'value', _gradient_values[ 3 ] );

			if ( ( _gradient_values[ 4 ] == 'linear' && $gradient_type.is( ':checked' ) ) ||
				( _gradient_values[ 4 ] == 'radial' && ! $gradient_type.is( ':checked' ) ) ) {
				$gradient_type.click();
			}

			setTimeout( function() {
				$gradient_value.val( _gradient_value );
			}, 50 );
		}
	}

	function gradient_picker_change( $gradient_wrap, $angle_slider, $range_slider, $gradient_type ) {
		var $gradient_preview  = $gradient_wrap.find( '.mpc-gradient-preview' ),
		    $gradient_value    = $gradient_wrap.find( '.mpc-value' ),
		    _start_color       = $gradient_wrap.find( '.mpc-gradient-start' ).val(),
		    _end_color         = $gradient_wrap.find( '.mpc-gradient-end' ).val(),
		    _gradient_angle    = $angle_slider.attr( 'data-value' ) || '0',
		    _start_color_range = $range_slider.attr( 'data-start-value' ) || 0,
		    _end_color_range   = $range_slider.attr( 'data-end-value' ) || 100,
		    _type              = $gradient_type.is( ':checked' ) ? 'radial' : 'linear',
		    _angle             = ( _type == 'linear' ) ? _gradient_angle + 'deg' : 'circle',
		    _tmp_color         = '';

		if( _start_color.length === 0 ) _start_color = 'rgba(0,0,0,0)';
		if( _end_color.length === 0 ) _end_color = 'rgba(0,0,0,0)';

		$gradient_value.val( _start_color + '||' + _end_color + '||' + _start_color_range + ';' + _end_color_range + '||' + _angle.replace( 'deg', '' ) + '||' + _type );

		var _linear_gradient = 'background: ' + _type + '-gradient(' + _angle + ', ' + _start_color + ' ' + _start_color_range + '%, ' + _end_color + ' ' + _end_color_range + '%);';

		_angle = _angle.replace( 'circle', '0' ).replace( 'deg', '' );

		if( 135 <= _angle && _angle < 225 ) {
			_type = 0;

			_tmp_color = _start_color;
			_start_color = _end_color;
			_end_color = _tmp_color;
		} else if( ( 0 <= _angle && _angle < 45 ) || ( 315 <= _angle && _angle < 360 ) ) {
			_type = 0;
		} else if( 45 <= _angle && _angle < 135 ) {
			_type = 1;
		} else if( 225 <= _angle && _angle < 315 ) {
			_type = 1;

			_tmp_color = _start_color;
			_start_color = _end_color;
			_end_color = _tmp_color;
		}

		var _ie_gradient = 'background: filter: progid:DXImageTransform.Microsoft.gradient(GradientType=' + _type + ',startColorstr=' + _start_color + ', endColorstr=' + _end_color + ');';

		$gradient_preview.attr( 'style', _linear_gradient + _ie_gradient );
	}

} )( jQuery );

( function( $ ) {
	"use strict";

	function get_icons_modal_font_icons( font ) {
		$.post( ajaxurl, {
			action: 'mpc_icon_get_icons_modal_font_icons',
			font:   font
		}, function( response ) {
			$icons_modal_grid.find( '.mpc-' + font ).remove();

			$icons_modal_grid
				.children()
					.css( 'display', 'none' )
					.end()
				.append( response )
					.trigger( 'mpc.updated' )
					.trigger( 'mpc.selected' );

			_icons_fonts_loaded[ font ] = true;

			$icons_modal.trigger( 'mpc.search' );
		} );
	}

	function get_icons_modal_font_link( font ) {
		$.post( ajaxurl, {
			action: 'mpc_icon_get_icons_modal_font_link',
			font:   font
		}, function( response ) {
			$( 'head' ).prepend( response );

			get_icons_modal_font_icons( font );
		} );
	}

/* Icon modal */
	var $icons_modal = $( '#mpc_icon_select_grid_modal' ),
		$icons_modal_grid = $( '#mpc_icon_select_grid' ),
		_icons_fonts_loaded = {};

	$icons_modal_grid.children().each( function() {
		_icons_fonts_loaded[ this.className.replace( 'mpc-', '' ) ] = true;
	} );

	if ( $icons_modal.is( '.mpc-modal-init' ) ) {
		var $icons_search = $( '#mpc_icon_select_search' ),
			$icons_family = $( '#mpc_icon_select_family' ),
			$icons = $icons_modal.find( 'i' );

		$icons_modal.removeClass( 'mpc-modal-init' );

		$icons_modal.dialog( {
			title: $icons_modal.attr( 'data-modal-title' ),
			dialogClass: 'mpc-icons-modal',
			target: null,
			active: null,
			show: true,
			hide: true,
			modal: true,
			resizable: false,
			width: 640,
			height: 600,
			autoOpen: false,
			closeOnEscape: true,
			close: function() {
				$icons_search.val( '' );
				$icons.show();
				$icons.filter( '[data-active="1"]' ).attr( 'data-active', 0 );

				_mpc_vars.$body.css( 'overflow', '' );
			},
			open: function() {
				var _active = $icons_modal.dialog( 'option', 'active' );

				if ( _active ) {
					_active = _active.split( ' ' );

					if ( _active.length == 2 ) {
						$icons_family.val( _active[ 0 ] );
					}
				}

				$icons_family.trigger( 'change' );

				_mpc_vars.$body.css( 'overflow', 'hidden' );
			}
		} );

		$icons_modal.on( 'click', 'i', function() {
			var icon_class = $( this ).attr( 'class' ),
				$target = $icons_modal.dialog( 'option', 'target' );

			if ( $target != null ) {
				$target.trigger( 'mpc.update', [ icon_class ] );

				$icons_modal.dialog( 'option', 'target', null );
			}

			$icons_modal.dialog( 'close' );
		} ).on( 'mpc.search', function() {
			if ( $icons_search.val() != '' ) {
				$icons_search.trigger( 'keyup' );
			}
		} );

		$icons_search.on( 'keyup', function() {
			if ( $icons_search.val() != '' ) {
				$icons.hide();
				$icons.filter( '[class*="' + $icons_search.val() + '"]' ).show();
			} else {
				$icons.show();
			}
		} );

		$icons_family.on( 'change', function() {
			if ( typeof _icons_fonts_loaded[ $icons_family.val() ] == 'undefined' ) {
				get_icons_modal_font_link( $icons_family.val() );
			} else {
				$icons_modal_grid
					.children()
					.css( 'display', 'none' )
					.filter( 'div.mpc-' + $icons_family.val() )
					.css( 'display', 'block' );

				$icons_modal_grid.trigger( 'mpc.selected' );

				$icons_modal.trigger( 'mpc.search' );
			}
		} );

		$icons_modal_grid.on( 'mpc.updated', function() {
			$icons = $icons_modal.find( 'i' );
		} ).on( 'mpc.selected', function() {
			$icons.filter( '[class="' + $icons_modal.dialog( 'option', 'active' ) + '"]' ).attr( 'data-active', 1 );
		} );
	}

/* Icon fields */
	var $icons_fields = $( '.vc_wrapper-param-type-mpc_icon' ),
		$icons_fields_values = $icons_fields.find( '.mpc-icon-value' ),
		$current_icon;

	$icons_fields.on( 'click', '.mpc-icon-select', function( event ) {
		$current_icon = $( this );

		if ( $icons_modal.length ) {
			$icons_modal.dialog( 'option', 'target', $icons_fields );
			$icons_modal.dialog( 'option', 'active', $current_icon.siblings( '.mpc-icon-value' ).val() );
			$icons_modal.dialog( 'open' );
		}

		event.preventDefault();
	} );

	// Update icon
	$icons_fields.on( 'mpc.update', function( event, icon_class ) {
		if ( $current_icon != null ) {
			$current_icon.siblings( '.mpc-icon-value' ).val( icon_class ).trigger( 'change' );
			$current_icon.children( 'i' ).attr( 'class', icon_class );
			$current_icon.removeClass( 'mpc-icon-empty' );
		}
	} );
	$icons_fields_values.on( 'mpc.change', function() {
		var $icon = $( this ),
			_icon_class = $icon.val();

		if ( _icon_class != '' ) {
			$icon
				.siblings( '.mpc-icon-select' )
					.removeClass( 'mpc-icon-empty' )
					.children( 'i' )
						.attr( 'class', _icon_class );

			get_icons_modal_font_link( _icon_class.split( ' ' )[ 0 ] );
		} else {
			$icon.siblings( '.mpc-icon-clear' ).trigger( 'click' );
		}
	} );

	// Clear icon
	$icons_fields.on( 'click', '.mpc-icon-clear', function( event ) {
		var $icon_clear = $( this );

		$icon_clear.siblings( '.mpc-icon-value' ).val( '' );
		$icon_clear.siblings( '.mpc-icon-select' )
			.addClass( 'mpc-icon-empty' )
			.children( 'i' ).attr( 'class', '' );

		event.preventDefault();
	} );

	// Get used icon fonts
	var _used_icons_fonts = {};
	$icons_fields.find( '.mpc-icon-value' ).filter( ':not([value=""])' ).each( function() {
		var _font = $( this ).val();

		if ( _font ) {
			_font = _font.split( ' ' );

			if ( _font.length == 2 && typeof _icons_fonts_loaded[ _font[ 0 ] ] == 'undefined' ) {
				_used_icons_fonts[ _font[ 0 ] ] = true;
			}
		}
	} );

	for ( var _font in _used_icons_fonts ) {
		get_icons_modal_font_link( _font );
	}
})( jQuery );

/*----------------------------------------------------------------------------*\
 MPC LAYOUT SELECT PARAM
\*----------------------------------------------------------------------------*/
( function( $ ) {
    "use strict";

	var $popup = $( '#vc_ui-panel-edit-element' );

	$popup.one( 'mpc.render', function() {
		$popup.find( '.vc_wrapper-param-type-mpc_layout_select' ).each( function() {
			var $layout_select      = $( this ),
			    $layout_items       = $layout_select.find( '.mpc-layout-item' ),
			    $layout_value       = $layout_select.find( '.mpc_layout_select_field' );

			$layout_items.on( 'click', function() {
				var $active_item = $layout_select.find( '.mpc-layout-item[data-checked="true"]' );

				$active_item.removeAttr( 'data-checked' );

				$( this ).attr( 'data-checked', true );

				$layout_value
					.val( $( this ).attr( 'data-value' ) )
					.trigger( 'change' );
			} );

			$layout_value.on( 'mpc.change', function() {
				$layout_items.filter( '[data-value="' + $layout_value.val() + '"]' ).trigger( 'click' );
			} );
		});
	});

} )( jQuery );

/*----------------------------------------------------------------------------*\
	MPC_LIST PARAM
\*----------------------------------------------------------------------------*/
( function( $ ) {
	"use strict";

	var $lists = $( '.mpc-vc-list-wrap' );

	$lists.each( function() {
		var $list  = $( this ),
			$order = $list.find( '.mpc-vc-list-order' ),
			$input  = $list.find( '.mpc-value' ),
			$options = $list.find( '.mpc-vc-list' );

		$order.sortable( {
			placeholder: 'mpc-list-item mpc-placeholder'
		} ).on( 'sortupdate', function() {
			var _order = [];

			$order.find( '.mpc-list-item' ).each( function() {
				_order.push( $( this ).attr( 'data-id' ) );
			} );

			$input.val( _order.join( ',' ) ).trigger( 'change' );
		} );

		$order.disableSelection();

		$options.on( 'change', '.mpc-list-option', function() {
			var $option = $( this ),
				_value = $option.val();

			if ( $option.prop( 'checked' ) ) {
				$order.append( '<div class="mpc-list-item mpc-list-' + _value + '" data-id="' + _value + '"><i class="dashicons dashicons-sort"></i>' + $option.attr( 'data-name' ) + '</div>' );
			} else {
				$order.find( '.mpc-list-item[data-id="' + _value + '"]' ).remove();
			}

			if ( $order.find( '.mpc-list-item' ).length == 0 ) {
				$order.addClass( 'mpc-empty' );
			} else {
				$order.removeClass( 'mpc-empty' );
			}

			$order.trigger( 'sortupdate' );
		} );

		$input.on( 'mpc.change', function() {
			var _items = $input.val();

			$options.find( '.mpc-list-option:checked' ).trigger( 'click' );

			if ( _items != '' ) {
				_items = _items.split( ',' );

				for ( var _index = 0; _index < _items.length; _index++ ) {
					$options.find( '.mpc-list-option[value="' + _items[ _index ] + '"]' ).trigger( 'click' );
				}
			}
		} );
	} );
} )( jQuery );

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

/*----------------------------------------------------------------------------*\
 MPC_SHADOW Param
 \*----------------------------------------------------------------------------*/
(function( $ ) {
	"use strict";

	var $popup = $( '#vc_ui-panel-edit-element' );

	$popup.one( 'mpc.render', function() {
		var $shadow_wraps = $( '.vc_wrapper-param-type-mpc_shadow' );

		$shadow_wraps.each( function() {
			var $shadow_wrap  = $( this ),
				$shadow_value = $shadow_wrap.find( '.mpc-value' ),
				$left_offset  = $shadow_wrap.find( '.mpc-shadow-left' ),
				$top_offset   = $shadow_wrap.find( '.mpc-shadow-top' ),
				$blur         = $shadow_wrap.find( '.mpc-shadow-blur' ),
				$params       = $shadow_wrap.find( '.mpc_text_field' ),
				$color_picker = $shadow_wrap.find( '.mpc-shadow-color' ),
				$shadow_text  = $shadow_wrap.find( '.mpc-shadow-text' ),
				_color_picker = {
					defaultColor: $( this ).val(),
					change:       function() {
						setTimeout( function() {
							$shadow_value.val( shadow_param_change( $left_offset, $top_offset, $blur, $color_picker, $shadow_text ) );
						}, 50 );
					},
					clear:        function() {
						setTimeout( function() {
							$shadow_value.val( shadow_param_change( $left_offset, $top_offset, $blur, $color_picker, $shadow_text ) );
						}, 50 );
					},
					hide:         true,
					palettes:     true
				};

			$color_picker.wpColorPicker( _color_picker );

			$params.on( 'change keyup', function() {
				$shadow_value.val( shadow_param_change( $left_offset, $top_offset, $blur, $color_picker, $shadow_text ) );
			} );

			$shadow_value.on( 'mpc.change', function() {
				shadow_value_change( $shadow_value.val(), $left_offset, $top_offset, $blur, $color_picker, $shadow_text );
			} );
		} );
	} );

	function shadow_param_change( $left_offset, $top_offset, $blur, $color_picker, $shadow_text ) {
		var _text_shadow = 'none';
		var _values = {
			left: $left_offset.val(),
			top: $top_offset.val(),
			blur: $blur.val(),
			color: $color_picker.val()
		};

		if ( _values.color != '' ) {
			_text_shadow = '';

			if ( _values.left != '' ) {
				_text_shadow += _values.left + 'px ';
			} else {
				_text_shadow += '0 ';
			}

			if ( _values.top != '' ) {
				_text_shadow += _values.top + 'px ';
			} else {
				_text_shadow += '0 ';
			}

			if ( _values.blur != '' ) {
				_text_shadow += _values.blur + 'px ';
			} else {
				_text_shadow += '0 ';
			}

			_text_shadow += _values.color;
		}

		$shadow_text.css( 'text-shadow', _text_shadow );

		return _text_shadow != 'none' ? _text_shadow : '';
	}

	function shadow_value_change( value, $left_offset, $top_offset, $blur, $color_picker, $shadow_text ) {
		value = value.trim().replace( /\s+/g, ' ' ).split( ' ' );

		if ( value.length > 4 ) {
			value[ 3 ] = value.slice( 3 ).join( '' );
		}

		if ( value.length != 4 ) {
			value[ 0 ] = value[ 1 ] = value[ 2 ] = value[ 3 ] = '';
		}

		$left_offset.val( parseInt( value[ 0 ] ) );
		$top_offset.val( parseInt( value[ 1 ] ) );
		$blur.val( parseInt( value[ 2 ] ) );
		$color_picker.val( value[ 3 ] ).trigger( 'change' );

		shadow_param_change( $left_offset, $top_offset, $blur, $color_picker, $shadow_text );
	}

})( jQuery );

/*----------------------------------------------------------------------------*\
	MPC_SLIDER PARAM
\*----------------------------------------------------------------------------*/
( function( $ ) {
	"use strict";

	var $sliders = $( '.mpc-vc-slider' );

	$sliders.each( function() {
		var $slider_wrap = $( this ),
			$slider      = $slider_wrap.children( '.mpc-slider' ),
			$input       = $slider_wrap.siblings( '.mpc-value' );

		$slider.slider( {
			animate: 'fast',
			min:     parseFloat( $slider.attr( 'data-min' ) ),
			max:     parseFloat( $slider.attr( 'data-max' ) ),
			step:    parseFloat( $slider.attr( 'data-step' ) ),
			value:   parseFloat( $slider.attr( 'data-value' ) ),
			range:   $slider_wrap.is( '.mpc-fill' ) ? 'min' : false
		} );

		if ( $input.length ) {
			$slider.on( 'slide', function( event, ui ) {
				if ( ui.value === +ui.value && ui.value !== ( ui.value|0 )  ) {
					ui.value = ui.value.toFixed( 2 );
				}

				$input.val( ui.value );
			} );

			$input.on( 'change', function() {
				$slider.slider( 'value', $input.val() );

				var _value = $slider.slider( 'value' );
				if ( _value === +_value && _value !== ( _value|0 )  ) {
					_value = _value.toFixed( 2 );
				}

				$input.val( _value );
			} )
		}
	} );

} )( jQuery );

/*----------------------------------------------------------------------------*\
	MPC_SPLIT PARAM
\*----------------------------------------------------------------------------*/
( function( $ ) {
	"use strict";

	function update_labels() {
		var $labels = $( '.vc_param_group-list .vc_param-group-admin-labels' );

		$labels.each( function() {
			$( this ).html( $( this ).html().replace( new RegExp( '(\\|){3}', 'g' ), ' | ' ) );
		} );
	}

	function init_split() {
		var $split_fields = $( '.mpc-vc-split-text' ),
			$split_values = $( '.mpc-vc-split' );

		$split_fields.off( 'blur' );
		$split_values.off( 'change mpc.change' );

		$split_fields.on( 'blur', function() {
			var $field = $( this ),
				_value = $field.val();

			if ( _value != '' ) {
				$field.siblings( '.mpc-vc-split' ).val( _value.replace( /\n/g, '|||' ) );
			}

			update_labels();
		} );

		$split_values.on( 'change mpc.change', function() {
			var $field = $( this ),
				$split = $field.siblings( '.mpc-vc-split-text' ),
				_value = $field.val();

			if ( _value != '' ) {
				$split.val( _value.replace( /\|\|\|/g, '\n' ) );
			} else {
				$split.val( '' );
			}

			update_labels();
		} );
	}

	var $popup = $( '#vc_ui-panel-edit-element' );

	$popup.one( 'mpc.render', function() {
		var	$list_group = $popup.find( '[data-vc-shortcode-param-name="elements"]' ),
			$group_add = $list_group.find( '.vc_param_group-add_content' ),
			$group_duplicate = $list_group.find( '.column_clone' );

		$group_add.on( 'click', function() {
			setTimeout( function(){
				init_split();
			}, 250 );
		} );

		$group_duplicate.on( 'click', function() {
			init_split();
		} );

		init_split();
		update_labels();
	} );


} )( jQuery );

/*----------------------------------------------------------------------------*\
	MPC_TEXT Param
\*----------------------------------------------------------------------------*/
(function( $ ) {
	"use strict";

	function mpc_validate__int( $field ) {
		var _value = parseInt( $field.val() );

		$field.val( isNaN( _value ) ? '' : _value );
	}

	function mpc_validate__float( $field ) {
		var _value = parseFloat( $field.val() );

		$field.val( isNaN( _value ) ? '' : _value );
	}

	var $popup = $( '#vc_ui-panel-edit-element' );

	$popup.one( 'mpc.render', function() {
		var $mpc_texts = $( '.vc_wrapper-param-type-mpc_text .mpc-text-input' );

		$mpc_texts.each( function() {
			var $field = $( this ),
				$input = $field.children( 'input' ),
				_validate = $field.attr( 'data-validate' );

			if ( _validate == '1' || _validate == 'int' ) {
				mpc_validate__int( $input );

				$input.on( 'blur', function() {
					mpc_validate__int( $input );
				} );
			} else if ( _validate == 'float' ) {
				mpc_validate__float( $input );

				$input.on( 'blur', function() {
					mpc_validate__float( $input );
				} );
			}
		} );
	} );
})( jQuery );

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
