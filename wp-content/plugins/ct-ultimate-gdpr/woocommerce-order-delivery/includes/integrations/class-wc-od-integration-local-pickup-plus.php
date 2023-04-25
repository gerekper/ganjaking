<?php
/**
 * Integration: Local Pickup Plus.
 *
 * @package WC_OD\Integrations
 * @since   2.2.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Class WC_OD_Integration_Local_Pickup_Plus.
 */
class WC_OD_Integration_Local_Pickup_Plus implements WC_OD_Integration {

	/**
	 * Init.
	 *
	 * @since 2.2.0
	 */
	public static function init() {
		add_filter( 'wc_od_checkout_needs_details', array( __CLASS__, 'checkout_need_details' ) );
		add_filter( 'wc_od_shipping_settings', array( __CLASS__, 'shipping_settings' ) );
	}

	/**
	 * Gets the plugin basename.
	 *
	 * @since 2.2.0
	 *
	 * @return string
	 */
	public static function get_plugin_basename() {
		return 'woocommerce-shipping-local-pickup-plus/woocommerce-shipping-local-pickup-plus.php';
	}

	/**
	 * Gets if the 'Local Pickup Plus' shipping method is available at checkout.
	 *
	 * @since 2.2.0
	 *
	 * @return bool
	 */
	public static function is_shipping_method_available() {
		return wc_local_pickup_plus_shipping_method()->is_available();
	}

	/**
	 * Filters whether the checkout form needs delivery details.
	 *
	 * @since 2.2.0
	 *
	 * @param bool $needs_details Whether the checkout form needs delivery details.
	 * @return bool
	 */
	public static function checkout_need_details( $needs_details ) {
		if ( $needs_details && WC_OD()->checkout()->is_local_pickup() && self::is_shipping_method_available() ) {
			return false;
		}

		return $needs_details;
	}

	/**
	 * Filters the shipping settings.
	 *
	 * @since 2.2.0
	 *
	 * @param array $settings The shipping settings.
	 * @return array
	 */
	public static function shipping_settings( $settings ) {
		// Hide the 'Enable for Local Pickup' setting when the 'Local Pickup Plus' shipping method is available.
		if ( self::is_shipping_method_available() ) {
			$key = array_search( 'wc_od_enable_local_pickup', wp_list_pluck( $settings, 'id' ), true );

			unset( $settings[ $key ] );
		}

		return $settings;
	}
}
