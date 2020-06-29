<?php
/**
 * Uninstall plugin
 *
 * @author Your Inspiration Themes
 * @package YITH WooCommerce One-Click Checkout
 * @version 1.0.0
 */

// If uninstall not called from WordPress exit
if( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

// remove rewrite rules option
delete_option( 'yith-wocc-flush-rewrite-rules' );