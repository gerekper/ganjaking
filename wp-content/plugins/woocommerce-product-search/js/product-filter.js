/*!
 * product-filter.js
 *
 * Copyright (c) "kento" Karim Rahimpur www.itthinx.com
 *
 * This code is provided subject to the license granted.
 * Unauthorized use and distribution is prohibited.
 * See COPYRIGHT.txt and LICENSE.txt
 *
 * This code is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 * This header and all notices must be kept intact.
 *
 * @author itthinx
 * @package woocommerce-product-search
 * @since 2.0.0
 */

var ixwpsf = {
	blinkerTimeouts : [],
	blinkerTimeout : 5000,
	productsBlinker : false,
	taxonomy : [],
	ts : 0,
	autoToggleFilterWidgets : true,
	embed : true,
	expandTime : 200,
	retractTime : 320,
	xhr : null
};

var ix_dropdown_thumbnails = [],
	ix_dropdown_order = [];

( function( $ ) {

	ixwpsf.productFilter = function( query, containers, args ) {
		var href                = decodeURI( window.location.href ),
			hrefIsAlt           = false,
			$products           = $( containers.products ).filter( function() { return $( this ).closest( '.related.products,[class^="wp-block-woocommerce"]' ).length < 1 } ),
			width               = $products.css( 'width' ),
			height              = $products.css( 'height' ),
			blinkerTimeout      = ixwpsf.blinkerTimeout,
			updateAddressBar    = true,
			updateDocumentTitle = true,
			origin_id           = typeof args.origin_id !== 'undefined' ? args.origin_id : null;

		if ( typeof args.href !== 'undefined' ) {
			if ( href !== args.href ) {
				href = args.href;
				hrefIsAlt = true;
			}
		}

		if ( typeof args.unpage_url === 'undefined' || args.unpage_url !== false ) {
			var unpage_regex = new RegExp( '\\/page\\/[0-9]+', 'gi' );
			var unpage_href = href.replace( unpage_regex, '' );
			unpage_href = ixwpsf.updateQueryArg( 'paged', '', unpage_href );

			unpage_href = ixwpsf.updateQueryArg( 'product-page', '', unpage_href );
			href = unpage_href;
		}

		var reset_href = null;
		if (
			typeof args.reset !== 'undefined' && args.reset &&
			typeof args.reset_url !== 'undefined'
		) {
			reset_href = decodeURI( args.reset_url );

			if ( typeof args.unpage_url === 'undefined' || args.unpage_url !== false ) {
				var unpage_regex = new RegExp( '\\/page\\/[0-9]+', 'gi' );
				reset_href = reset_href.replace( unpage_regex, '' );
				reset_href = ixwpsf.updateQueryArg( 'paged', '', reset_href );

				reset_href = ixwpsf.updateQueryArg( 'product-page', '', reset_href );
			}
		}

		if ( typeof args.term !== 'undefined' ) {

			var regex  = new RegExp( 'ixwpst\\[' + args.taxonomy + '\\]\\[[0-9]+\\]', 'g' );
			href = href.replace( regex, 'ixwpst[' + args.taxonomy + '][]' );
			if ( !$.isArray( args.term ) ) {
				switch ( args.action ) {
					case 'add' :
						href = ixwpsf.addQueryArg( 'ixwpst[' + args.taxonomy + '][]', args.term, href );
						break;
					case 'remove' :
						href = ixwpsf.removeQueryArg( 'ixwpst[' + args.taxonomy + '][]', args.term, href );
						break;
					default :
						href = ixwpsf.updateQueryArg( 'ixwpst[' + args.taxonomy + '][]', args.term, href );
				}
			} else {

				href = ixwpsf.updateQueryArg( 'ixwpst[' + args.taxonomy + '][]', '', href );
				for ( var i = 0; i < args.term.length; i++ ) {
					href = ixwpsf.addQueryArg( 'ixwpst[' + args.taxonomy + '][]', args.term[i], href );
				}
			}
		}

		var min_max_price = '';
		if ( typeof args.min_price !== 'undefined' ) {
			href = ixwpsf.updateQueryArg( 'min_price', args.min_price, href );
			min_max_price += args.min_price;
		} else {

			var maybe_min_price = ixwpsf.getQueryArg( href, 'min_price' );
			if ( maybe_min_price !== null && maybe_min_price.length > 0 ) {
				min_max_price += maybe_min_price;
			}
		}
		if ( typeof args.max_price !== 'undefined' ) {
			href = ixwpsf.updateQueryArg( 'max_price', args.max_price, href );
			min_max_price += args.max_price;
		} else {

			var maybe_max_price = ixwpsf.getQueryArg( href, 'max_price' );
			if ( maybe_max_price !== null && maybe_max_price.length > 0 ) {
				min_max_price += maybe_max_price;
			}
		}
		var ixwpsp = min_max_price.length > 0 ? '1' : '';
		href = ixwpsf.updateQueryArg( 'ixwpsp', ixwpsp, href );
		if ( ixwpsp !== '' ) {
			args.ixwpsp = ixwpsp;
		}

		if ( typeof args.blinkerTimeout !== 'undefined' ) {
			blinkerTimeout = args.blinkerTimeout;
		};

		if ( typeof args.updateAddressBar !== 'undefined' ) {
			updateAddressBar = args.updateAddressBar;
		};

		if ( typeof args.updateDocumentTitle !== 'undefined' ) {
			updateDocumentTitle = args.updateDocumentTitle;
		};

		query = $.trim( query );

		href = ixwpsf.updateQueryArg( 'ixwpss', encodeURIComponent( query ), href );

		if ( typeof args.title !== 'undefined' ) {
			href = ixwpsf.updateQueryArg( 'title', args.title, href );
		}
		if ( typeof args.excerpt !== 'undefined' ) {
			href = ixwpsf.updateQueryArg( 'excerpt', args.excerpt, href );
		}
		if ( typeof args.content !== 'undefined' ) {
			href = ixwpsf.updateQueryArg( 'content', args.content, href );
		}
		if ( typeof args.categories !== 'undefined' ) {
			href = ixwpsf.updateQueryArg( 'categories', args.categories, href );
		}
		if ( typeof args.attributes !== 'undefined' ) {
			href = ixwpsf.updateQueryArg( 'attributes', args.attributes, href );
		}
		if ( typeof args.tags !== 'undefined' ) {
			href = ixwpsf.updateQueryArg( 'tags', args.tags, href );
		}
		if ( typeof args.sku !== 'undefined' ) {
			href = ixwpsf.updateQueryArg( 'sku', args.sku, href );
		}

		if ( typeof args.order_by !== 'undefined' ) {
			href = ixwpsf.updateQueryArg( 'order_by', args.order_by, href );
		}
		if ( typeof args.order !== 'undefined' ) {
			href = ixwpsf.updateQueryArg( 'order', args.order, href );
		}

		if ( typeof args.wpml !== 'undefined' ) {
			href = ixwpsf.updateQueryArg( 'wpml', args.wpml, href );
		}

		if ( typeof args.terms !== 'undefined' ) {
			href = ixwpsf.updateQueryArg( 'terms', args.terms, href );
		}

		if ( ixwpsf.taxonomy.length > 0 ) {

			var origin_taxonomy = null,
				origin_index = null;
			if ( origin_id !== null ) {
				for ( var i = 0; i < ixwpsf.taxonomy.length; i++ ) {
					if ( typeof ixwpsf.taxonomy[i].origin_id !== 'undefined' ) {
						if ( ixwpsf.taxonomy[i].origin_id === origin_id ) {
							origin_taxonomy = ixwpsf.taxonomy[i].taxonomy;
							origin_index = i;
							break;
						}
					}
				}
			}
			for ( var i = 0; i < ixwpsf.taxonomy.length; i++ ) {

				if ( origin_taxonomy !== null && ixwpsf.taxonomy[i].taxonomy === origin_taxonomy && i !== origin_index ) {
					continue;
				}
				if ( typeof ixwpsf.taxonomy[i].show !== 'undefined' ) {
					href = ixwpsf.updateQueryArg( 'ixwpsf[taxonomy][' + ixwpsf.taxonomy[i].taxonomy + '][show]', ixwpsf.taxonomy[i].show, href );
				}
				if ( typeof ixwpsf.taxonomy[i].multiple !== 'undefined' ) {
					href = ixwpsf.updateQueryArg( 'ixwpsf[taxonomy][' + ixwpsf.taxonomy[i].taxonomy + '][multiple]', ixwpsf.taxonomy[i].multiple ? 1 : 0, href );
				}
				if ( typeof ixwpsf.taxonomy[i].filter !== 'undefined' ) {
					href = ixwpsf.updateQueryArg( 'ixwpsf[taxonomy][' + ixwpsf.taxonomy[i].taxonomy + '][filter]', ixwpsf.taxonomy[i].filter ? 1 : 0, href );
				}
			}
		}

		if ( typeof args.ixwpse !== 'undefined' ) {
			href = ixwpsf.updateQueryArg( 'ixwpse', args.ixwpse, href );
		}
		if ( typeof args.on_sale !== 'undefined' ) {
			href = ixwpsf.updateQueryArg( 'on_sale', args.on_sale, href );
		}
		if ( typeof args.rating !== 'undefined' ) {
			href = ixwpsf.updateQueryArg( 'rating', args.rating, href );
		}

		$( containers.field ).addClass( 'blinker' );
		if ( ixwpsf.productsBlinker ) {
			$products.addClass( 'product-search-filter-blinker' ).html( '' ).show();
		}
		$products.css( 'minWidth', width );
		$products.css( 'minHeight', height );
		$( containers.pagination ).hide();
		$( containers.count ).hide();

		if ( blinkerTimeout > 0 ) {
			ixwpsf.blinkerTimeouts[containers.field] = setTimeout(
				function(){
					$( containers.field ).removeClass( 'blinker' );
				},
				blinkerTimeout
			);
			ixwpsf.blinkerTimeouts[containers.products] = setTimeout(
				function(){
					$products.removeClass( 'product-search-filter-blinker' );
				},
				blinkerTimeout
			);
		}

		var r_start = ( typeof Date.now === 'function' ) ? Date.now() : 0;

		if ( reset_href !== null ) {
			href = reset_href;
		}
		var e_href = ixwpsf.updateQueryArg( 'ixmbd', ixwpsf.embed ? '1' : '', href );

		$( 'body' ).addClass( 'product-search-filter-loading' );

		if ( ixwpsf.xhr !== null ) {
			ixwpsf.xhr.abort();
		}
		ixwpsf.xhr = $.ajax({
			url : e_href,
			error : function( data, textStatus, jqXHR ) {
				if ( typeof textStatus === 'undefined' || textStatus !== 'abort' ) {
					$( 'body' ).removeClass( 'product-search-filter-loading' );
					$( '.loading-term-filter' ).removeClass( 'loading-term-filter' );
					$( containers.field ).removeClass( 'blinker' );
					$( containers.pagination ).show();
					$( containers.count ).show();
				}
			},
			success : function( data, textStatus, jqXHR ) {

				$( 'body' ).removeClass( 'product-search-filter-loading' );

				ixwpsf.xhr = null;

				if ( r_start >= ixwpsf.ts ) {
					ixwpsf.ts = r_start;
				} else {
					return;
				}

				var breadcrumb = $( data ).find( containers.breadcrumb );
				if ( breadcrumb.length > 0 ) {
					$( containers.breadcrumb ).replaceWith( breadcrumb );
					$( containers.breadcrumb ).show();
				} else {
					$( containers.breadcrumb ).html( '' );
					$( containers.breadcrumb ).hide();
				}

				var header = $( data ).find( containers.header );
				if ( header.length > 0 ) {
					$( containers.header ).replaceWith( header );
					$( containers.header ).show();
				} else {
					$( containers.header ).html( '' );
					$( containers.header ).hide();
				}

				$( containers.field ).removeClass( 'blinker' );
				if ( ixwpsf.productsBlinker ) {
					$products.removeClass( 'product-search-filter-blinker' );
				} else {
					$products.html( '' ).show();
				}
				if ( blinkerTimeout > 0 ) {
					clearTimeout( ixwpsf.blinkerTimeouts[containers.field] );
					clearTimeout( ixwpsf.blinkerTimeouts[containers.products] );
				}
				var newProducts = $( data ).find( containers.products ).filter( function() { return $( this ).closest( '.related.products,[class^="wp-block-woocommerce"]' ).length < 1 } );
				var warn_products_container = false,
					warn_product_container = false;
				if ( newProducts.length === 0 ) {
					warn_products_container = true;
				}
				if ( $( newProducts ).find( containers.product ).length > 0 ) {
					if ( $products.length > 0 ) {

						$products.show().replaceWith( newProducts );
					} else {

						$( containers.header ).parent().replaceWith(
							$( data ).find( containers.header ).parent()
						);

						warn_products_container = false;
					}
					$( containers.info ).remove();
					$( containers.ordering ).show();
				} else {
					if ( $products.length > 0 ) {
						$( containers.info ).remove();
						$products.html( '' ).hide().after( $( data ).find( containers.info ) );
						$( containers.ordering ).hide();
						warn_products_container = false;
					}

					if ( $( data ).find( containers.info ).length === 0 ) {
						warn_product_container = true;
					}
				}
				if ( warn_products_container ) {

					console.log( 'Could not find the general Products Container, expected the selector ' + containers.products );
				}
				if ( !warn_products_container && warn_product_container ) {
					console.log( 'Could not find the individual Product Container, expected the selector ' + containers.product );
				}

				var paginations = $( data ).find( containers.pagination );
				$.each( $( containers.pagination ), function( index, value ) {
					if ( paginations.eq( index ).length > 0 ) {
						$( containers.pagination ).eq( index ).replaceWith( paginations.eq( index ) );
					} else {
						$( containers.pagination ).eq( index ).html( '' );
					}
				});

				var counts = $( data ).find( containers.count );
				$.each( $( containers.count ), function( index, value ) {
					if ( counts.eq( index ).length > 0 ) {
						$( containers.count ).eq( index ).replaceWith( counts.eq( index ) );
					} else {
						$( containers.count ).eq( index ).html( '' );
					}
				});

				$( containers.pagination ).show();
				$( containers.count ).show();

				if ( ! hrefIsAlt ) {

					if ( updateAddressBar ) {
						window.history.pushState( { query:query, href:href }, '', href );
					}

					if ( updateDocumentTitle ) {
						var title = $( data ).filter( 'title' ).text();
						document.title = title;
					}
				}

				ixwpsf.updateOrderingForm( query, containers, args, href );

				if ( reset_href !== null ) {
					$( '.product-filter-field' ).val( '' ); // search field input element
					$( '.product-filter-field' ).parent().find( '.product-search-filter-search-clear' ).hide();
				}

				if ( typeof $().slider !== 'undefined' ) {
					var sliders = $( data ).find( '.product-search-filter-price-slider' );
					$.each( $( '.product-search-filter-price-slider' ), function( index, value ) {
						if ( sliders.eq( index ).length > 0 ) {
							var min_price = sliders.eq( index ).data( 'min_price' ),
								max_price = sliders.eq( index ).data( 'max_price' ),
								precision = sliders.eq( index ).data( 'precision' ),
								min_price_display = isNaN( parseFloat( min_price ) ) ? min_price : parseFloat( min_price ).toFixed( precision ),
								max_price_display = isNaN( parseFloat( max_price ) ) ? max_price : parseFloat( max_price ).toFixed( precision );
							$( value ).slider( 'option', 'min', min_price );
							$( value ).slider( 'option', 'max', max_price );
							$( value ).find( 'span.slider-limit-min' ).text( min_price_display );
							$( value ).find( 'span.slider-limit-max' ).text( max_price_display );

							$( value ).data( 'min_price', min_price );
							$( value ).data( 'max_price', max_price );

							var new_slider_min = sliders.eq( index ).find( '.slider-min' ).text(),
								new_slider_max = sliders.eq( index ).find( '.slider-max' ).text();
								new_slider_min_display = isNaN( parseFloat( new_slider_min ) ) ? new_slider_min : parseFloat( new_slider_min ).toFixed( precision ),
								new_slider_max_display = isNaN( parseFloat( new_slider_max ) ) ? new_slider_max : parseFloat( new_slider_max ).toFixed( precision );
							$( value ).parent().find( '.slider-min' ).text( new_slider_min_display );
							$( value ).parent().find( '.slider-max' ).text( new_slider_max_display );

							if ( reset_href === null ) {

								var min1 = min_price;
								if ( new_slider_min !== '' ) {
									min1 = wps_price_slider.easeOut( new_slider_min, min_price, max_price );
								}

								var max1 = max_price;
								if ( new_slider_max !== '' ) {
									max1 = wps_price_slider.easeOut( new_slider_max, min_price, max_price );
								}

								$( value ).slider( 'disable' );

								$( value ).slider( 'values', [min1, max1] );
								$( value ).slider( 'values', [min1, max1] );
								$( value ).slider( 'enable' );

								$( value ).parent().find( 'input.product-search-filter-min-price' ).val( new_slider_min_display );
								$( value ).parent().find( 'input.product-search-filter-max-price' ).val( new_slider_max_display );
							} else {

								$( value ).slider( 'disable' );

								$( value ).slider( 'values', [min_price, max_price] );
								$( value ).slider( 'values', [min_price, max_price] );
								$( value ).slider( 'enable' );

								$( value ).parent().find( 'input.product-search-filter-min-price' ).val( '' ).trigger( 'clear' );
								$( value ).parent().find( 'input.product-search-filter-max-price' ).val( '' ).trigger( 'clear' );
								var slider_min = $( value ).parent().find( '.slider-min' ),
									slider_max = $( value ).parent().find( '.slider-max' );
								slider_min.text( '' ); // slider_min.text( min_price );
								slider_max.text( '' ); // slider_max.text( max_price );
								$( value ).parent().parent().find( '.product-search-filter-price-clear' ).hide();
							}
						}
					} );
					if ( typeof wps_price_slider !== 'undefined' ) {
						wps_price_slider.updateForm( query, '.product-search-filter-price-slider', args, href );
					}
				}

				if ( reset_href !== null ) {
					$.each( $( '.product-search-filter-price' ), function( index, value ) {
						var has_slider = $( value ).find( '.product-search-filter-price-slider' ).length > 0;
						if ( !has_slider ) {

							$( value ).find( 'input.product-search-filter-min-price' ).val( '' ).trigger( 'clear' );
							$( value ).find( 'input.product-search-filter-max-price' ).val( '' ).trigger( 'clear' );
							$( value ).find( '.product-search-filter-price-clear' ).hide();
						}
					} );
				}

				$( data ).find( '.product-search-filter-items option' ).each( function( index, element ) {
					var value = $( element ).val(),
						element_data = $( element ).data();
					ix_dropdown_thumbnails[value] = element_data;
				} );

				$( '.product-search-filter-terms' ).each( function( index, element ) {
					var newContainer = $( data ).find( '#' + this.id );
					if ( newContainer.length > 0 ) {

						var selects = $( this ).find( 'select.selectized' );
						if ( selects.length === 0 ) {

							$( '#' + this.id ).replaceWith( newContainer );
						} else {

							var new_select = newContainer.find( '#' + selects.first()[0].id );
							if ( new_select.length > 0 ) {
								new_select = new_select.first();

								var element_order = [];
								$( new_select ).find( 'option' ).each( function( el_index, el ) {
									$( el ).data( 'data', { $order:el_index } );
									element_order[$( el ).val()] = el_index;
								} );
								ix_dropdown_order[new_select.id] = element_order;

								$( '#' + this.id ).replaceWith( newContainer );

								new_select.trigger( 'apply-selectize' );
							}
						}

						$( newContainer ).find( '.product-search-product_cat-filter-item a' ).on( 'click', ixwpsf.categoryFilterItemOnClick );
						$( newContainer ).find( 'select.product-search-filter-product_cat' ).on( 'change', ixwpsf.categoryFilterSelectOnChange );
						$( newContainer ).find( 'select.product-search-filter-attribute' ).on( 'change', ixwpsf.attributeFilterSelectOnChange );
						$( newContainer ).find( '.product-search-product_tag-filter-item' ).on( 'click', ixwpsf.tagFilterItemOnClick );
						$( newContainer ).find( '.product-search-attribute-filter-item a' ).on( 'click', ixwpsf.attributeFilterItemOnClick );
					}
				} );

				$( '.product-search-filter-extras' ).each( function( index, element ) {
					var newContainer = $( data ).find( '#' + this.id );
					if ( newContainer.length > 0 ) {
						$( '#' + this.id ).replaceWith( newContainer );
					}
				} );

				ixwpsf.toggleWidgets();

				$( '.product-search-filter-reset .product-search-filter-reset-clear' ).removeClass( 'loading-reset-filter' );

				$( '.product-search-filter-reset' ).trigger( 'ixProductFilterRequestDone' );

				$( document ).triggerHandler( 'ixProductFilterRequestProcessed' );
			}
		});
	};

	ixwpsf.updateOrderingForm = function( query, containers, args, href ) {
		var orderingForm = $( containers.ordering ).closest( 'form' );
		if ( orderingForm.length > 0 ) {
			if ( query.length > 0 ) {
				var ixwpss = $( orderingForm ).find( 'input[name="ixwpss"]' );
				if ( ixwpss.length > 0 ) {
					ixwpss.val( query );
				} else {
					$( orderingForm ).append( '<input type="hidden" name="ixwpss" value="' + query + '"/>' );
				}
			}
			if ( typeof args.title !== 'undefined' ) {
				var title = $( orderingForm ).find( 'input[name="title"]' );
				if ( title.length > 0 ) {
					title.val( args.title );
				} else {
					$( orderingForm ).append( '<input type="hidden" name="title" value="' + args.title + '"/>' );
				}
			} else {
				$( orderingForm ).find( 'input[name="title"]' ).remove();
			}
			if ( typeof args.excerpt !== 'undefined' ) {
				var excerpt = $( orderingForm ).find( 'input[name="excerpt"]' );
				if ( excerpt.length > 0 ) {
					excerpt.val( args.excerpt );
				} else {
					$( orderingForm ).append( '<input type="hidden" name="excerpt" value="' + args.excerpt + '"/>' );
				}
			} else {
				$( orderingForm ).find( 'input[name="excerpt"]' ).remove();
			}
			if ( typeof args.content !== 'undefined' ) {
				var content = $( orderingForm ).find( 'input[name="content"]' );
				if ( content.length > 0 ) {
					content.val( args.content );
				} else {
					$( orderingForm ).append( '<input type="hidden" name="content" value="' + args.content + '"/>' );
				}
			} else {
				$( orderingForm ).find( 'input[name="content"]' ).remove();
			}
			if ( typeof args.categories !== 'undefined' ) {
				var categories = $( orderingForm ).find( 'input[name="categories"]' );
				if ( categories.length > 0 ) {
					categories.val( args.categories );
				} else {
					$( orderingForm ).append( '<input type="hidden" name="categories" value="' + args.categories + '"/>' );
				}
			} else {
				$( orderingForm ).find( 'input[name="categories"]' ).remove();
			}
			if ( typeof args.attributes !== 'undefined' ) {
				var attributes = $( orderingForm ).find( 'input[name="attributes"]' );
				if ( attributes.length > 0 ) {
					attributes.val( args.attributes );
				} else {
					$( orderingForm ).append( '<input type="hidden" name="attributes" value="' + args.attributes + '"/>' );
				}
			} else {
				$( orderingForm ).find( 'input[name="attributes"]' ).remove();
			}
			if ( typeof args.tags !== 'undefined' ) {
				var tags = $( orderingForm ).find( 'input[name="tags"]' );
				if ( tags.length > 0 ) {
					tags.val( args.tags );
				} else {
					$( orderingForm ).append( '<input type="hidden" name="tags" value="' + args.tags + '"/>' );
				}
			} else {
				$( orderingForm ).find( 'input[name="tags"]' ).remove();
			}
			if ( typeof args.sku !== 'undefined' ) {
				var sku = $( orderingForm ).find( 'input[name="sku"]' );
				if ( sku.length > 0 ) {
					sku.val( args.sku );
				} else {
					$( orderingForm ).append( '<input type="hidden" name="sku" value="' + args.sku + '"/>' );
				}
			} else {
				$( orderingForm ).find( 'input[name="sku"]' ).remove();
			}
			if ( typeof args.wpml !== 'undefined' ) {
				var wpml = $( orderingForm ).find( 'input[name="wpml"]' );
				if ( wpml.length > 0 ) {
					wpml.val( args.wpml );
				} else {
					$( orderingForm ).append( '<input type="hidden" name="wpml" value="' + args.wpml + '"/>' );
				}
			} else {
				$( orderingForm ).find( 'input[name="wpml"]' ).remove();
			}

			$( orderingForm ).find( 'input[name="min_price"]' ).remove();
			$( orderingForm ).find( 'input[name="max_price"]' ).remove();
			$( orderingForm ).find( 'input[name^="ixwpst"]' ).remove();
			$( orderingForm ).find( 'input[name^="ixwpsf"]' ).remove();
			$( orderingForm ).find( 'input[name="ixwpse"]' ).remove(); // @since 2.19.0
			$( orderingForm ).find( 'input[name="on_sale"]' ).remove(); // @since 2.19.0
			$( orderingForm ).find( 'input[name="rating"]' ).remove(); // @since 2.20.0

			var params = href.substring( href.indexOf( '?' ) + 1 );
			var hash = params.indexOf( '#' );
			if ( hash >= 0 ) {
				params = params.substring( 0, hash );
			}
			params = params.split( '&' );
			if ( params.length > 0 ) {
				for ( var i = 0; i < params.length; i++ ) {
					var pair  = params[i].split( '=' ),
						key   = '',
						value = '';
					key = unescape( pair[0] );
					if ( pair.length > 1 ) {
						value = unescape( pair[1] );
					}
					if (
						key !== '' && key !== 'orderby' && key !== 'ixwpss' &&
						key !== 'title' && key !== 'excerpt' && key !== 'content' &&
						key !== 'categories' && key !== 'attributes' && key !== 'tags' && key !== 'sku' && key !== 'wpml' &&
						(
							key === 'min_price' ||
							key === 'max_price' ||
							key.indexOf( 'ixwpst' ) === 0 ||
							key.indexOf( 'ixwpsf' ) === 0 ||
							key === 'ixwpse' || // @since 2.19.0
							key === 'on_sale' || // @since 2.19.0
							key === 'rating' // @since 2.20.0
						) // (*)
					) {
						var field = $( orderingForm ).find( 'input[name="' + key + '"]' );

						if ( field.length > 0 && !key.endsWith( '[]' ) ) {
							field.val( value );
						} else {

							$( orderingForm ).append( $( '<input type="hidden" name="' + key + '" />' ).attr( { value:value } ) );
						}
					}
				}
			}

			if ( typeof args.ixwpsp !== 'undefined' ) {
				var ixwpsp = $( orderingForm ).find( 'input[name="ixwpsp"]' );
				if ( ixwpsp.length > 0 ) {
					ixwpsp.val( args.ixwpsp );
				} else {
					$( orderingForm ).append( '<input type="hidden" name="ixwpsp" value="' + args.ixwpsp + '"/>' );
				}
			} else {
				$( orderingForm ).find( 'input[name="ixwpsp"]' ).remove();
			}

		}
	};

	ixwpsf.updateQueryArg = function( key, value, url ) {
		url = ixwpsf.decodeURI( url );

		var rkey   = key.replace( /\[/g, '\\[' ).replace( /\]/g, '\\]' ),
			regex  = new RegExp( '([?&])' + rkey + '=.*?(&|#|$)', 'g' ),
			result = null;

		if ( typeof url === 'undefined' ) {
			url = window.location.href;
		}

		if ( value === '' ) {

			regex = new RegExp( '([?&])' + rkey + '=([^&#$]*)', 'g' );
			result = url.replace( regex, '$1' );
			result = result.replace( '?&', '?' ).replace( /&&+/g, '&' ).replace( /&$/, '' );
		} else {
			var tmp;
			if ( tmp = url.match( regex ) ) {

				rmregex = new RegExp( '([?&])' + rkey + '=([^&#$]*)', 'g' );
				result = url.replace( rmregex, '$1' );
				result = result.replace( '?&', '?' ).replace( /&&+/g, '&' ).replace( /&$/, '' );
				result = ixwpsf.addQueryArg( key, value, result );
			} else {
				var hash      = '',
					separator = url.indexOf( '?' ) !== -1 ? '&' : '?';
				if ( url.indexOf( '#' ) !== -1 ) {

					hash = url.replace( /.*#/, '#' );

					url = url.replace( /#.*/, '' );
				}
				result = url + separator + key + '=' + value + hash;
			}
		}
		return result;
	};

	ixwpsf.getQueryArgs = function( url ) {
		url = ixwpsf.decodeURI( url );
		var regex   = /[^=&?]+\s*=\s*[^&#]*/g;
		var matches = url.match( regex );
		var pairs   = [];
		if ( matches !== null ) {
			for ( var i = 0; i < matches.length; i++ ) {
				var pair = matches[i].split( '=' );
				pairs.push( pair );
			}
		}
		return pairs;
	};

	ixwpsf.getQueryArg = function( url, key ) {
		url = ixwpsf.decodeURI( url );
		var regex   = new RegExp( "[&?]" + key + "\s*=\s*[^&#]*", "g" );
		var matches = url.match( regex );
		var result  = null;
		if ( matches !== null ) {
			for ( var i = 0; i < matches.length; i++ ) {
				var pair = matches[i].split( '=' );
				result = pair[1];
				break;
			}
		}
		return result;
	};

	ixwpsf.addQueryArg = function( key, value, url ) {
		url = ixwpsf.decodeURI( url );
		var add = true;
		if ( typeof url === 'undefined' ) {
			url = window.location.href;
		}
		var pairs = ixwpsf.getQueryArgs( url );
		for ( var i = 0; i < pairs.length; i++ ) {
			if ( pairs[i][0] == key && pairs[i][1] == value ) {
				add = false;
				break;
			}
		}
		if ( add ) {
			var hash      = '',
				separator = url.indexOf( '?' ) !== -1 ? '&' : '?';
			if ( url.indexOf( '#' ) !== -1 ) {

				hash = url.replace( /.*#/, '#' );

				url = url.replace( /#.*/, '' );
			}
			url = url + separator + key + '=' + value + hash;
		}
		return url;
	};

	ixwpsf.removeQueryArg = function( key, value, url ) {
		url = ixwpsf.decodeURI( url );
		var rkey   = key.replace( /\[/g, '\\[' ).replace( /\]/g, '\\]' ),
			regex  = new RegExp( '([?&])' + rkey + '=' + value + '(&|#|$)', 'i' ),
			result = url.replace( regex, '$1$2' ).replace( '?&', '?' ).replace( /&&+/g, '&' ).replace( /&$/, '' );
		return result;
	};

	ixwpsf.categoryFilterItemOnClick = function( e ) {

		var container = $( this ).closest( '.product-search-filter-terms' ),
			origin_id = $( container ).attr( 'id' ),
			multiple  = $( container ).data( 'multiple' ),
			clear     = $( this ).parent().data( 'term' ) === '';

		if ( multiple && !clear ) {
			var action = 'add';
		} else {
			var action = 'replace';
		}

		if ( typeof e.preventDefault === 'function' ) {

			e.preventDefault();
		}
		if ( typeof e.stopImmediatePropagation === 'function' ) {
			e.stopImmediatePropagation();
		}
		if ( typeof e.stopPropagation === 'function' ) {
			e.stopPropagation();
		}
		if ( $( this ).parent().hasClass( 'current-cat' ) ) {
			$( this ).addClass( 'loading-term-filter' );
			if ( multiple ) {
				$( this ).parent().removeClass( 'current-cat' );
				$( '.product-filter-field' ).first().trigger( 'ixTermFilter', [ $( this ).parent().data( 'term' ), 'product_cat', 'remove', origin_id ] );
			} else {
				$( '.product-search-product_cat-filter-item' ).removeClass( 'current-cat' );
				$( '.product-filter-field' ).first().trigger( 'ixTermFilter', [ '', 'product_cat', null, origin_id ] );
			}
		} else {
			if ( !multiple ) {
				$( '.product-search-product_cat-filter-item' ).removeClass( 'current-cat' );
			}
			$( this ).parent().addClass( 'current-cat' );
			$( this ).addClass( 'loading-term-filter' );
			$( '.product-filter-field' ).first().trigger( 'ixTermFilter', [ $( this ).parent().data( 'term' ), 'product_cat', action, origin_id ] );
		}
		return false;
	};

	ixwpsf.categoryFilterSelectOnChange = function( e ) {
		var container = $( this ).closest( '.product-search-filter-terms' ),
			origin_id = $( container ).attr( 'id' ),
			multiple  = $( container ).data( 'multiple' ),
			clear     = false; // $( this ).val() === ''; <-- this won't do when the select is empty

		var selected_terms = $( this ).val();
		if (
			selected_terms === null ||
			selected_terms === '' ||
			( typeof selected_terms === 'object'  ) && selected_terms.length === 0
		) {
			selected_terms = '';
			clear = true;
		}

		if ( multiple && !clear ) {
			var action = 'add';
		} else {
			var action = 'replace';
		}

		if ( typeof e.preventDefault === 'function' ) {

			e.preventDefault();
		}
		if ( typeof e.stopImmediatePropagation === 'function' ) {
			e.stopImmediatePropagation();
		}
		if ( typeof e.stopPropagation === 'function' ) {
			e.stopPropagation();
		}

		$( '.product-filter-field' ).first().trigger( 'ixTermFilter', [ selected_terms, 'product_cat', action, origin_id ] );
		return false;
	};

	ixwpsf.attributeFilterSelectOnChange = function( e ) {
		var container = $( this ).closest( '.product-search-filter-terms' ),
			origin_id = $( container ).attr( 'id' ),
			multiple  = $( container ).data( 'multiple' ),
			clear     = false,
			taxonomy  = $( this ).data( 'taxonomy' );

		var selected_terms = $( this ).val(); // see note in ixwpsf.categoryFilterSelectOnChange()
		if (
			selected_terms === null ||
			selected_terms === '' ||
			( typeof selected_terms === 'object'  ) && selected_terms.length === 0
		) {
			selected_terms = '';
			clear = true;
		}

		if ( multiple && !clear ) {
			var action = 'add';
		} else {
			var action = 'replace';
		}

		if ( typeof e.preventDefault === 'function' ) {

			e.preventDefault();
		}
		if ( typeof e.stopImmediatePropagation === 'function' ) {
			e.stopImmediatePropagation();
		}
		if ( typeof e.stopPropagation === 'function' ) {
			e.stopPropagation();
		}

		$( '.product-filter-field' ).first().trigger( 'ixTermFilter', [ selected_terms, taxonomy, action, origin_id ] );
		return false;
	};

	ixwpsf.tagFilterItemOnClick = function( e ) {

		var container = $( this ).closest( '.product-search-filter-terms' ),
			origin_id = $( container ).attr( 'id' ),
			multiple  = $( container ).data( 'multiple' ),
			clear     = $( this ).data( 'term' ) === ''; // if we ever had a select as an option to show tags, see notes on required adjustments in ixwpsf.categoryFilterSelectOnChange()

		if ( multiple && !clear ) {
			var action = 'add';
		} else {
			var action = 'replace';
		}

		if ( typeof e.preventDefault === 'function' ) {

			e.preventDefault();
		}
		if ( typeof e.stopImmediatePropagation === 'function' ) {
			e.stopImmediatePropagation();
		}
		if ( typeof e.stopPropagation === 'function' ) {
			e.stopPropagation();
		}
		if ( $( this ).hasClass( 'current-tag' ) ) {
			$( this ).addClass( 'loading-term-filter' );
			if ( multiple ) {
				$( this ).removeClass( 'current-tag' );
				$( '.product-filter-field' ).first().trigger( 'ixTermFilter', [ $( this ).data( 'term' ), 'product_tag', 'remove', origin_id ] );
			} else {
				$( '.product-search-product_tag-filter-item' ).removeClass( 'current-tag' );
				$( '.product-filter-field' ).first().trigger( 'ixTermFilter', [ '', 'product_tag', null, origin_id ] );
			}
		} else {
			if ( !multiple ) {
				$( '.product-search-product_tag-filter-item' ).removeClass( 'current-tag' );
			}
			$( this ).addClass( 'current-tag' );
			$( this ).addClass( 'loading-term-filter' );
			$( '.product-filter-field' ).first().trigger( 'ixTermFilter', [ $( this ).data( 'term' ), 'product_tag', action, origin_id ] );
		}
		return false;
	};

	ixwpsf.attributeFilterItemOnClick = function( e ) {

		var container = $( this ).closest( '.product-search-filter-terms' ),
			origin_id = $( container ).attr( 'id' ),
			multiple  = $( container ).data( 'multiple' ),
			clear     = $( this ).parent().data( 'term' ) === '';

		if ( multiple && !clear ) {
			var action = 'add';
		} else {
			var action = 'replace';
		}

		if ( typeof e.preventDefault === 'function' ) {

			e.preventDefault();
		}
		if ( typeof e.stopImmediatePropagation === 'function' ) {
			e.stopImmediatePropagation();
		}
		if ( typeof e.stopPropagation === 'function' ) {
			e.stopPropagation();
		}
		if ( $( this ).parent().hasClass( 'current-attribute' ) ) {
			$( '.product-search-' + $( this ) .parent().data( 'taxonomy' ) + '-filter-item' ).removeClass( 'current-attribute' );
			$( this ).addClass( 'loading-term-filter' );
			if ( multiple ) {
				$( '.product-filter-field' ).first().trigger( 'ixTermFilter', [ $( this ).parent().data( 'term' ), $( this ).parent().data( 'taxonomy' ), 'remove', origin_id ] );
			} else {
				$( '.product-filter-field' ).first().trigger( 'ixTermFilter', [ '', $( this ).parent().data( 'taxonomy' ), null, origin_id ] );
			}
		} else {
			$( this ).parent().addClass( 'current-attribute' );
			$( this ).addClass( 'loading-term-filter' );
			if ( $( this ).parent().data( 'term' ) === 'undefined' || $( this ).parent().data( 'term' ) === '' ) {
				$( '.product-filter-field' ).first().trigger( 'ixTermFilter', [ '', $( this ).parent().data( 'taxonomy' ), null, origin_id ] );
			} else {
				$( '.product-filter-field' ).first().trigger( 'ixTermFilter', [ $( this ).parent().data( 'term' ), $( this ).parent().data( 'taxonomy' ), action, origin_id ] );
			}
		}

		return false;
	};

	ixwpsf.decodeURI = function( uri ) {
		try {
			uri = decodeURI( uri );
		} catch( e ) {
			uri = uri.replace( /%5B/g, '[' ).replace( /%5D/g, ']' ).replace( /%20/g, ' ' );
		}
		return uri;
	};

	ixwpsf.toggleWidgets = function() {
		if ( ixwpsf.autoToggleFilterWidgets ) {
			$( '.product-search-filter-items' ).each( function() {
				if ( ! $( this ).is( 'select' ) ) {

					if ( ! $( this ).is( '.selectize-control,.selectize-dropdown' ) ) {
						var show = $( this ).children().length > 0;

						if ( show || $( this ).hasClass( 'product-search-filter-toggle-widget' ) ) {
							$( this ).closest( '.widget' ).toggle( show );
						}

						if ( show || $( this ).hasClass( 'product-search-filter-toggle' ) ) {
							$( this ).toggle( show );
						}
					}
				} else {

					if ( !$( this ).is( 'select.selectized' ) ) {
						var options = $( this ).children(),
							show = true;
						if ( options.length === 0 ) {
							show = false;
						} else if ( options.length === 1 ) {
							var value = options.first().val();
							if ( typeof value === 'undefined' || value === '' ) {
								show = false;
							}
						}
					} else {

						var show = true;
						if ( typeof this.selectize !== 'undefined' && typeof this.selectize.options !== 'undefined' ) {
							var object_options = Object.keys(this.selectize.options).length;
							if ( object_options === 0 ) {
								show = false;
							} else if ( object_options === 1 ) {
								for ( i in this.selectize.options ) {
									if ( typeof this.selectize.options[i].value === 'undefined' || this.selectize.options[i].value === '' ) {
										show = false;
										break;
									}
								}
							}
						}
					}

					if ( show || $( this ).hasClass( 'product-search-filter-toggle-widget' ) ) {
						$( this ).closest( '.widget' ).toggle( show );
					}
					if ( show || $( this ).hasClass( 'product-search-filter-toggle' ) ) {

						$( this ).closest( '.product-search-filter-terms' ).find( '.selectize-control' ).toggle( show );

						$( this ).filter( 'select:not(.apply-selectize)' ).toggle( show );

					}
				}
			} );
		}
	};

	$( document ).ready( function() {

		$( document ).on( 'mouseenter', '.expandable.auto-expand', function( event ) {

			if ( typeof event.preventDefault === 'function' ) {
				event.preventDefault();
			}
			if ( typeof event.stopImmediatePropagation === 'function' ) {
				event.stopImmediatePropagation();
			}
			if ( typeof event.stopPropagation === 'function' ) {
				event.stopPropagation();
			}

			var expander = $( this ).find( '.term-expander' ).first();
			var locked = false;
			if ( typeof expander.data( 'locked' ) !== 'undefined' ) {
				locked = expander.data( 'locked' );
			}
			if ( locked ) {
				return;
			}

			$( this ).addClass( 'expanded' );
			var tmp = $( this ).find( '> ul' );
			tmp.stop( true, true ).show( ixwpsf.expandTime );

			if ( $( this ).hasClass( 'auto-retract' ) ) {
				$( this ).closest( '.product-search-filter-items' ).on( 'mouseleave.ixwpsf', function( e ) {
					tmp.parent().removeClass( 'expanded' );
					tmp.stop( true, true ).hide( ixwpsf.retractTime );
					$( this ).off( 'mouseleave.ixwpsf' );
				} );
			}
		} );

		$( document ).on( 'click touchstart', '.term-expander', function( event ) {

			if ( typeof event.preventDefault === 'function' ) {
				event.preventDefault();
			}
			if ( typeof event.stopImmediatePropagation === 'function' ) {
				event.stopImmediatePropagation();
			}
			if ( typeof event.stopPropagation === 'function' ) {
				event.stopPropagation();
			}

			var expander = $( this ).closest( '.term-expander' );
			var stop = false;
			var locked = false;
			if ( typeof expander.data( 'locked' ) !== 'undefined' ) {
				locked = expander.data( 'locked' );
			}
			switch ( locked ) {
				case true :
					stop = true;
					break;
				default :
					expander.data( 'locked', true );
					var lockedTimeout = setTimeout(
						function() {
							expander.data( 'locked', false );
							expander.data( 'lockedTimeout', null );
						},
						500
					);
					expander.data( 'lockedTimeout', lockedTimeout );
			}
			if ( stop ) {
				return;
			}

			var item = $( this ).closest( '.expandable' );
			var tmp = item.find( '> ul' );
			if ( !item.hasClass( 'expanded' ) ) {
				item.addClass( 'expanded' );
				tmp.show( ixwpsf.expandTime );
			} else {
				item.removeClass( 'expanded' );
				tmp.hide( ixwpsf.retractTime );
			}
		} );

		$( '.product-search-form input.product-filter-field' ).prop( 'disabled', false );
		$( '.product-search-filter-price-form input.product-search-filter-price-field' ).prop( 'disabled', false );

		$( '.product-search-filter-price.hide-fields .min-max-fields' ).hide();
		$( 'select.product-search-filter-product_cat' ).prop( 'disabled', false );
		$( 'select.product-search-filter-attribute' ).prop( 'disabled', false );

		$( '.product-search-product_cat-filter-item a' ).on( 'click', ixwpsf.categoryFilterItemOnClick );
		$( 'select.product-search-filter-product_cat' ).on( 'change', ixwpsf.categoryFilterSelectOnChange );
		$( 'select.product-search-filter-attribute' ).on( 'change', ixwpsf.attributeFilterSelectOnChange );
		$( '.product-search-product_tag-filter-item' ).on( 'click', ixwpsf.tagFilterItemOnClick );
		$( '.product-search-attribute-filter-item a' ).on( 'click', ixwpsf.attributeFilterItemOnClick );
		$( document ).on( 'change input textInput', '.product-search-filter-search input.product-filter-field', function() {
			var value = $( this ).val().trim();
			if ( value.length > 0 ) {
				$( this ).parent().find( '.product-search-filter-search-clear' ).show();
			} else {
				$( this ).parent().find( '.product-search-filter-search-clear' ).hide();
			}
		} );
		$( document ).on( 'click', '.product-search-filter-search .product-search-filter-search-clear', function() {
			$( this ).parent().find( 'input.product-filter-field' ).val( '' ).trigger( 'input' );
		} );
		$( document ).on( 'change input textInput', '.product-search-filter-price input.product-search-filter-min-price, .product-search-filter-price input.product-search-filter-max-price', function() {
			var min = $( this ).parent().find( 'input.product-search-filter-min-price' ).val().trim(),
				max = $( this ).parent().find( 'input.product-search-filter-max-price' ).val().trim();
			if ( min.length > 0 || max.length > 0 ) {
				$( this ).parent().parent().find( '.product-search-filter-price-clear' ).show();
			} else {
				$( this ).parent().parent().find( '.product-search-filter-price-clear' ).hide();
			}
		} );
		$( document ).on( 'click', '.product-search-filter-price .product-search-filter-price-clear', function() {
			var min_input = $( this ).parent().find( 'input.product-search-filter-min-price' ),
				max_input = $( this ).parent().find( 'input.product-search-filter-max-price' ),
				min       = false,
				max       = false;
			if ( min_input.length > 0 ) {
				if ( min_input.val() !== '' ) {
					min_input.val( '' );
					min = true;
				}
			}
			if ( max_input.length > 0 ) {
				if ( max_input.val() !== '' ) {
					max_input.val( '' );
					max = true;
				}
			}
			if ( min ) {
				min_input.trigger( 'input' );
			} else if ( max ) {
				max_input.trigger( 'input' );
			}

			if ( typeof $().slider !== "undefined" ) {
				var slider = $( this ).parent().find( '.product-search-filter-price-slider' );
				if ( slider.length > 0 ) {
					var min = slider.slider( 'option', 'min' ),
						max = slider.slider( 'option', 'max' );
					slider.slider( 'option', 'values', [min, max] );
					var slider_min = slider.parent().find( '.slider-min' ),
						slider_max = slider.parent().find( '.slider-max' );
					slider_min.text( min );
					slider_max.text( max );
				}
			}
		} );

		$( document ).on( 'click', '.product-search-filter-extra:not(.filter-dead)', function( e ) {
			if ( typeof e.preventDefault === 'function' ) {
				e.preventDefault();
			}
			if ( typeof e.stopImmediatePropagation === 'function' ) {
				e.stopImmediatePropagation();
			}
			if ( typeof e.stopPropagation === 'function' ) {
				e.stopPropagation();
			}
			$( this ).parent().find( 'a.product-search-filter-extra' ).addClass( 'loading-extra-filter' );

			var rating = null;

			if ( $( this ).is( 'a' ) ) {
				var cb = $( this ).parent().find( 'input[type="checkbox"]' ).first();
				cb.prop( 'checked', !cb.prop( 'checked' ) );

				if ( $( this ).is( 'a.rating-filter-option' ) ) {
					rating = $( this ).data( 'rating' );
				} else {

					if ( !$( this ).is( 'a.rating-filter-clear' ) ) {
						var rating_anchors = $( '.product-search-filter-rating a.rating-selected' );
						if ( rating_anchors.length > 0 ) {
							rating = rating_anchors.first().data( 'rating' );
						}
					}
				}
			}

			var extras = {ixwpse:1};
			var checkboxes = $( this ).parent().find( 'input[type="checkbox"]' );
			checkboxes.each( function() {
				extras[$( this ).attr( 'name' )] = this.checked ? 1 : 0;
			} ); // each

			if ( rating !== null ) {
				extras['rating'] = rating;
			} else {

				extras['rating'] = '';
			}

			$( '.product-search-form input.product-filter-field' ).first().trigger( 'ixExtraFilter', extras );
		} );

		$( document ).on( 'click', '.product-search-filter-reset .product-search-filter-reset-clear', function( e ) {
			if ( typeof e.preventDefault === 'function' ) {
				e.preventDefault();
			}
			if ( typeof e.stopImmediatePropagation === 'function' ) {
				e.stopImmediatePropagation();
			}
			if ( typeof e.stopPropagation === 'function' ) {
				e.stopPropagation();
			}
			$( this ).addClass( 'loading-reset-filter' );
			$( '.product-search-form input.product-filter-field' ).first().trigger( 'ixFilterReset' );
		} );

		$( '.product-search-filter-reset' ).on( 'ixProductFilterRequestDone', function( e ) {

			var has_filter = false;

			$( '.product-filter-field' ).each( function( index ) {
				if ( $( this ).val().length > 0 ) {
					has_filter = true;

					return false;
				}
			} );

			if ( !has_filter ) {
				has_filter = $( '.product-search-filter-terms .current-cat, .product-search-filter-terms .current-tag, .product-search-filter-terms .current-attribute, .product-search-filter-terms select.selectized option[selected]:not([value=""])' ).length > 0;
			}
			if ( !has_filter ) {
				$( '.product-search-filter-min-price, .product-search-filter-max-price' ).each( function( index ) {
					if ( $( this ).val().length > 0 ) {
						has_filter = true;

						return false;
					}
				} );
			}

			if ( !has_filter ) {
				$( 'input.product-search-filter-on-sale' ).each( function( index ) {
					if ( this.checked ) {
						has_filter = true;
						return false;
					}
				} );
			}

			if ( !has_filter ) {
				has_filter = $( '.product-search-filter-rating .rating-filter-option.rating-selected' ).length > 0;
			}

			if ( has_filter ) {
				$( this ).show();
			} else {
				$( this ).hide();
			}
		} );

		$( '.product-search-filter-reset' ).trigger( 'ixProductFilterRequestDone' );

		$( '.product-search-filter-items option' ).each( function( index, element ) {
			var value = $( element ).val(),
				element_data = $( element ).data();
			ix_dropdown_thumbnails[value] = element_data;
		} );

		$( 'select.apply-selectize' ).each( function( index, element ) {
			var element_order = [];
			$( element ).find( 'option' ).each( function( el_index, el ) {
				$( el ).data( 'data', { $order:el_index } );
				element_order[$( el ).val()] = el_index;
			} );
			ix_dropdown_order[element.id] = element_order;
		} );

		if ( typeof $().selectize !== 'undefined' ) {
			$( 'select.apply-selectize' ).trigger( 'apply-selectize' );
		}

		ixwpsf.toggleWidgets();

	} );
} )( jQuery );
