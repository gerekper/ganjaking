<?php
/**
 * Integration: Subscriptions
 *
 * @package WC_OD\Integrations
 * @since   2.2.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Class WC_OD_Integration_Subscriptions.
 */
class WC_OD_Integration_Subscriptions implements WC_OD_Integration {

	/**
	 * Minimum required version.
	 *
	 * @var string
	 */
	public static $min_version = '3.0';

	/**
	 * Init.
	 *
	 * @since 2.2.0
	 */
	public static function init() {
		if ( version_compare( get_option( 'woocommerce_subscriptions_active_version' ), self::$min_version, '<' ) ) {
			add_action( 'admin_notices', array( __CLASS__, 'requirements_notice' ) );
			return;
		}

		include_once WC_OD_PATH . 'includes/subscriptions/class-wc-od-subscriptions.php';
	}

	/**
	 * Gets the plugin basename.
	 *
	 * @since 2.2.0
	 *
	 * @return string
	 */
	public static function get_plugin_basename() {
		return 'woocommerce-subscriptions/woocommerce-subscriptions.php';
	}

	/**
	 * Displays an admin notice when the minimum requirements are not satisfied.
	 *
	 * @since 2.2.0
	 */
	public static function requirements_notice() {
		if ( ! current_user_can( 'activate_plugins' ) ) {
			return;
		}

		/* translators: %s: woocommerce subscription version */
		$message = sprintf( __( '<strong>WooCommerce Order Delivery</strong> requires WooCommerce Subscriptions %s or higher.', 'woocommerce-order-delivery' ), self::$min_version );

		printf( '<div class="error"><p>%s</p></div>', wp_kses_post( $message ) );
	}
}
