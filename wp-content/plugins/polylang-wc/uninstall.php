<?php
/**
 * @package Polylang-WC
 */

// If uninstall not called from WordPress exit.
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit();
}

/**
 * Manages Polylang for WooCommerce uninstallation.
 *
 * @since 0.4
 */
class PLLWC_Uninstall {

	/**
	 * Constructor: manages uninstall for multisite.
	 *
	 * @since 0.4
	 */
	public function __construct() {
		global $wpdb;

		// Check if it is a multisite uninstall - if so, run the uninstall function for each blog id.
		if ( is_multisite() ) {
			foreach ( $wpdb->get_col( "SELECT blog_id FROM {$wpdb->blogs}" ) as $blog_id ) {
				switch_to_blog( $blog_id );
				$this->uninstall();
			}
			restore_current_blog();
		} else {
			$this->uninstall();
		}
	}

	/**
	 * Removes ALL plugin data.
	 *
	 * @since 0.4
	 */
	public function uninstall() {
		// Delete options.
		delete_option( 'polylang-wc' );
	}
}

new PLLWC_Uninstall();
