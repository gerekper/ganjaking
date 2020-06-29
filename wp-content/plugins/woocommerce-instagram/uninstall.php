<?php
/**
 * WooCommerce Instagram Uninstall
 *
 * Deletes the plugin options.
 *
 * @package WC_Instagram/Uninstaller
 * @version 2.0.0
 */

defined( 'WP_UNINSTALL_PLUGIN' ) || exit;

/**
 * Plugin uninstall script.
 *
 * @since 2.0.0
 *
 * @global wpdb $wpdb The WordPress Database Access Abstraction Object.
 */
function wc_instagram_uninstall() {
	global $wpdb;

	/*
	 * Only remove ALL the plugin data if WC_REMOVE_ALL_DATA constant is set to true in the wp-config.php file.
	 * This is to prevent data loss when deleting the plugin from the backend
	 * and to ensure only the site owner can perform this action.
	 */
	if ( defined( 'WC_REMOVE_ALL_DATA' ) && true === WC_REMOVE_ALL_DATA ) {
		// Delete the plugin settings.
		delete_option( 'wc_instagram_settings' );

		// Delete options and transients.
		$wpdb->query( "DELETE FROM $wpdb->options WHERE option_name LIKE '%wc_instagram_%';" );

		// Delete products metas.
		$wpdb->query( "DELETE FROM $wpdb->postmeta WHERE meta_key LIKE '%_instagram_hashtag%';" );
	} else {
		include_once 'includes/wc-instagram-functions.php';

		// Only delete credentials.
		wc_instagram_disconnect();
	}
}
wc_instagram_uninstall();
