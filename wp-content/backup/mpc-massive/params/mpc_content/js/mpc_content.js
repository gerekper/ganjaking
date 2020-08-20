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
