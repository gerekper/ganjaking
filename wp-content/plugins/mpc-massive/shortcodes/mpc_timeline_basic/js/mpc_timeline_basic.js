/*----------------------------------------------------------------------------*\
	TIMELINE BASIC SHORTCODE
\*----------------------------------------------------------------------------*/
( function( $ ) {
	"use strict";

	var _icon_spots;

	function in_range( _r_start, _r_end, _needle ) {
		return _r_start <= _needle && _needle <= _r_end;
	}

	function spots_walker( _icon_pos, _spots, _pos_range ) {
		// ToDo: Check if the pointer is inside item range, test more variations: middle, bottom alignment etc
		var _modifier = 0;
		_spots.forEach( function( _spot ) {
			if( in_range( _spot.top, _spot.bottom, _icon_pos.top ) ) {
				// top inside, move below
				return _modifier = _spot.bottom - _icon_pos.top + 5;
			} else if( in_range( _spot.top, _spot.bottom, _icon_pos.bottom ) ) {
				// bottom inside, move above
				return _modifier = _spot.top - _icon_pos.bottom + 5;
			}
		} );

		return _modifier;
	}

	function check_position_overflow( $parent, $icon, $pointer ) {
		var _icon_pos = {
				top: $icon.offset().top,
				bottom: $icon.offset().top + $icon.outerHeight()
			},
			_pos_range = {
				top: $parent.offset().top,
				bottom: $parent.offset().top + $parent.height()
			},
			_side = $parent.attr( 'data-side' ),
			_modifier = 0;

		if( _side == 'left' ) {
			_modifier = spots_walker( _icon_pos, _icon_spots.right, _pos_range );
			_icon_pos = track_icon_recalc( $icon, $pointer, _icon_pos, _modifier );
			_icon_spots.left.push( _icon_pos );

			return true;
		} else {
			_modifier = spots_walker( _icon_pos, _icon_spots.left, _pos_range );
			_icon_pos = track_icon_recalc( $icon, $pointer, _icon_pos, _modifier );
			_icon_spots.right.push( _icon_pos );

			return true;
		}
	}

	function track_icon_recalc( $icon, $pointer, _cur_pos, _modifier ) {
		if( _modifier == 0 ) { return _cur_pos; }

		_cur_pos.top += _modifier;
		_cur_pos.bottom += _modifier;

		$icon.css( { "margin-top" : parseInt( $icon.css( 'margin-top' ) ) +  parseInt( _modifier ) } );
		$pointer.css( { "margin-top" : parseInt( $pointer.css( 'margin-top' ) ) + parseInt( _modifier ) } );

		return _cur_pos;
	}

	function track_icon_calc( $timeline ) {
		var $timeline_items = $timeline.find( '.mpc-timeline-item__wrap' );

		$timeline_items.each( function() {
			var $parent = $( this ),
				$item = $parent.find( '.mpc-timeline-item' ),
				$icon = $parent.find( '.mpc-tl-icon' ),
				$pointer = $item.find( '.mpc-tl-before' ),
				_top = 0;

			if( $timeline.is( '.mpc-layout--left' ) ) {
				$parent.attr( 'data-side', 'right' );
			} else if( $timeline.is( '.mpc-layout--right' ) ) {
				$parent.attr( 'data-side', 'left' );
			} else {
				if( $parent.css( 'left' ) == '0px' ) {
					$parent.attr( 'data-side', 'left' );
				} else {
					$parent.attr( 'data-side', 'right' );
				}
			}

			$pointer.removeAttr( 'style' );
			if( $parent.attr( 'data-side' ) == 'left' && !$timeline.is( '.mpc-layout--right' ) && _mpc_vars.breakpoints.custom( '(min-width: 767px)' ) ) {
				$pointer.css( { 'margin-left' : parseInt( $item.css( 'border-right-width' ) ) } ) ;
			} else {
				$pointer.css( { 'margin-right' : parseInt( $item.css( 'border-left-width' ) ) } ) ;
			}

			if( $timeline.is( '.mpc-pointer--middle' ) ) {
				$pointer.css( { 'margin-top' : parseInt( $pointer.css( 'margin-top' ) ) - parseInt( $pointer.outerHeight() * 0.5 ) })
			}

			_top = $pointer.offset().top - $parent.offset().top - $icon.height() * 0.5;
			if( !$timeline.is( '.mpc-pointer--right-triangle' ) ) {
				_top += $pointer.outerHeight() * .5;
			}

			$icon.css( { "margin-top": parseInt( _top ) } );

			if( $timeline.is( '.mpc-layout--both' ) ) {
				if ( $parent.length && $icon.length && $pointer.length ) {
					check_position_overflow( $parent, $icon, $pointer );
				}
			}
		});
	}

	function ornament_icon_pos( $timeline ) {
		var $icon = $timeline.find( '.mpc-track__icon' ),
			$track = $timeline.find( '.mpc-timeline__track' );

		$icon.css( { 'margin-left' : - parseInt( ( $icon.outerWidth() + $track.outerWidth() ) * .5 ) } );
	}

	function delay_init( $timeline ) {
		if ( $.fn.isotope && $.fn.imagesLoaded ) {
			init_shortcode( $timeline );
		} else {
			setTimeout( function() {
				delay_init( $timeline );
			}, 50 );
		}
	}

	function init_shortcode( $timeline ) {
		var $row = $timeline.parents( '.mpc-row' );

		ornament_icon_pos( $timeline );

		$timeline.trigger( 'mpc.inited' ); // removing float

		var _isotope = {
			itemSelector: '.mpc-timeline-item__wrap',
			layoutMode: 'masonry'
		};

		if( $timeline.is( '.mpc-layout--right' ) ) {
			_isotope.isOriginLeft = false;
		}

		$timeline.imagesLoaded().done( function() {
			$timeline.on( 'layoutComplete', function() {
				_icon_spots = {
					'left' : [],
					'right' : []
				};
				track_icon_calc( $( this ) );

				MPCwaypoint.refreshAll();
			} );

			$timeline.isotope(  _isotope );

			$row.on( 'mpc.rowResize', function() {
				if( $timeline.data( 'isotope' ) ) {
					$timeline.isotope( 'layout' );
				}
			} );

			_mpc_vars.$document.ready( function() {
				setTimeout( function() {
					if( $timeline.data( 'isotope' ) ) {
						$timeline.isotope( 'layout' );
					}
				}, 50 );
			});
		});
	}

	var $timelines_basic = $( '.mpc-timeline-basic' );

	$timelines_basic.each( function() {
		var $timeline_basic = $( this );

		$timeline_basic.one( 'mpc.init', function() {
			delay_init( $timeline_basic );
		} );
	});

} )( jQuery );
