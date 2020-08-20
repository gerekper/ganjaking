/*----------------------------------------------------------------------------*\
	PAGINATION SHORTCODE
\*----------------------------------------------------------------------------*/
( function( $ ) {
	"use strict";

	function mpc_pagination_classic( $pagination ) {
		if( $pagination.is( '.mpc--square-init' ) ) {
			mpc_pagination_square_button( $pagination );
		}

		$pagination.find( 'li a' ).on( 'click', function( _ev ) {
			var $this      = $( this ),
				$parent    = $this.parents( '.mpc-pagination' ),
				_type      = $parent.attr( 'data-type' ),
				_query	   = window[ '_' + $parent.attr( 'data-grid' ) + '_query' ],
				_current   = parseInt( $parent.attr( 'data-current' ) ),
				_max_pages = parseInt( $parent.attr( 'data-pages' ) ),
				_load_page = $this.parents( 'li' ).attr( 'data-page' );

			if( !$parent.is( '.mpc-pagination--classic' ) || $parent.is( '.mpc-non-ajax' ) ) {
				return true;
			}

			_ev.preventDefault();

			if( _current != _load_page && $this.parent( '.mpc-pagination' ).is( '.mpc-disabled' ) ) return false;

			$parent.addClass( 'mpc-disabled' );

			if( _load_page == 'prev' ) {
				_query.paged = _current > 1 ? _current - 1 : false;
			} else if( _load_page == 'next' ) {
				_query.paged = _current < _max_pages ? _current + 1 : false;
			} else {
				_query.paged = parseInt( _load_page ) != _current ? parseInt( _load_page ) : false;
			}

			if( _query.paged ) {
				window[ '_' + $parent.attr( 'data-grid' ) + '_query' ] = _query;
				mpc_get_paged_content( $parent.attr( 'data-grid' ), _type, $this );
				mpc_refresh_pagination( $parent, $parent.attr( 'data-grid' ) );
			}
		} );
	}

	function mpc_pagination_loadmore( $pagination ) {
		$pagination.find( '.mpc-pagination__link' ).on( 'click', function( _ev ) {
			var $this      = $( this ),
				$parent    = $this.parents( '.mpc-pagination' ),
				_type      = $parent.attr( 'data-type' ),
				_current   = parseInt( $parent.attr( 'data-current' ) ),
				_max_pages = parseInt( $parent.attr( 'data-pages' ) );

			if( !$parent.is( '.mpc-pagination--classic' ) ) {
				_ev.preventDefault();

				if( _current >= _max_pages || $this.is( '.mpc-disabled' ) ) return false;

				$this.addClass( 'mpc-disabled' );

				window[ '_' + $parent.attr( 'data-grid' ) + '_query' ].paged = _current + 1;

				mpc_get_paged_content( $parent.attr( 'data-grid' ), _type, $this );
			}
		} );
	}

	function mpc_pagination_infinity( $pagination ) {
		$pagination.on( 'mpc.infinity', function() {
			var $this = $( this );

			if( $this.is( '.mpc-pagination--infinity' ) ) {
				$this.find( '.mpc-pagination__link' ).trigger( 'click' );
			}
		});
	}

	function mpc_get_paged_content( _id, _type, $this ) {
		var _query = window[ '_' + _id + '_query' ],
			_atts  = window[ '_' + _id + '_atts' ];

		$.post(
			_mpc_vars.ajax_url,
			{
				action:     'mpc_pagination_set',
				type:       _type,
				current:    _query.paged,
				query:      _query,
				atts:       _atts,
				dataType:   'html'
			},
			function( _response ) {
				var $grid       = $( '#' + _id ),
					$pagination = $this.parents( '.mpc-pagination' ),
					$items 		= $( _response );

				if( _type == 'classic' && !$pagination.is( '.mpc-append-ajax' ) ) {
					var $grid_items = $grid.children();
					$grid.isotope( 'remove', $grid_items );
				}

				mpc_init_lightbox( $items, true );
				$grid.append( $items ).isotope( 'insert', $items );
				$grid.imagesLoaded().done( function() {
					$grid.isotope( 'layout' );
				} );

				var _pages = $grid.find( '.mpc-pagination--settings' ).data( 'pages' ),
					_current = $grid.find( '.mpc-pagination--settings' ).data( 'current' );

				$grid.find( '.mpc-pagination--settings' ).remove();
				$grid.trigger( 'mpc.loaded' );

				if( _type == 'infinity' ) {
					$pagination.removeClass( 'mpc-infinity--init' );
				}

				$pagination
					.attr( 'data-current', _current )
					.attr( 'data-pages', _pages );

				if( !$pagination.is( '.mpc-pagination--classic' ) && _pages > _current ) {
					$pagination.removeClass( 'mpc-disabled' );
				} else if( $pagination.is( '.mpc-pagination--classic' ) ) {
					$pagination.removeClass( 'mpc-disabled' );
				}

				$pagination.find( '.mpc-current, .mpc-disabled' )
					.removeClass( 'mpc-current mpc-disabled' );

				$pagination.find( '[data-page="' + _current + '"]' )
					.addClass( 'mpc-current' );

				if( $pagination.is( '.mpc-pagination--classic' ) && _current == 1 ) {
					$pagination.find( '.mpc-pagination__prev' ).addClass( 'mpc-disabled' );
				}

				if ( $pagination.is( '.mpc-pagination--classic' ) && _current == _pages ) {
					$pagination.find( '.mpc-pagination__next' ).addClass( 'mpc-disabled' );
				} else if( _current == _pages ) {
					$pagination.off().remove();
				}

			}
		);
	}

	function mpc_refresh_pagination( $pagination, _grid_id ) {
		$.post(
			_mpc_vars.ajax_url,
			{
				action:     'mpc_pagination_refresh',
				query:      window[ '_' + _grid_id + '_query' ],
				preset:     $pagination.data( 'preset' )
			},
			function( _response ) {
				$pagination.after( _response.data );
				$pagination.remove();

				mpc_pagination_classic( $( '.mpc-pagination[data-grid="' + _grid_id + '"]' ) );
			}
		);
	}

	function mpc_pagination_square_button( $pagination ) {
		var $prev = $pagination.find( '.mpc-pagination__prev' ),
			$next = $pagination.find( '.mpc-pagination__next' ),
			$items = $pagination.find( '.mpc-pagination__link' ),
			_max_size = 0;

		$.each( $pagination.find( '.mpc-pagination__link' ), function() {
			var $this = $( this );

			_max_size = Math.max( $this.width(), $this.height(), _max_size );
		} );

		$items.css( {
			'width' : _max_size + 'px',
			'height' : _max_size + 'px',
			'line-height' : _max_size + 'px'
		} );

		$prev.css( {
			'height' : _max_size + 'px',
			'line-height' : _max_size + 'px'
		} );

		$next.css( {
			'height' : _max_size + 'px',
			'line-height' : _max_size + 'px'
		} );

		$pagination.removeClass( 'mpc--square-init' ).addClass( 'mpc--square');
	}

	var $waypoints = $( '.mpc-pagination--infinity' );

	$waypoints.each( function() {
		var $waypoint = $( this ),
		    _inview = new MPCwaypoint( {
			    element: $waypoint[ 0 ],
			    handler: function() {
				    $waypoint
					    .addClass( 'mpc-infinity-init' )
					    .trigger( 'mpc.infinity' );
			    },
			    offset: '80%'
		    } );
	} );

	var $paginations = $( '.mpc-pagination' );

	$paginations.on( 'mpc.init', function() {
		var $pagination = $( this );

		if( $pagination.is( '.mpc--square-init' ) ) {
			mpc_pagination_square_button( $pagination );
		}

		/* Classic */
		mpc_pagination_classic( $pagination );

		/* Load More */
		mpc_pagination_loadmore( $pagination );

		/* Infinity based on Load More */
		mpc_pagination_infinity( $pagination );

		$( '#' + $pagination.data( 'grid' ) ).on( 'layoutComplete', function() {
			MPCwaypoint.refreshAll();
		});

		$pagination.trigger( 'mpc.inited' );
	});
} )( jQuery );
