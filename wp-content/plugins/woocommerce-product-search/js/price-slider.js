/*!
 * price-slider.js
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
 * @since 2.4.0
 */
var wps_price_slider = {};
( function( $ ) {

	wps_price_slider.easeIn = function( value, min, max ) {
		var power = Math.max( 1, Math.log10( Math.ceil( max / 10 ) * 10 ) ),
			x = Math.max( 0, value - min ) / Math.max( 1, max - min );
		value = ( max - min ) * Math.pow( x, power ) + min;
		value = Math.max( min, value );
		value = Math.min( max, value );
		return value;
	};

	wps_price_slider.easeOut = function( v, min, max ) {
		var power = Math.max( 1, Math.log10( Math.ceil( max / 10 ) * 10 ) );
			v = Math.pow( Math.max( 0, v - min ) / ( max - min ), 1 / power ) * ( max - min ) + min;
		return v;
	};
	wps_price_slider.create = function( slider, min_price, max_price, current_min_price, current_max_price, precision ) {
		if ( current_min_price == '' ) {
			current_min_price = min_price;
		}
		if ( current_max_price == '' ) {
			current_max_price = max_price;
		}
		if ( typeof $().slider !== "undefined" ) {
			$( slider ).slider( {
				range : true,
				animate : true,
				min : min_price,
				max : max_price,
				values : [ current_min_price, current_max_price ],
				create : function() {
				},
				slide : function( event, ui ) {
					var min_input = $( this ).parent().find( 'input.product-search-filter-min-price' ),
						max_input = $( this ).parent().find( 'input.product-search-filter-max-price' ),
						slider_min = $( this ).parent().find( '.slider-min' ),
						slider_max = $( this ).parent().find( '.slider-max' ),
						new_min = ui.values[0],
						new_max = ui.values[1],
						min_price = $( this ).data( 'min_price' ),
						max_price = $( this ).data( 'max_price' );
					new_min = parseFloat( wps_price_slider.easeIn( new_min, min_price, max_price ) ).toFixed( precision );
					new_max = parseFloat( wps_price_slider.easeIn( new_max, min_price, max_price ) ).toFixed( precision );
					min_input.val( new_min );
					max_input.val( new_max );
					slider_min.text( new_min );
					slider_max.text( new_max );
				},
				change : function( event, ui ) {
					var min_input = $( this ).parent().find( 'input.product-search-filter-min-price' ),
						max_input = $( this ).parent().find( 'input.product-search-filter-max-price' ),
						new_min   = parseFloat( ui.values[0] ).toFixed( precision ),
						new_max   = parseFloat( ui.values[1] ).toFixed( precision );
					if ( new_min != min_input.data( 'old_min' ) ) {
						min_input.data( 'old_min', new_min );
						if ( !$( this ).slider( 'option', 'disabled' ) ) {
							min_input.trigger( 'input' );
						}
					} else if ( new_max != max_input.data( 'old_max' ) ) {
						max_input.data( 'old_max', new_max );
						if ( !$( this ).slider( 'option', 'disabled' ) ) {
							max_input.trigger( 'input' );
						}
					}
				}
			} );
		}
	};

	wps_price_slider.updateForm = function( query, container, args, href ) {
		var form = $( container ).closest( 'form' );
		if ( form.length > 0 ) {
			if ( query.length > 0 ) {
				var ixwpss = $( form ).find( 'input[name="ixwpss"]' );
				if ( ixwpss.length > 0 ) {
					ixwpss.val( query );
				} else {
					$( form ).append( '<input type="hidden" name="ixwpss" value="' + query + '"/>' );
				}
			}
			if ( typeof args.title !== 'undefined' ) {
				var title = $( form ).find( 'input[name="title"]' );
				if ( title.length > 0 ) {
					title.val( args.title );
				} else {
					$( form ).append( '<input type="hidden" name="title" value="' + args.title + '"/>' );
				}
			} else {
				$( form ).find( 'input[name="title"]' ).remove();
			}
			if ( typeof args.excerpt !== 'undefined' ) {
				var excerpt = $( form ).find( 'input[name="excerpt"]' );
				if ( excerpt.length > 0 ) {
					excerpt.val( args.excerpt );
				} else {
					$( form ).append( '<input type="hidden" name="excerpt" value="' + args.excerpt + '"/>' );
				}
			} else {
				$( form ).find( 'input[name="excerpt"]' ).remove();
			}
			if ( typeof args.content !== 'undefined' ) {
				var content = $( form ).find( 'input[name="content"]' );
				if ( content.length > 0 ) {
					content.val( args.content );
				} else {
					$( form ).append( '<input type="hidden" name="content" value="' + args.content + '"/>' );
				}
			} else {
				$( form ).find( 'input[name="content"]' ).remove();
			}
			if ( typeof args.categories !== 'undefined' ) {
				var categories = $( form ).find( 'input[name="categories"]' );
				if ( categories.length > 0 ) {
					categories.val( args.categories );
				} else {
					$( form ).append( '<input type="hidden" name="categories" value="' + args.categories + '"/>' );
				}
			} else {
				$( form ).find( 'input[name="categories"]' ).remove();
			}
			if ( typeof args.attributes !== 'undefined' ) {
				var attributes = $( form ).find( 'input[name="attributes"]' );
				if ( attributes.length > 0 ) {
					attributes.val( args.attributes );
				} else {
					$( form ).append( '<input type="hidden" name="attributes" value="' + args.attributes + '"/>' );
				}
			} else {
				$( form ).find( 'input[name="attributes"]' ).remove();
			}
			if ( typeof args.tags !== 'undefined' ) {
				var tags = $( form ).find( 'input[name="tags"]' );
				if ( tags.length > 0 ) {
					tags.val( args.tags );
				} else {
					$( form ).append( '<input type="hidden" name="tags" value="' + args.tags + '"/>' );
				}
			} else {
				$( form ).find( 'input[name="tags"]' ).remove();
			}
			if ( typeof args.sku !== 'undefined' ) {
				var sku = $( form ).find( 'input[name="sku"]' );
				if ( sku.length > 0 ) {
					sku.val( args.sku );
				} else {
					$( form ).append( '<input type="hidden" name="sku" value="' + args.sku + '"/>' );
				}
			} else {
				$( form ).find( 'input[name="sku"]' ).remove();
			}
			if ( typeof args.wpml !== 'undefined' ) {
				var wpml = $( form ).find( 'input[name="wpml"]' );
				if ( wpml.length > 0 ) {
					wpml.val( args.wpml );
				} else {
					$( form ).append( '<input type="hidden" name="wpml" value="' + args.wpml + '"/>' );
				}
			} else {
				$( form ).find( 'input[name="wpml"]' ).remove();
			}

			$( form ).find( 'input[name="orderby"]' ).remove();
			$( form ).find( 'input[name^="ixwpst"]' ).remove();
			$( form ).find( 'input[name^="ixwpsf"]' ).remove();
			$( form ).find( 'input[name="ixwpse"]' ).remove();
			$( form ).find( 'input[name="on_sale"]' ).remove();
			$( form ).find( 'input[name="rating"]' ).remove();

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
						key !== '' && /*key !== 'orderby' && */ key !== 'ixwpsp' && key !== 'min_price' && key !== 'max_price' && key !== 'ixwpss' &&
						key !== 'title' && key !== 'excerpt' && key !== 'content' &&
						key !== 'categories' && key !== 'attributes' && key !== 'tags' && key !== 'sku' && key !== 'wpml' &&
						(
							key.indexOf( 'ixwpst' ) === 0 ||
							key.indexOf( 'ixwpsf' ) === 0 ||
							key === 'ixwpse' || // @since 2.19.0
							key === 'on_sale' || // @since 2.19.0
							key === 'rating' // @since 2.20.0
						) // (*)
					) {
						var field = $( form ).find( 'input[name="' + key + '"]' );

						if ( field.length > 0 && !key.endsWith( '[]' ) ) {
							field.val( value );
						} else {
							$( form ).append( $( '<input type="hidden" name="' + key + '" />' ).attr( { value:value } ) );
						}
					}
				}
			}
		}
	};
} )( jQuery );
