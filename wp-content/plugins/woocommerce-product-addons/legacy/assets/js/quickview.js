jQuery( document ).ready( function($) {

	/**
	 * Matches inline variation objects to chosen attributes
	 * @type {Object}
	 */
	var wc_variation_form_matcher = {
		find_matching_variations: function( product_variations, settings ) {
			var matching = [];
			for ( var i = 0; i < product_variations.length; i++ ) {
				var variation = product_variations[i];

				if ( wc_variation_form_matcher.variations_match( variation.attributes, settings ) ) {
					matching.push( variation );
				}
			}
			return matching;
		},
		variations_match: function( attrs1, attrs2 ) {
			var match = true;
			for ( var attr_name in attrs1 ) {
				var val1 = attrs1[ attr_name ];
				var val2 = attrs2[ attr_name ];
				if ( val1 !== undefined && val2 !== undefined && val1.length !== 0 && val2.length !== 0 && val1 !== val2 ) {
					match = false;
				}
			}
			return match;
		}
	};

	$( 'body' ).on( 'change', '.variations select', function() {
		var $form               = $( '.variations_form' ),
			$product_variations = $form.data( 'product_variations' ),
			data                = {},
			$totals             = $( '#product-addons-total' );

		$form.find( '.variations select' ).each( function() {
			var attribute_name = $( this ).data( 'attribute_name' ) || $( this ).attr( 'name' );
			data[ attribute_name ] = $( this ).val();
		});

		if ( 'undefined' === typeof( $product_variations ) ) {
			return;
		}

		var matching_variations = wc_variation_form_matcher.find_matching_variations( $product_variations, data );
		var variation = matching_variations.shift();

		if ( typeof variation.display_price !== 'undefined' ) {
			$totals.data( 'price', variation.display_price );
		} else if ( $( variation.price_html ).find('.amount').last().length ) {
			product_price = $( variation.price_html ).find('.amount').last().text();
			product_price = product_price.replace( woocommerce_addons_params.currency_format_symbol, '' );
			product_price = product_price.replace( woocommerce_addons_params.currency_format_thousand_sep, '' );
			product_price = product_price.replace( woocommerce_addons_params.currency_format_decimal_sep, '.' );
			product_price = product_price.replace(/[^0-9\.]/g, '');
			product_price = parseFloat( product_price );

			$totals.data( 'price', product_price );
		}

		$(this).trigger( 'woocommerce-product-addons-update' );
	} );
} );
