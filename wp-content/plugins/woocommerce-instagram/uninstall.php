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
	require_once dirname( __FILE__ ) . '/includes/class-wc-instagram-uninstall.php';
	WC_Instagram_Uninstall::uninstall();
}
wc_instagram_uninstall();
