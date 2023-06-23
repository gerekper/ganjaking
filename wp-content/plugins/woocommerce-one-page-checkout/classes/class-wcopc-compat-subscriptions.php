<?php
/**
 * @package     WooCommerce One Page Checkout
 * @subpackage  Subscriptions Extension Compatibility
 * @category    Template Class
 * @version 1.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Class to hold subscriptions compat functionality until merge with the main extension for dev purposes
 */
class WCOPC_Compat_Subscriptions {

	public function __construct() {

		if ( class_exists( 'WC_Subscriptions_Cart' ) ) {
			add_action( 'woocommerce_before_checkout_form', __CLASS__ . '::set_checkout_registration', 10 );
			add_filter( 'wcopc_script_data', __CLASS__ . '::add_switch_params', 10 );
		}
	}

	/**
	 * Override subscriptions setting of enable_guest_checkout to false and set to true - we will hide it using JS
	 *
	 * @param  \WC_Checkout $checkout Checkout object.
	 */
	public static function set_checkout_registration( $checkout ) {
		if ( ! is_user_logged_in() && PP_One_Page_Checkout::is_any_form_of_opc_page() && WC_Subscriptions_Cart::cart_contains_subscription() && wcopc_is_checkout_registration_enabled() ) {
			$checkout->enable_guest_checkout = true;
		}
	}

	/**
	 * Add switch related URL params to the OPC ajax URL to make sure switching of limited products
	 * is possible from an OPC product page.
	 *
	 * @param array
	 * @return array
	 * @since 1.2.8
	 */
	public static function add_switch_params( $wcopc_script_data ) {
		// PHPCS:Disable WordPress.Security.NonceVerification.Recommended
		if ( isset( $_GET['switch-subscription'], $_GET['item'] ) ) {
			$switch_args = array(
				'auto-switch'         => 'true',
				'switch-subscription' => absint( $_GET['switch-subscription'] ),
				'item'                => absint( $_GET['item'] ),
				'_wcsnonce'           => isset( $_GET['_wcsnonce'] ) ? sanitize_text_field( $_GET['_wcsnonce'] ) : '',
			);

			$wcopc_script_data['ajax_url'] = esc_url_raw( add_query_arg( $switch_args, $wcopc_script_data['ajax_url'] ) );
		}
		// PHPCS:Enable
		return $wcopc_script_data;
	}
}
new WCOPC_Compat_Subscriptions();
