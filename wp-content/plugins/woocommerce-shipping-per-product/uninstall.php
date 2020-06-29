<?php
/**
 * Per Product Shipping Uninstaller.
 *
 * @package WC_Shipping_Per_Product
 */

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit();
}

global $wpdb;

// Remove rules table.
$wpdb->query( 'DROP TABLE IF EXISTS ' . $wpdb->prefix . 'woocommerce_per_product_shipping_rules' );
