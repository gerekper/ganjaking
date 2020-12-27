/*----------------------------------------------------------------------------*\
	ACCORDION SHORTCODE
\*----------------------------------------------------------------------------*/
(function( $ ) {
	"use strict";

	function calculate_height( $items ) {
		$items.each( function() {
			var $item = $( this ).find( '.mpc-accordion-item__content' );

			if( $item.attr( 'data-active' ) === 'true' ) {
				$item
					.removeAttr( 'style' )
					.removeClass( 'mpc-hidden' )
					.css( 'height', parseInt( $item.height() ) );
			}

			$item.addClass( 'mpc-hidden' );
		} );
	}

	function init_shortcode( $accordion ) {
		var $items             = $accordion.find( '.mpc-accordion__item' ),
		    $active            = $accordion.find( '[data-active="true"].mpc-accordion-item__content' ),
		    $accordions_toggle = $accordion.find( '.mpc-accordion-item__heading' );

		if( $active.find( '.mpc-parent--init' ).length ) {
			$active.trigger( 'mpc.parent-init' );
			$active.find( '.mpc-parent--init' ).removeClass( 'mpc-parent--init' );
		}

		calculate_height( $items );

		$accordions_toggle.on( 'click', function() {
			toggle_accordion( $( this ) );
		} );

		setTimeout( function() {
			calculate_height( $items );
		}, 250 );

		$accordion.trigger( 'mpc.inited' );
	}

	function toggle_accordion( $accordion ) {
		var $item    = $accordion.siblings( '.mpc-accordion-item__content' ),
		    $current = $accordion.parents( '.mpc-accordion' ).find( '[data-active="true"].mpc-accordion-item__content' ),
		    _height  = $item.find( '.mpc-accordion-item__wrapper' ).outerHeight( true ),
		    _toggle  = $accordion.parents( '.mpc-accordion' ).is( '.mpc-accordion--toggle' );

		if( _toggle ) {

			if( $item.attr( 'data-active' ) === 'true' ) {
				$item.velocity( 'stop' ).velocity( { height: 0 }, 300 );

				$item.removeAttr( 'data-active' );
				$accordion.removeClass( 'mpc-active' );
			} else if ( $item !== $current ) {
				$current.velocity( 'stop' ).velocity( { height: 0 }, 300 );
				$item.velocity( 'stop' ).velocity( { height: _height }, 300 );

				$current.removeAttr( 'data-active' ).siblings( '.mpc-accordion-item__heading' ).removeClass( 'mpc-active' );
				$item.attr( 'data-active', 'true' );
				$accordion.addClass( 'mpc-active' );

				if( $item.find( '.mpc-parent--init' ).length ) {
					$item.trigger( 'mpc.parent-init' );
					$item.find( '.mpc-parent--init' ).removeClass( 'mpc-parent--init' );
				}
			}
		} else {
			if( $item.attr( 'data-active' ) === 'true' ) {
				$item.velocity( 'stop' ).velocity( { height: 0 }, 300 );

				$item.removeAttr( 'data-active' );
				$accordion.removeClass( 'mpc-active' );
			} else {
				$item.velocity( 'stop' ).velocity( { height: _height }, 300 );

				$item.attr( 'data-active', 'true' );
				$accordion.addClass( 'mpc-active' );

				if( $item.find( '.mpc-parent--init' ).length ) {
					$item.trigger( 'mpc.parent-init' );
					$item.find( '.mpc-parent--init' ).removeClass( 'mpc-parent--init' );
				}
			}
		}

		setTimeout( function() {
			$accordion.parents( '.mpc-row' ).trigger( 'mpc.recalc' );
		}, 300 );
	}

	var $accordions = $( '.mpc-accordion' );

	$accordions.each( function() {
		var $accordion = $( this );

		$accordion.one( 'mpc.init', function() {
			init_shortcode( $accordion );
		} );
	} );

	_mpc_vars.$window.on( 'mpc.resize', function() {
		$.each( $accordions, function() {
			calculate_height( $( this ).find( '.mpc-accordion__item' ) );
		} );
	} );
})( jQuery );
