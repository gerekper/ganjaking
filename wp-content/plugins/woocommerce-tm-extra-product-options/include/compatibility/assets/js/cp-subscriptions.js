( function( window, document, $ ) {
	'use strict';

	var TMEPOJS;

	function tm_set_subscription_period( epoObject ) {
		var cart_id;
		var $cart;
		var subscription_period;
		var variations_subscription_period;
		var base;
		var is_subscription;
		var is_hidden;
		var variation_id_selector;
		var current_variation;
		var $_cart;
		var subscription_period_separator;
		var this_epo_totals_container = epoObject.this_epo_totals_container;
		var main_product = epoObject.main_product;

		this_epo_totals_container.each( function() {
			cart_id = $( this ).attr( 'data-cart-id' );
			$cart = main_product.find( '.tm-extra-product-options.tm-cart-' + cart_id );
			subscription_period = $( this ).data( 'subscription-period' );
			variations_subscription_period = $( this ).data( 'variations-subscription-period' );
			base = $cart.find( '.tmcp-field' ).closest( '.tmcp-field-wrap' );
			is_subscription = $( this ).data( 'is-subscription' );

			if ( is_subscription ) {
				base.find( '.tmperiod' ).remove();

				is_hidden = base.find( '.amount' ).is( '.hidden' );
				if ( is_hidden ) {
					is_hidden = ' hidden';
				} else {
					is_hidden = '';
				}

				variation_id_selector = "input[name^='variation_id']";
				$_cart = $( this ).data( 'tm_for_cart' );

				if ( $_cart ) {
					if ( $_cart.find( 'input.variation_id' ).length > 0 ) {
						variation_id_selector = 'input.variation_id';
					}
					current_variation = $_cart.find( variation_id_selector ).val();
					if ( ! current_variation ) {
						current_variation = 0;
					}
					if ( variations_subscription_period[ $.epoAPI.math.toFloat( current_variation ) ] ) {
						subscription_period = variations_subscription_period[ $.epoAPI.math.toFloat( current_variation ) ];
					}
				}
				subscription_period_separator = ' / ';
				if ( window.subscription_period_separator !== undefined ) {
					subscription_period_separator = window.subscription_period_separator;
				}
				base.find( '.amount' ).after( '<span class="tmperiod' + is_hidden + '">' + subscription_period_separator + subscription_period + '</span>' );

				$( this ).find( '.tmperiod' ).remove();
				$( this )
					.find( '.amount.options' )
					.after( '<span class="tmperiod">' + subscription_period_separator + subscription_period + '</span>' );
				$( this )
					.find( '.amount.final' )
					.after( '<span class="tmperiod">' + subscription_period_separator + subscription_period + '</span>' );
			}
		} );
	}

	/**
	 * Adjust totals object
	 *
	 * @since  1.0
	 * @return String
	 */
	function tc_adjust_tc_totals_ob( tc_totals_ob, data ) {
		var subscription_options_total = 0;
		var formatted_subscription_fee_total = '';
		var subscription_sign_up_fee;
		var subscription_total;
		var show_sign_up_fee = false;
		var showTotal = data.showTotal;
		var epoHolder = data.epoHolder;
		var totalsHolder = data.totalsHolder;
		var tm_set_price_totals = data.tm_set_price_totals;

		epoHolder
			.find( '.tmcp-sub-fee-field' )
			.filter( '.tcenabled' )
			.toArray()
			.forEach( function( field ) {
				var option_price = 0;
				var options;

				field = $( field );

				if ( field.is( ':checkbox, :radio, :input' ) ) {
					if ( field.is( '.tmcp-checkbox, .tmcp-radio' ) ) {
						if ( field.is( ':checked' ) ) {
							option_price = field.data( 'raw_price' );
							showTotal = true;
							field.data( 'isset', 1 );
						} else {
							field.data( 'isset', 0 );
						}
					} else if ( field.is( '.tmcp-select' ) ) {
						option_price = field.find( 'option:selected' ).data( 'raw_price' );
						options = field.children( 'option:selected' );
						if ( ! ( options.val() === '' && options.attr( 'data-rulestype' ) === '' ) ) {
							showTotal = true;
						}
						field.find( 'option' ).data( 'isset', 0 );
						field.find( 'option:selected' ).data( 'isset', 1 );
					} else if ( field.val() ) {
						option_price = field.data( 'raw_price' );
						showTotal = true;
						field.data( 'isset', 1 );
					} else {
						field.data( 'isset', 0 );
					}
					if ( ! option_price ) {
						option_price = 0;
					}

					if ( field.is( '.tmcp-sub-fee-field' ) ) {
						subscription_options_total = parseFloat( subscription_options_total ) + parseFloat( option_price );
					}
				}
			} );

		if ( totalsHolder.data( 'is-subscription' ) ) {
			subscription_sign_up_fee = parseFloat( totalsHolder.data( 'subscription-sign-up-fee' ) );
			if ( ! Number.isFinite( subscription_sign_up_fee ) ) {
				subscription_sign_up_fee = 0;
			}
			subscription_total = subscription_sign_up_fee + parseFloat( subscription_options_total );
			if ( subscription_total ) {
				show_sign_up_fee = true;
				formatted_subscription_fee_total = tm_set_price_totals( subscription_total, totalsHolder, false, true );
			}
		}

		tc_totals_ob.formatted_subscription_fee_total = formatted_subscription_fee_total;
		tc_totals_ob.show_sign_up_fee = show_sign_up_fee;
		tc_totals_ob.sign_up_fee = TMEPOJS.i18n_sign_up_fee;
		tc_totals_ob.showTotal = showTotal;

		return tc_totals_ob;
	}

	$( document ).ready( function() {
		TMEPOJS = window.TMEPOJS || null;

		if ( ! TMEPOJS ) {
			return;
		}

		$( window ).on( 'tm-epo-init-end', function( event, eventData ) {
			if ( event && eventData && eventData.epo ) {
				tm_set_subscription_period( eventData.epo );
			}
		} );

		$( window ).on( 'tc-epo-after-update', function( event, eventData ) {
			if ( event && eventData && eventData.data && eventData.data.epo_object ) {
				tm_set_subscription_period( eventData.data.epo_object );
			}
		} );

		$( window ).on( 'tm-epo-found-variation', function( event, eventData ) {
			var totalsHolder;
			var variation;
			var variations;

			if ( event && eventData && eventData.epo ) {
				totalsHolder = eventData.totalsHolder;
				variation = eventData.variation;
				variations = totalsHolder.data( 'variations' );

				if ( variations && variation && variation.variation_id ) {
					if ( variation.tc_subscription_sign_up_fee !== undefined ) {
						totalsHolder.data( 'subscription-sign-up-fee', variation.tc_subscription_sign_up_fee );
					} else {
						totalsHolder.data( 'subscription-sign-up-fee', 0 );
					}

					if ( variation.tc_subscription_period !== undefined ) {
						totalsHolder.data( 'subscription-period', variation.tc_subscription_period );
					}
				}
			}
		} );

		$.epoAPI.addFilter( 'tc_adjust_tc_totals_ob', tc_adjust_tc_totals_ob, 10, 2 );
	} );
}( window, document, window.jQuery ) );
