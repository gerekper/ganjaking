<?php
/**
 * Integration: WooCommerce Shipping & Tax.
 *
 * @package WC_Store_Credit\Integrations
 * @since   4.1.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Class WC_Store_Credit_Integration_Shipping_Tax
 */
class WC_Store_Credit_Integration_Shipping_Tax implements WC_Store_Credit_Integration {

	/**
	 * Init.
	 *
	 * @since 4.1.0
	 */
	public static function init() {
		add_filter( 'wc_store_credit_calculate_shipping_discounts_for_cart', array( __CLASS__, 'calculate_shipping_discounts_for_cart' ) );
	}

	/**
	 * Gets the plugin basename.
	 *
	 * @since 4.1.0
	 *
	 * @return string
	 */
	public static function get_plugin_basename() {
		return 'woocommerce-services/woocommerce-services.php';
	}

	/**
	 * Whether to calculate the shipping discounts for the specified cart.
	 *
	 * @since 4.1.0
	 *
	 * @param bool $calculate_discounts Whether to calculate the shipping discounts.
	 * @return bool
	 */
	public static function calculate_shipping_discounts_for_cart( $calculate_discounts ) {
		if ( ! $calculate_discounts ) {
			return $calculate_discounts;
		}

		$backtrace  = wp_debug_backtrace_summary( 'WP_Hook', 0, false ); // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_wp_debug_backtrace_summary
		$save_index = array_search( "do_action('woocommerce_after_calculate_totals')", $backtrace, true );

		/*
		 * "WooCommerce Shipping & Tax" calls again to the method WC_Cart->calculate_totals() inside the callback
		 * bound to the hook "woocommerce_after_calculate_totals". So, we need to discard the nested call and
		 * apply the changes only in the call made by WC_Cart->calculate_totals().
		 */
		if ( 'WC_Cart->calculate_totals' !== $backtrace[ $save_index + 1 ] ) {
			return false;
		}

		return $calculate_discounts;
	}
}
