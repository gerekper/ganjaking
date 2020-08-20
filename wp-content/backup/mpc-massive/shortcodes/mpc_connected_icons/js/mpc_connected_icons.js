/*----------------------------------------------------------------------------*\
	CONNECTED ICONS SHORTCODE
\*----------------------------------------------------------------------------*/
(function( $ ) {
	"use strict";

	function init_connector() {
		return {
			size: {
				width: 0,
				height: 0
			},
			position: {
				top: 0,
				left: 0
			},
			margin: {
				top: 0,
				left: 0
			}
		};
	}

	function get_dimensions( $item ) {
		return {
			size: {
				width: $item.outerWidth( false ),
				height: $item.outerHeight( false )
			},
			border: {
				left:   parseInt( $item.css( 'border-left-width' ).replace( 'px', '' ) ),
				right:  parseInt( $item.css( 'border-right-width' ).replace( 'px', '' ) ),
				top:    parseInt( $item.css( 'border-top-width' ).replace( 'px', '' ) ),
				bottom: parseInt( $item.css( 'border-bottom-width' ).replace( 'px', '' ) )
			},
			offset: $item.offset(),
			position: $item.position()
		};
	}

	function wrap_columns( $icons ) {
		$icons.find( '.mpc-icon-column' ).each( function() {
			$( this ).wrap( '<div class="mpc-connected-icons__item">' );
		} );
	}

	function draw_connections( $icons ) {
		var $line        = $icons.find( '.mpc-connected-icons__line' ),
		    _target      = $icons.attr( 'data-target' ) != '' ? $icons.attr( 'data-target' ) : 'icon',
		    _layout      = $icons.attr( 'data-layout' ) != '' ? $icons.attr( 'data-layout' ) : 'vertical',
		    $target      = _target == 'box' ? $icons.find( '.mpc-icon-column' ) : $icons.find( '.mpc-icon-column > .mpc-icon' ),
		    _items_count = $target.length - 1;

		$target.each( function( _index ) {
			if( _index >= _items_count ) return true;

			var $item        = $( this ),
			    $item_parent = $item.parents( '.mpc-connected-icons__item' ),
			    $item_next   = _target == 'box' ? $item_parent.next().find( '.mpc-icon-column' ) : $item_parent.next().find( '.mpc-icon' ),
			    $connector   = $line.clone(),
			    _line_size   = _layout == 'horizontal' ? $line.height() : $line.width(),
			    _item        = get_dimensions( $item ),
			    _item_next   = get_dimensions( $item_next ),
			    _connector   = init_connector(),
			    _css         = {},
			    _animation   = {};

			if( _layout == 'horizontal' ) {
				_connector.size.width = _item_next.offset.left - _item.offset.left - _item.size.width;

				_connector.margin.left = _item.border.right;
				_connector.position.top = ( _item.size.height - _line_size ) * .5 - _item.border.top;
				_connector.position.left = false;

				_css = {
					top: parseInt( _connector.position.top ),
					width: parseInt( _connector.size.width ),
					marginLeft: parseInt( _connector.margin.left )
				};

				_animation = {
					width: parseInt( _connector.size.width )
				};
			} else {
				_connector.size.height = _item_next.offset.top - _item.offset.top - _item.size.height;

				_connector.margin.top = _item.border.top;
				_connector.position.left = ( _item.size.width - _line_size ) * .5 - _item.border.left;

				_connector.position.top = false;

				_css = {
					left: parseInt( _connector.position.left ),
					height: parseInt( _connector.size.height ),
					marginTop: parseInt( _connector.margin.top )
				};

				_animation = {
					height: parseInt( _connector.size.height )
				};
			}

			$connector.css( _css ).appendTo( $item );
			$connector.find( 'span' ).velocity( _animation, 300 );
		} );
	}

	function responsive( $icons ) {
		var _cols = $icons.data( 'ci-cols' );
		$icons.find( '.mpc-connected-icons__item .mpc-connected-icons__line' ).remove();

		if ( _cols >= 3 && _mpc_vars.breakpoints.custom( '(min-width: 992px)' )
			|| _mpc_vars.breakpoints.custom( '(min-width: 769px)' ) ) {
			draw_connections( $icons );
		}
	}

	function init_shortcode( $icons ) {
		var _cols = $icons.data( 'ci-cols' );
		if ( _cols >= 3 && _mpc_vars.breakpoints.custom( '(min-width: 992px)' )
			|| _mpc_vars.breakpoints.custom( '(min-width: 769px)' ) ) {
			draw_connections( $icons );
		}

		$icons.trigger( 'mpc.inited' );
	}

	function delay_init( $icons ) {
		if ( $.fn.imagesLoaded ) {
			$icons.imagesLoaded().always( function() {
				draw_connections( $icons );
			} );

			$icons.trigger( 'mpc.inited' );
		} else {
			setTimeout( function() {
				delay_init( $icons );
			}, 50 );
		}
	}

	function frontend_wrap_columns( $icons ) {
		$icons.find( '.vc_mpc_icon_column' ).addClass( 'mpc-connected-icons__item' );
	}

	if( typeof window.InlineShortcodeViewContainer != 'undefined' ) {
		var $body = $( 'body' );

		window.InlineShortcodeView_mpc_connected_icons = window.InlineShortcodeViewContainer.extend( {
			initialize: function( params ) {
				_.bindAll( this, 'holdActive' );
				window.InlineShortcodeView_mpc_connected_icons.__super__.initialize.call( this, params );
				this.parent_view = vc.shortcodes.get( this.model.get( 'parent_id' ) ).view;

				this.listenTo( this.model, 'mpcRender', this.rendered );
			},
			rendered: function() {
				var $icons = this.$el.find( '.mpc-connected-icons' );

				$icons.addClass( 'mpc-waypoint--init' );

				$body.trigger( 'mpc.icon-loaded', [ $icons ] );
				$body.trigger( 'mpc.font-loaded', [ $icons ] );
				$body.trigger( 'mpc.inited', [ $icons ] );

				setTimeout( function() {
					frontend_wrap_columns( $icons );
					var _cols = $icons.data( 'ci-cols' );
					if( _cols >= 3 && _mpc_vars.breakpoints.custom( '(min-width: 992px)' )
						|| _mpc_vars.breakpoints.custom( '(min-width: 769px)' ) ) {
						init_shortcode( $icons );
					} else {
						$icons.trigger( 'mpc.inited' );
					}
				}, 250 );

				window.InlineShortcodeView_mpc_connected_icons.__super__.rendered.call( this );
			},
			render: function() {
				window.InlineShortcodeView_mpc_connected_icons.__super__.render.call( this );

				this.content().addClass( 'vc_element-container' );
				this.$el.addClass( 'vc_container-block' );

				return this;
			},
			beforeUpdate: function() {
				this.$el.find( '.mpc-icon-column .mpc-connected-icons__line' ).remove();
			}
		} );
	}

	var $connected_icons = $( '.mpc-connected-icons' );

	wrap_columns( $connected_icons );

	$connected_icons.each( function() {
		var $connected_icon = $( this );

		$connected_icon.one( 'mpc.init', function() {
			if( _mpc_vars.breakpoints.custom( '(min-width: 769px)' ) ) {
				delay_init( $connected_icon );
			} else {
				$connected_icon.trigger( 'mpc.inited' );
			}
		} );
	} );

	_mpc_vars.$window.on( 'mpc.resize', function() {
		$.each( $connected_icons, function() {
			responsive( $( this ) );
		} );
	} );
})( jQuery );


