/*----------------------------------------------------------------------------*\
	COLUMN SHORTCODE
\*----------------------------------------------------------------------------*/
(function( $ ) {
	"use strict";

	var $sticky_columns = $( '.mpc-column--sticky' );

	$sticky_columns.each( function() {
		$( this ).before( '<div class="mpc-column--spacer"></div>' )
	} );

	_mpc_vars.$window.on( 'mpc.resize', function() {
		if ( _mpc_vars.breakpoints.custom( '(max-width: 992px)' ) ) {
			$.each( $sticky_columns, function() {
				var $sticky = $( this );

				$sticky.removeAttr( 'style' );
				$sticky.prev( '.mpc-column--spacer' ).removeClass( 'mpc-active' );
			} );
		}
	} );

	_mpc_vars.$window.on( 'scroll', function() {
		$sticky_columns.each( function() {
			var $this       = $( this ),
				$parent     = $this.parents( '.mpc-row' ),
				_offset     = $this.data( 'offset' ) != '' ? parseInt( $this.data( 'offset' ) ) : 0,
				_windowY    = window.pageYOffset,
				_margin_top;

			if ( _mpc_vars.breakpoints.custom( '(max-width: 992px)' ) ) {
				$this.removeAttr( 'style' );
				$this.prev( '.mpc-column--spacer' ).removeClass( 'mpc-active' );

				return '';
			}

			_margin_top = _windowY - $parent.offset().top + _offset > 0 ? _windowY - $parent.offset().top + _offset : 0;

			if ( $this.outerHeight() + _margin_top >= $parent.height() ) {
				_margin_top = $parent.height() - $this.outerHeight();

				$this
					.removeAttr( 'style' )
					.css( 'top', _margin_top );
				$this
					.prev( '.mpc-column--spacer' )
					.removeClass( 'mpc-active' );

			} else if ( _margin_top == 0 ) {
				$this.removeAttr( 'style' );
				$this
					.prev( '.mpc-column--spacer' )
					.removeClass( 'mpc-active' );
			} else {
				$this.css( {
					'position': 'fixed',
					'top':      _offset,
					'left':     $this.offset().left,
					'width':    $this.outerWidth( true )
				} );

				$this
					.prev( '.mpc-column--spacer' )
					.css( 'width', $this.outerWidth( true ) )
					.addClass( 'mpc-active' );
			}
		} );
	} );
})( jQuery );
