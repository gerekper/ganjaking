/*----------------------------------------------------------------------------*\
	HOTSPOT SHORTCODE - Panel
\*----------------------------------------------------------------------------*/
( function( $ ) {
	"use strict";

	function init_frame( $position_field, _frame, _background_id, _cache ) {

		$position_field.parent().append( _frame );

		var $frame       = $position_field.siblings( '.mpc-coords' ),
			$overlay     = $frame.find( '.mpc-coords__overlay' ),
			$point       = $frame.find( '.mpc-coords__point' ),
			_position    = $position_field.val().split( '||' ),
			_image_width = $frame.find( '.mpc-coords__image' )[ 0 ].width;

		$frame.css( 'max-width', _image_width );

		if ( _cache ) {
			$frame.attr( 'data-id', _background_id );
			$images_cache.append( $frame.clone() );
		}

		if ( _position.length == 2 ) {
			_position[ 0 ] = isNaN( parseFloat( _position[ 0 ] ) ) ? 50 : parseFloat( _position[ 0 ] );
			_position[ 1 ] = isNaN( parseFloat( _position[ 1 ] ) ) ? 50 : parseFloat( _position[ 1 ] );

			$point.css( {
				left: _position[ 0 ] + '%',
				top: _position[ 1 ] + '%'
			} );
		}

		frame_behavior( $frame, $overlay, $point, $position_field );
	}

	function frame_behavior( $frame, $overlay, $point, $position_field ) {
		var _is_dragging = false,
			_release_timer;

		$overlay.on( 'mousedown', function( event ) {
			_is_dragging = true;

			event.preventDefault();
		} ).on( 'mouseup', function() {
			_is_dragging = false;
		} ).on( 'mouseleave', function() {
			_release_timer = setTimeout( function() {
				$overlay.trigger( 'mouseup' );
			}, 500 );
		} ).on( 'mouseenter', function() {
			clearTimeout( _release_timer );
		} ).on( 'mousemove', function( event ) {
			if ( ! _is_dragging ) {
				return;
			}

			set_position( $frame, $point, $position_field, event );
		} ).on( 'click', function( event ) {
			set_position( $frame, $point, $position_field, event );
		} ).on( 'dragstart', function( event ) {
			event.preventDefault();
		} );
	}

	function set_position( $frame, $point, $position_field, event ) {
		var _offsetX = typeof event.offsetX != 'undefined' ? event.offsetX : event.originalEvent.layerX,
			_offsetY = typeof event.offsetY != 'undefined' ? event.offsetY : event.originalEvent.layerY,
			_position = {
				x: ( _offsetX / $frame.width() * 100 ).toFixed( 3 ),
				y: ( _offsetY / $frame.height() * 100 ).toFixed( 3 )
			};

		$point.css( {
			left: _position.x + '%',
			top: _position.y + '%'
		} );

		$position_field.val( _position.x + '||' + _position.y );
	}

	var $popup = $( '#vc_ui-panel-edit-element' ),
		$images_cache = $( '<div id="mpc_hotspot_images_cache" class="mpc-hotspot-images-cache" />' );

	$images_cache.appendTo( 'body' );

	$popup.on( 'mpc.render', function() {
		if ( $popup.attr( 'data-vc-shortcode' ) != 'mpc_hotspot' ) {
			return;
		}

		var $position_field = $( '.wpb_vc_param_value.position' ),
			$load_image = $( '<button class="mpc-vc-button button mpc-default">' + _mpc_lang.mpc_hotspot.set_position + '</button>' ),
			_background_id = '';

		_background_id = vc.shortcodes.findWhere( { id: vc.active_panel.model.attributes.parent_id } ).attributes.params.background_image;
		if ( typeof _background_id == 'undefined' ) {
			_background_id = '';
		}

		if ( _background_id == '' ) {
			$position_field.parent().append( '<p class="mpc-error">' + _mpc_lang.mpc_hotspot.no_background + '</p>' );
			return;
		}

		$position_field.parent().append( $load_image );

		$load_image.one( 'click', function() {
			$load_image.remove();

			if ( $images_cache.find( '.mpc-coords[data-id="' + _background_id + '"]' ).length ) {
				init_frame( $position_field, $images_cache.find( '.mpc-coords[data-id="' + _background_id + '"]' ).clone(), _background_id, false );
			} else {
				$.post( ajaxurl, {
					action: 'mpc_hotspot_get_image',
					image_id: _background_id
				}, function( response ) {
					init_frame( $position_field, response, _background_id, true );
				} );
			}
		} );
	} );
} )( jQuery );
