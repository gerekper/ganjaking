'use strict';

/* global jQuery, yith_wcan, yith_wcan_frontend */

jQuery( function ( $ ) {
	let ajax_call = false;

	const see_all_taxonomies = function () {
		const categories_link = $( '#yith-wcan-reset-all-categories' ).find(
			'a.yith-wcan-reset-categories-link'
		);
		const tags_link = $( '#yith-wcan-reset-all-tags' ).find(
			'a.yith-wcan-reset-tags-link'
		);
		categories_link.add( tags_link ).on( 'click', function ( e ) {
			$( this ).yith_wcan_ajax_filters( e, this );
		} );
	};

	$.fn.yith_wcan_ajax_filters = function ( e, obj ) {
		e.preventDefault();
		let href = obj.href,
			t = $( obj ),
			is_reset = t.hasClass( 'yith-wcan-reset-navigation' );

		if (
			typeof href === 'undefined' &&
			t.parents().hasClass( 'price_slider_wrapper' )
		) {
			const form = t.parents( 'form' ),
				l = window.location,
				shop_uri = l.origin + l.pathname,
				is_filtered = shop_uri !== l.href,
				search = l.search,
				min_price = $( '.price_slider_amount #min_price' ).val(),
				max_price = $( '.price_slider_amount #max_price' ).val(),
				regex_min = new RegExp( '^min_price', 'i' ),
				regex_max = new RegExp( '^max_price', 'i' );

			href = l.href;

			if ( is_filtered ) {
				href = RemoveParameterFromUrl( href, 'min_price' );
				href = RemoveParameterFromUrl( href, 'max_price' );
			}

			const concat = shop_uri === href ? '?' : '&';

			href =
				href +
				concat +
				$.param( {
					min_price,
					max_price,
				} );
		}

		if ( t.data( 'type' ) === 'select' ) {
			t.parents( 'div.yith-woo-ajax-navigation' )
				.find( 'a.yit-wcan-select-open' )
				.removeClass( 'active' );

			t.parent()
				.find( 'div.yith-wcan-select-wrapper' )
				.css( 'z-index', '-1' )
				.animate(
					{
						visibility: 'hidden',
						opacity: 0,
					},
					300
				);
		}

		//loading
		$( yith_wcan.container )
			.not( '.ywcps-products' )
			.html( '' )
			.addClass( 'yith-wcan-loading' );
		$( document ).trigger( 'yith-wcan-ajax-loading' );

		if ( typeof yith_wcan_frontend !== 'undefined' ) {
			$( yith_wcan.container )
				.not( '.ywcps-products' )
				.css(
					'backgroundImage',
					'url(' + yith_wcan_frontend.loader_url + ')'
				);
		}

		// Check for scrollTop mode
		let scrollTopEnabled = false;

		if ( yith_wcan.scroll_top_mode === 'both' ) {
			scrollTopEnabled = true;
		} else if (
			yith_wcan.scroll_top_mode === 'mobile' &&
			!! yith_wcan.is_mobile
		) {
			scrollTopEnabled = true;
		} else if (
			yith_wcan.scroll_top_mode === 'desktop' &&
			! yith_wcan.is_mobile
		) {
			scrollTopEnabled = true;
		}

		if ( scrollTopEnabled ) {
			$( window ).scrollTop( $( yith_wcan.scroll_top ).offset().top );
		}

		$( yith_wcan.pagination ).hide();
		$( yith_wcan.result_count ).hide();

		if ( ajax_call ) {
			ajax_call.abort();
			ajax_call = false;
		}

		ajax_call = $.ajax( {
			url: href,
			success( response ) {
				ajax_call = false;
				$( yith_wcan.container )
					.not( '.ywcps-products' )
					.removeClass( 'yith-wcan-loading' );

				//container
				if (
					$( response )
						.find( yith_wcan.container )
						.not( '.ywcps-products' ).length > 0
				) {
					$( '.yit-wcan-container' ).html(
						$( response )
							.find( yith_wcan.container )
							.not( '.ywcps-products' )
					);
				} else {
					$( '.yit-wcan-container' ).html(
						$( response ).find( '.woocommerce-info' )
					);
				}

				//pagination
				if ( $( response ).find( yith_wcan.pagination ).length > 0 ) {
					//se non esiste lo creo
					if ( ! $( yith_wcan.pagination ).length ) {
						$.jseldom( yith_wcan.pagination ).insertAfter(
							$( yith_wcan.container ).not( '.ywcps-products' )
						);
					}

					$( yith_wcan.pagination )
						.html(
							$( response ).find( yith_wcan.pagination ).html()
						)
						.show();
				} else {
					$( yith_wcan.pagination ).empty();
				}

				// quantity fields
				$(
					'div.quantity:not(.buttons_added), td.quantity:not(.buttons_added)'
				)
					.addClass( 'buttons_added' )
					.append( '<input type="button" value="+" class="plus" />' )
					.prepend(
						'<input type="button" value="-" class="minus" />'
					);

				//result count
				if ( $( response ).find( yith_wcan.result_count ).length > 0 ) {
					$( yith_wcan.result_count )
						.html(
							$( response ).find( yith_wcan.result_count ).html()
						)
						.show();
				}

				const widget_reload = function ( el ) {
					const id = el.attr( 'id' ),
						widget_in_response = $( response ).find( '#' + id );

					if ( ! widget_in_response.length ) {
						el.hide();
					} else {
						el.html( widget_in_response.html() );
						el.show();
					}
				};

				//load new widgets
				$( '.yith-woo-ajax-navigation' )
					.add( '.yith-wcan-sort-by' )
					.add( '.yith-wcan-stock-on-sale' )
					.add( '.yith-wcan-list-price-filter' )
					.each( function () {
						widget_reload( $( this ) );
					} );

				/* === YooTheme Theme Support === */
				if ( yith_wcan.yootheme.is_enabled ) {
					$( '.widget-yith-woo-ajax-navigation' )
						.add( '.widget-yith-woo-ajax-navigation-sort-by' )
						.add( '.widget-yith-woo-ajax-reset-navigation' )
						.add( '.widget-yith-wcan-stock-on-sale' )
						.add( '.widget-yith-wcan-list-price-filter' )
						.each( function () {
							widget_reload( $( this ) );
						} );
				}

				/* === Avada Theme Support === */
				if ( yith_wcan.avada.is_enabled ) {
					const avada_sort_count = $( yith_wcan.avada.sort_count );
					avada_sort_count.html(
						$( response ).find( yith_wcan.avada.sort_count ).html()
					);

					if ( t.text() === '' ) {
						avada_sort_count.hide();
					} else {
						avada_sort_count.show();
					}
				}

				//update browser history (IE doesn't support it)
				if (
					yith_wcan.change_browser_url &&
					! navigator.userAgent.match( /msie/i )
				) {
					window.history.pushState(
						{ pageTitle: response.pageTitle },
						'',
						href
					);
				}

				//trigger ready event
				$( document ).trigger( 'ready' );
				$( document ).trigger( 'yith-wcan-ajax-filtered', [
					response,
				] );
				$( document ).trigger( 'yith_wcwl_reload_fragments' );
				$( window ).trigger( 'scroll' );
				if ( is_reset ) {
					if ( typeof $.fn.slider !== 'undefined' ) {
						const min_price = parseInt(
								$( yith_wcan.wc_price_slider.min_price ).data(
									'min'
								)
							),
							max_price = parseInt(
								$( yith_wcan.wc_price_slider.max_price ).data(
									'max'
								)
							);
						$( yith_wcan.wc_price_slider.wrapper ).slider(
							'values',
							[ min_price, max_price ]
						);
						$( document.body ).trigger( 'price_slider_slide', [
							min_price,
							max_price,
						] );
					}
					$( document ).trigger( 'yith-wcan-ajax-reset-filtered' );
				}

				//See al categories in ajax
				see_all_taxonomies();
			},
		} );
	};

	//wrap the container
	$( yith_wcan.container )
		.not( '.ywcps-products' )
		.wrap( '<div class="yit-wcan-container"></div>' );

	$( document ).on( 'yith-wcan-wrapped', function () {
		see_all_taxonomies();
	} );

	$( document ).trigger( 'yith-wcan-wrapped' );

	$( document ).on( 'click', '.yith-wcan a', function ( e ) {
		$( this ).yith_wcan_ajax_filters( e, this );
	} );

	/*AJAX NAVIGATION DROPDOWN STYLE*/
	function yit_open_select_dropdown( element ) {
		$( element )
			.parent()
			.find( 'div.yith-wcan-select-wrapper' )
			.css( 'z-index', '1' )
			.animate(
				{
					visibility: 'visible',
					opacity: 1,
				},
				{
					duration: 300,
					start() {
						const t = $( this );
						t.css( 'display', 'block' );
					},
				}
			);

		$( element )
			.parent()
			.find( 'a.yit-wcan-select-open' )
			.addClass( 'active' );
	}

	function yit_close_select_dropdown( element ) {
		$( element )
			.parent()
			.find( 'div.yith-wcan-select-wrapper' )
			.css( 'z-index', '-1' )
			.animate(
				{
					visibility: 'hidden',
					opacity: 0,
				},
				300,
				function () {
					const t = $( this );
					t.css( 'display', 'none' );
				}
			);

		$( element )
			.parent()
			.find( 'a.yit-wcan-select-open' )
			.removeClass( 'active' );
	}

	const yit_hidden_filters_wrapper = function () {
		$( 'div.yith-wcan-select-wrapper' )
			.css( 'z-index', '-1' )
			.animate(
				{
					visibility: 'hidden',
					opacity: 0,
				},
				0,
				function () {
					const t = $( this );
					t.css( 'display', 'none' );
				}
			);

		$( 'a.yit-wcan-select-open' ).removeClass( 'active' );
	};

	const yit_active_filter = function () {
		const filter_number = $(
			'div.yith-wcan-select-wrapper ul.yith-wcan-select li.chosen'
		).length;

		yit_hidden_filters_wrapper();

		$( 'div.yith-wcan-select-wrapper' ).each( function () {
			let filter_name = '';
			const chosen = $( this )
				.find( 'ul.yith-wcan-select li.chosen' )
				.each( function () {
					filter_name += $( this ).text() + ', ';
				} );

			filter_name = filter_name.substring( 0, filter_name.length - 2 );

			if ( filter_name !== '' ) {
				$( this )
					.parent()
					.find( 'a.yit-wcan-select-open' )
					.text( filter_name );
			}
		} );
	};

	$( document ).on( 'click', 'a.yit-wcan-select-open.active', function ( e ) {
		e.preventDefault();
		yit_close_select_dropdown( this );
	} );

	$( document ).on(
		'click',
		'a.yit-wcan-select-open:not(.active)',
		function ( e ) {
			e.preventDefault();
			//close other enbled dropdown
			$( 'a.yit-wcan-select-open.active' ).trigger( 'click' );
			yit_open_select_dropdown( this );
		}
	);

	$( document ).on( 'yith-wcan-ajax-filtered', yit_active_filter );

	yit_active_filter();
	yit_hidden_filters_wrapper();

	$( 'body' ).on( 'click', function ( e ) {
		if ( ! $( e.target ).hasClass( 'yit-wcan-select-open' ) ) {
			yit_hidden_filters_wrapper();
		}
	} );

	function RemoveParameterFromUrl( url, parameter ) {
		return url
			.replace(
				new RegExp( '[?&]' + parameter + '=[^&#]*(#.*)?$' ),
				'$1'
			)
			.replace( new RegExp( '([?&])' + parameter + '=[^&]*&' ), '$1' );
	}

	/* === Flatsome Theme Support === */
	if (
		yith_wcan.flatsome.is_enabled &&
		yith_wcan.flatsome.lazy_load_enabled
	) {
		$( document ).on(
			'yith-wcan-ajax-filtered',
			function ( event, response ) {
				//Lazy Load
				const context = $( document );
				jQuery( '.lazy-load', context ).each( function (
					index,
					element
				) {
					const $element = jQuery( element );
					const waypoint = $element.waypoint(
						function ( direction ) {
							if ( $element.hasClass( 'lazy-load-active' ) )
								return;
							const src = $element.data( 'src' );
							const srcset = $element.data( 'srcset' );
							if ( src ) $element.attr( 'src', src );
							if ( srcset ) $element.attr( 'srcset', srcset );
							$element.imagesLoaded( function () {
								$element
									.addClass( 'lazy-load-active' )
									.removeClass( 'lazy-load' );
							} );
							//this.destroy();
						},
						{ offset: '140%' }
					);
				} );
			}
		);
	}

	/* Browser History Back/Prev Button */
	window.addEventListener( 'popstate', function ( e ) {
		window.location.reload( true );
	} );
} );
