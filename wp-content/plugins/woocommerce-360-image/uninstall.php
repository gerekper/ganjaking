<?php
/**
 * WooCommerce 360 Image Uninstall.
 *
 * Deletes the plugin options.
 *
 * @package WC_360_Image/Uninstaller
 * @since   1.0.0
 */

defined( 'WP_UNINSTALL_PLUGIN' ) || exit;

/*
 * Only remove ALL the plugin data if WC_REMOVE_ALL_DATA constant is set to true in the wp-config.php file.
 * This is to prevent data loss when deleting the plugin from the backend
 * and to ensure only the site owner can perform this action.
 */
if ( defined( 'WC_REMOVE_ALL_DATA' ) && true === WC_REMOVE_ALL_DATA ) {
	// Delete options.
	delete_option( 'wc360_fullscreen_enable' );
	delete_option( 'wc360_navigation_enable' );

	// Delete meta.
	delete_post_meta_by_key( 'wc360_enable' );
}
