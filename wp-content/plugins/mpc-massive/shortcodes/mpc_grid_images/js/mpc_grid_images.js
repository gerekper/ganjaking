/*----------------------------------------------------------------------------*\
	GRID IMAGES SHORTCODE
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

	function init_shortcode( $grid_images ) {
		var $row = $grid_images.parents( '.mpc-row' );

		$grid_images.imagesLoaded().done( function() {
			$grid_images.on( 'layoutComplete', function() {
				MPCwaypoint.refreshAll();
				mpc_init_lightbox( $grid_images, true );
			} );

			$grid_images.isotope( {
				itemSelector: '.mpc-item',
				layoutMode: 'masonry'
			} );

			$row.on( 'mpc.rowResize', function() {
				if( $grid_images.data( 'isotope' ) ) {
					$grid_images.isotope( 'layout' );
				}
			} );
		} );

		$grid_images.trigger( 'mpc.inited' );
	}

	if ( typeof window.InlineShortcodeView != 'undefined' ) {
		window.InlineShortcodeView_mpc_grid_images = window.InlineShortcodeView.extend( {
			rendered: function() {
				var $grid_images = this.$el.find( '.mpc-grid-images' ),
					$pagination = $grid_images.siblings( '.mpc-pagination' );

				$grid_images.addClass( 'mpc-waypoint--init' );

				_mpc_vars.$body.trigger( 'mpc.icon-loaded', [ $grid_images, $pagination ] );
				_mpc_vars.$body.trigger( 'mpc.font-loaded', [ $grid_images, $pagination ] );
				_mpc_vars.$body.trigger( 'mpc.pagination-loaded', [ $pagination ] );
				_mpc_vars.$body.trigger( 'mpc.inited', [ $grid_images, $pagination ] );

				setTimeout( function() {
					delay_init( $grid_images );
				}, 250 );

				window.InlineShortcodeView_mpc_grid_images.__super__.rendered.call( this );
			},
			beforeUpdate: function() {
				this.$el.find( '.mpc-grid-images' ).isotope( 'destroy' );

				window.InlineShortcodeView_mpc_grid_images.__super__.beforeUpdate.call( this );
			}
		} );
	}

	var $grids_images = $( '.mpc-grid-images' );

	$grids_images.each( function() {
		var $grid_images = $( this );

		$grid_images.one( 'mpc.init', function () {
			delay_init( $grid_images );
		} );
	});
} )( jQuery );
