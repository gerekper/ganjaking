<?php
/**
 * Uninstall plugin
 *
 * @author YITH <plugins@yithemes.com>
 * @package YITH\Wishlist
 * @version 2.0.16
 */

// If uninstall not called from WordPress exit.
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

/**
 * Uninstall completely wishlist from the site
 *
 * @return void
 * @since 3.0.0
 */
function yith_wcwl_uninstall() {
	global $wpdb;

	if ( defined( 'YITH_WCWL_REMOVE_ALL_DATA' ) && true === YITH_WCWL_REMOVE_ALL_DATA ) {
		// define local private attribute.
		$wpdb->yith_wcwl_items     = $wpdb->prefix . 'yith_wcwl';
		$wpdb->yith_wcwl_wishlists = $wpdb->prefix . 'yith_wcwl_lists';

		// Delete option from options table.
		delete_option( 'yith_wcwl_version' );
		delete_option( 'yith_wcwl_db_version' );
		$wpdb->query( $wpdb->prepare( "DELETE FROM {$wpdb->options} WHERE option_name LIKE %s", 'yith_wcwl_%' ) ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching

		// delete pages created for this plugin.
		wp_delete_post( get_option( 'yith-wcwl-pageid' ), true );

		// remove any additional options and custom table.
		// phpcs:disable WordPress.DB.DirectDatabaseQuery, WordPress.DB.PreparedSQL.NotPrepared
		$sql = "DROP TABLE IF EXISTS `{$wpdb->yith_wcwl_items}`";
		$wpdb->query( $sql );
		$sql = "DROP TABLE IF EXISTS `{$wpdb->yith_wcwl_wishlists}`";
		$wpdb->query( $sql );
		// phpcs:enable WordPress.DB.DirectDatabaseQuery, WordPress.DB.PreparedSQL.NotPrepared
	}
}

if ( ! is_multisite() ) {
	yith_wcwl_uninstall();
} else {
	global $wpdb;
	$blog_ids         = $wpdb->get_col( "SELECT blog_id FROM {$wpdb->blogs}" ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
	$original_blog_id = get_current_blog_id();

	foreach ( $blog_ids as $blog_to_process ) {
		switch_to_blog( $blog_to_process );
		yith_wcwl_uninstall();
	}

	switch_to_blog( $original_blog_id );
}
