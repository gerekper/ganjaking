<?php
/**
 * WooCommerce Currency Converter Widget Uninstall
 *
 * Deletes the plugin options.
 *
 * @package WC_Currency_Converter_Widget/Uninstaller
 * @since   2.0.1
 */

defined( 'WP_UNINSTALL_PLUGIN' ) || exit;

/**
 * Plugin uninstall script.
 *
 * @since 2.0.1
 *
 * @global wpdb $wpdb The WordPress Database Access Abstraction Object.
 */
function wc_currency_coverter_widget_uninstall() {
	global $wpdb;

	// Delete the Open Exchange API credentials.
	delete_option( 'wc_currency_converter_app_id' );

	/*
	 * Only remove ALL the plugin data if WC_REMOVE_ALL_DATA constant is set to true in the wp-config.php file.
	 * This is to prevent data loss when deleting the plugin from the backend
	 * and to ensure only the site owner can perform this action.
	 */
	if ( defined( 'WC_REMOVE_ALL_DATA' ) && true === WC_REMOVE_ALL_DATA ) {
		// Deletes the plugin options.
		$wpdb->query( "DELETE FROM $wpdb->options WHERE option_name LIKE '%currency_converter%';" );
	}
}
wc_currency_coverter_widget_uninstall();
