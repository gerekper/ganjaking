<?php
/**
 * WooCommerce Products Compare Uninstall.
 *
 * Deletes the plugin options.
 *
 * @package WC_Products_Compare/Uninstaller
 * @since   1.0.0
 */

defined( 'WP_UNINSTALL_PLUGIN' ) || exit;

/*
 * Only remove ALL product and page data if WC_REMOVE_ALL_DATA constant is set to true in user's
 * wp-config.php. This is to prevent data loss when deleting the plugin from the backend
 * and to ensure only the site owner can perform this action.
 */
if ( defined( 'WC_REMOVE_ALL_DATA' ) && true === WC_REMOVE_ALL_DATA ) {
	// Remove options.
	delete_option( 'wc_products_compare_endpoint_set' );
}
