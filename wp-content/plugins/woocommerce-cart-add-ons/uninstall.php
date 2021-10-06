<?php

defined( 'ABSPATH' ) || exit;
defined( 'WP_UNINSTALL_PLUGIN' ) || exit;

global $wpdb;

$tables = array(
	$wpdb->prefix . 'sfn_cart_addons',
	$wpdb->prefix . 'sfn_cart_addons_categories',
	$wpdb->prefix . 'sfn_cart_addons_products',
);

foreach ( $tables as $tbl ) {
	$wpdb->query( "DROP TABLE `$tbl`" );
}
