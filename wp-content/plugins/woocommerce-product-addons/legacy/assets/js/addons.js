jQuery( document ).ready( function($) {

	$.fn.init_addon_totals = function() {

		function isGroupedMixedProductType() {
			var group  = $( '.product-type-grouped' ),
				subs   = 0,
				simple = 0;

			if ( group.length ) {
				group.find( '.group_table tr.product' ).each( function() {
					if ( 0 < $( this ).find( '.input-text.qty' ).val() ) {
						// For now only checking between simple and subs.
						if ( $( this ).find( '.entry-summary .subscription-details' ).length ) {
							subs++;
						} else {
							simple++;
						}
					}
				});

				if ( 0 < subs && 0 < simple ) {
					return true;
				}
			}

			return false;
		}

		function isGroupedSubsSelected() {
			var group = $( '.product-type-grouped' ),
				subs  = false;

			if ( group.length ) {
				group.find( '.group_table tr.product' ).each( function() {
					if ( 0 < $( this ).find( '.input-text.qty' ).val() ) {
						if ( $( this ).find( '.entry-summary .subscription-details' ).length ) {
							subs = true;
							return false;
						}
					}
				});
			}

			return subs;
		}

		var $cart            = $( this ),
			$variation_input = $cart.hasClass( 'variations_form' ) ? $cart.find( 'input[name="variation_id"], input.variation_id' ) : false;

		// Clear all values on variable product when clear selection is clicked
		$( this ).on( 'click', '.reset_variations', function() {

			$.each( $cart.find( '.product-addon' ), function() {
				var element = $( this ).find( '.addon' );

				if ( element.is( ':checkbox' ) || element.is( ':radio' ) ) {
					element.prop( 'checked', false );
				}

				if ( element.is( 'select' ) ) {
					element.prop( 'selectedIndex', 0 );
				}

				if ( element.is( ':text' ) || element.is( 'textarea' ) || element.is( 'input[type="number"]' ) || element.is( 'input[type="file"]' ) ) {
					element.val( '' );
				}
			});

			$cart.trigger( 'woocommerce-product-addons-update' );
		});

		// clicking on a number input scrollers updates the total continuously.
		$( this ).on( 'mouseup', 'input[type=number]', function (e) {
			$( this ).trigger( 'woocommerce-product-addons-update' );
		});

		$( this ).on( 'keyup change', '.product-addon input, .product-addon textarea', function() {

			if ( $( this ).attr( 'maxlength' ) > 0 ) {

				var value     = $( this ).val();
				var remaining = $( this ).attr( 'maxlength' ) - value.length;

				$( this ).next( '.chars_remaining' ).find( 'span' ).text( remaining );
			}

		});

		$( this ).find( ' .addon-custom, .addon-custom-textarea' ).each( function() {

			if ( $( this ).attr( 'maxlength' ) > 0 ) {

				$( this ).after( '<small class="chars_remaining"><span>' + $( this ).attr( 'maxlength' ) + '</span> ' + woocommerce_addons_params.i18n_remaining + '</small>' );

			}

		});

		$( this ).on( 'change', '.product-addon input, .product-addon textarea, .product-addon select, input.qty', function() {

			$( this ).trigger( 'woocommerce-product-addons-update' );
		});

		$( this ).on( 'found_variation', function( event, variation ) {

			var $variation_form = $( this ),
				$totals         = $variation_form.find( '#product-addons-total' );

			if ( typeof( variation.display_price ) !== 'undefined' ) {

				$totals.data( 'price', variation.display_price );

			} else if ( $( variation.price_html ).find( '.amount:last' ).size() ) {

				product_price = $( variation.price_html ).find( '.amount:last' ).text();
				product_price = product_price.replace( woocommerce_addons_params.currency_format_symbol, '' );
				product_price = product_price.replace( woocommerce_addons_params.currency_format_thousand_sep, '' );
				product_price = product_price.replace( woocommerce_addons_params.currency_format_decimal_sep, '.' );
				product_price = product_price.replace(/[^0-9\.]/g, '' );
				product_price = parseFloat( product_price );

				$totals.data( 'price', product_price );
			}

			$variation_form.trigger( 'woocommerce-product-addons-update' );
		});

		// Compatibility with Smart Coupons self declared gift amount purchase.
		var custom_gift_card_amount = $( '#credit_called' );

		$( custom_gift_card_amount ).on( 'keyup', function() {
			$cart.trigger( 'woocommerce-product-addons-update' );
		});

		$( this ).on( 'woocommerce-product-addons-update', function() {
			var total         = 0,
				total_raw     = 0,
				$totals       = $cart.find( '#product-addons-total' ),
				is_variable   = $variation_input && $variation_input.length > 0,
				product_id    = is_variable ? $variation_input.val() : $totals.data( 'product-id' ),
				product_price = $totals.data( 'price' ),
				product_type  = $totals.data( 'type' ),
				qty           = $cart.find( '.quantity .qty' ).val();

			// Compatibility with Smart Coupons self declared gift amount purchase.
			if ( '' === product_price && custom_gift_card_amount.length && 0 < custom_gift_card_amount.val() ) {
				product_price = custom_gift_card_amount.val();
			}

			$cart.find( '.addon' ).each( function() {
				var addon_cost     = 0,
					addon_cost_raw = 0;

				if ( $( this ).is( '.addon-custom-price' ) ) {
					addon_cost = $( this ).val();
				} else if ( $( this ).is( '.addon-input_multiplier' ) ) {
					if( isNaN( $( this ).val() ) || $( this ).val() == "" ) { // Number inputs return blank when invalid
						$( this ).val( '' );
						$( this ).closest( 'p' ).find( '.addon-alert' ).show();
					} else {
						if( $( this ).val() != "" ){
							$( this ).val( Math.ceil( $( this ).val() ) );
						}
						$( this ).closest( 'p' ).find( '.addon-alert' ).hide();
					}
					addon_cost     = $( this ).data( 'price' ) * $( this ).val();
					addon_cost_raw = $( this ).data( 'raw-price' ) * $( this ).val();
				} else if ( $( this ).is( '.addon-checkbox, .addon-radio' ) ) {
					if ( $( this ).is( ':checked' ) ) {
						addon_cost     = $( this ).data( 'price' );
						addon_cost_raw = $( this ).data( 'raw-price' );
					}
				} else if ( $( this ).is( '.addon-select' ) ) {
					if ( $( this ).val() ) {
						addon_cost     = $( this ).find( 'option:selected' ).data( 'price' );
						addon_cost_raw = $( this ).find( 'option:selected' ).data( 'raw-price' );
					}
				} else {
					if ( $( this ).val() ) {
						addon_cost     = $( this ).data( 'price' );
						addon_cost_raw = $( this ).data( 'raw-price' );
					}
				}

				if ( ! addon_cost ) {
					addon_cost = 0;
				}
				if ( ! addon_cost_raw ) {
					addon_cost_raw = 0;
				}

				total = parseFloat( total ) + parseFloat( addon_cost );
				total_raw = parseFloat( total_raw ) + parseFloat( addon_cost_raw );
			} );

			$totals.data( 'addons-price', total );
			$totals.data( 'addons-raw-price', total_raw );

			if ( $cart.find( 'input.qty' ).size() ) {
				var qty = 0;

				$cart.find( 'input.qty' ).each( function() {
					qty += parseFloat( $( this ).val() );
				});
			} else {
				var qty = 1;
			}

			if ( total && qty ) {

				var product_total_price,
					subscription_details = false;

				total     = parseFloat( total * qty );
				total_raw = parseFloat( total_raw * qty );

				var formatted_addon_total = accounting.formatMoney( total, {
					symbol 		: woocommerce_addons_params.currency_format_symbol,
					decimal 	: woocommerce_addons_params.currency_format_decimal_sep,
					thousand	: woocommerce_addons_params.currency_format_thousand_sep,
					precision 	: ! woocommerce_addons_params.trim_trailing_zeros ? woocommerce_addons_params.currency_format_num_decimals : 0,
					format		: woocommerce_addons_params.currency_format
				});

				if ( 'undefined' !== typeof product_price && product_id ) {

					product_total_price = parseFloat( product_price * qty );

					var formatted_sub_total = accounting.formatMoney( product_total_price + total, {
						symbol 		: woocommerce_addons_params.currency_format_symbol,
						decimal 	: woocommerce_addons_params.currency_format_decimal_sep,
						thousand	: woocommerce_addons_params.currency_format_thousand_sep,
						precision 	: ! woocommerce_addons_params.trim_trailing_zeros ? woocommerce_addons_params.currency_format_num_decimals : 0,
						format		: woocommerce_addons_params.currency_format
					});
				}

				if ( $( this ).parent().find( '.subscription-details' ).length ) {
					// Add-Ons added at bundle level only affect the up-front price.
					if ( ! $cart.hasClass( 'bundle_data' ) ) {
						subscription_details = $( this ).parent().find( '.subscription-details' ).clone().wrap( '<p>' ).parent().html();
					}
				}

				if ( 'grouped' === product_type ) {
					if ( subscription_details && ! isGroupedMixedProductType() && isGroupedSubsSelected() ) {
						formatted_addon_total += subscription_details;
						if ( formatted_sub_total ) {
							formatted_sub_total += subscription_details;
						}
					}
				} else if ( subscription_details ) {
					formatted_addon_total += subscription_details;
					if ( formatted_sub_total ) {
						formatted_sub_total += subscription_details;
					}
				}

				var html = '<dl class="product-addon-totals"><dt>' + woocommerce_addons_params.i18n_addon_total + '</dt><dd><strong><span class="amount">' + formatted_addon_total + '</span></strong></dd>';

				if ( formatted_sub_total && '1' == $totals.data( 'show-sub-total' ) ) {

					// To show our "price display suffix" we have to do some magic since the string can contain variables (excl/incl tax values)
					// so we have to take our sub total and find out what the tax value is, which we can do via an ajax call
					// if its a simple string, or no string at all, we can output the string without an extra call
					var price_display_suffix = '',
						sub_total_string     = typeof( $totals.data( 'i18n_sub_total' ) ) === 'undefined' ? woocommerce_addons_params.i18n_sub_total : $totals.data( 'i18n_sub_total' );

					// no sufix is present, so we can just output the total
					if ( ! woocommerce_addons_params.price_display_suffix ) {
						html = html + '<dt>' + sub_total_string + '</dt><dd><strong><span class="amount">' + formatted_sub_total + '</span></strong></dd></dl>';
						$totals.html( html );
						$cart.trigger( 'updated_addons' );
						return;
					}

					// a suffix is present, but no special labels are used - meaning we don't need to figure out any other special values - just display the playintext value
					if ( false === ( woocommerce_addons_params.price_display_suffix.indexOf( '{price_including_tax}' ) > -1 ) && false === ( woocommerce_addons_params.price_display_suffix.indexOf( '{price_excluding_tax}' ) > -1 ) ) {
						html = html + '<dt>' + sub_total_string + '</dt><dd><strong><span class="amount">' + formatted_sub_total + '</span> ' + woocommerce_addons_params.price_display_suffix + '</strong></dd></dl>';
						$totals.html( html );
						$cart.trigger( 'updated_addons' );
						return;
					}

					// Based on the totals/info and settings we have, we need to use the get_price_*_tax functions
					// to get accurate totals. We can get these values with a special Ajax function
					$.ajax( {
						type: 'POST',
						url:  woocommerce_addons_params.ajax_url,
						data: {
							action: 'wc_product_addons_calculate_tax',
							product_id: product_id,
							add_on_total: total,
							add_on_total_raw: total_raw,
							qty: qty
						},
						success: 	function( result ) {
							if ( result.result == 'SUCCESS' ) {
								price_display_suffix = '<small class="woocommerce-price-suffix">' + woocommerce_addons_params.price_display_suffix + '</small>';
								var formatted_price_including_tax = accounting.formatMoney( result.price_including_tax, {
									symbol 		: woocommerce_addons_params.currency_format_symbol,
									decimal 	: woocommerce_addons_params.currency_format_decimal_sep,
									thousand	: woocommerce_addons_params.currency_format_thousand_sep,
									precision 	: woocommerce_addons_params.currency_format_num_decimals,
									format		: woocommerce_addons_params.currency_format
								} );
								var formatted_price_excluding_tax = accounting.formatMoney( result.price_excluding_tax, {
									symbol 		: woocommerce_addons_params.currency_format_symbol,
									decimal 	: woocommerce_addons_params.currency_format_decimal_sep,
									thousand	: woocommerce_addons_params.currency_format_thousand_sep,
									precision 	: woocommerce_addons_params.currency_format_num_decimals,
									format		: woocommerce_addons_params.currency_format
								} );
								price_display_suffix = price_display_suffix.replace( '{price_including_tax}', formatted_price_including_tax );
								price_display_suffix = price_display_suffix.replace( '{price_excluding_tax}', formatted_price_excluding_tax );
								html                 = html + '<dt>' + sub_total_string + '</dt><dd><strong><span class="amount">' + formatted_sub_total + '</span> ' + price_display_suffix + ' </strong></dd></dl>';
								$totals.html( html );
								$cart.trigger( 'updated_addons' );
							} else {
								html = html + '<dt>' + sub_total_string + '</dt><dd><strong><span class="amount">' + formatted_sub_total + '</span></strong></dd></dl>';
								$totals.html( html );
								$cart.trigger( 'updated_addons' );
							}
						},
						error: function() {
							html = html + '<dt>' + sub_total_string + '</dt><dd><strong><span class="amount">' + formatted_sub_total + '</span></strong></dd></dl>';
							$totals.html( html );
							$cart.trigger( 'updated_addons' );
						}
					});
				} else {
					$totals.empty();
					$cart.trigger( 'updated_addons' );
				}
			} else {
				$totals.empty();
				$cart.trigger( 'updated_addons' );
			}

		});

		$( this ).find( '.addon-custom, .addon-custom-textarea, .product-addon input, .product-addon textarea, .product-addon select, input.qty' ).change();

		// When default variation exists, 'found_variation' must be triggered
		$( this ).find( '.variations select' ).change();
	}

	// Quick view
	$( 'body' ).on( 'quick-view-displayed', function() {
		$( this ).find( '.cart:not(.cart_group)' ).each( function() {
			$( this ).init_addon_totals();
		});
	});

	// Composites
	$( 'body .component' ).on( 'wc-composite-component-loaded', function() {
		$( this ).find( '.cart' ).each( function() {
			$( this ).init_addon_totals();
		});
	});

	// Initialize
	$( 'body' ).find( '.cart:not(.cart_group)' ).each( function() {
		$( this ).init_addon_totals();
	});

});
