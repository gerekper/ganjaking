/*----------------------------------------------------------------------------*\
	CHART SHORTCODE
\*----------------------------------------------------------------------------*/
( function( $ ) {
	"use strict";

	function set_defaults( options, leave_empty ) {
		if ( options.type == undefined ) {
			options.type = 'color';

			if ( leave_empty ) {
				options.color = 'transparent';
			} else {
				options.color = '#aaaaaa';
			}
		} else if ( options.type == 'color' ) {
			if ( options.color == undefined || options.color == '' ) {
				options.type = 'color';

				if ( leave_empty ) {
					options.color = 'transparent';
				} else {
					options.color = '#aaaaaa';
				}
			}
		} else if ( options.type == 'image' ) {
			if ( options.image == undefined || options.image == '' ) {
				options.type = 'color';

				if ( leave_empty ) {
					options.color = 'transparent';
				} else {
					options.color = '#aaaaaa';
				}
			}
		} else if ( options.type == 'gradient' ) {
			if ( options.gradient == undefined || options.gradient == '' ) {
				options.type = 'color';

				if ( leave_empty ) {
					options.color = 'transparent';
				} else {
					options.color = '#aaaaaa';
				}
			}
		}

		return options;
	}

	function create_background( _options, _context, _canvas, $wrap ) {
		var _width = _options.width,
			_x = _canvas.width * .5,
			_y = _canvas.height * .5,
			_radius = _canvas.width * .5;

		if ( _options.type == 'color' ) {
			_options.loaded      = true;
			_options.strokeStyle = _options.color;

			$wrap.trigger( 'mpc.loaded' );
		} else if ( _options.type == 'image' ) {
			var _image = new Image();
			_image.onload = function() {
				_options.loaded      = true;
				_options.strokeStyle = _context.createPattern( _image, _options.repeat );

				$wrap.trigger( 'mpc.loaded' );
			};
			_image.src = _options.image;
		} else if ( _options.type == 'gradient' ) {
			var _values = _options.gradient.split( '||' );

			if ( _values.length == 5 ) {
				var _stops = _values[ 2 ].split( ';' ),
					_gradient;

				if ( _values[ 4 ] == 'linear' ) {
					var _angles = parseInt( _values[ 3 ] ) + 180,
						_start_point = arc_to_coords( _x, _y, _radius + _width * .5, _angles ),
						_end_point   = arc_to_coords( _x, _y, _radius + _width * .5, _angles + 180 > 360 ? _angles - 180 : _angles + 180 );

					_gradient = _context.createLinearGradient( _start_point.x, _start_point.y, _end_point.x, _end_point.y );
				} else {
					_gradient = _context.createRadialGradient( _x, _y, 0, _x, _y, _radius + _width * .5 );
				}

				_gradient.addColorStop( _stops[ 0 ] * .01, _values[ 0 ] );
				_gradient.addColorStop( _stops[ 1 ] * .01, _values[ 1 ] );

				_options.loaded      = true;
				_options.strokeStyle = _gradient;

				$wrap.trigger( 'mpc.loaded' );
			}
		}
	}

	function arc_to_coords( _x, _y, _radius, _angle ) {
		_angle = ( _angle - 90 ) * Math.PI / 180;

		return {
			x: _x + ( _radius * Math.cos( _angle ) ),
			y: _y + ( _radius * Math.sin( _angle ) )
		}
	}

	function animate_chart( $wrap, _context, _canvas, _options ) {
		var _offset = - Math.PI * .5,
			_radians = Math.PI / 180,
			_circle = Math.PI * 2,
			_width = _options.width,
			_x = _canvas.width * .5,
			_y = _canvas.height * .5,
			_radius = _canvas.width * .5 - _width * .5,
			_with_marker = _options.marker !== false,
			_duration = 3000,
			_angle,
			_coords;

		if ( _options.fast != undefined ) {
			_duration = 0;
		}

		$wrap.velocity( {
			tween: [ 0, 1 ]
		}, {
			easing: [ 0.25, 0.1, 0.25, 1.0 ],
			duration: _duration * _options.value,
			progress: function( elements, complete, remaining, start, tweenValue ) {
				_angle  = ( _options.value - _options.value * tweenValue ) * 360;
				_coords = arc_to_coords( _x, _y, _radius, _angle );

				_context.clearRect( 0, 0, _canvas.width, _canvas.height );

				// Background
				_context.lineWidth = _options.width - .5;

				_context.strokeStyle = _options.chart_back.strokeStyle;
				_context.beginPath();
				_context.arc( _x, _y, _radius, 0, _circle, false );
				_context.stroke();

				// Foreground
				_context.lineWidth = _options.width;

				_context.strokeStyle = _options.chart_front.strokeStyle;
				_context.beginPath();
				_context.arc( _x, _y, _radius, _offset, _angle * _radians + _offset, false );
				_context.stroke();

				// Marker
				if ( _with_marker ) {
					_options.marker.css( {
						left: _coords.x,
						top:  _coords.y
					} );
				}
			}
		});
	}

	function init_shortcode( $chart, fast_init ) {
		var $marker  = $chart.find( '.mpc-chart__marker' ),
			$box     = $chart.find( '.mpc-chart__box' ),
			$inner   = $chart.find( '.mpc-chart__inner_circle' ),
			$outer   = $chart.find( '.mpc-chart__outer_circle' ),
			_options = $chart.data( 'options' ),
			_canvas  = $chart.find( 'canvas.mpc-chart' )[ 0 ],
			_context = _canvas.getContext( '2d' ),
			_circles_radius;

		if ( typeof _options == 'undefined') {
			return;
		}

		if ( $chart.is( '.mpc-init--fast' ) || fast_init ) {
			_options.fast = true;
		}

		_options.radius = parseInt( _options.radius );
		_options.value  = parseInt( _options.value ) / 100;
		_options.width  = parseInt( _options.width );

		_options.default_radius = _options.radius;

		if ( $chart.width() < _options.radius * 2 ) {
			_options.radius = Math.floor( $chart.width() / 2 );

			$box.width( _options.radius * 2 );

			if ( $inner.length ) {
				_circles_radius = _options.inner_radius * ( _options.radius / _options.default_radius ) * 2;

				$inner.css( { 'width': _circles_radius, 'height': _circles_radius } );
			}

			if ( $outer.length ) {
				_circles_radius = _options.outer_radius * ( _options.radius / _options.default_radius ) * 2;

				$outer.css( { 'width': _circles_radius, 'height': _circles_radius } );
			}
		}

		if ( _options.width >= _options.radius ) {
			_options.width = _options.radius - 0.01;
		}

		_options.marker = $marker.length ? $marker : false;

		_canvas.setAttribute( 'width', _options.radius * 2 );
		_canvas.setAttribute( 'height', _options.radius * 2 );

		_context.lineWidth = _options.width;

		_options.chart_back  = _options.chart_back != undefined ? _options.chart_back : {};
		_options.chart_front = _options.chart_front != undefined ? _options.chart_front : {};

		_options.chart_back.width  = _options.width;
		_options.chart_back.loaded = false;

		_options.chart_front.width  = _options.width;
		_options.chart_front.loaded = false;

		_options.chart_back  = set_defaults( _options.chart_back, true );
		_options.chart_front = set_defaults( _options.chart_front, false );

		$chart.on( 'mpc.loaded', function() {
			if ( _options.chart_front.loaded && _options.chart_back.loaded ) {
				var $parent   = $chart.parents( '.mpc-container' );

				$chart.trigger( 'mpc.inited' );

				if( $parent.length ) {
					if ( $chart.is( '.mpc-parent--init' ) ) {
						$parent.one( 'mpc.parent-init', function() {
							animate_chart( $box, _context, _canvas, _options );
						} );
					} else {
						animate_chart( $box, _context, _canvas, _options );
					}
				} else if ( $chart.is( '.mpc-waypoint--init' ) ) {
					animate_chart( $box, _context, _canvas, _options );
				} else {
					$chart.one( 'mpc.waypoint', function() {
						animate_chart( $box, _context, _canvas, _options );
					} );
				}
			}
		} );

		create_background( _options.chart_back, _context, _canvas, $chart );
		create_background( _options.chart_front, _context, _canvas, $chart );

		_mpc_vars.$window.on( 'mpc.resize', function() {
			var _radius = _options.radius;

			if ( $chart.width() < _options.radius * 2 ) {
				_options.radius = Math.floor( $chart.width() / 2 );

				$box.width( _options.radius * 2 );
			} else if ( _options.radius < _options.default_radius ) {
				_options.radius = Math.floor( Math.min( $chart.width() / 2, _options.default_radius ) );

				$box.width( _options.radius * 2 );
			}

			if ( _radius != _options.radius ) {
				_options.fast = true;

				_canvas.setAttribute( 'width', _options.radius * 2 );
				_canvas.setAttribute( 'height', _options.radius * 2 );

				animate_chart( $box, _context, _canvas, _options );

				if ( $inner.length ) {
					_circles_radius = _options.inner_radius * ( _options.radius / _options.default_radius ) * 2;

					$inner.css( { 'width': _circles_radius, 'height': _circles_radius } );
				}

				if ( $outer.length ) {
					_circles_radius = _options.outer_radius * ( _options.radius / _options.default_radius ) * 2;

					$outer.css( { 'width': _circles_radius, 'height': _circles_radius } );
				}
			}
		} );
	}

	if ( typeof window.InlineShortcodeView != 'undefined' ) {
		window.InlineShortcodeView_mpc_chart = window.InlineShortcodeView.extend( {
			rendered: function() {
				var $chart = this.$el.find( '.mpc-chart-wrap' );

				$chart.addClass( 'mpc-waypoint--init' );

				_mpc_vars.$body.trigger( 'mpc.icon-loaded', [ $chart ] );
				_mpc_vars.$body.trigger( 'mpc.font-loaded', [ $chart ] );
				_mpc_vars.$body.trigger( 'mpc.inited', [ $chart ] );

				init_shortcode( $chart, false );

				window.InlineShortcodeView_mpc_chart.__super__.rendered.call( this );
			}
		} );
	}

	var $charts = $( '.mpc-chart-wrap' );

	$charts.each( function() {
		var $chart = $( this );

		$chart.one( 'mpc.init', function () {
			init_shortcode( $chart, false );
		} );

		$chart.one( 'mpc.init-fast', function () {
			init_shortcode( $chart.parents( '.mpc-carousel__wrapper' ).find( '.slick-cloned .mpc-chart-wrap[data-id="' + $chart.attr( 'data-id' ) + '"]' ), true );
		} );
	} );
} )( jQuery );
