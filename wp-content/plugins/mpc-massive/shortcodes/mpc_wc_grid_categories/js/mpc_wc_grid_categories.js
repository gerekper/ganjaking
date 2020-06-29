/*----------------------------------------------------------------------------*\
	GRID PRODUCTS CATEGORIES SHORTCODE
\*----------------------------------------------------------------------------*/
( function( $ ) {
	"use strict";

	function delay_init( $grid ) {
		if ( $.fn.isotope && $.fn.imagesLoaded ) {
			init_shortcode( $grid );
		} else {
			setTimeout( function() {
				delay_init( $grid );
			}, 50 );
		}
	}

	function init_shortcode( $grid ) {
		var $row = $grid.parents( '.vc_row' );

		$grid.imagesLoaded().done( function() {
			$grid.on( 'layoutComplete', function() {
				MPCwaypoint.refreshAll();
			} );

			$grid.isotope( {
				itemSelector: '.mpc-wc-category',
				layoutMode: 'masonry'
			} );

			$row.on( 'mpc.rowResize', function() {
				if( $grid.data( 'isotope' ) ) {
					$grid.isotope( 'layout' );
				}
			} );
		} );

		$grid.on( 'mpc.loaded', function() {
			mpc_init_lightbox( $grid, true );
		} );

		$grid.trigger( 'mpc.inited' );
	}

	var $grids_wc_categories = $( '.mpc-wc-grid-categories' );

	$grids_wc_categories.each( function() {
		var $grid_wc_categories = $( this );

		$grid_wc_categories.one( 'mpc.init', function () {
			delay_init( $grid_wc_categories );
		} );
	});
} )( jQuery );
