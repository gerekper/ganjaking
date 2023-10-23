/* global wc_currency_converter_params  */
jQuery(document).ready(function() {
	var money             = fx.noConflict();
	var current_currency  = wc_currency_converter_params.current_currency;
	var currency_codes    = JSON.parse( wc_currency_converter_params.currencies );
	var currency_position = wc_currency_converter_params.currency_pos;
	var currency_decimals = wc_currency_converter_params.num_decimals;
	var remove_zeros      = wc_currency_converter_params.trim_zeros;
	var locale_info       = wc_currency_converter_params.locale_info;

	money.rates           = wc_currency_converter_params.rates;
	money.base            = wc_currency_converter_params.base;
	money.settings.from   = wc_currency_converter_params.currency;

	money = set_default_rate_on_missing_currency( money, wc_currency_converter_params.currency );
	money = set_default_rate_on_missing_currency( money, wc_currency_converter_params.current_currency );

	if ( 'undefined' !== typeof( set_initial_currency ) ) {
		jQuery.cookie( 'woocommerce_current_currency', set_initial_currency, { expires: 7, path: '/' } );
	}

	if ( money.settings.from == 'RMB' ) {
		money.settings.from = 'CNY';
	}

	function set_default_rate_on_missing_currency( money, currency ) {
	    if ( ! money.rates[ currency ] ) {
	        money.rates[ currency ] = parseFloat( wc_currency_converter_params.currency_rate_default );
	    }
	    return money;
	}

	function switch_currency( to_currency ) {

		if ( wc_currency_converter_params.symbol_positions[ to_currency ] ) {
			currency_position = wc_currency_converter_params.symbol_positions[ to_currency ];
		}
		money = set_default_rate_on_missing_currency( money, to_currency );

		var new_thousand_sep = wc_currency_converter_params.thousand_sep;
		var new_decimal_sep = wc_currency_converter_params.decimal_sep;

		for(const country_code in locale_info) {
			if (current_currency === locale_info[country_code].currency_code) {
				new_thousand_sep = locale_info[country_code].thousand_sep;
				new_decimal_sep = locale_info[country_code].decimal_sep;
				break;
			}
		}

		// Span.amount
		jQuery('span.amount, span.wc-block-formatted-money-amount').each(function(){

			// Original markup
			var original_code = jQuery(this).attr("data-original");

			if (typeof original_code == 'undefined' || original_code == false) {
				jQuery(this).attr("data-original", jQuery(this).html());
			}

			// Original price
			var original_price = jQuery(this).attr("data-price");

			if ( typeof original_price == 'undefined' || original_price == false ) {

				// Get original price
				var original_price = jQuery(this).html();

				// Small hack to prevent errors with $ symbols
				jQuery( '<del></del>' + original_price ).find('del').remove();

				// Remove formatting
				original_price = original_price.replace( wc_currency_converter_params.currency_format_symbol, '' );
				original_price = original_price.split( wc_currency_converter_params.thousand_sep ).join( '' );
				original_price = original_price.replace( wc_currency_converter_params.decimal_sep, '.' );
				original_price = original_price.replace(/[^0-9\.]/g, '');
				original_price = parseFloat( original_price );

				// Store original price
				jQuery(this).attr("data-price", original_price);
			}

			price = money( original_price ).from( money.settings.from ).to( to_currency );
			price = price.toFixed( currency_decimals );
			price = accounting.formatNumber( price, currency_decimals, new_thousand_sep, new_decimal_sep );

			if ( remove_zeros ) {
				price = price.replace( wc_currency_converter_params.zero_replace, '' );
			}

			if ( currency_codes[ to_currency ] ) {

				if ( currency_position == 'left' ) {

					jQuery(this).html( currency_codes[ to_currency ] + price );

				} else if ( currency_position == 'right' ) {

					jQuery(this).html( price + "" + currency_codes[ to_currency ] );

				} else if ( currency_position == 'left_space' ) {

					jQuery(this).html( currency_codes[ to_currency ] + " " + price );

				} else if ( currency_position == 'right_space' ) {

					jQuery(this).html( price + " " + currency_codes[ to_currency ] );

				}

			} else {
				jQuery(this).html( price + " " + to_currency );

			}

			jQuery(this).attr( 'title', wc_currency_converter_params.i18n_oprice + original_price );
		});

		// #shipping_method prices
		jQuery('#shipping_method option').each(function(){

			// Original markup
			var original_code = jQuery(this).attr("data-original");

			if (typeof original_code == 'undefined' || original_code == false) {

				original_code = jQuery(this).text();

				jQuery(this).attr("data-original", original_code);

			}

			var current_option = original_code;

			current_option = current_option.split(":");

			if (!current_option[1] || current_option[1] == '') return;

			price = current_option[1];

			if (!price) return;

			// Remove formatting
			price = price.replace( wc_currency_converter_params.currency_format_symbol, '' );
			price = price.split( wc_currency_converter_params.thousand_sep ).join( '' );
			price = price.replace( wc_currency_converter_params.decimal_sep, '.' );
			price = price.replace(/[^0-9\.]/g, '');
			price = parseFloat( price );

			price = money(price).to(to_currency);
			price = price.toFixed( currency_decimals );
			price = accounting.formatNumber( price, currency_decimals, new_thousand_sep, new_decimal_sep );

			if ( remove_zeros ) {
				price = price.replace( wc_currency_converter_params.zero_replace, '' );
			}

			jQuery(this).html( current_option[0] + ": " + price  + " " + to_currency );

		});

		price_filter_update( to_currency );

		jQuery('body').trigger( 'currency_converter_switch', [to_currency] );
		jQuery('ul.currency_switcher li a').removeClass('active');
		jQuery('ul.currency_switcher li a[data-currencycode="' + current_currency + '"]').addClass('active');
		jQuery('select.currency_switcher').val( current_currency );
	}

	function price_filter_update( to_currency ) {
		if ( to_currency ) {
			jQuery('.ui-slider').each(function() {
				theslider = jQuery( this );
				values    = theslider.slider("values");

				original_price = "" + values[1];
				original_price = original_price.replace( wc_currency_converter_params.currency_format_symbol, '' );
				original_price = original_price.split( wc_currency_converter_params.thousand_sep ).join( '' );
				original_price = original_price.replace( wc_currency_converter_params.decimal_sep, '.' );
				original_price = original_price.replace(/[^0-9\.]/g, '');
				original_price = parseFloat( original_price );

				price_max = money(original_price).to(to_currency);

				original_price = "" + values[0];
				original_price = original_price.replace( wc_currency_converter_params.currency_format_symbol, '' );
				original_price = original_price.split( wc_currency_converter_params.thousand_sep ).join( '' );
				original_price = original_price.replace( wc_currency_converter_params.decimal_sep, '.' );
				original_price = original_price.replace(/[^0-9\.]/g, '');
				original_price = parseFloat( original_price );

				price_min = money(original_price).to(to_currency);

				jQuery('.price_slider_amount').find('span.from').html( price_min.toFixed(2) + " " + to_currency );
				jQuery('.price_slider_amount').find('span.to').html( price_max.toFixed(2) + " " + to_currency );
			});
		}
	}

	jQuery(document).ready(function($) {
		jQuery('body').on( "price_slider_create price_slider_slide price_slider_change", function() {
			price_filter_update( current_currency );
		} );
		price_filter_update( current_currency );
	});

	// Ajax events
	jQuery('body').on('wc_fragments_refreshed wc_fragments_loaded show_variation updated_checkout updated_shipping_method added_to_cart cart_page_refreshed cart_widget_refreshed updated_addons post-load', function() {
		if ( current_currency ) {
			switch_currency( current_currency );
		}
	});

	jQuery( document.body ).on( 'wc_booking_form_changed wc_currency_converter_calculate', function() {
		if ( current_currency ) {
			switch_currency( current_currency );
		}
	});

	// On load
	if ( current_currency ) {
		switch_currency( current_currency );
	} else {
		jQuery('ul.currency_switcher li a[data-currencycode="' + wc_currency_converter_params.currency + '"]').addClass('active');
		jQuery('select.currency_switcher').val( wc_currency_converter_params.currency );
	}

	// If products are displayed inside of 'all products block', we need to listen for reloads and change after loaded.
	// The following block of code is using a MutationObserver to identify when the products have finished loading.
	// This is a quick fix, we will want to rewrite it using React.
	if ( jQuery( '.wp-block-woocommerce-all-products' ).length ) {
		var isLoading = true;
		var observer = new MutationObserver(function(mutations) {
			if ( jQuery( '.is-loading' ).length ) {
				isLoading = true;
			}
			// Once products are not loading, switch currency.
			if ( current_currency && !jQuery( '.is-loading' ).length ) {
				if ( isLoading ) {
					switch_currency( current_currency );
					isLoading = false;
				}
			}
		});

		observer.observe(
			document.body,
			{
				attributes: true,
				childList: true,
				subtree: true,
				characterData: true
			}
		);
	}


	jQuery( document.body )
		.on( 'click', 'a.wc-currency-converter-reset', function() {
			jQuery('span.amount, #shipping_method option, span.wc-block-formatted-money-amount').each(function(){
				var original_code = jQuery(this).attr("data-original");

				if (typeof original_code !== 'undefined' && original_code !== false) {
					jQuery(this).html( original_code );
				}
			});

			jQuery('ul.currency_switcher li a').removeClass('active');
			jQuery('ul.currency_switcher li a[data-currencycode="' + wc_currency_converter_params.currency + '"]').addClass('active');
			jQuery('select.currency_switcher').val( wc_currency_converter_params.currency );

			jQuery.cookie( 'woocommerce_current_currency', '', { expires: 7, path: '/' } );

			current_currency = '';

			jQuery('body').trigger('currency_converter_reset');

			if ( jQuery( '.price_slider' ).length ) {
				jQuery('body').trigger('price_slider_slide', [jQuery(".price_slider").slider("values", 0), jQuery(".price_slider").slider("values", 1)]);
			}

			return false;
		})
		.on( 'click', 'ul.currency_switcher li a:not(".reset")', function() {
			current_currency = jQuery(this).attr('data-currencycode');
			switch_currency( current_currency );
			jQuery.cookie('woocommerce_current_currency', current_currency, { expires: 7, path: '/' });
			return false;
		})
		.on( 'change', 'select.currency_switcher', function() {
			current_currency = jQuery(this).val();
			switch_currency( current_currency );
			jQuery.cookie('woocommerce_current_currency', current_currency, { expires: 7, path: '/' });
			return false;
		});
});
