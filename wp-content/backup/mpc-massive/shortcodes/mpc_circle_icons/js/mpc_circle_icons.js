/*----------------------------------------------------------------------------*\
	CIRCLE ICONS SHORTCODE
\*----------------------------------------------------------------------------*/
( function( $ ) {
	"use strict";

	function arc_to_coords( angle ) {
		angle = ( angle - 90 ) * Math.PI / 180;

		return {
			x: _center + ( _radius * Math.cos( angle ) ),
			y: _center + ( _radius * Math.sin( angle ) )
		}
	}

	function next_slide( $icons, $columns, index, _effect, $circle_icon ) {
		var $active_column = $icons.eq( index ).parents( '.mpc-icon-column' );

		$columns.removeClass( 'mpc-active' );
		$active_column.addClass( 'mpc-active' );

		if ( _effect != undefined ) {
			clearInterval( $circle_icon._effect_interval );

			$active_column.find( '.mpc-icon' )
				.velocity( 'stop', true )
				.velocity( _effect );

			$circle_icon._effect_interval = setInterval( function() {
				$active_column.find( '.mpc-icon' )
					.velocity( 'stop', true )
					.velocity( _effect );
			}, 1200 );
		}

		return ++index;
	}

	function init_shortcode( $circle_icon ) {
		var $icons           = $circle_icon.find( '.mpc-icon' ),
			$columns         = $circle_icon.find( '.mpc-icon-column' ),
			_effect          = $circle_icon.attr( 'data-effect' ),
			_active_icon     = 1,
			_max_icons       = $icons.length,
			_slideshow_delay = 0,
			_angle           = 0,
			_angle_gap       = 360 / $icons.length,
			_interval;

		$circle_icon._effect_interval = null;

		if ( $circle_icon.attr( 'data-active-item' ) != undefined ) {
			_active_icon = parseInt( $circle_icon.attr( 'data-active-item' ) ) - 1;
		}

		if ( $circle_icon.attr( 'data-delay' ) != undefined ) {
			_slideshow_delay = parseInt( $circle_icon.attr( 'data-delay' ) );
			_slideshow_delay = Math.max( Math.min( _slideshow_delay, 15000 ), 1000 );
		}

		$icons.each( function() {
			var $icon   = $( this ),
				_coords = arc_to_coords( _angle );

			$icon.css( {
				left: _coords.x + '%',
				top:  _coords.y + '%'
			} );

			$icon.imagesLoaded().always( function() {
				$icon.css( {
					marginLeft: $icon.outerWidth() * -.5,
					marginTop:  $icon.outerHeight() * -.5
				} );
			} );

			_angle += _angle_gap;
		} );

		_mpc_vars.$window.on( 'load', function() {
			// Added in case of slow icons/images load
			$icons.each( function() {
				var $icon = $( this );

				$icon.css( {
					marginLeft: $icon.outerWidth() * -.5,
					marginTop:  $icon.outerHeight() * -.5
				} );
			} );
		} );

		$icons.on( 'click mouseenter', function() {
			var $active_column = $( this ).parents( '.mpc-icon-column' );

			if ( window.vc_mode == 'admin_frontend_editor' ) {
				$active_column = $( this ).parents( '.vc_mpc_icon_column' );
			}

			next_slide( $icons, $columns, $active_column.index(), _effect, $circle_icon );

			_active_icon = $active_column.index();
		} );

		next_slide( $icons, $columns, _active_icon, _effect, $circle_icon );

		$columns.removeClass( 'mpc-parent-hover' );

		if ( _slideshow_delay != 0 ) {
			_active_icon = ( _active_icon + 1 ) % _max_icons;

			_interval = setInterval( function() {
				_active_icon = next_slide( $icons, $columns, _active_icon, _effect, $circle_icon ) % _max_icons;
			}, _slideshow_delay );

			$circle_icon.on( 'click mouseenter', function() {
				clearInterval( _interval );
			} ).on( 'mouseleave', function() {
				_interval = setInterval( function() {
					_active_icon = next_slide( $icons, $columns, _active_icon, _effect, $circle_icon ) % _max_icons;
				}, _slideshow_delay );
			} );
		}

		$circle_icon.trigger( 'mpc.inited' );
	}

	if ( typeof window.InlineShortcodeViewContainer != 'undefined' ) {
		window.InlineShortcodeView_mpc_circle_icons = window.InlineShortcodeViewContainer.extend( {
			initialize: function( params ) {
				this.listenTo( this.model, 'mpc:forceRender', this.rendered );

				window.InlineShortcodeView_mpc_circle_icons.__super__.initialize.call( this, params );
			},
			rendered: function() {
				var $icons = this.$el.find( '.mpc-circle-icons' );

				$icons.addClass( 'mpc-waypoint--init' );

				_mpc_vars.$body.trigger( 'mpc.icon-loaded', [ $icons ] );
				_mpc_vars.$body.trigger( 'mpc.font-loaded', [ $icons ] );
				_mpc_vars.$body.trigger( 'mpc.inited', [ $icons ] );

				setTimeout( function() {
					init_shortcode( $icons );
				}, 250 );

				window.InlineShortcodeView_mpc_circle_icons.__super__.rendered.call( this );
			}
		} );
	}

	var $circle_icons = $( '.mpc-circle-icons' ),
		_center = 50,
		_radius = 45;

	$circle_icons.each( function() {
		var $circle_icon = $( this );

		$circle_icon.one( 'mpc.init', function () {
			init_shortcode( $circle_icon );
		} );
	} );
} )( jQuery );


