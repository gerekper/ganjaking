/*----------------------------------------------------------------------------*\
	MODALBOX SHORTCODE
\*----------------------------------------------------------------------------*/
( function( $ ) {
	"use strict";

	function show_modal( $modal, $document ) {
		$modal.addClass( 'mpc-visible' );

		if ( _is_bridge_theme ) {
			$document.addClass( 'mpc-block-scroll-bridge' );
		} else {
			$document.addClass( 'mpc-block-scroll' );
		}

		stop_body_scrolling( true );
	}

	function close_position( $modal ) {
		var $close = $modal.find( '.mpc-modal__close' );

		if ( ! $modal.is( '.mpc-close--outside' ) ) {
			return false;
		}

		if ( _mpc_vars.breakpoints.custom( '(max-width: 768px)' ) ) {
			$close.prependTo( $modal.find( '.mpc-modal' ) );
		} else {
			$close.prependTo( $modal );
		}
	}

	function stop_body_scrolling( bool ) {
		if ( bool === true ) {
			$document[0].addEventListener( 'touchmove', freeze, false );
		} else {
			$document[0].removeEventListener( 'touchmove', freeze, false );
		}
	}

	function init_shortcode( $modal ) {
		var $modal_box      = $modal.find( '.mpc-modal' ),
			$modal_row      = $modal.closest( '.mpc-row' ),
			$modal_waypoint = $( '.mpc-modal-waypoint[data-id="' + $modal.attr( 'id' ) + '"]' ),
			_delay          = parseInt( $modal.attr( 'data-delay' ) ),
			_frequency      = $modal.attr( 'data-frequency' );

		$modal_row.addClass( 'mpc-row-modal' );

		_delay = isNaN( _delay ) ? 0 : _delay;

		if ( _frequency != undefined && _frequency != 'onclick' ) {
			$.post( _mpc_vars.ajax_url, {
				action:    'mpc_set_modal_cookie',
				id:        $modal.attr( 'id' ),
				frequency: _frequency
			} );
		}

		if ( _frequency == 'onclick' ) {
			if ( !! $modal.attr( 'data-target-id' ) ) {
				$( 'a[href="#' + $modal.attr( 'data-target-id' ) + '"]' ).on( 'click', function( event) {
					event.preventDefault();

					$modal_box.trigger( 'mpc.animation' );

					show_modal( $modal, $document );
				} );
			}
		} else if ( $modal_waypoint.length ) {
			if ( $modal_waypoint.is( '.mpc-waypoint--init' ) ) {
				$modal_box.trigger( 'mpc.animation' );

				show_modal( $modal, $document );
			} else {
				$modal_waypoint.on( 'mpc.waypoint', function() {
					$modal_box.trigger( 'mpc.animation' );

					show_modal( $modal, $document );
				} );
			}
		} else {
			if ( _delay > 0 ) {
				setTimeout( function() {
					$modal_box.trigger( 'mpc.animation' );

					show_modal( $modal, $document );
				}, _delay * 1000 );
			} else {
				$modal_box.trigger( 'mpc.animation' );
			}
		}

		close_position( $modal );
	}

	var $modals          = $( '.mpc-modal-overlay' ),
		$close_modals    = $( '.mpc-modal__close' ),
		$document        = $( 'html, body' ),
		_is_bridge_theme = $document.hasClass( 'qode-theme-bridge' );

	var freeze = function( event ) {
		event.preventDefault();
	};

	$modals.each( function() {
		var $modal = $( this ),
			$modal_box = $modal.find( '.mpc-modal' );

		$modal_box.one( 'mpc.init', function () {
			init_shortcode( $modal );
		} );
	});

	_mpc_vars.$window.on( 'mpc.resize', function() {
		$.each( $modals, function() {
			close_position( $( this ) );
		});
	} );

	$modals.on( 'click', function( event ) {
		if ( event.target == this ) {
			var $this = $( this );

			if ( $this.is( '.mpc-close-on-click' ) || _mpc_vars.breakpoints.custom( '(max-width: 768px)' ) ) {
				$this.find( '.mpc-modal__close' ).trigger( 'click' );
			}
		}
	} );

	$close_modals.on( 'click', function() {
		var $modal = $( this ).closest( '.mpc-modal-overlay' );

		$modal.removeClass( 'mpc-visible' );

		if ( $modals.filter( '.mpc-visible' ).length == 0 ) {
			if ( _is_bridge_theme ) {
				$document.removeClass( 'mpc-block-scroll-bridge' );
			} else {
				$document.removeClass( 'mpc-block-scroll' );
			}

			stop_body_scrolling( false );
		}
	});
} )( jQuery );
