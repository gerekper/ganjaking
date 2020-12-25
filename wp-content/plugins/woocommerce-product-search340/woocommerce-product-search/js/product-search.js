/*!
 * product-search.js
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
 * @since 1.0.0
 */

var ixwps = {

	doPost : true,
	blinkerTimeouts : [],
	blinkerTimeout : 5000,
	xhr : null
};

( function( $ ) {

ixwps.inhibitEnter = function( fieldId ) {
	$( "#" + fieldId ).keydown( function( e ) {
		if ( e.keyCode == 13 ) { // enter
			e.preventDefault();
			return false;
		}
	} );
};

ixwps.dynamicFocus = function ( fieldId, resultsId ) {
	var $field   = $( '#' + fieldId ),
		$results = $( '#' + resultsId );
	$field.focusout( function( e ) {

		var $elem = $( this );
		setTimeout(
			function() {
				var hasFocus = ( $elem.find( ':focus' ).length > 0 );
				if ( ! hasFocus ) {
					$results.hide();
				}
			},
			100
		);
	} );
	$field.focusin( function( e ) {

		var $elem        = $( this ),
			$searchField = $elem.find( 'input.product-search-field' );
		if ( $searchField.length > 0 ) {
			if ( $( $searchField[0] ).val().length == 0 ) {

				$results.html( '' );
			}
		}

		$results.show();
	} );
};

ixwps.navigate = function( fieldId, resultsId ) {
	$( "#" + fieldId ).keydown( function(e) {
		var i = 0, navigate = false, escape = false;
		switch ( e.keyCode ) {
			case 37 : // left
				break;
			case 39 : // right
				break;
			case 38 : // up
				i = -1;
				break;
			case 40 : // down
				i = 1;
				break;
			case 13 : // enter
				navigate = true;
				break;
			case 27 : // esc
				escape = true;
				break;
		}
		if ( i != 0 ) {
			var entries = $( '#' + resultsId ).find( '.entry' ),
				active  = $( '#' + resultsId + ' .entry.active' ).index();
			if ( entries.length > 0 ) {
				if ( active >= 0 ) {
					$( entries[active] ).removeClass( 'active' );
				}
				active += i;
				if ( active < 0 ) {
					active = entries.length - 1;
				} else if ( active >= entries.length ) {
					active = 0;
				}
				$( entries[active] ).addClass( 'active' );

				var offset = entries[active].offsetTop;
				$( '#' + resultsId ).find( '.product-search-results-content' ).first().prop( 'scrollTop', offset );
			}
			e.preventDefault();
			return false;
		}
		if ( navigate ) {
			var entries = $( '#' + resultsId ).find( '.entry' ),
				active  = $( '#' + resultsId + ' .entry.active' ).index();
			if ( ( active >= 0 ) && ( active < entries.length ) ) {
				var link = $( entries[active] ).find( 'a' ).get( 0 );
				if ( typeof link !== 'undefined' ) {
					var url = $( link ).attr( 'href' );
					if ( typeof url !== 'undefined' ) {
						e.preventDefault();
						ixwps.doPost = false; // disable posting the query
						document.location = url;
						return false;
					}
				}
			}
		}
		if ( escape ) {
			var entries = $( '#' + resultsId ).find( '.entry' ),
			active = $( '#' + resultsId + ' .entry.active' ).index();
			if ( entries.length > 0 ) {
				if ( active >= 0 ) {
					$( entries[active] ).removeClass( "active" );

					$( '#' + resultsId ).find( '.product-search-results-content' ).first().prop( 'scrollTop', 0 );
				}
			}
			e.preventDefault();
			return false;
		}
	});
};

ixwps.autoAdjust = function( fieldId, resultsId ) {
	var $field   = $( '#' + fieldId ),
		$results = $( '#' + resultsId );
	$results.on( 'adjustWidth', function( e ) {
		e.stopPropagation();

		var w = $field.outerWidth() - ( $results.outerWidth() - $results.innerWidth() );
		$results.width( w );
	} );
};

ixwps.productSearch = function( fieldId, containerId, resultsId, url, query, args ) {

	if ( ! ixwps.doPost ) {
		return;
	}

	if ( typeof args === "undefined" ) {
		args = {};
	}

	var $results       = $( '#' + resultsId ),
		$blinker       = $( '#' + fieldId ),
		blinkerTimeout = ixwps.blinkerTimeout;

	if ( typeof args.blinkerTimeout !== 'undefined' ) {
		blinkerTimeout = args.blinkerTimeout;
	}
	query = $.trim( query );
	if ( query != '' ) {
		$blinker.addClass( 'blinker' );
		if ( blinkerTimeout > 0 ) {
			ixwps.blinkerTimeouts[ '#' + fieldId ] = setTimeout(
				function() {
					$blinker.removeClass( 'blinker' );
				},
				blinkerTimeout
			);
		}
		var params = {
			'action'         : 'product_search',
			'product-search' : 1,
			'product-query'  : query
		};
		if ( typeof args.lang !== 'undefined' ) {
			params.lang = args.lang;
		}
		$( '#' + fieldId ).parent().find( '.product-search-field-clear' ).hide();

		if ( ixwps.xhr !== null ) {
			ixwps.xhr.abort();
		}
		ixwps.xhr = $.post(
			url,
			params,
			function ( data ) {
				ixwps.xhr = null;
				var results = '';
				if ( ( data !== null ) && ( data.length > 0 ) ) {
					var current_type       = null,
						product_thumbnails = true,
						show_description   = true,
						show_price         = true,
						show_add_to_cart   = true,
						show_more          = true;
					if ( typeof args.product_thumbnails !== 'undefined' ) {
						product_thumbnails = args.product_thumbnails;
					}
					if ( typeof args.show_description !== 'undefined' ) {
						show_description = args.show_description;
					}
					if ( typeof args.show_price !== 'undefined' ) {
						show_price = args.show_price;
					}
					if ( typeof args.show_add_to_cart !== 'undefined' ) {
						show_add_to_cart = args.show_add_to_cart;
					}
					if ( typeof args.show_more !== 'undefined' ) {
						show_more = args.show_more;
					}

					results += '<table class="search-results">';
					for ( var key in data ) {
						var first = '';
						if ( current_type != data[key].type ) {
							current_type = data[key].type;
							first = 'first';
						}

						results += '<tr class="entry ' + data[key].type + ' ' + first + '">';

						if ( product_thumbnails && current_type != 's_product_cat' && current_type != 's_more' ) {
							results += '<td class="product-image">';
							results += '<a href="' + data[key].url + '" title="' + data[key].title + '">';
							if ( typeof data[key].thumbnail !== "undefined" ) {
								var width = '', height = '', alt = '';
								if ( typeof data[key].thumbnail_alt !== "undefined" ) {
									alt = ' alt="' + data[key].thumbnail_alt + '" ';
								}
								if ( typeof data[key].thumbnail_width !== "undefined" ) {
									width = ' width="' + data[key].thumbnail_width + '" ';
								}
								if ( typeof data[key].thumbnail_height !== "undefined" ) {
									height = ' height="' + data[key].thumbnail_height + '" ';
								}
								results += '<img class="thumbnail" src="' + data[key].thumbnail + '" ' + alt + width + height + '/>';
							}
							results += '</a>';
							results += '</td>';
						}

						switch ( current_type ) {
							case 's_more' :
								if ( show_more ) {
									results += '<td class="more-info" colspan="2">';
									results += '<a href="' + data[key].url + '" title="' + data[key].a_title + '">';
									results += '<span class="title">' + data[key].title + '</span>';
									results += '</a>';
									results += '</td>';
								}
								break;
							case 's_product_cat' :
								results += '<td class="category-info" colspan="2">';
								results += '<a href="' + data[key].url + '" title="' + data[key].title + '">';
								results += '<span class="title">' + data[key].title + '</span>';
								results += '</a>';
								results += '</td>';
								break;
							default :
								results += '<td class="product-info">';
								results += '<a href="' + data[key].url + '" title="' + data[key].title + '">';
								results += '<span class="title">' + data[key].title + '</span>';
								if ( show_description ) {
									if ( typeof data[key].description !== "undefined" ) {
										results += '<span class="description">' + data[key].description + '</span>';
									}
								}
								if ( show_price ) {
									if ( typeof data[key].price !== "undefined" ) {
										results += '<span class="price">' + data[key].price + '</span>';
									}
								}
								results += '</a>';
								if ( show_add_to_cart ) {
									if ( typeof data[key].add_to_cart !== "undefined" ) {
										results += '<div class="wps_add_to_cart">' + data[key].add_to_cart + '</div>';
									}
								}
								results += '</td>';
						}

						results += '</tr>';
					}
					results += '</table>';

				} else {
					if ( typeof args.no_results !== 'undefined' ) {
						if ( args.no_results.length > 0 ) {
							results += '<div class="no-results">';
							results += args.no_results;
							results += '</div>';
						}
					}
				}
				$results.show().html( results );
				ixwps.clickable( resultsId );
				$results.trigger( 'adjustWidth' );
				$blinker.removeClass( 'blinker' );
				if ( blinkerTimeout > 0 ) {
					clearTimeout( ixwps.blinkerTimeouts[ '#' + fieldId ] );
				}
				$( '#' + fieldId ).parent().find( '.product-search-field-clear' ).show();
			},
			'json'
		);
	} else {

		$results.hide().html( '' );

		$( '#' + fieldId ).parent().find( '.product-search-field-clear' ).hide();
	}
};

ixwps.clickable = function( resultsId ) {
	$( '#' + resultsId + ' table.search-results tr' ).click( function(e) {
		if ( !$( e.target ).hasClass( 'add_to_cart_button' ) ) {
			var url = $( this ).find( 'a' ).attr( 'href' );
			if ( url ) {
				window.location = url;
			}
		}
	});
	$( '#' + resultsId + ' table.search-results tr' ).css( 'cursor', 'pointer' );
};

$( document ).ready( function() {

	$( '.product-search-form input.product-search-field' ).prop( 'disabled', false );

	$( document ).on( 'click', '.product-search-form .product-search-field-clear', function() {
		var field = $( this ).parent().find( 'input.product-search-field' );
		if ( field.length > 0 ) {
			field.trigger( 'clear' );
			field.closest( '.product-search' ).find( '.product-search-results-content' ).html( '' );

			if ( field.closest( '.product-search' ).find( '.product-search-results-content' ).length === 0 ) {
				field.closest( '.product-search' ).find( '.product-search-results' ).html( '' );
			}
			$( this ).hide();
		}
	} );
} );

} )( jQuery );
