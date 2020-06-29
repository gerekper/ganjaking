<?php

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit();
}

function wp_cart_reports_uninstall() {
	global $wpdb;
	$sql    = 'SELECT * FROM ' . $wpdb->prefix . "posts WHERE post_type = 'carts'";
	$result = $wpdb->get_results($sql);

	foreach ($result as $cart) {
		$delete_meta_sql = 'DELETE FROM ' . $wpdb->prefix . "postmeta WHERE post_id = '" . $cart->ID . "'";
		$wpdb->query($delete_meta_sql);
		$delete_sql = 'DELETE FROM ' . $wpdb->prefix . "posts WHERE ID = '" . $cart->ID . "'";
		$wpdb->query($delete_sql);
	}
}

wp_cart_reports_uninstall();


