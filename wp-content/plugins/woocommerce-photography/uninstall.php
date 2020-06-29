<?php
/**
 * Bookings Photography
 */
if( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit();
}

// Remove caps
$admin        = get_role( 'administrator' );
$shop_manager = get_role( 'shop_manager' );
$admin->remove_cap( 'manage_photography' );
$shop_manager->remove_cap( 'manage_photography' );

delete_option( 'wc_photography_version' ); // Version
delete_option( 'woocommerce_photography' ); // Plugin general settings
delete_option( 'woocommerce_wc_photography_new_collection_settings' ); // Email settings
