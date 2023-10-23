'use strict';

/* global globalThis, jQuery, yith_wcan_shortcodes, accounting */

import { $, $body } from '../config.js';

export default class YITH_WCAN_Filter {
	// currently executing xhr
	xhr = null;

	// flag set during ajax call handling
	doingAjax = false;

	// register original url search param
	originalSearch = location.search;

	// flag set once init has executed
	initialized = false;

	// flag set when page has at least one active filter.
	filtered = false;

	// init object
	constructor() {
		this.initPopState();
		this.initialized = true;
	}

	// init page reload when popstate event alter filters
	initPopState() {
		if ( ! yith_wcan_shortcodes.reload_on_back ) {
			return;
		}

		this.pushUrlToHistory( window.location, document.title, null, true );

		$( window ).on( 'popstate', function () {
			if ( ! window.history.state?._yithWcan ) {
				return;
			}

			window.location.reload( true );
		} );
	}

	// execute call to filter products in current view
	doFilter( filters, target, preset ) {
		let targetUrl,
			$target = target ? $( target ) : $( 'body' ),
			customFilters;

		// filter properties
		customFilters = $( document ).triggerHandler(
			'yith_wcan_filters_parameters',
			[ filters ]
		);

		if ( !! customFilters ) {
			filters = customFilters;
		}

		// block elements before filtering
		$target && this.block( $target );

		// calculate target url
		targetUrl = this.buildUrl( filters );

		// if no ajax, simply change page url
		if ( ! yith_wcan_shortcodes.ajax_filters ) {
			this.pushUrlToHistory( targetUrl, document.title, filters );

			window.location = targetUrl;
			return;
		}

		// start doing ajax
		this.doingAjax = true;

		return this._doAjax( targetUrl ).done( ( response ) => {
			targetUrl = this.searchAlternativeUrl( response, targetUrl );

			this._beforeFilter( response, filters );

			this.refreshFragments( target, preset, response );
			this.pushUrlToHistory( targetUrl, response.pageTitle, filters );
			this.originalSearch = location.search;

			$target && this.unblock( $target );

			this._afterFilter( response, filters );

			this.doingAjax = false;
		} );
	}

	// actions performed before filter
	_beforeFilter( response, filters ) {
		$( document ).trigger( 'yith-wcan-ajax-loading', [
			response,
			filters,
		] );
	}

	// actions performed after filter
	_afterFilter( response, filters ) {
		$( '.woocommerce-ordering' ).on(
			'change',
			'select.orderby',
			function () {
				$( this ).closest( 'form' ).submit();
			}
		);

		this.filtered = filters && !! Object.keys( filters ).length;
		this.filtered
			? $body.addClass( 'filtered' )
			: $body.removeClass( 'filtered' );

		$( window ).trigger( 'scroll' );

		$( document )
			.trigger( 'yith-wcan-ajax-filtered', [ response, filters ] )
			.trigger( 'yith_wcwl_reload_after_ajax' );
	}

	// build url to show
	buildUrl( filters ) {
		let queryParam = yith_wcan_shortcodes.query_param,
			params = {},
			location = window.location,
			url = !! yith_wcan_shortcodes.base_url
				? yith_wcan_shortcodes.base_url
				: location?.origin + location?.pathname,
			search = '',
			self = this;

		const haveFilters =
			typeof filters === 'object' && Object.keys( filters ).length;

		// remove filter session from current url, if any
		if ( !! yith_wcan_shortcodes.session_param ) {
			url = url.replace(
				new RegExp(
					'/' + yith_wcan_shortcodes.session_param + '/[^/]*/'
				),
				''
			);
		}

		if ( haveFilters ) {
			params[ queryParam ] = 1;
		}

		if ( !! this.originalSearch ) {
			const searchParams = this.originalSearch
				.replace( '?', '' )
				.split( '&' )
				.reduce( ( a, v ) => {
					const items = v.split( '=' );

					if ( items.length === 2 ) {
						if ( this.isFilterParam( items[ 0 ] ) ) {
							return a;
						}

						a[ items[ 0 ] ] = items[ 1 ];
					}

					return a;
				}, {} );

			params = $.extend( params, searchParams );
		}

		if ( haveFilters ) {
			params = $.extend( params, filters );
		}

		search = Object.keys( params )
			.reduce( function ( a, i ) {
				const v = params[ i ];

				if ( ! v || ! i ) {
					return a;
				}

				a += self._cleanParam( i ) + '=' + self._cleanParam( v ) + '&';

				return a;
			}, '?' )
			.replace( /&$/g, '' )
			.replace( /%2B/g, '+' )
			.replace( /%2C/g, ',' );

		if ( search.length > 1 ) {
			url += search;
		}

		return url;
	}

	// retrieves alternative sharing url in response body
	searchAlternativeUrl( response, defaultUrl = '' ) {
		let url = defaultUrl,
			matches;

		if ( -1 === response.indexOf( 'yith_wcan:sharing_url' ) ) {
			return url;
		}

		matches = response.match(
			/<meta name="yith_wcan:sharing_url" content="([^"]*)">/
		);
		url = matches && 1 in matches ? matches[ 1 ] : url;

		return url;
	}

	// push url to browser history
	pushUrlToHistory( url, title, filters, current ) {
		if (
			! yith_wcan_shortcodes.change_browser_url ||
			navigator.userAgent.match( /msie/i )
		) {
			return;
		}

		let method = 'pushState';

		if ( !! current ) {
			method = 'replaceState';
		}

		window.history[ method ](
			{
				_yithWcan: true,
				pageTitle: title,
				filters,
			},
			'',
			url
		);
	}

	// replaces elements in the page with refreshed ones
	refreshFragments( target, preset, response ) {
		const responseDom = document.createElement( 'html' ),
			$response = $( responseDom );

		responseDom.innerHTML = response;

		if ( target ) {
			let $preset = $( preset ),
				$target = $( target ),
				$destination;

			if ( $preset.length ) {
				$destination = $response.find( preset );

				if ( $destination.length ) {
					$preset.replaceWith( $destination.first() );
				}
			}

			if ( $target.length ) {
				$destination = $response.find( target );

				if ( $destination.length ) {
					$target.replaceWith( $destination.first() );
				}
			}
		} else {
			const content = $( yith_wcan_shortcodes.content );

			if ( content.length ) {
				content.replaceWith(
					$response.find( yith_wcan_shortcodes.content )
				);
			} else {
				$( 'body' ).replaceWith( $response.find( 'body' ) );
			}
		}

		$( document ).trigger( 'yith_wcan_init_shortcodes' );
	}

	// clean url parameters
	_cleanParam( param ) {
		if (
			! yith_wcan_shortcodes?.process_sanitize ||
			yith_wcan_shortcodes?.skip_sanitize
		) {
			return param;
		}

		return encodeURIComponent( param );
	}

	// executes Ajax calls
	_doAjax( url, params ) {
		if ( this.xhr ) {
			this.xhr.abort();
		}

		params = $.extend(
			{
				url,
				headers: {
					'X-YITH-WCAN': 1,
				},
			},
			params
		);

		this.xhr = $.ajax( params );

		return this.xhr;
	}

	// block dom elements
	block( $el ) {
		if ( typeof $.fn.block === 'undefined' ) {
			return;
		}

		let background = '#fff center center no-repeat';

		if ( yith_wcan_shortcodes?.loader ) {
			background = `url('${ yith_wcan_shortcodes.loader }') ${ background }`;
		}

		$el.block( {
			message: null,
			overlayCSS: {
				background,
				opacity: 0.7,
			},
		} );
	}

	// unblock dom elements
	unblock( $el ) {
		if ( typeof $.fn.unblock === 'undefined' ) {
			return;
		}

		$el.unblock();
	}

	// checks if param is one used by layared nav to filter products.
	isFilterParam( param ) {
		let supportedParams = [
				'rating_filter',
				'min_price',
				'max_price',
				'price_ranges',
				'onsale_filter',
				'instock_filter',
				'featured_filter',
				'orderby',
				'product-page',
				yith_wcan_shortcodes.query_param,
			],
			customParams;

		// filter properties
		customParams = $( document ).triggerHandler(
			'yith_wcan_supported_filters_parameters',
			[ supportedParams ]
		);

		if ( !! customParams ) {
			supportedParams = customParams;
		}

		supportedParams = supportedParams.concat(
			yith_wcan_shortcodes.supported_taxonomies.map( ( i ) =>
				i.replace( 'pa_', 'filter_' )
			)
		);

		if ( -1 !== supportedParams.indexOf( param ) ) {
			return true;
		}

		if ( -1 !== param.indexOf( 'filter_' ) ) {
			return true;
		}

		if ( -1 !== param.indexOf( 'query_type_' ) ) {
			return true;
		}

		return false;
	}
}
