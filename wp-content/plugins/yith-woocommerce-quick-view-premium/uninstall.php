<?php
/**
 * Uninstall plugin
 *
 * @author YITH
 * @package YITH WooCommerce Quick View Premium
 * @version 1.0
 */

// If uninstall not called from WordPress exit
if( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

global $wpdb;
