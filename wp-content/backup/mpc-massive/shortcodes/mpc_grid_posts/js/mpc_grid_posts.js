/*----------------------------------------------------------------------------*\
	GRID POSTS SHORTCODE
\*----------------------------------------------------------------------------*/
( function( $ ) {
	"use strict";

	function resize_single_posts( $grid ) {
		if( $grid.is( '.mpc-layout--style_4' ) ) {
			var $posts_content = $grid.find( '.mpc-post__wrapper > .mpc-post__content' ),
				$first  = $posts_content.eq( 1 ),
				_margin = parseInt( $first.outerHeight() * -0.5 ) ;

			$posts_content.parents( '.mpc-post' ).css( 'margin-bottom', _margin );
		}
	}

	function delay_init( $grid ) {
		if ( $.fn.isotope && $.fn.imagesLoaded ) {
			init_shortcode( $grid );
		} else {
			setTimeout( function() {
				delay_init( $grid );
			}, 50 );
		}
	}

	function init_shortcode( $grid_posts ) {
		var $row = $grid_posts.parents( '.mpc-row' );

		resize_single_posts( $grid_posts );

		$grid_posts.imagesLoaded().done( function() {
			$grid_posts.on( 'layoutComplete', function() {
				mpc_init_lightbox( $grid_posts, true );
				MPCwaypoint.refreshAll();
			} );

			$grid_posts.isotope( {
				itemSelector: '.mpc-post',
				layoutMode: 'masonry',
				masonry: {
					columnWidth: '.mpc-grid-sizer'
				}
			} );

			$row.on( 'mpc.rowResize', function() {
				if( $grid_posts.data( 'isotope' ) ) {
					$grid_posts.isotope( 'layout' );
				}
			} );
		} );

		$grid_posts.trigger( 'mpc.inited' );
	}

	if ( typeof window.InlineShortcodeView != 'undefined' ) {
		window.InlineShortcodeView_mpc_grid_posts = window.InlineShortcodeView.extend( {
			rendered: function() {
				var $grid_posts = this.$el.find( '.mpc-grid-posts' ),
					$pagination = $grid_posts.siblings( '.mpc-pagination' );

				$grid_posts.addClass( 'mpc-waypoint--init' );

				_mpc_vars.$body.trigger( 'mpc.icon-loaded', [ $grid_posts, $pagination ] );
				_mpc_vars.$body.trigger( 'mpc.font-loaded', [ $grid_posts, $pagination ] );
				_mpc_vars.$body.trigger( 'mpc.pagination-loaded', [ $pagination ] );
				_mpc_vars.$body.trigger( 'mpc.inited', [ $grid_posts, $pagination ] );

				setTimeout( function() {
					delay_init( $grid_posts );
				}, 500 );

				window.InlineShortcodeView_mpc_grid_posts.__super__.rendered.call( this );
			},
			beforeUpdate: function() {
				this.$el.find( '.mpc-grid-posts' ).isotope( 'destroy' );

				window.InlineShortcodeView_mpc_grid_posts.__super__.beforeUpdate.call( this );
			}
		} );
	}

	var $grids_posts = $( '.mpc-grid-posts' );

	$grids_posts.each( function() {
		var $grid_posts = $( this );

		$grid_posts.one( 'mpc.init', function () {
			delay_init( $grid_posts );
		} );
	});

	/* Fix Google Fonts resize */
	_mpc_vars.$window.load( function() {
		$grids_posts.each( function() {
			var $grid_posts = $( this );

			if ( $grid_posts.data( 'isotope' ) ) {
				setTimeout( function() {
					$grid_posts.isotope( 'layout' );
				}, 250 );
			}
		});
	});
} )( jQuery );
