/*----------------------------------------------------------------------------*\
  CAROUSEL POSTS SHORTCODE - Panel
 \*----------------------------------------------------------------------------*/
(function( $ ) {
	"use strict";

	var $popup      = $( '#vc_ui-panel-edit-element' ),
		_hide_class = 'vc_dependent-hidden';

	function layout_dependency( _layout, _state, _animation ) {
		var _parts = _layout.split( ',' ),
			_prefix = _state == 'hover' ? 'hover_' : '',
			_title_fields = [ 'title_font_preset', 'title_font_align', 'title_font_transform', 'title_overflow' ],
			_count_fields = [ 'count_font_preset', 'count_font_align', 'count_font_transform' ],
			$title_elements = $popup.find( '[data-vc-shortcode-param-name^="' + _prefix + 'title_"]' ),
			$count_elements = $popup.find( '[data-vc-shortcode-param-name^="' + _prefix + 'count_"]' );

		$title_elements.addClass( _hide_class );
		$count_elements.addClass( _hide_class );

		_parts.forEach( function( _part ) {
			if( _state == 'regular' || _animation == 'replace' ) {
				if( _part == 'title' ) {
					$title_elements.removeClass( _hide_class );
				} else if( _part == 'count' ) {
					$count_elements.removeClass( _hide_class );
				}
			} else {
				if( _part == 'title' ) {
					$title_elements.removeClass( _hide_class );

					_title_fields.forEach( function( _field ) {
						$( '[data-vc-shortcode-param-name="hover_' + _field + '"]' ).addClass( _hide_class );
					} );
				} else if( _part == 'count' ) {
					$count_elements.removeClass( _hide_class );

					_count_fields.forEach( function( _field ) {
						$( '[data-vc-shortcode-param-name="hover_' + _field + '"]' ).addClass( _hide_class );
					} );
				}
			}
		} );
	}

	$popup.on( 'mpc.render', function() {
		if( $popup.attr( 'data-vc-shortcode' ) != 'mpc_wc_carousel_categories' ) {
			return '';
		}

		var $regular   = $popup.find( '[name="layout"]' ),
			$hover     = $popup.find( '[name="hover_layout"]' ),
			$animation = $popup.find( '[name="animation_type"]' );

		$animation.on( 'change', function() {
			if( $( this ).val() == 'move' ) {
				$hover.val( $regular.val() );
			}

			$hover.trigger( 'change' );
		} );

		$regular.on( 'change', function() {
			var _animation = $animation.val();

			if( _animation == 'move' ) {
				$hover.val( $regular.val() );
			}

			layout_dependency( $regular.val(), 'regular', _animation );
			layout_dependency( $hover.val(), 'hover', _animation );
		} );

		$hover.on( 'change', function() {
			var _animation = $animation.val();

			layout_dependency( $regular.val(), 'regular', _animation );
			layout_dependency( $hover.val(), 'hover', _animation );
		} );

		// Triggers
		setTimeout( function() {
			$regular.trigger( 'change' );
		}, 350 );
	} );
})( jQuery );
