/*----------------------------------------------------------------------------*\
	WC PRODUCT SHORTCODE - Panel
\*----------------------------------------------------------------------------*/
// ToDo: Dependency
(function( $ ) {
	"use strict";

	var $popup      = $( '#vc_ui-panel-edit-element' ),
	    _hide_class = 'vc_dependent-hidden';

	function section_dependency( _dependencies, _value ) {
		if( typeof _dependencies === 'undefined' ) {
			return;
		}

		_dependencies.forEach( function( _el ) {
			var $section  = $popup.find( '[data-vc-shortcode-param-name="' + _el + '"]' ),
			    $siblings = $section.siblings( '.mpc-vc-indent' );

			if( _value === true ) {
				$siblings.addClass( _hide_class );
				$section.addClass( _hide_class );
			} else {
				$siblings.removeClass( _hide_class );
				$section.removeClass( _hide_class );
			}
		} );
	}

	function tab_dependency( _group_name, _hide ) {
		$.each( $popup.find( '[data-vc-ui-element="panel-tabs-controls"] li' ), function() {
			var $this = $( this );

			if( $this.find( 'button' ).text().indexOf( _group_name ) > -1 ) {
				if( _hide === true ) {
					$this.addClass( _hide_class );
				} else {
					$this.removeClass( _hide_class );
				}
			}
		} );
	}

	function buttons_dependency( _value ) {
		var  _dependencies = {
			    'wishlist': [ 'buttons_wcwl_icon_divider' ],
			    'lightbox': [ 'buttons_lb_icon_divider' ],
			    'url': [ 'buttons_url_icon_divider' ]
			};

		for ( var _el in _dependencies ) {
			if ( _dependencies.hasOwnProperty( _el ) ) {
				section_dependency( _dependencies[ _el ], true );
			}
		}

		_value = _value.split( ',' );

		_value.forEach( function( _el ) {
			section_dependency( _dependencies[ _el ], false );
		} );
	}

	function layout_dependency( _el_main, _el_hover, _el_thumb, _el_thumb_hover ) {
		var	_enabled_elements = [],
			_dependencies = {
				'title' : [ 'title_font_divider', 'title_margin_divider' ],
				'price' : [ 'price_font_divider', 'price_margin_divider' ],
				'categories' : [ 'tax_font_divider', 'tax_margin_divider' ],
				'rating' : [ 'rating_section_divider', 'rating_value_section_divider', 'rating_margin_divider' ],
				'atc_button' : []
			},
			_atc_tab = $popup.find( '[data-vc-shortcode-param-name="mpc_wc_add_to_cart__preset"]' ).data( 'param_settings' ).group;

		tab_dependency( _atc_tab, true );
		for ( var _el in _dependencies ) {
			if ( _dependencies.hasOwnProperty( _el ) ) {
				section_dependency( _dependencies[ _el ], true );
			}
		}

		_el_main = _el_main.split( ',' );
		_el_hover = _el_hover.split( ',' );
		_el_thumb = _el_thumb.split( ',' );
		_el_thumb_hover = _el_thumb_hover.split( ',' );

		if( _el_main.length > 0 ) {
			_el_main.forEach( function( _el ) {
				if( _el != '' && _enabled_elements.indexOf( _el ) == -1 ) {
					_enabled_elements.push( _el );
				}
			} );
		}
		if( _el_hover.length > 0 ) {
			_el_hover.forEach( function( _el ) {
				if ( _el != '' && _enabled_elements.indexOf( _el ) == -1 ) {
					_enabled_elements.push( _el );
				}
			} );
		}
		if( _el_thumb.length > 0 ) {
			_el_thumb.forEach( function( _el ) {
				if ( _el != '' && _enabled_elements.indexOf( _el ) == -1 ) {
					_enabled_elements.push( _el );
				}
			} );
		}
		if( _el_thumb_hover.length > 0 ) {
			_el_thumb_hover.forEach( function( _el ) {
				if( _el != '' && _enabled_elements.indexOf( _el ) == -1 ) {
					_enabled_elements.push( _el );
				}
			} );
		}

		_enabled_elements.forEach( function( _el ) {
			if( _el != 'atc_button' ) {
				section_dependency( _dependencies[ _el ], false );
			} else {
				tab_dependency( _atc_tab, false );
			}
		});
	}

	$popup.on( 'mpc.render', function() {
		if( $popup.attr( 'data-vc-shortcode' ) != 'mpc_wc_product' ) {
			return '';
		}

		var $el_main        = $popup.find( '[name="main_elements"]' ),
			$el_hover       = $popup.find( '[name="hover_elements"]' ),
			$el_thumb       = $popup.find( '[name="thumb_elements"]' ),
			$el_thumb_hover = $popup.find( '[name="thumb_hover_elements"]' ),
		    $buttons        = $popup.find( '[name="buttons_list"]' );

		$buttons.on( 'change', function() {
			buttons_dependency( $buttons.val() );
			layout_dependency( $el_main.val(), $el_hover.val(), $el_thumb.val(), $el_thumb_hover.val() );
		} );

		$el_main.on( 'change', function() {
			layout_dependency( $el_main.val(), $el_hover.val(), $el_thumb.val(), $el_thumb_hover.val() );
		});
		$el_hover.on( 'change', function() {
			layout_dependency( $el_main.val(), $el_hover.val(), $el_thumb.val(), $el_thumb_hover.val() );
		});
		$el_thumb.on( 'change', function() {
			layout_dependency( $el_main.val(), $el_hover.val(), $el_thumb.val(), $el_thumb_hover.val() );
		});
		$el_thumb_hover.on( 'change', function() {
			layout_dependency( $el_main.val(), $el_hover.val(), $el_thumb.val(), $el_thumb_hover.val() );
		});

		// Triggers
		setTimeout( function() {
			$buttons.trigger( 'change' );
		}, 500 );
	} );
})( jQuery );
