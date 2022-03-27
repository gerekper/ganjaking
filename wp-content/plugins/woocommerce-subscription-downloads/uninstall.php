<?php
/**
 * WooCommerce Subscription Downloads Uninstall
 *
 * @package  WC_Subscription_Downloads
 * @category Uninstall
 * @author   WooThemes
 */
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit();
}

global $wpdb;

// Delete the plugin options.
delete_option( 'woocommerce_subscription_downloads_version' );
$wpdb->query( "DROP TABLE IF EXISTS {$wpdb->prefix}woocommerce_subscription_downloads" );
