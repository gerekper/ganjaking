<?php
/**
 * Integration: All Products for WooCommerce Subscriptions.
 *
 * @package WC_Account_Funds\Integrations
 * @since   2.5.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Class WC_Account_Funds_Integration_All_Products_Subscriptions.
 */
class WC_Account_Funds_Integration_All_Products_Subscriptions implements WC_Account_Funds_Integration {

	/**
	 * Init.
	 *
	 * @since 2.5.0
	 */
	public static function init() {
		add_filter( 'wcsatt_supported_product_types', array( __CLASS__, 'supported_product_types' ), 10, 3 );
	}

	/**
	 * Gets the plugin basename.
	 *
	 * @since 2.5.0
	 *
	 * @return string
	 */
	public static function get_plugin_basename() {
		return 'woocommerce-all-products-for-subscriptions/woocommerce-all-products-for-subscriptions.php';
	}

	/**
	 * Filters the product types that support subscription.
	 *
	 * @since 2.5.0
	 *
	 * @param array $types Product types.
	 * @return array
	 */
	public static function supported_product_types( $types ) {
		$types[] = 'deposit';

		return $types;
	}
}
