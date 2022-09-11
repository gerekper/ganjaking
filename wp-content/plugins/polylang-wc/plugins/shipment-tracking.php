<?php
/**
 * @package Polylang-WC
 */

/**
 * Manages the compatibility with WooCommerce Shipment Tracking.
 * Version tested: 1.6.10.
 *
 * @since 0.6
 */
class PLLWC_Shipment_Tracking {

	/**
	 * Constructor.
	 *
	 * @since 0.6
	 */
	public function __construct() {
		add_action( 'change_locale', array( $this, 'change_locale' ) );
	}

	/**
	 * Reloads the Shipment Tracking translations in emails.
	 * Hooked to the action 'change_locale'.
	 *
	 * @since 1.0
	 *
	 * @return void
	 */
	public function change_locale() {
		WC_Shipment_Tracking_Actions::get_instance()->load_plugin_textdomain();
	}
}
