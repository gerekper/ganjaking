<?php
/**
 * @package Polylang-WC
 */

/**
 * Manages the compatibility with:
 *
 * @see https://wordpress.org/plugins/woocommerce-germanized/ WooCommerce Germanized, version tested: 3.10.2.
 *
 * This plugin already includes a compatibility with Polylang, not specifically PLLWC.
 * This class adds a quick fix for emails.
 *
 * @since 1.6.3
 */
class PLLWC_Germanized {

	/**
	 * Constructor.
	 *
	 * @since 1.6.3
	 */
	public function __construct() {
		// Deactivates the translation of emails sent to the shop manager to fix a conflict.
		add_filter( 'pllwc_enable_shop_manager_email_language', '__return_false' );
	}
}
