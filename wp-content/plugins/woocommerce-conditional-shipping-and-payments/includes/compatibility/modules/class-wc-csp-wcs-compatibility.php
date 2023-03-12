<?php
/**
 * WC_CSP_WCS_Compatibility class
 *
 * @package  WooCommerce Conditional Shipping and Payments
 * @since    1.14.5
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * WooCommerce Subscriptions Compatibility.
 *
 * @version 1.12.1
 */
class WC_CSP_WCS_Compatibility {

	/**
	 * Initialization.
	 */
	public static function init() {

		self::load_conditions();

		// Include recurring packages for the checkout process.
		add_filter( 'woocommerce_csp_shipping_packages', array( __CLASS__, 'get_recurring_packages' ) );

		// Add an extra billing period variable in all recurring packages.
		add_filter( 'woocommerce_cart_shipping_packages', array( __CLASS__, 'set_period_on_recurring_packages') );
	}

	/**
	 * Load additional conditions by adding to the global conditions array.
	 *
	 * @return void
	 */
	public static function load_conditions() {

		$load_conditions = array(
			'WC_CSP_Condition_Cart_Recurring_Item',
			'WC_CSP_Condition_Package_Recurring_Item',
			'WC_CSP_Condition_Package_Recurring_Package'
		);

		if ( is_array( WC_CSP()->conditions->conditions ) ) {

			foreach ( $load_conditions as $condition ) {

				$condition = new $condition();
				WC_CSP()->conditions->conditions[ $condition->id ] = $condition;
			}
		}
	}

	/**
	 * Add an extra billing period variable in all recurring packages.
	 *
	 * @param  array  $packages
	 * @return array
	 */
	public static function set_period_on_recurring_packages( $packages ) {

		if ( 'recurring_total' !== WC_Subscriptions_Cart::get_calculation_type() ) {
			return $packages;
		}

		foreach ( $packages as $package_index => $package ) {

			if ( ! empty( $package[ 'contents' ] ) ) {

				foreach ( $package[ 'contents' ] as $cart_item_key => $cart_item ) {

					if ( WC_Subscriptions_Product::is_subscription( $cart_item[ 'data' ] ) ) {

						WC_CSP_Restriction::add_extra_package_variable( $packages[ $package_index ], 'billing_period', WC_Subscriptions_Product::get_period( $cart_item[ 'data' ] ) );
						break;
					}
				}
			}
		}

		return $packages;
	}

	/**
	 * Include recurring packages for the checkout process.
	 *
	 * @param  array  $initial_packages
	 * @return array
	 */
	public static function get_recurring_packages( $initial_packages ) {

		$shipping_methods     = WC()->checkout()->shipping_methods;
		$added_invalid_method = false;
		$recurring_packages   = array();

		if ( empty( WC()->cart->recurring_carts ) ) {
			return $initial_packages;
		}

		foreach ( WC()->cart->recurring_carts as $recurring_cart_key => $recurring_cart ) {

			if ( false === $recurring_cart->needs_shipping() || 0 == $recurring_cart->next_payment_date ) {
				continue;
			}

			$packages               = $recurring_cart->get_shipping_packages();
			$current_billing_period = wcs_cart_pluck( $recurring_cart, 'subscription_period' );

			foreach ( $packages as $package_index => $base_package ) {

				$package = WC()->shipping->calculate_shipping_for_package( $base_package );

				if ( isset( $package[ 'recurring_cart_key' ] ) ) {
					$recurring_shipping_package_key = $package_index;
				} else {
					$recurring_shipping_package_key = WC_Subscriptions_Cart::get_recurring_shipping_package_key( $recurring_cart_key, $package_index );
				}

				// Manually include the recurring package period.
				WC_CSP_Restriction::add_extra_package_variable( $package, 'billing_period', wcs_cart_pluck( $recurring_cart, 'subscription_period' ) );

				$recurring_packages[ $recurring_shipping_package_key ] = $package;
			}
		}

		return array_merge( $initial_packages, $recurring_packages );
	}
}

WC_CSP_WCS_Compatibility::init();
