<?php
/**
 * YWSBS_Multi_Currency_Switcher class to add compatibility with YITH Multi Currency Switcher for WooCommerce
 *
 * @class   YWSBS_Multi_Currency_Switcher
 * @since   2.4.0
 * @author  YITH
 * @package YITH WooCommerce Subscription
 */

if ( ! defined( 'ABSPATH' ) || ! defined( 'YITH_YWSBS_VERSION' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Class YWSBS_Multi_Currency_Switcher
 */
class YWSBS_Multi_Currency_Switcher {
	/**
	 * Single instance of the class
	 *
	 * @var YWSBS_Multi_Currency_Switcher
	 */
	protected static $instance;

	/**
	 * Returns single instance of the class
	 *
	 * @return YWSBS_Multi_Currency_Switcher
	 * @since 1.0.0
	 */
	public static function get_instance() {
		return ! is_null( self::$instance ) ? self::$instance : self::$instance = new self();
	}

	/**
	 * Constructor
	 *
	 * Initialize class and registers actions and filters to be used
	 *
	 * @since  1.0.0
	 */
	private function __construct() {
		add_filter( 'ywsbs_subscription_recurring_price', array( $this, 'convert_subscription_prices' ), 10, 2 );
		add_filter( 'ywsbs_get_total_subscription_price', array( $this, 'convert_subscription_prices' ), 10, 2 );
		add_filter( 'ywsbs_change_subtotal_price_in_cart_html', array( $this, 'convert_subscription_prices' ), 10, 2 );
		add_filter( 'ywsbs_subscription_price', array( $this, 'convert_subscription_prices' ), 10, 2 );
		add_filter( 'ywsbs_my_subscriptions_view_before', array( $this, 'remove_filters_before_my_subscription_view' ), 10, 2 );
		add_filter( 'ywsbs_before_subscription_view', array( $this, 'remove_filters_before_my_subscription_view' ), 10, 2 );
		add_filter( 'ywsbs_product_fee', array( $this, 'convert_subscription_prices' ), 10, 2 );
		add_filter( 'ywsbs_change_price_in_cart_html', array( $this, 'convert_subscription_prices' ), 10, 2 );
	}

	/**
	 * Convert subscription internal prices
	 *
	 * @param float $price Price.
	 *
	 * @return float
	 */
	public function convert_subscription_prices( $price,$cart_item ) {
		if ( ! empty( $price ) ) {
			$currency_id = yith_wcmcs_get_current_currency_id();
			if ( $currency_id && yith_wcmcs_get_wc_currency_options( 'currency' ) !== $currency_id ) {
				$price = YITH_WCMCS_Products::get_instance()->filter_manual_price( $price, $cart_item );
			}
		}
		return $price;

	}

	/**
	 * Remove the currency symbol filter
	 */
	public function remove_filters_before_my_subscription_view() {
		remove_filter( 'woocommerce_currency_symbol', array( YITH_WCMCS_Products::get_instance(), 'filter_currency_symbol' ), 99 );
	}

}


/**
 * Get the YWSBS_Multi_Currency_Switcher instance
 *
 * @return YWSBS_Multi_Currency_Switcher
 */
function ywsbs_yith_wcmcs() {
	return YWSBS_Multi_Currency_Switcher::get_instance();
}

