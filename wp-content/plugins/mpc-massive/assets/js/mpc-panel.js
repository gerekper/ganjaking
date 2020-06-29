/*----------------------------------------------------------------------------*\
	GLOBAL
\*----------------------------------------------------------------------------*/

var $_mpc_previews_source = jQuery( '#mpc_previews_source' );

window.mpc = {
	_is_working:      false,
	_base_url:        $_mpc_previews_source.val(),
	_style_postfix:   $_mpc_previews_source.val() == $_mpc_previews_source.data( 'local' ) ? 'mpc_presets/' : 'presets/',
	_content_postfix: $_mpc_previews_source.val() == $_mpc_previews_source.data( 'local' ) ? 'mpc_contents/' : 'contents/',
	_wpnonce:         jQuery( '#_wpnonce' ).val()
};

window.mpc.show_error = function() {
	var $error = jQuery( '#mpc_panel__error' );

	$error.addClass( 'mpc-visible' );
	setTimeout( function() {
		$error.removeClass( 'mpc-visible' );
	}, 3000 );
};

window.mpc.templates_error = function() {
	var $error = jQuery( '#mpc_panel__error' ),
		$close = $error.find( '.mpc-panel__close' );

	$error.addClass( 'mpc-visible' );

	$close.on( 'click', function(){
		$error.removeClass( 'mpc-visible' );
		jQuery( '#mpc_panel__cover' ).removeClass( 'mpc-active' );
	} );
};

window.mpc.templates_success = function() {
	var $error = jQuery( '#mpc_panel__error' ),
		$close = $error.find( '.mpc-panel__close' );

	$error.addClass( 'mpc-visible mpc-success' );

	$close.on( 'click', function(){
		$error.removeClass( 'mpc-visible mpc-success' );
		jQuery( '#mpc_panel__cover' ).removeClass( 'mpc-active' );
	} );
};

window.mpc.progress_button = function( $button, data, duration, type, $clear_button ) {
	var $progress = $button.find( '.mpc-progress' );

	if ( type == 'save' ) {
		var $inputs = data.options;
	}

	$button.on( 'click', function( event ) {
		event.preventDefault();

		if ( window.mpc._is_working ) {
			return;
		}

		if ( type == 'save' ) {
			data.options = $inputs.serialize();
		} else if ( type == 'batch' || type == 'download' ) {
			if ( ! confirm( $button.attr( 'data-message' ) ) ) {
				return;
			}
		} else if ( type == 'delete' || type == 'install' ) {
			var $selected = $button.closest( '.mpc-section' ).find( '.mpc-presets__list ' + ( type == 'delete' ? '.mpc-active.mpc-installed' : '.mpc-active' ) ),
				_presets  = [];

			if ( type == 'delete' ) {
				if ( $selected.length && ! confirm( $button.attr( 'data-message' ) ) ) {
					return;
				}
			} else {
				if ( $selected.filter( '.mpc-installed' ).length && ! confirm( $button.attr( 'data-message' ) ) ) {
					return;
				}
			}

			if ( $selected.length ) {
				$selected.each( function() {
					_presets.push( jQuery( this ).attr( 'data-preset' ) );
				} );
			} else {
				return;
			}

			if ( typeof data.presets != 'undefined' ) {
				data.presets = _presets;
			} else if ( typeof data.contents != 'undefined' ) {
				data.contents = _presets;
			}
		}

		if ( typeof data.shortcode != 'undefined' ) {
			data.shortcode = $button.closest( '.mpc-section' ).find( '.mpc-presets-select' ).val();
		}

		data._wpnonce = window.mpc._wpnonce;

		window.mpc._is_working = true;
		$button
			.removeClass( 'mpc-finished' )
			.addClass( 'mpc-working' );

		$progress
			.stop( true )
			.animate( {
				width: '90%'
			}, duration, 'linear' );

		jQuery.ajax( {
			url:    ajaxurl,
			method: 'POST',
			data:   data
		} ).always( function( response ) {
			if ( ! response.success ) {
				window.mpc.show_error();
			}

			$progress
				.stop( true )
				.animate( {
					width: '100%'
				}, {
					complete: function() {
						setTimeout( function() {
							window.mpc._is_working = false;

							if ( response.success ) {
								if ( type == 'delete' ) {
									$selected.removeClass( 'mpc-installed' );
								} else if ( type == 'install' ) {
									$selected.addClass( 'mpc-installed' );
								}
							}

							if ( type == 'download' ) {
								window.mpc._base_url        = $_mpc_previews_source.attr( 'data-local' );
								window.mpc._style_postfix   = 'mpc_presets/';
								window.mpc._content_postfix = 'mpc_contents/';
							}

							$progress.animate( {
								left: '100%'
							}, {
								complete: function() {
									if ( typeof $clear_button != 'undefined' ) {
										$clear_button.trigger( 'click' );
									}

									$button
										.removeClass( 'mpc-working' )
										.addClass( 'mpc-finished' );

									$progress.css( {
										width: 0,
										left: 0
									} );

									setTimeout( function() {
										$button.removeClass( 'mpc-finished' );
									}, 2500 );
								}
							} )
						}, 1000 );
					}
				} );
		} );
	} );
};

/*----------------------------------------------------------------------------*\
	PANEL
\*----------------------------------------------------------------------------*/

( function( $ ) {
	"use strict";

	var $panel        = $( '#mpc_panel' ),
		$panel_inputs = $panel.find( ':input' ),
		$save_panel   = $( '#mpc_panel__save' );

	window.mpc.progress_button( $save_panel, {
		action:  'mpc_save_panel',
		options: $panel_inputs
	}, 1000, 'save' );

} )( jQuery );

/*----------------------------------------------------------------------------*\
	SHORTCODES
\*----------------------------------------------------------------------------*/

( function( $ ) {
	"use strict";

	function toggle_input( $checkbox, $shortcodes ) {
		var $value     = $checkbox.siblings( '.mpc-shortcode-value' ),
			_shortcode = $checkbox.attr( 'data-shortcode' ),
			_index     = 0,
			_subindex  = 0,
			_enabled   = false;

		if ( $checkbox.is( ':checked' ) ) {
			$value.val( 1 );

			if ( _enable_dependencies[ _shortcode ] !== undefined ) {
				for ( _index in _enable_dependencies[ _shortcode ] ) {
					$shortcodes.filter( '[data-shortcode="' + _enable_dependencies[ _shortcode ][ _index ] + '"]:not(:checked)' ).click();
				}
			}
		} else {
			$value.val( 0 );

			if ( _disable_dependencies[ _shortcode ] !== undefined ) {
				for ( _index in _disable_dependencies[ _shortcode ] ) {
					$shortcodes.filter( '[data-shortcode="' + _disable_dependencies[ _shortcode ][ _index ] + '"]:checked' ).click();
				}
			}
		}

		for ( _index in _multi_dependencies ) {
			_enabled = false;

			for ( _subindex in _multi_dependencies[ _index ] ) {
				if ( $shortcodes.filter( '[data-shortcode="' + _multi_dependencies[ _index ][ _subindex ] + '"]:checked' ).length > 0 ) {
					_enabled = true;
				}
			}

			if ( _enabled ) {
				$shortcodes.filter( '[data-shortcode="' +  _index + '"]:not(:checked)' ).click();
			} else {
				$shortcodes.filter( '[data-shortcode="' +  _index + '"]:checked' ).click();
			}
		}
	}

	var _enable_dependencies = {
		'mpc_accordion':              [ 'mpc_icon' ],
		'mpc_alert':                  [ 'mpc_ribbon' ],
		'mpc_wc_add_to_cart':         [ 'mpc_tooltip' ],
		'mpc_button':                 [ 'mpc_tooltip' ],
		'mpc_button_set':             [ 'mpc_button', 'mpc_lightbox' ],
		'mpc_callout':                [ 'mpc_button', 'mpc_divider', 'mpc_ribbon' ],
		'mpc_carousel_anything':      [ 'mpc_navigation' ],
		'mpc_carousel_image':         [ 'mpc_navigation' ],
		'mpc_carousel_posts':         [ 'mpc_navigation', 'mpc_single_post' ],
		'mpc_carousel_slider':        [ 'mpc_navigation' ],
		'mpc_carousel_testimonial':   [ 'mpc_navigation', 'mpc_testimonial' ],
		'mpc_circle_icons':           [ 'mpc_icon_column' ],
		'mpc_connected_icons':        [ 'mpc_icon_column' ],
		'mpc_counter':                [ 'mpc_icon', 'mpc_divider' ],
		'mpc_cubebox':                [ 'mpc_ribbon' ],
		'mpc_grid_posts':             [ 'mpc_pagination', 'mpc_single_post' ],
		'mpc_hotspot':                [ 'mpc_tooltip' ],
		'mpc_icon_column':            [ 'mpc_icon', 'mpc_divider' ],
		'mpc_icon_list':              [ 'mpc_icon' ],
		'mpc_ihover':                 [ 'mpc_ihover_item' ],
		'mpc_interactive_image':      [ 'mpc_hotspot', 'mpc_ribbon' ],
		'mpc_image':                  [ 'mpc_ribbon' ],
		'mpc_lightbox':               [ 'mpc_button', 'mpc_tooltip' ],
		'mpc_map':                    [ 'mpc_marker' ],
		'mpc_modal':                  [ 'mpc_icon' ],
		'mpc_pricing_box':            [ 'mpc_button', 'mpc_navigation', 'mpc_pricing_column', 'mpc_pricing_legend' ],
		'mpc_quote':                  [ 'mpc_ribbon' ],
		'mpc_single_post':            [ 'mpc_button' ],
		'mpc_tabs':                   [ 'mpc_button' ],
		'mpc_timeline_basic':         [ 'mpc_timeline_item' ],
		'mpc_textblock':              [ 'mpc_tooltip' ],
		'mpc_timeline_item':          [ 'mpc_icon', 'mpc_divider', 'mpc_tooltip' ],
		'mpc_wc_category':            [ 'mpc_divider' ],
		'mpc_wc_carousel_categories': [ 'mpc_navigation', 'mpc_wc_category' ],
		'mpc_wc_grid_categories':     [ 'mpc_wc_category' ],
		'mpc_wc_product':             [ 'mpc_wc_add_to_cart', 'mpc_icon' ]
	};

	var _disable_dependencies = {
		'mpc_button':            [ 'mpc_callout', 'mpc_lightbox', 'mpc_pricing_box', 'mpc_single_post', 'mpc_tabs' ],
		'mpc_divider':           [ 'mpc_callout', 'mpc_counter', 'mpc_icon_column', 'mpc_timeline_item', 'mpc_wc_category' ],
		'mpc_icon':              [ 'mpc_accordion', 'mpc_counter', 'mpc_icon_column', 'mpc_icon_list', 'mpc_modal', 'mpc_timeline_item' ],
		'mpc_icon_column':       [ 'mpc_circle_icons', 'mpc_connected_icons' ],
		'mpc_ihover':            [ 'mpc_ihover_item' ],
		'mpc_interactive_image': [ 'mpc_hotspot' ],
		'mpc_lightbox':          [ 'mpc_button' ],
		'mpc_map':               [ 'mpc_marker' ],
		'mpc_pricing_box':       [ 'mpc_pricing_column', 'mpc_pricing_legend' ],
		'mpc_single_post':       [ 'mpc_carousel_posts', 'mpc_grid_posts' ],
		'mpc_testimonial':       [ 'mpc_carousel_testimonial' ],
		'mpc_timeline_item':     [ 'mpc_timeline_basic' ],
		'mpc_wc_category':       [ 'mpc_wc_carousel_categories', 'mpc_wc_grid_categories' ]
	};

	var _multi_dependencies = {
		'mpc_navigation': [ 'mpc_carousel_anything', 'mpc_carousel_image', 'mpc_carousel_posts', 'mpc_carousel_slider', 'mpc_carousel_testimonial', 'mpc_pricing_box', 'mpc_wc_carousel_categories' ],
		'mpc_pagination': [ 'mpc_grid_posts' ],
		'mpc_ribbon':     [ 'mpc_alert', 'mpc_callout', 'mpc_cubebox', 'mpc_image', 'mpc_interactive_image', 'mpc_quote' ],
		'mpc_tooltip':    [ 'mpc_wc_add_to_cart', 'mpc_button', 'mpc_hotspot', 'mpc_lightbox', 'mpc_timeline_item', 'mpc_textblock'  ]
	};

	/* Field init */
	var $shortcodes_wrap = $( '.mpc-shortcodes' ),
		$shortcodes      = $shortcodes_wrap.find( '.mpc-shortcode:not(.mpc-all)' ),
		$all_shortcodes  = $shortcodes_wrap.find( '.mpc-all' ),
		_use_all         = $all_shortcodes.is( ':checked' ),
		_index           = 0;

	$shortcodes.parent().each( function() {
		var $shortcode    = $( this ),
			$list_enable  = $(),
			$list_disable = $(),
			_shortcode    = $shortcode.children( '.mpc-shortcode' ).attr( 'data-shortcode' );

		if ( _disable_dependencies[ _shortcode ] !== undefined ) {
			for ( _index in _disable_dependencies[ _shortcode ] ) {
				$list_disable = $list_disable.add( $shortcodes.filter( '[data-shortcode="' + _disable_dependencies[ _shortcode ][ _index ] + '"]' ).parent() );
			}
		}
		if ( _enable_dependencies[ _shortcode ] !== undefined ) {
			for ( _index in _enable_dependencies[ _shortcode ] ) {
				$list_enable = $list_enable.add( $shortcodes.filter( '[data-shortcode="' + _enable_dependencies[ _shortcode ][ _index ] + '"]' ).parent() );
			}
		}

		$shortcode.data( 'dependent-enable', $list_enable );
		$shortcode.data( 'dependent-disable', $list_disable );
	} );

	$all_shortcodes.on( 'click', function() {
		toggle_input( $( this ), $shortcodes );

		if ( $all_shortcodes.is( ':checked' ) ) {
			$shortcodes.prop( 'disabled', true );
			$shortcodes.parent().addClass( 'mpc-disabled' );

			_use_all = true;
		} else {
			$shortcodes.prop( 'disabled', false );
			$shortcodes.parent().removeClass( 'mpc-disabled' );

			_use_all = false;
		}
	} );

	$shortcodes.on( 'click', function() {
		var $shortcode = $( this );

		$shortcode.parent().toggleClass( 'mpc-active' );

		toggle_input( $shortcode, $shortcodes );
	} );

	$shortcodes.parent().on( 'mouseenter', function() {
		if ( _use_all ) {
			return;
		}

		$( this ).data( 'dependent-enable' ).addClass( 'mpc-dependent--enable' );
		$( this ).data( 'dependent-disable' ).addClass( 'mpc-dependent--disable' );
	} ).on( 'mouseleave', function() {
		if ( _use_all ) {
			return;
		}

		$shortcodes.parent().removeClass( 'mpc-dependent--enable mpc-dependent--disable' );
	} );

} )( jQuery );

/*----------------------------------------------------------------------------*\
	PRESETS
\*----------------------------------------------------------------------------*/

( function( $ ) {
	"use strict";

	function lazy_load( $images, _offset, _postfix ) {
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

						$image.attr( 'src', window.mpc._base_url + _postfix + $image.attr( 'data-src' ) );
					} );

					if ( $images.length > _offset ) {
						setTimeout( function() {
							lazy_load( $images, _offset + 8, _postfix );
						}, 250 );
					}
				},
				offset:  '100%'
			} );
	}

	function shortcode_select( event ) {
		var $shortcode_select = event.data.shortcode_select,
			$presets_list     = event.data.presets_list,
			$controls         = event.data.controls,
			$ajax             = event.data.ajax,
			_action           = event.data._action,
			_postfix          = window.mpc[ event.data._postfix ],
			_wide_view        = event.data._wide_view,
			_shortcode        = $shortcode_select.val();

		$presets_list.html( '' );
		$controls.addClass( 'mpc-hidden' );

		if ( _shortcode == '' ) {
			return;
		}

		if ( _wide_view.indexOf( _shortcode ) != -1 ) {
			$presets_list.addClass( 'mpc-wide-view' );
		} else {
			$presets_list.removeClass( 'mpc-wide-view' );
		}

		$shortcode_select.prop( 'disabled', true );
		$ajax.addClass( 'is-active' );

		$.ajax( {
			url: ajaxurl,
			method: 'POST',
			data: {
				action:    _action,
				shortcode: _shortcode,
				_wpnonce:  window.mpc._wpnonce
			},
			dataType: 'json'
		} ).done( function( presets_list ) {
			$controls.removeClass( 'mpc-hidden' );

			for ( var _preset in presets_list ) {
				$presets_list.append( _preset_template( presets_list[ _preset ] ) );
			}

			var $images = $presets_list.find( '.mpc-preset > img' );

			lazy_load( $images, 0, _postfix );
		} ).fail( function() {
			window.mpc.show_error();
		} ).always( function() {
			$shortcode_select.prop( 'disabled', false );
			$ajax.removeClass( 'is-active' );
		} );
	}

	function select_all( event ) {
		event.preventDefault();

		var $presets_list = event.data.presets_list,
			$install      = event.data.install,
			$delete       = event.data.delete;

		$presets_list.find( '.mpc-preset' ).addClass( 'mpc-active' );
		$install.removeClass( 'mpc-disabled' );

		if ( $presets_list.find( '.mpc-preset.mpc-installed' ).length ) {
			$delete.removeClass( 'mpc-disabled' );
		}
	}

	function select_none( event ) {
		event.preventDefault();

		var $presets_list = event.data.presets_list,
			$install      = event.data.install,
			$delete       = event.data.delete;

		$presets_list.find( '.mpc-preset' ).removeClass( 'mpc-active' );
		$install.addClass( 'mpc-disabled' );
		$delete.addClass( 'mpc-disabled' );
	}

	function presets_list( event ) {
		var $preset       = $( this ),
			$presets_list = event.data.presets_list,
			$install      = event.data.install,
			$delete       = event.data.delete;

		$preset.toggleClass( 'mpc-active' );

		if ( $presets_list.find( '.mpc-active' ).length ) {
			$install.removeClass( 'mpc-disabled' );

			if ( $presets_list.find( '.mpc-active.mpc-installed' ).length ) {
				$delete.removeClass( 'mpc-disabled' );
			} else {
				$delete.addClass( 'mpc-disabled' );
			}
		} else {
			$install.addClass( 'mpc-disabled' );
			$delete.addClass( 'mpc-disabled' );
		}
	}

	var $shortcode_select_style   = $( '#mpc_presets__select' ),
		$shortcode_select_content = $( '#mpc_contents__select' ),
		$presets_list_style       = $( '#mpc_presets__list' ),
		$presets_list_content     = $( '#mpc_contents__list' ),
		$ajax_style               = $( '#mpc_presets__ajax' ),
		$ajax_content             = $( '#mpc_contents__ajax' ),
		$controls_style           = $( '#mpc_presets__controls' ),
		$controls_content         = $( '#mpc_contents__controls' ),
		$select_all_style         = $( '#mpc_presets__all' ),
		$select_all_content       = $( '#mpc_contents__all' ),
		$select_none_style        = $( '#mpc_presets__none' ),
		$select_none_content      = $( '#mpc_contents__none' ),
		$install_style            = $( '#mpc_presets__install' ),
		$install_content          = $( '#mpc_contents__install' ),
		$delete_style             = $( '#mpc_presets__delete' ),
		$delete_content           = $( '#mpc_contents__delete' ),
		$batch_install_style      = $( '#mpc_presets__batch' ),
		$batch_install_content    = $( '#mpc_contents__batch' ),
		$download                 = $( '#mpc_presets__download' ),
		_preset_template          = $( '#mpc_templates__preset' ).html(),
		_wide_view_style          = [ 'mpc_accordion', 'mpc_alert', 'mpc_animated_text', 'mpc_button_set', 'mpc_callout', 'mpc_carousel_image', 'mpc_carousel_posts', 'mpc_carousel_slider', 'mpc_connected_icons', 'mpc_countdown', 'mpc_cubebox', 'mpc_grid_images', 'mpc_grid_posts', 'mpc_icon_list', 'mpc_ihover', 'mpc_mailchimp', 'mpc_modal', 'mpc_pricing_box', 'mpc_progress', 'mpc_quote', 'mpc_tabs', 'mpc_testimonial' ],
		_wide_view_content = [];

	_preset_template = _.template( _preset_template ? _preset_template : '' );

	$shortcode_select_style.on( 'change', {
		shortcode_select: $shortcode_select_style,
		presets_list:     $presets_list_style,
		controls:         $controls_style,
		ajax:             $ajax_style,
		_action:          'mpc_get_presets',
		_postfix:         '_style_postfix',
		_wide_view:       _wide_view_style
	}, shortcode_select );

	$shortcode_select_content.on( 'change', {
		shortcode_select: $shortcode_select_content,
		presets_list:     $presets_list_content,
		controls:         $controls_content,
		ajax:             $ajax_content,
		_action:          'mpc_get_contents',
		_postfix:         '_content_postfix',
		_wide_view:       _wide_view_content
	}, shortcode_select );

	$select_all_style.on( 'click', {
		presets_list: $presets_list_style,
		install:      $install_style,
		delete:       $delete_style
	}, select_all );

	$select_all_content.on( 'click', {
		presets_list: $presets_list_content,
		install:      $install_content,
		delete:       $delete_content
	}, select_all );

	$select_none_style.on( 'click', {
		presets_list: $presets_list_style,
		install:      $install_style,
		delete:       $delete_style
	}, select_none );

	$select_none_content.on( 'click', {
		presets_list: $presets_list_content,
		install:      $install_content,
		delete:       $delete_content
	}, select_none );

	$presets_list_style.on( 'click', '.mpc-preset', {
		presets_list: $presets_list_style,
		install:      $install_style,
		delete:       $delete_style
	}, presets_list );

	$presets_list_content.on( 'click', '.mpc-preset', {
		presets_list: $presets_list_content,
		install:      $install_content,
		delete:       $delete_content
	}, presets_list );

	// Style Buttons
	window.mpc.progress_button( $batch_install_style, {
		action: 'mpc_install_all_presets'
	}, 90000, '' );

	window.mpc.progress_button( $install_style, {
		action:    'mpc_install_presets',
		shortcode: '',
		presets:   ''
	}, 10000, 'install', $select_none_style );

	window.mpc.progress_button( $delete_style, {
		action:    'mpc_delete_presets',
		shortcode: '',
		presets:   ''
	}, 2000, 'delete', $select_none_style );

	// Content Buttons
	window.mpc.progress_button( $batch_install_content, {
		action: 'mpc_install_all_contents'
	}, 90000, '' );

	window.mpc.progress_button( $install_content, {
		action:    'mpc_install_contents',
		shortcode: '',
		contents:  ''
	}, 10000, 'install', $select_none_content );

	window.mpc.progress_button( $delete_content, {
		action:    'mpc_delete_contents',
		shortcode: '',
		contents:  ''
	}, 2000, 'delete', $select_none_content );

	// Other Buttons
	window.mpc.progress_button( $download, {
		action: 'mpc_download_images'
	}, 60000, 'download' );
} )( jQuery );

/*----------------------------------------------------------------------------*\
	SYSTEM INFO
\*----------------------------------------------------------------------------*/

( function( $ ) {
	"use strict";

	var $show      = $( '#mpc_panel__show_info' ),
		$info_wrap = $( '#mpc_panel__system_wrap' ),
		$info_file = $( '#mpc_panel__info_file' ),
		$info_text = $info_wrap.find( 'textarea' ),
		_info      = $info_text.val();

	$show.on( 'click', function() {
		$info_wrap.css( 'max-height', 250 );

		setTimeout( function() {
			$info_wrap.css( 'max-height', '' );
		}, 250 );
	} );

	$info_file.on( 'click', function() {
		location.href = ajaxurl + '?action=mpc_export_info&_wpnonce=' + window.mpc._wpnonce + '&system_info=' + escape( _info );
	} );

	$info_text.on( 'click', function() {
		$info_text.select();
	} );
} )( jQuery );

/*----------------------------------------------------------------------------*\
	PAGE INSTALLER
\*----------------------------------------------------------------------------*/

( function( $ ) {
	"use strict";

	var $panel          = $( '#mpc_panel' ),
		$pages_list     = $panel.find( '.mpc-section.mpc-section--pages' ),
		$pages_filter   = $( '#mpc_pages__filter' ),
		$pages_settings = $( '#mpc_pages__settings' ),
		$install_pages  = $( '#mpc_pages__install' ),
		$panel_cover    = $( '#mpc_panel__cover' ),
		$error_response = $panel.find( '.mpc-panel__error-response' ),
		_wpnonce        = $( '#_wpnonce' ).val();

	$pages_list.on( 'click', '.mpc-preset', function() {
		var $this     = $( this ),
			_template = $this.attr( 'data-preset' );

		if ( $this.is( '.mpc-active' ) ) {
			$this.removeClass( 'mpc-active' );

			$pages_settings.find( '[value="' + _template + '"]' ).remove();
			$pages_settings.val( '' );
		} else {
			$this.addClass( 'mpc-active' );

			$pages_settings.append( '<option value="' + _template + '">' + $this.text() + '</option>' );
		}

		if ( $pages_list.find( '.mpc-preset.mpc-active' ).length ) {
			$install_pages.addClass( 'mpc-active' );
			$pages_settings.addClass( 'mpc-active' );
		} else {
			$install_pages.removeClass( 'mpc-active' );
			$pages_settings.removeClass( 'mpc-active' );
		}
	} );

	$pages_filter.on( 'click', 'a:not(.mpc-active)', function() {
		var $this   = $( this ),
			_filter = this.hash == '#all' ? '' : '.mpc-template--' + this.hash.substring( 1 );

		$( 'a', $pages_filter ).removeClass( 'mpc-active' );
		$this.addClass( 'mpc-active' );

		$( '.mpc-page-template', $pages_list ).addClass( 'mpc-hide' );

		setTimeout( function() {
			$( '.mpc-page-template:not(' + _filter + ')', $pages_list ).css( 'display', 'none' ).removeClass( 'mpc-active' );

			if ( $pages_list.find( '.mpc-preset.mpc-active' ).length ) {
				$install_pages.addClass( 'mpc-active' );
				$pages_settings.addClass( 'mpc-active' );
			} else {
				$install_pages.removeClass( 'mpc-active' );
				$pages_settings.removeClass( 'mpc-active' );
			}

			$( '.mpc-page-template' + _filter, $pages_list ).css( 'display', '' );
			setTimeout( function() {
				$( '.mpc-page-template' + _filter, $pages_list ).removeClass( 'mpc-hide' );
			}, 20 );
		}, 250 );
	} );

	$install_pages.on( 'click', function() {
		if ( ! $install_pages.is( '.mpc-active' ) ) {
			return;
		}

		$panel_cover.addClass( 'mpc-active' );

		var _list = [];

		$pages_list.find( '.mpc-preset.mpc-active' ).each( function() {
			_list.push( this.getAttribute( 'data-preset' ) );
		} );

		$error_response.html( '' );

		$.ajax( {
			url:    ajaxurl,
			method: 'POST',
			data:   {
				action:        'mpc_import_pages_templates',
				templates_ids: _list,
				panel_id:      $pages_settings.val(),
				_wpnonce:      _wpnonce
			}
		} ).done( function( response ) {
			var $selected_templates = $pages_list.find( '.mpc-preset.mpc-active' );

			if ( ! response.success ) {
				window.mpc.templates_error();

				if ( typeof response.data != 'undefined' ) {
					for ( var _template in response.data ) {
						$selected_templates = $selected_templates.not( $selected_templates.filter( '[data-preset="' + response.data[ _template ] + '"]' ) );
					}

					$error_response.html( response.data );
				}

			} else {
				window.mpc.templates_success();
			}

			$selected_templates.addClass( 'mpc-installed' ).removeClass( 'mpc-active' );
		} );
	} );
} )( jQuery );