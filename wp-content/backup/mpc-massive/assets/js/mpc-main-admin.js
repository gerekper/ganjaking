/*----------------------------------------------------------------------------*\
	MAIN
\*----------------------------------------------------------------------------*/

/*----------------------------------------------------------------------------*\
	VISUAL COMPOSER - HINTS
\*----------------------------------------------------------------------------*/
( function( $ ) {
	"use strict";

	var $panel = $( '#vc_ui-panel-edit-element' );

	$panel.on( 'vcPanel.shown', function() {
		var $params = $panel.find( '.vc_shortcode-param' );

		$params.each( function() {
			var $param    = $( this ),
				_settings = $param.data( 'param_settings' ),
				_tooltip  = '';

			if ( typeof _settings != 'undefined' && ( typeof _settings.tooltip != 'undefined' || typeof _settings.tooltip_title != 'undefined' ) ) {
				_tooltip += '<span class="mpc-hint">?<span class="mpc-hint-content"><span class="mpc-hint-triangle"></span>';
					if ( typeof _settings.tooltip_title != 'undefined' ) {
						_tooltip += '<strong class="mpc-hint-title">' + _settings.tooltip_title + '</strong>';
					}
					if ( typeof _settings.tooltip != 'undefined' ) {
						_tooltip += _settings.tooltip;
					}
				_tooltip += '</span></span>';

				$param.find( '.wpb_element_label' )
					.addClass( 'mpc-with-tooltip' )
					.append( _tooltip );
			}
		} );

		$panel.on( 'mouseenter', '.mpc-hint', function() {
			var $hint         = $( this ),
				_panel_center = $panel.offset().left + parseInt( $panel.width() / 2 );

			$hint.removeClass( 'mpc-hint-right mpc-hint-left' );

			if( _panel_center > $hint.offset().left ) {
				$hint.addClass( 'mpc-hint-left' );
			} else {
				$hint.addClass( 'mpc-hint-right' )
			}
		} );
	} );
} )( jQuery );

/*----------------------------------------------------------------------------*\
	VISUAL COMPOSER - COPY/PASTE/CLEAR
\*----------------------------------------------------------------------------*/
( function( $ ) {
	"use strict";

	if ( typeof localStorage == 'undefined' ) {
		return;
	}

	function copy_shortcode() {
		if ( typeof vc.active_panel && typeof vc.active_panel.model ) {
			localStorage.setItem( 'mpc_shortcode', vc.shortcodes._getShortcodeContent( vc.active_panel.model ) );
		} else {
			console.warn( 'Please open popup to copy shortcode content.' );
		}
	}

	function paste_shortcode() {
		if ( typeof vc.active_panel && typeof vc.active_panel.model ) {
			if ( localStorage.getItem( 'mpc_shortcode' ) != '' ) {
				vc.shortcodes.createFromString( localStorage.getItem( 'mpc_shortcode' ), vc.active_panel.model );
			} else {
				console.warn( 'Shortcode content is empty.' );
			}
		} else {
			console.warn( 'Please open popup to paste shortcode content.' );
		}
	}

	function clear_shortcode() {
		if ( typeof vc.active_panel && typeof vc.active_panel.model ) {
			var _id = vc.active_panel.model.get( 'id' ),
				_children = vc.shortcodes.where( { parent_id: _id } );

			_children.forEach( function( child ) {
				child.destroy();
			} );
		} else {
			console.warn( 'Please open popup to clear shortcode content.' );
		}
	}

	_mpc_vars.$window.on( 'keydown', function( event ) {
		if ( event.altKey && event.shiftKey && event.ctrlKey ) {
			if ( event.keyCode == '67' ) { // C
				console.info( 'Copying shortcode.' );
				copy_shortcode();
			} else if ( event.keyCode == '86' ) { // V
				console.info( 'Pasting shortcode.' );
				paste_shortcode();
			} else if ( event.keyCode == '88' ) { // X
				console.info( 'Clearing shortcode.' );
				clear_shortcode();
			}
		}
	} );
} )( jQuery );

/*----------------------------------------------------------------------------*\
	VISUAL COMPOSER - DIVIDERS
\*----------------------------------------------------------------------------*/
( function( $ ) {
	"use strict";

	var $panel = $( '#vc_ui-panel-edit-element' );

	/* Wrap dividers and fields in sections */
	$panel.on( 'vcPanel.shown', function() {
		var $dividers = $( '.vc_wrapper-param-type-mpc_divider' );

		if( typeof tinyMCE !== 'undefined' ) {
			if ( tinyMCE.get( 'wpb_tinymce_content' ) ) {
				var _formated_content = tinyMCE.get( 'wpb_tinymce_content' ).getContent();
				_formated_content = _formated_content.replace( /><p>\s<\/p>/g, '>' );
			}
			tinyMCE.EditorManager.execCommand( 'mceRemoveEditor', true, 'wpb_tinymce_content' );
		}

		$dividers.each( function() {
			var $divider = $( this ),
				$fields  = $divider.nextUntil( '.vc_wrapper-param-type-mpc_divider, .vc_shortcode-param.mpc-no-wrap' ),
				$wrapper = $( '<div class="mpc-vc-wrapper' + ( $divider.is( '.mpc-vc-highlight' ) ? ' mpc-vc-highlight' : '' ) + '" />' ),
				$indent  = $( '<div class="mpc-vc-indent" />' );

			$divider.before( $wrapper );
			$wrapper.append( $divider );

			if ( $divider.is( '[data-vc-shortcode-param-name*="border_divider"], [data-vc-shortcode-param-name*="padding_divider"], [data-vc-shortcode-param-name*="margin_divider"]' ) ) {
				// $fields.find( 'input:not(.mpc-vc-css):not(.mpc-extra-field), select' ).addClass( 'mpc-ignored-field' );
				$fields.filter( ':not(.mpc-extra-field)' ).find( 'input:not(.mpc-vc-css), select' ).addClass( 'mpc-ignored-field' );
			}

			if ( $fields.length ) {
				$indent.append( $fields );
				$wrapper.append( $indent );
			}
		} );

		if( typeof tinyMCE !== 'undefined' ) {
			tinyMCE.EditorManager.execCommand( 'mceAddEditor', true, 'wpb_tinymce_content' );
			if ( typeof _formated_content !== typeof undefined ) {
				tinyMCE.get( 'wpb_tinymce_content' ).setContent( _formated_content );
			}
		}

		$panel.trigger( 'mpc.render' );
	} );

	/* Hide indented sections on param update */
	$panel.on( 'change', '.wpb_el_type_mpc_divider', function() {
		var $divider = $( this );

		if ( $divider.is( '.vc_dependent-hidden' ) ) {
			$divider.parent( '.mpc-vc-wrapper' ).addClass( 'vc_dependent-hidden' );
		} else {
			$divider.parent( '.mpc-vc-wrapper' ).removeClass( 'vc_dependent-hidden' );
		}
	} );

	/* Hide indented sections on panel init */
	$panel.on( 'mpc.render', function() {
		$( '.wpb_el_type_mpc_divider' ).each( function() {
			var $divider = $( this );

			if ( $divider.is( '.vc_dependent-hidden' ) ) {
				$divider.parent( '.mpc-vc-wrapper' ).addClass( 'vc_dependent-hidden' );
			} else {
				$divider.parent( '.mpc-vc-wrapper' ).removeClass( 'vc_dependent-hidden' );
			}
		} );
	} );
} )( jQuery );

/*----------------------------------------------------------------------------*\
	VISUAL COMPOSER - FIXES
\*----------------------------------------------------------------------------*/
( function( $ ) {
	"use strict";

	var $panel            = $( '#vc_ui-panel-edit-element' ),
		$shortcodes_panel = $( '#vc_ui-panel-add-element' ),
		$shortcodes       = $shortcodes_panel.find( '.wpb-layout-element-button[data-element^="mpc_"]' ),
		$shortcodes_hook  = $shortcodes_panel.find( '.wpb-layout-element-button[data-element^="vc_wp_"]' ).first(),
		$content          = $( '#visual_composer_content' );

	$panel.on( 'vcPanel.shown', function() {
		var $html_editors = $( '.wpb-textarea_raw_html' );

		$html_editors.attr( 'rows', 6 );

		_mpc_vars.$body.css( 'overflow', 'hidden' );

		$content.find( '.mpc-active-shortcode, .mpc-last-shortcode' ).removeClass( 'mpc-active-shortcode mpc-last-shortcode' );
		$content.find( '[data-model-id="' + vc.active_panel.model.id + '"]' ).addClass( 'mpc-active-shortcode mpc-last-shortcode' );
	} );

	$panel.on( 'click', '.vc_ui-button, .vc_ui-close-button', function() {
		$panel.trigger( 'mpc.close' );

		$panel.find( '.mpc-ajax-overlay' ).hide();

		_mpc_vars.$body.css( 'overflow', '' );

		$content.find( '.mpc-active-shortcode' ).removeClass( 'mpc-active-shortcode' );
	} );

	// Sort shortcodes
	$shortcodes.sort( function( first, second ) {
		var _first_name  = first.textContent,
			_second_name = second.textContent;

		if ( _first_name > _second_name ) {
			return 1;
		} else if ( _first_name < _second_name ) {
			return -1;
		}

		return 0;
	} );

	$shortcodes_hook.before( $shortcodes );
} )( jQuery );

/*----------------------------------------------------------------------------*\
	VISUAL COMPOSER - EASY MODE
\*----------------------------------------------------------------------------*/
( function( $ ) {
	"use strict";

	function check_empty_fields() {
		var $border_css = $( '.mpc-vc-css[name$="border_css"]' );

		$border_css.each( function() {
			var $this = $( this );

			if ( $this.val().match( /border-width|border-left|border-top|border-right|border-bottom/ ) == null ) {
				$this.closest( '.mpc-vc-wrapper' ).find( '.vc_shortcode-param[data-vc-shortcode-param-name$="border_color"], .vc_shortcode-param[data-vc-shortcode-param-name$="border_divider"]' ).addClass( 'mpc-advanced-field' );
			} else {
				$this.closest( '.mpc-vc-wrapper' ).find( '.vc_shortcode-param[data-vc-shortcode-param-name$="border_color"], .vc_shortcode-param[data-vc-shortcode-param-name$="border_divider"]' ).removeClass( 'mpc-advanced-field' );
			}
		} );

		// Button...
		if ( $panel.is( '[data-vc-shortcode="mpc_button"]' ) ) {
			var $icon_effect = $( 'select[name$="icon_effect"]' );

			if ( $icon_effect.val() == 'none-none' ) {
				$icon_effect.closest( '.mpc-vc-wrapper' ).find( '.vc_shortcode-param[data-vc-shortcode-param-name$="icon_divider"], .vc_shortcode-param[data-vc-shortcode-param-name$="icon"], .vc_shortcode-param[data-vc-shortcode-param-name$="icon_color"]' ).addClass( 'mpc-advanced-field' );
			} else {
				$icon_effect.closest( '.mpc-vc-wrapper' ).find( '.vc_shortcode-param[data-vc-shortcode-param-name$="icon_divider"], .vc_shortcode-param[data-vc-shortcode-param-name$="icon"], .vc_shortcode-param[data-vc-shortcode-param-name$="icon_color"]' ).removeClass( 'mpc-advanced-field' );
			}
		}

		// Chart
		if ( $panel.is( '[data-vc-shortcode="mpc_chart"]' ) ) {
			var $sections = $( '.checkbox[name="disable_title"], .checkbox[name="disable_description"], .checkbox[name="disable_value"]' );

			$sections.each( function() {
				var $this = $( this );

				if ( $this.prop( 'checked' ) ) {
					$this.closest( '.mpc-vc-wrapper' ).children( '.wpb_el_type_mpc_divider' ).addClass( 'mpc-advanced-field' );
				} else {
					$this.closest( '.mpc-vc-wrapper' ).children( '.wpb_el_type_mpc_divider' ).removeClass( 'mpc-advanced-field' );
				}
			} );
		}
	}

	var $panel          = $( '#vc_ui-panel-edit-element' ),
		$easy_mode_wrap = $( '<label class="mpc-easy-mode-switch"><input type="checkbox" class="checkbox" value="true">' + _mpc_lang.easy_mode + '</label>' ),
		$easy_mode;

	$panel.find( '.vc_ui-panel-header-controls' ).prepend( $easy_mode_wrap );
	$easy_mode = $easy_mode_wrap.find( '.checkbox' );

	if ( _mpc_vars.$body.is( '.mpc-easy-mode-enabled' ) ) {
		$easy_mode.prop( 'checked', true );
	}

	$panel.on( 'vcPanel.shown', function() {
		$easy_mode.trigger( 'change' );

		$panel.find( '.vc_shortcode-param[data-vc-shortcode-param-name$="padding_divider"], .vc_shortcode-param[data-vc-shortcode-param-name$="margin_divider"]' ).closest( '.mpc-vc-wrapper' ).addClass( 'mpc-advanced-field' );
	} );

	$easy_mode.on( 'change', function() {
		if ( $easy_mode.prop( 'checked' ) ) {
			_mpc_vars.$body.addClass( 'mpc-easy-mode-enabled' );

			check_empty_fields();
		} else {
			_mpc_vars.$body.removeClass( 'mpc-easy-mode-enabled' );
		}
	} );

	_mpc_vars.$body.on( 'mpc.preset_loaded', function() {
		check_empty_fields();
	} )
} )( jQuery );

/*----------------------------------------------------------------------------*\
	VISUAL COMPOSER - DISABLE SECTION
\*----------------------------------------------------------------------------*/
( function( $ ) {
	"use strict";

	var $panel = $( '#vc_ui-panel-edit-element' );

	/* Wrap dividers and fields in sections */
	$panel.on( 'vcPanel.shown', function() {
		var $disablers = $panel.find( '.mpc-section-disabler .wpb_vc_param_value' ),
			$tabs = $panel.find( '.vc_edit-form-tabs-menu' );

		$disablers.on( 'change', function() {
			var $disabler_value = $( this ),
				$sections = $disabler_value.parents( '.mpc-section-disabler' ).siblings(),
				_section_id = '#' + $disabler_value.parents( '.vc_edit-form-tab' ).attr( 'id' );

			var _sub_tabs_name = $tabs.find( '[href="' + _section_id + '"]' ).text() + ':',
				$sub_tabs = $tabs.find( '.vc_edit-form-tab-control' ).filter( function() {
					return $( this ).text().indexOf( _sub_tabs_name ) === 0;
				} );

			if ( $disabler_value.is( ':checked' ) ) {
				$sections.css( 'display', 'none' );
				$sub_tabs.addClass( 'mpc-disabled' );
			} else {
				$sections.css( 'display', '' );
				$sub_tabs.removeClass( 'mpc-disabled' );
			}
		} ).trigger( 'change' );
	} );
} )( jQuery );
