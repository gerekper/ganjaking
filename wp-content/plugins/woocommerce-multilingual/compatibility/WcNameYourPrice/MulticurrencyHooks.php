<?php

namespace WCML\Compatibility\WcNameYourPrice;

use WC_Name_Your_Price_Compatibility;

use function WPML\Container\make;
use function WCML\functions\getClientCurrency;

/**
 * WooCommerce Name Your Price compatibility.
 *
 * @version 5.2.1
 */
class MulticurrencyHooks implements \IWPML_Action {

	public function add_hooks() {
		// Price filters.
		if ( ! is_admin() ) {
			if ( is_callable( [ 'WC_Name_Your_Price_Compatibility', 'is_nyp_gte' ] ) && WC_Name_Your_Price_Compatibility::is_nyp_gte( '3.0' ) ) {
				add_filter( 'wc_nyp_raw_suggested_price', [ $this, 'product_price_filter' ] );
				add_filter( 'wc_nyp_raw_minimum_price', [ $this, 'product_price_filter' ] );
				add_filter( 'wc_nyp_raw_maximum_price', [ $this, 'product_price_filter' ] );
			} else {
				add_filter( 'woocommerce_raw_suggested_price', [ $this, 'product_price_filter' ] );
				add_filter( 'woocommerce_raw_minimum_price', [ $this, 'product_price_filter' ] );
				add_filter( 'woocommerce_raw_maximum_price', [ $this, 'product_price_filter' ] );
			}
		}

		// Cart currency switching.
		add_filter( 'woocommerce_add_cart_item_data', [ $this, 'add_initial_currency' ] );
		add_filter( 'woocommerce_get_cart_item_from_session', [ $this, 'filter_woocommerce_get_cart_item_from_session' ], 20, 2 );

		// Convert cart editing price.
		add_filter( 'wc_nyp_edit_in_cart_args', [ $this, 'edit_in_cart_args' ], 10 );
		add_filter( 'wc_nyp_get_initial_price', [ $this, 'get_initial_price' ], 10, 3 );
	}

	/**
	 * @param float|int    $price
	 * @param string|false $currency
	 *
	 * @return float|int
	 */
	public function product_price_filter( $price, $currency = false ) {
		return apply_filters( 'wcml_raw_price_amount', $price, $currency );
	}

	/**
	 * Store the initial currency when item is added.
	 *
	 * @param array $cart_item_data The Cart Item data.
	 *
	 * @return array
	 * @since 4.12.2
	 */
	public function add_initial_currency( $cart_item_data ) {

		if ( isset( $cart_item_data['nyp'] ) ) {
			$cart_item_data['nyp_currency'] = get_woocommerce_currency();
			$cart_item_data['nyp_original'] = $cart_item_data['nyp'];
		}

		return $cart_item_data;
	}

	/**
	 * Filter Name Your Price Cart prices.
	 *
	 * @param array $session_data The Session data.
	 * @param array $values       The values.
	 *
	 * @return array
	 *
	 * @since    4.12.2
	 *
	 * @internal filter.
	 */
	public function filter_woocommerce_get_cart_item_from_session( $session_data, $values ) {

		// Preserve original currency.
		if ( isset( $values['nyp_currency'] ) ) {
			$session_data['nyp_currency'] = $values['nyp_currency'];
		}

		// Preserve original entered value.
		if ( isset( $values['nyp_original'] ) ) {
			$session_data['nyp_original'] = $values['nyp_original'];
		}

		$current_currency = getClientCurrency();

		if ( isset( $session_data['nyp_currency'] ) && $session_data['nyp_currency'] !== $current_currency ) {

			// Product is in the 'data'.
			$product = $session_data['data'];

			$price_in_current_currency = $this->product_price_filter( $session_data['nyp'], $current_currency );

			// Set to price in current currency.
			$product->set_price( $price_in_current_currency );
			$product->set_regular_price( $price_in_current_currency );
			$product->set_sale_price( $price_in_current_currency );

			// Subscription-specific price and variable billing period.
			if ( $product->is_type( [ 'subscription', 'subscription_variation' ] ) ) {
				$product->update_meta_data( '_subscription_price', $price_in_current_currency );
			}
		}

		return $session_data;
	}

	/**
	 * Add currency to cart edit link.
	 *
	 * @param array $args
	 * @return array
	 */
	public function edit_in_cart_args( $args ) {
		$args['nyp_currency'] = get_woocommerce_currency();
		return $args;
	}

	/**
	 * Maybe convert any prices being edited from the cart
	 *
	 * @param string           $initial_price
	 * @param mixed|WC_Product $product
	 * @param string           $suffix
	 * @return string
	 */
	public function get_initial_price( $initial_price, $product, $suffix ) {

		if ( isset( $_REQUEST[ 'nyp_raw' . $suffix ] ) && isset( $_REQUEST[ 'nyp_currency' ] ) ) {
			$from_currency = wc_clean( $_REQUEST[ 'nyp_currency' ] );
			$current_currency = get_woocommerce_currency();
			if ( $from_currency !== $current_currency ) {
				$raw_price = wc_clean( $_REQUEST[ 'nyp_raw' . $suffix ] );

				$multi_currency = make( \WCML_Multi_Currency::class );
				$initial_price = $multi_currency->prices->convert_price_amount_by_currencies( $raw_price, $from_currency, $current_currency );
			}
		}
		
		return $initial_price;
	}  

}
