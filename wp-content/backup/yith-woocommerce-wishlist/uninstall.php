<?php
/**
 * Uninstall plugin
 *
 * @author Your Inspiration Themes
 * @package YITH WooCommerce Wishlist
 * @version 2.0.16
 */

// If uninstall not called from WordPress exit
if( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

function yith_wcwl_uninstall(){
	global $wpdb;

	if ( defined( 'YITH_WCWL_REMOVE_ALL_DATA' ) && true === YITH_WCWL_REMOVE_ALL_DATA && ! defined( 'YITH_WCWL_PREMIUM' ) ) {

		// define local private attribute
		$wpdb->yith_wcwl_items     = $wpdb->prefix . 'yith_wcwl';
		$wpdb->yith_wcwl_wishlists = $wpdb->prefix . 'yith_wcwl_lists';

		// Delete option from options table
		delete_option( 'yith_wcwl_version' );
		delete_option( 'yith_wcwl_db_version' );
		$wpdb->query( $wpdb->prepare( "DELETE FROM {$wpdb->options} WHERE option_name LIKE %s", 'yith_wcwl_%' ) );

		//delete pages created for this plugin
		wp_delete_post( get_option( 'yith-wcwl-pageid' ), true );

		//remove any additional options and custom table
		$sql = "DROP TABLE IF EXISTS `" . $wpdb->yith_wcwl_items . "`";
		$wpdb->query( $sql );
		$sql = "DROP TABLE IF EXISTS `" . $wpdb->yith_wcwl_wishlists . "`";
		$wpdb->query( $sql );

	}
}



if ( ! is_multisite() ) {
	yith_wcwl_uninstall();
}
else {
	global $wpdb;
	$blog_ids = $wpdb->get_col( "SELECT blog_id FROM {$wpdb->blogs}" );
	$original_blog_id = get_current_blog_id();

	foreach ( $blog_ids as $blog_id ) {
		switch_to_blog( $blog_id );
		yith_wcwl_uninstall();
	}

	switch_to_blog( $original_blog_id );
}