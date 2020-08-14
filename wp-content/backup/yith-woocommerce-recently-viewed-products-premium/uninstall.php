<?php
/**
 * Uninstall plugin
 *
 * @author YITH
 * @package YITH WooCommerce Recently Viewed Products
 * @version 1.0
 */

// If uninstall not called from WordPress exit
if( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

//delete pages created for this plugin
wp_delete_post( get_option( 'yith-woocompare-page-id' ), true );