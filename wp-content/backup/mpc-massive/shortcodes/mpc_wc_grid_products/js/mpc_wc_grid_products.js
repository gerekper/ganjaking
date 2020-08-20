/*----------------------------------------------------------------------------*\
	GRID PRODUCTS SHORTCODE
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

	function init_shortcode( $grid_products ) {
		var $row = $grid_products.parents( '.mpc-row' );

		$grid_products.imagesLoaded().done( function() {
			$grid_products.on( 'layoutComplete', function() {
				mpc_init_lightbox( $grid_products, true );
				MPCwaypoint.refreshAll();
			} );

			$grid_products.isotope( {
				itemSelector: '.mpc-wc-product',
				layoutMode: 'masonry',
				masonry: {
					columnWidth: '.mpc-grid-sizer'
				}
			} );

			$row.on( 'mpc.rowResize', function() {
				if( $grid_products.data( 'isotope' ) ) {
					$grid_products.isotope( 'layout' );
				}
			} );
		} );

		$grid_products.trigger( 'mpc.inited' );
	}

	if ( typeof window.InlineShortcodeView != 'undefined' ) {
		window.InlineShortcodeView_mpc_wc_grid_products = window.InlineShortcodeView.extend( {
			rendered: function() {
				var $grid_products = this.$el.find( '.mpc-wc-grid-products' ),
					$pagination = $grid_products.siblings( '.mpc-pagination' );

				$grid_products.addClass( 'mpc-waypoint--init' );

				_mpc_vars.$body.trigger( 'mpc.icon-loaded', [ $grid_products, $pagination ] );
				_mpc_vars.$body.trigger( 'mpc.font-loaded', [ $grid_products, $pagination ] );
				_mpc_vars.$body.trigger( 'mpc.pagination-loaded', [ $pagination ] );
				_mpc_vars.$body.trigger( 'mpc.inited', [ $grid_products, $pagination ] );

				setTimeout( function() {
					delay_init( $grid_products );
				}, 500 );

				window.InlineShortcodeView_mpc_wc_grid_products.__super__.rendered.call( this );
			},
			beforeUpdate: function() {
				this.$el.find( '.mpc-wc-grid-products' ).isotope( 'destroy' );

				window.InlineShortcodeView_mpc_wc_grid_products.__super__.beforeUpdate.call( this );
			}
		} );
	}

	var $grids_products = $( '.mpc-wc-grid-products' );

	$grids_products.each( function() {
		var $grid_product = $( this );

		$grid_product.one( 'mpc.init', function () {
			delay_init( $grid_product );
		} );
	});

	/* Fix Google Fonts resize */
	_mpc_vars.$window.load( function() {
		if( $grids_products.data( 'isotope' ) ) {
			setTimeout( function() {
				$grids_products.isotope( 'layout' );
			}, 250 );
		}
	});
} )( jQuery );
