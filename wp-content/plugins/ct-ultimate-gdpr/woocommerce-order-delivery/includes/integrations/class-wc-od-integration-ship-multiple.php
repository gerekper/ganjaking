<?php
/**
 * Integration: Shipping Multiple Addresses.
 *
 * @package WC_OD\Integrations
 * @since   1.9.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Class WC_OD_Integration_Ship_Multiple.
 */
class WC_OD_Integration_Ship_Multiple implements WC_OD_Integration {

	/**
	 * Init.
	 *
	 * @since 1.9.0
	 */
	public static function init() {
		add_filter( 'wc_od_checkout_needs_details', array( __CLASS__, 'checkout_needs_details' ) );
	}

	/**
	 * Gets the plugin basename.
	 *
	 * @since 1.9.0
	 *
	 * @return string
	 */
	public static function get_plugin_basename() {
		return 'woocommerce-shipping-multiple-addresses/woocommerce-shipping-multiple-addresses.php';
	}

	/**
	 * Filters if it's necessary to display the delivery details in the checkout form.
	 *
	 * @since 1.9.0
	 *
	 * @global WC_Ship_Multiple $wcms The 'Ship to Multiple Addresses' plugin instance.
	 *
	 * @param bool $needs_details Whether to display the delivery details in the checkout form.
	 * @return bool
	 */
	public static function checkout_needs_details( $needs_details ) {
		global $wcms;

		if ( $needs_details && $wcms instanceof WC_Ship_Multiple && $wcms->cart->cart_has_multi_shipping() ) {
			$needs_details = false;
		}

		return $needs_details;
	}
}
